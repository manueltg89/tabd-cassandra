<?php

namespace app\controllers;

use Yii;
use app\models\Alumno;
use app\models\AlumnoSearch;
use app\models\AlumnoForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;


/**
 * AlumnoController implements the CRUD actions for Alumno model.
 */
class AlumnoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Alumno models.
     * @return mixed
     */
    public function actionIndex()
    {

        //sacamos todos los resultados almacenados en Cassandra en la tabla alumnos
        $cluster   = \Cassandra::cluster()                 // connects to localhost by default
        ->build();

        $keyspace  = 'mykeyspace';
        $session   = $cluster->connect($keyspace);        // create session, optionally scoped to a keyspace

        $statement = new \Cassandra\SimpleStatement(       // also supports prepared and batch statements
            "SELECT * FROM alumnos"
        );
        
        $results = $session->execute($statement, ['page_size' => 100]);

        $arrayResults = [];

        foreach ($results as $key => $value) 
            $arrayResults[] = ['id'=>$value['email']."_".$value['edad'], 'email'=>$value['email'], 'nombre'=>$value['nombre'], 'edad'=>$value['edad']];
        
        $provider = new ArrayDataProvider([
            'allModels' => $arrayResults,
            //'sort'=>false,
            'sort' => [
                'attributes' => ['id', 'email', 'nombre', 'edad'],
            ],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $provider,
        ]);

    }

    /**
     * Displays a single Alumno model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Alumno model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $alumnoForm = new AlumnoForm();

        if($alumnoForm->load(Yii::$app->request->post())) 
        {

            /*----------  Transacción en Cassandra  ----------*/
    
            $cluster   = \Cassandra::cluster()                 // connects to localhost by default
                 ->build();

            $keyspace  = 'mykeyspace';
            $session   = $cluster->connect($keyspace);        // create session, optionally scoped to a keyspace

            // also supports prepared and batch statements
            $statement = new \Cassandra\SimpleStatement(       
                "INSERT INTO alumnos (email, nombre, edad) VALUES (?, ?, ?)"
            );

            $options = [

                "arguments" => [
                    $alumnoForm->email,
                    $alumnoForm->nombre ?? null,
                    intval($alumnoForm->edad)
                ]
            ];

            $session->execute($statement, $options);
            //$session->executeAsync($statement, $options);

            Yii::$app->session->setFlash('success', 'El Alumno ha sido guardado correctamente.');

            /*----------  FIN Transacción en Cassandra  ----------*/

            return $this->redirect(['index']);
        }  

        return $this->render('create', [
            'alumnoForm' => $alumnoForm,
        ]);
    
    }

    /**
     * Updates an existing Alumno model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($email, $edad)
    {

        $alumnoForm = new AlumnoForm;

        /*----------  Sacamos el valor que tiene desde la base de datos  ----------*/
        
        $cluster   = \Cassandra::cluster()                 // connects to localhost by default
                 ->build();

        $keyspace  = 'mykeyspace';
        $session   = $cluster->connect($keyspace);        // create session, optionally scoped to a keyspace

        // also supports prepared and batch statements
        $statement = new \Cassandra\SimpleStatement(       
            "SELECT * FROM alumnos WHERE email = ? AND edad = ?"
        );

        $options = [
            "arguments" => [
                $email,
                intval($edad)
            ],
            "page_size" => 100
        ];

        $result = $session->execute($statement, $options);

        if(!empty($result[0]))
        {

            $model = $result[0];

            $alumnoForm->email = $model['email'];
            $alumnoForm->nombre = $model['nombre'];
            $alumnoForm->edad = $model['edad'];

        }

        /*----------  FIN Sacamos el valor que tiene desde la base de datos  ----------*/

        //si lo que nos viene es una petición post...
        if ($alumnoForm->load(Yii::$app->request->post()))
        {

            $statement = new \Cassandra\SimpleStatement(       
                "UPDATE alumnos SET nombre = ? WHERE email = ? AND edad = ?"
            );

            $options = [
                "arguments" => [
                    $alumnoForm->nombre,
                    $alumnoForm->email,
                    intval($alumnoForm->edad)
                ],
                "page_size" => 100
            ];

            $result = $session->execute($statement, $options);
            
            Yii::$app->session->setFlash('success', 'El Alumno ha sido actualizado correctamente.');

            return $this->redirect(['index']);

        }        

        return $this->render('update', [
            'alumnoForm' => $alumnoForm,
        ]);

    }


    public function actionDelete()
    {
        //sacamos los parametros que nos vienen

        $email = Yii::$app->request->post('email');
        $edad = Yii::$app->request->post('edad');

        //si es una peticion post...
        if(Yii::$app->request->isPost)
        {

            /*----------  Sacamos el valor que tiene desde la base de datos  ----------*/
        
            $cluster   = \Cassandra::cluster()                 // connects to localhost by default
                     ->build();

            $keyspace  = 'mykeyspace';
            $session   = $cluster->connect($keyspace);        // create session, optionally scoped to a keyspace

            // also supports prepared and batch statements
            $statement = new \Cassandra\SimpleStatement(       
                "DELETE FROM alumnos WHERE email = ? and edad = ?"
            );

            $options = [
                "arguments" => [
                    $email,
                    intval($edad)
                ]
            ];

            $result = $session->execute($statement, $options);

            Yii::$app->session->setFlash('success', 'El Alumno ha sido eliminado correctamente.');

            /*----------  FIN Sacamos el valor que tiene desde la base de datos  ----------*/    

        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Alumno model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Alumno the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Alumno::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
