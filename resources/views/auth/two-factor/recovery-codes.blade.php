@extends('layouts.guest')
@section('title', __('Recovery Codes'))

@section('content')
<div class="auth-card" style="max-width:480px">
    <div style="text-align:center;margin-bottom:24px">
        <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#22c55e,#16a34a);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
            <i class="fas fa-key" style="color:#fff;font-size:20px"></i>
        </div>
        <h2 style="font-size:18px;font-weight:800;color:var(--text);margin:0">{{ __('Your Recovery Codes') }}</h2>
        <p style="font-size:13px;color:var(--text-muted);margin:6px 0 0">{{ __('Save these codes in a safe place. Each code can only be used once.') }}</p>
    </div>

    <div style="padding:16px;border-radius:12px;background:var(--bg-2);border:1px solid var(--border);margin-bottom:20px">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
            @foreach($recoveryCodes as $code)
            <div style="padding:8px 12px;border-radius:6px;background:var(--bg);border:1px solid var(--border);font-family:'JetBrains Mono',monospace;font-size:13px;font-weight:700;color:var(--text);text-align:center;letter-spacing:1px">
                {{ $code }}
            </div>
            @endforeach
        </div>
    </div>

    <div style="padding:12px;border-radius:10px;background:rgba(234,179,8,.08);border:1px solid rgba(234,179,8,.2);margin-bottom:20px;display:flex;gap:10px;align-items:flex-start">
        <i class="fas fa-triangle-exclamation" style="color:#eab308;font-size:14px;margin-top:2px"></i>
        <div style="font-size:12px;color:var(--text-secondary);line-height:1.5">
            {{ __('These codes will not be shown again. Store them in a secure password manager.') }}
        </div>
    </div>

    <div style="display:flex;gap:8px">
        <button onclick="copyCodes()" style="flex:1;padding:10px;border-radius:10px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px">
            <i class="fas fa-copy"></i> {{ __('Copy Codes') }}
        </button>
        <a href="{{ route('dashboard') }}" style="flex:1;padding:10px;border-radius:10px;background:var(--accent);color:white;font-size:13px;font-weight:700;cursor:pointer;text-align:center;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:6px">
            <i class="fas fa-arrow-right"></i> {{ __('Go to Dashboard') }}
        </a>
    </div>
</div>

<script>
function copyCodes() {
    const codes = @json($recoveryCodes);
    navigator.clipboard.writeText(codes.join('\n')).then(() => {
        alert('{{ __("Codes copied to clipboard!") }}');
    });
}
</script>
@endsection
