<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Epilogue:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

    :root {
        --teal: #0d9488;
        --teal-light: #ccfbf1;
        --teal-mid: #14b8a6;
        --teal-dim: rgba(13, 148, 136, 0.08);
        --amber: #d97706;
        --amber-light: #fef3c7;
        --ink: #0f172a;
        --ink-mid: #334155;
        --muted: #64748b;
        --border: #e2e8f0;
        --bg: #f8fafc;
        --font-disp: 'Playfair Display', serif;
        --font-body: 'Epilogue', sans-serif;
        --font-mono: 'JetBrains Mono', monospace;
        --radius: .875rem;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
        font-family: var(--font-body);
    }

    .mono {
        font-family: var(--font-mono) !important;
    }

    .disp {
        font-family: var(--font-disp) !important;
    }

    .hidden {
        display: none !important;
    }


    .table-wrap {
        width: 100%;
        overflow-x: auto;
        /* scroll horizontal si besoin */
        overflow-y: auto;
        /* scroll vertical */
        max-height: 400px;
        /* IMPORTANT */
    }

    .table-wrap {
        max-height: calc(100vh - 300px);
    }

    /* ── PAGE HEADER ── */
    .ph {
        margin-bottom: 1.5rem;
    }

    .ph-title {
        font-family: var(--font-disp);
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--ink);
        letter-spacing: -.02em;
    }

    .ph-sub {
        font-size: .85rem;
        color: var(--muted);
        margin-top: .2rem;
    }

    /* ── BREADCRUMB ── */
    .bc {
        display: flex;
        align-items: center;
        gap: .4rem;
        font-size: .8rem;
        color: var(--muted);
        margin-bottom: 1.25rem;
    }

    .bc a {
        color: var(--muted);
        text-decoration: none;
        transition: color .15s;
    }

    .bc a:hover {
        color: var(--ink);
    }

    .bc-sep {
        opacity: .4;
    }

    .bc-cur {
        color: var(--ink);
        font-weight: 500;
    }

    /* ── ALERTS ── */
    .alert {
        border-radius: .625rem;
        padding: .875rem 1.25rem;
        font-size: .875rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .alert-s {
        background: #d1fae5;
        border: 1px solid #6ee7b7;
        color: #065f46;
    }

    .alert-e {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #991b1b;
        flex-direction: column;
        align-items: flex-start;
    }

    .alert-e ul {
        margin: .25rem 0 0 1rem;
        padding: 0;
    }

    /* ── FILTER BAR ── */
    .filter-bar {
        background: white;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
    }

    .filter-bar-title {
        font-size: .72rem;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .05em;
        flex-shrink: 0;
    }

    .filter-sep {
        width: 1px;
        height: 24px;
        background: var(--border);
        flex-shrink: 0;
    }

    .filter-spacer {
        flex: 1;
    }

    /* ── CARD ── */
    .t-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .t-card+.t-card {
        margin-top: 1.25rem;
    }

    .t-header {
        padding: 1rem 1.375rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        flex-wrap: wrap;
    }

    .t-title {
        font-size: .9375rem;
        font-weight: 600;
        color: var(--ink);
    }

    .t-sub {
        font-size: .72rem;
        color: var(--muted);
        margin-top: .1rem;
    }

    .t-body {
        padding: 1.25rem;
    }

    /* ── KPI GRID ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    @media (max-width: 768px) {
        .table-wrap {
            max-height: 300px;
        }
    }

    @media(max-width:900px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .kpi {
        background: white;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.25rem;
        position: relative;
        overflow: hidden;
        transition: box-shadow .2s;
    }

    .kpi:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, .06);
    }

    .kpi-bar {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
    }

    .kpi-icon {
        width: 40px;
        height: 40px;
        border-radius: .625rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: .875rem;
    }

    .kpi-val {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--ink);
        letter-spacing: -.04em;
        font-family: var(--font-mono);
        line-height: 1;
    }

    .kpi-lbl {
        font-size: .75rem;
        color: var(--muted);
        font-weight: 500;
        margin-top: .25rem;
    }

    .kpi-note {
        font-size: .72rem;
        font-weight: 600;
        margin-top: .5rem;
    }

    .kpi-note.up {
        color: #10b981;
    }

    .kpi-note.down {
        color: #ef4444;
    }

    .kpi-note.flat {
        color: var(--amber);
    }

    /* ── TABLE ── */
    .t-table {
        width: 100%;
        border-collapse: collapse;
    }

    .t-table th {
        background: #f8fafc;
        padding: .7rem 1.25rem;
        text-align: left;
        font-size: .68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--muted);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    .t-table td {
        padding: .875rem 1.25rem;
        border-bottom: 1px solid #f8fafc;
        font-size: .8375rem;
        color: var(--ink-mid);
        vertical-align: middle;
    }

    .t-table tr:last-child td {
        border-bottom: none;
    }

    .t-table tr:hover td {
        background: #fafbfc;
    }

    .t-table.compact td {
        padding: .625rem 1rem;
    }

    .t-table.compact th {
        padding: .5rem 1rem;
    }

    /* ── EMPTY STATE ── */
    .empty {
        text-align: center;
        padding: 3.5rem 1rem;
    }

    .empty-icon {
        font-size: 2.5rem;
        margin-bottom: .75rem;
        opacity: .5;
    }

    .empty-text {
        font-size: .875rem;
        color: var(--muted);
    }

    /* ── BADGES ── */
    .badge {
        display: inline-block;
        padding: .2rem .6rem;
        border-radius: 9999px;
        font-size: .7rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .b-green {
        background: #dcfce7;
        color: #166534;
    }

    .b-teal {
        background: var(--teal-light);
        color: var(--teal);
    }

    .b-amber {
        background: var(--amber-light);
        color: var(--amber);
    }

    .b-blue {
        background: #dbeafe;
        color: #1e40af;
    }

    .b-red {
        background: #fee2e2;
        color: #991b1b;
    }

    .b-gray {
        background: #f3f4f6;
        color: #374151;
    }

    .b-purple {
        background: #ede9fe;
        color: #5b21b6;
    }

    .b-pink {
        background: #fce7f3;
        color: #9d174d;
    }

    /* ── AVATAR ── */
    .av {
        border-radius: .5rem;
        object-fit: cover;
        flex-shrink: 0;
    }

    .av-sm {
        width: 34px;
        height: 34px;
    }

    .av-md {
        width: 48px;
        height: 48px;
    }

    .av-lg {
        width: 72px;
        height: 72px;
    }

    /* ── BUTTONS ── */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        border-radius: .5rem;
        font-size: .8375rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all .2s;
        padding: .5rem 1.125rem;
        text-decoration: none;
    }

    .btn-p {
        background: var(--teal);
        color: white;
    }

    .btn-p:hover {
        background: #0f766e;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 148, 136, .25);
        color: white;
    }

    .btn-o {
        background: white;
        color: var(--ink-mid);
        border: 1px solid var(--border);
    }

    .btn-o:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: var(--ink-mid);
    }

    .btn-d {
        background: #ef4444;
        color: white;
    }

    .btn-d:hover {
        background: #dc2626;
        color: white;
    }

    .btn-sm {
        padding: .3rem .7rem;
        font-size: .75rem;
    }

    .btn-xs {
        padding: .2rem .5rem;
        font-size: .7rem;
    }

    /* ── FORM ── */
    .f-label {
        font-size: .72rem;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .05em;
        display: block;
        margin-bottom: .35rem;
    }

    .f-input {
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: .5rem;
        padding: .625rem .875rem;
        font-size: .875rem;
        color: var(--ink-mid);
        width: 100%;
        transition: all .2s;
    }

    .f-input:focus {
        outline: none;
        border-color: var(--teal);
        background: white;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, .08);
    }

    .f-input::placeholder {
        color: #94a3b8;
    }

    .f-input.inline {
        width: auto;
    }

    .f-group {
        margin-bottom: 1rem;
    }

    .f-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .875rem;
    }

    .f-grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: .875rem;
    }

    /* ── PAGINATION ── */
    .pager {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .75rem 1.25rem;
        border-top: 1px solid #f1f5f9;
    }

    .pager-info {
        font-size: .78rem;
        color: var(--muted);
    }

    .pager-btns {
        display: flex;
        gap: .375rem;
    }

    /* ── PROGRESS ── */
    .prog {
        background: #e2e8f0;
        height: 6px;
        border-radius: 99px;
        overflow: hidden;
    }

    .prog-fill {
        height: 100%;
        border-radius: 99px;
    }

    /* ── MODAL ── */
    .modal-bg {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .45);
        z-index: 900;
        align-items: center;
        justify-content: center;
    }

    .modal-bg.open {
        display: flex;
    }

    .modal {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
    }

    .modal-lg {
        max-width: 800px;
    }

    .modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: #f1f5f9;
        border: none;
        cursor: pointer;
        color: var(--muted);
        font-size: 1rem;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .2s;
    }

    .modal-close:hover {
        background: #e2e8f0;
        color: var(--ink);
    }

    .modal-title {
        font-family: var(--font-disp);
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 1.25rem;
    }

    /* ── INFO GRID ── */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .info-item {
        padding: .875rem 1.375rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-item:nth-child(odd) {
        border-right: 1px solid #f1f5f9;
    }

    .info-item.full {
        grid-column: span 2;
        border-right: none;
    }

    .t-scroll {
        max-height: 260px;
        /* ajuste selon ton design */
        overflow-y: auto;
        overflow-x: hidden;
        border-radius: .5rem;
    }

    .t-scroll {
        position: relative;
    }

    .t-scroll::after {
        content: "";
        position: sticky;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 20px;
        background: linear-gradient(to bottom, transparent, white);
        pointer-events: none;
    }

    /* Scroll plus joli */
    .t-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .t-scroll::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }

    .info-key {
        font-size: .68rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: .06em;
    }

    .info-val {
        font-size: .875rem;
        font-weight: 500;
        color: var(--ink);
        margin-top: .2rem;
    }

    /* ── TABS (mini) ── */
    .mini-tabs {
        display: flex;
        gap: .25rem;
        background: #f1f5f9;
        border-radius: .5rem;
        padding: .25rem;
        margin-bottom: 1.25rem;
    }

    .mini-tab {
        flex: 1;
        text-align: center;
        padding: .45rem .75rem;
        border-radius: .375rem;
        font-size: .8rem;
        font-weight: 500;
        color: var(--muted);
        background: none;
        border: none;
        cursor: pointer;
        transition: all .2s;
    }

    .mini-tab.active {
        background: white;
        color: var(--ink);
        font-weight: 600;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
    }

    /* ── GRADE PILL ── */
    .gp {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 26px;
        padding: 0 6px;
        border-radius: .35rem;
        font-size: .78rem;
        font-weight: 700;
        font-family: var(--font-mono);
    }

    .gp-A {
        background: #d1fae5;
        color: #065f46;
    }

    .gp-B {
        background: #dbeafe;
        color: #1e40af;
    }

    .gp-C {
        background: #fef3c7;
        color: #92400e;
    }

    .gp-D {
        background: #fee2e2;
        color: #991b1b;
    }

    @media(max-width:768px) {

        .f-grid-2,
        .f-grid-3 {
            grid-template-columns: 1fr;
        }

        .two-col {
            grid-template-columns: 1fr !important;
        }

        .three-col {
            grid-template-columns: 1fr !important;
        }
    }
</style>
