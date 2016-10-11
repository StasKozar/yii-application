<?php

namespace frontend\controllers;


use frontend\models\Task;
use frontend\models\Validator;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class TasksController extends ActiveController
{
    public $modelClass = 'frontend\models\Task';
    public $serializer = 'components\jsonapi\Serializer';


    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/vnd.api+json' => Response::FORMAT_JSON,
                ],
            ]
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionCreate()
    {
        $request = \Yii::$app->request;
        if (Validator::validateContentType($request)) {
            $model = new Task();
            $data = json_decode(file_get_contents("php://input"));
            $model->begin = isset($data->data->attributes->begin) ? $data->data->attributes->begin : '';
            $model->end = isset($data->data->attributes->end) ? $data->data->attributes->end : '';
            $model->validateDate();

            if ($model->message === false) {
                if ($model->save()) {
                    $response = \Yii::$app->getResponse();
                    $response->setStatusCode(201);
                    $id = implode(',', array_values($model->getPrimaryKey(true)));
                    return Task::findOne($id);
                } elseif (!$model->hasErrors()) {
                    throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
                }
            } else {
                $response = \Yii::$app->getResponse();
                $response->statusCode = 400;
                $error = ([
                    'status' => $response->statusCode,
                    'title' => 'Incorrect period',
                    'Detail' => $model->message,
                ]);
                $response->data = $error;
            }
        }
    }

    public function actionUpdate($id)
    {
        $model = Task::findOne($id);
        $request = \Yii::$app->request;
        $data = json_decode(file_get_contents("php://input"));
        $model->begin = $data->data->attributes->begin;
        $model->end = $data->data->attributes->end;
        $model->validateDate();

        if(Validator::validateContentType($request)){
            if($model->message === false)
            {
                if ($model->save()) {
                    $response = \Yii::$app->getResponse();
                    $response->setStatusCode(201);
                    return $model;
                } elseif (!$model->hasErrors()) {
                    throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
                }
            }else{
                $response = \Yii::$app->getResponse();
                $response->statusCode = 400;
                $error = ([
                    'status' => $response->statusCode,
                    'title' => 'Incorrect period',
                    'Detail' => $model->message,
                ]);
                $response->data = $error;
            }
        }
    }

    public function prepareDataProvider()
    {
        $request = \Yii::$app->request;
        $response = \Yii::$app->response;
        if(Validator::validateContentType($request)){
            $get = $request->get('filter');

            if(!isset($get['begin']) && !isset($get['end'])){
                $response->data = Task::find()->all();
            }elseif (!isset($get['begin']) || !isset($get['end'])) {
                $response = \Yii::$app->getResponse();
                $response->statusCode = 400;
                $error = ([
                    'status' => $response->statusCode,
                    'title' => 'Incorrect period',
                    'Detail' => 'Period must have time of begin and time of end',
                ]);
                $response->data = $error;
            } elseif (\DateTime::createFromFormat('Y-m-d', $get['begin']) === false
                || \DateTime::createFromFormat('Y-m-d', $get['end']) === false
            ) {
                $response = \Yii::$app->getResponse();
                $response->statusCode = 400;
                $error = ([
                    'status' => $response->statusCode,
                    'title' => 'Incorrect period',
                    'Detail' => 'Date do not must be a string and format to Y-m-d',
                ]);
                $response->data = $error;
            } elseif (isset($get['begin']) && isset($get['end'])) {
                $begin = $get['begin'];
                $end = $get['end'];

                if ($begin > $end) {
                    $response = \Yii::$app->getResponse();
                    $response->statusCode = 400;
                    $error = ([
                        'status' => $response->statusCode,
                        'title' => 'Incorrect period',
                        'Detail' => 'Begin time can\'t be bigger than the time of end',
                    ]);
                    $response->data = $error;
                } else {
                    $model = new Task();
                    $model->begin = $begin;
                    $model->end = $end;
                    $response->data = $model->getTime();
                }
            }
        }
    }
}