<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Alumno */

$this->title = Yii::t('app', 'Actualizar {modelClass}: ', [
    'modelClass' => 'Alumno',
]) . $alumnoForm->email;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Alumnos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="alumno-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'alumnoForm' => $alumnoForm,
    ]) ?>

</div>
