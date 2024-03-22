<?php

/*-------------------------------------------------------------------------------*/
/*   Frontend Register JS & CSS
/*-------------------------------------------------------------------------------*/
function ewic_reg_script()
{

    $is_rtl = ( is_rtl() ? '-rtl' : '' );

    wp_register_style( 'ewic-pricing-css', plugins_url( 'css/pricing'.$is_rtl.'.css', dirname( __FILE__ ) ), false, EWIC_VERSION );
    wp_register_style( 'ewic-cpstyles', plugins_url( 'css/funcstyle.css', dirname( __FILE__ ) ), false, EWIC_VERSION, 'all' );
    wp_register_style( 'ewic-sldr', plugins_url( 'css/slider.css', dirname( __FILE__ ) ), false, EWIC_VERSION );
    wp_register_style( 'ewic-colorpicker', plugins_url( 'css/colorpicker.css', dirname( __FILE__ ) ), false, EWIC_VERSION );
    wp_register_style( 'ewic-introcss', plugins_url( 'css/introjs.min.css', dirname( __FILE__ ) ), false, EWIC_VERSION );
    wp_register_script( 'ewic-colorpickerjs', plugins_url( 'js/colorpicker/colorpicker.js', dirname( __FILE__ ) ), false );
    wp_register_script( 'ewic-eye', plugins_url( 'js/colorpicker/eye.js', dirname( __FILE__ ) ), false );
    wp_register_script( 'ewic-utils', plugins_url( 'js/colorpicker/utils.js', dirname( __FILE__ ) ), false );
    wp_register_script( 'ewic-introjs', plugins_url( 'js/jquery/intro.min.js', dirname( __FILE__ ) ), false );
    wp_register_style( 'ewic-tinymcecss', plugins_url( 'css/tinymce.css', dirname( __FILE__ ) ), false, EWIC_VERSION, 'all' );
    wp_register_script( 'ewic-tinymcejs', plugins_url( 'js/tinymce.js', dirname( __FILE__ ) ), false );
    wp_register_style( 'ewic-modal-css', plugins_url( 'css/modal/css/modal.min.css', dirname( __FILE__ ) ), false, EWIC_VERSION );
    wp_register_script( 'ewic-modal-js', plugins_url( 'js/modal/modal.min.js', dirname( __FILE__ ) ) );
    wp_register_script( 'ewic-wnew', plugins_url( 'js/wnew/ewic-wnew.js', dirname( __FILE__ ) ), false, EWIC_VERSION );

}

add_action( 'admin_init', 'ewic_reg_script' );

function ewic_frontend_js()
{

    $is_rtl = ( is_rtl() ? '-rtl' : '' );

    wp_register_script( 'ewic-flexslider', EWIC_URL.'/js/jquery/flexslider/jquery.flexslider-min.js' );
    wp_register_script( 'ewic-bxslider-easing', EWIC_URL.'/js/jquery/jquery.easing.js' );
    wp_register_script( 'ewic-prettyphoto', EWIC_URL.'/js/jquery/prettyphoto/jquery.prettyPhoto.js' );
    wp_register_style( 'ewic-frontend-css', EWIC_URL.'/css/frontend.css' );
    wp_register_style( 'ewic-flexslider-css', EWIC_URL.'/css/flexslider/flexslider'.$is_rtl.'.css' );
    wp_register_style( 'ewic-prettyphoto-css', EWIC_URL.'/css/prettyphoto/css/prettyPhoto.css' );

}

add_action( 'wp_enqueue_scripts', 'ewic_frontend_js' );

