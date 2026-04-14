@extends('layouts.employee')

@section('employee_content')
<div class="row g-4 mb-4">
    <div class="col-md-2">
        <div class="card p-4 text-center" style="background: linear-gradient(145deg, #16213e, #0f3460);">
            <div class="h1 text-success mb-2">📋</div>
            <h5 class="text-gold">Total Orders</h5>
            <div id="total-orders-count" class="h2 fw-bold">0</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card p-4 text-center" style="background: linear-gradient(145deg, #1b3a4b, #102a3a);">
            <div class="h1 text-warning mb-2">⏳</div>
            <h5 class="text-gold">Pending</h5>
            <div id="pending-orders-count" class="h2 fw-bold text-warning">0</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card p-4 text-center" style="background: linear-gradient(145deg, #1a4d2e, #143d24);">
            <div class="h1 text-success-light mb-2">🚲</div>
            <h5 class="text-gold">Confirmed</h5>
            <div id="confirmed-orders-count" class="h2 fw-bold text-success-light">0</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card p-4 text-center" style="background: linear-gradient(145deg, #1f4d33, #173b27);">
            <div class="h1 text-info mb-2">✅</div>
            <h5 class="text-gold">Delivered</h5>
            <div id="delivered-orders-count" class="h2 fw-bold text-info">0</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card p-4 text-center" style="background: linear-gradient(145deg, #3d1a1a, #2d1414);">
            <div class="h1 text-danger mb-2">❌</div>
            <h5 class="text-gold">Cancelled</h5>
            <div id="cancelled-orders-count" class="h2 fw-bold text-danger">0</div>
        </div>
    </div>
</div>
<div id="alert-messages" class="mb-4"></div>


<div class="card border-0 shadow-lg overflow-hidden">
    <div class="card-header bg-success p-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-white fw-bold">📦 Order Management</h5>
        <button class="btn btn-outline-light btn-sm" onclick="loadOrders()">🔄 Refresh List</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr class="text-success border-bottom border-white border-opacity-10">
                        <th class="ps-4">Order ID</th>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Rider</th>
                        <th>Amount</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="orders-list">
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <div class="spinner-border text-success spinner-border-sm me-2"></div>
                            Loading system data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Assign Delivery Modal -->
<div class="modal fade" id="assignModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="background: #1a1a2e; color: #fff;">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold">🚴 Assign Free Rider</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info py-2 small bg-opacity-10 border-0 text-white" style="background: rgba(13, 202, 240, 0.1);">
                    Order confirmed! Please select an available rider to proceed with the delivery.
                </div>
                <input type="hidden" id="assign-order-id">
                <div class="mb-3">
                    <label class="form-label fw-bold text-gold">Select Available Rider</label>
                    <select class="form-select bg-dark text-white border-secondary" id="deliveryman-select">
                        <option value="">Searching for riders...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Assign Later</button>
                <button type="button" class="btn btn-success px-4" onclick="submitAssignDelivery()">Assign Now</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadOrders();
    });

    function loadOrders() {
        console.log('Loading orders...');
        fetch(`${API_URL}/employee/orders`, {
            headers: getHeaders()
        })
        .then(res => {
            if(res.status === 401 || res.status === 403) logout();
            return res.json();
        })
        .then(data => {
            const orders = Array.isArray(data) ? data : (data.orders || []);
            const tbody = document.getElementById('orders-list');
            const effectiveStatuses = orders.map(getEffectiveOrderStatus);
            
            // Update stats
            document.getElementById('total-orders-count').innerText = orders.length;
            document.getElementById('pending-orders-count').innerText = effectiveStatuses.filter(s => s === 'Pending').length;
            document.getElementById('confirmed-orders-count').innerText = effectiveStatuses.filter(s => s === 'Confirmed').length;
            document.getElementById('delivered-orders-count').innerText = effectiveStatuses.filter(s => s === 'Delivered').length;
            document.getElementById('cancelled-orders-count').innerText = effectiveStatuses.filter(s => s === 'Cancelled').length;

            if(orders.length > 0) {
                tbody.innerHTML = orders.map(order => {
                    const custName = order.customer ? order.customer.CustomerName : 'Guest';
                    const custPhone = order.customer ? order.customer.Phone : 'N/A';
                    const effectiveStatus = getEffectiveOrderStatus(order);
                    const statusClass = getStatusClass(effectiveStatus);
                    const riderName = order.delivery && order.delivery.delivery_man ? 
                        `<span class="text-info"><i class="bi bi-person-badge me-1"></i>${order.delivery.delivery_man.DelManName}</span>` : 
                        '<span class="text-white small fw-semibold">Not Assigned</span>';
                    const actions = getActionsHtml(order);

                    return `
                    <tr class="border-bottom border-white border-opacity-10" id="order-row-${order.OrderID}">
                        <td class="ps-4"><strong>#ORD-${order.OrderID}</strong></td>
                        <td>${custName}</td>
                        <td><span class="badge bg-light text-dark">${custPhone}</span></td>
                        <td><span class="badge bg-${statusClass} px-3 text-uppercase small">${effectiveStatus}</span></td>
                        <td class="rider-cell">${riderName}</td>
                        <td class="fw-bold text-success-light">৳${order.TotalAmount}</td>
                        <td class="text-end pe-4">${actions}</td>
                    </tr>`;
                }).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">No orders found in systems.</td></tr>';
            }
            console.log('Orders loaded successfully');
        })
        .catch(err => {
            console.error('Fetch error:', err);
            document.getElementById('orders-list').innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Error loading data. Verify connection.</td></tr>';
        });
    }

    function getStatusClass(status) {
        if(status === 'Pending') return 'warning';
        if(status === 'Confirmed') return 'success';
        if(status === 'Delivered') return 'info';
        if(status === 'Cancelled') return 'danger';
        return 'primary';
    }

    function getEffectiveOrderStatus(order) {
        if (order && order.delivery && order.delivery.DeliveryStatus === 'Delivered') {
            return 'Delivered';
        }
        return order.OrderStatus;
    }

    function getActionsHtml(order) {
        const effectiveStatus = getEffectiveOrderStatus(order);

        if(effectiveStatus === 'Pending') {
            return `
                <button class="btn btn-sm btn-success me-1 px-3 shadow-sm transition-btn" onclick="confirmOrder(${order.OrderID}, this)">✅ Confirm</button>
                <button class="btn btn-sm btn-danger px-3 shadow-sm transition-btn" onclick="cancelOrder(${order.OrderID}, this)">❌ Cancel</button>
            `;
        }
        if(effectiveStatus === 'Confirmed') {
            const hasRider = order.delivery && order.delivery.delivery_man;
            if(hasRider) return '<span class="badge bg-primary text-white"><i class="bi bi-check-circle-fill me-1"></i>Ready for Delivery</span>';
            
            return `
                <button class="btn btn-sm btn-info text-white px-3 shadow-sm transition-btn" onclick="openAssignModal(${order.OrderID})">🚴 Assign Rider</button>
            `;
        }
        if(effectiveStatus === 'Delivered') {
            return '<span class="badge bg-info text-dark"><i class="bi bi-truck me-1"></i>Delivered</span>';
        }
        return '<span class="text-muted small">No action</span>';
    }

    function confirmOrder(id, btn) {
        console.log('Confirming order:', id);
        if(!confirm('Confirm this order and move to delivery?')) return;
        
        if(btn) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            btn.disabled = true;
        }

        fetch(`${API_URL}/employee/orders/${id}/confirm`, {
            method: 'POST',
            headers: getHeaders()
        })
        .then(res => res.json())
        .then(data => {
            console.log('Order confirmed:', data);
            
            showOrderAlert(`✅ Order <strong>#ORD-${id}</strong> has been confirmed. Please assign a rider now.`, 'success');
            
            loadOrders();
            // Automatically trigger rider assignment
            setTimeout(() => {
                openAssignModal(id);
            }, 500);
        })
        .catch(err => {
            console.error('Confirmation error:', err);
            showOrderAlert('Failed to confirm order', 'danger');
            if(btn) {
                btn.innerHTML = '✅ Confirm';
                btn.disabled = false;
            }
        });
    }

    function cancelOrder(id, btn) {
        console.log('Cancelling order:', id);
        if(!confirm('Are you sure you want to CANCEL this order? This cannot be undone.')) return;
        
        if(btn) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            btn.disabled = true;
        }

        fetch(`${API_URL}/employee/orders/${id}/cancel`, {
            method: 'POST',
            headers: getHeaders()
        })
        .then(res => res.json())
        .then(data => {
            console.log('Order cancelled:', data);
            showOrderAlert(`❌ Order <strong>#ORD-${id}</strong> has been cancelled.`, 'danger');
            
            loadOrders();
        })
        .catch(err => {
            console.error('Cancellation error:', err);
            showOrderAlert('Failed to cancel order', 'danger');
            if(btn) {
                btn.innerHTML = '❌ Cancel';
                btn.disabled = false;
            }
        });
    }

    function openAssignModal(orderId) {
        console.log('Opening assign modal for:', orderId);
        document.getElementById('assign-order-id').value = orderId;
        const select = document.getElementById('deliveryman-select');
        select.innerHTML = `<option value="">Searching for free riders...</option>`;

        fetch(`${API_URL}/employee/deliverymen/all`, { headers: getHeaders() })
        .then(res => res.json())
        .then(data => {
            if(data && data.length > 0) {
                select.innerHTML = data.map(rider => {
                    if (rider.Status === 'Available') {
                        return `<option value="${rider.DelManID}">🟢 ${rider.DelManName} (ID: ${rider.DelManID} - Available)</option>`;
                    } else {
                        // Rider is busy, find out who they are delivering to
                        let busyText = "Busy";
                        if (rider.deliveries && rider.deliveries.length > 0) {
                            // Find the first active delivery
                            const activeTask = rider.deliveries.find(d => d.DeliveryStatus === 'Pending' || d.DeliveryStatus === 'In Progress');
                            if (activeTask && activeTask.order && activeTask.order.customer) {
                                busyText = `Delivering to: ${activeTask.order.customer.CustomerName}`;
                            }
                        }
                        return `<option value="${rider.DelManID}" disabled>🔴 ${rider.DelManName} (ID: ${rider.DelManID} - ${busyText})</option>`;
                    }
                }).join('');
            } else {
                select.innerHTML = `<option value="">No riders exist in the system yet</option>`;
            }
        })
        .catch(err => {
            console.error('Rider load error:', err);
            select.innerHTML = `<option value="">Error loading riders</option>`;
        });

        const modal = new bootstrap.Modal(document.getElementById('assignModal'));
        modal.show();
    }

    function submitAssignDelivery() {
        const orderId = document.getElementById('assign-order-id').value;
        const selectElement = document.getElementById('deliveryman-select');
        const delManId = selectElement.value;
        
        if(!delManId) {
            showOrderAlert('Please select a rider from the list', 'warning');
            return;
        }

        const riderName = selectElement.options[selectElement.selectedIndex].text.split(' (ID:')[0];
        const alertDiv = document.getElementById('alert-messages');
        const btn = document.querySelector('#assignModal .btn-success');
        
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Assigning...';
        btn.disabled = true;

        fetch(`${API_URL}/employee/orders/${orderId}/assign-delivery`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ DelManID: delManId })
        })
        .then(res => res.json())
        .then(data => {
            console.log('Rider assigned:', data);
            
            // Show selection message beside order
            const row = document.getElementById(`order-row-${orderId}`);
            if(row) {
                const riderCell = row.querySelector('.rider-cell');
                riderCell.innerHTML = `<span class="text-info fw-bold animate__animated animate__pulse"><i class="bi bi-bicycle me-1"></i>${riderName} is selected to deliver the order</span>`;
            }

            alertDiv.innerHTML = `<div class="alert alert-success border-0 shadow-sm py-3">✅ <strong>${riderName}</strong> has been assigned to the task successfully</div>`;
            
            const modalEl = document.getElementById('assignModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if(modal) modal.hide();
            
            setTimeout(() => {
                loadOrders();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 1500);
            
            // Clear message after 5 seconds
            setTimeout(() => {
                alertDiv.innerHTML = '';
            }, 5000);
        })
        .catch(err => {
            console.error('Assignment error:', err);
            showOrderAlert('Could not assign rider', 'danger');
            btn.innerHTML = 'Assign Now';
            btn.disabled = false;
        });
    }

    function showOrderAlert(message, type) {
        const alertDiv = document.getElementById('alert-messages');
        if (!alertDiv) return;

        alertDiv.innerHTML = `
            <div class="alert alert-${type} border-0 shadow-sm alert-dismissible fade show py-3" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        setTimeout(() => {
            if (alertDiv.innerHTML) {
                alertDiv.innerHTML = '';
            }
        }, 5000);
    }
</script>
@endsection
