<?php require_once 'views/layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="fas fa-arrow-circle-up me-2"></i>Expense Manager</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php?page=expense/create" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Add Expense
        </a>
        <a href="index.php?page=expense/categories" class="btn btn-secondary">
            <i class="fas fa-tags me-1"></i>Categories
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter</h5>
    </div>
    <div class="card-body">
        <form action="index.php" method="get">
            <input type="hidden" name="page" value="expenses">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $startDate; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $endDate; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $categoryId == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="mb-3 w-100">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Expense List</h5>
    </div>
    <div class="card-body">
        <?php if (empty($expenses)): ?>
            <p class="text-muted text-center">No expense records found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($expense['expense_date'])); ?></td>
                                <td><?php echo htmlspecialchars($expense['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                <td class="text-end"><?php echo $_SESSION['currency']; ?> <?php echo number_format($expense['amount'], 2); ?></td>
                                <td class="text-center">
                                    <a href="index.php?page=expense/edit&id=<?php echo $expense['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?page=expense/delete&id=<?php echo $expense['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this expense?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th class="text-end">
                                <?php 
                                $total = 0;
                                foreach ($expenses as $expense) {
                                    $total += $expense['amount'];
                                }
                                echo $_SESSION['currency'] . ' ' . number_format($total, 2);
                                ?>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>