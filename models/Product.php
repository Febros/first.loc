<?php
/*Таблица продуктов связана с таблицей категорий  один к одному(hasOne)*/

namespace app\models;
use yii\db\ActiveRecord;

class Product extends ActiveRecord
{
    /*Costa-rico(images-behavior module)*/
    public function behaviors()
    {
        return [
            'image' => [
                'class' => 'rico\yii2images\behaviors\ImageBehave',
            ]
        ];
    }

    public static function tableName()
    {
        return 'product';
    }

    public function getProduct()
    {

    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}