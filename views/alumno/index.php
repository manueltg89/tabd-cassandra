<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\web\View;
use yii\bootstrap\Alert;

/* @var $searchModel app\models\AlumnoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Alumnos');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alumno-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Crear Alumno'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(['id'=>'gridview-alumnos', 'timeout'=>false]); ?>    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'email:email',
            'nombre',
            'edad',
            [   
                'class' => 'yii\grid\ActionColumn',
                'header'=>Yii::t('app', 'actions'),
                'template'=>'{update_alumno}&nbsp;{delete_alumno}',
                    'buttons'=>[

                        'update_alumno' => function ($url, $model) {     
                            return Html::a('<span class="glyphicon glyphicon-edit"></span>', Url::to(['alumno/update', 'email'=>$model['email'], 'edad'=>$model['edad']]), ['title' => Yii::t('app', 'update'), 'data-pjax'=>0]);                               
                        },

                        'delete_alumno' => function ($url, $model) {     
                            return Html::a('<span class="glyphicon glyphicon-remove"></span>','javascript:void(0);', ['title' => Yii::t('app', 'delete'), 'data-pjax'=>0, 'data-email'=>$model['email'], 'data-edad'=>$model['edad'], 'onclick'=>'ajaxDeleteRow(this);']);                               
                        },
                    
                    ]    
            ]
        ],
    ]); ?>
<?php Pjax::end(); ?></div>

<?php echo Html::hiddenInput('ajax-url', yii\helpers\Url::to(['alumno/delete']), ['id'=>'ajax-url']); ?>

<?php 

    $script = "

        function ajaxDeleteRow(obj)
        {

            let url = $('#ajax-url').val();

            let email = $(obj).data('email');
            let edad = $(obj).data('edad');

            let result = window.confirm('¿Está seguro de que desea eliminar el registro?');

            if(result == true)
            {
                $.post(url, {email: email, edad: edad}, function(data){}, 'json');
            }   

            return ;

        }


    ";


    $this->registerJs(
        $script,
        View::POS_END,
        'js-script'
    );

?>
