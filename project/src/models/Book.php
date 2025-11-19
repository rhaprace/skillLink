<?php

class Book
{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT b.*, c.name as category_name, c.slug as category_slug 
                FROM books b 
                LEFT JOIN categories c ON b.category_id = c.id 
                ORDER BY b.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->pdo->prepare($sql);
        
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT b.*, c.name as category_name, c.slug as category_slug 
                FROM books b 
                LEFT JOIN categories c ON b.category_id = c.id 
                WHERE b.id = :id 
                LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch();
    }

    public function getByCategory($categoryId, $limit = null) {
        $sql = "SELECT b.*, c.name as category_name, c.slug as category_slug 
                FROM books b 
                LEFT JOIN categories c ON b.category_id = c.id 
                WHERE b.category_id = :category_id 
                ORDER BY b.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function search($query) {
        $sql = "SELECT b.*, c.name as category_name, c.slug as category_slug 
                FROM books b 
                LEFT JOIN categories c ON b.category_id = c.id 
                WHERE b.title LIKE :query OR b.description LIKE :query 
                ORDER BY b.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $searchTerm = '%' . $query . '%';
        $stmt->execute([':query' => $searchTerm]);
        
        return $stmt->fetchAll();
    }

    public function getFeatured($limit = 6) {
        $sql = "SELECT b.*, c.name as category_name, c.slug as category_slug 
                FROM books b 
                LEFT JOIN categories c ON b.category_id = c.id 
                WHERE b.is_featured = 1 
                ORDER BY b.created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getRecent($limit = 10) {
        $sql = "SELECT b.*, c.name as category_name, c.slug as category_slug 
                FROM books b 
                LEFT JOIN categories c ON b.category_id = c.id 
                ORDER BY b.created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function incrementViews($id) {
        $sql = "UPDATE books SET views_count = views_count + 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM books";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        return $result['total'];
    }
}

