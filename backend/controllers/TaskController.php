<?php

namespace backend\controllers;

use Faker\Provider\cs_CZ\DateTime;
use Yii;
use backend\models\Task;
use backend\models\TaskSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Task models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $tasks = Task::find()->all();
        $workTime = Task::getWorkTime();
        $begin = date('H:i:s',$workTime['begin']);
        $end = date('H:i:s',$workTime['end']);
        $workDays = Task::getWorkDays();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tasks' => $tasks,
            'begin' => $begin,
            'end' => $end,
            'workDays' => $workDays
        ]);
    }

    /**
     * Displays a single Task model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionSearch()
    {
        $model = new Task();
        $free = null;
        $busy = null;
        $notAvailable = null;
        if($model->load(Yii::$app->request->post()))
        {
            $result = $model->getTime();
            $free = $result['freeTime'];
            $busy = $result['busyTime'];
            $notAvailable = $result['notAvailableTime'];

            return $this->render('search', [
                'model' => $model,
                'free' => $free,
                'busy' => $busy,
                'notAvailable' => $notAvailable,
            ]);
        }
        else
        {
            return $this->render('search', [
                'model' => $model,
                'free' => $free,
                'busy' => $busy,
                'notAvailable' => $notAvailable,
            ]);
        }
    }

    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate()
    {
        $model = new Task();

        if ($model->load(Yii::$app->request->post())) {
            $result = $model->validateDate();
            if (!empty($result['message'])){
                return $this->render('create', [
                    'model' => $model,
                    'message' => $result['message'],
                ]);
            }
            if (!empty($result['begin']))
                $model->begin = $result['begin'];
            if (!empty($result['end']))
                $model->end = $result['end'];
            $i = 1;
            while (true) {
                $task = 'task' . $i;
                $model->name = $task;
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                };
                $i++;
            }

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Task model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
