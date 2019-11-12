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

	public function custom_post_types() {
		$labels = array(
				'name'                  => _x( 'Authors', 'Post Type General Name', 'litteraturnett' ),
				'singular_name'         => _x( 'Author', 'Post Type Singular Name', 'litteraturnett' ),
				'menu_name'             => __( 'Authors', 'litteraturnett' ),
				'name_admin_bar'        => __( 'Authors', 'litteraturnett' ),
				'archives'              => __( 'Author Archives', 'litteraturnett' ),
				'all_items'             => __( 'All Authors', 'litteraturnett' ),
				'add_new_item'          => __( 'Add New Author', 'litteraturnett' ),
				'add_new'               => __( 'Add New', 'litteraturnett' ),
				'new_item'              => __( 'New Author', 'litteraturnett' ),
				'edit_item'             => __( 'Edit Author', 'litteraturnett' ),
				'update_item'           => __( 'Update Author', 'litteraturnett' ),
				'view_item'             => __( 'View Autho', 'litteraturnett' ),
				'search_items'          => __( 'Search Author', 'litteraturnett' ),
				'not_found'             => __( 'Not found', 'litteraturnett' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'litteraturnett' ),
				'items_list'            => __( 'Author list', 'litteraturnett' ),
				'items_list_navigation' => __( 'Author list navigation', 'litteraturnett' ),
				'filter_items_list'     => __( 'Filter author list', 'litteraturnett' ),
				);
		$rewrite = array(
				'slug'                  => 'author',
				'with_front'            => false,
				'pages'                 => true,
				'feeds'                 => true,
				);
		$args = array(
				'label'                 => __( 'Author', 'litteraturnett' ),
				'description'           => __( 'Author Description', 'litteraturnett' ),
				'labels'                => $labels,
				'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes', 'post-formats', ),
				'taxonomies'            => array( 'category', 'post_tag' ),
				'hierarchical'          => false,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 20,
				'menu_icon'             => 'dashicons-admin-post',
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => 'author',
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'rewrite'               => $rewrite,
				'capability_type'       => 'post',
			     );
		register_post_type( 'author', $args );

	}

	// This is the main options-page for this plugin. The classes VippsLogin and WooLogin adds more options to this screen just to 
	// keep the option-data local to each class. IOK 2019-10-14
	public function toolpage () {
		if (!is_admin() || !current_user_can('manage_options')) {
			die(__("Insufficient privileges",'litteraturnett'));
		}
		$options = get_option('litteraturnett_options'); 
		$wikis = array('https://nn.wikipedia.org','https://nb.wikipedia.org/');
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
	}

	public static  function deactivate () {
	}
	public static function uninstall() {
	}

}
