<?php
include '../db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $cat = $_POST['category_id'];
    $desc = $_POST['description'];
    $img = $_POST['image']; 
    $stock = $_POST['stock'];
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    $stmt = $conn->prepare("INSERT INTO products (name, category_id, price, description, image, stock, is_featured, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $cat, $price, $desc, $img, $stock, $featured]);
    
    echo "<script>window.location.href='products.php';</script>";
}

$cats = $conn->query("SELECT * FROM categories")->fetchAll();
$products = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h3 class="fw-bold mb-0">Product Management</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal" style="background: var(--admin-accent); border:none;">
        <i class="fa-solid fa-plus me-2"></i> Add Product
    </button>
</div>

<div class="card p-0 overflow-hidden border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 700px;">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Product</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo $p['image']; ?>" class="rounded me-3" width="40" height="40" style="object-fit:cover;">
                            <span class="fw-bold text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($p['name']); ?></span>
                            <?php if($p['is_featured']): ?>
                                <span class="badge bg-warning text-dark ms-2" style="font-size:0.6rem;">FEATURED</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>$<?php echo $p['price']; ?></td>
                    <td><span class="badge bg-light text-dark border"><?php echo $p['cat_name']; ?></span></td>
                    <td>
                        <?php echo $p['stock'] > 0 ? '<span class="text-success fw-bold">'.$p['stock'].'</span>' : '<span class="text-danger">Out</span>'; ?>
                    </td>
                    <td>
                        <a href="#" class="btn btn-sm btn-light text-danger"><i class="fa-solid fa-trash"></i></a>
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
                <h5 class="modal-title fw-bold">Add New Product</h5>
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
                            <div class="form-text">Use a link from Unsplash or similar.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="featured" id="feat">
                                <label class="form-check-label" for="feat">Show in 'Featured Products' on Home</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--admin-accent); border:none;">Upload Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>