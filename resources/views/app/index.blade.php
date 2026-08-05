@extends('layouts.app')

@section('title', 'Studio — Typography Studio')
@section('description', 'Create stunning kinetic typography videos. Type your text and watch all 12 templates animate live.')

@push('head')
<style>
/* ===== STUDIO PAGE LAYOUT ===== */
.studio-header {
    padding: 2.2rem 0 1.75rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    background: linear-gradient(180deg, rgba(139,92,246,0.08) 0%, transparent 100%);
}
.text-input-label {
    display: block;
    text-align: center;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.4);
    margin-bottom: 0.75rem;
}
.studio-input {
    width: 100%;
    background: rgba(255,255,255,0.06);
    border: 1.5px solid rgba(255,255,255,0.12);
    border-radius: 16px;
    padding: 1rem 1.2rem;
    font-size: 1.1rem;
    color: #fff;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    font-family: 'Inter', sans-serif;
    resize: none;
    min-height: 64px;
    max-height: 130px;
}
.studio-input::placeholder { color: rgba(255,255,255,0.25); }
.studio-input:focus {
    border-color: rgba(139,92,246,0.65);
    background: rgba(255,255,255,0.08);
    box-shadow: 0 0 0 4px rgba(139,92,246,0.15);
}
.char-count {
    position: absolute;
    bottom: 0.6rem;
    right: 1rem;
    font-size: 0.7rem;
    color: rgba(255,255,255,0.25);
    pointer-events: none;
}
.char-count.warn { color: rgba(239,68,68,0.7); }

/* ===== SECTION HEADER ===== */
.section-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.65rem;
    padding: 1.5rem 1rem 0.85rem;
}
.section-title {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.35);
}
.live-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: rgba(34,197,94,0.15);
    color: #4ade80;
    border: 1px solid rgba(34,197,94,0.25);
    border-radius: 100px;
    padding: 0.2rem 0.65rem;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.06em;
}
.live-dot {
    width: 6px; height: 6px;
    background: #4ade80;
    border-radius: 50%;
    animation: livePulse 1.4s ease-in-out infinite;
}
@keyframes livePulse { 0%,100%{opacity:1;} 50%{opacity:0.25;} }

/* ===== TEMPLATE GRID ===== */
.templates-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    padding: 0.85rem 1rem 5rem;
    max-width: 960px;
    margin: 0 auto;
}
@media(min-width:560px)  { .templates-grid { grid-template-columns: repeat(3,1fr); } }
@media(min-width:920px)  { .templates-grid { grid-template-columns: repeat(4,1fr); gap:1.25rem; } }

/* ===== TEMPLATE CARD ===== */
.template-card {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.25s, border-color 0.25s;
    border: 1px solid rgba(255,255,255,0.08);
    background: #0d0d18;
}
.template-card:hover {
    transform: translateY(-6px) scale(1.03);
    box-shadow: 0 22px 55px rgba(0,0,0,0.6);
    border-color: rgba(139,92,246,0.4);
}
.template-card:active { transform: scale(0.98); }

/* 9:16 preview canvas with Container Queries */
.preview-canvas {
    width: 100%;
    aspect-ratio: 9/16;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    container-type: inline-size;
}
.preview-canvas .kte-stage {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 8cqi;
    overflow: hidden;
}
.preview-canvas .kte-support {
    line-height: 1.25;
    text-align: center;
    margin-bottom: 2cqi;
    font-size: clamp(12px, 5cqi, 22px);
}
.preview-canvas .kte-keyword {
    line-height: 1.1;
    text-align: center;
    font-size: clamp(20px, 10.5cqi, 48px);
}

/* Big Hover Hint overlay */
.hover-preview-hint {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,0.65);
    backdrop-filter: blur(4px);
    color: rgba(255,255,255,0.9);
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.6rem;
    font-weight: 600;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.2s;
    z-index: 5;
    display: flex;
    align-items: center;
    gap: 3px;
}
.template-card:hover .hover-preview-hint { opacity: 1; }

/* ===== CARD FOOTER ===== */
.card-footer {
    position: absolute;
    bottom: 0;
    left: 0; right: 0;
    padding: 1.8rem 0.75rem 0.7rem;
    background: linear-gradient(to top, rgba(0,0,0,0.92) 0%, rgba(0,0,0,0.4) 60%, transparent 100%);
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 0.5rem;
    z-index: 10;
}
.card-name {
    font-size: 0.7rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.25;
    flex: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-shadow: 0 1px 3px rgba(0,0,0,0.8);
}

/* Download button styling - crisp & non-disabled */
.download-btn {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 8px;
    padding: 0.4rem 0.75rem;
    font-size: 0.68rem;
    font-weight: 700;
    cursor: pointer !important;
    transition: all 0.2s ease;
    white-space: nowrap;
    text-decoration: none !important;
    box-shadow: 0 4px 12px rgba(124,58,237,0.4);
    opacity: 1 !important;
}
.download-btn:hover {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    transform: translateY(-1px) scale(1.05);
    box-shadow: 0 6px 18px rgba(124,58,237,0.6);
}
.download-btn:disabled {
    opacity: 0.7 !important;
    cursor: wait !important;
}
.download-btn.login-btn {
    background: rgba(255,255,255,0.15);
    box-shadow: none;
}
.download-btn.login-btn:hover {
    background: rgba(255,255,255,0.25);
}

