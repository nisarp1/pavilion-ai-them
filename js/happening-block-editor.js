(function() {
    'use strict';

    // Wait for WordPress Block Editor to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Only run on happening post type
        if (!document.body.classList.contains('post-type-happening')) {
            return;
        }

        // Wait for the block editor to be fully loaded
        waitForBlockEditor();
    });

    function waitForBlockEditor() {
        const checkEditor = setInterval(function() {
            if (window.wp && wp.data && wp.data.dispatch && wp.data.dispatch('core/block-editor')) {
                clearInterval(checkEditor);
                initHappeningBlockEditor();
            }
        }, 100);
    }

    function initHappeningBlockEditor() {
        // Add repeatable block controls
        addRepeatableBlockControls();
        
        // Add custom fields to existing blocks
        addCustomFieldsToExistingBlocks();
        
        // Monitor for new blocks
        monitorForNewBlocks();
        
        // Override the block inserter UI
        overrideBlockInserterUI();
        
        // Initialize existing blocks
        initializeExistingBlocks();
    }

    function addRepeatableBlockControls() {
        // Add a toolbar for managing repeatable blocks
        const toolbar = createRepeatableBlockToolbar();
        document.body.appendChild(toolbar);
        
        // Add event listeners for the toolbar
        addToolbarEventListeners(toolbar);
    }

    function createRepeatableBlockToolbar() {
        const toolbar = document.createElement('div');
        toolbar.className = 'happening-repeatable-toolbar';
        toolbar.innerHTML = `
            <div class="happening-toolbar-content">
                <h3>📅 Happening Post Blocks</h3>
                <div class="happening-toolbar-actions">
                    <button type="button" class="happening-add-block-btn" id="addHappeningBlock">
                        <i class="fas fa-plus"></i> Add New Block
                    </button>
                    <button type="button" class="happening-save-all-btn" id="saveAllBlocks">
                        <i class="fas fa-save"></i> Save All Blocks
                    </button>
                    <button type="button" class="happening-publish-all-btn" id="publishAllBlocks">
                        <i class="fas fa-globe"></i> Publish All
                    </button>
                </div>
            </div>
        `;
        return toolbar;
    }

    function addToolbarEventListeners(toolbar) {
        const addBlockBtn = toolbar.querySelector('#addHappeningBlock');
        const saveAllBtn = toolbar.querySelector('#saveAllBlocks');
        const publishAllBtn = toolbar.querySelector('#publishAllBlocks');

        if (addBlockBtn) {
            addBlockBtn.addEventListener('click', addNewHappeningBlock);
        }

        if (saveAllBtn) {
            saveAllBtn.addEventListener('click', saveAllBlocks);
        }

        if (publishAllBtn) {
            publishAllBtn.addEventListener('click', publishAllBlocks);
        }
    }

    function addNewHappeningBlock() {
        // Create a new happening block
        const blockData = {
            blockName: 'core/group',
            attrs: {
                className: 'happening-post-block'
            },
            innerBlocks: [
                {
                    blockName: 'core/heading',
                    attrs: {
                        level: 2,
                        content: 'New Happening Block'
                    }
                },
                {
                    blockName: 'core/paragraph',
                    attrs: {
                        content: 'Enter your content here...'
                    }
                }
            ]
        };

        // Insert the block at the beginning
        wp.data.dispatch('core/block-editor').insertBlock(blockData, 0);
    }

    function saveAllBlocks() {
        const blocks = document.querySelectorAll('.happening-post-block');
        const blockData = [];

        blocks.forEach((block, index) => {
            const blockInfo = extractBlockData(block, index);
            blockData.push(blockInfo);
        });

        // Save all blocks data
        saveBlocksData(blockData, 'draft');
    }

    function publishAllBlocks() {
        const blocks = document.querySelectorAll('.happening-post-block');
        const blockData = [];

        blocks.forEach((block, index) => {
            const blockInfo = extractBlockData(block, index);
            blockInfo.status = 'published';
            blockData.push(blockInfo);
        });

        // Save all blocks data as published
        saveBlocksData(blockData, 'published');
    }

    function extractBlockData(block, index) {
        const headerInput = block.querySelector('.happening-block-header');
        const contentInput = block.querySelector('.happening-block-content');
        const dateInput = block.querySelector('.happening-block-date');
        const timeInput = block.querySelector('.happening-block-time');
        const statusInput = block.querySelector('.happening-block-status');

        return {
            index: index,
            header: headerInput ? headerInput.value : '',
            content: contentInput ? contentInput.value : '',
            date: dateInput ? dateInput.value : '',
            time: timeInput ? timeInput.value : '',
            status: statusInput ? statusInput.value : 'draft',
            timestamp: new Date().toISOString()
        };
    }

    function saveBlocksData(blockData, action) {
        const formData = new FormData();
        formData.append('action', 'save_happening_blocks_data');
        formData.append('nonce', happeningBlockEditor.nonce);
        formData.append('post_id', happeningBlockEditor.postId);
        formData.append('blocks_data', JSON.stringify(blockData));
        formData.append('action_type', action);

        fetch(happeningBlockEditor.ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showGlobalMessage(`All blocks ${action === 'published' ? 'published' : 'saved'} successfully!`, 'success');
            } else {
                showGlobalMessage('Error saving blocks', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving blocks:', error);
            showGlobalMessage('Error saving blocks', 'error');
        });
    }

    function showGlobalMessage(message, type = 'success') {
        // Remove existing message
        const existingMessage = document.querySelector('.happening-global-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        // Create new message
        const messageElement = document.createElement('div');
        messageElement.className = `happening-global-message ${type}`;
        messageElement.textContent = message;

        document.body.appendChild(messageElement);

        // Remove message after 5 seconds
        setTimeout(function() {
            if (messageElement.parentNode) {
                messageElement.remove();
            }
        }, 5000);
    }

    function addCustomFieldsToBlock(block, index) {
        // Skip if already processed
        if (block.querySelector('.happening-custom-fields')) {
            return;
        }

        // Create custom fields container
        const customFieldsContainer = document.createElement('div');
        customFieldsContainer.className = 'happening-custom-fields';
        customFieldsContainer.dataset.blockIndex = index;

        // Create comprehensive fields HTML
        const fieldsHTML = `
            <div class="happening-block-header-section">
                <div class="happening-field-group">
                    <label>Block Header:</label>
                    <input type="text" class="happening-block-header" data-block-index="${index}" placeholder="Enter block header">
                </div>
            </div>
            
            <div class="happening-block-content-section">
                <div class="happening-field-group">
                    <label>Block Content:</label>
                    <textarea class="happening-block-content" data-block-index="${index}" placeholder="Enter block content" rows="4"></textarea>
                </div>
            </div>
            
            <div class="happening-fields-grid">
                <div class="happening-field-group">
                    <label>Date:</label>
                    <input type="date" class="happening-block-date" data-block-index="${index}">
                </div>
                <div class="happening-field-group">
                    <label>Time:</label>
                    <input type="time" class="happening-block-time" data-block-index="${index}">
                </div>
                <div class="happening-field-group">
                    <label>Status:</label>
                    <select class="happening-block-status" data-block-index="${index}">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>
            
            <div class="happening-fields-actions">
                <button type="button" class="happening-publish-block" data-block-index="${index}">
                    <i class="fas fa-globe"></i> Publish/Update
                </button>
                <button type="button" class="happening-save-draft" data-block-index="${index}">
                    <i class="fas fa-save"></i> Save Draft
                </button>
                <button type="button" class="happening-remove-block" data-block-index="${index}">
                    <i class="fas fa-trash"></i> Remove Block
                </button>
            </div>
        `;

        customFieldsContainer.innerHTML = fieldsHTML;

        // Insert the custom fields before the block content
        const blockContent = block.querySelector('.wp-block-edit, .block-editor-block-list__layout');
        if (blockContent) {
            blockContent.parentNode.insertBefore(customFieldsContainer, blockContent);
        } else {
            // Fallback: insert at the beginning of the block
            block.insertBefore(customFieldsContainer, block.firstChild);
        }

        // Load existing values
        loadBlockMetaValues(block, index);

        // Add event listeners
        addBlockFieldEventListeners(block, index);
    }

    function loadBlockMetaValues(block, index) {
        // Load values from window.happeningBlockMeta if available
        if (window.happeningBlockMeta && window.happeningBlockMeta[index]) {
            const meta = window.happeningBlockMeta[index];
            
            const headerInput = block.querySelector('.happening-block-header');
            const contentInput = block.querySelector('.happening-block-content');
            const dateInput = block.querySelector('.happening-block-date');
            const timeInput = block.querySelector('.happening-block-time');
            const statusInput = block.querySelector('.happening-block-status');
            
            if (headerInput && meta.header) headerInput.value = meta.header;
            if (contentInput && meta.content) contentInput.value = meta.content;
            if (dateInput && meta.date) dateInput.value = meta.date;
            if (timeInput && meta.time) timeInput.value = meta.time;
            if (statusInput && meta.status) statusInput.value = meta.status;
        }
    }

    function addBlockFieldEventListeners(block, index) {
        const publishButton = block.querySelector('.happening-publish-block');
        const saveDraftButton = block.querySelector('.happening-save-draft');
        const removeButton = block.querySelector('.happening-remove-block');

        if (publishButton) {
            publishButton.addEventListener('click', function() {
                saveBlockMeta(index, 'published');
            });
        }

        if (saveDraftButton) {
            saveDraftButton.addEventListener('click', function() {
                saveBlockMeta(index, 'draft');
            });
        }

        if (removeButton) {
            removeButton.addEventListener('click', function() {
                removeBlock(index);
            });
        }

        // Auto-save on field change with debouncing
        const inputs = block.querySelectorAll('.happening-block-header, .happening-block-content, .happening-block-date, .happening-block-time, .happening-block-status');
        inputs.forEach(function(input) {
            input.addEventListener('change', function() {
                // Debounce auto-save
                clearTimeout(input.autoSaveTimeout);
                input.autoSaveTimeout = setTimeout(function() {
                    saveBlockMeta(index, 'draft');
                }, 1000);
            });
        });
    }

    function saveBlockMeta(blockIndex, status = 'draft') {
        const block = document.querySelectorAll('.wp-block')[blockIndex];
        if (!block) return;

        const headerInput = block.querySelector('.happening-block-header');
        const contentInput = block.querySelector('.happening-block-content');
        const dateInput = block.querySelector('.happening-block-date');
        const timeInput = block.querySelector('.happening-block-time');
        const statusInput = block.querySelector('.happening-block-status');

        const blockData = {
            header: headerInput ? headerInput.value : '',
            content: contentInput ? contentInput.value : '',
            date: dateInput ? dateInput.value : '',
            time: timeInput ? timeInput.value : '',
            status: status,
            timestamp: new Date().toISOString()
        };

        // Update the global meta object
        if (!window.happeningBlockMeta) {
            window.happeningBlockMeta = {};
        }
        window.happeningBlockMeta[blockIndex] = blockData;

        // Save via AJAX
        const formData = new FormData();
        formData.append('action', 'save_happening_block_meta');
        formData.append('nonce', happeningBlockEditor.nonce);
        formData.append('post_id', happeningBlockEditor.postId);
        formData.append('block_data', JSON.stringify(window.happeningBlockMeta));
        formData.append('status', status);

        fetch(happeningBlockEditor.ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSaveMessage(block, `Block ${status === 'published' ? 'published' : 'saved'}!`);
            } else {
                showSaveMessage(block, 'Error saving block', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving block meta:', error);
            showSaveMessage(block, 'Error saving block', 'error');
        });
    }

    function removeBlock(blockIndex) {
        if (confirm('Are you sure you want to remove this block? This action cannot be undone.')) {
            const block = document.querySelectorAll('.wp-block')[blockIndex];
            if (block) {
                // Remove the block from the editor
                const blockId = block.getAttribute('data-block');
                if (blockId) {
                    wp.data.dispatch('core/block-editor').removeBlock(blockId);
                } else {
                    // Fallback: remove the DOM element
                    block.remove();
                }
                
                // Remove from meta data
                if (window.happeningBlockMeta && window.happeningBlockMeta[blockIndex]) {
                    delete window.happeningBlockMeta[blockIndex];
                }
                
                showGlobalMessage('Block removed successfully', 'success');
            }
        }
    }

    function showSaveMessage(block, message, type = 'success') {
        // Remove existing message
        const existingMessage = block.querySelector('.happening-save-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        // Create new message
        const messageElement = document.createElement('div');
        messageElement.className = 'happening-save-message';
        if (type === 'error') {
            messageElement.classList.add('error');
        }
        messageElement.textContent = message;

        const customFields = block.querySelector('.happening-custom-fields');
        if (customFields) {
            customFields.appendChild(messageElement);
        }

        // Remove message after 3 seconds
        setTimeout(function() {
            if (messageElement.parentNode) {
                messageElement.remove();
            }
        }, 3000);
    }

    function initializeExistingBlocks() {
        // Wait a bit for blocks to be rendered
        setTimeout(function() {
            const blocks = document.querySelectorAll('.wp-block');
            blocks.forEach(function(block, index) {
                addCustomFieldsToBlock(block, index);
            });
        }, 1000);
    }

    function addCustomFieldsToExistingBlocks() {
        // Wait a bit for blocks to be rendered
        setTimeout(function() {
            const blocks = document.querySelectorAll('.wp-block');
            blocks.forEach(function(block, index) {
                addCustomFieldsToBlock(block, index);
            });
        }, 1000);
    }

    function monitorForNewBlocks() {
        // Use MutationObserver to watch for new blocks
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        // Check if this is a new block
                        if (node.classList && node.classList.contains('wp-block')) {
                            const blocks = document.querySelectorAll('.wp-block');
                            const index = Array.from(blocks).indexOf(node);
                            addCustomFieldsToBlock(node, index);
                        }
                        
                        // Also check for blocks added within the node
                        const newBlocks = node.querySelectorAll ? node.querySelectorAll('.wp-block') : [];
                        newBlocks.forEach(function(block) {
                            const blocks = document.querySelectorAll('.wp-block');
                            const index = Array.from(blocks).indexOf(block);
                            addCustomFieldsToBlock(block, index);
                        });
                    }
                });
            });
        });

        const editorContainer = document.querySelector('.editor-styles-wrapper') || 
                               document.querySelector('.wp-block-editor') || 
                               document.querySelector('.block-editor-block-list__layout');
        
        if (editorContainer) {
            observer.observe(editorContainer, {
                childList: true,
                subtree: true
            });
        }
    }

    function overrideBlockInserterUI() {
        // Override the block inserter button behavior
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        // Find block inserter buttons
                        const inserterButtons = node.querySelectorAll('.block-editor-inserter__toggle, .block-editor-button-block-appender');
                        inserterButtons.forEach(function(button) {
                            if (!button.dataset.happeningModified) {
                                button.dataset.happeningModified = 'true';
                                
                                // Override click behavior
                                const originalClick = button.onclick;
                                button.onclick = function(e) {
                                    // Set a flag to indicate this is a new block insertion
                                    window.happeningNewBlockInsertion = true;
                                    
                                    if (originalClick) {
                                        originalClick.call(this, e);
                                    }
                                };
                            }
                        });
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Additional override for the block inserter menu
    function overrideBlockInserterMenu() {
        // Override the block inserter menu to always insert at the top
        const originalInsertBlock = wp.data.dispatch('core/block-editor').insertBlock;
        
        // Hook into the block inserter menu
        document.addEventListener('click', function(e) {
            if (e.target.closest('.block-editor-inserter__menu-item')) {
                // Set a flag to indicate this is a new block insertion
                window.happeningNewBlockInsertion = true;
            }
        });
    }

    // Initialize the block inserter menu override
    setTimeout(overrideBlockInserterMenu, 2000);

})(); 