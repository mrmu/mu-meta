<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://audilu.com
 * @since      1.0.0
 *
 * @package    Mu_Meta
 * @subpackage Mu_Meta/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Mu_Meta
 * @subpackage Mu_Meta/includes
 * @author     Audi Lu <khl0327@gmail.com>
 */
class Mu_Meta {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Mu_Meta_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	protected $settings;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct($settings) {
		if ( defined( 'MU_META_VERSION' ) ) {
			$this->version = MU_META_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'mu-meta';
		if (!empty($settings)){
			$this->settings = $settings;
		}else{
			$this->settings = array(
				array(
					'meta_slug' => 'meta-related-articles',
					'meta_title' => '相關文章',
					'post_type' => 'post',
					'context' => 'normal', // normal, side, advanced
					'priority' => 'default', // default, high, low 
					'fields' => array(
						'fd-ra' => array(
							'type' => 'post_selector',
							'meta_key' => 'fd-ra',
							'title' => 'FD RA',
							'field_name' => 'fd_name_ra',
							'desc' => '相關文章選取',
							'post_type' => 'post',
						),
						'fd-hala' => array(
							'type' => 'post_selector',
							'meta_key' => 'fd-hala',
							'title' => 'FD Hala',
							'field_name' => 'fd_name_hala',
							'desc' => '哈啦文章選取',
							'post_type' => 'post',
						),
						'fd-myname' => array(
							'type' => 'text',
							'meta_key' => 'fd-myname',
							'title' => 'My Name',
							'field_name' => 'fd_name_myname',
							'desc' => '填你的名字',
						),
						'fd-mydate' => array(
							'type' => 'date',
							'meta_key' => 'fd-mydate',
							'title' => 'My Date',
							'field_name' => 'fd_name_mydate',
							'desc' => '填你的日子',
						),
						'fd-content' => array(
							'type' => 'editor',
							'meta_key' => 'fd-content',
							'title' => 'My Content',
							'field_name' => 'fd_name_content',
							'desc' => '填你的內容',
						),
						'fd-content2' => array(
							'type' => 'editor',
							'meta_key' => 'fd-content2',
							'title' => '通知信模版',
							'field_name' => 'fd_name_content2',
							'desc' => '填你的模版內容',
						),
						'demo-tab' => array(
							'type' => 'tabs',
							'content' => array (
								'normal' => array(
									'title' => 'Normal', 
									'fds' => array('fd-ra', 'fd-myname')
								), 
								'notification' => array(
									'title' => 'DEMO', 
									'fds' => array('fd-hala', 'fd-mydate', 'fd-content')
								), 
							)
						)
					),
					'render' => array(
						'fd-content2', 'demo-tab'
					),
				),
				array(
					'meta_slug' => 'meta-hala-articles',
					'meta_title' => '哈拉文章',
					'post_type' => 'post',
					'context' => 'side', // normal, side, advanced
					'priority' => 'default', // default, high, low 
					'fields' => array(

					),
				)
			);
		}
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Mu_Meta_Loader. Orchestrates the hooks of the plugin.
	 * - Mu_Meta_i18n. Defines internationalization functionality.
	 * - Mu_Meta_Admin. Defines all hooks for the admin area.
	 * - Mu_Meta_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mu-meta-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mu-meta-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mu-meta-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mu-meta-public.php';

		/**
		 * Form fields
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mu-meta-post-selector.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mu-meta-text.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mu-meta-wp-editor.php';

		$this->loader = new Mu_Meta_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Mu_Meta_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Mu_Meta_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Mu_Meta_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_settings() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action('wp_ajax_mu_meta_post_selector_lookup', $plugin_admin,  'post_lookup');
		$this->loader->add_action('save_post', $plugin_admin,  'post_save');

		$this->loader->add_action('admin_init', $plugin_admin, 'demo');
		$this->loader->add_action('add_meta_boxes', $plugin_admin, 'demo_add_meta_box');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Mu_Meta_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_settings() {
		return $this->settings;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Mu_Meta_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