/* ===== RENDER OVERLAY ===== */
.render-overlay {
    position: absolute;
    inset: 0;
    background: rgba(8,8,16,0.85);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    z-index: 20;
    border-radius: 16px;
    backdrop-filter: blur(3px);
}
.spinner {
    width: 32px; height: 32px;
    border: 3px solid rgba(255,255,255,0.15);
    border-top-color: #a78bfa;
    border-radius: 50%;
    animation: spin 0.75s linear infinite;
}
.render-status-text { font-size: 0.72rem; color: #fff; font-weight: 700; }
.status-icon { font-size: 1.6rem; animation: iconPop 0.4s cubic-bezier(0.34,1.56,0.64,1); }
@keyframes spin    { to { transform: rotate(360deg); } }
@keyframes iconPop { from { transform: scale(0); opacity: 0; } to { transform: scale(1); opacity: 1; } }

/* ===== BIG PREVIEW MODAL ===== */
.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.88);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    animation: fadeIn 0.2s ease;
}
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

.big-modal-container {
    background: #0f0f1c;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 24px;
    width: 100%;
    max-width: 440px;
    max-height: 92vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 25px 80px rgba(0,0,0,0.8);
    position: relative;
    animation: modalPop 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes modalPop { from { transform: scale(0.9) translateY(20px); opacity: 0; } to { transform: scale(1) translateY(0); opacity: 1; } }

.modal-header {
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.modal-title {
    font-size: 1rem;
    font-weight: 800;
    color: #fff;
    font-family: 'Fraunces', serif;
}
.modal-close-btn {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    border: none;
    color: rgba(255,255,255,0.7);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}
.modal-close-btn:hover { background: rgba(255,255,255,0.18); color: #fff; }

.modal-body {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    overflow-y: auto;
}

.big-preview-canvas {
    width: 100%;
    max-width: 330px;
    aspect-ratio: 9/16;
    border-radius: 18px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 15px 45px rgba(0,0,0,0.6);
    border: 1px solid rgba(255,255,255,0.1);
    container-type: inline-size;
}

.modal-footer {
    padding: 1rem 1.25rem;
    border-top: 1px solid rgba(255,255,255,0.08);
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.big-download-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%);
    color: #fff;
    font-weight: 800;
    font-size: 0.95rem;
    padding: 0.85rem;
    border-radius: 14px;
    border: none;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.2s;
    box-shadow: 0 6px 20px rgba(124,58,237,0.4);
    text-decoration: none;
}
.big-download-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(124,58,237,0.6);
}

/* Worker notice */
.queue-notice {
    margin: 0 auto 0.5rem;
    max-width: 680px;
    background: rgba(234,179,8,0.12);
    border: 1px solid rgba(234,179,8,0.3);
    color: #facc15;
    font-size: 0.78rem;
    padding: 0.65rem 1rem;
    border-radius: 12px;
    text-align: center;
    display: none;
}
.queue-notice.show { display: block; }
</style>
@endpush

@section('content')
<div x-data="studioApp()" x-init="init()" class="min-h-screen">

    {{-- Studio Header --}}
    <div class="studio-header">
        <div style="max-width:680px;margin:0 auto;padding:0 1rem;">
            <div style="text-align:center;margin-bottom:1.25rem;">
                <h1 style="font-size:1.6rem;font-weight:800;color:#fff;font-family:'Fraunces',serif;margin-bottom:0.25rem;">Typography Studio</h1>
                <p style="font-size:0.82rem;color:rgba(255,255,255,0.45);">Type your text below — click any card for a <strong>Big Preview</strong> or <strong>MP4 Download</strong></p>
            </div>
            <label class="text-input-label">Your Text</label>
            <div style="position:relative;">
                <textarea
                    id="studio-text-input"
                    x-model="text"
                    @input="handleInput()"
                    class="studio-input"
                    placeholder="Apna text yahan likhein..."
                    rows="2"
                    maxlength="200"
                    autofocus
                ></textarea>
                <span class="char-count" :class="{'warn': text.length > 170}" x-text="text.length + '/200'"></span>
            </div>

            {{-- Canvas Ratio Selector --}}
            <div style="margin-top:0.85rem;display:flex;justify-content:center;position:relative;" x-data="{ openRatio: false }">
                <button
                    type="button"
                    @click="openRatio = !openRatio"
                    style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);color:#fff;padding:0.45rem 1rem;border-radius:100px;font-size:0.78rem;font-weight:700;display:inline-flex;align-items:center;gap:8px;cursor:pointer;backdrop-filter:blur(6px);"
                >
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <span>Canvas Ratio: <strong style="color:#a78bfa;" x-text="aspectRatioLabel"></strong></span>
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div
                    x-show="openRatio"
                    @click.away="openRatio = false"
                    x-cloak
                    style="position:absolute;top:110%;z-index:90;background:#181824;border:1px solid rgba(255,255,255,0.15);border-radius:14px;padding:0.75rem;width:260px;box-shadow:0 20px 40px rgba(0,0,0,0.8);display:flex;flex-direction:column;gap:6px;"
                >
                    <span style="font-size:0.68rem;color:rgba(255,255,255,0.5);font-weight:600;margin-bottom:4px;">Set the canvas ratio used by preview & export</span>

                    <template x-for="r in aspectRatios" :key="r.id">
                        <div
                            @click="setRatio(r.id, r.name); openRatio = false;"
                            style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:8px;cursor:pointer;transition:background 0.2s;"
                            :style="aspectRatio === r.id ? 'background:rgba(124,58,237,0.3);border:1px solid rgba(124,58,237,0.5);' : 'background:rgba(255,255,255,0.03);'"
                        >
                            <span style="font-size:0.78rem;font-weight:700;color:#fff;" x-text="r.name + ' (' + r.id + ')'"></span>
                            <span style="font-size:0.65rem;font-weight:800;color:#a78bfa;" x-text="r.desc"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Queue worker notice --}}
    <div class="queue-notice" id="queue-notice">
        ⚡ To download MP4s, run <strong>start-queue-worker.bat</strong> in the project folder and keep it open.
    </div>

    {{-- Grid header --}}
    <div class="section-header">
        <span class="section-title">{{ $templates->count() }} Templates</span>
        <span class="live-badge"><span class="live-dot"></span>LIVE PREVIEW</span>
    </div>

    {{-- Template Grid --}}
    <div class="templates-grid">
        @foreach($templates as $template)
        <div
            class="template-card"
            id="card-{{ $template->slug }}"
            @click="openModal({{ $template->id }}, '{{ $template->slug }}', '{{ addslashes($template->name) }}')"
        >
            {{-- Big hover hint --}}
            <div class="hover-preview-hint">
                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Big View
            </div>

            {{-- Preview Canvas --}}
            <div
                class="preview-canvas"
                id="canvas-{{ $template->slug }}"
                data-slug="{{ $template->slug }}"
                data-type="{{ $template->animation_type }}"
                data-primary="{{ $template->primary_color }}"
                data-secondary="{{ $template->secondary_color }}"
                data-bg="{{ $template->background_color }}"
                data-font="{{ addslashes($template->font_family) }}"
                style="background:{{ $template->background_color }};"
            >
                <div class="kte-stage"></div>
            </div>

            {{-- Render overlay --}}
            <div class="render-overlay"
                x-show="renders['{{ $template->id }}'] && renders['{{ $template->id }}'].status !== 'idle'"
                x-cloak
                @click.stop
            >
                <template x-if="renders['{{ $template->id }}'] && renders['{{ $template->id }}'].status === 'pending'">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:0.5rem;">
                        <div class="spinner"></div>
                        <span class="render-status-text">Queued…</span>
                    </div>
                </template>
                <template x-if="renders['{{ $template->id }}'] && renders['{{ $template->id }}'].status === 'processing'">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:0.5rem;">
                        <div class="spinner"></div>
                        <span class="render-status-text">Rendering…</span>
                    </div>
                </template>
                <template x-if="renders['{{ $template->id }}'] && renders['{{ $template->id }}'].status === 'done'">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:0.5rem;">
                        <span class="status-icon">✅</span>
                        <span class="render-status-text">Downloaded!</span>
                    </div>
                </template>
                <template x-if="renders['{{ $template->id }}'] && renders['{{ $template->id }}'].status === 'failed'">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:0.5rem;">
                        <span class="status-icon">❌</span>
                        <span class="render-status-text" x-text="renders['{{ $template->id }}'] && renders['{{ $template->id }}'].errorMsg || 'Failed'"></span>
                    </div>
                </template>
            </div>

            {{-- Card Footer --}}
            <div class="card-footer" @click.stop>
                <span class="card-name" @click.stop="openModal({{ $template->id }}, '{{ $template->slug }}', '{{ addslashes($template->name) }}')">{{ $template->name }}</span>
                
                {{-- Edit Mode Link --}}
                <a
                    :href="'{{ url("/app/editor") }}/' + {{ $template->id }} + '?text=' + encodeURIComponent(text)"
                    class="download-btn"
                    style="background:rgba(255,255,255,0.12);box-shadow:none;padding:0.35rem 0.55rem;"
                    @click.stop
                    title="Customize timing, fonts, positions & images"
                >
                    ✏️ Edit
                </a>

                @auth
                    <button
                        class="download-btn"
                        id="dl-btn-{{ $template->id }}"
                        @click.stop="downloadTemplate({{ $template->id }}, '{{ $template->slug }}')"
                        title="Download as MP4"
                    >
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        MP4
                    </button>
                @else
                    <a href="{{ route('login') }}" class="download-btn login-btn" @click.stop title="Login to download">
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Login
                    </a>
                @endauth
            </div>
        </div>
        @endforeach
    </div>

    {{-- ===== BIG FULLSCREEN MODAL PREVIEW ===== --}}
    <div
        class="modal-backdrop"
        x-show="modal.open"
        x-cloak
        @keydown.escape.window="closeModal()"
        @click.self="closeModal()"
    >
        <div class="big-modal-container" @click.stop>
            {{-- Modal Header --}}
            <div class="modal-header">
                <span class="modal-title" x-text="modal.name">Template Preview</span>
                <button class="modal-close-btn" @click="closeModal()" title="Close">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body (Big 9:16 Canvas) --}}
            <div class="modal-body">
                <div
                    class="big-preview-canvas"
                    id="modal-canvas"
                    style="background:#000;"
                >
                    <div class="kte-stage"></div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer">
                <a
                    :href="'{{ url("/app/editor") }}/' + modal.id + '?text=' + encodeURIComponent(text)"
                    class="big-download-btn"
                    style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);"
                >
                    ✏️ Customize Phrases, Fonts & Timing in Editor Mode
                </a>

                @auth
                    <button
                        class="big-download-btn"
                        @click="downloadTemplate(modal.id, modal.slug); closeModal();"
                    >
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download 1080×1920 MP4 Video
                    </button>
                @else
                    <a href="{{ route('login') }}" class="big-download-btn">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Login to Download Video
                    </a>
                @endauth
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
/* ====================================================================
   KINETIC TYPOGRAPHY ENGINE
   Handles phrase splitting, sequential reveal, 12 GSAP animation types
   ==================================================================== */

