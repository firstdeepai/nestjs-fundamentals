<?php
include '../db.php';
include 'includes/header.php';

if (isset($_GET['status']) && isset($_GET['id'])) {
    $status = $_GET['status'];
    $vid = $_GET['id'];
    $conn->prepare("UPDATE vendors SET status=? WHERE id=?")->execute([$status, $vid]);
    echo "<script>window.location.href='vendors.php';</script>";
}

if (isset($_GET['del'])) {
    $vid = $_GET['del'];
    $conn->prepare("DELETE FROM vendors WHERE id=?")->execute([$vid]);
    echo "<script>window.location.href='vendors.php';</script>";
}

$vendors = $conn->query("SELECT v.*, u.email FROM vendors v JOIN users u ON v.user_id = u.id ORDER BY v.id DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Vendor Management</h3>
</div>

<div class="card p-0 shadow-sm border-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 700px;">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Store Name</th>
                    <th>Owner Email</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($vendors as $v): ?>
                <tr>
                    <td class="ps-4">#<?php echo $v['id']; ?></td>
                    <td class="fw-bold text-primary"><?php echo htmlspecialchars($v['name']); ?></td>
                    <td><?php echo htmlspecialchars($v['email']); ?></td>
                    <td>
                        <?php if($v['status'] == 'Active'): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Suspended</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-4">
                        <?php if($v['status'] == 'Active'): ?>
                            <a href="?status=Suspended&id=<?php echo $v['id']; ?>" class="btn btn-sm btn-outline-warning" title="Suspend"><i class="fa-solid fa-ban"></i></a>
                        <?php else: ?>
                            <a href="?status=Active&id=<?php echo $v['id']; ?>" class="btn btn-sm btn-outline-success" title="Activate"><i class="fa-solid fa-check"></i></a>
                        <?php endif; ?>
                        <a href="?del=<?php echo $v['id']; ?>" class="btn btn-sm btn-outline-danger ms-2" onclick="return confirm('Delete Vendor Profile?');"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>