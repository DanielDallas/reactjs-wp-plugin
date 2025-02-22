<?php
/*
Plugin Name: WP ReactJS Component
Description: Add React components to WordPress with shortcodes
Version: 1.1.5
Author: <a href="https://thedanieldallas.com" target="_blank">Daniel Dallas</a>

*/

// Create database table on plugin activation
function react_plugin_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'react_components';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        shortcode varchar(255) NOT NULL,
        code text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'react_plugin_activate');

// Admin Menu Setup
function react_plugin_admin_menu() {
    // Main menu item
    add_menu_page(
        'React Components',
        'React Components',
        'manage_options',
        'react-components',
        'react_plugin_list_components',
        'dashicons-layout'
    );
    
    // Submenu items
    add_submenu_page(
        'react-components',
        'All Components',
        'All Components',
        'manage_options',
        'react-components',
        'react_plugin_list_components'
    );
    
    add_submenu_page(
        'react-components',
        'Add New Component',
        'Add New Component',
        'manage_options',
        'add-react-component',
        'react_plugin_add_component'
    );
}
add_action('admin_menu', 'react_plugin_admin_menu');

// List Components Page
function react_plugin_list_components() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'react_components';
    
    // Handle component deletion
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $wpdb->delete($table_name, array('id' => $id));
        echo '<div class="notice notice-success is-dismissible"><p>Component deleted successfully!</p></div>';
    }
    
    $components = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A);
    
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">React Components</h1>
        <a href="<?php echo admin_url('admin.php?page=add-react-component'); ?>" class="page-title-action">Add New</a>
        
        <div style="display: flex; gap: 20px; margin-top: 20px;">
            <!-- Left Column (75%) - Component List -->
            <div style="width: 75%;">
                <?php if (empty($components)): ?>
                    <div class="notice notice-info">
                        <p>No components found. <a href="<?php echo admin_url('admin.php?page=add-react-component'); ?>">Create your first component</a>.</p>
                    </div>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Shortcode</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($components as $component): ?>
                            <tr>
                                <td><?php echo esc_html($component['title']); ?></td>
                                <td>
                                    <code>[react_component id="<?php echo esc_attr($component['shortcode']); ?>"]</code>
                                    <button class="button button-small copy-shortcode" 
                                            data-shortcode='[react_component id="<?php echo esc_attr($component['shortcode']); ?>"]'>
                                        Copy
                                    </button>
                                </td>
                                <td><?php echo esc_html($component['created_at']); ?></td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=add-react-component&edit=' . $component['id']); ?>" 
                                       class="button button-small">Edit</a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=react-components&action=delete&id=' . $component['id']), 'delete_component'); ?>" 
                                       class="button button-small button-link-delete"
                                       onclick="return confirm('Are you sure you want to delete this component?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
            <!-- Right Column (25%) - Support Info -->
            <div style="width: 25%; background: #f8f8f8; padding: 15px; border-radius: 5px; border: 1px solid #ddd; height: fit-content;">
                <h3>Plugin Support</h3>
                <p>If you need help, please contact:</p>
                <ul style="padding-left: 15px;">
                    <li>Email: <a href="mailto:support@thedanieldallas.com">support@thedanieldallas.com</a></li>
                    <li>Website: <a href="https://thedanieldallas.com" target="_blank">thedanieldallas.com</a></li>
                </ul>
                <!--Get the plugin version from the plugin header-->
    <?php $plugin_data = get_plugin_data(__FILE__);
    $plugin_version = $plugin_data['Version'];?>
    <p><strong>Version:</strong> <?php echo esc_html($plugin_version); ?></p>
                
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
                    <h4>Quick Tips</h4>
                    <ul style="padding-left: 15px;">
                        <li>Use shortcodes in posts, pages, or widgets</li>
                        <li>Components are isolated for better performance</li>
                        <li>Click "Copy" to copy shortcodes easily</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Add/Edit Component Page
