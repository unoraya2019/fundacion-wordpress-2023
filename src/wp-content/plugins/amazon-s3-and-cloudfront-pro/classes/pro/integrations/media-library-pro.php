<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Integrations;

use Amazon_S3_And_CloudFront_Pro;
use AS3CF_Utils;
use DeliciousBrains\WP_Offload_Media\Integrations\Media_Library;
use DeliciousBrains\WP_Offload_Media\Items\Download_Handler;
use DeliciousBrains\WP_Offload_Media\Items\Media_Library_Item;
use DeliciousBrains\WP_Offload_Media\Items\Remove_Local_Handler;
use DeliciousBrains\WP_Offload_Media\Items\Upload_Handler;
use DeliciousBrains\WP_Offload_Media\Pro\Items\Remove_Provider_Handler;
use DeliciousBrains\WP_Offload_Media\Pro\Items\Update_Acl_Handler;
use Exception;
use WP_Error;
use WP_Post;
use WP_User;

class Media_Library_Pro extends Media_Library {
	/**
	 * @var array
	 */
	protected $messages;
	/**
	 * @var array
	 */
	protected $_user_can_use_media_actions;

	/**
	 * @var Amazon_S3_And_CloudFront_Pro
	 */
	protected $as3cf;

	/**
	 * Init Media Library Pro integration.
	 */
	public function init() {
		parent::init();

		add_action( 'load-upload.php', array( $this, 'load_media_pro_assets' ), 11 );
		add_action( 'as3cf_load_attachment_assets', array( $this, 'load_attachment_js' ) );

		// Ajax handlers
		add_action( 'wp_ajax_as3cfpro_process_media_action', array( $this, 'ajax_process_media_action' ) );
		add_action( 'wp_ajax_as3cfpro_update_acl', array( $this, 'ajax_update_acl' ) );

		// Pro customisations
		add_filter( 'as3cf_media_action_strings', array( $this, 'media_action_strings' ) );

		// Media row actions
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'enrich_attachment_model' ), 10, 2 );
		add_filter( 'bulk_actions-upload', array( $this, 'add_list_table_bulk_actions' ) );
		add_filter( 'media_row_actions', array( $this, 'add_media_row_actions' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'maybe_display_media_action_message' ) );
		add_action( 'admin_init', array( $this, 'process_media_actions' ) );
	}

	/**
	 * Load the media Pro assets
	 */
	public function load_media_pro_assets() {
		if ( ! $this->as3cf->is_plugin_setup() ) {
			return;
		}

		$this->as3cf->enqueue_script( 'as3cf-pro-media-script', 'assets/js/pro/media', array(
			'jquery',
			'media-views',
			'media-grid',
			'wp-util',
		), false );

		$nonces = array(
			'get_attachment_provider_details' => wp_create_nonce( 'get-attachment-s3-details' ),
		);

		foreach ( $this->get_available_media_actions() as $action => $scopes ) {
			foreach ( $scopes as $scope ) {
				$nonces["{$scope}_{$action}"] = wp_create_nonce( "{$scope}-{$action}" );
			}
		}

		wp_localize_script( 'as3cf-pro-media-script', 'as3cfpro_media', array(
			'strings'  => $this->get_media_action_strings(),
			'actions'  => array(
				'bulk'     => $this->get_available_media_actions( 'bulk' ),
				'singular' => $this->get_available_media_actions( 'singular' ),
			),
			'nonces'   => $nonces,
			'settings' => array(
				'default_acl' => $this->as3cf->get_storage_provider()->get_default_acl(),
				'private_acl' => $this->as3cf->get_storage_provider()->get_private_acl(),
			),
		) );
	}

	/**
	 * Load the attachment JS only when editing an attachment.
	 */
	public function load_attachment_js() {
		$this->as3cf->enqueue_script( 'as3cf-pro-attachment-script', 'assets/js/pro/attachment', array(
			'jquery',
			'wp-util',
		), false );

		$actions = $this->get_available_media_actions( 'singular' );
		$nonces  = array();

		foreach ( $actions as $action ) {
			$nonces["singular_{$action}"] = wp_create_nonce( "singular-{$action}" );
		}

		wp_localize_script( 'as3cf-pro-attachment-script', 'as3cfpro_media', array(
			'strings'  => array(
				'local_warning'    => $this->get_media_action_strings( 'local_warning' ),
				'updating_acl'     => $this->get_media_action_strings( 'updating_acl' ),
				'change_acl_error' => $this->get_media_action_strings( 'change_acl_error' ),
			),
			'actions'  => $actions,
			'nonces'   => $nonces,
			'settings' => array(
				'post_id'     => get_the_ID(),
				'default_acl' => $this->as3cf->get_storage_provider()->get_default_acl(),
				'private_acl' => $this->as3cf->get_storage_provider()->get_private_acl(),
			),
		) );
	}

	/**
	 * Handle updating the ACL for an attachment
	 */
	public function ajax_update_acl() {
		check_ajax_referer( 'singular-update_acl' );

		$id         = $this->as3cf->filter_input( 'id', INPUT_POST, FILTER_VALIDATE_INT ); // input var ok
		$acl        = $this->as3cf->filter_input( 'acl', INPUT_POST, FILTER_SANITIZE_STRING ); // input var ok
		$title      = $this->get_media_action_strings( 'change_to_public' );
		$is_private = true;

		if ( empty( $id ) || empty( $acl ) ) {
			wp_send_json_error();
		}

		if ( $this->as3cf->get_storage_provider()->get_private_acl() !== $acl ) {
			$acl        = $this->as3cf->get_storage_provider()->get_default_acl();
			$title      = $this->get_media_action_strings( 'change_to_private' );
			$is_private = false;
		}

		// Update on provider.
		$as3cf_item = Media_Library_Item::get_by_source_id( $id );
		/** @var Update_Acl_Handler $updace_acl_handler */
		$update_acl_handler = $this->as3cf->get_item_handler( Update_Acl_Handler::get_item_handler_key_name() );
		$update             = $update_acl_handler->handle( $as3cf_item, array( 'set_private' => $is_private ) );

		$data = array(
			'acl'         => $acl,
			'acl_display' => $this->as3cf->get_acl_display_name( $acl ),
			'title'       => $title,
			'url'         => wp_get_attachment_url( $id ),
		);

		if ( is_wp_error( $update ) ) {
			wp_send_json_error();
		}

		wp_send_json_success( $data );
	}

	/**
	 * Enrich the attachment model attributes used in JS
	 *
	 * @param array      $response   Array of prepared attachment data.
	 * @param int|object $attachment Attachment ID or object.
	 *
	 * @return array
	 */
	public function enrich_attachment_model( $response, $attachment ) {
		$file = get_attached_file( $attachment->ID, true );

		// flag if the attachment file doesn't exist locally
		// so we can ask for confirmation when removing from S3
		$response['bulk_local_warning'] = ! file_exists( $file );

		return $response;
	}

	/**
	 * Add bulk media actions to a list table's bulk actions dropdown.
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	public function add_list_table_bulk_actions( $actions ) {
		$strings = $this->get_media_action_strings();

		foreach ( $this->get_available_media_actions( 'bulk' ) as $action ) {
			$actions["bulk_as3cfpro_{$action}"] = $strings[ $action ];
		}

		return $actions;
	}

	/**
	 * Add Pro media action strings.
	 *
	 * @param array $strings
	 *
	 * @return array
	 */
	public function media_action_strings( $strings ) {
		$strings['copy']               = __( 'Copy to Bucket', 'amazon-s3-and-cloudfront' );
		$strings['remove']             = __( 'Remove from Bucket', 'amazon-s3-and-cloudfront' );
		$strings['remove_local']       = __( 'Remove from Server', 'amazon-s3-and-cloudfront' );
		$strings['download']           = __( 'Copy to Server from Bucket', 'amazon-s3-and-cloudfront' );
		$strings['private_acl']        = __( 'Make Private in Bucket', 'amazon-s3-and-cloudfront' );
		$strings['public_acl']         = __( 'Make Public in Bucket', 'amazon-s3-and-cloudfront' );
		$strings['local_warning']      = __( 'This file does not exist locally so removing it from the bucket will result in broken links on your site. Are you sure you want to continue?', 'amazon-s3-and-cloudfront' );
		$strings['bulk_local_warning'] = __( 'Some files do not exist locally so removing them from the bucket will result in broken links on your site. Are you sure you want to continue?', 'amazon-s3-and-cloudfront' );
		$strings['change_to_private']  = __( 'Click to set as Private in the bucket', 'amazon-s3-and-cloudfront' );
		$strings['change_to_public']   = __( 'Click to set as Public in the bucket', 'amazon-s3-and-cloudfront' );
		$strings['updating_acl']       = __( 'Updating…', 'amazon-s3-and-cloudfront' );
		$strings['change_acl_error']   = __( 'There was an error changing the ACL. Make sure the IAM user has permission to change the ACL and try again.', 'amazon-s3-and-cloudfront' );

		return $strings;
	}

	/**
	 * Conditionally adds media action links for an attachment on the Media library list view.
	 *
	 * @param array       $actions
	 * @param WP_Post|int $post
	 *
	 * @return array
	 */
	public function add_media_row_actions( array $actions, $post ) {
		$available_actions = $this->get_available_media_actions( 'singular' );

		if ( ! $available_actions ) {
			return $actions;
		}

		$post_id     = ( is_object( $post ) ) ? $post->ID : $post;
		$file        = get_attached_file( $post_id, true );
		$file_exists = file_exists( $file );
		$as3cf_item  = Media_Library_Item::get_by_source_id( $post_id );

		// If offloaded to another provider can not do anything.
		if ( $as3cf_item && ! $as3cf_item->served_by_provider( true ) ) {
			$actions['as3cfpro_wrong_provider'] = '<span title="' . __( 'Offloaded to a different provider than currently configured.', 'amazon-s3-and-cloudfront' ) . '">' . __( 'Wrong Provider', 'amazon-s3-and-cloudfront' ) . '</span>';

			return $actions;
		}

		// If not offloaded at all, or offloaded to current provider, can use copy.
		if ( in_array( 'copy', $available_actions ) && $file_exists && ( ! $as3cf_item || $as3cf_item->served_by_provider( true ) ) ) {
			$this->add_media_row_action( $actions, $post_id, 'copy' );
		}

		// Actions beyond this point are for items on provider only
		if ( ! $as3cf_item || ! $as3cf_item->served_by_provider( true ) ) {
			return $actions;
		}

		if ( in_array( 'remove', $available_actions ) ) {
			$this->add_media_row_action( $actions, $post_id, 'remove' );
		}

		if ( in_array( 'download', $available_actions ) && ! $file_exists ) {
			$this->add_media_row_action( $actions, $post_id, 'download' );
		}

		if ( in_array( 'private_acl', $available_actions ) && ! $as3cf_item->is_private() ) {
			$this->add_media_row_action( $actions, $post_id, 'private_acl' );
		}

		if ( in_array( 'public_acl', $available_actions ) && $as3cf_item->is_private() ) {
			$this->add_media_row_action( $actions, $post_id, 'public_acl' );
		}

		if ( in_array( 'remove_local', $available_actions ) && $file_exists ) {
			$this->add_media_row_action( $actions, $post_id, 'remove_local' );
		}

		return $actions;
	}

	/**
	 * Display notices after processing media actions
	 */
	public function maybe_display_media_action_message() {
		global $pagenow;
		if ( ! in_array( $pagenow, array( 'upload.php', 'post.php' ) ) ) {
			return;
		}

		if ( isset( $_GET['as3cfpro-action'] ) && isset( $_GET['errors'] ) && isset( $_GET['count'] ) ) {
			$action = sanitize_key( $_GET['as3cfpro-action'] ); // input var okay

			$error_count = absint( $_GET['errors'] ); // input var okay
			$count       = absint( $_GET['count'] ); // input var okay

			$message_html = $this->get_media_action_result_message( $action, $count, $error_count );

			if ( false !== $message_html ) {
				echo $message_html;
			}
		}
	}

	/**
	 * Handler for single and bulk media actions
	 */
	public function process_media_actions() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		global $pagenow;
		if ( 'upload.php' != $pagenow ) {
			return;
		}

		if ( ! isset( $_GET['action'] ) ) { // input var okay
			return;
		}

		if ( ! empty( $_REQUEST['action2'] ) && '-1' != $_REQUEST['action2'] ) {
			// Handle bulk actions from the footer bulk action select
			$action = sanitize_key( $_REQUEST['action2'] ); // input var okay
		} else {
			$action = sanitize_key( $_REQUEST['action'] ); // input var okay
		}

		if ( false === strpos( $action, 'bulk_as3cfpro_' ) ) {
			$available_actions = $this->get_available_media_actions( 'singular' );
			$referrer          = 'as3cfpro-' . $action;
			$doing_bulk_action = false;
			if ( ! isset( $_GET['ids'] ) ) {
				return;
			}
			$ids = explode( ',', $_GET['ids'] ); // input var okay
		} else {
			$available_actions = $this->get_available_media_actions( 'bulk' );
			$action            = str_replace( 'bulk_as3cfpro_', '', $action );
			$referrer          = 'bulk-media';
			$doing_bulk_action = true;
			if ( ! isset( $_REQUEST['media'] ) ) {
				return;
			}
			$ids = $_REQUEST['media']; // input var okay
		}

		if ( ! in_array( $action, $available_actions ) ) {
			return;
		}

		$ids      = array_map( 'intval', $ids );
		$id_count = count( $ids );

		check_admin_referer( $referrer );

		$sendback = isset( $_GET['sendback'] ) ? $_GET['sendback'] : admin_url( 'upload.php' );

		$args = array(
			'as3cfpro-action' => $action,
		);

		$result = $this->maybe_do_provider_action( $action, $ids, $doing_bulk_action );

		if ( ! $result ) {
			unset( $args['as3cfpro-action'] );
			$result = array();
		}

		// If we're uploading a single file, add the id to the `$args` array.
		if ( 'copy' === $action && 1 === $id_count && ! empty( $result ) && 1 === ( $result['count'] + $result['errors'] ) ) {
			$args['as3cf_id'] = array_shift( $ids );
		}

		$args = array_merge( $args, $result );
		$url  = add_query_arg( $args, $sendback );

		wp_redirect( esc_url_raw( $url ) );
		$this->_exit();
	}

	/**
	 * Add an action link to the media actions array
	 *
	 * @param array  $actions
	 * @param int    $post_id
	 * @param string $action
	 * @param string $text
	 * @param bool   $show_warning
	 */
	public function add_media_row_action( &$actions, $post_id, $action, $text = '', $show_warning = false ) {
		$url   = $this->get_media_action_url( $action, $post_id );
		$text  = $text ?: $this->get_media_action_strings( $action );
		$class = $action;
		if ( $show_warning ) {
			$class .= ' local-warning';
		}

		$actions[ 'as3cfpro_' . $action ] = '<a href="' . $url . '" class="' . $class . '" title="' . esc_attr( $text ) . '">' . esc_html( $text ) . '</a>';
	}

	/**
	 * Check we can do the media actions
	 *
	 * @return bool
	 */
	public function verify_media_actions() {
		if ( ! $this->as3cf->is_pro_plugin_setup( true ) ) {
			return false;
		}

		return $this->user_can_use_media_actions();
	}

	/**
	 * Get a list of available media actions which can be performed according to plugin and user capability requirements.
	 *
	 * @param string|null $scope
	 *
	 * @return array
	 */
	public function get_available_media_actions( $scope = null ) {
		$actions = array();

		if ( ! $this->as3cf->is_plugin_setup( true ) || ! $this->user_can_use_media_actions() ) {
			return $actions;
		}

		// We've already tested provider credentials, but is license ok?
		if ( $this->as3cf->is_pro_plugin_setup( true ) ) {
			$actions['copy']         = array( 'singular', 'bulk' );
			$actions['download']     = array( 'singular', 'bulk' );
			$actions['update_acl']   = array( 'singular' );
			$actions['private_acl']  = array( 'singular', 'bulk' );
			$actions['public_acl']   = array( 'singular', 'bulk' );
			$actions['remove_local'] = array( 'singular', 'bulk' );
		}

		// Remove from Bucket should still be available even if license exceeded/invalid.
		$actions['remove'] = array( 'singular', 'bulk' );

		if ( $scope ) {
			$in_scope = array_filter( $actions, function ( $scopes ) use ( $scope ) {
				return in_array( $scope, $scopes );
			} );

			return array_keys( $in_scope );
		}

		return $actions;
	}

	/**
	 * Check if the given user can use on-demand S3 media actions.
	 *
	 * @param null|int|WP_User $user User to check. Defaults to current user.
	 *
	 * @return bool
	 */
	public function user_can_use_media_actions( $user = null ) {
		$user = $user ? $user : wp_get_current_user();

		if ( is_object( $user ) ) {
			$user = $user->ID;
		}

		if ( ! is_null( $this->_user_can_use_media_actions ) && isset( $this->_user_can_use_media_actions[ $user ] ) ) {
			return $this->_user_can_use_media_actions[ $user ];
		}

		$this->_user_can_use_media_actions[ $user ] = false;

		if ( user_can( $user, 'use_as3cf_media_actions' ) ) {
			$this->_user_can_use_media_actions[ $user ] = true;
		} else {
			/**
			 * The default capability for using on-demand S3 media actions.
			 *
			 * @param string $capability Registered capability identifier
			 */
			$capability                                 = apply_filters( 'as3cfpro_media_actions_capability', 'manage_options' );
			$this->_user_can_use_media_actions[ $user ] = user_can( $user, $capability );
		}

		return $this->_user_can_use_media_actions[ $user ];
	}

	/**
	 * Generate the URL for performing S3 media actions
	 *
	 * @param string      $action
	 * @param int         $post_id
	 * @param null|string $sendback_path
	 *
	 * @return string
	 */
	public function get_media_action_url( $action, $post_id, $sendback_path = null ) {
		$args = array(
			'action' => $action,
			'ids'    => $post_id,
		);

		if ( ! is_null( $sendback_path ) ) {
			$args['sendback'] = urlencode( admin_url( $sendback_path ) );
		}

		$url = add_query_arg( $args, admin_url( 'upload.php' ) );
		$url = wp_nonce_url( $url, 'as3cfpro-' . $action );

		return esc_url( $url );
	}

	/**
	 * Handle S3 actions applied to attachments via the Backbone JS
	 * in the media grid and edit attachment modal
	 */
	public function ajax_process_media_action() {
		if ( ! isset( $_POST['s3_action'] ) && ! isset( $_POST['ids'] ) ) {
			return;
		}

		$scope  = filter_input( INPUT_POST, 'scope' );
		$action = filter_input( INPUT_POST, 's3_action' );

		check_ajax_referer( "{$scope}-{$action}" );

		$ids = array_map( 'intval', $_POST['ids'] ); // input var okay

		// process the S3 action for the attachments
		$return = $this->maybe_do_provider_action( $action, $ids, true );

		$message_html = '';

		if ( $return ) {
			$message_html = $this->get_media_action_result_message( $action, $return['count'], $return['errors'] );
		}

		wp_send_json_success( $message_html );
	}

	/**
	 * Wrapper for media actions
	 *
	 * @param string $action             type of media action, copy, remove, download, remove_local
	 * @param array  $ids                attachment IDs
	 * @param bool   $doing_bulk_action  flag for multiple attachments, if true then we need to
	 *                                   perform a check for each attachment
	 *
	 * @return bool|array on success array with success count and error count
	 * @throws Exception
	 */
	public function maybe_do_provider_action( $action, $ids, $doing_bulk_action ) {
		switch ( $action ) {
			case 'copy':
				$result = $this->maybe_upload_attachments( $ids, $doing_bulk_action );
				break;
			case 'remove':
				$result = $this->maybe_delete_attachments_from_provider( $ids, $doing_bulk_action );
				break;
			case 'download':
				$result = $this->maybe_download_attachments_from_provider( $ids, $doing_bulk_action );
				break;
			case 'private_acl':
				$result = $this->maybe_update_acls_to_private( $ids, $doing_bulk_action );
				break;
			case 'public_acl':
				$result = $this->maybe_update_acls_to_public( $ids, $doing_bulk_action );
				break;
			case 'remove_local':
				$result = $this->maybe_remove_local_files_for_attachments( $ids, $doing_bulk_action );
				break;
			default:
				// not one of our actions, remove
				$result = false;
				break;
		}

		return $result;
	}

	/**
	 * Get the result message after an S3 action has been performed
	 *
	 * @param string $action      type of S3 action
	 * @param int    $count       count of successful processes
	 * @param int    $error_count count of errors
	 *
	 * @return bool|string
	 */
	public function get_media_action_result_message( $action, $count = 0, $error_count = 0 ) {
		$class = 'updated';
		$type  = 'success';

		if ( 0 === $count && 0 === $error_count ) {
			// don't show any message if no attachments processed
			// i.e. they haven't met the checks for bulk actions
			return false;
		}

		if ( $error_count > 0 ) {
			$type = $class = 'error';

			// We have processed some successfully.
			if ( $count > 0 ) {
				$type = 'partial';
			}
		}

		$message = $this->get_message( $action, $type );

		// can't find a relevant message, abort
		if ( ! $message ) {
			return false;
		}

		$id = $this->as3cf->filter_input( 'as3cf_id', INPUT_GET, FILTER_VALIDATE_INT );

		// If we're uploading a single item, add an edit link.
		if ( 1 === ( $count + $error_count ) && ! empty( $id ) ) {
			$url = esc_url( get_edit_post_link( $id ) );

			// Only add the link if we have a URL.
			if ( ! empty( $url ) ) {
				$text    = esc_html__( 'Edit attachment', 'amazon-s3-and-cloudfront' );
				$message .= sprintf( ' <a href="%1$s">%2$s</a>', $url, $text );
			}
		}

		$message = sprintf( '<div class="notice as3cf-notice %s is-dismissible"><p>%s</p></div>', $class, $message );

		return $message;
	}

	/**
	 * Retrieve all the media action related notice messages
	 *
	 * @return array
	 */
	public function get_messages() {
		if ( is_null( $this->messages ) ) {
			$this->messages = array(
				'copy'         => array(
					'success' => __( 'Media successfully copied to bucket.', 'amazon-s3-and-cloudfront' ),
					'partial' => __( 'Media copied to bucket with some errors.', 'amazon-s3-and-cloudfront' ),
					'error'   => __( 'There were errors when copying the media to bucket.', 'amazon-s3-and-cloudfront' ),
				),
				'remove'       => array(
					'success' => __( 'Media successfully removed from bucket.', 'amazon-s3-and-cloudfront' ),
					'partial' => __( 'Media removed from bucket, with some errors.', 'amazon-s3-and-cloudfront' ),
					'error'   => __( 'There were errors when removing the media from bucket.', 'amazon-s3-and-cloudfront' ),
				),
				'download'     => array(
					'success' => __( 'Media successfully downloaded from bucket.', 'amazon-s3-and-cloudfront' ),
					'partial' => __( 'Media downloaded from bucket, with some errors.', 'amazon-s3-and-cloudfront' ),
					'error'   => __( 'There were errors when downloading the media from bucket.', 'amazon-s3-and-cloudfront' ),
				),
				'private_acl'  => array(
					'success' => __( 'Media successfully set as private in bucket.', 'amazon-s3-and-cloudfront' ),
					'partial' => __( 'Media set as private in bucket, with some errors.', 'amazon-s3-and-cloudfront' ),
					'error'   => __( 'There were errors when setting the media as private in bucket.', 'amazon-s3-and-cloudfront' ),
				),
				'public_acl'   => array(
					'success' => __( 'Media successfully set as public in bucket.', 'amazon-s3-and-cloudfront' ),
					'partial' => __( 'Media set as public in bucket, with some errors.', 'amazon-s3-and-cloudfront' ),
					'error'   => __( 'There were errors when setting the media as public in bucket.', 'amazon-s3-and-cloudfront' ),
				),
				'remove_local' => array(
					'success' => __( 'Media successfully removed from server.', 'amazon-s3-and-cloudfront' ),
					'partial' => __( 'Media removed from server, with some errors.', 'amazon-s3-and-cloudfront' ),
					'error'   => __( 'There were errors when removing the media from server.', 'amazon-s3-and-cloudfront' ),
				),
			);
		}

		return $this->messages;
	}

	/**
	 * Get a specific media action notice message
	 *
	 * @param string $action type of action, e.g. copy, remove, download
	 * @param string $type   if the action has resulted in success, error, partial (errors)
	 *
	 * @return string|bool
	 */
	public function get_message( $action = 'copy', $type = 'success' ) {
		$messages = $this->get_messages();
		if ( isset( $messages[ $action ][ $type ] ) ) {
			return $messages[ $action ][ $type ];
		}

		return false;
	}

	/**
	 * Wrapper for uploading multiple attachments to S3
	 *
	 * @param array $post_ids            attachment IDs
	 * @param bool  $doing_bulk_action   flag for multiple attachments, if true then we need to
	 *                                   perform a check for each attachment to make sure the
	 *                                   file exists locally before uploading to S3
	 *
	 * @return array|WP_Error
	 * @throws Exception
	 */
	public function maybe_upload_attachments( $post_ids, $doing_bulk_action = false ) {
		$result         = array(
			'errors' => 0,
			'count'  => 0,
		);
		$upload_handler = $this->as3cf->get_item_handler( Upload_Handler::get_item_handler_key_name() );

		foreach ( $post_ids as $post_id ) {
			if ( $doing_bulk_action ) {
				// If bulk action check the file exists.
				$file = get_attached_file( $post_id, true );
				// If the file doesn't exist locally we can't copy.
				if ( ! file_exists( $file ) ) {
					continue;
				}
			}

			$offloaded_files = array();
			$as3cf_item      = Media_Library_Item::get_by_source_id( $post_id );

			if ( empty( $as3cf_item ) ) {
				$as3cf_item = Media_Library_Item::create_from_source_id( $post_id );
			} else {
				// Refresh the list of expected objects from attachment's expected thumbnail sizes.
				$offloaded_files = $as3cf_item->offloaded_files();
				$metadata        = wp_get_attachment_metadata( $post_id, true );

				if ( empty( $metadata ) || is_wp_error( $metadata ) ) {
					$result['errors']++;
					continue;
				}

				$this->update_item_from_new_metadata( $as3cf_item, $metadata );
			}

			if ( ! empty( $as3cf_item ) && ! is_wp_error( $as3cf_item ) ) {
				$upload_result = $upload_handler->handle( $as3cf_item, $offloaded_files );

				// Even if an upload was cancelled via a filter,
				// if there was no error, then it was successfully processed.
				if ( ! is_wp_error( $upload_result ) ) {
					$result['count']++;
					continue;
				}
			}

			$result['errors']++;
		}

		return $result;
	}

	/**
	 * Wrapper for removing multiple attachments from provider.
	 *
	 * @param array $post_ids          Attachment IDs
	 * @param bool  $doing_bulk_action Flag for multiple attachments. If true then we need to
	 *                                 perform a check for each attachment to make sure it has
	 *                                 been uploaded to provider before trying to delete it.
	 *                                 This flag currently has no effect, but is retained for
	 *                                 consistency with similar functions in this class.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function maybe_delete_attachments_from_provider( $post_ids, $doing_bulk_action = false ) {
		$result = array(
			'errors' => 0,
			'count'  => 0,
		);

		/** @var Remove_Provider_Handler $remove_handler */
		$remove_handler = $this->as3cf->get_item_handler( Remove_Provider_Handler::get_item_handler_key_name() );

		foreach ( $post_ids as $post_id ) {
			$as3cf_item = Media_Library_Item::get_by_source_id( $post_id );
			if ( empty( $as3cf_item ) ) {
				$result['count']++;
				continue;
			}

			// Delete attachment from provider.
			$remove_result = $remove_handler->handle( $as3cf_item );
			if ( is_wp_error( $remove_result ) ) {
				$result['errors']++;
				continue;
			}

			// There was no error, so either objects deleted or skipped due to duplicates.
			$as3cf_item->delete();

			if ( Media_Library_Item::get_by_source_id( $post_id ) ) {
				$result['errors']++;
				continue;
			}

			$result['count']++;
		}

		return $result;
	}

	/**
	 * Wrapper for downloading multiple attachments from provider.
	 *
	 * @param array $post_ids          Attachment IDs
	 * @param bool  $doing_bulk_action Flag for multiple attachments. If true then we need to
	 *                                 perform a check for each attachment to make sure it has
	 *                                 been uploaded to provider and does not exist locally before
	 *                                 trying to download it.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function maybe_download_attachments_from_provider( $post_ids, $doing_bulk_action = false ) {
		$result = array(
			'errors' => 0,
			'count'  => 0,
		);

		$download_handler = $this->as3cf->get_item_handler( Download_Handler::get_item_handler_key_name() );

		foreach ( $post_ids as $post_id ) {
			$file       = get_attached_file( $post_id, true );
			$as3cf_item = Media_Library_Item::get_by_source_id( $post_id );

			if ( empty( $as3cf_item ) || is_wp_error( $as3cf_item ) ) {
				continue;
			}

			// If bulk action, check whether original file exists locally before trying to download.
			$file_exists_locally = $doing_bulk_action && file_exists( $file );

			if ( ! $file_exists_locally ) {
				// Download the attachment from provider.
				$download_handler->handle( $as3cf_item );

				if ( ! file_exists( $file ) ) {
					$result['errors']++;
					continue;
				}
			}

			$result['count']++;
		}

		return $result;
	}

	/**
	 * Wrapper for removing multiple attachments from server if offloaded
	 *
	 * @param array $post_ids            attachment IDs
	 * @param bool  $doing_bulk_action   flag for multiple attachments, if true then we need to
	 *                                   perform a check for each attachment to make sure it has
	 *                                   been uploaded to bucket before trying to delete it
	 *
	 * @return array
	 * @throws Exception
	 */
	public function maybe_remove_local_files_for_attachments( $post_ids, $doing_bulk_action = false ) {
		$result      = array(
			'errors' => 0,
			'count'  => 0,
		);
		$as3cf_items = array();

		foreach ( $post_ids as $key => $post_id ) {
			$as3cf_item = Media_Library_Item::get_by_source_id( $post_id );
			if ( empty( $as3cf_item ) ) {
				$result['errors']++;
				unset( $post_ids[ $key ] );
				continue;
			}

			$as3cf_items[ $post_id ] = $as3cf_item;
		}

		if ( ! empty( $post_ids ) ) {
			/** @var Remove_Local_Handler $remove_local_handler */
			$remove_local_handler = $this->as3cf->get_item_handler( Remove_Local_Handler::get_item_handler_key_name() );

			// Set up to check provider keys before removing.
			$options = array(
				'verify_exists_on_provider' => true,
				'provider_keys'             => $this->as3cf->get_provider_keys( $post_ids ),
			);

			foreach ( $post_ids as $post_id ) {
				$as3cf_item = $as3cf_items[ $post_id ];
				$remove_local_handler->handle( $as3cf_item, $options );

				if ( ! $this->attachment_exists_locally( $post_id ) ) {
					$result['count']++;
				} else {
					$result['errors']++;
				}
			}
		}

		return $result;
	}

	/**
	 * Wrapper for updating the ACLs for multiple attachments on provider
	 *
	 * @param array $post_ids            Attachment IDs
	 * @param bool  $doing_bulk_action   Flag for multiple attachments, if true then we need to
	 *                                   perform a check for each attachment to make sure it has
	 *                                   been uploaded to the current provider
	 * @param bool  $private             Setting to private ACL? Default is public.
	 *
	 * @return array|WP_Error
	 * @throws Exception
	 */
	private function maybe_update_acls( $post_ids, $doing_bulk_action = false, $private = false ) {
		$result = array(
			'errors' => 0,
			'count'  => 0,
		);

		$provider_key = $this->as3cf->get_storage_provider()->get_provider_key_name();
		/** @var Update_Acl_Handler $update_acl_handler */
		$update_acl_handler = $this->as3cf->get_item_handler( Update_Acl_Handler::get_item_handler_key_name() );
		$options            = array( 'set_private' => $private );

		foreach ( $post_ids as $post_id ) {
			$as3cf_item = Media_Library_Item::get_by_source_id( $post_id );

			if ( ! $as3cf_item ) {
				$result['errors']++;
				continue;
			}

			if ( $doing_bulk_action ) {
				if ( $provider_key !== $as3cf_item->provider() ) {
					$result['errors']++;
					continue;
				}
			}

			$update_result = $update_acl_handler->handle( $as3cf_item, $options );

			if ( is_wp_error( $update_result ) ) {
				$result['errors']++;
				continue;
			}

			$result['count']++;
		}

		return $result;
	}

	/**
	 * Wrapper for updating the ACLs to private for multiple attachments on provider
	 *
	 * @param array $post_ids            Attachment IDs
	 * @param bool  $doing_bulk_action   Flag for multiple attachments, if true then we need to
	 *                                   perform a check for each attachment to make sure it has
	 *                                   been uploaded to the current provider
	 *
	 * @return array|WP_Error
	 * @throws Exception
	 */
	private function maybe_update_acls_to_private( $post_ids, $doing_bulk_action = false ) {
		return $this->maybe_update_acls( $post_ids, $doing_bulk_action, true );
	}

	/**
	 * Wrapper for updating the ACLs to public for multiple attachments on provider
	 *
	 * @param array $post_ids            Attachment IDs
	 * @param bool  $doing_bulk_action   Flag for multiple attachments, if true then we need to
	 *                                   perform a check for each attachment to make sure it has
	 *                                   been uploaded to the current provider
	 *
	 * @return array|WP_Error
	 * @throws Exception
	 */
	private function maybe_update_acls_to_public( $post_ids, $doing_bulk_action = false ) {
		return $this->maybe_update_acls( $post_ids, $doing_bulk_action, false );
	}

	/**
	 * Get attachment local paths.
	 *
	 * @param int $attachment_id
	 *
	 * @return array
	 */
	private function get_attachment_local_paths( $attachment_id ) {
		static $local_paths = array();

		$blog_id = get_current_blog_id();

		if ( isset( $local_paths[ $blog_id ][ $attachment_id ] ) ) {
			return $local_paths[ $blog_id ][ $attachment_id ];
		}

		$paths = AS3CF_Utils::get_attachment_file_paths( $attachment_id, false );

		$local_paths[ $blog_id ][ $attachment_id ] = array_unique( $paths );

		return $paths;
	}

	/**
	 * Does attachment exist locally?
	 *
	 * @param int $attachment_id
	 *
	 * @return bool
	 */
	private function attachment_exists_locally( $attachment_id ) {
		$paths = $this->get_attachment_local_paths( $attachment_id );

		foreach ( $paths as $path ) {
			if ( file_exists( $path ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get ACL value string.
	 *
	 * @param array $acl
	 * @param int   $post_id
	 *
	 * @return string
	 */
	public function get_acl_value_string( $acl, $post_id ) {
		$as3cf_item = Media_Library_Item::get_by_source_id( $post_id );
		if ( ! in_array( 'update_acl', $this->get_available_media_actions( 'singular' ) ) || ! $this->as3cf || ! $as3cf_item->served_by_provider( true ) ) {
			return parent::get_acl_value_string( $acl, $post_id );
		}

		return sprintf( '<a id="as3cfpro-toggle-acl" title="%s" data-currentACL="%s" href="#">%s</a>', $acl['title'], $acl['acl'], $acl['name'] );
	}

	/**
	 * Helper function for terminating script execution. Easily testable.
	 *
	 * @param int|string $exit_code
	 *
	 * @return void
	 */
	public function _exit( $exit_code = 0 ) {
		exit( $exit_code );
	}
}