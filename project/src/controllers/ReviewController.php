<?php
require_once __DIR__ . '/../models/Review.php';

class ReviewController
{
    private $reviewModel;

    public function __construct($pdo)
    {
        $this->reviewModel = new Review($pdo);
    }

    public function submitReview($userId, $bookId, $rating, $reviewText = null)
    {
        if (!$userId) {
            return ['success' => false, 'message' => 'You must be logged in to submit a review'];
        }

        if ($rating < 1 || $rating > 5) {
            return ['success' => false, 'message' => 'Rating must be between 1 and 5'];
        }

        $result = $this->reviewModel->create($userId, $bookId, $rating, $reviewText);
        
        if ($result) {
            return ['success' => true, 'message' => 'Review submitted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to submit review'];
    }

    public function getBookReviews($bookId, $limit = null, $offset = 0)
    {
        return $this->reviewModel->getByBookId($bookId, $limit, $offset);
    }

    public function getUserReview($userId, $bookId)
    {
        return $this->reviewModel->getUserReview($userId, $bookId);
    }

    public function getBookRatingStats($bookId)
    {
        return $this->reviewModel->getBookRatingStats($bookId);
    }

    public function deleteReview($reviewId, $userId)
    {
        if (!$userId) {
            return ['success' => false, 'message' => 'You must be logged in'];
        }

        $result = $this->reviewModel->delete($reviewId, $userId);
        
        if ($result) {
            return ['success' => true, 'message' => 'Review deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete review'];
    }

    public function toggleHelpful($reviewId, $userId)
    {
        if (!$userId) {
            return ['success' => false, 'message' => 'You must be logged in'];
        }

        $isMarked = $this->reviewModel->isMarkedHelpful($reviewId, $userId);
        
        if ($isMarked) {
            $result = $this->reviewModel->unmarkHelpful($reviewId, $userId);
            $action = 'unmarked';
        } else {
            $result = $this->reviewModel->markHelpful($reviewId, $userId);
            $action = 'marked';
        }
        
        if ($result) {
            return ['success' => true, 'action' => $action];
        }
        
        return ['success' => false, 'message' => 'Failed to update helpful status'];
    }
}

