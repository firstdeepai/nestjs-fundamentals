<?php
include '../db.php';
include 'includes/header.php';

if (isset($_GET['del'])) {
    $id = $_GET['del'];
    if ($id != $_SESSION['user_id']) {
        $conn->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
        echo "<script>window.location.href='users.php';</script>";
    } else {
        echo "<script>alert('You cannot delete your own account!');</script>";
    }
}

$users = $conn->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="fw-bold mb-0">User Management</h3>
    <span class="badge bg-primary fs-6"><?php echo count($users); ?> Total Users</span>
</div>

<div class="card p-0 shadow-sm border-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>User Details</th>
                    <th>Role</th>
                    <th>Joined At</th>
                    <th class="text-end pe-4">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td class="ps-4 text-muted">#<?php echo $u['id']; ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px; font-weight:bold; color: #f97316;">
                                <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($u['name']); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($u['email']); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if($u['role'] == 'admin'): ?>
                            <span class="badge bg-danger">Admin</span>
                        <?php elseif($u['role'] == 'vendor'): ?>
                            <span class="badge bg-success">Vendor</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Customer</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small"><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                    <td class="text-end pe-4">
                        <?php if($u['role'] != 'admin'): ?>
                            <a href="?del=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure? This will remove their order history too.');">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        <?php else: ?>
                            <button class="btn btn-sm btn-light text-muted" disabled>Protected</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>