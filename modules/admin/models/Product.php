<?php

namespace app\modules\admin\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product".
 *
 * @property string $id
 * @property string $category_id
 * @property string $name
 * @property string $content
 * @property double $price
 * @property string $keywords
 * @property string $description
 * @property string $img
 * @property string $hit
 * @property string $new
 * @property string $sale
 */
class Product extends ActiveRecord
{
    public $image;/*Св-во для выбранного пользователем загружаемого изображения*/
    public $gallery; /*Загрузка нескольких изображений*/

    /*Costa-rico(images-behavior module)*/
    public function behaviors()
    {
        return [
            'image' => [
                'class' => 'rico\yii2images\behaviors\ImageBehave',
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(),['id'=>'category_id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'name'], 'required'],
            [['category_id'], 'integer'],
            [['content', 'hit', 'new', 'sale'], 'string'],
            [['price'], 'number'],
            [['name', 'keywords', 'description', 'img'], 'string', 'max' => 255],
            /*Правила для закгрузки изображения/изображений. Бросает эксепшн валидации*/
            [['image'], 'file', 'extensions' => 'png,jpg'],
            [['gallery'], 'file', 'extensions' => 'png, jpg', 'maxFiles' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID товара',
            'category_id' => 'Категория',
            'name' => 'Наименование',
            'content' => 'Контент',
            'price' => 'Цена',
            'keywords' => 'Ключевые слова',
            'description' => 'Описание',
            'image' => 'Изображение',
            'gallery' => 'Несколько изображений',
            'hit' => 'Хит',
            'new' => 'Новинка',
            'sale' => 'Распродажа',
        ];
    }

    public function upload()
    {
        if ($this->validate())
        {
            $path = 'upload/store/' . $this->image->baseName . '.' . $this->image->extension;
            $this->image->saveAs($path); /*path - путь к папке с изображениями(web/upload/store)*/
            $this->attachImage($path, true)/*Прикрепить изображение из папки с файлом к базе данных. Если true - главное  изображение*/;
            unlink($path); /*Удалить оригинальный файл изображения после ее прикрепления*/
            return true;
        }
        else
        {
            return false;
        }
    }

    /*Загрузка нескольких изображений*/
    public function uploadGallery()
    {
        if ($this->validate())
        {
            foreach ($this->gallery as $file)
            {
                $path = 'upload/store/' . $file->baseName . '.' . $file->extension;
                $file->saveAs($path); /*path - путь к папке с изображениями(web/upload/store)*/
                $this->attachImage($path)/*Прикрепить изображение из папки с файлом к базе данных*/;
                unlink($path); /*Удалить оригинальный файл изображения после ее прикрепления*/
            }
            return true;
        }
        else
        {
            return false;
        }
    }
}
