@extends('layouts.app')

@section('title', 'Template Editor — ' . $template->name)
@section('description', 'Professional Kinetic Typography & Auto-Caption Editor matching Clucap UI.')

@push('head')
<style>
/* ===== 3-PANEL EDITOR LAYOUT (CLUCAP EXACT MATCH) ===== */
.editor-wrapper {
    display: grid;
    grid-template-columns: 340px 1fr 400px;
    height: calc(100vh - 64px);
    overflow: hidden;
    background: #0d0d14;
    color: #e2e8f0;
    font-family: 'Inter', sans-serif;
}
@media(max-width: 1200px) {
    .editor-wrapper { grid-template-columns: 300px 1fr 360px; }
}
@media(max-width: 900px) {
    .editor-wrapper { grid-template-columns: 1fr; height: auto; overflow: auto; }
}

/* Panel Header */
.panel-head {
    padding: 0.85rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #12121c;
}
.panel-title {
    font-size: 0.85rem; font-weight: 800; color: #fff;
    display: flex; align-items: center; gap: 0.5rem;
}
.badge-count {
    background: rgba(34,197,94,0.15); color: #4ade80;
    border: 1px solid rgba(34,197,94,0.25);
    font-size: 0.65rem; font-weight: 700;
    padding: 2px 8px; border-radius: 100px;
}

