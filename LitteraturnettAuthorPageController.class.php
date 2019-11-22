<?php
/*
  This class contains methods for displaying the content of the Author pages. It can be subclassed, if you do,
  override the filter 'litteraturnett_author_page_controller_class' to return your class name.
*/

class LitteraturnettAuthorPageController {

    // Shared instance so removing and modifying actions will be simpler. IOK 2019-11-21
    protected static $instance = null;

    // This class should only get instantiated with this method. IOK 2019-10-14
    public static function instance()  {
        if (!static::$instance) static::$instance = new static();
        return static::$instance;
    }

    public function single_author_content() {
        global $post;
        $wiki = Litteraturnett::get_wikipedia();
        $wikiUrl = $wiki."?curid=".get_post_meta($post->ID, "page_id",true);
        $localContent =  get_field('localcontent');
        if(!empty($post->post_content)):
            ?>
                <div class="author-page-content">
                <?php the_content(); ?>
                </div>
                <div class="post-detail-author">
                <?php echo __("This page uses material from", 'litteraturnett').' <a href="'.$wikiUrl.'" target="_blank">'.__("Wikipedia").'</a>, ' . __("licensed under", 'litteraturnett') . '  <a href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank"> CC-BY-SA </a>'  ; ?>
                </div>
                <?php
                endif;
        if (!empty($localContent)):
            ?>
                <div class="author-page-content local-content"><?php echo $localContent; ?></div><br>
                <?php
                endif;

    }

    // Wrapper  for the main content element. 
    public function before_main_content () {
        ?>
            <main class="content author-content <?php do_action('litteraturnett_author_main_classes'); ?>" 
            <?php  do_action('litteraturnett_author_main_attributes'); ?>
            >
            <?php

    }
    public function after_main_content() {
        ?>
            </main>
            <?php
    }

    public function before_single_author_summary() {
        ?>
            <div class="author-page-top <?php echo get_the_post_thumbnail() ? "has-image" : "" ?>">
            <?php 
    }
    public function after_single_author_summary() {
        ?>
            </div>
            <?php
    }


    // This encapsulates the main part of the top bit, the author summary
    public function before_author_page_info() {
        ?>
            <div class="author-page-info">
            <?php
    }
    public function after_author_page_info() {
        ?>
            </div>
            <?php
    }


    public function author_page_image () {
        $wikiapi = Litteraturnett::get_wikipedia();
        if(get_the_post_thumbnail()):?>
            <div class="author-page-image">
                <?php if ( has_post_thumbnail() ) {
                    $thumbz = get_the_post_thumbnail();
                    echo $thumbz;
                    echo '<span class="img-creator"></span><span class="ic-expand"></span>';                                                                     
                    $image_name = "";
                    $file_title = get_post(( get_post_thumbnail_id()) )->post_title;
                    $file_name = basename ( get_attached_file( get_post_thumbnail_id()) );
                    $image_name = $file_title.".".end(explode('.', $file_name)); //This is for get the image file name + extension with special character
// This depends on local popup code entirely IOK FIXME
                    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
                    if ( ! empty( $large_image_url[0] ) ) {
                        printf( '<div id="authorImageInfo" class="image-info-popup white-popup mfp-with-anim mfp-hide" data-image-name="%1$s" data-wiki-url="%2$s">
                                <img src="%3$s" alt="%4$s"/>
                                <div class="image-info-content" id="imageInfoContent"></div>
                                </div>',
                                $image_name,
                                $wikiapi,
                                esc_url( $large_image_url[0] ),
                                get_the_title()
                              );
                    }
                }
        ?>
            </div>
            <?php endif;


    }
    public function author_page_detail () {
        $infoBox = html_entity_decode(get_field('infobox'));
        $infoBox = preg_replace_callback('/u([0-9a-fA-F]{4})/', function ($match) {
                return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
                }, $infoBox);

        ?>
            <div class="author-page-detail">
            <h1><?php echo get_the_title(); ?></h1>
            <?php echo $infoBox; ?>
            </div>
            <?php
    }

    public function author_page_navigation () {
        $sections = html_entity_decode(get_field('sections'));
        $sections = preg_replace_callback('/u([0-9a-fA-F]{4})/', function ($match) {
                return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
                }, $sections);
        ?>
            <div class="author-page-navigation">
            <?php echo $sections; ?>
            <a href="#authorCommentForm"><?php echo __("Do you have information to add?", 'litteraturnett') ?></a>
            </div>
            <?php
    }

    public function author_sidebar() {
        get_sidebar();
    }

    public function author_taglist() {
        global $post;
        $tagList = wp_get_post_tags($post->ID);
        $tagListHtml = "";
        $containerclass = esc_attr(apply_filters('litteraturnett_author_container_class','container'));
        foreach ($tagList as $tag) {
            if($tag->description!=""){
                $tagListHtml .= '<a href="/tag/'.$tag->slug.'/" rel="tag" data-tag="'.$tag->name.'">'.$tag->description.'</a> | ';
            }
        }
        if($tagListHtml!=""){
            $tagListHtml = substr($tagListHtml, 0, -3); // remove the last seperator
            echo "<div class='$containerclass author-page-tag'><h2>".__('Tags','litteraturnett')."</h2><div class='author-page-tag-content'>$tagListHtml</div></div>";
        }

    }
    public function author_related_book () {
        $containerclass = esc_attr(apply_filters('litteraturnett_author_container_class','container'));
        ?>
            <div class="<?php echo $containerclass;?>">
            <div id="relatedBook" class='author-page-related-book'></div>
            </div>
            <?php
    }
    public function author_comments() {
        $containerclass = esc_attr(apply_filters('litteraturnett_author_container_class','container'));
        ?>
            <div class="<?php echo $containerclass;?>">
            <div id="authorCommentForm" class="author-conmment-form">
            <?php comments_template(); ?>
            </div>
            </div>
            <?php
    }


}
