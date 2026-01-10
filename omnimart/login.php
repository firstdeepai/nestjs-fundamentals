<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') header("Location: admin/dashboard.php");
    elseif ($_SESSION['user_role'] == 'vendor') header("Location: vendor/dashboard.php");
    else header("Location: index.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        
        if ($user['role'] == 'vendor') {
            $v_stmt = $conn->prepare("SELECT id, status FROM vendors WHERE user_id = ?");
            $v_stmt->execute([$user['id']]);
            $vendor_data = $v_stmt->fetch();

            if ($vendor_data) {
                if ($vendor_data['status'] == 'Suspended') {
                    $error = "Your vendor account is suspended. Please contact Admin.";
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];
                    header("Location: vendor/dashboard.php");
                    exit;
                }
            } else {
                $conn->prepare("INSERT INTO vendors (user_id, name, status) VALUES (?, ?, 'Active')")->execute([$user['id'], $user['name']]);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                header("Location: vendor/dashboard.php");
                exit;
            }
        } 
        
        elseif ($user['role'] == 'admin') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: admin/dashboard.php");
            exit;
        } 
        
        else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: index.php");
            exit;
        }

    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OmniMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0f172a; --accent: #f97316; }
        body { font-family: 'Outfit', sans-serif; min-height: 100vh; background: #fff; }
        .bg-image {
            background: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=1920&auto=format&fit=crop') no-repeat center center/cover;
            position: relative;
            min-height: 100vh;
        }
        .bg-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); }
        .login-section { display: flex; align-items: center; justify-content: center; background: white; min-height: 100vh; padding: 20px 0; }
        .form-control { padding: 12px 15px; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; }
        .form-control:focus { box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1); border-color: var(--accent); }
        .btn-primary { background: var(--primary); border: none; padding: 14px; border-radius: 8px; font-weight: 600; transition: 0.3s; }
        .btn-primary:hover { background: #1e293b; transform: translateY(-2px); }
    </style>
</head>
<body>
    <a href="index.php" class="position-absolute top-0 start-0 m-3 m-md-4 text-decoration-none text-dark fw-bold bg-white p-2 rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; z-index: 1000;">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="row g-0 min-vh-100">
        <div class="col-lg-7 d-none d-lg-block bg-image">
            <div class="bg-overlay"></div>
            <div class="position-absolute top-50 start-50 translate-middle text-white text-center px-5 w-100">
                <h1 class="display-3 fw-bold mb-3">Omni<span style="color: var(--accent);">Mart</span>.</h1>
                <p class="fs-4 fw-light opacity-75">The world's fastest growing multi-vendor marketplace.</p>
            </div>
        </div>

        <div class="col-lg-5 login-section">
            <div class="w-100 px-3 px-md-5" style="max-width: 500px;">
                <div class="mb-4 mb-md-5">
                    <h2 class="fw-bold text-dark display-6">Welcome back</h2>
                    <p class="text-muted">Please enter your details to sign in.</p>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger d-flex align-items-center mb-4">
                        <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label text-uppercase small fw-bold text-muted">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control border-start-0" placeholder="name@example.com" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <label class="form-label text-uppercase small fw-bold text-muted">Password</label>
                            <a href="#" class="text-decoration-none small text-muted">Forgot password?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-4">Sign in to Dashboard</button>
                    
                    <div class="text-center text-muted">
                        Don't have an account? <a href="signup.php" class="fw-bold text-decoration-none" style="color: var(--accent);">Create free account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>