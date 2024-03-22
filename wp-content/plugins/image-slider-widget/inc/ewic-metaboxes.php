<?php
/**
 * Add a custom Meta Box
 *
 * @param array $meta_box Meta box input data
 */
 

/*-----------------------------------------------------------------------------------*/
/*  Right Upgrade Metabox
/*-----------------------------------------------------------------------------------*/
function ewic_custom_metabox() {
	add_meta_box( 'ewicdemodiv', __( 'AMAZING Pro Version DEMO' ), 'ewic_prodemo_metabox', 'easyimageslider', 'side', 'default' );
	add_meta_box( 'ewicbuydiv', __( 'Upgrade to Pro Version' ), 'ewic_upgrade_metabox', 'easyimageslider', 'side', 'default' );
}

add_action( 'do_meta_boxes', 'ewic_custom_metabox' );
add_action( "admin_head", 'ewic_admin_head_script' );
add_action( 'admin_enqueue_scripts', 'ewic_load_script', 10, 1 );

function ewic_load_script() {
	
	if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) ) {
		
		if ( get_post_type( get_the_ID() ) == 'easyimageslider' ) {
			
			$is_rtl = ( is_rtl() ? '-rtl' : '' );
			
			wp_enqueue_media();
			wp_enqueue_script( 'ewic-ibutton-js', plugins_url( 'js/jquery/jquery.ibutton.js' , __FILE__ ) );
			wp_enqueue_style( 'ewic-ibutton-css', plugins_url( 'css/ibutton.css' , __FILE__ ), false, EWIC_VERSION );
			wp_enqueue_style( 'ewic-metacss', plugins_url( 'css/metabox'.$is_rtl.'.css' , __FILE__ ), false, '' );
			wp_enqueue_script( 'ewic-metascript', plugins_url( 'js/metabox/metabox.js' , __FILE__ ) );
			wp_enqueue_style( 'ewic-sldr' );	
			wp_enqueue_style( 'ewic-colorpicker' );		
			wp_enqueue_style( 'ewic-introcss' );	
			wp_enqueue_style( 'ewic-modal-css' );
			wp_enqueue_script( 'ewic-colorpickerjs' );
			wp_enqueue_script( 'jquery-ui-slider' );
			wp_enqueue_script( 'ewic-introjs' );
			wp_enqueue_script( 'ewic-modal-js' );
			//wp_enqueue_script( 'ewic-no-toggle', plugins_url( 'js/metabox/no-toggle.js' , __FILE__ ), array('jquery'), 1, true ); // @since 1.1.23
        	//wp_enqueue_script('ewic-no-toggle');
        	wp_enqueue_script('jquery-effects-highlight');
			
			add_action('admin_footer', 'ewic_upgrade_popup' );
					
		}
			
	}
		
}

function ewic_admin_head_script () {
	if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) ) {
		if ( get_post_type( get_the_ID() ) == 'easyimageslider' ) {
			?>
            
			<style type="text/css" media="screen">
				a:focus {box-shadow: none !important; }
				#minor-publishing { display: none !important; }
				.media-toolbar-secondary .spinner { float: left; margin-right: 5px; }
				@media only screen and (min-width: 1150px) {	
		    		#side-sortables.fixed { position: fixed; top: 55px; right: 20px; width: 280px; }
					body.rtl #side-sortables.fixed { position: fixed; top: 55px; right: auto; left: 20px; width: 280px; }
				}
            </style>
			
			<script type="text/javascript">
			/* Javascript/jQuery Code Here */
			jQuery(document).ready(function($) {
				jQuery('.images_list').sortable({
					opacity: 0.6,
					revert: true,
					placeholder: 'ui-sortable-placeholder',
					cursor: 'move',
					handle: '.ewic-shorters',
         			start: function(e, ui ){
             			ui.placeholder.width(ui.helper.outerWidth()-30);
         				},

					});
					
		    	var ewicrevPosition = $('#side-sortables').offset();
		    	$(window).scroll(function(){
			    if($(window).scrollTop() > ewicrevPosition.top)
			    	{
					$('#side-sortables').addClass('fixed');
			    		} 
			    	else 
			    		{
						$('#side-sortables').removeClass('fixed');
			    		}    
		    		});	
					
				});
                
             </script>  
                    
              <?php
              }
		}
} 
 
 
function ewic_add_meta_box( $meta_box )
{
    if ( !is_array( $meta_box ) ) return false;
	
    // Create a callback function
	if ( EWIC_PHP7 ) {
    	$callback = function( $post, $meta_box ) {
			return ewic_create_meta_box( $post, $meta_box["args"] );
		};
	} else {
		$callback = create_function( '$post, $meta_box', 'ewic_create_meta_box( $post, $meta_box["args"] );' );
	}
	
    add_meta_box( $meta_box['id'], $meta_box['title'], $callback, $meta_box['page'], $meta_box['context'], $meta_box['priority'], $meta_box );
}

