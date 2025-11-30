# Portfolio Detail Pages & Rich Content Guide

## Overview

Your portfolio system now includes:
- ✅ Rich text editor (Quill.js) for detailed project descriptions
- ✅ Featured image for portfolio list display
- ✅ Multiple image gallery (Shopify/AliExpress style)
- ✅ Detailed project pages with full content
- ✅ Interactive image gallery with hover effects
- ✅ Responsive design for all devices

---

## Features

### 1. Rich Text Editor
- **WYSIWYG Interface** - What you see is what you get
- **Formatting Options:**
  - Headings (H1, H2, H3)
  - Bold, Italic, Underline, Strikethrough
  - Blockquotes
  - Code blocks
  - Ordered & unordered lists
  - Links
  - Images
  - Videos
  - Clean formatting

### 2. Featured Image
- **Display:** Shows on portfolio list page
- **Size:** Recommended 600x400px
- **Purpose:** First impression of the project
- **Upload:** Direct upload or external URL

### 3. Image Gallery
- **Multiple Images:** Add unlimited images
- **Shopify Style:** Hover to view, click to zoom
- **Thumbnails:** Easy navigation
- **Alt Text:** For accessibility
- **Sorting:** Automatic ordering

### 4. Project Detail Page
- **Full Content:** Rich text body content
- **Image Gallery:** Interactive image viewer
- **Project Info:** Title, category, dates
- **External Link:** Link to live project
- **Responsive:** Works on all devices

---

## How to Use

### Step 1: Create Portfolio Item

1. Go to **Admin Dashboard** → **Portfolio**
2. Click **Add New Portfolio Item** or **Edit** existing
3. Fill in the form:
   - **Title:** Project name
   - **Short Description:** 1-2 sentences (appears on list)
   - **Full Description:** Rich text content (appears on detail page)
   - **Category:** Project type
   - **Project Link:** URL to live project
   - **Featured Image:** Main image for list

### Step 2: Add Rich Text Content

1. In the **Full Description** field, you'll see a rich text editor
2. Use the toolbar to format your content:
   - Click **Heading** to add titles
   - Click **Bold** to emphasize text
   - Click **Link** to add URLs
   - Click **Image** to embed images
   - Click **Code Block** for code snippets

### Step 3: Add Project Images

1. After creating the portfolio item, click **Edit**
2. Scroll down to **Portfolio Images Gallery**
3. Click **Add Image to Gallery**
4. Upload image and add alt text
5. Images appear in gallery below

### Step 4: View on Frontend

1. Go to **Portfolio** page
2. See featured image with title and description
3. Click **View Details** to see full project page
4. Hover over images to see them
5. Click images to zoom in

---

## Rich Text Editor Guide

### Toolbar Options

| Button | Function | Shortcut |
|--------|----------|----------|
| H1, H2, H3 | Headings | - |
| B | Bold | Ctrl+B |
| I | Italic | Ctrl+I |
| U | Underline | Ctrl+U |
| S | Strikethrough | - |
| " | Blockquote | - |
| {} | Code Block | - |
| 1. | Ordered List | - |
| • | Bullet List | - |
| Link | Add Link | Ctrl+K |
| Image | Insert Image | - |
| Video | Embed Video | - |
| X | Clear Format | - |

### Examples

#### Adding a Heading
1. Click **H1** dropdown
2. Select heading level
3. Type your heading

#### Adding Bold Text
1. Select text
2. Click **B** or press Ctrl+B
3. Text becomes bold

#### Adding a Link
1. Select text
2. Click **Link** icon
3. Enter URL
4. Click OK

#### Adding an Image
1. Click **Image** icon
2. Paste image URL or upload
3. Image appears in editor

#### Adding Code
1. Click **Code Block** icon
2. Paste your code
3. Code appears with formatting

---

## Image Gallery (Shopify Style)

### How It Works

1. **Featured Image:** Main image shown first
2. **Thumbnails:** Small images below main image
3. **Hover:** Hover over thumbnail to change main image
4. **Click:** Click main image to zoom in
5. **Responsive:** Adapts to screen size

### Adding Images

1. Edit portfolio item
2. Scroll to **Portfolio Images Gallery**
3. Click **Add Image to Gallery**
4. Upload image
5. Add alt text (for accessibility)
6. Click **Add Image**

### Image Organization

- Images display in order added
- First image is featured image
- Thumbnails show all images
- Easy to delete images

### Best Practices

- **Size:** 800x600px or larger
- **Format:** JPG for photos, PNG for graphics
- **Quality:** High quality (90%+ compression)
- **Alt Text:** Describe the image
- **Quantity:** 3-5 images per project

---

## Featured Image vs Gallery Images

### Featured Image
- **Purpose:** Thumbnail on portfolio list
- **Size:** 600x400px recommended
- **Display:** On portfolio list page
- **Required:** Yes (for list display)

### Gallery Images
- **Purpose:** Detailed project view
- **Size:** 800x600px or larger
- **Display:** On project detail page
- **Required:** No (optional)

---

## Project Detail Page

