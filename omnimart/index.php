<?php
$page_title = "Home";
$page_desc = "Discover the best deals on Electronics, Fashion, and more at OmniMart.";
include 'db.php';
include 'includes/header.php';

$banner_stmt = $conn->query("SELECT * FROM banners WHERE status='active' ORDER BY id DESC");
$banners = $banner_stmt->fetchAll();

$feat_stmt = $conn->query("SELECT * FROM products WHERE is_featured = 1 LIMIT 4");
$featured_products = $feat_stmt->fetchAll();

$cat_stmt = $conn->query("SELECT * FROM categories LIMIT 6");
$categories = $cat_stmt->fetchAll();

$new_stmt = $conn->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
$latest_products = $new_stmt->fetchAll();
?>

<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php if (count($banners) > 0): ?>
            <?php foreach ($banners as $index => $banner): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="hero-image-container" style="height: 500px; position: relative;">
                        <img src="<?php echo htmlspecialchars($banner['image']); ?>" class="d-block w-100 h-100" style="object-fit: cover; filter: brightness(0.6);">
                        <div class="carousel-caption d-block text-start" style="bottom: 20%; left: 5%; right: 5%;">
                            <h5 class="text-uppercase fw-bold text-warning mb-1 animate__animated animate__fadeInDown hero-subtitle">Welcome to OmniMart</h5>
                            <h1 class="display-3 fw-bold mb-3 animate__animated animate__fadeInLeft hero-title"><?php echo htmlspecialchars($banner['title']); ?></h1>
                            <p class="lead mb-4 animate__animated animate__fadeInUp hero-desc d-none d-sm-block"><?php echo htmlspecialchars($banner['subtitle']); ?></p>
                            <a href="search.php" class="btn btn-primary rounded-pill px-4 px-md-5 fw-bold hero-btn" style="background: #f97316; border:none;">
                                Shop Now <i class="fa-solid fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="carousel-item active">
                <div class="d-flex align-items-center justify-content-center bg-dark text-white" style="height: 400px;">
                    <h2>No Banners Found.</h2>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<div class="container my-5">
    <div class="text-center mb-4 mb-md-5">
        <h2 class="fw-bold display-6">Shop by Category</h2>
        <p class="text-muted">Browse our wide range of premium collections</p>
    </div>
    <div class="row g-3 g-md-4 justify-content-center">
        <?php foreach ($categories as $cat): ?>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="search.php?cat=<?php echo $cat['id']; ?>" class="text-decoration-none text-dark">
                    <div class="card border-0 shadow-sm text-center py-4 rounded-4 hover-up" style="background: #f8fafc;">
                        <i class="fa-solid fa-layer-group fa-2x mb-3 text-primary"></i>
                        <h6 class="fw-bold mb-0 text-truncate px-2"><?php echo htmlspecialchars($cat['name']); ?></h6>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if (count($featured_products) > 0): ?>
