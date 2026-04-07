@extends('layouts.customer')

@section('title', 'My Wallet')

@section('customer_styles')
<style>
    .wallet-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 14px;
    }
    .txn-table thead th {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-secondary);
        border-bottom-color: var(--border-color);
    }
    .txn-table td { border-bottom-color: rgba(255,255,255,0.06); }
    .credit { color: #4ade80; font-weight: 700; }
    .debit { color: #f87171; font-weight: 700; }
</style>
@endsection

@section('customer_content')
<div class="container">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="wallet-card p-4 mb-4">
                <h5 class="mb-2">Current Balance</h5>
                <div id="wallet-balance-main" class="display-6 fw-bold" style="color: var(--accent-orange);">Tk 0</div>
                <div class="text-muted small">Use this balance to place orders.</div>
            </div>

            <div class="wallet-card p-4">
                <h6 class="mb-3">Add Balance</h6>
                <form id="add-balance-form">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Amount</label>
                        <input type="number" id="add-amount" class="form-control" min="1" step="0.01" placeholder="Enter amount" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Description (Optional)</label>
                        <input type="text" id="add-description" class="form-control" maxlength="255" placeholder="e.g. bKash top-up">
                    </div>
                    <button type="submit" id="add-balance-btn" class="btn btn-cyan w-100">Add Balance</button>
                </form>
                <div id="wallet-message" class="small mt-3"></div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="wallet-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Wallet Transactions</h5>
                    <button class="btn btn-sm btn-outline-info" onclick="loadWallet()">Refresh</button>
                </div>
                <div class="table-responsive">
                    <table class="table txn-table table-dark align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="wallet-transactions-body">
                            <tr><td colspan="4" class="text-center text-muted py-4">Loading transactions...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customer_scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    loadWallet();
    document.getElementById('add-balance-form').addEventListener('submit', addBalance);
});

function loadWallet() {
    fetch(`${API_URL}/customer/wallet`, { headers: getHeaders() })
    .then(res => {
        if (res.status === 401 || res.status === 403) logout();
        return res.json();
    })
    .then(data => {
        const wallet = data.wallet || {};
        const txns = data.transactions || [];
        const balance = Number(wallet.Balance || 0);

        document.getElementById('wallet-balance-main').innerText = `Tk ${balance.toFixed(0)}`;

        const tbody = document.getElementById('wallet-transactions-body');
        if (!txns.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No transactions yet.</td></tr>';
        } else {
            tbody.innerHTML = txns.map(txn => {
                const date = txn.TransactionDate
                    ? new Date(txn.TransactionDate).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
                    : '—';
                const cls = txn.TransactionType === 'Credit' ? 'credit' : 'debit';
                const sign = txn.TransactionType === 'Credit' ? '+' : '-';

                return `
                <tr>
                    <td>${date}</td>
                    <td><span class="badge ${txn.TransactionType === 'Credit' ? 'bg-success' : 'bg-danger'}">${txn.TransactionType}</span></td>
                    <td>${txn.Description || ''}</td>
                    <td class="text-end ${cls}">${sign}Tk ${Number(txn.Amount || 0).toFixed(0)}</td>
                </tr>`;
            }).join('');
        }

        if (typeof loadWalletBalance === 'function') {
            loadWalletBalance();
        }
    })
    .catch(() => {
        document.getElementById('wallet-transactions-body').innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Failed to load wallet data.</td></tr>';
    });
}

function addBalance(e) {
    e.preventDefault();

    const btn = document.getElementById('add-balance-btn');
    const msg = document.getElementById('wallet-message');

    const amount = Number(document.getElementById('add-amount').value);
    const description = document.getElementById('add-description').value.trim();

    if (!amount || amount <= 0) {
        msg.className = 'small mt-3 text-danger';
        msg.innerText = 'Please enter a valid amount.';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...';

    fetch(`${API_URL}/customer/wallet/add-balance`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({
            Amount: amount,
            Description: description || null,
        })
    })
    .then(res => res.json().then(data => ({ status: res.status, body: data })))
    .then(res => {
        if (res.status === 200) {
            msg.className = 'small mt-3 text-info';
            msg.innerText = 'Balance added successfully.';
            document.getElementById('add-balance-form').reset();
            loadWallet();
        } else {
            const errors = res.body.errors ? Object.values(res.body.errors).flat().join(' ') : (res.body.message || 'Failed to add balance.');
            msg.className = 'small mt-3 text-danger';
            msg.innerText = errors;
        }
    })
    .catch(() => {
        msg.className = 'small mt-3 text-danger';
        msg.innerText = 'Network error. Please try again.';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerText = 'Add Balance';
    });
}
</script>
@endsection
