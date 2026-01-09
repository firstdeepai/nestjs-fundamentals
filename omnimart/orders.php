<?php
include 'db.php';
$page_title = "My Orders";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$stmt = $conn->prepare("
    SELECT o.*, count(oi.id) as item_count 
    FROM orders o 
    LEFT JOIN order_items oi ON o.id = oi.order_id 
    WHERE o.user_id = ? 
    GROUP BY o.id 
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<div class="container py-5" style="min-height: 70vh;">
    <h2 class="fw-bold mb-4">My Order History</h2>

    <?php if(isset($_GET['placed'])): ?>
        <div class="alert alert-success d-flex align-items-center rounded-3 shadow-sm mb-4">
            <i class="fa-solid fa-check-circle fs-4 me-3"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-0">Order Placed Successfully!</h5>
                <p class="mb-0">Thank you for shopping with OmniMart.</p>
            </div>
        </div>
    <?php endif; ?>

    <?php if(count($orders) > 0): ?>
        <div class="row g-4">
            <?php foreach($orders as $order): ?>
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <div>
                                <span class="text-muted small text-uppercase fw-bold">Order #<?php echo $order['id']; ?></span>
                                <div class="small text-muted"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                            </div>
                            <div>
                                <?php 
                                    $statusColor = match($order['status']) {
                                        'Pending' => 'bg-warning text-dark',
                                        'Shipped' => 'bg-primary',
                                        'Delivered' => 'bg-success',
                                        'Cancelled' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                ?>
                                <span class="badge rounded-pill <?php echo $statusColor; ?> px-3"><?php echo $order['status']; ?></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="fw-bold">$<?php echo number_format($order['total_amount'], 2); ?></h5>
                                    <p class="mb-0 text-muted"><?php echo $order['item_count']; ?> Items in this order</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a class="btn btn-outline-dark rounded-pill btn-sm" href="order_details.php?id=<?php echo $order['id']; ?>" ...>View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-light rounded-4">
            <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
            <h4>No orders yet</h4>
            <p class="text-muted">Start adding items to your cart.</p>
            <a href="index.php" class="btn btn-primary rounded-pill mt-3" style="background: #f97316; border:none;">Browse Products</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>