/**
 * Create content for a custom Meta Box
 *
 * @param array $meta_box Meta box input data
 */
function ewic_create_meta_box( $post, $meta_box )
{

	$ewic_allowedtags = array(
		'a' => array( 'href' => array(), 'title' => array(), 'target' => array(), 'style' => array() ),
		'div' => array( 'class' => array() ),
		'label' => array( 'for' => array() ),
		'th' => array(), 'p' => array(), 'i' => array(), 'strong' => array(), 'span' => array(), 'br' => array(),
		);
	
    if ( !is_array( $meta_box ) ) return false;
    
    if ( isset( $meta_box['description'] ) && $meta_box['description'] != '' ){
    	echo '<p>'. wp_kses( $meta_box['description'], $ewic_allowedtags ) .'</p>';
    }
    
	wp_nonce_field( basename( __FILE__ ), 'ewic_meta_box_nonce' );
	echo '<table class="form-table ewic-metabox-table">';
 
	foreach ( $meta_box['fields'] as $field ){
		// Get current post meta data
		$meta = get_post_meta( $post->ID, $field['id'], true );
		if ( isset( $field['isfull'] ) && $field['isfull'] == 'yes' ) {
			$isfull = '';
		} else {
			$isfull = '<th><label for="'. esc_attr( $field['id'] ) .'"><strong>'. esc_html( $field['name'] ) .'<br></strong><span>'. esc_html( $field['desc'] ) .'</span></label></th>';	
		}
		echo '<tr class="'. esc_attr( $field['id'] ) .'">'.wp_kses( $isfull, $ewic_allowedtags ).'';
		
		switch( $field['type'] ){	
		
			case 'select':
				echo'<td><select style="width:300px;" name="ewic_meta['. esc_attr( $field['id'] ) .']" id="'. esc_attr( $field['id'] ) .'">';
				foreach ( $field['options'] as $key => $option ){
					echo '<option value="' . esc_attr( $option ) . '"';
					if ( $meta ){ 
						if ( $meta == $option ) echo ' selected="selected"'; 
					} else {
						
					}
					echo'>'. esc_html( $option ) .'</option>';
				}
				echo'</select></td>';
				break;
		
			case 'imagepicker':
			
				echo '<td>
				<div style="width:100%;"><span style="display:inline-block;" id="intro1" class="ewic_add_images">Add Images</span><span class="view-switch" style="float:right;" id="ewic-thumb-view"><a title="List Mode" id="ewiclist" class="view-list" href="#"></a><a title="Grid Mode" id="ewicgrid" class="view-grid" href="#"></a></span></div>
				<div id="ewic_images_container">
				<ul data-nonce="'.wp_create_nonce( 'ewic-remove' ).'" data-postid="'.esc_attr( $post->ID ).'" class="images_list ui-sortable">';
				

				if ( is_array( $meta ) ) {
					foreach( $meta as $img_id ) {
						$img_data = get_post( $img_id['images'] );
						$img_url = wp_get_attachment_thumb_url( $img_id['images'] );
						
						echo '
						<li class="ewicthumbhandler" data-attachment_id="'.esc_attr( $img_id['images'] ).'">
							<input type="hidden" name="ewic_meta[ewic_meta_select_images]['.esc_attr( $img_id['images'] ).'][images]" value="'.esc_attr( $img_id['images'] ).'" />
							<div class="ewic-shorters">
							<img src="'.esc_url( $img_url ).'" />
							<span class="ewic-del-images"></span>
							<label for="title-for-'.esc_attr( $img_id['images'] ).'">Title </label>
							<input id="title-for-'.esc_attr( $img_id['images'] ).'" class="images-title" type="text" name="ewic_meta[ewic_meta_select_images]['.esc_attr( $img_id['images'] ).'][ttl]" value="'.esc_attr( $img_id['ttl'] ).'"/></div>
						</li>';			
						}
				} else {echo '<div class="noimgs ewic_noimgs"><span>No images...</span></div>';}
				
				echo '<input type="hidden" id="image_list_mode" name="ewic_meta[ewic_meta_list_mode]" value="'.esc_attr( get_post_meta( $post->ID, 'ewic_meta_list_mode', true ) ).'" />';

				echo '</ul></div></td>';
				
				if ( get_post_meta( $post->ID, 'ewic_meta_list_mode', true ) ) {
				
	?>	
    
				  <script type="text/javascript">
				  /*<![CDATA[*/
				  
				 jQuery(document).ready(function($) {
					 
					 jQuery('#<?php echo get_post_meta( $post->ID, 'ewic_meta_list_mode', true );?>').trigger('click');
					 
				  });				

				  /*]]>*/
                  </script> <?php
				  
				}
				
				break;	
	
	
			case 'slider': 
			echo '<td>';
	?>	
    
				  <script type="text/javascript">
				  /*<![CDATA[*/
				  
				 jQuery(document).ready(function($) { 
				  
/* Slider init */
		jQuery(function() {
	
        jQuery( '#<?php echo esc_html( $field['id'] ); ?>_slider' ).slider({
            range: 'min',
            min: <?php echo esc_html( $field['min'] ); ?>,
            max: <?php echo esc_html( $field['max'] ); ?>,
			<?php if ( $field['usestep'] == '1' ) { ?>
			step: <?php echo esc_html( $field['step'] ); ?>,
			<?php } ?>
            value: '<?php if ( $meta != "") { echo esc_html( $meta ); } else { echo esc_html( $field['std'] ); } ?>',
            slide: function( event, ui ) {
                jQuery( "#<?php echo esc_html( $field['id'] ); ?>" ).val( ui.value );
            	}
        	});
		});
				  
				  });				

				  /*]]>*/
                  </script>   
    
    <div class="ewic_metaslider"><div id="<?php echo esc_attr( $field['id'] ); ?>_slider" ></div><input style="margin-left:10px; margin-right:5px !important; width:40px !important;" name="ewic_meta[<?php echo esc_attr( $field['id'] ); ?>]" id="<?php echo esc_attr( $field['id'] ); ?>" type="text" value="<?php if ( $meta != "") { echo esc_attr( $meta ); } else { echo esc_attr( $field['std'] ); } ?>" /><?php echo esc_html( $field['pixopr'] ); ?></div> 
  
                <?php
			

				echo '</td>';
			    break;
					
				
			case 'radio':
				echo '<td>';
				
				if ( ewic_check_browser_version_admin( get_the_ID() ) != 'ie8' ) {
					foreach ( $field['options'] as $key => $option ){
						echo '<input id="'. esc_attr( $key ) .'" type="radio" name="ewic_meta['. esc_attr( $field['id'] ) .']" value="'. esc_attr( $key ) .'" class="css-checkbox"';
						if ( $meta ){
							if ( $meta == $key ) echo ' checked="checked"'; 
							} else {
								if ( $field['std'] == $key ) echo ' checked="checked"';
								}
								echo ' /><label for="'. esc_attr( $key ) .'" class="css-label">'. esc_html( $option ) .'</label> ';
								}
							}
							
				else {
					foreach ( $field['options'] as $key => $option ){
						echo '<label class="radio-label"><input type="radio" name="ewic_meta['. esc_attr( $field['id'] ) .']" value="'. esc_attr( $key ) .'" class="radio"';
						if ( $meta ){
							if ( $meta == $key ) echo ' checked="checked"';
							} else {
								if ( $field['std'] == $key ) echo ' checked="checked"';
								}
								echo ' /> '. esc_html( $option ) .'</label> ';
								}
							}							
												
				echo '</td>';
				
				break;
				
				
			case 'checkbox':
			    echo '<td>';
			    $val = '';
                if ( $meta ) {
                    if ( $meta == 'on' ) $val = ' checked="checked"';
                } else {
                    if ( $field['std'] == 'on' ) $val = ' checked="checked"';
                }

                echo '<input type="hidden" name="ewic_meta['. esc_attr( $field['id'] ) .']" value="off" />
                <input class="ewicswitch" type="checkbox" id="'. esc_attr( $field['id'] ) .'" name="ewic_meta['. esc_attr( $field['id'] ) .']" value="on"'. esc_attr( $val ) .' /> ';
			    echo '</td>';
			    break;	
				
			case 'customsize':
			
			    echo '<td>';
				
				if ( is_array( $meta ) ) {
					$sw = $meta['width'];
					$sh = $meta['height'];
					} else {
						$sw = $field['stdw'];
						$sh = $field['stdh'];
					}
				
                echo '<div id="cscontw"><strong>Width</strong> <input style="margin-right:5px !important; margin-left:3px; width:43px !important; float:none !important;" name="ewic_meta[ewic_meta_thumbsizelt][width]" id="'. esc_attr( $field['id'] ) .'_w" type="text" value="'. esc_attr( $sw ).'" />  ' .esc_html( $field['pixopr'] ). '</div>

<span id="cssep" style="border-right:solid 1px #CCC;margin-left:9px; margin-right:10px !important; "></span>
 	<div id="csconth"><strong>Height</strong> <input style="margin-left:3px; margin-right:5px !important; width:43px !important; float:none !important;" name="ewic_meta[ewic_meta_thumbsizelt][height]" id="'. esc_attr( $field['id'] ) .'_h" type="text" value="'.esc_attr( $sh ).'" /> ' .esc_html( $field['pixopr'] ). '';
				echo '</div>';
			    echo '</td>';
			    break;
				
	
	
/*-----------------------------------------------------------------------------------*/	
		}
		
		echo '</tr>';
	}
 
	echo '</table>';
}

