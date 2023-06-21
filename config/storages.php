<?php

return [
    'remote_disks' => [
        'manufacturer' => 'ftp_manufacturer',
        'restaurant' => 'ftp_restaurant',
    ],

    'remote_paths' => [
        'manufacturer' => [
            'import' => 'import.csv',
        ],
        'restaurant' => [
            'export' => 'truffles/export.csv',
            'export_tmp' => 'truffles/tmp/tmp.csv',
        ]
    ],

    'local_paths' => [
        'import' => 'truffles/import.csv',
    ],
];
