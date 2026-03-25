<?php
/**
 * Pavilion Codex Bridge - WordPress Integration Settings
 */

/**
 * Add admin menu for Pavilion Codex Bridge
 */
function add_pavilion_codex_bridge_admin_menu()
{
    add_menu_page(
        'Pavilion AI',
        'Pavilion AI',
        'manage_options',
        'pavilion-ai',
        'pavilion_codex_bridge_admin_page',
        'dashicons-cloud-upload',
        30
    );

    add_submenu_page(
        'pavilion-ai',
        'Codex Bridge Settings',
        'Codex Bridge',
        'manage_options',
        'pavilion-ai-bridge',
        'pavilion_codex_bridge_admin_page'
    );
}
add_action('admin_menu', 'add_pavilion_codex_bridge_admin_menu');

/**
 * Admin page for Pavilion Codex Bridge
 */
function pavilion_codex_bridge_admin_page()
{
    // Handle settings update
    if (isset($_POST['save_pavilion_settings']) && current_user_can('manage_options')) {
        check_admin_referer('pavilion_settings_nonce');
        
        update_option('pavilion_integration_mode', sanitize_text_field($_POST['integration_mode']));
        update_option('pavilion_tenant_id', sanitize_text_field($_POST['tenant_id']));
        
        echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully!</p></div>';
    }

    $integration_mode = get_option('pavilion_integration_mode', 'standalone');
    $tenant_id = get_option('pavilion_tenant_id', '');

    ?>
    <div class="wrap">
        <h1>Pavilion Codex Bridge</h1>
        <p>Manage the integration between this WordPress site and the Pavilion AI Platform.</p>

        <form method="post" action="">
            <?php wp_nonce_field('pavilion_settings_nonce'); ?>
            
            <div class="card" style="max-width: 800px; padding: 20px;">
                <h2>Integration Sync Settings</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="integration_mode">Integration Mode</label></th>
                        <td>
                            <select name="integration_mode" id="integration_mode">
                                <option value="standalone" <?php selected($integration_mode, 'standalone'); ?>>Standalone (Local Data Only)</option>
                                <option value="bridge" <?php selected($integration_mode, 'bridge'); ?>>Pavilion Bridge (Hybrid API Sync)</option>
                            </select>
                            <p class="description">Select how data should be handled. "Bridge" mode fetches content dynamically from the Pavilion AI platform.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="tenant_id">Tenant ID</label></th>
                        <td>
                            <input name="tenant_id" type="text" id="tenant_id" value="<?php echo esc_attr($tenant_id); ?>" class="regular-text">
                            <p class="description">Your unique Tenant ID from the Pavilion AI Dashboard.</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button('Save Settings', 'primary', 'save_pavilion_settings'); ?>
            </div>
        </form>

        <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
            <h2>Dynamic Synchronization (Codex Bridge)</h2>
            <p>In <strong>Bridge Mode</strong>, the following components are synchronized automatically:</p>
            <ul>
                <li><strong>Categories:</strong> Mapped to Pavilion Categories.</li>
                <li><strong>Authors:</strong> Mapped to Pavilion Users/Editors.</li>
                <li><strong>Content:</strong> Articles are fetched via the API using the Tenant ID.</li>
            </ul>
        </div>
    </div>
    <?php
}

/**
 * Add Pavilion Metabox to Post Editor
 */
function add_pavilion_post_metabox()
{
    if (get_option('pavilion_integration_mode', 'standalone') === 'bridge') {
        add_meta_box(
            'pavilion_metadata',
            'Pavilion AI Integration',
            'pavilion_post_metabox_callback',
            'post',
            'side',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'add_pavilion_post_metabox');

function pavilion_post_metabox_callback($post)
{
    // Check if this post is a Pavilion article (usually by slug match)
    $article = pavilion_get_article($post->post_name);
    
    if ($article && isset($article['id'])) {
        echo '<p><strong>Status:</strong> Linked to Pavilion AI</p>';
        echo '<p><strong>Pavilion ID:</strong> ' . esc_html($article['id']) . '</p>';
        echo '<p><a href="https://pavilion.ai/editor/' . esc_attr($article['id']) . '" target="_blank" class="button">Edit in Pavilion</a></p>';
        echo '<p class="description">This article is being synchronized via the Codex Bridge.</p>';
    } else {
        echo '<p><strong>Status:</strong> Not linked (Local only)</p>';
        echo '<p class="description">This article exists only on this WordPress site.</p>';
    }
}

/**
 * Add a dedicated Pavilion Dashboard page
 */
function add_pavilion_dashboard_page()
{
    add_submenu_page(
        'pavilion-ai',
        'Pavilion Dashboard',
        'Dashboard',
        'manage_options',
        'pavilion-ai-dashboard',
        'pavilion_dashboard_page_callback'
    );
}
add_action('admin_menu', 'add_pavilion_dashboard_page');

function pavilion_dashboard_page_callback()
{
    $tenant_id = get_option('pavilion_tenant_id', '');
    ?>
    <div class="wrap" style="height: calc(100vh - 120px);">
        <h1>Pavilion AI Dashboard</h1>
        <?php if (!empty($tenant_id)): ?>
            <iframe src="https://pavilion.ai/dashboard?tenant=<?php echo esc_attr($tenant_id); ?>" style="width: 100%; height: 100%; border: 1px solid #ccd0d4; background: #fff;"></iframe>
        <?php else: ?>
            <div class="notice notice-warning"><p>Please configure your <strong>Tenant ID</strong> in the <a href="?page=pavilion-ai-bridge">Codex Bridge settings</a> to view your dashboard.</p></div>
        <?php endif; ?>
    </div>
    <?php
}