/*-----------------------------------------------------------------------------------*/
/*	Register related Scripts and Styles
/*-----------------------------------------------------------------------------------*/

	// SELECT MEDIA METABOX
add_action( 'add_meta_boxes', 'ewic_metabox_work' );
function ewic_metabox_work(){

// Image Picker
	    $meta_box = array(
		'id' => 'ewic_meta_images',
		'title' =>  __( 'Select/Upload Images', 'image-slider-widget' ),
		'description' => __( 'Click <strong><i>Add Images</i></strong> button below and select an images that you want to show in your widget area.<br />Press <strong>Ctrl + click on each images</strong> to select multiple images.', 'image-slider-widget' ),
		/*'description' => __( '<span class="ewic-introjs"><span class="ewic-intro-help"></span><a href="javascript:void(0);" onclick="startIntro();">Click Here to learn How to Create Slider</a></span><br /><br /><div class="ewicinfobox">Upgrade to PRO VERSION and you will get awesome slider options like <a href="http://demo.ghozylab.com/content/ewicpro.html?utm_source=procp&utm_medium=settingspage&utm_campaign=gotodemoprocp" target="_blank">this</a>. You will able to create elegant slider like the following example:<ul><li><a href="http://demo.ghozylab.com/plugins/easy-image-slider-plugin/image-slider-with-thumbnails-at-the-bottom/" target="_blank">Image Slider with Thumbnails at The Bottom
</a></li><li><a href="http://demo.ghozylab.com/plugins/easy-image-slider-plugin/image-slider-with-bullet-navigation/" target="_blank">Image Slider with Bullet Navigation
</a></li><li><a href="http://demo.ghozylab.com/plugins/easy-image-slider-plugin/image-slider-with-thumbnails-on-left/" target="_blank">Image Slider with Thumbnails on Left
</a></li><li><a href="http://demo.ghozylab.com/plugins/easy-image-slider-plugin/image-slider-with-thumbnails-on-right/" target="_blank">Image Slider with Thumbnails on Right</a></li></ul></div><br /><br />Click <strong><i>Add Images</i></strong> button below and select an images that you want to show in your widget area.<br />Press <strong>Ctrl + click on each images</strong> to select multiple images.', 'image-slider-widget' ),*/
		'page' => 'easyimageslider',
		'context' => 'normal',
		'priority' => 'default',
		'fields' => array(
		
			array(
		
					'name' => '',
					'isfull' => 'yes',
					'desc' => '',
					'id' => 'ewic_meta_select_images',
					'type' => 'imagepicker',
					'std' => ''
					
				 ),

			)
	);
    ewic_add_meta_box( $meta_box );
	

// Config 	
	    $meta_box = array(
		'id' => 'ewic_meta_settings',
		'title' =>  __( 'Settings', 'image-slider-widget' ),
		'description' => '<div class="ewicinfobox">Try Pro Version Directly from Your Site 100% free!<a style="display:block;margin-top:5px;" href="https://trial.ghozylab.com/?product=image-slider-pro" target="_blank">DOWNLOAD NOW</a></div>',
		'page' => 'easyimageslider',
		'context' => 'normal',
		'priority' => 'default',
		'fields' => array(
		
			array(
					'name' => __( 'Slider Size', 'image-slider-widget' ),
					'desc' => __( 'Use this option to set Slider custom width and height. We recommend to set AUTO for height.', 'image-slider-widget' ),
					'id' => 'ewic_meta_thumbsizelt',
					'type' => 'customsize',
					'width' => 'tw',
					'height' => 'th',
					'stdw' => 'auto',
					'stdh' => 'auto',					
					"pixopr" => 'px',
					),
				 
			array(
					'name' => __( 'Auto Slide', 'image-slider-widget' ),
					'desc' => __( 'If ON slides will automatically transition / play.', 'image-slider-widget' ),
					'id' => 'ewic_meta_slide_auto',
					'type' => 'checkbox',
					'std' => 'on'
					),	
				 
			array(
					'name' => __( 'Slider Delay', 'image-slider-widget' ),
					'desc' => __( 'The amount of time (in sec) between each auto transition. Default : 5 sec', 'image-slider-widget' ),
					'id' => 'ewic_meta_slide_delay',
					'type' => 'slider',
					'std' => '5',
					'max' => '60',
					'min' => '1',
					'step' => '1',
					'usestep' => '1',
					'pixopr' => 'seconds',
					),	
					
			array(
					'name' => __( 'Slider Effect / Easing', 'image-slider-widget' ),
					'isfull' => 'no',
					'desc' => __( 'Choose an entrance animation or effect and pass it to the slider. Default : easeInOutCubic', 'image-slider-widget' ),
					'id' => 'ewic_meta_settings_effect',
					'type' => 'select',
					'options' => array( "easeInQuad", "easeOutQuad", "easeInOutQuad", "easeInCubic", "easeOutCubic", "easeInOutCubic", "easeInQuart", "easeOutQuart", "easeInOutQuart", "easeInQuint", "easeOutQuint", "easeInOutQuint", "easeInSine", "easeOutSine", "easeInOutSine", "easeInExpo", "easeOutExpo", "easeInOutExpo", "easeInCirc", "easeOutCirc", "easeInOutCirc", "easeInElastic" , "easeOutElastic", "easeInOutElastic", "easeInBack", "easeOutBack", "easeInOutBack", "easeInBounce", "easeOutBounce", "easeInOutBounce"),
					'std' => 'easeInOutCubic'
				 ),
					
					
			array(
					'name' => __( 'Slider Style', 'image-slider-widget' ),
					'desc' => __( 'You can set the slider style to move vertical or horizontal. Default: Horizontal', 'image-slider-widget' ),
					'id' => 'ewic_meta_slide_style',
					'type' => 'radio',
					'options' => array (	
										'horizontal'=> 'Horizontal',	
										'vertical'=> 'Vertical'),	
					'std' => 'horizontal'
					),
					
			array(
					'name' => __( 'Navigation Button', 'image-slider-widget' ),
					'desc' => __( 'You can set the navigation button style here. Default: Always Show', 'image-slider-widget' ),
					'id' => 'ewic_meta_slide_nav',
					'type' => 'radio',
					'options' => array (	
										'always'=> 'Always Show',	
										'onhover'=> 'On Hover'),	
					'std' => 'always'
					),
					
			array(
					'name' => __( 'Show Titles ( if Available )', 'image-slider-widget' ),
					'desc' => __( 'If ON your image title will appear on the bottom.', 'image-slider-widget' ),
					'id' => 'ewic_meta_slide_title',
					'type' => 'checkbox',
					'std' => 'on'
					),	
					
			array(
					'name' => __( 'Smart Title', 'image-slider-widget' ),
					'desc' => __( 'If ON the plugin will automatically convert uppercase the first character of each word and replace - with spaces in a title. For example : ferrari-f12-berlinetta-interior will change to Ferrari F12 Berlinetta Interior', 'image-slider-widget' ),
					'id' => 'ewic_meta_settings_smartttl',
					'type' => 'checkbox',
					'std' => 'on'
					),	
					
			array(
					'name' => __( 'Open an Images in a Lightbox', 'image-slider-widget' ),
					'desc' => __( 'If ON, your images will displayed in a lightbox when clicked.', 'image-slider-widget' ),
					'id' => 'ewic_meta_slide_lightbox',
					'type' => 'checkbox',
					'std' => 'on'
					),
					
			array(
					'name' => __( 'Lightbox Slideshow', 'image-slider-widget' ),
					'desc' => __( 'If ON, the lightbox slideshow will play automatically.', 'image-slider-widget' ),
					'id' => 'ewic_meta_slide_lightbox_autoslide',
					'type' => 'checkbox',
					'std' => 'on'
					),
					
			array(
					'name' => __( 'Lightbox Slideshow Interval ', 'image-slider-widget' ),
					'desc' => __( 'The amount of time (in sec) between each slide. Default : 5 sec', 'image-slider-widget' ),
					'id' => 'ewic_meta_slide_lightbox_delay',
					'type' => 'slider',
					'std' => '5',
					'max' => '60',
					'min' => '1',
					'step' => '1',
					'usestep' => '1',
					'pixopr' => 'seconds',
					),	
					

			)
	);
    ewic_add_meta_box( $meta_box );
	
}

