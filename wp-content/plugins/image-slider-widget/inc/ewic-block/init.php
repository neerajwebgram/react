<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.

if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( ! class_exists( 'Ewic_Block' ) ) {

    class Ewic_Block
    {

        public function __construct()
        {

            add_action( 'init', array( $this, 'register_block_action' ) );

        }

        public function register_block_action()
        {

            if ( ! function_exists( 'register_block_type' ) ) {
                return;
            }

            $script_slug       = 'ewic-block-js';
            $style_slug        = 'ewic-block-style-css';
            $editor_style_slug = 'ewic-block-editor-css';

            wp_register_script(
                $script_slug, // Handle.
                plugin_dir_url( __FILE__ ).'/dist/blocks.build.js', // Block.build.js: We register the block here. Built with Webpack.
                array( 'wp-blocks', 'wp-i18n', 'wp-element' ) // Dependencies, defined above.
            );

            // Styles.
            wp_register_style(
                $style_slug, // Handle.
                plugin_dir_url( __FILE__ ).'/dist/blocks.style.build.css', // Block style CSS.
                array( 'wp-blocks' ) // Dependency to include the CSS after it.
            );

            wp_register_style(
                $editor_style_slug, // Handle.
                plugin_dir_url( __FILE__ ).'/dist/blocks.editor.build.css', // Block editor CSS.
                array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
            );

            register_block_type(
                'ewic-slider/block', // Block name with namespace
                array(
                    'style'           => $style_slug, // General block style slug
                    'editor_style' => $editor_style_slug, // Editor block style slug
                    'editor_script' => $script_slug, // The block script slug
                    'attributes' => array(
                        'id' => array(
                            'type'    => 'string',
                            'default' => '',
                        ),
                    ),
                    'render_callback' => array( $this, 'render_callback' ),
                )
            );

            wp_localize_script( 'ewic-block-js', 'ewic_tinymce_vars', array( 'sliders' => ewic_get_sliders() ) );

        }

        public function render_callback( $attributes, $content = null, $context = 'frontend' )
        {

            if ( ! is_admin() && isset( $attributes['id'] ) && $attributes['id'] ) {
                return '[espro-slider id='.$attributes['id'].']';
            }

            return '';

        }

    }

    new Ewic_Block();

}