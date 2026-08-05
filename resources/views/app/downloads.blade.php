@extends('layouts.app')

@section('title', 'My Downloads')
@section('description', 'Your previously generated typography videos. Re-download anytime.')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">My Downloads</h1>
        <p class="text-white/50 text-sm">Your video render history. Click any row to re-download.</p>
    </div>

    @if($renders->count() === 0)
        <div class="empty-state">
            <div class="empty-icon">🎬</div>
            <h3 class="text-lg font-semibold text-white/80 mb-2">No videos yet</h3>
            <p class="text-white/40 text-sm mb-6">Head to the studio, type your text, and click any template to generate your first video.</p>
            <a href="{{ route('app') }}" class="btn-primary inline-flex">Go to Studio</a>
        </div>
    @else
        <div class="downloads-table-wrap">
            <table class="downloads-table">
                <thead>
                    <tr>
                        <th>Template</th>
                        <th>Text</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($renders as $render)
                    <tr>
                        <td>
                            <div class="template-cell">
                                <div class="template-dot" style="background:{{ $render->template->primary_color ?? '#7c3aed' }};"></div>
                                <span>{{ $render->template->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-cell" title="{{ $render->input_text }}">
                                {{ Str::limit($render->input_text, 40) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $render->status }}">
                                @switch($render->status)
                                    @case('done')      ✅ Done @break
                                    @case('processing') ⏳ Processing @break
                                    @case('pending')    🕐 Pending @break
                                    @case('failed')     ❌ Failed @break
                                @endswitch
                            </span>
                        </td>
                        <td class="date-cell">{{ $render->created_at->diffForHumans() }}</td>
                        <td>
                            @if($render->isDone())
                                <a href="{{ route('renders.download', $render->id) }}" class="dl-link">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download
                                </a>
                            @elseif($render->isFailed())
                                <a href="{{ route('app') }}" class="retry-link">Retry</a>
                            @else
                                <span class="text-white/30 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $renders->links() }}
        </div>
    @endif
</div>

<style>
.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 20px;
}
.empty-icon { font-size: 3rem; margin-bottom: 1rem; }
.downloads-table-wrap {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 16px;
    overflow: hidden;
}
.downloads-table { width: 100%; border-collapse: collapse; }
.downloads-table th {
    text-align: left;
    padding: 0.875rem 1.25rem;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.35);
    border-bottom: 1px solid rgba(255,255,255,0.07);
}
.downloads-table td {
    padding: 0.875rem 1.25rem;
    font-size: 0.875rem;
    color: rgba(255,255,255,0.8);
    border-bottom: 1px solid rgba(255,255,255,0.04);
    vertical-align: middle;
}
.downloads-table tr:last-child td { border-bottom: none; }
.downloads-table tr:hover td { background: rgba(255,255,255,0.02); }
.template-cell { display: flex; align-items: center; gap: 0.6rem; }
.template-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.text-cell { color: rgba(255,255,255,0.5); font-family: 'Courier Prime', monospace; font-size: 0.8rem; }
.date-cell { color: rgba(255,255,255,0.35); font-size: 0.78rem; white-space: nowrap; }
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.2rem 0.6rem;
    border-radius: 100px;
}
.status-done       { background: rgba(34,197,94,0.15);  color: #4ade80; }
.status-processing { background: rgba(234,179,8,0.15);  color: #facc15; }
.status-pending    { background: rgba(148,163,184,0.15); color: #94a3b8; }
.status-failed     { background: rgba(239,68,68,0.15);  color: #f87171; }
.dl-link {
    display: inline-flex; align-items: center; gap: 0.3rem;
    color: #a78bfa; font-size: 0.8rem; font-weight: 600;
    text-decoration: none; transition: color 0.15s;
}
.dl-link:hover { color: #7c3aed; }
.retry-link { color: #fb923c; font-size: 0.8rem; font-weight: 600; text-decoration: none; }
.retry-link:hover { color: #ea580c; }
</style>
@endsection
