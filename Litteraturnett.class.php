<?php 
/*
   Litteraturnett: 

   This singleton class handles the behaviour of the 'author' custom post type and related features.

 */

class Litteraturnett {
	public $settings= array();
	protected static $instance = null;

	function __construct() {
		$this->settings = get_option('litteraturnett_options', array());
	}

	// This class should only get instantiated with this method. IOK 2019-10-14 
	public static function instance()  {
		if (!static::$instance) static::$instance = new Litteraturnett();
		return static::$instance;
	}

	private function translation_dummy () {
		print __('Dynamic string', 'litteraturnett');
	}

	// Log; use Woo if present but otherwise just log to the error log
	public function log ($what,$type='info') {
		if (function_exists('wc_get_logger')) {
			$logger = wc_get_logger();
			$context = array('source'=>'litteraturnett');
			$logger->log($type,$what,$context);
		} else {
			error_log($what);
		}
	}

	public function init () {
		$this->custom_post_types();
		$this->add_shortcodes();
		$this->add_author_page_actions();
		$this->enqueue_styles();
		$this->enqueue_scripts();
		$this->create_topics_hierarchical_taxonomy();
		add_filter('template_include', array($this,'template_include'),99,1);
		add_action( 'wp_head', array($this,'add_facebook_meta_for_author_post') , 2 );


	}

