<?php
//Start der Session
  session_start();
  //Erstellt HTML Section welches das Programm zentriert
  echo "<section class='center'>";
  if(!empty($_POST)){
    if(isset($_POST['anmelden'])){
      if(!empty($_POST['username']) && !empty($_POST['password'])){
        //Datenbank Anbindung
        $link = new mysqli("127.0.0.1", "root", "", "zeiterfassung");

        //Query
        $stmt = $link->prepare("SELECT * FROM admin WHERE benutzername = ?");
        //Setzt das Fragezeichen in der Query zum benötigten Wert
        $stmt->bind_param('s', $_POST['username']);
        //Führt Query aus
        $stmt->execute();
        //Speichert das Ergebnis der Query in die Variable
        $result = $stmt->get_result();
        //Über $admin kann man auf die Daten des Benutzers zugreifen 
        $admin = $result->fetch_object();
        //Abfrage ob in $admin Daten drinnen sind, es kommen keine Daten rein wenn es den Benutzer in der Datenbank nicht gibt
        if($admin != null){
          //Verschlüsselt Passwort
          $password = md5($_POST['password'].$admin->benutzername);

          //Passwort Überprüfung
          if($password == $admin->kennwort){
            //Admin Daten werden in Session gespeichert
            $_SESSION['admin_id'] = $admin->id;
            $_SESSION['admin_benutzername'] = $admin->benutzername;
            header('Location: startseite.php');
          }
          else{
            $fehler = "Kennwort ist falsch";
            fehlermf($fehler);
          }
          //Schließe Datenbank Anbindung
          mysqli_close($link);
        }
        else{
          $fehler = "Benutzername existiert nicht.";
          fehlermf($fehler);
        }
      }
      else{
        $fehler = "Empty input";
        fehlermf($fehler);
      }
    }
  }
  //Fehlermeldung
  function fehlermf($fehlermeldung){
    echo "<form type='Post'>";
      echo "<div id='fehlerm' class='fehlerm'>";
      echo $fehlermeldung;
      echo "<input class='moveB' type='submit' name='fehlermb' value='Ok'>";
      echo "</div>";
      echo "</form>";
      echo "<script type='text/javascript'>";
      //Fehlerausgabe wird sichtbar gemacht
      echo "document.getElementById('fehlerm').style.visibility = 'visible';";
      echo "</script>";
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Zeiterfassung</title>
<style>
/*Zentriert das Programm*/ 
  section.center{
      background: #819FF7;
      font-weight: bold;
      border: solid;
      padding-left: 80px;
      padding-top: 30px;
      padding-bottom: 30px;
      margin: auto;
      width: 300px;
      height: 300px;
    }
    /*Formatiert die Überschrift*/
    .ueberschrift{
      font-size: 40px;
      margin-left: 5px;
    }
    /*Formatierung der Anmeldungs Felder*/
    .box{
      width: 230px;
      font-size: 30px;
    }
    /*Formatierung des Anmelden Knopfs*/
    .anmelden{
      margin-left: 40px;
      font-size: 30px;
    }
    /*Formatiert die Fehlermeldung*/
    .fehlerm{
      background: #819FF7;
      position: absolute;
      font-weight: bold;
      border: solid;
      padding: 5px;
      width: 200px;
      margin-left: 10px;
      margin-top: 100px;
      font-size: 20px;
      visibility: hidden;
    }
    /*Knopf der Fehlermeldung*/
    .moveB{
      margin-left: 160px;
    }
    /*Formatiert Hyperlink für den Login wechsel*/
    .loginch{
      margin-left: -50px;
    }
  </style>
</head>
<body>
<!-- Überschrift der Admin Login Seite-->
  <h1 class="ueberschrift">Admin Login</h1>
    <form action="" method="post">
    <!-- Eingabe Felder für den Login-->
      <input class="box" placeholder="Benutzername" type="text" name="username"/><br /><br />
      <input class="box" placeholder="Kennwort" type="password" name="password"/><br /><br />
      <!-- Login Knopf-->
      <input class="anmelden" type="submit" name="anmelden" value="Anmelden" />
    </form>
    <!-- Wechselt zum Mitarbeiter login-->
    <a class="loginch" href=../mitarbeiter/login.php>Mitarbeiter Login</a>
</section>
</body>
</html>