<!--
//  SISTEMA DE ANÁLISIS INTELIGENTE DE DATOS
//  SOFTWARE DE MINERÍA DE DATOS

//  Copyright 2014 Pedro Fernández Bosch.
//  Departamento de Lenguajes y Sistemas Informáticos.
//  Universidad de Granada.
//  $Revisión: 1.0 $ $Fecha: 2014/05/19 13:45:30 $ 
//  $Ejecución Windows: XAMPP v3.2.1
-->
<?php     
    set_time_limit(3600); //Configuración/Ampliación del tiempo límite de buble (60 minutos)
    
    $leyenda="LEYENDA: Las unidades estan expresadas en microgramos/metrocubico(ug/m3).<br />
    Fecha-Hora<br />
    SO2: Dioxido de azufre<br />
    PART: Particulas en suspension<br />
    NO2: Dioxido de nitrogeno<br />
    CO: Monoxido de carbono<br />
    O3: Ozono<br />
    SH2: Acido sulfhidrico<br />";
    
    echo $leyenda."<br />";
    
    //LECTURA Y FILTRADO DEL CÓDIGO HTML
    $fp = fopen($provincia.".txt", "r") or exit("Unable to open file!");

    while(!feof($fp)){
        $con_linea++;
        //Leemos una lineadel fichero
        $entrada=fgets($fp);
        $entrada = strtolower($entrada);
        
        //Recogida de estaciones
        if(strstr($entrada, 'estacion')){
            $valor[1]='n'; $valor[2]='n'; $valor[3]='n'; $valor[4]='n'; $valor[5]='n'; $valor[6]='n';
            
            //Extraemos el texto que hay entre Estacion y Direccion (Que es el nombre de la estación)
            $estacion = explode('estacion',$entrada);
            $estacion = explode('direccion',$estacion[1]);
            $estacion = $estacion[0];
            
            //Filtro de codigo HTML
            $estacion = utf8_decode($estacion);
            $estacion = strip_tags($estacion); //http://notasweb.com/articulo/php/eliminar-etiquetas-html-de-una-cadena-de-texto.html
            $estacion = substr($estacion,7);
            $estacion = str_replace(' ', '-', $estacion);
            
            //echo $estacion."<br />";
        }
        
        //Comprobacion de valores de la tabla
        $elemento = array("","so2", "part", "no2", "co", "o3", "sh2");
        
        if(strstr($entrada, '<td class="cabtabla">')){
            for($i=1;$i<=6;$i++){
                if(strstr($entrada,$elemento[$i]))
                    $valor[$i]='s';
            }
        }

        //Recogida de valores
        if(strstr($entrada, '<tr><td>')){
            //$entrada = trim($entrada);
            echo $con_linea."-".htmlspecialchars($entrada)."<br />"; //Imprimir codigo html
            $entrada = preg_replace("/&#?[a-z0-9]+;/i","?",$entrada); 
            
            //Añadir coma (,) entre valores
            $entrada  = str_replace('</td><td>',',',$entrada);
            
            //Eliminar etiquetas html de las lineas de valores
            $atb = array('<tr><td>','</td></tr>');
            $entrada = str_replace($atb,'',$entrada);
            
            //echo  $con_linea."-".htmlspecialchars($entrada)."<br />"; //Imprimir codigo HTML
            
            $atributo = explode(",", $entrada); 
            
            //Concordancia de posición de resultados
            $j=1;
            for($i=1;$i<=6;$i++){
                if($valor[$i]=='s'){ //SO2,PART,NO2,CO,O3,SH2
                    $resultado[$i]=$atributo[$j];
                    $j++;
                }else
                    $resultado[$i]='?';
            }
            
            $salida=trim($atributo[0]).",".trim($resultado[1]).",".trim($resultado[2]).",".trim($resultado[3]).",".trim($resultado[4]).",".trim($resultado[5]).",".trim($resultado[6])."\n";
            
            echo $con_linea."-".$salida."<br />";
            
            $fp2 = fopen($provincia."-".$estacion.".txt","a+") or exit("Unable to open file!");
            fwrite($fp2,$salida);
            fclose($fp2);
        }
    }
    
    fclose($fp);
    
    //RESULTADOS
    echo"<br />TAREA FINALIZADA"; 
?>