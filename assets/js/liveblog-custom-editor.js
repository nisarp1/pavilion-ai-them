/**
 * LiveBlog Custom Block Editor
 * A specialized editor for LiveBlog post type with custom block types
 */

(function ($) {
    'use strict';

    // Block types available in the custom editor
    const blockTypes = {
        text: {
            template: '<div class="liveblog-block text-block" data-block-type="text" data-timestamp=""><div class="block-title-column"><div class="title-content" contenteditable="true" placeholder="Enter title..."></div><div class="title-timestamp"><span class="block-timestamp-display" contenteditable="true" placeholder="Click to edit time"></span></div></div><div class="block-content" contenteditable="true" placeholder="Enter your text here..."></div><div class="block-actions"><button class="add-block-btn" data-type="text">+ Add Text</button><button class="add-block-btn" data-type="image">+ Add Image</button><button class="add-block-btn" data-type="video">+ Add Video</button><button class="add-block-btn" data-type="quote">+ Add Quote</button><button class="add-block-btn" data-type="social">+ Add Social</button><button class="publish-block-btn">Publish</button><button class="draft-block-btn">Save Draft</button><button class="remove-block-btn">Remove</button></div></div>'
        },
        image: {
            name: 'Image Block',
            icon: '🖼️',
            template: '<div class="liveblog-block image-block" data-block-type="image" data-timestamp=""><div class="block-header"><span class="block-icon">🖼️</span><span class="block-title">Image Block</span></div><div class="block-timestamp-container"><label>Timestamp:</label><span class="block-timestamp-display" contenteditable="true" placeholder="Click to edit time"></span></div><div class="image-upload-area"><div class="upload-placeholder">Click to upload image or drag and drop</div><input type="file" class="image-upload-input" accept="image/*" style="display: none;"></div><div class="image-caption" contenteditable="true" placeholder="Enter image caption..."></div><div class="block-actions"><button class="add-block-btn" data-type="text">+ Add Text</button><button class="add-block-btn" data-type="image">+ Add Image</button><button class="add-block-btn" data-type="video">+ Add Video</button><button class="add-block-btn" data-type="quote">+ Add Quote</button><button class="add-block-btn" data-type="social">+ Add Social</button><button class="publish-block-btn">Publish</button><button class="draft-block-btn">Save Draft</button><button class="remove-block-btn">Remove</button></div></div>'
        },
        video: {
            name: 'Video Block',
            icon: '🎥',
            template: '<div class="liveblog-block video-block" data-block-type="video" data-timestamp=""><div class="block-header"><span class="block-icon">🎥</span><span class="block-title">Video Block</span></div><div class="block-timestamp-container"><label>Timestamp:</label><span class="block-timestamp-display" contenteditable="true" placeholder="Click to edit time"></span></div><div class="video-input-area"><input type="url" class="video-url" placeholder="Enter video URL..."><button class="embed-video-btn">Embed</button></div><div class="video-embed-area"></div><div class="video-caption" contenteditable="true" placeholder="Enter video caption..."></div><div class="block-actions"><button class="add-block-btn" data-type="text">+ Add Text</button><button class="add-block-btn" data-type="image">+ Add Image</button><button class="add-block-btn" data-type="video">+ Add Video</button><button class="add-block-btn" data-type="quote">+ Add Quote</button><button class="add-block-btn" data-type="social">+ Add Social</button><button class="publish-block-btn">Publish</button><button class="draft-block-btn">Save Draft</button><button class="remove-block-btn">Remove</button></div></div>'
        },
        quote: {
            name: 'Quote Block',
            icon: '💬',
            template: '<div class="liveblog-block quote-block" data-block-type="quote" data-timestamp=""><div class="block-header"><span class="block-icon">💬</span><span class="block-title">Quote Block</span></div><div class="block-timestamp-container"><label>Timestamp:</label><span class="block-timestamp-display" contenteditable="true" placeholder="Click to edit time"></span></div><div class="quote-content" contenteditable="true" placeholder="Enter quote text..."></div><div class="quote-attribution" contenteditable="true" placeholder="Enter attribution..."></div><div class="block-actions"><button class="add-block-btn" data-type="text">+ Add Text</button><button class="add-block-btn" data-type="image">+ Add Image</button><button class="add-block-btn" data-type="video">+ Add Video</button><button class="add-block-btn" data-type="quote">+ Add Quote</button><button class="add-block-btn" data-type="social">+ Add Social</button><button class="publish-block-btn">Publish</button><button class="draft-block-btn">Save Draft</button><button class="remove-block-btn">Remove</button></div></div>'
        },
        social: {
            name: 'Social Media Block',
            icon: '📱',
            template: '<div class="liveblog-block social-block" data-block-type="social" data-timestamp=""><div class="block-header"><span class="block-icon">📱</span><span class="block-title">Social Block</span></div><div class="block-timestamp-container"><label>Timestamp:</label><span class="block-timestamp-display" contenteditable="true" placeholder="Click to edit time"></span></div><div class="social-input-area"><input type="url" class="social-url" placeholder="Enter social media URL..."><button class="embed-social-btn">Embed</button></div><div class="social-embed-area"></div><div class="block-actions"><button class="add-block-btn" data-type="text">+ Add Text</button><button class="add-block-btn" data-type="image">+ Add Image</button><button class="add-block-btn" data-type="video">+ Add Video</button><button class="add-block-btn" data-type="quote">+ Add Quote</button><button class="add-block-btn" data-type="social">+ Add Social</button><button class="publish-block-btn">Publish</button><button class="draft-block-btn">Save Draft</button><button class="remove-block-btn">Remove</button></div></div>'
        }
    };

    // Get current UAE time
    function getUAETime() {
        const now = new Date();
        const uaeTime = new Date(now.toLocaleString("en-US", { timeZone: "Asia/Dubai" }));
        return uaeTime.toLocaleString('en-US', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
    }

    // Format timestamp for display
    function formatTimestamp(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
    }

    // Update block timestamp
    function updateBlockTimestamp(block) {
        const timestamp = getUAETime();
        block.attr('data-timestamp', timestamp);
        block.find('.block-timestamp-display').text(formatTimestamp(timestamp));
    }

    // Convert datetime-local input to timestamp format
    function datetimeLocalToTimestamp(datetimeLocal) {
        if (!datetimeLocal) return '';
        const date = new Date(datetimeLocal);
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
    }

    // Convert timestamp to datetime-local format
    function timestampToDatetimeLocal(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        return date.toISOString().slice(0, 16);
    }

    // Parse display text back to timestamp
    function parseDisplayTextToTimestamp(displayText) {
        // Try to parse various timestamp formats
        const patterns = [
            // "8:30 PM, December 15, 2024"
            /(\d{1,2}):(\d{2})\s*(AM|PM),\s*(\w+)\s+(\d{1,2}),\s*(\d{4})/i,
            // "15 Dec 2024, 8:30 PM"
            /(\d{1,2})\s+(\w+)\s+(\d{4}),\s*(\d{1,2}):(\d{2})\s*(AM|PM)/i,
            // "2024-12-15 20:30"
            /(\d{4})-(\d{1,2})-(\d{1,2})\s+(\d{1,2}):(\d{2})/,
            // "15/12/2024 20:30"
            /(\d{1,2})\/(\d{1,2})\/(\d{4})\s+(\d{1,2}):(\d{2})/
        ];

        for (const pattern of patterns) {
            const match = displayText.match(pattern);
            if (match) {
                try {
                    let date;
                    if (pattern.source.includes('AM|PM')) {
                        // Handle 12-hour format
                        const [_, ...parts] = match;
                        if (parts.length === 6) {
                            // "8:30 PM, December 15, 2024"
                            const [hour, minute, ampm, month, day, year] = parts;
                            const monthIndex = new Date(`${month} 1, 2000`).getMonth();
                            let hour24 = parseInt(hour);
                            if (ampm.toUpperCase() === 'PM' && hour24 !== 12) hour24 += 12;
                            if (ampm.toUpperCase() === 'AM' && hour24 === 12) hour24 = 0;
                            date = new Date(year, monthIndex, day, hour24, minute);
                        } else {
                            // "15 Dec 2024, 8:30 PM"
                            const [day, month, year, hour, minute, ampm] = parts;
                            const monthIndex = new Date(`${month} 1, 2000`).getMonth();
                            let hour24 = parseInt(hour);
                            if (ampm.toUpperCase() === 'PM' && hour24 !== 12) hour24 += 12;
                            if (ampm.toUpperCase() === 'AM' && hour24 === 12) hour24 = 0;
                            date = new Date(year, monthIndex, day, hour24, minute);
                        }
                    } else {
                        // Handle 24-hour format
                        if (pattern.source.includes('-')) {
                            // "2024-12-15 20:30"
                            const [year, month, day, hour, minute] = match.slice(1);
                            date = new Date(year, month - 1, day, hour, minute);
                        } else {
                            // "15/12/2024 20:30"
                            const [day, month, year, hour, minute] = match.slice(1);
                            date = new Date(year, month - 1, day, hour, minute);
                        }
                    }

                    if (date && !isNaN(date.getTime())) {
                        return date.toISOString().replace('T', ' ').substring(0, 19);
                    }
                } catch (e) {
                    console.log('Error parsing timestamp:', e);
                }
            }
        }

        // If no pattern matches, try to create a new timestamp with current date
        try {
            const now = new Date();
            const timeMatch = displayText.match(/(\d{1,2}):(\d{2})/);
            if (timeMatch) {
                const [_, hour, minute] = timeMatch;
                const newDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), hour, minute);
                return newDate.toISOString().replace('T', ' ').substring(0, 19);
            }
        } catch (e) {
            console.log('Error creating timestamp:', e);
        }

        return null;
    }

    // Global flag to prevent automatic sorting
    let preventAutoSort = false;
    let contentLoaded = false;

    // Sort blocks by timestamp (latest first)
    function sortBlocksByTimestamp() {
        console.log('sortBlocksByTimestamp called, preventAutoSort:', preventAutoSort);

        if (preventAutoSort) {
            console.log('Sorting prevented by preventAutoSort flag');
            return;
        }

        const editorContainer = $('#liveblog-custom-editor .editor-content');
        const blocks = editorContainer.find('.liveblog-block').get();
        console.log('Found', blocks.length, 'blocks to sort');
        console.log('Blocks before sorting:', blocks.map((block, index) => `${index}: ${$(block).data('block-type')} - ${$(block).attr('data-timestamp')}`));

        blocks.sort(function (a, b) {
            const timestampA = $(a).attr('data-timestamp') || '';
            const timestampB = $(b).attr('data-timestamp') || '';
            return timestampB.localeCompare(timestampA);
        });

        blocks.forEach(function (block) {
            editorContainer.append(block);
        });

        console.log('Blocks after sorting and appending:', editorContainer.find('.liveblog-block').length);
        console.log('Final blocks:', editorContainer.find('.liveblog-block').map(function (index) {
            return `${index}: ${$(this).data('block-type')} - ${$(this).attr('data-timestamp')}`;
        }).get());
    }

    // Initialize the custom editor
    function initCustomEditor() {
        try {
            console.log('initCustomEditor called');

            // Safety check for localized data
            if (typeof liveblogEditorAjax === 'undefined') {
                console.error('liveblogEditorAjax is not defined. Script localization failed.');
                showNotification('Error: Editor configuration missing. Please refresh the page.', 'error');
                return;
            }

            // Safety check for custom blocks data
            if (typeof window.liveblogCustomBlocks === 'undefined') {
                console.warn('window.liveblogCustomBlocks is undefined, initializing as empty array');
                window.liveblogCustomBlocks = [];
            }

            console.log('window.liveblogCustomBlocks at init:', window.liveblogCustomBlocks);
            console.log('window.liveblogCustomBlocks length at init:', window.liveblogCustomBlocks ? window.liveblogCustomBlocks.length : 0);

            const editorContainer = $('#liveblog-custom-editor');
            if (editorContainer.length === 0) {
                console.log('Editor container not found - likely not on LiveBlog edit page');
                return;
            }

            console.log('Editor container found, blocks count:', editorContainer.find('.liveblog-block').length);
            console.log('Initial blocks in editor:', editorContainer.find('.liveblog-block').map(function (index) {
                return `${index}: ${$(this).data('block-type')} - ${$(this).attr('data-timestamp')}`;
            }).get());

            // Check if we have saved blocks from PHP that need to be loaded
            if (window.liveblogCustomBlocks && window.liveblogCustomBlocks.length > 0 && !contentLoaded) {
                console.log('Found saved blocks from PHP, loading them...');
                loadLiveBlogContent();
            } else if (editorContainer.find('.liveblog-block').length === 0) {
                // Only add initial text block if no blocks exist and no saved blocks
                console.log('No blocks found and no saved blocks, adding initial text block');
                addBlock('text');
                console.log('After adding initial block, total blocks:', editorContainer.find('.liveblog-block').length);
            }

            // Bind events
            bindEditorEvents();

            // Initialize social media embedder for this editor
            if (window.SocialMediaEmbedder) {
                window.SocialMediaEmbedder.initializeSocialMediaEmbedder(editorContainer[0]);
            } else {
                console.warn('SocialMediaEmbedder not found');
            }

            // Only sort blocks if we didn't just load them (loadLiveBlogContent handles its own sorting)
            if (!(window.liveblogCustomBlocks && window.liveblogCustomBlocks.length > 0)) {
                console.log('About to call sortBlocksByTimestamp from initCustomEditor');
                sortBlocksByTimestamp();
                console.log('After sortBlocksByTimestamp call, blocks count:', editorContainer.find('.liveblog-block').length);
            }

            // Regenerate embeds from stored URLs with a delay to ensure DOM is ready
            setTimeout(() => {
                try {
                    console.log('Calling regenerateEmbedsFromUrls after delay');
                    console.log('DOM ready check - embed areas found:', $('.auto-embed-area').length);
                    console.log('DOM ready check - embed areas with data-embed-urls:', $('.auto-embed-area[data-embed-urls]').length);
                    regenerateEmbedsFromUrls();

                    // Load Twitter widgets script globally if not already loaded
                    if (!window.twttr) {
                        const script = document.createElement('script');
                        script.src = 'https://platform.twitter.com/widgets.js';
                        script.charset = 'utf-8';
                        script.async = true;
                        document.head.appendChild(script);
                    }
                } catch (e) {
                    console.error('Error in delayed initialization:', e);
                }
            }, 1000);

        } catch (e) {
            console.error('Critical error in initCustomEditor:', e);
            showNotification('Error initializing editor: ' + e.message, 'error');
        }
    }

    // Bind all editor events
    function bindEditorEvents() {
        const editorContainer = $('#liveblog-custom-editor');

        // Add block buttons
        editorContainer.on('click', '.add-block-btn', function (e) {
            e.preventDefault();
            const blockType = $(this).data('type');
            const currentBlock = $(this).closest('.liveblog-block');
            addBlock(blockType, currentBlock);
        });

        // Remove block buttons
        editorContainer.on('click', '.remove-block-btn', function (e) {
            e.preventDefault();
            const block = $(this).closest('.liveblog-block');
            if (editorContainer.find('.liveblog-block').length > 1) {
                block.remove();
                // Save the updated block list after removal
                saveAllBlocks();
            } else {
                alert('You must have at least one block.');
            }
        });

        // Image upload handling
        editorContainer.on('click', '.upload-placeholder', function () {
            $(this).siblings('.image-upload-input').click();
        });

        editorContainer.on('change', '.image-upload-input', function () {
            const file = this.files[0];
            if (file) {
                handleImageUpload(file, $(this).closest('.image-block'));
            }
        });

        // Video embedding
        editorContainer.on('click', '.embed-video-btn', function () {
            const videoBlock = $(this).closest('.video-block');
            const url = videoBlock.find('.video-url').val();
            if (url) {
                embedVideo(url, videoBlock);
            }
        });

        // Social media embedding
        editorContainer.on('click', '.embed-social-btn', function () {
            const socialBlock = $(this).closest('.social-block');
            const url = socialBlock.find('.social-url').val();
            if (url) {
                embedSocialMedia(url, socialBlock);
            }
        });

        // Timestamp display change handler
        editorContainer.on('blur', '.block-timestamp-display', function () {
            const block = $(this).closest('.liveblog-block');
            const displayText = $(this).text().trim();
            if (displayText) {
                // Try to parse the displayed text as a timestamp
                const timestamp = parseDisplayTextToTimestamp(displayText);
                if (timestamp) {
                    block.attr('data-timestamp', timestamp);
                    // Sort blocks only when timestamp is actually changed
                    sortBlocksByTimestamp();
                }
            }
        });

        // Publish block functionality
        editorContainer.on('click', '.publish-block-btn', function (e) {
            e.preventDefault();
            const block = $(this).closest('.liveblog-block');
            saveBlock(block, false); // false = publish
        });

        // Draft block functionality
        editorContainer.on('click', '.draft-block-btn', function (e) {
            e.preventDefault();
            const block = $(this).closest('.liveblog-block');
            saveBlock(block, true); // true = draft
        });

        // Drag and drop for images
        editorContainer.on('dragover', '.upload-placeholder', function (e) {
            e.preventDefault();
            $(this).addClass('drag-over');
        });

        editorContainer.on('dragleave', '.upload-placeholder', function (e) {
            e.preventDefault();
            $(this).removeClass('drag-over');
        });

        editorContainer.on('drop', '.upload-placeholder', function (e) {
            e.preventDefault();
            $(this).removeClass('drag-over');
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0 && files[0].type.startsWith('image/')) {
                handleImageUpload(files[0], $(this).closest('.image-block'));
            }
        });

        // Auto-embedding for text blocks
        editorContainer.on('paste', '.text-block .block-content, .text-block .title-content', function (e) {
            const textBlock = $(this).closest('.text-block');
            const pastedText = (e.originalEvent || e).clipboardData.getData('text/plain');

            // Check if pasted text contains social media URLs
            const socialUrls = detectSocialMediaUrls(pastedText);
            if (socialUrls.length > 0) {
                // Delay to allow the paste to complete
                setTimeout(() => {
                    handleAutoEmbed(textBlock, socialUrls);
                }, 100);
            }
        });

        // Auto-embedding on input for text blocks
        editorContainer.on('input', '.text-block .block-content, .text-block .title-content', function (e) {
            const textBlock = $(this).closest('.text-block');
            const content = $(this).html();

            // Check if content contains social media URLs
            const socialUrls = detectSocialMediaUrls(content);
            if (socialUrls.length > 0) {
                // Debounce the auto-embed to avoid too many calls
                clearTimeout(textBlock.data('embedTimeout'));
                textBlock.data('embedTimeout', setTimeout(() => {
                    handleAutoEmbed(textBlock, socialUrls);
                }, 1000));
            }
        });
    }

    // Add a new block
    function addBlock(blockType, afterBlock = null) {
        // Prevent automatic sorting during block addition
        preventAutoSort = true;

        const template = blockTypes[blockType].template;
        const newBlock = $(template);

        // Set initial timestamp but don't trigger sorting
        const timestamp = getUAETime();
        newBlock.attr('data-timestamp', timestamp);
        newBlock.find('.block-timestamp-display').text(formatTimestamp(timestamp));

        if (afterBlock && afterBlock.length) {
            // Get the index of the current block and insert before it
            const currentIndex = afterBlock.index();
            const editorContent = $('#liveblog-custom-editor .editor-content');
            const existingBlocks = editorContent.find('.liveblog-block');

            if (currentIndex === 0) {
                // Insert at the beginning
                editorContent.prepend(newBlock);
            } else {
                // Insert before the current block
                existingBlocks.eq(currentIndex).before(newBlock);
            }
        } else {
            $('#liveblog-custom-editor .editor-content').append(newBlock);
        }

        // Set the block index for proper updating
        const blockIndex = $('#liveblog-custom-editor .liveblog-block').index(newBlock);
        newBlock.attr('data-block-index', blockIndex);

        // Re-enable automatic sorting after a short delay
        setTimeout(() => {
            preventAutoSort = false;
        }, 100);

        // Focus on the new block
        setTimeout(() => {
            newBlock.find('[contenteditable="true"]').first().focus();
        }, 100);
    }

    // Update button text based on block status
    function updateBlockButtonText(block, isDraft = false) {
        const publishBtn = block.find('.publish-block-btn');
        const draftBtn = block.find('.draft-block-btn');

        if (isDraft) {
            publishBtn.text('Publish');
            draftBtn.text('Drafted');
        } else {
            publishBtn.text('Update');
            draftBtn.text('Save Draft');
        }
    }

    // Save individual block
    function saveBlock(block, isDraft = false) {
        // Get the block index
        const blockIndex = parseInt(block.attr('data-block-index')) || block.index();

        const autoEmbedArea = block.find('.auto-embed-area');
        const autoEmbedUrls = autoEmbedArea.attr('data-embed-urls') || '[]';

        // Debug: Log auto-embed data
        console.log('saveBlock - Block index:', blockIndex, 'Type:', block.data('block-type'));
        console.log('saveBlock - autoEmbedArea found:', autoEmbedArea.length > 0);
        console.log('saveBlock - autoEmbedUrls:', autoEmbedUrls);

        const blockData = {
            index: blockIndex,
            type: block.data('block-type'),
            timestamp: block.attr('data-timestamp') || '',
            content: block.find('.block-content').html() || '',
            titleColumn: block.find('.title-content').html() || '',
            timeDateContent: block.find('.time-date-content').html() || '',
            imageId: block.data('image-id') || null,
            imageCaption: block.find('.image-caption').html() || '',
            videoUrl: block.find('.video-url').val() || '',
            videoCaption: block.find('.video-caption').html() || '',
            videoEmbed: block.find('.video-embed-area').html() || '',
            quoteContent: block.find('.quote-content').html() || '',
            quoteAttribution: block.find('.quote-attribution').html() || '',
            socialUrl: block.find('.social-url').val() || '',
            socialEmbed: block.find('.social-embed-area').html() || '',
            autoEmbedUrls: autoEmbedUrls,
            isDraft: isDraft
        };

        $.ajax({
            url: liveblogEditorAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'save_liveblog_block',
                nonce: liveblogEditorAjax.nonce,
                post_id: liveblogEditorAjax.post_id,
                block: JSON.stringify(blockData)
            },
            success: function (response) {
                if (response.success) {
                    const status = isDraft ? 'saved as draft' : 'published';
                    showNotification(`Block ${status} successfully!`, 'success');
                    block.addClass('saved');

                    // Update button text based on new status
                    updateBlockButtonText(block, isDraft);

                    // Update block's draft status
                    if (isDraft) {
                        block.addClass('draft-block');
                    } else {
                        block.removeClass('draft-block');
                    }

                    setTimeout(() => block.removeClass('saved'), 2000);
                } else {
                    showNotification('Error saving block: ' + response.data, 'error');
                }
            },
            error: function () {
                showNotification('Error saving block. Please try again.', 'error');
            }
        });
    }

    // Add time/date tab
    function addTimeDateTab() {
        const timestamp = getUAETime();
        const timeDateBlock = $(`
            <div class="liveblog-block time-date-block" data-block-type="time-date" data-timestamp="${timestamp}">
                <div class="block-header">
                    <span class="block-icon">📅</span>
                    <span class="block-title">Time/Date Update</span>
                </div>
                <div class="block-timestamp-container">
                    <label>Timestamp:</label>
                    <span class="block-timestamp-display" contenteditable="true" placeholder="Click to edit time">${formatTimestamp(timestamp)}</span>
                </div>
                <div class="time-date-content" contenteditable="true" placeholder="Enter time/date update content..."></div>
                <div class="block-actions">
                    <button class="add-block-btn" data-type="text">+ Add Text</button>
                    <button class="add-block-btn" data-type="image">+ Add Image</button>
                    <button class="add-block-btn" data-type="video">+ Add Video</button>
                    <button class="add-block-btn" data-type="quote">+ Add Quote</button>
                    <button class="add-block-btn" data-type="social">+ Add Social</button>
                    <button class="publish-block-btn">Publish</button>
                    <button class="draft-block-btn">Save Draft</button>
                    <button class="remove-block-btn">Remove</button>
                </div>
            </div>
        `);

        // Add to editor
        const editorContainer = $('#liveblog-custom-editor .editor-content');
        editorContainer.append(timeDateBlock);

        // Set the block index for proper updating
        const blockIndex = editorContainer.find('.liveblog-block').index(timeDateBlock);
        timeDateBlock[0].index = function () { return blockIndex; };

        // Focus on the new block
        timeDateBlock.find('.time-date-content').focus();

        // Sort blocks by timestamp
        sortBlocksByTimestamp();
    }

    // Handle image upload
    function handleImageUpload(file, block) {
        const formData = new FormData();
        formData.append('action', 'upload_liveblog_image');
        formData.append('nonce', liveblogEditorAjax.nonce);
        formData.append('image', file);

        $.ajax({
            url: liveblogEditorAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    const imageElement = block.find('.uploaded-image');
                    imageElement.attr('src', response.data.url);
                    imageElement.show();
                    block.find('.upload-placeholder').hide();

                    // Store image ID for saving
                    block.data('image-id', response.data.id);
                } else {
                    alert('Error uploading image: ' + response.data);
                }
            },
            error: function () {
                alert('Error uploading image. Please try again.');
            }
        });
    }

    // Embed video
    function embedVideo(url, block) {
        const embedArea = block.find('.video-embed-area');

        // Simple video embedding logic
        if (url.includes('youtube.com') || url.includes('youtu.be')) {
            let videoId = '';
            if (url.includes('youtu.be/')) {
                videoId = url.split('youtu.be/')[1].split('?')[0];
            } else if (url.includes('youtube.com/watch?v=')) {
                videoId = url.split('v=')[1].split('&')[0];
            }

            if (videoId) {
                const embedCode = `<div class="video-container"><iframe width="560" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe></div>`;
                embedArea.html(embedCode);
            }
        } else if (url.includes('vimeo.com')) {
            const videoId = url.split('vimeo.com/')[1];
            const embedCode = `<div class="video-container"><iframe src="https://player.vimeo.com/video/${videoId}" width="560" height="315" frameborder="0" allowfullscreen></iframe></div>`;
            embedArea.html(embedCode);
        } else {
            embedArea.html('<p>Unsupported video platform. Please use YouTube or Vimeo.</p>');
        }
    }

    // Embed social media
    function embedSocialMedia(url, block) {
        const embedArea = block.find('.social-embed-area');

        // Show loading state
        embedArea.html('<div class="embed-loading">Loading social media embed...</div>');

        if (window.SocialMediaEmbedder && window.SocialMediaEmbedder.detectSocialMediaLinks) {
            const links = window.SocialMediaEmbedder.detectSocialMediaLinks(url);
            if (links.length > 0) {
                const embedCode = window.SocialMediaEmbedder.replaceLinksWithEmbeds(url, links);
                embedArea.html(embedCode);
                showNotification('Successfully embedded social post!', 'success');
            } else {
                embedArea.html('<p class="embed-error">Unsupported social media platform.</p>');
                showNotification('Unsupported social media platform', 'error');
            }
        } else {
            // Fallback embedding for common platforms
            const embedCode = createFallbackEmbed(url);
            if (embedCode) {
                embedArea.html(embedCode);
                showNotification('Successfully embedded social post!', 'success');
            } else {
                embedArea.html('<p class="embed-error">Unable to embed this social media link.</p>');
                showNotification('Unable to embed this social media link', 'error');
            }
        }
    }

    // Create fallback embed for common social media platforms
    function createFallbackEmbed(url) {
        console.log('createFallbackEmbed called with URL:', url);

        // Twitter/X
        if (url.includes('twitter.com') || url.includes('x.com')) {
            const tweetId = extractTweetId(url);
            console.log('Twitter/X URL detected, tweet ID:', tweetId);
            if (tweetId) {
                const embedCode = `<div class="social-embed-preview" data-platform="twitter" data-url="${url}">
                    <div class="embed-header">
                        <span class="platform-icon">🐦</span>
                        <span class="platform-name">Twitter/X</span>
                    </div>
                    <div class="embed-content">
                        <blockquote class="twitter-tweet" data-theme="light" data-dnt="true">
                            <a href="${url}"></a>
                        </blockquote>
                        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                    </div>
                </div>`;
                console.log('Created Twitter embed code, length:', embedCode.length);
                return embedCode;
            }
        }

        // Facebook
        if (url.includes('facebook.com')) {
            return `<div class="social-embed-preview" data-platform="facebook" data-url="${url}">
                <div class="embed-header">
                    <span class="platform-icon">📘</span>
                    <span class="platform-name">Facebook</span>
                </div>
                <div class="embed-content">
                    <div class="fb-post" data-href="${url}" data-width="500"></div>
                    <div id="fb-root"></div>
                </div>
            </div>`;
        }

        // Instagram
        if (url.includes('instagram.com')) {
            const postId = extractInstagramPostId(url);
            if (postId) {
                return `<div class="social-embed-preview" data-platform="instagram" data-url="${url}">
                    <div class="embed-header">
                        <span class="platform-icon">📷</span>
                        <span class="platform-name">Instagram</span>
                    </div>
                    <div class="embed-content">
                        <iframe src="https://www.instagram.com/p/${postId}/embed/" width="400" height="480" frameborder="0" scrolling="no" allowtransparency="true"></iframe>
                    </div>
                </div>`;
            }
        }

        // YouTube
        if (url.includes('youtube.com') || url.includes('youtu.be')) {
            const videoId = extractYouTubeVideoId(url);
            if (videoId) {
                return `<div class="social-embed-preview" data-platform="youtube" data-url="${url}">
                    <div class="embed-header">
                        <span class="platform-icon">📺</span>
                        <span class="platform-name">YouTube</span>
                    </div>
                    <div class="embed-content">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>`;
            }
        }

        // Generic link preview
        const genericEmbedCode = `<div class="social-embed-preview" data-platform="generic" data-url="${url}">
            <div class="embed-header">
                <span class="platform-icon">🔗</span>
                <span class="platform-name">Social Media Link</span>
            </div>
            <div class="embed-content">
                <a href="${url}" target="_blank" class="social-link-preview">
                    <div class="link-preview">
                        <div class="link-url">${url}</div>
                        <div class="link-text">Click to view original post</div>
                    </div>
                </a>
            </div>
        </div>`;
        console.log('Created generic embed code, length:', genericEmbedCode.length);
        return genericEmbedCode;
    }



    // Helper functions for extracting IDs
    function extractTweetId(url) {
        const match = url.match(/twitter\.com\/\w+\/status\/(\d+)/) || url.match(/x\.com\/\w+\/status\/(\d+)/);
        return match ? match[1] : null;
    }

    function extractInstagramPostId(url) {
        const match = url.match(/instagram\.com\/p\/([a-zA-Z0-9_-]+)/);
        return match ? match[1] : null;
    }

    function extractYouTubeVideoId(url) {
        if (url.includes('youtu.be/')) {
            return url.split('youtu.be/')[1].split('?')[0];
        } else if (url.includes('youtube.com/watch?v=')) {
            return url.split('v=')[1].split('&')[0];
        }
        return null;
    }

    // Detect social media URLs in text
    function detectSocialMediaUrls(text) {
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        const urls = text.match(urlRegex) || [];

        return urls.filter(url => {
            return url.includes('twitter.com') ||
                url.includes('x.com') ||
                url.includes('facebook.com') ||
                url.includes('instagram.com') ||
                url.includes('youtube.com') ||
                url.includes('youtu.be') ||
                url.includes('linkedin.com') ||
                url.includes('tiktok.com');
        });
    }

    // Handle auto-embedding for text blocks
    function handleAutoEmbed(textBlock, socialUrls) {
        console.log('handleAutoEmbed called with:', textBlock, 'URLs:', socialUrls);

        if (!textBlock || !socialUrls || socialUrls.length === 0) {
            console.error('handleAutoEmbed: Invalid parameters', { textBlock, socialUrls });
            return;
        }

        // Check if embed area already exists
        let embedArea = textBlock.find('.auto-embed-area');
        if (embedArea.length === 0) {
            // Create embed area if it doesn't exist
            embedArea = $('<div class="auto-embed-area"></div>');
            textBlock.find('.block-content').after(embedArea);
            console.log('Created new embed area');
        } else {
            console.log('Found existing embed area');
        }

        // Clear existing embeds
        embedArea.empty();

        // Store URLs in data attribute for persistence
        embedArea.attr('data-embed-urls', JSON.stringify(socialUrls));
        console.log('Stored URLs in data attribute:', JSON.stringify(socialUrls));

        // Create actual embeds in the editor
        socialUrls.forEach((url, index) => {
            console.log('Processing URL', index, ':', url);
            const embedContainer = $('<div class="auto-embed-item"></div>');
            embedArea.append(embedContainer);

            // Create actual embed using the existing createFallbackEmbed function
            const embedHtml = createFallbackEmbed(url);
            embedContainer.html(embedHtml);

            console.log('Created actual embed for URL:', url);
            const platformName = getPlatformName(url);
            showNotification(`Added ${platformName} embed to content pane`, 'success');
        });

        // Add remove button for embeds
        if (embedArea.find('.auto-embed-item').length > 0) {
            const removeBtn = $('<button type="button" class="remove-embed-btn">Remove Social Posts</button>');
            embedArea.append(removeBtn);

            removeBtn.on('click', function () {
                embedArea.remove();
            });
        }

        // Initialize social media scripts for the new embeds
        initializeSocialScripts(embedArea);

        console.log('handleAutoEmbed completed. Embed area now contains', embedArea.find('.auto-embed-item').length, 'items');
    }

    // Get platform name from URL
    function getPlatformName(url) {
        if (url.includes('twitter.com') || url.includes('x.com')) return 'Twitter/X';
        if (url.includes('facebook.com')) return 'Facebook';
        if (url.includes('instagram.com')) return 'Instagram';
        if (url.includes('youtube.com') || url.includes('youtu.be')) return 'YouTube';
        if (url.includes('linkedin.com')) return 'LinkedIn';
        if (url.includes('tiktok.com')) return 'TikTok';
        return 'Social Media';
    }

    // Get platform icon from URL
    function getPlatformIcon(url) {
        if (url.includes('twitter.com') || url.includes('x.com')) return '🐦';
        if (url.includes('facebook.com')) return '📘';
        if (url.includes('instagram.com')) return '📷';
        if (url.includes('youtube.com') || url.includes('youtu.be')) return '📺';
        if (url.includes('linkedin.com')) return '💼';
        if (url.includes('tiktok.com')) return '🎵';
        return '🔗';
    }

    // Initialize social media scripts for embeds
    function initializeSocialScripts(container) {
        console.log('initializeSocialScripts called for container:', container);

        // Check if Twitter widgets.js is already loaded
        if (typeof twttr === 'undefined') {
            console.log('Loading Twitter widgets.js');
            const twitterScript = document.createElement('script');
            twitterScript.src = 'https://platform.twitter.com/widgets.js';
            twitterScript.async = true;
            twitterScript.charset = 'utf-8';
            document.head.appendChild(twitterScript);
        } else {
            console.log('Twitter widgets.js already loaded, re-initializing');
            if (twttr && twttr.widgets) {
                twttr.widgets.load(container[0]);
            }
        }

        // Check if Facebook SDK is already loaded
        if (typeof FB === 'undefined') {
            console.log('Loading Facebook SDK');
            const fbScript = document.createElement('script');
            fbScript.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0';
            fbScript.async = true;
            fbScript.defer = true;
            fbScript.crossOrigin = 'anonymous';
            document.head.appendChild(fbScript);
        } else {
            console.log('Facebook SDK already loaded, re-initializing');
            if (FB && FB.XFBML) {
                FB.XFBML.parse(container[0]);
            }
        }

        // Initialize Twitter embeds after a short delay
        setTimeout(function () {
            if (typeof twttr !== 'undefined' && twttr.widgets) {
                console.log('Initializing Twitter widgets');
                twttr.widgets.load(container[0]);
            }
        }, 1000);

        // Initialize Facebook embeds after a short delay
        setTimeout(function () {
            if (typeof FB !== 'undefined' && FB.XFBML) {
                console.log('Initializing Facebook XFBML');
                FB.XFBML.parse(container[0]);
            }
        }, 1500);
    }

    // Regenerate embeds from stored URLs
    function regenerateEmbedsFromUrls() {
        console.log('regenerateEmbedsFromUrls called');
        const embedAreas = $('.auto-embed-area[data-embed-urls]');
        console.log('Found embed areas with data-embed-urls:', embedAreas.length);

        if (embedAreas.length === 0) {
            console.log('No embed areas found with data-embed-urls attribute');
            return;
        }

        embedAreas.each(function (index) {
            const embedArea = $(this);
            const urlsJson = embedArea.attr('data-embed-urls');
            console.log('Embed area', index, 'URLs JSON:', urlsJson);

            if (urlsJson && urlsJson !== '[]') {
                try {
                    const urls = JSON.parse(urlsJson);
                    console.log('Parsed URLs for area', index, ':', urls);
                    if (urls && urls.length > 0) {
                        // Only regenerate if the embed area is empty
                        const existingItems = embedArea.find('.auto-embed-item');
                        console.log('Existing embed items in area', index, ':', existingItems.length);

                        if (existingItems.length === 0) {
                            // Find the parent text block
                            const textBlock = embedArea.closest('.text-block');
                            if (textBlock.length > 0) {
                                console.log('Regenerating embeds for block:', textBlock, 'URLs:', urls);
                                handleAutoEmbed(textBlock, urls);
                            } else {
                                console.error('Could not find parent text block for embed area', index);
                            }
                        } else {
                            console.log('Skipping regeneration for area', index, 'because it already has items');
                            // Initialize scripts for existing embeds
                            initializeSocialScripts(embedArea);
                        }
                    } else {
                        console.log('No URLs found for area', index);
                    }
                } catch (e) {
                    console.error('Error parsing stored embed URLs for area', index, ':', e);
                }
            } else {
                console.log('No URLs JSON or empty array for area', index);
            }
        });
    }

    // Save all current blocks to database
    function saveAllBlocks() {
        const blocks = [];
        $('#liveblog-custom-editor .liveblog-block').each(function (index) {
            const block = $(this);
            const autoEmbedArea = block.find('.auto-embed-area');
            const autoEmbedUrls = autoEmbedArea.attr('data-embed-urls') || '[]';

            // Debug: Log auto-embed data
            console.log('saveAllBlocks - Block index:', index, 'Type:', block.data('block-type'));
            console.log('saveAllBlocks - autoEmbedArea found:', autoEmbedArea.length > 0);
            console.log('saveAllBlocks - autoEmbedUrls:', autoEmbedUrls);

            const blockData = {
                index: index,
                type: block.data('block-type'),
                timestamp: block.attr('data-timestamp') || '',
                content: block.find('.block-content').html() || '',
                titleColumn: block.find('.title-content').html() || '',
                timeDateContent: block.find('.time-date-content').html() || '',
                imageId: block.data('image-id') || null,
                imageCaption: block.find('.image-caption').html() || '',
                videoUrl: block.find('.video-url').val() || '',
                videoCaption: block.find('.video-caption').html() || '',
                videoEmbed: block.find('.video-embed-area').html() || '',
                quoteContent: block.find('.quote-content').html() || '',
                quoteAttribution: block.find('.quote-attribution').html() || '',
                socialUrl: block.find('.social-url').val() || '',
                socialEmbed: block.find('.social-embed-area').html() || '',
                autoEmbedUrls: autoEmbedUrls,
                isDraft: block.hasClass('draft-block')
            };
            blocks.push(blockData);
        });

        $.ajax({
            url: liveblogEditorAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'save_liveblog_content',
                nonce: liveblogEditorAjax.nonce,
                post_id: liveblogEditorAjax.post_id,
                blocks: JSON.stringify(blocks)
            },
            success: function (response) {
                if (response.success) {
                    showNotification('Blocks updated successfully!', 'success');
                } else {
                    showNotification('Error updating blocks: ' + response.data, 'error');
                }
            },
            error: function () {
                showNotification('Error updating blocks. Please try again.', 'error');
            }
        });
    }

    // Show notification
    function showNotification(message, type = 'info') {
        const notification = $(`<div class="liveblog-notification ${type}">${message}</div>`);
        $('body').append(notification);

        setTimeout(() => {
            notification.fadeOut(() => notification.remove());
        }, 3000);
    }

    // Initialize when DOM is ready
    $(document).ready(function () {
        initCustomEditor();
    });

    // Save LiveBlog content
    function saveLiveBlogContent() {
        const blocks = [];
        const totalBlocks = $('#liveblog-custom-editor .liveblog-block').length;
        console.log('saveLiveBlogContent - Total blocks found in editor:', totalBlocks);

        $('#liveblog-custom-editor .liveblog-block').each(function (index) {
            const block = $(this);
            const autoEmbedArea = block.find('.auto-embed-area');
            const autoEmbedUrls = autoEmbedArea.attr('data-embed-urls') || '[]';

            // Debug: Log auto-embed data
            console.log('saveLiveBlogContent - Block', index, 'type:', block.data('block-type'));
            console.log('saveLiveBlogContent - Block', index, 'autoEmbedArea found:', autoEmbedArea.length > 0);
            console.log('saveLiveBlogContent - Block', index, 'autoEmbedUrls:', autoEmbedUrls);

            const blockData = {
                type: block.data('block-type'),
                timestamp: block.attr('data-timestamp') || '',
                content: block.find('.block-content').html() || '',
                titleColumn: block.find('.title-content').html() || '',
                timeDateContent: block.find('.time-date-content').html() || '',
                imageId: block.data('image-id') || null,
                imageCaption: block.find('.image-caption').html() || '',
                videoUrl: block.find('.video-url').val() || '',
                videoCaption: block.find('.video-caption').html() || '',
                videoEmbed: block.find('.video-embed-area').html() || '',
                quoteContent: block.find('.quote-content').html() || '',
                quoteAttribution: block.find('.quote-attribution').html() || '',
                socialUrl: block.find('.social-url').val() || '',
                socialEmbed: block.find('.social-embed-area').html() || '',
                autoEmbedUrls: autoEmbedUrls,
                isDraft: block.hasClass('draft-block')
            };
            blocks.push(blockData);
        });

        $.ajax({
            url: liveblogEditorAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'save_liveblog_content',
                nonce: liveblogEditorAjax.nonce,
                post_id: liveblogEditorAjax.post_id,
                blocks: JSON.stringify(blocks)
            },
            success: function (response) {
                if (response.success) {
                    showNotification('LiveBlog content saved successfully!', 'success');
                    // Reset contentLoaded flag so content can be reloaded on page refresh
                    contentLoaded = false;
                    console.log('Reset contentLoaded flag after successful save');
                } else {
                    showNotification('Error saving content: ' + response.data, 'error');
                }
            },
            error: function () {
                showNotification('Error saving content. Please try again.', 'error');
            }
        });
    }

    // Load LiveBlog content
    function loadLiveBlogContent() {
        console.log('loadLiveBlogContent called');
        console.log('contentLoaded flag:', contentLoaded);

        // Prevent multiple loads
        if (contentLoaded) {
            console.log('Content already loaded, skipping...');
            return;
        }

        console.log('window.liveblogCustomBlocks:', window.liveblogCustomBlocks);
        console.log('window.liveblogCustomBlocks length:', window.liveblogCustomBlocks ? window.liveblogCustomBlocks.length : 0);

        if (window.liveblogCustomBlocks && window.liveblogCustomBlocks.length > 0) {
            console.log('Loading', window.liveblogCustomBlocks.length, 'blocks');
            console.log('Block details:', window.liveblogCustomBlocks.map((block, index) => `${index}: ${block.type} - ${block.isDraft ? 'draft' : 'published'}`));

            // Prevent automatic sorting during loading
            preventAutoSort = true;

            $('#liveblog-custom-editor .editor-content').empty();

            // Create all blocks first
            const createdBlocks = [];

            window.liveblogCustomBlocks.forEach(function (block, index) {
                let newBlock;

                if (block.type === 'time-date') {
                    // Create time-date block manually since it's not in blockTypes
                    const timestamp = block.timestamp || getUAETime();
                    newBlock = $(`
                        <div class="liveblog-block time-date-block" data-block-type="time-date" data-timestamp="${timestamp}">
                            <div class="block-header">
                                <span class="block-icon">📅</span>
                                <span class="block-title">Time/Date Update</span>
                            </div>
                            <div class="block-timestamp-container">
                                <label>Timestamp:</label>
                                <span class="block-timestamp-display" contenteditable="true" placeholder="Click to edit time">${formatTimestamp(timestamp)}</span>
                            </div>
                            <div class="time-date-content" contenteditable="true" placeholder="Enter time/date update content...">${block.timeDateContent || ''}</div>
                            <div class="block-actions">
                                <button class="add-block-btn" data-type="text">+ Add Text</button>
                                <button class="add-block-btn" data-type="image">+ Add Image</button>
                                <button class="add-block-btn" data-type="video">+ Add Video</button>
                                <button class="add-block-btn" data-type="quote">+ Add Quote</button>
                                <button class="add-block-btn" data-type="social">+ Add Social</button>
                                <button class="publish-block-btn">Publish</button>
                                <button class="draft-block-btn">Save as Draft</button>
                                <button class="remove-block-btn">Remove</button>
                            </div>
                        </div>
                    `);
                } else {
                    // Create block using template
                    const template = blockTypes[block.type].template;
                    newBlock = $(template);
                }

                // Set the block index for proper updating
                newBlock.attr('data-block-index', index);

                // Set timestamp if available
                if (block.timestamp) {
                    newBlock.attr('data-timestamp', block.timestamp);
                    newBlock.find('.block-timestamp-display').text(formatTimestamp(block.timestamp));
                }

                // Set draft status and update button text
                if (block.isDraft) {
                    newBlock.addClass('draft-block');
                    updateBlockButtonText(newBlock, true);
                } else {
                    newBlock.removeClass('draft-block');
                    updateBlockButtonText(newBlock, false);
                }

                // Set content based on block type
                switch (block.type) {
                    case 'text':
                        newBlock.find('.title-content').html(block.titleColumn || '');
                        newBlock.find('.block-content').html(block.content || '');
                        break;
                    case 'time-date':
                        newBlock.find('.time-date-content').html(block.timeDateContent || '');
                        break;
                    case 'image':
                        if (block.imageId) {
                            newBlock.data('image-id', block.imageId);
                            newBlock.find('.uploaded-image').attr('src', block.imageUrl || '').show();
                            newBlock.find('.upload-placeholder').hide();
                        }
                        newBlock.find('.image-caption').html(block.imageCaption || '');
                        break;
                    case 'video':
                        newBlock.find('.video-url').val(block.videoUrl || '');
                        newBlock.find('.video-embed-area').html(block.videoEmbed || '');
                        newBlock.find('.video-caption').html(block.videoCaption || '');
                        break;
                    case 'quote':
                        newBlock.find('.quote-content').html(block.quoteContent || '');
                        newBlock.find('.quote-attribution').html(block.quoteAttribution || '');
                        break;
                    case 'social':
                        newBlock.find('.social-url').val(block.socialUrl || '');
                        newBlock.find('.social-embed-area').html(block.socialEmbed || '');
                        break;
                }

                // Handle auto-embed URLs for text blocks
                if (block.type === 'text' && block.autoEmbedUrls) {
                    try {
                        const urls = JSON.parse(block.autoEmbedUrls);
                        if (urls && urls.length > 0) {
                            handleAutoEmbed(newBlock, urls);
                        }
                    } catch (e) {
                        console.error('Error parsing autoEmbedUrls:', e);
                    }
                }

                createdBlocks.push(newBlock);
            });

            // Append all blocks to the editor
            createdBlocks.forEach(function (block) {
                $('#liveblog-custom-editor .editor-content').append(block);
            });

            console.log('Appended', createdBlocks.length, 'blocks to editor');
            console.log('Total blocks in editor after loading:', $('#liveblog-custom-editor .liveblog-block').length);
            console.log('Created blocks details:', createdBlocks.map((block, index) => `${index}: ${block.data('block-type')} - ${block.hasClass('draft-block') ? 'draft' : 'published'}`));

            // Sort blocks by timestamp after loading
            sortBlocksByTimestamp();

            // Re-enable automatic sorting after a short delay
            setTimeout(() => {
                preventAutoSort = false;
                contentLoaded = true;
                console.log('Re-enabled automatic sorting');
                console.log('Set contentLoaded flag to true');
                console.log('Final block count after loading:', $('#liveblog-custom-editor .liveblog-block').length);
                console.log('Final blocks after loading:', $('#liveblog-custom-editor .liveblog-block').map(function (index) {
                    return `${index}: ${$(this).data('block-type')} - ${$(this).attr('data-timestamp')}`;
                }).get());
            }, 100);

            showNotification('LiveBlog content loaded successfully!', 'success');
        } else {
            showNotification('No saved content found.', 'info');
        }
    }

    // Expose functions globally
    window.LiveBlogCustomEditor = {
        addBlock: addBlock,
        showNotification: showNotification,
        saveContent: saveLiveBlogContent,
        loadContent: loadLiveBlogContent,
        addTimeDateTab: addTimeDateTab,
        saveBlock: saveBlock,

    };

    // Expose save and load functions globally for inline onclick handlers
    window.saveLiveBlogContent = saveLiveBlogContent;
    window.loadLiveBlogContent = loadLiveBlogContent;

})(jQuery); 