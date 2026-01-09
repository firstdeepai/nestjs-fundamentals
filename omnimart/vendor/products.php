<?php
include '../db.php';
include 'includes/header.php';

$stmt = $conn->prepare("SELECT id FROM vendors WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$vendor = $stmt->fetch();
$vendor_id = $vendor['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $cat = $_POST['category_id'];
    $desc = $_POST['description'];
    $img = $_POST['image'];
    $stock = $_POST['stock'];
    
    $stmt = $conn->prepare("INSERT INTO products (vendor_id, name, category_id, price, description, image, stock, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$vendor_id, $name, $cat, $price, $desc, $img, $stock]);
    
    echo "<script>window.location.href='products.php';</script>";
}

if (isset($_GET['del'])) {
    $conn->prepare("DELETE FROM products WHERE id=? AND vendor_id=?")->execute([$_GET['del'], $vendor_id]);
    echo "<script>window.location.href='products.php';</script>";
}

$cats = $conn->query("SELECT * FROM categories")->fetchAll();
$products = $conn->prepare("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.vendor_id = ? ORDER BY p.id DESC");
$products->execute([$vendor_id]);
$my_products = $products->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h3 class="fw-bold mb-0">My Products</h3>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fa-solid fa-plus me-2"></i> Add New
    </button>
</div>

<div class="card p-0 shadow-sm border-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 700px;">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Product</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Sales</th>
                    <th class="text-end pe-4">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($my_products as $p): ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo $p['image']; ?>" class="rounded me-3" width="40" height="40" style="object-fit:cover;">
                            <span class="fw-bold text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($p['name']); ?></span>
                        </div>
                    </td>
                    <td>$<?php echo $p['price']; ?></td>
                    <td>
                        <?php echo $p['stock'] > 0 ? '<span class="badge bg-success">In Stock ('.$p['stock'].')</span>' : '<span class="badge bg-danger">Out of Stock</span>'; ?>
                    </td>
                    <td>-</td>
                    <td class="text-end pe-4">
                        <a href="?del=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?');"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Product to Store</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <?php foreach($cats as $c): echo "<option value='{$c['id']}'>{$c['name']}</option>"; endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Price ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Stock Qty</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Image URL</label>
                            <input type="url" name="image" class="form-control" placeholder="https://..." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Publish Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>