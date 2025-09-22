<?php

declare(strict_types=1);

namespace ContaoGraveyard\StylePickerBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use ContaoGraveyard\StylePickerBundle\ContaoGraveyardStylePickerBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            (new BundleConfig(ContaoGraveyardStylePickerBundle::class))
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
