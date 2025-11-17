# LiveBlog Features

## Overview
This document describes the enhanced LiveBlog functionality implemented for the Byline WordPress theme.

## Features

### 1. Independent Publishing System
- **Individual Block Management**: Each LiveBlog block can be independently published, drafted, or updated
- **Real-time Updates**: AJAX-powered publishing without page reload
- **WYSIWYG Editor**: Rich text editing for block content using TinyMCE
- **Media Selection**: Select and remove media directly from block header
- **Timestamp Management**: Set timestamps directly from block header (no label, aligned with buttons)
- **Auto-save**: Blocks auto-save after 2 seconds of inactivity
- **Status Persistence**: Button text and border indicators persist on page reload
- **Newest First**: New blocks appear above existing ones in both admin and frontend
- **Immediate Database Save**: New blocks are saved to database immediately for instant publishing

### 2. Dynamic Timestamp-Based Ordering
- **Timestamp-Based Sorting**: All blocks are ordered by timestamp (most recent first) in both admin and frontend
- **Dynamic Reordering**: When timestamp is changed, blocks automatically reorder in real-time
- **Default Timestamps**: New blocks automatically get current timestamp as default
- **Consistent Ordering**: Same timestamp-based order maintained across admin, frontend, and sidebar
- **Published Time Default**: Default timestamp reflects the published/last updated time for each block

### 3. Visual Status Indicators
- **Color-coded Borders**: 
  - Green border for published blocks
  - Orange border for draft blocks
  - Red border for auto-draft blocks
- **Status Messages**: Temporary success/error messages for user feedback
- **Dynamic Button Text**: Buttons change text based on current status

### 4. Block Management
- **Add New Blocks**: "Add New Update" button positioned above all blocks
- **Delete Blocks**: Individual delete buttons for each block
- **Media Management**: Integrated media selection with WordPress media library
- **Timestamp Control**: Precise timestamp setting for each update

### 5. Frontend Display
- Only **published** blocks appear on the frontend
- Draft and auto-draft blocks remain hidden
- Newest blocks appear first (reverse chronological order)
- Blocks are sorted by timestamp with newest at the top
- Sidebar "പ്രധാന സംഭവങ്ങൾ" maintains same timestamp-based order

### 6. Auto-Draft Cleanup
- Auto-draft blocks older than 7 days are automatically cleaned up
- Scheduled cleanup via WordPress cron

### 7. Error Handling
- Comprehensive error logging for debugging
- User-friendly error messages
- Graceful fallbacks for failed operations

### 8. Reliability Optimizations
- **Enhanced Content Restoration**: Multiple restoration methods with increased attempts (10 max) for better reliability
- **Multiple Content Verification**: Three verification checks during initialization (1.5s, 3s, 5s) to catch late-loading issues
- **Continuous Monitoring**: Background monitoring enabled by default (every 15 seconds) for persistent reliability
- **Automatic Re-initialization**: Automatic detection and re-initialization of problematic editors
- **Fallback Support**: Fallback to contentEditable for failed TinyMCE instances
- **Recursive Verification**: Automatic re-checking when issues are detected

## Usage

### Adding New Blocks
1. Click "Add New Update" button (positioned above all blocks)
2. New block starts as "Draft" and appears at the top of the list
3. Fill in title, content (using WYSIWYG editor), media, and timestamp
4. Use "Publish" or "Save as Draft" buttons
5. Block is automatically saved to database for immediate publishing

### Publishing Blocks
1. **Quick Publish**: Click "Publish" button for immediate publication (changes to "Update" after publishing)
2. **Save as Draft**: Click "Save as Draft" button to save as draft (changes to "Drafted" after saving)
3. **Media Management**: Use "Select Media" and "Remove Media" buttons in block header
4. **Timestamp Setting**: Use the datetime-local input field in block header (no label)
5. **Auto-save**: Changes are automatically saved

### Managing Existing Blocks
- Edit content using the WYSIWYG editor
- Manage media using Select/Remove Media buttons in block header
- Set timestamps using the datetime-local input field in block header
- Change status using Publish/Draft buttons
- Delete blocks using the Delete Update button

### Timestamp Management
- **Default Timestamps**: New blocks automatically get current timestamp
- **Dynamic Reordering**: Change timestamp to move block to appropriate position
- **Real-time Updates**: Block order updates immediately when timestamp changes
- **Consistent Display**: Same order maintained across all views

