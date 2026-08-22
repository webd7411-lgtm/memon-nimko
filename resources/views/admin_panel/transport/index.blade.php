@extends('admin_panel.layout.app')
@section('content')
<style>
:root {
    --tr-bg: #f1f5f9;
    --tr-surface: #ffffff;
    --tr-border: #e2e8f0;
    --tr-text: #0f172a;
    --tr-sec: #475569;
    --tr-muted: #94a3b8;
    --tr-accent: #0f766e;
}
.tr-desk { overflow-x: hidden; }
.tr-tbl { table-layout: fixed; width: 100% !important; margin: 0; border-collapse: separate; border-spacing: 0; font-size: .8rem; }
.tr-tbl thead th { background: #f8fafc; font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .45px; color: var(--tr-muted); padding: .5rem .65rem; border-bottom: 2px solid var(--tr-border); white-space: normal !important; overflow-wrap: break-word; }
.tr-tbl tbody td { padding: .5rem .65rem; border-bottom: 1px solid #eef2f7; vertical-align: middle; color: var(--tr-sec); white-space: normal !important; overflow-wrap: break-word; }
.tr-tbl tbody tr:hover td { background: #fafbfc; }
.tr-tbl .tr-actions { white-space: nowrap; }

.trc-cards { display: none; }
.trc-card { background: var(--tr-surface); border: 1.5px solid var(--tr-border); border-left: 4px solid var(--tr-accent); border-radius: 12px; box-shadow: 0 1px 2px rgba(0,0,0,.03), 0 2px 8px rgba(0,0,0,.04); padding: .75rem .85rem; margin-bottom: .6rem; }
.trc-head { display: flex; align-items: center; justify-content: space-between; gap: .6rem; padding-bottom: .5rem; margin-bottom: .5rem; border-bottom: 1px dashed var(--tr-border); }
.trc-name { font-size: .95rem; font-weight: 800; color: var(--tr-text); overflow-wrap: break-word; line-height: 1.25; }
.trc-id { flex: 0 0 auto; font-size: .7rem; font-weight: 800; color: var(--tr-accent); background: #ccfbf1; border: 1px solid #99f6e4; border-radius: 20px; padding: .22rem .6rem; white-space: nowrap; }
.trc-body { display: grid; grid-template-columns: repeat(2, 1fr); gap: .45rem .9rem; }
.trc-row { min-width: 0; }
.trc-row span { display: block; font-size: .6rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: var(--tr-muted); }
.trc-row b { display: block; font-size: .82rem; font-weight: 700; color: var(--tr-text); overflow-wrap: break-word; }
.trc-actions { grid-column: 1 / -1; display: flex; flex-wrap: wrap; gap: .4rem; padding-top: .55rem; margin-top: .1rem; border-top: 1px dashed var(--tr-border); }
.trc-actions .btn { flex: 1 1 40%; min-height: 40px; display: inline-flex; align-items: center; justify-content: center; gap: .35rem; margin: 0 !important; border-radius: 10px !important; font-size: .78rem !important; font-weight: 700; }
.trc-actions .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff !important; border: none; box-shadow: 0 4px 12px rgba(220,38,38,.25); }
.trc-empty { text-align: center; padding: 1.5rem 1rem; color: var(--tr-muted); font-size: .82rem; }

@media (max-width: 991.98px) {
    .tr-desk { display: none; }
    .trc-cards { display: block; margin-top: 1rem; }
}
</style>
<div class="main-content">
    <div class="main-content-inner">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h3 class="mb-0">Transport List</h3>
                <a href="{{ route('transport.create') }}" class="btn btn-success">Add New Transport</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="tr-desk">
                <table id="transportTable" class="table table-bordered table-striped tr-tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transports as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $item->name }}</td>
                                <td>{{ $item->company_name }}</td>
                                <td>{{ $item->mobile }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->address }}</td>
                                <td class="tr-actions">
                                    <div style="white-space: nowrap;">
                                        <a href="{{ route('transport.edit', $item->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="{{ route('transport.delete', $item->id) }}" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this?');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="trcCards" class="trc-cards"></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#transportTable')) {
            $('#transportTable').DataTable().destroy();
        }
        var table = $('#transportTable').DataTable({
            responsive: false,
            pageLength: 10,
            drawCallback: function() {
                if (window.matchMedia('(max-width: 991.98px)').matches) renderTrCards(this);
            }
        });

        window.setTimeout(function() {
            if (window.matchMedia('(max-width: 991.98px)').matches) renderTrCards(table);
        }, 0);

        function esc(str) {
            var d = document.createElement('div');
            d.textContent = str == null ? '' : String(str);
            return d.innerHTML;
        }

        function renderTrCards(dt) {
            if (!dt || !dt.rows) return;
            var rows = dt.rows({ page: 'current' }).nodes().toArray();
            var $wrap = $('#trcCards');
            if (!rows.length) {
                $wrap.html('<div class="trc-empty"><i class="bi bi-inbox"></i> No transports found</div>');
                return;
            }
            var html = '';
            rows.forEach(function(tr) {
                var tds = $(tr).children('td');
                var num = $(tds[0]).text().trim();
                var name = $(tds[1]).text().trim();
                var company = $(tds[2]).text().trim();
                var mobile = $(tds[3]).text().trim();
                var email = $(tds[4]).text().trim();
                var address = $(tds[5]).text().trim();
                var actions = $(tds[6]).html() || '';

                html += '<div class="trc-card">'
                    + '<div class="trc-head">'
                    + '<span class="trc-name">' + esc(name) + '</span>'
                    + '<span class="trc-id">#' + esc(num) + '</span>'
                    + '</div>'
                    + '<div class="trc-body">'
                    + '<div class="trc-row"><span>Company</span><b>' + (esc(company) || '-') + '</b></div>'
                    + '<div class="trc-row"><span>Mobile</span><b>' + (esc(mobile) || '-') + '</b></div>'
                    + '<div class="trc-row"><span>Email</span><b>' + (esc(email) || '-') + '</b></div>'
                    + '<div class="trc-row"><span>Address</span><b>' + (esc(address) || '-') + '</b></div>'
                    + '<div class="trc-actions">' + actions + '</div>'
                    + '</div>'
                    + '</div>';
            });
            $wrap.html(html);
        }

        $(window).on('resize.trc', function() {
            if (window.matchMedia('(max-width: 991.98px)').matches) renderTrCards(table);
        });
    });
</script>
@endsection