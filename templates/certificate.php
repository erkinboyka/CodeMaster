<?php if (!defined('APP_INIT'))
    die('Direct access not permitted'); ?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('cert_page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Montserrat:wght@400;500;600&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: #e2e8f0;
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
        }

        .cert-wrapper {
            width: 100%;
            max-width: 1100px;
            aspect-ratio: 1.414 / 1;
            margin: 0 auto;
            position: relative;
        }

        .course-cert {
            background: #ffffff;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 4rem;
            background-image: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.85) 100%);
        }

        .cert-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                repeating-linear-gradient(45deg, rgba(180, 134, 8, 0.03) 0, rgba(180, 134, 8, 0.03) 1px, transparent 1px, transparent 15px),
                repeating-linear-gradient(-45deg, rgba(180, 134, 8, 0.03) 0, rgba(180, 134, 8, 0.03) 1px, transparent 1px, transparent 15px),
                linear-gradient(135deg, rgba(0, 0, 0, 0.01) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.01) 75%, rgba(0, 0, 0, 0.01)),
                linear-gradient(45deg, rgba(0, 0, 0, 0.01) 25%, transparent 25%, transparent 75%, rgba(0, 0, 0, 0.01) 75%, rgba(0, 0, 0, 0.01));
            background-size: 15px 15px, 15px 15px, 30px 30px, 30px 30px;
            z-index: 0;
            pointer-events: none;
        }

        .cert-microtext {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                repeating-linear-gradient(0deg, transparent 0px, transparent 29px, rgba(180, 134, 8, 0.01) 30px),
                repeating-linear-gradient(90deg, transparent 0px, transparent 29px, rgba(180, 134, 8, 0.01) 30px),
                repeating-linear-gradient(180deg, transparent 0px, transparent 29px, rgba(180, 134, 8, 0.01) 30px),
                repeating-linear-gradient(270deg, transparent 0px, transparent 29px, rgba(180, 134, 8, 0.01) 30px);
            z-index: 0;
            pointer-events: none;
            opacity: 0.3;
        }

        .cert-frame-outer {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 3px solid #b48608;
            border-image: linear-gradient(45deg, #b48608 0%, #d4af37 50%, #b48608 100%) 1;
            z-index: 1;
            pointer-events: none;
            box-shadow: 0 0 20px rgba(180, 134, 8, 0.1);
        }

        .cert-frame-inner {
            position: absolute;
            top: 28px;
            left: 28px;
            right: 28px;
            bottom: 28px;
            border: 2px dashed #94a3b8;
            border-image: linear-gradient(135deg, #94a3b8 0%, #64748b 50%, #94a3b8 100%) 1;
            z-index: 1;
            pointer-events: none;
        }

        .cert-frame-middle {
            position: absolute;
            top: 35px;
            left: 35px;
            right: 35px;
            bottom: 35px;
            border: 1px solid rgba(180, 134, 8, 0.2);
            z-index: 1;
            pointer-events: none;
        }

        .corner-ornament {
            position: absolute;
            width: 100px;
            height: 100px;
            color: #b48608;
            z-index: 2;
            opacity: 0.8;
        }

        .corner-tl {
            top: 10px;
            left: 10px;
            transform: rotate(0deg);
        }

        .corner-tr {
            top: 10px;
            right: 10px;
            transform: rotate(90deg);
        }

        .corner-br {
            bottom: 10px;
            right: 10px;
            transform: rotate(180deg);
        }

        .corner-bl {
            bottom: 10px;
            left: 10px;
            transform: rotate(270deg);
        }

        .side-ornament {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #b48608;
            font-size: 28px;
            opacity: 0.6;
            z-index: 2;
            animation: pulse 3s infinite;
        }

        .side-left {
            left: 8px;
        }

        .side-right {
            right: 8px;
        }

        .top-ornament,
        .bottom-ornament {
            position: absolute;
            width: 100%;
            height: 40px;
            color: #b48608;
            opacity: 0.4;
            z-index: 2;
        }

        .top-ornament {
            top: 15px;
        }

        .bottom-ornament {
            bottom: 15px;
        }

        .center-ornament {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            color: #b48608;
            opacity: 0.1;
            z-index: 1;
            pointer-events: none;
        }

        .cert-content {
            position: relative;
            z-index: 10;
            width: 100%;
            height: 100%;
            text-align: center;
            display: flex;
            flex-direction: column;
            background: rgba(255, 255, 255, 0.92);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .course-cert-title {
            font-family: 'Playfair Display', serif;
            letter-spacing: 0.05em;
        }

        .cert-signature {
            font-family: 'Great Vibes', cursive;
            letter-spacing: 0;
        }

        .cert-qr canvas {
            display: block;
        }

        .text-gold {
            color: #b48608;
        }

        .border-gold {
            border-color: #b48608;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 0.4;
            }

            50% {
                opacity: 0.8;
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -100% 0;
            }

            100% {
                background-position: 100% 0;
            }
        }

        .holographic-effect {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(180, 134, 8, 0.02) 25%,
                    rgba(255, 255, 255, 0) 50%,
                    rgba(180, 134, 8, 0.02) 75%,
                    rgba(255, 255, 255, 0) 100%);
            background-size: 200% 200%;
            animation: shimmer 8s ease infinite;
            z-index: 0;
            pointer-events: none;
        }

        @media (max-width: 1024px) {
            .cert-wrapper {
                max-width: 100%;
                aspect-ratio: auto;
            }

            .course-cert {
                min-height: 720px;
                padding: 2rem;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 0.75rem;
            }

            #cert-actions {
                margin-bottom: 1rem;
                gap: 0.75rem;
                flex-direction: column;
                align-items: stretch;
            }

            #cert-actions .flex {
                width: 100%;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            #cert-actions button {
                width: 100%;
                padding: 0.625rem 0.5rem;
            }

            .course-cert {
                min-height: auto;
                padding: 1rem;
                border-radius: 0.75rem;
            }

            .cert-content {
                padding: 0.75rem;
            }

            .corner-ornament,
            .side-ornament,
            .top-ornament,
            .bottom-ornament,
            .center-ornament {
                display: none;
            }

            .cert-frame-outer {
                top: 10px;
                left: 10px;
                right: 10px;
                bottom: 10px;
            }

            .cert-frame-middle {
                top: 16px;
                left: 16px;
                right: 16px;
                bottom: 16px;
            }

            .cert-frame-inner {
                top: 13px;
                left: 13px;
                right: 13px;
                bottom: 13px;
            }
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 0;
            }

            body {
                background: white;
            }

            body * {
                visibility: hidden;
            }

            #cert-actions {
                display: none !important;
            }

            .cert-wrapper {
                position: absolute;
                left: 0;
                top: 0;
                width: 100vw !important;
                height: 100vh !important;
                max-width: none !important;
                aspect-ratio: auto;
            }

            .course-cert {
                box-shadow: none;
                border-radius: 0;
                padding: 40px 60px;
            }

            .course-cert::before,
            .course-cert,
            .course-cert *,
            .cert-bg-pattern,
            .cert-frame-outer,
            .cert-frame-inner,
            .cert-frame-middle,
            .cert-microtext,
            .holographic-effect {
                visibility: visible;
            }

            .side-ornament {
                animation: none;
            }
        }
    </style>
