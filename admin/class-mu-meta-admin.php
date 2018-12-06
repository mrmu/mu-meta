<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://audilu.com
 * @since      1.0.0
 *
 * @package    Mu_Meta
 * @subpackage Mu_Meta/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mu_Meta
 * @subpackage Mu_Meta/admin
 * @author     Audi Lu <khl0327@gmail.com>
 */
class Mu_Meta_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mu_Meta_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mu_Meta_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style('select2', plugin_dir_url( __FILE__ ) . 'js/select2/dist/css/select2.min.css', array(), '');
		wp_enqueue_style( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'css/mu-meta-admin.css', 
			array(), 
			filemtime( (dirname( __FILE__ )) . '/css/mu-meta-admin.css' ), 
			'all' 
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mu_Meta_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mu_Meta_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script(
			'select2', 
			plugin_dir_url( __FILE__ ) . 'js/select2/dist/js/select2.js', 
			array('jquery'),
			'', 
			true
		);
		wp_enqueue_script(
			'select2-custom', 
			plugin_dir_url( __FILE__ ) . 'js/select2-custom.js', 
			array('jquery', 'select2'), 
			filemtime( (dirname( __FILE__ )) . '/js/select2-custom.js' ), 
			true
		);
		wp_enqueue_script( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'js/mu-meta-admin.js', 
			array( 'jquery' ),
			filemtime( (dirname( __FILE__ )) . '/js/mu-meta-admin.js' ), 
			false );

	}

	public function post_lookup() {
		Mu_Meta_Post_Selector::post_lookup();
	}
	public function post_save() {
		Mu_Meta_Post_Selector::do_saves();
	}

	public function demo() {
		$this->settings = array(
			array(
				'slug' => 'related-articles',
				'title' => '相關文章',
				'field_name' => 'demo_related_articles',
				'type' => 'post_selector',
				'post_post_type' => 'post',
				'item_post_type' => 'post',
				'context' => 'normal', // normal, side, advanced
				'priority' => 'default', // default, high, low 
			),
			array(
				'slug' => 'hala-articles',
				'title' => '哈拉文章',
				'field_name' => 'demo_hala_articles',
				'type' => 'post_selector',
				'post_post_type' => 'post',
				'item_post_type' => 'post',
				'context' => 'side', // normal, side, advanced
				'priority' => 'default', // default, high, low 
			)
		);

		foreach ($this->settings as $set) {
			if ($set['type'] === 'post_selector') {
				Mu_Meta_Post_Selector::create( 
					$set['slug'], 
					$set['slug'], 
					$set['field_name'], 
					$set['title'], 
					$set['post_post_type'],  
					$set['item_post_type']
				);
			}
		}
	}
	public function demo_add_meta_box() {
		foreach($this->settings as $set) {
			if ($set['type'] === 'post_selector') {
				add_meta_box(
					$set['slug'], 
					$set['title'], 
					array($this, 'display_meta_box'),
					$set['post_post_type'], 
					$set['context'],
					$set['priority'],
					$set
				);
			}
		}
	}
	public function display_meta_box($post, $param) {
		$set = $param['args'];
		if ($set['type'] === 'post_selector') {
			Mu_Meta_Post_Selector::display( $set['slug'] );
		}
	}
}
