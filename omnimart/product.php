<?php
session_start();
include 'db.php';

if (isset($_POST['ajax_wishlist_toggle']) && isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    $uid = $_SESSION['user_id'];
    $pid = (int)$_POST['product_id'];

    $check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $check->execute([$uid, $pid]);

    if ($check->rowCount() > 0) {
        $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?")->execute([$uid, $pid]);
        echo json_encode(['status' => 'removed']);
    } else {
        $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)")->execute([$uid, $pid]);
        echo json_encode(['status' => 'added']);
    }
    exit; 
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    if (!$user_id) {
        echo "<script>alert('Please login to review'); window.location.href='product.php?id=$id';</script>";
        exit;
    } else {
        $check_purchase = $conn->prepare("SELECT COUNT(*) FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'Delivered'");
        $check_purchase->execute([$user_id, $id]);
        
        if ($check_purchase->fetchColumn() > 0) {
            $check_dup = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND product_id = ?");
            $check_dup->execute([$user_id, $id]);
            
            if ($check_dup->rowCount() == 0) {
                $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$id, $user_id, $_POST['rating'], $_POST['comment']]);
                header("Location: product.php?id=$id");
                exit;
            } else {
                echo "<script>alert('You have already reviewed this product.'); window.location.href='product.php?id=$id';</script>";
            }
        } else {
            echo "<script>alert('Verified purchase required for review.'); window.location.href='product.php?id=$id';</script>";
        }
    }
}

$stmt = $conn->prepare("SELECT p.*, c.name as category_name, v.name as vendor_name FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN vendors v ON p.vendor_id = v.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    include 'includes/header.php';
    echo "<div class='container py-5 text-center'><h3>Product not found.</h3><a href='index.php' class='btn btn-dark mt-3'>Back to Home</a></div>";
    include 'includes/footer.php';
    exit;
}

$reviews = $conn->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$reviews->execute([$id]);
$all_reviews = $reviews->fetchAll();

$avg_rating = 0;
if (count($all_reviews) > 0) {
    $sum = 0;
    foreach($all_reviews as $r) $sum += $r['rating'];
    $avg_rating = round($sum / count($all_reviews), 1);
}

$in_wishlist = false;
if ($user_id) {
    $check_wish = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $check_wish->execute([$user_id, $id]);
    if ($check_wish->rowCount() > 0) $in_wishlist = true;
}

$can_review = false;
if ($user_id) {
    $check_purchase = $conn->prepare("SELECT COUNT(*) FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'Delivered'");
    $check_purchase->execute([$user_id, $id]);
    $check_existing = $conn->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ? AND product_id = ?");
    $check_existing->execute([$user_id, $id]);
    if ($check_purchase->fetchColumn() > 0 && $check_existing->fetchColumn() == 0) $can_review = true;
}

$page_title = $product['name'];
include 'includes/header.php';
?>

