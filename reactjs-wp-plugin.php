<?php
/*
Plugin Name: ReactJS Plugin
Description: Display a simple React component in WordPress.
Version: 1.0
Author: Daniel Dallas
*/
// Enqueue scripts
function enqueue_react_plugin_scripts() {
    wp_enqueue_script('react', 'https://unpkg.com/react@18/umd/react.development.js', array(), null, true);
    wp_enqueue_script('react-dom', 'https://unpkg.com/react-dom@18/umd/react-dom.development.js', array('react'), null, true);
    wp_enqueue_script('react-plugin-script', plugin_dir_url(__FILE__) . 'react-plugin.js', array('react', 'react-dom'), null, true);


    function enqueue_codemirror_assets($hook) {
    if ($hook !== 'settings_page_react_component_plugin') {
        return;
    }

    wp_enqueue_code_editor(['type' => 'text/javascript']);
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');

    wp_enqueue_script('react_component_editor', plugins_url('admin-editor.js', __FILE__), ['jquery'], null, true);
}

add_action('admin_enqueue_scripts', 'enqueue_codemirror_assets');


    // Pass React component code from DB to JS
    $react_code = get_option('react_plugin_code', "function ReactComponent() { return React.createElement('h2', {}, 'Hello from React Plugin!'); }");
    wp_localize_script('react-plugin-script', 'reactPluginData', array('code' => $react_code));
}
add_action('wp_enqueue_scripts', 'enqueue_react_plugin_scripts');

// Register shortcode
function react_plugin_shortcode() {
    return '<div id="react-plugin-root"></div>';
}
add_shortcode('reactjs_component', 'react_plugin_shortcode');

// Admin Menu for Editing React Code
function react_plugin_admin_menu() {
    add_menu_page('ReactJS Component', 'ReactJS Component', 'manage_options', 'reactjs-plugin', 'react_plugin_admin_page');
}
add_action('admin_menu', 'react_plugin_admin_menu');

// Admin Page Content
function react_plugin_admin_page() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['react_code'])) {
        update_option('react_plugin_code', stripslashes($_POST['react_code']));
        echo '<div class="updated"><p>React Component Updated!</p></div>';
    }

    $saved_code = get_option('react_plugin_code', "function ReactComponent() { return React.createElement('h2', {}, 'Hello from React Plugin!'); }");

    echo '<div class="wrap">';
    echo '<h1>React Component Editor</h1>';

    // Shortcode display section
    echo '<p><strong>Use this shortcode to display the React component:</strong></p>';
    echo '<code>[react_component]</code>';
    
    // Main container with two columns
    echo '<div style="display: flex; gap: 20px; margin-top: 20px;">';

    // Left Column (Code Editor)
    echo '<div style="width: 75%;">';
    echo '<form method="post">';
    echo '<textarea id="react_component_code" name="react_code" rows="30" style="width: 100%; font-family: monospace; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">' . esc_textarea($saved_code) . '</textarea>';
    echo '<p><input type="submit" value="Save Changes" class="button button-primary"></p>';
    echo '</form>';
    echo '</div>';

    // Right Column (Support & Contact Info)
    echo '<div style="width: 25%; background: #f8f8f8; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">';
    echo '<h3>Plugin Support</h3>';
    echo '<p>If you need help, please contact:</p>';
    echo '<ul style="padding-left: 15px;">';
    echo '<li>Email: <a href="mailto:support@thedanieldallas.com">support@thedanieldallas.com</a></li>';
    echo '<li>Website: <a href="https://thedanieldallas.com" target="_blank">thedanieldallas.com</a></li>';
    echo '<li>GitHub: <a href="https://github.com/danieldallas" target="_blank">github.com/danieldallas</a></li>';
    echo '</ul>';
    echo '<p><strong>Version:</strong> 1.0</p>';
    echo '</div>';

    echo '</div>'; // Close flex container
    echo '</div>'; // Close .wrap
}

?>