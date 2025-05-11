<?php require_once 'views/layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="fas fa-chart-bar me-2"></i>Reports</h1>
    </div>
</div>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Generate Report</h5>
    </div>
    <div class="card-body">
        <form action="index.php" method="get">
            <input type="hidden" name="page" value="reports/generate">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="report_type" class="form-label">Report Type</label>
                        <select class="form-select" id="report_type" name="report_type">
                            <option value="all">Income & Expense</option>
                            <option value="income">Income Only</option>
                            <option value="expense">Expense Only</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="date_range" class="form-label">Date Range</label>
                        <select class="form-select" id="date_range" onchange="updateDateRange()">
                            <option value="current_month">Current Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="last_3_months">Last 3 Months</option>
                            <option value="last_6_months">Last 6 Months</option>
                            <option value="current_year">Current Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row" id="date_inputs">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo date('Y-m-01'); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo date('Y-m-t'); ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="income_category_id" class="form-label">Income Category</label>
                        <select class="form-select" id="income_category_id" name="income_category_id">
                            <option value="">All Categories</option>
                            <?php foreach ($incomeCategories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="expense_category_id" class="form-label">Expense Category</label>
                        <select class="form-select" id="expense_category_id" name="expense_category_id">
                            <option value="">All Categories</option>
                            <?php foreach ($expenseCategories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>Generate Report
                </button>
                <button type="button" class="btn btn-success" onclick="exportReport()">
                    <i class="fas fa-file-csv me-1"></i>Export to CSV
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateDateRange() {
    const dateRange = document.getElementById('date_range').value;
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    const today = new Date();
    let startDate = new Date();
    let endDate = new Date();
    
    switch (dateRange) {
        case 'current_month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'last_month':
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        case 'last_3_months':
            startDate = new Date(today.getFullYear(), today.getMonth() - 3, 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'last_6_months':
            startDate = new Date(today.getFullYear(), today.getMonth() - 6, 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'current_year':
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = new Date(today.getFullYear(), 11, 31);
            break;
        case 'custom':
            // Don't change the dates, let user select
            return;
    }
    
    startDateInput.value = formatDate(startDate);
    endDateInput.value = formatDate(endDate);
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function exportReport() {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    // Change the page to export
    formData.set('page', 'reports/export');
    
    // Create URL with parameters
    const params = new URLSearchParams();
    for (const [key, value] of formData.entries()) {
        params.append(key, value);
    }
    
    // Redirect to export URL
    window.location.href = 'index.php?' + params.toString();
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>