<?php
ob_start();
session_start();
include '../db.php';

if (isset($_POST['export_csv'])) {
    if (ob_get_length()) ob_end_clean();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="omnimart_sales_report_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, array('Order ID', 'Customer Name', 'Total Amount', 'Status', 'Date'));
    
    $sql = "SELECT o.id, u.name, o.total_amount, o.status, o.created_at 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC";
    $rows = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($rows as $row) {
        fputcsv($output, array(
            '#' . $row['id'], 
            $row['name'], 
            'Rs. ' . $row['total_amount'], 
            $row['status'], 
            $row['created_at']
        ));
    }
    
    fclose($output);
    exit; 
}

include 'includes/header.php';

$sql = "SELECT o.id, u.name, o.total_amount, o.status, o.created_at 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$orders = $conn->query($sql)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h3 class="fw-bold mb-0">Sales Reports</h3>
    <form method="POST">
        <button type="submit" name="export_csv" class="btn btn-success" style="background: #10b981; border: none;">
            <i class="fa-solid fa-file-csv me-2"></i> Download CSV
        </button>
    </form>
</div>

<div class="card p-0 shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-striped mb-0 align-middle" style="min-width: 600px;">
            <thead class="bg-dark text-white">
                <tr>
                    <th class="ps-4 py-3">Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php $total_rev = 0; ?>
                <?php foreach($orders as $o): $total_rev += $o['total_amount']; ?>
                <tr>
                    <td class="ps-4 fw-bold">#<?php echo $o['id']; ?></td>
                    <td><?php echo htmlspecialchars($o['name']); ?></td>
                    <td class="fw-bold text-success">₹<?php echo number_format($o['total_amount'], 2); ?></td>
                    <td>
                        <?php 
                            $badge = match($o['status']) {
                                'Delivered' => 'bg-success',
                                'Cancelled' => 'bg-danger',
                                'Shipped' => 'bg-primary',
                                default => 'bg-warning text-dark'
                            };
                        ?>
                        <span class="badge <?php echo $badge; ?>"><?php echo $o['status']; ?></span>
                    </td>
                    <td class="text-muted small"><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="fw-bold bg-light">
                    <td colspan="2" class="text-end pe-3 py-3">TOTAL REVENUE:</td>
                    <td colspan="3" class="text-primary fs-5 py-3">₹<?php echo number_format($total_rev, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php 
ob_end_flush(); 
?>
</body>
</html>