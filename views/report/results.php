<?php require_once 'views/layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="fas fa-chart-bar me-2"></i>Report Results</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php?page=reports" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Reports
        </a>
        <a href="index.php?page=reports/export<?php echo isset($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : ''; ?>" class="btn btn-success">
            <i class="fas fa-file-csv me-1"></i>Export to CSV
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow h-100">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">Total Income</h5>
                <h2 class="display-5 text-success">
                    <?php echo $_SESSION['currency']; ?> <?php echo number_format($summary['total_income'], 2); ?>
                </h2>
                <p class="text-muted">
                    <?php echo date('M d, Y', strtotime($startDate)); ?> - 
                    <?php echo date('M d, Y', strtotime($endDate)); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow h-100">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">Total Expense</h5>
                <h2 class="display-5 text-danger">
                    <?php echo $_SESSION['currency']; ?> <?php echo number_format($summary['total_expense'], 2); ?>
                </h2>
                <p class="text-muted">
                    <?php echo date('M d, Y', strtotime($startDate)); ?> - 
                    <?php echo date('M d, Y', strtotime($endDate)); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow h-100">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">Balance</h5>
                <h2 class="display-5 <?php echo $summary['balance'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                    <?php echo $_SESSION['currency']; ?> <?php echo number_format($summary['balance'], 2); ?>
                </h2>
                <p class="text-muted">
                    <?php echo date('M d, Y', strtotime($startDate)); ?> - 
                    <?php echo date('M d, Y', strtotime($endDate)); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php if ($reportType === 'all' || $reportType === 'expense'): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Expense by Category</h5>
            </div>
            <div class="card-body">
                <?php if (empty($expenseCategorySummary)): ?>
                    <p class="text-muted text-center">No expense data available for the selected period.</p>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="expenseChart"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($expenseCategorySummary as $category): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                                <td class="text-end"><?php echo $_SESSION['currency']; ?> <?php echo number_format($category['total'], 2); ?></td>
                                                <td class="text-end">
                                                    <?php 
                                                    $percentage = ($summary['total_expense'] > 0) ? ($category['total'] / $summary['total_expense'] * 100) : 0;
                                                    echo number_format($percentage, 1) . '%';
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($reportType === 'all' || $reportType === 'income'): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Income by Category</h5>
            </div>
            <div class="card-body">
                <?php if (empty($incomeCategorySummary)): ?>
                    <p class="text-muted text-center">No income data available for the selected period.</p>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="incomeChart"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($incomeCategorySummary as $category): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                                <td class="text-end"><?php echo $_SESSION['currency']; ?> <?php echo number_format($category['total'], 2); ?></td>
                                                <td class="text-end">
                                                    <?php 
                                                    $percentage = ($summary['total_income'] > 0) ? ($category['total'] / $summary['total_income'] * 100) : 0;
                                                    echo number_format($percentage, 1) . '%';
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($reportType === 'all' || $reportType === 'expense'): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Expense Details</h5>
            </div>
            <div class="card-body">
                <?php if (empty($expenseData)): ?>
                    <p class="text-muted text-center">No expense records found for the selected period.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expenseData as $expense): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($expense['expense_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($expense['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                        <td class="text-end"><?php echo $_SESSION['currency']; ?> <?php echo number_format($expense['amount'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-end"><?php echo $_SESSION['currency']; ?> <?php echo number_format($summary['total_expense'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($reportType === 'all' || $reportType === 'income'): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Income Details</h5>
            </div>
            <div class="card-body">
                <?php if (empty($incomeData)): ?>
                    <p class="text-muted text-center">No income records found for the selected period.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($incomeData as $income): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($income['income_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($income['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars($income['description']); ?></td>
                                        <td class="text-end"><?php echo $_SESSION['currency']; ?> <?php echo number_format($income['amount'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-end"><?php echo $_SESSION['currency']; ?> <?php echo number_format($summary['total_income'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (($reportType === 'all' || $reportType === 'expense') && !empty($expenseCategorySummary)): ?>
    // Expense Chart
    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    const expenseData = <?php echo json_encode($expenseCategorySummary); ?>;
    
    new Chart(expenseCtx, {
        type: 'pie',
        data: {
            labels: expenseData.map(item => item.name),
            datasets: [{
                data: expenseData.map(item => item.total),
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                    '#FF9F40', '#8AC249', '#EA526F', '#23B5D3', '#279AF1'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
    <?php endif; ?>
    
    <?php if (($reportType === 'all' || $reportType === 'income') && !empty($incomeCategorySummary)): ?>
    // Income Chart
    const incomeCtx = document.getElementById('incomeChart').getContext('2d');
    const incomeData = <?php echo json_encode($incomeCategorySummary); ?>;
    
    new Chart(incomeCtx, {
        type: 'pie',
        data: {
            labels: incomeData.map(item => item.name),
            datasets: [{
                data: incomeData.map(item => item.total),
                backgroundColor: [
                    '#4BC0C0', '#FFCE56', '#36A2EB', '#FF6384', '#9966FF',
                    '#8AC249', '#FF9F40', '#EA526F', '#23B5D3', '#279AF1'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
    <?php endif; ?>
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>