<style>
    .currency-symbol {
        font-size: 0.6em;
        vertical-align: top;
        margin-right: 1px;
        position: relative;
        top: 0.1em; 
        font-weight: 600;
    }
    
    .qty-input-group {
        border: 1px solid #dee2e6;
        border-radius: 50px;
        overflow: hidden;
        display: flex;
        width: 140px;
    }
    .qty-btn {
        background: #fff;
        border: none;
        width: 40px;
        font-weight: bold;
        color: #333;
        transition: 0.2s;
    }
    .qty-btn:hover { background: #f1f5f9; color: var(--accent, #f97316); }
    .qty-input {
        border: none;
        text-align: center;
        width: 60px;
        font-weight: 600;
        --moz-appearance: textfield;
    }
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    .wishlist-btn {
        cursor: pointer;
        transition: transform 0.2s;
        font-size: 1.5rem;
    }
    .wishlist-btn:hover { transform: scale(1.1); }
    .wishlist-active { color: #dc2626; }
    .wishlist-inactive { color: #94a3b8; }
</style>

<div class="bg-light py-3 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="search.php?cat=<?php echo $product['category_id']; ?>" class="text-decoration-none text-muted"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
                <li class="breadcrumb-item active text-dark fw-bold"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5 mb-5">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                <div class="position-relative bg-white p-4 text-center" style="min-height: 500px; display:flex; align-items:center; justify-content:center;">
                     <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid" style="max-height: 450px; object-fit: contain;">
                     <?php if($product['is_featured']): ?>
                        <span class="position-absolute top-0 start-0 m-4 badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">Featured</span>
                     <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="ps-lg-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-uppercase text-muted fw-bold small tracking-wide"><?php echo htmlspecialchars($product['vendor_name'] ?? 'OmniMart Store'); ?></span>
                        <h1 class="display-6 fw-bold mt-2 mb-2"><?php echo htmlspecialchars($product['name']); ?></h1>
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div class="text-warning small">
                                <?php
                                for($i=1; $i<=5; $i++) {
                                    echo $i <= $avg_rating ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                                }
                                ?>
                            </div>
                            <span class="text-muted small">(<?php echo count($all_reviews); ?> Reviews)</span>
                            <span class="text-muted small">|</span>
                            <span class="text-success small fw-bold"><i class="fa-solid fa-check-circle"></i> Verified</span>
                        </div>
                    </div>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="wishlist-btn <?php echo $in_wishlist ? 'wishlist-active' : 'wishlist-inactive'; ?>" 
                             onclick="toggleWishlist(<?php echo $product['id']; ?>)" 
                             id="wishlist-icon-<?php echo $product['id']; ?>">
                            <i class="<?php echo $in_wishlist ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="wishlist-btn wishlist-inactive" title="Login to add to wishlist">
                            <i class="fa-regular fa-heart"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="display-5 text-primary mb-3 fw-bold">
                    <sup class="currency-symbol">₹</sup><?php echo number_format($product['price'], 2); ?>
                </div>

                <p class="text-muted lead mb-4" style="font-size: 1.1rem; line-height: 1.6;">
                    <?php echo substr(htmlspecialchars($product['description']), 0, 150) . '...'; ?>
                    <a href="#description" class="text-decoration-none small fw-bold">Read More</a>
                </p>

                <div class="card border-0 bg-light rounded-4 p-4 mb-4">
                    <form action="cart.php" method="POST" id="addToCartForm">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        
                        <input type="hidden" name="redirect_checkout" id="redirectCheckout" value="0">

                        <div class="d-flex align-items-center gap-4 mb-4">
                            <div>
                                <label class="small fw-bold text-muted mb-1 d-block">Quantity</label>
                                <div class="qty-input-group bg-white">
                                    <button type="button" class="qty-btn" onclick="updateQty(-1)">-</button>
                                    <input type="number" name="quantity" id="qtyInput" value="1" min="1" max="<?php echo $product['stock']; ?>" class="qty-input" readonly>
                                    <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
                                </div>
                            </div>

                            <div class="mt-4">
                                <?php if($product['stock'] > 0): ?>
                                    <span class="text-success fw-bold small"><i class="fa-solid fa-circle-check me-1"></i> In Stock</span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold small"><i class="fa-solid fa-circle-xmark me-1"></i> Sold Out</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row g-2">
                            <?php if($product['stock'] > 0): ?>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-sm" onclick="setRedirect(0)">
                                        Add to Cart
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg" onclick="setRedirect(1)" style="background: #f97316; border:none;">
                                        Buy Now
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="col-12">
                                    <button type="button" class="btn btn-secondary w-100 py-3 rounded-pill" disabled>Currently Unavailable</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="row g-3 small text-muted">
                    <div class="col-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-truck-fast text-dark fs-5"></i>
                        <span style="line-height: 1.2;">Free<br>Shipping</span>
                    </div>
                    <div class="col-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-dark fs-5"></i>
                        <span style="line-height: 1.2;">Secure<br>Payment</span>
                    </div>
                    <div class="col-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-rotate-left text-dark fs-5"></i>
                        <span style="line-height: 1.2;">7 Day<br>Returns</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5" id="description">
        <div class="col-12">
            <ul class="nav nav-pills mb-4 border-bottom pb-2" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#desc-tab">Description</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#specs-tab">Specifications</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#reviews-tab">Reviews (<?php echo count($all_reviews); ?>)</button>
                </li>
            </ul>
            
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="desc-tab">
                    <div class="bg-white p-5 rounded-4 shadow-sm border">
                        <h4 class="fw-bold mb-4">Product Overview</h4>
                        <p class="text-muted" style="line-height: 1.8; white-space: pre-line;">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>
                    </div>
                </div>

                <div class="tab-pane fade" id="specs-tab">
                    <div class="bg-white p-5 rounded-4 shadow-sm border">
                        <h4 class="fw-bold mb-4">Technical Specifications</h4>
                        <table class="table table-striped table-hover">
                            <tbody>
                                <tr><th width="30%">Brand</th><td>OmniMart Select</td></tr>
                                <tr><th>Category</th><td><?php echo htmlspecialchars($product['category_name']); ?></td></tr>
                                <tr><th>Stock</th><td><?php echo $product['stock']; ?> Units</td></tr>
                                <tr><th>Vendor</th><td><?php echo htmlspecialchars($product['vendor_name'] ?? 'Official Store'); ?></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="reviews-tab">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="bg-white p-4 rounded-4 shadow-sm border text-center h-100">
                                <h1 class="display-1 fw-bold text-primary mb-0"><?php echo $avg_rating; ?></h1>
                                <div class="text-warning fs-4 mb-2">
                                    <?php for($i=1; $i<=5; $i++) echo $i <= round($avg_rating) ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>'; ?>
                                </div>
                                <p class="text-muted">Based on <?php echo count($all_reviews); ?> Reviews</p>
                                <hr>
                                <div class="d-grid">
                                    <?php if($can_review): ?>
                                        <button class="btn btn-dark rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#reviewForm">Write a Review</button>
                                    <?php elseif($user_id): ?>
                                        <button class="btn btn-light border rounded-pill" disabled>Purchase to Review</button>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-outline-primary rounded-pill">Login to Review</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="collapse mb-4" id="reviewForm">
                                <div class="card p-4 border-0 shadow-sm bg-light rounded-4">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-muted">Your Rating</label>
                                            <div class="rating-select">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="rating" value="5" checked> <label class="text-warning">⭐⭐⭐⭐⭐</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="rating" value="4"> <label class="text-warning">⭐⭐⭐⭐</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <textarea name="comment" class="form-control rounded-3" rows="3" placeholder="Write your review..." required></textarea>
                                        </div>
                                        <button type="submit" name="submit_review" class="btn btn-primary rounded-pill px-4 fw-bold">Submit</button>
                                    </form>
                                </div>
                            </div>

                            <?php if(count($all_reviews) > 0): ?>
                                <?php foreach($all_reviews as $r): ?>
                                    <div class="card border-0 shadow-sm rounded-4 mb-3 p-3">
                                        <div class="d-flex gap-3">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px; font-weight:bold;">
                                                <?php echo strtoupper(substr($r['name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($r['name']); ?> <span class="badge bg-success-subtle text-success small rounded-pill"><i class="fa-solid fa-check"></i> Verified</span></h6>
                                                <div class="text-warning small mb-1">
                                                    <?php for($i=0; $i<$r['rating']; $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
                                                </div>
                                                <p class="text-muted mb-0 small"><?php echo htmlspecialchars($r['comment']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-center text-muted py-4">No reviews yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateQty(change) {
        let input = document.getElementById('qtyInput');
        let val = parseInt(input.value);
        let max = parseInt(input.getAttribute('max'));
        let newVal = val + change;
        
        if(newVal >= 1 && newVal <= max) {
            input.value = newVal;
        }
    }

    function setRedirect(isCheckout) {
        document.getElementById('redirectCheckout').value = isCheckout ? "1" : "0";
    }

    function toggleWishlist(pid) {
        let iconContainer = document.getElementById('wishlist-icon-' + pid);
        let icon = iconContainer.querySelector('i');
        
        let isCurrentlyActive = iconContainer.classList.contains('wishlist-active');
        
        if (isCurrentlyActive) {
            iconContainer.classList.remove('wishlist-active');
            iconContainer.classList.add('wishlist-inactive');
            icon.classList.remove('fa-solid');
            icon.classList.add('fa-regular');
        } else {
            iconContainer.classList.remove('wishlist-inactive');
            iconContainer.classList.add('wishlist-active');
            icon.classList.remove('fa-regular');
            icon.classList.add('fa-solid');
        }

        let formData = new FormData();
        formData.append('ajax_wishlist_toggle', '1');
        formData.append('product_id', pid);

        fetch('product.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Something went wrong with wishlist.");
        });
    }
</script>

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

<script>
    function updateQty(change) {
        let input = document.getElementById('qtyInput');
        let val = parseInt(input.value);
        let max = parseInt(input.getAttribute('max'));
        let newVal = val + change;
        
        if(newVal >= 1 && newVal <= max) {
            input.value = newVal;
        }
    }

    function setRedirect(isCheckout) {
        document.getElementById('redirectCheckout').value = isCheckout ? "1" : "0";
    }

    document.getElementById('addToCartForm').addEventListener('submit', function(e) {
        let isCheckout = document.getElementById('redirectCheckout').value === "1";
        
        if (isCheckout) {
            return; 
        }

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
            if (count === 0) badge.style.display = 'none';
            else badge.style.display = 'inline-block';
        } else {
            if (count > 0) {
                let cartIcon = document.querySelector('.fa-bag-shopping');
                if(cartIcon && cartIcon.parentElement) {
                    let span = document.createElement('span');
                    span.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill badge-cart';
                    span.style.backgroundColor = '#f97316';
                    span.textContent = count;
                    cartIcon.parentElement.appendChild(span);
                }
            }
        }
    }

    function toggleWishlist(pid) {
       let iconContainer = document.getElementById('wishlist-icon-' + pid);
       let icon = iconContainer.querySelector('i');
       let isCurrentlyActive = iconContainer.classList.contains('wishlist-active');
       
       if (isCurrentlyActive) {
           iconContainer.classList.remove('wishlist-active');
           iconContainer.classList.add('wishlist-inactive');
           icon.classList.remove('fa-solid');
           icon.classList.add('fa-regular');
       } else {
           iconContainer.classList.remove('wishlist-inactive');
           iconContainer.classList.add('wishlist-active');
           icon.classList.remove('fa-regular');
           icon.classList.add('fa-solid');
       }

       let formData = new FormData();
       formData.append('ajax_wishlist_toggle', '1');
       formData.append('product_id', pid);

       fetch('product.php', { method: 'POST', body: formData });
    }
</script>

<?php include 'includes/footer.php'; ?>