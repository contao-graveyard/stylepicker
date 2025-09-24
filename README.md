> [!CAUTION]
> USE AT YOUR OWN RISK / Do not use in production
>
> This plugin has been revived as an example for a conference talk at the contao conference in 2025.
> Whilst most, if not all the functionality is given, this extension should most likely not be used in production at all.

<h1 align="center">Contao StylePicker (formerly known as `stylepicker4ward`)</h1>
<p align="center">
    <a href="https://github.com/contao-graveyard/stylepicker"><img src="https://img.shields.io/github/v/release/contao-graveyard/stylepicker" alt="github version"/></a>
    <a href="https://packagist.org/packages/contao-graveyard/stylepicker"><img src="https://img.shields.io/packagist/dt/contao-graveyard/stylepicker?color=f47c00" alt="amount of downloads"/></a>
    <a href="https://packagist.org/packages/contao-graveyard/stylepicker"><img src="https://img.shields.io/packagist/dependency-v/contao-graveyard/stylepicker/php?color=474A8A" alt="minimum php version"></a>
</p>

## Description

This bundle adds the possibility to easily select a css-class from a predefined list.

## Installation

### Via composer

```
composer require contao-graveyard/stylepicker
```

## Events

### GetStylePickerEvent

Formerly known as the `stylepicker4ward_getFilter` Hook, the new implementation happens as a Symfony event that you can
listen to. Please mind that the examples below need either autowiring to be on or need your own service tagging.

**Old implementation**
```php
/*
 * HOOK to get table,PID(s),section and condition
 * in-parameter: str $table, int $id
 * out-parameter as array or FALSE if the callback does not match:
 * 		array($tbl,$pids,$sec,$cond)
 * 		str $tbl: table name, mostly the same as from the in-parameter
 * 		array $layout: ID of Pagelayout
 * 		str $sec: a section (column) identifier
 * 		str $cond: some addition condition
 */
if (isset($GLOBALS['TL_HOOKS']['stylepicker4ward_getFilter']) && is_array($GLOBALS['TL_HOOKS']['stylepicker4ward_getFilter'])) {
    foreach ($GLOBALS['TL_HOOKS']['stylepicker4ward_getFilter'] as $callback) {
        System::importStatic($callback[0]);
        $result = $this->{$callback[0]}->{$callback[1]}($table, $id);
        if (is_array($result)) {
            [$table, $layout, $section, $condition] = $result;
            break;
        }
    }
}
```

#### Defining in a class

```php
<?php

namespace App\EventListener

use ContaoGraveyard\StylePickerBundle\Event\GetStylePickerFilterEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class StylePickerEventListener
{
    public function __invoke(GetStylePickerFilterEvent $event): void
    {
        if ($event->getTable() !== 'my_table') {
            return;
        }

        $event->setLayout(213);
        $event->setCondition('foobar');
        $event->setSection('your_section');
    }
}
```

#### Directly on the method

```php
<?php

namespace App\EventListener

use ContaoGraveyard\StylePickerBundle\Event\GetStylePickerFilterEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class MyMultiListener
{
    #[AsEventListener]
    public function onGetStylePickerFilterEvent(GetStylePickerFilterEvent $event): void
    {
        // ...
    }
}
```

