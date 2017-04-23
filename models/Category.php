<?php
/*Таблица категорий связана с таблицей продукты многие ко многим(hasMany)*/

namespace app\models;
use yii\db\ActiveRecord;

class Category extends ActiveRecord
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
        return 'category';
    }

    public function  getProducts()
    {
        return $this->hasMany(Product::className(), ['category_id' => 'id']);
    }

}