<?php
//Start der Session
  session_start();
  //Erstellt HTML Section welches das Programm zentriert
  echo "<section class='center'>";
  if(!empty($_POST)){
    //Entfernt Fehlermeldung
    if(!empty($_POST["fehlermb"])){
      echo "<script type='text/javascript'>";
      echo "document.getElementById('fehlerm').style.visibility = 'hidden';";
      echo "</script>";
    }
    //Login selbst, Überprüfung ob Benutzername und Kennwort Feld leer sind
    if(!empty($_POST['username']) && !empty($_POST['password'])){

      //Datenbank Verbindung
      $link = new mysqli("127.0.0.1", "root", "", "zeiterfassung");
      //Query um Daten aus der Datenbank zu bekommen, verbindet Query mit der Datenbank Verbindung
      $stmt = $link->prepare("SELECT * FROM mitarbeiter WHERE benutzername = ?");
      //Setzt das Fragezeichen in der Query zum Wert aus dem Benutzernamen Feld
      $stmt->bind_param('s', $_POST['username']);
      //Führt die Query aus
      $stmt->execute();
      //Speichert das Ergebnis der Query in die Variable
      $result = $stmt->get_result();
      //Über $user kann man auf die Daten des Benutzers zugreifen 
      $user = $result->fetch_object();
      //Abfrage ob in $user Daten drinnen sind, es kommen keine Daten rein wenn es den Benutzer in der Datenbank nicht gibt
      if($user != null){
        //Verschlüsselt das Passwort
        $password = md5($_POST['password'].$user->benutzername);

        //Passwort Überprüfung
        if($password == $user->kennwort){
          //Speicher die Loginzeit
          $_SESSION['time'] = date('H:i');
          //User Daten werden in Session gespeichert
          $_SESSION['user_id'] = $user->id;
          $_SESSION['vorname'] = $user->vorname;
          $_SESSION['nachname'] = $user->nachname;
          $_SESSION['benutzername'] = $user->benutzername;
          //Bei richtigem Login geht es auf die Startseit.php
          header('Location: startseite.php');
        }
        //Falsches Kennwort
        else{
          $fehler = "Kennwort ist falsch";
          fehlermf($fehler);
        }
        //Schließe Datenbank Anbindung
        mysqli_close($link);
      }
      //Wenn der Benutzername nicht existiert
      else{
        $fehler = "Benutzername existiert nicht.";
        fehlermf($fehler);
      }
    }
    //Wenn die Felder leer sind
    else{
      $fehler = "Bitte geben sie gültige Daten ein";
      fehlermf($fehler);
    }
  }
  //Fehlerausgabe
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
      background: #A9F5A9;
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
      margin-left:60px;
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
      background: #A9F5A9;
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
<!-- Überschrift der Login Seite-->
  <h1 class="ueberschrift">Login</h1>
    <form action="" method="post">
    <!-- Eingabe Felder für den Login-->
      <input class="box" placeholder="Benutzername" type="text" name="username"/><br /><br />
      <input class="box" placeholder="Kennwort" type="password" name="password"/><br /><br />
      <!-- Login Knopf-->
      <input class="anmelden" type="submit" value="Anmelden" />
    </form>
    <!-- Wechselt zum Admin login-->
    <a class="loginch" href=../admin/login.php>Admin Login</a>
</section>
</body>
</html>