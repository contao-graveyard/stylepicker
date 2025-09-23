<?php

declare(strict_types=1);

use Contao\DataContainer;
use Contao\DC_Table;
use ContaoGraveyard\StylePickerBundle\DcaHelper;

$GLOBALS['TL_DCA']['tl_stylepicker4ward'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_theme',
        'enableVersioning' => true,
        'oncopy_callback' => [[DcaHelper::class, 'copy']],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
            'fields' => ['title'],
            'headerFields' => ['name', 'author', 'tstamp'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'child_record_callback' => [DcaHelper::class, 'generateItem'],
            'panelLayout' => 'sort,search,limit',
        ],
        'label' => [
            'fields' => ['title', 'cssclass'],
            'format' => '%s <span style="color:#999">[%s]</span>',
        ],
        'global_operations' => [
            'import' => [
                'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['stylepicker4ward_import'],
                'href' => 'key=stylepicker4ward_import',
                'class' => 'header_new',
            ],
        ],
        'operations' => [
            'edit' => [
                'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ],
            'copy' => [
                'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['copy'],
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.gif',
            ],
            'cut' => [
                'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['cut'],
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.gif',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
            ],
            'show' => [
                'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif',
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{info_legend},title,cssclass,description,image;{layouts_legend},layouts;{CEs_legend},_CEs,_CE_Row;{Article_legend},_Article,_Article_Row,_ArticleTeaser;{Pages_legend},_Pages',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
            'foreignKey' => 'tl_layout.name',
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => [
                'type' => 'belongsTo',
                'load' => 'eager',
            ],
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['title'],
            'inputType' => 'text',
            'sorting' => true,
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'search' => true,
            'eval' => [
                'mandatory' => true,
                'maxlength' => 128,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'description' => [
            'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['description'],
            'inputType' => 'textarea',
            'search' => true,
            'eval' => [
                'style' => 'height:50px;',
                'tl_class' => 'clr',
            ],
            'sql' => 'text NULL',
        ],
        'image' => [
            'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['image'],
            'inputType' => 'fileTree',
            'eval' => [
                'fieldType' => 'radio',
                'files' => true,
                'extensions' => 'gif,png,jpg',
            ],
            'sql' => 'blob NULL',
        ],
        'cssclass' => [
            'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['cssclass'],
            'inputType' => 'text',
            'sorting' => true,
            'search' => true,
            'eval' => [
                'mandatory' => true,
                'maxlength' => 128,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],

        'layouts' => [
            'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['layouts'],
            'inputType' => 'checkbox',
            'options_callback' => [DcaHelper::class, 'getPagelayouts'],
            'load_callback' => [[DcaHelper::class, 'loadPagelayouts']],
            'save_callback' => [[DcaHelper::class, 'savePagelayouts']],
            'eval' => [
                'mandatory' => true,
                'multiple' => true,
                'doNotSaveEmpty' => false,
                'doNotCopy' => true,
                'tl_class' => 'w50" style="height:auto;',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],

        // Content Elements
        '_CEs' => [
            'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['_CEs'],
            'inputType' => 'checkbox',
            'options_callback' => [DcaHelper::class, 'getContentElements'],
            'load_callback' => [[DcaHelper::class, 'loadCEs']],
            'save_callback' => [[DcaHelper::class, 'saveCEs']],
            'reference' => &$GLOBALS['TL_LANG']['CTE'],
            'eval' => [
                'multiple' => true,
                'doNotSaveEmpty' => true,
                'tl_class' => 'w50" style="height:auto;',
            ],
        ],
        '_CE_Row' => [
            'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['_CE_Row'],
            'inputType' => 'checkbox',
            'options_callback' => [DcaHelper::class, 'getSections'],
            'load_callback' => [[DcaHelper::class, 'loadCE_Rows']],
            'save_callback' => [[DcaHelper::class, 'doNothing']],
            'reference' => &$GLOBALS['TL_LANG']['tl_article'],
            'eval' => [
                'multiple' => true,
                'doNotSaveEmpty' => true,
                'tl_class' => 'w50" style="height:auto;',
            ],
        ],

        // Articles
        '_Article' => [
            'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['_Article'],
            'inputType' => 'checkbox',
            'load_callback' => [[DcaHelper::class, 'loadArticles']],
            'save_callback' => [[DcaHelper::class, 'saveArticles']],
            'eval' => [
                'doNotSaveEmpty' => true,
                'tl_class' => 'w50',
            ],
        ],
        '_ArticleTeaser' => [
            'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['_ArticleTeaser'],
            'inputType' => 'checkbox',
            'load_callback' => [[DcaHelper::class, 'loadArticleTeasers']],
            'save_callback' => [[DcaHelper::class, 'saveArticleTeasers']],
            'eval' => [
                'doNotSaveEmpty' => true,
                'tl_class' => 'w50',
            ],
        ],
        '_Article_Row' => [
            'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['_CE_Row'],
            'inputType' => 'checkbox',
            'options_callback' => [DcaHelper::class, 'getSections'],
            'load_callback' => [[DcaHelper::class, 'loadArticle_Rows']],
            'save_callback' => [[DcaHelper::class, 'doNothing']],
            'reference' => &$GLOBALS['TL_LANG']['tl_article'],
            'eval' => [
                'multiple' => true,
                'doNotSaveEmpty' => true,
                'tl_class' => 'w50" style="height:auto;',
            ],
        ],

        // Pages
        '_Pages' => [
            'label' => &$GLOBALS['TL_LANG']['tl_stylepicker4ward']['_Pages'],
            'inputType' => 'checkbox',
            'load_callback' => [[DcaHelper::class, 'loadPages']],
            'save_callback' => [[DcaHelper::class, 'savePages']],
            'eval' => [
                'doNotSaveEmpty' => true,
                'tl_class' => 'w50',
            ],
        ],
    ],
];
