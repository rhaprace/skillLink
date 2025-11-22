<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['books'])) {
    header('Location: import-books.php');
    exit();
}

$selectedBooks = $_POST['books'];
$imported = 0;
$failed = 0;
$errors = [];

foreach ($selectedBooks as $bookJson) {
    $book = json_decode($bookJson, true);
    
    if (!$book) {
        $failed++;
        continue;
    }
    
    try {
        // Download cover image if available
        $coverImage = null;
        if (!empty($book['cover_image_url'])) {
            $coverImage = downloadCoverImage($book['cover_image_url'], $book['google_books_id']);
        }
        
        // Insert book into database
        $sql = "INSERT INTO books (title, description, author, category_id, cover_image, 
                difficulty_level, estimated_duration, is_featured, content)
                VALUES (:title, :description, :author, :category_id, :cover_image, 
                :difficulty_level, :estimated_duration, 0, :content)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':title' => $book['title'],
            ':description' => $book['description'],
            ':author' => $book['author'],
            ':category_id' => $book['category_id'],
            ':cover_image' => $coverImage,
            ':difficulty_level' => $book['difficulty_level'],
            ':estimated_duration' => $book['estimated_duration'],
            ':content' => generatePlaceholderContent($book)
        ]);
        
        if ($result) {
            $imported++;
        } else {
            $failed++;
            $errors[] = "Failed to import: " . $book['title'];
        }
        
    } catch (Exception $e) {
        $failed++;
        $errors[] = $book['title'] . ": " . $e->getMessage();
    }
}

// Set success message
$_SESSION['import_success'] = "Successfully imported $imported book(s)";
if ($failed > 0) {
    $_SESSION['import_warning'] = "$failed book(s) failed to import";
}
if (!empty($errors)) {
    $_SESSION['import_errors'] = $errors;
}

header('Location: import-books.php?imported=' . $imported);
exit();

/**
 * Download and save cover image
 */
function downloadCoverImage($url, $googleBooksId) {
    // Replace http with https for security
    $url = str_replace('http://', 'https://', $url);
    
    // Create directory if it doesn't exist
    $uploadDir = __DIR__ . '/../assets/images/book-covers/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate filename
    $filename = 'google-books-' . $googleBooksId . '.jpg';
    $filepath = $uploadDir . $filename;
    
    // Download image
    $ch = curl_init($url);
    $fp = fopen($filepath, 'wb');
    
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $success = curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    
    if ($success && file_exists($filepath) && filesize($filepath) > 0) {
        return $filename;
    }
    
    // Clean up failed download
    if (file_exists($filepath)) {
        unlink($filepath);
    }
    
    return null;
}

/**
 * Generate placeholder content for imported books
 */
function generatePlaceholderContent($book) {
    $content = "<h2>About This Book</h2>\n\n";
    $content .= "<p>" . htmlspecialchars($book['description']) . "</p>\n\n";
    
    $content .= "<h2>What You'll Learn</h2>\n\n";
    $content .= "<p>This book covers essential topics in " . htmlspecialchars($book['title']) . ". ";
    $content .= "Perfect for " . $book['difficulty_level'] . " level learners.</p>\n\n";
    
    $content .= "<h2>Book Details</h2>\n\n";
    $content .= "<ul>\n";
    $content .= "<li><strong>Author:</strong> " . htmlspecialchars($book['author']) . "</li>\n";
    $content .= "<li><strong>Pages:</strong> " . $book['page_count'] . "</li>\n";
    $content .= "<li><strong>Estimated Reading Time:</strong> " . $book['estimated_duration'] . " minutes</li>\n";
    $content .= "<li><strong>Level:</strong> " . ucfirst($book['difficulty_level']) . "</li>\n";
    $content .= "</ul>\n\n";
    
    $content .= "<p><em>Note: This is an imported book from Google Books. Full content may be available through external sources.</em></p>";
    
    return $content;
}

