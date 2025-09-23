<?php

declare(strict_types=1);

use Contao\System;
use ContaoGraveyard\StylePickerBundle\DcaHelper;
use ContaoGraveyard\StylePickerBundle\Importer;
use Symfony\Component\HttpFoundation\Request;

if (System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create(''))) {
    $GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_stylepicker4ward';
    $GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_stylepicker4ward_target';
    $GLOBALS['BE_MOD']['design']['themes']['stylepicker4ward_import'] = [Importer::class, 'generate'];

    $GLOBALS['TL_HOOKS']['loadDataContainer']['stylepicker4ward'] = [DcaHelper::class, 'injectStylepicker'];
}
