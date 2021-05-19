<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Alumno */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="alumno-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($alumnoForm, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($alumnoForm, 'nombre')->textInput(['maxlength' => true]) ?>

    <?= $form->field($alumnoForm, 'edad')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
