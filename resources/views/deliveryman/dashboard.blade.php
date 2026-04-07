@extends('layouts.deliveryman')

@section('delivery_content')
<!-- Rider Stats / Status -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card p-4 h-100 text-center shadow-lg border-0" style="background: linear-gradient(145deg, #16213e, #0f3460);">
            <div class="display-5 mb-2">🚴</div>
            <h5 class="text-gold">Rider Status</h5>
            <div id="status-container" class="mt-2 h4 fw-bold">
                <span class="badge bg-success rounded-pill px-4">Available</span>
            </div>
            <p class="small text-muted mt-2">Ready for assignments</p>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card h-100 border-0 shadow-lg p-4" style="background: rgba(22, 33, 62, 0.4);">
            <h5 class="text-gold mb-3">Today's Performance</h5>
            <div class="row text-center">
                <div class="col-4">
                    <div class="h3 fw-bold text-white" id="completed-count">0</div>
                    <div class="small text-muted">Delivered</div>
                </div>
                <div class="col-4 border-start border-white border-opacity-10">
                    <div class="h3 fw-bold" id="pending-count">0</div>
                    <div class="small text-muted">Active</div>
                </div>
                <div class="col-4 border-start border-white border-opacity-10">
                    <div class="h3 fw-bold text-gold" id="avg-rating">0.0</div>
                    <div class="small text-muted">Avg Rating</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-lg overflow-hidden">
    <div class="card-header bg-gold p-3 border-0">
        <h5 class="mb-0 text-dark fw-bold">🚀 My Active Tasks</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr class="text-gold border-bottom border-white border-opacity-10">
                        <th class="ps-4">Task ID</th>
                        <th>Order Ref</th>
                        <th>Status</th>
                        <th>Delivery Address</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody id="deliveries-list">
                    <!-- Loaded via JS -->
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <div class="spinner-border text-gold spinner-border-sm me-2"></div>
                            Fetching tasks...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadDeliveries();
        loadRiderStats();
    });

    function loadRiderStats() {
        fetch(`${API_URL}/deliveryman/profile`, { headers: getHeaders() })
        .then(res => res.json())
        .then(data => {
            document.getElementById('avg-rating').innerText = data.avg_rating || '0.0';
        });
    }

    function loadDeliveries() {
        fetch(`${API_URL}/deliveryman/deliveries`, {
            headers: getHeaders()
        })
        .then(res => {
            if(res.status === 401 || res.status === 403) logout();
            return res.json();
        })
        .then(data => {
            const tbody = document.getElementById('deliveries-list');
            const statusContainer = document.getElementById('status-container');
            const pendingCount = document.getElementById('pending-count');
            const completedCount = document.getElementById('completed-count');

            if(data && data.length > 0) {
                const active = data.filter(d => d.DeliveryStatus === 'Pending' || d.DeliveryStatus === 'In Progress');
                const completed = data.filter(d => d.DeliveryStatus === 'Delivered');

                pendingCount.innerText = active.length;
                completedCount.innerText = completed.length;

                // Update Rider Status Badge
                if(active.length > 0) {
                    statusContainer.innerHTML = `<span class="badge bg-warning text-dark rounded-pill px-4">Busy</span>`;
                } else {
                    statusContainer.innerHTML = `<span class="badge bg-success rounded-pill px-4">Available</span>`;
                }

                tbody.innerHTML = data.map(del => {
                    const statusClass = del.DeliveryStatus === 'Pending' ? 'warning' : (del.DeliveryStatus === 'Delivered' ? 'success' : 'info');
                    
                    return `
                    <tr class="border-bottom border-white border-opacity-10">
                        <td class="ps-4"><strong>#D${del.DeliveryID}</strong></td>
                        <td>#${del.OrderID}</td>
                        <td><span class="badge bg-${statusClass} px-3 text-uppercase small">${del.DeliveryStatus}</span></td>
                        <td><small>${del.order ? del.order.Address : 'N/A'}</small></td>
                        <td class="text-end pe-4">
                            ${del.DeliveryStatus === 'Pending' ? `
                                <button class="btn btn-gold btn-sm px-4 shadow-sm" onclick="updateStatus(${del.DeliveryID}, 'Delivered')">
                                    Done ✅
                                </button>
                            ` : '<span class="text-muted small">Completed</span>'}
                        </td>
                    </tr>`;
                }).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted">No active tasks assigned yet. Enjoy your break! ☕</td></tr>';
                statusContainer.innerHTML = `<span class="badge bg-success rounded-pill px-4">Available</span>`;
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('deliveries-list').innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Failed to load tasks. Refresh the page.</td></tr>';
        });
    }

    function updateStatus(id, status) {
        if(!confirm('Mark this delivery as completed?')) return;

        fetch(`${API_URL}/deliveryman/deliveries/${id}/update-status`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({DeliveryStatus: status})
        })
        .then(res => res.json())
        .then(data => {
            loadDeliveries();
        })
        .catch(err => alert('Error updating status'));
    }
</script>
@endsection