/*-------------------------------------------------------------------------------*/
/*   CHECK BROWSER VERSION ( IE ONLY )
/*-------------------------------------------------------------------------------*/
function ewic_check_browser_version_admin( $sid )
{

    if ( is_admin() && get_post_type( $sid ) == 'easyimageslider' ) {

        preg_match( '/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches );

        if ( count( $matches ) > 1 ) {
            $version = explode( '.', $matches[1] );

            switch ( true ) {
                case ( $version[0] <= '8' ):
                    $msg = 'ie8';

                    break;

                case ( $version[0] > '8' ):
                    $msg = 'gah';

                    break;

                default:
            }

            return $msg;
        } else {
            $msg = 'notie';

            return $msg;
        }

    }

}

/*-------------------------------------------------------------------------------*/
/*   Remove Images
/*-------------------------------------------------------------------------------*/
function ewic_img_remove()
{

    check_ajax_referer( 'ewic-remove', 'security' );

    if ( ! isset( $_POST['pstid'] ) || ! current_user_can( 'edit_theme_options' ) ) {

        wp_die();

    } else {
        $thepstid = sanitize_text_field( $_POST['pstid'] );
        update_post_meta( $thepstid, 'ewic_meta_select_images', '' );

        echo '1';

        wp_die();

    }

}

add_action( 'wp_ajax_ewic_img_remove', 'ewic_img_remove' );

/*-------------------------------------------------------------------------------*/
/*   AJAX Get Slider List
/*-------------------------------------------------------------------------------*/
function ewic_grab_slider_list_ajax()
{

    if ( ! isset( $_POST['grabslider'] ) ) {
        wp_die();
    } else {

        $list = array();

        global $post;

        $args = array(
            'post_type'      => 'easyimageslider',
            'order'          => 'ASC',
            'posts_per_page' => -1,
            'post_status'    => 'publish',

        );

        $myposts = get_posts( $args );

        foreach ( $myposts as $post ): setup_postdata( $post );

            $list[$post->ID] = array( 'val' => $post->ID, 'title' => esc_html( esc_js( the_title( null, null, false ) ) ) );

        endforeach;

    }

    echo json_encode( $list ); //Send to Option List ( Array )
    wp_die();

}

add_action( 'wp_ajax_ewic_grab_slider_list_ajax', 'ewic_grab_slider_list_ajax' );

/*-------------------------------------------------------------------------------*/
/*   AJAX Disable/Enable Auto Update
/*-------------------------------------------------------------------------------*/
function ewic_ajax_autoupdt()
{

    check_ajax_referer( 'easywic-lite-nonce', 'security' );

    $thecmd = sanitize_text_field( $_POST['cmd'] );

    if ( ! isset( $thecmd ) ) {
        echo '0';
        wp_die();
    } else {
        update_option( 'ewic-settings-automatic_update', $thecmd );
        echo '1';
        wp_die();
    }

}

add_action( 'wp_ajax_ewic_ajax_autoupdt', 'ewic_ajax_autoupdt' );

/*-------------------------------------------------------------------------------*/
/*  Create Upgrade Metabox
/*-------------------------------------------------------------------------------*/
function ewic_upgrade_metabox()
{
    $enobuy = '<div style="text-align:center;">';
    $enobuy .= '<a class="ewicprcngtableclr" id="ewicprcngtableclr" style="outline: none !important;" href="#"><img class="ewichvrbutton" src="'.plugins_url( 'images/buy-now.png', dirname( __FILE__ ) ).'" width="241" height="95" alt="Buy Now!" ></a>';
    $enobuy .= '</div>';
    echo $enobuy;
}

/*-------------------------------------------------------------------------------*/
/*  Create Pro Demo Metabox
/*-------------------------------------------------------------------------------*/
function ewic_prodemo_metabox()
{
    $enobuy = '<div style="text-align:center;">';
    $enobuy .= '<a id="ewicdemotableclr" style="outline: none !important;" target="_blank" href="https://ghozy.link/9vlg3"><img class="ewichvrbutton" src="'.plugins_url( 'images/view-demo-button.jpg', dirname( __FILE__ ) ).'" width="232" height="60" alt="Demo Pro Version" ></a>';
    $enobuy .= '</div>';
    echo $enobuy;
}

/*-------------------------------------------------------------------------------*/
/*  RENAME POST BUTTON @since 1.1.0
/*-------------------------------------------------------------------------------*/
function ewic_change_publish_button( $translation, $text )
{

    if ( 'easyimageslider' == get_post_type() ) {

        if ( $text == 'Publish' ) {
            return 'Save Slider';
        } else

        if ( $text == 'Update' ) {
            return 'Update Slider';
        }

    }

    return $translation;
}

add_filter( 'gettext', 'ewic_change_publish_button', 10, 2 );

/*-------------------------------------------------------------------------------*/
/*   GENERATE SHARE BUTTONS
/*-------------------------------------------------------------------------------*/
function ewic_share()
{
    ?>
<div style="position:relative; margin-top:6px;">
    <ul class='ewic-social' id='ewic-cssanime'>
        <li class='ewic-facebook'>
            <a onclick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=Check out the Best Image Slider Wordpress Plugin&amp;p[summary]=Best Image Slider Wordpress Plugin is powerful plugin to create image slider in minutes&amp;p[url]=http://demo.ghozylab.com/plugins/easy-image-slider-plugin/&amp;p[images][0]=<?php echo EWIC_URL.'/images/assets/easy-slider-widget-320-200.png'; ?>', 'sharer', 'toolbar=0,status=0,width=548,height=325');"
                href="javascript: void(0)" title="Share"><strong>Facebook</strong></a>
        </li>
        <li class='ewic-twitter'>
            <a onclick="window.open('https://twitter.com/share?text=Best Wordpress Image Slider Plugin &url=http://demo.ghozylab.com/plugins/easy-image-slider-plugin/', 'sharer', 'toolbar=0,status=0,width=548,height=325');"
                title="Twitter" class="circle"><strong>Twitter</strong></a>
        </li>
        <li class='ewic-googleplus'>
            <a
                onclick="window.open('https://plus.google.com/share?url=http://demo.ghozylab.com/plugins/easy-image-slider-plugin/','','width=415,height=450');"><strong>Google+</strong></a>
        </li>
        <li class='ewic-pinterest'>
            <a
                onclick="window.open('http://pinterest.com/pin/create/button/?url=http://demo.ghozylab.com/plugins/easy-image-slider-plugin/;media=<?php echo EWIC_URL.'/images/assets/easy-slider-widget-320-200.png'; ?>;description=Best Image Slider Wordpress Plugin','','width=600,height=300');"><strong>Pinterest</strong></a>
        </li>
    </ul>
</div>

<?php
}

/*-------------------------------------------------------------------------------*/
/*  Update Notify
/*-------------------------------------------------------------------------------*/
function easywic_update_notify()
{

    global $post;

    if ( ! empty( $post ) && 'easyimageslider' === $post->post_type && is_admin() ) {

        ?>
<div class="error ewic-setupdate">
    <p><?php _e( 'We recommend you to enable plugin Auto Update so you will get the latest features and other important updates from <strong>Image Slider (Lite)</strong>.<br />Click <a href="#"><strong><span id="ewicdoautoupdate">here</span></strong></a> to enable Auto Update.', 'image-slider-widget' );?>
    </p>
</div>

<script type="text/javascript">
/*<![CDATA[*/
/* Easy Media Gallery */
jQuery(document).ready(function() {
    jQuery('#ewicdoautoupdate').click(function() {
        var cmd = 'active';
        ewic_enable_auto_update(cmd);
    });

    function ewic_enable_auto_update(act) {
        var data = {
            action: 'ewic_enable_auto_update',
            security: '<?php echo wp_create_nonce( 'ewic-update-nonce' ); ?>',
            cmd: act,
        };

        jQuery.post(ajaxurl, data, function(response) {
            if (response == 1) {
                alert('Great! Auto Update successfully activated.');
                jQuery('.ewic-setupdate').fadeOut('3000');
            } else {
                alert('Ajax request failed, please refresh your browser window.');
            }

        });
    }

});

/*]]>*/
</script>

<?php

    }

}

function ewic_enable_auto_update()
{

    check_ajax_referer( 'ewic-update-nonce', 'security' );

    if ( ! isset( $_POST['cmd'] ) ) {
        echo '0';
        wp_die();
    } else {

        if ( $_POST['cmd'] == 'active' ) {
            $thecmd = sanitize_text_field( $_POST['cmd'] );
            update_option( 'ewic-settings-automatic_update', $thecmd );
            echo '1';
            wp_die();
        }

    }

}

add_action( 'wp_ajax_ewic_enable_auto_update', 'ewic_enable_auto_update' );

/*-------------------------------------------------------------------------------*/
/* Get latest info on What's New page
/*-------------------------------------------------------------------------------*/
function ewic_lite_get_news()
{

    if ( false === ( $cache = get_transient( 'ewiclite_whats_new' ) ) ) {

        $addlist = get_option( 'ewic_active_addons_lite' );

        $url = array(
            'c' => 'news',
            'p' => 'ewiclite',
        );

        $feed = wp_remote_get( 'http://content.ghozylab.com/feed.php?'.http_build_query( $url ).'', array( 'sslverify' => false ) );

        if ( ! is_wp_error( $feed ) ) {

            if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
                $cache = wp_remote_retrieve_body( $feed );
                set_transient( 'ewiclite_whats_new', $cache, 60 );
            }

        } else {
            $cache = '<div class="error"><p>'.__( 'There was an error retrieving the list from the server. Please try again later.', 'image-slider-widget' ).'</div>';
        }

    }

    echo $cache;
}

