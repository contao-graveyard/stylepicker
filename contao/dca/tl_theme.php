<?php

declare(strict_types=1);

/**
 * Contao Extension to pick predefined CSS-Classes in the backend.
 *
 * @copyright  4ward.media 2013
 * @see        http://www.4wardmedia.de
 * @licence    LGPL
 * @filesource
 */

// Add the theme-operation icon
array_insert(
    $GLOBALS['TL_DCA']['tl_theme']['list']['operations'],
    6,
        [
            'stylepicker4ward' => [
                'label' => &$GLOBALS['TL_LANG']['tl_theme']['stylepicker4ward'],
                'href' => 'table=tl_stylepicker4ward',
                'icon' => 'system/modules/_stylepicker4ward/assets/icon.png',
            ],
    ],
);