function react_plugin_add_component() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'react_components';
    $message = '';
    $message_type = '';
    $component = null;
    
    // Load existing component for editing
    if (isset($_GET['edit'])) {
        $component = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_GET['edit']),
            ARRAY_A
        );
    }
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['component_title'])) {
        $title = sanitize_text_field($_POST['component_title']);
        $code = stripslashes($_POST['component_code']);
        $shortcode = sanitize_title($title);
        
        if (isset($_POST['component_id'])) {
            // Update existing component
            $result = $wpdb->update(
                $table_name,
                array(
                    'title' => $title,
                    'code' => $code,
                    'shortcode' => $shortcode
                ),
                array('id' => $_POST['component_id'])
            );
            
            if ($result !== false) {
                $message = 'Component updated successfully!';
                $message_type = 'success';
                $component = array(
                    'id' => $_POST['component_id'],
                    'title' => $title,
                    'code' => $code
                );
            }
        } else {
            // Insert new component
            $result = $wpdb->insert(
                $table_name,
                array(
                    'title' => $title,
                    'code' => $code,
                    'shortcode' => $shortcode
                )
            );
            
            if ($result) {
                $new_id = $wpdb->insert_id;
                $message = 'Component added successfully!';
                $message_type = 'success';
                $component = array(
                    'id' => $new_id,
                    'title' => $title,
                    'code' => $code
                );
            }
        }
        
        if (!$result) {
            $message = 'Error saving component: ' . $wpdb->last_error;
            $message_type = 'error';
        }
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo $component ? 'Edit Component' : 'Add New Component'; ?></h1>
        
        <?php if ($message): ?>
            <div class="notice notice-<?php echo $message_type; ?> is-dismissible">
                <p><?php echo esc_html($message); ?></p>
            </div>
        <?php endif; ?>
        
        <div style="display: flex; gap: 20px; margin-top: 20px;">
            <!-- Left Column (75%) - Component Editor -->
            <div style="width: 75%;">
                <form method="post">
                    <?php if ($component): ?>
                        <input type="hidden" name="component_id" value="<?php echo esc_attr($component['id']); ?>">
                    <?php endif; ?>
                    
                    <table class="form-table">
                        <tr>
                            <th><label for="component_title">Component Title</label></th>
                            <td>
                                <input type="text" 
                                       name="component_title" 
                                       id="component_title" 
                                       class="regular-text" 
                                       value="<?php echo $component ? esc_attr($component['title']) : ''; ?>" 
                                       required>
                                <p class="description">This will be used to generate the shortcode.</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="component_code">Component Code</label></th>
                            <td>
                                <textarea id="component_code" 
                                          name="component_code" 
                                          rows="20" 
                                          class="large-text code" 
                                          required><?php 
                                    echo $component ? esc_textarea($component['code']) : "function ReactComponent() {\n  return React.createElement('div', {}, 'Hello from React!');\n}";
                                ?></textarea>
                                <p class="description">Write your React component code here. Must include a function named 'ReactComponent'.</p>
                            </td>
                        </tr>
                    </table>
                    
                    <?php submit_button($component ? 'Update Component' : 'Add Component'); ?>
                </form>
            </div>
            
            <!-- Right Column (25%) - Support Info -->
            <div style="width: 25%; background: #f8f8f8; padding: 15px; border-radius: 5px; border: 1px solid #ddd; height: fit-content;">
                <h3>Component Guidelines</h3>
                <p>Follow these guidelines when creating components:</p>
                <ul style="padding-left: 15px;">
                    <li>Use meaningful component names</li>
                    <li>Always return a single root element</li>
                    <li>Keep components focused and reusable</li>
                    <li>Test your component before saving</li>
                </ul>
                
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
                    <h4>Example Component</h4>
                    <pre style="background: #fff; padding: 10px; border-radius: 4px; font-size: 12px;">function ReactComponent() {
  return React.createElement(
    'div',
    { className: 'my-component' },
    'Hello from React!'
  );
}</pre>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Register shortcode
function react_plugin_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => ''
    ), $atts);
    
    if (empty($atts['id'])) {
        return '';
    }
    
    return sprintf('<div class="react-component" data-shortcode="%s"></div>', esc_attr($atts['id']));
}
add_shortcode('react_component', 'react_plugin_shortcode');

// Enqueue scripts
function enqueue_react_plugin_scripts() {
    wp_enqueue_script('react', 'https://unpkg.com/react@18/umd/react.development.js', array(), null, true);
    wp_enqueue_script('react-dom', 'https://unpkg.com/react-dom@18/umd/react-dom.development.js', array('react'), null, true);
    wp_enqueue_script('react-plugin-script', plugin_dir_url(__FILE__) . 'reactjs-wp-plugin.js', array('react', 'react-dom'), null, true);
    
    // Pass all components to JS
    global $wpdb;
    $table_name = $wpdb->prefix . 'react_components';
    $components = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    wp_localize_script('react-plugin-script', 'reactPluginData', array('components' => $components));
}
add_action('wp_enqueue_scripts', 'enqueue_react_plugin_scripts');

// Enqueue admin scripts 
function enqueue_react_plugin_admin_scripts($hook) {
    if (!in_array($hook, array('react-components_page_add-react-component', 'toplevel_page_react-components'))) {
        return;
    }
    
    wp_enqueue_code_editor(array('type' => 'text/javascript'));
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');
    wp_enqueue_script('react-admin-script', plugin_dir_url(__FILE__) . 'admin-editor.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_react_plugin_admin_scripts');