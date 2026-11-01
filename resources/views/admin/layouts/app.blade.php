<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - SMP Tunas Harapan Bekasi</title>

    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- AdminLTE CSS & Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@1.13.1/css/OverlayScrollbars.min.css">

    <style>
        :root {
            --admin-bg: #f8fafc;
            --admin-sidebar-bg: #090d16;
            --admin-sidebar-border: rgba(255, 255, 255, 0.08);
            --admin-card-bg: #ffffff;
            --admin-card-border: #e2e8f0;
            --admin-primary: #2563eb;
            --admin-primary-dark: #1d4ed8;
            --admin-text-main: #0f172a;
            --admin-text-muted: #64748b;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            background-color: var(--admin-bg) !important;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, h6,
        .brand-text, .card-title,
        .font-display {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 700;
            letter-spacing: -0.015em;
        }

        /* Content Wrapper */
        .content-wrapper {
            background-color: var(--admin-bg) !important;
            min-height: calc(100vh - 120px) !important;
        }

        /* Modern Floating Top Navbar */
        .main-header.navbar {
            background: rgba(255, 255, 255, 0.88) !important;
            backdrop-filter: blur(14px) !important;
            -webkit-backdrop-filter: blur(14px) !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.02) !important;
            padding: 0.65rem 1.5rem !important;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .main-header .nav-link {
            color: #475569 !important;
            font-weight: 500;
            transition: all 0.2s;
        }

        .main-header .nav-link:hover {
            color: var(--admin-primary) !important;
        }

        .visit-site-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 14px;
            border-radius: 9999px;
            background: #f1f5f9;
            color: #334155 !important;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none !important;
            border: 1px solid #e2e8f0;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .visit-site-pill:hover {
            background: #e2e8f0;
            color: #0f172a !important;
            transform: translateY(-1px);
        }

        /* Sleek Obsidian Sidebar */
        .main-sidebar {
            background: var(--admin-sidebar-bg) !important;
            border-right: 1px solid var(--admin-sidebar-border) !important;
            box-shadow: 4px 0 24px -2px rgba(0, 0, 0, 0.25) !important;
            overflow: hidden !important;
            overflow-x: hidden !important;
            overflow-y: hidden !important;
        }

        .brand-link {
            background: var(--admin-sidebar-bg) !important;
            border-bottom: 1px solid var(--admin-sidebar-border) !important;
            padding: 1.15rem 1.25rem !important;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-link .brand-image {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: #ffffff;
            padding: 2px;
        }

        .brand-title {
            color: #ffffff !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 15px;
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin: 0;
        }

        .brand-subtitle {
            color: #94a3b8;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            display: block;
        }

        /* Sidebar User Panel */
        .sidebar .user-panel {
            background: rgba(255, 255, 255, 0.04) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 14px !important;
            padding: 10px 12px !important;
            margin: 0.75rem 0.5rem !important;
            display: flex !important;
            align-items: center !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
            white-space: normal !important;
        }

        .sidebar .user-panel .image-circle {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            flex-shrink: 0 !important;
            border-radius: 10px !important;
            background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            font-size: 15px !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35) !important;
            margin-right: 12px !important;
        }

        .sidebar .user-panel .user-info-text {
            min-width: 0 !important;
            flex: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            overflow: hidden !important;
        }

        .sidebar .user-panel .user-name {
            color: #f8fafc !important;
            font-weight: 700 !important;
            font-size: 13.5px !important;
            line-height: 1.25 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            margin-bottom: 2px !important;
        }

        .sidebar .user-panel .role-pill {
            display: inline-block !important;
            width: fit-content !important;
            font-size: 9.5px !important;
            font-weight: 800 !important;
            letter-spacing: 0.06em !important;
            text-transform: uppercase !important;
            padding: 2px 8px !important;
            border-radius: 999px !important;
        }

        .role-pill-admin {
            background: rgba(239, 68, 68, 0.2) !important;
            color: #fca5a5 !important;
            border: 1px solid rgba(239, 68, 68, 0.35) !important;
        }

        .role-pill-spmb {
            background: rgba(14, 165, 233, 0.2) !important;
            color: #7dd3fc !important;
            border: 1px solid rgba(14, 165, 233, 0.35) !important;
        }

        .role-pill-penulis {
            background: rgba(16, 185, 129, 0.2) !important;
            color: #6ee7b7 !important;
            border: 1px solid rgba(16, 185, 129, 0.35) !important;
        }

        /* Sidebar Nav Links & Horizontal Scroll Prevention */
        .main-sidebar {
            overflow: hidden !important;
            overflow-x: hidden !important;
            overflow-y: hidden !important;
        }

        .main-sidebar .sidebar,
        .sidebar,
        .sidebar .nav-sidebar,
        .sidebar .user-panel {
            overflow-x: hidden !important;
            box-sizing: border-box !important;
        }

        /* Ensure native scrollbars inside OverlayScrollbars viewport are hidden */
        .os-viewport::-webkit-scrollbar,
        .sidebar::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        .os-viewport,
        .sidebar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }

        .os-scrollbar-horizontal,
        .os-scrollbar-horizontal * {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            max-height: 0 !important;
            pointer-events: none !important;
        }

        .sidebar .nav-header {
            font-size: 10px !important;
            letter-spacing: 0.12em !important;
            font-weight: 800 !important;
            color: #475569 !important;
            text-transform: uppercase !important;
            padding: 16px 14px 6px !important;
        }

        .sidebar .nav-sidebar > .nav-item {
            margin-bottom: 3px;
        }

        .sidebar .nav-sidebar .nav-link {
            border-radius: 10px !important;
            color: #94a3b8 !important;
            padding: 8px 12px !important;
            font-weight: 500 !important;
            font-size: 13.5px !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: flex !important;
            align-items: center !important;
            overflow: hidden !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        .sidebar .nav-sidebar .nav-link p {
            margin: 0 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: calc(100% - 24px) !important;
        }

        .sidebar .nav-sidebar .nav-link i.nav-icon {
            font-size: 14px !important;
            width: 26px !important;
            color: #64748b !important;
            transition: all 0.2s !important;
            text-align: left;
        }

        .sidebar .nav-sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.05) !important;
            color: #ffffff !important;
            transform: translateX(2px);
        }

        .sidebar .nav-sidebar .nav-link:hover i.nav-icon {
            color: #38bdf8 !important;
        }

        .sidebar .nav-sidebar > .nav-item > .nav-link.active,
        .sidebar .nav-sidebar > .nav-item > .nav-link.active:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 14px -1px rgba(37, 99, 235, 0.45) !important;
        }

        .sidebar .nav-sidebar > .nav-item > .nav-link.active i.nav-icon {
            color: #ffffff !important;
        }

        .nav-treeview {
            border-left: 1px solid rgba(255, 255, 255, 0.08);
            margin-left: 20px;
            padding-left: 4px;
        }

        /* Modern Cards */
        .card {
            border-radius: 16px !important;
            border: 1px solid var(--admin-card-border);
            background-color: var(--admin-card-bg);
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
            margin-bottom: 1.5rem;
        }

        .card.admin-hero-banner {
            background: linear-gradient(135deg, #090d16 0%, #1e293b 60%, #0f172a 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.35) !important;
            color: #ffffff !important;
        }

        .card:hover {
            box-shadow: 0 6px 24px -2px rgba(15, 23, 42, 0.07) !important;
        }

        .card-header {
            border-bottom: 1px solid #f1f5f9 !important;
            background: transparent !important;
            padding: 1.1rem 1.5rem;
            border-radius: 16px 16px 0 0 !important;
        }

        .card-header::after,
        .card-body::after,
        .card-footer::after {
            display: none !important;
            content: none !important;
        }

        .card-header .card-title {
            float: none !important;
            font-size: 1.05rem !important;
            font-weight: 700 !important;
            color: var(--admin-text-main) !important;
            margin: 0 !important;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-body.p-0 {
            padding: 0 !important;
        }

        /* Modern Buttons */
        .btn {
            border-radius: 10px !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            padding: 8px 16px !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .btn-sm {
            padding: 5px 12px !important;
            font-size: 12px !important;
            border-radius: 8px !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3) !important;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.45) !important;
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25) !important;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25) !important;
        }

        .btn-default, .btn-secondary {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #475569 !important;
        }

        .btn-default:hover, .btn-secondary:hover {
            background: #f8fafc !important;
            color: #0f172a !important;
            border-color: #94a3b8 !important;
        }

        /* Modern Form Controls */
        .form-control {
            border-radius: 10px;
            border: 1px solid #cbd5e1 !important;
            font-size: 13.5px;
            padding: 7px 12px;
            height: auto;
            transition: all 0.2s !important;
            background-color: #ffffff !important;
        }

        .form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
            outline: none !important;
        }

        .form-control-sm {
            border-radius: 8px;
            font-size: 12.5px !important;
            padding: 5px 10px !important;
            height: auto !important;
        }

        .input-group > .form-control:not(:last-child) {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        .input-group > .input-group-append > .btn {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        /* Modern Tables */
        .table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            width: 100% !important;
        }

        .table thead th,
        .thead-light th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.06em !important;
            border-top: none !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 12px 16px !important;
        }

        .table td {
            padding: 13px 16px !important;
            font-size: 13.5px !important;
            color: #334155 !important;
            border-top: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(248, 250, 252, 0.6) !important;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f5f9 !important;
        }

        /* Modern Badges */
        .badge {
            border-radius: 999px !important;
            padding: 4px 10px !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            letter-spacing: 0.02em !important;
        }

        .badge-status {
            font-size: 11px;
            padding: 4px 12px;
            border-radius: 999px;
            font-weight: 700;
        }

        .badge-success {
            background-color: #ecfdf5 !important;
            color: #047857 !important;
            border: 1px solid #a7f3d0 !important;
        }

        .badge-warning {
            background-color: #fffbeb !important;
            color: #b45309 !important;
            border: 1px solid #fde68a !important;
        }

        .badge-danger {
            background-color: #fef2f2 !important;
            color: #b91c1c !important;
            border: 1px solid #fecaca !important;
        }

        .badge-info {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe !important;
        }

        .badge-primary {
            background-color: #e0e7ff !important;
            color: #4338ca !important;
            border: 1px solid #c7d2fe !important;
        }

        .badge-secondary {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #cbd5e1 !important;
        }

        /* Modern Alerts */
        .alert {
            border-radius: 12px !important;
            border: none !important;
            padding: 14px 18px !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03) !important;
        }

        .alert-success {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%) !important;
            color: #065f46 !important;
            border-left: 4px solid #10b981 !important;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%) !important;
            color: #991b1b !important;
            border-left: 4px solid #ef4444 !important;
        }

        /* Breadcrumb Header */
        .content-header {
            padding: 20px 0.5rem 12px !important;
        }

        .content-header h1 {
            font-size: 1.45rem !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            letter-spacing: -0.02em !important;
        }

        .breadcrumb {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 12.5px !important;
            font-weight: 500 !important;
            align-items: center !important;
        }

        .breadcrumb-item a {
            color: #64748b !important;
            text-decoration: none;
            transition: color 0.15s;
        }

        .breadcrumb-item a:hover {
            color: var(--admin-primary) !important;
        }

        .breadcrumb-item.active {
            color: var(--admin-primary) !important;
            font-weight: 600 !important;
        }

        /* Unified Admin KPI Cards */
        .admin-kpi-card,
        .spk-kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
            height: 100%;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .admin-kpi-card:hover,
        .spk-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
        }

        .admin-kpi-value,
        .spk-kpi-value {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 4px;
            color: #0f172a;
        }

        .admin-kpi-label,
        .spk-kpi-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0;
        }

        .admin-kpi-icon,
        .spk-kpi-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
            margin-left: 12px;
        }

        /* Button & Card Refinements */
        .btn-xs {
            padding: 4px 10px !important;
            font-size: 11.5px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
        }

        .btn-flat {
            border-radius: 8px !important;
        }

        .btn-info {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(14, 165, 233, 0.25) !important;
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
            color: #ffffff !important;
            transform: translateY(-1px);
        }

        .card-footer {
            background: #ffffff !important;
            border-top: 1px solid #f1f5f9 !important;
            border-radius: 0 0 16px 16px !important;
            padding: 1rem 1.5rem;
        }

        /* Modern Nav Tabs */
        .nav-tabs {
            border-bottom: 1px solid #e2e8f0 !important;
        }

        .nav-tabs .nav-link {
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600 !important;
            font-size: 13.5px !important;
            color: #64748b !important;
            padding: 10px 18px !important;
            border: 1px solid transparent !important;
            transition: all 0.15s ease !important;
        }

        .nav-tabs .nav-link:hover {
            color: var(--admin-primary) !important;
            background: rgba(241, 245, 249, 0.6) !important;
        }

        .nav-tabs .nav-link.active {
            color: var(--admin-primary) !important;
            background: #ffffff !important;
            border-color: #e2e8f0 #e2e8f0 #ffffff !important;
        }

        /* Utility Shims for Flex Gap & Colors */
        .gap-1 { gap: 0.25rem !important; }
        .gap-2 { gap: 0.5rem !important; }
        .gap-3 { gap: 0.75rem !important; }
        .gap-4 { gap: 1rem !important; }
        .font-weight-semibold { font-weight: 600 !important; }
        .text-amber-500, .text-amber-600 { color: #d97706 !important; }
        .text-purple { color: #7c3aed !important; }
        .text-gray-600 { color: #475569 !important; }
        .bg-light-blue { background-color: #eff6ff !important; }

        /* Pagination Polish */
        .pagination {
            margin-bottom: 0 !important;
            gap: 4px;
        }
        .page-item .page-link {
            border-radius: 8px !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            font-weight: 600;
            font-size: 12.5px;
            padding: 6px 12px;
        }
        .page-item.active .page-link {
            background: var(--admin-primary) !important;
            border-color: var(--admin-primary) !important;
            color: #ffffff !important;
        }

        /* Dropdown Menus */
        .dropdown-menu {
            border-radius: 14px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08) !important;
            padding: 8px !important;
        }

        .dropdown-item {
            border-radius: 8px !important;
            padding: 8px 14px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            transition: all 0.15s !important;
        }

        .dropdown-item:hover {
            background-color: #f1f5f9 !important;
            color: var(--admin-primary) !important;
        }

        /* Footer */
        .main-footer {
            background: #ffffff !important;
            border-top: 1px solid #e2e8f0 !important;
            color: #64748b !important;
            font-size: 12px !important;
            padding: 1rem 1.5rem !important;
        }

        /* Image Thumbnails */
        .img-thumb {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .img-thumb-lg {
            width: 100%;
            max-height: 280px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
    </style>

    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Floating Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Toggle Navigation">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </a>
                </li>
                <li class="nav-item ml-2">
                    <a href="{{ route('home') }}" target="_blank" class="visit-site-pill" title="Buka Website Publik">
                        <i class="fa-solid fa-globe text-primary"></i>
                        <span>Lihat Website</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-muted"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto align-items-center">
                <!-- Unread Messages (Admin / SPMB) -->
                @if (in_array(auth()->user()->role, ['admin', 'spmb']))
                    <li class="nav-item dropdown mr-2">
                        <a class="nav-link position-relative" data-toggle="dropdown" href="#" title="Pesan Kontak">
                            <i class="fa-regular fa-envelope text-base"></i>
                            @php $unread = \App\Models\Contact::unread()->count(); @endphp
                            @if ($unread > 0)
                                <span class="badge badge-danger navbar-badge font-weight-bold" style="font-size: 9px; padding: 2px 5px; border-radius: 999px;">{{ $unread }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <span class="dropdown-item dropdown-header font-weight-bold text-dark">{{ $unread }} Pesan Masuk Baru</span>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('admin.contacts.index', ['filter' => 'unread']) }}" class="dropdown-item">
                                <i class="fa-solid fa-inbox mr-2 text-primary"></i> Buka Kotak Masuk
                            </a>
                        </div>
                    </li>
                @endif

                <!-- User Profile Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center gap-2 p-1" data-toggle="dropdown" href="#">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold shadow-sm"
                            style="width: 34px; height: 34px; background: linear-gradient(135deg, #2563eb, #06b6d4); font-size: 13px;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="d-none d-md-inline-block font-weight-semibold text-dark text-sm ml-2">{{ Str::limit(auth()->user()->name, 16) }}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-muted ml-1"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right mt-2" style="min-width: 220px;">
                        <div class="dropdown-item p-3 text-center bg-light rounded-top">
                            <p class="font-weight-bold text-dark mb-0">{{ auth()->user()->name }}</p>
                            <small class="text-muted d-block text-truncate">{{ auth()->user()->email }}</small>
                            <span class="badge {{ auth()->user()->role === 'admin' ? 'badge-danger' : 'badge-info' }} mt-1">
                                Role: {{ ucfirst(auth()->user()->role) }}
                            </span>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('home') }}" class="dropdown-item py-2" target="_blank">
                            <i class="fa-solid fa-arrow-up-right-from-square mr-2 text-muted"></i> Buka Website Sekolah
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger py-2">
                                <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Keluar (Logout)
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar">
            <!-- Brand Logo -->
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <img src="{{ asset('assets/images/logo-tunas-harapan.png') }}" alt="Logo" class="brand-image">
                <div class="d-flex flex-column">
                    <span class="brand-title">SMP Tunas Harapan</span>
                    <span class="brand-subtitle">Admin Workspace</span>
                </div>
            </a>

            <!-- Sidebar Content -->
            <div class="sidebar">
                <!-- User Profile Card in Sidebar -->
                <div class="user-panel">
                    <div class="image-circle">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="user-info-text">
                        <div class="user-name" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</div>
                        @php
                            $roleClass = match(auth()->user()->role) {
                                'admin' => 'role-pill-admin',
                                'spmb' => 'role-pill-spmb',
                                default => 'role-pill-penulis',
                            };
                        @endphp
                        <div>
                            <span class="role-pill {{ $roleClass }}">
                                {{ strtoupper(auth()->user()->role) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav class="mt-3">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                        <!-- Main Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}"
                                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fa-solid fa-gauge-high"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Content Menu (Admin & Penulis) -->
                        @if (in_array(auth()->user()->role, ['admin', 'penulis']))
                            <li class="nav-header">Manajemen Konten</li>
                            <li class="nav-item">
                                <a href="{{ route('admin.categories.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-tag"></i>
                                    <p>Kategori Berita</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.posts.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-newspaper"></i>
                                    <p>Artikel Berita</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.comments.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-comments"></i>
                                    <p>Moderasi Komentar</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.galleries.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.galleries.*') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-images"></i>
                                    <p>Galeri & Fasilitas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.programs.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-award"></i>
                                    <p>Program Unggulan</p>
                                </a>
                            </li>
                        @endif

                        <!-- PPDB / SPMB Menu (Admin & SPMB) -->
                        @if (in_array(auth()->user()->role, ['admin', 'spmb']))
                            <li class="nav-header">Portal SPMB Online</li>
                            <li class="nav-item">
                                <a href="{{ route('admin.registrations.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.registrations.*') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-user-graduate"></i>
                                    <p>Data Pendaftar</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.payments.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-receipt"></i>
                                    <p>Histori Pembayaran</p>
                                </a>
                            </li>

                            <li class="nav-item {{ request()->routeIs('admin.examp.*') ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ request()->routeIs('admin.examp.*') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-laptop-code"></i>
                                    <p>
                                        Ujian Online CBT
                                        <i class="right fa-solid fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.examp.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.examp.index') ? 'active' : '' }}">
                                            <i class="fa-solid fa-circle-question nav-icon text-xs text-primary"></i>
                                            <p>Bank Soal CBT</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.examp.results') }}"
                                            class="nav-link {{ request()->routeIs('admin.examp.results') ? 'active' : '' }}">
                                            <i class="fa-solid fa-chart-line nav-icon text-xs text-success"></i>
                                            <p>Hasil Ujian Siswa</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item {{ request()->routeIs('admin.spk.*') ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ request()->routeIs('admin.spk.*') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-scale-balanced"></i>
                                    <p>
                                        SPK Seleksi (SAW)
                                        <i class="right fa-solid fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.spk.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.spk.index') ? 'active' : '' }}">
                                            <i class="fa-solid fa-trophy nav-icon text-xs text-warning"></i>
                                            <p>Perhitungan & Ranking</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.spk.scores') }}"
                                            class="nav-link {{ request()->routeIs('admin.spk.scores*') ? 'active' : '' }}">
                                            <i class="fa-solid fa-pen-to-square nav-icon text-xs text-info"></i>
                                            <p>Input Nilai Siswa</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.spk.criteria.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.spk.criteria.*') ? 'active' : '' }}">
                                            <i class="fa-solid fa-sliders nav-icon text-xs text-primary"></i>
                                            <p>Data Kriteria & Bobot</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        <!-- School Management (Admin Only) -->
                        @if (auth()->user()->role === 'admin')
                            <li class="nav-header">Manajemen Sekolah</li>
                            <li class="nav-item">
                                <a href="{{ route('admin.teachers.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-chalkboard-user"></i>
                                    <p>Dewan Guru & Staf</p>
                                </a>
                            </li>
                        @endif

                        <!-- Messages (Admin & SPMB) -->
                        @if (in_array(auth()->user()->role, ['admin', 'spmb']))
                            <li class="nav-item">
                                <a href="{{ route('admin.contacts.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-inbox"></i>
                                    <p>
                                        Pesan Masuk
                                        @php $unread = \App\Models\Contact::unread()->count(); @endphp
                                        @if ($unread > 0)
                                            <span class="badge badge-warning right font-weight-bold" style="font-size: 10px;">{{ $unread }}</span>
                                        @endif
                                    </p>
                                </a>
                            </li>
                        @endif

                        <!-- Konfigurasi (Khusus Admin) -->
                        @if (auth()->user()->role === 'admin')
                            <li class="nav-header">Konfigurasi</li>
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-sliders"></i>
                                    <p>Pengaturan Situs</p>
                                </a>
                            </li>
                        @endif

                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Main Dynamic Content -->
            <section class="content pt-3">
                <div class="container-fluid px-sm-3 pb-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </section>
        </div>

        <!-- Modern Clean Footer -->
        <footer class="main-footer">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <div>
                    <strong>&copy; {{ date('Y') }} SMP Tunas Harapan Bekasi.</strong> Sistem Informasi Manajemen Sekolah.
                </div>
                <div class="text-muted text-xs">
                    Versi 2.0 &bull; Engine Laravel 12 &bull; Status: <span class="badge badge-success" style="font-size: 10px; padding: 2px 8px;">Active</span>
                </div>
            </div>
        </footer>
    </div>

    <!-- Admin Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@1.13.1/js/jquery.overlayScrollbars.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <!-- TinyMCE for rich text -->
    <script src="https://cdn.tiny.cloud/1/byvnra3fq3p3q64qd39ypde1ai5l2wi8aq5b4yqamuhxajf1/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: 'textarea.tinymce',
                height: 500,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount',
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
                content_style: 'body { font-family:Inter,Helvetica,Arial,sans-serif; font-size:15px; color:#334155; }',
                branding: false,
                statusbar: true,
                setup: function(editor) {
                    editor.on('blur', function() {
                        editor.save();
                    });
                }
            });
        }

        // Modern Confirm Delete
        document.addEventListener('submit', function(e) {
            if (e.target && e.target.classList.contains('form-delete-confirm')) {
                if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                    e.preventDefault();
                }
            }
        });

        // Bulk Delete: Select All & Toggle Button
        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'select-all') {
                var checked = e.target.checked;
                document.querySelectorAll('.row-checkbox').forEach(function(cb) {
                    cb.checked = checked;
                });
                toggleBulkDeleteBtn();
            }
            if (e.target && e.target.classList.contains('row-checkbox')) {
                toggleBulkDeleteBtn();
            }
        });

        function toggleBulkDeleteBtn() {
            var checked = document.querySelectorAll('.row-checkbox:checked');
            var btn = document.getElementById('bulk-delete-btn');
            if (btn) {
                btn.style.display = checked.length > 0 ? 'inline-block' : 'none';
            }
        }

        document.addEventListener('submit', function(e) {
            if (e.target && e.target.classList.contains('form-bulk-delete')) {
                if (!confirm('Yakin hapus data yang dipilih?')) {
                    e.preventDefault();
                }
            }
        });

        // Configure OverlayScrollbars to have only one sleek vertical scrollbar and prevent horizontal scroll
        $(function() {
            if ($.fn.overlayScrollbars) {
                var $sidebar = $('.main-sidebar .sidebar');
                var instance = $sidebar.overlayScrollbars();
                if (instance) {
                    instance.options({
                        overflowBehavior: {
                            x: 'hidden',
                            y: 'scroll'
                        }
                    });
                } else {
                    $sidebar.overlayScrollbars({
                        className: 'os-theme-light',
                        sizeAutoCapable: true,
                        scrollbars: {
                            autoHide: 'l',
                            clickScrolling: true
                        },
                        overflowBehavior: {
                            x: 'hidden',
                            y: 'scroll'
                        }
                    });
                }
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
