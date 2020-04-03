<?php
  //Startet die Session
  session_start();
  //Beendet die Session
  session_destroy();
  //Bringt den Mitarbeiter zum login.php
  header('Location: login.php');
?>