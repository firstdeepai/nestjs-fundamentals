<?php
include '../db.php';
include 'includes/header.php';

$stmt = $conn->prepare("SELECT id FROM vendors WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$vendor_id = $stmt->fetchColumn();

$sql = "SELECT oi.*, o.status as order_status, o.created_at, p.name as product_name, p.image 
        FROM order_items oi 
        JOIN orders o ON oi.order_id = o.id 
        JOIN products p ON oi.product_id = p.id 
        WHERE p.vendor_id = ? 
        ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$vendor_id]);
$orders = $stmt->fetchAll();
?>

<h3 class="fw-bold mb-4">Orders for My Products</h3>

<div class="card p-0 border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 600px;">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Order ID</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Total Earned</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                <tr>
                    <td class="ps-4 text-muted">#<?php echo $o['order_id']; ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="<?php echo $o['image']; ?>" class="rounded me-2" width="30" height="30" style="object-fit:cover;">
                            <span class="text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($o['product_name']); ?></span>
                        </div>
                    </td>
                    <td><?php echo $o['quantity']; ?></td>
                    <td class="fw-bold text-success">$<?php echo number_format($o['price'] * $o['quantity'], 2); ?></td>
                    <td>
                        <span class="badge bg-light text-dark border"><?php echo $o['order_status']; ?></span>
                    </td>
                    <td class="small text-muted"><?php echo date('M d', strtotime($o['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>