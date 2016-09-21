<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\News */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'News'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            [
                'attribute'=>'image',
                'label'=> Yii::t('app', 'Image'),
                'value'=> '/uploads'. DS . $model->image,
                'format'=>['image',['width'=>100, 'height'=>100]]
            ],
            'article',
            'intro_text',
            'description:ntext',
            'author',
            [
                'attribute' => 'created_at',
                'format' => 'html',
                'label' => Yii::t('app', 'Created At'),
                'value' => Yii::$app->formatter->asDate($model['created_at']),
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'html',
                'label' => Yii::t('app', 'Updated At'),
                'value' => Yii::$app->formatter->asDate($model['updated_at']),
            ],

        ],
    ]) ?>

</div>
