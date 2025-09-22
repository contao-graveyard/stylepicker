<?php

declare(strict_types=1);

use Contao\System;
use Symfony\Component\HttpFoundation\Request;
use Contao\Input;
use Contao\Environment;
use Psi\Stylepicker4ward\Importer;

/**
 * Contao Extension to pick predefined CSS-Classes in the backend.
 *
 * @copyright  4ward.media 2013
 * @see        http://www.4wardmedia.de
 * @licence    LGPL
 * @filesource
 */
if (System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create('')) && Input::get('do') !== 'repository_manager' && Input::get('do') !== 'composer' && Environment::get('script') !== 'contao/install.php') {
    $GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_stylepicker4ward';
    $GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_stylepicker4ward_target';
    $GLOBALS['BE_MOD']['design']['themes']['stylepicker4ward_import'] = [Importer::class, 'generate'];

    $GLOBALS['TL_HOOKS']['loadDataContainer']['stylepicker4ward'] = ['\Stylepicker4ward\DcaHelper', 'injectStylepicker'];

    $GLOBALS['TL_EASY_THEMES_MODULES']['stylepicker4ward'] =
    [
        'href_fragment' => 'table=tl_stylepicker4ward',
        'icon' => 'system/modules/_stylepicker4ward/assets/icon.png',
    ];
}
