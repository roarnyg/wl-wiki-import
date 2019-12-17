<?php
/*
    The [author] shortcode contains quite a lot of code, so it gets its own class.
    Singleton class, add it by calling the static "add" method.
    Depends on the LitteraturnettAuthorFields class.
*/


class LitteraturnettAuthorShortcode {
    protected static $instance = null;

    static public function add () {
          if (!$instance) static::$instance = new LitteraturnettAuthorShortcode();
          add_shortcode( 'authors', array(static::$instance,'generate_authorList' ));
    }

    //Add author shortcode
    function generate_authorList( $atts ) {
        $result = "";
        if(isset($_GET["type"])){
            if($_GET["type"] == "municipality" || $_GET["type"] == "period" || $_GET["type"] == "gerne" || $_GET['type'] == 'genre'){
                $result .= $this->generate_authorList_customField($_GET["type"]);
            }elseif($_GET["type"] == "alphabetical"){
                $result .= $this->generate_authorList_alphabetical();
            }elseif($_GET["type"] == "last-searched"){
                $result .= $this->generate_authorList_latestSearch();
            }
        } else {
            $result .= $this->generate_authorList_alphabetical();
        }
        return '<div class="author-list-page">'.$result.'</div>';
    }

    // IOK FIXME TO BE MORE EFFICIENT
    function generate_authorList_customField($type){
        $result = "";
        $fieldId = "";
        $pageTitle = __("Authors by ", 'avia_framework');
        switch ($type) {
            case 'municipality':
                $pageTitle .= __("municipality", 'litterturnett');
                $fieldId = 'municipality';
                break;
            case 'period':
                $pageTitle .= __("period", 'litteraturnett');
                $fieldId = 'period';
                break;
            case 'gerne': // IOK 2019-11-20 wut
            case 'genre':
                $pageTitle .= __("gerne", 'litteraturnett');
                $fieldId = 'genre';
                break;
        }
        $result .='<h1 class="author-title-sort">'.$pageTitle.'</h1>';

        $azRange = array();

        $fieldObject = LitteraturnettAuthorFields::get_field_object($fieldId);
        if($fieldId=='municipality'){
            asort($fieldObject["choices"]);
        }
        foreach ($fieldObject["choices"] as $optionKey=>$optionValue) {
            $string_arr = str_replace(' ', '', $optionValue);
            $result .='<h2 id="move'.$string_arr.'">'.$optionValue.'</h2>';
            $azRange[] = $optionValue;
            $result .= "<ul>";
            $getPostsArgs	= array('post_type'=>array('author'), 'posts_per_page' => -1);
            if($type == "municipality"){
                $getPostsArgs['meta_query'] = array(
                        array(
                            'key'		=> $type,
                            'value'		=> serialize($optionKey),
                            'compare'	=> 'LIKE'
                            )
                        );
            }elseif($type == "gerne"){
                $getPostsArgs['tag'] = sanitize_title($optionKey);
            }elseif($type == "period"){
                $period = explode("-", $optionKey);
                if(count($period)==2){
                    $peCondition = array('relation'		=> 'OR');
                    $period[0] = trim($period[0])=="" ? 0 : intval($period[0]);
                    $period[1] = trim($period[1])=="" ? 9999 : intval($period[1]);
                    array_push($peCondition, array(
                                'key'		=> 'birthyear',
                                'value'		=> $period,
                                'compare'	=> 'BETWEEN'
                                ));
                    $getPostsArgs['meta_query'] = $peCondition;
                }
            }
            if(count($getPostsArgs)>2){
                $posts = get_posts( $getPostsArgs );
                $postArray = array();
                foreach ( $posts as $post ) {
                    $resultPost = array(
                            "id" => $post->ID,
                            "url" => get_post_permalink($post->ID),
                            "title" => $post->post_title,
                            "first_name" => get_field('first_name',$post->ID),
                            "last_name" => get_field('last_name',$post->ID)
                            );
                    $postArray[] = $resultPost;
                }
                uasort($postArray, function($a, $b) {
                        return ($a["last_name"] == $b["last_name"]) ?  $this->np_strcmp($a["first_name"], $b["first_name"]): $this->np_strcmp($a["last_name"], $b["last_name"]);
                        });
                foreach ( $postArray as $post ) {
                    $result .= "<li><a href='".$post["url"]."'>".$post["title"]."</a></li>";
                }
            }
            $result .= "</ul>";
        }

        $result .= '<div class="alphabet-navigation moved-args">';
        foreach ($azRange as $letter)
        {
            $string_arr = str_replace(' ', '', $letter);
            $result .='<a href="#move'.$string_arr.'">'.$letter.'</h2>';
        }
        $result .= '</div>';

        return $result;
    }

