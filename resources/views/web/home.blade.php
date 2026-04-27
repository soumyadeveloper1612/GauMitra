<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gau Mitra - Cow Protection & Emergency Rescue Platform</title>

    <meta name="description" content="Gau Mitra is a cow welfare emergency reporting and rescue management platform for citizens, volunteers, gaushalas and administration.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Devanagari:wght@600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #176b3a;
            --primary-dark: #0f4d29;
            --secondary: #f59e0b;
            --soft-green: #ecfdf5;
            --soft-orange: #fff7ed;
            --text: #17201b;
            --muted: #67736d;
            --white: #ffffff;
            --border: #e7eee9;
            --shadow: 0 24px 70px rgba(15, 77, 41, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: #fbfdfc;
            overflow-x: hidden;
        }

        .topbar {
            background: var(--primary-dark);
            color: #eafff2;
            font-size: 14px;
            padding: 9px 0;
        }

        .topbar span {
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            color: var(--primary-dark) !important;
            letter-spacing: -0.5px;
        }

        .brand-icon {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), #36b36b);
            color: var(--white);
            display: grid;
            place-items: center;
            font-size: 24px;
            box-shadow: 0 12px 30px rgba(23, 107, 58, 0.28);
        }

        .nav-link {
            font-weight: 600;
            color: #24352c !important;
            margin: 0 6px;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .btn-main {
            background: linear-gradient(135deg, var(--primary), #29a65a);
            border: none;
            color: #fff;
            border-radius: 999px;
            padding: 12px 22px;
            font-weight: 700;
            box-shadow: 0 15px 35px rgba(23, 107, 58, 0.25);
        }

        .btn-main:hover {
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-light-main {
            background: #fff;
            color: var(--primary-dark);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 12px 22px;
            font-weight: 700;
        }

        .hero {
            position: relative;
            padding: 95px 0 80px;
            background:
                radial-gradient(circle at 12% 18%, rgba(245, 158, 11, 0.18), transparent 28%),
                radial-gradient(circle at 90% 14%, rgba(23, 107, 58, 0.14), transparent 30%),
                linear-gradient(180deg, #f3fff8 0%, #ffffff 100%);
            overflow: hidden;
        }

        .hero::after {
            content: "";
            position: absolute;
            right: -120px;
            bottom: -120px;
            width: 360px;
            height: 360px;
            background: rgba(23, 107, 58, 0.08);
            border-radius: 50%;
        }

        .badge-soft {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: var(--primary);
            border: 1px solid #cdeedb;
            padding: 9px 14px;
            border-radius: 999px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .hero h1 {
            font-size: clamp(42px, 6vw, 76px);
            line-height: 1.02;
            font-weight: 800;
            letter-spacing: -2.2px;
            color: #102417;
            margin-bottom: 20px;
        }

        .hero h1 span {
            color: var(--primary);
        }

        .hero p {
            color: var(--muted);
            font-size: 18px;
            line-height: 1.8;
            max-width: 640px;
        }

        .hero-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 34px;
            box-shadow: var(--shadow);
            padding: 26px;
            position: relative;
            z-index: 2;
        }

        .rescue-box {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: #fff;
            border-radius: 28px;
            padding: 26px;
            min-height: 360px;
            position: relative;
            overflow: hidden;
        }

        .rescue-box::before {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            right: -70px;
            top: -70px;
            background: rgba(255, 255, 255, 0.13);
            border-radius: 50%;
        }

        .rescue-icon {
            width: 76px;
            height: 76px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.16);
            display: grid;
            place-items: center;
            font-size: 38px;
            margin-bottom: 22px;
        }

        .status-card {
            background: #fff;
            border-radius: 22px;
            padding: 18px;
            margin-top: 18px;
            color: #17201b;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.10);
        }

        .status-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px dashed #e4e8e5;
            font-size: 14px;
        }

        .status-row:last-child {
            border-bottom: none;
        }

        .section {
            padding: 85px 0;
        }

        .section-title {
            max-width: 760px;
            margin: 0 auto 46px;
            text-align: center;
        }

        .section-title .mini {
            color: var(--secondary);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .section-title h2 {
            font-size: clamp(32px, 4vw, 48px);
            font-weight: 800;
            letter-spacing: -1.3px;
            color: #122319;
        }

        .section-title p {
            color: var(--muted);
            font-size: 17px;
            line-height: 1.8;
            margin-top: 12px;
        }

        .feature-card {
            height: 100%;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 30px;
            box-shadow: 0 18px 45px rgba(15, 77, 41, 0.06);
            transition: 0.25s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow);
        }

        .feature-icon {
            width: 62px;
            height: 62px;
            border-radius: 20px;
            background: var(--soft-green);
            color: var(--primary);
            display: grid;
            place-items: center;
            font-size: 30px;
            margin-bottom: 22px;
        }

        .feature-card h4 {
            font-weight: 800;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: var(--muted);
            line-height: 1.7;
            margin-bottom: 0;
        }

        .process {
            background: linear-gradient(180deg, #ffffff, #f2fff7);
        }

        .step-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 26px;
            padding: 26px;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .step-no {
            width: 46px;
            height: 46px;
            background: var(--primary);
            color: #fff;
            border-radius: 15px;
            display: grid;
            place-items: center;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .guideline-box {
            background: #fff;
            border-radius: 34px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            padding: 36px;
        }

        .guideline-item {
            display: flex;
            gap: 15px;
            padding: 16px 0;
            border-bottom: 1px solid #edf2ef;
        }

        .guideline-item:last-child {
            border-bottom: none;
        }

        .guideline-item i {
            width: 38px;
            height: 38px;
            min-width: 38px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            background: var(--soft-orange);
            color: var(--secondary);
            font-size: 20px;
        }

        .impact {
            background:
                linear-gradient(rgba(15, 77, 41, 0.94), rgba(15, 77, 41, 0.94)),
                radial-gradient(circle at center, #29a65a, #0f4d29);
            color: #fff;
        }

        .impact-card {
            text-align: center;
            padding: 24px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .impact-card h3 {
            font-size: 42px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .impact-card p {
            color: rgba(255, 255, 255, 0.78);
            margin: 0;
        }

        .cta {
            background: #fff7ed;
        }

        .cta-box {
            background:
                radial-gradient(circle at top left, rgba(245, 158, 11, 0.25), transparent 32%),
                linear-gradient(135deg, #ffffff, #f3fff8);
            border: 1px solid var(--border);
            border-radius: 36px;
            padding: 48px;
            box-shadow: var(--shadow);
        }

        .footer {
            background: #0b2114;
            color: #d8efe2;
            padding: 55px 0 24px;
        }

        .footer h5 {
            color: #fff;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .footer a {
            color: #d8efe2;
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
        }

        .footer a:hover {
            color: #fff;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 35px;
            padding-top: 22px;
            color: #a9c7b7;
            font-size: 14px;
        }

        @media (max-width: 991px) {
            .hero {
                padding: 70px 0 55px;
            }

            .hero-card {
                margin-top: 35px;
            }

            .section {
                padding: 65px 0;
            }
        }

        @media (max-width: 575px) {
            .hero h1 {
                letter-spacing: -1.3px;
            }

            .cta-box,
            .guideline-box {
                padding: 26px;
            }
        }
    </style>
</head>

<body>

<div class="topbar">
    <div class="container d-flex flex-wrap justify-content-between gap-2">
        <span><i class="bi bi-shield-check"></i> धर्मो रक्षति रक्षितः</span>
        <span><i class="bi bi-geo-alt"></i> Cow Rescue, Case Tracking & Public Safety Platform</span>
    </div>
</div>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('website.home') }}">
            <span class="brand-icon"><i class="bi bi-heart-pulse"></i></span>
            <span>
                Gau Mitra
                <small class="d-block fw-semibold text-muted" style="font-size: 12px;">Cow Welfare Mission</small>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
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
                <div class="badge-soft">
                    <i class="bi bi-lightning-charge-fill"></i>
                    Emergency Cow Rescue & Welfare Management
                </div>

                <h1>
                    Protecting Gau Mata with <span>Technology, Trust & Teamwork</span>
                </h1>

                <p>
                    Gau Mitra helps citizens report injured, abandoned, illegally transported or distressed cattle with
                    location, photo and video proof. The platform connects volunteers, gaushalas and administrators for
                    faster rescue, transparent case tracking and safer action.
                </p>

                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="#process" class="btn btn-main">
                        <i class="bi bi-phone me-1"></i> How to Report
                    </a>
                    <a href="#guidelines" class="btn btn-light-main">
                        <i class="bi bi-journal-check me-1"></i> App Guidelines
                    </a>
                </div>

                <div class="row g-3 mt-4">
                    <div class="col-sm-4">
                        <div class="impact-card bg-white text-dark border">
                            <h3 style="font-size: 28px; color: var(--primary);">24x7</h3>
                            <p>Emergency Reporting</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="impact-card bg-white text-dark border">
                            <h3 style="font-size: 28px; color: var(--primary);">GPS</h3>
                            <p>Location Based Cases</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="impact-card bg-white text-dark border">
                            <h3 style="font-size: 28px; color: var(--primary);">Safe</h3>
                            <p>Verified Response</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="hero-card">
                    <div class="rescue-box">
                        <div class="rescue-icon">
                            <i class="bi bi-shield-plus"></i>
                        </div>

                        <h3 class="fw-bold mb-3">Live Rescue Workflow</h3>
                        <p class="mb-0" style="color: rgba(255,255,255,0.82);">
                            Report, verify, assign, rescue and close every case with proper responsibility and digital record.
                        </p>

                        <div class="status-card">
                            <div class="status-row">
                                <strong><i class="bi bi-exclamation-circle text-danger me-1"></i> Case Reported</strong>
                                <span>Citizen</span>
                            </div>
                            <div class="status-row">
                                <strong><i class="bi bi-geo-alt text-success me-1"></i> Location Captured</strong>
                                <span>GPS</span>
                            </div>
                            <div class="status-row">
                                <strong><i class="bi bi-person-check text-primary me-1"></i> Assigned</strong>
                                <span>Volunteer</span>
                            </div>
                            <div class="status-row">
                                <strong><i class="bi bi-check-circle text-success me-1"></i> Resolved</strong>
                                <span>Admin Verified</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="section" id="about">
    <div class="container">
        <div class="section-title">
            <div class="mini">About Gau Mitra</div>
            <h2>A digital platform for cow protection, rescue coordination and responsible reporting.</h2>
            <p>
                Gau Mitra is designed to prevent delayed response, false reporting and unsafe public action.
                It creates a controlled system where every case has evidence, location, assignment, status and final resolution.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-bullseye"></i></div>
                    <h4>Mission</h4>
                    <p>To support timely rescue and care for cows through citizen participation and administrative monitoring.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-eye"></i></div>
                    <h4>Vision</h4>
                    <p>To build a transparent, accountable and technology-driven cow welfare ecosystem.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-people"></i></div>
                    <h4>Community</h4>
                    <p>Citizens, volunteers, gaushalas, police, veterinary teams and administration can work together.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section process" id="features">
    <div class="container">
        <div class="section-title">
            <div class="mini">Key Features</div>
            <h2>Built for real emergency response and public safety.</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-camera-video"></i></div>
                    <h4>Photo & Video Proof</h4>
                    <p>Users can submit case evidence with images or videos for verification.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-geo-alt"></i></div>
                    <h4>Location Tracking</h4>
                    <p>Cases include address and GPS location for accurate rescue response.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-bell"></i></div>
                    <h4>Notifications</h4>
                    <p>Nearby volunteers and assigned users can receive alerts for action.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-clipboard-check"></i></div>
                    <h4>Case Status</h4>
                    <p>Every case can be tracked from reported to rescued, resolved or closed.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="process">
    <div class="container">
        <div class="section-title">
            <div class="mini">How It Works</div>
            <h2>Simple reporting. Controlled action. Verified resolution.</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-no">1</div>
                    <h4 class="fw-bold">Report Case</h4>
                    <p class="text-muted mb-0">Citizen reports injured, abandoned, accident or illegal transport case from the app.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-no">2</div>
                    <h4 class="fw-bold">Add Evidence</h4>
                    <p class="text-muted mb-0">Upload clear photo/video, location and correct contact details.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-no">3</div>
                    <h4 class="fw-bold">Admin Assignment</h4>
                    <p class="text-muted mb-0">Admin verifies and assigns the case to volunteer, gaushala or responsible team.</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-no">4</div>
                    <h4 class="fw-bold">Rescue & Close</h4>
                    <p class="text-muted mb-0">Assigned person updates status and admin closes the case after verification.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="guidelines">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="section-title text-start mb-0">
                    <div class="mini">Important Guidelines</div>
                    <h2>Use Gau Mitra responsibly.</h2>
                    <p>
                        This platform is for genuine cow welfare emergencies. Misuse, fake reporting or fighting at the spot can delay real rescue operations and create legal issues.
                    </p>
                    <a href="{{ route('admin.login') }}" class="btn btn-main mt-3">
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
                            <h5 class="fw-bold mb-1">Stay near safe location</h5>
                            <p class="text-muted mb-0">Share exact location but maintain personal safety until rescue team reaches.</p>
                        </div>
                    </div>

                    <div class="guideline-item">
                        <i class="bi bi-telephone"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Keep phone reachable</h5>
                            <p class="text-muted mb-0">The rescue team or admin may call you for verification and location confirmation.</p>
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
            <div class="mini" style="color:#fbbf24;">Platform Impact</div>
            <h2 class="text-white">A connected response system for every genuine case.</h2>
            <p style="color: rgba(255,255,255,0.78);">
                Gau Mitra brings field-level reporting and administrative monitoring into one platform.
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
                    <h3>Live</h3>
                    <p>Status Updates</p>
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
                    <h2 class="fw-bold mb-3">Together, we can protect cattle with compassion and responsibility.</h2>
                    <p class="text-muted mb-0">
                        Gau Mitra is not only an app. It is a structured mission for rescue, care, accountability and community participation.
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
                <h5>Gau Mitra</h5>
                <p style="color:#b8d4c5; line-height:1.8;">
                    A professional cow welfare emergency reporting and rescue management platform for citizens, volunteers, gaushalas and administration.
                </p>
            </div>

            <div class="col-lg-2 col-md-4">
                <h5>Quick Links</h5>
                <a href="#about">About</a>
                <a href="#features">Features</a>
                <a href="#process">How It Works</a>
                <a href="#guidelines">Guidelines</a>
            </div>

            <div class="col-lg-3 col-md-4">
                <h5>Modules</h5>
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
            <span>Designed for Cow Welfare, Rescue & Responsible Reporting.</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>