<section class="bg-light py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold fs-2">Featured Products</h2>
            <a href="search.php?filter=featured" class="btn btn-outline-dark rounded-pill btn-sm px-3">View All</a>
        </div>
        
        <div class="row g-3 g-md-4">
            <?php foreach ($featured_products as $p): ?>
                <div class="col-6 col-md-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-card-hover">
                        <div class="position-relative" style="height: 200px; overflow: hidden;">
                            <img src="<?php echo !empty($p['image']) ? $p['image'] : 'assets/no-img.png'; ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s;">
                            <?php if($p['stock'] == 0): ?>
                                <span class="position-absolute top-0 start-0 bg-dark text-white px-2 py-1 m-2 rounded-pill small fw-bold" style="font-size: 0.7rem;">Sold Out</span>
                            <?php elseif($p['is_featured']): ?>
                                <span class="position-absolute top-0 start-0 bg-warning text-dark px-2 py-1 m-2 rounded-pill small fw-bold" style="font-size: 0.7rem;">Featured</span>
                            <?php endif; ?>
                            
                            <div class="position-absolute bottom-0 end-0 m-2 d-flex gap-2">
                                <a href="product.php?id=<?php echo $p['id']; ?>" class="btn btn-light rounded-circle shadow-sm" style="width: 35px; height: 35px; display:flex; align-items:center; justify-content:center;">
                                    <i class="fa-solid fa-eye text-dark" style="font-size: 0.9rem;"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-dark text-truncate mb-1" style="font-size: 1rem;"><?php echo htmlspecialchars($p['name']); ?></h6>
                            <p class="text-muted small mb-2 text-truncate"><?php echo htmlspecialchars($p['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold text-primary mb-0" style="font-size: 1.1rem;"><sup style="font-size:0.7em">₹</sup><?php echo number_format($p['price'], 2); ?></h5>
                                
                                <form class="ajax-add-form" method="POST">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button class="btn btn-sm btn-outline-dark rounded-circle" style="width: 30px; height: 30px; padding: 0;"><i class="fa-solid fa-plus"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold fs-2">New Arrivals</h2>
        <a href="search.php" class="btn btn-outline-dark rounded-pill btn-sm px-3">View All</a>
    </div>

    <div class="row g-3 g-md-4">
        <?php foreach ($latest_products as $p): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-card-hover">
                    <div class="position-relative" style="height: 220px; overflow: hidden;">
                        <?php $img = !empty($p['image']) ? $p['image'] : 'https://via.placeholder.com/300'; ?>
                        <img src="<?php echo $img; ?>" class="w-100 h-100 product-img" style="object-fit: cover; transition: transform 0.5s ease;">
                        
                        <?php if($p['stock'] == 0): ?>
                            <span class="position-absolute top-0 start-0 bg-secondary text-white px-2 py-1 m-2 rounded-pill small fw-bold shadow-sm" style="font-size: 0.7rem;">Out of Stock</span>
                        <?php endif; ?>

                        <div class="position-absolute bottom-0 end-0 m-2">
                            <a href="product.php?id=<?php echo $p['id']; ?>" class="btn btn-light rounded-circle shadow-lg hover-scale" style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center;">
                                <i class="fa-solid fa-arrow-right text-dark" style="font-size: 0.9rem;"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark text-truncate mb-1" style="font-size: 1rem;"><?php echo htmlspecialchars($p['name']); ?></h6>
                        <p class="text-muted small mb-2 text-truncate"><?php echo htmlspecialchars($p['description']); ?></p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap" style="gap: 5px;">
                            <h5 class="fw-bold text-primary mb-0" style="font-family: 'Outfit', sans-serif; font-size: 1.1rem;">
                                <sup style="font-size: 0.6em; top: -0.2em;">₹</sup><?php echo number_format($p['price'], 2); ?>
                            </h5>
                            
                            <form class="ajax-add-form" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm fw-bold" style="font-size: 0.8rem;">
                                    Add <i class="fa-solid fa-bag-shopping ms-1"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-6 col-md-3">
                <i class="fa-solid fa-truck-fast fa-2x mb-2 text-warning"></i>
                <h6 class="fw-bold">Fast Delivery</h6>
            </div>
            <div class="col-6 col-md-3">
                <i class="fa-solid fa-shield-halved fa-2x mb-2 text-warning"></i>
                <h6 class="fw-bold">Secure Payment</h6>
            </div>
            <div class="col-6 col-md-3">
                <i class="fa-solid fa-rotate-left fa-2x mb-2 text-warning"></i>
                <h6 class="fw-bold">Easy Returns</h6>
            </div>
            <div class="col-6 col-md-3">
                <i class="fa-solid fa-headset fa-2x mb-2 text-warning"></i>
                <h6 class="fw-bold">24/7 Support</h6>
            </div>
        </div>
    </div>
    <hr class="border-secondary">
</div>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
  <div id="liveToast" class="toast align-items-center text-white bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body fw-bold">
        <span id="toastMessage">Item added to cart!</span>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<style>
    .hover-up:hover { transform: translateY(-5px); transition: 0.3s; background: white !important; }
    .hover-scale:hover { transform: scale(1.1); transition: 0.2s; }
    .product-card-hover { transition: box-shadow 0.3s ease; }
    .product-card-hover:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
    .product-card-hover:hover .product-img { transform: scale(1.05); }

    @media (max-width: 768px) {
        .hero-image-container { height: 350px !important; }
        .hero-title { font-size: 1.8rem !important; margin-bottom: 0.5rem !important; }
        .hero-subtitle { font-size: 0.8rem !important; }
        .hero-btn { padding: 0.5rem 1.5rem !important; font-size: 0.9rem !important; }
        .hero-desc { display: none !important; }
    }
</style>

<script>
    document.querySelectorAll('.ajax-add-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let formData = new FormData(this);
            formData.append('ajax_add', '1');

            fetch('cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    showToast(data.message, 'success');
                    updateCartBadge(data.cart_count);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    function showToast(msg, type) {
        let toastEl = document.getElementById('liveToast');
        let msgEl = document.getElementById('toastMessage');
        msgEl.textContent = msg;
        
        if(type === 'error') {
            msgEl.innerHTML = '<i class="fa-solid fa-circle-xmark text-danger me-2"></i>' + msg;
        } else {
            msgEl.innerHTML = '<i class="fa-solid fa-circle-check text-success me-2"></i>' + msg;
        }

        let toast = new bootstrap.Toast(toastEl);
        toast.show();
    }

    function updateCartBadge(count) {
        let badge = document.querySelector('.badge-cart');
        if (badge) {
            badge.textContent = count;
            if (count === 0) badge.classList.add('d-none');
            else badge.classList.remove('d-none');
        } else {
            if (count > 0) {
                let cartIcon = document.querySelector('.fa-bag-shopping');
                if(cartIcon && cartIcon.parentElement) {
                    let span = document.createElement('span');
                    span.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill badge-cart';
                    span.textContent = count;
                    cartIcon.parentElement.appendChild(span);
                }
            }
        }
    }
</script>

<?php include 'includes/footer.php'; ?>