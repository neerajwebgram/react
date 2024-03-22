<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Please do not load this file directly!' );
}

/* Gutenberg Compatibility */
add_filter( 'mce_external_plugins', 'ewic_tinymce_mceplugin' );
add_action( 'current_screen', 'ewic_gutenberg_shortcode_manager' );

function ewic_gutenberg_shortcode_manager()
{

    if ( function_exists( 'get_current_screen' ) ) {

        global $current_screen;

        if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {

            add_filter( 'mce_buttons', 'ewic_register_mcebuttons', 0 );
            add_action( 'enqueue_block_editor_assets', 'ewic_block_editor_mcebtn_styles' );

        }

    }

}

function ewic_register_mcebuttons( $buttons )
{

    array_push( $buttons, 'ewicicons' );

    return $buttons;

}

//include the tinymce javascript plugin
function ewic_tinymce_mceplugin( $plugin_array )
{

    $plugin_array['ewicicons'] = EWIC_URL.'/inc/tinymce_plugin/ewic_editor_plugin.js';

    return $plugin_array;

}

/**
 * Enqueue block editor style
 */
function ewic_block_editor_mcebtn_styles()
{

    wp_enqueue_style( 'ewic-icon-editor-styles', EWIC_URL.'/inc/tinymce_plugin/ewic_mcebutton_style.css', false, '1.0', 'all' );

}