# LiveBlog Custom Block Editor

## Overview

The LiveBlog Custom Block Editor is a specialized editing interface designed specifically for the LiveBlog post type. It replaces the standard Gutenberg editor with a custom block-based editor that provides a more intuitive and efficient experience for creating live blog content.

## Features

### Block Types

The custom editor supports five different block types, each optimized for specific content needs:

1. **Text Block** 📝
   - Rich text editing with contenteditable interface
   - Supports basic formatting (bold, italic, links)
   - Auto-saves content as you type
   - Perfect for narrative content and updates

2. **Image Block** 🖼️
   - Drag and drop image upload
   - Click to upload functionality
   - Automatic image optimization
   - Caption support
   - Visual preview of uploaded images

3. **Video Block** 🎥
   - Support for YouTube and Vimeo embeds
   - Automatic video embedding from URLs
   - Caption support
   - Responsive video containers

4. **Quote Block** 💬
   - Styled quote display
   - Attribution field
   - Professional typography
   - Perfect for highlighting important statements

5. **Social Media Block** 📱
   - Integration with social media embedder
   - Support for Facebook, Twitter, Instagram, LinkedIn, Reddit
   - Automatic embedding from URLs
   - Real-time social content display

### Key Features

- **Auto-save**: Content is automatically saved every 2 seconds after changes
- **Drag & Drop**: Images can be uploaded by dragging and dropping
- **Social Media Integration**: Seamless integration with the social media embedder
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Real-time Notifications**: Visual feedback for all actions
- **Block Management**: Easy addition and removal of blocks
- **Content Persistence**: All content is saved and can be restored

## Technical Implementation

### Files Structure

```
wp-content/themes/byline/
├── functions.php (modified)
├── assets/js/liveblog-custom-editor.js
├── assets/css/liveblog-custom-editor.css
└── LIVEBLOG_CUSTOM_EDITOR.md
```

### Core Components

#### 1. PHP Functions (functions.php)

- `disable_gutenberg_for_liveblog()`: Disables Gutenberg for LiveBlog post type
- `enqueue_liveblog_custom_editor_scripts()`: Enqueues custom editor assets
- `upload_liveblog_image_ajax()`: Handles image uploads
- `auto_save_liveblog_blocks_ajax()`: Handles auto-save functionality
- `save_liveblog_custom_blocks()`: Saves custom blocks on post save
- `save_liveblog_content_ajax()`: AJAX handler for saving content
- `renderCustomBlock()`: Renders individual blocks from saved data

#### 2. JavaScript (liveblog-custom-editor.js)

- Block type definitions and templates
- Event handling for all user interactions
- AJAX communication with WordPress
- Auto-save functionality
- Image upload handling
- Video and social media embedding
- Notification system

#### 3. CSS (liveblog-custom-editor.css)

- Modern, responsive design
- Block-specific styling
- Hover and focus states
- Loading animations
- Mobile-friendly layout

## Usage

### For Content Creators

1. **Creating a New LiveBlog**:
   - Create a new LiveBlog post
   - The custom editor will automatically load
   - Start with a text block or choose from the toolbar

2. **Adding Blocks**:
   - Use the toolbar buttons to add different block types
   - Or use the "+" buttons within existing blocks
   - Blocks can be added in any order

3. **Editing Content**:
   - Click on any content area to edit
   - Images can be uploaded by clicking or dragging
   - Videos and social media posts are embedded automatically

4. **Saving Content**:
   - Content auto-saves every 2 seconds
   - Use "Save LiveBlog Content" for manual saves
   - Use "Load Saved Content" to restore previous versions

### For Developers

#### Adding New Block Types

1. **Define the block in JavaScript**:
```javascript
const blockTypes = {
    newblock: {
        name: 'New Block',
        icon: '🔧',
        template: '<div class="liveblog-block new-block" data-block-type="newblock">...</div>'
    }
};
```

2. **Add CSS styling**:
```css
.new-block {
    /* Your custom styles */
}
```

3. **Update the renderCustomBlock function**:
```php
case 'newblock':
    // Render your custom block
    break;
```

#### Customizing Block Behavior

- Modify event handlers in `bindEditorEvents()`
- Add new AJAX actions for custom functionality
- Extend the auto-save data structure

## Integration with Social Media Embedder

The custom editor seamlessly integrates with the existing social media embedder:

- Social media URLs are automatically detected and embedded
- All supported platforms (Facebook, Twitter, YouTube, TikTok, Instagram, LinkedIn, Reddit) work
- Embed codes are saved and restored with the content
- Real-time embedding preview

## Browser Support

- **Modern Browsers**: Chrome, Firefox, Safari, Edge (latest versions)
- **Mobile Browsers**: iOS Safari, Chrome Mobile, Samsung Internet
- **Features**: Contenteditable, File API, Drag & Drop, AJAX

## Performance Considerations

- **Auto-save**: Debounced to prevent excessive server requests
- **Image Optimization**: Automatic resizing and compression
- **Lazy Loading**: Social media embeds load on demand
- **Memory Management**: Proper cleanup of event listeners

## Security Features

- **Nonce Verification**: All AJAX requests are protected
- **File Upload Security**: Image uploads are validated and sanitized
- **Content Sanitization**: All content is properly escaped
- **Permission Checks**: User capabilities are verified

## Troubleshooting

### Common Issues

1. **Images not uploading**:
   - Check file permissions on uploads directory
   - Verify AJAX nonce is valid
   - Check browser console for errors

2. **Social media embeds not working**:
   - Ensure social media embedder is loaded
   - Check if social media platform scripts are available
   - Verify URL format is correct

3. **Auto-save not working**:
   - Check browser console for JavaScript errors
   - Verify AJAX endpoint is accessible
   - Check user permissions

### Debug Mode

Enable debug mode by adding to wp-config.php:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Future Enhancements

- **Block Templates**: Pre-defined block layouts
- **Collaborative Editing**: Real-time multi-user editing
- **Advanced Formatting**: Rich text formatting toolbar
- **Block Reordering**: Drag and drop block reordering
- **Version History**: Detailed content version tracking
- **Export Options**: Export to various formats
- **Analytics Integration**: Content performance tracking

## Changelog

### Version 1.0.0
- Initial release
- Five block types (text, image, video, quote, social)
- Auto-save functionality
- Social media integration
- Responsive design
- AJAX-based operations

## Support

For technical support or feature requests, please refer to the theme documentation or contact the development team. 