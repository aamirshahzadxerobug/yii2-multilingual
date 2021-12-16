<?php

namespace DevGroup\Multilingual\tests\models;

use DevGroup\Multilingual\behaviors\MultilingualActiveRecord;
use DevGroup\Multilingual\traits\MultilingualTrait;

/**
 * Class Post
 * @property integer $author_id
 */
class Post extends \yii\db\ActiveRecord
{
    use MultilingualTrait;

    public function behaviors()
    {
        return [
            'multilingual' => [
                'class' => MultilingualActiveRecord::className(),
            ],
        ];
    }


    public static function tableName()
    {
        return '{{%post}}';
    }

    public static function applyDefaultScope($query) {
        return $query->where(['post_translation.is_published'=>1]);
    }
}