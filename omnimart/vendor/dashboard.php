<?php
include '../db.php';
include 'includes/header.php';

$vendor_id = 0;
$stmt = $conn->prepare("SELECT id FROM vendors WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$v = $stmt->fetch();
if($v) $vendor_id = $v['id'];

$my_products = $conn->prepare("SELECT COUNT(*) FROM products WHERE vendor_id = ?");
$my_products->execute([$vendor_id]);
$prod_count = $my_products->fetchColumn();
?>

<div class="d-flex justify-content-between align-items-center mb-4 mb-md-5 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h3>
        <p class="text-muted mb-0">Here is what's happening with your store today.</p>
    </div>
    <a href="products.php" class="btn btn-success px-4 py-2 fw-bold rounded-pill">
        <i class="fa-solid fa-plus me-2"></i> Add Product
    </a>
</div>

<div class="row g-3 g-md-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-light p-3 rounded-circle text-success me-3">
                    <i class="fa-solid fa-box fa-2x"></i>
                </div>
                <h6 class="text-muted mb-0">Total Products</h6>
            </div>
            <h2 class="fw-bold mb-0"><?php echo $prod_count; ?></h2>
        </div>
    </div>
    
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-light p-3 rounded-circle text-primary me-3">
                    <i class="fa-solid fa-dollar-sign fa-2x"></i>
                </div>
                <h6 class="text-muted mb-0">Earnings</h6>
            </div>
            <h2 class="fw-bold mb-0">$0.00</h2>
            <small class="text-muted">Coming Soon</small>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card bg-dark text-white p-4 h-100 rounded-4" style="background: #1e293b;">
            <h5 class="fw-bold mb-3">Vendor Guide</h5>
            <p class="small opacity-75">Start by adding your products. Once approved by Admin, they will appear on the main site.</p>
            <button class="btn btn-sm btn-light text-dark fw-bold w-100">Read Guidelines</button>
        </div>
    </div>
</div>

</body>
</html>