<?php
class LitteraturnettWikiImport {
    private static $instance;
    public $wikidata = null;
    public $cron_import_page_size = 5;

    // This class should only get instantiated with this method. IOK 2019-10-14 
    public static function instance()  {
        if (!static::$instance) static::$instance = new LitteraturnettWikiImport();
        return static::$instance;
    }

    // Get wikipedia configuration settings based on the current API endpoint
    public function get_wiki_data() {
        if (!empty($this->wikidata)) return $this->wikidata;
        $api = Litteraturnett::instance()->get_wikipedia_api();
        if (!$api) return array();
        $lang = 'no';
        $data = array();
        $data['WIKIPEDIA_API_ENDPOINT'] = $api;
        if (preg_match("!https://nn\.!", $api)) {
            $lang = 'nn';
        }
        $data['lang'] = $lang;
        if ($data['lang'] === 'nn') {
            $data['WIKIPEDIA_INFOBOX_CLASS']= 'infobox';
            $data['WIKIPEDIA_TERM_CATEGORY']= 'Kategori';
            $data['WIKIPEDIA_TERM_BIRTHDAY']= 'Fødde i';
            $data['WIKIPEDIA_TERM_DEADDAY']= 'Døde i';
            $data['WIKIPEDIA_TERM_MUNICIPALITY']= 'Forfattarar frå (.*)';
            $data['WIKIPEDIA_TERM_MALE']= 'Einskildmenn';
            $data['WIKIPEDIA_TERM_FEMALE']= 'Einskildkvinner';
        } else {
            $data['WIKIPEDIA_INFOBOX_CLASS']= 'infoboks';
            $data['WIKIPEDIA_TERM_CATEGORY']= 'Kategori';
            $data['WIKIPEDIA_TERM_BIRTHDAY']= 'Fødsler i';
            $data['WIKIPEDIA_TERM_DEADDAY']= 'Dødsfall i';
            $data['WIKIPEDIA_TERM_MUNICIPALITY']= 'Personer fra (.*) kommune';
            $data['WIKIPEDIA_TERM_MALE']= 'Menn';
            $data['WIKIPEDIA_TERM_FEMALE']= 'Kvinner';
        }
        $this->wikidata = $data;
        return $this->wikidata;
    }

    function wiki_api_admin_default_setup() {
        add_options_page(__('Wiki API Import', 'wl-wiki-import'), __('Wiki API Import', 'wl-wiki-import'), 'manage_options', 'wiki_api_import_default_form', array($this,'wiki_api_import_default_form'));
    }

