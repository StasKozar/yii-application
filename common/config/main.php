<?php

return [
    'language' => 'en-US',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => ['languageSwitcher',],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'languageSwitcher' => [
            'class' => 'common\components\languageSwitcher',
        ],
        'urlManager' => [
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
        ],
    ],
];
