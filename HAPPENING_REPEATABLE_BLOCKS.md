# Happening Post Type - Repeatable Post Blocks

## Overview

The Happening post type now includes a comprehensive repeatable post blocks system that allows you to create multiple content blocks with individual headers, content, dates, times, and publishing controls.

## Features

### 1. Repeatable Block Toolbar
- **Add New Block**: Creates a new happening post block with default content
- **Save All Blocks**: Saves all blocks as drafts
- **Publish All**: Publishes all blocks at once

### 2. Individual Block Controls
Each block includes:
- **Header Field**: Custom header/title for the block
- **Content Field**: Rich text content area
- **Date Picker**: Select the date for the block
- **Time Picker**: Select the time for the block
- **Status Dropdown**: Choose between Draft and Published
- **Publish/Update Button**: Publishes or updates the individual block
- **Save Draft Button**: Saves the block as a draft
- **Remove Block Button**: Deletes the block (with confirmation)

### 3. Auto-Save Functionality
- Blocks automatically save as drafts when fields are changed
- 1-second debounce to prevent excessive saving
- Visual feedback for save status

### 4. Frontend Display
- Timeline-style display of all blocks
- Status indicators (Published/Draft)
- Responsive design for mobile devices
- Dark mode support

## How to Use

### Creating a New Happening Post

1. **Navigate to Happenings** in the WordPress admin
2. **Click "Add New"** to create a new happening post
3. **Add a title** for the main happening post
4. **Use the toolbar** at the top to manage blocks:
   - Click "Add New Block" to create your first block
   - Use "Save All Blocks" to save everything as drafts
   - Use "Publish All" to publish all blocks

### Managing Individual Blocks

For each block, you can:

1. **Set the Header**: Enter a descriptive title for the block
2. **Add Content**: Write the main content for this block
3. **Set Date & Time**: Choose when this block's event occurs
4. **Choose Status**: Set as Draft or Published
5. **Save Options**:
   - **Publish/Update**: Makes the block live immediately
   - **Save Draft**: Keeps it as a draft for later
   - **Remove Block**: Deletes the block (with confirmation)

### Frontend Display

On the frontend, blocks are displayed in a timeline format:
- Each block shows its header, content, date, time, and status
- Published blocks are highlighted with a green status badge
- Draft blocks show a gray status badge
- Blocks are ordered by their creation/editing time

## Technical Details

### Files Modified/Created

1. **JavaScript**: `wp-content/themes/byline/js/happening-block-editor.js`
   - Handles all block interactions
   - Manages AJAX calls for saving
   - Provides user interface controls

2. **CSS**: `wp-content/themes/byline/css/happening-block-editor.css`
   - Styles for the admin interface
   - Toolbar and form styling
   - Responsive design

3. **PHP Functions**: `wp-content/themes/byline/functions.php`
   - AJAX handlers for saving blocks
   - Frontend display functions
   - Data retrieval functions

4. **Frontend CSS**: `wp-content/themes/byline/style.css`
   - Timeline display styling
   - Status indicators
   - Responsive and dark mode support

### Database Storage

Blocks are stored as post meta with two keys:
- `_happening_block_meta`: Individual block data (backward compatibility)
- `_happening_blocks_data`: Complete blocks array (new format)

### AJAX Endpoints

- `save_happening_block_meta`: Saves individual block data
- `save_happening_blocks_data`: Saves all blocks data

## Customization

### Styling Customization

You can customize the appearance by modifying:
- Admin styles in `happening-block-editor.css`
- Frontend styles in `style.css`

### Function Customization

To add custom fields or modify behavior:
1. Edit the JavaScript file to add new form fields
2. Update the PHP functions to handle new data
3. Modify the frontend display function to show new fields

## Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile responsive design
- Touch-friendly interface on mobile devices

## Troubleshooting

### Common Issues

1. **Blocks not saving**: Check browser console for JavaScript errors
2. **Styling issues**: Ensure CSS files are loading properly
3. **AJAX errors**: Verify nonce and permissions

### Debug Mode

Enable WordPress debug mode to see detailed error messages:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Future Enhancements

Potential improvements for future versions:
- Drag and drop reordering of blocks
- Block templates for quick creation
- Advanced scheduling options
- Social media integration
- Block categories and filtering
- Export/import functionality 