<?php

if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) ) {

    // ADD STYLE & SCRIPT
    add_action( 'admin_head', 'ewic_editor_add_init' );
    function ewic_editor_add_init()
    {

        if ( get_post_type( get_the_ID() ) != 'easyimageslider' ) {

            wp_enqueue_style( 'thickbox' );
            wp_enqueue_script( 'thickbox' );
            wp_enqueue_style( 'ewic-tinymcecss' );
            wp_enqueue_script( 'ewic-tinymcejs' );

            $tinymcedata = array(
                'sc_version' => EWIC_VERSION,
                'sliders'    => ewic_get_sliders(),
            );

            wp_localize_script( 'ewic-tinymcejs', 'ewic_tinymce_vars', $tinymcedata );

        }

    }

    // ADD MEDIA BUTOON
    add_action( 'media_buttons', 'ewic_shortcode_button' );

    function ewic_shortcode_button()
    {

        $img = plugins_url( 'images/ewic-cp-icon.png', __FILE__ );
        printf( '<a class="thickbox button" id="ewic_gut_shorcode-button" title="Shortcode Generator" style="outline: medium none !important; cursor: pointer;" >
			<span style="margin-top: 3px;margin-right: 5px;" class="dashicons dashicons-images-alt2"></span>Image Slider</a>', '#' );

    }

}

// GENERATE POPUP CONTENT
add_action( 'admin_footer', 'ewic_popup_content' );
function ewic_popup_content()
{

    if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) ) {

        if ( get_post_type( get_the_ID() ) != 'easyimageslider' ) {
// START GENERATE POPUP CONTENT
            ?>
<div id="ewicmodal" style="display:none;">
    <div id="tinyewic">
        <form method="post">

            <div class="ewic_input" id="ewictinymce_select_slider_div">
                <label class="label_option_ewic" for="ewictinymce_select_slider">Slider</label>
                <select class="ewic_select" name="ewictinymce_select_slider" id="ewictinymce_select_slider">
                    <option id="selectslider" type="text" value="select">- Select Slider -</option>
                </select>
                <div class="clearfix"></div>
            </div>

            <div class="ewic_button">
                <input type="button" value="Insert Shortcode" name="ewic_insert_scrt" id="ewic_insert_scrt"
                    class="button-secondary" />
                <div class="clearfix"></div>
            </div>
            <div style="border-top: 1px solid #DDD; margin-top:10px; padding: 7px;display:block; width:505px;"></div>
            <div style="display:inline-block;">
                <h4 class="ewic_pro_here">Pro Version DEMO :</h4>
                <ul class="ewic_pro_demo_list">
                    <li><a href="https://ghozy.link/9vlg3" target="_blank">Slider with Bottom Thumbnails</a></li>
                    <li><a href="https://ghozy.link/gtjan" target="_blank">Slider with Right Navigation</a></li>
                    <li><a href="https://ghozy.link/q6zjf" target="_blank">Slider with Left Thumbnails</a></li>
                    <li><a href="https://ghozy.link/g74rv" target="_blank">Slider with Bullet Navigation</a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div style="float: right; position: relative; right:50px; bottom: 0;">
                <img src="<?php echo plugins_url( 'images/pro_version.png', __FILE__ ); ?>" alt="Pro Version"
                    width="130" height="182" style="margin-left:100px;" />
            </div>

        </form>
    </div>
</div>
<?php
}

    }

    //END
}