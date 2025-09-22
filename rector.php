<?php

declare(strict_types=1);

use Contao\Rector\Set\ContaoLevelSetList;
use Contao\Rector\Set\ContaoSetList;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPhpSets(
        php83: true,
    )
    ->withAttributesSets(
        symfony: true,
        doctrine: true,
    )
    ->withSets([
        ContaoLevelSetList::UP_TO_CONTAO_53,
        ContaoSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ])
    ->withPaths([
        __DIR__ . '/contao',
        __DIR__ . '/src',
    ])
    ->withImportNames(removeUnusedImports: true)
    ->withParallel()
;
