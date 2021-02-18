<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PlacecitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Place Cities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="place-city-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Place City', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'place_region_id',
            'created_at',
            'modified_at',
            //'created_by',
            //'modified_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
