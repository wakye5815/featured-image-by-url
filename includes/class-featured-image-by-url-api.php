<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package     Featured_Image_By_URL
 * @subpackage  Featured_Image_By_URL/admin
 * @copyright   Copyright (c) 2018, Knawat
 * @since       1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The admin-specific functionality of the plugin.
 *
 * @package     Featured_Image_By_URL
 * @subpackage  Featured_Image_By_URL/admin
 */
class Featured_Image_By_URL_Api {

	public $image_meta_url = '_knawatfibu_url';
	public $image_meta_alt = '_knawatfibu_alt';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action('rest_api_init', function(){
			register_rest_route( 'wp/v2', '/thumbnail', array(
				'methods' => 'POST',
				'callback' => array($this, 'knawatfibu_save_image_url_data'),
				'permission_callback' => function(){
					return current_user_can('publish_posts');
				})
			);
		});
	}

	/**
	 * @return void
	 */
	function knawatfibu_save_image_url_data( $request ) {
		if( isset( $request['knawatfibu_url'] ) ){
			global $knawatfibu;

			$post_id = $request['post_id'];
			$image_url = isset( $request['knawatfibu_url'] ) ? esc_url( $request['knawatfibu_url'] ) : '';
			$image_alt = isset( $request['knawatfibu_alt'] ) ? wp_strip_all_tags( $request['knawatfibu_alt'] ): '';

			if ( $image_url != ''){
				if( get_post_type( $post_id ) == 'product' ){
					$imagesize = @getimagesize( $image_url );

					$image_url = array(
						'img_url' => get_post_meta( $post_id, $this->image_meta_url , true ),
						'width'	  => isset( $imagesize[0] ) ? $imagesize[0] : '',
						'height'  => isset( $imagesize[1] ) ? $imagesize[1] : ''
					);
				}

				update_post_meta( $post_id, $this->image_meta_url, $image_url );
				if( $image_alt ){
					update_post_meta( $post_id, $this->image_meta_alt, $image_alt );
				}
			}else{
				delete_post_meta( $post_id, $this->image_meta_url );
				delete_post_meta( $post_id, $this->image_meta_alt );
			}
		}
	}
}