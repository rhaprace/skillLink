<?php

class Bookmark
{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addBookmark($userId, $bookId) {
        try {
            $sql = "INSERT INTO user_bookmarks (user_id, book_id) VALUES (:user_id, :book_id)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':user_id' => $userId,
                ':book_id' => $bookId
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return false;
            }
            throw $e;
        }
    }

    public function removeBookmark($userId, $bookId) {
        $sql = "DELETE FROM user_bookmarks WHERE user_id = :user_id AND book_id = :book_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':book_id' => $bookId
        ]);
    }

    public function isBookmarked($userId, $bookId) {
        $sql = "SELECT COUNT(*) as count FROM user_bookmarks WHERE user_id = :user_id AND book_id = :book_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':book_id' => $bookId
        ]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function getUserBookmarks($userId, $limit = null, $offset = 0) {
        $sql = "SELECT b.*, c.name as category_name, c.slug as category_slug, ub.created_at as bookmarked_at
                FROM user_bookmarks ub
                INNER JOIN books b ON ub.book_id = b.id
                LEFT JOIN categories c ON b.category_id = c.id
                WHERE ub.user_id = :user_id
                ORDER BY ub.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserBookmarkIds($userId) {
        $sql = "SELECT book_id FROM user_bookmarks WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $results;
    }

    public function getBookmarkCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM user_bookmarks WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function toggleBookmark($userId, $bookId) {
        if ($this->isBookmarked($userId, $bookId)) {
            return [
                'success' => $this->removeBookmark($userId, $bookId),
                'action' => 'removed',
                'bookmarked' => false
            ];
        } else {
            return [
                'success' => $this->addBookmark($userId, $bookId),
                'action' => 'added',
                'bookmarked' => true
            ];
        }
    }
}

