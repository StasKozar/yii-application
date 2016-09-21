<?php

namespace backend\controllers;

use Yii;
use common\models\News;
use common\models\NewsSearch;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
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
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return date('dd/MM/yyyy H:i:s');
                }
            ]
        ];
    }

    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single News model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new News();
        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'image');
            if(!empty($file))
            {
                $i = 0;
                while(file_exists(Yii::getAlias('@uploads') . DS . $model->image))
                {
                    $model->image = $file->baseName .'_'.$i.'.'.$file->extension;
                    $i++;
                }
            }

            if($model->save())
            {
                if (!empty($file))
                    $file->saveAs(Yii::getAlias('@uploads') . DS . $model->image);

                return $this->redirect(['view', 'id' => $model->id]);
            }
            return $this->render('create', ['model' => $model]);
        } else {
            return $this->render('create', ['model' => $model]);
        }
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())){
            $file = UploadedFile::getInstance($model, 'image');
            if (!empty($file))
            {
                unlink(Yii::getAlias('@uploads') . DS . $model->oldAttributes['image']);
                $i = 0;
                while(file_exists(Yii::getAlias('@uploads') . DS . $model->image))
                {
                    $model->image = $file->baseName .'_'.$i.'.'.$file->extension;
                    $i++;
                }

            }
            else{
                $model->image = $model->oldAttributes['image'];
            }
            if($model->save())
            {
                if (!empty($file))
                    $file->saveAs(Yii::getAlias('@uploads') . DS . $model->image);

                return $this->redirect(['view', 'id' => $model->id]);
            }
            return $this->render('update', ['model' => $model]);
        } else {
            return $this->render('update', ['model' => $model]);
        }
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if(file_exists(Yii::getAlias('@uploads') . DS . $model->image) && $model->image !== null)
            unlink(Yii::getAlias('@uploads') . DS . $model->image);
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
