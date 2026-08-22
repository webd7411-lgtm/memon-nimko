@extends('admin_panel.layout.app')
@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --evf-bg: #f8fafc;
  --evf-surface: #ffffff;
  --evf-border: #e2e8f0;
  --evf-border-lt: #f1f5f9;
  --evf-text: #0f172a;
  --evf-text-sec: #475569;
  --evf-text-muted: #64748b;
  --evf-primary: #2563eb;
  --evf-radius: 16px;
  --evf-radius-sm: 10px;
  --evf-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --evf-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --evf-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.evf-page * {
  font-family: var(--evf-font);
}

.evf-page {
  background-color: var(--evf-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.evf-hero {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--evf-radius);
  padding: 1.5rem 1.75rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 20px 30px -10px rgba(15, 23, 42, 0.3);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  overflow: hidden;
}

.evf-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(37, 99, 235, 0.25) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.evf-hero > * {
  position: relative;
  z-index: 1;
}

.evf-hero-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.evf-hero-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.evf-hero-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #60a5fa;
  font-size: 1.4rem;
}

.evf-hero-badge {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 30px;
  padding: 0.3rem 0.9rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #93c5fd;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.evf-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--evf-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.evf-btn-primary {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.evf-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
}

.evf-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.evf-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ FORM CARD ═══════ */
.evf-card {
  background: var(--evf-surface);
  border: 1px solid var(--evf-border);
  border-radius: var(--evf-radius);
  box-shadow: var(--evf-shadow-lg);
  padding: 1.5rem;
}

