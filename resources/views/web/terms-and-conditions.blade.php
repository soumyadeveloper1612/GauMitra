<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gau Mitra - Terms & Conditions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary: #15803d;
            --primary-dark: #166534;
            --soft-green: #ecfdf5;
            --warning-bg: #fff7ed;
            --danger-bg: #fef2f2;
            --info-bg: #eff6ff;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 45%, #ecfeff 100%);
            color: var(--text-dark);
        }

        .terms-wrapper {
            min-height: 100vh;
            padding: 24px 12px;
        }

        .terms-container {
            max-width: 980px;
            margin: 0 auto;
        }

        .hero-card {
            background: linear-gradient(135deg, #15803d, #22c55e);
            border-radius: 28px;
            padding: 34px 28px;
            color: #ffffff;
            box-shadow: 0 20px 45px rgba(21, 128, 61, 0.25);
            position: relative;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .hero-card::after {
            content: "";
            position: absolute;
            right: -80px;
            top: -80px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.16);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.25);
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 14px;
            margin-bottom: 14px;
        }

        .hero-title {
            font-size: clamp(28px, 5vw, 44px);
            font-weight: 800;
            margin-bottom: 10px;
            line-height: 1.12;
        }

        .hero-text {
            font-size: 16px;
            max-width: 780px;
            opacity: 0.95;
            margin-bottom: 0;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 18px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 21px;
            font-weight: 800;
            margin-bottom: 16px;
            color: var(--primary-dark);
        }

        .section-title i {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: var(--soft-green);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .guideline-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .guideline-list li {
            display: flex;
            gap: 12px;
            padding: 13px 0;
            border-bottom: 1px dashed var(--border);
            line-height: 1.55;
        }

        .guideline-list li:last-child {
            border-bottom: none;
        }

        .list-icon {
            min-width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            margin-top: 1px;
        }

        .icon-green {
            background: #dcfce7;
            color: #15803d;
        }

        .icon-red {
            background: #fee2e2;
            color: #dc2626;
        }

        .icon-orange {
            background: #ffedd5;
            color: #ea580c;
        }

        .alert-box {
            border-radius: 20px;
            padding: 18px;
            margin-top: 12px;
            border: 1px solid;
        }

        .alert-danger-custom {
            background: var(--danger-bg);
            border-color: #fecaca;
            color: #7f1d1d;
        }

        .alert-warning-custom {
            background: var(--warning-bg);
            border-color: #fed7aa;
            color: #7c2d12;
        }

        .alert-info-custom {
            background: var(--info-bg);
            border-color: #bfdbfe;
            color: #1e3a8a;
        }

        .step-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 14px;
        }

        .step-box {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 18px;
            height: 100%;
        }

        .step-number {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            background: var(--primary);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .step-box h6 {
            font-weight: 800;
            margin-bottom: 8px;
        }

        .step-box p {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.55;
        }

        .footer-note {
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
            padding: 20px 0 8px;
        }

        .last-updated {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border: 1px solid var(--border);
            color: var(--text-muted);
            padding: 9px 14px;
            border-radius: 999px;
            font-size: 13px;
            margin-top: 18px;
        }

        @media (max-width: 576px) {
            .terms-wrapper {
                padding: 14px 10px;
            }

            .hero-card {
                padding: 28px 20px;
                border-radius: 22px;
            }

            .section-card {
                padding: 20px 16px;
                border-radius: 20px;
            }

            .section-title {
                font-size: 19px;
            }
        }
    </style>
</head>
<body>

<div class="terms-wrapper">
    <div class="terms-container">

        <div class="hero-card">
            <div class="hero-badge">
                <i class="bi bi-shield-check"></i>
                Official User Safety Guidelines
            </div>

            <h1 class="hero-title">Gau Mitra App Terms & Conditions</h1>

            <p class="hero-text">
                Gau Mitra is created to help report genuine cow rescue, injured cattle, abandoned cattle,
                illegal transport, and emergency welfare cases. Users must use this application responsibly,
                safely, and only for genuine reports.
            </p>

            <div class="last-updated">
                <i class="bi bi-calendar2-check"></i>
                Last Updated: {{ now()->format('d M Y') }}
            </div>
        </div>

        <div class="section-card">
            <h2 class="section-title">
                <i class="bi bi-phone"></i>
                Purpose of Gau Mitra App
            </h2>

            <p>
                Gau Mitra App is a public support and reporting platform for cow welfare and emergency rescue coordination.
                The app helps users submit reports with location, details, images, videos, and contact information so that
                the concerned rescue team, admin, volunteer, or authority can review and act on the case.
            </p>

            <div class="alert-box alert-info-custom">
                <strong>Important:</strong> Submitting a report does not mean the user should personally handle the case.
                The case must be reviewed and assigned to the authorized rescue team or responsible department.
            </div>
        </div>

        <div class="section-card">
            <h2 class="section-title">
                <i class="bi bi-list-check"></i>
                Step-by-Step App Usage Guidelines
            </h2>

            <div class="step-grid">
                <div class="step-box">
                    <div class="step-number">1</div>
                    <h6>Report Genuine Case</h6>
                    <p>Use the app only for real cow emergency, injury, rescue, illegal transport, or abandoned cattle cases.</p>
                </div>

                <div class="step-box">
                    <div class="step-number">2</div>
                    <h6>Share Correct Location</h6>
                    <p>Provide accurate location, landmark, village, police station, district, and contact details where possible.</p>
                </div>

                <div class="step-box">
                    <div class="step-number">3</div>
                    <h6>Upload Clear Evidence</h6>
                    <p>Upload clear photos or videos only if it is safe. Do not risk your safety to capture media.</p>
                </div>

                <div class="step-box">
                    <div class="step-number">4</div>
                    <h6>Wait for Assignment</h6>
                    <p>After submitting, wait for the case to be verified and assigned to the rescue team or authority.</p>
                </div>

                <div class="step-box">
                    <div class="step-number">5</div>
                    <h6>Do Not Fight</h6>
                    <p>Do not argue, fight, block vehicles, threaten anyone, or create a public disturbance.</p>
                </div>

                <div class="step-box">
                    <div class="step-number">6</div>
                    <h6>Do Not Take Case in Hand</h6>
                    <p>Do not personally seize animals, stop vehicles, or handle legal matters. Inform the proper authority.</p>
                </div>
            </div>
        </div>

        <div class="section-card">
            <h2 class="section-title">
                <i class="bi bi-check-circle"></i>
                User Responsibilities
            </h2>

            <ul class="guideline-list">
                <li>
                    <span class="list-icon icon-green"><i class="bi bi-check-lg"></i></span>
                    <span>Submit only truthful and genuine reports related to cow welfare or emergency rescue.</span>
                </li>

                <li>
                    <span class="list-icon icon-green"><i class="bi bi-check-lg"></i></span>
                    <span>Enter correct mobile number, location, address, landmark, and case details.</span>
                </li>

                <li>
                    <span class="list-icon icon-green"><i class="bi bi-check-lg"></i></span>
                    <span>Cooperate with rescue teams, Gau Mitra volunteers, police, veterinary staff, and authorized officials.</span>
                </li>

                <li>
                    <span class="list-icon icon-green"><i class="bi bi-check-lg"></i></span>
                    <span>Maintain peace, safety, and respect while reporting or assisting in any case.</span>
                </li>

                <li>
                    <span class="list-icon icon-green"><i class="bi bi-check-lg"></i></span>
                    <span>Use the app only for lawful, social welfare, and animal protection purposes.</span>
                </li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="section-title">
                <i class="bi bi-x-octagon"></i>
                Strictly Prohibited Activities
            </h2>

            <ul class="guideline-list">
                <li>
                    <span class="list-icon icon-red"><i class="bi bi-x-lg"></i></span>
                    <span>Do not submit fake, false, misleading, duplicate, or prank reports.</span>
                </li>

                <li>
                    <span class="list-icon icon-red"><i class="bi bi-x-lg"></i></span>
                    <span>Do not misuse the application to harass any person, vehicle owner, farmer, trader, organization, or official.</span>
                </li>

                <li>
                    <span class="list-icon icon-red"><i class="bi bi-x-lg"></i></span>
                    <span>Do not fight, argue, threaten, abuse, or physically confront anyone at the case location.</span>
                </li>

                <li>
                    <span class="list-icon icon-red"><i class="bi bi-x-lg"></i></span>
                    <span>Do not block roads, stop vehicles, create crowd pressure, or take law and order matters into your own hands.</span>
                </li>

                <li>
                    <span class="list-icon icon-red"><i class="bi bi-x-lg"></i></span>
                    <span>Do not rescue, transport, seize, or shift cattle without permission from authorized rescue teams or officials.</span>
                </li>

                <li>
                    <span class="list-icon icon-red"><i class="bi bi-x-lg"></i></span>
                    <span>Do not upload unrelated, edited, violent, abusive, or misleading photos/videos.</span>
                </li>
            </ul>

            <div class="alert-box alert-danger-custom">
                <strong>Warning:</strong> If any user is found submitting fake reports or misusing the app,
                Gau Mitra may suspend the user account, block access, reject reports, and share details with
                the concerned authority if required.
            </div>
        </div>

        <div class="section-card">
            <h2 class="section-title">
                <i class="bi bi-geo-alt"></i>
                Location and Case Reporting Policy
            </h2>

            <p>
                Gau Mitra may collect and use your location, address, landmark, uploaded images/videos, device details,
                and report information only for case verification, assignment, rescue coordination, safety, and record management.
            </p>

            <ul class="guideline-list">
                <li>
                    <span class="list-icon icon-orange"><i class="bi bi-geo"></i></span>
                    <span>Users must provide the correct location of the case.</span>
                </li>

                <li>
                    <span class="list-icon icon-orange"><i class="bi bi-map"></i></span>
                    <span>Wrong location can delay rescue and may cause the report to be rejected.</span>
                </li>

                <li>
                    <span class="list-icon icon-orange"><i class="bi bi-shield-lock"></i></span>
                    <span>Location and report data may be shared with admin, rescue team, volunteers, police, veterinary staff, or authorized officials only for case handling.</span>
                </li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="section-title">
                <i class="bi bi-person-raised-hand"></i>
                Safety Guidelines for Users
            </h2>

            <ul class="guideline-list">
                <li>
                    <span class="list-icon icon-green"><i class="bi bi-shield-check"></i></span>
                    <span>Stay at a safe distance from injured animals, unknown people, vehicles, and crowded situations.</span>
                </li>

                <li>
                    <span class="list-icon icon-green"><i class="bi bi-telephone"></i></span>
                    <span>In serious or risky situations, immediately contact local police, veterinary service, or emergency authority.</span>
                </li>

                <li>
                    <span class="list-icon icon-green"><i class="bi bi-people"></i></span>
                    <span>Do not act alone. Wait for authorized rescue team, Gau Mitra team, or local administration.</span>
                </li>

                <li>
                    <span class="list-icon icon-green"><i class="bi bi-camera-video"></i></span>
                    <span>Take photos/videos only if it is safe and lawful. Do not provoke or disturb anyone.</span>
                </li>
            </ul>

            <div class="alert-box alert-warning-custom">
                <strong>Safety First:</strong> Gau Mitra does not encourage users to personally fight, chase, stop,
                seize, or handle any person, vehicle, animal, or legal situation.
            </div>
        </div>

        <div class="section-card">
            <h2 class="section-title">
                <i class="bi bi-clipboard-check"></i>
                Case Verification and Assignment
            </h2>

            <p>
                Every report submitted through the app may be reviewed by the Gau Mitra admin team or authorized users.
                The case may be accepted, rejected, marked duplicate, assigned to a handler, escalated, or closed based
                on the available information and ground verification.
            </p>

            <ul class="guideline-list">
                <li>
                    <span class="list-icon icon-green"><i class="bi bi-check2-square"></i></span>
                    <span>Valid reports may be assigned to rescue teams or responsible handlers.</span>
                </li>

                <li>
                    <span class="list-icon icon-orange"><i class="bi bi-arrow-repeat"></i></span>
                    <span>Duplicate reports may be merged or closed by the admin team.</span>
                </li>

                <li>
                    <span class="list-icon icon-red"><i class="bi bi-x-circle"></i></span>
                    <span>Fake, incomplete, abusive, or misleading reports may be rejected.</span>
                </li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="section-title">
                <i class="bi bi-ban"></i>
                Account Suspension and Legal Action
            </h2>

            <p>
                Gau Mitra reserves the right to restrict, suspend, or permanently block users who misuse the application.
                Misuse includes fake reporting, harassment, repeated duplicate reports, abusive behavior, false evidence,
                or any activity that creates law and order problems.
            </p>

            <div class="alert-box alert-danger-custom">
                <strong>Fake Report Policy:</strong> Submitting false reports may waste emergency resources and delay genuine rescue cases.
                Such activity may lead to account blocking and action as per applicable rules or law.
            </div>
        </div>

        <div class="section-card">
            <h2 class="section-title">
                <i class="bi bi-file-earmark-text"></i>
                Acceptance of Terms
            </h2>

            <p>
                By using Gau Mitra App, you confirm that you have read, understood, and accepted these Terms & Conditions.
                You agree to use the app responsibly, provide correct information, avoid misuse, and follow instructions
                from authorized rescue teams and officials.
            </p>
        </div>

        <div class="footer-note">
            © {{ date('Y') }} Gau Mitra. All Rights Reserved.
        </div>

    </div>
</div>

</body>
</html>