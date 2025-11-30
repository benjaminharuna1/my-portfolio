# Technology Icons Guide

## Overview

Add technology/programming language icons to your services. These icons appear below the service description and showcase the tools you use for each service.

## Features

✅ **Font Awesome Icons** - Access to 6000+ icons
✅ **Easy Management** - Add/edit from admin panel
✅ **Visual Display** - Icons appear on frontend
✅ **Hover Effects** - Interactive icon animations
✅ **Responsive** - Works on all devices

## How to Add Tech Icons

### Step 1: Go to Services Admin

1. Login to admin dashboard
2. Click **Services** in sidebar
3. Click **Edit** on a service or add new one

### Step 2: Add Technology Icons

Find the **"Technology Icons"** field:

```
Technology Icons (Comma Separated)
fab fa-php, fab fa-js, fab fa-react, fab fa-laravel
```

Enter Font Awesome icon classes separated by commas.

### Step 3: Save

Click **Update** or **Add** to save.

## Font Awesome Icon Classes

### Popular Programming Languages

| Language | Icon Class |
|----------|-----------|
| PHP | `fab fa-php` |
| JavaScript | `fab fa-js` |
| Python | `fab fa-python` |
| Java | `fab fa-java` |
| C++ | `fab fa-c` |
| Ruby | `fab fa-gem` |
| Go | `fab fa-golang` |

### Frameworks & Libraries

| Framework | Icon Class |
|-----------|-----------|
| React | `fab fa-react` |
| Vue | `fab fa-vuejs` |
| Angular | `fab fa-angular` |
| Laravel | `fab fa-laravel` |
| Django | `fab fa-python` |
| Node.js | `fab fa-node-js` |
| Express | `fab fa-node` |

### Tools & Platforms

| Tool | Icon Class |
|------|-----------|
| Git | `fab fa-git-alt` |
| GitHub | `fab fa-github` |
| GitLab | `fab fa-gitlab` |
| Docker | `fab fa-docker` |
| AWS | `fab fa-aws` |
| Google Cloud | `fab fa-google` |
| Figma | `fab fa-figma` |
| Adobe | `fab fa-adobe` |
| Sketch | `fab fa-sketch` |

### Databases

| Database | Icon Class |
|----------|-----------|
| MySQL | `fab fa-mysql` |
| MongoDB | `fab fa-mongodb` |
| PostgreSQL | `fab fa-postgresql` |
| Redis | `fab fa-redis` |

### Design Tools

| Tool | Icon Class |
|------|-----------|
| Figma | `fab fa-figma` |
| Adobe XD | `fab fa-adobe` |
| Sketch | `fab fa-sketch` |
| InVision | `fab fa-invision` |

## Example Configurations

### Web Design Service

```
fab fa-figma, fab fa-adobe, fab fa-sketch
```

### Web Development Service

```
fab fa-php, fab fa-laravel, fab fa-js, fab fa-react, fab fa-mysql
```

### Full Stack Development

```
fab fa-php, fab fa-laravel, fab fa-js, fab fa-react, fab fa-node-js, fab fa-docker, fab fa-git-alt
```

### Mobile Development

```
fab fa-react, fab fa-js, fab fa-swift, fab fa-java
```

### DevOps

```
fab fa-docker, fab fa-aws, fab fa-linux, fab fa-git-alt, fab fa-github
```

## Finding More Icons

Visit **Font Awesome** official website:
- https://fontawesome.com/icons

### Search Tips

1. Go to Font Awesome website
2. Search for technology name
3. Copy the icon class
4. Paste in admin panel

### Icon Prefixes

- `fab` - Brand icons (companies, platforms)
- `fas` - Solid icons (general purpose)
- `far` - Regular icons (outlined)
- `fal` - Light icons (thin)

**Most tech icons use `fab` prefix**

## Styling

### Icon Display

Icons appear in a row below service description:

```
[Icon] [Icon] [Icon] [Icon]
```

### Hover Effects

- Icons scale up on hover
- Color changes to primary color
- Smooth animation

### Responsive

- Desktop: Full size icons
- Tablet: Slightly smaller
- Mobile: Compact layout

## Admin Panel Display

In the admin panel, you'll see:

1. **Service Icon** - Main service icon (fa-palette, fa-code, etc.)
2. **Technology Icons** - Tech stack icons below description

## Frontend Display

On the website:

1. Service card with main icon
2. Service title
3. Service description
4. **Technology icons row** (your added icons)

## Tips & Best Practices

### 1. Keep It Concise

```
✅ Good: fab fa-php, fab fa-laravel, fab fa-mysql
❌ Too many: fab fa-php, fab fa-laravel, fab fa-mysql, fab fa-js, fab fa-react, fab fa-node-js, fab fa-docker, fab fa-git-alt
```

### 2. Logical Order

```
✅ Good: fab fa-php, fab fa-laravel, fab fa-mysql
(Language → Framework → Database)

❌ Random: fab fa-mysql, fab fa-php, fab fa-laravel
```

### 3. Relevant Icons

```
✅ Good: fab fa-react, fab fa-js, fab fa-node-js
(All related to service)

❌ Irrelevant: fab fa-react, fab fa-docker, fab fa-aws, fab fa-figma
(Mix of different categories)
```

### 4. Consistent Across Services

```
✅ Good: All services use 3-4 icons
❌ Inconsistent: One service has 2, another has 8
```

## Troubleshooting

### Icons Not Showing

**Check:**
1. Icon class is correct (e.g., `fab fa-php`)
2. Separated by commas
3. No extra spaces at start/end
4. Font Awesome CDN is loaded

### Wrong Icon Appears

1. Verify icon class name
2. Check Font Awesome website for correct name
3. Make sure prefix is correct (`fab`, `fas`, etc.)

### Icons Look Different

- Font Awesome updates icons occasionally
- Visit fontawesome.com for latest versions
- Use consistent version across site

## Advanced: Custom Icons

If Font Awesome doesn't have an icon:

1. Use similar icon
2. Or use emoji (not recommended)
3. Or upload custom SVG (requires code modification)

## Database

Tech icons stored in:
- Table: `services`
- Column: `tech_icons`
- Format: Comma-separated Font Awesome classes

## API Reference

### Display Tech Icons

```php
<?php if ($service['tech_icons']): ?>
    <div class="tech-icons">
        <?php
        $icons = array_map('trim', explode(',', $service['tech_icons']));
        foreach ($icons as $icon):
            if ($icon):
        ?>
            <i class="<?php echo htmlspecialchars($icon); ?> tech-icon"></i>
        <?php 
            endif;
        endforeach; 
        ?>
    </div>
<?php endif; ?>
```

## Support

For icon questions:
1. Visit https://fontawesome.com/icons
2. Search for technology
3. Copy icon class
4. Paste in admin panel

---

**Showcase your tech stack!** 🚀
