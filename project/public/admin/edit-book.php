<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';
require_once '../../src/models/Category.php';
require_once '../../src/models/Book.php';

$bookId = $_GET['id'] ?? null;

if (!$bookId) {
    header('Location: books.php');
    exit();
}

$bookModel = new Book($pdo);
$book = $bookModel->getById($bookId);

if (!$book) {
    header('Location: books.php?error=' . urlencode('Book not found'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminController = new AdminController($pdo);
    
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'content' => trim($_POST['content'] ?? ''),
        'author' => trim($_POST['author'] ?? ''),
        'category_id' => $_POST['category_id'] ?? null,
        'cover_image' => trim($_POST['cover_image'] ?? 'default-book.jpg'),
        'difficulty_level' => $_POST['difficulty_level'] ?? 'beginner',
        'estimated_duration' => trim($_POST['estimated_duration'] ?? ''),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0
    ];
    
    $result = $adminController->updateBook($bookId, $data);
    
    if ($result['success']) {
        header('Location: books.php?success=' . urlencode($result['message']));
        exit();
    } else {
        header('Location: edit-book.php?id=' . $bookId . '&error=' . urlencode($result['message']));
        exit();
    }
}

$categoryModel = new Category($pdo);
$categories = $categoryModel->getAll();

$pageTitle = 'Edit Book - Admin - SkillLink';
require_once '../../src/includes/components/admin-header.php';
?>

<div class="mb-6">
    <a href="books.php" class="text-gray-600 hover:text-black mb-4 inline-block">
        ← Back to Books
    </a>
    <h1 class="text-3xl font-bold text-black mb-2">Edit Book</h1>
    <p class="text-gray-600">Update book information</p>
</div>

<div class="max-w-4xl">
    <div class="card">
        <div class="p-6">
            <form method="POST" action="" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="form-group">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" id="title" name="title" class="form-input" 
                               value="<?php echo htmlspecialchars($book['title']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="author" class="form-label">Author *</label>
                        <input type="text" id="author" name="author" class="form-input" 
                               value="<?php echo htmlspecialchars($book['author']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description *</label>
                    <textarea id="description" name="description" rows="3" class="form-input" required><?php echo htmlspecialchars($book['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="content" class="form-label">Content (HTML) *</label>
                    <textarea id="content" name="content" rows="10" class="form-input font-mono text-sm" required><?php echo htmlspecialchars($book['content']); ?></textarea>
                    <p class="text-xs text-gray-500 mt-1">You can use HTML tags for formatting</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="form-group">
                        <label for="category_id" class="form-label">Category *</label>
                        <select id="category_id" name="category_id" class="form-input" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                    <?php echo $book['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="difficulty_level" class="form-label">Difficulty *</label>
                        <select id="difficulty_level" name="difficulty_level" class="form-input" required>
                            <option value="beginner" <?php echo $book['difficulty_level'] === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                            <option value="intermediate" <?php echo $book['difficulty_level'] === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="advanced" <?php echo $book['difficulty_level'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="estimated_duration" class="form-label">Duration *</label>
                        <input type="text" id="estimated_duration" name="estimated_duration" 
                               class="form-input" value="<?php echo htmlspecialchars($book['estimated_duration']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cover_image" class="form-label">Cover Image</label>
                    <input type="text" id="cover_image" name="cover_image" 
                           class="form-input" value="<?php echo htmlspecialchars($book['cover_image']); ?>">
                    <p class="text-xs text-gray-500 mt-1">Enter image filename (must be in assets/images/)</p>
                </div>

                <div class="form-group">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" class="w-4 h-4" 
                               <?php echo $book['is_featured'] ? 'checked' : ''; ?>>
                        <span class="text-sm font-medium text-gray-700">Mark as Featured</span>
                    </label>
                </div>

                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="btn btn-primary">Update Book</button>
                    <a href="books.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../src/includes/components/admin-footer.php'; ?>

