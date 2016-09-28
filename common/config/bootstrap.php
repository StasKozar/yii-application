<?php
define('DS', DIRECTORY_SEPARATOR);
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . DS .'frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . DS .'backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . DS .'console');
Yii::setAlias('@uploads', dirname(dirname(__DIR__)) . DS . 'uploads' );

