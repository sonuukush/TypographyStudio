@extends('layouts.app')

@section('title', 'Typography Studio — Create Stunning Text Videos Instantly')
@section('description', 'Turn any text into stunning animated typography videos. 12 professional templates, live preview, instant MP4 download. Free to use.')

@push('head')
<style>
/* ===== LANDING PAGE ===== */
.hero {
    min-height: calc(100vh - 64px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 1.5rem 3rem;
    position: relative;
    overflow: hidden;
}

/* Ambient background orbs */
.hero::before {
    content: '';
    position: absolute;
    width: 600px; height: 600px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(139,92,246,0.15) 0%, transparent 70%);
    top: -100px; left: 50%;
    transform: translateX(-50%);
    pointer-events: none;
}
.hero::after {
    content: '';
    position: absolute;
    width: 400px; height: 400px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(236,72,153,0.1) 0%, transparent 70%);
    bottom: 0; right: -100px;
    pointer-events: none;
}

.hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(139,92,246,0.12);
    border: 1px solid rgba(139,92,246,0.25);
    color: #a78bfa;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 0.35rem 0.9rem;
    border-radius: 100px;
    margin-bottom: 1.75rem;
}
.hero-eyebrow-dot { width: 6px; height: 6px; background: #7c3aed; border-radius: 50%; animation: pulse 2s ease infinite; }

.hero-title {
    font-size: clamp(2.5rem, 8vw, 5.5rem);
    font-weight: 900;
    line-height: 1.05;
    text-align: center;
    max-width: 820px;
    color: #fff;
    margin-bottom: 1.5rem;
    font-family: 'Fraunces', serif;
}
.hero-title .gradient-word {
    background: linear-gradient(135deg, #a78bfa 0%, #ec4899 50%, #f59e0b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: clamp(1rem, 2.5vw, 1.2rem);
    color: rgba(255,255,255,0.5);
    text-align: center;
    max-width: 540px;
    line-height: 1.6;
    margin-bottom: 2.5rem;
}

.hero-cta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    justify-content: center;
    margin-bottom: 4rem;
}

/* Template preview grid on landing */
.landing-previews {
    display: flex;
    gap: 1rem;
    justify-content: center;
    align-items: flex-end;
    flex-wrap: nowrap;
    overflow: hidden;
    max-width: 100%;
    padding: 0 1rem;
}
.landing-preview-card {
    flex-shrink: 0;
    width: 120px;
    aspect-ratio: 9/16;
    border-radius: 14px;
    overflow: hidden;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.08);
    transition: transform 0.3s ease;
}
.landing-preview-card:hover { transform: translateY(-8px) scale(1.04); }
.landing-preview-card:nth-child(even) { margin-bottom: 1.5rem; }

/* Features section */
.features-section {
    padding: 5rem 1.5rem;
    max-width: 1000px;
    margin: 0 auto;
}
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.5rem;
    margin-top: 3rem;
}
.feature-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 16px;
    padding: 1.75rem;
    transition: border-color 0.2s, transform 0.2s;
}
.feature-card:hover {
    border-color: rgba(139,92,246,0.3);
    transform: translateY(-2px);
}
.feature-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
    margin-bottom: 1rem;
}
.feature-title { font-size: 1rem; font-weight: 700; color: #fff; margin-bottom: 0.4rem; }
.feature-desc  { font-size: 0.85rem; color: rgba(255,255,255,0.45); line-height: 1.6; }

/* CTA section */
.cta-section {
    padding: 5rem 1.5rem;
    text-align: center;
    position: relative;
}
.cta-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at center, rgba(139,92,246,0.08) 0%, transparent 70%);
    pointer-events: none;
}
.cta-card {
    max-width: 600px;
    margin: 0 auto;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(139,92,246,0.2);
    border-radius: 24px;
    padding: 3.5rem 2rem;
    position: relative;
}

/* Section heading */
.section-heading {
    text-align: center;
    margin-bottom: 0.5rem;
}
.section-heading h2 {
    font-size: clamp(1.75rem, 4vw, 2.5rem);
    font-weight: 800;
    color: #fff;
    font-family: 'Fraunces', serif;
}
.section-subhead {
    text-align: center;
    color: rgba(255,255,255,0.4);
    font-size: 0.9rem;
}
</style>
@endpush

@section('content')

