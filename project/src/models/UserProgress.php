<?php

class UserProgress
{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getUserProgress($userId, $bookId) {
        $sql = "SELECT * FROM user_progress 
                WHERE user_id = :user_id AND book_id = :book_id 
                LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':book_id' => $bookId
        ]);
        
        return $stmt->fetch();
    }

    public function getAllUserProgress($userId, $status = null) {
        $sql = "SELECT up.*, b.title, b.description, b.cover_image, b.author, 
                       b.difficulty_level, b.estimated_duration, c.name as category_name 
                FROM user_progress up 
                JOIN books b ON up.book_id = b.id 
                LEFT JOIN categories c ON b.category_id = c.id 
                WHERE up.user_id = :user_id";
        
        if ($status) {
            $sql .= " AND up.status = :status";
        }
        
        $sql .= " ORDER BY up.last_accessed DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $params = [':user_id' => $userId];
        
        if ($status) {
            $params[':status'] = $status;
        }
        
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateProgress($userId, $bookId, $progressPercentage) {
        $existing = $this->getUserProgress($userId, $bookId);
        
        if ($existing) {
            $status = $progressPercentage >= 100 ? 'completed' : 'in_progress';
            $completedAt = $progressPercentage >= 100 ? date('Y-m-d H:i:s') : null;
            
            $sql = "UPDATE user_progress 
                    SET progress_percentage = :progress, 
                        status = :status, 
                        completed_at = :completed_at,
                        last_accessed = NOW() 
                    WHERE user_id = :user_id AND book_id = :book_id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':progress' => $progressPercentage,
                ':status' => $status,
                ':completed_at' => $completedAt,
                ':user_id' => $userId,
                ':book_id' => $bookId
            ]);
        } else {
            $status = $progressPercentage >= 100 ? 'completed' : ($progressPercentage > 0 ? 'in_progress' : 'not_started');
            $completedAt = $progressPercentage >= 100 ? date('Y-m-d H:i:s') : null;
            
            $sql = "INSERT INTO user_progress 
                    (user_id, book_id, progress_percentage, status, completed_at) 
                    VALUES (:user_id, :book_id, :progress, :status, :completed_at)";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':user_id' => $userId,
                ':book_id' => $bookId,
                ':progress' => $progressPercentage,
                ':status' => $status,
                ':completed_at' => $completedAt
            ]);
        }
    }

    public function markAsCompleted($userId, $bookId) {
        return $this->updateProgress($userId, $bookId, 100);
    }

    public function getUserStats($userId) {
        $sql = "SELECT 
                    COUNT(*) as total_books,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_books,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_books,
                    AVG(CASE WHEN status = 'completed' THEN progress_percentage ELSE NULL END) as avg_completion
                FROM user_progress 
                WHERE user_id = :user_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        return $stmt->fetch();
    }

    public function getRecentlyAccessed($userId, $limit = 5) {
        $sql = "SELECT up.*, b.title, b.description, b.cover_image, b.author, 
                       b.difficulty_level, c.name as category_name 
                FROM user_progress up 
                JOIN books b ON up.book_id = b.id 
                LEFT JOIN categories c ON b.category_id = c.id 
                WHERE up.user_id = :user_id 
                ORDER BY up.last_accessed DESC 
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
