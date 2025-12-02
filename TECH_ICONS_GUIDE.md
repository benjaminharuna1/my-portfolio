# Technology Icons Guide

## Overview

Technology icons are displayed in the services section to show the technologies used. They now support both Font Awesome and custom SVG icons with consistent sizing and styling.

## How to Add Tech Icons to a Service

### Step 1: Go to Services Admin

1. Go to `/admin/services.php`
2. Click "Edit" on a service or "Add New Service"

### Step 2: Add Technology Icons

In the "Technology Icons" field, enter icon names separated by commas:

```
fa-php, fab fa-js, fab fa-react, fab fa-laravel
```

Or mix custom and Font Awesome:

```
ci-photoshop, fa-palette, fab fa-figma
```

### Step 3: Save Service

Click "Update Service" or "Add Service"

## Icon Examples

### Font Awesome Icons

```
fa-php              Font Awesome PHP
fab fa-js           Font Awesome JavaScript
fab fa-react        Font Awesome React
fab fa-laravel      Font Awesome Laravel
fab fa-vue          Font Awesome Vue
fab fa-angular      Font Awesome Angular
fab fa-node         Font Awesome Node.js
fab fa-python       Font Awesome Python
fab fa-docker       Font Awesome Docker
fab fa-git          Font Awesome Git
fab fa-github       Font Awesome GitHub
fab fa-gitlab       Font Awesome GitLab
fab fa-wordpress    Font Awesome WordPress
fab fa-drupal       Font Awesome Drupal
fab fa-figma        Font Awesome Figma
fab fa-sketch       Font Awesome Sketch
fab fa-adobe        Font Awesome Adobe
```

### Custom SVG Icons

```
ci-photoshop        Custom Photoshop icon
ci-figma            Custom Figma icon
ci-sketch           Custom Sketch icon
ci-xd               Custom Adobe XD icon
```

## Icon Styling

### Default Styling

- **Size:** 1.3rem (responsive, 1.1rem on mobile)
- **Color:** Secondary color (gray)
- **Hover:** Primary color with scale effect
- **Transition:** Smooth 0.3s animation

### CSS Classes

The tech icons automatically get these classes:

```css
.tech-icons {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

.tech-icon {
    font-size: 1.3rem;
    color: var(--secondary-color);
    transition: all 0.3s ease;
}

.tech-icon:hover {
    color: var(--primary-color);
    transform: scale(1.2) translateY(-3px);
}

.tech-icon-svg {
    width: 1.3em;
    height: 1.3em;
    fill: var(--secondary-color);
}
```

## How It Works

### Icon Resolution

When you enter a tech icon name:

1. **Check if it's a custom icon** (starts with `ci-`)
   - Look for SVG in database
   - If found, display inline SVG
   - If not found, display nothing

2. **Check if it's Font Awesome** (starts with `fa-` or `fab `)
   - Display Font Awesome icon
   - Automatic prefix handling

3. **Styling**
   - Apply consistent sizing
   - Apply color from CSS
   - Support hover effects

### Example Flow

```
Input: "fa-php, fab fa-js, ci-photoshop"
↓
Split by comma: ["fa-php", "fab fa-js", "ci-photoshop"]
↓
For each icon:
  - fa-php → Display Font Awesome PHP icon
  - fab fa-js → Display Font Awesome JavaScript icon
  - ci-photoshop → Display custom Photoshop SVG
↓
All icons displayed with consistent styling
```

## Complete Example

### Service with Tech Icons

```
Title: Web Development
Description: Building robust and scalable web applications
Icon: fa-code
Tech Icons: fab fa-php, fab fa-js, fab fa-react, fab fa-laravel
```

### Result

- Service icon: Font Awesome code icon (large, 3rem)
- Tech icons: PHP, JavaScript, React, Laravel (1.3rem each)
- All icons styled consistently
- Hover effects on all icons

## Responsive Behavior

### Desktop (> 768px)

- Tech icon size: 1.3rem
- Gap between icons: 12px
- Hover scale: 1.2x

### Mobile (< 768px)

- Tech icon size: 1.1rem
- Gap between icons: 8px
- Hover scale: 1.2x

## Troubleshooting

### Icons Not Showing

**Problem:** Tech icons entered but not displaying

**Solutions:**
1. Check icon names are correct
2. Verify Font Awesome icon exists
3. For custom icons, check `ci-` prefix
4. Check for typos in icon names

### Wrong Icon Displaying

**Problem:** Icon showing but it's the wrong one

**Solutions:**
1. Verify icon name matches exactly
2. Check if custom icon exists in `/admin/icons.php`
3. Try Font Awesome icon instead
4. Check icon name spelling

### Icons Not Styled Correctly

**Problem:** Icons displaying but styling is off

**Solutions:**
1. Check CSS is loaded
2. Verify color variables are set
3. Check browser console for errors
4. Clear browser cache

## Best Practices

1. **Use consistent icon types** - Mix Font Awesome and custom icons
2. **Keep icon count reasonable** - 3-6 icons per service
3. **Use recognizable icons** - Choose icons that represent the technology
4. **Test on mobile** - Ensure icons look good on all devices
5. **Provide meaningful icons** - Use icons that users recognize

## Icon Naming Convention

### Font Awesome

- `fa-*` - Solid Font Awesome icons
- `fab fa-*` - Brand Font Awesome icons
- `far fa-*` - Regular Font Awesome icons
- `fas fa-*` - Solid Font Awesome icons (explicit)

### Custom SVG

- `ci-*` - Custom SVG icons

## Examples

### Web Development Service

```
fa-code, fab fa-php, fab fa-js, fab fa-react, fab fa-laravel
```

### Design Service

```
fa-palette, ci-photoshop, ci-figma, fab fa-sketch
```

### Mobile Development Service

```
fa-mobile-alt, fab fa-swift, fab fa-android, fab fa-react
```

### DevOps Service

```
fa-server, fab fa-docker, fab fa-git, fab fa-linux
```

## Summary

| Feature | Details |
|---------|---------|
| Size | 1.3rem (desktop), 1.1rem (mobile) |
| Color | Secondary color, primary on hover |
| Animation | Smooth 0.3s transition |
| Hover Effect | Scale 1.2x, move up 3px |
| Icon Types | Font Awesome + Custom SVG |
| Spacing | 12px gap (8px mobile) |
| Responsive | Yes, mobile optimized |

The tech icons now display beautifully with consistent styling across all services! 🎨
