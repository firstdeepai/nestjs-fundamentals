<?php
session_start();
include 'db.php';
include 'includes/header.php';

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    
    $stmt = $conn->prepare("INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)");
    if ($stmt->execute([$name, $email, $message])) {
        $msg = "Message sent successfully! We will contact you soon.";
    } else {
        $msg = "Error sending message.";
    }
}
?>

<div class="bg-light py-5">
    <div class="container text-center">
        <h1 class="fw-bold display-4">Contact Us</h1>
        <p class="lead text-muted">We'd love to hear from you.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg rounded-4 p-5">
                <?php if($msg): ?>
                    <div class="alert alert-success text-center mb-4"><?php echo $msg; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Your Name</label>
                            <input type="text" name="name" class="form-control bg-light border-0 py-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control bg-light border-0 py-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Message</label>
                            <textarea name="message" class="form-control bg-light border-0" rows="5" required></textarea>
                        </div>
                        <div class="col-12 text-center">
                            <button class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm" style="background: #f97316; border:none;">
                                Send Message <i class="fa-solid fa-paper-plane ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="row mt-5 text-center g-4">
        <div class="col-md-4">
            <i class="fa-solid fa-envelope fa-2x text-warning mb-3"></i>
            <h5 class="fw-bold">Email</h5>
            <p class="text-muted">support@omnimart.com</p>
        </div>
        <div class="col-md-4">
            <i class="fa-solid fa-phone fa-2x text-warning mb-3"></i>
            <h5 class="fw-bold">Phone</h5>
            <p class="text-muted">+1 234 567 890</p>
        </div>
        <div class="col-md-4">
            <i class="fa-solid fa-location-dot fa-2x text-warning mb-3"></i>
            <h5 class="fw-bold">Location</h5>
            <p class="text-muted">123 Market St, New York, USA</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>