class KineticEngine {
    constructor(stageEl, cfg) {
        this.stage    = stageEl;
        this.cfg      = cfg;     // { type, fontFamily, primaryColor, secondaryColor, uppercase }
        this.phrases  = [];
        this.idx      = 0;
        this.running  = false;
        this.tl       = null;
        this.holdTimer= null;
        this.glowTw   = null;

        // Clear stage and build inner wrapper
        this.stage.innerHTML = '';
        this.wrap = document.createElement('div');
        this.wrap.className = 'kte-stage';
        this.stage.appendChild(this.wrap);
    }

    /* ── Phrase splitting ────────────────────────────────────────────── */
    static splitPhrases(text) {
        const words = (text || '').trim().split(/\s+/).filter(Boolean);
        if (words.length === 0) return [{ support: '', keyword: 'Your Text' }];

        const CHUNK = 5; // max words per phrase
        const groups = [];
        for (let i = 0; i < words.length; i += CHUNK) {
            groups.push(words.slice(i, i + CHUNK));
        }

        return groups.map(chunk => {
            if (chunk.length <= 2) {
                return { support: '', keyword: chunk.join(' ') };
            }
            const splitAt = Math.floor(chunk.length / 2);
            return {
                support: chunk.slice(0, splitAt).join(' '),
                keyword: chunk.slice(splitAt).join(' '),
            };
        });
    }