### Frontend Display
- Published blocks appear in reverse chronological order
- Each block shows title, content, media, and timestamp
- Responsive design for mobile and desktop
- Sidebar maintains same timestamp-based order

## Technical Implementation

### Database Structure
- Blocks stored as post meta `_liveblog_additional_blocks`
- Each block contains: title, content, media_id, media_url, timestamp, status, created_date, last_modified, published_date

### AJAX Endpoints
- `publish_liveblog_block`: Publish individual blocks
- `draft_liveblog_block`: Save blocks as draft
- `update_liveblog_block`: Update block content and timestamp
- `add_liveblog_block`: Add new blocks to database

### Security
- Nonce verification for all AJAX requests
- User capability checks
- Input sanitization and validation

### Error Handling
- Comprehensive logging for debugging
- Graceful error recovery
- User-friendly error messages

### Reliability Optimizations
- **Enhanced Content Restoration**: Multiple restoration methods with increased attempts (10 max) for better reliability
- **Multiple Content Verification**: Three verification checks during initialization (1.5s, 3s, 5s) to catch late-loading issues
- **Continuous Monitoring**: Background monitoring enabled by default (every 15 seconds) for persistent reliability
- **Automatic Re-initialization**: Automatic detection and re-initialization of problematic editors
- **Fallback Support**: Fallback to contentEditable for failed TinyMCE instances
- **Recursive Verification**: Automatic re-checking when issues are detected
- **Debug Controls**: Debug features can be controlled via browser console:
  - `window.disableLiveBlogMonitoring = true;` - Disable continuous monitoring
  - `window.showLiveBlogDebug = true;` - Show debug information
  - `startContentMonitoring();` - Start monitoring manually

## Recent Fixes

### Dynamic Timestamp-Based Ordering (New Feature)
- **Feature**: Implemented timestamp-based ordering system for LiveBlog blocks
- **Implementation**:
  - **Admin Interface**: Blocks sorted by timestamp (newest first) in admin meta box
  - **Frontend**: Published blocks ordered by timestamp in main content area
  - **Sidebar**: "പ്രധാന സംഭവങ്ങൾ" sidebar maintains same timestamp-based order
  - **Dynamic Reordering**: JavaScript automatically reorders blocks when timestamp changes
  - **Default Timestamps**: New blocks get current timestamp as default
  - **Real-time Updates**: Block order updates immediately in admin interface
- **Technical Details**:
  - Added `usort()` functions to sort blocks by timestamp in PHP
  - Implemented JavaScript `reorderBlocksByTimestamp()` function
  - Added timestamp change event handlers
  - Updated `setDefaultTimestamp()` function for new blocks
  - Enhanced `updateBlockTimestamp()` for real-time database updates

### Missing Content Field Issue (Fixed)
- **Problem**: Content field was missing for older LiveBlog blocks, making them appear empty in the admin interface
- **Root Cause**: During recent updates, the content field was lost from some blocks in the database
- **Solution**: Created a recovery script to add default content to blocks with empty content fields
- **Implementation**:
  - Identified blocks with empty content using database query
  - Added default content: `<p>This is the content for [title]. Please edit this content to add your update.</p>`
  - Updated 4 out of 5 blocks that had missing content
  - Preserved the original content of the first block that already had content
  - Updated `last_modified` timestamp for affected blocks
- **Result**: All blocks now have visible and editable content fields in the admin interface

### Error Publishing Block Issue (Fixed)
- **Problem**: New blocks couldn't be published immediately because they weren't saved to database
- **Solution**: Added immediate database save when new blocks are created
- **Implementation**: 
  - New `saveNewBlockToDatabase()` JavaScript function
  - New `add_liveblog_block_ajax()` PHP handler
  - Blocks are now saved to database immediately upon creation
  - This allows instant publishing of newly created blocks

### Frontend Title Display Issue (Fixed)
- **Problem**: Frontend was not displaying titles for blocks with empty title fields
- **Solution**: Added fallback title display and validation to prevent empty titles
- **Implementation**:
  - Frontend template now shows "Update #X" for blocks with empty titles
  - Added client-side validation to prevent publishing blocks without titles
  - New blocks automatically get default titles ("Update #X") when created
  - Fixed existing blocks in database that had empty titles

