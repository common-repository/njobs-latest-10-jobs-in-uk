<?php

/*
Plugin Name: Njobs Latest 10 Jobs in UK
Plugin URI: http://www.njobs.co.uk
Description: Widget displays 10 latest job offers from the UK.
Author: njobs
Version: 1.0
Author URI: http://www.njobs.co.uk

This plugin is released under GPL:
	http://www.opensource.org/licenses/gpl-license.php
*/

/**
 * Njobs UK Last 10 Jobs Class
 * 
 * Display Last 10 Njobs in slidebar
 * 
 * @author njobs
 * 
 * @version 1.0
 */
class WidgetNjobsLatestJobs extends WP_Widget {

	function WidgetNjobsLatestJobs() {
		$widget_ops = array( 'classname' => 'widget_njobslastjobs', 
							 'description' => __( 'Njobs Latest 10 Jobs in UK.', 'njobs') );
		
		$this->WP_Widget('_njobslastjobs', __('Njobs Latest 10 Jobs in UK.', 'njobs'), $widget_ops);

	}
	
	function _parse_xml( $respond ) {
		
		$errors = libxml_use_internal_errors( 'true' );
		$data = simplexml_load_string( $respond );
		libxml_use_internal_errors( $errors );
		if ( is_object( $data ) ) {
			
			return $data;
		}
		return FALSE;
	}

	function widget($args, $instance) {
		
		global $wpdb, $wp_query;

		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Last 10 jobs in UK:', 'njobs') : $instance['title'], $instance, $this->id_base);
		$xml_url = empty( $instance['xml_url'] ) ? 'http://www.njobs.co.uk/xml_lastjob.php' : $instance[ 'xml_url' ];
		if ( $xml_url !== FALSE && function_exists( 'simplexml_load_string' ) ) {
			
			$response = wp_remote_request( $xml_url, array( 'method' => 'GET', 'redirection' => 1 ) );
			if ( !is_wp_error( $response ) ) {
				
				$response_body = wp_remote_retrieve_body( $response );
				$xml_struct = WidgetNjobsLatestJobs::_parse_xml( $response_body );
				if ( $xml_struct === FALSE || !isset( $xml_struct->job ) ) {
					
					return ;
				}

				$count = count( $xml_struct->job );
				$i = 0;
				
?>
		<div class="side-widget" style="width:100%; margin:0; padding:0;border:none;background:none;">
			<div style="border:0; margin:0 auto; padding:0;width:80%;">
				<table style="border:1px solid #ccc; background: #F5F7DF; padding: 0px;" cellspacing="0" cellpadding="0">
					<tr>
						<td style="height:52px; width:128px; margin:none; border:0; padding:5px 0px 0px 26px"><a href="http://www.njobs.eu/" style="margin:none; padding:none; cursor:hand"><img src="http://www.njobs.eu/img/logo_njobs.jpg" alt="njobs Europe" height="52" width="128" border="0"></a></td>
					</tr>
					<tr>
						<td style="height:30px; color:#333; font-weight:bold; text-align:left; font-size:11px; font-family:Tahoma, sans-serif; padding-left:8px">
							<?php echo $title; ?>
						</td>
					</tr>
					<?php foreach ( $xml_struct->job as $job ) : ?>
					<?php 
						$title = (string) $job->title;
						$url = (string) $job->url;
						$i++;
					?>
					<tr>
						<td width="150" style="font-weight:normal; text-align:left; font-size:11px; font-family:Tahoma, sans-serif; color:#333; padding-left:8px">
							<a style="color:#205B87" href="<?php echo $url; ?>" target="_blank"><?php echo $title; ?></a>
						</td>
					</tr>
					<tr>
						<td style="height:10px;">
							<?php if ( $i < $count ) : ?>
							<div style="height:1px;border-bottom:1px solid #999;width:80%;margin:0px auto;"></div>
							<?php endif; ?>						
						</td>
					</tr>
					<?php endforeach; ?>
					<tr>
						<td style="color:#333; font-weight:normal; text-align:center; font-size:9px; font-family:Tahoma, sans-serif;">
							<?php _e( 'Powered by njobs network', 'njobs' ); ?>
						</td>
					</tr>					
					<tr>
						<td height="116" style="font-family: Tahoma, sans-serif; height: 21px; font-size:12px; text-align:center;"><a href="http://www.njobs.nl" style="text-decoration:none;color:#999"><img src="http://www.njobs.eu/img/nl.jpg" alt="vacatures Netherlands" border="0"></a> <a href="http://www.njobs.de" style="text-decoration:none;color:#999"><img src="http://www.njobs.eu/img/de.jpg" alt="arbeit Deutschland" border="0"></a> 	<a href="http://www.njobs.co.uk" style="text-decoration:none;color:#999"><img src="http://www.njobs.eu/img/uk.jpg" alt="work United Kingdom" border="0"></a> <a href="http://www.n-jobs.it" style="text-decoration:none;color:#999"><img src="http://www.njobs.eu/img/it.jpg" alt="Lavoro Italia" border="0"></a> <a href="http://www.njobs.fr" style="text-decoration:none;color:#999"><img src="http://www.njobs.eu/img/fr.jpg" alt="Emploi France" border="0"></a> <a href="http://www.njobs.es" style="text-decoration:none;color:#999"><img src="http://www.njobs.eu/img/es.jpg" alt="trabajo Espana" border="0"></a></td>
					</tr>
				</table>
			</div>
		</div>
<?php
			}
		}
	}
	
	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		if ( preg_match( '#^https?://.+$#', $new_instance['xml_url'] ) ) {
			
			$instance['xml_url'] = $new_instance['xml_url'];
		}

		return $instance;
	}

	function form( $instance ) {
		
		$title = isset($instance['title']) ? esc_attr($instance['title']) : 'Last 10 jobs in UK:';
		$xml_url = isset($instance['xml_url']) ? esc_attr($instance['xml_url']) : 'http://www.njobs.co.uk/xml_lastjob.php';
		
		if ( !function_exists( 'simplexml_load_string' ) ) {
			
			echo '<div style="color:red">Function: simplexml_load_string missing!</div>';
		}
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('xml_url'); ?>"><?php _e('XML Url:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('xml_url'); ?>" name="<?php echo $this->get_field_name('xml_url'); ?>" type="text" value="<?php echo $xml_url; ?>" /></p>		
		
<?php
	}
} 

/**
 * Active Widget
 */
add_action( 'widgets_init', 'register_WidgetNjobsLatestJobs' );
function register_WidgetNjobsLatestJobs() {

	return register_widget( 'WidgetNjobsLatestJobs' );
}
?>
