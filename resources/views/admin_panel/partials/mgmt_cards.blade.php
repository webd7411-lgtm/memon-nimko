<style>
    /* ═══════ MANAGEMENT LIST — PREMIUM MOBILE CARDS ═══════ */
    /* Desktop: fixed layout = table NEVER overflows / no horizontal scroll */
    .rp-mgmt .table-responsive { overflow-x: hidden; }
    .rp-mgmt table.rp-table { table-layout: fixed; width: 100% !important; }
    .rp-mgmt .rp-table thead th,
    .rp-mgmt .rp-table tbody td { white-space: normal !important; overflow-wrap: anywhere; word-break: normal; }

    /* Cards (hidden on desktop) */
    .rp-ucards { display: none; }

    .rp-ucard {
        background: #fff;
        border: 1.5px solid #e9edf2;
        border-radius: 14px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 8px 24px rgba(0, 0, 0, .05);
        margin-bottom: .85rem;
        overflow: hidden;
    }

    .rp-uc-top {
        display: flex;
        align-items: center;
        gap: .7rem;
        padding: .8rem .9rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e9edf2;
    }

    .rp-uc-av {
        flex: 0 0 auto;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--rp-accent, #7c3aed) 0%, var(--rp-accent-2, #4338ca) 100%);
        color: #fff;
        font-weight: 800;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .12);
    }

    .rp-uc-idf { min-width: 0; display: flex; flex-direction: column; gap: 2px; }
    .rp-uc-nm { font-size: .95rem; font-weight: 800; color: #0f172a; word-break: normal; overflow-wrap: anywhere; line-height: 1.25; }
    .rp-uc-em { font-size: .75rem; color: #64748b; font-weight: 600; word-break: normal; overflow-wrap: anywhere; }

    .rp-uc-ix {
        flex: 0 0 auto;
        margin-left: auto;
        font-size: .72rem;
        font-weight: 800;
        color: var(--rp-accent, #7c3aed);
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: .2rem .65rem;
        white-space: nowrap;
    }

    .rp-uc-body { padding: .7rem .9rem 0; }

    .rp-uc-roles { display: flex; flex-wrap: wrap; gap: .35rem; margin-top: .55rem; }
    .rp-uc-roles .rp-chip { margin-right: 0; }

    .rp-uc-bal {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .6rem;
        margin: .7rem .9rem 0;
        padding: .6rem .8rem;
        background: #f8fafc;
        border: 1px dashed #d6deeb;
        border-radius: 10px;
    }
    .rp-uc-bal .bl { font-size: .62rem; font-weight: 800; text-transform: uppercase; letter-spacing: .4px; color: #64748b; }
    .rp-uc-bal b { font-size: .95rem; font-weight: 800; color: var(--rp-accent, #7c3aed); word-break: normal; overflow-wrap: anywhere; }

    .rp-uc-acts {
        display: flex;
        flex-wrap: wrap;
        gap: .45rem;
        padding: .7rem .9rem .9rem;
    }
    .rp-uc-acts .btn { flex: 1 1 auto; min-width: 0; white-space: normal; transition: transform .2s ease; }
    .rp-uc-acts .btn:hover { transform: translateY(-1px); }

    .rp-uc-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .6rem;
        padding: .7rem .9rem;
        color: #54657e;
        font-size: .8rem;
        font-weight: 600;
    }

    @media (max-width: 991.98px) {
        .rp-mgmt .table-responsive { display: none; }
        .rp-ucards { display: block; padding: .5rem .6rem 1rem; }
    }
</style>