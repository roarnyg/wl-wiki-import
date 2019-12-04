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
                add_filter('template_include', array($this,'template_include'),99,1);
	}

        public function enqueue_styles() {
            wp_enqueue_style('Litteraturnett', plugins_url( 'css/litteraturnett.css', __FILE__ ), array(), filemtime( plugin_dir_path( __FILE__ ) . 'css/litteraturnett.css'));
        }

        public function enqueue_scripts() {
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
            error_log("iverok $slug, $name");
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
            error_log("iverok got $default and $found");
            if (!$found && file_exists($default)) {
                 $found = $default;
            }
            if ($found) {
                 error_log("iverok loading $found");
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
	}
	public static function uninstall() {
	}

}
