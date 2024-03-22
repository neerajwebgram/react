<?php
/*
Plugin Name: Image Slider (Lite)
Plugin URI: https://ghozylab.com/plugins/
Description: Image Slider (Lite) - Displaying your image as slider in post/page/widget/sidebar area with very easy.<a href="https://demo.ghozylab.com/plugins/easy-image-slider-plugin/pricing/" target="_blank"><strong> Upgrade to Pro Version Now</strong></a> and get a tons of awesome features.
Author: Image Slider Team - GhozyLab
Text Domain: image-slider-widget
Domain Path: /languages
Version: 1.1.125
Author URI: https://ghozylab.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Please do not load this file directly!' );
}

/*
|--------------------------------------------------------------------------
| Requires Wordpress Version
|--------------------------------------------------------------------------
 */
function ewic_wordpress_version()
{

    require_once ABSPATH.'/wp-admin/includes/plugin.php';

    $plugin = plugin_basename( __FILE__ );

    if ( version_compare( get_bloginfo( 'version' ), '3.5', '<' ) ) {

        if ( is_plugin_active( $plugin ) ) {
            deactivate_plugins( $plugin );
            wp_die( "This plugin requires WordPress 3.5 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>" );
        }

    }

}

/*-------------------------------------------------------------------------------*/
/*   MAIN DEFINES
/*-------------------------------------------------------------------------------*/

define( 'EWIC_API_URLCURL', 'https://secure.ghozylab.com/' );
define( 'EWIC_API_URL', 'https://secure.ghozylab.com/' );

if ( ! defined( 'EWIC_PLUGIN_SLUG' ) ) {
    define( 'EWIC_PLUGIN_SLUG', 'image-slider-widget/easy-slider-widget-lite.php' );
}

// Plugin Version
if ( ! defined( 'EWIC_VERSION' ) ) {
    define( 'EWIC_VERSION', '1.1.125' );
}

// Plugin Name
if ( ! defined( 'EWIC_NAME' ) ) {
    define( 'EWIC_NAME', 'Image Slider (Lite)' );
}

// Pro Price
if ( ! defined( 'EWIC_PRO' ) ) {
    define( 'EWIC_PRO', '19' );
}

// Pro+
if ( ! defined( 'EWIC_PROPLUS' ) ) {
    define( 'EWIC_PROPLUS', '27' );
}

// Pro++ Price
if ( ! defined( 'EWIC_PROPLUSPLUS' ) ) {
    define( 'EWIC_PROPLUSPLUS', '35' );
}

// Dev Price
if ( ! defined( 'EWIC_DEV' ) ) {
    define( 'EWIC_DEV', '99' );
}

// PHP Version
if ( version_compare( PHP_VERSION, '7.1', '>' ) ) {
    define( 'EWIC_PHP7', true );
} else {
    define( 'EWIC_PHP7', false );
}