//-----------------------------------------------------------------------------------------------------------------

/**
 * Save custom Meta Box
 *
 * @param int $post_id The post ID
 */
function ewic_save_meta_box( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;
	
	if ( !isset( $_POST['ewic_meta'] ) || !isset( $_POST['ewic_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['ewic_meta_box_nonce'], basename( __FILE__ ) ) )
		return;
	
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) ) return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
	}
			
		$temp_key = array();
		$temp_val = array();
		$temp_size = array();
		
		foreach( $_POST['ewic_meta'] as $key => $val ) {
			
			delete_post_meta( $post_id, $key );
			
			if ( is_array( $val ) ) {
				
				switch ( $key ) {
					
					case 'ewic_meta_select_images':
					
						foreach ( $val as $skey => $fvl ) {
							
							$temp_key[$skey] = $skey;
							
							foreach ( $fvl as $sval => $fsval ) {
								
								$temp_val[$sval] = esc_attr( $fsval );
		
							}
							
							$temp_key[$skey] = $temp_val;
						}
						
						$tags = $temp_key;
					
					break;
					
					case 'ewic_meta_thumbsizelt':
					
						foreach ( $val as $vl => $sz ) {
							
							$temp_size[$vl] = esc_attr( $sz );
							$tags = $temp_size;
							
						}
					
					break;
					
					default:
						$tags = sanitize_text_field( $_POST['ewic_meta'][$key] );
					break;

				}
						
			}
			
			else {
				
				$tags = sanitize_text_field( $_POST['ewic_meta'][$key] );
				
			}
			
			// save data
			add_post_meta( $post_id, $key, $tags, true );
			
		}		
		
}
add_action( 'save_post', 'ewic_save_meta_box' );