/*-------------------------------------------------------------------------------*/
/*  Duplicate Slider
/*-------------------------------------------------------------------------------*/
function ewic_duplicate_slider()
{

    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Security Issue!' );
    }

    if ( ! check_ajax_referer( 'ewic-duplicate-nonce', 'nonce' ) && ( isset( $_GET['nonce'] ) && ! wp_verify_nonce( $_GET['nonce'], 'ewic-duplicate-nonce' ) ) ) {
        wp_die( 'Security Issue!' );
    }

    if ( ! ( isset( $_GET['post'] ) || isset( $_POST['post'] ) || ( isset( $_REQUEST['action'] ) && 'ewic_duplicate_slider' == $_REQUEST['action'] ) ) ) {
        wp_die( 'No post to duplicate has been supplied!' );
    }

 	    /*
     * get the original post id
     */
    $post_id = ( isset( $_GET['post'] ) ? sanitize_text_field( wp_unslash( $_GET['post'] ) ) : sanitize_text_field( wp_unslash( $_POST['post'] ) ) );
    $post_id = intval( $post_id );
    /*
     * and all the original post data then
     */
    $post = get_post( $post_id );

    /*
     * if you don't want current user to be the new post author,
     * then change next couple of lines to this: $new_post_author = $post->post_author;
     */
    $current_user    = wp_get_current_user();
    $new_post_author = $current_user->ID;

	/*
	* if post data exists, create the post duplicate
	*/
    if ( isset( $post ) && $post != null ) {

        /*
         * new post data array
         */
        $args = array(
            'comment_status' => $post->comment_status,
            'ping_status'    => $post->ping_status,
            'post_author'    => $new_post_author,
            'post_content'   => $post->post_content,
            'post_excerpt'   => $post->post_excerpt,
            'post_name'      => $post->post_name,
            'post_parent'    => $post->post_parent,
            'post_password'  => $post->post_password,
            'post_status'    => 'draft',
            'post_title'     => 'COPY of '.$post->post_title,
            'post_type'      => $post->post_type,
            'to_ping'        => $post->to_ping,
            'menu_order'     => $post->menu_order,
        );

        /*
         * insert the post by wp_insert_post() function
         */
        $new_post_id = wp_insert_post( $args );

        $data = get_post_custom( $post_id );

        foreach ( $data as $key => $values ) {

            foreach ( $values as $value ) {
                add_post_meta( $new_post_id, $key, maybe_unserialize( $value ) );
            }

        }

		/*
		* finally, redirect to the edit post screen for the new draft
		*/

        if ( wp_get_referer() ) {

            wp_safe_redirect( wp_get_referer() );

        } else {

            wp_redirect( admin_url( 'post.php?action=edit&post='.$new_post_id ) );

        }

        exit;
    } else {
        wp_die( 'Post creation failed, could not find original post: '.$post_id );
    }

}

