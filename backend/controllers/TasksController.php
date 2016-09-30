<?php
/**
 * Created by PhpStorm.
 * User: StasKozar
 * Date: 29/09/2016
 * Time: 15:33
 */

namespace backend\controllers;

use backend\models\ApiTask;
use backend\models\Task;
use Yii;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\rest\ActiveController;
use tuyakhov\jsonapi;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class TasksController extends Controller
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

    public function actionIndex()
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
            return Task::find()->all();
        }
    }

    public function actionView($id)
    {
        return Task::findOne($id);
    }

    public function actionsDelete($id)
    {
        return Task::findOne($id)->delete();
    }

   /* public function actions()
    {
        $actions = parent::actions();

        // настроить подготовку провайдера данных с помощью метода "prepareDataProvider()"
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }*/

    public function prepareDataProvider()
    {
        $formatter = new jsonapi\JsonApiResponseFormatter();
        // подготовить и вернуть провайдер данных для действия "index"

        if(isset($_GET['filter']))
        {
            $begin = $_GET['filter']['begin'];
            $end = $_GET['filter']['end'];
            if($begin>$end){
                $errors = ['Begin time can\'t be bigger than the time of end'];
                return $errors;
            }else{
                $model = new Task();
                $model->begin = $begin;
                $model->end = $end;
                return $formatter->format($model->getTime());
            }
        }else{
            return Task::find()->all();
        }
    }
}