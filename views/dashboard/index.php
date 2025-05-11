<?php require_once 'views/layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-4"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow h-100">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">Total Income</h5>
                <h2 class="display-5 text-success">
                    <?php echo $_SESSION['currency']; ?> <?php echo number_format($totalIncome, 2); ?>
                </h2>
                <p class="text-muted">Current Month</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow h-100">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">Total Expense</h5>
                <h2 class="display-5 text-danger">
                    <?php echo $_SESSION['currency']; ?> <?php echo number_format($totalExpense, 2); ?>
                </h2>
                <p class="text-muted">Current Month</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow h-100">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">Balance</h5>
                <h2 class="display-5 <?php echo $balance >= 0 ? 'text-success' : 'text-danger'; ?>">
                    <?php echo $_SESSION['currency']; ?> <?php echo number_format($balance, 2); ?>
                </h2>
                <p class="text-muted">Current Month</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Expense by Category</h5>
            </div>
            <div class="card-body">
                <canvas id="expenseChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Income by Category</h5>
            </div>
            <div class="card-body">
                <canvas id="incomeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-arrow-circle-down me-2"></i>Recent Incomes</h5>
                <a href="index.php?page=incomes" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentIncomes)): ?>
                    <p class="text-muted text-center">No recent incomes found.</p>
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
                                <?php foreach ($recentIncomes as $income): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($income['income_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($income['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars($income['description']); ?></td>
                                        <td class="text-end"><?php echo $_SESSION['currency']; ?> <?php echo number_format($income['amount'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-arrow-circle-up me-2"></i>Recent Expenses</h5>
                <a href="index.php?page=expenses" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentExpenses)): ?>
                    <p class="text-muted text-center">No recent expenses found.</p>
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
                                <?php foreach ($recentExpenses as $expense): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($expense['expense_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($expense['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                        <td class="text-end"><?php echo $_SESSION['currency']; ?> <?php echo number_format($expense['amount'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Expense Chart
    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    const expenseData = <?php echo json_encode($expenseByCategory); ?>;
    
    if (expenseData.length > 0) {
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
    } else {
        document.getElementById('expenseChart').parentNode.innerHTML = '<p class="text-muted text-center mt-5">No expense data available for the current month.</p>';
    }
    
    // Income Chart
    const incomeCtx = document.getElementById('incomeChart').getContext('2d');
    const incomeData = <?php echo json_encode($incomeByCategory); ?>;
    
    if (incomeData.length > 0) {
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
    } else {
        document.getElementById('incomeChart').parentNode.innerHTML = '<p class="text-muted text-center mt-5">No income data available for the current month.</p>';
    }
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>