    /**
     * Function to add plugin scripts
     */
    public function wiki_api_register_script() {
        if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'wiki_api_import_default_form') {
            wp_enqueue_script('wiki_api_script', plugins_url('js/wiki-import-script.js', __FILE__), array('jquery'), filemtime(dirname(__FILE__) . "/js/wiki-import-script.js" ));
        }
    }

    /**
     * Function to add plugin css
     */
    public function wiki_api_register_css() {
        if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'wiki_api_import_default_form') {
            wp_enqueue_style('wiki_api_style', plugins_url('css/wiki-import-style.css', __FILE__));
        }
    }

    /**
     * Generate complete wiki api endpoint uri
     * @param $wiki_host : the root url of wiki
     */
    function wiki_api_endpoint_uri_generate($wiki_host, $wiki_folder, $api_name, $key_word, $filter_type, $filter_option) {
        $errors            = '';
        $wiki_api_options  = get_option('wiki_api_options');
        $wiki_host_default = $wiki_api_options["wiki_host"];
        if ($wiki_host_default != $wiki_host) {
            $wiki_host_default = $wiki_host;
        }

        $wiki_api_full_uri = $wiki_host_default;
        if (!empty($wiki_folder)) {
            $wiki_api_full_uri = $wiki_api_full_uri . '/' . $wiki_folder;
        }

        if (!empty($api_name)) {
            $wiki_api_full_uri = $wiki_api_full_uri . '/' . $api_name . '?action=query';
        }

        if (!empty($key_word)) {
            $wiki_api_full_uri = $wiki_api_full_uri . '&titles=' . $key_word;
        }

        if (!empty($filter_type) && !empty($filter_option)) {
            // $filter_type could be 'list', 'prop', or 'meta'.
            $wiki_api_full_uri = $wiki_api_full_uri . '&' . $filter_type . '=' . $filter_option;
        }

        $wiki_api_full_uri = $wiki_api_full_uri . '&format=json';
        return $wiki_api_full_uri;
    }

    /**
     * Function to init the wiki search form for importing
     */
    function wiki_api_import_default_form() {

        $this->wiki_api_register_script();
        $this->wiki_api_register_css();
        $wiki_api_uri =  Litteraturnett::instance()->get_wikipedia_api();

        ?>
            <div class="wiki-api-search-form wrapper">
            <div class="wiki-api-search-title">
            <h1><?php _e("Import from Wikipedia : ", 'wl-wiki-import')?></h1>
            </div>
            <table class="form-table" id="wikiSearchForm">
            <tbody>
            <tr>
            <th scope="row"><?php _e("Search : ", 'wl-wiki-import')?></td>
            <td><input type="text" class="regular-text" id="wikiApiSearchText" /></td>
            </tr>
            <tr class="hidden" id="wikiAdvanceSearchField">
            <td>
            <select name="" id="wikiAdvanceSearchCondition" class="wiki-advance-search-selectbox">
            <option value="And">And</option>
            <option value="Or">Or</option>
            <option value="Not in">Not in</option>
            </select>
            </td>
            <td><input type="text" class="regular-text" id="wikiApiSearchExtraText" /></td>
            </tr>
            <tr>
            <th scope="row"><?php _e("Search Type: ", 'wl-wiki-import')?></td>
            <td>
            <input id="search-type-name" checked class="search-type" type="checkbox" name="search-type[]" /> <?php _e("Name", 'wl-wiki-import')?>
            <input id="search-type-category" class="search-type" type="checkbox" name="search-type[]" /> <?php _e("Category", 'wl-wiki-import')?>
            <!-- <input id="search-type-sub-categories" class="search-type" type="checkbox" name="search-type[]" /> <?php _e("Sub categories", 'wl-wiki-import')?>	 -->
            <input type="button" class="button wiki-advance-search-button" id="wiki-api-import-advance-search" value="<?php _e("Advance search", 'wl-wiki-import')?>"/>
            </td>
            </tr>
            <tr >
            <td colspan="2">
            <input  class="button button-primary"  type="button" id="wiki-api-search-button" value="<?php _e("Search", 'wl-wiki-import')?>" wiki-api-uri="<?php _e($wiki_api_uri, 'wl-wiki-import');?>" />
            </td>
            </tr>
            <tr>
            <td colspan="2">
            <div class="card  wiki-api-search-results" style="height: 300px;    overflow: scroll;">
            <!-- Fetch data results in here -->
            </div>
            </td>
            </tr>
            <tr>
            <td colspan="2" class="wiki-api-import-action">
            <input type="button" class="button button-primary" id="wiki-api-import-select-all" value="<?php _e("Select All", 'wl-wiki-import')?>"/>
            <input type="button"  class="button button-primary"  id="wiki-api-import-button" value="<?php _e("Import", 'wl-wiki-import')?>"/>
            <span class="import-loading hidden" id="importLoading"><img src="/wp-admin/images/wpspin_light.gif" alt="loading"><span></span></span>
            </td>
            </tr>
            </tbody>
            </table>
            </div>
            <?php
    }

    function wiki_api_save_file($name, $filename, $url) {

        $uploaddir  = wp_upload_dir();
        $uploadfile = $uploaddir['path'] . '/' . basename($filename);

        echo 'Upload File to: ' . $uploadfile . "\r\n";

        $contents = file_get_contents($url);
        $savefile = fopen($uploadfile, 'w');
        fwrite($savefile, $contents);
        fclose($savefile);

        $img_title = preg_replace('/\.[^.]+$/', '', $name);
        $wp_filetype = wp_check_filetype(basename($filename), null);

        $attachment = array(
                'guid'           => $uploaddir['url'] . '/' . basename($filename),
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => $img_title ,
                'post_content'   => '',
                'post_status'    => 'inherit',
                );
        $attach_id = wp_insert_attachment($attachment, $uploadfile);

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';

        $imagenew = get_post($attach_id);

        $fullsizepath = get_attached_file($imagenew->ID);
        $attach_data  = wp_generate_attachment_metadata($attach_id, $fullsizepath);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }

    function wiki_api_import($request_data, $force_update = true) {
        $wikidata = $this->get_wiki_data();
        if (!$wikidata) {
             $this->wiki_log("Could not import - cannot get wikipedia data");
             return false;
        } 

        $queryPageIds      = array();
        $result            = array();
        $result['success'] = array();
        $result['fail']    = array();
        $wikiCategoryGroup = array();
        $list_id 			= array();
        foreach ($request_data as $pageId => $page) {
            if (!$page) {
                continue;
            }


            /* for testing with 1 id
               if($page != '177073'){
               continue;
               }else{
               $force_update = true;
               }
             */

            $query                = array();
            $query['action']      = 'query';
            $query['prop']        = 'categories|categoryinfo|extracts|links|pageimages|imageinfo|categorieshtml';
            $query['pithumbsize'] = 500;
            $query['format']      = 'json';
            $query['pageids']     = $page;
            $query['clshow']	  = '!hidden'; //increases the limit of categoires to 50
            $query['cllimit']	  = 50; //increases the limit of categoires to 50
            $query['pllimit']	  = 500; //increases the limit of links to show to 500

            $resultAPI = $this->wiki_api_curl('GET', $query);
            $data = json_decode($resultAPI, true);

            if (empty($data)) {
                $result['fail'][] = "{$page}. Can not parse data";
                continue;
            }
            if (!isset($data['query'])) {
                $result['fail'][] = "{$page}. Query is empty";
                continue;
            }

            $i = 1;
            if (!isset($data['query']['pages'])) {
                $result['fail'][] = "{$page}. Query Page is empty";
                continue;
            }

            $wikiCategoryGroup = $this->getWikiCategory();

            foreach ($data['query']['pages'] as $pageId => $pageContent) {				
                $isNewImport = false;
                $postTitle  = sanitize_text_field($pageContent['title']);
                $pageid     = $pageId;
                $categories = $pageContent['categories'];
                $thumbnail  = null;
                $pageimage  = null;
                $queryPosts = get_posts(array(
                            'numberposts' => -1,
                            'post_type'   => 'author',
                            'meta_query'  => array(
                                'relation' => 'AND',
                                array(
                                    'key'     => 'page_id',
                                    'value'   => $pageid,
                                    'compare' => '=',
                                    ),
                                ),
                            ));

                // Don't update when Auto update  is false
                if (isset($queryPosts[0]) && !get_field('auto_update', $queryPosts[0]->ID) && !$force_update) {
                    continue;
                }

                if (isset($pageContent['thumbnail'])) {
                    $thumbnail = $pageContent['thumbnail'];
                    $pageimage = $pageContent['pageimage'];
                }

                $query                = array();
                $query['action']      = 'parse';
                $query['prop']        = 'sections|parsetree|revid|text|categories|categorieshtml';
                $query['format']      = 'json';
                $query['pageids']     = implode("|", $queryPageIds);
                $query['clshow']	  = '!hidden'; //increases the limit of categoires to 50
                $query['cllimit']	  = 50; //increases the limit of categoires to 50
                $query['pllimit']	  = 500; //increases the limit of links to show to 500 
                $query['pageid']      = $pageid;
                $query['generatexml'] = '';
                $parseResult          = $this->wiki_api_curl('GET', $query);
                $parseData            = json_decode($parseResult, true);

                if(isset($_GET["test"])){
                    print_r($parseData);
                }
                // Do Import To Wordpress Here

                $post = array(
                        'post_title'     => $postTitle,
                        'post_status'    => 'publish',
                        'post_type'      => 'author',
                        'post_author'    => 1,
                        'comment_status' => 'open',
                        );
                if (count($queryPosts) > 0) {
                    $postData              = $queryPosts[0];
                    $post_id               = $postData->ID;
                    $post['ID']            = $post_id;
                    $revid                 = get_field('revid', $post_id);
                    $post['post_category'] = wp_get_post_categories($postData->ID);
                    $post['post_date']	   = get_the_date("Y-m-d H:i:s",$postData->ID);
                    // update
                } else {
                    $authorcat = get_category_by_slug('forfatter');
                    $authorcat = $authorcat ? $authorcat : get_category_by_slug('author');
                    if ($authorcat && !is_wp_error($authorcat)) {
                      $post['post_category'] = array($authorcat->term_id);
                    }
                    $revid                 = -1;
                    // new
                    $isNewImport = true;
                }

                //For only update thumb
                /*
                   if($update_only_image){
                   if(isset($thumbnail) && !has_post_thumbnail( $post_id )){
                   echo 'update thumbnail post -'. $post_id . '<br>';
                   $filename_notconverthtf8 = $pageimage;
                   $filename = wiki_api_parse_utf8($pageimage);
                   $filename = str_replace($utf8char, $non_utf8char, $filename);

                   $attach_id = wiki_api_save_file($pageimage, $filename, $thumbnail['source']);
                   if ($attach_id) {
                   set_post_thumbnail($post_id, $attach_id);
                   }
                   }

                   }
                 */

                if (!empty($parseData) && isset($parseData['parse']) && ($force_update || $revid != $parseData['parse']['revid'])) {

                    $description = $parseData['parse']['text']["*"];
                    if (!is_null($description)) {
                        $descriptionDOM = new DOMDocument();
                        $descriptionDOM->loadHTML(mb_convert_encoding($description, 'HTML-ENTITIES'));
                        $xpath           = new DomXPath($descriptionDOM);
                        $infobox_results = $xpath->query("//table[contains(@class, '" . $wikidata['WIKIPEDIA_INFOBOX_CLASS'] . "')]");
                        if ($infobox = $infobox_results->item(0)) {
                            //remove the node the same way
                            $infobox->parentNode->removeChild($infobox);
                        }
                        $dl_results = $xpath->query("//dl[position()=1]");
                        if ($dl = $dl_results->item(0)) {
                            if ($dl->getLineNo() == 1) {
                                //only remove dl tag if it's the first element on the dom after remove infobox
                                //remove the node the same way
                                $dl->parentNode->removeChild($dl);
                            }
                        }
                        $toc_results = $xpath->query("//div[contains(@class, 'toc')]");
                        if ($toc = $toc_results->item(0)) {
                            //remove the node the same way
                            $toc->parentNode->removeChild($toc);
                        }
                        $navboks_results = $xpath->query("//table[contains(@class, 'navboks')]");
                        if ($navboks = $navboks_results->item(0)) {
                            //remove the node the same way
                            $navboks->parentNode->removeChild($navboks);
                        }
                        /*
                           $thumb_results = $xpath->query("//div[contains(@class, 'thumb')]");
                           if ($thumb = $thumb_results->item(0)) {
                        //remove the node the same way
                        $thumb->parentNode->removeChild($thumb);
                        }
                         */
                        $editsection_results = $xpath->query("//span[contains(@class, 'mw-editsection')]");
                        foreach ($editsection_results as $editsection) {
                            $editsection->parentNode->removeChild($editsection);
                        }
                        $wikiLinks_results = $xpath->query("//a[contains(@href, 'wiki') and not(contains(@href, 'nn.wikipedia')) and not(contains(@href, 'no.wikipedia')) ]");
                        foreach ($wikiLinks_results as $wikiLink) {
                            while ($wikiLink->hasChildNodes()) {
                                $child = $wikiLink->removeChild($wikiLink->firstChild);
                                $wikiLink->parentNode->insertBefore($child, $wikiLink);
                            }
                            $wikiLink->parentNode->removeChild($wikiLink);
                        }
                        $wikiLinks_results = $xpath->query("//a[contains(@href, '/w/')]");
                        foreach ($wikiLinks_results as $wikiLink) {
                            while ($wikiLink->hasChildNodes()) {
                                $child = $wikiLink->removeChild($wikiLink->firstChild);
                                $wikiLink->parentNode->insertBefore($child, $wikiLink);
                            }
                            $wikiLink->parentNode->removeChild($wikiLink);
                        }
                        $bodyContent          = $descriptionDOM->documentElement->lastChild;
                        $post['post_content'] = $descriptionDOM->saveHTML($bodyContent);

                        //Save excerpt for post
                        $anchorLinks_results = $xpath->query("//a[contains(@href, '#')]");
                        foreach ($anchorLinks_results as $anchorLink) {
                            $anchorLink->parentNode->removeChild($anchorLink);
                        }

                        $rightClass_results = $xpath->query("//div[contains(@class, 'tright')]");
                        foreach ($rightClass_results as $rightClass) {
                            $rightClass->parentNode->removeChild($rightClass);
                        }

                        $postExcerpt          = strip_tags($descriptionDOM->saveHTML());
                        $postExcerpt          = explode("Bibliografi", $postExcerpt)[0];
                        $postExcerpt          = html_entity_decode($postExcerpt);
                        $postExcerpt          = str_replace("\n", " ", $postExcerpt);
                        $postExcerpt          = (strlen($postExcerpt) > 350) ? mb_substr($postExcerpt, 0, 350) . " [...]" : $postExcerpt;
                        $post['post_excerpt'] = $postExcerpt;
                    }

                    $post_id = wp_insert_post($post, $wp_error = true);
                   
                    if (is_wp_error($post_id)) {
                        $msg = $post_id->get_error_message();
                        $this->wiki_log("Error importing: $msg");
                        die("Could not import post");
                    }

                    // Update Custom fields
                    update_field('page_id', $pageid, $post_id);
                    update_field('revid', $parseData['parse']['revid'], $post_id);

                    //Update FIRSTNAME and LASTNAME field from postTitle
                    $currentFirstName = get_field("first_name", $post_id);
                    $currentLastName  = get_field("last_name", $post_id);

                    $currentBookFirstName = get_field('book_first_name', $post_id);
                    $currentBookLastName  = get_field('book_last_name', $post_id);

                    if ($currentFirstName == "" && $currentLastName == "") {
                        $titleArray = explode(" ", $postTitle);
                        update_field('last_name', end($titleArray), $post_id); // last name
                        if(!$currentBookLastName){
                            update_field('book_last_name', end($titleArray), $post_id); // book last name
                        }

                        if (count($titleArray) > 1) {
                            array_pop($titleArray); //remove last name
                            update_field('first_name', implode(" ", $titleArray), $post_id); // first name
                            if(!$currentBookFirstName){
                                update_field('book_first_name', implode(" ", $titleArray), $post_id); // book first name
                            }

                        }
                    }else{
                        if(!$currentBookLastName){
                            update_field('book_last_name', end($titleArray), $post_id); // book last name
                        }
                        if(!$currentBookFirstName){
                            update_field('book_first_name', implode(" ", $titleArray), $post_id); // book first name
                        }
                    }

                    //$htmlStr = mb_convert_encoding($parseData['parse']['text']['*'], 'UTF-8');

                    $dom = new DOMDocument();
                    $dom->loadHTML(mb_convert_encoding($description, 'HTML-ENTITIES'));
                    $finder = new DomXPath($dom);

                    $infoBoxHtml = "";
                    $sectionHtml = "";
                    $nodes       = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' " . $wikidata['WIKIPEDIA_INFOBOX_CLASS'] . " ')]");
                    if ($nodes->length > 0) {
                        $allTrTag = $nodes->item(0)->getElementsByTagName('tr');
                        foreach ($allTrTag as $trTag) {
                            $allTdTag = $trTag->getElementsByTagName('td');
                            $allthTag = $trTag->getElementsByTagName('th');

                            $th_title = $allthTag->item(0);
                            // Why need to validate == 2 ????
                            if(!$th_title){
                                $title = $allTdTag->item(0)->nodeValue;
                                $value = $allTdTag->item(1)->nodeValue;
                                $title = preg_replace( "/\r|\n/", "", $title );
                                $title = preg_replace( '/\d{4}/','$0 ', $title );

                                $value = preg_replace( "/\r|\n/", "", $value );
                                $value = preg_replace( '/\d{4}/','$0 ', $value );
                                $r = '~(\))(\w)~';
                                $value = preg_replace($r, '$1 $2', $value);
                                if($value){
                                    $infoBoxHtml .= "<p>" . $title . ": " . $value . "</p>";
                                }
                            }else{
                                $title = $allthTag->item(0)->nodeValue;
                                $value = $allTdTag->item(0)->nodeValue;
                                $title = preg_replace( "/\r|\n/", "", $title );
                                $title = preg_replace( '/\d{4}/','$0 ', $title );

                                $value = preg_replace( "/\r|\n/", "", $value );
                                $value = preg_replace( '/\d{4}/','$0 ', $value );
                                $r = '~(\))(\w)~';
                                $value = preg_replace($r, '$1 $2', $value);
                                if($value){
                                    $infoBoxHtml .= "<p>" . $title . ": " . $value . "</p>";
                                }
                            }
                        }
                    }
                    $sections = $parseData['parse']['sections'];
                    foreach ($sections as $section) {
                        if ($section["level"] === "2") {
                            // section["level"] return string
                            $sectionHtml .= '<p><a href="#' . $section["anchor"] . '">' . $section["line"] . '</a></p>';
                        }
                    }

                    update_field('sections', $sectionHtml, $post_id); // sections
                    update_field('infobox', $infoBoxHtml, $post_id); // infobox

                    $tags = array();
                    $municipalityList = get_field('municipality', $post_id);
                    if ($municipalityList == "") {
                        $municipalityList = array();
                    }

                    $categories_data = $parseData['parse']['categories'];
                    $categorieshtml = $parseData['parse']['categorieshtml'];

                    //Make a list of current category
                    $new_arr_title = array();
                    foreach ($categories as $category) {
                        $new_arr_title[] = $category['title'];
                    }


                    //Try to add missing category to current
                    foreach($categories_data as $cat){
                        if(isset($cat['hidden']) ){ continue; };
                        $cate_name = str_replace("_"," ",$cat['*']);
                        $cate_name = 'Kategori:'. $cate_name;

                        if(!in_array($cate_name, $new_arr_title)){
                            $categories[] = array('ns' => 14, 'title' =>$cate_name);
                        }
                    }
                    //Process category update
                    foreach ($categories as $category) {
                        $subject = mb_convert_encoding($category["title"], 'UTF-8');
                        $subject = str_replace($wikidata['WIKIPEDIA_TERM_CATEGORY'] . ":", "", $subject);
                        if ($subject == "Norske lyrikere") {
                            // var_dump(array_key_exists($subject, $wikiCategoryGroup));
                            continue;
                        }
                        if (array_key_exists($subject, $wikiCategoryGroup)) {
                            $subject = $wikiCategoryGroup[$subject];
                        }
                        $tags []= $subject;
                        //Update other field
                        if (($subject == $wikidata['WIKIPEDIA_TERM_MALE']) || ($subject == $wikidata['WIKIPEDIA_TERM_FEMALE'])) {
                            update_field('gender', $subject, $post_id);
                            continue;
                        }
                        preg_match('/' . $wikidata['WIKIPEDIA_TERM_MUNICIPALITY'] . '/', $subject, $municipalityMatches);
                        if (count($municipalityMatches) > 1) {
                            array_push($municipalityList, $municipalityMatches[1]);
                            continue;
                        }
                        preg_match('/' . $wikidata['WIKIPEDIA_TERM_BIRTHDAY'] . ' (.*)/', $subject, $birthYearMatches);
                        if (count($birthYearMatches) > 1) {
                            update_field('birthyear', $birthYearMatches[1], $post_id);
                            continue;
                        }
                        preg_match('/' . $wikidata['WIKIPEDIA_TERM_DEADDAY'] . ' (.*)/', $subject, $deathYearMatches);
                        if (count($deathYearMatches) > 1) {
                            update_field('deathyear', $deathYearMatches[1], $post_id);
                            continue;
                        }
                    }

                    if (count($tags) > 0) {
                        $new_tags = implode(",", $tags);
                        echo 'before tag';
                        wp_set_post_tags($post_id, implode(",", $tags), true);
                    }

                    if (count($municipalityList) > 0) {
                        update_field('municipality', serialize($municipalityList), $post_id);
                    }


                    //||  $thumbnail['source'] &&
                    if ( (isset($thumbnail) && $post_id && $thumbnail['source'] !== get_field('thumbnail', $post_id) ) || ( $thumbnail['source'] && !has_post_thumbnail( $post_id ))  ) {

                        echo 'Add thumbnail post -'. $post_id . '<br>';
                        $filename_notconverthtf8 = $pageimage;
                        $filename = $this->wiki_api_parse_utf8($pageimage);
                        $filename = str_replace($utf8char, $non_utf8char, $filename);

                        $attach_id = $this->wiki_api_save_file($pageimage, $filename, $thumbnail['source']);
                        if ($attach_id) {
                            set_post_thumbnail($post_id, $attach_id);
                        }
                    }else{ //If wiki return no image, delete post image if exist 
                        if($post_id && !isset($thumbnail)){
                            echo 'Del thumbnail post -'. $post_id . '<br>';
                            delete_post_thumbnail( $post_id );
                        }						
                    }

                    if(isset($thumbnail) && !has_post_thumbnail( $post_id )){
                        echo 'Add thumbnail post -'. $post_id . '<br>';
                        $filename_notconverthtf8 = $pageimage;
                        $filename = $this->wiki_api_parse_utf8($pageimage);
                        $filename = str_replace($utf8char, $non_utf8char, $filename);

                        $attach_id = $this->wiki_api_save_file($pageimage, $filename, $thumbnail['source']);
                        if ($attach_id) {
                            set_post_thumbnail($post_id, $attach_id);
                        }
                    }

                    if($isNewImport){ //Set auto update field to true as default so that the post is updated by daily cron
                        update_field('auto_update', true, $post_id); 
                    }
                    $crr_date = date("Y.m.d");
                    $last_date = get_field("author_update_log", $post_id); 
                    $crr_date = $last_date. '<br>' . $crr_date;
                    $up = update_field("author_update_log", $crr_date, $post_id);

                    $crr_date_time = date("Y.m.d");
                    update_field("author_last_updated", $crr_date_time, $post_id);
                    $result['success'][] = "$pageId . [$postTitle] is inserted!";

                    /*Update list id of author*/
                    $list_id[] = $post_id. ' - ' . $postTitle;

                    $i++;
                } else {
                    $result['fail'][] = "$pageId not changed!";
                }

                if(!$force_update){
                    /*update log of list author was updated*/
                    $options = get_option('litteraturnett_options');
                    $last_updated_page = intval($options['last_updated_page']);
                    if (!$last_updated_page) {
                       $this->wiki_log(__("Cannot update the 'last updated' page as it does not exist", 'wl-wiki-import'));
                       return $result;
                    }
                    $list_id_content = implode("<br>", $list_id);
                    $my_post = array();
                    $my_post['ID'] = $last_updated_page;
                    $my_post['post_content'] = $list_id_content;
                    wp_update_post( $my_post );
                }

            }

        }

        return $result;
    }


    function getWikiCategory() {
        $result       = array();
        $parentGroups = get_terms('groups', array('hide_empty' => 0, "parent" => 0));
        foreach ($parentGroups as $group) {
            $groupId     = $group->term_id;
            $childGroups = get_terms('groups', array('hide_empty' => 0, "parent" => $groupId));
            foreach ($childGroups as $childGroup) {
                $result[$childGroup->name] = $group->name;
            }
        }
        return $result;
    }

    function wiki_api_curl($method, $data) {
        $url =  Litteraturnett::instance()->get_wikipedia_api();
        $url .= '?' . http_build_query($data);
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL            => $url,
                    CURLOPT_USERAGENT      => 'Wordpress Wiki cURL Request',
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                    ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        if ($resp === FALSE) {
            $this->wiki_log(print_r(curl_error($curl))); 
            die("Error fetching data from wikipedia");
        }
        // Close request to clear up some resources
        curl_close($curl);

        return $resp;
    }

    public static function wiki_activation() {
        wp_schedule_event(strtotime('23:59:00'), 'daily', 'wiki_cron_daily_event');
    }

    public static function wiki_deactivation() {
        wp_clear_scheduled_hook('wiki_cron_daily_event');
    }

    function wiki_api_parse_utf8($str) {
        $utf8char     = array(" ", "å", "ø", "æ", "Å", "Ø", "Æ");
        $non_utf8char = array(" ", "aa", "oo", "ae", "AA", "OO", "AE");

        return str_replace($utf8char, $non_utf8char, $str);
    }

    function wiki_api_nopriv_import_action() {
        if ($_REQUEST && isset($_REQUEST['data'])) {
            $result = $this->wiki_api_import($_REQUEST['data'], false);
            if (isset($result['success'])) {
                echo implode("\r\n", $result['success']);
            }

            if (isset($result['fail'])) {
                echo implode("\r\n", $result['fail']);
            }

        } else {
            echo 'Request Data is empty';
        }
        die();
    }
    function wiki_api_import_action() {
        if ($_REQUEST && isset($_REQUEST['data'])) {
            $result = $this->wiki_api_import($_REQUEST['data']);
            if (isset($result['success'])) {
                echo implode("\r\n", $result['success']);
            }

            if (isset($result['fail'])) {
                echo implode("\r\n", $result['fail']);
            }

        } else {
            echo 'Request Data is empty';
        }
        die();
    }

    function wiki_cron_daily_action() {
        $this->wiki_log("Start Cron");
        $queryPosts = get_posts(array(
                    'numberposts' => -1,
                    'post_type'   => 'author',
                    'meta_query'  => array(
                        'relation' => 'AND',
                        array(
                            'key'     => 'page_id',
                            'value'   => '',
                            'compare' => '!=',
                            ),
                        ),
                    ));
        if (count($queryPosts) <= 0) {
            $this->wiki_log("queryPosts < = 0");
            return;
        }else{
            $this->wiki_log("queryPosts > 0 - go ahead. Count result" . count($queryPosts));
        }

        $pageids[] = array();
        $i         = 0;
        foreach ($queryPosts as $post) {
            $pageid = get_post_meta($post->ID, 'page_id',true);
            $this->wiki_log("Before break page size - " . $pageid);
            if (!empty($pageid)) {
                $this->wiki_log("Count list $pageid - " . count($pageids ) );
                $pagesize = $this->cron_import_page_size;
                if (!is_array($pageids[round($i / $pagesize)])) {
                    $pageids[round($i / $pagesize)] = array();
                }
                $pageids[round($i / $pagesize)][] = $pageid;
                $i++;
            }else{
                $this->wiki_log("$pageid empty - can not update");
            }
        }

        $this->wiki_log("Before import");

        foreach ($pageids as $data) {
            $this->wiki_log("Import [" . implode(",", $data) . "]");
            $this->import_by_batch($data);
        }

    }

    function import_by_batch($pageids) {
        $data = array(
                'action' => 'wiki_api_import',
                'data'   => $pageids,
                'type'   => 'cron',
                );

        $url = get_site_url() . "/wp-admin/admin-ajax.php";
        $url .= '?' . http_build_query($data);
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 0,
                    CURLOPT_URL            => $url,
                    CURLOPT_USERAGENT      => 'Wordpress Wiki cURL Request',
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                    ));
        // Send the request & save response to $resp
        curl_exec($curl);
        curl_close($curl);
    }

    function wiki_log($data) {
        $data = date("Y-m-d h:i:s") . " : {$data}\r\n";

        $path     = ABSPATH . "wp-content/uploads/log";
        $pathFile = $path . "/" . 'litteraturnett-cron.log';
        if (!file_exists($path)) {
            mkdir($path, 0700);
        }
        file_put_contents($pathFile, $data, FILE_APPEND);
    }
}
/**
 * Add all hooks
 */
global $LitteraturnettWikiImport;
$LitteraturnettWikiImport = LitteraturnettWikiImport::instance();

add_action('admin_menu', array($LitteraturnettWikiImport,'wiki_api_admin_default_setup'));

add_action('wp_ajax_wiki_api_import',array($LitteraturnettWikiImport, 'wiki_api_import_action'));
add_action('wp_ajax_nopriv_wiki_api_import',array($LitteraturnettWikiImport, 'wiki_api_nopriv_import_action'));
add_action('wp_ajax_wiki_api_search',array($LitteraturnettWikiImport, 'wiki_api_search_search'));

register_activation_hook(__FILE__, array('LitteraturnettWikiImport', 'wiki_activation'));
register_deactivation_hook(__FILE__, array('LitteraturnettWikiImport', 'wiki_deactivation'));

add_action('wiki_cron_daily_event',array($LitteraturnettWikiImport, 'wiki_cron_daily_action'));