### What Visitors See

1. **Image Gallery**
   - Featured image (main)
   - Thumbnails below
   - Hover to change
   - Click to zoom

2. **Project Info**
   - Title
   - Category badge
   - Short description
   - View Project button
   - Created/Updated dates

3. **Full Description**
   - Rich text content
   - Formatted text
   - Embedded images
   - Code blocks
   - Lists and quotes

### URL Structure

```
/portfolio-detail.php?id=1
```

Replace `1` with portfolio item ID.

---

## Database Structure

### portfolio_items Table

| Column | Type | Purpose |
|--------|------|---------|
| id | INT | Unique ID |
| title | VARCHAR | Project title |
| description | TEXT | Short description |
| body | LONGTEXT | Full rich text content |
| featured_image_url | VARCHAR | Featured image URL |
| featured_image_filename | VARCHAR | Uploaded filename |
| category | VARCHAR | Project category |
| link | VARCHAR | External project link |
| created_at | TIMESTAMP | Creation date |
| updated_at | TIMESTAMP | Last update |

### portfolio_images Table

| Column | Type | Purpose |
|--------|------|---------|
| id | INT | Unique ID |
| portfolio_id | INT | Parent portfolio item |
| image_url | VARCHAR | Image URL |
| image_filename | VARCHAR | Uploaded filename |
| alt_text | VARCHAR | Alt text |
| sort_order | INT | Display order |
| created_at | TIMESTAMP | Upload date |

---

## Styling & Customization

### Featured Image
- Recommended: 600x400px (3:2 ratio)
- Format: JPG (smaller file size)
- Quality: 80-85% compression

### Gallery Images
- Recommended: 800x600px (4:3 ratio)
- Format: JPG or PNG
- Quality: 90%+ compression

### Rich Text Content
- Headings: Use H2 and H3 (H1 is page title)
- Paragraphs: Keep 2-3 sentences per paragraph
- Lists: Use for features or steps
- Code: Use code blocks for code snippets
- Images: Embed for visual explanation

---

## Best Practices

### Content Organization

1. **Title:** Clear, descriptive project name
2. **Short Description:** 1-2 sentences summary
3. **Featured Image:** Best representation of project
4. **Full Description:** Detailed explanation
5. **Gallery Images:** Process, results, details

### Writing Tips

- **Be Concise:** Get to the point
- **Use Headings:** Break up content
- **Add Images:** Visual breaks
- **Use Lists:** For features/steps
- **Include Links:** To live project

### Image Tips

- **Optimize:** Compress before upload
- **Consistent:** Similar sizes and style
- **Descriptive:** Meaningful alt text
- **Quality:** High resolution
- **Relevant:** Show project details

---

## Troubleshooting

### Rich Text Not Saving
- Check browser console for errors
- Ensure JavaScript is enabled
- Try different browser
- Clear browser cache

### Images Not Uploading
- Check file size (max 5MB)
- Check file format (JPG, PNG, GIF, WebP)
- Check /uploads folder permissions
- Check server disk space

### Gallery Not Displaying
- Ensure images are uploaded
- Check image URLs are correct
- Verify portfolio item is published
- Check browser console for errors

### Featured Image Not Showing
- Upload featured image
- Or provide featured image URL
- Check image URL is accessible
- Verify image format is supported

---

## Advanced Features

### Embedding Videos

In rich text editor:
1. Click **Video** icon
2. Paste YouTube/Vimeo URL
3. Video embeds in content

### Adding Code Blocks

In rich text editor:
1. Click **Code Block** icon
2. Paste code
3. Code displays with formatting

### Linking to Other Projects

In rich text editor:
1. Select text
2. Click **Link** icon
3. Enter portfolio-detail.php?id=X
4. Link to other projects

---

## File Structure

```
portfolio/
├── portfolio-detail.php        ← Project detail page
├── portfolio.php               ← Portfolio list page
├── admin/portfolio.php         ← Admin management
├── includes/upload.php         ← Upload handler
├── uploads/                    ← Uploaded images
└── assets/css/style.css        ← Styling
```

---

## API Reference

### Display Portfolio Item

```php
<?php
$id = intval($_GET['id']);
$portfolio = $conn->query("SELECT * FROM portfolio_items WHERE id = $id")->fetch_assoc();
echo $portfolio['body']; // Rich text content
?>
```

### Display Gallery Images

```php
<?php
$images = $conn->query("SELECT * FROM portfolio_images WHERE portfolio_id = $id ORDER BY sort_order");
while ($img = $images->fetch_assoc()) {
    echo '<img src="' . $img['image_url'] . '" alt="' . $img['alt_text'] . '">';
}
?>
```

---

## Support

For issues:
1. Check troubleshooting section
2. Review code comments
3. Check browser console
4. Verify file permissions
5. Check database tables

---

## Version Info

- **Feature:** Portfolio Detail Pages v1.0
- **Editor:** Quill.js 1.3.6
- **Release:** November 30, 2025
- **Status:** Production Ready

---

**Showcase your projects beautifully!** 🎨