### Frontend Updates Not Showing Issue (Fixed)
- **Problem**: Updates were not showing on the frontend due to a PHP error in the sorting function
- **Solution**: Fixed the `usort` closure in `get_published_liveblog_blocks()` function
- **Implementation**:
  - Added `use ($blocks)` to the closure to make the `$blocks` variable accessible
  - This was causing a "Undefined variable $blocks" error and preventing the function from returning results
  - The error was in the fallback sorting logic when timestamps were equal

### Block Status Preservation Issue (Fixed)
- **Problem**: When updating the original LiveBlog post, all update blocks were reverting to draft mode
- **Root Cause**: The `save_liveblog_additional_blocks()` function was not preserving the existing status when the main post was saved
- **Solution**: 
  - Added hidden status input fields to the meta box template for each block
  - Modified the save function to preserve existing status: `'status' => sanitize_text_field($block_data['status'] ?? $existing_block['status'] ?? 'draft')`
  - Updated JavaScript functions to maintain the hidden status field when publish/draft buttons are clicked
  - Added status field to new blocks created via "Add New Update" button
- **Implementation**:
  - **Meta Box Template**: Added `<input type="hidden" id="block_status_${index}" name="liveblog_blocks[${index}][status]" value="${block_status}" />` to preserve status in form data
  - **Save Function**: Modified to check for existing status before defaulting to 'draft'
  - **JavaScript**: Updated `publishLiveBlogBlock()` and `draftLiveBlogBlock()` to update hidden status fields
  - **New Blocks**: Added status field to `addLiveBlogBlock()` function for newly created blocks

### Performance Optimization and Code Cleanup (Latest Update)
- **Performance Improvements**:
  - **Cached DOM Selectors**: Implemented `getContainer()` and `getEditors()` functions to reduce repeated DOM queries
  - **Reduced Content Restoration Attempts**: Decreased from 15 to 5 attempts for faster loading
  - **Minimized Debug Logging**: Removed excessive console logs and error_log statements for production use
  - **Optional Continuous Monitoring**: Made background monitoring optional and disabled by default
  - **Single Content Verification**: Replaced multiple verification calls with single optimized check after initialization
  - **Streamlined Timeout Delays**: Reduced and optimized setTimeout delays for faster initialization
- **Code Cleanup**:
  - **Removed Debug Logs**: Cleaned up excessive console.log and error_log statements
  - **Optimized Functions**: Streamlined content restoration and verification functions
  - **Better Documentation**: Added comprehensive comments explaining performance optimizations
  - **Optional Debug Features**: Debug features can be enabled via browser console when needed
- **Maintained Functionality**: All existing features remain fully functional while improving performance

### Reliability Optimization and Permanent Fix (Latest Update)
- **Reliability Improvements**:
  - **Enhanced Content Restoration**: Multiple restoration methods with increased attempts (10 max) for better reliability
  - **Multiple Content Verification**: Three verification checks during initialization (1.5s, 3s, 5s) to catch late-loading issues
  - **Continuous Monitoring**: Background monitoring enabled by default (every 15 seconds) for persistent reliability
  - **Automatic Re-initialization**: Automatic detection and re-initialization of problematic editors
  - **Fallback Support**: Fallback to contentEditable for failed TinyMCE instances
  - **Recursive Verification**: Automatic re-checking when issues are detected
- **Permanent Fix for Re-initialization Issues**:
  - **Problem**: Re-initialization was not happening automatically for older blocks, requiring manual "Re-initialize All Editors" button
  - **Solution**: Implemented comprehensive automatic detection and fixing system
  - **Implementation**:
    - Enhanced `verifyAndRestoreContent()` to detect both missing content and missing editor instances
    - Improved `forceContentRestoration()` with multiple content-setting methods
    - Added continuous monitoring that runs every 15 seconds by default
    - Implemented automatic re-initialization of problematic editors
    - Added fallback to contentEditable for failed TinyMCE instances
    - Enhanced `initializeWysiwygEditors()` with better error handling and retry logic
- **Debug Controls**: Debug features can be controlled via browser console:
  - `window.disableLiveBlogMonitoring = true;` - Disable continuous monitoring
  - `window.showLiveBlogDebug = true;` - Show debug information
  - `startContentMonitoring();` - Start monitoring manually 