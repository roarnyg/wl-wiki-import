<?php

class LitteraturnettAdvancedSearch {

	protected static $instance = null;
	// Make field definitions accessible by name instead of field ID
	protected static $indexedfields = array();

	// This class should only get instantiated with this method. IOK 2019-10-14
	public static function instance()  {
		if (!static::$instance) static::$instance = new LitteraturnettAdvancedSearch();
		return static::$instance;
	}

	public function __construct() {
	}

	public function init () {
		add_action('wp_footer', array($this,'wp_footer'));
		add_filter('get_search_form', array($this,'custom_search_form'));
		// pre_get_search_form to add javascript I guess
		add_shortcode( 'authorFromRegionSelect', array($this,'generate_authorFromRegionSelect' ));
		add_shortcode( 'municipalitySelect', array($this,'generate_municipalitySelect' ));
		add_action( 'pre_get_posts', array($this,'filter_search_results' ));


	}

	public function wp_footer (){
		if (is_search()) {
			echo "<script type='text/javascript'>jQuery(function(){Site.Search.init();});</script>";
		}
	}

	//Add search form custom element
	function custom_search_form($html) {
		$parts = explode("</form>", $html,2);
		$newform = $parts[0] . $this->get_advanced_search_form() . "</form>";
		if (isset($parts[1])) $newform .= $parts[1];
		return $newform;
	}

	function get_advanced_search_form() {
		ob_start();
		?>
			<label class="hidden-item" for="s"><?php _e("Search") ; ?> </label>
			<div class="advance-search-container" id="advanceSearchContainer">
			<a href="#" id="advanceSearchBut"><span class="item-text"><?php _e("Avansert søk",'litteraturnett'); ?></span><span class="ic"></span></a>
			<div class="advance-search-content" id="advanceSearchContent">
			<h2><?php _e("Filter", 'litteraturnett'); ?><a id="checkAllBut" href="#"><?php _e("Check / Uncheck all", 'litteraturnett'); ?></a></h2>
			<ul>
			<li>
			<fieldset>
			<legend><?php _e("Genre",'litteraturnett') ?></legend>
			<div class="scroll-box"><?php echo $this->generate_Option('genre','ge','genre'); ?></div>
			</fieldset>
			</li>
			<li>
			<fieldset>
			<legend><?php _e("Birthyear",'litteraturnett'); ?></legend>
			<div class="scroll-box"><?php echo $this->generate_Option('period','pe','period');?></div>
			</fieldset>
			</li>
			<li>
			<fieldset>
			<legend><?php _e("Gender",'litteraturnett');?></legend>
			<div class="scroll-box"><?php echo $this->generate_Option('gender','gen','gender');?></div>
			</fieldset>
			</li>
			<li>
			<fieldset>
			<legend><?php _e("Municipality",'litteraturnett');?></legend>
			<div class="scroll-box"><?php echo $this->generate_Option('municipality','mu','municipality') ?></div>
			</fieldset>
			</li>
			</ul>
			<a href="javascript:void(0)" id="advanceSearchBoxBut"><?php_e("Search",'litteraturnett')?></a>
			<div class="clear"></div>
			</div>
			</div>
			<?php
			return $html . "\n" . ob_get_clean();
	}

	function generate_Option($fieldId,$checkboxName,$checkboxIdPrefix){
		$result ='';
		$fieldObject = LitteraturnettAuthorFields::get_field_object($fieldId);
		$currentFieldValue = @$_GET[$checkboxName];
		$index=0;



		// IOK sort areas alphabetically if there are no 'separator' areas (that is, areas with null value. This can be used to group areas.
		if($fieldId=='municipality') {
                   $separators = in_array('', array_values($fieldObject['choices']));
                   if (!$separators) asort($fieldObject["choices"]);
		}
		foreach ($fieldObject["choices"] as $optionKey=>$optionValue) {
			if($optionValue==""){
				if($fieldId=='municipality' ){ //Add group on Nordland site
					$result .='<h3>'.$optionKey.'</h3>';
				}
				continue;
			}

			if ($fieldId == 'gender') {
				error_log("option $optionKey - $optionValue");
			}
			$checked = isset( $currentFieldValue) && in_array($optionKey,$currentFieldValue) ? ' checked ' : '';
			$result .='<p><input type="checkbox" name="'.$checkboxName.'[]" value="'.$optionKey.'" id="'.$checkboxIdPrefix.$index.'" ' . $checked. ' /> <label for="'.$checkboxIdPrefix.$index.'">'.$optionValue.'</label></p>';
			$index++;
			if ($fieldId == 'gender') error_log("Index $index");
		}
		return $result;
	}


