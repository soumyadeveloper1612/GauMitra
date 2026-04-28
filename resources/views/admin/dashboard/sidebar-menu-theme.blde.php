<style>
    :root {
        --gm-saffron: #f57c00;
        --gm-deep-saffron: #d35400;
        --gm-brown: #5d2f12;
        --gm-dark-brown: #3b1d0c;
        --gm-cream: #fff8ec;
        --gm-light: #fff3db;
        --gm-soft: #fff7ea;
        --gm-green: #2e7d32;
        --gm-red: #c0392b;
        --gm-border: rgba(93, 47, 18, 0.14);
        --gm-shadow: 0 18px 45px rgba(93, 47, 18, 0.12);
    }

    body {
        background:
            radial-gradient(circle at top left, rgba(245, 124, 0, 0.13), transparent 32%),
            linear-gradient(135deg, #fffaf0 0%, #fff3db 45%, #fff8ec 100%);
    }

    .gm-page-hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: 26px;
        background:
            linear-gradient(135deg, rgba(93, 47, 18, 0.97), rgba(211, 84, 0, 0.95)),
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
    }

    .gm-hero-title {
        margin: 0;
        font-size: 28px;
        font-weight: 900;
        letter-spacing: -0.4px;
    }

    .gm-hero-subtitle {
        margin: 8px 0 0;
        color: rgba(255, 255, 255, 0.86);
        max-width: 720px;
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

    .gm-btn-primary {
        background: linear-gradient(135deg, var(--gm-saffron), var(--gm-deep-saffron));
        color: #fff;
        border: 0;
        border-radius: 999px;
        padding: 11px 24px;
        font-weight: 900;
        box-shadow: 0 12px 24px rgba(211, 84, 0, 0.24);
    }

    .gm-btn-primary:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 16px 28px rgba(211, 84, 0, 0.32);
    }

    .gm-btn-light {
        background: #fff7ea;
        color: var(--gm-brown);
        border: 1px solid rgba(93, 47, 18, 0.16);
        border-radius: 999px;
        padding: 11px 24px;
        font-weight: 900;
    }

    .gm-btn-light:hover {
        background: #ffe8bf;
        color: var(--gm-dark-brown);
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

    .gm-form-control:focus,
    .gm-form-select:focus {
        border-color: var(--gm-saffron);
        box-shadow: 0 0 0 0.22rem rgba(245, 124, 0, 0.16);
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
    }

    .gm-section-box {
        border: 1px solid rgba(93, 47, 18, 0.1);
        background: linear-gradient(180deg, #fffdfa, #fff7ea);
        border-radius: 24px;
        padding: 22px;
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

    .gm-menu-icon {
        width: 42px;
        height: 42px;
        border-radius: 15px;
        background: linear-gradient(135deg, #ffe0ad, #f57c00);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        margin-right: 10px;
        flex: 0 0 auto;
    }

    .gm-menu-title-text {
        color: var(--gm-brown);
        font-weight: 900;
    }

    .gm-parent-chip {
        background: #fff0d3;
        color: var(--gm-brown);
        border: 1px solid rgba(245, 124, 0, 0.22);
        border-radius: 999px;
        padding: 7px 11px;
        font-weight: 800;
        display: inline-block;
    }

    .gm-badge-success {
        background: rgba(46, 125, 50, 0.12);
        color: var(--gm-green);
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 900;
    }

    .gm-badge-danger {
        background: rgba(192, 57, 43, 0.12);
        color: var(--gm-red);
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 900;
    }

    .gm-badge-dark {
        background: rgba(59, 29, 12, 0.12);
        color: var(--gm-dark-brown);
        border-radius: 999px;
        padding: 7px 12px;
        font-weight: 900;
    }

    .gm-action-btn {
        border-radius: 999px;
        font-weight: 800;
        padding: 7px 13px;
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

    .gm-small-help {
        color: rgba(93, 47, 18, 0.62);
        font-size: 12px;
        font-weight: 600;
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
    }
</style>