/* ===== LEFT PANEL: CAPTIONS LIST & TIMELINE ===== */
.left-panel {
    display: flex; flex-direction: column;
    border-right: 1px solid rgba(255,255,255,0.08);
    background: #12121c; overflow: hidden;
}
.captions-scroll {
    flex: 1; overflow-y: auto; padding: 0.75rem;
    display: flex; flex-direction: column; gap: 0.5rem;
}
.caption-row {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 10px; padding: 0.65rem 0.8rem;
    display: flex; align-items: center; gap: 0.65rem;
    cursor: pointer; transition: all 0.15s ease;
}
.caption-row:hover { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.12); }
.caption-row.active {
    background: rgba(34, 197, 94, 0.08);
    border-color: #22c55e;
    box-shadow: 0 0 15px rgba(34, 197, 94, 0.15);
}
.caption-num {
    width: 22px; height: 22px; border-radius: 50%;
    background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6);
    font-size: 0.65rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.caption-row.active .caption-num { background: #22c55e; color: #000; }
.caption-text-input {
    flex: 1; background: transparent; border: none; outline: none;
    color: #fff; font-size: 0.8rem; font-weight: 600; width: 100%;
}
.caption-time { font-size: 0.62rem; color: rgba(255,255,255,0.4); font-weight: 600; white-space: nowrap; }

/* Left Bottom Toolbar & Timeline */
.left-toolbar {
    padding: 0.65rem 0.85rem; border-top: 1px solid rgba(255,255,255,0.08);
    display: flex; align-items: center; justify-content: space-between; background: #0e0e17;
}
.tb-btn {
    background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.8); font-size: 0.72rem; font-weight: 700;
    padding: 0.4rem 0.65rem; border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; gap: 0.3rem; transition: all 0.15s;
}
.tb-btn.add-btn { background: rgba(34, 197, 94, 0.15); color: #4ade80; border-color: rgba(34,197,94,0.3); }

.timeline-section {
    border-top: 1px solid rgba(255,255,255,0.08);
    background: #090910; padding: 0.6rem 0.85rem; display: flex; flex-direction: column; gap: 0.5rem;
}
.word-blocks-track { display: flex; gap: 4px; overflow-x: auto; padding: 4px 0; }
.word-block {
    background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);
    border-radius: 6px; padding: 4px 8px; font-size: 0.65rem; font-weight: 700;
    color: #cbd5e1; white-space: nowrap; cursor: pointer; transition: all 0.15s;
}
.word-block.emphasized { background: rgba(234, 179, 8, 0.25); border-color: #facc15; color: #facc15; }

/* ===== CENTER PANEL: LIVE PREVIEW & PLAYER ===== */
.center-panel {
    display: flex; flex-direction: column; align-items: center; justify-content: space-between;
    padding: 1rem; background: #09090f; position: relative;
}
.player-container {
    width: 100%; max-width: 320px; display: flex; flex-direction: column; align-items: center;
    gap: 0.75rem; margin: auto 0;
}
.video-viewport {
    width: 100%; aspect-ratio: 9/16; background: #000; border-radius: 18px;
    overflow: hidden; position: relative; box-shadow: 0 20px 50px rgba(0,0,0,0.8), 0 0 0 1px rgba(255,255,255,0.1);
    container-type: inline-size;
}

/* Scrubber & Player Controls */
.seeker-bar {
    width: 100%; height: 4px; background: rgba(255,255,255,0.15); border-radius: 100px;
    appearance: none; outline: none; cursor: pointer;
}
.seeker-bar::-webkit-slider-thumb {
    appearance: none; width: 12px; height: 12px; border-radius: 50%; background: #22c55e; cursor: pointer;
}
.player-controls {
    width: 100%; display: flex; align-items: center; justify-content: space-between;
    background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px; padding: 0.4rem 0.8rem;
}
.ctrl-btn { background: transparent; border: none; color: rgba(255,255,255,0.7); cursor: pointer; }

/* ===== RIGHT PANEL: TEMPLATES, CUSTOMIZE, EMPHASIS, MOTION ===== */
.right-panel {
    display: flex; flex-direction: column; border-left: 1px solid rgba(255,255,255,0.08);
    background: #12121c; overflow: hidden;
}

/* Top Navigation Tabs */
.tab-header { display: flex; border-bottom: 1px solid rgba(255,255,255,0.08); background: #0a0a12; }
.tab-btn {
    flex: 1; padding: 0.8rem 0.2rem; font-size: 0.75rem; font-weight: 800;
    color: rgba(255,255,255,0.4); border: none; background: transparent; cursor: pointer;
    border-bottom: 2px solid transparent; transition: all 0.15s; text-align: center;
}
.tab-btn.active { color: #fff; border-bottom-color: #22c55e; background: rgba(255,255,255,0.02); }

.right-body { flex: 1; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: 1rem; }

/* Sub Tabs & Search */
.sub-tabs { display: flex; background: rgba(255,255,255,0.05); padding: 3px; border-radius: 10px; gap: 3px; }
.sub-tab-btn {
    flex: 1; padding: 0.45rem; font-size: 0.7rem; font-weight: 700; border: none;
    background: transparent; color: rgba(255,255,255,0.5); border-radius: 8px; cursor: pointer;
}
.sub-tab-btn.active { background: rgba(255,255,255,0.14); color: #fff; }

.search-save-bar { display: flex; align-items: center; gap: 0.5rem; }
.search-input {
    flex: 1; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px; padding: 0.45rem 0.75rem; font-size: 0.75rem; color: #fff; outline: none;
}
.save-preset-btn {
    background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34,197,94,0.3);
    color: #4ade80; font-size: 0.7rem; font-weight: 700; padding: 0.45rem 0.75rem;
    border-radius: 8px; cursor: pointer; white-space: nowrap;
}

/* Template Cards Grid */
.gallery-grid { display: flex; flex-direction: column; gap: 0.85rem; }
.tpl-card {
    min-height: 85px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    cursor: pointer; position: relative; overflow: hidden; transition: transform 0.15s, border-color 0.2s;
    user-select: none; padding: 0.8rem;
}
.tpl-card:hover { transform: scale(1.015); border-color: #22c55e; }
.tpl-card.selected { border: 2px solid #22c55e; box-shadow: 0 0 20px rgba(34, 197, 94, 0.2); }

.tpl-header-row { width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
.tpl-card-title { font-size: 0.7rem; font-weight: 800; color: rgba(255,255,255,0.9); }
.tpl-tags { display: flex; gap: 4px; }
.tpl-tag-badge { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.6); font-size: 0.55rem; font-weight: 800; padding: 1px 5px; border-radius: 4px; text-transform: uppercase; }
.tpl-badge-new { background: #ff5e00; color: #fff; font-size: 0.55rem; font-weight: 800; padding: 1px 5px; border-radius: 4px; }

/* COLORS Quick Edit Panel (Clucap Screenshot 1 Match) */
.colors-quick-panel {
    background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px; padding: 0.75rem; display: flex; flex-direction: column; gap: 0.6rem;
    margin-top: -0.25rem;
}
.color-row { display: flex; align-items: center; justify-content: space-between; }
.color-label { font-size: 0.7rem; font-weight: 700; color: rgba(255,255,255,0.7); }
.color-picker-wrapper { display: flex; align-items: center; gap: 0.5rem; }
.color-swatch-input { width: 28px; height: 28px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.2); cursor: pointer; padding: 0; background: none; }
.color-hex-text { font-size: 0.7rem; font-family: monospace; font-weight: 700; color: #fff; background: rgba(0,0,0,0.4); padding: 3px 8px; border-radius: 6px; width: 75px; border: none; }

/* Accordions for Emphasis & Motion Tabs */
.accordion-box { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 10px; overflow: hidden; }
.accordion-head { padding: 0.65rem 0.85rem; font-size: 0.7rem; font-weight: 800; color: rgba(255,255,255,0.7); text-transform: uppercase; cursor: pointer; display: flex; justify-content: space-between; }
.accordion-body { padding: 0.75rem; border-top: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; gap: 0.65rem; }

/* Motion Cards Grid (Clucap Screenshot 5 Match) */
.motion-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; }
.motion-card {
    background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
    border-radius: 8px; height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.15s; user-select: none; position: relative;
}
.motion-card:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.2); }
.motion-card.active { border-color: #22c55e; background: rgba(34,197,94,0.1); }
.motion-card-title { font-size: 0.65rem; font-weight: 800; color: rgba(255,255,255,0.8); }
.motion-card-sub { font-size: 0.55rem; color: rgba(255,255,255,0.4); margin-top: 2px; }

/* Export Panel CTA */
.export-panel-btn {
    width: 100%; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: #000; font-size: 0.85rem; font-weight: 900; padding: 0.75rem;
    border-radius: 10px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    box-shadow: 0 4px 15px rgba(34,197,94,0.3); margin-top: auto;
}
</style>
@endpush

@section('content')
<div x-data="editorApp()" x-init="init()" class="editor-wrapper">

    {{-- ==================================================================
       LEFT PANEL — CAPTIONS LIST & WORD-LEVEL TIMELINE
       ================================================================== --}}
    <div class="left-panel">
        <div class="panel-head">
            <span class="panel-title">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                Captions
            </span>
            <span class="badge-count" x-text="calcTotalDuration() + ' secs'"></span>
        </div>

        <div class="captions-scroll">
            <template x-for="(phrase, idx) in scenes" :key="phrase.id">
                <div
                    class="caption-row"
                    :class="{'active': selectedPhraseIndex === idx}"
                    @click="selectedPhraseIndex = idx; syncPlayerToPhrase(idx);"
                >
                    <span class="caption-num" x-text="idx + 1"></span>

                    <input
                        type="text"
                        class="caption-text-input"
                        x-model="phrase.text"
                        @input="triggerUpdate()"
                        placeholder="Phrase text..."
                    >

                    <span class="caption-time" x-text="(phrase.start || 0).toFixed(1) + 's - ' + (phrase.end || 2.5).toFixed(1) + 's'"></span>

                    <div class="row-actions" @click.stop>
                        <button class="row-icon-btn" @click="selectedPhraseIndex = idx; activeTab = 'customize';" title="Fine-tune Style">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 010 4m-6 8a2 2 0 100-4m0 4a2 2 0 010-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 010-4m0 4v2m0-6V4"/></svg>
                        </button>
                        <button class="row-icon-btn del" @click="deletePhrase(idx)" :disabled="scenes.length <= 1" title="Delete">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <div class="left-toolbar">
            <button class="tb-btn add-btn" @click="addPhrase()">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Phrase
            </button>
            <button class="tb-btn" @click="resetPhrases()" title="Reset Phrases">Reset</button>
        </div>

        <div class="timeline-section">
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.68rem;">
                <div class="mode-toggle">
                    <button class="mode-btn active">WORD</button>
                    <button class="mode-btn">LINE</button>
                </div>
                <span style="color:rgba(255,255,255,0.4);" x-text="formatTime(currentTime) + ' / ' + formatTime(totalDuration)"></span>
            </div>

            <div class="word-blocks-track">
                <template x-if="scenes[selectedPhraseIndex]">
                    <template x-for="(w, wIdx) in scenes[selectedPhraseIndex].words" :key="wIdx">
                        <span
                            class="word-block"
                            :class="{'emphasized': w.emphasis}"
                            @click="w.emphasis = !w.emphasis; triggerUpdate();"
                            x-text="w.text"
                            title="Click to toggle Emphasis"
                        ></span>
                    </template>
                </template>
            </div>
        </div>
    </div>

    {{-- ==================================================================
       CENTER PANEL — LIVE PREVIEW & PLAYER CONTROLS
       ================================================================== --}}
    <div class="center-panel">
        <div style="width:100%;display:flex;align-items:center;justify-content:space-between;position:relative;">
            <a href="{{ route('app') }}" style="color:rgba(255,255,255,0.5);text-decoration:none;font-size:0.75rem;font-weight:700;">
                ‹ Studio Gallery
            </a>

            {{-- Aspect Ratio Selector Dropdown --}}
            <div style="position:relative;" x-data="{ openRatio: false }">
                <button
                    type="button"
                    @click="openRatio = !openRatio"
                    style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);color:#fff;padding:0.3rem 0.75rem;border-radius:100px;font-size:0.72rem;font-weight:700;display:inline-flex;align-items:center;gap:6px;cursor:pointer;"
                >
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <span x-text="aspectRatioLabel"></span>
                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div
                    x-show="openRatio"
                    @click.away="openRatio = false"
                    x-cloak
                    style="position:absolute;top:110%;left:50%;transform:translateX(-50%);z-index:90;background:#181824;border:1px solid rgba(255,255,255,0.15);border-radius:14px;padding:0.75rem;width:240px;box-shadow:0 20px 40px rgba(0,0,0,0.8);display:flex;flex-direction:column;gap:6px;"
                >
                    <span style="font-size:0.65rem;color:rgba(255,255,255,0.5);font-weight:600;margin-bottom:4px;">Set the canvas ratio used by preview & export</span>

                    <template x-for="r in aspectRatios" :key="r.id">
                        <div
                            @click="setRatio(r.id, r.name); openRatio = false;"
                            style="display:flex;align-items:center;justify-content:space-between;padding:7px 10px;border-radius:8px;cursor:pointer;transition:background 0.2s;"
                            :style="aspectRatio === r.id ? 'background:rgba(34,197,94,0.25);border:1px solid rgba(34,197,94,0.4);' : 'background:rgba(255,255,255,0.03);'"
                        >
                            <span style="font-size:0.75rem;font-weight:700;color:#fff;" x-text="r.name + ' (' + r.id + ')'"></span>
                            <span style="font-size:0.65rem;font-weight:800;color:#22c55e;" x-text="r.desc"></span>
                        </div>
                    </template>
                </div>
            </div>

            <label style="font-size:0.7rem;color:rgba(255,255,255,0.5);cursor:pointer;display:flex;align-items:center;gap:4px;">
                <input type="checkbox" x-model="showSafeZone" style="accent-color:#22c55e;">
                Reels Safe Zone
            </label>
        </div>

        <div class="player-container">
            <div class="video-viewport" style="background: {{ $template->background_color }};">
                <div id="editor-preview-stage" class="kte-stage"></div>
            </div>

            <input type="range" min="0" :max="totalDuration" step="0.05" class="seeker-bar" x-model.number="currentTime" @input="seekTime()">

            <div class="player-controls">
                <button class="ctrl-btn" @click="togglePlay()">
                    <template x-if="!isPlaying"><svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></template>
                    <template x-if="isPlaying"><svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg></template>
                </button>
                <span style="font-size:0.65rem;color:rgba(255,255,255,0.6);font-weight:700;" x-text="formatTime(currentTime) + ' / ' + formatTime(totalDuration)"></span>
                <button class="ctrl-btn" @click="isMuted = !isMuted">🔊</button>
            </div>
        </div>
        <div></div>
    </div>

    {{-- ==================================================================
       RIGHT PANEL — TEMPLATES, CUSTOMIZE, EMPHASIS, MOTION (CLUCAP EXACT)
       ================================================================== --}}
    <div class="right-panel">
        {{-- Top Tabs --}}
        <div class="tab-header">
            <button class="tab-btn" :class="{'active': activeTab === 'templates'}" @click="activeTab = 'templates'">Templates</button>
            <button class="tab-btn" :class="{'active': activeTab === 'customize'}" @click="activeTab = 'customize'">Customize</button>
            <button class="tab-btn" :class="{'active': activeTab === 'emphasis'}" @click="activeTab = 'emphasis'">Emphasis</button>
            <button class="tab-btn" :class="{'active': activeTab === 'motion'}" @click="activeTab = 'motion'">Motion</button>
        </div>

        <div class="right-body">
            
            {{-- ================= TAB 1: TEMPLATES ================= --}}
            <template x-if="activeTab === 'templates'">
                <div style="display:flex;flex-direction:column;gap:0.85rem;">
                    
                    <div class="sub-tabs">
                        <button class="sub-tab-btn" :class="{'active': subTab === 'builtin'}" @click="subTab = 'builtin'">Built-in Templates</button>
                        <button class="sub-tab-btn" :class="{'active': subTab === 'presets'}" @click="subTab = 'presets'">My Presets</button>
                    </div>

                    <div class="search-save-bar">
                        <input type="text" class="search-input" x-model="searchQuery" placeholder="Find a template...">
                        <button class="save-preset-btn" @click="saveCustomPreset()">💾 Save preset</button>
                    </div>

                    {{-- Category Filter Pills Bar --}}
                    <div style="display:flex;gap:4px;overflow-x:auto;padding-bottom:4px;" x-show="subTab === 'builtin'">
                        <template x-for="cat in ['All', 'Bold & Shadow', 'Elegant', 'Playful', 'Minimal', 'Emphasis/Sync', 'Textured/Grunge', 'Personal']" :key="cat">
                            <button
                                class="sub-tab-btn"
                                style="white-space:nowrap;font-size:0.62rem;padding:3px 8px;"
                                :class="{'active': selectedCategory === cat}"
                                @click="selectedCategory = cat"
                                x-text="cat"
                            ></button>
                        </template>
                    </div>

                    <div class="gallery-grid">
                        <template x-for="tpl in filteredTemplates" :key="tpl.id">
                            <div style="display:flex;flex-direction:column;gap:0.5rem;">
                                {{-- Template Card --}}
                                <div
                                    class="tpl-card"
                                    :class="{'selected': scenes[selectedPhraseIndex]?.style?.template_id === tpl.id}"
                                    :style="'background:' + tpl.bg + '; color:' + tpl.color + ';'"
                                    @click="applyTemplateToPhrase(selectedPhraseIndex, tpl)"
                                >
                                    <div class="tpl-header-row">
                                        <span class="tpl-card-title" x-text="tpl.name"></span>
                                        <div class="tpl-tags">
                                            <span class="tpl-badge-new">New</span>
                                            <template x-for="t in tpl.tags" :key="t">
                                                <span class="tpl-tag-badge" x-text="t"></span>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    {{-- Render exact Template Card Preview --}}
                                    <div :style="tpl.previewStyle" x-html="tpl.previewHtml"></div>
                                </div>

                                {{-- COLORS Quick Panel (Matches Screenshot 1) --}}
                                <template x-if="scenes[selectedPhraseIndex]?.style?.template_id === tpl.id">
                                    <div class="colors-quick-panel">
                                        <span style="font-size:0.65rem;font-weight:800;color:rgba(255,255,255,0.5);letter-spacing:0.05em;">COLORS</span>

                                        <div class="color-row">
                                            <span class="color-label">Text Color</span>
                                            <div class="color-picker-wrapper">
                                                <input type="color" class="color-swatch-input" x-model="scenes[selectedPhraseIndex].style.color" @input="triggerUpdate()">
                                                <input type="text" class="color-hex-text" x-model="scenes[selectedPhraseIndex].style.color" @input="triggerUpdate()">
                                            </div>
                                        </div>

                                        <div class="color-row">
                                            <span class="color-label">Shadow Color</span>
                                            <div class="color-picker-wrapper">
                                                <input type="color" class="color-swatch-input" x-model="scenes[selectedPhraseIndex].style.shadow_color" @input="triggerUpdate()">
                                                <input type="text" class="color-hex-text" x-model="scenes[selectedPhraseIndex].style.shadow_color" @input="triggerUpdate()">
                                            </div>
                                        </div>

                                        <div class="color-row">
                                            <span class="color-label">Background Color</span>
                                            <div class="color-picker-wrapper">
                                                <input type="color" class="color-swatch-input" x-model="scenes[selectedPhraseIndex].style.background_color" @input="triggerUpdate()">
                                                <input type="text" class="color-hex-text" x-model="scenes[selectedPhraseIndex].style.background_color" @input="triggerUpdate()">
                                            </div>
                                        </div>

                                        <div style="display:flex;gap:0.5rem;margin-top:4px;">
                                            <button class="sub-tab-btn" style="background:rgba(255,255,255,0.08);color:#fff;" @click="activeTab = 'customize'">✏️ Edit</button>
                                            <button class="sub-tab-btn" style="background:rgba(239,68,68,0.15);color:#ef4444;" @click="resetPhraseStyle(selectedPhraseIndex)">🗑️ Remove</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- ================= TAB 2: CUSTOMIZE ================= --}}
            <template x-if="activeTab === 'customize'">
                <div style="display:flex;flex-direction:column;gap:0.85rem;">
                    <span style="font-size:0.75rem;font-weight:800;color:#22c55e;">
                        Editing Phrase #<span x-text="selectedPhraseIndex + 1"></span> Style
                    </span>

                    <div style="display:flex;flex-direction:column;gap:0.5rem;">
                        <label style="font-size:0.68rem;font-weight:700;color:rgba(255,255,255,0.5);">FONT FAMILY</label>
                        <select class="cust-select" x-model="scenes[selectedPhraseIndex].style.font" @change="triggerUpdate()">
                            <option value="Poppins-Bold.ttf">Poppins Bold</option>
                            <option value="PlayfairDisplay-Regular.ttf">Playfair Display</option>
                            <option value="Fraunces-Black.ttf">Fraunces Heavy</option>
                            <option value="DancingScript-Bold.ttf">Dancing Script</option>
                            <option value="ArchivoBlack-Regular.ttf">Archivo Black</option>
                            <option value="Oswald-Bold.ttf">Oswald Bold</option>
                            <option value="BebasNeue-Regular.ttf">Bebas Neue</option>
                            <option value="CinzelDecorative-Regular.ttf">Cinzel Decorative</option>
                            <option value="CourierPrime-Regular.ttf">Courier Monospace</option>
                            <option value="Caveat-Regular.ttf">Caveat Script</option>
                            <option value="Rajdhani-Bold.ttf">Rajdhani Bold</option>
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:0.5rem;">
                        <label style="font-size:0.68rem;font-weight:700;color:rgba(255,255,255,0.5);">ALIGNMENT</label>
                        <div class="sub-tabs">
                            <button class="sub-tab-btn" :class="{'active': scenes[selectedPhraseIndex].style.align === 'left'}" @click="scenes[selectedPhraseIndex].style.align = 'left'; triggerUpdate();">Left</button>
                            <button class="sub-tab-btn" :class="{'active': scenes[selectedPhraseIndex].style.align === 'center'}" @click="scenes[selectedPhraseIndex].style.align = 'center'; triggerUpdate();">Center</button>
                            <button class="sub-tab-btn" :class="{'active': scenes[selectedPhraseIndex].style.align === 'right'}" @click="scenes[selectedPhraseIndex].style.align = 'right'; triggerUpdate();">Right</button>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:0.5rem;">
                        <label style="font-size:0.68rem;font-weight:700;color:rgba(255,255,255,0.5);">POSITION</label>
                        <div class="sub-tabs">
                            <button class="sub-tab-btn" :class="{'active': scenes[selectedPhraseIndex].style.vertical_position === 'top'}" @click="scenes[selectedPhraseIndex].style.vertical_position = 'top'; triggerUpdate();">Top</button>
                            <button class="sub-tab-btn" :class="{'active': scenes[selectedPhraseIndex].style.vertical_position === 'center'}" @click="scenes[selectedPhraseIndex].style.vertical_position = 'center'; triggerUpdate();">Center</button>
                            <button class="sub-tab-btn" :class="{'active': scenes[selectedPhraseIndex].style.vertical_position === 'bottom'}" @click="scenes[selectedPhraseIndex].style.vertical_position = 'bottom'; triggerUpdate();">Bottom</button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- ================= TAB 3: EMPHASIS ================= --}}
            <template x-if="activeTab === 'emphasis'">
                <div style="display:flex;flex-direction:column;gap:0.85rem;">
                    
                    <div class="sub-tabs">
                        <button class="sub-tab-btn" :class="{'active': emphasisSubTab === 'highlight'}" @click="emphasisSubTab = 'highlight'">Highlight & Size</button>
                        <button class="sub-tab-btn" :class="{'active': emphasisSubTab === 'focus'}" @click="emphasisSubTab = 'focus'">Focus</button>
                    </div>

                    {{-- Word Size & Emphasis Selector Grid --}}
                    <div style="display:flex;flex-direction:column;gap:0.5rem;background:rgba(255,255,255,0.03);padding:0.75rem;border-radius:10px;border:1px solid rgba(255,255,255,0.08);">
                        <span style="font-size:0.7rem;font-weight:800;color:#facc15;">PER-WORD SIZE VARIATION</span>
                        <p style="font-size:0.65rem;color:rgba(255,255,255,0.5);margin-bottom:4px;">Click any word to change its font size tier (Small / Medium / Large / XL):</p>

                        <div style="display:flex;flex-direction:column;gap:6px;">
                            <template x-if="scenes[selectedPhraseIndex]">
                                <template x-for="(w, wIdx) in scenes[selectedPhraseIndex].words" :key="wIdx">
                                    <div style="display:flex;align-items:center;justify-content:space-between;background:rgba(0,0,0,0.3);padding:4px 8px;border-radius:6px;">
                                        <span style="font-size:0.75rem;font-weight:700;color:#fff;" x-text="w.text"></span>
                                        <div class="sub-tabs" style="width:auto;gap:2px;">
                                            <button class="sub-tab-btn" style="padding:2px 6px;font-size:0.6rem;" :class="{'active': w.size === 'small'}" @click="w.size = 'small'; triggerUpdate();">Small (0.6x)</button>
                                            <button class="sub-tab-btn" style="padding:2px 6px;font-size:0.6rem;" :class="{'active': w.size === 'medium' || !w.size}" @click="w.size = 'medium'; triggerUpdate();">Med (1.0x)</button>
                                            <button class="sub-tab-btn" style="padding:2px 6px;font-size:0.6rem;" :class="{'active': w.size === 'large'}" @click="w.size = 'large'; triggerUpdate();">Large (1.45x)</button>
                                            <button class="sub-tab-btn" style="padding:2px 6px;font-size:0.6rem;background:rgba(234,179,8,0.2);color:#facc15;" :class="{'active': w.size === 'xl'}" @click="w.size = 'xl'; triggerUpdate();">XL (1.9x)</button>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>

                    {{-- Accordion 1: FONTS --}}
                    <div class="accordion-box">
                        <div class="accordion-head">
                            <span>∨ FONTS</span>
                        </div>
                        <div class="accordion-body">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:0.7rem;color:rgba(255,255,255,0.7);">Font Family</span>
                                <select class="cust-select" style="width:60%;" x-model="emphasisFont" @change="triggerUpdate()">
                                    <option value="Inter">Inter</option>
                                    <option value="Poppins">Poppins</option>
                                    <option value="Roboto">Roboto</option>
                                    <option value="Dancing Script">Dancing Script</option>
                                </select>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:0.7rem;color:rgba(255,255,255,0.7);">Size</span>
                                <input type="range" min="12" max="96" class="seeker-bar" style="width:50%;" x-model="emphasisFontSize" @input="triggerUpdate()">
                                <span style="font-size:0.7rem;font-weight:800;" x-text="emphasisFontSize"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Accordion 2: COLOR --}}
                    <div class="accordion-box">
                        <div class="accordion-head">
                            <span>∨ COLOR</span>
                        </div>
                        <div class="accordion-body">
                            <div class="color-row">
                                <span class="color-label">Text Color</span>
                                <div class="color-picker-wrapper">
                                    <input type="color" class="color-swatch-input" x-model="emphasisColor" @input="triggerUpdate()">
                                    <input type="text" class="color-hex-text" x-model="emphasisColor" @input="triggerUpdate()">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Accordion 3: SPACING --}}
                    <div class="accordion-box">
                        <div class="accordion-head">
                            <span>∨ SPACING</span>
                        </div>
                        <div class="accordion-body">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:0.7rem;color:rgba(255,255,255,0.7);">Letter Gap</span>
                                <input type="range" min="0" max="20" class="seeker-bar" style="width:50%;" x-model="emphasisLetterGap" @input="triggerUpdate()">
                                <span style="font-size:0.7rem;font-weight:800;" x-text="emphasisLetterGap"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- ================= TAB 4: MOTION (Clucap Screenshot 5 Match) ================= --}}
            <template x-if="activeTab === 'motion'">
                <div style="display:flex;flex-direction:column;gap:0.85rem;">
                    
                    {{-- Scope Sub Tabs --}}
                    <div class="sub-tabs">
                        <button class="sub-tab-btn" :class="{'active': motionScope === 'line'}" @click="motionScope = 'line'; triggerUpdate();">Line</button>
                        <button class="sub-tab-btn" :class="{'active': motionScope === 'word'}" @click="motionScope = 'word'; triggerUpdate();">Word</button>
                        <button class="sub-tab-btn" :class="{'active': motionScope === 'char'}" @click="motionScope = 'char'; triggerUpdate();">Char</button>
                    </div>

                    <span style="font-size:0.65rem;color:#22c55e;font-weight:700;">Motion applies on <span x-text="'each ' + motionScope"></span></span>

                    <div style="display:flex;flex-direction:column;gap:0.4rem;">
                        <div style="display:flex;justify-content:space-between;font-size:0.68rem;font-weight:800;color:rgba(255,255,255,0.5);">
                            <span>STRENGTH</span>
                            <span x-text="motionStrength"></span>
                        </div>
                        <input type="range" min="0" max="100" class="seeker-bar" x-model="motionStrength" @input="triggerUpdate()">
                    </div>

                    <div style="display:flex;flex-direction:column;gap:0.4rem;">
                        <div style="display:flex;justify-content:space-between;font-size:0.68rem;font-weight:800;color:rgba(255,255,255,0.5);">
                            <span>SPEED</span>
                            <span x-text="motionSpeed + ' ms'"></span>
                        </div>
                        <input type="range" min="100" max="1000" step="50" class="seeker-bar" x-model="motionSpeed" @input="triggerUpdate()">
                    </div>

                    {{-- Motion Grid Cards (18 Clucap Motion Cards) --}}
                    <div class="motion-grid">
                        <template x-for="m in motionCards" :key="m.id">
                            <div
                                class="motion-card"
                                :class="{'active': scenes[selectedPhraseIndex]?.style?.animation === m.id}"
                                @click="scenes[selectedPhraseIndex].style.animation = m.id; triggerUpdate();"
                            >
                                <span class="motion-card-title">Clucap Motion</span>
                                <span class="motion-card-sub" x-text="m.name"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Free AI Voiceover Box (Self-Hosted VEXYL-TTS / AI4Bharat) --}}
            <div style="background:rgba(255,255,255,0.03);padding:0.85rem;border-radius:12px;border:1px solid rgba(255,255,255,0.08);display:flex;flex-direction:column;gap:0.5rem;margin-top:1rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:0.72rem;font-weight:800;color:#22c55e;">🎙 FREE AI VOICEOVER (SELF-HOSTED)</span>
                    <span style="font-size:0.6rem;color:rgba(255,255,255,0.4);">No API Cost</span>
                </div>
                
                <div style="display:flex;gap:6px;">
                    <select class="cust-select" style="font-size:0.68rem;" x-model="ttsVoice">
                        <option value="hi_female">Hindi / Hinglish Female (AI4Bharat Warm & Natural)</option>
                        <option value="hi_male">Hindi / Hinglish Male (AI4Bharat Confident)</option>
                        <option value="chatterbox_hindi">Hindi / Hinglish (Resemble AI Chatterbox)</option>
                        <option value="hi_energetic">Hindi Energetic (Upbeat Cadence)</option>
                        <option value="en_natural">English Natural Voice</option>
                    </select>
                    <button class="save-preset-btn" style="white-space:nowrap;background:#22c55e;color:#000;font-weight:800;" @click="generateVoiceover()" :disabled="isGeneratingVoice">
                        <span x-text="isGeneratingVoice ? 'Generating...' : '🎙 Generate Audio'"></span>
                    </button>
                </div>

                <template x-if="voiceoverUrl">
                    <div style="display:flex;flex-direction:column;gap:4px;background:rgba(34,197,94,0.1);padding:6px 10px;border-radius:6px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <audio controls :src="voiceoverUrl" style="height:28px;width:100%;"></audio>
                            <button style="color:#ef4444;background:none;border:none;cursor:pointer;font-weight:800;" @click="voiceoverUrl = null; voiceoverPath = null; ttsEngineLabel = null;">✕</button>
                        </div>
                        <span style="font-size:0.6rem;color:#22c55e;font-weight:700;" x-text="ttsEngineLabel || '⚡ Powered by AI4Bharat Natural Voice'"></span>
                    </div>
                </template>
            </div>

            {{-- Export Button in Right Panel --}}
            <button class="export-panel-btn" @click="exportVideo()" style="margin-top:0.75rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export MP4 Video (With Audio)
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
/* ====================================================================
   3-PANEL EDITOR ALPINE COMPONENT (CLUCAP SPECIFICATION MATCH)
   ==================================================================== */
