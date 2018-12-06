<?php
class Mu_Meta_Post_Selector_Instance {
	private $field_id = null;
	private $meta_key = '';
	private $form_field_name = '';
	private $form_field_label = '';
	private $post_post_type = 'post';
	private $item_post_type = 'post';
	private $additional_query_params = array();
	
	function __construct($field_id, $meta_key, $form_field_name, $form_field_label, $post_post_type='post', $item_post_type='post', $additional_query_params=array()) {
		$this->field_id = $field_id;
		$this->meta_key = $meta_key;
		$this->form_field_name = $form_field_name;
		$this->form_field_label = $form_field_label;
		$this->post_post_type = $post_post_type;
		$this->item_post_type = $item_post_type;
		$this->additional_query_params = $additional_query_params;
	}
	function get_addition_query_params() {
		return $this->additional_query_params;
	}
	/*
	 * Note that we're using Select2 which, for AJAX-powered selects uses a hidden field as starting point
	 * and that the value should be a comma-separated list
	 */
	function display() {
		global $post;
	    $current_item_ids = get_post_meta( $post->ID, $this->meta_key, false );
	    // Some entries may be arrays themselves!
	    $processed_item_ids = array();
	    foreach ($current_item_ids as $this_id) {
	        if (is_array($this_id)) {
	            $processed_item_ids = array_merge( $processed_item_ids, $this_id );
	        } else {
	            $processed_item_ids[] = $this_id;
	        }
	    }
	    if (is_array($processed_item_ids) && !empty($processed_item_ids)) {
	        $processed_item_ids = implode(',', $processed_item_ids);
	    } else {
	        $processed_item_ids = '';
	    }
	?>
	    <p>
	        <label for="<?php echo $this->form_field_name; ?>"><?php echo $this->form_field_label; ?></label>

	<?php
	$post_ids = get_post_meta($post->ID, $this->field_id, true);
	?>
			<select 
				class="mu-meta-post-selector" 
				name="<?php echo $this->form_field_name; ?>[]" 
				data-post-type="<?php echo $this->item_post_type ?>" 
				data-mu-meta-post-selector-field-id="<?php echo $this->field_id; ?>" 
				multiple
			>
			<?php
			if (!empty($post_ids)) {
				foreach ($post_ids as $the_id) {
					echo '<option value="'.$the_id.'" selected="selected">'.get_the_title($the_id).'</option>';
				}
			}
			?>
			</select>
	    </p>
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
	        /* OK, its safe for us to save the data now. */
	        
	        // Make sure that it is set.
	        if ( ! isset( $_POST[$this->form_field_name] ) ) {
	            return;
	        }
	        // If it's set but empty, the lists may have been deleted, so we need to delete existing meta values
	        if ( empty( $_POST[$this->form_field_name] ) ) {
	        	delete_post_meta($post->ID, $this->meta_key);
				return;	        	
	        }
	        // The Select2 with multiple option submits a comma-separated list of vaules
	        // but we want to store each ID as a separate meta item (for compatibility with existing
	        // options and queries - note that this is compatible with how the meta-box
	        // plugin handles multiple selects)
	        // if (strpos($_POST[$this->form_field_name], ',') === false) {
	        //     // No comma, must be single value - still needs to be in an array for now
	        //     $post_ids = array( $_POST[$this->form_field_name] );
	        // } else {
	        //     // There is a comma so it's explodable
	        //     $post_ids = explode(',', $_POST[$this->form_field_name]);
			// }
			if (is_array($_POST[$this->form_field_name])) {
				$post_ids = $_POST[$this->form_field_name];
				update_post_meta($post->ID, $this->meta_key, $post_ids);
			}
	        // // Delete all existing entries
	        // delete_post_meta($post->ID, $this->meta_key);
	        // // Add new entries
	        // if (is_array($post_ids) && !empty($post_ids)) {
	        // 	foreach($post_ids as $this_id) {
	        // 		add_post_meta($post->ID, $this->meta_key, $this_id, false );
	        // 	}
	        // }
	    }
	}
}

class Mu_Meta_Post_Selector {
	private static $instances = array();
	
	public static function post_lookup() {
	    global $wpdb;
	    $result = array();
	    $search = like_escape($_REQUEST['q']);
	    $post_type = $_REQUEST['post_type'];
	    $field_id = $_REQUEST['mu_meta_post_selector_field_id'];

		// Don't forget that the callback here is a closure that needs to use the $search from the current scope
	    add_filter('posts_where', function( $where ) use ($search) {
	    							$where .= (" AND post_title LIKE '%" . $search . "%'");
	    							return $where;
	    						});
	    $default_query = array(
	    					'posts_per_page' => -1,
	    					'post_status' => array('publish', 'draft', 'pending', 'future', 'private'),
	    					'post_type' => $post_type,
	    					'order' => 'ASC',
	    					'orderby' => 'title',
	    					'suppress_filters' => false,
	    				);
	    $custom_query = self::$instances[$field_id]->get_addition_query_params();
	    $merged_query = array_merge( $default_query, $custom_query );
	    $posts = get_posts( $merged_query );
	    // We'll return a JSON-encoded result. 
	    foreach ($posts as $this_post) {
	        $post_title = $this_post->post_title;
	        $id = $this_post->ID;
	        $result['results'][] = array(
	        				'id' => $id,
	        				'text' => $post_title,
	        				);
	    }
	    echo json_encode($result);
	    die();
	}

	/*
	 * This creates a new instance, stores it, and prints the form field. It returns the instance ID.
	 *
	 * Parameters:
	 *   $field_id - this is the 'name' of the field - used to identify it for printing or saving - it must be unique!
	 *   $meta_key - the meta_key fo fetch/save data to/from
	 *   $form_field_name - the name attribute of the form field to be created
	 *   $form_field_label - the label text for the form field
	 *   $post_post_type - the post type of the post we're creating the field for
	 *   $item_post_type - the post type of the things to appear in the list
	 *   $additional_query_params - any additional query params for generating the list
	 *
	 * Returns the id of the created instance as passed in
	 */
	public static function create( $field_id, $meta_key, $form_field_name, $form_field_label, $post_post_type='post', $item_post_type='post', $additional_query_params=array() ) {
		$new_instance = new Mu_Meta_Post_Selector_Instance($field_id, $meta_key, $form_field_name, $form_field_label, $post_post_type, $item_post_type, $additional_query_params);
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

