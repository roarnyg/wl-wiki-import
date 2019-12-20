<?php
// IOK 2019-11-20 This encapsulates the ACF fields used to manage author fields

class LitteraturnettAuthorFields {
    protected static $instance = null;
    // Make field definitions accessible by name instead of field ID
    protected static $indexedfields = array();
    protected static $authorfields = null;

    // This class should only get instantiated with this method. IOK 2019-10-14 
    public static function instance()  {
        if (!static::$instance) static::$instance = new LitteraturnettAuthorFields();
        return static::$instance;
    }

    public function __construct() {
        $authorfields = static::get_authorfields();
        foreach($authorfields as $af) {
           static::$indexedfields[$af['name']] = $af;
        }
    } 

    // Supported genres for this  site
    public static function genres () {
         $genres = array(__('Romaner, noveller, fortellinger','wl-wiki-import'), __('Dikt og sanger','wl-wiki-import'), __('Skuespill','wl-wiki-import'),__('Krim','wl-wiki-import'),__('Sakprosa','wl-wiki-import'),__('Barne- og ungdomslitteratur','wl-wiki-import'), __('Sakprosa','wl_wiki_import'), __('Humor','wl-wiki-import'), __('Serieforfattere','wl-wiki-import'), __('Tegneserieskapere','wl-wiki-import')); 
         $genres = apply_filters('litteraturnett_filters',$genres);
         $choices = array();
         foreach($genres as $g) $choices[$g] = $g;
         return $choices;
    }

    // Return a properly formatted list of municipalities based on the users settings
    public static function municipalities () {
        $mun = get_option('litteraturnett_regions');
        if (empty($mun)) {
          $regiondata = LitteraturnettRegions::allRegions();
          $mun = $regiondata['Norge'];
          update_option('litteraturnett_regions', $mun, true);
        }
        // This is a list of either kommuner or fylker, if fylker, the value will be a list of kommuner.
        // Flatten this so that a 'fylke' is a choice with null value
        $choices = array();
        foreach ($mun as $key=>$value) {
          if (is_array($value)) { 
              $choices[$key]='';
              foreach($value as $m) $choices[$m]=$m;
          } else {
              $choices[$value]=$value;
          }
        }

        return apply_filters('litteraturnett_regions', $choices); 
    }

    // Retrieve field object by name instead of field id (as in ACF get_field_object)
    public function get_field_object($name) {
        if (isset(static::$indexedfields[$name])) return static::$indexedfields[$name];
        return null;
    }

    public function plugins_loaded() {
        $this->register_acf_fields();
    }

    protected function register_acf_fields() { 
        if( function_exists('acf_add_local_field_group') ):
            acf_add_local_field_group(array(
                        'key' => 'group_5d4d0f0eb4563',
                        'title' => __('Author Fields','wl-wiki-import'),
                        'fields' => static::get_authorfields(),
                        'location' => array(
                            array(
                                array(
                                    'param' => 'post_type',
                                    'operator' => '==',
                                    'value' => 'post',
                                    ),
                                ),
                            array(
                                array(
                                    'param' => 'post_type',
                                    'operator' => '==',
                                    'value' => 'author',
                                    ),
                                ),
                            ),


                        'menu_order' => 0,
                        'position' => 'normal',
                        'style' => 'default',
                        'label_placement' => 'top',
                        'instruction_placement' => 'label',
                        'hide_on_screen' => array(
                                ),
                        'active' => true,
                        'description' => __('Fields used for the authors. Do not edit, these are managed by the Litteraturnett plugin','wl-wiki-import'),
                                ));

        endif;
    }

