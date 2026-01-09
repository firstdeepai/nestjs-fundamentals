<?php
session_start();
include 'db.php';
$page_title = "My Profile";
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $new_pass = $_POST['new_password'];

    $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
    $params = [$name, $email, $user_id];

    if (!empty($new_pass)) {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
        $params = [$name, $email, $hashed, $user_id];
    }

    $stmt = $conn->prepare($sql);
    if ($stmt->execute($params)) {
        $_SESSION['user_name'] = $name; 
        $msg = "Profile updated successfully!";
        $msg_type = "success";
    } else {
        $msg = "Update failed.";
        $msg_type = "danger";
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<div class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                    <h4 class="fw-bold"><?php echo htmlspecialchars($user['name']); ?></h4>
                    <p class="text-muted mb-4"><?php echo htmlspecialchars($user['email']); ?></p>
                    
                    <div class="d-grid gap-2">
                        <a href="orders.php" class="btn btn-outline-dark rounded-pill">
                            <i class="fa-solid fa-box me-2"></i> My Orders
                        </a>
                        <a href="cart.php" class="btn btn-outline-dark rounded-pill">
                            <i class="fa-solid fa-cart-shopping me-2"></i> My Cart
                        </a>
                        <a href="logout.php" class="btn btn-danger rounded-pill mt-2">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-5">
                    <h3 class="fw-bold mb-4">Edit Profile</h3>
                    
                    <?php if($msg): ?>
                        <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $msg; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <h5 class="fw-bold text-muted border-bottom pb-2">Change Password</h5>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">New Password (leave blank to keep current)</label>
                                <input type="password" name="new_password" class="form-control" placeholder="••••••••">
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-pill" style="background: #f97316; border:none;">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>