<?php
session_start();
include 'db.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role']; 

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        $error = "This email is already registered.";
    } else {
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$name, $email, $hashed_pass, $role])) {
            $new_user_id = $conn->lastInsertId();
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = $role;

            if ($role == 'vendor') {
                $v_stmt = $conn->prepare("INSERT INTO vendors (user_id, name, status) VALUES (?, ?, 'Active')");
                $v_stmt->execute([$new_user_id, $name]);
                header("Location: vendor/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Registration failed. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join OmniMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0f172a; --accent: #f97316; }
        body { font-family: 'Outfit', sans-serif; background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 10px; }
        .card-signup { border: none; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.08); overflow: hidden; max-width: 900px; width: 100%; margin: auto; }
        .left-panel { background: var(--primary); color: white; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
        .btn-register { background: var(--accent); color: white; font-weight: 600; padding: 12px; border-radius: 8px; border: none; transition: 0.3s; }
        .btn-register:hover { background: #ea580c; }
        
        @media (max-width: 767px) {
            .left-panel { padding: 30px 20px; text-align: center; }
            .card-signup { margin-top: 60px; border-radius: 15px; }
        }
    </style>
</head>
<body>
    <a href="index.php" class="position-absolute top-0 start-0 m-3 m-md-4 text-decoration-none text-dark fw-bold bg-white p-2 rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; z-index: 1000;">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="card card-signup">
        <div class="row g-0">
            <div class="col-md-5 left-panel text-center text-md-start">
                <h2 class="fw-bold mb-3 mb-md-4">Join the Revolution</h2>
                <p class="opacity-75 mb-4">Create an account to start shopping or selling on the world's most premium marketplace.</p>
                <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-3">
                    <i class="fa-solid fa-check-circle text-success me-3 fs-4"></i> <span>Exclusive Discounts</span>
                </div>
                <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                    <i class="fa-solid fa-check-circle text-success me-3 fs-4"></i> <span>Fast Vendor Approval</span>
                </div>
            </div>

            <div class="col-md-7 p-4 p-md-5 bg-white">
                <h3 class="fw-bold text-dark mb-4 text-center text-md-start">Create Account</h3>
                <?php if($error): ?>
                    <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">I want to:</label>
                        <select name="role" class="form-select">
                            <option value="customer">Buy Products (Customer)</option>
                            <option value="vendor">Sell Products (Vendor)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-register w-100">Get Started <i class="fa-solid fa-arrow-right ms-2"></i></button>
                </form>
                
                <div class="text-center mt-4 small">
                    Already a member? <a href="login.php" class="text-dark fw-bold">Sign In</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>