{{-- ===== HERO ===== --}}
<section class="hero">
    <div class="hero-eyebrow">
        <span class="hero-eyebrow-dot"></span>
        Free · 18 Templates · Instant Download
    </div>

    <h1 class="hero-title">
        Make your words<br>
        <span class="gradient-word">unforgettable</span>
    </h1>

    <p class="hero-subtitle">
        Type any text. Watch it animate across 18 stunning templates in real time. Click to download a perfect 1080×1920 MP4 — ready for Reels, Shorts, and TikTok.
    </p>

    <div class="hero-cta">
        @auth
            <a href="{{ route('app') }}" class="btn-primary btn-large">
                Open Studio
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        @else
            <a href="{{ route('register') }}" class="btn-primary btn-large">
                Get Started Free
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            <a href="{{ route('login') }}" class="btn-ghost btn-large">Login</a>
        @endauth
    </div>

    {{-- Live demo preview cards --}}
    <div class="landing-previews">
        @php
            $demoTemplates = [
                ['bg' => '#1a0a00', 'color' => '#F5E6D3', 'font' => 'Playfair Display', 'text' => 'Beautiful Words', 'anim' => 'fadeRevealAnim 3s ease-in-out infinite'],
                ['bg' => '#1a1a2e', 'color' => '#FF6B6B',  'font' => 'Poppins',          'text' => 'Make It Pop!',   'anim' => 'bounceOdd 0.8s ease-in-out infinite', 'weight' => '900'],
                ['bg' => '#0d0d0d', 'color' => '#D4AF37',  'font' => 'Dancing Script',    'text' => 'Elegant Style', 'anim' => 'fadeInUp 0.8s ease forwards'],
                ['bg' => '#000000', 'color' => '#00F5FF',  'font' => 'Rajdhani',          'text' => 'NEON VIBES',    'anim' => 'neonFlicker 2s ease-in-out infinite', 'weight' => '700', 'shadow' => '0 0 14px #00F5FF, 0 0 42px #00F5FF'],
                ['bg' => '#073B4C', 'color' => '#FFD166',  'font' => 'Fraunces',          'text' => 'BOLD.',         'anim' => 'boldPop 3s ease-in-out infinite', 'weight' => '900', 'upper' => true],
            ];
        @endphp
        @foreach($demoTemplates as $demo)
        <div class="landing-preview-card" style="background:{{ $demo['bg'] }};">
            <div style="
                font-family:'{{ $demo['font'] }}',serif,sans-serif;
                color:{{ $demo['color'] }};
                font-size:14px;
                font-weight:{{ $demo['weight'] ?? '400' }};
                text-align:center;
                padding:8px;
                animation:{{ $demo['anim'] }};
                text-shadow:{{ $demo['shadow'] ?? 'none' }};
                {{ isset($demo['upper']) ? 'text-transform:uppercase;' : '' }}
            ">{{ $demo['text'] }}</div>
        </div>
        @endforeach
    </div>
</section>

{{-- ===== FEATURES ===== --}}
<section class="features-section">
    <div class="section-heading">
        <h2>Everything you need, nothing you don't</h2>
    </div>
    <p class="section-subhead">Simple, fast, powerful — built for creators.</p>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(139,92,246,0.15);">🎙️</div>
            <div class="feature-title">Free Self-Hosted AI Voiceover</div>
            <div class="feature-desc">Generate natural Hindi, Hinglish & English voiceovers for your video with zero third-party API costs!</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(236,72,153,0.15);">📐</div>
            <div class="feature-title">5 Canvas Aspect Ratios</div>
            <div class="feature-desc">Switch between 9:16 Portrait, 1:1 Square, 16:9 Widescreen, 4:3 Landscape, and 21:9 Cinema ratios instantly.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(245,158,11,0.15);">🎨</div>
            <div class="feature-title">18 Trending Reel Styles</div>
            <div class="feature-desc">From NeonSplash and Dual Tone Script to Highlight Marker and Yellow Shadow — 18 premium typography presets.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(16,185,129,0.15);">🎬</div>
            <div class="feature-title">MP4 Export with Audio</div>
            <div class="feature-desc">Download crisp H.264 MP4 videos muxed with voiceover audio, ready for Reels, Shorts, and YouTube.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(99,102,241,0.15);">✍️</div>
            <div class="feature-title">Per-Word Font Sizing & Marker Pills</div>
            <div class="feature-desc">Mix big, medium, and small word sizes within phrases to create rhythm and highlight key words with marker plates.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(239,68,68,0.15);">🆓</div>
            <div class="feature-title">100% Free & Unlimited</div>
            <div class="feature-desc">No subscriptions, no watermarks, no hidden limits. Pure creative power at zero cost.</div>
        </div>
    </div>
</section>

{{-- ===== CTA ===== --}}
<section class="cta-section">
    <div class="cta-card">
        <h2 style="font-family:'Fraunces',serif;font-size:2rem;font-weight:800;color:#fff;margin-bottom:0.75rem;">
            Ready to create?
        </h2>
        <p style="color:rgba(255,255,255,0.45);font-size:0.95rem;margin-bottom:2rem;">
            Join thousands of creators making stunning typography videos every day.
        </p>
        @auth
            <a href="{{ route('app') }}" class="btn-primary btn-large">Open Studio</a>
        @else
            <a href="{{ route('register') }}" class="btn-primary btn-large">Create Free Account</a>
        @endauth
    </div>
</section>

@endsection
