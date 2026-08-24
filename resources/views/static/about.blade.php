@extends('layouts.app')
@section('title', __('About') . ' - CodeMaster')
@section('main-class', '')
@section('content')
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html {
            scroll-behavior: smooth;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-sans);
            overflow-x: hidden;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 clamp(20px, 4vw, 48px);
        }
        .gradient-text {
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .glass {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--border);
        }
        .noise { position: relative; }
        .noise::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            opacity: 0.015;
            pointer-events: none;
            mix-blend-mode: overlay;
        }

        .section-header { text-align: center; margin-bottom: 40px; }
        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 18px;
            border-radius: 100px;
            background: var(--accent-glow);
            border: 1px solid var(--accent-glow-strong);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 24px;
        }
        .section-title {
            font-size: clamp(36px, 5vw, 64px);
            font-weight: 800;
            letter-spacing: -2px;
            line-height: 1.1;
            margin-bottom: 16px;
            color: var(--text);
        }
        .section-desc {
            font-size: 17px;
            color: var(--text-secondary);
            max-width: 520px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* ══════════════════════════════════════════
       HERO SECTION
       ══════════════════════════════════════════ */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 120px 0;
            overflow: hidden;
        }
        .hero-bg {
            position: absolute;
            inset: 0;
            overflow: hidden;
            z-index: 0;
        }
        .hero-gradient-mesh {
            position: absolute;
            inset: -50%;
            background:
                radial-gradient(ellipse 80% 50% at 20% 40%, var(--accent-glow-strong) 0%, transparent 50%),
                radial-gradient(ellipse 60% 80% at 80% 60%, rgba(139,92,246,0.06) 0%, transparent 50%),
                radial-gradient(ellipse 40% 40% at 50% 20%, rgba(245,158,11,0.04) 0%, transparent 50%);
            animation: meshMove 30s ease-in-out infinite;
        }
        @@keyframes meshMove {
            0%, 100% { transform: translate(0,0) rotate(0deg); }
            25% { transform: translate(2%,-2%) rotate(1deg); }
            50% { transform: translate(-1%,3%) rotate(-0.5deg); }
            75% { transform: translate(3%,1%) rotate(0.5deg); }
        }
        .hero-grid-pattern {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 100px 100px;
            opacity: 0.3;
            mask-image: radial-gradient(ellipse 80% 60% at 50% 50%, black 20%, transparent 70%);
            -webkit-mask-image: radial-gradient(ellipse 80% 60% at 50% 50%, black 20%, transparent 70%);
        }
        .hero-orbs {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }
        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            will-change: transform;
        }
        .hero-orb-1 {
            width: 600px; height: 600px;
            background: var(--accent);
            opacity: 0.06;
            top: -20%; left: -10%;
            animation: orbFloat1 20s ease-in-out infinite;
        }
        .hero-orb-2 {
            width: 500px; height: 500px;
            background: var(--accent-2);
            opacity: 0.05;
            bottom: -15%; right: -5%;
            animation: orbFloat2 25s ease-in-out infinite;
        }
        .hero-orb-3 {
            width: 300px; height: 300px;
            background: var(--accent-4);
            opacity: 0.04;
            top: 60%; left: 40%;
            animation: orbFloat3 15s ease-in-out infinite;
        }
        @@keyframes orbFloat1 {
            0%, 100% { transform: translate(0,0) scale(1); }
            33% { transform: translate(80px,60px) scale(1.1); }
            66% { transform: translate(-40px,100px) scale(0.9); }
        }
        @@keyframes orbFloat2 {
            0%, 100% { transform: translate(0,0) scale(1); }
            33% { transform: translate(-60px,-80px) scale(1.05); }
            66% { transform: translate(50px,-40px) scale(0.95); }
        }
        @@keyframes orbFloat3 {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(-100px,-60px) scale(1.2); }
        }
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 0 clamp(20px, 4vw, 48px);
        }
        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 20px;
            border-radius: 100px;
            background: var(--accent-glow);
            border: 1px solid var(--accent-glow-strong);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 32px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s 0.2s var(--ease-out-expo) forwards;
        }
        .hero-eyebrow-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--accent);
            animation: pulse 2s ease-in-out infinite;
        }
        .hero-title {
            font-size: clamp(48px, 8vw, 96px);
            font-weight: 900;
            line-height: 0.92;
            letter-spacing: -4px;
            margin-bottom: 28px;
            opacity: 0;
            transform: translateY(40px);
            animation: fadeInUp 1s 0.4s var(--ease-out-expo) forwards;
        }
        .hero-title-gradient {
            background: linear-gradient(135deg, var(--accent), var(--accent-2), var(--accent-4));
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 8s ease-in-out infinite;
        }
        @@keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .hero-description {
            font-size: clamp(16px, 1.8vw, 20px);
            color: var(--text-secondary);
            line-height: 1.7;
            max-width: 520px;
            margin: 0 auto 44px;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.8s 0.6s var(--ease-out-expo) forwards;
        }
        .hero-stats {
            display: flex;
            gap: 48px;
            justify-content: center;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s 1s var(--ease-out-expo) forwards;
        }
        .hero-stat { position: relative; }
        .hero-stat::after {
            content: '';
            position: absolute;
            right: -24px; top: 50%;
            transform: translateY(-50%);
            width: 1px; height: 40px;
            background: var(--border);
        }
        .hero-stat:last-child::after { display: none; }
        .hero-stat-value {
            font-size: 36px;
            font-weight: 900;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }
        .hero-stat-label {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 6px;
            font-weight: 500;
        }
        @@keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
        @@keyframes fadeIn {
            to { opacity: 1; }
        }
        @@keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.5); }
        }

        /* ══════════════════════════════════════════
       MARQUEE SECTION
       ══════════════════════════════════════════ */
        .marquee-section {
            padding: 60px 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            background: var(--bg-2);
            overflow: hidden;
            position: relative;
        }
        .marquee-label {
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 28px;
        }
        .marquee-track {
            display: flex;
            animation: marquee 40s linear infinite;
            width: max-content;
        }
        .marquee-track:hover { animation-play-state: paused; }
        .marquee-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 48px;
            white-space: nowrap;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-muted);
            transition: color 0.3s;
        }
        .marquee-item:hover { color: var(--text); }
        .marquee-item i { font-size: 24px; }
        @@keyframes marquee {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }
        .marquee-fade-left,
        .marquee-fade-right {
            position: absolute;
            top: 0; bottom: 0;
            width: 120px;
            z-index: 2;
            pointer-events: none;
        }
        .marquee-fade-left {
            left: 0;
            background: linear-gradient(to right, var(--bg-2), transparent);
        }
        .marquee-fade-right {
            right: 0;
            background: linear-gradient(to left, var(--bg-2), transparent);
        }

        /* ══════════════════════════════════════════
       MISSION SECTION (Journey-style panels)
       ══════════════════════════════════════════ */
        .mission-section {
            position: relative;
            height: 250vh;
        }
        .mission-sticky {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
        }
        .mission-track {
            position: relative;
            width: 100%;
            height: 100%;
        }
        .mission-panel {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            opacity: 0;
            transform: translateY(40px) scale(0.96);
            transition: opacity 0.7s var(--ease-out-expo), transform 0.7s var(--ease-out-expo);
            pointer-events: none;
            will-change: transform, opacity;
        }
        .mission-panel.active {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }
        .mission-panel.exit-up {
            opacity: 0;
            transform: translateY(-40px) scale(0.96);
        }
        .mission-panel-content {
            max-width: 1200px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }
        .mission-number {
            font-size: clamp(120px, 20vw, 280px);
            font-weight: 900;
            line-height: 0.8;
            background: linear-gradient(180deg, var(--border-hover) 0%, transparent 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: absolute;
            top: 50%; left: 5%;
            transform: translateY(-50%);
            opacity: 0.3;
            user-select: none;
        }
        .mission-step-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 20px;
            background: rgba(34,197,94,0.1);
            color: #22c55e;
            border: 1px solid rgba(34,197,94,0.2);
        }
        .mission-step-tag.purple {
            background: rgba(139,92,246,0.1);
            color: #8b5cf6;
            border: 1px solid rgba(139,92,246,0.2);
        }
        .mission-title {
            font-size: clamp(32px, 4.5vw, 56px);
            font-weight: 800;
            letter-spacing: -2px;
            line-height: 1.1;
            margin-bottom: 20px;
            color: var(--text);
        }
        .mission-desc {
            font-size: 17px;
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 24px;
            max-width: 480px;
        }
        .mission-quote {
            margin-top: 16px;
            padding: 20px 24px;
            border-left: 3px solid var(--accent);
            background: var(--accent-glow);
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
            font-style: italic;
            font-size: 15px;
            color: var(--text);
            line-height: 1.6;
        }
        .mission-stats-card {
            background: var(--gradient);
            border-radius: var(--radius-lg);
            padding: 48px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg), var(--shadow-glow);
            transform: perspective(1000px) rotateY(5deg);
            transition: transform 0.5s var(--ease-out-expo);
        }
        .mission-stats-card:hover {
            transform: perspective(1000px) rotateY(0deg);
        }
        .mission-stats-card::before {
            content: '';
            position: absolute;
            top: -50%; right: -50%;
            width: 100%; height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%);
            pointer-events: none;
        }
        .mission-stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            position: relative;
            z-index: 1;
        }
        .mission-stat {
            text-align: center;
            padding: 24px 16px;
            border-radius: var(--radius-md);
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.12);
            transition: transform 0.35s var(--ease-out-expo), background 0.35s ease;
        }
        .mission-stat:hover {
            transform: translateY(-4px) scale(1.02);
            background: rgba(255,255,255,0.18);
        }
        .mission-stat-number {
            font-size: 32px;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 6px;
            background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.8) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .mission-stat-label {
            font-size: 12px;
            color: rgba(255,255,255,0.75);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ══════════════════════════════════════════
       VALUES SECTION (Journey-style 6 panels)
       ══════════════════════════════════════════ */
        .values-section {
            position: relative;
            height: 350vh;
        }
        .values-sticky {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
        }
        .values-track {
            position: relative;
            width: 100%;
            height: 100%;
        }
        .value-panel {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            opacity: 0;
            transform: translateY(40px) scale(0.96);
            transition: opacity 0.7s var(--ease-out-expo), transform 0.7s var(--ease-out-expo);
            pointer-events: none;
            will-change: transform, opacity;
        }
        .value-panel.active {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }
        .value-panel.exit-up {
            opacity: 0;
            transform: translateY(-40px) scale(0.96);
        }
        .value-panel-content {
            max-width: 1200px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }
        .value-number {
            font-size: clamp(120px, 20vw, 280px);
            font-weight: 900;
            line-height: 0.8;
            background: linear-gradient(180deg, var(--border-hover) 0%, transparent 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: absolute;
            top: 50%; left: 5%;
            transform: translateY(-50%);
            opacity: 0.3;
            user-select: none;
        }
        .value-panel-step-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 20px;
            background: var(--accent-glow);
            color: var(--accent);
            border: 1px solid var(--accent-glow-strong);
        }
        .value-panel-title {
            font-size: clamp(32px, 4.5vw, 56px);
            font-weight: 800;
            letter-spacing: -2px;
            line-height: 1.1;
            margin-bottom: 20px;
            color: var(--text);
        }
        .value-panel-desc {
            font-size: 17px;
            color: var(--text-secondary);
            line-height: 1.8;
            max-width: 480px;
        }
        .value-holo-card {
            background: var(--card);
            backdrop-filter: blur(30px) saturate(180%);
            border: 1px solid var(--border);
            border-radius: 32px;
            transform-style: preserve-3d;
            padding: 48px;
            box-shadow: 0 50px 120px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .value-holo-card::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: conic-gradient(from 0deg, transparent 0%, rgba(255,255,255,0.05) 10%, transparent 20%);
            animation: holoSpin 8s linear infinite;
            pointer-events: none;
        }
        @@keyframes holoSpin {
            to { transform: rotate(360deg); }
        }
        .value-holo-icon {
            width: 80px; height: 80px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: #fff;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
            transform: translateZ(40px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            transition: transform 0.5s var(--ease-out-expo);
        }
        .value-holo-card:hover .value-holo-icon {
            transform: translateZ(60px) scale(1.1);
        }
        .value-holo-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
            transform: translateZ(20px);
        }
        .value-holo-desc {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.7;
            max-width: 360px;
            position: relative;
            z-index: 1;
            transform: translateZ(10px);
        }

        /* ══════════════════════════════════════════
       HISTORY SECTION (Leader-style stacked sticky)
       ══════════════════════════════════════════ */
        .history-panels {
            position: relative;
        }
        .history-panel {
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            z-index: 1;
            background: var(--bg);
        }
        .history-panel-bg {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.6s;
        }
        .history-panel.active .history-panel-bg { opacity: 1; }
        .history-panel-inner {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }
        .history-panel-visual {
            position: relative;
            display: flex;
            justify-content: center;
        }
        .history-year-badge {
            width: 200px; height: 200px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
            font-weight: 900;
            color: var(--bg);
            background: var(--gradient);
            position: relative;
            opacity: 0;
            transform: scale(0.6) translateY(60px);
            transition: all 0.8s var(--ease-out-expo);
            box-shadow: 0 0 80px rgba(0,0,0,0.3);
        }
        .history-panel.active .history-year-badge {
            opacity: 1;
            transform: scale(1) translateY(0);
            box-shadow: 0 0 80px var(--accent-glow-strong), 0 20px 60px rgba(0,0,0,0.3);
        }
        .history-ring {
            position: absolute;
            width: 260px; height: 260px;
            border: 1px solid var(--border);
            border-radius: 50%;
            top: 50%; left: 50%;
            transform: translate(-50%,-50%) scale(0.5) rotate(0deg);
            opacity: 0;
            transition: all 1s var(--ease-out-expo) 0.2s;
            pointer-events: none;
        }
        .history-panel.active .history-ring {
            opacity: 0.4;
            transform: translate(-50%,-50%) scale(1) rotate(30deg);
        }
        .history-ring-2 {
            width: 320px; height: 320px;
            border-style: dashed;
            transition-delay: 0.3s;
        }
        .history-panel.active .history-ring-2 {
            transform: translate(-50%,-50%) scale(1) rotate(-20deg);
        }
        .history-panel-icon {
            position: absolute;
            bottom: -10px; right: 40px;
            width: 56px; height: 56px;
            border-radius: var(--radius-md);
            background: var(--card);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--accent);
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s var(--ease-out-expo) 0.4s;
        }
        .history-panel.active .history-panel-icon {
            opacity: 1;
            transform: translateY(0);
        }
        .history-panel-text {
            opacity: 0;
            transform: translateX(60px);
            transition: all 0.8s var(--ease-out-expo) 0.15s;
        }
        .history-panel.active .history-panel-text {
            opacity: 1;
            transform: translateX(0);
        }
        .history-panel-index {
            font-family: var(--font-mono);
            font-size: 13px;
            color: var(--accent);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .history-panel-index::before {
            content: '';
            width: 40px; height: 1px;
            background: var(--accent);
        }
        .history-panel-year {
            font-size: clamp(36px, 4vw, 56px);
            font-weight: 800;
            letter-spacing: -2px;
            line-height: 1.1;
            color: var(--text);
            margin-bottom: 16px;
        }
        .history-panel-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 20px;
        }
        .history-panel-desc {
            font-size: 16px;
            color: var(--text-secondary);
            line-height: 1.8;
            max-width: 440px;
        }

        /* ══════════════════════════════════════════
       TEAM SECTION (Leader-style stacked sticky)
       ══════════════════════════════════════════ */
        .team-panels {
            position: relative;
        }
        .team-panel {
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            z-index: 1;
            background: var(--bg-2);
        }
        .team-panel-inner {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }
        .team-panel-visual {
            position: relative;
            display: flex;
            justify-content: center;
        }
        .team-panel-avatar {
            width: 280px; height: 280px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 72px;
            font-weight: 900;
            color: #fff;
            background: var(--gradient);
            position: relative;
            opacity: 0;
            transform: scale(0.6) translateY(60px);
            transition: all 0.8s var(--ease-out-expo);
            box-shadow: 0 0 80px rgba(0,0,0,0.3);
        }
        .team-panel.active .team-panel-avatar {
            opacity: 1;
            transform: scale(1) translateY(0);
            border-color: var(--accent);
            box-shadow: 0 0 80px var(--accent-glow-strong), 0 20px 60px rgba(0,0,0,0.3);
        }
        .team-panel-ring {
            position: absolute;
            width: 320px; height: 320px;
            border: 1px solid var(--border);
            border-radius: 50%;
            top: 50%; left: 50%;
            transform: translate(-50%,-50%) scale(0.5) rotate(0deg);
            opacity: 0;
            transition: all 1s var(--ease-out-expo) 0.2s;
            pointer-events: none;
        }
        .team-panel.active .team-panel-ring {
            opacity: 0.4;
            transform: translate(-50%,-50%) scale(1) rotate(30deg);
        }
        .team-panel-ring-2 {
            width: 380px; height: 380px;
            border-style: dashed;
            transition-delay: 0.3s;
        }
        .team-panel.active .team-panel-ring-2 {
            transform: translate(-50%,-50%) scale(1) rotate(-20deg);
        }
        .team-panel-text {
            opacity: 0;
            transform: translateX(60px);
            transition: all 0.8s var(--ease-out-expo) 0.15s;
        }
        .team-panel.active .team-panel-text {
            opacity: 1;
            transform: translateX(0);
        }
        .team-panel-index {
            font-family: var(--font-mono);
            font-size: 13px;
            color: var(--accent);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .team-panel-index::before {
            content: '';
            width: 40px; height: 1px;
            background: var(--accent);
        }
        .team-panel-name {
            font-size: clamp(36px, 4vw, 56px);
            font-weight: 800;
            letter-spacing: -2px;
            line-height: 1.1;
            color: var(--text);
            margin-bottom: 12px;
        }
        .team-panel-role {
            font-size: 20px;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .team-panel-role i { font-size: 24px; }
        .team-panel-desc {
            font-size: 16px;
            color: var(--text-secondary);
            line-height: 1.8;
            max-width: 440px;
        }

        /* ══════════════════════════════════════════
       CTA SECTION
       ══════════════════════════════════════════ */
        .cta-section {
            padding: 160px 0;
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
        }
        .cta-bg-gradient {
            position: absolute;
            inset: -50%;
            background:
                radial-gradient(ellipse 60% 40% at 50% 50%, var(--accent-glow-strong) 0%, transparent 50%),
                radial-gradient(ellipse 40% 60% at 30% 70%, rgba(139,92,246,0.08) 0%, transparent 50%);
            animation: ctaPulse 10s ease-in-out infinite;
        }
        @@keyframes ctaPulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        .cta-content {
            position: relative;
            z-index: 2;
        }
        .cta-title {
            font-size: clamp(40px, 6vw, 80px);
            font-weight: 900;
            letter-spacing: -3px;
            line-height: 1;
            margin-bottom: 20px;
        }
        .cta-desc {
            font-size: 18px;
            color: var(--text-secondary);
            max-width: 560px;
            margin: 0 auto 44px;
            line-height: 1.7;
        }
        .cta-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 32px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.4s var(--ease-out-expo);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background: var(--gradient);
            color: var(--bg);
            box-shadow: 0 8px 32px var(--accent-glow-strong);
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--accent-2), var(--accent));
            opacity: 0;
            transition: opacity 0.4s;
        }
        .btn-primary:hover::before { opacity: 1; }
        .btn-primary:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 16px 48px var(--accent-glow-strong);
        }
        .btn-primary span,
        .btn-primary i {
            position: relative;
            z-index: 1;
        }
        .btn-secondary {
            background: var(--glass-bg, rgba(255,255,255,0.04));
            color: var(--text);
            border: 1px solid var(--border-hover);
            backdrop-filter: blur(12px);
        }
        .btn-secondary:hover {
            border-color: var(--accent);
            background: var(--accent-glow);
            transform: translateY(-4px);
            box-shadow: var(--shadow-glow);
        }

        /* ══════════════════════════════════════════
       REVEAL ANIMATIONS
       ══════════════════════════════════════════ */
        .reveal {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.9s var(--ease-out-expo);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }
        .reveal-delay-5 { transition-delay: 0.5s; }
        .reveal-delay-6 { transition-delay: 0.6s; }
        .reveal-left {
            opacity: 0;
            transform: translateX(-60px);
            transition: all 0.9s var(--ease-out-expo);
        }
        .reveal-left.visible {
            opacity: 1;
            transform: translateX(0);
        }
        .reveal-right {
            opacity: 0;
            transform: translateX(60px);
            transition: all 0.9s var(--ease-out-expo);
        }
        .reveal-right.visible {
            opacity: 1;
            transform: translateX(0);
        }
        .reveal-scale {
            opacity: 0;
            transform: scale(0.9);
            transition: all 0.9s var(--ease-out-expo);
        }
        .reveal-scale.visible {
            opacity: 1;
            transform: scale(1);
        }

        /* ══════════════════════════════════════════
       SCROLL PROGRESS BAR
       ══════════════════════════════════════════ */
        .scroll-progress {
            position: fixed;
            top: 0; left: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(90deg, var(--accent), var(--accent-glow-strong));
            z-index: 99999;
            transition: width 0.1s linear;
        }

        /* ══════════════════════════════════════════
       SCROLL HINT
       ══════════════════════════════════════════ */
        .scroll-hint {
            position: absolute;
            bottom: 40px; left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            opacity: 0;
            animation: fadeIn 1s 1.8s var(--ease-out-expo) forwards;
            z-index: 10;
        }
        .scroll-hint-text {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-muted);
        }
        .scroll-hint-arrow {
            font-size: 14px;
            color: var(--accent);
            animation: bounceDown 1.5s ease-in-out infinite;
        }
        @@keyframes bounceDown {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(8px); }
        }

        /* ══════════════════════════════════════════
       PARTICLES CANVAS
       ══════════════════════════════════════════ */
        #particles-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            opacity: 0.4;
        }

        /* ══════════════════════════════════════════
       RESPONSIVE
       ══════════════════════════════════════════ */
        @@media (max-width: 1024px) {
            .hero { min-height: auto; padding: 100px 0 60px; }
            .hero-title { font-size: clamp(36px, 10vw, 56px); }
            .hero-stats { gap: 32px; }
            .mission-panel-content,
            .value-panel-content { grid-template-columns: 1fr; gap: 40px; padding: 40px; }
            .mission-number,
            .value-number { font-size: 120px; left: 3%; }
            .history-panel-inner,
            .team-panel-inner { grid-template-columns: 1fr; gap: 40px; padding: 0 32px; text-align: center; }
            .history-year-badge { width: 160px; height: 160px; font-size: 44px; }
            .history-ring { width: 200px; height: 200px; }
            .history-ring-2 { width: 240px; height: 240px; }
            .history-panel-visual { order: -1; }
            .history-panel-desc { max-width: 100%; }
            .history-panel-index { justify-content: center; }
            .history-panel-index::before { display: none; }
            .team-panel-avatar { width: 200px; height: 200px; font-size: 52px; }
            .team-panel-ring { width: 240px; height: 240px; }
            .team-panel-ring-2 { width: 280px; height: 280px; }
            .marquee-item { font-size: 15px; padding: 0 32px; }
        }
        @@media (max-width: 768px) {
            .container { padding: 0 16px; }
            .hero { padding: 80px 0 40px; }
            .hero-eyebrow { font-size: 10px; padding: 6px 14px; margin-bottom: 20px; }
            .hero-title { font-size: 36px; letter-spacing: -2px; margin-bottom: 16px; }
            .hero-description { font-size: 15px; margin-bottom: 28px; }
            .hero-stats { flex-direction: column; gap: 20px; align-items: center; }
            .hero-stat::after { display: none; }
            .hero-stat-value { font-size: 28px; }
            .marquee-section { padding: 40px 0; }
            .marquee-item { font-size: 13px; padding: 0 24px; gap: 8px; }
            .marquee-item i { font-size: 18px; }
            .marquee-fade-left, .marquee-fade-right { width: 40px; }
            .mission-panel { padding: 60px 20px; }
            .mission-panel-content { gap: 24px; padding: 20px 0; }
            .mission-number { font-size: 80px; left: 2%; }
            .mission-title { font-size: 28px; letter-spacing: -1px; }
            .mission-desc { font-size: 15px; }
            .mission-stats-card { padding: 28px; }
            .mission-stat-number { font-size: 24px; }
            .value-panel { padding: 60px 20px; }
            .value-panel-content { gap: 24px; padding: 20px 0; }
            .value-number { font-size: 80px; left: 2%; }
            .value-panel-title { font-size: 28px; letter-spacing: -1px; }
            .value-panel-desc { font-size: 15px; }
            .value-holo-card { padding: 28px; border-radius: 20px; }
            .value-holo-icon { width: 60px; height: 60px; font-size: 28px; }
            .value-holo-title { font-size: 22px; }
            .history-panel { padding: 40px 20px; }
            .history-year-badge { width: 120px; height: 120px; font-size: 32px; }
            .history-ring { width: 160px; height: 160px; }
            .history-ring-2 { width: 190px; height: 190px; }
            .history-panel-year { font-size: 28px; letter-spacing: -1px; }
            .history-panel-title { font-size: 16px; }
            .history-panel-desc { font-size: 14px; }
            .team-panel { padding: 40px 20px; }
            .team-panel-avatar { width: 140px; height: 140px; font-size: 40px; border-width: 3px; }
            .team-panel-ring { width: 180px; height: 180px; }
            .team-panel-ring-2 { width: 210px; height: 210px; }
            .team-panel-name { font-size: 28px; letter-spacing: -1px; }
            .team-panel-role { font-size: 16px; }
            .team-panel-desc { font-size: 14px; }
            .cta-section { padding: 80px 0; }
            .cta-title { font-size: clamp(28px, 8vw, 48px); letter-spacing: -2px; }
            .cta-desc { font-size: 15px; margin-bottom: 32px; }
            .cta-actions { flex-direction: column; align-items: center; gap: 12px; }
            .section-title { font-size: clamp(24px, 6vw, 40px); letter-spacing: -1px; }
            .section-desc { font-size: 14px; }
            .scroll-hint { bottom: 20px; }
        }
        @@media (max-width: 480px) {
            .hero { padding: 60px 0 30px; }
            .hero-title { font-size: 32px; letter-spacing: -1.5px; }
            .hero-stat-value { font-size: 24px; }
            .btn { width: 100%; max-width: 300px; justify-content: center; padding: 14px 24px; font-size: 14px; }
        }
        @@media (hover: none) and (pointer: coarse) {
            .btn:hover { transform: none; }
            .btn-primary:hover { transform: none; }
            .nav-dot:hover { transform: scale(1); }
        }
        @@media print {
            .scroll-progress, .scroll-hint, #particles-canvas, .hero-bg, .cta-bg-gradient { display: none !important; }
            .hero { min-height: auto; padding: 20px 0; }
            body { background: #fff; color: #000; }
        }
        @@media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
    <!-- Particles Canvas -->
    <canvas id="particles-canvas"></canvas>
    <!-- Scroll Progress Bar -->
    <div class="scroll-progress" id="scrollProgress"></div>

    <!-- ══════════════════════════════════════════
       HERO SECTION
       ══════════════════════════════════════════ -->
    <section class="hero noise" id="hero">
        <div class="hero-bg">
            <div class="hero-gradient-mesh"></div>
            <div class="hero-grid-pattern"></div>
            <div class="hero-orbs">
                <div class="hero-orb hero-orb-1"></div>
                <div class="hero-orb hero-orb-2"></div>
                <div class="hero-orb hero-orb-3"></div>
            </div>
        </div>
        <div class="hero-content">
            <div class="hero-eyebrow">
                <div class="hero-eyebrow-dot"></div>
                {{ __('about.badge') }}
            </div>
            <h1 class="hero-title">
                <span class="hero-title-gradient">{{ __('about.hero_title') }}</span>
            </h1>
            <p class="hero-description">{{ __('about.hero_subtitle') }}</p>
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-value">{{ number_format($stats['students']) }}+</div>
                    <div class="hero-stat-label">{{ __('about.stat_students') }}</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">{{ number_format($stats['courses']) }}+</div>
                    <div class="hero-stat-label">{{ __('about.stat_courses') }}</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">{{ number_format($stats['vacancies']) }}+</div>
                    <div class="hero-stat-label">{{ __('about.stat_hires') }}</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">{{ number_format($stats['countries']) }}+</div>
                    <div class="hero-stat-label">{{ __('about.stat_countries') }}</div>
                </div>
            </div>
        </div>
    </section>
    <div class="scroll-hint">
        <span class="scroll-hint-text">{{ __('Scroll down') }}</span>
        <div class="scroll-hint-arrow"><i class="fas fa-chevron-down"></i></div>
    </div>

    <!-- ══════════════════════════════════════════
       MARQUEE SECTION
       ══════════════════════════════════════════ -->
    <section class="marquee-section">
        <div class="marquee-label">{{ __('home_marquee_label') }}</div>
        <div class="marquee-fade-left"></div>
        <div class="marquee-fade-right"></div>
        <div style="overflow:hidden">
            <div class="marquee-track">
                <div class="marquee-item"><i class="fab fa-google"></i> Google</div>
                <div class="marquee-item"><i class="fab fa-apple"></i> Apple</div>
                <div class="marquee-item"><i class="fab fa-microsoft"></i> Microsoft</div>
                <div class="marquee-item"><i class="fab fa-meta"></i> Meta</div>
                <div class="marquee-item"><i class="fab fa-amazon"></i> Amazon</div>
                <div class="marquee-item"><i class="fas fa-bolt"></i> Tesla</div>
                <div class="marquee-item"><i class="fas fa-rocket"></i> SpaceX</div>
                <div class="marquee-item"><i class="fas fa-brain"></i> OpenAI</div>
                <div class="marquee-item"><i class="fab fa-spotify"></i> Spotify</div>
                <div class="marquee-item"><i class="fab fa-slack"></i> Slack</div>
                <div class="marquee-item"><i class="fab fa-google"></i> Google</div>
                <div class="marquee-item"><i class="fab fa-apple"></i> Apple</div>
                <div class="marquee-item"><i class="fab fa-microsoft"></i> Microsoft</div>
                <div class="marquee-item"><i class="fab fa-meta"></i> Meta</div>
                <div class="marquee-item"><i class="fab fa-amazon"></i> Amazon</div>
                <div class="marquee-item"><i class="fas fa-bolt"></i> Tesla</div>
                <div class="marquee-item"><i class="fas fa-rocket"></i> SpaceX</div>
                <div class="marquee-item"><i class="fas fa-brain"></i> OpenAI</div>
                <div class="marquee-item"><i class="fab fa-spotify"></i> Spotify</div>
                <div class="marquee-item"><i class="fab fa-slack"></i> Slack</div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
       MISSION SECTION (Journey-style 2 panels)
       ══════════════════════════════════════════ -->
    <section class="mission-section" id="mission">
        <div class="mission-sticky">
            <div class="mission-track" id="missionTrack">
                <!-- Panel 1: Mission Text + Stats Card -->
                <div class="mission-panel">
                    <span class="mission-number">01</span>
                    <div class="mission-panel-content">
                        <div>
                            <div class="mission-step-tag"><i class="fas fa-bullseye"></i> {{ __('about.mission_label') }}</div>
                            <h2 class="mission-title">{{ __('about.mission_title') }}</h2>
                            <p class="mission-desc">{{ __('about.mission_p1') }}</p>
                        </div>
                        <div>
                            <div class="mission-stats-card">
                                <div class="mission-stats-grid">
                                    <div class="mission-stat">
                                        <p class="mission-stat-number">{{ number_format($stats['students']) }}+</p>
                                        <p class="mission-stat-label">{{ __('about.stat_students') }}</p>
                                    </div>
                                    <div class="mission-stat">
                                        <p class="mission-stat-number">{{ number_format($stats['courses']) }}+</p>
                                        <p class="mission-stat-label">{{ __('about.stat_courses') }}</p>
                                    </div>
                                    <div class="mission-stat">
                                        <p class="mission-stat-number">{{ number_format($stats['vacancies']) }}+</p>
                                        <p class="mission-stat-label">{{ __('about.stat_hires') }}</p>
                                    </div>
                                    <div class="mission-stat">
                                        <p class="mission-stat-number">{{ number_format($stats['countries']) }}+</p>
                                        <p class="mission-stat-label">{{ __('about.stat_countries') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Panel 2: Mission Continued + Quote -->
                <div class="mission-panel">
                    <span class="mission-number">02</span>
                    <div class="mission-panel-content">
                        <div>
                            <div class="mission-step-tag purple"><i class="fas fa-heart"></i> {{ __('about.mission_label') }}</div>
                            <h2 class="mission-title">{{ __('about.mission_title') }}</h2>
                            <p class="mission-desc">{{ __('about.mission_p2') }}</p>
                        </div>
                        <div>
                            <div class="mission-quote">
                                {{ __('about.mission_p1') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
       VALUES SECTION (Journey-style 6 panels)
       ══════════════════════════════════════════ -->
    <section class="values-section" id="values">
        <div class="values-sticky">
            <div class="values-track" id="valuesTrack">
                @php
                    $values = [
                        ['num' => '01', 'icon' => 'fa-globe', 'title' => __('about.value_accessibility'), 'desc' => __('about.value_accessibility_desc')],
                        ['num' => '02', 'icon' => 'fa-award', 'title' => __('about.value_quality'), 'desc' => __('about.value_quality_desc')],
                        ['num' => '03', 'icon' => 'fa-users', 'title' => __('about.value_community'), 'desc' => __('about.value_community_desc')],
                        ['num' => '04', 'icon' => 'fa-laptop-code', 'title' => __('about.value_practice'), 'desc' => __('about.value_practice_desc')],
                        ['num' => '05', 'icon' => 'fa-rocket', 'title' => __('about.value_career'), 'desc' => __('about.value_career_desc')],
                        ['num' => '06', 'icon' => 'fa-lightbulb', 'title' => __('about.value_innovation'), 'desc' => __('about.value_innovation_desc')],
                    ];
                @endphp
                @foreach($values as $i => $v)
                <div class="value-panel">
                    <span class="value-number">{{ $v['num'] }}</span>
                    <div class="value-panel-content">
                        <div>
                            <div class="value-panel-step-tag"><i class="fas {{ $v['icon'] }}"></i> {{ $v['title'] }}</div>
                            <h2 class="value-panel-title">{{ $v['title'] }}</h2>
                            <p class="value-panel-desc">{{ $v['desc'] }}</p>
                        </div>
                        <div>
                            <div class="value-holo-card">
                                <div class="value-holo-icon">
                                    <i class="fas {{ $v['icon'] }}"></i>
                                </div>
                                <h3 class="value-holo-title">{{ $v['title'] }}</h3>
                                <p class="value-holo-desc">{{ $v['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
       HISTORY SECTION (Leader-style stacked sticky)
       ══════════════════════════════════════════ -->
    <div class="history-panels" id="historyPanels">
        @php
            $milestones = [
                ['year' => '2022', 'title' => __('about.timeline_founding'), 'desc' => __('about.timeline_founding_desc'), 'icon' => 'fas fa-flag'],
                ['year' => '2023', 'title' => __('about.timeline_growth'), 'desc' => __('about.timeline_growth_desc'), 'icon' => 'fas fa-chart-line'],
                ['year' => '2024', 'title' => __('about.timeline_global'), 'desc' => __('about.timeline_global_desc'), 'icon' => 'fas fa-globe'],
                ['year' => '2025', 'title' => __('about.timeline_partners'), 'desc' => __('about.timeline_partners_desc'), 'icon' => 'fas fa-handshake'],
                ['year' => '2026', 'title' => __('about.timeline_new_era'), 'desc' => __('about.timeline_new_era_desc'), 'icon' => 'fas fa-rocket'],
            ];
        @endphp
        @foreach($milestones as $i => $m)
            <div class="history-panel" data-history-index="{{ $i }}">
                <div class="history-panel-bg"></div>
                <div class="history-panel-inner">
                    <div class="history-panel-visual">
                        <div class="history-ring"></div>
                        <div class="history-ring history-ring-2"></div>
                        <div class="history-year-badge">{{ $m['year'] }}</div>
                        <div class="history-panel-icon"><i class="{{ $m['icon'] }}"></i></div>
                    </div>
                    <div class="history-panel-text">
                        <div class="history-panel-index">0{{ $i + 1 }} / 05</div>
                        <h3 class="history-panel-year">{{ $m['year'] }}</h3>
                        <div class="history-panel-title">{{ $m['title'] }}</div>
                        <p class="history-panel-desc">{{ $m['desc'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- ══════════════════════════════════════════
       TEAM SECTION (Leader-style stacked sticky)
       ══════════════════════════════════════════ -->
    <div class="team-panels" id="teamPanels">
        @php
            $team = [
                ['name' => __('about.team_alex_name'), 'role' => __('about.team_alex_role'), 'initials' => 'ЭС', 'icon' => 'fas fa-crown'],
                ['name' => __('about.team_mike_name'), 'role' => __('about.team_mike_role'), 'initials' => 'РВ', 'icon' => 'fas fa-code'],
                ['name' => __('about.team_sara_name'), 'role' => __('about.team_sara_role'), 'initials' => '💻', 'icon' => 'fas fa-code'],
            ];
        @endphp
        @foreach($team as $i => $member)
            <div class="team-panel" data-team-index="{{ $i }}">
                <div class="team-panel-inner">
                    <div class="team-panel-visual">
                        <div class="team-panel-ring"></div>
                        <div class="team-panel-ring team-panel-ring-2"></div>
                        <div class="team-panel-avatar">{{ $member['initials'] }}</div>
                    </div>
                    <div class="team-panel-text">
                        <div class="team-panel-index">0{{ $i + 1 }} / 03</div>
                        <h3 class="team-panel-name">{{ $member['name'] }}</h3>
                        <div class="team-panel-role"><i class="{{ $member['icon'] }}"></i> {{ $member['role'] }}</div>
                        <p class="team-panel-desc">{{ __('about.team_' . ['alex', 'sara', 'mike'][$i] . '_desc') }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- ══════════════════════════════════════════
       CTA SECTION
       ══════════════════════════════════════════ -->
    <section class="cta-section noise" id="cta">
        <div class="cta-bg-gradient"></div>
        <div class="container">
            <div class="cta-content reveal-scale">
                <div class="section-tag" style="margin:0 auto 24px;display:inline-flex"><i class="fas fa-rocket"></i> {{ __('about.cta_title') }}</div>
                <h2 class="cta-title">{{ __('about.cta_title') }}</h2>
                <p class="cta-desc">{{ __('about.cta_desc') }}</p>
                <div class="cta-actions">
                    <a href="{{ route('courses.index') }}" class="btn btn-primary magnetic">
                        <i class="fas fa-arrow-right"></i>
                        <span>{{ __('about.cta_button') }}</span>
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-secondary magnetic">
                        <i class="fas fa-user-plus"></i>
                        <span>{{ __('Register') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        (function () {
            'use strict';
            /* ══════════════════════════════════════
               CONFIGURATION
               ══════════════════════════════════════ */
            let scrollY = 0;
            let ticking = false;
            let vh = window.innerHeight;

            /* ══════════════════════════════════════
               RESIZE HANDLER
               ══════════════════════════════════════ */
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    vh = window.innerHeight;
                }, 150);
            });

            /* ══════════════════════════════════════
               PARTICLES
               ══════════════════════════════════════ */
            const particlesCanvas = document.getElementById('particles-canvas');
            const ctx = particlesCanvas ? particlesCanvas.getContext('2d') : null;
            let particles = [];
            let pW = 0, pH = 0;
            function resizeParticles() {
                if (!particlesCanvas) return;
                pW = particlesCanvas.width = window.innerWidth;
                pH = particlesCanvas.height = window.innerHeight;
            }
            resizeParticles();
            window.addEventListener('resize', resizeParticles);
            const particleCount = window.innerWidth < 768 ? 15 : 20;
            for (let i = 0; i < particleCount; i++) {
                particles.push({
                    x: Math.random() * pW,
                    y: Math.random() * pH,
                    vx: (Math.random() - 0.5) * 0.3,
                    vy: (Math.random() - 0.5) * 0.3,
                    r: Math.random() * 1.5 + 0.5
                });
            }
            let particleRAF;
            function drawParticles() {
                if (!ctx) return;
                ctx.clearRect(0, 0, pW, pH);
                const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#00F5D4';
                for (let i = 0; i < particles.length; i++) {
                    const p = particles[i];
                    p.x += p.vx;
                    p.y += p.vy;
                    if (p.x < 0) p.x = pW;
                    if (p.x > pW) p.x = 0;
                    if (p.y < 0) p.y = pH;
                    if (p.y > pH) p.y = 0;
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                    ctx.fillStyle = accentColor;
                    ctx.globalAlpha = 0.15;
                    ctx.fill();
                    for (let j = i + 1; j < particles.length; j++) {
                        const dx = p.x - particles[j].x;
                        const dy = p.y - particles[j].y;
                        const dist = dx * dx + dy * dy;
                        if (dist < 22500) {
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.strokeStyle = accentColor;
                            ctx.globalAlpha = 0.03 * (1 - dist / 22500);
                            ctx.stroke();
                        }
                    }
                }
                ctx.globalAlpha = 1;
                particleRAF = requestAnimationFrame(drawParticles);
            }
            drawParticles();

            /* ══════════════════════════════════════
               SCROLL REVEAL (IntersectionObserver)
               ══════════════════════════════════════ */
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    entry.target.classList.toggle('visible', entry.isIntersecting);
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
            document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale').forEach(el => {
                revealObserver.observe(el);
            });

            /* ══════════════════════════════════════
               MAGNETIC BUTTONS
               ══════════════════════════════════════ */
            document.querySelectorAll('.magnetic').forEach(btn => {
                let rect;
                btn.addEventListener('mouseenter', () => {
                    rect = btn.getBoundingClientRect();
                });
                btn.addEventListener('mousemove', (e) => {
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;
                    btn.style.transition = 'transform 0.15s ease-out';
                    btn.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
                });
                btn.addEventListener('mouseleave', () => {
                    btn.style.transition = 'transform 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
                    btn.style.transform = 'translate(0, 0)';
                });
            });

            /* ══════════════════════════════════════
               MISSION FADE REVEAL (Journey-style)
               ══════════════════════════════════════ */
            const missionSection = document.querySelector('.mission-section');
            const missionPanels = document.querySelectorAll('.mission-panel');
            let lastMissionIndex = -1;
            function updateMission() {
                if (!missionSection) return;
                const sectionTop = missionSection.offsetTop;
                const sectionHeight = missionSection.offsetHeight - vh;
                const scrolled = scrollY - sectionTop;
                if (scrolled > sectionHeight) return;
                if (scrolled < -vh) {
                    missionPanels.forEach(p => p.classList.remove('active', 'exit-up'));
                    lastMissionIndex = -1;
                    return;
                }
                const progress = Math.max(0, Math.min(1, scrolled / sectionHeight));
                const totalPanels = missionPanels.length;
                const activeIndex = Math.min(totalPanels - 1, Math.floor(progress * totalPanels));
                if (activeIndex !== lastMissionIndex) {
                    lastMissionIndex = activeIndex;
                    missionPanels.forEach((panel, i) => {
                        panel.classList.remove('active', 'exit-up');
                        if (i < activeIndex) {
                            panel.classList.add('exit-up');
                        } else if (i === activeIndex) {
                            panel.classList.add('active');
                        }
                    });
                }
            }

            /* ══════════════════════════════════════
               VALUES FADE REVEAL (Journey-style)
               ══════════════════════════════════════ */
            const valuesSection = document.querySelector('.values-section');
            const valuePanels = document.querySelectorAll('.value-panel');
            let lastValueIndex = -1;
            function updateValues() {
                if (!valuesSection) return;
                const sectionTop = valuesSection.offsetTop;
                const sectionHeight = valuesSection.offsetHeight - vh;
                const scrolled = scrollY - sectionTop;
                if (scrolled > sectionHeight) return;
                if (scrolled < -vh) {
                    valuePanels.forEach(p => p.classList.remove('active', 'exit-up'));
                    lastValueIndex = -1;
                    return;
                }
                const progress = Math.max(0, Math.min(1, scrolled / sectionHeight));
                const totalPanels = valuePanels.length;
                const activeIndex = Math.min(totalPanels - 1, Math.floor(progress * totalPanels));
                if (activeIndex !== lastValueIndex) {
                    lastValueIndex = activeIndex;
                    valuePanels.forEach((panel, i) => {
                        panel.classList.remove('active', 'exit-up');
                        if (i < activeIndex) {
                            panel.classList.add('exit-up');
                        } else if (i === activeIndex) {
                            panel.classList.add('active');
                        }
                    });
                }
            }

            /* ══════════════════════════════════════
               HISTORY FULL-SCREEN SCROLL (Leader-style)
               ══════════════════════════════════════ */
            const historyPanels = document.querySelectorAll('.history-panel');
            const historyContainer = document.getElementById('historyPanels');
            let lastHistoryIndex = -1;
            function updateHistoryScroll() {
                if (!historyContainer || !historyPanels.length) return;
                const scrollY = window.scrollY;
                const vh = window.innerHeight;
                const containerTop = historyContainer.offsetTop;
                const scrolled = Math.max(0, scrollY - containerTop);
                const panelH = vh;
                const totalPanels = historyPanels.length;
                const activeIndex = Math.min(totalPanels - 1, Math.floor(scrolled / panelH));
                historyPanels.forEach((panel, i) => {
                    panel.classList.toggle('active', i === activeIndex);
                });
                lastHistoryIndex = activeIndex;
            }

            /* ══════════════════════════════════════
               TEAM FULL-SCREEN SCROLL (Leader-style)
               ══════════════════════════════════════ */
            const teamPanels = document.querySelectorAll('.team-panel');
            const teamContainer = document.getElementById('teamPanels');
            let lastTeamIndex = -1;
            function updateTeamScroll() {
                if (!teamContainer || !teamPanels.length) return;
                const scrollY = window.scrollY;
                const vh = window.innerHeight;
                const containerTop = teamContainer.offsetTop;
                const scrolled = Math.max(0, scrollY - containerTop);
                const panelH = vh;
                const totalPanels = teamPanels.length;
                const activeIndex = Math.min(totalPanels - 1, Math.floor(scrolled / panelH));
                teamPanels.forEach((panel, i) => {
                    panel.classList.toggle('active', i === activeIndex);
                });
                lastTeamIndex = activeIndex;
            }

            /* ══════════════════════════════════════
               MASTER SCROLL HANDLER
               ══════════════════════════════════════ */
            function onScroll() {
                scrollY = window.scrollY;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                const scrollPercent = docHeight > 0 ? (scrollY / docHeight) * 100 : 0;
                const progressBar = document.getElementById('scrollProgress');
                if (progressBar) progressBar.style.width = scrollPercent + '%';
                updateMission();
                updateValues();
                updateHistoryScroll();
                updateTeamScroll();
            }
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(() => {
                        onScroll();
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });
            setTimeout(onScroll, 100);

            /* ══════════════════════════════════════
               VISIBILITY CLEANUP
               ══════════════════════════════════════ */
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    if (particleRAF) cancelAnimationFrame(particleRAF);
                } else {
                    if (ctx) drawParticles();
                }
            });
        })();
    </script>
@endsection
