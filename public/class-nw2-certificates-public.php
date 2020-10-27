<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/zarausto
 * @since      1.0.0
 *
 * @package    Nw2_Certificates
 * @subpackage Nw2_Certificates/public
 */

use Mpdf\Mpdf;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Nw2_Certificates
 * @subpackage Nw2_Certificates/public
 * @author     Fausto Rodrigo Toloi <fausto@nw2web.com.br>
 */
class Nw2_Certificates_Public
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
	 * The ID of post type.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $post_ID = -692; //NW2 in keyboard numeric

	/**
	 * The ID of post type.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $id_event = false; //NW2 in keyboard numeric
	/**
	 * The ID of post type.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $dpi = 72; //Don't change this!
	/**
	 * The ID of post type.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $Mpdf_args = array(); //NW2 in keyboard numeric

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	public function create_fake_page()
	{
		new Fake_Page(
			array(
				'slug' => $this->plugin_name,
				'post_title' => '',
				'post_ID' => $this->post_ID,
				'post_content' => '',
			)
		);
	}
	public function define_public_hooks(Nw2_Certificates_Loader $loader)
	{
		$loader->add_action('init', $this, 'create_fake_page', 10);
		$loader->add_action('the_post', $this, 'check_post', 10, 1);
		$loader->add_action('template_include', $this, 'pdf_template_include', 10, 1);
	}


	public function check_post($post)
	{
		global $post;
		$post_slug = $post->post_name;
		if ($post_slug == $this->plugin_name) {
			$this->create_pdf();
		}
		return $post;
	}

	public function create_pdf()
	{

		$params = (array) $_GET;
		$paramsdecoded = Nw2_Certificates_Admin::decomprees_array($params);
		$this->id_event = $paramsdecoded['id_event'];



		$this->set_paper_size();

		$this->Mpdf_args['mode'] 						= 'utf-8';
		$this->Mpdf_args['dpi'] 						= $this->dpi;
		// $this->Mpdf_args['debug'] 					= false;
		// $this->Mpdf_args['debugfonts'] 				= false;
		$this->Mpdf_args['showImageErrors'] 			= true;
		$this->Mpdf_args['keep_table_proportions'] 		= false;
		$this->Mpdf_args['curlAllowUnsafeSslRequests'] 	= true; //curlAllowUnsafeSslRequests = true;

		$this->Mpdf_args['margin_left'] 				= 0;
		$this->Mpdf_args['margin_right'] 				= 0;
		$this->Mpdf_args['margin_top'] 					= 0;
		$this->Mpdf_args['margin_bottom'] 				= 0;
		//$this->Mpdf_args['default_font_size'] = 12;
		$this->Mpdf_args['default_font'] 				= 'sans-serif';
		//$this->Mpdf_args['fontdata'] 					= array('sans-serif');

		$mpdf = new \Mpdf\Mpdf(
			$this->Mpdf_args
		);

		if (!Nw2_Certificates_Admin::check_valid_key($paramsdecoded)) {
			$mpdf->WriteHTML('nonono');
			return $mpdf->Output();
		}

		$this->define_background($mpdf);
		$mpdf->SetDisplayMode('fullpage');

		$mpdf->WriteHTML($this->create_layout($paramsdecoded), \Mpdf\HTMLParserMode::HTML_BODY);
		$mpdf->Output();
	}

	public function set_paper_size()
	{
		$this->Mpdf_args['format'] =  'A4-L';
		return;
	}

	public function define_background(Mpdf $mpdf)
	{
		$src = wp_get_attachment_image_src(get_post_thumbnail_id($this->id_event), 'full', false);
		// if (!$src)
		// 	$mpdf->SetDefaultBodyCSS('background', "white");

		//$mpdf->SetDefaultBodyCSS('background', "url('" . $src[0] . "')");
		//$mpdf->SetDefaultBodyCSS('background-image-resize:', 6);
		$src[0] = str_replace("https://certifica.marketeria.com.br/wp-content/uploads","/home/marketeria/www/certifica/wp-content/uploads",$src[0]);
		$html =
			"@page {
			margin: 0%; 						
			margin-header: 0;
			margin-footer: 0;
			background: url('" . $src[0] . "');
			background-image-resize: 6;
		}
		body,div,span,html { 
			/*border:1px solid red;/**/
		}";
		$mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HEADER_CSS);

		$this->Mpdf_args['setAutoTopMargin'] = 'stretch';
		$this->Mpdf_args['autoMarginPadding'] = 0;
	}

	public function create_layout($data)
	{

		$c = "";
		ob_start();
		$canvas =  get_post_meta($this->id_event,  Nw2_Certificates_Admin::_get_plugin_cpt() . "_canvas", true);
		foreach ($data as $key => $value) {
			$canvas = str_replace('[' . $key . ']', $value, $canvas);
		}
		$canvas = str_replace("https://certifica.marketeria.com.br/wp-content/uploads","/home/marketeria/www/certifica/wp-content/uploads",$canvas);
		echo $canvas;

		$c .= ob_get_contents();
		ob_clean();
		return $c;
	}

	public function pdf_template_include($template)
	{
		global $post;
		if ($post) {
			$post_slug = $post->post_name;
			if ($post_slug == $this->plugin_name) {
				return WP_PLUGIN_DIR . "/nw2-certificates" . "/page.php";
			}
		}

		return $template;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/nw2-certificates-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/nw2-certificates-public.js', array('jquery'), $this->version, false);
	}
}
