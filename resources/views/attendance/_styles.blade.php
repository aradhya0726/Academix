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

    .att-page .att-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .att-page .page-heading {
        margin: 0;
        font-size: clamp(1.45rem, 2vw, 1.9rem);
        font-weight: 750;
        letter-spacing: -.04em;
    }

    .att-page .att-head p {
        margin: .35rem 0 0;
        color: var(--att-muted);
        font-size: .86rem;
    }

    .att-page .att-card {
        min-width: 0;
        border: 1px solid var(--att-line);
        border-radius: 15px;
        background: var(--bs-body-bg, #fff);
        box-shadow: 0 2px 10px rgba(15, 23, 42, .045);
    }

    .att-page .att-card-pad {
        padding: 1.15rem 1.25rem;
    }

    .att-page .att-card-title {
        font-size: .98rem;
        font-weight: 700;
        letter-spacing: -.02em;
    }

    .att-page .att-stat {
        display: flex;
        align-items: center;
        gap: .9rem;
        min-width: 0;
    }

    .att-page .att-stat-icon {
        width: 43px;
        height: 43px;
        flex: 0 0 43px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        font-size: 1.15rem;
    }

    .att-page .tone-neutral {
        color: #2563eb;
        background: rgba(37, 99, 235, .11);
    }

    .att-page .tone-present {
        color: #16a34a;
        background: rgba(22, 163, 74, .11);
    }

    .att-page .tone-late {
        color: #d97706;
        background: rgba(217, 119, 6, .12);
    }

    .att-page .tone-absent {
        color: #dc2626;
        background: rgba(220, 38, 38, .1);
    }

    .att-page .att-stat-value {
        font-size: 1.45rem;
        font-weight: 750;
        line-height: 1.2;
        letter-spacing: -.04em;
        font-variant-numeric: tabular-nums;
    }

    .att-page .att-stat-label {
        margin-top: .2rem;
        color: var(--att-muted);
        font-size: .75rem;
        font-weight: 600;
    }

    .att-page .att-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: end;
        gap: .75rem;
    }

    .att-page .att-field {
        min-width: 170px;
        flex: 1 1 170px;
    }

    .att-page .att-field label {
        margin-bottom: .4rem;
        font-size: .78rem;
        font-weight: 650;
    }

    .att-page .form-control,
    .att-page .form-select {
        min-height: 42px;
        border-radius: 9px;
        font-size: .84rem;
    }

    .att-page .btn {
        border-radius: 9px;
        font-weight: 650;
        font-size: .82rem;
        padding: .58rem .85rem;
    }

    .att-page .att-chips {
        display: flex;
        flex-wrap: wrap;
        gap: .45rem;
    }

    .att-page .att-chip {
        display: inline-flex;
        align-items: center;
        padding: .4rem .7rem;
        border: 1px solid var(--att-line);
        border-radius: 999px;
        color: var(--att-muted);
        background: var(--bs-body-bg, #fff);
        text-decoration: none;
        font-size: .74rem;
        font-weight: 650;
        transition: .15s ease;
    }

    .att-page .att-chip:hover,
    .att-page .att-chip.is-active {
        color: var(--att-accent);
        border-color: var(--att-accent);
        background: rgba(37, 99, 235, .07);
    }

    .att-page .att-table {
        margin: 0;
        vertical-align: middle;
        --bs-table-bg: transparent;
    }

    .att-page .att-table thead th {
        padding: .8rem 1rem;
        border-bottom: 1px solid var(--att-line);
        color: var(--att-muted);
        background: rgba(100, 116, 139, .045);
        font-size: .7rem;
        font-weight: 750;
        letter-spacing: .045em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .att-page .att-table tbody td {
        padding: .85rem 1rem;
        border-bottom: 1px solid var(--att-line);
        font-size: .82rem;
    }

    .att-page .att-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .att-page .att-table tbody tr:hover {
        background: rgba(100, 116, 139, .035);
    }

    .att-page .att-pill {
        display: inline-flex;
        align-items: center;
        padding: .28rem .58rem;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 750;
        white-space: nowrap;
    }

    .att-page .att-pill.present {
        color: #15803d;
        background: rgba(22, 163, 74, .11);
    }

    .att-page .att-pill.late {
        color: #b45309;
        background: rgba(217, 119, 6, .13);
    }

    .att-page .att-pill.absent {
        color: #b91c1c;
        background: rgba(220, 38, 38, .1);
    }

    .att-page .att-pill.none {
        color: var(--att-muted);
        background: rgba(100, 116, 139, .1);
    }

    .att-page .att-sub {
        display: block;
        margin-top: .2rem;
        color: var(--att-muted);
        font-size: .72rem;
    }

    .att-page .att-meter {
        width: 100%;
        height: 8px;
        overflow: hidden;
        border-radius: 99px;
        background: var(--att-track);
    }

    .att-page .att-meter i {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: #16a34a;
        transition: width .25s ease;
    }

    .att-page .att-meter.is-warn i {
        background: #d97706;
    }

    .att-page .att-meter.is-low i {
        background: #dc2626;
    }

    .att-page .att-list-row {
        padding: .85rem 0;
        border-bottom: 1px solid var(--att-line);
    }

    .att-page .att-list-row:first-child {
        padding-top: 0;
    }

    .att-page .att-list-row:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .att-page .att-empty {
        padding: 1.7rem 1rem;
        color: var(--att-muted);
        text-align: center;
        font-size: .82rem;
    }

    .att-page .att-empty i {
        display: block;
        margin-bottom: .55rem;
        font-size: 1.6rem;
        opacity: .55;
    }

    .att-page .att-empty strong {
        display: block;
        margin-bottom: .2rem;
        color: var(--bs-body-color, #111827);
        font-size: .88rem;
    }

    .att-page .att-table .form-check-input {
        width: 1.05rem;
        height: 1.05rem;
        cursor: pointer;
    }

    .att-page .att-table .form-check-input:focus {
        box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .15);
    }

    .att-page .att-table .form-check-input:checked {
        background-color: var(--att-accent);
        border-color: var(--att-accent);
    }

    .att-page .att-table .form-control {
        min-width: 130px;
    }

    .att-page .pagination {
        margin-bottom: 0;
    }

    .att-page .page-link {
        border-radius: 7px;
        font-size: .78rem;
    }

    @media (max-width: 767.98px) {
        .att-page .att-card-pad {
            padding: 1rem;
        }

        .att-page .att-stat-value {
            font-size: 1.25rem;
        }

        .att-page .att-stat-icon {
            width: 38px;
            height: 38px;
            flex-basis: 38px;
        }

        .att-page .att-head {
            margin-bottom: 1.1rem;
        }

        .att-page .att-table thead th,
        .att-page .att-table tbody td {
            padding: .7rem .75rem;
        }
    }

    @media (max-width: 420px) {
        .att-page .att-stat {
            gap: .6rem;
        }

        .att-page .att-stat-value {
            font-size: 1.1rem;
        }

        .att-page .att-stat-label {
            font-size: .68rem;
        }
    }
</style>