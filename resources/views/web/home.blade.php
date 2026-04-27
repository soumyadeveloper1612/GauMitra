<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gau Mitra - Gau Mata Protection & Emergency Rescue Platform</title>

    <meta name="description" content="Gau Mitra is a spiritual and technology-driven cow welfare emergency reporting and rescue platform for Gau Mata protection.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Noto+Sans+Devanagari:wght@600;700;800&family=Cinzel:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --saffron: #f97316;
            --saffron-dark: #c2410c;
            --gold: #facc15;
            --gold-dark: #b7791f;
            --kumkum: #9f1239;
            --maroon: #5b1020;
            --green: #166534;
            --green-dark: #063b22;
            --cream: #fff7ed;
            --cream-light: #fffaf0;
            --sand: #fdecc8;
            --brown: #6b3f18;
            --text: #2b1908;
            --muted: #7a5c3a;
            --white: #ffffff;
            --border: rgba(139, 92, 24, 0.18);
            --shadow: 0 28px 80px rgba(91, 16, 32, 0.16);
            --soft-shadow: 0 18px 55px rgba(107, 63, 24, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(250, 204, 21, 0.16), transparent 30%),
                radial-gradient(circle at top right, rgba(249, 115, 22, 0.12), transparent 28%),
                linear-gradient(180deg, #fffaf0 0%, #fff7ed 42%, #ffffff 100%);
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
        }

        .deva {
            font-family: 'Noto Sans Devanagari', sans-serif;
        }

        .brand-font {
            font-family: 'Cinzel', serif;
        }

        .topbar {
            background:
                linear-gradient(90deg, var(--maroon), var(--kumkum), var(--saffron-dark));
            color: #fff8dc;
            font-size: 14px;
            padding: 10px 0;
            position: relative;
            overflow: hidden;
        }

        .topbar::before {
            content: "ॐ गौ माता ॐ गौ रक्षा ॐ सेवा ॐ";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            white-space: nowrap;
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size: 42px;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.055);
            letter-spacing: 12px;
            pointer-events: none;
        }

        .topbar span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            z-index: 2;
        }

        .navbar {
            background: rgba(255, 250, 240, 0.88);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(180, 83, 9, 0.16);
            box-shadow: 0 16px 45px rgba(91, 16, 32, 0.08);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 13px;
            font-weight: 900;
            color: var(--maroon) !important;
            letter-spacing: -0.5px;
        }

        .brand-icon {
            width: 54px;
            height: 54px;
            border-radius: 19px;
            background:
                radial-gradient(circle at 30% 20%, #fff7ad, transparent 30%),
                linear-gradient(135deg, var(--gold), var(--saffron), var(--kumkum));
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 27px;
            box-shadow: 0 15px 36px rgba(249, 115, 22, 0.34);
            border: 2px solid rgba(255, 255, 255, 0.75);
        }

        .brand-title {
            line-height: 1.05;
        }

        .brand-title strong {
            font-size: 23px;
            font-family: 'Cinzel', serif;
            letter-spacing: 0.5px;
        }

        .brand-title small {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.6px;
        }

        .nav-link {
            color: #4b2506 !important;
            font-weight: 800;
            margin: 0 5px;
            position: relative;
        }

        .nav-link::after {
            content: "";
            position: absolute;
            width: 0;
            height: 2px;
            left: 12px;
            bottom: 4px;
            background: linear-gradient(90deg, var(--saffron), var(--gold));
            transition: 0.25s ease;
        }

        .nav-link:hover {
            color: var(--saffron-dark) !important;
        }

        .nav-link:hover::after {
            width: calc(100% - 24px);
        }

        .btn-main {
            background:
                linear-gradient(135deg, var(--saffron), var(--kumkum));
            border: none;
            color: #fff;
            border-radius: 999px;
            padding: 13px 24px;
            font-weight: 900;
            box-shadow: 0 18px 40px rgba(159, 18, 57, 0.28);
            transition: 0.25s ease;
        }

        .btn-main:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 24px 55px rgba(159, 18, 57, 0.35);
        }

        .btn-light-main {
            background: rgba(255, 255, 255, 0.88);
            color: var(--maroon);
            border: 1px solid rgba(249, 115, 22, 0.28);
            border-radius: 999px;
            padding: 13px 24px;
            font-weight: 900;
            box-shadow: 0 12px 35px rgba(107, 63, 24, 0.08);
            transition: 0.25s ease;
        }

        .btn-light-main:hover {
            transform: translateY(-2px);
            background: #fff;
            color: var(--saffron-dark);
        }

        .hero {
            position: relative;
            padding: 105px 0 86px;
            overflow: hidden;
            background:
                radial-gradient(circle at 15% 15%, rgba(250, 204, 21, 0.32), transparent 28%),
                radial-gradient(circle at 85% 18%, rgba(159, 18, 57, 0.18), transparent 28%),
                radial-gradient(circle at 78% 78%, rgba(22, 101, 52, 0.14), transparent 28%),
                linear-gradient(135deg, #fff7ed 0%, #fffaf0 45%, #fff2cc 100%);
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle, rgba(91, 16, 32, 0.08) 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.45;
        }

        .hero::after {
            content: "ॐ";
            position: absolute;
            right: 5%;
            top: 7%;
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size: 220px;
            font-weight: 800;
            color: rgba(159, 18, 57, 0.06);
            line-height: 1;
        }

        .hero .container {
            position: relative;
            z-index: 2;
        }

        .spiritual-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.78);
            color: var(--kumkum);
            border: 1px solid rgba(249, 115, 22, 0.30);
            padding: 10px 17px;
            border-radius: 999px;
            font-weight: 900;
            margin-bottom: 20px;
            box-shadow: 0 12px 35px rgba(107, 63, 24, 0.08);
        }

        .hero h1 {
            font-size: clamp(42px, 6vw, 78px);
            line-height: 1.02;
            font-weight: 900;
            letter-spacing: -2.4px;
            color: var(--maroon);
            margin-bottom: 22px;
        }

        .hero h1 span {
            background: linear-gradient(135deg, var(--saffron-dark), var(--gold-dark), var(--green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            color: var(--muted);
            font-size: 18px;
            line-height: 1.86;
            max-width: 660px;
            font-weight: 500;
        }

        .mantra-line {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0 4px;
            color: var(--green-dark);
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size: 19px;
            font-weight: 800;
            background: rgba(255, 255, 255, 0.58);
            border: 1px solid rgba(22, 101, 52, 0.14);
            border-radius: 18px;
            padding: 12px 16px;
        }

        .hero-stat {
            background: rgba(255, 255, 255, 0.74);
            border: 1px solid rgba(249, 115, 22, 0.22);
            border-radius: 24px;
            padding: 18px;
            box-shadow: 0 16px 38px rgba(107, 63, 24, 0.09);
            height: 100%;
        }

        .hero-stat h3 {
            margin: 0;
            font-size: 29px;
            font-weight: 900;
            color: var(--kumkum);
        }

        .hero-stat p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.4;
            font-weight: 800;
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(249, 115, 22, 0.24);
            border-radius: 38px;
            box-shadow: var(--shadow);
            padding: 22px;
            position: relative;
            z-index: 2;
        }

        .hero-card::before {
            content: "";
            position: absolute;
            inset: -10px;
            border-radius: 44px;
            background:
                linear-gradient(135deg, rgba(250, 204, 21, 0.28), rgba(249, 115, 22, 0.12), rgba(22, 101, 52, 0.12));
            z-index: -1;
            filter: blur(18px);
        }

        .gau-panel {
            background:
                radial-gradient(circle at 18% 15%, rgba(255, 255, 255, 0.26), transparent 26%),
                linear-gradient(145deg, var(--maroon), var(--kumkum) 46%, var(--saffron-dark));
            color: #fff;
            border-radius: 32px;
            padding: 30px;
            min-height: 430px;
            position: relative;
            overflow: hidden;
        }

        .gau-panel::before {
            content: "गौ";
            position: absolute;
            right: -20px;
            top: -38px;
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size: 170px;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.08);
            line-height: 1;
        }

        .gau-panel::after {
            content: "";
            position: absolute;
            width: 210px;
            height: 210px;
            right: -70px;
            bottom: -70px;
            background: rgba(250, 204, 21, 0.16);
            border-radius: 50%;
        }

        .gau-icon-large {
            width: 88px;
            height: 88px;
            border-radius: 29px;
            background:
                radial-gradient(circle at 30% 25%, rgba(255,255,255,0.75), transparent 22%),
                linear-gradient(135deg, var(--gold), var(--saffron));
            display: grid;
            place-items: center;
            font-size: 42px;
            color: #5b1020;
            box-shadow: 0 18px 45px rgba(0,0,0,0.18);
            margin-bottom: 22px;
            position: relative;
            z-index: 2;
        }

        .gau-panel h3,
        .gau-panel p,
        .gau-panel .rescue-flow {
            position: relative;
            z-index: 2;
        }

        .gau-panel h3 {
            font-weight: 900;
            margin-bottom: 13px;
        }

        .gau-panel p {
            color: rgba(255,255,255,0.86);
            line-height: 1.7;
            margin-bottom: 0;
        }

        .rescue-flow {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 26px;
            padding: 17px;
            margin-top: 24px;
            color: var(--text);
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.16);
        }

        .flow-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px dashed rgba(107, 63, 24, 0.22);
            font-size: 14px;
        }

        .flow-row:last-child {
            border-bottom: none;
        }

        .flow-row strong {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--maroon);
        }

        .flow-row span {
            color: var(--muted);
            font-weight: 800;
        }

        .section {
            padding: 88px 0;
            position: relative;
        }

        .section-title {
            max-width: 790px;
            margin: 0 auto 48px;
            text-align: center;
        }

        .section-title .mini {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--saffron-dark);
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 13px;
            margin-bottom: 13px;
            background: rgba(255, 247, 237, 0.9);
            border: 1px solid rgba(249, 115, 22, 0.20);
            border-radius: 999px;
            padding: 8px 14px;
        }

        .section-title h2 {
            font-size: clamp(32px, 4vw, 50px);
            font-weight: 900;
            letter-spacing: -1.5px;
            color: var(--maroon);
            margin-bottom: 14px;
        }

        .section-title p {
            color: var(--muted);
            font-size: 17px;
            line-height: 1.85;
            margin: 0;
            font-weight: 500;
        }

        .feature-card {
            height: 100%;
            background:
                linear-gradient(180deg, rgba(255,255,255,0.96), rgba(255,250,240,0.88));
            border: 1px solid rgba(249, 115, 22, 0.18);
            border-radius: 30px;
            padding: 31px;
            box-shadow: var(--soft-shadow);
            transition: 0.28s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: "";
            position: absolute;
            width: 130px;
            height: 130px;
            right: -52px;
            top: -52px;
            background: rgba(250, 204, 21, 0.16);
            border-radius: 50%;
            transition: 0.28s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow);
            border-color: rgba(249, 115, 22, 0.36);
        }

        .feature-card:hover::before {
            transform: scale(1.25);
        }

        .feature-icon {
            width: 66px;
            height: 66px;
            border-radius: 22px;
            background:
                linear-gradient(135deg, rgba(250, 204, 21, 0.28), rgba(249, 115, 22, 0.18));
            color: var(--saffron-dark);
            display: grid;
            place-items: center;
            font-size: 31px;
            margin-bottom: 23px;
            position: relative;
            z-index: 2;
        }

        .feature-card h4 {
            font-weight: 900;
            color: var(--maroon);
            margin-bottom: 12px;
            position: relative;
            z-index: 2;
        }

        .feature-card p {
            color: var(--muted);
            line-height: 1.75;
            margin-bottom: 0;
            position: relative;
            z-index: 2;
        }

        .spiritual-strip {
            background:
                linear-gradient(90deg, var(--green-dark), var(--green), var(--saffron-dark));
            color: #fff8dc;
            padding: 22px 0;
            overflow: hidden;
            position: relative;
        }

        .spiritual-strip::before {
            content: "ॐ सर्वे भवन्तु सुखिनः  •  गौ सेवा परम धर्म  •  धर्मो रक्षति रक्षितः  •";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size: 42px;
            font-weight: 800;
            opacity: 0.08;
            white-space: nowrap;
        }

        .strip-content {
            position: relative;
            z-index: 2;
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size: clamp(20px, 3vw, 32px);
            font-weight: 800;
            text-align: center;
        }

        .process {
            background:
                radial-gradient(circle at 8% 22%, rgba(250, 204, 21, 0.18), transparent 28%),
                linear-gradient(180deg, #ffffff, #fff7ed);
        }

        .step-card {
            background: rgba(255, 255, 255, 0.93);
            border: 1px solid rgba(249, 115, 22, 0.18);
            border-radius: 28px;
            padding: 29px;
            height: 100%;
            position: relative;
            overflow: hidden;
            box-shadow: var(--soft-shadow);
        }

        .step-card::after {
            content: "";
            position: absolute;
            inset: auto -45px -45px auto;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(159, 18, 57, 0.07);
        }

        .step-no {
            width: 52px;
            height: 52px;
            background:
                linear-gradient(135deg, var(--kumkum), var(--saffron));
            color: #fff;
            border-radius: 17px;
            display: grid;
            place-items: center;
            font-weight: 900;
            margin-bottom: 19px;
            box-shadow: 0 14px 30px rgba(159, 18, 57, 0.22);
        }

        .step-card h4 {
            color: var(--maroon);
        }

        .guideline-area {
            background:
                radial-gradient(circle at 90% 5%, rgba(249, 115, 22, 0.14), transparent 28%),
                linear-gradient(180deg, #fffaf0, #ffffff);
        }

        .guideline-box {
            background: rgba(255, 255, 255, 0.94);
            border-radius: 36px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(249, 115, 22, 0.20);
            padding: 37px;
            position: relative;
            overflow: hidden;
        }

        .guideline-box::before {
            content: "ॐ";
            position: absolute;
            right: 24px;
            top: 8px;
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size: 88px;
            font-weight: 800;
            color: rgba(249, 115, 22, 0.07);
        }

        .guideline-item {
            display: flex;
            gap: 16px;
            padding: 17px 0;
            border-bottom: 1px solid rgba(107, 63, 24, 0.12);
            position: relative;
            z-index: 2;
        }

        .guideline-item:last-child {
            border-bottom: none;
        }

        .guideline-item i {
            width: 42px;
            height: 42px;
            min-width: 42px;
            display: grid;
            place-items: center;
            border-radius: 15px;
            background:
                linear-gradient(135deg, rgba(250,204,21,0.26), rgba(249,115,22,0.16));
            color: var(--saffron-dark);
            font-size: 21px;
        }

        .guideline-item h5 {
            color: var(--maroon);
        }

        .impact {
            background:
                radial-gradient(circle at 15% 15%, rgba(250, 204, 21, 0.18), transparent 28%),
                radial-gradient(circle at 80% 10%, rgba(255,255,255,0.12), transparent 25%),
                linear-gradient(135deg, var(--maroon), var(--kumkum) 48%, var(--green-dark));
            color: #fff;
            overflow: hidden;
        }

        .impact::before {
            content: "गौ माता संरक्षण";
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size: 120px;
            font-weight: 800;
            color: rgba(255,255,255,0.05);
            white-space: nowrap;
        }

        .impact .container {
            position: relative;
            z-index: 2;
        }

        .impact-card {
            text-align: center;
            padding: 26px 18px;
            border-radius: 26px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            height: 100%;
        }

        .impact-card h3 {
            font-size: 41px;
            font-weight: 900;
            margin-bottom: 6px;
            color: var(--gold);
        }

        .impact-card p {
            color: rgba(255, 255, 255, 0.82);
            margin: 0;
            font-weight: 700;
        }

        .cta {
            background:
                radial-gradient(circle at 20% 20%, rgba(250,204,21,0.18), transparent 30%),
                linear-gradient(180deg, #fff7ed, #fffaf0);
        }

        .cta-box {
            background:
                radial-gradient(circle at top left, rgba(250, 204, 21, 0.28), transparent 32%),
                radial-gradient(circle at bottom right, rgba(159, 18, 57, 0.10), transparent 32%),
                linear-gradient(135deg, #ffffff, #fff7ed);
            border: 1px solid rgba(249, 115, 22, 0.22);
            border-radius: 38px;
            padding: 50px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .cta-box::before {
            content: "सेवा";
            position: absolute;
            right: 28px;
            top: 4px;
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size: 90px;
            font-weight: 800;
            color: rgba(159, 18, 57, 0.06);
        }

        .cta-box h2 {
            color: var(--maroon);
            position: relative;
            z-index: 2;
        }

        .cta-box p,
        .cta-box a {
            position: relative;
            z-index: 2;
        }

        .footer {
            background:
                radial-gradient(circle at 18% 18%, rgba(249, 115, 22, 0.15), transparent 28%),
                linear-gradient(135deg, #2b0b12, #42101b 55%, #062d1b);
            color: #ffeecb;
            padding: 58px 0 24px;
        }

        .footer h5 {
            color: #fff8dc;
            font-weight: 900;
            margin-bottom: 18px;
        }

        .footer p {
            color: rgba(255, 238, 203, 0.78);
        }

        .footer a {
            color: rgba(255, 238, 203, 0.82);
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .footer a:hover {
            color: var(--gold);
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.12);
            margin-top: 35px;
            padding-top: 22px;
            color: rgba(255, 238, 203, 0.72);
            font-size: 14px;
        }

        .floating-help {
            position: fixed;
            right: 22px;
            bottom: 22px;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background:
                linear-gradient(135deg, var(--saffron), var(--kumkum));
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 25px;
            box-shadow: 0 18px 44px rgba(159, 18, 57, 0.34);
            z-index: 30;
            transition: 0.25s ease;
        }

        .floating-help:hover {
            color: #fff;
            transform: translateY(-3px) scale(1.02);
        }

        @media (max-width: 991px) {
            .hero {
                padding: 74px 0 58px;
            }

            .hero-card {
                margin-top: 30px;
            }

            .section {
                padding: 66px 0;
            }

            .navbar-nav {
                padding-top: 14px;
            }

            .nav-link::after {
                display: none;
            }
        }

        @media (max-width: 575px) {
            .topbar {
                font-size: 12px;
            }

            .hero h1 {
                letter-spacing: -1.3px;
            }

            .cta-box,
            .guideline-box {
                padding: 27px;
            }

            .gau-panel {
                padding: 24px;
                min-height: auto;
            }

            .floating-help {
                right: 16px;
                bottom: 16px;
                width: 52px;
                height: 52px;
            }
        }
    </style>
</head>

<body>

<div class="topbar">
    <div class="container d-flex flex-wrap justify-content-between gap-2">
        <span class="deva"><i class="bi bi-brightness-high-fill"></i> ॐ श्री गौ मातायै नमः</span>
        <span><i class="bi bi-shield-check"></i> Gau Mata Protection, Rescue & Welfare Platform</span>
    </div>
</div>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('website.home') }}">
            <span class="brand-icon">
                <i class="bi bi-heart-pulse-fill"></i>
            </span>
            <span class="brand-title">
                <strong>Gau Mitra</strong>
                <small class="d-block">Gau Seva • Gau Raksha • Gau Kalyan</small>
            </span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#features">Seva Modules</a></li>
                <li class="nav-item"><a class="nav-link" href="#process">How It Works</a></li>
                <li class="nav-item"><a class="nav-link" href="#guidelines">Guidelines</a></li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-main" href="{{ route('admin.login') }}">
                        <i class="bi bi-person-lock me-1"></i> Admin Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="spiritual-badge">
                    <i class="bi bi-stars"></i>
                    Spiritual Cow Protection Mission
                </div>

                <h1>
                    Protecting <span>Gau Mata</span> with Seva, Technology & Responsibility
                </h1>

                <p>
                    Gau Mitra is a dedicated Gau Mata protection platform where citizens can report injured,
                    abandoned, accident-affected or illegally transported cattle with location, photo and video proof.
                    The system connects administration, volunteers and gaushalas for faster rescue and transparent action.
                </p>

                <div class="mantra-line">
                    <i class="bi bi-flower1"></i>
                    <span>गौ सेवा परम धर्मः</span>
                </div>

                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="#process" class="btn btn-main">
                        <i class="bi bi-phone-vibrate me-1"></i> Report Process
                    </a>
                    <a href="#guidelines" class="btn btn-light-main">
                        <i class="bi bi-journal-check me-1"></i> Safety Guidelines
                    </a>
                </div>

                <div class="row g-3 mt-4">
                    <div class="col-sm-4">
                        <div class="hero-stat">
                            <h3>24x7</h3>
                            <p>Emergency Reporting</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="hero-stat">
                            <h3>GPS</h3>
                            <p>Location Based Rescue</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="hero-stat">
                            <h3>Verified</h3>
                            <p>Admin Controlled Action</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="hero-card">
                    <div class="gau-panel">
                        <div class="gau-icon-large">
                            <i class="bi bi-shield-fill-check"></i>
                        </div>

                        <h3>Live Gau Raksha Workflow</h3>
                        <p>
                            Every case moves through a proper digital workflow: report, verify, assign, rescue,
                            update status and close with admin approval.
                        </p>

                        <div class="rescue-flow">
                            <div class="flow-row">
                                <strong><i class="bi bi-exclamation-octagon-fill text-danger"></i> Case Reported</strong>
                                <span>Citizen</span>
                            </div>
                            <div class="flow-row">
                                <strong><i class="bi bi-camera-fill text-warning"></i> Proof Uploaded</strong>
                                <span>Photo / Video</span>
                            </div>
                            <div class="flow-row">
                                <strong><i class="bi bi-geo-alt-fill text-success"></i> Location Captured</strong>
                                <span>GPS</span>
                            </div>
                            <div class="flow-row">
                                <strong><i class="bi bi-person-check-fill text-primary"></i> Team Assigned</strong>
                                <span>Volunteer</span>
                            </div>
                            <div class="flow-row">
                                <strong><i class="bi bi-check-circle-fill text-success"></i> Case Resolved</strong>
                                <span>Admin Verified</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="spiritual-strip">
    <div class="container">
        <div class="strip-content">
            धर्मो रक्षति रक्षितः  •  गौ सेवा परम धर्मः  •  सेवा ही संकल्प
        </div>
    </div>
</section>

<section class="section" id="about">
    <div class="container">
        <div class="section-title">
            <div class="mini"><i class="bi bi-flower1"></i> About Gau Mitra</div>
            <h2>A divine mission powered by a professional emergency response system.</h2>
            <p>
                Gau Mitra is built to stop delayed response, fake reporting and unsafe public action.
                It creates a controlled digital system where every Gau Mata related emergency has evidence,
                location, assigned responsibility, live status and final resolution.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-bullseye"></i></div>
                    <h4>Mission</h4>
                    <p>
                        To support timely rescue, treatment and safe shelter for cows through citizen participation and administrative monitoring.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-brightness-high"></i></div>
                    <h4>Vision</h4>
                    <p>
                        To build a transparent, spiritual and technology-driven Gau Mata welfare ecosystem for every region.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-people"></i></div>
                    <h4>Community Seva</h4>
                    <p>
                        Citizens, volunteers, gaushalas, veterinary teams, police and administration can coordinate through one platform.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section process" id="features">
    <div class="container">
        <div class="section-title">
            <div class="mini"><i class="bi bi-grid-3x3-gap-fill"></i> Seva Modules</div>
            <h2>Designed for real Gau Mata rescue, verification and monitoring.</h2>
            <p>
                The platform combines emergency reporting, case evidence, location intelligence, volunteer assignment
                and admin-level monitoring in one clean workflow.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-camera-video"></i></div>
                    <h4>Photo & Video Proof</h4>
                    <p>Users can submit real case evidence with images or videos for admin verification.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-geo-alt"></i></div>
                    <h4>GPS Location</h4>
                    <p>Every report can include exact address, landmark and GPS coordinates for fast rescue.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-bell"></i></div>
                    <h4>Smart Alerts</h4>
                    <p>Nearby volunteers and assigned users can receive notifications for quick response.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-clipboard2-check"></i></div>
                    <h4>Case Tracking</h4>
                    <p>Track every case from reported to assigned, rescued, resolved and closed.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-hospital"></i></div>
                    <h4>Veterinary Support</h4>
                    <p>Maintain proper treatment flow for injured or sick cattle with responsible teams.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-house-heart"></i></div>
                    <h4>Gaushala Network</h4>
                    <p>Connect gaushalas for shelter, care, rehabilitation and long-term Gau Seva support.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-shield-exclamation"></i></div>
                    <h4>Misuse Control</h4>
                    <p>False reports, duplicate cases and unsafe actions can be monitored by admin.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-bar-chart-line"></i></div>
                    <h4>Admin Dashboard</h4>
                    <p>Administration can review cases, volunteers, reports, status and rescue performance.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="process">
    <div class="container">
        <div class="section-title">
            <div class="mini"><i class="bi bi-signpost-split"></i> How It Works</div>
            <h2>Simple reporting, responsible action and verified case closure.</h2>
            <p>
                Gau Mitra guides the user from emergency reporting to safe rescue coordination without encouraging direct conflict or unsafe handling.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-no">1</div>
                    <h4 class="fw-bold">Report Case</h4>
                    <p class="text-muted mb-0">
                        Citizen reports injured, abandoned, accident, illegal transport or emergency case from the app.
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-no">2</div>
                    <h4 class="fw-bold">Add Proof</h4>
                    <p class="text-muted mb-0">
                        Upload clear photo/video, contact number, location, address and nearby landmark.
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-no">3</div>
                    <h4 class="fw-bold">Admin Assignment</h4>
                    <p class="text-muted mb-0">
                        Admin verifies the case and assigns it to volunteer, gaushala or responsible rescue team.
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-no">4</div>
                    <h4 class="fw-bold">Rescue & Close</h4>
                    <p class="text-muted mb-0">
                        Assigned person updates progress and admin closes the case after confirmation.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section guideline-area" id="guidelines">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="section-title text-start mb-0">
                    <div class="mini"><i class="bi bi-shield-lock"></i> Important Guidelines</div>
                    <h2>Use Gau Mitra with devotion and responsibility.</h2>
                    <p>
                        Gau Mitra is only for genuine Gau Mata welfare emergencies. Do not misuse the app,
                        create fake reports, fight at the location or take law into your own hands.
                    </p>

                    <div class="mantra-line mt-3">
                        <i class="bi bi-flower2"></i>
                        <span>सेवा, सुरक्षा और सत्य</span>
                    </div>

                    <a href="{{ route('admin.login') }}" class="btn btn-main mt-4">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Admin Portal
                    </a>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="guideline-box">
                    <div class="guideline-item">
                        <i class="bi bi-check2-circle"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Submit only genuine reports</h5>
                            <p class="text-muted mb-0">Do not create fake cases, duplicate cases or misleading emergency reports.</p>
                        </div>
                    </div>

                    <div class="guideline-item">
                        <i class="bi bi-camera"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Upload clear proof</h5>
                            <p class="text-muted mb-0">Add proper image/video, correct address and nearby landmark for faster rescue.</p>
                        </div>
                    </div>

                    <div class="guideline-item">
                        <i class="bi bi-shield-exclamation"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Do not take law in your own hands</h5>
                            <p class="text-muted mb-0">Do not fight, threaten or stop vehicles yourself. Report and wait for authorised response.</p>
                        </div>
                    </div>

                    <div class="guideline-item">
                        <i class="bi bi-geo-alt"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Share exact safe location</h5>
                            <p class="text-muted mb-0">Send accurate location but maintain personal safety until rescue team reaches.</p>
                        </div>
                    </div>

                    <div class="guideline-item">
                        <i class="bi bi-telephone"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Keep phone reachable</h5>
                            <p class="text-muted mb-0">The rescue team or admin may call you for verification and location confirmation.</p>
                        </div>
                    </div>

                    <div class="guideline-item">
                        <i class="bi bi-person-check"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Follow official instructions</h5>
                            <p class="text-muted mb-0">Cooperate with volunteers, police, veterinary staff and assigned authorities.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="section impact">
    <div class="container">
        <div class="section-title">
            <div class="mini" style="color:#fde68a; background:rgba(255,255,255,0.12); border-color:rgba(255,255,255,0.18);">
                <i class="bi bi-stars"></i> Gau Raksha Impact
            </div>
            <h2 class="text-white">A connected response system for every genuine Gau Mata case.</h2>
            <p style="color: rgba(255,255,255,0.80);">
                Gau Mitra brings spiritual service, field-level reporting and administrative monitoring into one platform.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="impact-card">
                    <h3>Fast</h3>
                    <p>Emergency Response</p>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="impact-card">
                    <h3>Safe</h3>
                    <p>Public Reporting</p>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="impact-card">
                    <h3>Clear</h3>
                    <p>Case Tracking</p>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="impact-card">
                    <h3>Seva</h3>
                    <p>Community Support</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section cta">
    <div class="container">
        <div class="cta-box">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-3">
                        Together, we can protect Gau Mata with devotion, discipline and technology.
                    </h2>
                    <p class="text-muted mb-0">
                        Gau Mitra is not only an app. It is a structured Gau Seva mission for rescue,
                        care, accountability and community participation.
                    </p>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <a href="#process" class="btn btn-main">
                        <i class="bi bi-arrow-right-circle me-1"></i> View Process
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand">
                    <span class="brand-icon">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </span>
                    <div>
                        <h5 class="mb-0 brand-font">Gau Mitra</h5>
                        <small class="deva">गौ सेवा परम धर्मः</small>
                    </div>
                </div>

                <p style="line-height:1.85;">
                    A spiritual and professional Gau Mata protection platform for emergency reporting,
                    rescue management, gaushala coordination and responsible public participation.
                </p>
            </div>

            <div class="col-lg-2 col-md-4">
                <h5>Quick Links</h5>
                <a href="#about">About</a>
                <a href="#features">Seva Modules</a>
                <a href="#process">How It Works</a>
                <a href="#guidelines">Guidelines</a>
            </div>

            <div class="col-lg-3 col-md-4">
                <h5>Platform Modules</h5>
                <a href="#">Emergency Cases</a>
                <a href="#">Volunteer Assignment</a>
                <a href="#">Gaushala Support</a>
                <a href="#">Admin Monitoring</a>
            </div>

            <div class="col-lg-3 col-md-4">
                <h5>Contact</h5>
                <a href="mailto:support@gaumitra.com"><i class="bi bi-envelope me-1"></i> support@gaumitra.com</a>
                <a href="#"><i class="bi bi-geo-alt me-1"></i> India</a>
                <a href="{{ route('admin.login') }}"><i class="bi bi-person-lock me-1"></i> Admin Login</a>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-wrap justify-content-between gap-2">
            <span>© {{ date('Y') }} Gau Mitra. All rights reserved.</span>
            <span class="deva">ॐ श्री गौ मातायै नमः</span>
        </div>
    </div>
</footer>

<a href="{{ route('admin.login') }}" class="floating-help" title="Admin Login">
    <i class="bi bi-shield-lock-fill"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>