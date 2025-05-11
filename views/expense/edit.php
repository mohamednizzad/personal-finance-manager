<?php require_once 'views/layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="fas fa-edit me-2"></i>Edit Expense</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="index.php?page=expenses" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Expense List
        </a>
    </div>
</div>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Expense Details</h5>
    </div>
    <div class="card-body">
        <form action="index.php?page=expense/update" method="post">
            <input type="hidden" name="expense_id" value="<?php echo $expense['id']; ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text"><?php echo $_SESSION['currency']; ?></span>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" value="<?php echo $expense['amount']; ?>" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="expense_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?php echo $expense['expense_date']; ?>" required>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <div class="input-group">
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $expense['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <a href="index.php?page=expense/category/create" class="btn btn-outline-secondary">
                        <i class="fas fa-plus"></i> New
                    </a>
                </div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($expense['description']); ?></textarea>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Update Expense
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>