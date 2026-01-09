<?php
include '../db.php';
include 'includes/header.php';

$total_sales = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status != 'Cancelled'")->fetchColumn();
$total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_users = $conn->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn();

$recent_orders = $conn->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC LIMIT 5")->fetchAll();
?>

<h3 class="fw-bold mb-4">Dashboard Overview</h3>

<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-4 text-white h-100" style="background: linear-gradient(45deg, #0f172a, #334155);">
            <h6 class="opacity-75">Total Revenue</h6>
            <h2 class="fw-bold mb-0">$<?php echo number_format($total_sales, 2); ?></h2>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-4 text-white h-100" style="background: linear-gradient(45deg, #f97316, #fb923c);">
            <h6 class="opacity-75">Total Orders</h6>
            <h2 class="fw-bold mb-0"><?php echo $total_orders; ?></h2>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-4 border-0 shadow-sm h-100">
            <h6 class="text-muted">Active Products</h6>
            <h2 class="fw-bold mb-0 text-primary"><?php echo $total_products; ?></h2>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-4 border-0 shadow-sm h-100">
            <h6 class="text-muted">Customers</h6>
            <h2 class="fw-bold mb-0 text-success"><?php echo $total_users; ?></h2>
        </div>
    </div>
</div>

<div class="card p-4 border-0 shadow-sm">
    <h5 class="fw-bold mb-3">Recent Orders</h5>
    <div class="table-responsive">
        <table class="table align-middle" style="min-width: 600px;">
            <thead class="bg-light">
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recent_orders as $o): ?>
                <tr>
                    <td>#<?php echo $o['id']; ?></td>
                    <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
                    <td class="fw-bold">$<?php echo number_format($o['total_amount'], 2); ?></td>
                    <td>
                        <?php 
                        $badge = match($o['status']) {
                            'Pending' => 'bg-warning text-dark',
                            'Shipped' => 'bg-primary',
                            'Delivered' => 'bg-success',
                            'Cancelled' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?php echo $badge; ?>"><?php echo $o['status']; ?></span>
                    </td>
                    <td><?php echo date('M d', strtotime($o['created_at'])); ?></td>
                    <td><a href="orders.php" class="btn btn-sm btn-outline-dark">Manage</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>