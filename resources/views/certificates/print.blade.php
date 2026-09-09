<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#0b1220">
    <title>{{ __('Certificate') }} - {{ $certificate->cert_hash }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .certificate { width: 800px; background: white; border: 3px solid #4f46e5; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
        .header { background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 40px; text-align: center; color: white; }
        .header .icon { width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 36px; }
        .header h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        .header p { opacity: 0.8; font-size: 14px; }
        .body { padding: 40px; text-align: center; }
        .name { font-size: 28px; font-weight: 700; color: #111827; margin-bottom: 8px; }
        .subtitle { color: #6b7280; margin-bottom: 20px; font-size: 14px; }
        .course { font-size: 18px; font-weight: 700; color: #4f46e5; margin-bottom: 24px; }
        .meta { display: flex; justify-content: center; gap: 40px; margin-bottom: 32px; font-size: 13px; color: #6b7280; }
        .meta div p:first-child { font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; }
        .meta div p:last-child { font-weight: 600; margin-top: 2px; }
        .footer { border-top: 1px solid #e5e7eb; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
        @@media print { body { background: none; } .certificate { box-shadow: none; border: 2px solid #4f46e5; } }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <div class="icon">&#127942;</div>
            <h1>{{ __('Certificate of Completion') }}</h1>
            <p>{{ __('This certifies that') }}</p>
        </div>
        <div class="body">
            <div class="name">{{ $certificate->user->name }}</div>
            <div class="subtitle">{{ __('has successfully completed the course') }}</div>
            <div class="course">{{ $certificate->course->title }}</div>
            <div class="meta">
                <div>
                    <p>{{ __('Issue Date') }}</p>
                    <p>{{ $certificate->issue_date }}</p>
                </div>
                <div>
                    <p>{{ __('Certificate ID') }}</p>
                    <p style="font-family: monospace;">{{ $certificate->cert_hash }}</p>
                </div>
                @if($certificate->course->instructor)
                <div>
                    <p>{{ __('Instructor') }}</p>
                    <p>{{ $certificate->course->instructor }}</p>
                </div>
                @endif
            </div>
        </div>
        <div class="footer">
            CodeMaster {{ __('Certificate of Completion') }} &mdash; {{ __('Verify at') }} {{ config('app.url') }}/certificate/{{ $certificate->cert_hash }}
        </div>
    </div>
</body>
</html>
