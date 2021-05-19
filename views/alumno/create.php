<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Alumno */

$this->title = Yii::t('app', 'Crear Alumno');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Alumnos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alumno-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'alumnoForm' => $alumnoForm,
    ]) ?>

</div>