	function add_facebook_meta_for_author_post() {
		if ( is_single('author')) {
			global $post;
			$imgUrl = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID),"large");
			if(is_array($imgUrl)){
				echo '<meta  property="og:url" content="' . get_permalink($post->ID) . '" />';
				echo '<meta  property="og:title" content="' . $post->post_title . '" />';
				echo '<meta  property="og:image" content="' . $imgUrl[0] . '" />';
			}
		}
	}




	public function create_topics_hierarchical_taxonomy() {
		$labels = array(
				'name' => _x( 'Wiki category groups', 'taxonomy general name','litteraturnett' ),
				'singular_name' => _x( 'Wiki category group', 'taxonomy singular name','litteraturnett' ),
				'search_items' =>  __( 'Search groups' ,'litteraturnett'),
				'all_items' => __( 'All groups','litteraturnett' ),
				'parent_item' => __( 'Parent group','litteraturnett' ),
				'parent_item_colon' => __( 'Parent group:' ,'litteraturnett'),
				'edit_item' => __( 'Edit group' ,'litteraturnett'),
				'update_item' => __( 'Update group','litteraturnett' ),
				'add_new_item' => __( 'Add New group','litteraturnett' ),
				'new_item_name' => __( 'New group Name' ,'litteraturnett'),
				'menu_name' => __( 'Wiki category groups' ,'litteraturnett'),
			       );
		// IOK added 'author' 2019-11-13
		register_taxonomy('groups',array('post','author'), array(
					'hierarchical' => true,
					'labels' => $labels,
					'show_ui' => true,
					'show_admin_column' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'group' ),
					));

	}


	public function enqueue_styles() {
		wp_enqueue_style('maginfic-popup', plugins_url( 'css/magnific-popup.css', __FILE__ ), array(), filemtime( plugin_dir_path( __FILE__ ) . 'css/magnific-popup.css'));
		wp_enqueue_style('Litteraturnett', plugins_url( 'css/litteraturnett.css', __FILE__ ), array('magnific-popup'), filemtime( plugin_dir_path( __FILE__ ) . 'css/litteraturnett.css'));
	}

	public function enqueue_scripts() {
		wp_enqueue_script('magnific-popup', plugins_url( 'js/jquery.magnific-popup.min.js', __FILE__ ), array('jquery'), filemtime( plugin_dir_path( __FILE__ ) . 'js/jquery.magnific-popup.min.js'));
		wp_register_script('litteraturnett', plugins_url( 'js/litteraturnett.js', __FILE__ ), array('jquery','magnific-popup'), filemtime( plugin_dir_path( __FILE__ ) . 'js/litteraturnett.js'));
		wp_localize_script('litteraturnett', 'LitteraturnettSettings',
				array('sourceperson'=>__('By', 'litteraturnett'),
					'source'      =>__('Source', 'litteraturnett'),
					'wikiApiUrl'  => $this->get_wikipedia_api()
				     ));
		wp_enqueue_script('litteraturnett');

	}

	public function add_author_page_actions() {
		$page_controller_class = apply_filters('litteraturnett_author_page_controller_class', 'LitteraturnettAuthorPageController');
		$page_controller =  $page_controller_class::instance();

		add_action('litteraturnett_before_single_author', array($page_controller,'before_single_author'));
		add_action('litteraturnett_after_single_author', array($page_controller,'after_single_author'));

		add_action('litteraturnett_author_before_main_content', array($page_controller,'before_main_content'),10);
		add_action('litteraturnett_author_after_main_content', array($page_controller,'after_main_content'),10);
		add_action('litteraturnett_before_single_author_summary', array($page_controller,'before_single_author_summary'),10);
		add_action('litteraturnett_after_single_author_summary', array($page_controller,'author_page_navigation'),9);
		add_action('litteraturnett_after_single_author_summary', array($page_controller,'after_single_author_summary'),10);
		add_action('litteraturnett_single_author_summary', array($page_controller,'before_author_page_info'),10);
		add_action('litteraturnett_single_author_summary', array($page_controller,'author_page_image'),11);
		add_action('litteraturnett_single_author_summary', array($page_controller,'author_page_detail'),12);
		add_action('litteraturnett_single_author_summary', array($page_controller,'after_author_page_info'),13);

		add_action('litteraturnett_single_author_content', array($page_controller,'single_author_content'),10);

		add_action('litteraturnett_author_sidebar', array($page_controller,'author_sidebar'),10);

		add_action('litteraturnett_author_after_page_wrapper', array($page_controller,'author_taglist'),10);
		add_action('litteraturnett_author_after_page_wrapper', array($page_controller,'author_related_book'),11);
		add_action('litteraturnett_author_after_page_wrapper', array($page_controller,'author_comments'),12);
	}


	public function template_include($template) {
		if (!is_singular('author')) return $template;
		if (basename($template) == 'single-author.php') return $template;
		return dirname(__FILE__) . "/templates/single-author.php";
	}

	/* Like 'get template part', but defaults to the plugins' template parts */
	/* Does the work of both get_template_part and locate_template with load=true */
	public static function get_template_part ($slug,$name=null) {
		do_action( "get_template_part_{$slug}", $slug, $name );
		$templates = array();
		$name      = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "{$slug}-{$name}.php";
		}

		$templates[] = "{$slug}.php";
		do_action( 'get_template_part', $slug, $name, $templates );
		$default = dirname(__FILE__) . "/templates/" . $templates[0];
		$found = locate_template($templates,false);
		if (!$found && file_exists($default)) {
			$found = $default;
		}
		if ($found) {
			load_template($found, false);
		}
	}

	public function add_shortcodes() {
		LitteraturnettAuthorShortcode::add();
	}

	public function plugins_loaded () {
		$ok = load_plugin_textdomain('litteraturnett', false, basename( dirname( __FILE__ ) ) . "/languages");
	}

	public function admin_init () {
		add_action('admin_enqueue_scripts', array($this,'admin_enqueue_scripts'));
		register_setting('litteraturnett_options','litteraturnett_options', array($this,'validate'));

                add_filter( 'manage_author_posts_columns', array($this,'np_author_manage_sortable_columns' ));
                add_filter( 'manage_author_posts_sortable_columns', array($this,'np_author_title_not_sortable' ));
                add_filter( 'manage_edit-author_sortable_columns', array($this,'np_author_sortable_column' ));
                add_action( 'pre_get_posts', array($this,'np_author_orderby' ));
                add_action( 'manage_author_posts_custom_column', array($this,'np_author_column_content'), 10, 2 );
	}
	public function admin_enqueue_scripts ($suffix) {
		// Nothing yet
	}

	public function admin_menu () {
		add_options_page(__('Litteraturnett', 'litteraturnett'), __('Litteraturnett','litteraturnett'), 'manage_options', 'litteraturnett_options',array($this,'toolpage'));
	}
	// Helper function for creating an admin notice.
	public function add_admin_notice($notice) {
		add_action('admin_notices', function() use ($notice) { echo "<div class='notice notice-info is-dismissible'><p>$notice</p></div>"; });
	}


	// Register Custom Post Type
	public function custom_post_types() {
		$labels = array(
				'name'                  => _x( 'Authors', 'Post Type General Name', 'litteraturnett' ),
				'singular_name'         => _x( 'Author', 'Post Type Singular Name', 'litteraturnett' ),
				'menu_name'             => __( 'Authors', 'litteraturnett' ),
				'name_admin_bar'        => __( 'Author', 'litteraturnett' ),
				'archives'              => __( 'Author Archives', 'litteraturnett' ),
				'attributes'            => __( 'Author Attributes', 'litteraturnett' ),
				'parent_item_colon'     => __( 'Parent Item:', 'litteraturnett' ),
				'all_items'             => __( 'All Authors', 'litteraturnett' ),
				'add_new_item'          => __( 'Add New Author', 'litteraturnett' ),
				'add_new'               => __( 'Add New', 'litteraturnett' ),
				'new_item'              => __( 'New Author', 'litteraturnett' ),
				'edit_item'             => __( 'Edit Author', 'litteraturnett' ),
				'update_item'           => __( 'Update Author', 'litteraturnett' ),
				'view_item'             => __( 'View Author', 'litteraturnett' ),
				'view_items'            => __( 'View Authors', 'litteraturnett' ),
				'search_items'          => __( 'Search Authors', 'litteraturnett' ),
				'not_found'             => __( 'Not found', 'litteraturnett' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'litteraturnett' ),
				'featured_image'        => __( 'Featured Image', 'litteraturnett' ),
				'set_featured_image'    => __( 'Set featured image', 'litteraturnett' ),
				'remove_featured_image' => __( 'Remove featured image', 'litteraturnett' ),
				'use_featured_image'    => __( 'Use as featured image', 'litteraturnett' ),
				'insert_into_item'      => __( 'Insert into item', 'litteraturnett' ),
				'uploaded_to_this_item' => __( 'Uploaded to this item', 'litteraturnett' ),
				'items_list'            => __( 'Items list', 'litteraturnett' ),
				'items_list_navigation' => __( 'Author list navigation', 'litteraturnett' ),
				'filter_items_list'     => __( 'Filter authors list', 'litteraturnett' ),
				);
		$rewrite = array(
				'slug'                  => 'writer',
				'with_front'            => false,
				'pages'                 => true,
				'feeds'                 => true);

		$args = array(
				'label'                 => __( 'Author', 'litteraturnett' ),
				'description'           => __( 'Authors', 'litteraturnett' ),
				'labels'                => $labels,
				'supports'              => array( 'title', 'editor', 'thumbnail', 'comments', 'revisions', 'custom-fields', 'page-attributes', 'post-formats' ),
				'taxonomies'            => array( 'category', 'post_tag', 'group' ),
				'hierarchical'          => false,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 5,
				'menu_icon'             => 'dashicons-admin-post',
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => 'writers',
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'query_var' => 'writer',
				'rewrite' => $rewrite,
				'capability_type'       => 'post',
				'show_in_rest'          => true,
				);
		register_post_type( 'author', $args );

	}

	// Return this installations' wikipedia link 
	public static function get_wikipedia() {
		return static::instance()->settings['wikipedia'];
	}
	public static function get_wikipedia_api() {
		return trailingslashit(static::get_wikipedia()) . 'w/api.php';
	}

	// This is the main options-page for this plugin. The classes VippsLogin and WooLogin adds more options to this screen just to 
	// keep the option-data local to each class. IOK 2019-10-14
	public function toolpage () {
		if (!is_admin() || !current_user_can('manage_options')) {
			die(__("Insufficient privileges",'litteraturnett'));
		}
		$options = get_option('litteraturnett_options'); 
		$wikis = array('https://nn.wikipedia.org','https://no.wikipedia.org/');
		$last_updated_page = intval($options['last_updated_page']);

		?>
			<div class='wrap'>
			<h2><?php _e('Litteraturnett', 'litteraturnett'); ?></h2>

			<?php do_action('admin_notices'); ?>

			<form action='options.php' method='post'>
			<?php settings_fields('litteraturnett_options'); ?>
			<table class="form-table" style="width:100%">

			<tr>
			<td><?php _e('Wikipedia Source', 'litteraturnett'); ?></td>
			<td width=30%>
			<select required id=wikipedia name="litteraturnett_options[wikipedia]">
			<option value=""><?php _e('Select one', 'litteraturnett'); ?></option>
			<?php foreach ($wikis as $wiki): ?>
			<option <?php if ($options['wikipedia']==$wiki) echo ' selected '; ?> value="<?php echo esc_attr($wiki);?>"><?php echo esc_html($wiki); ?></option>
			<?php endforeach; ?>
			</select>
			</td>
			<td><?php _e('Select the Wikipedia you will use as a source for this site.','litteraturnett'); ?></td>
			</tr>

			<tr>
			<td><?php _e('Wikipedia Source', 'litteraturnett'); ?></td>
			<td width=30%>
			<?php wp_dropdown_pages(array('post_status'=>'publish,private','selected'=>$last_updated_page, 'echo'=>1, 'show_option_none'=>__('None chosen', 'litteraturnett'), 'name'=>'litteraturnett_options[last_updated_page]')); ?>
			</td>
			<td><?php _e('Select an (empty! and probably private) page to be used to show last updated authors','litteraturnett'); ?></td>

			</table>
			<div> <input type="submit" style="float:left" class="button-primary" value="<?php _e('Save Changes') ?>" /> </div>

			</form>

			</div>

			<?php
	}

	// Validating user options. Currenlty a trivial function. IOK 2019-10-19
	public function validate ($input) {
		$current =  get_option('litteraturnett_options'); 

		$valid = array();
		foreach($input as $k=>$v) {
			switch ($k) {
				default: 
					$valid[$k] = $v;
			}
		}
		return $valid;
	}

	// The activation hook will create the session database tables if they do not or if the database has been upgraded. IOK 2019-10-14
	public function activate () {
		// Options
		$default = array();
		add_option('litteraturnett_options',$default,false);
		// This is only for the first versions IOK 2019-11-19
		// "Promote" all posts of category "Forfatter" to the 'author' custom post type.
		global $wpdb;
		$catid = get_cat_ID('Forfatter');
		if (!is_wp_error($catid) && $catid) {
			$q = "update `{$wpdb->prefix}term_relationships` o join `{$wpdb->prefix}posts` p on (p.id=o.object_id) set p.post_type='author' WHERE `term_taxonomy_id` = %d and p.post_type='post'";
			$query = $wpdb->prepare($q,$catid);
			$wpdb->query($query);
		}

	}
	public static  function deactivate () {
		global $wpdb;
		$wpdb->query("UPDATE {$wpdb->prefix}posts SET post_type='post' WHERE post_type='author'");
		LitteraturnettWikiImport::wiki_deactivation();
	}
	public static function uninstall() {
	}



        function np_author_manage_sortable_columns( $sortable_columns ) {
                // Let's also make the film rating column sortable
                $sortable_columns[ 'author_last_updated' ] = __('Last Updated','litteraturnett');

                return $sortable_columns;
        }

        function slug_title_not_sortable( $cols ) {
                $sortable_columns[ 'author_last_updated' ] = __('Last Updated','litteraturnett');
                return $cols;
        }
        function np_author_sortable_column( $columns ) {
                $columns['author_last_updated'] = 'author_last_updated';
                return $columns;
        }
        function np_author_orderby( $query ) {
                if( ! is_admin() )
                        return;

                $orderby = $query->get( 'orderby');

                if( 'author_last_updated' == $orderby ) {
                        $query->set('meta_key','author_last_updated');
                        $query->set('orderby','meta_value_num');
                }
        }
        function np_author_column_content( $column_name, $post_id ) {

                if ( 'author_last_updated' != $column_name )
                        return;
                //Get number of slices from post meta
                $slices = get_post_meta($post_id, 'author_last_updated', true);
                echo $slices;
        }




}
