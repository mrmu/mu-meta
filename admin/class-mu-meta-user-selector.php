<?php
class Mu_Meta_User_Selector_Instance {
	private $field_id = null;
	private $meta_key = '';
	private $form_field_name = '';
	private $form_field_label = '';
	private $form_field_desc = '';
	private $post_post_type = 'post';
	private $item_role = '';
	private $additional_query_params = array();

	function __construct($field_id, $meta_key, $form_field_name, $form_field_label, $form_field_desc, $post_post_type='post', $item_role='', $additional_query_params=array()) {
		$this->field_id = $field_id;
		$this->meta_key = $meta_key;
		$this->form_field_name = $form_field_name;
		$this->form_field_label = $form_field_label;
		$this->form_field_desc = $form_field_desc;
		$this->post_post_type = $post_post_type;
		$this->item_role = $item_role;
		$this->additional_query_params = $additional_query_params;
	}

	function get_addition_query_params() {
		return $this->additional_query_params;
	}

	function display() {
		global $post;
		$the_id = get_post_meta($post->ID, $this->meta_key, true);
		?>
		<div class="mu-meta-field">
			<div class="mu-meta-label">
				<label for="<?php echo $this->form_field_name; ?>"><?php echo $this->form_field_label; ?></label>
				<div class="mu-meta-desc">
					<?php echo $this->form_field_desc; ?>
				</div>
			</div>
			<div class="mu-meta-input">
				<select 
					class="mu-meta-user-selector" 
					name="<?php echo $this->form_field_name; ?>" 
					data-role="<?php echo $this->item_role; ?>" 
					data-mu-meta-user-selector-field-id="<?php echo $this->field_id; ?>" 
				>
					<?php
					// if (!empty($user_ids)) {
					// 	foreach ($user_ids as $the_id) {
							$user_info = get_userdata($the_id);
							echo '<option value="'.$the_id.'" selected="selected">'.$user_info->display_name.'</option>';
					// 	}
					// }
					?>
				</select>
			</div>
		</div>
	<?php
	}

	public function save() {
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

			// if ( is_array( $_POST[$this->form_field_name] ) ) {
				$user_ids = $_POST[$this->form_field_name];
				update_post_meta($post->ID, $this->meta_key, $user_ids);
			// }
	    }
	}
}

class Mu_Meta_User_Selector {
	private static $instances = array();
	
	public static function user_lookup() {
	    global $wpdb;
	    $result = array();
	    $search = esc_attr($_REQUEST['q']);
	    $role = $_REQUEST['role'];
	    $field_id = $_REQUEST['mu_meta_user_selector_field_id'];

		// search user
		$args = array(
			'role__in' => array($role),
			'search' => '*'.$search.'*',
			'search_columns' => array('user_login', 'user_email', 'display_name')
		);
		$wp_user_query = new WP_User_Query($args);
		$vendors = $wp_user_query->get_results();
		$vendor_ids = array();
		if ( ! empty( $vendors ) ) {
			foreach ( $vendors as $vendor ) {
				// $vendor_ids[] = $author->ID;
				$result['results'][] = array(
					'id' => $vendor->ID,
					'text' => $vendor->display_name,
				);
			}
		}

	    echo json_encode($result);
	    die();
	}

	public static function create( $field_id, $meta_key, $form_field_name, $form_field_label, $post_post_type='post', $item_role='', $additional_query_params=array() ) {
		$new_instance = new Mu_Meta_User_Selector_Instance($field_id, $meta_key, $form_field_name, $form_field_label, $post_post_type, $item_role, $additional_query_params);
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

