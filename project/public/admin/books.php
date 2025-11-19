<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';
require_once '../../src/models/Category.php';

$pageTitle = 'Books Management - Admin - SkillLink';
$adminController = new AdminController($pdo);
$categoryModel = new Category($pdo);

$search = $_GET['search'] ?? null;
$categoryId = $_GET['category'] ?? null;
$difficulty = $_GET['difficulty'] ?? null;

$books = $adminController->getAllBooksAdmin($search, $categoryId, $difficulty);
$categories = $categoryModel->getAll();

require_once '../../src/includes/components/admin-header.php';
?>

<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-black mb-2">Books Management</h1>
            <p class="text-gray-600">Manage all learning materials</p>
        </div>
        <a href="create-book.php" class="btn btn-primary">
            + Add New Book
        </a>
    </div>
</div>

<div class="card mb-6">
    <div class="p-6">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input
                type="text"
                name="search"
                placeholder="Search by title or author..."
                class="form-input"
                value="<?php echo htmlspecialchars($search ?? ''); ?>"
            >
            <select name="category" class="form-input">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $categoryId == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="difficulty" class="form-input">
                <option value="">All Levels</option>
                <option value="beginner" <?php echo $difficulty === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                <option value="intermediate" <?php echo $difficulty === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                <option value="advanced" <?php echo $difficulty === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary flex-1">Filter</button>
                <a href="books.php" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Difficulty</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($books)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-gray-500 py-8">No books found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td class="font-mono text-sm">#<?php echo $book['id']; ?></td>
                            <td class="font-semibold"><?php echo htmlspecialchars($book['title']); ?></td>
                            <td class="text-gray-600"><?php echo htmlspecialchars($book['author']); ?></td>
                            <td>
                                <span class="admin-badge admin-badge-default">
                                    <?php echo htmlspecialchars($book['category_name'] ?? 'Uncategorized'); ?>
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-<?php 
                                    echo $book['difficulty_level'] === 'beginner' ? 'success' : 
                                        ($book['difficulty_level'] === 'intermediate' ? 'warning' : 'danger'); 
                                ?>">
                                    <?php echo ucfirst($book['difficulty_level']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="toggle-featured.php" class="inline">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" class="text-sm <?php echo $book['is_featured'] ? 'text-yellow-600' : 'text-gray-400'; ?>">
                                        <?php echo $book['is_featured'] ? '★ Featured' : '☆ Not Featured'; ?>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="../book.php?id=<?php echo $book['id']; ?>" 
                                       target="_blank"
                                       class="btn btn-secondary btn-sm">
                                        View
                                    </a>
                                    <a href="edit-book.php?id=<?php echo $book['id']; ?>" 
                                       class="btn btn-secondary btn-sm">
                                        Edit
                                    </a>
                                    <button 
                                        onclick="showDeleteBookModal(<?php echo $book['id']; ?>, '<?php echo htmlspecialchars($book['title']); ?>')"
                                        class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="deleteBookModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="Modal.hide('deleteBookModal', event)">
    <div class="card max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <h3 class="text-2xl font-bold text-red-600 mb-4">Delete Book</h3>
            <p class="text-gray-600 mb-6">
                Are you sure you want to delete <strong id="deleteBookTitle"></strong>?
            </p>
            <form method="POST" action="delete-book.php">
                <input type="hidden" name="book_id" id="deleteBookId">
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-danger flex-1">Delete</button>
                    <button type="button" onclick="Modal.hide('deleteBookModal')" class="btn btn-secondary flex-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showDeleteBookModal(bookId, title) {
    document.getElementById('deleteBookId').value = bookId;
    document.getElementById('deleteBookTitle').textContent = title;
    Modal.show('deleteBookModal');
}
</script>

<?php require_once '../../src/includes/components/admin-footer.php'; ?>

