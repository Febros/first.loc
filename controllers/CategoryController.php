<?php
/**
 * Created by PhpStorm.
 * User: KT829A
 * Date: 20.02.2017
 * Time: 13:26
 */

namespace app\controllers;
use app\models\Category;
use app\models\Product;
use yii\data\Pagination;
use yii\web\HttpException;
use Yii;

class CategoryController extends AppController
{
    public function actionIndex()
    {
        $hits = Product::find()->where(['hit'=>'1'])->limit(6)->all();
        $this->setMeta('IronCart');

        $rangeQuery = Product::find()->orderBy(['price' => SORT_DESC]);/*СОРТИРОВКА ТОВАРОВ ПО ЦЕНЕ*/

        return $this->render('index', compact('hits', 'rangeQuery'));
    }

    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $query = Product::find()->where(['category_id'=> $id]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 6, 'forcePageParam' => false, 'pageSizeParam' => false]);
        $products = $query->offset($pages->offset)->limit($pages->limit)->all();
        $category = Category::findOne($id);
        $this->setMeta( 'IronCart | '. $category->name,  $category->keywords, $category->description);
        if (empty($category))
        {
            throw new HttpException(404, 'Такой категории нет');
        }
        return $this->render('view', compact('products', 'pages', 'category'));
    }
    /**********************************************************
     *                       Поиск                            *
    ***********************************************************/
    public function actionSearch()
    {
        $q = trim(Yii::$app->request->get('q'));
        $this->setMeta( 'IronCart | '. $q);
        if (!$q)
        {
            return $this->render('search');
        }
        $query = Product::find()->where(['like', 'name', $q]);
        $pages = new Pagination
        ([
            'totalCount' => $query->count(),
            'pageSize' => 3,
            'forcePageParam' => false,
            'pageSizeParam' => false
        ]);
        $products = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('search', compact('products', 'pages', 'q'));
    }
}