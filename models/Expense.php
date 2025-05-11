<?php
class Expense {
    private $db;
    
    public function __construct() {
        $this->db = getDbConnection();
    }
    
    public function getExpenses($userId, $startDate = null, $endDate = null, $categoryId = null) {
        $sql = "SELECT e.*, ec.name as category_name 
                FROM expenses e 
                JOIN expense_categories ec ON e.category_id = ec.id 
                WHERE e.user_id = ?";
        $params = [$userId];
        $types = "i";
        
        if ($startDate) {
            $sql .= " AND e.expense_date >= ?";
            $params[] = $startDate;
            $types .= "s";
        }
        
        if ($endDate) {
            $sql .= " AND e.expense_date <= ?";
            $params[] = $endDate;
            $types .= "s";
        }
        
        if ($categoryId) {
            $sql .= " AND e.category_id = ?";
            $params[] = $categoryId;
            $types .= "i";
        }
        
        $sql .= " ORDER BY e.expense_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $expenses = [];
        while ($row = $result->fetch_assoc()) {
            $expenses[] = $row;
        }
        
        return $expenses;
    }
    
    public function getExpenseById($userId, $expenseId) {
        $stmt = $this->db->prepare("SELECT e.*, ec.name as category_name 
                                   FROM expenses e 
                                   JOIN expense_categories ec ON e.category_id = ec.id 
                                   WHERE e.id = ? AND e.user_id = ?");
        $stmt->bind_param("ii", $expenseId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function createExpense($userId, $categoryId, $amount, $description, $expenseDate) {
        $stmt = $this->db->prepare("INSERT INTO expenses (user_id, category_id, amount, description, expense_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iidss", $userId, $categoryId, $amount, $description, $expenseDate);
        
        return $stmt->execute();
    }
    
    public function updateExpense($userId, $expenseId, $categoryId, $amount, $description, $expenseDate) {
        $stmt = $this->db->prepare("UPDATE expenses SET category_id = ?, amount = ?, description = ?, expense_date = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("idssii", $categoryId, $amount, $description, $expenseDate, $expenseId, $userId);
        
        return $stmt->execute();
    }
    
    public function deleteExpense($userId, $expenseId) {
        $stmt = $this->db->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $expenseId, $userId);
        
        return $stmt->execute();
    }
    
    public function getTotalExpenseByDateRange($userId, $startDate = null, $endDate = null, $categoryId = null) {
        $sql = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ?";
        $params = [$userId];
        $types = "i";
        
        if ($startDate) {
            $sql .= " AND expense_date >= ?";
            $params[] = $startDate;
            $types .= "s";
        }
        
        if ($endDate) {
            $sql .= " AND expense_date <= ?";
            $params[] = $endDate;
            $types .= "s";
        }
        
        if ($categoryId) {
            $sql .= " AND category_id = ?";
            $params[] = $categoryId;
            $types .= "i";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }
    
    public function getRecentExpenses($userId, $limit = 5) {
        $stmt = $this->db->prepare("SELECT e.*, ec.name as category_name 
                                   FROM expenses e 
                                   JOIN expense_categories ec ON e.category_id = ec.id 
                                   WHERE e.user_id = ? 
                                   ORDER BY e.expense_date DESC 
                                   LIMIT ?");
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $expenses = [];
        while ($row = $result->fetch_assoc()) {
            $expenses[] = $row;
        }
        
        return $expenses;
    }
    
    public function getExpenseByCategory($userId, $startDate = null, $endDate = null) {
        $sql = "SELECT ec.id, ec.name, SUM(e.amount) as total 
                FROM expenses e 
                JOIN expense_categories ec ON e.category_id = ec.id 
                WHERE e.user_id = ?";
        $params = [$userId];
        $types = "i";
        
        if ($startDate) {
            $sql .= " AND e.expense_date >= ?";
            $params[] = $startDate;
            $types .= "s";
        }
        
        if ($endDate) {
            $sql .= " AND e.expense_date <= ?";
            $params[] = $endDate;
            $types .= "s";
        }
        
        $sql .= " GROUP BY ec.id, ec.name ORDER BY total DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $categorySummary = [];
        while ($row = $result->fetch_assoc()) {
            $categorySummary[] = $row;
        }
        
        return $categorySummary;
    }
}
?>