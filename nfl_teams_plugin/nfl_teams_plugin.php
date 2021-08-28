<?php
/**
 * Plugin name: NFL Teams Layout
 * Plugin URI: https://devsass3.com
 * Description: This plugin allow us to insert a shortcode on a WordPress page. And this shortcode will print the API data - A list of NFL Football Teams.
 * Author: Sandra Alvarez
 * Author URI: https://devsass3.com
 * Version: 1.0
 */

// This line of code is to avoid a curious user to execute the plugin route directly from the browser
defined( 'ABSPATH' ) or die( 'Unauthorized Access' );

// Loading some style in the head tag
function callback_for_setting_up_scripts() {
    wp_register_style( 'customApp', 'https://devsass3.com/challenges/wordpress/wp-content/plugins/nfl_teams_plugin/customCode/customApp.css' );
    wp_enqueue_style( 'customApp' );
}
add_action('wp_enqueue_scripts', 'callback_for_setting_up_scripts');

// Shortcode that we will insert in the WordPress page
add_shortcode( 'nfl_players', 'display_data');

// Plugin main function to display data in a table from API
function display_data() {

    $url = 'https://delivery.oddsandstats.co/team_list/NFL.JSON?api_key=74db8efa2a6db279393b433d97c2bc843f8e32b0';
    
    $response = wp_remote_get( $url );

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        return "Something went wrong: $error_message";
    } else {
        $api_results = json_decode( wp_remote_retrieve_body( $response ) );
    }
    
    //Here starts the TABLE
    $html = '';
    
    $html .= '<table>';
        $html .= '<tr class="tableHeadings">';
            $html .= '<td>Name</td>';
            $html .= '<td>Nickname</td>';
            $html .= '<td>Display_name</td>';
            $html .= '<td>Id</td>';
            $html .= '<td>Conference</td>';
            $html .= '<td>Division</td>';
        $html .= '</tr>';
        
        //returning data in the TABLE with a loop method
        foreach( $api_results->results->data->team as $api_data ) {
    
            $html .= '<tr>';
            $html .= '<td>' . $api_data->name . '</td>';
            $html .= '<td>' . $api_data->nickname . '</td>';
            $html .= '<td>' . $api_data->display_name . '</td>';
            $html .= '<td>' . $api_data->id . '</td>';
            $html .= '<td>' . $api_data->conference . '</td>';
            $html .= '<td>' . $api_data->division . '</td>';
            $html .= '</tr>';
        }
        
    $html .= '</table>';
    
    return $html;
}	

//displaying the json in the admin panel just to overview the data there

function display_json_in_admin() {

    $url = 'https://delivery.oddsandstats.co/team_list/NFL.JSON?api_key=74db8efa2a6db279393b433d97c2bc843f8e32b0';
    
    $response = wp_remote_get( $url );

	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		return "Something went wrong: $error_message";
	} else {
		echo '<pre>';
		var_dump( json_decode (wp_remote_retrieve_body( $response )) );
		echo '</pre>';
	}
}
 
function display_on_menu() {
	add_menu_page(
		__( 'NFL Teams General Settings', '' ),
		'NFL Teams JSON Overview',
		'manage_options',
		'nfl-api.php',
		'display_json_in_admin',
		'dashicons-visibility',
		16
	);
}

add_action( 'admin_menu', 'display_on_menu' );
