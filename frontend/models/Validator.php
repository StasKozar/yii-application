<?php
/**
 * Created by PhpStorm.
 * User: StasKozar
 * Date: 11/10/2016
 * Time: 9:01
 */

namespace frontend\models;

use frontend\models\Task;

class Validator
{
    public static function validateContentType($request)
    {

        if(strpos($request->contentType, 'application/vnd.api+json') === 0){
            if(strlen(strcmp($request->contentType, 'application/vnd.api+json') === 0)){
                return true;
            }
            $response = \Yii::$app->getResponse();
            $response->statusCode = 406;
            $error = ([
                'status' => $response->statusCode,
                'title' => 'Not Acceptable',
                'Detail' => 'Not acceptable content-type of request',
            ]);
            $response->data = $error;
        }else{
            $response = \Yii::$app->getResponse();
            $response->statusCode = 415;
            $error = ([
                'status' => $response->statusCode,
                'title' => 'Unsupported Media Type',
                'Detail' => 'Unsupported content-type of request',
            ]);
            $response->data = $error;
        }
    }
}