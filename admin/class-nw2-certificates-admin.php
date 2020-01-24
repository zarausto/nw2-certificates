<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/zarausto
 * @since      1.0.0
 *
 * @package    Nw2_Certificates
 * @subpackage Nw2_Certificates/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Nw2_Certificates
 * @subpackage Nw2_Certificates/admin
 * @author     Fausto Rodrigo Toloi <fausto@nw2web.com.br>
 */
class Nw2_Certificates_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private static $plugin_name_static;

	/**
	 * The cpt of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	public  $plugin_cpt;
	/**
	 * The cpt of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private static  $plugin_cpt_static;

	/**
	 * The linh to print.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $print_link;

	/**
	 * The linh to print.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $canvas_size = array('800', '800');

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		self::$plugin_name_static = $plugin_name;

		self::$plugin_cpt_static = $this->plugin_cpt = substr($this->plugin_name . '_events', 0, 20);

		$this->print_link =  get_site_url() . '/' . $this->plugin_name . '/';
	}

	public static function _get_plugin_cpt()
	{
		return self::$plugin_cpt_static;
	}

	/**
	 * Set admin filters 
	 *
	 * @param Nw2_Certificates_Loader $loader
	 * @return void
	 */
	public function define_admin_hooks(Nw2_Certificates_Loader $loader)
	{
		$loader->add_filter('shortcode_atts_wpcf7', $this, 'wpcf7_enable_attr', 10, 3);
		$loader->add_filter('wpcf7_before_send_mail', $this, 'wpcf7_check_if_plugin_is_used', 10, 2);
		$loader->add_action('init', $this, 'register_cpt_events', 10, 1);
		$loader->add_action("add_meta_boxes",  $this, "nw2_certificates_add_metaboxes");
		$loader->add_action("save_post",  $this, "nw2_certificates_save_meta_fields");
		$loader->add_action("new_to_publish",  $this, "nw2_certificates_save_meta_fields");
	}
	public function nw2_certificates_add_metaboxes($metaboxes)
	{
		add_meta_box(
			$this->plugin_name . '_cf7',    				 	// $id
			_('NW2 Certificates'),				                 	// $title
			array($this, 'nw2_certificates_show_metaboxes'),  	// $callback
			$this->plugin_cpt,                 					// $page
			'normal',                  							// $context
			'high'                     							// $priority
		);
	}
	public function nw2_certificates_show_metaboxes()
	{
		// Use nonce for verification to secure data sending
		wp_nonce_field(basename(__FILE__), $this->plugin_name . '_nonce');


		$this->nw2_certificates_metabox_cf7();
		$this->nw2_certificates_metabox_canvas();
	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function nw2_certificates_metabox_canvas()
	{
		global $post;
		$src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full', false);
		if (!$src)
			return;

		$canvas =  get_post_meta($post->ID,  $this->plugin_cpt . "_canvas", true);

		$canvas_size = $this->calculate_canvas_size();
		//$ratio =  get_post_meta($post->ID,  $this->plugin_cpt . "_proportion", true);
		echo '<input type="hidden" value="' . $canvas_size[2]  . '" name="' . $this->plugin_cpt . '_proportion">';

		include_once(dirname(__FILE__) . '/partials/nw2_certificates_metabox_canvas-display.php');
	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function nw2_certificates_metabox_cf7()
	{
		global $post;

		$cf7 =  get_post_meta($post->ID,  $this->plugin_cpt . "_cf7", true);

		if (!function_exists('wpcf7_contact_form')) {
			return _e('Plugin Contact Form 7 not activated or not installed', 'nw2-certificates');
		}
		$options = array();
		$forms = WPCF7_ContactForm::find();
		$form_used = false;
		foreach ($forms as $form) {
			$array[$form->id()] = $form->title();
			$selected = "";
			if ($form->id() == $cf7) {
				$selected = 'selected';
				$form_used = array(
					'id_form' => $form->id(),
					'title' => $form->title(),
				);
			}
			$options[] = '<option value="' . $form->id() . '" ' . $selected . '>' . $form->title() . '</option>';
		}
		if (empty($array)) {
			$options = array('' => __('No contact form found', 'nw2-certificates'));
		}
		include_once(dirname(__FILE__) . '/partials/nw2_certificates_metabox_cf7-display.php');
	}

	public function calculate_canvas_size()
	{
		global $post;
		$src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full', false);
		if (!$src) {
			return;
		}
		$ratio = $src[1] / $src[2]; // width/height
		$width = 842;
		$height = 595;
		return array($width, $height, $ratio);
	}
	/**
	 * Save the Meta Field
	 *
	 * @param [type] $post_id
	 * @return void
	 */
	public function nw2_certificates_save_meta_fields($post_id)
	{

		global $post;
		//skip auto save
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}
		//check for you post type only
		if ($post && $post->post_type == $this->plugin_cpt) {
			if (isset($_POST[$this->plugin_cpt . "_value"])) {
				update_post_meta($post->ID, $this->plugin_cpt . "_value", $_POST[$this->plugin_cpt . "_value"]);
			}
			if (isset($_POST[$this->plugin_cpt . "_cf7"])) {
				update_post_meta($post->ID, $this->plugin_cpt . "_cf7", $_POST[$this->plugin_cpt . "_cf7"]);
			}
			if (isset($_POST[$this->plugin_cpt . "_canvas"])) {
				update_post_meta($post->ID, $this->plugin_cpt . "_canvas", $_POST[$this->plugin_cpt . "_canvas"]);
			}
			if (isset($_POST[$this->plugin_cpt . "_proportion"])) {
				update_post_meta($post->ID, $this->plugin_cpt . "_proportion", $_POST[$this->plugin_cpt . "_proportion"]);
			}
		}
	}


	/**
	 * Enable parameter injection in contact form 7 shortcode
	 *
	 * @param [type] $out
	 * @param [type] $pairs
	 * @param [type] $atts
	 * @return void
	 */
	function wpcf7_enable_attr($out, $pairs, $atts)
	{
		$my_attr = 'nw2-event';

		if (isset($atts[$my_attr])) {
			$out[$my_attr] = $atts[$my_attr];
		}

		return $out;
	}

	/**
	 * Check if this form submited is using the attribute nw2-event
	 *
	 * @param [type] $wpcf7
	 * @return void
	 */
	public function wpcf7_check_if_plugin_is_used(Object $wpcf7)
	{
		$submission = WPCF7_Submission::get_instance();
		if ($submission) {
			$posted_data = $submission->get_posted_data();
			if ($posted_data['nw2-event']) {
				$id_event = $posted_data['nw2-event'];

				//Gerenare unique key to this submit
				$secretkey = $this->generate_key($posted_data);

				$others = array();
				foreach ($posted_data as $key => $data) {
					if (substr($key, 0, 1) !== '_') {
						$others[$key] = $data;
					}
				}

				//Get CF7 e-mail object
				$mail = $wpcf7->prop('mail');

				$query = get_post($id_event);
				$content = apply_filters('the_content', $query->post_content);
				$content = str_replace('[link-to-print-certificate]', $this->generate_link($id_event, $secretkey, $others), $content);


				//Add what you need
				$mail['body'] = $content;

				// Set back to CF7
				$wpcf7->set_properties(array(
					"mail" => $mail
				));
			}
		}

		return $wpcf7;
	}

	public function generate_link($id_event,  $key = array(),  $others = array())
	{
		$args = array(
			array('id_event' => $id_event),
			$key,
			$others
		);
		$newArray = $this->comprees_array($args);
		$link = $this->print_link . '?' . http_build_query($newArray);
		return $link;
	}


	public function compress_parameters($stringArray)
	{
		$s = strtr(base64_encode(addslashes(gzcompress(serialize($stringArray), 9))), '+/=', '-_,');
		return $s;
	}
	public static function decode_parameters($stringArray)
	{
		$s = unserialize(gzuncompress(stripslashes(base64_decode(strtr($stringArray, '-_,', '+/=')))));
		return $s;
	}

	public function comprees_array(array $args)
	{
		$newArray = array();
		foreach ($args as $array) {
			foreach ($array as $k => $v) {
				$newArray[$k] = $this->compress_parameters($v);
			}
		}
		return $newArray;
	}
	public static function decomprees_array($args)
	{
		$newArray = array();
		foreach ($args as $k => $v) {
			$newArray[$k] = Nw2_Certificates_Admin::decode_parameters($v);
		}
		return $newArray;
	}

	public function generate_key(array $posted_data)
	{
		$first_field = '';

		foreach ($posted_data as $key => $data) {
			if (substr($key, 0, 1) !== '_') {
				$first_field = $data;
				break;
			}
		}
		$key = array(
			'key' => md5($this->plugin_name . $first_field),
			'first_field' => $first_field
		);
		//	$key = md5($this->plugin_name . $first_field);
		return (array) $key;
	}

	public static function check_valid_key($arraykey)
	{
		if (!array_key_exists('key', $arraykey))
			return false;

		if (!array_key_exists('first_field', $arraykey))
			return false;

		$key = $arraykey['key'];
		$first_field = $arraykey['first_field'];

		if (md5(self::$plugin_name_static . $first_field) != $key) {
			return false;
		}
		return true;
	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/nw2-certificates-admin.css', array(), $this->version, 'all');
		if (!wp_script_is('jquery-ui', 'enqueued')) {
			//Why use 1.11?...
			wp_enqueue_style('jquery-ui-1.12.1', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', array(), '1.12.1', 'all');
		}
		//wp_enqueue_style('trumbowyg-css', 'https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.20.0/ui/trumbowyg.min.css', array(), '2.20.0', 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/nw2-certificates-admin.js', array('jquery'), $this->version, false);
		if (!wp_script_is('jquery-ui', 'enqueued')) {
			//Why use 1.11?...
			wp_enqueue_script('jquery-ui-core-1.12.1', 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js', array(), '1.12.1', true);
		}
		//Best WYSIWYG editor ever!
		// wp_enqueue_script('trumbowyg', 'https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.20.0/trumbowyg.min.js', array('jquery'), '2.20.0', true);
		// wp_enqueue_script('trumbowyg-fontsize', 'https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.20.0/plugins/fontsize/trumbowyg.fontsize.min.js', array('trumbowyg'), '2.20.0', true);
	}

	/**
	 * Register the Event custom post type
	 *
	 * @return void
	 */
	public function register_cpt_events()
	{
		$labels = array(
			'name'                  => _x('Events Elegible', 'The events that will be used to send certificates', 'nw2-certificates'),
			'singular_name'         => _x('Event', 'The event that will be used to send certificate', 'nw2-certificates'),
			'menu_name'             => __('Elegible Events', 'nw2-certificates'),
			'name_admin_bar'        => __('Elegible Event', 'nw2-certificates'),
			'archives'              => __('Event Archives', 'nw2-certificates'),
			'attributes'            => __('Event Attributes', 'nw2-certificates'),
			'parent_item_colon'     => __('Parent Event:', 'nw2-certificates'),
			'all_items'             => __('All Events', 'nw2-certificates'),
			'add_new_item'          => __('Add New Event', 'nw2-certificates'),
			'add_new'               => __('Add New', 'nw2-certificates'),
			'new_item'              => __('New Event', 'nw2-certificates'),
			'edit_item'             => __('Edit Event', 'nw2-certificates'),
			'update_item'           => __('Update Event', 'nw2-certificates'),
			'view_item'             => __('View Event', 'nw2-certificates'),
			'view_items'            => __('View Events', 'nw2-certificates'),
			'search_items'          => __('Search Event', 'nw2-certificates'),
			'not_found'             => __('Not found', 'nw2-certificates'),
			'not_found_in_trash'    => __('Not found in Trash', 'nw2-certificates'),
			'featured_image'        => __('Featured Image', 'nw2-certificates'),
			'set_featured_image'    => __('Set featured image', 'nw2-certificates'),
			'remove_featured_image' => __('Remove featured image', 'nw2-certificates'),
			'use_featured_image'    => __('Use as featured image', 'nw2-certificates'),
			'insert_into_item'      => __('Insert into Event', 'nw2-certificates'),
			'uploaded_to_this_item' => __('Uploaded to this Event', 'nw2-certificates'),
			'items_list'            => __('Events list', 'nw2-certificates'),
			'items_list_navigation' => __('Events list navigation', 'nw2-certificates'),
			'filter_items_list'     => __('Filter Events list', 'nw2-certificates'),
		);
		$rewrite = array(
			'slug'                  => __('events', 'nw2-certificates'),
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		);
		$args = array(
			'label'                 => __('Event', 'nw2-certificates'),
			'description'           => __('The holding place for all events.', 'nw2-certificates'),
			'labels'                => $labels,
			'supports'              => array('title', 'editor', 'excerpt', 'thumbnail',),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-calendar',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'rewrite'               => false,
			'capability_type'       => 'post',
		);
		register_post_type($this->plugin_cpt, $args);
	}
}
