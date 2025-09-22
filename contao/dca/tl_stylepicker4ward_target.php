<?php

declare(strict_types=1);

$GLOBALS['TL_DCA']['tl_stylepicker4ward_target'] =
[
    'config' => [
        'dataContainer' => 'Table',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
            ],
        ],
    ],

    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'tbl' => [
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'fld' => [
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'cond' => [
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'sec' => [
            'sql' => "varchar(128) NOT NULL default ''",
        ],
    ],
];
