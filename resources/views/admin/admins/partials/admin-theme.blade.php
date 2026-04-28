<style>
    :root {
        --gm-saffron: #f57c00;
        --gm-deep-saffron: #d35400;
        --gm-brown: #5d2f12;
        --gm-dark-brown: #3b1d0c;
        --gm-cream: #fff8ec;
        --gm-light: #fff3db;
        --gm-soft: #fff7ea;
        --gm-gold: #ffc107;
        --gm-green: #2e7d32;
        --gm-red: #c0392b;
        --gm-blue: #1d4ed8;
        --gm-border: rgba(93, 47, 18, 0.14);
        --gm-shadow: 0 18px 45px rgba(93, 47, 18, 0.12);
    }

    body {
        background:
            radial-gradient(circle at top left, rgba(245, 124, 0, 0.12), transparent 32%),
            linear-gradient(135deg, #fffaf0 0%, #fff3db 45%, #fff8ec 100%) !important;
    }

    .gm-page-hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: 26px;
        background:
            linear-gradient(135deg, rgba(93, 47, 18, 0.96), rgba(211, 84, 0, 0.94)),
            url("data:image/svg+xml,%3Csvg width='160' height='160' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='%23ffffff' stroke-opacity='.12' stroke-width='2'%3E%3Cpath d='M80 10 L95 60 L148 60 L105 91 L122 142 L80 110 L38 142 L55 91 L12 60 L65 60 Z'/%3E%3Ccircle cx='80' cy='80' r='54'/%3E%3C/g%3E%3C/svg%3E");
        color: #fff;
        box-shadow: var(--gm-shadow);
        margin-bottom: 24px;
    }

    .gm-page-hero::after {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        right: -70px;
        top: -70px;
        border-radius: 50%;
        background: rgba(255, 193, 7, 0.18);
    }

    .gm-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.16);
        font-weight: 800;
        font-size: 13px;
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
    }

    .gm-hero-title {
        margin: 0;
        font-size: 28px;
        font-weight: 900;
        letter-spacing: -0.4px;
        position: relative;
        z-index: 1;
    }

    .gm-hero-subtitle {
        margin: 8px 0 0;
        color: rgba(255, 255, 255, 0.86);
        max-width: 720px;
        font-weight: 500;
        position: relative;
        z-index: 1;
    }

    .gm-card {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid var(--gm-border);
        border-radius: 26px;
        box-shadow: var(--gm-shadow);
        padding: 26px;
        backdrop-filter: blur(10px);
    }

    .gm-card-title {
        color: var(--gm-brown);
        font-weight: 900;
        margin-bottom: 4px;
    }

    .gm-card-subtitle {
        color: rgba(93, 47, 18, 0.68);
        margin-bottom: 0;
        font-weight: 600;
    }

    .gm-form-label {
        color: var(--gm-brown);
        font-weight: 800;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .gm-form-control,
    .gm-form-select {
        min-height: 48px;
        border-radius: 16px;
        border: 1px solid rgba(93, 47, 18, 0.18);
        background: #fffdfa;
        color: var(--gm-dark-brown);
        font-weight: 600;
    }

    textarea.gm-form-control {
        min-height: auto;
    }

    .gm-form-control:focus,
    .gm-form-select:focus {
        border-color: var(--gm-saffron);
        box-shadow: 0 0 0 0.22rem rgba(245, 124, 0, 0.16);
        background: #fffdfa;
        color: var(--gm-dark-brown);
    }

    .gm-input-icon {
        width: 44px;
        height: 44px;
        border-radius: 15px;
        background: linear-gradient(135deg, #fff3db, #ffe0ad);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--gm-deep-saffron);
        font-size: 20px;
        flex: 0 0 auto;
        border: 1px solid rgba(245, 124, 0, 0.16);
    }

    .gm-role-card {
        height: 100%;
        border: 1px solid rgba(93, 47, 18, 0.14);
        border-radius: 22px;
        padding: 18px;
        background: linear-gradient(180deg, #fffdfa, #fff5e5);
        transition: all 0.22s ease;
    }

    .gm-role-card:hover {
        transform: translateY(-3px);
        border-color: rgba(245, 124, 0, 0.45);
        box-shadow: 0 14px 30px rgba(93, 47, 18, 0.12);
    }

    .gm-role-card .form-check-input {
        border-color: rgba(93, 47, 18, 0.45);
        cursor: pointer;
    }

    .gm-role-card .form-check-input:checked {
        background-color: var(--gm-saffron);
        border-color: var(--gm-saffron);
    }

    .gm-switch .form-check-input {
        width: 52px;
        height: 28px;
        border-color: rgba(93, 47, 18, 0.28);
        cursor: pointer;
    }

    .gm-switch .form-check-input:checked {
        background-color: var(--gm-saffron);
        border-color: var(--gm-saffron);
    }

    .gm-btn-primary {
        background: linear-gradient(135deg, var(--gm-saffron), var(--gm-deep-saffron));
        color: #fff !important;
        border: 0;
        border-radius: 999px;
        padding: 11px 24px;
        font-weight: 900;
        box-shadow: 0 12px 24px rgba(211, 84, 0, 0.24);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .gm-btn-primary:hover {
        color: #fff !important;
        transform: translateY(-1px);
        box-shadow: 0 16px 28px rgba(211, 84, 0, 0.32);
    }

    .gm-btn-light {
        background: #fff7ea;
        color: var(--gm-brown) !important;
        border: 1px solid rgba(93, 47, 18, 0.16);
        border-radius: 999px;
        padding: 11px 24px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .gm-btn-light:hover {
        background: #ffe8bf;
        color: var(--gm-dark-brown) !important;
    }

    .gm-stat-card {
        position: relative;
        overflow: hidden;
        border-radius: 26px;
        padding: 24px;
        color: #fff;
        min-height: 138px;
        box-shadow: var(--gm-shadow);
    }

    .gm-stat-card::after {
        content: "";
        position: absolute;
        width: 140px;
        height: 140px;
        right: -45px;
        bottom: -45px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
    }

    .gm-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 17px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.18);
        margin-bottom: 14px;
        font-size: 23px;
        position: relative;
        z-index: 1;
    }

    .gm-stat-card h6 {
        margin: 0;
        color: rgba(255, 255, 255, 0.82);
        font-weight: 800;
        position: relative;
        z-index: 1;
    }

    .gm-stat-card h2 {
        margin: 4px 0 0;
        font-weight: 900;
        font-size: 34px;
        position: relative;
        z-index: 1;
    }

    .gm-gradient-1 {
        background: linear-gradient(135deg, #5d2f12, #b45309);
    }

    .gm-gradient-2 {
        background: linear-gradient(135deg, #2e7d32, #8d6e00);
    }

    .gm-gradient-3 {
        background: linear-gradient(135deg, #7b2d12, #f57c00);
    }

    .gm-gradient-4 {
        background: linear-gradient(135deg, #7b1f12, #c0392b);
    }

    .gm-table {
        border-collapse: separate;
        border-spacing: 0 12px;
    }

    .gm-table thead th {
        color: var(--gm-brown);
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 0;
        padding: 0 16px 8px;
        white-space: nowrap;
        font-weight: 900;
    }

    .gm-table tbody tr {
        background: #fffdfa;
        box-shadow: 0 10px 24px rgba(93, 47, 18, 0.08);
    }

    .gm-table tbody td {
        border-top: 1px solid rgba(93, 47, 18, 0.08);
        border-bottom: 1px solid rgba(93, 47, 18, 0.08);
        padding: 16px;
        vertical-align: middle;
    }

    .gm-table tbody td:first-child {
        border-left: 1px solid rgba(93, 47, 18, 0.08);
        border-radius: 18px 0 0 18px;
    }

    .gm-table tbody td:last-child {
        border-right: 1px solid rgba(93, 47, 18, 0.08);
        border-radius: 0 18px 18px 0;
    }

    .gm-avatar {
        width: 42px;
        height: 42px;
        border-radius: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ffe0ad, #f57c00);
        color: #fff;
        font-weight: 900;
        margin-right: 10px;
        flex: 0 0 auto;
        box-shadow: 0 10px 22px rgba(211, 84, 0, 0.22);
    }

    .gm-badge-role {
        background: #fff0d3;
        color: var(--gm-brown);
        border: 1px solid rgba(245, 124, 0, 0.22);
        border-radius: 999px;
        padding: 6px 10px;
        font-weight: 800;
        display: inline-block;
        margin: 2px;
        font-size: 12px;
    }

    .gm-badge-success {
        background: rgba(46, 125, 50, 0.12);
        color: var(--gm-green);
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 900;
        display: inline-block;
    }

    .gm-badge-danger {
        background: rgba(192, 57, 43, 0.12);
        color: var(--gm-red);
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 900;
        display: inline-block;
    }

    .gm-badge-warning {
        background: rgba(245, 124, 0, 0.15);
        color: var(--gm-deep-saffron);
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 900;
        display: inline-block;
    }

    .gm-badge-dark {
        background: rgba(59, 29, 12, 0.12);
        color: var(--gm-dark-brown);
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 900;
        display: inline-block;
    }

    .gm-empty {
        background: #fff7ea;
        border: 1px dashed rgba(93, 47, 18, 0.25);
        border-radius: 22px;
        padding: 32px;
        color: rgba(93, 47, 18, 0.7);
        font-weight: 700;
    }

    .gm-alert {
        border-radius: 18px;
        border: 1px solid rgba(192, 57, 43, 0.16);
        background: rgba(192, 57, 43, 0.08);
        color: #9f2d22;
        font-weight: 700;
    }

    .gm-action-btn {
        border-radius: 999px;
        font-weight: 800;
        padding: 7px 13px;
    }

    .gm-section-box {
        border: 1px solid rgba(93, 47, 18, 0.1);
        background: linear-gradient(180deg, #fffdfa, #fff7ea);
        border-radius: 24px;
        padding: 22px;
    }

    .gm-small-help {
        color: rgba(93, 47, 18, 0.62);
        font-size: 12px;
        font-weight: 600;
    }

    .alert-success {
        background: rgba(46, 125, 50, 0.12);
        border-color: rgba(46, 125, 50, 0.22);
        color: var(--gm-green);
    }

    .alert-danger {
        background: rgba(192, 57, 43, 0.1);
        border-color: rgba(192, 57, 43, 0.22);
        color: var(--gm-red);
    }

    .btn-outline-primary {
        color: var(--gm-deep-saffron);
        border-color: rgba(211, 84, 0, 0.38);
        font-weight: 800;
    }

    .btn-outline-primary:hover {
        background: var(--gm-deep-saffron);
        border-color: var(--gm-deep-saffron);
        color: #fff;
    }

    .btn-outline-danger {
        color: var(--gm-red);
        border-color: rgba(192, 57, 43, 0.42);
        font-weight: 800;
    }

    .btn-outline-danger:hover {
        background: var(--gm-red);
        border-color: var(--gm-red);
        color: #fff;
    }

    .text-warning {
        color: var(--gm-saffron) !important;
    }

    @media (max-width: 767px) {
        .gm-page-hero,
        .gm-card {
            border-radius: 22px;
            padding: 20px;
        }

        .gm-hero-title {
            font-size: 23px;
        }

        .gm-table {
            border-spacing: 0 10px;
        }

        .gm-table tbody td {
            padding: 14px;
        }

        .gm-btn-primary,
        .gm-btn-light {
            width: 100%;
        }
    }
</style>