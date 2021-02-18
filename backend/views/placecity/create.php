<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PlaceCity */

$this->title = 'Create Place City';
$this->params['breadcrumbs'][] = ['label' => 'Place Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="place-city-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
