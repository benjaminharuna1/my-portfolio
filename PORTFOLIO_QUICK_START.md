# Portfolio v3.0 - Quick Start Guide

## ⚡ 5-Minute Setup

### Step 1: Update Database

Run these SQL queries:

```sql
-- Add new columns to portfolio_items
ALTER TABLE portfolio_items ADD COLUMN body LONGTEXT;
ALTER TABLE portfolio_items ADD COLUMN featured_image_url VARCHAR(255);
ALTER TABLE portfolio_items ADD COLUMN featured_image_filename VARCHAR(255);

-- Create portfolio_images table
CREATE TABLE portfolio_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    portfolio_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    image_filename VARCHAR(255),
    alt_text VARCHAR(255),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (portfolio_id) REFERENCES portfolio_items(id) ON DELETE CASCADE
);

-- Migrate existing data
UPDATE portfolio_items SET featured_image_url = image_url WHERE image_url IS NOT NULL;
UPDATE portfolio_items SET featured_image_filename = image_filename WHERE image_filename IS NOT NULL;
```

### Step 2: Update Files

Replace these files:
- `admin/portfolio.php` (new version with editor)
- `portfolio.php` (updated for featured images)
- `assets/css/style.css` (new gallery styling)

### Step 3: Add New File

Add this file:
- `portfolio-detail.php` (project detail page)

### Step 4: Test

1. Go to Admin → Portfolio
2. Click "Add New Portfolio Item"
3. Fill in the form
4. Click "Add"
5. Click "Edit" to add images
6. Upload images
7. View on frontend

---

## 🎯 Creating Your First Project

### In Admin Panel

1. **Admin Dashboard** → **Portfolio**
2. Click **Add New Portfolio Item**
3. Fill in:
   - **Title:** "My Awesome Project"
   - **Short Description:** "A brief description"
   - **Full Description:** Use the rich text editor
   - **Category:** "Web Design"
   - **Project Link:** "https://example.com"
   - **Featured Image:** Upload or paste URL
4. Click **Add**

### Add Images to Gallery

1. Click **Edit** on your project
2. Scroll to **Portfolio Images Gallery**
3. Click **Add Image to Gallery**
4. Upload image
5. Add alt text (e.g., "Project screenshot")
6. Click **Add Image**
7. Repeat for more images

### View on Frontend

1. Go to **Portfolio** page
2. See your project with featured image
3. Click **View Details**
4. See full project page with gallery
5. Hover/click images to view

---

## 📝 Rich Text Editor Tips

### Adding Content

**Heading:**
1. Click **H1** dropdown
2. Select heading level
3. Type heading

**Bold Text:**
1. Select text
2. Click **B** or Ctrl+B
3. Text becomes bold

**List:**
1. Click **•** (bullet list)
2. Type items
3. Press Enter for new item

**Link:**
1. Select text
2. Click **Link** icon
3. Paste URL
4. Click OK

**Image:**
1. Click **Image** icon
2. Paste image URL
3. Image appears

**Code:**
1. Click **Code Block** icon
2. Paste code
3. Code displays with formatting

---

## 🖼️ Image Gallery Guide

### Featured Image
- **Purpose:** Shown on portfolio list
- **Size:** 600x400px recommended
- **Upload:** In main form

### Gallery Images
- **Purpose:** Shown on detail page
- **Size:** 800x600px recommended
- **Upload:** In gallery section
- **Quantity:** Add as many as you want

### Best Practices
- Compress images before upload
- Use JPG for photos
- Use PNG for graphics
- Add descriptive alt text
- Keep file sizes small

---

## 🎨 Gallery Features

### Shopify Style
- **Main Image:** Large display
- **Thumbnails:** Below main
- **Hover:** Change main image
- **Click:** Zoom in
- **Responsive:** Works on mobile

### Interactions
- Hover thumbnail → changes main image
- Click main image → zooms in
- Scroll → view content
- Mobile → compact layout

---

## 📱 Viewing Projects

### On Portfolio List
- See featured image
- See title
- See short description
- Click "View Details"

### On Project Detail
- See featured image (main)
- See thumbnail gallery
- See project title
- See full description
- See all gallery images
- Click "View Project" link

---

## 🔧 Common Tasks

### Edit Project
1. Admin → Portfolio
2. Click **Edit**
3. Update content
4. Click **Update**

### Add Image to Gallery
1. Edit project
2. Scroll to gallery section
3. Click **Add Image**
4. Upload image
5. Add alt text
6. Click **Add Image**

### Delete Image
1. Edit project
2. Find image in gallery
3. Click **Delete**
4. Confirm

### Delete Project
1. Admin → Portfolio
2. Click **Delete**
3. Confirm
4. All images deleted automatically

---

## 📊 Content Examples

### Project Title
```
"E-Commerce Website Redesign"
"Mobile App Development"
"Brand Identity Design"
```

### Short Description
```
"Redesigned e-commerce platform for 40% increase in conversions"
"Built iOS and Android app with 50k+ downloads"
"Created complete brand identity including logo and guidelines"
```

### Full Description (Rich Text)
```
## Project Overview
This project involved redesigning the entire e-commerce platform...

## Challenges
- Performance optimization
- Mobile responsiveness
- User experience improvement

## Solutions
1. Implemented caching
2. Responsive design
3. A/B testing

## Results
- 40% increase in conversions
- 50% faster load time
- 95% user satisfaction

[Include images throughout]
```

---

## 🎯 URL Structure

### Portfolio List
```
/portfolio.php
/portfolio.php?category=Web%20Design
```

### Project Detail
```
/portfolio-detail.php?id=1
/portfolio-detail.php?id=2
```

---

## 🆘 Quick Troubleshooting

### Rich Text Not Saving
- Enable JavaScript
- Try different browser
- Clear cache

### Images Not Uploading
- Check file size (< 5MB)
- Check file format (JPG, PNG, GIF, WebP)
- Check /uploads folder permissions

### Gallery Not Showing
- Verify images uploaded
- Check image URLs
- Check browser console

### Detail Page Not Loading
- Check portfolio ID in URL
- Verify portfolio item exists
- Check database connection

---

## 📚 Full Documentation

For more details, see:
- **PORTFOLIO_DETAIL_GUIDE.md** - Complete guide
- **PORTFOLIO_ENHANCEMENTS.md** - All enhancements
- **PORTFOLIO_V3_SUMMARY.md** - Full summary

---

## ✅ Checklist

### Setup
- [ ] Run database queries
- [ ] Update files
- [ ] Add portfolio-detail.php
- [ ] Test admin panel

### First Project
- [ ] Create project
- [ ] Add rich text content
- [ ] Upload featured image
- [ ] Add gallery images
- [ ] View on frontend

### Customization
- [ ] Update colors in CSS
- [ ] Add your projects
- [ ] Test on mobile
- [ ] Deploy to production

---

## 🚀 You're Ready!

Your portfolio system is now ready to showcase your projects professionally!

**Next:** Create your first project and start impressing visitors! 🎉

---

**Happy showcasing!** ✨
