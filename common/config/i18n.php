<?php
return [
    'sourcePath' => __DIR__. '..' . DS . '..' . DS . '..' . DS,
    'languages' => ['ru-RU', 'en-US'],
    'translator' => 'Yii::t',
    'sort' => false,
    'removeUnused' => false,
    'only' => ['*.php'],
    'except' => [
        '.svn',
        '.git',
        '.gitignore',
        '.gitkeep',
        '.hgignore',
        '.hgkeep',
        '/messages',
        '/vendor',
    ],
    'format' => 'php',
    'messagePath' => __DIR__ . DS . '..' . DS . 'messages',
    'overwrite' => true,
];