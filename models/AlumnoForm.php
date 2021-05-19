<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * AlumnoForm is the model behind the contact form.
 */
class AlumnoForm extends Model
{

    public $email;
    public $nombre;
    public $edad;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['email', 'edad'], 'required'],

            // email has to be a valid email address
            ['email', 'email'],
            ['edad', 'integer'],
            ['nombre', 'safe'],
            

        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'nombre' => Yii::t('app', 'Nombre'),
            'edad' => Yii::t('app', 'Edad')
        ];
    }


    //funcion que restablece el modelo
    public function unsetAttributes($names=null)
    {
        if($names===null)
            $names=$this->attributeLabels();
        foreach($names as $key => $name)
            $this->$key=null;
    }

}
