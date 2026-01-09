<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

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
$final_total = $subtotal - $discount;
if ($final_total < 0) $final_total = 0;

include 'includes/header.php';
?>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<div class="bg-light py-3">
    <div class="container">
        <h3 class="fw-bold mb-0">Secure Checkout</h3>
    </div>
</div>

<div class="container py-5">
    <form action="place_order.php" method="POST" id="checkoutForm">
        
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        
        <div class="row g-5">
            <div class="col-md-7">
                <h5 class="fw-bold mb-3">Shipping Details</h5>
                <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Address</label>
                            <input type="text" class="form-control" name="address" placeholder="123 Main St" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted">Country</label>
                            <input type="text" class="form-control" name="country" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">State</label>
                            <input type="text" class="form-control" name="state" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Zip</label>
                            <input type="text" class="form-control" name="zip" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Phone Number</label>
                            <input type="text" class="form-control" name="phone" id="phone" required placeholder="9876543210">
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-3">Payment Method</h5>
                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="cod" value="COD" checked>
                        <label class="form-check-label fw-bold" for="cod">
                            Cash on Delivery (COD)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="online" value="ONLINE">
                        <label class="form-check-label fw-bold text-primary" for="online">
                            Pay Online (Razorpay) <span class="badge bg-warning text-dark ms-2">SECURE</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-3">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                            <li class="list-group-item d-flex justify-content-between lh-sm px-0">
                                <div>
                                    <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                </div>
                                <span class="text-muted">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </li>
                            <?php endforeach; ?>
                            
                            <li class="list-group-item d-flex justify-content-between px-0 pt-3">
                                <span>Subtotal</span>
                                <strong>$<?php echo number_format($subtotal, 2); ?></strong>
                            </li>

                            <?php if($discount > 0): ?>
                            <li class="list-group-item d-flex justify-content-between px-0 text-success">
                                <span>Discount</span>
                                <strong>-$<?php echo number_format($discount, 2); ?></strong>
                            </li>
                            <?php endif; ?>

                            <li class="list-group-item d-flex justify-content-between px-0 bg-light p-3 rounded mt-2">
                                <span class="fw-bold">Total Pay</span>
                                <strong class="text-primary">$<?php echo number_format($final_total, 2); ?></strong>
                            </li>
                        </ul>
                        <button type="submit" id="placeOrderBtn" class="btn btn-dark w-100 py-3 rounded-3 fw-bold fs-5" style="background: #f97316; border:none;">
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const RAZORPAY_KEY_ID = "rzp_test_ih3r0c7gDMCAOg"; 

const form = document.getElementById('checkoutForm');
const totalAmount = <?php echo $final_total * 100; ?>; 

form.addEventListener('submit', function(e) {
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
    
    if (paymentMethod === 'ONLINE') {
        e.preventDefault();

        const options = {
            "key": RAZORPAY_KEY_ID,
            "amount": totalAmount, 
            "currency": "INR",
            "name": "OmniMart Store",
            "description": "Order Payment",
            "handler": function (response){
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                form.submit(); 
            },
            "prefill": {
                "name": "<?php echo $_SESSION['user_name']; ?>",
                "contact": document.getElementById('phone').value
            },
            "theme": { "color": "#f97316" }
        };

        if(RAZORPAY_KEY_ID === "YOUR_KEY_ID_HERE") {
            alert("Error: Please put your Razorpay Key ID in the code!");
        } else {
            const rzp1 = new Razorpay(options);
            rzp1.open();
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>