    /* ── Public: feed new text ───────────────────────────────────────── */
    update(text) {
        this.phrases = KineticEngine.splitPhrases(text);
        this.idx     = 0;
        this.stop();
        this.cycle();
    }

    stop() {
        this.running = false;
        clearTimeout(this.holdTimer);
        if (this.tl)     { this.tl.kill();     this.tl     = null; }
        if (this.glowTw) { this.glowTw.kill(); this.glowTw = null; }
        gsap.killTweensOf(this.wrap.querySelectorAll('*'));
    }

    /* ── Internal cycle ──────────────────────────────────────────────── */
    cycle() {
        this.running = true;
        if (!this.phrases.length) return;
        this.render(this.phrases[this.idx]);
    }

    render(phrase) {
        /* Build DOM elements */
        this.wrap.innerHTML = '';

        const supportEl = document.createElement('div');
        supportEl.className = 'kte-support';

        let keyword = phrase.keyword;
        let support = phrase.support;
        if (this.cfg.type === 'heart-accent') {
            keyword = '♥ ' + keyword + ' ♥';
        }
        if (this.cfg.uppercase) {
            keyword = keyword.toUpperCase();
            support = support.toUpperCase();
        }

        supportEl.textContent = support;
        Object.assign(supportEl.style, {
            fontFamily:    this.cfg.fontFamily,
            color:         this.cfg.secondaryColor || 'rgba(255,255,255,0.65)',
            fontWeight:    '500',
            fontSize:      '0.7em',
            letterSpacing: '0.07em',
            opacity:       '0',
            textTransform: this.cfg.uppercase ? 'uppercase' : 'none',
        });

        const keyEl = document.createElement('div');
        keyEl.className = 'kte-keyword';
        keyEl.textContent = keyword;
        Object.assign(keyEl.style, {
            fontFamily:    this.cfg.fontFamily,
            color:         this.cfg.primaryColor,
            fontWeight:    this.cfg.weight || '900',
            fontSize:      '1.6em',
            letterSpacing: this.cfg.uppercase ? '0.04em' : '0.01em',
            opacity:       '0',
            textTransform: this.cfg.uppercase ? 'uppercase' : 'none',
        });

        if (support) this.wrap.appendChild(supportEl);
        this.wrap.appendChild(keyEl);

        /* Delegate to per-template animation */
        this._animIn(supportEl, keyEl, phrase);
    }

