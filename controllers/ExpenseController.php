<?php
require_once 'models/Expense.php';
require_once 'models/ExpenseCategory.php';

class ExpenseController {
    private $expenseModel;
    private $categoryModel;
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $this->expenseModel = new Expense();
        $this->categoryModel = new ExpenseCategory();
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get filter parameters
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        $categoryId = $_GET['category_id'] ?? null;
        
        // Get expenses based on filters
        $expenses = $this->expenseModel->getExpenses($userId, $startDate, $endDate, $categoryId);
        
        // Get all categories for the filter dropdown
        $categories = $this->categoryModel->getCategoriesByUserId($userId);
        
        // Load expenses view
        require_once 'views/expense/index.php';
    }
    
    public function create() {
        $userId = $_SESSION['user_id'];
        
        // Get all categories for the dropdown
        $categories = $this->categoryModel->getCategoriesByUserId($userId);
        
        // Load create expense view
        require_once 'views/expense/create.php';
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=expenses');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $categoryId = $_POST['category_id'] ?? null;
        $amount = $_POST['amount'] ?? 0;
        $description = $_POST['description'] ?? '';
        $expenseDate = $_POST['expense_date'] ?? date('Y-m-d');
        
        // Validate input
        if (empty($categoryId) || empty($amount) || empty($expenseDate)) {
            $_SESSION['error'] = "All fields are required";
            header('Location: index.php?page=expense/create');
            exit;
        }
        
        // Create expense
        $success = $this->expenseModel->createExpense($userId, $categoryId, $amount, $description, $expenseDate);
        
        if ($success) {
            $_SESSION['success'] = "Expense added successfully";
            header('Location: index.php?page=expenses');
        } else {
            $_SESSION['error'] = "Failed to add expense";
            header('Location: index.php?page=expense/create');
        }
        exit;
    }
    
    public function edit() {
        $userId = $_SESSION['user_id'];
        $expenseId = $_GET['id'] ?? null;
        
        if (!$expenseId) {
            header('Location: index.php?page=expenses');
            exit;
        }
        
        // Get expense details
        $expense = $this->expenseModel->getExpenseById($userId, $expenseId);
        
        if (!$expense) {
            $_SESSION['error'] = "Expense not found";
            header('Location: index.php?page=expenses');
            exit;
        }
        
        // Get all categories for the dropdown
        $categories = $this->categoryModel->getCategoriesByUserId($userId);
        
        // Load edit expense view
        require_once 'views/expense/edit.php';
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=expenses');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $expenseId = $_POST['expense_id'] ?? null;
        $categoryId = $_POST['category_id'] ?? null;
        $amount = $_POST['amount'] ?? 0;
        $description = $_POST['description'] ?? '';
        $expenseDate = $_POST['expense_date'] ?? date('Y-m-d');
        
        // Validate input
        if (!$expenseId || empty($categoryId) || empty($amount) || empty($expenseDate)) {
            $_SESSION['error'] = "All fields are required";
            header('Location: index.php?page=expense/edit&id=' . $expenseId);
            exit;
        }
        
        // Update expense
        $success = $this->expenseModel->updateExpense($userId, $expenseId, $categoryId, $amount, $description, $expenseDate);
        
        if ($success) {
            $_SESSION['success'] = "Expense updated successfully";
            header('Location: index.php?page=expenses');
        } else {
            $_SESSION['error'] = "Failed to update expense";
            header('Location: index.php?page=expense/edit&id=' . $expenseId);
        }
        exit;
    }
    
    public function delete() {
        $userId = $_SESSION['user_id'];
        $expenseId = $_GET['id'] ?? null;
        
        if (!$expenseId) {
            header('Location: index.php?page=expenses');
            exit;
        }
        
        // Delete expense
        $success = $this->expenseModel->deleteExpense($userId, $expenseId);
        
        if ($success) {
            $_SESSION['success'] = "Expense deleted successfully";
        } else {
            $_SESSION['error'] = "Failed to delete expense";
        }
        
        header('Location: index.php?page=expenses');
        exit;
    }
}
?>