	function generate_authorFromRegionSelect(){
		$result = '<label for="authorFromRegionSelect" class="hidden-item">'.__("Choose municipality", 'litteraturnett').'</label>';
		$result .= '<select class="styled-selectbox" id="authorFromRegionSelect">';
		$result .= generate_authorFromRegion();
		$result .= '</select>';
		return $result;
	}
	function generate_authorFromRegion(){
		$authors = LitteraturnettAuthorFields::get_field_object('region');
		$result ='<option value="">'.__("Choose a region", 'litteraturnett').'</option>';
		if(!is_null($authors)){
			foreach ($authors["choices"] as $key => $value) {
				$result .='<option value="'.$value.'">'.$key.'</option>';
			}

		}
		return $result;
	}

	//Add municipalitySelect shortcode
	function generate_municipalitySelect( $atts ) {
		$result = '<label for="municipalitySelect" class="hidden-item">'.__("Choose municipality", 'litteraturnett').'</label>';
		$result .= '<select class="styled-selectbox municipality-select" id="municipalitySelect">';
		$result .= $this->generate_municipalityOption();
		$result .= '</select>';
		return $result;
	}

	function generate_municipalityOption() {
		$result ='<option value="">'.__("Choose municipality", 'avia_framework').'</option>';
		$municipalityField = get_field_object('field_568b3b88327a3');

		$choicevalues = array_values($municipalityField["choices"]);
		$separators = in_array('',$choicevalues);

		if(!is_null($municipalityField)){
			if($separators) {
				$count = 1;
				$muLeng  = count($municipalityField["choices"]);
				foreach ($municipalityField["choices"] as $muKey => $muValue) {
					if($muValue==""){
						if($count>1){
							$result .='</optgroup>';
						}
						$result .='<optgroup label="'.$muKey.'">';
					}else{
						if(isset($_GET["mu"]) && $_GET["mu"] == $muValue){
							$result .='<option value="'.$muValue.'" selected>'.$muValue.'</option>';
						}else{
							$result .='<option value="'.$muValue.'">'.$muValue.'</option>';
						}
					}
					if($count == $muLeng){
						$result .='</optgroup>';
					}
					$count++;
				}
			}else{
				$municipalityChoice = $municipalityField["choices"];
				sort($municipalityChoice);
				foreach ($municipalityChoice as $municipality) {
					if($municipality=="") continue;
					if(isset($_GET["mu"]) && $_GET["mu"] == $municipality){
						$result .='<option value="'.$municipality.'" selected>'.$municipality.'</option>';
					}else{
						$result .='<option value="'.$municipality.'">'.$municipality.'</option>';
					}
				}
			}
		}
		return $result;
	}

	// Custom search
	function filter_search_results( $query ) {
		if ( ! $query->is_admin && $query->is_search ) {
			$query->set( 'post_type', 'author'); // Don't use the 'forfatter' category any more
			$metaQueryCondition = array('relation'		=> 'AND');
			if(isset($_GET["mu"])){
				$muCondition = array('relation'		=> 'OR');
				foreach ($_GET["mu"] as $muValue) { // Have to do like this because the ACF extension serialize all data for custom field, can not use IN to compare but have to use LIKE for each
					array_push($muCondition, array(
								'key'		=> 'municipality',
								'value'		=> serialize($muValue),
								'compare'	=> 'LIKE'
								));
				}
				array_push($metaQueryCondition,$muCondition);
			}
			if(isset($_GET["gen"])){
				$genCondition = array('relation'		=> 'OR');
				foreach ($_GET["gen"] as $genValue) {
					array_push($genCondition, array(
								'key'		=> 'gender',
								'value'		=> $genValue
								));
				}
				array_push($metaQueryCondition,$genCondition);
			}
			if(isset($_GET["pe"])){
				$peCondition = array('relation'		=> 'OR');
				foreach ($_GET["pe"] as $peValue) {
					$period = explode("-", $peValue);
					if(count($period)==2){
						$period[0] = trim($period[0])=="" ? 0 : intval($period[0]);
						$period[1] = trim($period[1])=="" ? 9999 : intval($period[1]);
						array_push($peCondition, array(
									'key'		=> 'birthyear',
									'value'		=> $period,
									'compare'	=> 'BETWEEN'
									));
					}
				}
				array_push($metaQueryCondition,$peCondition);
			}
			if(isset($_GET["ge"])){
				$geList = array_map(function($val) { return sanitize_title($val); }, $_GET["ge"]);
				$geTagList = implode(',', $geList);
				$query->set('tag', $geTagList);
			}
			if(!is_null($metaQueryCondition)){
				$query->set('meta_query', $metaQueryCondition);
				if(trim($query->get('s'))==""){
					$query->set("meta_key" , 'last_name');
					$query->set("orderby" , "meta_value");
					$query->set("order" , "ASC");
				}
			}
		}
		return $query;
	}


}
