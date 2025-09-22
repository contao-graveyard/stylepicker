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
if (TL_MODE === 'BE' && Input::get('do') !== 'repository_manager' && Input::get('do') !== 'composer' && Environment::get('script') !== 'contao/install.php') {
    $GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_stylepicker4ward';
    $GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_stylepicker4ward_target';
    $GLOBALS['BE_MOD']['design']['themes']['stylepicker4ward_import'] = ['Psi\Stylepicker4ward\Importer', 'generate'];

    $GLOBALS['TL_HOOKS']['loadDataContainer']['stylepicker4ward'] = ['\Stylepicker4ward\DcaHelper', 'injectStylepicker'];

    $GLOBALS['TL_EASY_THEMES_MODULES']['stylepicker4ward'] =
    [
        'href_fragment' => 'table=tl_stylepicker4ward',
        'icon' => 'system/modules/_stylepicker4ward/assets/icon.png',
    ];
}
