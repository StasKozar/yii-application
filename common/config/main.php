<?php

return [
    'language' => 'ru-RU',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => ['languageSwitcher',],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'languageSwitcher' => [
            'class' => 'common\components\languageSwitcher',
        ],
        'formatter' => [
            'dateFormat' => 'php:d/m/Y H:i:s',
            'defaultTimeZone' => 'Europe/Paris',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
        ],
        'i18n' => [
            'translations' => [
                'frontend*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
                'backend*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
            ],
        ],
        /*'urlManager' => [
            'class' => 'yii\web\UrlManager',
            // Disable upload.php
            'showScriptName' => false,
            // Disable r= routes
            'enablePrettyUrl' => true,
            'rules' => array(
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ],*/
    ],
    'params' => [
        'baseUrl' => 'http://localhost/yii-application'
    ],
];