add_action( 'wp_ajax_ewic_duplicate_slider', 'ewic_duplicate_slider' );

/*-------------------------------------------------------------------------------*/
/*  Admin Bar @since 1.1.73
/*-------------------------------------------------------------------------------*/
function ewic_add_toolbar_items( $admin_bar )
{

    $admin_bar->add_menu( array(
        'id'     => 'ewic-tb-item',
        'title'  => '<span style="padding:5px;margin-left: 5px;margin-right: 5px;color:#fff;background-color: #f44;background-image:-moz-linear-gradient(bottom,#0074A2, #009DD9);
	background-image: -webkit-gradient(linear, left bottom, left top, from(#0074A2), to(#009DD9));"><img src="'.plugins_url( 'images/ewic-cp-icon.png', dirname( __FILE__ ) ).'" style="vertical-align:middle;margin-right:5px" alt="Image Slider Plugin" title="Image Slider Plugin" />UPGRADE IMAGE SLIDER TO PRO</span>',
        'href'   => 'https://ghozylab.com/plugins/ordernow.php?order=eispro&utm_source=adminbar&utm_medium=ewic_adminbar&utm_campaign=ewic_adminbar',
        'target' => '_blank',
        'meta'   => array(
            'title'  => __( 'Upgrade to Pro Version' ),
            'target' => '_blank',
        ),
    ) );

}

add_action( 'admin_head', 'ewic_add_toolbar_items_handler' );

function ewic_add_toolbar_items_handler()
{

    global $current_screen;

    if ( isset( $current_screen ) && 'easyimageslider' == $current_screen->post_type ) {

        add_action( 'admin_bar_menu', 'ewic_add_toolbar_items', 101 );

    }

}

function ewic_get_sliders()
{

    $args = array(
        'post_type'      => 'easyimageslider',
        'order'          => 'ASC',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    );

    $gt = get_posts( $args );

    $sliders = array();

    if ( $gt ) {

        foreach ( $gt as $ce ) {
            $img_count = get_post_meta( $ce->ID, 'ewic_meta_select_images', true );
            $sliders[] = array( 'id' => ''.$ce->ID.'', 'name' => $ce->post_title, 'img_count' => ''.( is_array( $img_count ) ? count( $img_count ) : 0 ).'' );
        }

    }

    return $sliders;

}

function ewic_is_gutenberg_in_widget()
{
	
	$currentScreen = get_current_screen();
	
    $in_widget_page = ( isset( $currentScreen->id ) && $currentScreen->id === 'widgets' ? true : false );
    
    if ( function_exists( 'wp_use_widgets_block_editor' ) && wp_use_widgets_block_editor() && $in_widget_page ) {
        return true;
    }

    return false;

}