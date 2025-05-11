<?php
require_once 'models/Income.php';
require_once 'models/IncomeCategory.php';

class IncomeController {
    private $incomeModel;
    private $categoryModel;
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $this->incomeModel = new Income();
        $this->categoryModel = new IncomeCategory();
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get filter parameters
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        $categoryId = $_GET['category_id'] ?? null;
        
        // Get incomes based on filters
        $incomes = $this->incomeModel->getIncomes($userId, $startDate, $endDate, $categoryId);
        
        // Get all categories for the filter dropdown
        $categories = $this->categoryModel->getCategoriesByUserId($userId);
        
        // Load incomes view
        require_once 'views/income/index.php';
    }
    
    public function create() {
        $userId = $_SESSION['user_id'];
        
        // Get all categories for the dropdown
        $categories = $this->categoryModel->getCategoriesByUserId($userId);
        
        // Load create income view
        require_once 'views/income/create.php';
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=incomes');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $categoryId = $_POST['category_id'] ?? null;
        $amount = $_POST['amount'] ?? 0;
        $description = $_POST['description'] ?? '';
        $incomeDate = $_POST['income_date'] ?? date('Y-m-d');
        
        // Validate input
        if (empty($categoryId) || empty($amount) || empty($incomeDate)) {
            $_SESSION['error'] = "All fields are required";
            header('Location: index.php?page=income/create');
            exit;
        }
        
        // Create income
        $success = $this->incomeModel->createIncome($userId, $categoryId, $amount, $description, $incomeDate);
        
        if ($success) {
            $_SESSION['success'] = "Income added successfully";
            header('Location: index.php?page=incomes');
        } else {
            $_SESSION['error'] = "Failed to add income";
            header('Location: index.php?page=income/create');
        }
        exit;
    }
    
    public function edit() {
        $userId = $_SESSION['user_id'];
        $incomeId = $_GET['id'] ?? null;
        
        if (!$incomeId) {
            header('Location: index.php?page=incomes');
            exit;
        }
        
        // Get income details
        $income = $this->incomeModel->getIncomeById($userId, $incomeId);
        
        if (!$income) {
            $_SESSION['error'] = "Income not found";
            header('Location: index.php?page=incomes');
            exit;
        }
        
        // Get all categories for the dropdown
        $categories = $this->categoryModel->getCategoriesByUserId($userId);
        
        // Load edit income view
        require_once 'views/income/edit.php';
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=incomes');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $incomeId = $_POST['income_id'] ?? null;
        $categoryId = $_POST['category_id'] ?? null;
        $amount = $_POST['amount'] ?? 0;
        $description = $_POST['description'] ?? '';
        $incomeDate = $_POST['income_date'] ?? date('Y-m-d');
        
        // Validate input
        if (!$incomeId || empty($categoryId) || empty($amount) || empty($incomeDate)) {
            $_SESSION['error'] = "All fields are required";
            header('Location: index.php?page=income/edit&id=' . $incomeId);
            exit;
        }
        
        // Update income
        $success = $this->incomeModel->updateIncome($userId, $incomeId, $categoryId, $amount, $description, $incomeDate);
        
        if ($success) {
            $_SESSION['success'] = "Income updated successfully";
            header('Location: index.php?page=incomes');
        } else {
            $_SESSION['error'] = "Failed to update income";
            header('Location: index.php?page=income/edit&id=' . $incomeId);
        }
        exit;
    }
    
    public function delete() {
        $userId = $_SESSION['user_id'];
        $incomeId = $_GET['id'] ?? null;
        
        if (!$incomeId) {
            header('Location: index.php?page=incomes');
            exit;
        }
        
        // Delete income
        $success = $this->incomeModel->deleteIncome($userId, $incomeId);
        
        if ($success) {
            $_SESSION['success'] = "Income deleted successfully";
        } else {
            $_SESSION['error'] = "Failed to delete income";
        }
        
        header('Location: index.php?page=incomes');
        exit;
    }
}
?>