// plugin path
if ( ! defined( 'EWIC_PLUGIN_BASENAME' ) ) {
    define( 'EWIC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'EWIC_PLUGIN_NAME' ) ) {
    define( 'EWIC_PLUGIN_NAME', trim( dirname( EWIC_PLUGIN_BASENAME ), '/' ) );
}

if ( ! defined( 'EWIC_PLUGIN_DIR' ) ) {
    define( 'EWIC_PLUGIN_DIR', WP_PLUGIN_DIR.'/'.EWIC_PLUGIN_NAME.'/' );
}

// plugin url
if ( ! defined( 'EWIC_URL' ) ) {
    $en_plugin_url = substr( plugin_dir_url( __FILE__ ), 0, -1 );
    define( 'EWIC_URL', $en_plugin_url );
}

// plugin url
if ( ! defined( 'EWIC_URL_FILE' ) ) {
    define( 'EWIC_URL_FILE', plugin_dir_url( __FILE__ ) );
}

// All Actions and filters
register_activation_hook( __FILE__, 'ewic_plugin_activate' );
add_action( 'plugins_loaded', 'ewic_first_load' );
add_action( 'admin_init', 'ewic_plugin_updater', 0 );
add_action( 'init', 'ewic_init' );
add_action( 'admin_init', 'ewic_wordpress_version' );
add_action( 'admin_menu', 'ewic_rename_submenu' );
add_filter( 'widget_text', 'do_shortcode', 11 );
add_filter( 'post_row_actions', 'ewic_remove_row_actions', 10, 2 );
add_filter( 'manage_edit-easyimageslider_columns', 'easyimageslider_edit_columns' );
add_filter( 'manage_posts_custom_column', 'easyimageslider_columns_edit_columns_list', 10, 2 );
add_filter( 'plugin_action_links', 'ewic_settings_link', 10, 2 );
add_filter( 'plugin_row_meta', 'ewic_settings_link_rowmeta', 10, 2 );
add_action( 'current_screen', 'ewic_current_screen' );

function ewic_first_load()
{

    load_plugin_textdomain( 'image-slider-widget', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );

    include_once dirname( __FILE__ ).'/inc/ewic-widget.php';

    if ( ewic_is_gutenberg() ) {
        include_once plugin_dir_path( __FILE__ ).'inc/ewic-block/init.php';
    }

}

function ewic_init()
{

    // Create custom postType
    ewic_post_type();

/*-------------------------------------------------------------------------------*/
    /*  Global Includes
    /*-------------------------------------------------------------------------------*/
    include_once dirname( __FILE__ ).'/inc/functions/ewic-functions.php';
    include_once dirname( __FILE__ ).'/inc/ewic-shortcode.php';

/*-------------------------------------------------------------------------------*/

/* Admin includes
/*-------------------------------------------------------------------------------*/
    if ( is_admin() ) {

        include_once dirname( __FILE__ ).'/inc/tinymce_plugin/register_mce_button.php';
        include_once dirname( __FILE__ ).'/inc/ewic-tinymce.php';
        include_once dirname( __FILE__ ).'/inc/ewic-metaboxes.php';
        include_once dirname( __FILE__ ).'/inc/pages/ewic-freeplugins.php';
        include_once dirname( __FILE__ ).'/inc/pages/ewic-featured.php';
        include_once dirname( __FILE__ ).'/inc/pages/ewic-pricing.php';
        include_once dirname( __FILE__ ).'/inc/pages/ewic-settings.php';
        include_once dirname( __FILE__ ).'/inc/pages/ewic-freethemes.php';
        include_once dirname( __FILE__ ).'/inc/ewic-notice.php';
        include_once dirname( __FILE__ ).'/inc/pages/ewic-welcome.php';

    }

    if ( ! is_admin() ) {
        wp_enqueue_script( 'jquery' );
    }

}

/*-------------------------------------------------------------------------------*/
/*   Registers custom post type
/*-------------------------------------------------------------------------------*/
function ewic_post_type()
{

    $labels = array(
        'name'               => _x( 'Image Sliders', 'post type general name', 'image-slider-widget' ),
        'singular_name'      => _x( 'Image Slider', 'post type singular name', 'image-slider-widget' ),
        'add_new'            => __( 'Add New Slider', 'image-slider-widget' ),
        'add_new_item'       => __( 'Slider Item', 'image-slider-widget' ),
        'edit_item'          => __( 'Edit Slider', 'image-slider-widget' ),
        'new_item'           => __( 'New Slider', 'image-slider-widget' ),
        'view_item'          => __( 'View Slider', 'image-slider-widget' ),
        'search_items'       => __( 'Search Slider', 'image-slider-widget' ),
        'not_found'          => __( 'No Slider Found', 'image-slider-widget' ),
        'not_found_in_trash' => __( 'No Slider Found In Trash', 'image-slider-widget' ),
        'parent_item_colon'  => __( 'Parent Slider', 'image-slider-widget' ),
        'menu_name'          => __( 'Image Slider', 'image-slider-widget' ),
    );

    $taxonomies = array();
    $supports   = array( 'title' );

    $post_type_args = array(
        'labels'             => $labels,
        'singular_label'     => __( 'Easy Slider', 'image-slider-widget' ),
        'public'             => false,
        'show_ui'            => true,
        'publicly_queryable' => true,
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'rewrite'            => array( 'slug' => 'easyimagesldr', 'with_front' => false ),
        'supports'           => $supports,
        'menu_position'      => 20,
        'menu_icon'          => plugins_url( 'inc/images/ewic-cp-icon.png', __FILE__ ),
        'taxonomies'         => $taxonomies,
    );

    register_post_type( 'easyimageslider', $post_type_args );

}

/*-------------------------------------------------------------------------------*/
/*  Rename Sub Menu
/*-------------------------------------------------------------------------------*/
function ewic_rename_submenu()
{

    global $submenu;

    if ( is_admin() && current_user_can( 'manage_options' ) ) {
        $submenu['edit.php?post_type=easyimageslider'][5][0] = __( 'Sliders', 'image-slider-widget' );
    }

}

/*-------------------------------------------------------------------------------*/
/*   Hide & Disabled View, Quick Edit and Preview Button
/*-------------------------------------------------------------------------------*/
function ewic_remove_row_actions( $actions )
{

    global $post;

    if ( $post->post_type == 'easyimageslider' ) {
        unset( $actions['view'] );
        unset( $actions['inline hide-if-no-js'] );
    }

    return $actions;

}

/*--------------------------------------------------------------------------------*/
/*  Add Custom Columns for Slider Review Page @since 1.1.11
/*--------------------------------------------------------------------------------*/
function easyimageslider_edit_columns( $easyimageslider_columns )
{

    $easyimageslider_columns = array(
        'cb'          => '<input type="checkbox" />',
        //'title' => _x( 'Sliders', 'post type general name', 'image-slider-widget' ),
        'ewic_ttl'    => __( 'Slider Name', 'image-slider-widget' ),
        'ewic_imgcnt' => __( 'Total Image', 'image-slider-widget' ),
        'ewic_sc'     => __( 'Shortcode', 'image-slider-widget' ).' ( <span style="font-style:italic; font-size:12px;">click to copy</span> )',
        'ewic_id'     => __( 'ID', 'image-slider-widget' ),
        //'ewic_preview' => __( 'Preview', 'image-slider-widget'),
        'ewic_editor' => __( 'Actions', 'image-slider-widget' ),

    );

    return $easyimageslider_columns;

}

function easyimageslider_columns_edit_columns_list( $easyimageslider_columns, $post_id )
{

    if ( is_array( get_post_meta( $post_id, 'ewic_meta_select_images', true ) ) ) {
        $ittl = array_filter( get_post_meta( $post_id, 'ewic_meta_select_images', true ) );
        $ittl = count( $ittl );
    } else {
        $ittl = '0';
    }

    switch ( $easyimageslider_columns ) {

        case 'ewic_ttl':

            echo '<span class="dashicons dashicons-images-alt2" style="margin: 0px 5px 0px 0px;"></span> <strong>'.strip_tags( get_the_title( $post_id ) ).'</strong>';

            break;

        case 'ewic_imgcnt':

            echo esc_html( $ittl ).' image(s)';

            break;

        case 'ewic_id':

            echo esc_html( $post_id );

            break;

        case 'ewic_sc':

            echo '<input size="27" readonly="readonly" value="[espro-slider id='.esc_attr( $post_id ).']" class="ewic-scode-block" type="text">';

            break;

        /* case 'ewic_preview':

        echo '<a class="button ewicprev" onClick="alert(\'This feature only available in Pro Version.\')"><span class="dashicons dashicons-desktop" style="margin: 4px 5px 0px 0px;"></span>Preview</a>';
        break;*/

        case 'ewic_editor':

            echo '<a class="ewic_tooltips" alt="Edit Slider" href="'.get_edit_post_link( $post_id ).'"><span class="dashicons dashicons-edit ewic_actions"></span></a>'.( current_user_can( 'edit_posts' ) ? '<a class="ewic_tooltips" alt="Duplicate Slider" href="'.admin_url( 'admin-ajax.php?action=ewic_duplicate_slider&amp;post='.esc_attr( $post_id ).'&amp;nonce='.wp_create_nonce( 'ewic-duplicate-nonce' ).'"><span class="dashicons dashicons-admin-page ewic_actions"></span></a>' ) : '' ).'<a style="cursor:pointer;" class="ewic_tooltips" alt="Preview" onClick="alert(\'This feature only available in Pro Version.\')"><span class="dashicons dashicons-desktop ewic_actions"></span></a><a class="ewic_tooltips delsliders" alt="Delete Slider" href="'.( isset( $_GET['post_status'] ) && $_GET['post_status'] == 'trash' ? get_delete_post_link( $post_id, '', true ) : get_delete_post_link( $post_id ) ).'"><span class="dashicons dashicons-trash ewic_actions"></span></a>';

            break;

        default:
            break;

    }

}

/*-------------------------------------------------------------------------------*/
/*   Redirect to What's New Page
/*-------------------------------------------------------------------------------*/
function ewic_plugin_activate()
{

    add_option( 'activatedewic', 'ewic-activate' );

}

/*-------------------------------------------------------------------------------*/
/*   Auto Update
/*-------------------------------------------------------------------------------*/
function ewic_plugin_updater()
{

    $ewic_auto_updt = get_option( 'ewic-settings-automatic_update' );

    switch ( $ewic_auto_updt ) {

        case 'active':

            if ( ! wp_next_scheduled( 'ewic_auto_update' ) ) {
                wp_schedule_event( time(), 'daily', 'ewic_auto_update' );
            }

            add_action( 'ewic_auto_update', 'plugin_ewic_auto_update' );
            break;

        case 'inactive':
            wp_clear_scheduled_hook( 'ewic_auto_update' );
            break;

        case '':
            wp_clear_scheduled_hook( 'ewic_auto_update' );
            update_option( 'ewic-settings-automatic_update', 'active' );
            break;

        default:
            break;

    }

}

function plugin_ewic_auto_update()
{

    try
    {
        require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH.'wp-admin/includes/misc.php';

        if ( ! defined( 'FS_METHOD' ) ) {
            define( 'FS_METHOD', 'direct' );
        }

        require_once ABSPATH.'wp-includes/update.php';
        require_once ABSPATH.'wp-admin/includes/file.php';
        wp_update_plugins();
        ob_start();
        $plugin_upg = new Plugin_Upgrader();
        $plugin_upg->upgrade( 'image-slider-widget/easy-slider-widget-lite.php' );
        $output = @ob_get_contents();
        @ob_end_clean();
    } catch ( Exception $e ) {}

}

/*-------------------------------------------------------------------------------*/
/*  Plugin Settings Link @since 1.1.63
/*-------------------------------------------------------------------------------*/
function ewic_settings_link( $link, $file )
{

    static $this_plugin;

    if ( ! $this_plugin ) {
        $this_plugin = plugin_basename( __FILE__ );
    }

    if ( $file == $this_plugin ) {
        $settings_link = '<a href="'.admin_url( 'edit.php?post_type=easyimageslider&page=ewic-settings-page' ).'"><span class="ewic_settings_icon dashicons dashicons-admin-generic"></span>&nbsp;'.__( 'Settings', 'image-slider-widget' ).'</a>';
        array_unshift( $link, $settings_link );
    }

    return $link;

}

/*
|--------------------------------------------------------------------------
| Plugin List Menu @since 1.1.85
|--------------------------------------------------------------------------
 */
function ewic_settings_link_rowmeta( $link, $file )
{

    static $this_plugin;

    if ( ! $this_plugin ) {
        $this_plugin = plugin_basename( __FILE__ );
    }

    if ( $file == $this_plugin ) {
        $link[] = '<a href="https://ghozy.link/rs3bq" target="_blank"><span class="dashicons dashicons-heart"></span>&nbsp;'.__( 'Donate', 'image-slider-widget' ).'</a>';
        $link[] = '<a href="https://www.youtube.com/watch?v=-W8u_t05K2Y" target="_blank"><span class="dashicons dashicons-editor-help"></span>&nbsp;'.__( 'Tutorials', 'image-slider-widget' ).'</a>';
        $link[] = '<a href="https://wordpress.org/support/view/plugin-reviews/image-slider-widget?filter=5" target="_blank"><span class="dashicons dashicons-star-filled"></span>&nbsp;'.__( 'Rate Us', 'image-slider-widget' ).'</a>';
    }

    return $link;

}

function ewic_current_screen( $current_screen )
{

    if ( 'plugins' == $current_screen->id ) {
        add_action( 'admin_head', 'ewic_custom_admin_head' );
    }

}

function ewic_custom_admin_head()
{

    echo '<style>.ewic_settings_icon:before {font-size: 20px !important;background-color: rgba(0, 0, 0, 0) !important;padding: 0 !important;box-shadow: none !important;width: 20px !important;height: 20px !important;}.ewic_settings_icon.dashicons {width: 20px !important;height: 20px !important;padding: 0 !important;position: relative;top: 1.3px;}</style>';

}

function ewic_is_gutenberg()
{

    // Gutenberg plugin is installed and activated.
    $gutenberg = ! ( false === has_filter( 'replace_editor', 'gutenberg_init' ) );

    // Block editor since 5.0.
    $block_editor = version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' );

    if ( ! $gutenberg && ! $block_editor ) {
        return false;
    }

    if ( function_exists( 'is_classic_editor_plugin_active' ) && is_classic_editor_plugin_active() ) {
        $editor_option       = get_option( 'classic-editor-replace' );
        $block_editor_active = array(
            'no-replace',
            'block',
        );

        return in_array( $editor_option, $block_editor_active, true );
    }

    return true;

}