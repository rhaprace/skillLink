# Book Cover Images

This folder contains book cover images for the SkillLink platform.

## Placeholder Images

The system automatically cycles through **4 placeholder images** for books without custom covers:

- `placeholder-1.jpg` - Used for books with ID % 4 = 1 (e.g., Book ID: 1, 5, 9, 13...)
- `placeholder-2.jpg` - Used for books with ID % 4 = 2 (e.g., Book ID: 2, 6, 10, 14...)
- `placeholder-3.jpg` - Used for books with ID % 4 = 3 (e.g., Book ID: 3, 7, 11, 15...)
- `placeholder-4.jpg` - Used for books with ID % 4 = 0 (e.g., Book ID: 4, 8, 12, 16...)

## How It Works

When a book doesn't have a custom `cover_image` in the database, the system automatically selects one of the 4 placeholder images based on the book's ID using this formula:

```php
$placeholderNumber = ($bookId % 4) + 1;
$coverImage = "placeholder-{$placeholderNumber}.jpg";
```

This ensures:
- ✅ Visual variety across your book catalog
- ✅ Consistent image assignment (same book always gets the same placeholder)
- ✅ No need to manually assign covers to every book
- ✅ Easy to update - just replace the 4 placeholder files

## Recommended Image Specifications

- **Format:** JPG or PNG
- **Dimensions:** 600x800px (3:4 aspect ratio)
- **File Size:** < 200KB per image
- **Style:** Book-themed, professional, clean design

## Where to Get Free Book Images

1. **Unsplash** - https://unsplash.com/s/photos/book
2. **Pexels** - https://www.pexels.com/search/book/
3. **Pixabay** - https://pixabay.com/images/search/book/

## Custom Book Covers

To add a custom cover for a specific book:

1. Upload the image to this folder (e.g., `my-custom-book.jpg`)
2. Update the book's `cover_image` field in the database:
   ```sql
   UPDATE books SET cover_image = 'my-custom-book.jpg' WHERE id = 1;
   ```

## Current Setup

The system is configured to use these 4 placeholder images. Simply add your images to this folder with these exact filenames:

- ✅ `placeholder-1.jpg`
- ✅ `placeholder-2.jpg`
- ✅ `placeholder-3.jpg`
- ✅ `placeholder-4.jpg`

**Fallback:** If an image fails to load, the system falls back to `placeholder-1.jpg`

