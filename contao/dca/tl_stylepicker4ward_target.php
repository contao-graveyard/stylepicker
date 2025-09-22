<?php

$GLOBALS['TL_DCA']['tl_stylepicker4ward_target'] = array
(
    'config' => array
    (
        'dataContainer' => 'Table',
        'sql' => array
        (
            'keys' => array
            (
                'id'  => 'primary',
                'pid' => 'index',
            )
        ),
    ),

    'fields' => array
    (
        'id' => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'tstamp' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'tbl' => array
        (
            'sql' => "varchar(128) NOT NULL default ''"
        ),
        'fld' => array
        (
            'sql' => "varchar(128) NOT NULL default ''"
        ),
        'cond' => array
        (
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'sec' => array
        (
            'sql' => "varchar(128) NOT NULL default ''"
        ),
    ),
);
