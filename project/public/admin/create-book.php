<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';
require_once '../../src/models/Category.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminController = new AdminController($pdo);
    
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'content' => trim($_POST['content'] ?? ''),
        'author' => trim($_POST['author'] ?? ''),
        'category_id' => $_POST['category_id'] ?? null,
        'cover_image' => !empty(trim($_POST['cover_image'] ?? '')) ? trim($_POST['cover_image']) : null,
        'difficulty_level' => $_POST['difficulty_level'] ?? 'beginner',
        'estimated_duration' => trim($_POST['estimated_duration'] ?? ''),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0
    ];
    
    $result = $adminController->createBook($data);
    
    if ($result['success']) {
        header('Location: books.php?success=' . urlencode($result['message']));
        exit();
    } else {
        header('Location: create-book.php?error=' . urlencode($result['message']));
        exit();
    }
}

$categoryModel = new Category($pdo);
$categories = $categoryModel->getAll();

$pageTitle = 'Create Book - Admin - SkillLink';
require_once '../../src/includes/components/admin-header.php';
?>

<div class="mb-6">
    <a href="books.php" class="text-gray-600 hover:text-black mb-4 inline-block">
        ← Back to Books
    </a>
    <h1 class="text-3xl font-bold text-black mb-2">Create New Book</h1>
    <p class="text-gray-600">Add a new learning material</p>
</div>

<div class="max-w-4xl">
    <div class="card">
        <div class="p-6">
            <form method="POST" action="" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="form-group">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" id="title" name="title" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="author" class="form-label">Author *</label>
                        <input type="text" id="author" name="author" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description *</label>
                    <textarea id="description" name="description" rows="3" class="form-input" required></textarea>
                </div>

                <div class="form-group">
                    <label for="content" class="form-label">Content (HTML) *</label>
                    <textarea id="content" name="content" rows="10" class="form-input font-mono text-sm" required></textarea>
                    <p class="text-xs text-gray-500 mt-1">You can use HTML tags for formatting</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="form-group">
                        <label for="category_id" class="form-label">Category *</label>
                        <select id="category_id" name="category_id" class="form-input" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="difficulty_level" class="form-label">Difficulty *</label>
                        <select id="difficulty_level" name="difficulty_level" class="form-input" required>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="estimated_duration" class="form-label">Duration *</label>
                        <input type="text" id="estimated_duration" name="estimated_duration" 
                               class="form-input" placeholder="e.g., 2 hours" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cover_image" class="form-label">Cover Image (Optional)</label>
                    <input type="text" id="cover_image" name="cover_image"
                           class="form-input" placeholder="Leave empty to use placeholder">
                    <p class="text-xs text-gray-500 mt-1">Enter image filename (e.g., my-book.jpg) or leave empty to auto-assign placeholder</p>
                </div>

                <div class="form-group">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" class="w-4 h-4">
                        <span class="text-sm font-medium text-gray-700">Mark as Featured</span>
                    </label>
                </div>

                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="btn btn-primary">Create Book</button>
                    <a href="books.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../src/includes/components/admin-footer.php'; ?>

