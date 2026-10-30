<style>
    /* Attendance pages — shared styles (scoped to .att-page) */
    .att-page {
        --att-present: #16a34a;
        --att-late: #d97706;
        --att-absent: #dc2626;
        --att-accent: #2563eb;
        --att-track: rgba(100, 116, 139, .18);
        --att-line: var(--bs-border-color, #e5e7eb);
        --att-muted: var(--bs-secondary-color, #6b7280);
    }

    .att-page .att-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; }
    .att-page .att-head h2 { margin: 0 0 .25rem; font-weight: 650; letter-spacing: -.01em; }
    .att-page .att-head p { margin: 0; color: var(--att-muted); }

    .att-page .att-card { background: var(--bs-card-bg, #fff); border: 1px solid var(--att-line); border-radius: 12px; }
    .att-page .att-card-pad { padding: 1.25rem 1.5rem; }
    .att-page .att-card-title { font-size: 1rem; font-weight: 600; margin: 0 0 .15rem; }
    .att-page .att-card-sub { font-size: .85rem; color: var(--att-muted); margin: 0 0 1rem; }

    /* Form controls */
    .att-page .form-label { font-size: .8rem; font-weight: 600; color: var(--att-muted); margin-bottom: .3rem; }
    .att-page .form-control { border-radius: 8px; }
    .att-page .btn { border-radius: 8px; font-weight: 500; }

    /* Quick range chips */
    .att-page .att-chips { display: flex; flex-wrap: wrap; gap: .4rem; }
    .att-page .att-chip {
        padding: .25rem .75rem; border-radius: 999px; font-size: .8rem; text-decoration: none;
        border: 1px solid var(--att-line); color: var(--att-muted); background: transparent;
    }
    .att-page .att-chip:hover { border-color: var(--att-accent); color: var(--att-accent); }
    .att-page .att-chip.is-active { background: var(--att-accent); border-color: var(--att-accent); color: #fff; }
    .att-page .att-chip:focus-visible,
    .att-page .btn:focus-visible { outline: 2px solid var(--att-accent); outline-offset: 2px; }

    /* Stat tiles */
    .att-page .att-stat { display: flex; align-items: center; gap: .9rem; height: 100%; }
    .att-page .att-stat-icon {
        width: 44px; height: 44px; border-radius: 10px; display: grid; place-items: center; font-size: 1.15rem; flex: none;
    }
    .att-page .att-stat-label { font-size: .8rem; color: var(--att-muted); }
    .att-page .att-stat-value { font-size: 1.6rem; font-weight: 650; line-height: 1.1; font-variant-numeric: tabular-nums; }
    .att-page .tone-present { background: rgba(22, 163, 74, .12); color: var(--att-present); }
    .att-page .tone-late    { background: rgba(217, 119, 6, .14); color: var(--att-late); }
    .att-page .tone-absent  { background: rgba(220, 38, 38, .12); color: var(--att-absent); }
    .att-page .tone-neutral { background: rgba(37, 99, 235, .12); color: var(--att-accent); }

    /* Attendance ring */
    .att-page .att-ring {
        --p: 0; --ring: var(--att-present);
        width: 88px; height: 88px; border-radius: 50%; flex: none; position: relative; display: grid; place-items: center;
        background: conic-gradient(var(--ring) calc(var(--p) * 1%), var(--att-track) 0);
    }
    .att-page .att-ring::before { content: ""; position: absolute; inset: 9px; border-radius: 50%; background: var(--bs-card-bg, #fff); }
    .att-page .att-ring span { position: relative; font-weight: 650; font-size: 1.1rem; font-variant-numeric: tabular-nums; }
    .att-page .att-ring.is-warn { --ring: var(--att-late); }
    .att-page .att-ring.is-low  { --ring: var(--att-absent); }

    /* Table */
    .att-page .att-table { margin: 0; }
    .att-page .att-table thead th {
        font-size: .78rem; font-weight: 600; color: var(--att-muted); white-space: nowrap;
        border-bottom: 1px solid var(--att-line); padding: .75rem 1rem; background: transparent;
    }
    .att-page .att-table tbody td { padding: .85rem 1rem; border-color: var(--att-line); vertical-align: middle; }
    .att-page .att-table tbody tr:last-child td { border-bottom: 0; }
    .att-page .att-num { font-variant-numeric: tabular-nums; text-align: right; }
    .att-page .att-sub { display: block; font-size: .78rem; color: var(--att-muted); }

    /* Status pills */
    .att-page .att-pill { display: inline-flex; align-items: center; gap: .35rem; padding: .2rem .6rem; border-radius: 999px; font-size: .78rem; font-weight: 600; }
    .att-page .att-pill::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .att-page .att-pill.present { background: rgba(22, 163, 74, .12); color: var(--att-present); }
    .att-page .att-pill.late    { background: rgba(217, 119, 6, .14); color: var(--att-late); }
    .att-page .att-pill.absent  { background: rgba(220, 38, 38, .12); color: var(--att-absent); }
    .att-page .att-pill.none    { background: rgba(100, 116, 139, .14); color: var(--att-muted); }

    /* Split bar (present / late / absent) */
    .att-page .att-split { display: flex; height: 6px; width: 120px; border-radius: 999px; overflow: hidden; background: var(--att-track); }
    .att-page .att-split i { display: block; height: 100%; }
    .att-page .att-split .p { background: var(--att-present); }
    .att-page .att-split .l { background: var(--att-late); }
    .att-page .att-split .a { background: var(--att-absent); }

    /* Empty state */
    .att-page .att-empty { text-align: center; padding: 3rem 1rem; color: var(--att-muted); }
    .att-page .att-empty i { font-size: 2rem; display: block; margin-bottom: .5rem; opacity: .6; }
    .att-page .att-empty strong { display: block; color: var(--bs-body-color); margin-bottom: .15rem; }

    /* Segmented status control (mark attendance) */
    .att-page .att-seg { position: relative; display: inline-flex; border: 1px solid var(--att-line); border-radius: 9px; overflow: hidden; white-space: nowrap; }
    .att-page .att-seg input { position: absolute; opacity: 0; pointer-events: none; }
    .att-page .att-seg label { margin: 0; padding: .35rem .85rem; font-size: .82rem; font-weight: 500; cursor: pointer; color: var(--att-muted); border-right: 1px solid var(--att-line); user-select: none; }
    .att-page .att-seg label:last-of-type { border-right: 0; }
    .att-page .att-seg label:hover { background: rgba(100, 116, 139, .08); }
    .att-page .att-seg input[value="present"]:checked + label { background: rgba(22, 163, 74, .14); color: var(--att-present); font-weight: 600; }
    .att-page .att-seg input[value="late"]:checked + label    { background: rgba(217, 119, 6, .16); color: var(--att-late); font-weight: 600; }
    .att-page .att-seg input[value="absent"]:checked + label  { background: rgba(220, 38, 38, .14); color: var(--att-absent); font-weight: 600; }
    .att-page .att-seg input:focus-visible + label { outline: 2px solid var(--att-accent); outline-offset: -2px; }

    .att-page .att-avatar { width: 34px; height: 34px; border-radius: 50%; display: grid; place-items: center; flex: none; font-size: .75rem; font-weight: 600; background: rgba(37, 99, 235, .12); color: var(--att-accent); }
    .att-page .att-person { display: flex; align-items: center; gap: .75rem; }
    .att-page .att-savebar { position: sticky; bottom: 0; z-index: 5; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem; padding: .9rem 1.5rem; background: var(--bs-card-bg, #fff); border-top: 1px solid var(--att-line); }
    .att-page .att-row-hidden { display: none; }

    @media (max-width: 575.98px) {
        .att-page .att-card-pad { padding: 1rem; }
        .att-page .att-split { width: 80px; }
    }

    /* Polish pass */
    .att-page .att-head h2 { font-size: 1.65rem; }
    .att-page .att-card { box-shadow: 0 1px 2px rgba(15, 23, 42, .04); }

    .att-page .att-table thead th { background: rgba(100, 116, 139, .06); }
    .att-page .att-table thead th:first-child,
    .att-page .att-table tbody td:first-child { padding-left: 1.5rem; }
    .att-page .att-table thead th:last-child,
    .att-page .att-table tbody td:last-child { padding-right: 1.5rem; }

    /* Two-section filter panel */
    .att-page .att-panel { display: grid; grid-template-columns: 1fr; }
    .att-page .att-panel > section { display: flex; flex-direction: column; padding: 1.25rem 1.5rem; }
    .att-page .att-panel > section + section { border-top: 1px solid var(--att-line); }
    @media (min-width: 992px) {
        .att-page .att-panel { grid-template-columns: 1.7fr 1fr; }
        .att-page .att-panel > section + section { border-top: 0; border-left: 1px solid var(--att-line); }
    }
    .att-page .att-toolbar { display: flex; flex-wrap: wrap; align-items: flex-end; gap: .75rem; margin-top: auto; }
    .att-page .att-toolbar .att-field { flex: 0 1 165px; min-width: 140px; }
    .att-page .att-toolbar .att-field.grow { flex: 1 1 160px; }

    /* Meter + list rows (subject breakdown) */
    .att-page .att-meter { height: 6px; border-radius: 999px; background: var(--att-track); overflow: hidden; }
    .att-page .att-meter i { display: block; height: 100%; background: var(--att-present); border-radius: 999px; }
    .att-page .att-meter.is-warn i { background: var(--att-late); }
    .att-page .att-meter.is-low i { background: var(--att-absent); }
    .att-page .att-list-row { padding: .9rem 1.5rem; border-top: 1px solid var(--att-line); }
    .att-page .att-list-row:first-child { border-top: 0; }


/* ==========================================
   ATTENDANCE PAGES - DARK MODE OVERRIDES
   Add this at the END of the existing CSS
========================================== */

html[data-theme="dark"] .att-page,
html[data-bs-theme="dark"] .att-page {
    --att-line: #334155;
    --att-muted: #94a3b8;
    --att-track: rgba(148, 163, 184, .2);

    color: #e2e8f0;
}

/* Main cards and panels */
html[data-theme="dark"] .att-page .att-card,
html[data-bs-theme="dark"] .att-page .att-card {
    background: #151f30 !important;
    border-color: #334155 !important;
    color: #e2e8f0 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
}

/* Two-section filter panel */
html[data-theme="dark"] .att-page .att-panel > section,
html[data-bs-theme="dark"] .att-page .att-panel > section {
    background: #151f30;
}

/* Headings */
html[data-theme="dark"] .att-page .att-head h2,
html[data-bs-theme="dark"] .att-page .att-head h2,
html[data-theme="dark"] .att-page .att-card-title,
html[data-bs-theme="dark"] .att-page .att-card-title {
    color: #f1f5f9;
}

/* Description and muted text */
html[data-theme="dark"] .att-page .att-head p,
html[data-bs-theme="dark"] .att-page .att-head p,
html[data-theme="dark"] .att-page .att-card-sub,
html[data-bs-theme="dark"] .att-page .att-card-sub,
html[data-theme="dark"] .att-page .att-stat-label,
html[data-bs-theme="dark"] .att-page .att-stat-label,
html[data-theme="dark"] .att-page .att-sub,
html[data-bs-theme="dark"] .att-page .att-sub {
    color: #94a3b8 !important;
}

/* Form labels */
html[data-theme="dark"] .att-page .form-label,
html[data-bs-theme="dark"] .att-page .form-label {
    color: #cbd5e1 !important;
}

/* Inputs */
html[data-theme="dark"] .att-page .form-control,
html[data-bs-theme="dark"] .att-page .form-control,
html[data-theme="dark"] .att-page .form-select,
html[data-bs-theme="dark"] .att-page .form-select {
    background-color: #202c3c !important;
    color: #f1f5f9 !important;
    border-color: #475569 !important;
}

html[data-theme="dark"] .att-page .form-control::placeholder,
html[data-bs-theme="dark"] .att-page .form-control::placeholder {
    color: #94a3b8 !important;
}

html[data-theme="dark"] .att-page .form-control:focus,
html[data-bs-theme="dark"] .att-page .form-control:focus,
html[data-theme="dark"] .att-page .form-select:focus,
html[data-bs-theme="dark"] .att-page .form-select:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 .2rem rgba(59, 130, 246, .2);
    outline: none;
}

/* Date input calendar icon */
html[data-theme="dark"] .att-page input[type="date"]::-webkit-calendar-picker-indicator,
html[data-bs-theme="dark"] .att-page input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}

/* Quick filter chips */
html[data-theme="dark"] .att-page .att-chip,
html[data-bs-theme="dark"] .att-page .att-chip {
    color: #cbd5e1;
    border-color: #475569;
}

html[data-theme="dark"] .att-page .att-chip:hover,
html[data-bs-theme="dark"] .att-page .att-chip:hover {
    color: #60a5fa;
    border-color: #60a5fa;
}

html[data-theme="dark"] .att-page .att-chip.is-active,
html[data-bs-theme="dark"] .att-page .att-chip.is-active {
    background: #2563eb;
    color: #fff;
    border-color: #2563eb;
}

/* Attendance table */
html[data-theme="dark"] .att-page .att-table,
html[data-bs-theme="dark"] .att-page .att-table {
    --bs-table-bg: #151f30;
    --bs-table-color: #e2e8f0;
    --bs-table-border-color: #334155;
    --bs-table-hover-bg: #202c40;
    --bs-table-hover-color: #fff;

    color: #e2e8f0 !important;
    background: #151f30 !important;
}

/* Table headings */
html[data-theme="dark"] .att-page .att-table thead th,
html[data-bs-theme="dark"] .att-page .att-table thead th {
    background: #202c40 !important;
    color: #cbd5e1 !important;
    border-color: #334155 !important;
}

/* Table rows */
html[data-theme="dark"] .att-page .att-table tbody tr,
html[data-bs-theme="dark"] .att-page .att-table tbody tr {
    background: #151f30 !important;
}

html[data-theme="dark"] .att-page .att-table tbody td,
html[data-bs-theme="dark"] .att-page .att-table tbody td {
    background: #151f30 !important;
    color: #e2e8f0 !important;
    border-color: #334155 !important;
}

/* Table hover */
html[data-theme="dark"] .att-page .att-table tbody tr:hover td,
html[data-bs-theme="dark"] .att-page .att-table tbody tr:hover td {
    background: #202c40 !important;
}

/* Attendance percentage and stat values */
html[data-theme="dark"] .att-page .att-stat-value,
html[data-bs-theme="dark"] .att-page .att-stat-value,
html[data-theme="dark"] .att-page .att-num,
html[data-bs-theme="dark"] .att-page .att-num {
    color: #f1f5f9;
}

/* Attendance ring center */
html[data-theme="dark"] .att-page .att-ring::before,
html[data-bs-theme="dark"] .att-page .att-ring::before {
    background: #151f30 !important;
}

html[data-theme="dark"] .att-page .att-ring span,
html[data-bs-theme="dark"] .att-page .att-ring span {
    color: #f8fafc;
}

/* Segmented attendance buttons */
html[data-theme="dark"] .att-page .att-seg,
html[data-bs-theme="dark"] .att-page .att-seg {
    border-color: #475569;
}

html[data-theme="dark"] .att-page .att-seg label,
html[data-bs-theme="dark"] .att-page .att-seg label {
    color: #cbd5e1;
    border-color: #475569;
}

html[data-theme="dark"] .att-page .att-seg label:hover,
html[data-bs-theme="dark"] .att-page .att-seg label:hover {
    background: #334155;
}

/* Sticky save bar */
html[data-theme="dark"] .att-page .att-savebar,
html[data-bs-theme="dark"] .att-page .att-savebar {
    background: #151f30 !important;
    color: #e2e8f0;
    border-color: #334155 !important;
}

/* Subject list rows */
html[data-theme="dark"] .att-page .att-list-row,
html[data-bs-theme="dark"] .att-page .att-list-row {
    border-color: #334155;
    color: #e2e8f0;
}

/* Empty state */
html[data-theme="dark"] .att-page .att-empty,
html[data-bs-theme="dark"] .att-page .att-empty {
    color: #94a3b8;
}

html[data-theme="dark"] .att-page .att-empty strong,
html[data-bs-theme="dark"] .att-page .att-empty strong {
    color: #f1f5f9;
}

</style>