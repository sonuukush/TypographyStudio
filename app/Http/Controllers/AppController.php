<?php

namespace App\Http\Controllers;

use App\Jobs\RenderVideoJob;
use App\Models\Render;
use App\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AppController extends Controller
{
    /**
     * Main app screen: text input + template grid
     */
    public function index()
    {
        $templates = Template::active();
        return view('app.index', compact('templates'));
    }

    /**
     * Template Editor screen: per-phrase customization
     */
    public function editor($id, Request $request)
    {
        $template = Template::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $initialText = $request->query('text', 'Zindagi ki asli jeet dusron ko harane mein nahi');
        return view('app.editor', compact('template', 'initialText'));
    }

    /**
     * Upload phrase image
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $path = $request->file('image')->store('uploads', 'public');
        $url = asset('storage/' . $path);

        return response()->json([
            'url'  => $url,
            'path' => 'public/' . $path,
        ]);
    }

    /**
     * Queue & render a new video job (supports custom scenes array)
     */
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'template_id'    => 'required|integer|exists:templates,id',
            'text'           => 'nullable|string|max:500',
            'scenes'         => 'nullable|array',
            'voiceover_path' => 'nullable|string',
            'aspect_ratio'   => 'nullable|string|in:9:16,1:1,16:9,4:3,21:9',
        ]);

        $text = $validated['text'] ?? '';
        if (empty($text) && !empty($validated['scenes'])) {
            $text = implode(' ', array_column($validated['scenes'], 'text'));
        }
        if (empty($text)) {
            $text = 'Custom Kinetic Typography';
        }

        $scenes = $validated['scenes'] ?? null;
        $voiceoverPath = $validated['voiceover_path'] ?? null;
        $aspectRatio = $validated['aspect_ratio'] ?? '9:16';

        // Auto-generate free AI voiceover audio if not explicitly passed
        if (empty($voiceoverPath)) {
            try {
                $ttsReq = new Request([
                    'text' => $text,
                    'voice' => 'hi_female',
                    'language' => 'hi',
                ]);
                $ttsResp = (new VoiceoverController())->generate($ttsReq);
                $ttsData = json_decode($ttsResp->getContent(), true);
                if (!empty($ttsData['storage_path']) && file_exists($ttsData['storage_path'])) {
                    $voiceoverPath = $ttsData['storage_path'];
                }
            } catch (\Throwable $e) {}
        }

        if (empty($scenes)) {
            $scenes = [
                '_voiceover_path' => $voiceoverPath,
                '_aspect_ratio'   => $aspectRatio,
            ];
        } else {
            $scenes['_voiceover_path'] = $voiceoverPath;
            $scenes['_aspect_ratio']   = $aspectRatio;
        }

        $render = Render::create([
            'user_id'     => Auth::id(),
            'template_id' => $validated['template_id'],
            'input_text'  => mb_substr($text, 0, 300),
            'scenes_json' => $scenes,
            'status'      => 'pending',
        ]);

        // Process job (sync execution ensures MP4 is built right away)
        try {
            (new RenderVideoJob($render->id))->handle();
            $render->refresh();
        } catch (\Throwable $e) {
            // Handled inside job
        }

        return response()->json([
            'render_id'    => $render->id,
            'status'       => $render->status,
            'download_url' => $render->isDone() ? route('renders.download', $render->id) : null,
            'message'      => $render->isDone() ? 'Video generated' : 'Render processing',
        ]);
    }

    /**
     * Poll render job status
     */
    public function status(int $id): JsonResponse
    {
        $render = Render::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Fail-safe: If status is not done yet, complete it now
        if (!$render->isDone() && !$render->isFailed()) {
            try {
                (new RenderVideoJob($render->id))->handle();
                $render->refresh();
            } catch (\Throwable $e) {
                // Handled inside job
            }
        }

        return response()->json([
            'render_id'    => $render->id,
            'status'       => $render->status,
            'download_url' => $render->isDone()
                ? route('renders.download', $render->id)
                : null,
        ]);
    }

    /**
     * Serve the rendered MP4 file as a download
     */
    public function download(int $id): BinaryFileResponse
    {
        $render = Render::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $filePath = storage_path('app/' . ($render->output_file_path ?? ''));

        // Fail-safe: If file is missing on disk, render it on the fly!
        if (empty($render->output_file_path) || !file_exists($filePath)) {
            (new RenderVideoJob($render->id))->handle();
            $render->refresh();
            $filePath = storage_path('app/' . ($render->output_file_path ?? ''));
        }

        abort_if(!file_exists($filePath), 404, 'Video file generation failed.');

        $templateName = $render->template->name ?? 'video';
        $filename = 'typography-' . str($templateName)->slug() . '-' . $render->id . '.mp4';

        return response()->download($filePath, $filename, [
            'Content-Type' => 'video/mp4',
        ]);
    }

    /**
     * Download history page
     */
    public function downloads()
    {
        // Fail-safe for history page: process any old pending renders on the fly
        $pendingRenders = Render::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'processing'])
            ->get();

        foreach ($pendingRenders as $pRender) {
            try {
                (new RenderVideoJob($pRender->id))->handle();
            } catch (\Throwable $e) {}
        }

        $renders = Render::with('template')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('app.downloads', compact('renders'));
    }
}
