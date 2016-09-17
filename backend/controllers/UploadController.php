<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\UploadForm;
use yii\web\UploadedFile;

class UploadController extends Controller
{
    public function actionUpload()
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->upload()) {
                // file is uploaded successfully
                return $this->redirect(['news/index']);
            }
        }

        return $this->render('upload', ['model' => $model]);
    }
}