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
	public function __construct( $plugin_name, $version, $settings ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->settings = $settings;

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
		wp_register_style('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
		wp_enqueue_style( 'jquery-ui' );
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

		wp_enqueue_script( 'jquery-ui-datepicker' );
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
		Mu_Meta_Text::do_saves();
		Mu_Meta_WPEditor::do_saves();
	}

	public function demo() {
		foreach ($this->settings as $meta_set) {
			foreach ($meta_set['fields'] as $slug => $fd) {
				switch ($fd['type']) {
					case 'post_selector':
						Mu_Meta_Post_Selector::create( 
							$slug, 
							$fd['meta_key'], 
							$fd['field_name'], 
							$fd['title'], 
							$fd['desc'], 
							$meta_set['post_type'],  
							$fd['post_type']
						);
					break;
					case 'text':
						Mu_Meta_Text::create( 
							$slug, 
							$fd['meta_key'], 
							$fd['field_name'], 
							$fd['title'], 
							$fd['desc'], 
							$meta_set['post_type']
						);
					break;
					case 'date':
						Mu_Meta_Text::create( 
							$slug, 
							$fd['meta_key'], 
							$fd['field_name'], 
							$fd['title'], 
							$fd['desc'], 
							$meta_set['post_type'],
							'date'
						);
					break;
					case 'editor':
						Mu_Meta_WPEditor::create( 
							$slug, 
							$fd['meta_key'], 
							$fd['field_name'], 
							$fd['title'], 
							$fd['desc'], 
							$meta_set['post_type']
						);
					break;
				}
			}
		}
	}
	public function demo_add_meta_box() {
		// display each metabox
		foreach($this->settings as $meta_set) {
			add_meta_box(
				$meta_set['meta_slug'], 
				$meta_set['meta_title'], 
				array($this, 'display_meta_box'),
				$meta_set['post_type'], 
				$meta_set['context'],
				$meta_set['priority'],
				$meta_set
			);
		}
	}
	public function display_meta_box($post, $param) {
		$meta_set = $param['args'];
		// display each field of metabox
		if (!empty($meta_set['render'])) {
			foreach ($meta_set['render'] as $ren_item) {
				switch ($meta_set['fields'][$ren_item]['type']) {
					case 'tabs': 
					?>
					<section class="generic-tabs">
						<ul class="tabs">
							<?php
							foreach ($meta_set['fields'][$ren_item]['content'] as $tab_slug => $content) {
								?>
								<li>
									<a title="<?php echo $content['title'];?>" href="#<?php echo $tab_slug;?>"><i class="fa fa-home"></i> <?php echo $content['title'];?></a>
								</li>
								<?php
							}
							?>
						</ul>
						<?php
							foreach ($meta_set['fields'][$ren_item]['content'] as $tab_slug => $content) {
								?>
								<div id="<?php echo $tab_slug;?>" class="tab-content">
								<?php 
									foreach ($content['fds'] as $slug) {
										switch ($meta_set['fields'][$slug]['type']) {
											case 'post_selector':
												Mu_Meta_Post_Selector::display( $slug );
											break;
											case 'text':
											case 'date':
												Mu_Meta_Text::display( $slug );
											break;
											case 'editor':
												Mu_Meta_WPEditor::display( $slug );
											break;
										}
									}
								?>
								</div>
								<?php
							}
						?>
					</section>

					<?php
					break;

					case 'post_selector':
						Mu_Meta_Post_Selector::display( $ren_item );
					break;
					case 'text':
					case 'date':
						Mu_Meta_Text::display( $ren_item );
					break;
					case 'editor':
						Mu_Meta_WPEditor::display( $ren_item );
					break;

				}
				echo apply_filters('after_field_'.$ren_item, '');
			}
		}
	}
}
