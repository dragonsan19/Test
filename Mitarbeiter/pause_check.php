<?php
  header('Access-Control-Allow-Origin: *');
  //$time wird zur angeforderten Zeit
  $time = isset($_REQUEST['time'])?$_REQUEST['time']:"";
  //$time2 bekommt die Zeitangabe von $time und dazu werden 6 Stunden addiert
  $time2 = date('H:i' ,strtotime('+6 hours', strtotime($time)));
  //Abfrage ob $time2 jetzt entspricht
  if($time2 == date('H:i')){
    echo "1";
  }
  else{
    echo "2";
  }
?>