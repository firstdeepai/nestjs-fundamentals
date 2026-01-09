<?php
session_start();
include 'db.php';
$page_title = "My Wishlist";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['remove'])) {
    $pid = $_GET['remove'];
    $conn->prepare("DELETE FROM wishlist WHERE user_id=? AND product_id=?")->execute([$user_id, $pid]);
    echo "<script>window.location.href='wishlist.php';</script>";
}

if (isset($_GET['add'])) {
    $pid = $_GET['add'];
    $check = $conn->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
    $check->execute([$user_id, $pid]);
    if ($check->rowCount() == 0) {
        $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)")->execute([$user_id, $pid]);
    }
    echo "<script>window.location.href='wishlist.php';</script>";
}

$stmt = $conn->prepare("SELECT p.* FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();
?>

<div class="container py-5" style="min-height: 70vh;">
    <h2 class="fw-bold mb-4 display-6">My Wishlist <i class="fa-solid fa-heart text-danger ms-2"></i></h2>
    
    <?php if(count($items) > 0): ?>
        <div class="row g-4">
            <?php foreach($items as $p): ?>
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-card-hover">
                    
                    <div class="position-relative" style="height: 250px; overflow: hidden;">
                        <?php $img = !empty($p['image']) ? $p['image'] : 'assets/no-img.png'; ?>
                        <img src="<?php echo $img; ?>" class="w-100 h-100 product-img" style="object-fit: cover; transition: transform 0.5s ease;">
                        
                        <a href="?remove=<?php echo $p['id']; ?>" class="position-absolute top-0 end-0 m-2 btn btn-light text-danger rounded-circle shadow-sm" style="width: 35px; height: 35px; display:flex; align-items:center; justify-content:center;" title="Remove">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>

                        <?php if($p['stock'] == 0): ?>
                            <span class="position-absolute top-0 start-0 bg-secondary text-white px-2 py-1 m-2 rounded-pill small fw-bold shadow-sm" style="font-size: 0.7rem;">Out of Stock</span>
                        <?php endif; ?>
                    </div>

                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark text-truncate mb-1" style="font-size: 1.1rem;"><?php echo htmlspecialchars($p['name']); ?></h6>
                        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap" style="gap: 5px">
                            <h5 class="fw-bold text-primary mb-0" style="font-family: 'Outfit', sans-serif;">
                                <sup style="font-size: 0.6em; top: -0.2em;">₹</sup><?php echo number_format($p['price'], 2); ?>
                            </h5>
                            
                            <form class="ajax-add-form" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm fw-bold">
                                    Add <i class="fa-solid fa-bag-shopping ms-1"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-light rounded-4">
            <i class="fa-regular fa-heart fa-3x text-muted mb-3"></i>
            <h4>Your wishlist is empty</h4>
            <p class="text-muted">Save items you love here.</p>
            <a href="index.php" class="btn btn-outline-dark mt-3 rounded-pill px-4">Explore Products</a>
        </div>
    <?php endif; ?>
</div>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
  <div id="liveToast" class="toast align-items-center text-white bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body fw-bold">
        <i class="fa-solid fa-circle-check text-success me-2"></i> 
        <span id="toastMessage">Item added to cart!</span>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<style>
    .product-card-hover { transition: box-shadow 0.3s ease; }
    .product-card-hover:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
    .product-card-hover:hover .product-img { transform: scale(1.05); }
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