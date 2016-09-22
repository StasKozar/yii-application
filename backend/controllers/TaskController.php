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

            var_dump(true);
            var_dump($result);
            die();

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
        $tasks = Task::find()->all();

        if ($model->load(Yii::$app->request->post())) {
            $task_begin = new \DateTime($model->begin);
            $task_end = new \DateTime($model->end);
            $workTime = Task::getWorkTime();
            $workDays = Task::getWorkDays();

            if($task_begin >= $task_end){
                return $this->render('create', [
                    'model' => $model,
                    'message' => 'Date of begin can\'t be bigger or equal then date of end ',
                ]);
            }

            if (in_array(date_format($task_begin, 'w'), $workDays)
                && in_array(date_format($task_end, 'w'), $workDays)
            ) {
                if (date('H:i', $workTime['begin']) <= date_format($task_begin, 'H:i')
                    && date('H:i', $workTime['end']) >= date_format($task_end, 'H:i')
                ) {
                    if($tasks === null)
                    {
                        $model->name = 'task1';
                        $model->save();

                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                    $temp_begin = '';
                    $temp_end = '';
                    foreach ($tasks as $key => $value) {
                        $value_begin = new \DateTime($value->attributes['begin']);
                        $value_end = new \DateTime($value->attributes['end']);

                        if (($value_begin > $task_begin || $value_end < $task_begin)
                            && ($value_begin > $task_end || $value_end < $task_end)
                            && !($task_begin < $value_begin && $value_end < $task_end)
                        ) {
                            $temp_begin = (array)$task_begin;
                            $temp_end = (array)$task_end;

                        } else {
                            return $this->render('create', [
                                'model' => $model,
                                'message' => 'Please choose another datetime!',
                            ]);
                        }
                    }

                    $i = 1;
                    while (true) {
                        $task = 'task' . $i;
                        if (!array_key_exists($task, $tasks)/*$key !== $task*/) {
                            $model->begin = $temp_begin['date'];
                            $model->end = $temp_end['date'];
                            $model->name = $task;
                            if($model->save()){
                                return $this->redirect(['view', 'id' => $model->id]);
                            };
                        }
                        $i++;
                    }
                } else {
                    return $this->render('create', [
                        'model' => $model,
                        'message' => 'Please choose another datetime!',
                    ]);
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'message' => 'Please choose another datetime!',
                ]);
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
