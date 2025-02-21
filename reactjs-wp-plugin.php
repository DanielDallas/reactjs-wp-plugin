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
    echo '<h1>React Plugin Editor</h1>';
    echo '<form method="post">';
    echo '<textarea name="react_code" rows="40" style="width:100%;">' . esc_textarea($saved_code) . '</textarea>';
    echo '<p><input type="submit" value="Save Changes" class="button button-primary"></p>';
    echo '</form>';
    echo '</div>';
}
?>