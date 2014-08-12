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
    //DATOS: FECHA DE INICIO - FECHA DE FIN -PROVINCIA =========================
    $dia_ini=7;     $mes_ini=10;    $ano_ini=5;
    $dia_fin=9;    $mes_fin=10;     $ano_fin=5;
    $provincia=1;
    //END DATOS ================================================================
    
    //ABRIR URL
    //URL de ejemplo: http://www.juntadeandalucia.es/medioambiente/atmosfera/informes_siva/jul14/nal140721.htm
    
    $provincia_a = array("","al", "ca", "co", "gr", "hu", "ja", "ma", "se");
    $provincia=$provincia_a[$provincia];
    
    $mes_a = array("","ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic");
    
    set_time_limit(3600); //Configuración/Ampliación del tiempo límite de buble (60 minutos)

    function cURL($url, $fp){
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec($ch);
        curl_close($ch);
    }
    $fp = fopen($provincia.".txt","w") or exit("Unable to open file!");  //Reinicia el contenido del fichero
    fclose($fp);
    
    $fp = fopen($provincia.".txt","a+") or exit("Unable to open file!"); //Añade; abre o crea un fichero de texto para actualización, escribiendo al final del fichero (EOF)
        
    $mes_lim=12;
    for($ano=$ano_ini;$ano<=$ano_fin;$ano++){ //Año
        if(strlen($ano)<=1) $ano="0".$ano;
        for($mes=$mes_ini;$mes<=$mes_lim;$mes++){ //Mes
            $dias_mes=cal_days_in_month(1,$mes,$ano); //Calcula los dias que tiene el mes
            
            //BEGIN Excepción para la fecha de fin
            if($dia>=$dia_ini) 
                $dia_ini=1;
            if($ano==$ano_fin && $mes==$mes_fin) 
                $dias_mes=$dia_fin;
            //END
            
            $mes_n=$mes;
            if(strlen($mes)<=1) $mes="0".$mes;
            for($dia=$dia_ini;$dia<=$dias_mes;$dia++){ //Día
                if(strlen($dia)<=1) $dia="0".$dia;
                //$dato="jul14/nal140721";
                $dato = $mes_a[$mes_n].$ano."/n".$provincia.$ano.$mes.$dia;
                echo "DATO FECHA: ".$dato."<br />";
            
                $url="http://www.juntadeandalucia.es/medioambiente/atmosfera/informes_siva/".$dato.".htm";
                echo"URL ANALIZADA: ".$url."<br /><br />";

                $registros++;
                fwrite($fp, cURL($url,$fp) . PHP_EOL);
            }
            
            //BEGIN Excepción para la fecha de fin
            if($mes>=$mes_ini)
                $mes_ini=1;
            if($ano==$ano_fin) 
                $mes_lim=$mes_fin;
            //END
        }
    }
    
    fclose($fp);
    
    //RESULTADOS
    echo"<br />TAREA FINALIZADA: ".$registros." DIAS REGISTRADOS<br /><br />"; 
    
    //CARGAMOS EL FICHERO DE FILTRO
    include('filtrar.php');
?>