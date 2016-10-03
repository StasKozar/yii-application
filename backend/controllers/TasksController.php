<?php
/**
 * Created by PhpStorm.
 * User: StasKozar
 * Date: 29/09/2016
 * Time: 15:33
 */

namespace backend\controllers;

use backend\models\ApiTask;
use Yii;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class TasksController extends ActiveController
{

    public $modelClass = 'backend\models\ApiTask';
    public $serializer = 'tuyakhov\jsonapi\Serializer';


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
        $model = new ApiTask();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->validateDate();

        if($model->message === false)
        {
            if ($model->save()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
                $id = implode(',', array_values($model->getPrimaryKey(true)));
                $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
            } elseif (!$model->hasErrors()) {
                throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
            }
        }else{
            $response = Yii::$app->getResponse();
            $response->statusCode = 400;
            $error = ([
                'status' => $response->statusCode,
                'title' => 'Incorrect period',
                'Detail' => $model->message,
            ]);
            $response->data = $error;
        }

    }

    public function actionUpdate($id)
    {
        $model = ApiTask::findOne($id);
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->validateDate();

        if($model->message === false)
        {
            if ($model->save()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
                $id = implode(',', array_values($model->getPrimaryKey(true)));
                $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
            } elseif (!$model->hasErrors()) {
                throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
            }
        }else{
            $response = Yii::$app->getResponse();
            $response->statusCode = 400;
            $error = ([
                'status' => $response->statusCode,
                'title' => 'Incorrect period',
                'Detail' => $model->message,
            ]);
            $response->data = $error;
        }


    }

    public function prepareDataProvider()
    {
        $request = Yii::$app->request;
        $get = $request->get('filter');

        if(isset($get))
        {
            $begin = $get['begin'];
            $end = $get['end'];
            if($begin>$end){
                $response = Yii::$app->getResponse();
                $response->statusCode = 400;
                $error = ([
                    'status' => $response->statusCode,
                    'title' => 'Incorrect period',
                    'Detail' => 'Begin time can\'t be bigger than the time of end',
                ]);
                $response->data = $error;
            }else{
                $model = new ApiTask();
                $model->begin = $begin;
                $model->end = $end;
                return $model->getTime();
            }
        }else{
            return ApiTask::find()->all();
        }
    }
}