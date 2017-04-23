<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Product */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php $img = $model->getImage(); ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
            'content:html',
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
            [
                'attribute' => 'image',
                'value' => "<img src='{$img->getUrl()}'>",
                'format' => 'html',
            ],
            // 'keywords',
            // 'description',
            // 'img',
            // 'hit',
            // 'new',
            // 'sale',
        ],
    ]) ?>

</div>
