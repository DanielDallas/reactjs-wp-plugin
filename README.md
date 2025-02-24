# React Plugin for WordPress

## Description

The **React Plugin for WordPress** allows you to embed a React component inside your WordPress site. This plugin dynamically loads React and ReactDOM from a CDN and renders a simple React component inside a specified HTML element. You can control where the component appears using shortcodes, hooks, or manual placement in theme files.

## Features

- Embed a **React component** anywhere in WordPress.
- Use a **shortcode** to insert the component inside posts, pages, or widgets.
- Automatically render the component in **header, footer, or theme templates**.
- Lightweight and fast, using **React from a CDN**.

## Installation

### Method 1: Install via WordPress Plugin Directory (Coming Soon)

_Currently, this plugin is not available in the WordPress Plugin Directory. You must install it manually._

### Method 2: Manual Installation

1. **Download the plugin files**.
2. Extract the folder and **upload it to** `/wp-content/plugins/react-plugin/`.
3. Go to **WordPress Admin > Plugins** and **activate** the plugin.

## How to Use

### 1. Using the Shortcode (Recommended)

You can add the React component **anywhere** using the following shortcode:

```html
[react_component]
```

📌 **Works inside:** Posts, Pages, and Widgets.

### 2. Auto-Render in Footer

The plugin **automatically renders** the React component in the footer of every page. To disable this, comment out the `wp_footer` hook inside `react-plugin.php`.

### 3. Manually Add to Theme Files

If you want to **control where it appears**, add the following line inside any PHP file in your theme:

```php
<?php echo do_shortcode('[react_component]'); ?>
```

📌 **Use in:** `header.php`, `footer.php`, `single.php`, `page.php`, etc.

## Plugin Files Structure

```
react-plugin/
├── react-plugin.php    # Main plugin file
├── react-plugin.js     # React component script
└── README.md           # Documentation
```

## FAQ

### Q: Will this slow down my website?

A: No. The plugin loads React from a **CDN**, reducing load time.

### Q: Can I use my own React components?

A: Yes! create as many react components as you need and add them to your website with the shortcode.

### Q: Does this work with Gutenberg and Elementor?

A: Yes! You can insert `[react_component id="sample-component-id"]` inside a an Elementor built Page or Gutenberg block.

## Support

For issues or feature requests, open a GitHub issue or contact [thedanieldallas.com].

## License

This project is licensed under the **MIT License**. Feel free to modify and use it as needed.
