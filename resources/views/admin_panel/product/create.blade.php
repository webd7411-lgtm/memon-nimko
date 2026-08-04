<!-- meta tags and other links -->

@extends('admin_panel.layout.app')
@section('content')

<style>
    /* ====== PREMIUM WIZARD STYLES ====== */
    .pz-root {
        --pz-accent: #6d5cff;
        --pz-accent2: #8b5cf6;
        --pz-accent-drk: #4f3dff;
        --pz-grad: linear-gradient(135deg, #6d5cff 0%, #8b5cf6 50%, #a855f7 100%);
        --pz-grad-soft: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
        --pz-border: #e5e7eb;
        --pz-text: #1e293b;
        --pz-muted: #94a3b8;
        --pz-success: #10b981;
        --pz-danger: #ef4444;
        --pz-radius: 16px;
        --pz-shadow: 0 20px 50px rgba(76, 55, 160, 0.10);
    }

    .pz-root { font-family: 'Inter', system-ui, sans-serif; max-width: 1150px; margin: 0 auto; }

    .pz-hero {
        background: var(--pz-grad);
        border-radius: 16px;
        padding: 1.2rem 1.6rem;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .8rem;
        box-shadow: 0 10px 30px rgba(109, 92, 255, .25);
        margin-bottom: 1rem;
        position: relative;
        overflow: hidden;
    }
    .pz-hero::after {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
    }
    .pz-hero::before {
        content: '';
        position: absolute;
        right: 60px;
        bottom: -60px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,.06);
    }
    .pz-hero h2 { font-size: 1.15rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: .5rem; position: relative; z-index: 1; }
    .pz-hero h2 i { background: rgba(255,255,255,.2); border-radius: 10px; padding: .4rem .5rem; font-size: .95rem; }
    .pz-hero .pz-hero-sub { color: rgba(255,255,255,.85); font-size: .76rem; margin-top: .15rem; }
    .pz-hero-actions { display: flex; gap: .45rem; position: relative; z-index: 1; flex-wrap: wrap; }
    .pz-hero-actions .btn {
        border-radius: 9px; font-weight: 600; font-size: .74rem; padding: .38rem .85rem;
        background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.35); transition: all .2s;
    }
    .pz-hero-actions .btn:hover { background: rgba(255,255,255,.3); color: #fff; transform: translateY(-1px); }
    .pz-hero-actions .btn.pz-btn-solid { background: #fff; color: var(--pz-accent); border-color: #fff; }
    .pz-hero-actions .btn.pz-btn-solid:hover { background: #f3f0ff; }

    /* Progress indicator */
    .pz-progress-wrap {
        background: #fff;
        border: 1px solid var(--pz-border);
        border-radius: 14px;
        padding: .8rem 1.2rem;
        margin-bottom: 1rem;
        box-shadow: 0 8px 25px rgba(76, 55, 160, .06);
        position: sticky;
        top: 70px;
        z-index: 50;
    }
    .pz-steps { display: flex; align-items: flex-start; justify-content: space-between; position: relative; }
    .pz-steps::before {
        content: '';
        position: absolute;
        top: 19px; left: 12%; right: 12%;
        height: 2.5px;
        background: #e9eaf0;
        border-radius: 3px;
    }
    .pz-steps::after {
        content: '';
        position: absolute;
        top: 19px; left: 12%;
        height: 2.5px;
        width: 0;
        background: var(--pz-grad);
        border-radius: 3px;
        transition: width .45s cubic-bezier(.4,0,.2,1);
        z-index: 1;
    }
    .pz-steps[data-progress="1"]::after { width: 0%; }
    .pz-steps[data-progress="2"]::after { width: 25%; }
    .pz-steps[data-progress="3"]::after { width: 50%; }
    .pz-steps[data-progress="4"]::after { width: 75%; }
    .pz-steps[data-progress="5"]::after { width: 100%; }

    .pz-step { flex: 1; text-align: center; position: relative; z-index: 2; cursor: pointer; background: transparent; border: none; padding: 0; }
    .pz-step .pz-dot {
        width: 38px; height: 38px; border-radius: 50%;
        background: #fff; border: 2px solid #dfe0ea;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .9rem; font-weight: 700; color: #a0a6b8;
        transition: all .35s cubic-bezier(.4,0,.2,1);
        margin-bottom: .35rem;
        position: relative;
    }
    .pz-step .pz-dot i { display: none; font-size: .9rem; }
    .pz-step .pz-dot .pz-dot-badge {
        display: none; position: absolute; top: -5px; right: -5px; width: 16px; height: 16px;
        background: var(--pz-success); border: 2px solid #fff; border-radius: 50%;
        align-items: center; justify-content: center; font-size: .5rem; color: #fff; font-style: normal;
    }
    .pz-step .pz-step-label { display: block; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #a0a6b8; transition: all .3s; }
    .pz-step .pz-step-desc { display: block; font-size: .64rem; color: #c0c5d3; margin-top: 1px; }
    .pz-step.active .pz-dot {
        background: var(--pz-grad); border-color: transparent; color: #fff;
        box-shadow: 0 6px 16px rgba(139, 92, 246, .35);
        transform: scale(1.08);
    }
    .pz-step.active .pz-step-label { color: var(--pz-accent); }
    .pz-step.done .pz-dot { background: var(--pz-success); border-color: transparent; color: #fff; }
    .pz-step.done .pz-dot .pz-dot-badge { display: inline-flex; }
    .pz-step.done .pz-step-label { color: var(--pz-success); }

    /* Wizard body */
    .pz-body {
        background: #fff;
        border: 1px solid var(--pz-border);
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(76, 55, 160, .07);
        overflow: hidden;
        min-height: 280px;
    }
    .pz-panel { display: none; padding: 1.4rem 1.6rem; animation: pzFade .4s ease; }
    .pz-panel.active { display: block; }
    @keyframes pzFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

    .pz-panel-head { margin-bottom: 1.1rem; padding-bottom: .9rem; border-bottom: 1px solid #f1f2f6; }
    .pz-panel-head h4 { font-size: 1rem; font-weight: 800; color: var(--pz-text); margin: 0; display: flex; align-items: center; gap: .45rem; }
    .pz-panel-head h4 .pz-num {
        width: 24px; height: 24px; border-radius: 7px; background: var(--pz-grad); color: #fff;
        display: inline-flex; align-items: center; justify-content: center; font-size: .72rem; font-weight: 800;
    }
    .pz-panel-head p { color: var(--pz-muted); font-size: .78rem; margin: .2rem 0 0; }

    .pz-label { font-size: .74rem; font-weight: 700; color: #475569; margin-bottom: .3rem; display: flex; align-items: center; gap: .3rem; letter-spacing: .2px; }
    .pz-label i { color: var(--pz-accent); }
    .pz-req { color: var(--pz-danger); }
    .pz-control {
        width: 100%; border: 1.5px solid #e2e5ee; border-radius: 10px;
        padding: .48rem .75rem; font-size: .82rem; font-weight: 500; color: var(--pz-text);
        outline: none; transition: all .2s; background: #fbfbfd;
    }
    .pz-control:focus { border-color: var(--pz-accent); box-shadow: 0 0 0 4px rgba(109, 92, 255, .12); background: #fff; }
    .pz-control.is-invalid { border-color: var(--pz-danger); box-shadow: 0 0 0 4px rgba(239, 68, 68, .12); }
    .pz-control:disabled, .pz-control[readonly] { background: #f3f4f8; cursor: not-allowed; }

    .pz-barcode-group { display: flex; align-items: stretch; gap: .4rem; }
    .pz-barcode-group .pz-control { flex: 1; min-width: 0; }
    .pz-barcode-btn {
        flex: 0 0 38px; border: none; border-radius: 10px;
        background: var(--pz-accent); color: #fff; font-size: 1rem;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all .2s; line-height: 1;
    }
    .pz-barcode-btn:hover { background: #5943e8; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(109, 92, 255, .3); }

    .pz-hint { font-size: .7rem; color: var(--pz-muted); margin-top: .25rem; }

    /* Unit selector */
    .pz-unit-selector { display: grid; grid-template-columns: repeat(3, 1fr); gap: .7rem; }
    .pz-unit-opt input { display: none; }
    .pz-unit-opt label {
        display: block; text-align: center; padding: .85rem .7rem; border: 2px solid #e2e5ee;
        border-radius: 12px; cursor: pointer; transition: all .25s; background: #fbfbfd;
    }
    .pz-unit-opt label i { display: block; font-size: 1.4rem; margin-bottom: .3rem; color: #a0a6b8; transition: all .25s; }
    .pz-unit-opt label small { color: var(--pz-muted); }
    .pz-unit-opt label .pz-unit-name { font-weight: 700; font-size: .82rem; color: var(--pz-text); display: block; }
    .pz-unit-opt input:checked + label {
        border-color: var(--pz-accent); background: var(--pz-grad-soft);
        box-shadow: 0 5px 14px rgba(109, 92, 255, .18);
    }
    .pz-unit-opt input:checked + label i, .pz-unit-opt input:checked + label .pz-unit-name { color: var(--pz-accent); }

    /* Variants */
    .pz-variant-row {
        background: #fafaff; border: 1.5px solid #e6e8f2; border-radius: 12px; padding: .85rem 1rem;
        margin-bottom: .75rem; position: relative; transition: all .25s;
    }
    .pz-variant-row:hover { border-color: #cfd3ff; box-shadow: 0 4px 16px rgba(109, 92, 255, .08); }
    .pz-variant-row .pz-remove-variant {
        position: absolute; top: 8px; right: 8px; width: 24px; height: 24px; border-radius: 50%;
        background: #fee2e2; color: #dc2626; border: none; font-size: 13px; cursor: pointer; transition: all .2s;
    }
    .pz-variant-row .pz-remove-variant:hover { background: #ef4444; color: #fff; transform: scale(1.1); }
    .variant-barcode-row { margin-top: .65rem; padding-top: .65rem; border-top: 1px dashed #e3e6f1; }
    .variant-barcode-row .pz-barcode-group { max-width: 340px; }
    .pz-add-variant-btn {
        background: var(--pz-grad); color: #fff; border: none; border-radius: 10px; padding: .5rem 1.2rem;
        font-weight: 700; font-size: .8rem; cursor: pointer; transition: all .25s; box-shadow: 0 5px 14px rgba(139, 92, 246, .28);
    }
    .pz-add-variant-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(139, 92, 246, .38); }

    /* BOM table */
    .pz-bom-card { background: #fafaff; border: 1.5px solid #e6e8f2; border-radius: 12px; padding: .85rem; }
    .pz-bom-card table { margin-bottom: 0; }
    .pz-bom-card thead th { font-size: .68rem; text-transform: uppercase; letter-spacing: .5px; color: var(--pz-muted); background: #f1f2fa; border: none; }
    .pz-bom-card tbody td { vertical-align: middle; padding: .45rem .5rem; }
    .pz-bom-card .form-select, .pz-bom-card .form-control { border-radius: 9px; }

    /* Image upload */
    .pz-img-drop {
        border: 2px dashed #d5d8e5; border-radius: 14px; padding: 1.2rem; text-align: center;
        background: #fafaff; cursor: pointer; transition: all .25s; position: relative; overflow: hidden;
    }
    .pz-img-drop:hover { border-color: var(--pz-accent); background: #f5f3ff; }
    .pz-img-drop img { max-width: 100%; max-height: 200px; border-radius: 8px; display: block; margin: 0 auto; }
    .pz-img-drop .pz-img-placeholder i { font-size: 2.2rem; color: #c3c8d8; display: block; margin-bottom: .4rem; }
    .pz-img-drop .pz-img-placeholder span { font-size: .76rem; color: var(--pz-muted); font-weight: 600; }
    .pz-clear-img { position: absolute; top: 8px; right: 8px; width: 28px; height: 28px; border-radius: 50%;
        background: rgba(0,0,0,.6); color: #fff; border: none; font-size: 14px; cursor: pointer; z-index: 5; }

    /* Review panel */
    .pz-review-summary { margin-bottom: 1.2rem; }
    .pz-review-hero {
        display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;
        background: var(--pz-grad); border-radius: 16px; padding: 1.1rem 1.3rem; color: #fff;
        box-shadow: 0 10px 26px rgba(139, 92, 246, .28);
    }
    .pz-review-hero-left { display: flex; align-items: center; gap: .9rem; min-width: 0; }
    .pz-review-hero-icon {
        flex: 0 0 46px; height: 46px; border-radius: 12px; background: rgba(255,255,255,.18);
        display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem;
    }
    .pz-review-hero-name { font-size: 1.05rem; font-weight: 800; letter-spacing: .2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .pz-review-hero-meta { font-size: .72rem; opacity: .85; margin-top: 1px; }
    .pz-review-hero-stats { display: flex; gap: .8rem; }
    .pz-review-stat {
        background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.22);
        border-radius: 12px; padding: .5rem .85rem; text-align: center; min-width: 84px;
    }
    .pz-review-stat-num { display: block; font-size: 1.15rem; font-weight: 800; line-height: 1.1; }
    .pz-review-stat-lbl { font-size: .64rem; text-transform: uppercase; letter-spacing: .6px; opacity: .85; }
    .pz-review-card {
        background: #fff; border: 1px solid var(--pz-border); border-radius: 14px; overflow: hidden;
        box-shadow: 0 2px 10px rgba(15, 23, 42, .04);
    }
    .pz-review-card-head {
        display: flex; align-items: center; gap: .5rem;
        padding: .65rem 1rem; font-size: .74rem; font-weight: 800;
        text-transform: uppercase; letter-spacing: .5px; color: #475569;
        background: linear-gradient(180deg, #fbfbfe, #f6f6fb); border-bottom: 1px solid #f0f1f5;
    }
    .pz-review-card-head i { color: var(--pz-accent); font-size: .95rem; }
    .pz-review-table { width: 100%; border-collapse: collapse; font-size: .8rem; }
    .pz-review-table th { text-align: left; padding: .55rem .9rem; color: var(--pz-muted); font-weight: 600; width: 38%; background: #fafafd; }
    .pz-review-table td { padding: .55rem .9rem; font-weight: 700; color: var(--pz-text); }
    .pz-review-table tr { border-bottom: 1px solid #f3f4f8; }
    .pz-review-table tr:last-child { border-bottom: none; }
    .pz-review-mono { font-family: 'Consolas', monospace; letter-spacing: .4px; }
    .pz-review-chip { display: inline-block; background: #f3f0ff; color: var(--pz-accent); border-radius: 8px; padding: .25rem .6rem; font-size: .74rem; font-weight: 700; margin: 2px 3px 2px 0; border: 1px solid #e5dfff; }
    .pz-review-chip-wrap { padding: .8rem .9rem; }

    /* BOM (Step 3) card */
    .pz-bom-card {
        background: #fff; border: 1px solid var(--pz-border); border-radius: 14px; overflow: hidden;
        box-shadow: 0 2px 10px rgba(15, 23, 42, .04);
    }
    .pz-bom-head {
        display: flex; align-items: center; justify-content: space-between; gap: .8rem; flex-wrap: wrap;
        padding: .7rem 1rem; border-bottom: 1px solid #f0f1f5;
        background: linear-gradient(180deg, #fbfbfe, #f6f6fb);
    }
    .pz-bom-title { font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #475569; display: inline-flex; align-items: center; gap: .5rem; }
    .pz-bom-title i { color: var(--pz-accent); font-size: 1rem; }
    .pz-btn-add {
        background: var(--pz-accent); color: #fff; border-radius: 9px; padding: .45rem 1rem; font-size: .76rem;
        border: none; font-weight: 700; cursor: pointer; transition: all .25s; display: inline-flex; align-items: center; gap: .4rem;
        box-shadow: 0 4px 12px rgba(109, 92, 255, .28);
    }
    .pz-btn-add:hover { background: #5943e8; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(109, 92, 255, .36); }
    .pz-bom-table-wrap { overflow-x: auto; }
    .pz-bom-table { margin: 0; }
    .pz-bom-table thead th { font-size: .7rem; text-transform: uppercase; letter-spacing: .5px; color: var(--pz-muted); background: #fafafd; border-bottom: 1px solid #f0f1f5; padding: .6rem .9rem; }
    .pz-bom-table tbody td { vertical-align: middle; padding: .55rem .9rem; }
    .pz-bom-remove {
        background: #fef2f2; color: var(--pz-danger); border: 1px solid #fecaca; border-radius: 9px;
        padding: .35rem .7rem; line-height: 1; transition: all .2s;
    }
    .pz-bom-remove:hover { background: var(--pz-danger); color: #fff; border-color: var(--pz-danger); }
    .pz-bom-hint { padding: .55rem 1rem; font-size: .7rem; color: var(--pz-muted); border-top: 1px dashed #e8eaf2; background: #fcfcfe; display: flex; align-items: center; gap: .4rem; }
    .pz-bom-hint kbd { background: #eef0f5; border: 1px solid #dfe2ea; border-bottom-width: 2px; border-radius: 5px; padding: 0 .4rem; font-size: .68rem; color: #64748b; font-family: 'Consolas', monospace; }

    /* Nav buttons */
    .pz-nav { display: flex; align-items: center; justify-content: space-between; padding: .9rem 1.6rem; background: #fbfbfe; border-top: 1px solid #f0f1f5; }
    .pz-btn {
        border: none; border-radius: 10px; padding: .55rem 1.4rem; font-weight: 700; font-size: .82rem;
        cursor: pointer; transition: all .25s; display: inline-flex; align-items: center; gap: .45rem;
    }
    .pz-btn-next { background: var(--pz-grad); color: #fff; box-shadow: 0 6px 18px rgba(139, 92, 246, .32); }
    .pz-btn-next:hover { transform: translateY(-1px); box-shadow: 0 9px 24px rgba(139, 92, 246, .42); }
    .pz-btn-prev { background: #fff; color: #64748b; border: 1.5px solid #e2e5ee; }
    .pz-btn-prev:hover { background: #f1f2f6; }
    .pz-btn-submit { background: linear-gradient(135deg, #10b981, #059669); color: #fff; box-shadow: 0 6px 18px rgba(16, 185, 129, .32); }
    .pz-btn-submit:hover { transform: translateY(-1px); box-shadow: 0 9px 24px rgba(16, 185, 129, .42); }

    /* Legacy variant section fallback (kept for compat) */
    .variant-section { background: var(--pz-grad-soft); border: 2px dashed #cfd3ff; border-radius: 14px; padding: 1.2rem; }
    .unit-badge { display: inline-block; padding: 3px 10px; border-radius: 15px; font-size: 12px; font-weight: 600; margin-left: 5px; }
    .unit-badge.kg { background: #e3f2fd; color: #1565c0; }
    .unit-badge.piece { background: #e8f5e9; color: #2e7d32; }
    .unit-badge.pound { background: #fff3e0; color: #e65100; }
    .gram-display { color: #6d5cff; font-size: 12px; font-weight: 600; margin-top: 2px; }
    .default-radio-label { display: flex; align-items: center; gap: 5px; font-size: 13px; color: #555; cursor: pointer; }
    .default-radio-label input[type="radio"]:checked + span { color: #6d5cff; font-weight: 700; }
    .variant-number { width: 26px; height: 26px; background: var(--pz-grad); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; }

    @media (max-width: 768px) {
        .pz-hero { padding: 1rem; }
        .pz-hero-actions { width: 100%; justify-content: flex-start; }
        .pz-unit-selector { grid-template-columns: 1fr; }
        .pz-step .pz-step-desc { display: none; }
        .pz-panel { padding: 1.1rem; }
        .pz-nav { padding: .9rem 1rem; }
        .pz-btn { padding: .5rem 1.1rem; font-size: .78rem; }
        .pz-progress-wrap { top: 60px; }
    }
    @media (max-width: 480px) {
        .pz-step .pz-step-label { font-size: .6rem; letter-spacing: 0; }
        .pz-step .pz-dot { width: 32px; height: 32px; font-size: .8rem; }
        .pz-steps::before, .pz-steps::after { top: 16px; }
        .pz-nav { flex-direction: column-reverse; gap: .6rem; }
        .pz-nav > div { width: 100%; display: flex; gap: .6rem; }
        .pz-nav .pz-btn { flex: 1; justify-content: center; }
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid">
            <div class="body-wrapper">
                <div class="bodywrapper__inner">
                    <div class="pz-root">

                        {{-- HERO --}}
                        <div class="pz-hero">
                            <div>
                                <h2><i class="las la-birthday-cake"></i> Add New Product</h2>
                                <div class="pz-hero-sub">Create products with sizes, pricing & raw material recipes in minutes.</div>
                            </div>
                            <div class="pz-hero-actions">
                                <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#categoryModal"><i class="las la-plus"></i> Category</button>
                                <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#subcategoryModal"><i class="las la-plus"></i> Subcategory</button>
                                <button type="button" class="btn cuModalBtn" data-modal_title="Add New Brand" data-bs-toggle="modal" data-bs-target="#cuModal"><i class="las la-plus"></i> Brand</button>
                                <a class="btn pz-btn-solid" href="{{ route('product') }}"><i class="las la-arrow-left"></i> Back</a>
                            </div>
                        </div>

                        @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show"><strong>Success!</strong> {{ session('success') }}.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                        @endif
                        @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show"><strong>Error!</strong> {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                        @endif

                        <form action="/store-product" method="POST" enctype="multipart/form-data" id="productForm">

                            {{-- STEP PROGRESS --}}
                            <div class="pz-progress-wrap">
                                <div class="pz-steps" id="pzSteps" data-progress="1">
                                    <button type="button" class="pz-step active" data-step="1">
                                        <span class="pz-dot"><span>1</span><i class="pz-dot-badge las la-check"></i></span>
                                        <span class="pz-step-label">Basic Info</span>
                                        <span class="pz-step-desc">Name & Category</span>
                                    </button>
                                    <button type="button" class="pz-step" data-step="2">
                                        <span class="pz-dot"><span>2</span><i class="pz-dot-badge las la-check"></i></span>
                                        <span class="pz-step-label">Sizes & Pricing</span>
                                        <span class="pz-step-desc">Variants & Prices</span>
                                    </button>
                                    <button type="button" class="pz-step" data-step="3">
                                        <span class="pz-dot"><span>3</span><i class="pz-dot-badge las la-check"></i></span>
                                        <span class="pz-step-label">Recipe (BOM)</span>
                                        <span class="pz-step-desc">Raw Materials</span>
                                    </button>
                                    <button type="button" class="pz-step" data-step="4">
                                        <span class="pz-dot"><span>4</span><i class="pz-dot-badge las la-check"></i></span>
                                        <span class="pz-step-label">Review & Save</span>
                                        <span class="pz-step-desc">Confirm & Submit</span>
                                    </button>
                                </div>
                            </div>

                            @if ($errors->any())
                            <div class="alert alert-danger py-2 mb-3">
                                <strong>Validation Errors:</strong>
                                <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                            @endif

                            <div class="pz-body">

                                {{-- ============ STEP 1: BASIC INFO ============ --}}
                                <div class="pz-panel active" data-panel="1">
                                    <div class="pz-panel-head">
                                        <h4><span class="pz-num">1</span> Basic Information</h4>
                                        <p>Enter the core details of your product.</p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                            <label class="pz-label"><i class="las la-tag"></i> Product Name <span class="pz-req">*</span></label>
                                            <input type="text" name="product_name" id="pzProductName" class="pz-control" placeholder="Product name" required>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <label class="pz-label"><i class="las la-th-large"></i> Category</label>
                                            <select id="category-dropdown" name="category_id" class="pz-control">
                                                <option value="">Select</option>
                                                @foreach ($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <label class="pz-label"><i class="las la-layer-group"></i> Sub Category</label>
                                            <select id="subcategory-dropdown" name="sub_category_id" class="pz-control">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <label class="pz-label"><i class="las la-certificate"></i> Brand</label>
                                            <select name="brand_id[]" class="pz-control brand-select">
                                                <option value="">Select</option>
                                                @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <label class="pz-label"><i class="las la-exclamation-triangle"></i> Alert Qty</label>
                                            <input type="number" name="alert_quantity" class="pz-control" value="0" min="0">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="pz-label"><i class="las la-balance-scale"></i> Product Unit Type <span class="pz-req">*</span></label>
                                            <div class="pz-unit-selector">
                                                <div class="pz-unit-opt">
                                                    <input type="radio" name="unit_type" value="kg" id="unit_kg">
                                                    <label for="unit_kg">
                                                        <i class="las la-weight-hanging"></i>
                                                        <span class="pz-unit-name">KG</span>
                                                        <small>Weight</small>
                                                    </label>
                                                </div>
                                                <div class="pz-unit-opt">
                                                    <input type="radio" name="unit_type" value="piece" id="unit_piece" checked>
                                                    <label for="unit_piece">
                                                        <i class="las la-cubes"></i>
                                                        <span class="pz-unit-name">Pieces</span>
                                                        <small>Count</small>
                                                    </label>
                                                </div>
                                                <div class="pz-unit-opt">
                                                    <input type="radio" name="unit_type" value="pound" id="unit_pound">
                                                    <label for="unit_pound">
                                                        <i class="las la-birthday-cake"></i>
                                                        <span class="pz-unit-name">Pound</span>
                                                        <small>Cake</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="pz-label"><i class="las la-sticky-note"></i> Note</label>
                                            <textarea name="note" class="pz-control" rows="2" placeholder="Additional notes..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- ============ STEP 2: IMAGE + VARIANTS ============ --}}
                                <div class="pz-panel" data-panel="2">
                                    <div class="pz-panel-head">
                                        <h4><span class="pz-num">2</span> Product Image, Sizes & Pricing</h4>
                                        <p>Upload a photo and add different sizes/variants with prices.</p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="pz-label"><i class="las la-image"></i> Product Image</label>
                                            <div class="pz-img-drop" id="pzImgDrop">
                                                <input type="file" id="imageInput" name="image" class="d-none" accept="image/*">
                                                <div class="pz-img-placeholder" id="pzImgPlaceholder">
                                                    <i class="las la-cloud-upload-alt"></i>
                                                    <span>Click to upload</span>
                                                </div>
                                                <img id="preview" src="" alt="" style="display:none;">
                                                <button type="button" class="pz-clear-img" id="clearImageBtn" style="display:none;">&times;</button>
                                            </div>
                                            <div class="pz-hint">JPG, PNG, WebP. Max 2MB.</div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="pz-label mb-0"><i class="las la-layer-group"></i> Sizes / Variants <span class="unit-badge" id="selectedUnitBadge">Piece</span></div>
                                                <button type="button" class="pz-add-variant-btn" id="addVariantBtn"><i class="las la-plus"></i> Add Size</button>
                                            </div>
                                            <p class="pz-hint mb-3"><i class="las la-info-circle"></i> <span id="variantHelpText">Add different sizes and prices. Example: 1 Pound = Rs 1000, 2 Pound = Rs 1500</span></p>
                                            <div id="variantsContainer"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ============ STEP 3: RECIPE / BOM ============ --}}
                                <div class="pz-panel" data-panel="3">
                                    <div class="pz-panel-head">
                                        <h4><span class="pz-num">3</span> Raw Material Recipe (Bill of Materials)</h4>
                                        <p>Specify raw materials consumed per unit of this product.</p>
                                    </div>
                                    <div class="pz-bom-card">
                                        <div class="pz-bom-head">
                                            <span class="pz-bom-title"><i class="las la-list-ul"></i> Recipe Items</span>
                                            <button type="button" class="pz-btn pz-btn-add" id="addBomRow"><i class="las la-plus"></i> Add Raw Material</button>
                                        </div>
                                        <div class="pz-bom-table-wrap">
                                            <table class="table pz-bom-table" id="bomTable">
                                                <thead>
                                                    <tr>
                                                        <th width="50%">Raw Material</th>
                                                        <th width="35%">Qty Required per Unit</th>
                                                        <th width="15%" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="bomBody">
                                                    <tr>
                                                        <td>
                                                            <select name="raw_material_id[]" class="form-select bom-material-select">
                                                                <option value="">Select Raw Material...</option>
                                                                @if(isset($rawMaterials))
                                                                @foreach($rawMaterials as $rm)
                                                                <option value="{{ $rm->id }}">{{ $rm->name }} ({{ $rm->unit }})</option>
                                                                @endforeach
                                                                @endif
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.001" min="0" name="qty_per_unit[]" class="form-control bom-qty-input" value="0" placeholder="e.g. 0.250">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn pz-bom-remove remove-bom-row" title="Remove"><i class="las la-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="pz-bom-hint"><i class="las la-keyboard"></i> Press <kbd>Enter</kbd> in a row to add another raw material</div>
                                    </div>
                                </div>

                                {{-- ============ STEP 4: REVIEW ============ --}}
                                <div class="pz-panel" data-panel="4">
                                    <div class="pz-panel-head">
                                        <h4><span class="pz-num">4</span> Review & Confirm</h4>
                                        <p>Double-check everything before saving your product.</p>
                                    </div>

                                    <div class="pz-review-summary">
                                        <div class="pz-review-hero">
                                            <div class="pz-review-hero-left">
                                                <span class="pz-review-hero-icon"><i class="las la-box-open"></i></span>
                                                <div>
                                                    <div class="pz-review-hero-name" id="revName">—</div>
                                                    <div class="pz-review-hero-meta" id="revHeroMeta">—</div>
                                                </div>
                                            </div>
                                            <div class="pz-review-hero-stats">
                                                <div class="pz-review-stat">
                                                    <span class="pz-review-stat-num" id="revVariantCount">0</span>
                                                    <span class="pz-review-stat-lbl">Variants</span>
                                                </div>
                                                <div class="pz-review-stat">
                                                    <span class="pz-review-stat-num" id="revMaterialCount">0</span>
                                                    <span class="pz-review-stat-lbl">Materials</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="pz-review-card">
                                                <div class="pz-review-card-head">
                                                    <i class="las la-tags"></i> Product Details
                                                </div>
                                                <table class="pz-review-table">
                                                    <tr><th>Category</th><td id="revCategory">—</td></tr>
                                                    <tr><th>Sub Category</th><td id="revSubcat">—</td></tr>
                                                    <tr><th>Brand</th><td id="revBrand">—</td></tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="pz-review-card">
                                                <div class="pz-review-card-head">
                                                    <i class="las la-sliders-h"></i> Settings
                                                </div>
                                                <table class="pz-review-table">
                                                    <tr><th>Alert Qty</th><td id="revAlert">—</td></tr>
                                                    <tr><th>Unit Type</th><td id="revUnit">—</td></tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pz-review-card mt-3">
                                        <div class="pz-review-card-head">
                                            <i class="las la-sitemap"></i> Sizes / Variants
                                        </div>
                                        <div id="revVariants" class="pz-review-chip-wrap pz-hint">No variants added.</div>
                                    </div>

                                    <div class="pz-review-card mt-3">
                                        <div class="pz-review-card-head">
                                            <i class="las la-cubes"></i> Recipe (Bill of Materials)
                                        </div>
                                        <div id="revBom" class="pz-review-chip-wrap pz-hint">No raw materials added.</div>
                                    </div>
                                </div>

                            </div>

                            {{-- NAV BUTTONS --}}
                            <div class="pz-nav">
                                <button type="button" class="pz-btn pz-btn-prev" id="pzPrevBtn" style="visibility:hidden;"><i class="las la-arrow-left"></i> Previous</button>
                                <div style="display:flex;gap:.6rem;">
                                    <button type="button" class="pz-btn pz-btn-next" id="pzNextBtn">Next <i class="las la-arrow-right"></i></button>
                                    <button type="submit" class="pz-btn pz-btn-submit" id="submitProductBtn" style="display:none;"><i class="las la-check-circle"></i> Save Product</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div><!-- bodywrapper__inner end -->
            </div><!-- body-wrapper end -->
        </div>
    </div>

    {{-- category modal  --}}
    <div id="categoryModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span>Add Category</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i class="las la-times"></i></button>
                </div>
                <form action="{{ route('manual.category') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="redirect_url" value="{{ route('product') }}">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary h-45 w-100">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Subcategory modal  --}}
    <div id="subcategoryModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span>Add Subcategory</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i class="las la-times"></i></button>
                </div>
                <form action="{{ route('manual.subcategory') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Category Name</label>
                            <select name="category_id" class="form-select">
                                @foreach ($categories as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sub-Category Name</label>
                            <input type="text" name="sub_category" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary h-45 w-100">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- brand modal --}}
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span>Add Brand</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i class="las la-times"></i></button>
                </div>
                <form action="{{ route('manual.Brand') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary h-45 w-100">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')

<script>
$(document).ready(function() {
    $('.brand-select').select2({
        placeholder: "Select Brand",
        allowClear: true,
        width: '100%'
    });
});
</script>

<script>
/* ===================== WIZARD NAVIGATION ===================== */
(function() {
    const steps = document.querySelectorAll('.pz-step');
    const panels = document.querySelectorAll('.pz-panel');
    const stepsWrap = document.getElementById('pzSteps');
    const prevBtn = document.getElementById('pzPrevBtn');
    const nextBtn = document.getElementById('pzNextBtn');
    const submitBtn = document.getElementById('submitProductBtn');
    const form = document.getElementById('productForm');
    let currentStep = 1;
    const TOTAL_STEPS = 4;

    function goTo(step) {
        currentStep = step;
        // Update progress dots
        steps.forEach(function(s) {
            const n = parseInt(s.dataset.step);
            s.classList.remove('active', 'done');
            if (n < currentStep) s.classList.add('done');
            if (n === currentStep) s.classList.add('active');
        });
        stepsWrap.setAttribute('data-progress', currentStep);
        // Show panel
        panels.forEach(function(p) {
            p.classList.toggle('active', parseInt(p.dataset.panel) === currentStep);
        });
        // Buttons
        prevBtn.style.visibility = currentStep === 1 ? 'hidden' : 'visible';
        if (currentStep === TOTAL_STEPS) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'inline-flex';
            buildReview();
        } else {
            nextBtn.style.display = 'inline-flex';
            submitBtn.style.display = 'none';
        }
        // Scroll to top of wizard
        document.querySelector('.pz-body').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function validateStep(step) {
        let valid = true;
        const panel = document.querySelector('.pz-panel[data-panel="' + step + '"]');
        panel.querySelectorAll('input, select').forEach(function(el) {
            if (el.hasAttribute('required') && !el.value.trim()) {
                el.classList.add('is-invalid');
                valid = false;
            } else {
                el.classList.remove('is-invalid');
            }
        });
        // Variants step: at least one variant with a name & price
        if (step === 2) {
            const rows = document.querySelectorAll('.pz-variant-row');
            let anyValid = false;
            rows.forEach(function(r) {
                const name = r.querySelector('input[name="variant_name[]"]');
                const price = r.querySelector('input[name="variant_price[]"]');
                if (name && name.value.trim() && price && parseFloat(price.value) > 0) anyValid = true;
            });
            if (!anyValid) {
                Swal.fire({ icon: 'warning', title: 'Missing Variants', text: 'Please add at least one size/variant with a name and sale price.', confirmButtonColor: '#6d5cff' });
                valid = false;
            }
        }
        return valid;
    }

    nextBtn.addEventListener('click', function() {
        if (!validateStep(currentStep)) return;
        if (currentStep < TOTAL_STEPS) goTo(currentStep + 1);
    });

    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) goTo(currentStep - 1);
    });

    // Clickable progress dots
    steps.forEach(function(s) {
        s.addEventListener('click', function() {
            const target = parseInt(s.dataset.step);
            // only allow going back or to next consecutive validated step
            if (target < currentStep) { goTo(target); return; }
            if (target === currentStep + 1 && validateStep(currentStep)) { goTo(target); }
        });
    });

    // Realtime invalid feedback
    form.addEventListener('input', function(e) {
        if (e.target.classList) e.target.classList.remove('is-invalid');
    });
    form.addEventListener('change', function(e) {
        if (e.target.classList) e.target.classList.remove('is-invalid');
    });

    window.pzGoTo = goTo;
})();
</script>

<script>
/* ===================== VARIANTS ===================== */
(function() {
    let variantIndex = 0;
    const container = document.getElementById('variantsContainer');
    const addBtn = document.getElementById('addVariantBtn');
    const unitRadios = document.querySelectorAll('input[name="unit_type"]');
    const unitBadge = document.getElementById('selectedUnitBadge');
    const helpText = document.getElementById('variantHelpText');

    function getUnitType() {
        const checked = document.querySelector('input[name="unit_type"]:checked');
        return checked ? checked.value : 'piece';
    }

    function getSizeLabel(unitType) {
        switch(unitType) {
            case 'kg': return 'Weight (Grams)';
            case 'pound': return 'Size (Pound)';
            case 'piece': return 'Quantity';
            default: return 'Size';
        }
    }

    function getPlaceholder(unitType) {
        switch(unitType) {
            case 'kg': return 'e.g. 250, 500, 1000';
            case 'pound': return 'e.g. 1, 2, 3';
            case 'piece': return 'e.g. 6, 12, 24';
            default: return 'Enter value';
        }
    }

    function getVariantPlaceholder(unitType) {
        switch(unitType) {
            case 'kg': return 'e.g. 250g, Half KG, 1 KG';
            case 'pound': return 'e.g. 1 Pound, 2 Pound';
            case 'piece': return 'e.g. Box of 6, Box of 12';
            default: return 'Variant Name';
        }
    }

    function getHelpText(unitType) {
        switch(unitType) {
            case 'kg': return 'Add weight in Grams. It will auto-convert to KG (e.g. 250 = 0.250 KG)';
            case 'pound': return 'Add different sizes and prices. Example: 1 Pound = Rs 1000, 2 Pound = Rs 1500';
            case 'piece': return 'Add different piece quantities and prices. Example: Box of 6 = Rs 500, Box of 12 = Rs 900';
            default: return '';
        }
    }

    function updateUnitBadge(unitType) {
        unitBadge.textContent = unitType.toUpperCase();
        unitBadge.className = 'unit-badge ' + unitType;
        helpText.textContent = getHelpText(unitType);
    }

    unitRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            updateUnitBadge(this.value);
            document.querySelectorAll('.variant-size-label').forEach(function(el) {
                el.textContent = getSizeLabel(radio.value);
            });
            document.querySelectorAll('.variant-size-input').forEach(function(el) {
                el.placeholder = getPlaceholder(radio.value);
            });
            document.querySelectorAll('.variant-size-unit-hidden').forEach(input => input.value = radio.value);
            const stockCols = document.querySelectorAll('.variant-stock-col');
            stockCols.forEach(col => {
                col.style.display = radio.value === 'kg' ? 'none' : 'block';
            });
            document.querySelectorAll('.variant-name-input').forEach(function(el) {
                el.placeholder = getVariantPlaceholder(radio.value);
            });
            document.querySelectorAll('.variant-size-input').forEach(function(el) {
                el.dispatchEvent(new Event('input'));
            });
        });
    });

    addBtn.addEventListener('click', function() {
        addVariantRow();
    });

    function addVariantRow() {
        const unitType = getUnitType();
        const idx = variantIndex++;
        const isFirst = container.children.length === 0;

        const row = document.createElement('div');
        row.className = 'pz-variant-row';
        row.innerHTML = `
            <button type="button" class="pz-remove-variant" onclick="this.closest('.pz-variant-row').remove(); updateVariantNumbers();">&times;</button>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="variant-number">${container.children.length + 1}</span>
                <label class="default-radio-label mb-0">
                    <input type="radio" name="variant_default" value="${idx}" ${isFirst ? 'checked' : ''}>
                    <span>Default Variant</span>
                </label>
            </div>
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="pz-label small fw-bold">Variant Name <span class="pz-req">*</span></label>
                    <input type="text" name="variant_name[]" class="pz-control variant-name-input"
                        placeholder="${getVariantPlaceholder(unitType)}" required>
                </div>
                <div class="col-md-2">
                    <label class="pz-label small fw-bold variant-size-label">${getSizeLabel(unitType)}</label>
                    <input type="number" name="variant_size_value[]" class="pz-control variant-size-input"
                        placeholder="${getPlaceholder(unitType)}" step="0.01" min="0" value="0">
                    <input type="hidden" name="variant_size_unit[]" class="variant-size-unit-hidden" value="${unitType}">
                    <div class="gram-display" id="gramDisplay_${idx}"></div>
                </div>
                <div class="col-md-3">
                    <label class="pz-label small fw-bold">Sale Price <span class="pz-req">*</span></label>
                    <input type="number" name="variant_price[]" class="pz-control"
                        placeholder="0" step="0.01" min="0" required>
                </div>
                <div class="col-md-2">
                    <label class="pz-label small fw-bold">Purchase Price</label>
                    <input type="number" name="variant_cost_price[]" class="pz-control"
                        placeholder="0" step="0.01" min="0" value="0">
                </div>
                <div class="col-md-2 variant-stock-col" style="display: ${unitType === 'kg' ? 'none' : 'block'}">
                    <label class="pz-label small fw-bold">Stock</label>
                    <input type="number" name="variant_stock[]" class="pz-control"
                        placeholder="0" step="0.01" min="0" value="0">
                </div>
            </div>
            <div class="variant-barcode-row">
                <label class="pz-label small fw-bold mb-1"><i class="las la-barcode"></i> Barcode</label>
                <div class="pz-barcode-group">
                    <input type="text" name="variant_barcode[]" class="pz-control variant-barcode-input"
                        placeholder="6-digit barcode">
                    <button type="button" class="pz-barcode-btn variant-barcode-btn" title="Generate barcode"><i class="la la-barcode"></i></button>
                </div>
            </div>
        `;

        container.appendChild(row);

        const sizeInput = row.querySelector('.variant-size-input');
        const gramDisplay = row.querySelector('.gram-display');

        sizeInput.addEventListener('input', function() {
            const currentUnit = getUnitType();
            if (currentUnit === 'kg') {
                const gramsValue = parseFloat(this.value) || 0;
                const kg = gramsValue / 1000;
                gramDisplay.textContent = '= ' + kg.toFixed(3) + ' KG';
            } else {
                gramDisplay.textContent = '';
            }
        });

        sizeInput.dispatchEvent(new Event('input'));
    }

    window.updateVariantNumbers = function() {
        document.querySelectorAll('.pz-variant-row .variant-number').forEach(function(el, i) {
            el.textContent = i + 1;
        });
    };

    addVariantRow();
    updateUnitBadge(getUnitType());
})();
</script>

<script>
/* ===================== FORM GUARD ===================== */
(function() {
    const form = document.getElementById('productForm');

    form.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        const el = e.target;
        const tag = el.tagName.toLowerCase();
        if (tag === 'textarea') return;
        if (el.classList && el.classList.contains('select2-search__field')) return;
        e.preventDefault();
    });

    form.addEventListener('submit', function(e) {
        const byButton = e.submitter && e.submitter.id === 'submitProductBtn';
        if (!byButton) {
            e.preventDefault();
        }
    });
})();
</script>

<script>
/* ===================== BARCODE (per variant) ===================== */
function generateVariantBarcode(inputEl) {
    const currentValue = inputEl.value.trim();
    const url = currentValue !== ""
        ? '/generate-barcode-image?code=' + currentValue
        : '{{ route("generate-barcode-image") }}';
    fetch(url)
        .then(res => { if (!res.ok) throw new Error('Server error'); return res.json(); })
        .then(data => {
            inputEl.value = data.barcode_number;
        })
        .catch(err => console.error('Barcode error:', err));
}

document.getElementById('variantsContainer').addEventListener('click', function(e) {
    const btn = e.target.closest('.variant-barcode-btn');
    if (btn) {
        const row = btn.closest('.pz-variant-row');
        generateVariantBarcode(row.querySelector('.variant-barcode-input'));
    }
});
</script>

<script>
/* ===================== IMAGE PREVIEW ===================== */
(function() {
    const imageInput = document.getElementById('imageInput');
    const preview = document.getElementById('preview');
    const clearImageBtn = document.getElementById('clearImageBtn');
    const dropZone = document.getElementById('pzImgDrop');
    const placeholder = document.getElementById('pzImgPlaceholder');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
                clearImageBtn.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }
    });

    dropZone.addEventListener('click', function() {
        imageInput.click();
    });

    clearImageBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        preview.src = "";
        preview.style.display = 'none';
        placeholder.style.display = 'block';
        clearImageBtn.style.display = 'none';
        imageInput.value = "";
    });

    // Category-Subcategory dropdown
    $('#category-dropdown').on('change', function() {
        var categoryId = $(this).val();
        if (categoryId) {
            $.ajax({
                url: '/get-subcategories/' + categoryId,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#subcategory-dropdown').empty();
                    $('#subcategory-dropdown').append('<option selected disabled>Select Subcategory</option>');
                    $.each(data, function(key, value) {
                        $('#subcategory-dropdown').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        } else {
            $('#subcategory-dropdown').empty();
        }
    });

    // BOM (Recipe) Row Handlers
    function addBomRow() {
        var row = $('#bomBody tr:first').clone();
        row.find('select').val('');
        row.find('input').val('0');
        $('#bomBody').append(row);
    }

    $('#addBomRow').click(addBomRow);

    $(document).on('keydown', '#bomBody select, #bomBody input', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addBomRow();
            $('#bomBody tr:last select').trigger('focus');
        }
    });

    $(document).on('click', '.remove-bom-row', function() {
        if ($('#bomBody tr').length > 1) {
            $(this).closest('tr').remove();
        }
    });
})();
</script>

<script>
/* ===================== REVIEW SUMMARY ===================== */
function buildReview() {
    const name = document.getElementById('pzProductName').value.trim();
    document.getElementById('revName').textContent = name || '—';

    const catSel = document.getElementById('category-dropdown');
    const catText = catSel.selectedOptions[0] ? catSel.selectedOptions[0].textContent.trim() : '—';
    document.getElementById('revCategory').textContent = catText;

    const subSel = document.getElementById('subcategory-dropdown');
    const subText = subSel.selectedOptions[0] ? subSel.selectedOptions[0].textContent.trim() : '—';
    document.getElementById('revSubcat').textContent = subText;

    const brandSel = document.querySelector('.brand-select');
    const brandText = brandSel && brandSel.selectedOptions[0] ? brandSel.selectedOptions[0].textContent.trim() : '—';
    document.getElementById('revBrand').textContent = brandText;

    const alertQty = document.querySelector('input[name="alert_quantity"]').value || '0';
    document.getElementById('revAlert').textContent = alertQty;

    const unitChecked = document.querySelector('input[name="unit_type"]:checked');
    const unitMap = { kg: 'KG (Weight)', piece: 'Pieces', pound: 'Pound (Bakery)' };
    const unitText = unitChecked ? (unitMap[unitChecked.value] || unitChecked.value) : '—';
    document.getElementById('revUnit').textContent = unitText;

    // Hero
    const heroMetaParts = [catText, brandText].filter(function(v) { return v !== '—'; });
    document.getElementById('revHeroMeta').textContent = heroMetaParts.length ? heroMetaParts.join('  •  ') : 'No category or brand selected';
    document.getElementById('revVariantCount').textContent = document.querySelectorAll('.pz-variant-row').length;
    document.getElementById('revMaterialCount').textContent = document.querySelectorAll('#bomBody tr').length;

    // Variants
    const variantsBox = document.getElementById('revVariants');
    const rows = document.querySelectorAll('.pz-variant-row');
    if (rows.length === 0) {
        variantsBox.innerHTML = '<span class="pz-hint">No variants added.</span>';
    } else {
        let html = '';
        rows.forEach(function(r, i) {
            const vName = r.querySelector('input[name="variant_name[]"]').value;
            const vSize = r.querySelector('input[name="variant_size_value[]"]').value;
            const vPrice = r.querySelector('input[name="variant_price[]"]').value;
            const vStock = r.querySelector('input[name="variant_stock[]"]').value;
            const vBarcode = r.querySelector('input[name="variant_barcode[]"]').value;
            html += '<span class="pz-review-chip">' + (i+1) + '. ' + (vName || '?') +
                (vSize && parseFloat(vSize) > 0 ? ' (' + vSize + ')' : '') +
                ' — Rs ' + (vPrice || 0) +
                (vStock && parseFloat(vStock) > 0 ? ' | Stock: ' + vStock : '') +
                (vBarcode ? ' <span class="pz-review-mono">| BC: ' + vBarcode + '</span>' : '') + '</span>';
        });
        variantsBox.innerHTML = html;
    }

    // BOM
    const bomBox = document.getElementById('revBom');
    const bomRows = document.querySelectorAll('#bomBody tr');
    let bomHtml = '';
    bomRows.forEach(function(r) {
        const sel = r.querySelector('select[name="raw_material_id[]"]');
        const qty = r.querySelector('input[name="qty_per_unit[]"]');
        if (sel && sel.value && qty) {
            const label = sel.selectedOptions[0] ? sel.selectedOptions[0].textContent.trim() : 'Material';
            bomHtml += '<span class="pz-review-chip">' + label + ' × ' + (parseFloat(qty.value) || 0) + '</span>';
        }
    });
    bomBox.innerHTML = bomHtml || '<span class="pz-hint">No raw materials added.</span>';
}
</script>

@endsection
