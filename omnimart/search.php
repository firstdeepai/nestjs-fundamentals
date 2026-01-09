<?php
include 'db.php';
include 'includes/header.php';

$where = "1";
$params = [];

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $where .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%".$_GET['q']."%";
    $params[] = "%".$_GET['q']."%";
}

if (isset($_GET['cat']) && !empty($_GET['cat'])) {
    $where .= " AND category_id = ?";
    $params[] = $_GET['cat'];
}

$stmt = $conn->prepare("SELECT * FROM products WHERE $where ORDER BY id DESC");
$stmt->execute($params);
$products = $stmt->fetchAll();

$cats = $conn->query("SELECT * FROM categories")->fetchAll();
?>

<div class="bg-light py-3 border-bottom">
    <div class="container d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-bold">Shop</h4>
        <span class="text-muted"><?php echo count($products); ?> results found</span>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px; z-index: 1;">
                
                <form action="search.php" method="GET">
                    
                    <?php if(isset($_GET['q'])): ?>
                        <input type="hidden" name="q" value="<?php echo htmlspecialchars($_GET['q']); ?>">
                    <?php endif; ?>

                    <h6 class="fw-bold mb-3 text-uppercase small ls-1">Categories</h6>
                    <div class="list-group list-group-flush mb-4">
                        <a href="search.php<?php echo isset($_GET['q']) ? '?q='.$_GET['q'] : ''; ?>" 
                           class="list-group-item list-group-item-action border-0 px-0 <?php echo !isset($_GET['cat']) ? 'fw-bold text-primary' : ''; ?>">
                           All Products
                        </a>
                        <?php foreach($cats as $c): ?>
                            <div class="form-check my-1">
                                <input class="form-check-input" type="radio" name="cat" value="<?php echo $c['id']; ?>" 
                                    id="cat<?php echo $c['id']; ?>" 
                                    <?php echo (isset($_GET['cat']) && $_GET['cat'] == $c['id']) ? 'checked' : ''; ?>
                                    onchange="this.form.submit()">
                                <label class="form-check-label" for="cat<?php echo $c['id']; ?>">
                                    <?php echo htmlspecialchars($c['name']); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if(isset($_GET['cat']) || isset($_GET['q'])): ?>
                        <a href="search.php" class="btn btn-outline-danger w-100 rounded-pill btn-sm mt-2">Reset Filters</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="col-md-9">
            <div class="row g-4">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $p): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-card-hover">
                                <div class="position-relative" style="height: 220px; overflow: hidden;">
                                    <img src="<?php echo htmlspecialchars($p['image']); ?>" class="w-100 h-100" style="object-fit: cover;">
                                    <?php if($p['stock'] == 0): ?>
                                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-white opacity-50 d-flex align-items-center justify-content-center">
                                            <span class="badge bg-dark">Sold Out</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-bold text-truncate mb-2"><?php echo htmlspecialchars($p['name']); ?></h6>
                                    <p class="text-muted small mb-3 text-truncate"><?php echo htmlspecialchars($p['description']); ?></p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="fw-bold text-primary mb-0">₹<?php echo number_format($p['price'], 2); ?></h5>
                                        <a href="product.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-dark rounded-circle btn-sm" style="width: 32px; height: 32px; display:flex; align-items:center; justify-content:center;">
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                        <h5>No products found.</h5>
                        <p class="text-muted">Try selecting a different category.</p>
                        <a href="search.php" class="btn btn-outline-primary mt-3 rounded-pill">View All Products</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .product-card-hover { transition: transform 0.2s; }
    .product-card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
</style>

<?php include 'includes/footer.php'; ?>