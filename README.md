# Mu Meta
用於建立後台設定用元件 (metaboxes)

## Features
* 支援元件：
    * text
    * date
    * editor
    * tabs
    * post selector
    * user selector

## Usage
* 以 set_mu_meta($settings) 建立 metaboxes
* 因為要註冊 ajax functions 和 enqueue 一些 js，set_mu_meta 不可太晚才被執行，建議在 admin_init, priority 99999 之前，因為 99999 會初始化 mu meta 各元件：
```
$this->loader->add_action( 'admin_init', $plugin_admin, 'set_mu_meta_fds');
```
* User Selector 設定參考：
```
	public function set_mu_meta_fds() {
		if (function_exists('set_mu_meta')) {
			$meta_settings = array(
				array(
					'meta_slug' => 'author-account',
					'meta_title' => '使用者',
					'post_type' => 'product',
					'context' => 'side', // normal, side, advanced
					'priority' => 'default', // default, high, low 
					'fields' => array(
						'fd-author' => array(
							'type' => 'user_selector',
							'meta_key' => 'author-account',
							'title' => 'author Account',
							'field_name' => 'fd_author_account',
							'desc' => '選取帳號',
							'role' => 'my_author',
						)
					),
					'render' => array(
						'fd-author'
					),
				),
			);
			set_mu_meta($meta_settings);
		}
	}
```
* 其他元件設定參考：
```
$settings = array(
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
```
