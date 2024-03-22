<?php

if ( ! defined( 'ABSPATH' ) ) exit;


function ewic_lite_free_themes() {

	$api_params = array(
		'author'    => 'GhozyLab',
		'per_page'  => -1
	);
	 
	$themes_object = ewic_call_wp_api_themes( 'query_themes', $api_params );
	 
	$themes_list = $themes_object->themes;
	
	if ( is_array( $themes_list ) ) {
		
		echo '<div class="theme-list-container ewic-themes-list">';
		echo '<ul class="free-themes-list">';
		
		// Move active theme to the first
		if ( count( $themes_list ) > 1 ) {
			
			foreach ( $themes_list as $key => $details ) {
				
				if ( $details->slug == get_template() ) {
	
					$new_value = $themes_list[$key];
					unset( $themes_list[$key] );
					array_unshift( $themes_list, $new_value );
					
				}
				
			}
		}

		// Let's display theme on the list
		foreach ( $themes_list as $key => $details ) {
			
			echo '<li class="'.( $details->slug == get_template() ? 'current-active-theme ' : '' ).'drop-shadow lifted free-themes-item'.( $key % 2 == 0 ? '' : ' no-left-margin' ).'">';
			echo '<div class="theme-sc"><img src="'.esc_url( $details->screenshot_url ).'"></div>';
			echo '<div class="theme-details-cont"><div class="theme-desc-cont">';
			echo '<h3 class="theme-title">'.esc_html( $details->name ).'</h3><span class="theme-by">by '.esc_html( ucfirst( $details->author ) ).'</span>';
			echo '<div class="theme-desc">'.esc_html( $details->description ).'</div>';
			echo '</div></div>';
			echo '<div class="theme-details-footer">';
			echo '<span class="theme-details-ratings"><span class="dashicons dashicons-star-filled rating-color"></span></span><span class="rating-content">'.esc_html( $details->rating ).'% <span style="font-style:italic;">( '.esc_html( $details->num_ratings ).' likes )</span></span>';
			echo '<span class="theme-details-actions">'.( get_template() != $details->slug && ! ewic_get_installed_themes( $details->slug ) ? '<a class="button-secondary" href="'.esc_url( $details->preview_url ).'" target="_blank">' . esc_html__( 'Preview', 'image-slider-widget' ) . '</a>' : '' ).''.ewic_generate_action_button( $details->slug ).'</span>';
			echo '</div>';
			echo '</li>';
			
		}
		
		echo '</ul>';
		echo '</div>';
	
	}

}

function ewic_call_wp_api_themes( $action, $api_params = array() ) {
	
    $url = 'https://api.wordpress.org/themes/info/1.0/';
    
	if ( $ssl = wp_http_supports( array( 'ssl' ) ) ) {
         $url = set_url_scheme( $url, 'https' );
    }
	
    $args = ( object ) $api_params;
	
    $http_args = array(
        'body' => array(
        	'action' => $action,
        	'timeout' => 15,
        	'request' => serialize( $args )
        )
    );
	
	$cache_key = 'ghozylab_theme_' . md5( serialize( $http_args ) );
	
	if ( false === ( $themes_list = get_transient( $cache_key ) ) ) {
	
		$request = wp_remote_post( $url, $http_args ); 
	 
		if ( is_wp_error( $request ) ) return false;
		
		$themes_list = maybe_unserialize( wp_remote_retrieve_body( $request ) );
		
		if ( ! is_object( $themes_list ) && ! is_array( $themes_list ) )
		
		return new WP_Error( 'theme_api_error', 'An unexpected error has occurred' );
		
		// Set transient for next time... keep it for 24 hours should be good
		set_transient( $cache_key, $themes_list, 60*60*24 );
 
	}
	
	return $themes_list;
	
}

function ewic_generate_action_button( $slug ) {
	
	$is_active = false;
	$is_update = false;
	
	// Check is Active
	if ( $slug == get_template() )
	$is_active = true;
	
	if ( ! ewic_get_installed_themes( $slug ) ) {
		
		return ewic_generate_action_button_link( $slug, 'install', 'Install Now' );
	
	} else {

		if ( $is_active ) {
		
			$themes_info = wp_prepare_themes_for_js( array( wp_get_theme() ) );
		
			foreach ( $themes_info as $theme ) :
		
				if ( $theme['hasUpdate'] )
				$is_update = true;
			
			endforeach;
		
			// If active and need update
			if ( $is_update ) {
				
				return ewic_generate_action_button_link( $slug, 'upgrade', esc_html__( 'Update Now', 'image-slider-widget' ) );
			
			} else {
				
				return ewic_generate_action_button_link( '', '', '', 'is_active' );
				
			}
			
		// Activate theme
		} else {
			
			return ewic_generate_action_button_link( $slug, '', '', 'switch' );
				
		}
	
	}

}

function ewic_generate_action_button_link( $slug, $action, $text, $return = '' ) {
	
	if ( $return == 'is_active' ) {
		
		return '<span class="active-theme-cont"><span class="dashicons dashicons-yes active-theme"></span>'.esc_html__( 'Active Theme', 'image-slider-widget' ).'</span>';
		
	}
	
	if ( $return == 'switch' ) {
		
		return '<a class="switch-theme-now button button-primary" href="'.esc_url_raw( admin_url( 'themes.php' ).'?action=activate&stylesheet='.$slug.'&_wpnonce='.wp_create_nonce( 'switch-theme_' .$slug ).'' ).'">' . esc_html__( 'Activate', 'image-slider-widget' ) . '</a>';
		
	}
	
	if ( $return == '' ) {
	
	$nonce = wp_create_nonce( ''.$action.'-theme_' . $slug );
	$install_url = add_query_arg( array( 'action' => ''.$action.'-theme', 'theme' => $slug, '_wpnonce' => $nonce ), admin_url( 'update.php' ) );
	
	return '<a class="'.$action.'-theme-now button button-'.( $action == 'install' ? 'primary' : 'secondary' ).'" href="'.esc_url_raw( $install_url ).'">' . esc_html( $text ) . '</a>';
	
	}
	
}

function ewic_get_installed_themes( $slug ) {
	
	$all_themes = wp_get_themes();
	$installed_themes = array();
	$is_installed = false;

	foreach ( $all_themes as $si => $tar ) {
		
		$installed_themes[] = $si;
	}
	
	if ( in_array( $slug, $installed_themes ) ) {
		
		$is_installed = true;
		
	}
	
	return $is_installed;
	
}