    _animIn(supEl, keyEl, phrase) {
        const HOLD    = 2000;   // ms phrase stays visible
        const hasSupp = !!phrase.support;
        const elems   = hasSupp ? [supEl, keyEl] : [keyEl];
        const type    = this.cfg.type;

        const startHold = () => {
            this.holdTimer = setTimeout(() => this._animOut(supEl, keyEl), HOLD);
        };

        if (this.tl) this.tl.kill();
        if (this.glowTw) { this.glowTw.kill(); this.glowTw = null; }

        switch (type) {
            case 'fade-reveal':
                this.tl = gsap.timeline({ onComplete: startHold })
                    .fromTo(elems, { opacity: 0 }, { opacity: 1, duration: 0.55, stagger: 0.14 });
                break;

            case 'slide-up':
            case 'big-small-stack':
                this.tl = gsap.timeline({ onComplete: startHold })
                    .fromTo(elems,
                        { opacity: 0, y: 28 },
                        { opacity: 1, y: 0, duration: 0.5, stagger: 0.14, ease: 'power2.out' });
                break;

            case 'scale-pop':
            case 'bounce-baseline':
                this.tl = gsap.timeline({ onComplete: startHold });
                if (hasSupp) this.tl.fromTo(supEl, { opacity: 0 }, { opacity: 1, duration: 0.3 }, 0);
                this.tl.fromTo(keyEl,
                    { opacity: 0, scale: 0.25 },
                    { opacity: 1, scale: 1, duration: 0.6, ease: 'back.out(1.8)' },
                    hasSupp ? 0.1 : 0);
                break;

            case 'typewriter':
            case 'typewriter-reveal': {
                const fullText = keyEl.textContent;
                keyEl.textContent = '';
                keyEl.style.opacity = '1';
                if (hasSupp) gsap.fromTo(supEl, { opacity: 0 }, { opacity: 1, duration: 0.3 });
                let counter = { v: 0 };
                this.tl = gsap.to(counter, {
                    v: fullText.length,
                    duration: Math.max(0.8, fullText.length * 0.06),
                    ease: 'none',
                    onUpdate: () => {
                        keyEl.textContent = fullText.substring(0, Math.round(counter.v));
                    },
                    onComplete: startHold
                });
                break;
            }

            case 'color-sweep':
            case 'color-highlight-split':
                keyEl.style.opacity = '1';
                this.tl = gsap.timeline({ onComplete: startHold });
                if (hasSupp) this.tl.fromTo(supEl, { opacity: 0 }, { opacity: 1, duration: 0.3 }, 0);
                this.tl.fromTo(keyEl,
                    { clipPath: 'inset(0 100% 0 0)' },
                    { clipPath: 'inset(0 0% 0 0)', duration: 0.7, ease: 'power2.inOut' },
                    0.1);
                break;

            case 'rotate-in':
            case 'rotate-in-transition':
                this.tl = gsap.timeline({ onComplete: startHold });
                if (hasSupp) this.tl.fromTo(supEl, { opacity: 0, y: 8 }, { opacity: 1, y: 0, duration: 0.35 }, 0);
                this.tl.fromTo(keyEl,
                    { opacity: 0, rotationX: 90, transformOrigin: 'center 60%' },
                    { opacity: 1, rotationX: 0, duration: 0.6, ease: 'power2.out' },
                    hasSupp ? 0.15 : 0);
                break;

            case 'glow-pulse':
            case 'center-glow-focus':
                this.tl = gsap.timeline({
                    onComplete: () => {
                        this.glowTw = gsap.to(keyEl, {
                            textShadow: `0 0 35px ${this.cfg.primaryColor}, 0 0 70px ${this.cfg.primaryColor}60, 0 0 120px ${this.cfg.primaryColor}30`,
                            duration: 0.9,
                            yoyo: true,
                            repeat: -1,
                            ease: 'sine.inOut'
                        });
                        startHold();
                    }
                });
                this.tl.fromTo(elems, { opacity: 0 }, { opacity: 1, duration: 0.5, stagger: 0.1 });
                keyEl.style.textShadow = `0 0 12px ${this.cfg.primaryColor}60`;
                break;

            case 'zoom-in':
            case 'watermark-background':
                this.tl = gsap.timeline({ onComplete: startHold });
                if (hasSupp) this.tl.fromTo(supEl, { opacity: 0 }, { opacity: 1, duration: 0.35 }, 0);
                this.tl.fromTo(keyEl,
                    { opacity: 0, scale: 0.05 },
                    { opacity: 1, scale: 1, duration: 0.65, ease: 'power3.out' },
                    0);
                break;

            case 'split-reveal':
            case 'heart-accent':
                this.tl = gsap.timeline({ onComplete: startHold });
                if (hasSupp) this.tl.fromTo(supEl,
                    { opacity: 0, x: -22 },
                    { opacity: 1, x: 0, duration: 0.4, ease: 'power2.out' }, 0);
                this.tl.fromTo(keyEl,
                    { opacity: 0, x: 22 },
                    { opacity: 1, x: 0, duration: 0.45, ease: 'power2.out' },
                    hasSupp ? 0.1 : 0);
                break;

            case 'blur-focus':
            case 'script-serif-combo':
                this.tl = gsap.timeline({ onComplete: startHold })
                    .fromTo(elems,
                        { opacity: 0, filter: 'blur(14px)' },
                        { opacity: 1, filter: 'blur(0px)', duration: 0.65, stagger: 0.12, ease: 'power2.out' });
                break;

            case 'word-stagger':
            case 'all-caps-bold-display': {
                const words = keyEl.textContent.split(' ');
                keyEl.innerHTML = words.map(w =>
                    `<span style="display:inline-block;margin:0 2px;opacity:0;">${w}</span>`
                ).join('');
                keyEl.style.opacity = '1';
                const spans = keyEl.querySelectorAll('span');
                this.tl = gsap.timeline({ onComplete: startHold });
                if (hasSupp) this.tl.fromTo(supEl, { opacity: 0, y: -10 }, { opacity: 1, y: 0, duration: 0.3 }, 0);
                this.tl.fromTo(spans,
                    { opacity: 0, y: -24 },
                    { opacity: 1, y: 0, duration: 0.38, stagger: 0.09, ease: 'back.out(1.5)' },
                    hasSupp ? 0.1 : 0);
                break;
            }

            case 'pink-brush-blink':
            case 'purple-voice-blink':
            case 'neonsplash':
            case 'speech-flow':
            case 'mint-editorial':
            case 'clean-smooth':
            case 'plate-reveal':
            case 'comic-stroke':
            case 'grad-shadow':
            case 'liquid-glass':
            case 'neon-flicker':
            case 'neon-glow': {
                if (type === 'pink-brush-blink') {
                    keyEl.style.background = '#FF2E88';
                    keyEl.style.padding = '8px 18px';
                    keyEl.style.borderRadius = '8px';
                    keyEl.style.boxShadow = '3px 3px 0px #4A0827';
                } else if (type === 'purple-voice-blink') {
                    keyEl.style.background = '#6366f1';
                    keyEl.style.padding = '10px 24px';
                    keyEl.style.borderRadius = '100px';
                } else if (type === 'plate-reveal') {
                    keyEl.style.background = '#FFFFFF';
                    keyEl.style.color = '#000000';
                    keyEl.style.padding = '8px 18px';
                    keyEl.style.borderRadius = '6px';
                } else if (type === 'comic-stroke') {
                    keyEl.style.webkitTextStroke = '2px #000000';
                    keyEl.style.textShadow = '4px 4px 0px #000000';
                } else if (type === 'grad-shadow') {
                    keyEl.style.background = 'linear-gradient(180deg, #f97316, #eab308)';
                    keyEl.style.webkitBackgroundClip = 'text';
                    keyEl.style.webkitTextFillColor = 'transparent';
                } else if (type === 'liquid-glass') {
                    keyEl.style.background = 'rgba(255,255,255,0.15)';
                    keyEl.style.border = '1px solid rgba(255,255,255,0.3)';
                    keyEl.style.backdropFilter = 'blur(12px)';
                    keyEl.style.borderRadius = '100px';
                    keyEl.style.padding = '10px 24px';
                } else {
                    keyEl.style.textShadow = `0 0 8px ${this.cfg.primaryColor}, 0 0 20px ${this.cfg.primaryColor}80, 0 0 50px ${this.cfg.primaryColor}40`;
                }

                if (hasSupp) gsap.fromTo(supEl, { opacity: 0 }, { opacity: 1, duration: 0.4 });
                const flickTl = gsap.timeline({ onComplete: startHold });
                flickTl
                    .set(keyEl, { opacity: 0 })
                    .to(keyEl,  { opacity: 1,   duration: 0.05 })
                    .to(keyEl,  { opacity: 0,   duration: 0.05 })
                    .to(keyEl,  { opacity: 0.4, duration: 0.08 })
                    .to(keyEl,  { opacity: 1,   duration: 0.05 });
                this.tl = flickTl;
                break;
            }

            default:
                this.tl = gsap.timeline({ onComplete: startHold })
                    .fromTo(elems, { opacity: 0 }, { opacity: 1, duration: 0.5, stagger: 0.12 });
        }
    }

