<?php
include '../db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = strtoupper($_POST['code']);
    $val = $_POST['value'];
    $type = $_POST['type'];
    
    $stmt = $conn->prepare("INSERT INTO coupons (code, value, discount_type) VALUES (?, ?, ?)");
    $stmt->execute([$code, $val, $type]);
    echo "<script>window.location.href='coupons.php';</script>";
}

if (isset($_GET['del'])) {
    $conn->prepare("DELETE FROM coupons WHERE id=?")->execute([$_GET['del']]);
    echo "<script>window.location.href='coupons.php';</script>";
}

$coupons = $conn->query("SELECT * FROM coupons ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Coupon Manager</h3>
</div>

<div class="row g-4">
    <div class="col-12 col-md-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Create Coupon</h5>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Coupon Code</label>
                    <input type="text" name="code" class="form-control text-uppercase" placeholder="SALE2024" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Discount Value</label>
                    <input type="number" name="value" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="percent">Percentage (%)</option>
                        <option value="fixed">Fixed Amount ($)</option>
                    </select>
                </div>
                <button class="btn btn-primary w-100" style="background: var(--admin-accent); border:none;">Create Coupon</button>
            </form>
        </div>
    </div>

    <div class="col-12 col-md-8">
        <div class="card p-0 border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Code</th>
                            <th>Discount</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($coupons as $c): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary"><?php echo $c['code']; ?></td>
                            <td>
                                <?php echo $c['discount_type'] == 'percent' ? $c['value'].'%' : '$'.$c['value']; ?> Off
                            </td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td class="text-end pe-4">
                                <a href="?del=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>