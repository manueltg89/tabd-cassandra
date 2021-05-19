<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use GraphAware\Neo4j\Client\ClientBuilder;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {

        return $this->render('index');
    }


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionBenchmark()
    {

        /*$client = ClientBuilder::create()
            //->addConnection('default', 'http://neo4j:1234@localhost:7474') // Example for HTTP connection configuration (port is optional)
            ->addConnection('bolt', 'bolt://neo4j:1234@localhost:7687') // Example for BOLT connection configuration (port is optional)
            ->build();

        $result = $client->run('MATCH (n:alumnos) RETURN n');

        // get all records
        //$records = $result->getRecords();

        //var_dump($records);
        die;*/

        $time_start = microtime(true);

        /*for($i=0; $i<150000; $i++)
        {
         
            $sql = "INSERT INTO alumnos (name, email, age) VALUES (:name, :email, :age)";
            Yii::$app->db->createCommand($sql)->bindValues([':name'=>"manuel", ':email'=>"manuel@buyin.es", ':age'=>18])->execute();

        }*/

        $sql = "SELECT * FROM alumnos LIMIT 160000";
        $rows = Yii::$app->db->createCommand($sql)->queryAll();

        $count=0;
        
        foreach ($rows as $key => $value) {
            $count++;
        }

        echo $count."<br/>";

        $time_end = microtime(true);
        $time = $time_end - $time_start;

        echo "Tiempo de las operacionees en MySQL ".$time;
        echo "<br/>";

        $time_start = microtime(true);

        $cluster   = \Cassandra::cluster()                 // connects to localhost by default
                 ->build();

        $keyspace  = 'tabd';
        $session   = $cluster->connect($keyspace);        // create session, optionally scoped to a keyspace
        

        /*for($i=0; $i<150000; $i++)
        {

            // also supports prepared and batch statements
            $statement = new \Cassandra\SimpleStatement(       
                "INSERT INTO alumnos (id, name, email, age) VALUES (?, ?, ?, ?)"
            );

            $options = [

                "arguments" => [
                    new \Cassandra\Uuid(),
                    'manuel',
                    'manuel.trinidad@uca.es',
                    18
                ]
            ];

            //$session->execute($statement, $options);
            $session->executeAsync($statement, $options);
        }*/

        $statement = new \Cassandra\SimpleStatement(       // also supports prepared and batch statements
            "SELECT * FROM alumnos"
        );
    
        $results = $session->execute($statement, ['page_size' => 160000]);

        $count = 0;
        
        foreach ($results as $key => $value) {
            $count++;
            var_dump($value);
            die;
        }

        echo $count."<br/>";

        //$future    = $session->executeAsync($statement);  // fully asynchronous and easy parallel execution
        //$results    = $future->get();                      // wait for the result, with an optional timeout
        

        $time_end = microtime(true);
        $time = $time_end - $time_start;

        echo "Tiempo de las operacionees en Cassandra ".$time;
        echo "<br/>";

        //return $this->render('index');
    }


    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