    _animOut(supEl, keyEl) {
        if (!this.running) return;
        if (this.glowTw) { this.glowTw.kill(); this.glowTw = null; }

        const alive = el => el && el.parentNode && this.wrap.contains(el);
        const elems = [supEl, keyEl].filter(alive);
        if (!elems.length) { this._next(); return; }

        const type = this.cfg.type;
        let outTl;

        switch (type) {
            case 'slide-up':
            case 'big-small-stack':
                outTl = gsap.timeline({ onComplete: () => this._next() })
                    .to(elems, { opacity: 0, y: -26, duration: 0.35, ease: 'power2.in' });
                break;

            case 'scale-pop':
            case 'bounce-baseline':
                outTl = gsap.timeline({ onComplete: () => this._next() })
                    .to(keyEl, { opacity: 0, scale: 1.35, duration: 0.3, ease: 'power2.in' });
                if (alive(supEl)) outTl.to(supEl, { opacity: 0, duration: 0.25 }, 0);
                break;

            case 'zoom-in':
            case 'watermark-background':
                outTl = gsap.timeline({ onComplete: () => this._next() })
                    .to(elems, { opacity: 0, scale: 1.6, duration: 0.3, ease: 'power2.in' });
                break;

            case 'rotate-in':
            case 'rotate-in-transition':
                outTl = gsap.timeline({ onComplete: () => this._next() })
                    .to(elems, { opacity: 0, rotationX: -90, duration: 0.38, ease: 'power2.in' });
                break;

            case 'blur-focus':
            case 'script-serif-combo':
                outTl = gsap.timeline({ onComplete: () => this._next() })
                    .to(elems, { opacity: 0, filter: 'blur(14px)', duration: 0.38, ease: 'power2.in' });
                break;

            case 'split-reveal':
            case 'heart-accent':
                outTl = gsap.timeline({ onComplete: () => this._next() });
                if (alive(supEl)) outTl.to(supEl, { opacity: 0, x: -20, duration: 0.3 }, 0);
                outTl.to(keyEl, { opacity: 0, x: 20, duration: 0.3 }, 0);
                break;

            default:
                outTl = gsap.timeline({ onComplete: () => this._next() })
                    .to(elems, { opacity: 0, duration: 0.35 });
        }

        this.tl = outTl;
    }

