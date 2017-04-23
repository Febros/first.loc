<?php
/**
 * Created by PhpStorm.
 * User: KT829A
 * Date: 22.02.2017
 * Time: 18:18
 */

namespace app\controllers;
use app\models\Category;
use app\models\Product;
use yii\web\HttpException;
use Yii;

class ProductController extends AppController
{
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $product = Product::findOne($id);
        if (empty($product))
        {
            throw new HttpException(404, 'Такого продутка нет');
        }
        $hits = Product::find()->where(['hit'=>'1'])->limit(6)->all();
        $this->setMeta( 'IronCart | '. $product->name,  $product->keywords, $product->description);
        return $this->render('view', compact('product', 'hits'));
    }
}