<?php
include '../db.php';
include 'includes/header.php';

if (isset($_POST['update_status'])) {
    $oid = $_POST['order_id'];
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("SELECT status FROM orders WHERE id=?");
    $stmt->execute([$oid]);
    $current_status = $stmt->fetchColumn();

    if ($current_status != 'Cancelled' && $new_status == 'Cancelled') {
        $items = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id=?");
        $items->execute([$oid]);
        foreach ($items->fetchAll() as $item) {
            $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?")
                 ->execute([$item['quantity'], $item['product_id']]);
        }
    }

    $conn->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$new_status, $oid]);
    echo "<script>window.location.href='orders.php';</script>";
}

$orders = $conn->query("SELECT o.*, u.name as c_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC")->fetchAll();
?>

<h3 class="fw-bold mb-4">Order Management</h3>

<div class="card p-0 overflow-hidden border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">#ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Current Status</th>
                    <th>Update Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                <tr>
                    <td class="ps-4 fw-bold">#<?php echo $o['id']; ?></td>
                    <td>
                        <div class="fw-bold"><?php echo htmlspecialchars($o['c_name']); ?></div>
                        <small class="text-muted"><?php echo $o['email']; ?></small>
                    </td>
                    <td class="fw-bold">$<?php echo number_format($o['total_amount'], 2); ?></td>
                    <td>
                        <span class="badge rounded-pill bg-light text-dark border"><?php echo $o['status']; ?></span>
                    </td>
                    <td>
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                            <select name="status" class="form-select form-select-sm" style="width: 120px;">
                                <option value="Pending" <?php if($o['status']=='Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Processing" <?php if($o['status']=='Processing') echo 'selected'; ?>>Processing</option>
                                <option value="Shipped" <?php if($o['status']=='Shipped') echo 'selected'; ?>>Shipped</option>
                                <option value="Delivered" <?php if($o['status']=='Delivered') echo 'selected'; ?>>Delivered</option>
                                <option value="Cancelled" <?php if($o['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-sm btn-dark">Update</button>
                        </form>
                    </td>
                    <td class="text-muted small"><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>