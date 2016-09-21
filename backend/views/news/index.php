<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'News');
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create News'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'article',
            'intro_text',
            'author',
            [
                'attribute' => 'created_at',
                'format' => 'html',
                'label' => Yii::t('app', 'Created At'),
                'value' => function($data)
                {
                    return Yii::$app->formatter->asDate($data['created_at']);
                }
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'html',
                'label' => Yii::t('app', 'Updated At'),
                'value' => function($data)
                {
                    return Yii::$app->formatter->asDate($data['updated_at']);
                }
            ],
            [
                'label' => Yii::t('app', 'Image'),
                'format' => 'html',
                'value' => function($data){
                    return Html::img('/uploads/'.str_replace(DS, '/', $data['image']),
                        ['width' => '50px']);
                },
            ],


            ['class' => 'yii\grid\ActionColumn'],

        ],
    ]); ?>
</div>
