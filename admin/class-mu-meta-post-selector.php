<?php
class Mu_Meta_Post_Selector_Instance {
	private $field_id = null;
	private $meta_key = '';
	private $form_field_name = '';
	private $form_field_label = '';
	private $form_field_desc = '';
	private $post_post_type = 'post';
	private $item_post_type = 'post';
	private $additional_query_params = array();

	function __construct($field_id, $meta_key, $form_field_name, $form_field_label, $form_field_desc, $post_post_type='post', $item_post_type='post', $additional_query_params=array()) {
		$this->field_id = $field_id;
		$this->meta_key = $meta_key;
		$this->form_field_name = $form_field_name;
		$this->form_field_label = $form_field_label;
		$this->form_field_desc = $form_field_desc;
		$this->post_post_type = $post_post_type;
		$this->item_post_type = $item_post_type;
		$this->additional_query_params = $additional_query_params;
	}

	function get_addition_query_params() {
		return $this->additional_query_params;
	}

	function display() {
		global $post;
		$post_ids = get_post_meta($post->ID, $this->meta_key, true);
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

			if ( is_array( $_POST[$this->form_field_name] ) ) {
				$post_ids = $_POST[$this->form_field_name];
				update_post_meta($post->ID, $this->meta_key, $post_ids);
			}
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

