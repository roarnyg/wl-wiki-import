<?php

// Used to get field object definitions IOK 2019-11-21
global $LitteraturnettAuthorFields;

//Add custom script on page
function custom_script_in_footer(){
    if (is_search()) {
        echo "<script type='text/javascript'>jQuery(function(){Site.Search.init();});</script>";
    }
}
add_action( 'wp_footer', 'custom_script_in_footer' );

//Add search form custom element
function custom_search_form($html) {
  return $html . "\n" . get_advanced_search_form();
}
function get_advanced_search_form() {
ob_start();
<label class="hidden-item" for="s"><?php _e("Search") ; ?> </label>
<div class="advance-search-container" id="advanceSearchContainer">
        <a href="#" id="advanceSearchBut"><span class="item-text"><?php _e("Avansert søk",'litteraturnett'); ?></span><span class="ic"></span></a>
        <div class="advance-search-content" id="advanceSearchContent">
        <h2><?php _e("Filter", 'litteraturnett'); ?><a id="checkAllBut" href="#"><?php _e("Check / Uncheck all", 'litteraturnett'); ?></a></h2>
        <ul>
        <li>
        <fieldset>
        <legend><?php _e("Genre",'litteraturnett') ?></legend>
        <div class="scroll-box"><?php echo generate_Option('genre','ge','genre'); ?></div>
        </fieldset>
        </li>
        <li>
        <fieldset>
        <legend><?php _e("Birthyear",'litteraturnett'); ?></legend>
        <div class="scroll-box"><?php generate_Option('period','pe','period');?></div>
        </fieldset>
        </li>
        <li>
        <fieldset>
        <legend><?php _e("Gender",'litteraturnett');?></legend>
        <div class="scroll-box"><?php generate_Option('gender','gen','gender');?></div>
        </fieldset>
        </li>
        <li>
        <fieldset>
        <legend><?php _e("Municipality",'litteraturnett');?></legend>
        <div class="scroll-box"><?php echo generate_Option('municipality','mu','municipality') ?></div>
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
add_filter('get_search_form', 'custom_search_form');
// pre_get_search_form to add javascript I guess

function generate_Option($fieldId,$checkboxName,$checkboxIdPrefix){
    $result ='';
    $fieldObject = LitteraturnettAuthorFields::get_field_object($fieldId);
    $currentFieldValue = $_GET[$checkboxName];
    $index=0;
    if($fieldId=='municipality' && WEB_SITE !== "NO"){ //Do not sort on Nordland site as need to support option group
        asort($fieldObject["choices"]);
    }
    foreach ($fieldObject["choices"] as $optionKey=>$optionValue) {
        if($optionValue==""){
            if($fieldId=='municipality' ){ //Add group on Nordland site
                $result .='<h3>'.$optionKey.'</h3>';
            }
            continue;
        }
        if(isset( $currentFieldValue) && in_array($optionKey,$currentFieldValue)){
            $result .='<p><input type="checkbox" name="'.$checkboxName.'[]" value="'.$optionKey.'" id="'.$checkboxIdPrefix.$index.'" checked/> <label for="'.$checkboxIdPrefix.$index.'">'.$optionValue.'</label></p>';
        }else{
            $result .='<p><input type="checkbox" name="'.$checkboxName.'[]" value="'.$optionKey.'" id="'.$checkboxIdPrefix.$index.'"/> <label for="'.$checkboxIdPrefix.$index.'">'.$optionValue.'</label></p>';
        }
        $index++;
    }
    return $result;
}

//Change search placeholder text
function change_search_form_placeholder()
{
    $params['placeholder'] = __('Search by name, city, genre or other...', 'avia_framework');
    $params['search_id'] = "s";
    $params['form_action'] = home_url( '/' );
    $params['ajax_disable'] = false;
    return $params;
}
add_filter('avf_frontend_search_form_param', 'change_search_form_placeholder' );

function generate_authorFromRegionSelect(){
    $result = '<label for="authorFromRegionSelect" class="hidden-item">'.__("Choose municipality", 'avia_framework').'</label>';
    $result .= '<select class="styled-selectbox" id="authorFromRegionSelect">';
    $result .= generate_authorFromRegion();
    $result .= '</select>';
    return $result;
}
function generate_authorFromRegion(){
    $authors = LitteraturnettAuthorFields::get_field_object('region');
    $result ='<option value="">'.__("Choose a region", 'avia_framework').'</option>';
    if(!is_null($authors)){
        foreach ($authors["choices"] as $key => $value) {
            $result .='<option value="'.$value.'">'.$key.'</option>';
        }

    }
    return $result;
}
add_shortcode( 'authorFromRegionSelect', 'generate_authorFromRegionSelect' );
//Add municipalitySelect shortcode
function generate_municipalitySelect( $atts ) {
    $result = '<label for="municipalitySelect" class="hidden-item">'.__("Choose municipality", 'avia_framework').'</label>';
    $result .= '<select class="styled-selectbox municipality-select" id="municipalitySelect">';
    $result .= generate_municipalityOption();
    $result .= '</select>';
    return $result;
}
add_shortcode( 'municipalitySelect', 'generate_municipalitySelect' );
function generate_municipalityOption() {
    $result ='<option value="">'.__("Choose municipality", 'avia_framework').'</option>';
    $municipalityField = LitteraturnettAuthorFields::get_field_object('municipality');
    if(!is_null($municipalityField)){
        if(WEB_SITE  === 'NO'){ // support optiongroup for Nordland site
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
if ( ! function_exists( 'bb_filter_search_results' ) )
{
    add_action( 'pre_get_posts', 'bb_filter_search_results' );
    function bb_filter_search_results( $query )
    {
        if ( ! $query->is_admin && $query->is_search )
        {
            $query->set( 'category__in', array(AUTHOR_CATEGORY_ID) );
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


function add_facebook_meta_for_author_post() {
    if ( is_author_post() ) {
        global $post;
        $imgUrl = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID),"large");
        if(is_array($imgUrl)){
            echo '<meta  property="og:url" content="' . get_permalink($post->ID) . '" />';
            echo '<meta  property="og:title" content="' . $post->post_title . '" />';
            echo '<meta  property="og:image" content="' . $imgUrl[0] . '" />';
        }
    }
}
add_action( 'wp_head', 'add_facebook_meta_for_author_post' , 2 );


add_filter( 'manage_post_posts_columns', 'np_author_manage_sortable_columns' );
function np_author_manage_sortable_columns( $sortable_columns ) {
    // Let's also make the film rating column sortable
    $sortable_columns[ 'author_last_updated' ] = 'Last Updated';

    return $sortable_columns;
}
add_filter( 'manage_post_posts_sortable_columns', 'np_author_title_not_sortable' );
function slug_title_not_sortable( $cols ) {
    $sortable_columns[ 'author_last_updated' ] = 'Last Updated';
    return $cols;
}
add_filter( 'manage_edit-post_sortable_columns', 'np_author_sortable_column' );
function np_author_sortable_column( $columns ) {
    $columns['author_last_updated'] = 'author_last_updated';
    return $columns;
}
add_action( 'pre_get_posts', 'np_author_orderby' );
function np_author_orderby( $query ) {
    if( ! is_admin() )
        return;

    $orderby = $query->get( 'orderby');

    if( 'author_last_updated' == $orderby ) {
        $query->set('meta_key','author_last_updated');
        $query->set('orderby','meta_value_num');
    }
}
add_action( 'manage_post_posts_custom_column', 'np_author_column_content', 10, 2 );
function np_author_column_content( $column_name, $post_id ) {

    if ( 'author_last_updated' != $column_name )
        return;
    //Get number of slices from post meta
    $slices = get_post_meta($post_id, 'author_last_updated', true);
    echo $slices;
}