    public static function get_authorfields() {
       if (static::$authorfields) return static::$authorfields;
       static::$authorfields = array(
                array(
                    'key' => 'field_569cc8be15cce',
                    'label' => __('Local content','wl-wiki-import'),
                    'name' => 'localcontent',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        ),
                    'default_value' => '',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'tabs' => 'all',
                    'delay' => 0,
                    ),
                array(
                    'key' => 'field_568a29a383565',
                    'label' => 'Page Id',
                    'name' => 'page_id',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                        ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'formatting' => 'html',
                    'maxlength' => '',
                    ),
                array(
                        'key' => 'field_56ce9a41d6344',
                        'label' => __('Gender','wl-wiki-import'),
                        'name' => 'gender',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                            ),
                        'choices' => array(
                            '' => '',
                            __('Men','wl-wiki-import') => 'Menn',
                            __('Women','wl-wiki-import') => 'Kvinner',
                            ),
                        'default_value' => array(
                            ),
                        'allow_null' => 1,
                        'multiple' => 0,
                        'ui' => 0,
                        'ajax' => 0,
                        'placeholder' => '',
                        'return_format' => 'value',
                        ),
                        array(
                                'key' => 'field_56dfc65f8c8ea',
                                'label' => __('First name','wl-wiki-import'),
                                'name' => 'first_name',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                    ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'formatting' => 'html',
                                'maxlength' => '',
                             ),
                        array(
                                'key' => 'field_56dfc66f8c8eb',
                                'label' => __('Last name','wl-wiki-import'),
                                'name' => 'last_name',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                    ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'formatting' => 'html',
                                'maxlength' => '',
                             ),
                        array(
                                'key' => 'field_568a29ce83566',
                                'label' => __('Birth year','wl-wiki-import'),
                                'name' => 'birthyear',
                                'type' => 'number',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                    ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'min' => '',
                                'max' => '',
                                'step' => '',
                             ),
                             array(
                                     'key' => 'field_5733f9e2a053a',
                                     'label' => __('Book first name','wl-wiki-import'),
                                     'name' => 'book_first_name',
                                     'type' => 'text',
                                     'instructions' => '',
                                     'required' => 0,
                                     'conditional_logic' => 0,
                                     'wrapper' => array(
                                         'width' => '',
                                         'class' => '',
                                         'id' => '',
                                         ),
                                     'default_value' => '',
                                     'placeholder' => '',
                                     'prepend' => '',
                                     'append' => '',
                                     'formatting' => 'html',
                                     'maxlength' => '',
                                  ),
                             array(
                                     'key' => 'field_5733f9fda053b',
                                     'label' => __('Book last name','wl-wiki-import'),
                                     'name' => 'book_last_name',
                                     'type' => 'text',
                                     'instructions' => '',
                                     'required' => 0,
                                     'conditional_logic' => 0,
                                     'wrapper' => array(
                                         'width' => '',
                                         'class' => '',
                                         'id' => '',
                                         ),
                                     'default_value' => '',
                                     'placeholder' => '',
                                     'prepend' => '',
                                     'append' => '',
                                     'formatting' => 'html',
                                     'maxlength' => '',
                                  ),
                             array(
                                     'key' => 'field_5698908f8686d',
                                     'label' => __('Death year','wl-wiki-import'),
                                     'name' => 'deathyear',
                                     'type' => 'number',
                                     'instructions' => '',
                                     'required' => 0,
                                     'conditional_logic' => 0,
                                     'wrapper' => array(
                                         'width' => '',
                                         'class' => '',
                                         'id' => '',
                                         ),
                                     'default_value' => '',
                                     'placeholder' => '',
                                     'prepend' => '',
                                     'append' => '',
                                     'min' => '',
                                     'max' => '',
                                     'step' => '',
                                  ),
                                  array(
                                          'key' => 'field_568b3b5b327a1',
                                          'label' => __('Genre','wl-wiki-import'),
                                          'name' => 'genre',
                                          'type' => 'checkbox',
                                          'instructions' => '',
                                          'required' => 0,
                                          'conditional_logic' => 0,
                                          'wrapper' => array(
                                              'width' => '',
                                              'class' => '',
                                              'id' => '',
                                              ),
                                          'choices' => static::genres(),
                                          'default_value' => array(
                                                  ),
                                          'layout' => 'horizontal',
                                          'allow_custom' => 0,
                                          'save_custom' => 0,
                                          'toggle' => 0,
                                          'return_format' => 'value',
                                          ),
                                          array(
                                                  'key' => 'field_568b3b83327a2',
                                                  'label' => __('Period','wl-wiki-import'),
                                                  'name' => 'period',
                                                  'type' => 'checkbox',
                                                  'instructions' => '',
                                                  'required' => 0,
                                                  'conditional_logic' => 0,
                                                  'wrapper' => array(
                                                      'width' => '',
                                                      'class' => '',
                                                      'id' => '',
                                                      ),
                                                  'choices' => array(
                                                      '- 1800' => '- 1800',
                                                      '1800 - 1899' => '1800 - 1899',
                                                      '1900 - 1949' => '1900 - 1949',
                                                      '1950 - 1999' => '1950 - 1999',
                                                      '2000 -' => '2000 -',
                                                      ),
                                                  'default_value' => array(
                                                      ),
                                                  'layout' => 'horizontal',
                                                  'allow_custom' => 0,
                                                  'save_custom' => 0,
                                                  'toggle' => 0,
                                                  'return_format' => 'value',
                                                  ),
                                                  array(
                                                          'key' => 'field_568b3b88327a3',
                                                          'label' => __('Municipality','wl-wiki-import'),
                                                          'name' => 'municipality',
                                                          'type' => 'checkbox',
                                                          'instructions' => 'Ikke hak av fylker, de er work in progress.',
                                                          'required' => 0,
                                                          'conditional_logic' => 0,
                                                          'wrapper' => array(
                                                              'width' => '',
                                                              'class' => '',
                                                              'id' => '',
                                                              ),
                                                          'choices' => static::municipalities(),
                                                              'default_value' => array(
                                                                      ),
                                                              'layout' => 'horizontal',
                                                              'allow_custom' => 0,
                                                              'save_custom' => 0,
                                                              'toggle' => 0,
                                                              'return_format' => 'value',
                                                              ),
                                                              array(
                                                                      'key' => 'field_58b01b229c99f',
                                                                      'label' => __('Fylke','wl-wiki-import'),
                                                                      'name' => 'fylke',
                                                                      'type' => 'checkbox',
                                                                      'instructions' => 'Work in progress, ingen funksjon',
                                                                      'required' => 0,
                                                                      'conditional_logic' => 0,
                                                                      'wrapper' => array(
                                                                          'width' => '',
                                                                          'class' => '',
                                                                          'id' => '',
                                                                          ),
                                                                      'choices' => array(
                                                                          'Nordland' => 'Nordland',
                                                                          'Troms' => 'Troms',
                                                                          'Finnmark' => 'Finnmark',
                                                                          ),
                                                                      'default_value' => array(
                                                                          ),
                                                                      'layout' => 'horizontal',
                                                                      'allow_custom' => 0,
                                                                      'save_custom' => 0,
                                                                      'toggle' => 0,
                                                                      'return_format' => 'value',
                                                                      ),
                                                                      array(
                                                                              'key' => 'field_568b3bcf0d81a',
                                                                              'label' => __('Sections','wl-wiki-import'),
                                                                              'name' => 'sections',
                                                                              'type' => 'wysiwyg',
                                                                              'instructions' => '',
                                                                              'required' => 0,
                                                                              'conditional_logic' => 0,
                                                                              'wrapper' => array(
                                                                                  'width' => '',
                                                                                  'class' => '',
                                                                                  'id' => '',
                                                                                  ),
                                                                              'default_value' => '',
                                                                              'toolbar' => 'full',
                                                                              'media_upload' => 1,
                                                                              'tabs' => 'all',
                                                                              'delay' => 0,
                                                                           ),
                                                                      array(
                                                                              'key' => 'field_568b6532598ab',
                                                                              'label' => __('Infobox','wl-wiki-import'),
                                                                              'name' => 'infobox',
                                                                              'type' => 'wysiwyg',
                                                                              'instructions' => '',
                                                                              'required' => 0,
                                                                              'conditional_logic' => 0,
                                                                              'wrapper' => array(
                                                                                  'width' => '',
                                                                                  'class' => '',
                                                                                  'id' => '',
                                                                                  ),
                                                                              'default_value' => '',
                                                                              'toolbar' => 'full',
                                                                              'media_upload' => 1,
                                                                              'tabs' => 'all',
                                                                              'delay' => 0,
                                                                           ),
                                                                      array(
                                                                              'key' => 'field_568b6910431b1',
                                                                              'label' => __('Thumbnail','wl-wiki-import'),
                                                                              'name' => 'thumbnail',
                                                                              'type' => 'text',
                                                                              'instructions' => '',
                                                                              'required' => 0,
                                                                              'conditional_logic' => 0,
                                                                              'wrapper' => array(
                                                                                  'width' => '',
                                                                                  'class' => '',
                                                                                  'id' => '',
                                                                                  ),
                                                                              'default_value' => '',
                                                                              'placeholder' => '',
                                                                              'prepend' => '',
                                                                              'append' => '',
                                                                              'formatting' => 'none',
                                                                              'maxlength' => '',
                                                                           ),
                                                                      array(
                                                                              'key' => 'field_569da670cc7a1',
                                                                              'label' => __('Disable book section','wl-wiki-import'),
                                                                              'name' => 'disable_book_section',
                                                                              'type' => 'true_false',
                                                                              'instructions' => '',
                                                                              'required' => 0,
                                                                              'conditional_logic' => 0,
                                                                              'wrapper' => array(
                                                                                  'width' => '',
                                                                                  'class' => '',
                                                                                  'id' => '',
                                                                                  ),
                                                                              'message' => '',
                                                                              'default_value' => 0,
                                                                              'ui' => 0,
                                                                              'ui_on_text' => '',
                                                                              'ui_off_text' => '',
                                                                           ),
                                                                      array(
                                                                              'key' => 'field_569f06a4d47b3',
                                                                              'label' => __('Revid','wl-wiki-import'),
                                                                              'name' => 'revid',
                                                                              'type' => 'text',
                                                                              'instructions' => '',
                                                                              'required' => 0,
                                                                              'conditional_logic' => 0,
                                                                              'wrapper' => array(
                                                                                  'width' => '',
                                                                                  'class' => '',
                                                                                  'id' => '',
                                                                                  ),
                                                                              'default_value' => '',
                                                                              'placeholder' => '',
                                                                              'prepend' => '',
                                                                              'append' => '',
                                                                              'formatting' => 'none',
                                                                              'maxlength' => '',
                                                                           ),
                                                                      array(
                                                                              'key' => 'field_56c29feedd231',
                                                                              'label' => __('Auto Update','wl-wiki-import'),
                                                                              'name' => 'auto_update',
                                                                              'type' => 'true_false',
                                                                              'instructions' => '',
                                                                              'required' => 0,
                                                                              'conditional_logic' => 0,
                                                                              'wrapper' => array(
                                                                                  'width' => '',
                                                                                  'class' => '',
                                                                                  'id' => '',
                                                                                  ),
                                                                              'message' => '',
                                                                              'default_value' => 1,
                                                                              'ui' => 0,
                                                                              'ui_on_text' => '',
                                                                              'ui_off_text' => '',
                                                                           ),
                                                                      array(
                                                                              'key' => 'field_58dcba3de38da',
                                                                              'label' => __('Last updated','wl-wiki-import'),
                                                                              'name' => 'author_last_updated',
                                                                              'type' => 'text',
                                                                              'instructions' => '',
                                                                              'required' => 0,
                                                                              'conditional_logic' => 0,
                                                                              'wrapper' => array(
                                                                                  'width' => '',
                                                                                  'class' => '',
                                                                                  'id' => '',
                                                                                  ),
                                                                              'default_value' => '',
                                                                              'placeholder' => '',
                                                                              'prepend' => '',
                                                                              'append' => '',
                                                                              'formatting' => 'html',
                                                                              'maxlength' => '',
                                                                           ),
                                                                      );
           	return static::$authorfields;
           }
}
