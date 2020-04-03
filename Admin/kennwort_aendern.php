<?php 
    //Startet Session
    session_start();   
    echo "<section class='center'>";
    //Abbrechen Knopf
    if (!empty($_POST['abbrechen'])) {
      //Wechselt zu startseite.php
      header('Location: startseite.php');
    }
    //Speichern Knopf
    else if(!empty($_POST['speichern'])){
      //Überprüfung ob admin_id Daten hat
      if(isset($_SESSION['admin_id'])){
        //Überprüfung ob Felder leer sind
        if((!empty($_POST['newpass']) && !empty($_POST['newpassa'])) && $_POST['newpass'] == $_POST['newpassa']){
          $admin = $_SESSION['admin_benutzername'];
          //Verschlüsselt Passwort
          $password = md5($_POST['newpass'].$admin);
          //Datenbank Anbindung mit Query
          $link = new mysqli("127.0.0.1", "root", "", "zeiterfassung");
          $stmt = $link->prepare("UPDATE admin SET kennwort = ? WHERE benutzername = ?");
          $stmt->bind_param('ss', $password, $_SESSION['admin_benutzername']);
          $stmt->execute();
          mysqli_close($link);
          //Wechselt zu startseite.php
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
      background: #819FF7;
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