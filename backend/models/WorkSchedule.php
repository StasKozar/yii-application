<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "work_schedule".
 *
 * @property integer $day
 * @property integer $begin
 * @property integer $end
 */
class WorkSchedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'work_schedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['day', 'begin', 'end'], 'required'],
            [['day', 'begin', 'end'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'day' => 'Day',
            'begin' => 'Begin',
            'end' => 'End',
        ];
    }
}
