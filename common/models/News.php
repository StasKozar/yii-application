<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property string $article
 * @property string $intro_text
 * @property string $description
 * @property string $author
 * @property string $created_at
 * @property string $updated_at
 */
class News extends \yii\db\ActiveRecord
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
            [['article', 'intro_text', 'author'], 'string', 'max' => 255],
            [['image'], 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'article' => 'Article',
            'intro_text' => 'Intro Text',
            'description' => 'Description',
            'author' => 'Author',
            'image' => 'Image',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