    _next() {
        if (!this.running || !this.phrases.length) return;
        this.idx = (this.idx + 1) % this.phrases.length;
        this.cycle();
    }
}

/* ====================================================================
   ALPINE.JS COMPONENT
   ==================================================================== */
function studioApp() {
    return {
        text:        '',
        renders:     {},    // templateId → { status, renderId, errorMsg }
        pollTimers:  {},
        engines:     {},    // slug → KineticEngine instance
        modalEngine: null,
        debounceT:   null,
        modal:       { open: false, id: null, slug: null, name: '' },

        aspectRatio: '9:16',
        aspectRatioLabel: 'Portrait 9:16',
        aspectRatios: [
            { id: '1:1',  name: 'Square',     desc: '1:1' },
            { id: '9:16', name: 'Portrait',   desc: '9:16' },
            { id: '4:3',  name: 'Landscape',  desc: '4:3' },
            { id: '16:9', name: 'Widescreen', desc: '16:9' },
            { id: '21:9', name: 'Cinema',     desc: '21:9' },
        ],

        setRatio(id, name) {
            this.aspectRatio = id;
            this.aspectRatioLabel = name + ' ' + id;
            const ratioCss = id.replace(':', '/');
            document.querySelectorAll('.preview-canvas, .big-preview-canvas').forEach(el => {
                el.style.aspectRatio = ratioCss;
            });
        },

        /* Template configs — match DB slugs 1:1 */
        templateCfgs: {
            'fade-reveal':           { type:'fade-reveal',    fontFamily:"'Playfair Display',serif",    primaryColor:'#F5E6D3', secondaryColor:'#C9A96E90', weight:'700' },
            'bounce-baseline':       { type:'bounce-baseline',fontFamily:"'Poppins',sans-serif",        primaryColor:'#FF6B6B', secondaryColor:'#FFE66D90', weight:'900' },
            'big-small-stack':       { type:'big-small-stack',fontFamily:"'Fraunces',serif",            primaryColor:'#FFFFFF', secondaryColor:'#6B7FBF',   weight:'900' },
            'script-serif-combo':    { type:'script-serif-combo',fontFamily:"'Dancing Script',cursive", primaryColor:'#D4AF37', secondaryColor:'#FFFFFF90', weight:'700' },
            'color-highlight-split': { type:'color-highlight-split',fontFamily:"'Archivo Black',sans-serif", primaryColor:'#F72585', secondaryColor:'#FFFFFFAA', weight:'400' },
            'center-glow-focus':     { type:'center-glow-focus',fontFamily:"'Oswald',sans-serif",       primaryColor:'#E94560', secondaryColor:'#FFFFFF80', weight:'700' },
            'rotate-in-transition':  { type:'rotate-in-transition',fontFamily:"'Bebas Neue',sans-serif",primaryColor:'#A8DADC', secondaryColor:'#F1FAEE90', weight:'400' },
            'watermark-background':  { type:'watermark-background',fontFamily:"'Cinzel Decorative',cursive",primaryColor:'#C77DFF',secondaryColor:'#9D4EDD90',weight:'700' },
            'all-caps-bold-display': { type:'all-caps-bold-display',fontFamily:"'Fraunces',serif",      primaryColor:'#FFD166', secondaryColor:'#EF476F',   weight:'900', uppercase:true },
            'typewriter-reveal':     { type:'typewriter-reveal',fontFamily:"'Courier Prime',monospace", primaryColor:'#06D6A0', secondaryColor:'#FFFFFF80', weight:'700' },
            'heart-accent':          { type:'heart-accent',   fontFamily:"'Caveat',cursive",            primaryColor:'#FF006E', secondaryColor:'#FFCCD5',   weight:'700' },
            'neon-glow':             { type:'neon-glow',      fontFamily:"'Rajdhani',sans-serif",       primaryColor:'#00F5FF', secondaryColor:'#FF00FF',   weight:'700', uppercase:true },
        },

        init() {
            this.$nextTick(() => {
                document.querySelectorAll('.preview-canvas[data-slug]').forEach(canvas => {
                    const slug  = canvas.dataset.slug;
                    const stage = canvas.querySelector('.kte-stage');
                    if (!stage) return;

                    const cfg = this.templateCfgs[slug] || {
                        type:          canvas.dataset.type,
                        fontFamily:    canvas.dataset.font || 'Inter, sans-serif',
                        primaryColor:  canvas.dataset.primary  || '#FFFFFF',
                        secondaryColor:canvas.dataset.secondary || 'rgba(255,255,255,0.5)',
                    };

                    const engine = new KineticEngine(stage, cfg);
                    this.engines[slug] = engine;
                    engine.update(this.text || 'Your Text Here');
                });
            });
        },

        handleInput() {
            clearTimeout(this.debounceT);
            this.debounceT = setTimeout(() => this.pushText(), 250);
        },

        pushText() {
            const display = this.text.trim() || 'Your Text Here';
            Object.values(this.engines).forEach(e => e.update(display));
            if (this.modalEngine) {
                this.modalEngine.update(display);
            }
        },

        /* ── Modal Big Preview ───────────────────────────────────────── */
        openModal(id, slug, name) {
            this.modal.id   = id;
            this.modal.slug = slug;
            this.modal.name = name;
            this.modal.open = true;

            this.$nextTick(() => {
                const canvas = document.getElementById('modal-canvas');
                if (!canvas) return;

                const cardCanvas = document.getElementById('canvas-' + slug);
                const bg = cardCanvas ? cardCanvas.style.background : '#0d0d18';
                canvas.style.background = bg;

                const cfg = this.templateCfgs[slug] || {
                    type: 'fade-reveal',
                    fontFamily: 'Inter, sans-serif',
                    primaryColor: '#FFFFFF',
                    secondaryColor: 'rgba(255,255,255,0.6)',
                };

                if (this.modalEngine) {
                    this.modalEngine.stop();
                }

                this.modalEngine = new KineticEngine(canvas, cfg);
                this.modalEngine.update(this.text.trim() || 'Your Text Here');
            });
        },

        closeModal() {
            this.modal.open = false;
            if (this.modalEngine) {
                this.modalEngine.stop();
                this.modalEngine = null;
            }
        },

        /* ── Download / render flow ─────────────────────────────────── */
        async downloadTemplate(templateId, slug) {
            @auth
            if (!this.text.trim()) {
                alert('Please enter some text first!');
                return;
            }

            const cur = this.renders[templateId];
            if (cur && ['pending','processing'].includes(cur.status)) return;

            this.setRender(templateId, 'pending');

            try {
                const resp = await fetch('{{ route("generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type':  'application/json',
                        'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':        'application/json',
                    },
                    body: JSON.stringify({ text: this.text.trim(), template_id: templateId, aspect_ratio: this.aspectRatio }),
                });

                if (!resp.ok) {
                    const err = await resp.json().catch(() => ({}));
                    throw new Error(err.message || 'Server error ' + resp.status);
                }

                const data = await resp.json();
                this.setRender(templateId, data.status, data.render_id);

                if (data.status === 'done' && data.download_url) {
                    const a = document.createElement('a');
                    a.href = data.download_url;
                    a.download = '';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    setTimeout(() => this.setRender(templateId, 'idle'), 3500);
                } else {
                    this.poll(templateId, data.render_id);
                }

            } catch (err) {
                console.error('Generate error:', err);
                this.setRender(templateId, 'failed', null, err.message || 'Request failed');
                setTimeout(() => this.setRender(templateId, 'idle'), 4000);
            }
            @else
            window.location.href = '{{ route("login") }}';
            @endauth
        },

        poll(templateId, renderId) {
            clearInterval(this.pollTimers[templateId]);
            let attempts = 0;

            this.pollTimers[templateId] = setInterval(async () => {
                attempts++;
                if (attempts > 90) {        // 3 min timeout
                    clearInterval(this.pollTimers[templateId]);
                    this.setRender(templateId, 'failed', renderId, 'Timed out. Is queue worker running?');
                    setTimeout(() => this.setRender(templateId, 'idle'), 5000);
                    return;
                }

                try {
                    const resp = await fetch('{{ url("/renders") }}/' + renderId + '/status', {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!resp.ok) return;
                    const data = await resp.json();

                    this.setRender(templateId, data.status, renderId);

                    if (data.status === 'done' && data.download_url) {
                        clearInterval(this.pollTimers[templateId]);
                        const a = document.createElement('a');
                        a.href = data.download_url;
                        a.download = '';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        setTimeout(() => this.setRender(templateId, 'idle'), 3500);
                    } else if (data.status === 'failed') {
                        clearInterval(this.pollTimers[templateId]);
                        this.setRender(templateId, 'failed', renderId, 'FFmpeg render failed. Check logs.');
                        setTimeout(() => this.setRender(templateId, 'idle'), 5000);
                    }
                } catch (e) { /* network blip */ }
            }, 2000);
        },

        setRender(tid, status, renderId = null, errorMsg = '') {
            this.renders = {
                ...this.renders,
                [tid]: { status, renderId, errorMsg }
            };
        },
    };
}
</script>
@endpush
