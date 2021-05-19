<?php

use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

?>

<div class="site-index">

    <div class="jumbotron">
        <h1>Mi Primera Integración con <br/>Yii 2.0 y Apache Cassandra</h1>
        <?php echo Html::a('Alumnos', Url::to(['alumno/index']), ['class'=>'btn btn-success']); ?>
    </div>
    
</div>
