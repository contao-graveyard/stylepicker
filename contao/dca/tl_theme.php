<?php

declare(strict_types=1);

use Contao\ArrayUtil;

// Add the theme-operation icon
ArrayUtil::arrayInsert($GLOBALS['TL_DCA']['tl_theme']['list']['operations'], 6, [
    'stylepicker4ward' => [
        'label' => &$GLOBALS['TL_LANG']['tl_theme']['stylepicker4ward'],
        'href' => 'table=tl_stylepicker4ward',
        'icon' => 'system/modules/_stylepicker4ward/assets/icon.png',
    ],
]);