function ewic_upgrade_popup() {
	
echo '<!-- Modal -->
<div class="modal modalContent" id="myModalupgrade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closeTrigger" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Pricing Table</h4>
            </div>
            <div class="modal-body" style="background-color: #f5f5f5;">
            
           
            <div class="row flat"> <!-- Content Start -->
            
            
              <div class="col-lg-3 col-md-3 col-xs-6">
                <ul class="plan plan1">
                    <li class="plan-name">
                        Pro
                    </li>
                    <li class="plan-price">
                        <strong>$'.EWIC_PRO.'</strong>
                    </li>
                    <li>
                        <strong>1 site</strong>
                    </li>
                    <li class="plan-action">
                        <a href="https://ghozylab.com/plugins/ordernow.php?order=eispro&utm_source=imageslider&utm_medium=editor&utm_campaign=orderfromeditor" target="_blank" class="btn btn-danger btn-lg">BUY NOW</a>
                    </li>
                </ul>
            </div> 
            
              <div class="col-lg-3 col-md-3 col-xs-6"><span class="featured"></span>
                <ul class="plan plan1">
                    <li class="plan-name">
                        Pro+
                    </li>
                    <li class="plan-price">
                        <strong>$'.EWIC_PROPLUS.'</strong>
                    </li>
                    <li>
                        <strong>3 sites</strong>
                    </li>
                    <li class="plan-action">
                        <a href="https://ghozylab.com/plugins/ordernow.php?order=eisproplus&utm_source=imageslider&utm_medium=editor&utm_campaign=orderfromeditor" target="_blank" class="btn btn-danger btn-lg">BUY NOW</a>
                    </li>
                </ul>
            </div> 
            
              <div class="col-lg-3 col-md-3 col-xs-6">
                <ul class="plan plan1">
                    <li class="plan-name">
                        Pro++
                    </li>
                    <li class="plan-price">
                        <strong>$'.EWIC_PROPLUSPLUS.'</strong>
                    </li>
                    <li>
                        <strong>5 sites</strong>
                    </li>
                    <li class="plan-action">
                        <a href="https://ghozylab.com/plugins/ordernow.php?order=eisplusplus&utm_source=imageslider&utm_medium=editor&utm_campaign=orderfromeditor" target="_blank" class="btn btn-danger btn-lg">BUY NOW</a>
                    </li>
                </ul>
            </div> 
            
              <div class="col-lg-3 col-md-3 col-xs-6">
                <ul class="plan plan1">
                    <li class="plan-name">
                        Developer
                    </li>
                    <li class="plan-price">
                        <strong>$'.EWIC_DEV.'</strong>
                    </li>
                    <li>
                        <strong>15 sites</strong>
                    </li>
                    <li class="plan-action">
                        <a href="https://ghozylab.com/plugins/ordernow.php?order=eisdev&utm_source=imageslider&utm_medium=editor&utm_campaign=orderfromeditor" target="_blank" class="btn btn-danger btn-lg">BUY NOW</a>
                    </li>
                </ul>
            </div> 
            
            
            </div><!-- Content End  --> 
            
            </div>
        </div>
    </div>
</div>
    
<!--  END HTML (to Trigger Modal) -->';	
	
	
}

?>