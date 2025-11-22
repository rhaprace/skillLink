<?php

class Review
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($userId, $bookId, $rating, $reviewText = null)
    {
        try {
            $sql = "INSERT INTO book_reviews (user_id, book_id, rating, review_text, created_at)
                    VALUES (:user_id, :book_id, :rating, :review_text, NOW())
                    ON DUPLICATE KEY UPDATE
                    rating = VALUES(rating),
                    review_text = VALUES(review_text),
                    updated_at = NOW()";

            $stmt = $this->pdo->prepare($sql);
            $params = [
                ':user_id' => $userId,
                ':book_id' => $bookId,
                ':rating' => $rating,
                ':review_text' => $reviewText
            ];

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Review creation failed: " . $e->getMessage());
            throw new Exception("Failed to create review: " . $e->getMessage());
        }
    }

    public function getByBookId($bookId, $limit = null, $offset = 0)
    {
        $sql = "SELECT r.*, u.username, u.email,
                (SELECT COUNT(*) FROM review_helpful WHERE review_id = r.id) as helpful_count
                FROM book_reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.book_id = :book_id
                ORDER BY r.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':book_id', $bookId, PDO::PARAM_INT);
        
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserReview($userId, $bookId)
    {
        $sql = "SELECT * FROM book_reviews 
                WHERE user_id = :user_id AND book_id = :book_id 
                LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':book_id' => $bookId
        ]);
        
        return $stmt->fetch();
    }

    public function getBookRatingStats($bookId)
    {
        try {
            $sql = "SELECT
                    COUNT(*) as total_reviews,
                    AVG(rating) as average_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                    FROM book_reviews
                    WHERE book_id = :book_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':book_id' => $bookId]);

            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Error fetching rating stats: " . $e->getMessage());
        }
    }

    public function delete($reviewId, $userId)
    {
        $sql = "DELETE FROM book_reviews 
                WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $reviewId,
            ':user_id' => $userId
        ]);
    }

    public function markHelpful($reviewId, $userId)
    {
        $sql = "INSERT IGNORE INTO review_helpful (review_id, user_id, created_at) 
                VALUES (:review_id, :user_id, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':review_id' => $reviewId,
            ':user_id' => $userId
        ]);
    }

    public function unmarkHelpful($reviewId, $userId)
    {
        $sql = "DELETE FROM review_helpful 
                WHERE review_id = :review_id AND user_id = :user_id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':review_id' => $reviewId,
            ':user_id' => $userId
        ]);
    }

    public function isMarkedHelpful($reviewId, $userId)
    {
        $sql = "SELECT COUNT(*) as count FROM review_helpful
                WHERE review_id = :review_id AND user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':review_id' => $reviewId,
            ':user_id' => $userId
        ]);

        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    public function getAllReviews($search = null, $bookId = null, $userId = null, $rating = null)
    {
        $sql = "SELECT r.*, u.username, u.email, b.title as book_title,
                (SELECT COUNT(*) FROM review_helpful WHERE review_id = r.id) as helpful_count
                FROM book_reviews r
                JOIN users u ON r.user_id = u.id
                JOIN books b ON r.book_id = b.id
                WHERE 1=1";

        $params = [];

        if ($search) {
            $sql .= " AND (u.username LIKE :search OR b.title LIKE :search2 OR r.review_text LIKE :search3)";
            $params[':search'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
            $params[':search3'] = '%' . $search . '%';
        }

        if ($bookId) {
            $sql .= " AND r.book_id = :book_id";
            $params[':book_id'] = $bookId;
        }

        if ($userId) {
            $sql .= " AND r.user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        if ($rating) {
            $sql .= " AND r.rating = :rating";
            $params[':rating'] = $rating;
        }

        $sql .= " ORDER BY r.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function deleteByAdmin($reviewId)
    {
        $sql = "DELETE FROM book_reviews WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $reviewId]);
    }

    public function getTotalReviewsCount()
    {
        $sql = "SELECT COUNT(*) as count FROM book_reviews";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        return $result['count'];
    }
}

