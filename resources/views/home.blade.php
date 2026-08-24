@extends('layouts.app')
@section('title', t('home_title'))
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

        /* ══════════════════════════════════════════
       GLOBAL UTILITIES
       ══════════════════════════════════════════ */
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
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--border);
        }

        .noise {
            position: relative;
        }

        .noise::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            opacity: 0.015;
            pointer-events: none;
            mix-blend-mode: overlay;
        }

        /* ══════════════════════════════════════════
       GLOBAL STYLES
       ══════════════════════════════════════════ */
        /* ══════════════════════════════════════════
       NAVIGATION DOTS
       ══════════════════════════════════════════ */
        .nav-dots {
            position: fixed;
            right: 24px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 16px;
            opacity: 0;
            transition: opacity 0.5s ease;
            pointer-events: none;
        }

        .nav-dots.visible {
            opacity: 1;
            pointer-events: auto;
        }

        .nav-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--border-hover);
            cursor: pointer;
            transition: all 0.4s var(--ease-out-expo);
            position: relative;
        }

        .nav-dot::before {
            content: '';
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            border: 1px solid transparent;
            transition: border-color 0.3s;
        }

        .nav-dot.active {
            background: var(--accent);
            transform: scale(1.5);
            box-shadow: 0 0 20px var(--accent-glow-strong);
        }

        .nav-dot.active::before {
            border-color: var(--accent-glow-strong);
        }

        .nav-dot:hover {
            transform: scale(1.3);
        }

        .nav-dot-label {
            position: absolute;
            right: 24px;
            top: 50%;
            transform: translateY(-50%) translateX(10px);
            white-space: nowrap;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: var(--text-muted);
            opacity: 0;
            transition: all 0.3s var(--ease-out-expo);
            pointer-events: none;
        }

        .nav-dot:hover .nav-dot-label {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }

        /* ══════════════════════════════════════════
       HERO SECTION
       ══════════════════════════════════════════ */
        .hero {
            position: relative;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 80px;
            padding: 120px 0;
            overflow: hidden;
            perspective: 2000px;
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
                radial-gradient(ellipse 60% 80% at 80% 60%, rgba(139, 92, 246, 0.06) 0%, transparent 50%),
                radial-gradient(ellipse 40% 40% at 50% 20%, rgba(245, 158, 11, 0.04) 0%, transparent 50%);
            animation: meshMove 30s ease-in-out infinite;
        }

        @@keyframes meshMove {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(2%, -2%) rotate(1deg);
            }

            50% {
                transform: translate(-1%, 3%) rotate(-0.5deg);
            }

            75% {
                transform: translate(3%, 1%) rotate(0.5deg);
            }
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
            width: 600px;
            height: 600px;
            background: var(--accent);
            opacity: 0.06;
            top: -20%;
            left: -10%;
            animation: orbFloat1 20s ease-in-out infinite;
        }

        .hero-orb-2 {
            width: 500px;
            height: 500px;
            background: var(--accent-2);
            opacity: 0.05;
            bottom: -15%;
            right: -5%;
            animation: orbFloat2 25s ease-in-out infinite;
        }

        .hero-orb-3 {
            width: 300px;
            height: 300px;
            background: var(--accent-4);
            opacity: 0.04;
            top: 60%;
            left: 40%;
            animation: orbFloat3 15s ease-in-out infinite;
        }

        @@keyframes orbFloat1 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(80px, 60px) scale(1.1);
            }

            66% {
                transform: translate(-40px, 100px) scale(0.9);
            }
        }

        @@keyframes orbFloat2 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(-60px, -80px) scale(1.05);
            }

            66% {
                transform: translate(50px, -40px) scale(0.95);
            }
        }

        @@keyframes orbFloat3 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(-100px, -60px) scale(1.2);
            }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding-left: clamp(20px, 5vw, 80px);
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
            width: 6px;
            height: 6px;
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

        .hero-title-line {
            display: block;
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

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .hero-description {
            font-size: clamp(16px, 1.8vw, 20px);
            color: var(--text-secondary);
            line-height: 1.7;
            max-width: 520px;
            margin-bottom: 44px;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.8s 0.6s var(--ease-out-expo) forwards;
        }

        .hero-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 64px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s 0.8s var(--ease-out-expo) forwards;
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

        .btn-primary:hover::before {
            opacity: 1;
        }

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
            background: var(--glass-bg, rgba(255, 255, 255, 0.04));
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

        .hero-stats {
            display: flex;
            gap: 48px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s 1s var(--ease-out-expo) forwards;
        }

        .hero-stat {
            position: relative;
        }

        .hero-stat::after {
            content: '';
            position: absolute;
            right: -24px;
            top: 50%;
            transform: translateY(-50%);
            width: 1px;
            height: 40px;
            background: var(--border);
        }

        .hero-stat:last-child::after {
            display: none;
        }

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

        /* Hero Visual (Right Side) */
        .hero-visual {
            position: relative;
            z-index: 2;
            height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1200px;
        }

        .hero-code-window {
            position: relative;
            width: 100%;
            max-width: 580px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-lg), var(--shadow-glow);
            transform-style: preserve-3d;
            opacity: 0;
            transform: perspective(1200px) rotateY(-8deg) rotateX(4deg) translateZ(0);
            animation: codeWindowIn 1.2s 0.6s var(--ease-out-expo) forwards;
        }

        @@keyframes codeWindowIn {
            to {
                opacity: 1;
                transform: perspective(1200px) rotateY(-4deg) rotateX(2deg) translateZ(0);
            }
        }

        .code-window-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            background: rgba(0, 0, 0, 0.2);
        }

        .code-window-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .code-window-dot.red {
            background: #ff5f57;
        }

        .code-window-dot.yellow {
            background: #febc2e;
        }

        .code-window-dot.green {
            background: #28c840;
        }

        .code-window-tabs {
            display: flex;
            gap: 2px;
            margin-left: 16px;
        }

        .code-window-tab {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-family: var(--font-mono);
            color: var(--text-muted);
            background: transparent;
            transition: all 0.3s;
        }

        .code-window-tab.active {
            background: var(--accent-glow);
            color: var(--accent);
        }

        .code-window-body {
            padding: 24px;
            font-family: var(--font-mono);
            font-size: 13px;
            line-height: 2;
            min-height: 340px;
        }

        .code-line {
            display: flex;
            align-items: center;
            gap: 12px;
            opacity: 0;
            transform: translateX(-10px);
            animation: codeLineIn 0.5s var(--ease-out-expo) forwards;
        }

        .code-line:nth-child(1) {
            animation-delay: 1s;
        }

        .code-line:nth-child(2) {
            animation-delay: 1.2s;
        }

        .code-line:nth-child(3) {
            animation-delay: 1.4s;
        }

        .code-line:nth-child(4) {
            animation-delay: 1.6s;
        }

        .code-line:nth-child(5) {
            animation-delay: 1.8s;
        }

        .code-line:nth-child(6) {
            animation-delay: 2s;
        }

        .code-line:nth-child(7) {
            animation-delay: 2.2s;
        }

        .code-line:nth-child(8) {
            animation-delay: 2.4s;
        }

        @@keyframes codeLineIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .code-line-num {
            color: var(--text-muted);
            min-width: 24px;
            text-align: right;
            user-select: none;
            font-size: 11px;
        }

        .code-keyword {
            color: #c678dd;
        }

        .code-function {
            color: #61afef;
        }

        .code-string {
            color: #98c379;
        }

        .code-comment {
            color: #5c6370;
            font-style: italic;
        }

        .code-class {
            color: #e5c07b;
        }

        .code-variable {
            color: #e06c75;
        }

        .code-operator {
            color: #56b6c2;
        }

        .code-cursor {
            display: inline-block;
            width: 2px;
            height: 1.2em;
            background: var(--accent);
            animation: blink 1s step-end infinite;
            vertical-align: text-bottom;
            margin-left: 2px;
        }

        @@keyframes blink {
            50% {
                opacity: 0;
            }
        }

        /* Hero floating elements */
        .hero-float {
            position: absolute;
            pointer-events: none;
        }

        .hero-float-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 14px 18px;
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            opacity: 0;
            animation: floatIn 1s var(--ease-out-expo) forwards;
        }

        .hero-float-card-1 {
            top: 10%;
            right: 0;
            animation-delay: 1.5s;
        }

        .hero-float-card-2 {
            bottom: 15%;
            left: -20px;
            animation-delay: 1.8s;
        }

        .hero-float-card-3 {
            top: 50%;
            right: -30px;
            animation-delay: 2.1s;
        }

        @@keyframes floatIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .hero-float-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .hero-float-icon.green {
            background: rgba(34, 197, 94, 0.15);
            color: #22c55e;
        }

        .hero-float-icon.purple {
            background: rgba(139, 92, 246, 0.15);
            color: #8b5cf6;
        }

        .hero-float-icon.amber {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }

        .hero-float-text strong {
            display: block;
            font-size: 13px;
            color: var(--text);
        }

        .hero-float-text span {
            font-size: 11px;
            color: var(--text-muted);
        }

        @@keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @@keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @@keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.5);
            }
        }

        /* ══════════════════════════════════════════
       MARQUEE SECTION (Social Proof)
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

        .marquee-track:hover {
            animation-play-state: paused;
        }

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

        .marquee-item:hover {
            color: var(--text);
        }

        .marquee-item i {
            font-size: 24px;
        }

        @@keyframes marquee {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        .marquee-fade-left,
        .marquee-fade-right {
            position: absolute;
            top: 0;
            bottom: 0;
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
       JOURNEY SECTION (Fade Reveal)
       ══════════════════════════════════════════ */
        .journey-section {
            position: relative;
            height: 400vh;
        }

        .journey-sticky {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
        }

        .journey-track {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .journey-panel {
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

        .journey-panel.active {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        .journey-panel.exit-up {
            opacity: 0;
            transform: translateY(-40px) scale(0.96);
        }

        .journey-panel-content {
            max-width: 1200px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .journey-number {
            font-size: clamp(120px, 20vw, 280px);
            font-weight: 900;
            line-height: 0.8;
            background: linear-gradient(180deg, var(--border-hover) 0%, transparent 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: absolute;
            top: 50%;
            left: 5%;
            transform: translateY(-50%);
            opacity: 0.3;
            user-select: none;
        }

        .journey-step-tag {
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
        }

        .journey-step-tag.green {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .journey-step-tag.red {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .journey-step-tag.amber {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .journey-step-tag.blue {
            background: var(--accent-glow);
            color: var(--accent);
            border: 1px solid rgba(0, 245, 212, 0.2);
        }

        .journey-title {
            font-size: clamp(32px, 4.5vw, 56px);
            font-weight: 800;
            letter-spacing: -2px;
            line-height: 1.1;
            margin-bottom: 20px;
            color: var(--text);
        }

        .journey-desc {
            font-size: 17px;
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 32px;
            max-width: 480px;
        }

        /* Journey Panel 1 - Terminal */
        .journey-terminal {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            transform: perspective(1000px) rotateY(5deg);
            transition: transform 0.5s var(--ease-out-expo);
        }

        .journey-terminal:hover {
            transform: perspective(1000px) rotateY(0deg);
        }

        .terminal-header {
            display: flex;
            align-items: center;
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
            background: rgba(0, 0, 0, 0.3);
        }

        .terminal-dots {
            display: flex;
            gap: 6px;
        }

        .terminal-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .terminal-body {
            padding: 24px;
            font-family: var(--font-mono);
            font-size: 13px;
            line-height: 2;
        }

        .terminal-line {
            display: flex;
            gap: 8px;
        }

        .terminal-prompt {
            color: var(--accent);
        }

        .terminal-command {
            color: var(--text);
        }

        .terminal-output {
            color: var(--text-secondary);
        }

        .terminal-success {
            color: #22c55e;
        }

        .terminal-error {
            color: #ef4444;
        }

        /* Journey Panel 2 - Bug Cards */
        .journey-bugs {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .journey-bug-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px 22px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            transition: all 0.4s var(--ease-out-expo);
            transform: perspective(600px) rotateX(2deg) translateZ(0);
        }

        .journey-bug-card:hover {
            border-color: var(--border-hover);
            transform: perspective(600px) rotateX(0deg) translateX(8px);
            box-shadow: var(--shadow-md);
        }

        .journey-bug-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .journey-bug-icon.error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .journey-bug-icon.warning {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .journey-bug-icon.info {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .journey-bug-text h4 {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .journey-bug-text p {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Journey Panel 3 - Success Terminal */
        .journey-success-terminal {
            background: var(--card);
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-lg), 0 0 80px rgba(34, 197, 94, 0.1);
            transform: perspective(1000px) rotateY(-5deg);
            transition: transform 0.5s var(--ease-out-expo);
        }

        .journey-success-terminal:hover {
            transform: perspective(1000px) rotateY(0deg);
        }

        /* ══════════════════════════════════════════
       LEADERS SECTION (Full-Screen Scroll)
       ══════════════════════════════════════════ */
        .leaders-intro {
            padding: 160px 0 80px;
            position: relative;
            background: var(--bg-2);
            text-align: center;
        }

        .leaders-intro::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
        }

        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

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

        /* Leader panel — full-screen card */
        .leaders-panels {
            position: relative;
            background: var(--bg-1);
        }

        .leader-panel {
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            z-index: 1;
            background: var(--bg-1);
        }

        .leader-panel-bg {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.6s;
        }

        .leader-panel.active .leader-panel-bg {
            opacity: 1;
        }

        .leader-panel-inner {
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

        /* Left: Avatar + visuals */
        .leader-panel-visual {
            position: relative;
            display: flex;
            justify-content: center;
        }

        .leader-panel-avatar {
            width: 280px;
            height: 280px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid var(--border);
            position: relative;
            opacity: 0;
            transform: scale(0.6) translateY(60px);
            transition: all 0.8s var(--ease-out-expo);
            box-shadow: 0 0 80px rgba(0, 0, 0, 0.3);
        }

        .leader-panel.active .leader-panel-avatar {
            opacity: 1;
            transform: scale(1) translateY(0);
            border-color: var(--accent);
            box-shadow: 0 0 80px var(--accent-glow-strong), 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .leader-panel-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .leader-panel-ring {
            position: absolute;
            width: 320px;
            height: 320px;
            border: 1px solid var(--border);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.5) rotate(0deg);
            opacity: 0;
            transition: all 1s var(--ease-out-expo) 0.2s;
            pointer-events: none;
        }

        .leader-panel.active .leader-panel-ring {
            opacity: 0.4;
            transform: translate(-50%, -50%) scale(1) rotate(30deg);
        }

        .leader-panel-ring-2 {
            width: 380px;
            height: 380px;
            border-style: dashed;
            transition-delay: 0.3s;
        }

        .leader-panel.active .leader-panel-ring-2 {
            transform: translate(-50%, -50%) scale(1) rotate(-20deg);
        }

        .leader-panel-icon {
            position: absolute;
            bottom: -10px;
            right: 40px;
            width: 56px;
            height: 56px;
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

        .leader-panel.active .leader-panel-icon {
            opacity: 1;
            transform: translateY(0);
        }

        /* Right: Text content */
        .leader-panel-text {
            opacity: 0;
            transform: translateX(60px);
            transition: all 0.8s var(--ease-out-expo) 0.15s;
        }

        .leader-panel.active .leader-panel-text {
            opacity: 1;
            transform: translateX(0);
        }

        .leader-panel-index {
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

        .leader-panel-index::before {
            content: '';
            width: 40px;
            height: 1px;
            background: var(--accent);
        }

        .leader-panel-name {
            font-size: clamp(36px, 4vw, 56px);
            font-weight: 800;
            letter-spacing: -2px;
            line-height: 1.1;
            color: var(--text);
            margin-bottom: 16px;
        }

        .leader-panel-company {
            font-size: 20px;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .leader-panel-company i {
            font-size: 24px;
        }

        .leader-panel-desc {
            font-size: 16px;
            color: var(--text-secondary);
            line-height: 1.8;
            max-width: 440px;
        }

        .leader-panel-quote {
            margin-top: 32px;
            padding: 20px 24px;
            border-left: 3px solid var(--accent);
            background: var(--accent-glow);
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
            font-style: italic;
            font-size: 15px;
            color: var(--text);
            line-height: 1.6;
            opacity: 0;
            transform: translateX(40px);
            transition: all 0.6s var(--ease-out-expo) 0.4s;
        }

        .leader-panel.active .leader-panel-quote {
            opacity: 1;
            transform: translateX(0);
        }

        .ai-chat-msg .ai-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--accent);
            padding: 5px 12px;
            background: var(--accent-glow);
            border-radius: 100px;
        }

        /* ══════════════════════════════════════════
       CINEMATIC 3D SCROLL ENGINE
       ══════════════════════════════════════════ */
        .cinematic-scene {
            position: relative;
            perspective: 2500px;
            perspective-origin: 50% 50%;
            z-index: 10;
            background: var(--bg);
        }

        .sticky-viewport {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            transform-style: preserve-3d;
        }

        .cinematic-bg-grid {
            position: absolute;
            inset: -50%;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            transform: rotateX(60deg) translateZ(-200px) scale(2.5);
            transform-style: preserve-3d;
            mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
            pointer-events: none;
        }

        .z-axis-panel {
            position: absolute;
            width: 90%;
            max-width: 1100px;
            transform-style: preserve-3d;
            will-change: transform, opacity;
            pointer-events: none;
            opacity: 0;
        }

        .z-axis-panel.is-active {
            pointer-events: auto;
        }

        /* Holographic 3D Cards */
        .holo-card {
            background: var(--card);
            backdrop-filter: blur(30px) saturate(180%);
            border: 1px solid var(--border);
            border-radius: 32px;
            transform-style: preserve-3d;
            padding: 48px;
            box-shadow:
                0 50px 120px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.1),
                0 0 80px rgba(var(--accent-rgb, 0, 245, 212), 0.05);
            position: relative;
            overflow: hidden;
        }

        .holo-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent 0%, rgba(255, 255, 255, 0.05) 10%, transparent 20%);
            animation: holoSpin 8s linear infinite;
            pointer-events: none;
        }

        @@keyframes holoSpin {
            to {
                transform: rotate(360deg);
            }
        }

        .holo-layer {
            transform-style: preserve-3d;
            position: relative;
        }

        .holo-layer-bg {
            transform: translateZ(0px);
        }

        .holo-layer-text {
            transform: translateZ(40px);
        }

        .holo-layer-icon {
            transform: translateZ(80px);
            filter: drop-shadow(0 20px 30px rgba(0, 0, 0, 0.5));
        }

        .holo-layer-code {
            transform: translateZ(60px) rotateX(10deg);
            transform-origin: bottom;
        }

        /* AI HUD Interface */
        .ai-hud-scene {
            transform-style: preserve-3d;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ai-hud-core {
            position: absolute;
            transform-style: preserve-3d;
            transition: transform 0.1s linear;
        }

        .ai-hud-chat {
            position: absolute;
            width: 100%;
            max-width: 600px;
            transform-style: preserve-3d;
            opacity: 0;
        }

        .ai-hud-msg {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 12px;
            transform: translateZ(20px);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .ai-hud-msg.bot {
            border-color: var(--accent-glow-strong);
            background: rgba(var(--accent-rgb, 0, 245, 212), 0.05);
            transform: translateZ(40px);
        }
        .holo-card {
            padding: 24px;
        }

        .holo-layer-icon {
            transform: translateZ(40px);
        }

            .holo-layer-text {
                transform: translateZ(20px);
            }
        }

        /* Feature Card Extras (for Z-axis) */
        .feature-icon-wrap {
            width: 80px;
            height: 80px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
        }

        .feature-title {
            font-size: 42px;
            font-weight: 900;
            margin-bottom: 16px;
        }

        .feature-desc {
            font-size: 18px;
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .feature-code {
            background: var(--card);
            border-radius: 14px;
            padding: 22px;
            font-family: var(--font-mono);
            font-size: 12px;
            line-height: 2;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        .feature-code-dots {
            display: flex;
            gap: 7px;
        }

        .feature-code-dots span {
            width: 11px;
            height: 11px;
            border-radius: 50%;
        }

        .feature-code-dots span:nth-child(1) {
            background: #ff5f57;
        }

        .feature-code-dots span:nth-child(2) {
            background: #febc2e;
        }

        .feature-code-dots span:nth-child(3) {
            background: #28c840;
        }

        .feature-code-body {
            margin-top: 20px;
        }

        .feature-code-line {
            display: block;
            margin-top: 4px;
        }

        .feature-chart {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            height: 150px;
        }

        .feature-bar {
            flex: 1;
            border-radius: 8px 8px 0 0;
            min-height: 8px;
        }

        .feature-stats {
            display: flex;
            gap: 14px;
        }

        .feature-stat {
            flex: 1;
            padding: 18px;
            border-radius: 14px;
            background: var(--card);
            border: 1px solid var(--border);
            text-align: center;
        }

        .feature-stat-num {
            font-size: 30px;
            font-weight: 900;
            color: var(--accent);
            font-variant-numeric: tabular-nums;
            line-height: 1;
        }

        .feature-stat-label {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 8px;
        }

        .feature-chat {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .feature-chat-msg {
            padding: 16px 20px;
            border-radius: 20px;
            font-size: 13px;
            line-height: 1.55;
            max-width: 90%;
        }

        .feature-chat-msg.user {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(0, 245, 212, 0.1));
            color: var(--text);
            align-self: flex-end;
            border-bottom-right-radius: 6px;
            border: 1px solid rgba(139, 92, 246, 0.15);
        }

        .feature-chat-msg.ai {
            background: var(--card);
            color: var(--text);
            align-self: flex-start;
            border-bottom-left-radius: 6px;
            border: 1px solid var(--border);
        }

        /* ═══════════════════════════════════════
           FEATURE STEPS — STICKY SCROLL
           ═══════════════════════════════════════ */
        .fp-steps-panels {
            position: relative;
            height: 600vh;
        }

        .fp-step {
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

        .fp-step.active {
            z-index: 2;
        }

        .fp-step::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--step-color), transparent 70%);
            opacity: 0;
            transition: opacity 0.8s ease;
            pointer-events: none;
            filter: blur(80px);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .fp-step.active::before {
            opacity: 0.12;
        }

        .fp-step::after {
            content: '';
            position: absolute;
            inset: 0;
            border: 2px solid transparent;
            transition: border-color 0.6s ease, box-shadow 0.6s ease;
            pointer-events: none;
        }

        .fp-step.active::after {
            border-color: rgba(255,255,255,0.06);
            box-shadow: inset 0 0 80px rgba(255,255,255,0.02);
        }

        .fp-step-inner {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            max-width: 700px;
            padding: 0 32px;
            opacity: 0;
            transform: scale(0.85) translateY(60px);
            transition: opacity 0.9s cubic-bezier(0.16, 1, 0.3, 1), transform 0.9s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .fp-step.active .fp-step-inner {
            opacity: 1;
            transform: scale(1) translateY(0);
        }

        .fp-step:not(.active) .fp-step-inner {
            opacity: 0;
            transform: scale(1.1) translateY(-40px);
        }

        .fp-step-num {
            font-size: clamp(80px, 12vw, 160px);
            font-weight: 900;
            color: var(--step-color);
            line-height: 1;
            margin-bottom: 24px;
            opacity: 0.15;
            transition: opacity 0.8s ease 0.1s;
            font-family: var(--font-mono);
            letter-spacing: -6px;
        }

        .fp-step.active .fp-step-num {
            opacity: 1;
        }

        .fp-step-title {
            font-size: clamp(28px, 4vw, 48px);
            font-weight: 800;
            color: var(--text);
            margin-bottom: 16px;
            letter-spacing: -0.5px;
            line-height: 1.15;
        }

        .fp-step-desc {
            font-size: clamp(15px, 1.8vw, 18px);
            line-height: 1.7;
            color: var(--text-secondary);
            max-width: 520px;
        }

        @@media (max-width: 600px) {
            .fp-step-inner { padding: 0 20px; }
            .fp-step-num { letter-spacing: -3px; }
            .fp-visual { transform: scale(0.7); margin-bottom: 10px; }
        }

        /* ═══════════════════════════════════════
           FEATURE STEPS — STEP VISUALS
           ═══════════════════════════════════════ */
        .fp-visual {
            position: relative;
            width: 340px;
            height: 260px;
            margin-bottom: 32px;
        }
        /* --- Step 1: Courses floating cards --- */
        .fp-courses-ring {
            position: absolute;
            inset: 0;
            animation: fpRingSpin 20s linear infinite;
        }
        @@keyframes fpRingSpin { to { transform: rotate(360deg); } }
        .fp-course-mini {
            position: absolute;
            width: 80px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            animation: fpCardFloat 3s ease-in-out infinite alternate;
        }
        .fp-course-mini:nth-child(1) { top: 0; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg,#E44D26,#F16529); animation-delay: 0s; }
        .fp-course-mini:nth-child(2) { top: 30%; right: 0; background: linear-gradient(135deg,#F7DF1E,#323330); animation-delay: 0.5s; }
        .fp-course-mini:nth-child(3) { bottom: 0; right: 10%; background: linear-gradient(135deg,#61DAFB,#20232A); animation-delay: 1s; }
        .fp-course-mini:nth-child(4) { bottom: 0; left: 10%; background: linear-gradient(135deg,#3776AB,#FFD43B); animation-delay: 1.5s; }
        .fp-course-mini:nth-child(5) { top: 30%; left: 0; background: linear-gradient(135deg,#777BB4,#4F5B93); animation-delay: 2s; }
        .fp-course-mini:nth-child(6) { top: 50%; left: 50%; transform: translate(-50%,-50%); background: var(--gradient); width: 64px; height: 64px; border-radius: 50%; animation-delay: 0.3s; }
        @@keyframes fpCardFloat {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(-8px) scale(1.05); }
        }
        /* --- Step 2: Roadmap nodes --- */
        .fp-roadmap {
            position: absolute;
            inset: 0;
        }
        .fp-rm-node {
            position: absolute;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid var(--step-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--step-color);
            background: var(--card);
            animation: fpNodePulse 2s ease-in-out infinite;
        }
        .fp-rm-node:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
        .fp-rm-node:nth-child(2) { top: 5%; left: 45%; animation-delay: 0.3s; }
        .fp-rm-node:nth-child(3) { top: 10%; right: 10%; animation-delay: 0.6s; }
        .fp-rm-node:nth-child(4) { top: 50%; left: 25%; animation-delay: 0.9s; }
        .fp-rm-node:nth-child(5) { top: 50%; right: 15%; animation-delay: 1.2s; }
        .fp-rm-node:nth-child(6) { bottom: 10%; left: 40%; animation-delay: 1.5s; }
        .fp-rm-node.active-node { background: var(--step-color); color: #0f1117; box-shadow: 0 0 30px var(--step-color); }
        @@keyframes fpNodePulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255,255,255,0.1); }
            50% { box-shadow: 0 0 20px 4px rgba(255,255,255,0.05); }
        }
        .fp-rm-line {
            position: absolute;
            height: 2px;
            background: linear-gradient(90deg, var(--step-color), transparent);
            transform-origin: left center;
            opacity: 0.3;
        }
        .fp-rm-line:nth-child(7) { top: 14%; left: 14%; width: 100px; transform: rotate(12deg); }
        .fp-rm-line:nth-child(8) { top: 14%; left: 50%; width: 120px; transform: rotate(5deg); }
        .fp-rm-line:nth-child(9) { top: 54%; left: 28%; width: 160px; transform: rotate(-8deg); }
        .fp-rm-line:nth-child(10) { top: 54%; right: 20%; width: 80px; transform: rotate(50deg); }
        .fp-rm-line:nth-child(11) { bottom: 14%; left: 44%; width: 60px; transform: rotate(-30deg); }
        /* --- Step 3: Contest leaderboard --- */
        .fp-contest {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 10px;
        }
        .fp-contest-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 10px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            animation: fpBarSlide 0.8s ease-out backwards;
        }
        .fp-contest-bar:nth-child(1) { animation-delay: 0s; }
        .fp-contest-bar:nth-child(2) { animation-delay: 0.15s; }
        .fp-contest-bar:nth-child(3) { animation-delay: 0.3s; }
        .fp-contest-bar:nth-child(4) { animation-delay: 0.45s; }
        @@keyframes fpBarSlide { from { opacity: 0; transform: translateX(-30px); } }
        .fp-contest-rank {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            color: #0f1117;
        }
        .fp-contest-rank.gold { background: linear-gradient(135deg,#FFD700,#FFA500); }
        .fp-contest-rank.silver { background: linear-gradient(135deg,#C0C0C0,#A0A0A0); }
        .fp-contest-rank.bronze { background: linear-gradient(135deg,#CD7F32,#A0522D); }
        .fp-contest-rank.normal { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); }
        .fp-contest-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
        }
        .fp-contest-name { flex: 1; font-size: 13px; font-weight: 600; }
        .fp-contest-score {
            font-family: var(--font-mono);
            font-size: 13px;
            color: var(--step-color);
            font-weight: 700;
        }
        .fp-contest-timer {
            text-align: center;
            padding: 8px;
            border-radius: 10px;
            background: rgba(245,158,11,0.08);
            border: 1px solid rgba(245,158,11,0.2);
            font-family: var(--font-mono);
            font-size: 20px;
            font-weight: 800;
            color: #f59e0b;
            margin-top: auto;
            animation: fpTimerPulse 1s ease-in-out infinite;
        }
        @@keyframes fpTimerPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        /* --- Step 4: Jobs --- */
        .fp-jobs {
            position: absolute;
            inset: 0;
        }
        .fp-job-card {
            position: absolute;
            width: 140px;
            padding: 12px;
            border-radius: 12px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            animation: fpJobFloat 4s ease-in-out infinite alternate;
        }
        .fp-job-card:nth-child(1) { top: 5%; left: 5%; animation-delay: 0s; }
        .fp-job-card:nth-child(2) { top: 5%; right: 5%; animation-delay: 0.8s; }
        .fp-job-card:nth-child(3) { bottom: 30%; left: 15%; animation-delay: 1.6s; }
        .fp-job-card:nth-child(4) { bottom: 10%; right: 10%; animation-delay: 2.4s; }
        @@keyframes fpJobFloat { 0% { transform: translateY(0) rotate(0deg); } 100% { transform: translateY(-10px) rotate(1deg); } }
        .fp-job-logo {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #fff;
            margin-bottom: 8px;
        }
        .fp-job-title { font-size: 11px; font-weight: 700; margin-bottom: 3px; }
        .fp-job-company { font-size: 10px; color: rgba(255,255,255,0.35); margin-bottom: 6px; }
        .fp-job-salary {
            font-size: 12px;
            font-weight: 800;
            color: var(--step-color);
            font-family: var(--font-mono);
        }
        .fp-job-line {
            position: absolute;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--step-color), transparent);
            opacity: 0.15;
            animation: fpLinePulse 3s ease-in-out infinite;
        }
        .fp-job-line:nth-child(5) { top: 30%; left: 20%; width: 60%; transform: rotate(-5deg); }
        .fp-job-line:nth-child(6) { bottom: 35%; left: 10%; width: 70%; transform: rotate(3deg); animation-delay: 1s; }
        @@keyframes fpLinePulse { 0%, 100% { opacity: 0.1; } 50% { opacity: 0.3; } }
        /* --- Step 5: AI Brain --- */
        .fp-ai {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .fp-ai-core {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,245,212,0.2), transparent);
            border: 2px solid rgba(0,245,212,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: var(--accent);
            animation: fpAIBreathe 3s ease-in-out infinite;
            position: relative;
            z-index: 2;
        }
        @@keyframes fpAIBreathe {
            0%, 100% { box-shadow: 0 0 30px rgba(0,245,212,0.2); transform: scale(1); }
            50% { box-shadow: 0 0 60px rgba(0,245,212,0.4); transform: scale(1.05); }
        }
        .fp-ai-orbit {
            position: absolute;
            border-radius: 50%;
            border: 1px dashed rgba(0,245,212,0.15);
            animation: fpOrbitSpin 10s linear infinite;
        }
        .fp-ai-orbit:nth-child(2) { width: 180px; height: 180px; top: 50%; left: 50%; transform: translate(-50%,-50%); }
        .fp-ai-orbit:nth-child(3) { width: 260px; height: 260px; top: 50%; left: 50%; transform: translate(-50%,-50%); animation-direction: reverse; animation-duration: 15s; }
        @@keyframes fpOrbitSpin { to { transform: translate(-50%,-50%) rotate(360deg); } }
        .fp-ai-dot {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 10px var(--accent);
        }
        .fp-ai-orbit:nth-child(2) .fp-ai-dot { top: -4px; left: 50%; }
        .fp-ai-orbit:nth-child(3) .fp-ai-dot { bottom: -4px; right: 10%; }
        .fp-ai-code {
            position: absolute;
            padding: 6px 10px;
            border-radius: 6px;
            background: var(--card);
            border: 1px solid rgba(0,245,212,0.2);
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--accent);
            white-space: nowrap;
            animation: fpCodeFloat 5s ease-in-out infinite alternate;
        }
        .fp-ai-code:nth-child(4) { top: 15%; left: 5%; animation-delay: 0s; }
        .fp-ai-code:nth-child(5) { top: 20%; right: 5%; animation-delay: 1s; }
        .fp-ai-code:nth-child(6) { bottom: 20%; left: 8%; animation-delay: 2s; }
        .fp-ai-code:nth-child(7) { bottom: 15%; right: 8%; animation-delay: 3s; }
        @@keyframes fpCodeFloat { 0% { transform: translateY(0) scale(1); opacity: 0.7; } 100% { transform: translateY(-12px) scale(1.05); opacity: 1; } }
        /* --- Step 6: Community --- */
        .fp-community {
            position: absolute;
            inset: 0;
        }
        .fp-comm-avatar {
            position: absolute;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            border: 2px solid rgba(255,255,255,0.1);
            animation: fpCommPop 0.6s ease-out backwards;
        }
        .fp-comm-avatar:nth-child(1) { top: 8%; left: 15%; animation-delay: 0s; }
        .fp-comm-avatar:nth-child(2) { top: 5%; right: 20%; animation-delay: 0.1s; }
        .fp-comm-avatar:nth-child(3) { top: 35%; left: 5%; animation-delay: 0.2s; }
        .fp-comm-avatar:nth-child(4) { top: 40%; right: 8%; animation-delay: 0.3s; }
        .fp-comm-avatar:nth-child(5) { bottom: 25%; left: 20%; animation-delay: 0.4s; }
        .fp-comm-avatar:nth-child(6) { bottom: 20%; right: 15%; animation-delay: 0.5s; }
        .fp-comm-avatar:nth-child(7) { bottom: 5%; left: 40%; animation-delay: 0.6s; }
        @@keyframes fpCommPop { from { opacity: 0; transform: scale(0); } }
        .fp-comm-msg {
            position: absolute;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 11px;
            max-width: 120px;
            animation: fpMsgFly 6s ease-in-out infinite;
        }
        .fp-comm-msg:nth-child(8) { top: 18%; left: 30%; background: rgba(0,245,212,0.1); color: var(--accent); border: 1px solid rgba(0,245,212,0.2); animation-delay: 0s; }
        .fp-comm-msg:nth-child(9) { top: 50%; right: 20%; background: rgba(139,92,246,0.1); color: #8b5cf6; border: 1px solid rgba(139,92,246,0.2); animation-delay: 2s; }
        .fp-comm-msg:nth-child(10) { bottom: 15%; left: 35%; background: rgba(236,72,153,0.1); color: #ec4899; border: 1px solid rgba(236,72,153,0.2); animation-delay: 4s; }
        @@keyframes fpMsgFly { 0%, 100% { opacity: 0; transform: translateY(10px); } 10%, 90% { opacity: 1; transform: translateY(0); } }
        .fp-comm-line {
            position: absolute;
            height: 1px;
            opacity: 0.1;
        }
        .fp-comm-line:nth-child(11) { top: 12%; left: 20%; width: 100px; background: var(--accent); transform: rotate(15deg); }
        .fp-comm-line:nth-child(12) { top: 42%; left: 10%; width: 140px; background: #8b5cf6; transform: rotate(-10deg); }
        .fp-comm-line:nth-child(13) { bottom: 22%; left: 25%; width: 120px; background: #ec4899; transform: rotate(8deg); }

        /* Course Card Extras */
        .course-card-cover {
            height: 200px;
            position: relative;
        }

        .course-card-cover-bg {
            position: absolute;
            inset: 0;
            opacity: 0.4;
        }

        .course-card-cover-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 64px;
            color: white;
            opacity: 0.8;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.5));
        }

        .course-card-body {
            padding: 32px;
        }

        .course-card-title {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .course-card-instructor {
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .course-card-meta {
            display: flex;
            gap: 20px;
            font-size: 13px;
            color: var(--text-secondary);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 16px;
        }

        /* Course card colors */
        .course-card-cover-bg.html {
            background: linear-gradient(135deg, #E44D26, #F16529);
        }

        .course-card-cover-bg.js {
            background: linear-gradient(135deg, #F7DF1E, #323330);
        }

        .course-card-cover-bg.react {
            background: linear-gradient(135deg, #61DAFB, #20232A);
        }

        .course-card-cover-bg.python {
            background: linear-gradient(135deg, #3776AB, #FFD43B);
        }

        .course-card-cover-bg.php {
            background: linear-gradient(135deg, #777BB4, #8892BF);
        }

        .course-card-cover-bg.other {
            background: linear-gradient(135deg, #8B5CF6, #EC4899);
        }

        /* ═══════════════════════════════════════
           COURSES — CHAOTIC STACKED CARDS
           ═══════════════════════════════════════ */
        .cs-section {
            position: relative;
            z-index: 10;
        }
        .cs-header {
            text-align: center;
            padding: 100px 24px 40px;
        }
        .cs-header .section-title {
            font-size: clamp(32px, 4vw, 56px);
        }
        .cs-sub {
            text-align: center;
            font-size: 15px;
            color: var(--text-muted);
            padding: 0 24px 20px;
        }
        .cs-sticky {
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .cs-stack-area {
            position: relative;
            width: 100%;
            max-width: 1200px;
            height: 500px;
        }
        .cs-scatter-card {
            position: absolute;
            width: 260px;
            border-radius: 18px;
            overflow: hidden;
            background: var(--card);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
            text-decoration: none;
            color: inherit;
            cursor: pointer;
            will-change: transform, opacity;
        }
        .cs-card-cover {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .cs-card-cover::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, transparent 40%, rgba(15, 17, 23, 0.95));
            pointer-events: none;
        }
        .cs-card-cover i {
            font-size: 44px;
            color: rgba(255,255,255,0.85);
            filter: drop-shadow(0 6px 16px rgba(0,0,0,0.5));
            z-index: 1;
        }
        .cs-card-cover.html { background: linear-gradient(135deg, #E44D26, #F16529, #F7931E); }
        .cs-card-cover.js { background: linear-gradient(135deg, #F7DF1E, #323330, #F0DB4F); }
        .cs-card-cover.react { background: linear-gradient(135deg, #61DAFB, #20232A, #00D8FF); }
        .cs-card-cover.python { background: linear-gradient(135deg, #3776AB, #FFD43B, #4B8BBE); }
        .cs-card-cover.php { background: linear-gradient(135deg, #777BB4, #4F5B93, #8892BF); }
        .cs-card-cover.other { background: linear-gradient(135deg, #8B5CF6, #EC4899, #A855F7); }
        .cs-card-body {
            padding: 16px 18px 18px;
        }
        .cs-card-level {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 8px;
            border-radius: 5px;
            margin-bottom: 10px;
            background: rgba(0, 245, 212, 0.08);
            color: var(--accent);
            border: 1px solid rgba(0, 245, 212, 0.12);
        }
        .cs-card-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 6px;
            line-height: 1.3;
        }
        .cs-card-meta {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .cs-card-meta i { font-size: 11px; }
        .cs-link-wrap {
            text-align: center;
            padding: 60px 24px 40px;
        }
        @@media (max-width: 900px) {
            .cs-scatter-card { width: 200px; }
        }
        @@media (max-width: 500px) {
            .cs-scatter-card { width: 160px; }
        }

        /* AI Logo */
        .ai-logo-core {
            position: relative;
            z-index: 3;
            width: 180px;
            height: 180px;
        }

        .ai-logo-core svg {
            width: 100%;
            height: 100%;
            filter: drop-shadow(0 0 30px var(--accent-glow-strong));
        }

        .ai-orbit {
            position: absolute;
            border-radius: 50%;
            border: 1px solid var(--border);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .ai-orbit-1 {
            width: 300px;
            height: 300px;
            animation: aiOrbitSpin 12s linear infinite;
        }

        .ai-orbit-2 {
            width: 450px;
            height: 450px;
            animation: aiOrbitSpin 18s linear infinite reverse;
            border-style: dashed;
            opacity: 0.5;
        }

        @@keyframes aiOrbitSpin {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .ai-orbit-dot {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 12px var(--accent-glow-strong);
        }

        .ai-orbit-1 .ai-orbit-dot {
            top: -5px;
            left: 50%;
            margin-left: -5px;
        }

        .ai-orbit-2 .ai-orbit-dot {
            bottom: -5px;
            left: 50%;
            margin-left: -5px;
            background: var(--accent-2);
            box-shadow: 0 0 12px var(--accent-2);
        }

        .ai-glow-backdrop {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--accent-glow-strong) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: aiGlowPulse 4s ease-in-out infinite;
            z-index: 0;
        }

        @@keyframes aiGlowPulse {

            0%,
            100% {
                opacity: 0.15;
                transform: translate(-50%, -50%) scale(1);
            }

            50% {
                opacity: 0.25;
                transform: translate(-50%, -50%) scale(1.1);
            }
        }

        /* AI Chat Header */
        .ai-chat-header {
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .ai-chat-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ai-chat-avatar i {
            color: white;
        }

        .ai-chat-info h4 {
            color: var(--text);
            font-size: 14px;
            font-weight: 700;
        }

        .ai-chat-info p {
            color: var(--accent);
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .ai-chat-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22c55e;
            animation: pulse 2s infinite;
        }

        .ai-chat-messages {
            min-height: auto;
            padding: 0;
        }

        .ai-hud-msg code {
            background: rgba(0, 0, 0, 0.3);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: var(--font-mono);
            font-size: 11px;
        }

        /* ══════════════════════════════════════════
       COMMUNITY SECTION (Layered)
       ══════════════════════════════════════════ */
        .community-section {
            padding: 160px 0;
            position: relative;
            background: var(--bg-2);
            overflow: hidden;
        }

        .community-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
        }

        .community-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .community-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-top: 40px;
        }

        .community-stat-card {
            padding: 24px;
            border-radius: var(--radius-md);
            background: var(--card);
            border: 1px solid var(--border);
            transition: all 0.4s var(--ease-out-expo);
        }

        .community-stat-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-4px);
        }

        .community-stat-value {
            font-size: 32px;
            font-weight: 900;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 6px;
        }

        .community-stat-label {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .community-visual {
            position: relative;
            height: 500px;
        }

        .community-card-stack {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .community-stack-card {
            position: absolute;
            width: 320px;
            padding: 24px;
            border-radius: var(--radius-lg);
            background: var(--card);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
            transition: all 0.5s var(--ease-out-expo);
        }

        .community-stack-card:nth-child(1) {
            top: 0;
            left: 10%;
            transform: rotate(-3deg);
            z-index: 3;
        }

        .community-stack-card:nth-child(2) {
            top: 80px;
            right: 5%;
            transform: rotate(2deg);
            z-index: 2;
        }

        .community-stack-card:nth-child(3) {
            bottom: 40px;
            left: 20%;
            transform: rotate(1deg);
            z-index: 1;
        }

        .community-stack-card:hover {
            transform: rotate(0deg) scale(1.05) !important;
            z-index: 10 !important;
            border-color: var(--accent);
            box-shadow: var(--shadow-lg), var(--shadow-glow);
        }

        .community-stack-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .community-stack-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: white;
            font-weight: 700;
        }

        .community-stack-name {
            font-size: 14px;
            font-weight: 700;
        }

        .community-stack-role {
            font-size: 11px;
            color: var(--text-muted);
        }

        .community-stack-content {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .community-stack-tags {
            display: flex;
            gap: 6px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .community-stack-tag {
            padding: 3px 10px;
            border-radius: 100px;
            font-size: 10px;
            font-weight: 600;
            background: var(--accent-glow);
            color: var(--accent);
            border: 1px solid rgba(0, 245, 212, 0.15);
        }

        /* ══════════════════════════════════════════
       FINAL CTA SECTION
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
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
        }

        .cta-bg-gradient {
            position: absolute;
            inset: -50%;
            background:
                radial-gradient(ellipse 60% 40% at 50% 50%, var(--accent-glow-strong) 0%, transparent 50%),
                radial-gradient(ellipse 40% 60% at 30% 70%, rgba(139, 92, 246, 0.08) 0%, transparent 50%);
            animation: ctaPulse 10s ease-in-out infinite;
        }

        @@keyframes ctaPulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
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

        .reveal-delay-1 {
            transition-delay: 0.1s;
        }

        .reveal-delay-2 {
            transition-delay: 0.2s;
        }

        .reveal-delay-3 {
            transition-delay: 0.3s;
        }

        .reveal-delay-4 {
            transition-delay: 0.4s;
        }

        .reveal-delay-5 {
            transition-delay: 0.5s;
        }

        .reveal-delay-6 {
            transition-delay: 0.6s;
        }

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
       SCROLL PROGRESS BAR
       ══════════════════════════════════════════ */
        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
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
            bottom: 40px;
            left: 50%;
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

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(8px);
            }
        }

        /* ══════════════════════════════════════════
       RESPONSIVE — TABLET (768px–1024px)
       ══════════════════════════════════════════ */
        @@media (max-width: 1024px) {
            .hero {
                grid-template-columns: 1fr;
                gap: 40px;
                text-align: center;
                padding: 100px 0 60px;
                min-height: auto;
            }
            .hero-content { padding: 0 24px; }
            .hero-description { margin: 0 auto 32px; max-width: 100%; }
            .hero-actions { justify-content: center; }
            .hero-stats { justify-content: center; gap: 32px; }
            .hero-visual { height: 350px; margin-top: 20px; }
            .hero-code-window {
                transform: perspective(1200px) rotateY(0deg) rotateX(0deg);
                max-width: 500px;
                margin: 0 auto;
            }
            .hero-float-card-1 { right: 5%; }
            .hero-float-card-2 { left: 5%; }
            .hero-float-card-3 { right: 0; }
            .hero-orb-1 { width: 400px; height: 400px; }
            .hero-orb-2 { width: 350px; height: 350px; }
            .hero-orb-3 { width: 200px; height: 200px; }

            .journey-panel-content { grid-template-columns: 1fr; gap: 40px; padding: 40px; }
            .journey-number { font-size: 120px; left: 3%; }

            .leader-panel-inner { grid-template-columns: 1fr; gap: 40px; padding: 0 32px; text-align: center; }
            .leader-panel-avatar { width: 200px; height: 200px; }
            .leader-panel-ring { width: 240px; height: 240px; }
            .leader-panel-ring-2 { width: 280px; height: 280px; }
            .leader-panel-visual { order: -1; }
            .leader-panel-desc { max-width: 100%; }
            .leader-panel-index { justify-content: center; }
            .leader-panel-index::before { display: none; }

            .holo-card { padding: 32px; }
            .feature-title { font-size: 32px; }
            .feature-desc { font-size: 16px; }
            .feature-chart { height: 120px; }
            .feature-stats { gap: 10px; }
            .feature-stat { padding: 14px; }
            .feature-stat-num { font-size: 24px; }

            .community-layout { grid-template-columns: 1fr; gap: 40px; }
            .community-visual { height: auto; min-height: 400px; }

            .cs-stack-area { height: auto; min-height: 400px; }
            .cs-scatter-card { width: 220px; }

            .marquee-item { font-size: 15px; padding: 0 32px; }

            .nav-dots { display: none; }
        }

        /* ══════════════════════════════════════════
       RESPONSIVE — MOBILE (max 768px)
       ══════════════════════════════════════════ */
        @@media (max-width: 768px) {
            .container { padding: 0 16px; }

            /* Hero */
            .hero { padding: 80px 0 40px; gap: 30px; }
            .hero-eyebrow { font-size: 10px; padding: 6px 14px; margin-bottom: 20px; }
            .hero-title { font-size: clamp(36px, 10vw, 56px); letter-spacing: -2px; margin-bottom: 16px; }
            .hero-description { font-size: 15px; margin-bottom: 28px; }
            .hero-actions { flex-direction: column; align-items: center; gap: 12px; margin-bottom: 40px; }
            .btn { width: 100%; max-width: 300px; justify-content: center; padding: 14px 24px; font-size: 14px; }
            .hero-stats { flex-direction: column; gap: 20px; align-items: center; }
            .hero-stat::after { display: none; }
            .hero-stat-value { font-size: 28px; }
            .hero-stat-label { font-size: 11px; }
            .hero-visual { height: 260px; }
            .hero-code-window { max-width: 100%; transform: none; }
            .code-window-body { padding: 14px; font-size: 11px; min-height: 180px; line-height: 1.8; }
            .code-window-tabs { display: none; }
            .hero-float { display: block; }
            .hero-float-card { padding: 10px 14px; font-size: 11px; gap: 8px; }
            .hero-float-icon { width: 28px; height: 28px; font-size: 13px; border-radius: 8px; }
            .hero-float-text strong { font-size: 11px; }
            .hero-float-text span { font-size: 10px; }
            .hero-float-card-1 { top: 5%; right: 0; animation-delay: 1.5s; }
            .hero-float-card-2 { bottom: 20%; left: 0; animation-delay: 1.8s; }
            .hero-float-card-3 { top: 55%; right: -10px; animation-delay: 2.1s; }

            /* Marquee */
            .marquee-section { padding: 40px 0; }
            .marquee-label { font-size: 10px; margin-bottom: 16px; }
            .marquee-item { font-size: 13px; padding: 0 24px; gap: 8px; }
            .marquee-item i { font-size: 18px; }
            .marquee-fade-left, .marquee-fade-right { width: 40px; }

            /* Journey — KEEP sticky scroll effects */
            .journey-panel { padding: 60px 20px; }
            .journey-panel-content { gap: 24px; padding: 20px 0; }
            .journey-number { font-size: 80px; left: 2%; }
            .journey-title { font-size: 28px; letter-spacing: -1px; }
            .journey-desc { font-size: 15px; margin-bottom: 24px; }
            .terminal-body { padding: 16px; font-size: 11px; }
            .journey-bug-card { padding: 14px; gap: 12px; }
            .journey-bug-icon { width: 36px; height: 36px; font-size: 15px; }

            /* Leaders — KEEP sticky scroll effects */
            .leaders-intro { padding: 80px 0 40px; }
            .leader-panel { padding: 40px 20px; }
            .leader-panel-inner { padding: 0; gap: 24px; }
            .leader-panel-avatar { width: 140px; height: 140px; border-width: 3px; }
            .leader-panel-ring { width: 180px; height: 180px; }
            .leader-panel-ring-2 { width: 210px; height: 210px; }
            .leader-panel-icon { width: 44px; height: 44px; font-size: 18px; right: 20px; }
            .leader-panel-name { font-size: 28px; letter-spacing: -1px; }
            .leader-panel-company { font-size: 16px; }
            .leader-panel-desc { font-size: 14px; }
            .leader-panel-quote { padding: 16px; font-size: 13px; margin-top: 20px; }

            /* Feature Steps — KEEP sticky scroll effects */
            .fp-step { padding: 60px 20px; }
            .fp-step-num { font-size: 60px; letter-spacing: -3px; margin-bottom: 16px; }
            .fp-step-title { font-size: 24px; margin-bottom: 12px; }
            .fp-step-desc { font-size: 14px; }
            .fp-visual { width: 260px; height: 200px; transform: scale(0.8); margin-bottom: 20px; }
            .fp-course-mini { width: 60px; height: 42px; font-size: 18px; }
            .fp-rm-node { width: 36px; height: 36px; font-size: 14px; }
            .fp-contest-bar { padding: 6px 10px; }
            .fp-contest-name { font-size: 12px; }
            .fp-contest-score { font-size: 12px; }
            .fp-job-card { width: 110px; padding: 10px; }
            .fp-job-title { font-size: 10px; }
            .fp-ai-core { width: 80px; height: 80px; font-size: 28px; }
            .fp-ai-orbit:nth-child(2) { width: 140px; height: 140px; }
            .fp-ai-orbit:nth-child(3) { width: 200px; height: 200px; }
            .fp-ai-code { font-size: 9px; padding: 4px 8px; }
            .fp-comm-avatar { width: 32px; height: 32px; font-size: 13px; }
            .fp-comm-msg { font-size: 10px; max-width: 90px; padding: 4px 8px; }

            /* Holo Card */
            .holo-card { padding: 20px; border-radius: 20px; }
            .feature-icon-wrap { width: 56px; height: 56px; border-radius: 16px; font-size: 26px; }
            .feature-title { font-size: 24px; }
            .feature-desc { font-size: 14px; margin-bottom: 20px; }
            .feature-code { padding: 14px; font-size: 10px; }
            .feature-chart { height: 100px; gap: 5px; }
            .feature-stats { flex-direction: column; gap: 8px; }
            .feature-stat { padding: 12px; }
            .feature-stat-num { font-size: 20px; }
            .feature-stat-label { font-size: 10px; }
            .feature-chat-msg { padding: 12px 14px; font-size: 12px; }

            /* Courses */
            .cs-header { padding: 60px 16px 24px; }
            .cs-header .section-title { font-size: 28px; }
            .cs-sub { font-size: 13px; padding: 0 16px 16px; }
            .cs-scatter-card { width: 160px; }
            .cs-card-cover { height: 90px; }
            .cs-card-cover i { font-size: 32px; }
            .cs-card-body { padding: 12px 14px 14px; }
            .cs-card-title { font-size: 12px; }
            .cs-card-meta { font-size: 10px; gap: 6px; }
            .cs-link-wrap { padding: 40px 16px 24px; }

            /* Community */
            .community-section { padding: 80px 0; }
            .community-stack-card { padding: 18px; }
            .community-stack-avatar { width: 36px; height: 36px; font-size: 14px; }
            .community-stack-name { font-size: 13px; }
            .community-stack-content { font-size: 12px; }
            .community-stat-value { font-size: 24px; }
            .community-stat-label { font-size: 10px; }

            /* CTA */
            .cta-section { padding: 80px 0; }
            .cta-title { font-size: clamp(28px, 8vw, 48px); letter-spacing: -2px; }
            .cta-desc { font-size: 15px; margin-bottom: 32px; }
            .cta-actions { flex-direction: column; align-items: center; gap: 12px; }

            /* Section Headers */
            .section-title { font-size: clamp(24px, 6vw, 40px); letter-spacing: -1px; }
            .section-desc { font-size: 14px; }

            /* Scroll Hint */
            .scroll-hint { bottom: 20px; }
            .scroll-hint-text { font-size: 10px; }

            /* AI Section */
            .ai-logo-core { width: 120px; height: 120px; }
            .ai-orbit-1 { width: 200px; height: 200px; }
            .ai-orbit-2 { width: 300px; height: 300px; }
            .ai-glow-backdrop { width: 250px; height: 250px; }
        }

        /* ══════════════════════════════════════════
       RESPONSIVE — SMALL MOBILE (max 480px)
       ══════════════════════════════════════════ */
        @@media (max-width: 480px) {
            .hero { padding: 60px 0 30px; gap: 20px; }
            .hero-eyebrow { font-size: 9px; padding: 5px 12px; margin-bottom: 16px; gap: 6px; }
            .hero-title { font-size: 32px; letter-spacing: -1.5px; margin-bottom: 12px; }
            .hero-description { font-size: 14px; line-height: 1.6; margin-bottom: 24px; }
            .hero-actions { gap: 10px; margin-bottom: 32px; }
            .btn { padding: 12px 20px; font-size: 13px; max-width: 260px; }
            .hero-stats { gap: 16px; }
            .hero-stat-value { font-size: 24px; }
            .hero-visual { height: 220px; }
            .code-window-body { padding: 12px; font-size: 10px; line-height: 1.8; min-height: 160px; }
            .code-line-num { display: none; }
            .hero-float-card { padding: 8px 12px; font-size: 10px; gap: 6px; }
            .hero-float-icon { width: 24px; height: 24px; font-size: 11px; }
            .hero-float-text strong { font-size: 10px; }
            .hero-float-text span { font-size: 9px; }

            .marquee-item { font-size: 12px; padding: 0 16px; gap: 6px; }
            .marquee-item i { font-size: 15px; }

            .journey-panel { padding: 40px 16px; }
            .journey-number { font-size: 60px; }
            .journey-title { font-size: 22px; }
            .journey-desc { font-size: 13px; }

            .leader-panel { padding: 30px 16px; }
            .leader-panel-avatar { width: 110px; height: 110px; }
            .leader-panel-ring { width: 140px; height: 140px; }
            .leader-panel-ring-2 { width: 170px; height: 170px; }
            .leader-panel-name { font-size: 24px; }
            .leader-panel-company { font-size: 14px; }
            .leader-panel-desc { font-size: 13px; }

            .fp-step { padding: 40px 16px; }
            .fp-step-num { font-size: 48px; }
            .fp-step-title { font-size: 20px; }
            .fp-step-desc { font-size: 13px; }
            .fp-visual { width: 200px; height: 160px; transform: scale(0.75); }

            .holo-card { padding: 16px; border-radius: 16px; }
            .feature-icon-wrap { width: 48px; height: 48px; border-radius: 12px; font-size: 22px; }
            .feature-title { font-size: 20px; }
            .feature-desc { font-size: 13px; }

            .cs-scatter-card { width: 140px; }
            .cs-card-cover { height: 75px; }
            .cs-card-cover i { font-size: 26px; }

            .community-stack-card { padding: 14px; }

            .cta-title { font-size: 24px; letter-spacing: -1px; }
            .cta-desc { font-size: 13px; }

            .section-title { font-size: 22px; }
            .section-desc { font-size: 13px; }
        }

        /* ══════════════════════════════════════════
       RESPONSIVE — LANDSCAPE MOBILE
       ══════════════════════════════════════════ */
        @@media (max-height: 500px) and (orientation: landscape) {
            .hero { min-height: auto; padding: 40px 0; }
            .hero-visual { height: 200px; }
        }

        /* ══════════════════════════════════════════
       RESPONSIVE — LARGE DESKTOP (1400px+)
       ══════════════════════════════════════════ */
        @@media (min-width: 1400px) {
            .hero { gap: 100px; padding: 140px 0; }
            .hero-content { padding-left: 80px; }
            .holo-card { padding: 56px; }
            .feature-title { font-size: 48px; }
        }

        /* ══════════════════════════════════════════
       TOUCH DEVICE — disable hover transforms only
       ══════════════════════════════════════════ */
        @@media (hover: none) and (pointer: coarse) {
            .btn:hover { transform: none; }
            .btn-primary:hover { transform: none; }
            .community-stack-card:hover { transform: none !important; }
            .journey-bug-card:hover { transform: none; }
            .holo-card:hover { transform: none; }
            .nav-dot:hover { transform: scale(1); }
        }

        /* ══════════════════════════════════════════
       PRINT
       ══════════════════════════════════════════ */
        @@media print {
            .nav-dots, .scroll-progress, .scroll-hint, #particles-canvas,
            .hero-bg, .hero-float, .hero-orbs, .marquee-section,
            .journey-section, .leader-panel-bg, .cinematic-scene,
            .cta-bg-gradient { display: none !important; }
            .hero { min-height: auto; padding: 20px 0; }
            .hero-visual { display: none; }
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
    <!-- Navigation Dots -->
    <div class="nav-dots" id="navDots">
        <div class="nav-dot active" data-target="hero">
            <span class="nav-dot-label">{{ t('home_nav_start') }}</span>
        </div>
        <div class="nav-dot" data-target="journey">
            <span class="nav-dot-label">{{ t('home_nav_journey') }}</span>
        </div>
        <div class="nav-dot" data-target="leaders">
            <span class="nav-dot-label">{{ t('home_nav_inspiration') }}</span>
        </div>
        <div class="nav-dot" data-target="features">
            <span class="nav-dot-label">{{ t('home_nav_features') }}</span>
        </div>
        <div class="nav-dot" data-target="courses">
            <span class="nav-dot-label">{{ t('home_nav_courses') }}</span>
        </div>
        <div class="nav-dot" data-target="ai">
            <span class="nav-dot-label">{{ t('home_nav_ai') }}</span>
        </div>
        <div class="nav-dot" data-target="community">
            <span class="nav-dot-label">{{ t('home_nav_community') }}</span>
        </div>
    </div>
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
            <h1 class="hero-title">
                <span class="hero-title-line">{{ t('home_hero_title_1') }}</span>
                <span class="hero-title-line hero-title-gradient">{{ t('home_hero_title_2') }}</span>
            </h1>
            <p class="hero-description">
                {{ t('home_hero_desc') }}
            </p>
            <div class="hero-actions">
                <a href="{{ route('courses.index') }}" class="btn btn-primary magnetic">
                    <i class="fas fa-rocket"></i>
                    <span>{{ t('home_hero_cta_start') }}</span>
                </a>
                <a href="{{ route('vacancies.index') }}" class="btn btn-secondary magnetic">
                    <i class="fas fa-briefcase"></i>
                    <span>{{ t('home_hero_cta_jobs') }}</span>
                </a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-value" data-count="{{ $totalUsers }}">0</div>
                    <div class="hero-stat-label">{{ t('home_hero_stat_students') }}</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value" data-count="{{ $totalCourses }}">0</div>
                    <div class="hero-stat-label">{{ t('home_hero_stat_courses') }}</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value" data-count="{{ $totalVacancies }}">0</div>
                    <div class="hero-stat-label">{{ t('home_hero_stat_vacancies') }}</div>
                </div>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-code-window" id="heroCodeWindow">
                <div class="code-window-bar">
                    <div class="terminal-dots">
                        <div class="terminal-dot" style="background:#ff5f57"></div>
                        <div class="terminal-dot" style="background:#febc2e"></div>
                        <div class="terminal-dot" style="background:#28c840"></div>
                    </div>
                    <div class="code-window-tabs">
                        <div class="code-window-tab active">career.py</div>
                        <div class="code-window-tab">skills.js</div>
                    </div>
                </div>
                <div class="code-window-body">
                    <div class="code-line">
                        <span class="code-line-num">1</span>
                        <span><span class="code-keyword">class</span> <span class="code-class">Developer</span>:</span>
                    </div>
                    <div class="code-line">
                        <span class="code-line-num">2</span>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-keyword">def</span> <span
                                class="code-function">__init__</span>(<span class="code-variable">self</span>):</span>
                    </div>
                    <div class="code-line">
                        <span class="code-line-num">3</span>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-variable">self</span>.skills
                            = []</span>
                    </div>
                    <div class="code-line">
                        <span class="code-line-num">4</span>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span
                                class="code-variable">self</span>.passion = <span class="code-keyword">True</span></span>
                    </div>
                    <div class="code-line">
                        <span class="code-line-num">5</span>
                        <span></span>
                    </div>
                    <div class="code-line">
                        <span class="code-line-num">6</span>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-keyword">def</span> <span
                                class="code-function">learn</span>(<span class="code-variable">self</span>, <span
                                class="code-variable">topic</span>):</span>
                    </div>
                    <div class="code-line">
                        <span class="code-line-num">7</span>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span
                                class="code-variable">self</span>.skills.<span class="code-function">append</span>(<span
                                class="code-variable">topic</span>)</span>
                    </div>
                    <div class="code-line">
                        <span class="code-line-num">8</span>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-keyword">return</span> <span
                                class="code-string">"Level Up!"</span><span class="code-cursor"></span></span>
                    </div>
                </div>
            </div>
            <!-- Floating cards -->
            <div class="hero-float hero-float-card hero-float-card-1">
                <div class="hero-float-icon green"><i class="fas fa-check"></i></div>
                <div class="hero-float-text">
                    <strong>{{ t('home_float_course_done') }}</strong>
                    <span>{{ t('home_float_course_name') }}</span>
                </div>
            </div>
            <div class="hero-float hero-float-card hero-float-card-2">
                <div class="hero-float-icon purple"><i class="fas fa-trophy"></i></div>
                <div class="hero-float-text">
                    <strong>{{ t('home_float_achievement') }}</strong>
                    <span>+500 XP</span>
                </div>
            </div>
            <div class="hero-float hero-float-card hero-float-card-3">
                <div class="hero-float-icon amber"><i class="fas fa-briefcase"></i></div>
                <div class="hero-float-text">
                    <strong>{{ t('home_float_invite') }}</strong>
                    <span>{{ t('home_float_job_title') }}</span>
                </div>
            </div>
        </div>
    </section>
    <div class="scroll-hint">
        <span class="scroll-hint-text">{{ t('home_scroll_hint') }}</span>
        <div class="scroll-hint-arrow"><i class="fas fa-chevron-down"></i></div>
    </div>
    <!-- ══════════════════════════════════════════
       MARQUEE SECTION
       ══════════════════════════════════════════ -->
    <section class="marquee-section">
        <div class="marquee-label">{{ t('home_marquee_label') }}</div>
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
       JOURNEY SECTION (Horizontal Scroll)
       ══════════════════════════════════════════ -->
    <section class="journey-section" id="journey">
        <div class="journey-sticky">
            <div class="journey-track" id="journeyTrack">
                <!-- Panel 1: First Code -->
                <div class="journey-panel">
                    <span class="journey-number">01</span>
                    <div class="journey-panel-content">
                        <div>
                            <div class="journey-step-tag green"><i class="fas fa-play"></i> {{ t('home_journey_step') }} 1</div>
                            <h2 class="journey-title">{{ t('home_journey_1_title') }}</h2>
                            <p class="journey-desc">{{ t('home_journey_1_desc') }}</p>
                            <a href="{{ route('courses.index') }}" class="btn btn-primary magnetic">
                                <i class="fas fa-terminal"></i>
                                <span>{{ t('home_journey_1_cta') }}</span>
                            </a>
                        </div>
                        <div>
                            <div class="journey-terminal">
                                <div class="terminal-header">
                                    <div class="terminal-dots">
                                        <div class="terminal-dot" style="background:#ff5f57"></div>
                                        <div class="terminal-dot" style="background:#febc2e"></div>
                                        <div class="terminal-dot" style="background:#28c840"></div>
                                    </div>
                                    <span
                                        style="margin-left:12px;font-size:11px;color:var(--text-muted);font-family:var(--font-mono)">terminal</span>
                                </div>
                                <div class="terminal-body">
                                    <div class="terminal-line">
                                        <span class="terminal-prompt">$</span>
                                        <span class="terminal-command">python hello.py</span>
                                    </div>
                                    <div class="terminal-line">
                                        <span class="terminal-success">Hello, World! 🌍</span>
                                    </div>
                                    <div class="terminal-line" style="margin-top:8px">
                                        <span class="terminal-prompt">$</span>
                                        <span class="terminal-command">python future.py</span>
                                    </div>
                                    <div class="terminal-line">
                                        <span class="terminal-success">{{ t('home_terminal_journey_started') }}</span>
                                    </div>
                                    <div class="terminal-line" style="margin-top:8px">
                                        <span class="terminal-prompt">$</span>
                                        <span class="terminal-command" style="color:var(--text-muted)">_</span>
                                        <span class="code-cursor" style="background:#22c55e"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Panel 2: Bugs -->
                <div class="journey-panel">
                    <span class="journey-number">02</span>
                    <div class="journey-panel-content">
                        <div>
                            <div class="journey-bugs">
                                <div class="journey-bug-card">
                                    <div class="journey-bug-icon error"><i class="fas fa-times-circle"></i></div>
                                    <div class="journey-bug-text">
                                        <h4>SyntaxError: unexpected EOF</h4>
                                        <p>{{ t('home_journey_bug_syntax') }}</p>
                                    </div>
                                </div>
                                <div class="journey-bug-card">
                                    <div class="journey-bug-icon warning"><i class="fas fa-exclamation-triangle"></i></div>
                                    <div class="journey-bug-text">
                                        <h4>{{ t('home_journey_bug_logic_title') }}</h4>
                                        <p>{{ t('home_journey_bug_debug') }}</p>
                                    </div>
                                </div>
                                <div class="journey-bug-card">
                                    <div class="journey-bug-icon info"><i class="fas fa-lightbulb"></i></div>
                                    <div class="journey-bug-text">
                                        <h4>{{ t('home_journey_bug_hint') }}</h4>
                                        <p>{{ t('home_journey_bug_norm') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="journey-step-tag red"><i class="fas fa-bug"></i> {{ t('home_journey_step') }} 2</div>
                            <h2 class="journey-title">{{ t('home_journey_2_title') }}</h2>
                            <p class="journey-desc">{{ t('home_journey_2_desc') }}</p>
                            <p style="color:var(--accent);font-weight:700;font-size:15px">{{ t('home_journey_2_quote') }}</p>
                        </div>
                    </div>
                </div>
                <!-- Panel 3: Success -->
                <div class="journey-panel">
                    <span class="journey-number">03</span>
                    <div class="journey-panel-content">
                        <div>
                            <div class="journey-step-tag amber"><i class="fas fa-trophy"></i> {{ t('home_journey_step') }} 3</div>
                            <h2 class="journey-title">{{ t('home_journey_3_title') }}</h2>
                            <p class="journey-desc">{{ t('home_journey_3_desc') }}</p>
                            <a href="{{ route('register') }}" class="btn btn-primary magnetic">
                                <i class="fas fa-rocket"></i>
                                <span>{{ t('home_journey_3_cta') }}</span>
                            </a>
                        </div>
                        <div>
                            <div class="journey-success-terminal">
                                <div class="terminal-header">
                                    <div class="terminal-dots">
                                        <div class="terminal-dot" style="background:#ff5f57"></div>
                                        <div class="terminal-dot" style="background:#febc2e"></div>
                                        <div class="terminal-dot" style="background:#28c840"></div>
                                    </div>
                                    <span
                                        style="margin-left:12px;font-size:11px;color:#22c55e;font-family:var(--font-mono)">✓
                                        {{ t('home_terminal_deployed') }}</span>
                                </div>
                                <div class="terminal-body">
                                    <div class="terminal-line">
                                        <span class="terminal-prompt">$</span>
                                        <span class="terminal-command">git push origin main</span>
                                    </div>
                                    <div class="terminal-line">
                                        <span class="terminal-success">✓ {{ t('home_terminal_deploy_success') }}</span>
                                    </div>
                                    <div class="terminal-line" style="margin-top:8px">
                                        <span class="terminal-prompt">$</span>
                                        <span class="terminal-command">cat achievements.txt</span>
                                    </div>
                                    <div class="terminal-line"><span class="terminal-output">✓ {{ t('home_journey_3_prog1') }}</span></div>
                                    <div class="terminal-line"><span class="terminal-output">✓ {{ t('home_journey_3_prog2') }}</span>
                                    </div>
                                    <div class="terminal-line"><span class="terminal-output">✓ {{ t('home_journey_3_prog3') }}
                                            </span></div>
                                    <div class="terminal-line"><span class="terminal-output">✓ {{ t('home_journey_3_prog4') }}
                                            </span></div>
                                    <div class="terminal-line" style="margin-top:12px">
                                        <span style="font-weight:700;color:var(--text)">★ {{ t('home_journey_3_welcome') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Panel 4: Infinity -->
                <div class="journey-panel" style="text-align:center">
                    <div style="max-width:700px;margin:0 auto">
                        <div class="journey-step-tag blue" style="margin:0 auto 24px;display:inline-flex"><i
                                class="fas fa-infinity"></i> {{ t('home_journey_inf_tag') }}</div>
                        <h2 class="journey-title" style="font-size:clamp(36px,5vw,64px);margin-bottom:24px">{{ t('home_journey_inf_title') }}</h2>
                        <p class="journey-desc" style="max-width:540px;margin:0 auto 40px">{{ t('home_journey_inf_desc') }}</p>
                        <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap">
                            <a href="{{ route('courses.index') }}" class="btn btn-primary magnetic"><i
                                    class="fas fa-book"></i><span>{{ t('home_journey_inf_cta1') }}</span></a>
                            <a href="{{ route('register') }}" class="btn btn-secondary magnetic"><i
                                    class="fas fa-user-plus"></i><span>{{ t('home_journey_inf_cta2') }}</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ══════════════════════════════════════════
       LEADERS SECTION (Full-Screen Scroll)
       ══════════════════════════════════════════ -->
    <section class="leaders-intro noise" id="leaders">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-tag"><i class="fas fa-star"></i> {{ t('home_leaders_tag') }}</div>
                <h2 class="section-title">{{ t('home_leaders_title') }}</h2>
                <p class="section-desc">{{ t('home_leaders_desc') }}</p>
            </div>
        </div>
    </section>
    @php
        $leaders = [
            [
                'name' => 'Bill Gates',
                'company' => 'Microsoft',
                'img' => 'https://upload.wikimedia.org/wikipedia/commons/c/cc/Bill_Gates%2C_September_2024.jpg',
                'icon' => 'fab fa-microsoft',
                'desc' => t('home_leader_bill_desc'),
                'quote' => t('home_leader_bill_quote'),
            ],
            [
                'name' => 'Elon Musk',
                'company' => 'Tesla & SpaceX',
                'img' => 'https://upload.wikimedia.org/wikipedia/commons/0/06/Elon_Musk%2C_2018_%28cropped%29.jpg',
                'icon' => 'fas fa-bolt',
                'desc' => t('home_leader_elon_desc'),
                'quote' => t('home_leader_elon_quote'),
            ],
            [
                'name' => 'Linus Torvalds',
                'company' => 'Linux & Git',
                'img' => 'https://upload.wikimedia.org/wikipedia/commons/6/69/Linus_Torvalds.jpeg',
                'icon' => 'fab fa-linux',
                'desc' => t('home_leader_linus_desc'),
                'quote' => t('home_leader_linus_quote'),
            ],
            [
                'name' => 'Mark Zuckerberg',
                'company' => 'Meta',
                'img' => 'https://upload.wikimedia.org/wikipedia/commons/c/cf/Mark_Zuckerberg_%282020%29_%28cropped%29.jpg',
                'icon' => 'fab fa-meta',
                'desc' => t('home_leader_mark_desc'),
                'quote' => t('home_leader_mark_quote'),
            ],
            [
                'name' => 'Larry Page',
                'company' => 'Google',
                'img' => 'https://upload.wikimedia.org/wikipedia/commons/7/77/Larry_Page_in_the_European_Parliament%2C_17.06.2009_%28cropped1%29.jpg',
                'icon' => 'fab fa-google',
                'desc' => t('home_leader_larry_desc'),
                'quote' => t('home_leader_larry_quote'),
            ],
            [
                'name' => 'Sam Altman',
                'company' => 'OpenAI',
                'img' => 'https://upload.wikimedia.org/wikipedia/commons/1/12/Sam_altman.jpg',
                'icon' => 'fas fa-brain',
                'desc' => t('home_leader_sam_desc'),
                'quote' => t('home_leader_sam_quote'),
            ],
        ];
    @endphp
    <div class="leaders-panels" id="leadersPanels">
        @foreach($leaders as $i => $l)
            <div class="leader-panel" data-leader-index="{{ $i }}">
                <div class="leader-panel-bg"></div>
                <div class="leader-panel-inner">
                    <div class="leader-panel-visual">
                        <div class="leader-panel-ring"></div>
                        <div class="leader-panel-ring leader-panel-ring-2"></div>
                        <div class="leader-panel-avatar">
                            <img src="{{ $l['img'] }}" alt="{{ $l['name'] }}" loading="lazy">
                        </div>
                        <div class="leader-panel-icon"><i class="{{ $l['icon'] }}"></i></div>
                    </div>
                    <div class="leader-panel-text">
                        <div class="leader-panel-index">0{{ $i + 1 }} / 06</div>
                        <h3 class="leader-panel-name">{{ $l['name'] }}</h3>
                        <div class="leader-panel-company"><i class="{{ $l['icon'] }}"></i> {{ $l['company'] }}</div>
                        <p class="leader-panel-desc">{{ $l['desc'] }}</p>
                        <div class="leader-panel-quote">"{{ $l['quote'] }}"</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!-- ══════════════════════════════════════════
       FEATURES — SCROLL STEPS
       ══════════════════════════════════════════ -->
    <section id="features">
        <div style="text-align:center;padding:100px 24px 60px">
            <div class="section-tag" style="margin-bottom:16px">{{ t('home_features_tag') }}</div>
            <h2 class="section-title" style="font-size:clamp(32px,4vw,56px)">{{ t('home_features_title') }}</h2>
        </div>
        <div class="fp-steps-panels" id="fpSteps">
            <!-- STEP 1: Курсы -->
            <div class="fp-step" style="--step-color:var(--accent)" data-step-index="0">
                <div class="fp-step-inner">
                    <div class="fp-visual">
                        <div class="fp-courses-ring">
                            <div class="fp-course-mini"><i class="fab fa-html5"></i></div>
                            <div class="fp-course-mini"><i class="fab fa-js-square"></i></div>
                            <div class="fp-course-mini"><i class="fab fa-react"></i></div>
                            <div class="fp-course-mini"><i class="fab fa-python"></i></div>
                            <div class="fp-course-mini"><i class="fab fa-php"></i></div>
                            <div class="fp-course-mini"><i class="fas fa-play"></i></div>
                        </div>
                    </div>
                    <div class="fp-step-num">01</div>
                    <div class="fp-step-content">
                        <h3 class="fp-step-title">{{ t('home_feature_courses_title') }}</h3>
                        <p class="fp-step-desc">{{ t('home_feature_courses_desc') }}</p>
                    </div>
                </div>
            </div>
            <!-- STEP 2: Дорожные карты -->
            <div class="fp-step" style="--step-color:#8b5cf6" data-step-index="1">
                <div class="fp-step-inner">
                    <div class="fp-visual">
                        <div class="fp-roadmap">
                            <div class="fp-rm-node active-node"><i class="fas fa-flag"></i></div>
                            <div class="fp-rm-node"><i class="fas fa-code"></i></div>
                            <div class="fp-rm-node"><i class="fas fa-database"></i></div>
                            <div class="fp-rm-node"><i class="fas fa-server"></i></div>
                            <div class="fp-rm-node"><i class="fas fa-cloud"></i></div>
                            <div class="fp-rm-node"><i class="fas fa-trophy"></i></div>
                            <div class="fp-rm-line"></div>
                            <div class="fp-rm-line"></div>
                            <div class="fp-rm-line"></div>
                            <div class="fp-rm-line"></div>
                            <div class="fp-rm-line"></div>
                        </div>
                    </div>
                    <div class="fp-step-num">02</div>
                    <div class="fp-step-content">
                        <h3 class="fp-step-title">{{ t('home_feature_roadmaps_title') }}</h3>
                        <p class="fp-step-desc">{{ t('home_feature_roadmaps_desc') }}</p>
                    </div>
                </div>
            </div>
            <!-- STEP 3: Контесты -->
            <div class="fp-step" style="--step-color:#f59e0b" data-step-index="2">
                <div class="fp-step-inner">
                    <div class="fp-visual">
                        <div class="fp-contest">
                            <div class="fp-contest-bar">
                                <div class="fp-contest-rank gold">1</div>
                                <div class="fp-contest-avatar" style="background:linear-gradient(135deg,#E44D26,#F16529)">АМ</div>
                                <div class="fp-contest-name">Алексей М.</div>
                                <div class="fp-contest-score">2847</div>
                            </div>
                            <div class="fp-contest-bar">
                                <div class="fp-contest-rank silver">2</div>
                                <div class="fp-contest-avatar" style="background:linear-gradient(135deg,#8b5cf6,#6366f1)">ДК</div>
                                <div class="fp-contest-name">Дмитрий К.</div>
                                <div class="fp-contest-score">2691</div>
                            </div>
                            <div class="fp-contest-bar">
                                <div class="fp-contest-rank bronze">3</div>
                                <div class="fp-contest-avatar" style="background:linear-gradient(135deg,#22c55e,#16a34a)">ОП</div>
                                <div class="fp-contest-name">Ольга П.</div>
                                <div class="fp-contest-score">2534</div>
                            </div>
                            <div class="fp-contest-bar">
                                <div class="fp-contest-rank normal">4</div>
                                <div class="fp-contest-avatar" style="background:linear-gradient(135deg,#ec4899,#db2777)">ИН</div>
                                <div class="fp-contest-name">Игорь Н.</div>
                                <div class="fp-contest-score">2410</div>
                            </div>
                            <div class="fp-contest-timer">02:14:33</div>
                        </div>
                    </div>
                    <div class="fp-step-num">03</div>
                    <div class="fp-step-content">
                        <h3 class="fp-step-title">{{ t('home_feature_contests_title') }}</h3>
                        <p class="fp-step-desc">{{ t('home_feature_contests_desc') }}</p>
                    </div>
                </div>
            </div>
            <!-- STEP 4: Вакансии -->
            <div class="fp-step" style="--step-color:#22c55e" data-step-index="3">
                <div class="fp-step-inner">
                    <div class="fp-visual">
                        <div class="fp-jobs">
                            <div class="fp-job-card">
                                <div class="fp-job-logo" style="background:linear-gradient(135deg,#4285F4,#34A853)"><i class="fab fa-google"></i></div>
                                <div class="fp-job-title">{{ t('home_job_frontend') }}</div>
                                <div class="fp-job-company">Google</div>
                                <div class="fp-job-salary">$120K+</div>
                            </div>
                            <div class="fp-job-card">
                                <div class="fp-job-logo" style="background:linear-gradient(135deg,#00A1F4,#0078D4)"><i class="fab fa-microsoft"></i></div>
                                <div class="fp-job-title">{{ t('home_job_backend') }}</div>
                                <div class="fp-job-company">Microsoft</div>
                                <div class="fp-job-salary">$110K+</div>
                            </div>
                            <div class="fp-job-card">
                                <div class="fp-job-logo" style="background:linear-gradient(135deg,#FF9900,#232F3E)"><i class="fab fa-amazon"></i></div>
                                <div class="fp-job-title">{{ t('home_job_fullstack') }}</div>
                                <div class="fp-job-company">Amazon</div>
                                <div class="fp-job-salary">$130K+</div>
                            </div>
                            <div class="fp-job-card">
                                <div class="fp-job-logo" style="background:linear-gradient(135deg,#E44D26,#1572B6)"><i class="fab fa-apple"></i></div>
                                <div class="fp-job-title">{{ t('home_job_ios') }}</div>
                                <div class="fp-job-company">Apple</div>
                                <div class="fp-job-salary">$140K+</div>
                            </div>
                            <div class="fp-job-line"></div>
                            <div class="fp-job-line"></div>
                        </div>
                    </div>
                    <div class="fp-step-num">04</div>
                    <div class="fp-step-content">
                        <h3 class="fp-step-title">{{ t('home_feature_vacancies_title') }}</h3>
                        <p class="fp-step-desc">{{ t('home_feature_vacancies_desc') }}</p>
                    </div>
                </div>
            </div>
            <!-- STEP 5: AI -->
            <div class="fp-step" style="--step-color:var(--accent)" data-step-index="4">
                <div class="fp-step-inner">
                    <div class="fp-visual">
                        <div class="fp-ai">
                            <div class="fp-ai-core"><i class="fas fa-brain"></i></div>
                            <div class="fp-ai-orbit"><div class="fp-ai-dot"></div></div>
                            <div class="fp-ai-orbit"><div class="fp-ai-dot"></div></div>
                            <div class="fp-ai-code">def train():</div>
                            <div class="fp-ai-code">loss = 0.003</div>
                            <div class="fp-ai-code">return result</div>
                            <div class="fp-ai-code">accuracy: 99%</div>
                        </div>
                    </div>
                    <div class="fp-step-num">05</div>
                    <div class="fp-step-content">
                        <h3 class="fp-step-title">{{ t('home_feature_ai_title') }}</h3>
                        <p class="fp-step-desc">{{ t('home_feature_ai_desc') }}</p>
                    </div>
                </div>
            </div>
            <!-- STEP 6: Сообщество -->
            <div class="fp-step" style="--step-color:#ec4899" data-step-index="5">
                <div class="fp-step-inner">
                    <div class="fp-visual">
                        <div class="fp-community">
                            <div class="fp-comm-avatar" style="background:linear-gradient(135deg,#E44D26,#F16529)">А</div>
                            <div class="fp-comm-avatar" style="background:linear-gradient(135deg,#8b5cf6,#6366f1)">Д</div>
                            <div class="fp-comm-avatar" style="background:linear-gradient(135deg,#22c55e,#16a34a)">О</div>
                            <div class="fp-comm-avatar" style="background:linear-gradient(135deg,#ec4899,#db2777)">И</div>
                            <div class="fp-comm-avatar" style="background:linear-gradient(135deg,#f59e0b,#d97706)">С</div>
                            <div class="fp-comm-avatar" style="background:linear-gradient(135deg,#06b6d4,#0891b2)">М</div>
                            <div class="fp-comm-avatar" style="background:linear-gradient(135deg,#f43f5e,#e11d48)">Н</div>
                            <div class="fp-comm-msg">{{ t('home_community_chat1') }}</div>
                            <div class="fp-comm-msg">{{ t('home_community_chat2') }}</div>
                            <div class="fp-comm-msg">{{ t('home_community_chat3') }}</div>
                            <div class="fp-comm-line"></div>
                            <div class="fp-comm-line"></div>
                            <div class="fp-comm-line"></div>
                        </div>
                    </div>
                    <div class="fp-step-num">06</div>
                    <div class="fp-step-content">
                        <h3 class="fp-step-title">{{ t('home_feature_community_title') }}</h3>
                        <p class="fp-step-desc">{{ t('home_feature_community_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ══════════════════════════════════════════
       COURSES — SCROLL-DRIVEN CHAOTIC STACK
       ══════════════════════════════════════════ -->
    @if(isset($courses) && $courses->count())
        <section class="cs-section" id="courses" style="height:300vh">
            <div class="cs-sticky">
                <div>
                    <div class="cs-header">
                        <div class="section-tag" style="margin-bottom:16px"><i class="fas fa-book-open"></i> {{ t('home_courses_tag') }}</div>
                        <h2 class="section-title">{{ t('home_courses_title') }}</h2>
                    </div>
                    <p class="cs-sub">{{ t('home_courses_desc') }}</p>
                    <div class="cs-stack-area" id="csStack">
                        @php
                    $positions = [
                        ['left' => '2%',  'top' => '10px',   'rotate' => '-6deg',  'z' => 4, 'side' => 'left',  'delay' => 0, 'startX' => -120, 'startY' => 40,  'startRot' => -35],
                        ['left' => '26%', 'top' => '-10px',  'rotate' => '4deg',   'z' => 3, 'side' => 'right', 'delay' => 1, 'startX' => 130,  'startY' => -30, 'startRot' => 25],
                        ['left' => '50%', 'top' => '20px',   'rotate' => '-3deg',  'z' => 5, 'side' => 'left',  'delay' => 2, 'startX' => -110, 'startY' => 50,  'startRot' => -40],
                        ['left' => '72%', 'top' => '5px',    'rotate' => '2deg',   'z' => 2, 'side' => 'right', 'delay' => 3, 'startX' => 140,  'startY' => -20, 'startRot' => 30],
                        ['left' => '8%',  'top' => '200px',  'rotate' => '5deg',   'z' => 6, 'side' => 'left',  'delay' => 4, 'startX' => -130, 'startY' => 60,  'startRot' => -25],
                        ['left' => '34%', 'top' => '185px',  'rotate' => '-4deg',  'z' => 1, 'side' => 'right', 'delay' => 5, 'startX' => 120,  'startY' => -40, 'startRot' => 35],
                        ['left' => '58%', 'top' => '210px',  'rotate' => '3deg',   'z' => 3, 'side' => 'left',  'delay' => 6, 'startX' => -115, 'startY' => 45,  'startRot' => -20],
                        ['left' => '78%', 'top' => '170px',  'rotate' => '-5deg',  'z' => 4, 'side' => 'right', 'delay' => 7, 'startX' => 125,  'startY' => -35, 'startRot' => 28],
                    ];
                        @endphp
                        @foreach($courses->take(8) as $i => $course)
                            @php
                                $pos = $positions[$i] ?? $positions[0];
                                $title = strtolower($course->title);
                                $cc = 'other';
                                $ci = 'fas fa-code';
                                $cl = $course->level ?? 'Beginner';
                                if (str_contains($title, 'html')) { $cc = 'html'; $ci = 'fab fa-html5'; }
                                elseif (str_contains($title, 'js') || str_contains($title, 'javascript')) { $cc = 'js'; $ci = 'fab fa-js-square'; }
                                elseif (str_contains($title, 'react')) { $cc = 'react'; $ci = 'fab fa-react'; }
                                elseif (str_contains($title, 'python')) { $cc = 'python'; $ci = 'fab fa-python'; }
                                elseif (str_contains($title, 'php')) { $cc = 'php'; $ci = 'fab fa-php'; }
                                $lessons = $course->lessons->count() ?? 0;
                            @endphp
                            <a href="{{ route('courses.show', $course->id) }}"
                               class="cs-scatter-card"
                               data-left="{{ $pos['left'] }}"
                               data-top="{{ $pos['top'] }}"
                               data-rotate="{{ $pos['rotate'] }}"
                               data-z="{{ $pos['z'] }}"
                               data-side="{{ $pos['side'] }}"
                               data-delay="{{ $pos['delay'] }}"
                               data-start-x="{{ $pos['startX'] }}"
                               data-start-y="{{ $pos['startY'] }}"
                               data-start-rot="{{ $pos['startRot'] }}">
                                <div class="cs-card-cover {{ $cc }}">
                                    <i class="{{ $ci }}"></i>
                                </div>
                                <div class="cs-card-body">
                                    <div class="cs-card-level"><i class="fas fa-signal"></i> {{ __('courses_level_' . mb_strtolower($cl)) }}</div>
                                    <div class="cs-card-title">{{ $course->title }}</div>
                                    <div class="cs-card-meta">
                                        <span><i class="fas fa-play-circle"></i> {{ $lessons }}</span>
                                        <span><i class="fas fa-signal"></i> {{ t($cl) }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="cs-link-wrap">
                <a href="{{ route('courses.index') }}" class="btn btn-secondary magnetic">
                    <i class="fas fa-compass"></i>
                    <span>{{ t('home_courses_view_all') }}</span>
                </a>
            </div>
        </section>
    @endif
    <!-- ══════════════════════════════════════════
       COMMUNITY SECTION
       ══════════════════════════════════════════ -->
    <section class="community-section noise" id="community">
        <div class="container">
            <div class="community-layout">
                <div class="reveal-left">
                    <div class="section-tag"><i class="fas fa-users"></i> {{ t('home_community_tag') }}</div>
                    <h2 class="section-title" style="text-align:left;margin-bottom:16px">{{ t('home_community_title') }}</h2>
                    <p class="section-desc" style="margin:0;text-align:left">{{ t('home_community_desc') }}</p>
                </div>
                <div class="community-visual reveal-right">
                    <div class="community-card-stack">
                        @if($reviews->count())
                            @foreach($reviews->take(3) as $review)
                                <div class="community-stack-card">
                                    <div class="community-stack-header">
                                        <div class="community-stack-avatar"
                                            style="background:linear-gradient(135deg,var(--accent),var(--accent-2))">
                                            {{ mb_substr($review->user->name, 0, 1) }}</div>
                                        <div>
                                            <div class="community-stack-name">{{ $review->user->name }}</div>
                                            <div class="community-stack-role" style="color:#f59e0b">
                                                @for($i = 0; $i < $review->rating; $i++)★@endfor
                                            </div>
                                        </div>
                                    </div>
                                    <div class="community-stack-content">{{ $review->text }}</div>
                                </div>
                            @endforeach
                        @else
                            @foreach($recentUsers as $idx => $u)
                                <div class="community-stack-card">
                                    <div class="community-stack-header">
                                        <div class="community-stack-avatar"
                                            style="background:linear-gradient(135deg,{{ $u['color'] }},var(--accent))">
                                            {{ $u['initial'] }}</div>
                                        <div>
                                            <div class="community-stack-name">{{ $u['name'] }}</div>
                                            <div class="community-stack-role">{{ $u['role'] }}</div>
                                        </div>
                                    </div>
                                    <div class="community-stack-content">{{ $u['name'] }} {{ t('home_community_joined') }}</div>
                                    <div class="community-stack-tags">
                                        @foreach($u['tags'] as $tag)
                                            <span class="community-stack-tag">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ══════════════════════════════════════════
       FINAL CTA SECTION
       ══════════════════════════════════════════ -->
    <section class="cta-section noise" id="cta">
        <div class="cta-bg-gradient"></div>
        <div class="container">
            <div class="cta-content reveal-scale">
                <div class="section-tag" style="margin:0 auto 24px;display:inline-flex"><i class="fas fa-rocket"></i> {{ t('home_cta_tag') }}</div>
                <h2 class="cta-title">{{ t('home_cta_title') }}</h2>
                <p class="cta-desc">{{ t('home_cta_desc') }}</p>
                <div class="cta-actions">
                    <a href="{{ route('register') }}" class="btn btn-primary magnetic">
                        <i class="fas fa-user-plus"></i>
                        <span>{{ t('home_cta_register') }}</span>
                    </a>
                    <a href="{{ route('courses.index') }}" class="btn btn-secondary magnetic">
                        <i class="fas fa-compass"></i>
                        <span>{{ t('home_cta_explore') }}</span>
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
            const config = {
                navDots: document.getElementById('navDots'),
                journeySection: document.querySelector('.journey-section'),
                particles: document.getElementById('particles-canvas'),
            };
            let scrollY = 0;
            let ticking = false;
            let vh = window.innerHeight;
            let docH = document.documentElement.scrollHeight - vh;
            /* ══════════════════════════════════════
               RESIZE HANDLER
               ══════════════════════════════════════ */
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    vh = window.innerHeight;
                    docH = document.documentElement.scrollHeight - vh;
                }, 150);
            });
            /* ══════════════════════════════════════
               PARTICLES
               ══════════════════════════════════════ */
            const ctx = config.particles ? config.particles.getContext('2d') : null;
            let particles = [];
            let pW = 0, pH = 0;
            function resizeParticles() {
                if (!config.particles) return;
                pW = config.particles.width = window.innerWidth;
                pH = config.particles.height = window.innerHeight;
            }
            resizeParticles();
            window.addEventListener('resize', resizeParticles);
            const particleCount = window.innerWidth < 768 ? 15 : 20;
            const drawConnections = true;
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
                    if (drawConnections) {
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
               COUNTER ANIMATION
               ══════════════════════════════════════ */
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !entry.target.dataset.animated) {
                        entry.target.dataset.animated = '1';
                        const target = parseInt(entry.target.dataset.count);
                        if (isNaN(target)) return;
                        const startTime = performance.now();
                        const duration = 2000;
                        function animateCounter(currentTime) {
                            const elapsed = currentTime - startTime;
                            const progress = Math.min(elapsed / duration, 1);
                            const eased = 1 - Math.pow(1 - progress, 3);
                            const current = Math.round(target * eased);
                            if (target >= 1000) {
                                entry.target.textContent = (current / 1000).toFixed(target >= 10000 ? 0 : 1) + 'K+';
                            } else {
                                entry.target.textContent = current + '+';
                            }
                            if (progress < 1) {
                                requestAnimationFrame(animateCounter);
                            }
                        }
                        requestAnimationFrame(animateCounter);
                    } else if (!entry.isIntersecting && entry.target.dataset.animated) {
                        delete entry.target.dataset.animated;
                        const target = parseInt(entry.target.dataset.count);
                        if (!isNaN(target)) {
                            entry.target.textContent = target >= 1000 ? (target / 1000).toFixed(target >= 10000 ? 0 : 1) + 'K+' : target + '+';
                        }
                    }
                });
            }, { threshold: 0.5 });
            document.querySelectorAll('[data-count]').forEach(el => counterObserver.observe(el));
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
               HERO CODE WINDOW TILT
               ══════════════════════════════════════ */
            const heroCodeWindow = document.getElementById('heroCodeWindow');
            if (heroCodeWindow) {
                let codeRect;
                heroCodeWindow.addEventListener('mouseenter', () => {
                    codeRect = heroCodeWindow.getBoundingClientRect();
                });
                heroCodeWindow.addEventListener('mousemove', (e) => {
                    const x = (e.clientX - codeRect.left) / codeRect.width - 0.5;
                    const y = (e.clientY - codeRect.top) / codeRect.height - 0.5;
                    heroCodeWindow.style.transition = 'transform 0.1s ease-out';
                    heroCodeWindow.style.transform = `perspective(1200px) rotateY(${x * 8}deg) rotateX(${-y * 8}deg)`;
                });
                heroCodeWindow.addEventListener('mouseleave', () => {
                    heroCodeWindow.style.transition = 'transform 0.6s cubic-bezier(0.16, 1, 0.3, 1)';
                    heroCodeWindow.style.transform = 'perspective(1200px) rotateY(-4deg) rotateX(2deg)';
                });
            }
            /* ══════════════════════════════════════
               CINEMATIC Z-AXIS 3D SCROLL ENGINE
               ══════════════════════════════════════ */
            const cinematicScenes = document.querySelectorAll('.cinematic-scene');
            function lerp(start, end, amt) {
                return (1 - amt) * start + amt * end;
            }
            function updateCinematic3DScroll() {
                const vh = window.innerHeight;
                const scrollY = window.scrollY;
                cinematicScenes.forEach(scene => {
                    const sceneTop = scene.offsetTop;
                    const sceneHeight = scene.offsetHeight - vh;
                    const scrolled = scrollY - sceneTop;
                    const globalProgress = Math.max(0, Math.min(1, scrolled / sceneHeight));
                    const panels = scene.querySelectorAll('.z-axis-panel');
                    const totalPanels = panels.length;
                    // Специальная логика для AI HUD
                    if (scene.id === 'ai') {
                        const core = document.getElementById('aiHudCore');
                        const chat = document.getElementById('aiHudChat');
                        if (globalProgress < 0.4) {
                            const p = globalProgress / 0.4;
                            const scale = lerp(0.8, 1.5, p);
                            const rotZ = p * 180;
                            core.style.transform = `translateZ(${lerp(-200, 100, p)}px) scale(${scale}) rotateZ(${rotZ}deg)`;
                            core.style.opacity = lerp(0.5, 1, p);
                            chat.style.opacity = 0;
                            chat.style.transform = `translateZ(-100px) scale(0.8)`;
                        } else {
                            const p = (globalProgress - 0.4) / 0.6;
                            core.style.transform = `translateZ(${lerp(100, -300, p)}px) scale(${lerp(1.5, 0.5, p)}) rotateZ(${180 + p * 90}deg)`;
                            core.style.opacity = lerp(1, 0.2, p);
                            chat.style.opacity = lerp(0, 1, Math.min(1, p * 2));
                            chat.style.transform = `translateZ(${lerp(-100, 50, p)}px) scale(${lerp(0.8, 1, p)}) rotateX(${lerp(20, 0, p)}deg)`;
                        }
                        return;
                    }
                    // Логика для Features и Courses (Z-Axis Fly-through)
                    panels.forEach((panel, index) => {
                        const panelStart = index / totalPanels;
                        const panelEnd = (index + 1) / totalPanels;
                        const panelProgress = (globalProgress - panelStart) / (panelEnd - panelStart);
                        let z, y, rotX, scale, op;
                        if (panelProgress < 0) {
                            z = -1000; y = 0; rotX = -30; op = 0; scale = 0.6;
                        } else if (panelProgress >= 0 && panelProgress <= 1) {
                            if (panelProgress < 0.25) {
                                const p = panelProgress / 0.25;
                                z = lerp(-1000, 0, p);
                                op = lerp(0, 1, p);
                                scale = lerp(0.6, 1, p);
                                rotX = lerp(-30, 0, p);
                                y = 0;
                            } else if (panelProgress > 0.75) {
                                const p = (panelProgress - 0.75) / 0.25;
                                z = lerp(0, 800, p);
                                y = lerp(0, -300, p);
                                op = lerp(1, 0, p);
                                scale = lerp(1, 1.2, p);
                                rotX = lerp(0, 45, p);
                            } else {
                                z = 0; y = 0; op = 1; scale = 1; rotX = 0;
                            }
                        } else {
                            z = 800; y = -300; op = 0; scale = 1.2; rotX = 45;
                        }
                        panel.style.transform = `translateZ(${z}px) translateY(${y}px) rotateX(${rotX}deg) scale(${scale})`;
                        panel.style.opacity = op;
                        if (op > 0.5) {
                            panel.classList.add('is-active');
                        } else {
                            panel.classList.remove('is-active');
                        }
                    });
                });
            }
            /* ══════════════════════════════════════
               FEATURE STEPS — STICKY SCROLL
               ══════════════════════════════════════ */
            const fpSteps = document.querySelectorAll('.fp-step');
            const fpStepsContainer = document.getElementById('fpSteps');
            let lastFpStepIndex = -1;
            function updateFpSteps() {
                if (!fpStepsContainer || !fpSteps.length) return;
                const scrollY = window.scrollY;
                const vh = window.innerHeight;
                const rect = fpStepsContainer.getBoundingClientRect();
                const containerTop = scrollY + rect.top;
                const containerH = rect.height;
                if (scrollY + vh < containerTop || scrollY > containerTop + containerH) {
                    if (lastFpStepIndex !== -1) {
                        fpSteps.forEach(p => p.classList.remove('active'));
                        lastFpStepIndex = -1;
                    }
                    return;
                }
                const scrolled = Math.max(0, scrollY - containerTop);
                const activeIndex = Math.min(fpSteps.length - 1, Math.floor(scrolled / vh));
                if (activeIndex !== lastFpStepIndex) {
                    lastFpStepIndex = activeIndex;
                    fpSteps.forEach((panel, i) => {
                        panel.classList.toggle('active', i === activeIndex);
                    });
                }
            }
            updateFpSteps();
            /* ══════════════════════════════════════
               COURSES — SCROLL-DRIVEN FLY-IN
               ══════════════════════════════════════ */
            const csSection = document.getElementById('courses');
            const csCards = document.querySelectorAll('.cs-scatter-card');
            if (csSection && csCards.length) {
                const cardData = [];
                csCards.forEach((card, i) => {
                    cardData.push({
                        el: card,
                        left: card.dataset.left,
                        top: card.dataset.top,
                        rotate: parseFloat(card.dataset.rotate),
                        z: parseInt(card.dataset.z),
                        startX: parseFloat(card.dataset.startX),
                        startY: parseFloat(card.dataset.startY),
                        startRot: parseFloat(card.dataset.startRot),
                        startScale: 0.5,
                        delay: parseInt(card.dataset.delay)
                    });
                });

                function updateCsScroll() {
                    const rect = csSection.getBoundingClientRect();
                    const sectionH = csSection.offsetHeight - window.innerHeight;
                    const scrolled = -rect.top;
                    const progress = Math.max(0, Math.min(1, scrolled / sectionH));
                    const totalCards = cardData.length;
                    const stagger = 0.7;

                    cardData.forEach((c, i) => {
                        const cardStart = c.delay / (totalCards * stagger);
                        const cardEnd = cardStart + (1 - stagger) + (1 / totalCards);
                        const p = Math.max(0, Math.min(1, (progress - cardStart) / (cardEnd - cardStart)));
                        const eased = 1 - Math.pow(1 - p, 4);

                        const x = c.startX * (1 - eased);
                        const y = c.startY * (1 - eased);
                        const rot = c.startRot * (1 - eased) + c.rotate * eased;
                        const sc = c.startScale + (1 - c.startScale) * eased;
                        const op = Math.min(1, eased * 3);
                        const floatY = eased >= 1 ? Math.sin(Date.now() * 0.002 + i * 1.5) * 4 : 0;

                        c.el.style.opacity = op;
                        c.el.style.left = c.left;
                        c.el.style.top = `calc(${c.top} + ${y}px + ${floatY}px)`;
                        c.el.style.zIndex = c.z;
                        c.el.style.transform = `translateX(${x}vw) rotate(${rot}deg) scale(${sc})`;
                    });
                    requestAnimationFrame(updateCsScroll);
                }
                requestAnimationFrame(updateCsScroll);

                csCards.forEach(card => {
                    card.addEventListener('mouseenter', () => {
                        card.style.zIndex = 50;
                        card.style.transform = 'rotate(0deg) scale(1.1)';
                        card.style.boxShadow = '0 20px 60px -12px rgba(0,0,0,0.5), 0 0 60px -10px rgba(0,245,212,0.25)';
                        card.style.borderColor = 'var(--accent)';
                    });
                    card.addEventListener('mouseleave', () => {
                        card.style.zIndex = card.dataset.z;
                        card.style.boxShadow = '';
                        card.style.borderColor = '';
                    });
                });
            }
            /* ══════════════════════════════════════
               JOURNEY FADE REVEAL
               ══════════════════════════════════════ */
            const journeyPanels = document.querySelectorAll('.journey-panel');
            let lastJourneyIndex = -1;
            function updateJourney() {
                if (!config.journeySection) return;
                const sectionTop = config.journeySection.offsetTop;
                const sectionHeight = config.journeySection.offsetHeight - vh;
                const scrolled = scrollY - sectionTop;
                if (scrolled > sectionHeight) return;
                if (scrolled < -vh) {
                    journeyPanels.forEach(p => p.classList.remove('active', 'exit-up'));
                    lastJourneyIndex = -1;
                    return;
                }
                const progress = Math.max(0, Math.min(1, scrolled / sectionHeight));
                const totalPanels = journeyPanels.length;
                const activeIndex = Math.min(totalPanels - 1, Math.floor(progress * totalPanels));
                if (activeIndex !== lastJourneyIndex) {
                    lastJourneyIndex = activeIndex;
                    journeyPanels.forEach((panel, i) => {
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
               LEADERS FULL-SCREEN SCROLL
               ══════════════════════════════════════ */
            const leadersPanels = document.querySelectorAll('.leader-panel');
            const leadersContainer = document.getElementById('leadersPanels');
            let lastLeaderIndex = -1;
            function updateLeadersScroll() {
                if (!leadersContainer || !leadersPanels.length) return;
                const scrollY = window.scrollY;
                const vh = window.innerHeight;
                const leadersIntro = document.getElementById('leaders');
                const sectionStart = leadersIntro.offsetTop;
                const sectionEnd = leadersContainer.offsetTop + leadersContainer.offsetHeight;
                if (scrollY + vh < sectionStart || scrollY > sectionEnd) {
                    leadersPanels.forEach(p => p.classList.remove('active'));
                    lastLeaderIndex = -1;
                    return;
                }
                const containerTop = leadersContainer.offsetTop;
                const scrolled = Math.max(0, scrollY - containerTop);
                const panelH = vh;
                const totalPanels = leadersPanels.length;
                const activeIndex = Math.min(totalPanels - 1, Math.floor(scrolled / panelH));
                leadersPanels.forEach((panel, i) => {
                    panel.classList.toggle('active', i === activeIndex);
                });
                lastLeaderIndex = activeIndex;
            }
            /* ══════════════════════════════════════
               NAVIGATION DOTS
               ══════════════════════════════════════ */
            const sections = ['hero', 'journey', 'leaders', 'features', 'courses', 'ai', 'community'];
            const navDotEls = document.querySelectorAll('.nav-dot');
            navDotEls.forEach((dot, i) => {
                dot.addEventListener('click', () => {
                    const target = document.getElementById(sections[i]);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
            function updateNavDots() {
                if (!config.navDots) return;
                const showDots = scrollY > vh * 0.5;
                config.navDots.classList.toggle('visible', showDots);
                let activeIndex = 0;
                sections.forEach((id, i) => {
                    const el = document.getElementById(id);
                    if (el) {
                        const top = el.offsetTop - vh * 0.4;
                        if (scrollY >= top) activeIndex = i;
                    }
                });
                navDotEls.forEach((dot, i) => {
                    dot.classList.toggle('active', i === activeIndex);
                });
            }
            /* ══════════════════════════════════════
               MASTER SCROLL HANDLER
               ══════════════════════════════════════ */
            function onScroll() {
                scrollY = window.scrollY;
                // Scroll progress bar
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                const scrollPercent = docHeight > 0 ? (scrollY / docHeight) * 100 : 0;
                const progressBar = document.getElementById('scrollProgress');
                if (progressBar) progressBar.style.width = scrollPercent + '%';
                // Journey fade reveal
                updateJourney();
                // Leaders full-screen scroll
                updateLeadersScroll();
                // CINEMATIC 3D SCROLL
                updateCinematic3DScroll();
                // Feature steps sticky scroll
                updateFpSteps();
                // Navigation dots
                updateNavDots();
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
            // Initial call
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
            /* ══════════════════════════════════════
               SMOOTH ANCHOR SCROLL
               ══════════════════════════════════════ */
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const id = this.getAttribute('href');
                    if (id.length > 1) {
                        const target = document.querySelector(id);
                        if (target) {
                            e.preventDefault();
                            target.scrollIntoView({ behavior: 'smooth' });
                        }
                    }
                });
            });
            /* ══════════════════════════════════════
               COMMUNITY CARDS FLOAT
               ══════════════════════════════════════ */
            const communityCards = document.querySelectorAll('.community-stack-card');
            let floatAngle = 0;
            function animateCommunityFloat() {
                floatAngle += 0.005;
                communityCards.forEach((card, i) => {
                    const offset = Math.sin(floatAngle + i * 1.5) * 5;
                    const rotate = Math.sin(floatAngle * 0.5 + i) * 1;
                    card.style.transform = `translateY(${offset}px) rotate(${card.style.transform.match(/rotate\(([^)]+)\)/)?.[1] || (i === 0 ? '-3' : i === 1 ? '2' : '1')}deg)`;
                });
                requestAnimationFrame(animateCommunityFloat);
            }
            if (communityCards.length) {
                setTimeout(animateCommunityFloat, 2000);
            }
        })();
    </script>
@endsection
