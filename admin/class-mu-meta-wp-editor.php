<?php
class Mu_Meta_WPEditor_Instance {
	private $field_id = null;
	private $meta_key = '';
	private $form_field_name = '';
	private $form_field_label = '';
	private $post_post_type = 'post';
	private $additional_query_params = array();

	function __construct($field_id, $meta_key, $form_field_name, $form_field_label, $form_field_desc, $post_post_type, $additional_query_params=array()) {
		$this->field_id = $field_id;
		$this->meta_key = $meta_key;
		$this->form_field_name = $form_field_name;
		$this->form_field_label = $form_field_label;
		$this->form_field_desc = $form_field_desc;
		$this->post_post_type = $post_post_type;
		$this->additional_query_params = $additional_query_params;
	}

	function get_addition_query_params() {
		return $this->additional_query_params;
	}

	function display() {
		global $post;
		$text_value = get_post_meta($post->ID, $this->meta_key, true);
		$input_classes = array('mu-meta-wpeditor');
		$input_class = implode(' ', $input_classes);
		?>
		<div class="mu-meta-field">
			<div class="mu-meta-label">
				<label for="<?php echo $this->form_field_name; ?>"><?php echo $this->form_field_label; ?></label>
				<div class="mu-meta-desc">
					<?php echo $this->form_field_desc; ?>
				</div>
			</div>
			<div class="mu-meta-editor">
				<?php
				$args = array("textarea_name" => $this->form_field_name);

				wp_editor( esc_html($text_value), $this->form_field_name, $args );
				?>
			</div>
		</div>

	<?php
	}

	function save() {

		global $post;
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	        return;
	    }
	    if ( isset( $_POST['post_type'] ) && $this->post_post_type == $_POST['post_type'] ) {
	        // Check the user's permissions.
	        if ( ! current_user_can( 'edit_post', $post->ID ) ) {
	            return;
	        }
	        if ( ! isset( $_POST[$this->form_field_name] ) ) {
	            return;
	        }
	        if ( empty( $_POST[$this->form_field_name] ) ) {
	        	delete_post_meta($post->ID, $this->meta_key);
				return;	        	
	        }

			$input_text_value = $_POST[$this->form_field_name];
			update_post_meta($post->ID, $this->meta_key, $input_text_value);
	    }
	}
}

class Mu_Meta_WPEditor {
	private static $instances = array();

	public static function create( $field_id, $meta_key, $form_field_name, $form_field_label, $post_post_type='post', $additional_query_params=array() ) {
		$new_instance = new Mu_Meta_WPEditor_Instance($field_id, $meta_key, $form_field_name, $form_field_label, $post_post_type, $additional_query_params);
		self::$instances[$field_id] = $new_instance;
		return $field_id;
	}
	public static function display( $field_id ) {
		self::$instances[$field_id]->display();
	}
	public static function do_saves( ) {
		foreach (self::$instances as $this_instance) {
			$this_instance->save();
		}
	}
}

