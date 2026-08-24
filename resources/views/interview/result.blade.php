@extends('layouts.app')

@section('title', __('interview_result_title') . ' - CodeMaster')

@section('head')
<style>
    .ir-res {
        max-width: 680px;
        margin: 0 auto;
        padding: 40px 24px;
    }
    .ir-res-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        color: var(--text-muted);
        border: 1px solid var(--border);
        background: var(--card);
        transition: all .2s;
        margin-bottom: 32px;
    }
    .ir-res-back:hover { border-color: var(--accent); color: var(--accent); }

    .ir-res-hero {
        border-radius: 24px;
        padding: 48px 36px;
        text-align: center;
        position: relative;
        overflow: hidden;
        margin-bottom: 28px;
    }
    .ir-res-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 50% 0%, rgba(255,255,255,0.1) 0%, transparent 60%);
    }
    .ir-res-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        font-size: 36px;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }
    .ir-res-score {
        font-size: 64px;
        font-weight: 900;
        line-height: 1;
        position: relative;
        z-index: 1;
    }
    .ir-res-score-label {
        font-size: 14px;
        color: rgba(255,255,255,0.6);
        margin-top: 8px;
        position: relative;
        z-index: 1;
    }
    .ir-res-verdict {
        font-size: 20px;
        font-weight: 800;
        margin-top: 16px;
        position: relative;
        z-index: 1;
    }
    .ir-res-hero.pass { background: linear-gradient(135deg, #059669, #10b981); color: white; }
    .ir-res-hero.fail { background: linear-gradient(135deg, #dc2626, #ef4444); color: white; }
    .ir-res-hero.mid { background: linear-gradient(135deg, #d97706, #f59e0b); color: white; }

    .ir-res-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 28px;
    }
    .ir-res-stat {
        border-radius: 16px;
        background: var(--card);
        border: 1px solid var(--border);
        padding: 20px;
        text-align: center;
        transition: all .3s;
    }
    .ir-res-stat:hover { border-color: var(--accent); transform: translateY(-2px); }
    .ir-res-stat-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        font-size: 16px;
        margin-bottom: 10px;
    }
    .ir-res-stat-val {
        font-size: 20px;
        font-weight: 800;
        color: var(--text);
    }
    .ir-res-stat-label {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .8px;
        margin-top: 4px;
        font-weight: 600;
    }

    .ir-res-feedback {
        border-radius: 20px;
        background: var(--card);
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 28px;
    }
    .ir-res-fb-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
    }
    .ir-res-fb-head i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--accent-glow);
        color: var(--accent-2);
        font-size: 14px;
    }
    .ir-res-fb-title { font-size: 16px; font-weight: 700; color: var(--text); }
    .ir-res-fb-body {
        padding: 24px;
        font-size: 14px;
        color: var(--text-secondary);
        line-height: 1.8;
        white-space: pre-line;
    }

    .ir-res-actions {
        display: flex;
        justify-content: center;
        gap: 12px;
    }
    .ir-res-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: all .3s;
    }
    .ir-res-btn-primary {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: white;
        box-shadow: 0 4px 14px var(--accent-glow);
    }
    .ir-res-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px var(--accent-glow); }
    .ir-res-btn-secondary {
        background: var(--card);
        color: var(--text-muted);
        border: 1px solid var(--border);
    }
    .ir-res-btn-secondary:hover { border-color: var(--accent); color: var(--accent); }
</style>
@endsection

@section('content')
<div class="ir-res">
    <a href="{{ route('interview.index') }}" class="ir-res-back reveal-up">
        <i class="fas fa-arrow-left"></i> {{ __('interview_back_to_interviews') }}
    </a>

    @php
        $score = $interview->score ?? 0;
        $heroClass = $score >= 70 ? 'pass' : ($score >= 40 ? 'mid' : 'fail');
        $verdict = $score >= 70 ? __('interview_verdict_excellent') : ($score >= 40 ? __('interview_verdict_good') : __('interview_verdict_continue'));
        $icon = $score >= 70 ? 'fa-trophy' : ($score >= 40 ? 'fa-star' : 'fa-rocket');
    @endphp

    <div class="ir-res-hero {{ $heroClass }} reveal-up" data-delay="0">
        <div class="ir-res-icon"><i class="fas {{ $icon }}"></i></div>
        <div class="ir-res-score">{{ $score }}</div>
        <div class="ir-res-score-label">/100</div>
        <div class="ir-res-verdict">{{ $verdict }}</div>
    </div>

    <div class="ir-res-stats reveal-up" data-delay="0.1">
        <div class="ir-res-stat">
            <div class="ir-res-stat-icon" style="background:var(--accent-glow);color:var(--accent)"><i class="fas fa-layer-group"></i></div>
            <div class="ir-res-stat-val">{{ match($interview->type) { 'technical' => __('interview_type_technical'), 'behavioral' => __('interview_type_behavioral'), 'coding' => __('interview_type_coding'), 'system_design' => __('interview_type_system_design'), default => ucfirst(str_replace('_', ' ', $interview->type)) } }}</div>
            <div class="ir-res-stat-label">{{ __('interview_type_label') }}</div>
        </div>
        <div class="ir-res-stat">
            <div class="ir-res-stat-icon" style="background:rgba(234,179,8,0.1);color:var(--warning)"><i class="fas fa-signal"></i></div>
            <div class="ir-res-stat-val">{{ match($interview->difficulty) { 'easy' => __('interview_difficulty_easy'), 'medium' => __('interview_difficulty_medium'), 'hard' => __('interview_difficulty_hard'), default => ucfirst($interview->difficulty) } }}</div>
            <div class="ir-res-stat-label">{{ __('interview_difficulty_label') }}</div>
        </div>
        <div class="ir-res-stat">
            <div class="ir-res-stat-icon" style="background:rgba(34,197,94,0.1);color:var(--success)"><i class="fas fa-check-circle"></i></div>
            <div class="ir-res-stat-val" style="color:var(--success)">{{ __('interview_status_completed_label') }}</div>
            <div class="ir-res-stat-label">{{ __('interview_status_label') }}</div>
        </div>
    </div>

    @if($interview->feedback)
    <div class="ir-res-feedback reveal-up" data-delay="0.2">
        <div class="ir-res-fb-head">
            <i class="fas fa-robot"></i>
            <div class="ir-res-fb-title">{{ __('interview_ai_feedback') }}</div>
        </div>
        <div class="ir-res-fb-body">{{ $interview->feedback }}</div>
    </div>
    @endif

    <div class="ir-res-actions reveal-up" data-delay="0.3">
        <a href="{{ route('interview.index') }}" class="ir-res-btn ir-res-btn-primary">
            <i class="fas fa-play"></i> {{ __('interview_new_session') }}
        </a>
        <a href="{{ route('interview.index') }}" class="ir-res-btn ir-res-btn-secondary">
            <i class="fas fa-list"></i> {{ __('interview_all_sessions') }}
        </a>
    </div>
</div>
@endsection