function editorApp() {
    return {
        templateId: {{ $template->id }},
        templateSlug: '{{ $template->slug }}',
        primaryColor: '{{ $template->primary_color }}',
        secondaryColor: '{{ $template->secondary_color }}',
        defaultFont: '{{ $template->config_json["ffmpeg_font"] ?? "Poppins-Bold.ttf" }}',
        defaultAnim: '{{ $template->animation_type }}',

        activeTab: 'templates',
        subTab: 'builtin',
        searchQuery: '',
        selectedCategory: 'All',
        showSafeZone: true,

        emphasisSubTab: 'highlight',
        emphasisFont: 'Inter',
        emphasisFontSize: 44,
        emphasisColor: '#a3e635',
        emphasisLetterGap: 0,

        ttsVoice: 'hi_female',
        isGeneratingVoice: false,
        voiceoverUrl: null,
        voiceoverPath: null,
        ttsEngineLabel: null,

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
            const viewport = document.querySelector('.video-viewport');
            if (viewport) {
                viewport.style.aspectRatio = ratioCss;
            }
        },

        motionScope: 'word',
        motionStrength: 100,
        motionSpeed: 400,

        selectedPhraseIndex: 0,
        scenes: [],
        myPresets: [],

        currentTime: 0.0,
        totalDuration: 5.0,
        isPlaying: false,
        isMuted: false,

        playInterval: null,
        debounceT: null,

        /* Motion Cards Grid (30 High-Energy Trending Reel Motion Effects) */
        motionCards: [
            { id:'none', name:'● None' },
            { id:'fade', name:'● Fade' },
            { id:'pop', name:'● Pop' },
            { id:'slide', name:'● Slide' },
            { id:'diagonal_slide', name:'● Diagonal Slide' },
            { id:'zoom', name:'● Zoom' },
            { id:'pulse', name:'● Pulse' },
            { id:'spin_in', name:'● Spin In' },
            { id:'pendulum', name:'● Pendulum' },
            { id:'spring_bounce', name:'● Spring Bounce' },
            { id:'elastic_snap', name:'● Elastic Snap' },
            { id:'wipe_reveal', name:'● Wipe Reveal' },
            { id:'gravity_drop', name:'● Gravity Drop' },
            { id:'iris_open', name:'● Iris Open' },
            { id:'punch_zoom', name:'● Punch Zoom' },
            { id:'hook_snap', name:'● Hook Snap' },
            { id:'ripple', name:'● Ripple' },
            { id:'typewriter', name:'● Typewriter' },
            { id:'flip_3d', name:'🔥 3D Flip In' },
            { id:'glitch_jitter', name:'🔥 Glitch Jitter' },
            { id:'word_wave', name:'🔥 Word Wave' },
            { id:'char_typewriter', name:'🔥 Char Typewriter' },
            { id:'elastic_bounce', name:'🔥 Elastic Bounce' },
            { id:'diagonal_fly', name:'🔥 Diagonal Fly In' },
            { id:'blur_speed', name:'🔥 Blur Speed Zoom' },
            { id:'card_flip_3d', name:'🔥 3D Card Flip' },
            { id:'shatter_scale', name:'🔥 Shatter Scale' },
            { id:'curtain_reveal', name:'🔥 Curtain Reveal' },
            { id:'text_strobe', name:'🔥 Text Strobe' },
            { id:'magnet_snap', name:'🔥 Magnet Snap' },
        ],

        /* 18 Expanded Templates matching database TemplateSeeder slugs */
        builtInTemplates: [
            {
                id: 'neonsplash', name: 'NEONSPLASH', badge: 'New', tags: ['SPLASH', 'CENTER'], category: 'Bold & Shadow',
                font: 'ArchivoBlack-Regular.ttf', color: '#CCFF00', shadow_color: '#000000', background_color: '#101010', animation: 'neonsplash', bg: '#101010',
                previewHtml: '<div style="font-size:0.75rem;color:#fff;">Hello and</div><div style="font-size:1.25rem;font-weight:900;color:#CCFF00;text-shadow:0 0 12px rgba(204,255,0,0.6);">WELCOME</div><div style="font-size:0.65rem;color:#CCFF00;">to NeonSplash.</div>'
            },
            {
                id: 'dual-tone-script', name: 'DUAL TONE SCRIPT', badge: 'New', tags: ['GRADIENT', 'SCRIPT'], category: 'Elegant',
                font: 'DancingScript-Bold.ttf', color: '#C77DFF', secondary_color: '#FF6FD8', shadow_color: 'transparent', background_color: '#111827', animation: 'script-serif-combo', bg: '#111827',
                previewHtml: '<div style="font-size:0.75rem;color:#fff;">Lekin ye baat</div><div style="font-size:1.2rem;font-weight:900;background:linear-gradient(135deg,#C77DFF,#FF6FD8);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">dil se kahi</div>'
            },
            {
                id: 'yellow-shadow', name: 'YELLOW SHADOW', badge: 'New', tags: ['SHADOW', 'BOLD'], category: 'Bold & Shadow',
                font: 'Poppins-Bold.ttf', color: '#FFD60A', shadow_color: '#000000', background_color: '#0f172a', animation: 'bounce-baseline', bg: '#0f172a',
                previewHtml: '<div style="font-size:1.25rem;font-weight:900;color:#FFD60A;text-shadow:4px 4px 0px #000;">YELLOW SHADOW</div>'
            },
            {
                id: 'golden-aura', name: 'GOLDEN AURA', badge: 'New', tags: ['GOLD', 'GRADIENT', 'GLOW'], category: 'Elegant',
                font: 'CinzelDecorative-Regular.ttf', color: '#FFD700', shadow_color: '#B8860B', background_color: '#1a0010', animation: 'center-glow-focus', bg: '#1a0010',
                previewHtml: '<div style="font-size:1.2rem;font-weight:900;color:#FFD700;text-shadow:0 0 15px #FFD700, 0 0 30px #B8860B;">GOLDEN AURA</div>'
            },
            {
                id: 'glass-blur', name: 'GLASS BLUR', tags: ['FROSTED', 'MINIMAL'], category: 'Minimal',
                font: 'Poppins-Bold.ttf', color: '#FFFFFF', shadow_color: 'transparent', background_color: '#090d16', animation: 'liquid-glass', bg: '#090d16',
                previewHtml: '<div style="font-size:0.7rem;color:#ccc;">Simple aur</div><div style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;padding:4px 12px;border-radius:100px;font-weight:800;display:inline-block;backdrop-filter:blur(5px);">CLEAN</div><div style="font-size:0.65rem;color:#ccc;">lagta hai</div>'
            },
            {
                id: 'retro-vhs', name: 'RETRO VHS', tags: ['RETRO', 'GLITCH'], category: 'Textured/Grunge',
                font: 'Rajdhani-Bold.ttf', color: '#00F5FF', shadow_color: '#FF00FF', background_color: '#0a0a0f', animation: 'neon-glow', bg: '#0a0a0f',
                previewHtml: '<div style="font-size:1.3rem;font-weight:900;color:#00F5FF;text-shadow:2px 0 #FF00FF,-2px 0 #00F5FF;">REWIND 90\'s vibe</div>'
            },
            {
                id: 'comic-pop', name: 'COMIC POP', tags: ['BOLD', 'PLAYFUL'], category: 'Playful',
                font: 'BebasNeue-Regular.ttf', color: '#FFD166', shadow_color: '#000000', background_color: '#18181b', animation: 'all-caps-bold-display', bg: '#18181b',
                previewHtml: '<div style="font-size:0.75rem;color:#fff;">Wait for it...</div><div style="font-size:1.4rem;font-weight:900;color:#FFD166;-webkit-text-stroke:1.5px #000;text-shadow:3px 3px 0 #000;">BOOM!</div>'
            },
            {
                id: 'minimal-mono', name: 'MINIMAL MONO', tags: ['CLEAN', 'MODERN'], category: 'Minimal',
                font: 'CourierPrime-Regular.ttf', color: '#FFFFFF', shadow_color: 'transparent', background_color: '#0a0a0a', animation: 'typewriter', bg: '#0a0a0a',
                previewHtml: '<div style="font-size:0.7rem;color:#888;">note to self:</div><div style="font-size:1.1rem;font-weight:900;color:#fff;font-family:monospace;">stay consistent</div>'
            },
            {
                id: 'highlight-marker', name: 'HIGHLIGHT MARKER', badge: 'Popular', tags: ['EMPHASIS', 'POPULAR'], category: 'Emphasis/Sync',
                font: 'Poppins-Bold.ttf', color: '#000000', shadow_color: 'transparent', background_color: '#FFE66D', animation: 'highlight_marker', bg: '#12121c',
                previewHtml: '<div style="font-size:0.75rem;color:#fff;">This is</div><div style="background:#FFE66D;color:#000;padding:2px 10px;border-radius:4px;font-weight:900;display:inline-block;">IMPORTANT</div><div style="font-size:0.65rem;color:#fff;">read again</div>'
            },
            {
                id: 'karaoke-bounce', name: 'KARAOKE BOUNCE', badge: 'Popular', tags: ['SYNC', 'POPULAR'], category: 'Emphasis/Sync',
                font: 'Poppins-Bold.ttf', color: '#FFFFFF', secondary_color: '#A3E635', shadow_color: 'transparent', background_color: '#0d0d16', animation: 'karaoke-bounce', bg: '#0d0d16',
                previewHtml: '<div><span style="color:#A3E635;font-weight:900;">WORD</span> <span style="color:#fff;">BY WORD HIGHLIGHT</span></div>'
            },
            {
                id: 'glitch-cyber', name: 'GLITCH CYBER', tags: ['GLITCH', 'BOLD'], category: 'Bold & Shadow',
                font: 'Rajdhani-Bold.ttf', color: '#00F5FF', shadow_color: '#FF0055', background_color: '#030712', animation: 'neon-glow', bg: '#030712',
                previewHtml: '<div style="font-size:1.3rem;font-weight:900;color:#00F5FF;text-shadow:-2px 0 #FF0055, 2px 0 #00F5FF;">SYSTEM ONLINE</div>'
            },
            {
                id: 'bubble-pop', name: 'BUBBLE POP', tags: ['PLAYFUL', 'ROUNDED'], category: 'Playful',
                font: 'Caveat-Regular.ttf', color: '#FFADAD', shadow_color: 'transparent', background_color: '#1a0515', animation: 'bounce-baseline', bg: '#1a0515',
                previewHtml: '<div style="font-size:0.75rem;color:#FFD6A5;">Aaj kal</div><div style="font-size:1.4rem;font-weight:900;color:#CAFFBF;">MAST</div><div style="font-size:0.75rem;color:#FFADAD;">mood hai</div>'
            },
            {
                id: 'ink-splatter', name: 'INK SPLATTER', tags: ['GRUNGE', 'TEXTURED'], category: 'Textured/Grunge',
                font: 'Fraunces-Black.ttf', color: '#E63946', shadow_color: '#000000', background_color: '#0a0505', animation: 'ink-splatter', bg: '#0a0505',
                previewHtml: '<div style="font-size:1.3rem;font-weight:900;color:#E63946;letter-spacing:2px;text-shadow:3px 3px 0 #000;">DHANDOPAYAM</div>'
            },
            {
                id: 'chrome-metallic', name: 'CHROME METALLIC', tags: ['PREMIUM', '3D'], category: 'Bold & Shadow',
                font: 'Oswald-Bold.ttf', color: '#E2E8F0', shadow_color: '#000000', background_color: '#0f172a', animation: 'chrome-metallic', bg: '#0f172a',
                previewHtml: '<div style="font-size:1.4rem;font-weight:900;background:linear-gradient(180deg,#FFFFFF,#94A3B8);-webkit-background-clip:text;-webkit-text-fill-color:transparent;filter:drop-shadow(2px 2px 4px #000);">PREMIUM</div>'
            },
            {
                id: 'y2k-sparkle', name: 'Y2K SPARKLE', tags: ['TRENDY', 'GLOW'], category: 'Playful',
                font: 'DancingScript-Bold.ttf', color: '#FF007F', shadow_color: '#E0E0E0', background_color: '#1a0015', animation: 'y2k-sparkle', bg: '#1a0015',
                previewHtml: '<div style="font-size:0.75rem;color:#fff;">That</div><div style="font-size:1.3rem;font-weight:900;color:#FF007F;text-shadow:0 0 15px #FF007F;">✨ Y2K ✨</div><div style="font-size:0.75rem;color:#fff;">energy</div>'
            },
            {
                id: 'elegant-gold-serif', name: 'ELEGANT GOLD SERIF', tags: ['LUXURY', 'SERIF'], category: 'Elegant',
                font: 'PlayfairDisplay-Regular.ttf', color: '#F5E6D3', secondary_color: '#D4AF37', shadow_color: 'transparent', background_color: '#1a0a00', animation: 'script-serif-combo', bg: '#1a0a00',
                previewHtml: '<div style="font-size:0.75rem;color:#F5E6D3;">Timeless and</div><div style="font-size:1.3rem;font-weight:900;color:#D4AF37;letter-spacing:2px;">ELEGANT</div>'
            },
            {
                id: 'confetti-burst', name: 'CONFETTI BURST', tags: ['CELEBRATION', 'PLAYFUL'], category: 'Playful',
                font: 'ArchivoBlack-Regular.ttf', color: '#FFCC00', secondary_color: '#00FFCC', shadow_color: 'transparent', background_color: '#1e1b4b', animation: 'confetti-burst', bg: '#1e1b4b',
                previewHtml: '<div style="font-size:1.2rem;font-weight:900;color:#FFCC00;text-shadow:0 0 10px #00FFCC;">🎉 CONGRATULATIONS!</div>'
            },
            {
                id: 'handwritten-note', name: 'HANDWRITTEN NOTE', tags: ['PERSONAL', 'CASUAL'], category: 'Personal',
                font: 'Caveat-Regular.ttf', color: '#1D4ED8', shadow_color: 'transparent', background_color: '#F8FAFC', animation: 'typewriter', bg: '#F8FAFC',
                previewHtml: '<div style="font-size:0.75rem;color:#475569;">just a thought...</div><div style="font-size:1.3rem;font-weight:900;color:#1D4ED8;">be kind today</div>'
            },
        ],

        init() {
            const initialText = {!! json_encode($initialText) !!};
            this.buildInitialScenes(initialText);

            try {
                this.myPresets = JSON.parse(localStorage.getItem('typographic_my_presets') || '[]');
            } catch(e) {}

            this.$nextTick(() => {
                this.startLivePlayer();
            });
        },

        buildInitialScenes(text) {
            let cleaned = (text || '').replace(/^["']|["']$/g, '').trim();
            let lines = cleaned.split(/\r?\n/).map(l => l.trim()).filter(Boolean);
            if (!lines.length) lines = [cleaned];

            const phrases = [];
            lines.forEach(line => {
                const words = line.split(/\s+/).filter(Boolean);
                const CHUNK = 5;
                for (let i = 0; i < words.length; i += CHUNK) {
                    phrases.push(words.slice(i, i + CHUNK).join(' '));
                }
            });

            const stopwords = ['ki', 'ko', 'hai', 'mein', 'aur', 'se', 'nahi', 'hi', 'to', 'bhi', 'par', 'pe', 'ke', 'ka', 'me', 'ne', 'is', 'a', 'the', 'and', 'or', 'in', 'to', 'of', 'for', 'with', 'on', 'at', 'by', 'an', 'be', 'it', 'was', 'are'];

            let cumulativeStart = 0.0;
            this.scenes = phrases.map((txt, i) => {
                const duration = 2.5;
                const start = cumulativeStart;
                const end = start + duration;
                cumulativeStart = end;

                const rawWords = txt.split(/\s+/);

                // Find longest non-stopword for xl hook word size
                let longestIdx = -1;
                let maxLen = 0;
                rawWords.forEach((w, idx) => {
                    const clean = w.toLowerCase().replace(/[^a-z0-9]/g, '');
                    if (!stopwords.includes(clean) && clean.length > maxLen) {
                        maxLen = clean.length;
                        longestIdx = idx;
                    }
                });

                const wordsArr = rawWords.map((w, wIdx, arr) => {
                    const step = duration / arr.length;
                    const clean = w.toLowerCase().replace(/[^a-z0-9]/g, '');
                    
                    let sizeTier = 'medium';
                    if (wIdx === longestIdx) {
                        sizeTier = 'xl';
                    } else if (stopwords.includes(clean)) {
                        sizeTier = 'small';
                    } else if (wIdx % 2 === 0) {
                        sizeTier = 'large';
                    } else {
                        sizeTier = 'medium';
                    }

                    return {
                        text: w,
                        start: start + wIdx * step,
                        end: start + (wIdx + 1) * step,
                        size: sizeTier,
                        emphasis: (sizeTier === 'xl' || sizeTier === 'large'),
                    };
                });

                return {
                    id: 'scene_' + Date.now() + '_' + i,
                    text: txt,
                    start: start,
                    end: end,
                    duration: duration,
                    style: {
                        template_id: this.templateSlug,
                        font: this.defaultFont,
                        color: this.primaryColor,
                        shadow_color: '#000000',
                        background_color: '{{ $template->background_color }}',
                        animation: this.defaultAnim,
                        vertical_position: 'center',
                        align: 'center',
                    },
                    words: wordsArr,
                    image: null,
                };
            });

            this.calcTotalDuration();
        },

        get filteredTemplates() {
            let list = (this.subTab === 'presets') ? this.myPresets : this.builtInTemplates;
            if (this.selectedCategory !== 'All' && this.subTab !== 'presets') {
                list = list.filter(t => t.category === this.selectedCategory);
            }
            if (!this.searchQuery.trim()) return list;
            const q = this.searchQuery.toLowerCase();
            return list.filter(t => t.name.toLowerCase().includes(q) || (t.tags && t.tags.some(tg => tg.toLowerCase().includes(q))));
        },

        calcTotalDuration() {
            let dur = 0.0;
            this.scenes.forEach(s => dur += (parseFloat(s.duration) || 2.5));
            this.totalDuration = dur;
            return dur.toFixed(1);
        },

        /* ===== CORE FIX: Independent Value Copy Template Application ===== */
        applyTemplateToPhrase(idx, tpl) {
            if (!this.scenes[idx]) return;

            // Deep clone template properties into scenes[idx].style ONLY
            this.scenes[idx].style = {
                template_id: tpl.id,
                font: tpl.font,
                color: tpl.color,
                shadow_color: tpl.shadow_color || '#000000',
                background_color: tpl.background_color || 'transparent',
                animation: tpl.animation || 'zoom',
                vertical_position: 'center',
                align: 'center',
            };

            // Force stage DOM re-render immediately
            const stage = document.getElementById('editor-preview-stage');
            if (stage) delete stage.dataset.activeKey;

            this.currentTime = this.scenes[idx].start || 0;
            this.renderCurrentFrame();
        },

        resetPhraseStyle(idx) {
            if (!this.scenes[idx]) return;
            this.scenes[idx].style.color = '#FFFFFF';
            this.scenes[idx].style.shadow_color = '#000000';
            this.scenes[idx].style.background_color = 'transparent';
            this.triggerUpdate();
        },

        saveCustomPreset() {
            if (!this.scenes[this.selectedPhraseIndex]) return;
            const currStyle = this.scenes[this.selectedPhraseIndex].style;
            const name = prompt('Enter preset name:', 'My Clucap Style');
            if (!name) return;

            const preset = {
                id: 'preset_' + Date.now(),
                name: name.toUpperCase(),
                tags: ['CUSTOM'],
                font: currStyle.font,
                color: currStyle.color,
                shadow_color: currStyle.shadow_color,
                background_color: currStyle.background_color,
                animation: currStyle.animation,
                bg: '#14141d',
                previewHtml: `<span style="background:${currStyle.background_color};color:${currStyle.color};padding:6px 14px;border-radius:6px;font-weight:900;">${name}</span>`,
            };

            this.myPresets.push(preset);
            localStorage.setItem('typographic_my_presets', JSON.stringify(this.myPresets));
            this.subTab = 'presets';
        },

        addPhrase() {
            const newIdx = this.scenes.length;
            const prevEnd = newIdx > 0 ? this.scenes[newIdx - 1].end : 0;
            const dur = 2.5;

            this.scenes.push({
                id: 'scene_' + Date.now() + '_' + newIdx,
                text: 'New phrase text',
                start: prevEnd,
                end: prevEnd + dur,
                duration: dur,
                style: {
                    template_id: 'pink_brush_blink',
                    font: this.defaultFont,
                    color: '#FFFFFF',
                    shadow_color: '#4A0827',
                    background_color: '#FF2E88',
                    animation: 'spin_in',
                    vertical_position: 'center',
                    align: 'center',
                },
                words: [{ text:'New', start:prevEnd, end:prevEnd+1, emphasis:false }, { text:'phrase', start:prevEnd+1, end:prevEnd+2, emphasis:false }],
                image: null,
            });

            this.selectedPhraseIndex = newIdx;
            this.calcTotalDuration();
            this.triggerUpdate();
        },

        deletePhrase(idx) {
            if (this.scenes.length <= 1) return;
            this.scenes.splice(idx, 1);
            if (this.selectedPhraseIndex >= this.scenes.length) {
                this.selectedPhraseIndex = this.scenes.length - 1;
            }
            this.calcTotalDuration();
            this.triggerUpdate();
        },

        resetPhrases() {
            const initialText = {!! json_encode($initialText) !!};
            this.buildInitialScenes(initialText);
            this.triggerUpdate();
        },

        triggerUpdate() {
            clearTimeout(this.debounceT);
            this.debounceT = setTimeout(() => {
                this.startLivePlayer();
            }, 150);
        },

        /* ===== LIVE PLAYER & GSAP MOTION ENGINE ===== */
        startLivePlayer() {
            clearInterval(this.playInterval);
            this.isPlaying = true;

            this.playInterval = setInterval(() => {
                if (!this.isPlaying) return;
                this.currentTime += 0.05;
                if (this.currentTime >= this.totalDuration) {
                    this.currentTime = 0;
                }
                this.renderCurrentFrame();
            }, 50);
        },

        togglePlay() { this.isPlaying = !this.isPlaying; },
        seekTime() { this.renderCurrentFrame(); },

        syncPlayerToPhrase(idx) {
            if (!this.scenes[idx]) return;
            this.currentTime = this.scenes[idx].start || 0;
            this.renderCurrentFrame();
        },

        renderCurrentFrame() {
            let activeIdx = 0;
            for (let i = 0; i < this.scenes.length; i++) {
                if (this.currentTime >= this.scenes[i].start && this.currentTime <= this.scenes[i].end) {
                    activeIdx = i; break;
                }
            }

            const scene = this.scenes[activeIdx];
            if (!scene) return;

            const stage = document.getElementById('editor-preview-stage');
            if (!stage) return;

            // Only rebuild stage elements if scene or style changed
            const style = scene.style || {};
            const stageKey = `${activeIdx}_${style.template_id}_${style.font}_${style.color}_${style.background_color}_${style.shadow_color}_${style.animation}_${style.align}_${style.vertical_position}_${this.emphasisColor}_${this.emphasisFontSize}`;
            if (stage.dataset.activeKey !== stageKey) {
                stage.dataset.activeKey = stageKey;
                stage.innerHTML = '';

                const container = document.createElement('div');
                container.style.position = 'absolute';
                container.style.inset = '0';
                container.style.display = 'flex';
                container.style.flexDirection = 'column';
                container.style.padding = '8cqi';

                const style = scene.style || {};

                // Align & Position
                if (style.align === 'left') container.style.alignItems = 'flex-start';
                else if (style.align === 'right') container.style.alignItems = 'flex-end';
                else container.style.alignItems = 'center';

                if (style.vertical_position === 'top') container.style.justifyContent = 'flex-start';
                else if (style.vertical_position === 'bottom') container.style.justifyContent = 'flex-end';
                else container.style.justifyContent = 'center';

                const textEl = document.createElement('div');
                textEl.className = 'phrase-main-element';
                textEl.style.color = style.color || '#fff';
                textEl.style.textAlign = style.align || 'center';
                textEl.style.lineHeight = '1.2';
                textEl.style.fontWeight = '900';
                textEl.style.fontSize = (activeIdx === 0) ? 'clamp(24px, 12cqi, 52px)' : 'clamp(20px, 9.5cqi, 44px)';
                textEl.style.letterSpacing = (this.emphasisLetterGap || 0) + 'px';

                // Container typography
                const fontMap = {
                    'Poppins-Bold.ttf': "'Poppins', sans-serif",
                    'PlayfairDisplay-Regular.ttf': "'Playfair Display', serif",
                    'Fraunces-Black.ttf': "'Fraunces', serif",
                    'DancingScript-Bold.ttf': "'Dancing Script', cursive",
                    'ArchivoBlack-Regular.ttf': "'Archivo Black', sans-serif",
                    'Oswald-Bold.ttf': "'Oswald', sans-serif",
                    'BebasNeue-Regular.ttf': "'Bebas Neue', sans-serif",
                    'CinzelDecorative-Regular.ttf': "'Cinzel Decorative', cursive",
                    'CourierPrime-Regular.ttf': "'Courier Prime', monospace",
                    'Caveat-Regular.ttf': "'Caveat', cursive",
                    'Rajdhani-Bold.ttf': "'Rajdhani', sans-serif",
                };
                textEl.style.fontFamily = fontMap[style.font] || (this.emphasisFont ? `'${this.emphasisFont}', sans-serif` : "'Inter', sans-serif");

                // Render words with word-level template styling & per-word font size variation
                if (scene.words && scene.words.length) {
                    const sizeMultipliers = {
                        small: 0.65,
                        medium: 1.0,
                        large: 1.45,
                        xl: 1.9
                    };

                    scene.words.forEach(w => {
                        const span = document.createElement('span');
                        span.className = 'motion-target-word';
                        span.style.display = 'inline-block';
                        span.style.margin = '4px 6px';
                        span.textContent = w.text;

                        const mult = sizeMultipliers[w.size || 'medium'] || 1.0;
                        span.style.fontSize = `calc(1em * ${mult})`;

                        const isKeyword = (w.size === 'xl' || w.size === 'large' || w.emphasis);

                        if (isKeyword) {
                            span.style.fontWeight = '900';

                            // Per-template Keyword Styling Rules (matching NeonSplash & Highlight Marker reference cards)
                            if (style.template_id === 'highlight_marker' || style.template_id === 'highlight-marker') {
                                span.style.background = '#FFE66D';
                                span.style.color = '#000000';
                                span.style.padding = '4px 14px';
                                span.style.borderRadius = '6px';
                                span.style.boxShadow = '0 2px 8px rgba(255,230,109,0.4)';
                            } else if (style.template_id === 'pink_brush_blink' || style.template_id === 'pink-brush-blink') {
                                span.style.background = style.background_color || '#FF2E88';
                                span.style.color = '#FFFFFF';
                                span.style.padding = '4px 14px';
                                span.style.borderRadius = '6px';
                                span.style.boxShadow = `3px 3px 0px ${style.shadow_color || '#4A0827'}`;
                            } else if (style.template_id === 'purple_voice_blink' || style.template_id === 'purple-voice-blink') {
                                span.style.background = style.background_color || '#6366f1';
                                span.style.color = '#FFFFFF';
                                span.style.padding = '6px 18px';
                                span.style.borderRadius = '100px';
                                span.style.boxShadow = '0 0 20px rgba(99,102,241,0.5)';
                            } else if (style.template_id === 'plate_reveal' || style.template_id === 'plate-reveal') {
                                span.style.background = style.background_color || '#FFFFFF';
                                span.style.color = style.color || '#000000';
                                span.style.padding = '4px 14px';
                                span.style.borderRadius = '6px';
                            } else if (style.template_id === 'comic_stroke' || style.template_id === 'comic-stroke') {
                                span.style.webkitTextStroke = '2px #000000';
                                span.style.textShadow = '4px 4px 0px #000000';
                                span.style.color = style.color || '#FFD166';
                            } else if (style.template_id === 'grad_shadow' || style.template_id === 'grad-shadow') {
                                span.style.background = 'linear-gradient(180deg, #f97316, #eab308)';
                                span.style.webkitBackgroundClip = 'text';
                                span.style.webkitTextFillColor = 'transparent';
                                span.style.filter = 'drop-shadow(3px 3px 6px #000000)';
                            } else if (style.template_id === 'liquid_glass' || style.template_id === 'liquid-glass') {
                                span.style.background = 'rgba(255,255,255,0.15)';
                                span.style.border = '1px solid rgba(255,255,255,0.3)';
                                span.style.backdropFilter = 'blur(12px)';
                                span.style.borderRadius = '100px';
                                span.style.padding = '6px 18px';
                                span.style.color = '#FFFFFF';
                            } else if (style.background_color && style.background_color !== 'transparent') {
                                span.style.background = style.background_color;
                                span.style.color = style.color || '#FFFFFF';
                                span.style.padding = '4px 14px';
                                span.style.borderRadius = '6px';
                            } else {
                                span.style.color = w.emphasis ? (this.emphasisColor || '#a3e635') : (style.color || '#FFFFFF');
                                if (w.emphasis) span.style.textShadow = `0 0 15px ${this.emphasisColor || '#a3e635'}`;
                            }
                        } else {
                            // Support / small words
                            span.style.color = style.secondary_color || 'rgba(255,255,255,0.85)';
                            span.style.fontWeight = '600';
                        }
                        textEl.appendChild(span);
                    });
                } else {
                    textEl.textContent = scene.text || ' ';
                }

                container.appendChild(textEl);
                stage.appendChild(container);

                // Trigger GSAP Live Motion Engine
                this.applyGSAPMotion(textEl, style.animation || 'spin_in');
            }
        },

        /* ===== GSAP LIVE MOTION ENGINE ===== */
        applyGSAPMotion(targetEl, animType) {
            if (typeof gsap === 'undefined') return;

            gsap.killTweensOf(targetEl);
            const words = targetEl.querySelectorAll('.motion-target-word');
            const targets = words.length ? words : targetEl;

            const speedSec = (parseFloat(this.motionSpeed) || 400) / 1000;
            const strFactor = (parseFloat(this.motionStrength) || 100) / 100;

            switch (animType) {
                case 'flip_3d':
                    gsap.fromTo(targets, { rotationX: 90 * strFactor, opacity: 0 }, { rotationX: 0, opacity: 1, duration: speedSec, stagger: 0.08, ease: 'power2.out' });
                    break;
                case 'glitch_jitter':
                    gsap.fromTo(targets, { x: () => (Math.random() - 0.5) * 40 * strFactor, opacity: 0 }, { x: 0, opacity: 1, duration: speedSec, stagger: 0.05, ease: 'rough' });
                    break;
                case 'word_wave':
                    gsap.fromTo(targets, { y: 40 * strFactor, scale: 0.8 }, { y: 0, scale: 1, duration: speedSec, stagger: { amount: 0.3, from: "center" }, ease: 'back.out(1.7)' });
                    break;
                case 'char_typewriter':
                    gsap.fromTo(targets, { opacity: 0, scale: 0.5 }, { opacity: 1, scale: 1, duration: speedSec, stagger: 0.05, ease: 'power1.inOut' });
                    break;
                case 'elastic_bounce':
                    gsap.fromTo(targets, { scaleY: 2.0 * strFactor, opacity: 0 }, { scaleY: 1, opacity: 1, duration: speedSec, stagger: 0.07, ease: 'elastic.out(1, 0.4)' });
                    break;
                case 'diagonal_fly':
                    gsap.fromTo(targets, { x: -120 * strFactor, y: -120 * strFactor, rotation: -45, opacity: 0 }, { x: 0, y: 0, rotation: 0, opacity: 1, duration: speedSec, stagger: 0.08, ease: 'back.out(1.4)' });
                    break;
                case 'blur_speed':
                    gsap.fromTo(targets, { filter: 'blur(20px)', scale: 2.5 * strFactor, opacity: 0 }, { filter: 'blur(0px)', scale: 1, opacity: 1, duration: speedSec, stagger: 0.08, ease: 'power3.out' });
                    break;
                case 'card_flip_3d':
                    gsap.fromTo(targets, { rotationY: 180 * strFactor, opacity: 0 }, { rotationY: 0, opacity: 1, duration: speedSec, stagger: 0.09, ease: 'back.out(1.5)' });
                    break;
                case 'shatter_scale':
                    gsap.fromTo(targets, { scale: 3.5 * strFactor, opacity: 0 }, { scale: 1, opacity: 1, duration: speedSec, stagger: 0.06, ease: 'power4.out' });
                    break;
                case 'curtain_reveal':
                    gsap.fromTo(targets, { clipPath: 'inset(50% 0 50% 0)' }, { clipPath: 'inset(0% 0 0% 0)', duration: speedSec, stagger: 0.08, ease: 'power2.inOut' });
                    break;
                case 'text_strobe':
                    gsap.fromTo(targets, { opacity: 0 }, { opacity: 1, duration: 0.05, repeat: 4, yoyo: true, ease: 'steps(1)' });
                    break;
                case 'magnet_snap':
                    gsap.fromTo(targets, { x: 100 * strFactor, scale: 0.5, opacity: 0 }, { x: 0, scale: 1, opacity: 1, duration: speedSec, stagger: 0.08, ease: 'back.out(2)' });
                    break;
                case 'spin_in':
                    gsap.fromTo(targets, { rotation: -180 * strFactor, scale: 0.2, opacity: 0 }, { rotation: 0, scale: 1, opacity: 1, duration: speedSec, stagger: 0.08, ease: 'back.out(1.7)' });
                    break;
                case 'spring_bounce':
                    gsap.fromTo(targets, { y: -80 * strFactor, scaleY: 1.4, opacity: 0 }, { y: 0, scaleY: 1, opacity: 1, duration: speedSec, stagger: 0.08, ease: 'bounce.out' });
                    break;
                case 'elastic_snap':
                    gsap.fromTo(targets, { scale: 0.1, opacity: 0 }, { scale: 1, opacity: 1, duration: speedSec, stagger: 0.08, ease: 'elastic.out(1, 0.5)' });
                    break;
                case 'pop':
                    gsap.fromTo(targets, { scale: 0.3 * strFactor, opacity: 0 }, { scale: 1, opacity: 1, duration: speedSec, stagger: 0.06, ease: 'back.out(2)' });
                    break;
                case 'slide':
                case 'slide_up':
                    gsap.fromTo(targets, { y: 60 * strFactor, opacity: 0 }, { y: 0, opacity: 1, duration: speedSec, stagger: 0.08, ease: 'power2.out' });
                    break;
                case 'diagonal_slide':
                    gsap.fromTo(targets, { x: -60 * strFactor, y: -60 * strFactor, opacity: 0 }, { x: 0, y: 0, opacity: 1, duration: speedSec, stagger: 0.08, ease: 'power2.out' });
                    break;
                case 'zoom':
                case 'punch_zoom':
                    gsap.fromTo(targets, { scale: 2.2 * strFactor, opacity: 0 }, { scale: 1, opacity: 1, duration: speedSec, stagger: 0.08, ease: 'power2.out' });
                    break;
                case 'pulse':
                    gsap.fromTo(targets, { scale: 0.9 }, { scale: 1.15 * strFactor, repeat: -1, yoyo: true, duration: speedSec });
                    break;
                case 'pendulum':
                    gsap.fromTo(targets, { rotation: -25 * strFactor }, { rotation: 25 * strFactor, repeat: -1, yoyo: true, duration: speedSec, ease: 'power1.inOut' });
                    break;
                case 'wipe_reveal':
                    gsap.fromTo(targets, { clipPath: 'polygon(0 0, 0 0, 0 100%, 0 100%)' }, { clipPath: 'polygon(0 0, 100% 0, 100% 100%, 0 100%)', duration: speedSec, stagger: 0.1 });
                    break;
                case 'gravity_drop':
                    gsap.fromTo(targets, { y: -120 * strFactor, opacity: 0 }, { y: 0, opacity: 1, duration: speedSec, stagger: 0.08, ease: 'bounce.out' });
                    break;
                case 'typewriter':
                    gsap.fromTo(targets, { opacity: 0, y: 10 }, { opacity: 1, y: 0, duration: speedSec, stagger: 0.12, ease: 'none' });
                    break;
                case 'fade':
                default:
                    gsap.fromTo(targets, { opacity: 0 }, { opacity: 1, duration: speedSec, stagger: 0.08 });
                    break;
            }
        },

        formatTime(sec) {
            sec = parseFloat(sec) || 0;
            const m = Math.floor(sec / 60);
            const s = (sec % 60).toFixed(1);
            return `${m}:${s < 10 ? '0' : ''}${s}`;
        },

        async generateVoiceover() {
            this.isGeneratingVoice = true;
            const fullText = this.scenes.map(s => s.text).join('. ');

            try {
                const resp = await fetch('{{ route("api.generate_voiceover") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        text: fullText,
                        voice: this.ttsVoice,
                        language: 'hi',
                    }),
                });

                const data = await resp.json();

                if (data.status === 'success' && data.audio_url) {
                    this.voiceoverUrl = data.audio_url;
                    this.voiceoverPath = data.storage_path;
                    this.ttsEngineLabel = data.engine_label || '⚡ AI4Bharat Indic Parler-TTS (Warm & Expressive)';

                    // Auto-play generated voiceover preview
                    const audio = new Audio(data.audio_url);
                    audio.play().catch(() => {});
                } else {
                    alert('Audio generation failed: ' + (data.message || 'Unknown error'));
                }
            } catch (e) {
                alert('Audio voiceover request failed: ' + e.message);
            } finally {
                this.isGeneratingVoice = false;
            }
        },

        async exportVideo() {
            @auth
            try {
                const resp = await fetch('{{ route("generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        template_id: this.templateId,
                        scenes: this.scenes,
                        voiceover_path: this.voiceoverPath,
                        aspect_ratio: this.aspectRatio,
                    }),
                });

                if (!resp.ok) throw new Error('Export failed');
                const data = await resp.json();

                if (data.status === 'done' && data.download_url) {
                    const a = document.createElement('a');
                    a.href = data.download_url;
                    a.download = '';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }
            } catch (err) {
                alert('Export error: ' + err.message);
            }
            @else
            window.location.href = '{{ route("login") }}';
            @endauth
        },
    };
}
</script>
@endpush
