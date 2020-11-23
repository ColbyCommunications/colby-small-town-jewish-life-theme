<?php

namespace App;

/**
 * Add <body> classes
 */
add_filter('body_class', function (array $classes) {
    /** Add page slug if it doesn't exist */
    if (is_single() || is_page() && !is_front_page()) {
        if (!in_array(basename(get_permalink()), $classes)) {
            $classes[] = basename(get_permalink());
        }
    }

    /** Add class if sidebar is active */
    if (display_sidebar()) {
        $classes[] = 'sidebar-primary';
    }

    /** Clean up class names for custom templates */
    $classes = array_map(function ($class) {
        return preg_replace(['/-blade(-php)?$/', '/^page-template-views/'], '', $class);
    }, $classes);

    return array_filter($classes);
});

/**
 * Add "… Continued" to the excerpt
 */
add_filter('excerpt_more', function () {
    return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
});

/**
 * Template Hierarchy should search for .blade.php files
 */
collect([
    'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 'home',
    'frontpage', 'page', 'paged', 'search', 'single', 'singular', 'attachment', 'embed'
])->map(function ($type) {
    add_filter("{$type}_template_hierarchy", __NAMESPACE__.'\\filter_templates');
});

/**
 * Render page using Blade
 */
add_filter('template_include', function ($template) {
    collect(['get_header', 'wp_head'])->each(function ($tag) {
        ob_start();
        do_action($tag);
        $output = ob_get_clean();
        remove_all_actions($tag);
        add_action($tag, function () use ($output) {
            echo $output;
        });
    });
    $data = collect(get_body_class())->reduce(function ($data, $class) use ($template) {
        return apply_filters("sage/template/{$class}/data", $data, $template);
    }, []);
    if ($template) {
        echo template($template, $data);
        return get_stylesheet_directory().'/index.php';
    }
    return $template;
}, PHP_INT_MAX);

/**
 * Render comments.blade.php
 */
add_filter('comments_template', function ($comments_template) {
    $comments_template = str_replace(
        [get_stylesheet_directory(), get_template_directory()],
        '',
        $comments_template
    );

    $data = collect(get_body_class())->reduce(function ($data, $class) use ($comments_template) {
        return apply_filters("sage/template/{$class}/data", $data, $comments_template);
    }, []);

    $theme_template = locate_template(["views/{$comments_template}", $comments_template]);

    if ($theme_template) {
        echo template($theme_template, $data);
        return get_stylesheet_directory().'/index.php';
    }

    return $comments_template;
}, 100);


function colby_base_theme_sanitize_radio( $input, $setting ) {

    // Ensure input is a slug.
    $input = sanitize_key( $input );

    // Get list of choices from the control associated with the setting.
    $choices = $setting->manager->get_control( $setting->id )->choices;

    // If the input is a valid key, return it; otherwise, return the default.
    return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
  }

add_action( 'customize_register', function ( $wp_customize ) {
    $wp_customize->add_section( 'colby_base_theme_customize_section' , array(
        'title'      => __( 'Colby Base Theme' ),
        'priority'   => 30,
    ) );

    // Navbar container option
    $wp_customize->add_setting( 'colby_base_theme_nav_container_option', array(
        'capability' => 'edit_theme_options',
        'default' => 'container',
      ) );

    $wp_customize->add_control( 'colby_base_theme_nav_container_option', array(
        'type' => 'radio',
        'section' => 'colby_base_theme_customize_section',
        'label' => __( 'Colby Base Theme Nav Container Options' ),
        'choices' => array(
          'container' => __( 'container' ),
          'container-fluid' => __( 'container-fluid' ),
        ),
    ));

    // Navbar type option
    $wp_customize->add_setting( 'colby_base_theme_nav_type_option', array(
        'capability' => 'edit_theme_options',
        'default' => 'default',
      ) );

    $wp_customize->add_control( 'colby_base_theme_nav_type_option', array(
        'type' => 'radio',
        'section' => 'colby_base_theme_customize_section',
        'label' => __( 'Colby Base Theme Nav Type Options' ),
        'choices' => array(
          'default' => __( 'Default: Dropdown' ),
          'colby-base-theme-slide-menu' => __( 'Slide from Right' ),
        ),
    ));
});
