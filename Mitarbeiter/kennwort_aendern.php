<?php 
    //Startet die Session
    session_start();   
    //Erstellt HTML Section welches das Programm zentriert
    echo "<section class='center'>";
    //Wenn der Button Abbrechen gedrückt wird
    if (!empty($_POST['abbrechen'])) {
      //Wechselt auf die startseite.php
      header('Location: startseite.php');
    }
    //Wenn der Button Speichern gedrückt wird
    else if(!empty($_POST['speichern'])){
      //Überprüfung ob user_id existiert
      if(isset($_SESSION['user_id'])){
        //Überprüfung ob die Felder leer sind
        if((!empty($_POST['newpass']) && !empty($_POST['newpassa'])) && $_POST['newpass'] == $_POST['newpassa']){
          //$user wird zum Benutzernamen
          $user = $_SESSION['benutzername'];
          //Verschlüsselt das Passwort
          $password = md5($_POST['newpass'].$user);
          //Datenbank Anbindung
          $link = new mysqli("127.0.0.1", "root", "", "zeiterfassung");
          //Query
          $stmt = $link->prepare("UPDATE mitarbeiter SET kennwort = ? WHERE benutzername = ?");
          //Wechselt die Fragezeichen in der Query mit den benötigten Daten aus
          $stmt->bind_param('ss', $password, $_SESSION['benutzername']);
          //Führt die Query aus
          $stmt->execute();
          //Schließt die Datenbank Anbindung
          mysqli_close($link);
          //Wechselt zur startseite.php
          header('Location: startseite.php');
        }
        //Fehlermeldung
        else{
            echo "<form type='Post'>";
            echo "<div id='fehlerm' class='fehlerm'>";
            echo "Leere Passwörter sind nicht erlaubt und die Passwörter müssen übereinstimmen.";
            echo "<input class='moveB' type='submit' name='fehlermb' value='Ok'>";
            echo "</div>";
            echo "</form>";
            echo "<script type='text/javascript'>";
            echo "document.getElementById('fehlerm').style.visibility = 'visible';";
            echo "</script>";
        }
      }
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
      padding-left: 60px;
      padding-top: 30px;
      padding-bottom: 30px;
      margin: auto;
      width: 300px;
      height: 300px;
    }
    /*Formatiert die Überschrift*/
    .ueberschrift{
      font-size: 30px;
      margin-left: 17px;
    }
    /*Formatiert die Kennwort ändern Felder*/
    .box{
      width: 260px; 
      font-size: 20px;
    }
    /*Formatiert die Knöpfe Abbrechen und Speichern*/
    .chpw{
      margin-left: 26px;
      font-size: 15px;
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
  </style>
</head>
<body>
<!-- Überschrift der Kennwort ändern Seite-->
<h1 class="ueberschrift">Kennwort ändern</h1>
<form method="post">
<!-- Neues Kennwort Feld-->
    <input class="box" placeholder="Neues Kennwort" type="password" name="newpass"/><br /><br />
    <!-- Neues Kennwort wiederholen Felde-->
    <input class="box" placeholder="Neues Kennwort wiederholen" type="password" name="newpassa"/><br /><br />
    <!-- Abbrechen Knopf-->
    <input class="chpw" type="submit" name="abbrechen" value="Abbrechen" />
    <!-- Speichern Knopf-->
    <input class="chpw" type="submit" name="speichern" value="Speichern" />
    </form>
</section>
</body>
</html>