<?php

/*-------------------------------------------------------------------------------*/
/*  POST/WIDGET SHORTCODE
/*-------------------------------------------------------------------------------*/
function ewic_shortcode( $attsn ) {
	
	extract( shortcode_atts( array(
	'id' => -1,
	'iswidget' => ''
	), $attsn ) );
	
	
	ob_start();
	wp_enqueue_script( 'ewic-prettyphoto' );	
	wp_enqueue_script( 'ewic-flexslider' );
	wp_enqueue_script( 'ewic-bxslider-easing' );
	
	wp_enqueue_style( 'ewic-frontend-css' );
	wp_enqueue_style( 'ewic-flexslider-css' );	
	wp_enqueue_style( 'ewic-prettyphoto-css' );


if ( $id != '' ) {
	
	$finid = explode(",", $id);
	
	$ewicargs = array(
		'post__in' => $finid, 
		'post_type' => 'easyimageslider',
		);
	}   


 
$ewic_query = new WP_Query( $ewicargs );

if ( $ewic_query->have_posts() ):


while ( $ewic_query->have_posts() ) : $ewic_query->the_post();
		
		
		// START GENERATE SLIDER
		require_once 'ewic-template.php';
		$uid = uniqid();
		echo '<div class="flexslider ewic-slider-lite is_on_'.esc_attr( $iswidget ).'" id="ewic-con'.esc_attr( $iswidget.'-'.$id.$uid ).'">';
		echo ewic_generate_slider( $id, $iswidget, $uid );
		echo '</div>';

?>

<?php
endwhile;
else:
echo '<div style="clear: both; display: block; text-align:center; margin-left: auto; margin-right: auto;"><img src="'.plugins_url('images/ajax-loader.gif' , __FILE__).'" width="32" height="32"/></div>'; 

$contnt = ob_get_clean();
return $contnt;  

endif;
wp_reset_postdata();


$content = ob_get_clean();
return $content;
	
}

add_shortcode( 'espro-slider', 'ewic_shortcode' );


?>