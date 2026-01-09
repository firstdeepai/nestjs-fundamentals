<?php
session_start();
include 'db.php';
$page_title = "Order Details";
include 'includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$order_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "<div class='container py-5'><h3>Order not found.</h3></div>";
    include 'includes/footer.php';
    exit;
}

$items_stmt = $conn->prepare("
    SELECT oi.*, p.name, p.image 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$items_stmt->execute([$order_id]);
$items = $items_stmt->fetchAll();
?>

<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="orders.php" class="text-decoration-none text-muted">My Orders</a></li>
                <li class="breadcrumb-item active">Order #<?php echo $order['id']; ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Order Items</h5>
                </div>
                <div class="card-body p-0">
                    <?php foreach($items as $item): ?>
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                        <div class="ms-3 flex-grow-1">
                            <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                            <small class="text-muted">Qty: <?php echo $item['quantity']; ?> x $<?php echo number_format($item['price'], 2); ?></small>
                        </div>
                        <div class="fw-bold text-end">
                            $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3">Order Summary</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Order ID</span>
                    <span class="fw-bold">#<?php echo $order['id']; ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Date</span>
                    <span class="fw-bold"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Status</span>
                    <span class="badge bg-primary"><?php echo $order['status']; ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold fs-5">Total Paid</span>
                    <span class="fw-bold fs-5 text-success">$<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>