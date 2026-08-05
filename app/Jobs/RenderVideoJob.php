<?php

namespace App\Jobs;

use App\Models\Render;
use App\Models\Template;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RenderVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries = 2;

    public function __construct(
        public int $renderId
    ) {}

    public function handle(): void
    {
        $render = Render::with('template')->findOrFail($this->renderId);

        $render->update(['status' => 'processing']);

        try {
            $outputPath = $this->buildVideo($render);
            $render->update([
                'status' => 'done',
                'output_file_path' => $outputPath,
            ]);
        } catch (\Throwable $e) {
            Log::error('RenderVideoJob failed', [
                'render_id' => $this->renderId,
                'error' => $e->getMessage(),
            ]);
            $render->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    private function buildVideo(Render $render): string
    {
        $template  = $render->template;
        $text      = $this->sanitizeText($render->input_text);
        $config    = $template->config_json ?? [];
        $ffmpeg    = config('app.ffmpeg_binary', 'C:/ffmpeg-8.1.2-essentials_build/bin/ffmpeg.exe');
        $fontsDir  = storage_path('app/fonts');
        $outputDir = storage_path('app/renders');

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0775, true);
        }

        $filename   = 'render_' . $this->renderId . '_' . Str::random(8) . '.mp4';
        $outputFile = $outputDir . DIRECTORY_SEPARATOR . $filename;

        $bgColor   = ltrim($template->background_color, '#');
        $primaryColor = ltrim($template->primary_color, '#');
        $secondaryColor = ltrim($template->secondary_color ?? 'B8C0CC', '#');

        $fontFilename = $config['ffmpeg_font'] ?? 'Poppins-Bold.ttf';
        $fontFile  = $fontsDir . DIRECTORY_SEPARATOR . $fontFilename;

        if (!file_exists($fontFile)) {
            $availableFonts = glob($fontsDir . '/*.ttf');
            if (empty($availableFonts)) {
                $winFonts = glob('C:/Windows/Fonts/*.ttf');
                $fontFile = $winFonts[0] ?? '';
            } else {
                $fontFile = $availableFonts[0];
            }
        }

        $customScenes = $render->scenes_json;

        if (!empty($customScenes) && is_array($customScenes) && isset($customScenes[0]['text'])) {
            $cmdArray = $this->buildCustomScenesFFmpegCommandArray(
                $ffmpeg, $template, $customScenes, $bgColor, $primaryColor,
                $fontFile, $fontsDir, $outputFile
            );
        } else {
            $phrases = $this->splitPhrases($text);
            $phraseDuration = 2.5; // seconds per phrase
            $totalDuration = max(5, (int) ceil(count($phrases) * $phraseDuration));

            if (is_array($customScenes) && !empty($customScenes['_voiceover_path'])) {
                $config['_voiceover_path'] = $customScenes['_voiceover_path'];
            }

            $cmdArray = $this->buildFFmpegCommandArray(
                $ffmpeg, $template->animation_type, $phrases, $phraseDuration,
                $bgColor, $primaryColor, $secondaryColor, $fontFile, $fontsDir, $totalDuration,
                $outputFile, $config
            );
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($cmdArray, $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new \RuntimeException('Failed to start FFmpeg process.');
        }

        fclose($pipes[0]);
        $stderr = stream_get_contents($pipes[2]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new \RuntimeException("FFmpeg failed (exit {$exitCode}): " . $stderr);
        }

        return 'renders/' . $filename;
    }

    private function splitPhrases(string $text): array
    {
        $words = preg_split('/\s+/', trim($text));
        $words = array_values(array_filter($words));
        if (empty($words)) {
            return [['support' => '', 'keyword' => 'Your Text']];
        }

        $chunkSize = 5;
        $chunks = array_chunk($words, $chunkSize);

        $phrases = [];
        foreach ($chunks as $chunk) {
            if (count($chunk) <= 2) {
                $phrases[] = ['support' => '', 'keyword' => implode(' ', $chunk)];
            } else {
                $splitAt = (int) floor(count($chunk) / 2);
                $phrases[] = [
                    'support' => implode(' ', array_slice($chunk, 0, $splitAt)),
                    'keyword' => implode(' ', array_slice($chunk, $splitAt)),
                ];
            }
        }
        return $phrases;
    }

    private function getFontArg(string $fontFile): string
    {
        if (!$fontFile) return '';
        $normFont = str_replace('\\', '/', $fontFile);
        $normFont = str_replace(':', '\\:', $normFont);
        return "fontfile='{$normFont}':";
    }

    private function buildCustomScenesFFmpegCommandArray(
        string $ffmpeg, Template $template, array $scenes, string $bgColor,
        string $primaryColor, string $defaultFontFile, string $fontsDir, string $outputFile
    ): array {
        $aspectRatio = $scenes['_aspect_ratio'] ?? '9:16';
        [$w, $h] = $this->getCanvasDimensions($aspectRatio);

        // Calculate total video duration
        $totalDuration = 0.0;
        foreach ($scenes as $scene) {
            if (isset($scene['end']) && is_numeric($scene['end'])) {
                $totalDuration = max($totalDuration, (float)$scene['end']);
            } else {
                $dur = max(0.5, (float)($scene['duration'] ?? 2.5));
                $totalDuration += $dur;
            }
        }
        $totalDuration = max(2.5, (float) $totalDuration);

        $cmd = [
            $ffmpeg,
            '-y',
            '-f', 'lavfi',
            '-i', "color=c=0x{$bgColor}:s={$w}x{$h}:d={$totalDuration}"
        ];

        $filterChains = [];
        $tCurrent = 0.0;

        foreach ($scenes as $i => $scene) {
            if (isset($scene['start']) && isset($scene['end'])) {
                $tStartVal = (float)$scene['start'];
                $tEndVal   = (float)$scene['end'];
                $duration  = max(0.3, $tEndVal - $tStartVal);
            } else {
                $duration  = max(0.5, (float)($scene['duration'] ?? 2.5));
                $tStartVal = $tCurrent;
                $tEndVal   = $tCurrent + $duration;
                $tCurrent += $duration;
            }

            $tStart = sprintf('%.2f', $tStartVal);
            $tEnd   = sprintf('%.2f', $tEndVal);
            $relT   = "(t-{$tStart})";
            $enableCond = "between(t,{$tStart},{$tEnd})";

            $sceneText = trim($scene['text'] ?? '');
            if (empty($sceneText)) continue;

            // Extract style (supports nested style object OR top-level scene attributes)
            $style = $scene['style'] ?? [];
            $textColor = ltrim($style['color'] ?? ($scene['color'] ?? $primaryColor), '#');
            $animType  = $style['animation'] ?? ($scene['animation'] ?? 'fade');
            $fontName  = $style['font'] ?? ($scene['font'] ?? 'Poppins-Bold.ttf');
            $position  = $style['vertical_position'] ?? ($style['position'] ?? ($scene['position'] ?? 'center'));
            $align     = $style['align'] ?? ($scene['align'] ?? 'center');

            // Resolve font file
            $fPath = $fontsDir . '/' . $fontName;
            if (!file_exists($fPath)) {
                $fPath = $defaultFontFile;
            }
            $sceneFontArg = $this->getFontArg($fPath);

            // Hook-word / First phrase size boost
            $fontSize = ($i === 0) ? 96 : 82;

            // Position presets (respecting Instagram Reels safe zones)
            if ($position === 'top') {
                $yBase = 380;
            } elseif ($position === 'bottom') {
                $yBase = 1380;
            } else {
                $yBase = 920; // center
            }

            // Align presets
            if ($align === 'left') {
                $xExpr = "100";
            } elseif ($align === 'right') {
                $xExpr = "w-text_w-100";
            } else {
                $xExpr = "(w-text_w)/2";
            }

            // Alpha curve
            $alphaExpr = "if(lt({$relT},0.3),{$relT}/0.3,if(gt({$relT},{$duration}-0.3),({$duration}-{$relT})/0.3,1))";

            // Y animation presets
            switch ($animType) {
                case 'bounce':
                case 'bounce_baseline':
                    $yExpr = "({$yBase})+30*sin({$relT}*9)-if(lt({$relT},0.3),40*(1-{$relT}/0.3),0)";
                    break;

                case 'slide_up':
                case 'slide':
                    $yExpr = "({$yBase})+if(lt({$relT},0.4),60*(1-{$relT}/0.4)*(1-{$relT}/0.4),if(gt({$relT},{$duration}-0.3),-50*(({$relT}-({$duration}-0.3))/0.3),0))";
                    break;

                case 'rotate_in':
                    $yExpr = "({$yBase})-if(lt({$relT},0.4),90*(1-{$relT}/0.4)*(1-{$relT}/0.4),0)";
                    break;

                case 'zoom_in':
                    $yExpr = "({$yBase})+if(lt({$relT},0.4),45*(1-{$relT}/0.4),0)";
                    break;

                case 'fade':
                default:
                    $yExpr = "({$yBase})";
                    break;
            }

            $escText = $this->ffmpegEscapeText($sceneText);
            $extraOpts = "";

            if ($animType === 'neon_glow' || ($style['template_id'] ?? '') === 'neon-glow') {
                $extraOpts = ":borderw=5:bordercolor=0x00F5FF@0.95:shadowcolor=0xFF00FF@0.85:shadowx=0:shadowy=0";
            } elseif ($animType === 'glow' || ($style['template_id'] ?? '') === 'center-glow-focus') {
                $extraOpts = ":borderw=4:bordercolor=0x{$textColor}@0.85:shadowcolor=0x{$textColor}@0.7:shadowx=0:shadowy=0";
            } elseif (($style['template_id'] ?? '') === 'yellow_shadow') {
                $extraOpts = ":shadowcolor=0x000000@0.9:shadowx=6:shadowy=6";
            } elseif (($style['template_id'] ?? '') === 'golden_aura') {
                $extraOpts = ":borderw=3:bordercolor=0xFFD700@0.9:shadowcolor=0xFF8C00@0.8:shadowx=0:shadowy=0";
            }

            // Drawtext filter
            $filterChains[] = "drawtext={$sceneFontArg}text='{$escText}':fontcolor=0x{$textColor}:fontsize={$fontSize}:x='{$xExpr}':y='{$yExpr}':alpha='{$alphaExpr}':enable='{$enableCond}'{$extraOpts}";
        }

        // Check for audio voiceover file
        $voiceoverPath = $scenes['_voiceover_path'] ?? null;
        if ($voiceoverPath && file_exists($voiceoverPath)) {
            $cmd[] = '-i';
            $cmd[] = $voiceoverPath;
        }

        $vf = implode(',', $filterChains);
        if (empty($vf)) {
            $vf = "null";
        }

        $cmd[] = '-vf';
        $cmd[] = $vf;
        $cmd[] = '-c:v';
        $cmd[] = 'libx264';
        $cmd[] = '-pix_fmt';
        $cmd[] = 'yuv420p';
        $cmd[] = '-r';
        $cmd[] = '30';

        if ($voiceoverPath && file_exists($voiceoverPath)) {
            $cmd[] = '-c:a';
            $cmd[] = 'aac';
            $cmd[] = '-b:a';
            $cmd[] = '192k';
            $cmd[] = '-shortest';
        }

        $cmd[] = $outputFile;

        return $cmd;
    }

    private function buildFFmpegCommandArray(
        string $ffmpeg, string $animationType, array $phrases, float $phraseDuration,
        string $bgColor, string $primaryColor, string $secondaryColor, string $fontFile,
        string $fontsDir, int $totalDuration, string $outputFile, array $config
    ): array {
        $aspectRatio = $config['_aspect_ratio'] ?? ($config['aspect_ratio'] ?? '9:16');
        [$w, $h] = $this->getCanvasDimensions($aspectRatio);
        $cy = (int)($h / 2);

        $fontArg = $this->getFontArg($fontFile);
        $filterChains = [];

        // Watermark background layer for template 8
        if ($animationType === 'watermark-background') {
            $escBgWord = $this->ffmpegEscapeText('TYPOGRAPHY');
            $filterChains[] = "drawtext={$fontArg}text='{$escBgWord}':fontcolor=0xFFFFFF@0.07:fontsize=160:x=(w-text_w)/2:y=(h-text_h)/2";
        }

        $scriptFontFile = $fontsDir . '/DancingScript-Bold.ttf';
        $scriptFontArg  = file_exists($scriptFontFile) ? $this->getFontArg($scriptFontFile) : $fontArg;

        foreach ($phrases as $i => $phrase) {
            $tStartVal = $i * $phraseDuration;
            $tEndVal   = ($i + 1) * $phraseDuration;
            $tStart = sprintf('%.2f', $tStartVal);
            $tEnd   = sprintf('%.2f', $tEndVal);

            $supportText = $phrase['support'];
            $keywordText = $phrase['keyword'];

            if ($animationType === 'heart-accent') {
                $keywordText = '♥ ' . $keywordText . ' ♥';
            }

            if (!empty($config['text_transform']) && $config['text_transform'] === 'uppercase' || $animationType === 'all-caps-bold-display' || $animationType === 'neon-glow') {
                $supportText = mb_strtoupper($supportText);
                $keywordText = mb_strtoupper($keywordText);
            }

            $escSupport = $this->ffmpegEscapeText($supportText);
            $escKeyword = $this->ffmpegEscapeText($keywordText);

            $enableCond = "between(t,{$tStart},{$tEnd})";
            $relT = "(t-{$tStart})";

            $keyFontSize = (int) ($config['font_size_large'] ?? 88);
            $supFontSize = (int) max(40, $keyFontSize * 0.52);

            $hasSupport = !empty($supportText);
            $supY = $hasSupport ? "{$cy}-{$keyFontSize}-35" : "{$cy}-40";
            $keyY = $hasSupport ? "{$cy}+15" : "{$cy}-{$keyFontSize}/2";

            $alphaExpr = "if(lt({$relT},0.3),{$relT}/0.3,if(gt({$relT},{$phraseDuration}-0.3),({$phraseDuration}-{$relT})/0.3,1))";

            $curKeyFontArg = $fontArg;
            $curSupFontArg = $fontArg;
            $extraKeyOpts  = "";
            $extraSupOpts  = "";

            $curPrimaryColor   = $primaryColor;
            $curSecondaryColor = $secondaryColor;

            $keyYExpr = "({$keyY})";
            $supYExpr = "({$supY})";
            $keyXExpr = "(w-text_w)/2";
            $supXExpr = "(w-text_w)/2";

            switch ($animationType) {
                case 'fade-reveal':
                    $alphaExpr = "if(lt({$relT},0.4),{$relT}/0.4,if(gt({$relT},2.1),(2.5-{$relT})/0.4,1))";
                    break;

                case 'bounce-baseline':
                    $keyYExpr = "({$keyY})+35*sin({$relT}*9)-if(lt({$relT},0.3),40*(1-{$relT}/0.3),0)";
                    $supYExpr = "({$supY})-if(lt({$relT},0.3),20*(1-{$relT}/0.3),0)";
                    break;

                case 'slide-up':
                case 'big-small-stack':
                    $keyYExpr = "({$keyY})+if(lt({$relT},0.4),70*(1-{$relT}/0.4)*(1-{$relT}/0.4),if(gt({$relT},2.1),-60*(({$relT}-2.1)/0.4)*(({$relT}-2.1)/0.4),0))";
                    $supYExpr = "({$supY})+if(lt({$relT},0.4),70*(1-{$relT}/0.4)*(1-{$relT}/0.4),if(gt({$relT},2.1),-60*(({$relT}-2.1)/0.4)*(({$relT}-2.1)/0.4),0))";
                    break;

                case 'script-serif-combo':
                    $curSupFontArg = $scriptFontArg;
                    $supFontSize   = (int) ($keyFontSize * 0.75);
                    $supYExpr      = "({$cy}-{$keyFontSize}-20)";
                    $keyYExpr      = "({$cy}+20)";
                    break;

                case 'color-highlight-split':
                    $colors = ['F72585', 'FFD60A', '4CC9F0', 'FFFFFF'];
                    $curPrimaryColor = $colors[$i % count($colors)];
                    $keyYExpr = "({$keyY})-if(lt({$relT},0.35),35*(1-{$relT}/0.35),0)";
                    break;

                case 'center-glow-focus':
                    $extraKeyOpts = ":borderw=5:bordercolor=0x{$primaryColor}@0.85:shadowcolor=0x{$primaryColor}@0.7:shadowx=0:shadowy=0";
                    break;

                case 'rotate-in-transition':
                    $keyYExpr = "({$keyY})-if(lt({$relT},0.45),100*(1-{$relT}/0.45)*(1-{$relT}/0.45),if(gt({$relT},2.1),90*(({$relT}-2.1)/0.4),0))";
                    break;

                case 'zoom-in':
                case 'watermark-background':
                    $keyYExpr = "({$keyY})+if(lt({$relT},0.4),50*(1-{$relT}/0.4),0)";
                    break;

                case 'all-caps-bold-display':
                    $extraKeyOpts = ":borderw=4:bordercolor=0xEF476F@0.85";
                    $keyYExpr = "({$keyY})-if(lt({$relT},0.4),80*(1-{$relT}/0.4),0)";
                    break;

                case 'typewriter-reveal':
                    break;

                case 'heart-accent':
                    $supXExpr = "(w-text_w)/2-if(lt({$relT},0.4),70*(1-{$relT}/0.4),0)";
                    $keyXExpr = "(w-text_w)/2+if(lt({$relT},0.4),70*(1-{$relT}/0.4),0)";
                    break;

                case 'neon-glow':
                    $alphaExpr = "if(lt({$relT},0.05),0,if(lt({$relT},0.1),1,if(lt({$relT},0.15),0.2,if(lt({$relT},0.25),1,if(gt({$relT},2.1),(2.5-{$relT})/0.4,1)))))";
                    $extraKeyOpts = ":borderw=5:bordercolor=0x00F5FF@0.95:shadowcolor=0xFF00FF@0.85:shadowx=0:shadowy=0";
                    break;
            }

            if ($hasSupport) {
                $filterChains[] = "drawtext={$curSupFontArg}text='{$escSupport}':fontcolor=0x{$curSecondaryColor}:fontsize={$supFontSize}:x='{$supXExpr}':y='{$supYExpr}':alpha='{$alphaExpr}':enable='{$enableCond}'{$extraSupOpts}";
            }

            if ($animationType === 'typewriter-reveal') {
                $len = mb_strlen($keywordText);
                if ($len > 0) {
                    $stepDuration = 1.2 / $len;
                    for ($k = 1; $k <= $len; $k++) {
                        $sub = mb_substr($keywordText, 0, $k);
                        $escSub = $this->ffmpegEscapeText($sub);
                        $stepStart = sprintf('%.2f', $tStartVal + ($k - 1) * $stepDuration);
                        $stepEnd   = sprintf('%.2f', $k === $len ? $tEndVal : ($tStartVal + $k * $stepDuration));
                        $stepCond  = "between(t,{$stepStart},{$stepEnd})";
                        $filterChains[] = "drawtext={$curKeyFontArg}text='{$escSub}':fontcolor=0x{$curPrimaryColor}:fontsize={$keyFontSize}:x='{$keyXExpr}':y='{$keyYExpr}':alpha='if(gt({$relT},2.1),({$phraseDuration}-{$relT})/0.4,1)':enable='{$stepCond}'{$extraKeyOpts}";
                    }
                }
            } else {
                $filterChains[] = "drawtext={$curKeyFontArg}text='{$escKeyword}':fontcolor=0x{$curPrimaryColor}:fontsize={$keyFontSize}:x='{$keyXExpr}':y='{$keyYExpr}':alpha='{$alphaExpr}':enable='{$enableCond}'{$extraKeyOpts}";
            }
        }

        $vf = implode(',', $filterChains);
        if (empty($vf)) {
            $vf = "null";
        }

        $voiceoverPath = $config['_voiceover_path'] ?? ($config['voiceover_path'] ?? null);

        $cmd = [
            $ffmpeg,
            '-y',
            '-f', 'lavfi',
            '-i', "color=c=0x{$bgColor}:s={$w}x{$h}:d={$totalDuration}",
        ];

        if ($voiceoverPath && file_exists($voiceoverPath)) {
            $cmd[] = '-i';
            $cmd[] = $voiceoverPath;
        }

        $cmd[] = '-vf';
        $cmd[] = $vf;
        $cmd[] = '-c:v';
        $cmd[] = 'libx264';
        $cmd[] = '-pix_fmt';
        $cmd[] = 'yuv420p';
        $cmd[] = '-r';
        $cmd[] = '30';

        if ($voiceoverPath && file_exists($voiceoverPath)) {
            $cmd[] = '-c:a';
            $cmd[] = 'aac';
            $cmd[] = '-b:a';
            $cmd[] = '192k';
            $cmd[] = '-shortest';
        }

        $cmd[] = $outputFile;

        return $cmd;
    }

    private function getCanvasDimensions(string $ratio): array
    {
        return match ($ratio) {
            '1:1'   => [1080, 1080],
            '16:9'  => [1920, 1080],
            '4:3'   => [1440, 1080],
            '21:9'  => [2520, 1080],
            default => [1080, 1920], // 9:16 Portrait
        };
    }

    private function sanitizeText(string $text): string
    {
        return mb_substr(trim($text), 0, 200);
    }

    private function ffmpegEscapeText(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace("'",  "\\'",  $text);
        $text = str_replace(':',  '\\:',  $text);
        $text = str_replace('%',  '\\%',  $text);
        return $text;
    }
}
