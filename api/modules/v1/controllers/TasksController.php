<?php
/**
 * Created by PhpStorm.
 * User: StasKozar
 * Date: 29/09/2016
 * Time: 15:33
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\Task;
use Yii;
use yii\rest\Controller;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class TasksController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Task';

    public function actions()
    {
        $actions = parent::actions();

        // настроить подготовку провайдера данных с помощью метода "prepareDataProvider()"
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    public function prepareDataProvider()
    {
        // подготовить и вернуть провайдер данных для действия "index"

        if(isset($_GET['begin']) && isset($_GET['end']))
        {
            $begin = $_GET['begin'];
            $end = $_GET['end'];
            $model = new Task();
            $model->begin = $begin;
            $model->end = $end;
            return $model->getTime();
        }else{
            return Task::find()->all();
        }
    }
}