/**
 * Social Media Link Embedder
 * Automatically converts social media links to embedded content when pasted
 * Supports: Facebook, X (Twitter), YouTube, TikTok, Instagram, LinkedIn, Reddit
 */

(function($) {
    'use strict';

    // Social media platform patterns and embed functions
    const socialMediaPatterns = {
        facebook: {
            patterns: [
                /https?:\/\/(www\.)?facebook\.com\/[^\/]+\/posts\/\d+/i,
                /https?:\/\/(www\.)?facebook\.com\/photo\.php\?fbid=\d+/i,
                /https?:\/\/(www\.)?facebook\.com\/permalink\.php\?story_fbid=\d+/i,
                /https?:\/\/(www\.)?facebook\.com\/groups\/[^\/]+\/posts\/\d+/i
            ],
            embed: function(url) {
                return `<div class="fb-post" data-href="${url}" data-width="500" data-show-text="true"></div>`;
            }
        },
        twitter: {
            patterns: [
                /https?:\/\/(www\.)?(twitter\.com|x\.com)\/[^\/]+\/status\/\d+/i
            ],
            embed: function(url) {
                // Convert x.com to twitter.com for better compatibility
                const twitterUrl = url.replace('x.com', 'twitter.com');
                return `<blockquote class="twitter-tweet" data-theme="light"><a href="${twitterUrl}"></a></blockquote><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>`;
            }
        },
        youtube: {
            patterns: [
                /https?:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/i
            ],
            embed: function(url) {
                let videoId = '';
                if (url.includes('youtu.be/')) {
                    videoId = url.split('youtu.be/')[1].split('?')[0];
                } else if (url.includes('youtube.com/watch?v=')) {
                    videoId = url.split('v=')[1].split('&')[0];
                } else if (url.includes('youtube.com/embed/')) {
                    videoId = url.split('embed/')[1].split('?')[0];
                }
                
                if (videoId) {
                    return `<div class="video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; margin: 20px 0;"><iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe></div>`;
                }
                return null;
            }
        },
        tiktok: {
            patterns: [
                /https?:\/\/(www\.)?(tiktok\.com\/@[^\/]+\/video\/\d+|vm\.tiktok\.com\/[a-zA-Z0-9]+)/i
            ],
            embed: function(url) {
                return `<blockquote class="tiktok-embed" cite="${url}" data-video-id="${url.split('/video/')[1] || ''}" style="max-width: 325px; min-width: 325px;"><section></section></blockquote><script async src="https://www.tiktok.com/embed.js"></script>`;
            }
        },
        instagram: {
            patterns: [
                /https?:\/\/(www\.)?instagram\.com\/p\/[a-zA-Z0-9_-]+\/?/i,
                /https?:\/\/(www\.)?instagram\.com\/reel\/[a-zA-Z0-9_-]+\/?/i
            ],
            embed: function(url) {
                return `<blockquote class="instagram-media" data-instgrm-permalink="${url}" data-instgrm-version="14" style="background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:540px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:16px;"><a href="${url}" style="background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;" target="_blank"><div style="display: flex; flex-direction: row; align-items: center;"><div style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;"></div><div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"><div style="background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;"></div><div style="background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;"></div></div></div><div style="padding: 19% 0;"></div><div style="display:block; height:50px; margin:0 auto 12px; width:50px;"><svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-511.000000, -20.000000)" fill="#000000"><g><path d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.756,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.756,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631"></path></g></g></g></svg></div><div style="padding-top: 8px;"><div style="color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;">View this post on Instagram</div></div><div style="padding: 12.5% 0;"></div><div style="border-top: 1px solid #e6e6e6; margin:26px 0 13px; width:100%;"></div><div style="display: flex; flex-direction: row; margin-top: 13px;"><div style="background-color: #F4F4F4; border-radius: 50%; height: 32.5px; width: 32.5px; flex-shrink: 0; margin-right: 14px;"></div><div style="display: flex; flex-direction: column; justify-content: center; flex-grow: 1; height: 32.5px;"><div style="background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 70%;"></div><div style="background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 40%;"></div></div></div></a><p style="color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;"><a href="${url}" style="color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none;" target="_blank">A post shared by Instagram (@instagram)</a></p></div></blockquote><script async src="//www.instagram.com/embed.js"></script>`;
            }
        },
        linkedin: {
            patterns: [
                /https?:\/\/(www\.)?linkedin\.com\/posts\/[a-zA-Z0-9_-]+/i,
                /https?:\/\/(www\.)?linkedin\.com\/pulse\/[a-zA-Z0-9_-]+/i
            ],
            embed: function(url) {
                return `<iframe src="https://www.linkedin.com/embed/feed/update/urn:li:activity:${url.split('/').pop()}" height="400" width="550" frameborder="0" allowfullscreen="" title="Post from LinkedIn"></iframe>`;
            }
        },
        reddit: {
            patterns: [
                /https?:\/\/(www\.)?reddit\.com\/r\/[^\/]+\/comments\/[a-zA-Z0-9]+\/[^\/]+\/?/i
            ],
            embed: function(url) {
                return `<iframe src="https://www.redditmedia.com/rpan/s/${url.split('/comments/')[1].split('/')[0]}" height="400" width="550" frameborder="0" allowfullscreen="" title="Reddit Post"></iframe>`;
            }
        }
    };

    // Function to detect social media links in text
    function detectSocialMediaLinks(text) {
        const links = [];
        
        // Find all URLs in the text
        const urlRegex = /https?:\/\/[^\s]+/g;
        const urls = text.match(urlRegex);
        
        if (!urls) return links;
        
        urls.forEach(url => {
            for (const [platform, config] of Object.entries(socialMediaPatterns)) {
                for (const pattern of config.patterns) {
                    if (pattern.test(url)) {
                        links.push({
                            url: url,
                            platform: platform,
                            embed: config.embed(url)
                        });
                        break;
                    }
                }
            }
        });
        
        return links;
    }

    // Function to replace links with embeds
    function replaceLinksWithEmbeds(content, links) {
        let newContent = content;
        
        links.forEach(link => {
            if (link.embed) {
                // Replace the URL with the embed code
                newContent = newContent.replace(link.url, link.embed);
            }
        });
        
        return newContent;
    }

    // Function to handle paste events
    function handlePasteEvent(event) {
        const clipboardData = event.clipboardData || window.clipboardData;
        const pastedText = clipboardData.getData('text');
        
        if (!pastedText) return;
        
        // Detect social media links in pasted content
        const socialLinks = detectSocialMediaLinks(pastedText);
        
        if (socialLinks.length > 0) {
            // Prevent default paste behavior
            event.preventDefault();
            
            // Get the target element
            const target = event.target;
            
            // If it's a TinyMCE editor
            if (target.closest('.mce-content-body') || target.classList.contains('mce-content-body')) {
                const editor = tinymce.get(target.id || target.closest('[id]').id);
                if (editor) {
                    // Insert the embed code at cursor position
                    socialLinks.forEach(link => {
                        if (link.embed) {
                            editor.insertContent(link.embed);
                        }
                    });
                }
            } else {
                // For regular textareas or contenteditable
                const selection = window.getSelection();
                if (selection.rangeCount > 0) {
                    const range = selection.getRangeAt(0);
                    range.deleteContents();
                    
                    socialLinks.forEach(link => {
                        if (link.embed) {
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = link.embed;
                            range.insertNode(tempDiv.firstChild);
                        }
                    });
                }
            }
            
            // Show notification
            showEmbedNotification(socialLinks.length);
        }
    }

    // Function to show embed notification
    function showEmbedNotification(count) {
        const notification = document.createElement('div');
        notification.className = 'social-embed-notification';
        notification.innerHTML = `
            <div style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: #009688;
                color: white;
                padding: 15px 20px;
                border-radius: 5px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                z-index: 10000;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                font-size: 14px;
                max-width: 300px;
            ">
                <strong>✓ Auto-embedded ${count} social media link${count > 1 ? 's' : ''}</strong>
                <br>
                <small>Your social media content has been automatically embedded.</small>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }

    // Function to initialize social media embedder for an editor
    function initializeSocialMediaEmbedder(editorElement) {
        if (!editorElement) return;
        
        // Add paste event listener
        editorElement.addEventListener('paste', handlePasteEvent);
        
        // For TinyMCE editors, also listen to the iframe
        if (editorElement.tagName === 'TEXTAREA' && editorElement.id) {
            const editor = tinymce.get(editorElement.id);
            if (editor) {
                editor.on('paste', function(e) {
                    handlePasteEvent(e);
                });
            }
        }
    }

    // Function to initialize for all editors on the page
    function initializeAllEditors() {
        // Regular WordPress post editor
        const postEditor = document.getElementById('content');
        if (postEditor) {
            initializeSocialMediaEmbedder(postEditor);
        }
        
        // LiveBlog WYSIWYG editors
        const liveblogEditors = document.querySelectorAll('.liveblog-wysiwyg-editor');
        liveblogEditors.forEach(editor => {
            initializeSocialMediaEmbedder(editor);
        });
        
        // TinyMCE editors
        if (typeof tinymce !== 'undefined') {
            tinymce.on('AddEditor', function(e) {
                const editor = e.editor;
                if (editor) {
                    editor.on('paste', function(e) {
                        handlePasteEvent(e);
                    });
                }
            });
        }
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        initializeAllEditors();
        
        // Initialize social media embedding for regular WordPress post editor
        initializeSocialMediaEmbedderForPostEditor();
        
        // Re-initialize for dynamically added editors (like in LiveBlog)
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        if (node.classList && node.classList.contains('liveblog-wysiwyg-editor')) {
                            initializeSocialMediaEmbedder(node);
                        }
                        // Check for editors within added nodes
                        const editors = node.querySelectorAll ? node.querySelectorAll('.liveblog-wysiwyg-editor') : [];
                        editors.forEach(editor => {
                            initializeSocialMediaEmbedder(editor);
                        });
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
    
    // Function to initialize social media embedding for regular WordPress post editor
    function initializeSocialMediaEmbedderForPostEditor() {
        // Wait for TinyMCE to be ready
        if (typeof tinymce !== 'undefined') {
            tinymce.on('AddEditor', function(e) {
                const editor = e.editor;
                if (editor && (editor.id === 'content' || editor.id === 'post_content')) {
                    editor.on('paste', function(e) {
                        // Check if social media embedder is available
                        if (window.SocialMediaEmbedder && window.SocialMediaEmbedder.detectSocialMediaLinks) {
                            const clipboardData = e.clipboardData || window.clipboardData;
                            const pastedText = clipboardData.getData('text');
                            
                            if (pastedText) {
                                const socialLinks = window.SocialMediaEmbedder.detectSocialMediaLinks(pastedText);
                                
                                if (socialLinks.length > 0) {
                                    // Prevent default paste behavior
                                    e.preventDefault();
                                    
                                    // Insert the embed code at cursor position
                                    socialLinks.forEach(function(link) {
                                        if (link.embed) {
                                            editor.insertContent(link.embed);
                                        }
                                    });
                                    
                                    // Show notification
                                    showEmbedNotification(socialLinks.length);
                                }
                            }
                        }
                    });
                }
            });
        }
        
        // Also handle the regular textarea
        const postEditor = document.getElementById('content');
        if (postEditor) {
            postEditor.addEventListener('paste', function(e) {
                if (window.SocialMediaEmbedder && window.SocialMediaEmbedder.detectSocialMediaLinks) {
                    const clipboardData = e.clipboardData || window.clipboardData;
                    const pastedText = clipboardData.getData('text');
                    
                    if (pastedText) {
                        const socialLinks = window.SocialMediaEmbedder.detectSocialMediaLinks(pastedText);
                        
                        if (socialLinks.length > 0) {
                            e.preventDefault();
                            
                            // Insert embed code at cursor position
                            const selection = window.getSelection();
                            if (selection.rangeCount > 0) {
                                const range = selection.getRangeAt(0);
                                range.deleteContents();
                                
                                socialLinks.forEach(function(link) {
                                    if (link.embed) {
                                        const tempDiv = document.createElement('div');
                                        tempDiv.innerHTML = link.embed;
                                        range.insertNode(tempDiv.firstChild);
                                    }
                                });
                            }
                            
                            showEmbedNotification(socialLinks.length);
                        }
                    }
                }
            });
        }
    }

    // Expose functions globally for LiveBlog integration
    window.SocialMediaEmbedder = {
        detectSocialMediaLinks: detectSocialMediaLinks,
        replaceLinksWithEmbeds: replaceLinksWithEmbeds,
        initializeSocialMediaEmbedder: initializeSocialMediaEmbedder,
        initializeAllEditors: initializeAllEditors
    };

})(jQuery); 