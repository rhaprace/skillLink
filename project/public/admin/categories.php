<?php
session_start();
require_once '../../src/middleware/admin-auth.php';
require_once '../../src/config/database.php';
require_once '../../src/controllers/AdminController.php';

$pageTitle = 'Categories Management - Admin - SkillLink';
$adminController = new AdminController($pdo);

$categories = $adminController->getAllCategoriesAdmin();

require_once '../../src/includes/components/admin-header.php';
?>

<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-black mb-2">Categories Management</h1>
            <p class="text-gray-600">Manage book categories</p>
        </div>
        <button onclick="showCreateCategoryModal()" class="btn btn-primary">
            + Add New Category
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($categories as $category): ?>
        <div class="card">
            <div class="p-6">
                <div class="flex items-start justify-between mb-3">
                    <div class="text-3xl"><?php echo $category['icon']; ?></div>
                    <span class="admin-badge admin-badge-info">
                        <?php echo $category['books_count']; ?> books
                    </span>
                </div>
                <h3 class="text-xl font-bold text-black mb-2">
                    <?php echo htmlspecialchars($category['name']); ?>
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    <?php echo htmlspecialchars($category['description'] ?? 'No description'); ?>
                </p>
                <div class="flex gap-2">
                    <button 
                        onclick='showEditCategoryModal(<?php echo json_encode($category); ?>)'
                        class="btn btn-secondary btn-sm flex-1">
                        Edit
                    </button>
                    <button 
                        onclick="showDeleteCategoryModal(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>', <?php echo $category['books_count']; ?>)"
                        class="btn btn-danger btn-sm flex-1">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div id="createCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="Modal.hide('createCategoryModal', event)">
    <div class="card max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <h3 class="text-2xl font-bold text-black mb-4">Create Category</h3>
            <form method="POST" action="create-category.php" class="space-y-4">
                <div class="form-group">
                    <label for="name" class="form-label">Name *</label>
                    <input type="text" id="name" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="slug" class="form-label">Slug *</label>
                    <input type="text" id="slug" name="slug" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="icon" class="form-label">Icon (Emoji) *</label>
                    <input type="text" id="icon" name="icon" class="form-input" placeholder="📚" required>
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="3" class="form-input"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary flex-1">Create</button>
                    <button type="button" onclick="Modal.hide('createCategoryModal')" class="btn btn-secondary flex-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="editCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="Modal.hide('editCategoryModal', event)">
    <div class="card max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <h3 class="text-2xl font-bold text-black mb-4">Edit Category</h3>
            <form method="POST" action="edit-category.php" class="space-y-4">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="edit_name" class="form-label">Name *</label>
                    <input type="text" id="edit_name" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="edit_slug" class="form-label">Slug *</label>
                    <input type="text" id="edit_slug" name="slug" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="edit_icon" class="form-label">Icon (Emoji) *</label>
                    <input type="text" id="edit_icon" name="icon" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="edit_description" class="form-label">Description</label>
                    <textarea id="edit_description" name="description" rows="3" class="form-input"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary flex-1">Update</button>
                    <button type="button" onclick="Modal.hide('editCategoryModal')" class="btn btn-secondary flex-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="deleteCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="Modal.hide('deleteCategoryModal', event)">
    <div class="card max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <h3 class="text-2xl font-bold text-red-600 mb-4">Delete Category</h3>
            <p class="text-gray-600 mb-6" id="deleteCategoryMessage"></p>
            <form method="POST" action="delete-category.php" id="deleteCategoryForm">
                <input type="hidden" name="category_id" id="deleteCategoryId">
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-danger flex-1" id="deleteCategoryBtn">Delete</button>
                    <button type="button" onclick="Modal.hide('deleteCategoryModal')" class="btn btn-secondary flex-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showCreateCategoryModal() {
    Modal.show('createCategoryModal');
}

function showEditCategoryModal(category) {
    document.getElementById('edit_id').value = category.id;
    document.getElementById('edit_name').value = category.name;
    document.getElementById('edit_slug').value = category.slug;
    document.getElementById('edit_icon').value = category.icon;
    document.getElementById('edit_description').value = category.description || '';
    Modal.show('editCategoryModal');
}

function showDeleteCategoryModal(id, name, booksCount) {
    document.getElementById('deleteCategoryId').value = id;
    const message = document.getElementById('deleteCategoryMessage');
    const btn = document.getElementById('deleteCategoryBtn');
    
    if (booksCount > 0) {
        message.textContent = `Cannot delete "${name}" because it has ${booksCount} book(s). Please reassign or delete those books first.`;
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        message.textContent = `Are you sure you want to delete "${name}"?`;
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    
    Modal.show('deleteCategoryModal');
}
</script>

<?php require_once '../../src/includes/components/admin-footer.php'; ?>