.evf-input {
  border: 1px solid var(--evf-border);
  border-radius: var(--evf-radius-sm);
  padding: 0.55rem 0.85rem;
  font-size: 0.88rem;
  color: var(--evf-text);
  background: #ffffff;
  transition: all 0.2s ease;
  width: 100%;
}
.evf-input:focus {
  border-color: var(--evf-primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  outline: none;
}

.evf-label {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  color: var(--evf-text-sec);
  margin-bottom: 0.35rem;
  display: block;
}

.evf-table {
  width: 100% !important;
  border-collapse: separate;
  border-spacing: 0;
}
.evf-table thead th {
  background: #f8fafc;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--evf-text-muted);
  padding: 0.75rem 1rem;
  border-bottom: 1px solid var(--evf-border);
}
.evf-table tbody td {
  padding: 0.65rem 1rem;
  border-bottom: 1px solid var(--evf-border-lt);
}
</style>

<div class="evf-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="evf-hero">
      <div class="evf-hero-title">
        <div class="evf-hero-icon">
          <i class="bi bi-pencil-square"></i>
        </div>
        <div>
          <h2>Create Expense Voucher</h2>
          <span class="evf-hero-badge">Direct Expense Entry</span>
        </div>
      </div>

      <div class="d-flex gap-2">
        <a href="{{ route('all-expense-vochers') }}" class="evf-btn evf-btn-glass">
          <i class="bi bi-list-ul me-1"></i> All Vouchers
        </a>

        <a href="{{ url()->previous() }}" class="evf-btn evf-btn-glass">
          <i class="bi bi-arrow-left me-1"></i> Exit
        </a>
      </div>
    </div>

    {{-- ═══════ MAIN FORM CARD ═══════ --}}
    <div class="evf-card">
      @if ($errors->any())
      <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-3">
        <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Errors detected:</strong>
        <ul class="mb-0 ps-3 small">
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      @if (session('error'))
      <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-3">
        <i class="bi bi-exclamation-circle-fill me-1"></i> {{ session('error') }}
      </div>
      @endif

      @if (session('success'))
      <div class="alert alert-success rounded-3 border-0 shadow-sm mb-3">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
      </div>
      @endif

      <form action="{{ route('expense.vochers.store') }}" method="POST">
        @csrf

        {{-- Top Information Fields --}}
        <div class="row g-3 mb-4">
          <div class="col-12 col-md-2">
            <label class="evf-label">EVID</label>
            <input type="text" class="evf-input fw-bold font-monospace" name="evid" value="{{ $nextRvid }}" readonly style="background:#f8fafc;">
          </div>

          <div class="col-12 col-md-4">
            <label class="evf-label">Account Head</label>
            <select name="vendor_type" class="evf-input">
              <option value="">Select Account Head</option>
              @foreach($AccountHeads as $head)
              <option value="{{ $head->id }}">{{ $head->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-4">
            <label class="evf-label">Account</label>
            <select name="vendor_id" class="evf-input">
              <option disabled selected>Select Head First</option>
            </select>
          </div>

          <div class="col-12 col-md-2">
            <label class="evf-label">Date</label>
            <input type="date" class="evf-input" name="date" value="{{ date('Y-m-d') }}">
          </div>
        </div>

        {{-- Voucher Table --}}
        <div class="table-responsive rounded-3 border overflow-hidden mb-4">
          <table class="table evf-table align-middle m-0" id="voucherTable">
            <thead>
              <tr>
                <th>Remarks / Line Details</th>
                <th class="text-end" style="width:200px">Amount (PKR)</th>
                <th class="text-center" style="width:70px">Action</th>
              </tr>
            </thead>

            <tbody>
              <tr>
                <td>
                  <input type="text" name="remarks[]" class="evf-input remark-input" placeholder="Enter remarks...">
                </td>
                <td>
                  <input type="number" step="0.01" name="amount[]" class="evf-input text-end amount-input" placeholder="0.00">
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-outline-danger rounded-2 removeRow">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>

            <tfoot>
              <tr class="bg-light">
                <th class="text-end fw-bold text-dark">Total Voucher Amount</th>
                <th>
                  <input type="text" id="totalAmount" class="evf-input text-end fw-bold text-primary fs-6" readonly value="0.00">
                </th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>

        {{-- Form Actions --}}
        <div class="d-flex gap-2">
          <button type="submit" class="evf-btn evf-btn-primary px-4">
            <i class="bi bi-save me-1"></i> Save Voucher
          </button>
          <a href="{{ url()->previous() }}" class="evf-btn evf-btn-glass text-dark border">
            <i class="bi bi-x-circle me-1"></i> Cancel
          </a>
        </div>

      </form>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
function calculateTotal() {
    let total = 0;
    $('.amount-input').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    $('#totalAmount').val(total.toFixed(2));
}

function addRow() {
    let row = `
    <tr>
        <td>
            <input type="text" name="remarks[]" class="evf-input remark-input" placeholder="Enter remarks...">
        </td>
        <td>
            <input type="number" step="0.01" name="amount[]" class="evf-input text-end amount-input" placeholder="0.00">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger rounded-2 removeRow">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>`;
    $('#voucherTable tbody').append(row);
    $('#voucherTable tbody tr:last .remark-input').focus();
}

$(document).on('keypress', '.amount-input', function(e) {
    if (e.which === 13) {
        e.preventDefault();
        calculateTotal();
        addRow();
    }
});

$(document).on('input', '.amount-input', function() {
    calculateTotal();
});

$(document).on('click', '.removeRow', function() {
    $(this).closest('tr').remove();
    calculateTotal();
});

$(document).on('change', 'select[name="vendor_type"]', function() {
    let headId = $(this).val();
    let $account = $('select[name="vendor_id"]');

    $account.html('<option disabled selected>Loading...</option>');

    if (!headId) {
        $account.html('<option disabled selected>Select</option>');
        return;
    }

    $.get('{{ url("get-accounts-by-head") }}/' + headId, function(res) {
        let html = '<option disabled selected>Select Account</option>';
        res.forEach(acc => {
            html += `<option value="${acc.id}">${acc.title}</option>`;
        });
        $account.html(html);
    });
});
</script>
@endsection