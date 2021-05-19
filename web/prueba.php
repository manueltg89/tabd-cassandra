<?php 


		$bd = new \mysqli('127.0.0.1', 'root', '', 'bdweb');

        if ($bd->connect_errno)
        {
            echo "Fallo al conectar a MySQL: (" . $bd->connect_errno . ") " . $bd->connect_error;
            die;
        }


        $bd->set_charset("utf8");

        /*$file = file_get_contents(Yii::$app->basePath.'/web/file.csv');

        $rows = explode("\n", $file);

        $str = $rows[0];
        $var = explode(';', $rows[0]);*/


        $fila = 1;
        if (($gestor = fopen('file.csv', "r")) !== FALSE) {
            while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) 
            {
                
                $str = $datos[1];

                $str = mb_convert_encoding($str, "UTF-8", "ISO-8859-1");

                $sql = "INSERT INTO webs(url, name) VALUES('".$str."', '".$str."')";
                $bd->query($sql);

                die;
                //$numero = count($datos);
                //echo "<p> $numero de campos en la línea $fila: <br /></p>\n";
                //$fila++;
                /*for ($c=0; $c < $numero; $c++) {
                    echo $datos[$c] . "<br />\n";
                }*/
            }
            fclose($gestor);
        }


        die;


?>