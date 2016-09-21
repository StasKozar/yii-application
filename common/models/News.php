<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property string $article
 * @property string $intro_text
 * @property string $description
 * @property string $author
 * @property string $image
 * @property string $created_at
 * @property string $updated_at
 */
class News extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article', 'intro_text', 'description', 'author'], 'required'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['article', 'intro_text', 'author', 'image'], 'string', 'max' => 255],
            ['image', 'image',
                'skipOnEmpty' => true,
                'extensions' => 'jpg, gif, png, jpeg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'article' => Yii::t('app', 'Article'),
            'intro_text' => Yii::t('app', 'Intro Text'),
            'description' => Yii::t('app', 'Description'),
            'author' => Yii::t('app', 'Author'),
            'image' => Yii::t('app', 'Image'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
