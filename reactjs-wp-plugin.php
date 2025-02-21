<?php
/*
Plugin Name: ReactJS Plugin
Description: A simple React component in WordPress.
Version: 1.0
Author: Daniel Dallas
*/

function enqueue_react_plugin_scripts() {
    wp_enqueue_script('react', 'https://unpkg.com/react@18/umd/react.development.js', array(), null, true);
    wp_enqueue_script('react-dom', 'https://unpkg.com/react-dom@18/umd/react-dom.development.js', array('react'), null, true);
    wp_enqueue_script('react-plugin-script', plugin_dir_url(__FILE__) . 'react-plugin.js', array('react', 'react-dom'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_react_plugin_scripts');

function add_react_div() {
    echo '<div id="react-plugin-root"></div>';
}
// Function to render the React root element
function react_plugin_shortcode() {
    return '<div id="react-plugin-root"></div>';
}
add_shortcode('react_component', 'react_plugin_shortcode');
