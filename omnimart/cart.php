<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax_add'])) {
    header('Content-Type: application/json');
    
    $pid = $_POST['product_id'];
    $qty = (int)$_POST['quantity'];
    $action = $_POST['action'];

    if ($action == 'add') {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$pid]);
        $product = $stmt->fetch();

        if ($product) {
            $current_qty = isset($_SESSION['cart'][$pid]) ? $_SESSION['cart'][$pid]['quantity'] : 0;
            $total_needed = $current_qty + $qty;

            if ($product['stock'] >= $total_needed) {
                if (isset($_SESSION['cart'][$pid])) {
                    $_SESSION['cart'][$pid]['quantity'] += $qty;
                } else {
                    $_SESSION['cart'][$pid] = [
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'image' => $product['image'],
                        'quantity' => $qty
                    ];
                }
                
                $total_items = 0;
                foreach ($_SESSION['cart'] as $item) $total_items += $item['quantity'];

                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Item added to cart!', 
                    'cart_count' => $total_items
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Out of stock! Only ' . $product['stock'] . ' left.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
        }
    }
    exit; 
}

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['ajax_add'])) {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $pid = $_POST['product_id'];

        if ($action == 'add') {
            $qty = (int)$_POST['quantity'];
            $redirect = isset($_POST['redirect_checkout']) && $_POST['redirect_checkout'] == '1';

            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$pid]);
            $product = $stmt->fetch();

            if ($product) {
                $current_qty = isset($_SESSION['cart'][$pid]) ? $_SESSION['cart'][$pid]['quantity'] : 0;
                if ($product['stock'] >= ($current_qty + $qty)) {
                    if (isset($_SESSION['cart'][$pid])) {
                        $_SESSION['cart'][$pid]['quantity'] += $qty;
                    } else {
                        $_SESSION['cart'][$pid] = [
                            'name' => $product['name'],
                            'price' => $product['price'],
                            'image' => $product['image'],
                            'quantity' => $qty
                        ];
                    }
                    
                    if ($redirect) {
                        header("Location: checkout.php");
                        exit;
                    }
                } else {
                    $msg = "Insufficient stock.";
                }
            }
        } elseif ($action == 'remove') {
            unset($_SESSION['cart'][$pid]);
            if (count($_SESSION['cart']) == 0) unset($_SESSION['coupon']); 
        } elseif ($action == 'update') {
            $qty = (int)$_POST['quantity'];
            if ($qty > 0) $_SESSION['cart'][$pid]['quantity'] = $qty;
        }
    } 
    
    if (isset($_POST['apply_coupon'])) {
        $code = strtoupper(trim($_POST['coupon_code']));
        $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ?");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch();
        if ($coupon) {
            $_SESSION['coupon'] = [
                'code' => $coupon['code'],
                'type' => $coupon['discount_type'],
                'value' => $coupon['value']
            ];
            $msg = "Coupon applied!";
        } else {
            $msg = "Invalid Coupon.";
            unset($_SESSION['coupon']);
        }
    }
    if (isset($_POST['remove_coupon'])) {
        unset($_SESSION['coupon']);
        $msg = "Coupon removed.";
    }
}

include 'includes/header.php';

$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$discount = 0;
if (isset($_SESSION['coupon'])) {
    if ($_SESSION['coupon']['type'] == 'percent') {
        $discount = $subtotal * ($_SESSION['coupon']['value'] / 100);
    } else {
        $discount = $_SESSION['coupon']['value'];
    }
}
$final_total = max(0, $subtotal - $discount);
?>

<div class="container py-5" style="min-height: 60vh;">
    <h2 class="fw-bold mb-4 display-6">Shopping Bag</h2>
    <?php if($msg): ?><div class="alert alert-info"><?php echo $msg; ?></div><?php endif; ?>

    <div class="row g-5">
        <div class="col-lg-8">
            <?php if (count($_SESSION['cart']) > 0): ?>
                <?php foreach ($_SESSION['cart'] as $id => $item): 
                    $line_total = $item['price'] * $item['quantity'];
                ?>
                <div class="card mb-3 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-2">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" class="img-fluid h-100" style="object-fit: cover; min-height: 120px;">
                        </div>
                        <div class="col-md-10">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($item['name']); ?></h5>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                        <button class="btn btn-sm text-danger"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                                <div class="d-flex justify-content-between align-items-end">
                                    <div class="text-muted small">Price: ₹<?php echo number_format($item['price'], 2); ?></div>
                                    <form method="POST" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control form-control-sm text-center" style="width: 60px;">
                                        <button class="btn btn-sm btn-light border"><i class="fa-solid fa-rotate"></i></button>
                                    </form>
                                    <div class="fw-bold fs-5">₹<?php echo number_format($line_total, 2); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5 bg-light rounded-4">
                    <i class="fa-solid fa-cart-arrow-down fa-3x text-muted mb-3"></i>
                    <h4>Your cart is empty</h4>
                    <a href="index.php" class="btn btn-dark mt-3 rounded-pill px-4">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 p-4">
                <h5 class="fw-bold mb-4">Order Summary</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-bold">₹<?php echo number_format($subtotal, 2); ?></span>
                </div>
                
                <?php if(isset($_SESSION['coupon'])): ?>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Discount (<?php echo $_SESSION['coupon']['code']; ?>)</span>
                        <span>-₹<?php echo number_format($discount, 2); ?></span>
                    </div>
                    <form method="POST" class="mb-3 text-end">
                        <button type="submit" name="remove_coupon" class="btn btn-link btn-sm text-danger p-0">Remove Coupon</button>
                    </form>
                <?php else: ?>
                    <form method="POST" class="input-group mb-3">
                        <input type="text" name="coupon_code" class="form-control" placeholder="Promo Code">
                        <button type="submit" name="apply_coupon" class="btn btn-outline-secondary">Apply</button>
                    </form>
                <?php endif; ?>

                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <span class="fw-bold fs-5">Total</span>
                    <span class="fw-bold fs-5 text-primary">₹<?php echo number_format($final_total, 2); ?></span>
                </div>
                
                <?php if ($subtotal > 0): ?>
                    <a href="checkout.php" class="btn btn-dark w-100 py-3 rounded-3 fw-bold" style="background: #f97316; border:none;">Proceed to Checkout</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>