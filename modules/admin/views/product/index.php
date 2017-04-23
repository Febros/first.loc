<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            /*'category_id',*/
            [
                    'attribute' => 'category_id',
                    'value' => function($data)
                    {
                        return $data->category->name;
                    },
            ],
            'name',
            /*'content:ntext',*/
            'price',
            [
                'attribute' => 'hit',
                'value' => function($data)
                {
                    return !$data->hit ? '<i class="fa fa-times" aria-hidden="true"></i>' : '<i class="fa fa-check" aria-hidden="true"></i>';
                },
                'format' => 'html',
            ], [
                'attribute' => 'new',
                'value' => function($data)
                {
                    return !$data->new ? '<i class="fa fa-times" aria-hidden="true"></i>' : '<i class="fa fa-check" aria-hidden="true"></i>';
                },
                'format' => 'html',
            ], [
                'attribute' => 'sale',
                'value' => function($data)
                {
                    return !$data->sale ? '<i class="fa fa-times" aria-hidden="true"></i>' : '<i class="fa fa-check" aria-hidden="true"></i>';
                },
                'format' => 'html',
            ],
            // 'keywords',
            // 'description',
            // 'img',
            // 'hit',
            // 'new',
            // 'sale',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
