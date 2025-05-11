<?php
require_once 'models/Income.php';
require_once 'models/Expense.php';

class DashboardController {
    private $incomeModel;
    private $expenseModel;
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $this->incomeModel = new Income();
        $this->expenseModel = new Expense();
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get current month's data
        $currentMonth = date('Y-m');
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        
        // Get total income for current month
        $totalIncome = $this->incomeModel->getTotalIncomeByDateRange($userId, $startDate, $endDate);
        
        // Get total expense for current month
        $totalExpense = $this->expenseModel->getTotalExpenseByDateRange($userId, $startDate, $endDate);
        
        // Get balance
        $balance = $totalIncome - $totalExpense;
        
        // Get recent incomes (last 5)
        $recentIncomes = $this->incomeModel->getRecentIncomes($userId, 5);
        
        // Get recent expenses (last 5)
        $recentExpenses = $this->expenseModel->getRecentExpenses($userId, 5);
        
        // Get expense by category for current month
        $expenseByCategory = $this->expenseModel->getExpenseByCategory($userId, $startDate, $endDate);
        
        // Get income by category for current month
        $incomeByCategory = $this->incomeModel->getIncomeByCategory($userId, $startDate, $endDate);
        
        // Load dashboard view
        require_once 'views/dashboard/index.php';
    }
}
?>