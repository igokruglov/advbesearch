<?php
$TCA['USERsearch'] = array(
    'ctrl' => array(
        'title' => 'USER search',
        'label' => 'first_name,last_name',
        'hideTable' => 1,
        'EXT' => array(
            'realTable' => 'fe_users'
        )
    ),
    'interface' => array(
        'showRecordFieldList' => 'first_name,last_name',
        'maxDBListItems' => 5
    ),
    'feInterface' => $TCA['USERsearch']['feInterface'],
    'columns' => array(
        'uid' => array(
            'exclude' => 0,
            'label' => 'user id',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            )
        ),
        'pid' => array(
            'exclude' => 0,
            'label' => 'pid',
            'config' => array(
                'type' => 'passthrough',
            )
        ),
        'disabled' => array(
            'exclude' => 1,
            'label' => 'disabled',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            )
        ),
        'deleted' => array(
            'exclude' => 1,
            'label' => 'deleted',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            )
        ),
        'name' => array(
            'exclude' => 0,
            'label' => 'name',
            'config' => array(
                'type' => 'passthrough',
            )
        ),
        'company' => array(
            'exclude' => 0,
            'label' => 'company',
            'config' => array(
                'type' => 'passthrough',
            )
        ),
        'username' => array(
            'exclude' => 0,
            'label' => 'username',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            )
        ),
        'first_name' => array(
            'exclude' => 0,
            'label' => 'first_name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            )
        ),
        'last_name' => array(
            'exclude' => 0,
            'label' => 'last_name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            )
        ),
        'title' => array(
            'exclude' => 0,
            'label' => 'title',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            )
        )
    ),
    'types' => array(
        '0' => array('showitem' => 'first_name,last_name,title')
    ),
    'palettes' => array(
        '1' => array('showitem' => '')
    )
);