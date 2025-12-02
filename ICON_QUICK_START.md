# Icon System - Quick Start

## 30-Second Overview

```php
<?php
// Custom icon
echo icon('ci-photoshop');

// Font Awesome icon
echo icon('fa-palette');

// With styling
echo icon('ci-photoshop', 'w-8 h-8 text-primary');

// With fallback
echo icon('ci-photoshop', '', 'fa-image');
?>
```

## Icon Prefixes

| Prefix | Type | Example |
|--------|------|---------|
| `ci-` | Custom SVG | `ci-photoshop` |
| `fa-` | Font Awesome | `fa-palette` |
| `fab ` | Font Awesome Brand | `fab fa-php` |

## Upload Custom Icon

1. Go to `/admin/icons.php`
2. Click "Add New Icon"
3. Upload SVG
4. Click "Add Icon"

## Use in Services

1. Go to `/admin/services.php`
2. Enter: `ci-photoshop` or `fa-palette`
3. Save

## Use in Code

```php
<?php
echo icon('ci-photoshop');                    // Simple
echo icon('ci-photoshop', 'w-8 h-8');        // With class
echo icon('fa-palette', 'text-primary');     // Font Awesome
echo icon('ci-photoshop', '', 'fa-image');   // With fallback
?>
```

## Popular Font Awesome Icons

```
fa-palette       fa-code          fa-pencil-ruler
fa-mobile-alt    fa-laptop        fa-database
fa-server        fa-cloud         fa-shield-alt
fa-lock          fa-cog           fa-tools
fa-star          fa-heart         fa-check
fab fa-php       fab fa-js        fab fa-react
```

## CSS Styling

```css
svg {
    width: 1em;
    height: 1em;
    vertical-align: middle;
}

.w-8 { width: 32px; }
.text-primary { fill: #007bff; }
```

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Icon not showing | Check prefix (`ci-` or `fa-`) |
| Wrong icon | Verify icon name matches |
| Custom icon missing | Upload to `/admin/icons.php` |
| Styling not working | Add CSS classes |

## Functions

```php
icon($name, $class = '', $fallback = '')
fontAwesomeIcon($name, $class = '')
getCustomIconSVG($name, $class = '')
getAllCustomIcons($category = null)
getIconCategories()
customIconExists($name)
```

## Full Documentation

- **ICON_SYSTEM_SIMPLIFIED.md** - Complete guide
- **ICON_PREFIX_GUIDE.md** - Prefix details
- **USING_CUSTOM_ICONS.md** - Custom icons
- **IMPLEMENTATION_COMPLETE.md** - Full reference

---

That's it! You're ready to use the icon system. 🎉