</head>

<body class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-8 tf-public-motion">
    <?php
    $certPublicView = !empty($certPublicView);
    $certHash = (string) ($cert['cert_hash'] ?? '');
    $issuerName = normalizeMojibakeText((string) ($cert['issuer'] ?? 'CodeMaster'));
    $signatureName = defined('APP_NAME') ? (string) APP_NAME : ($issuerName !== '' ? $issuerName : 'CodeMaster');

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $basePath = rtrim(str_replace('\\', '/', (string) dirname((string) ($_SERVER['SCRIPT_NAME'] ?? ''))), '/');
    $baseUrl = $scheme . '://' . $host . ($basePath !== '' ? $basePath : '');
    $publicUrl = $certHash !== '' ? ($baseUrl . '/?action=certificate-public&hash=' . rawurlencode($certHash)) : '';
    ?>

    <div id="cert-actions" class="w-full max-w-[1100px] flex items-center justify-between gap-3 mb-6">
        <a href="<?= $certPublicView ? '?action=home' : '?action=profile&tab=certificates' ?>"
            class="text-sm text-slate-600 hover:text-indigo-600 font-medium transition-colors">
            <i class="fas fa-arrow-left mr-1"></i> <?= $certPublicView ? t('cert_home', 'Home') : t('cert_back') ?>
        </a>
        <div class="flex gap-3">
            <button onclick="window.print()"
                class="px-5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm">
                <i class="fas fa-print mr-1"></i> <?= t('cert_print') ?>
            </button>
            <button onclick="window.print()"
                class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                <i class="fas fa-download mr-1"></i> <?= t('cert_download_pdf') ?>
            </button>
        </div>
    </div>

    <main class="cert-wrapper">
        <div id="cert-sheet" class="course-cert rounded-xl">

            <div class="cert-bg-pattern"></div>
            <div class="cert-microtext"></div>
            <div class="holographic-effect"></div>

            <div class="cert-frame-outer"></div>
            <div class="cert-frame-middle"></div>
            <div class="cert-frame-inner"></div>

            <?php
            // УлСѓчшРµнныРµ СѓРілРѕвыРµ SVG СѓР·Рѕры
            $cornerSvg = '<svg viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.5" class="w-full h-full">
                            <path d="M5,115 Q30,70 5,60 Q30,50 5,40 Q30,30 5,20 Q30,10 5,5" stroke-width="1.2"/>
                            <path d="M15,115 Q40,75 15,65 Q40,55 15,45 Q40,35 15,25 Q40,15 15,5" stroke-width="1"/>
                            <path d="M25,115 Q50,80 25,70 Q50,60 25,50 Q50,40 25,30 Q50,20 25,5" stroke-width="0.8"/>
                            <circle cx="15" cy="15" r="2" fill="currentColor"/>
                            <circle cx="25" cy="25" r="1.5" fill="currentColor"/>
                            <circle cx="35" cy="35" r="1" fill="currentColor"/>
                          </svg>';
            ?>
            <div class="corner-ornament corner-tl"><?= $cornerSvg ?></div>
            <div class="corner-ornament corner-tr"><?= $cornerSvg ?></div>
            <div class="corner-ornament corner-br"><?= $cornerSvg ?></div>
            <div class="corner-ornament corner-bl"><?= $cornerSvg ?></div>

            <div class="side-ornament side-left"><i class="fas fa-dharmachakra"></i></div>
            <div class="side-ornament side-right"><i class="fas fa-dharmachakra"></i></div>

            <?php
            // Р’Рµрхний и ниР¶ний дРµкРѕративныРµ СѓР·Рѕры
            $horizontalOrnament = '<svg viewBox="0 0 1000 40" fill="none" stroke="currentColor" stroke-width="1" class="w-full h-full">
                                    <path d="M0,20 L1000,20" stroke-dasharray="5,5" opacity="0.6"/>
                                    <path d="M0,15 L1000,15 M0,25 L1000,25" stroke-dasharray="2,3" opacity="0.4"/>
                                    <circle cx="50" cy="20" r="2" fill="currentColor" opacity="0.3"/>
                                    <circle cx="150" cy="20" r="2" fill="currentColor" opacity="0.3"/>
                                    <circle cx="250" cy="20" r="2" fill="currentColor" opacity="0.3"/>
                                    <circle cx="350" cy="20" r="2" fill="currentColor" opacity="0.3"/>
                                    <circle cx="450" cy="20" r="2" fill="currentColor" opacity="0.3"/>
                                    <circle cx="550" cy="20" r="2" fill="currentColor" opacity="0.3"/>
                                    <circle cx="650" cy="20" r="2" fill="currentColor" opacity="0.3"/>
                                    <circle cx="750" cy="20" r="2" fill="currentColor" opacity="0.3"/>
                                    <circle cx="850" cy="20" r="2" fill="currentColor" opacity="0.3"/>
                                    <circle cx="950" cy="20" r="2" fill="currentColor" opacity="0.3"/>
                                  </svg>';
            ?>
            <div class="top-ornament"><?= $horizontalOrnament ?></div>
            <div class="bottom-ornament"><?= $horizontalOrnament ?></div>

            <div class="center-ornament">
                <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1">
                    <circle cx="50" cy="50" r="45" stroke-dasharray="5,5" opacity="0.5" />
                    <circle cx="50" cy="50" r="35" stroke-dasharray="3,3" opacity="0.4" />
                    <circle cx="50" cy="50" r="25" stroke-dasharray="2,2" opacity="0.3" />
                    <circle cx="50" cy="50" r="15" fill="currentColor" opacity="0.1" />
                </svg>
            </div>

            <div class="cert-content justify-between">

                <div class="mt-4">
                    <div
                        class="text-sm font-bold uppercase tracking-[0.4em] text-gold mb-4 flex items-center justify-center gap-3">
                        <span class="h-[1px] w-12 bg-gold opacity-50"></span>
                        <span><?= t('cert_academy') ?></span>
                        <span class="h-[1px] w-12 bg-gold opacity-50"></span>
                    </div>
                    <h1
                        class="course-cert-title text-3xl sm:text-5xl md:text-6xl font-extrabold text-slate-900 mt-2 mb-6">
                        <?= t('cert_heading') ?>
                    </h1>
                </div>

                <div class="flex-grow flex flex-col justify-center">
                    <p class="text-slate-500 uppercase tracking-widest text-sm mb-3"><?= t('cert_confirm') ?></p>
                    <p
                        class="course-cert-title text-2xl sm:text-4xl font-bold text-slate-800 mb-8 border-b border-slate-300 inline-block mx-auto pb-3 px-6 sm:px-12 relative">
                        <?= htmlspecialchars(normalizeMojibakeText((string) ($cert['user_name'] ?? ''))) ?>
                        <span
                            class="absolute -bottom-[5px] left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gold rotate-45"></span>
                    </p>

                    <p class="text-slate-500 uppercase tracking-widest text-sm mb-3"><?= t('cert_completed') ?></p>
                    <p class="text-xl sm:text-2xl font-semibold text-slate-900 max-w-3xl mx-auto leading-relaxed">
                        <?= htmlspecialchars(normalizeMojibakeText((string) ($cert['course_title'] ?? ''))) ?>
                    </p>
                </div>

                <div class="w-full mt-8 px-2 sm:px-8">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5 pt-6">

                        <div class="text-center w-full sm:w-1/3">
                            <div
                                class="text-lg text-slate-800 font-medium border-b border-slate-400 pb-1 mb-2 inline-block min-w-[120px] sm:min-w-[180px]">
                                <?php $issueDate = (string) ($cert['issue_date'] ?? ''); ?>
                                <?= htmlspecialchars($issueDate !== '' ? formatDate($issueDate) : '') ?>
                            </div>
                            <div class="text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                                <?= t('cert_date') ?>
                            </div>
                        </div>

                        <div class="w-full sm:w-1/3 flex justify-center">
                            <div
                                class="w-20 h-20 rounded-full border-[3px] border-dashed border-gold text-gold flex items-center justify-center relative bg-white">
                                <div class="absolute inset-1 rounded-full border border-gold opacity-50"></div>
                                <i class="fas fa-award text-3xl"></i>
                            </div>
                        </div>

                        <div class="text-center w-full sm:w-1/3">
                            <div
                                class="text-lg text-slate-800 font-medium border-b border-slate-400 pb-1 mb-2 inline-block min-w-[120px] sm:min-w-[180px]">
                                <?= htmlspecialchars($issuerName) ?>
                            </div>
                            <div class="text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                                <?= t('cert_issuer') ?>
                            </div>
                            <div class="cert-signature text-3xl text-slate-800 mt-3 leading-none">
                                <?= htmlspecialchars($signatureName) ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($publicUrl !== ''): ?>
                        <div class="mt-7 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                            <div class="text-left">
                                <div class="text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                                    <?= t('cert_verify', 'Verify') ?>
                                </div>
                                <div class="text-xs text-slate-600 mt-1 break-all"><?= htmlspecialchars($publicUrl) ?></div>
                                <div class="text-[10px] tracking-widest text-slate-400 mt-2">
                                    <?= t('cert_scan_qr', 'Scan QR') ?>
                                </div>
                            </div>
                            <div class="bg-white/80 border border-slate-200 rounded-lg p-2 shadow-sm">
                                <div id="cert-qr" class="cert-qr" data-url="<?= htmlspecialchars($publicUrl) ?>"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div
                        class="mt-8 text-[10px] tracking-widest text-slate-400 font-mono flex items-center justify-center gap-2">
                        <span class="h-[1px] w-8 bg-slate-200"></span>
                        ID: <?= (int) $cert['id'] ?>
                        <span class="h-[1px] w-8 bg-slate-200"></span>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php if (!empty($_GET['download'])): ?>
        <script>
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 500);
            });
        </script>
    <?php endif; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        (function () {
            const el = document.getElementById('cert-qr');
            if (!el || typeof QRCode === 'undefined') return;
            const url = el.dataset.url || '';
            if (!url) return;
            el.innerHTML = '';
            new QRCode(el, {
                text: url,
                width: 96,
                height: 96,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        })();
    </script>
</body>

</html>