    // IOK FIXME TO USE WPDB DIRECTLY TO SAVE MEMORY also sort in PHP
    function generate_authorList_alphabetical(){
        $result = '<h1>'.__("Authors by alphabetical", 'avia_framework').'</h1>';
        $getPostsArgs	= array(
                'post_type'=>array('author'),
                'fields'=>array('ids','post_title'),
                'posts_per_page' => -1,
                'meta_key' => 'last_name',
                'orderby' => "meta_value",
                'order' => "ASC"
                );
        $posts = get_posts( $getPostsArgs );
        $postArray = array();
        foreach ( $posts as $post ) {
            $resultPost = array(
                    "id" => $post->ID,
                    "url" => get_post_permalink($post->ID),
                    "title" => $post->post_title,
                    "first_name" => get_field('first_name',$post->ID),
                    "last_name" => get_field('last_name',$post->ID)
                    );
            $postArray[] = $resultPost;
        }
        $azRange = range('A', 'Z');
        array_push($azRange, 'Æ','Ø','Å');
        $result .= '<div class="alphabet-navigation">';
        foreach ($azRange as $letter)
        {
            $result .='<a href="#alphabet'.$letter.'">'.$letter.'</h2>';
        }
        $result .= '</div>';
        foreach ($azRange as $letter)
        {
            $result .='<h2 id="alphabet'.$letter.'">'.$letter.'</h2>';
            $result .= "<ul>";
            $filtered = array_filter($postArray, create_function('$a', 'return strtoupper(mb_substr($a["last_name"],0,1) )== "' . $letter . '";'));
            uasort($filtered, function($a, $b) {
                    return ($a["last_name"] == $b["last_name"]) ?  $this->np_strcmp($a["first_name"], $b["first_name"]): $this->np_strcmp($a["last_name"], $b["last_name"]);
                    });
            foreach ($filtered as $resultItem) {
                $result .= "<li><a href='".$resultItem["url"]."'>".$resultItem["title"]."</a></li>";
            }
            $result .= "</ul>";
        }
        return $result;
    }
    function np_strcmp($a, $b){
        $f = false;
        $specialCharArr = array('æ','Æ','ø','Ø','å','Å');
        if ((strpos($a, 'Æ') !== false )||(strpos($a, 'æ') !== false )||
                (strpos($a, 'Ø') !== false )||(strpos($a, 'ø') !== false )||
                (strpos($a, 'Å') !== false )||(strpos($a, 'å') !== false )||
                (strpos($b, 'Æ') !== false )||(strpos($b, 'æ') !== false )||
                (strpos($b, 'Ø') !== false )||(strpos($b, 'ø') !== false )||
                (strpos($b, 'Å') !== false )||(strpos($b, 'å') !== false )){
            $f = true;
        }
        if($f == false){
            return strcmp($a, $b);
        }else{
            $len = strlen($a) > strlen($b) ? strlen($a) : strlen($b);
            for ($i = 0; $i < $len ; $i++) {
                $charA = mb_substr($a, $i, 1);
                $charB = mb_substr($b, $i, 1);
                if(in_array($charA, $specialCharArr) && in_array($charB, $specialCharArr)){
                    if(array_search($charA, $specialCharArr) < array_search($charB, $specialCharArr)){
                        return -1;
                    }
                    if(array_search($charA, $specialCharArr) > array_search($charB, $specialCharArr)){
                        return 1;
                    }
                }else{
                    if(strcmp($charA, $charB) != 0){
                        return strcmp($charA, $charB);
                    }
                }
            }
            return strlen($a) - strlen($b);
        }
    }

    // IOK 2019-11-20 This requires the plugin "Search Tracking"
    function generate_authorList_latestSearch(){
        $numberOfResult = 10;
        $result = '<h1>' . __('Last searched authors','litteraturnett') . "</h1>";
        $result .= "<ul class='latest-search'>";
        $latestSearch = array();
        if (function_exists('st_get_latest_search')) {
            $latestSearch = st_get_latest_search($numberOfResult);
        }
        $index=1;
        foreach ($latestSearch as $searchItem) {
            $result .= "<li><span class='numerical-order'>".$index.".</span> <a href='/?s=".$searchItem->search_string."'>".$searchItem->search_string."</a></li>";
            $index++;
        }
        return $result;
    }
}
