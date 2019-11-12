<?php
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
    'key' => 'group_5d4d0f0eb4563',
    'title' => 'Wiki Author',
    'fields' => array(
        array(
            'key' => 'field_569cc8be15cce',
            'label' => 'Lokalt innhold',
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
            'label' => 'Kjønn',
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
                'Menn' => 'Menn',
                'Kvinner' => 'Kvinner',
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
            'label' => 'Fornavn',
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
            'label' => 'Etternavn',
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
            'label' => 'Birth year',
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
            'label' => 'Book first name',
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
            'label' => 'Book last name',
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
            'label' => 'Death year',
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
            'label' => 'Genre',
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
            'choices' => array(
                'Romaner, noveller, fortellinger' => 'Romaner, noveller, fortellinger',
                'Dikt og sanger' => 'Dikt og sanger',
                'Skuespill' => 'Skuespill',
                'Krim' => 'Krim',
                'Barne- og ungdomslitteratur' => 'Barne- og ungdomslitteratur',
                'Sakprosa' => 'Sakprosa',
                'Humor' => 'Humor',
                'Serieforfattere' => 'Serieforfattere',
                'Tegneserieskapere' => 'Tegneserieskapere',
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
            'key' => 'field_568b3b83327a2',
            'label' => 'Period',
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
            'label' => 'Kommune',
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
            'choices' => array(
                'Nordland' => 'Nordland',
                'Alstahaug' => 'Alstahaug',
                'Andøy' => 'Andøy',
                'Ballangen' => 'Ballangen',
                'Beiarn' => 'Beiarn',
                'Bindal' => 'Bindal',
                'Bodø' => 'Bodø',
                'Brønnøy' => 'Brønnøy',
                'Bø' => 'Bø',
                'Dønna' => 'Dønna',
                'Evenes' => 'Evenes',
                'Fauske' => 'Fauske',
                'Flakstad' => 'Flakstad',
                'Gildeskål' => 'Gildeskål',
                'Grane' => 'Grane',
                'Hadsel' => 'Hadsel',
                'Hamarøy' => 'Hamarøy',
                'Hattfjelldal' => 'Hattfjelldal',
                'Hemnes' => 'Hemnes',
                'Herøy' => 'Herøy',
                'Leirfjord' => 'Leirfjord',
                'Lurøy' => 'Lurøy',
                'Lødingen' => 'Lødingen',
                'Meløy' => 'Meløy',
                'Moskenes' => 'Moskenes',
                'Narvik' => 'Narvik',
                'Nesna' => 'Nesna',
                'Rana' => 'Rana',
                'Rødøy' => 'Rødøy',
                'Røst' => 'Røst',
                'Saltdal' => 'Saltdal',
                'Sortland' => 'Sortland',
                'Steigen' => 'Steigen',
                'Sømna' => 'Sømna',
                'Sørfold' => 'Sørfold',
                'Tjeldsund' => 'Tjeldsund',
                'Træna' => 'Træna',
                'Tysfjord' => 'Tysfjord',
                'Vefsn' => 'Vefsn',
                'Vega' => 'Vega',
                'Vestvågøy' => 'Vestvågøy',
                'Vevelstad' => 'Vevelstad',
                'Værøy' => 'Værøy',
                'Vågan' => 'Vågan',
                'Øksnes' => 'Øksnes',
                'Troms' => 'Troms',
                'Balsfjord' => 'Balsfjord',
                'Bardu' => 'Bardu',
                'Berg' => 'Berg',
                'Dyrøy' => 'Dyrøy',
                'Gratangen' => 'Gratangen',
                'Harstad' => 'Harstad',
                'Ibestad' => 'Ibestad',
                'Karlsøy' => 'Karlsøy',
                'Kvæfjord' => 'Kvæfjord',
                'Kvænangen' => 'Kvænangen',
                'Kåfjord' => 'Kåfjord',
                'Lavangen' => 'Lavangen',
                'Lenvik' => 'Lenvik',
                'Lyngen' => 'Lyngen',
                'Målselv' => 'Målselv',
                'Nordreisa' => 'Nordreisa',
                'Salangen' => 'Salangen',
                'Skjervøy' => 'Skjervøy',
                'Skånland' => 'Skånland',
                'Storfjord' => 'Storfjord',
                'Sørreisa' => 'Sørreisa',
                'Torsken' => 'Torsken',
                'Tranøy' => 'Tranøy',
                'Tromsø' => 'Tromsø',
                'Finnmark' => 'Finnmark',
                'Alta' => 'Alta',
                'Berlevåg' => 'Berlevåg',
                'Båtsfjord' => 'Båtsfjord',
                'Gamvik' => 'Gamvik',
                'Hammerfest' => 'Hammerfest',
                'Hasvik' => 'Hasvik',
                'Karasjok' => 'Karasjok',
                'Kautokeino' => 'Kautokeino',
                'Kvalsund' => 'Kvalsund',
                'Lebesby' => 'Lebesby',
                'Loppa' => 'Loppa',
                'Måsøy' => 'Måsøy',
                'Nesseby' => 'Nesseby',
                'Nordkapp' => 'Nordkapp',
                'Porsanger' => 'Porsanger',
                'Sør-Varanger' => 'Sør-Varanger',
                'Tana' => 'Tana',
                'Vadsø' => 'Vadsø',
                'Vardø' => 'Vardø',
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
            'key' => 'field_58b01b229c99f',
            'label' => 'Fylke',
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
            'label' => 'Sections',
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
            'label' => 'Infobox',
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
            'label' => 'Thumbnail',
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
            'label' => 'Disable book section',
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
            'label' => 'Revid',
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
            'label' => 'Auto Update',
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
            'label' => 'Last updated',
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
    ),

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
    'description' => '',
));

endif;
