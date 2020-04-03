<?php
//Startet Session
  session_start();
  echo "<section class='center'>";
  //Mitarbeiter Knöpfe Liste
  if(isset($_POST['liste'])){
      header('Location: mitarbeiter_liste.php');
  }
  //Abfrage ob admin_id vorhanden ist
  if(isset($_SESSION['admin_id'])){
    //Speichern Knopf
    if(isset($_POST['speichern'])){
      //Überprüfung ob die Felder leer sind
      if(!empty($_POST['vorname']) && !empty($_POST['nachname']) && !empty($_POST['username']) && !empty($_POST['password'])){
        //Abfrage ob Vorname und Nachname nur Buchstaben beinhaltet
        if(preg_match('/[a-zA-Z]/', $_POST['vorname']) && preg_match('/[a-zA-Z]/', $_POST['nachname']) && preg_match('/[a-zA-Z]/', $_POST['username'])){
          //Datenbank Abfrage der Benutzernamen
          $link = new mysqli("127.0.0.1", "root", "", "zeiterfassung");
          $statement = $link->prepare("SELECT * FROM mitarbeiter WHERE benutzername = ?");
          $statement->bind_param('s', $_POST['username']);
          $statement->execute();
          $ergebnis = $statement->get_result(); //Korrigieren
          $vorhanden = $ergebnis->fetch_object();
          //Abfrage ob der Benutzername bereits existiert
          if($vorhanden->benutzername != $_POST['username']){
            //Passwort Verschlüsselung
            $password = md5($_POST['password'].$_POST['username']);
            
            //Ermöglicht Umlaute in die Datenbank zu speichern
            mysqli_query($link, "SET NAMES utf8");
            //Datenbank eingabe des Mitarbeiters
            $stmt = $link->prepare("INSERT INTO mitarbeiter(vorname, nachname, benutzername, kennwort)
            VALUES (?, ? ,? ,?)");
            $stmt->bind_param('ssss', $_POST['vorname'], $_POST['nachname'], $_POST['username'], $password);
            $stmt->execute();

            //Gibt die ID des Mitarbeiters wieder
            $stmt2 = $link->prepare("SELECT id FROM mitarbeiter ORDER BY id DESC");
            $stmt2->execute();
            $result = $stmt2->get_result(); //Korrigieren
            $user = $result->fetch_object();

            //Datenbank eingabe in Mitarbeiterstatistik 
            $stmt3 = $link->prepare("INSERT INTO mitarbeiterstatistik(sollstunden, urlaubssoll, mitarbeiterid, krankenstand)
            VALUES (?, ?, ?, ?)");
            $stmt3->bind_param('iiii', $_POST['sollstunden'], $_POST['urlaubssoll'], $user->id, $_POST['krankenstand']);
            $stmt3->execute();
          
            //Wechselt zu startseite.php
            header('Location: startseite.php');
          }
          else{
            $fehler = "Benutzername bereits verwendet";
            fehlermf($fehler);
          }
        }
        else{
          $fehler = "Nur Buchstaben bei Vorname, Nachname und Benutzernamen verwenden";
          fehlermf($fehler);
        }
      }
      else{
        $fehler = "Daten fehlen";
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
      padding-left: 150px;
      padding-top: 30px;
      padding-bottom: 30px;
      margin: auto;
      width: 400px;
      height: 450px;
    }
    /*Formatiert die Eingabe Felder*/
    .box{
      font-size: 20px;
    }
    /*Formatiert den Speichern Knopf*/
    .einzug{
      font-size: 20px;
      margin-left: 70px;
    }
    /*Formatierung des Benutzernamen*/
    .einzug2{
      margin-left: 115px;
      margin-right: 8px;
    }
    /*Formatierung der Hyperlinks zum Kennwort ändern und Abmeldens*/
    .einzug3{
      margin-right: 5px;
    }
    /*Seiten Knöpfe*/
    .bleft{
      margin-left: -140px;
      margin-top: 40px;
      font-size: 20px;
    }
    /*Formatiert den Inhalt des Programms*/
    .iverschieben{
      margin-top: -140px;
      margin-left: 100px;
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
<script>
</script>
<?php
//Abfrage ob die admin_id vorhanden ist
  if(isset($_SESSION['admin_id'])){
    $admin = $_SESSION['admin_benutzername'];
  
    //Zeigt den Benutzernamen an
    echo "<a class='einzug2'>".$admin." "."</a>";
    //Hyperlinks von Kennwort Ändern und Abmelden
    echo "<a class='einzug3' href=kennwort_aendern.php>Kennwort ändern</a>"." ";
    echo "<a class='einzug3' href=logout.php>Abmelden</a><br /><br /><br />";
    echo "<form method='post'>";
    //Seiten Knöpfe
    echo "<input class='bleft' type='submit' name='anlegen' value='Mitarbeiter anlegen'><br /><br />";
    echo "<input class='bleft' type='submit' name='liste' value='Mitarbeiter Liste'>";
    echo "</form>";
    //Eingabefelder
    echo "<form class='iverschieben' action='startseite.php' method='post'>";
    echo "<input class='box' placeholder='Vorname' type='text' name='vorname'>"."<br /><br />";
    echo "<input class='box' placeholder='Nachname' type='text' name='nachname'>"."<br /><br />";
    echo "<input class='box' placeholder='Benutzername' type='text' name='username'/><br /><br />";
    echo "<input class='box' placeholder='Kennwort' type='password' name='password'/><br /><br />";
    echo "<input class='box' placeholder='Sollstunden' type='number' name='sollstunden'/><br /><br />";
    echo "<input class='box' placeholder='Urlaubstage' type='number' name='urlaubssoll'/><br /><br />";
    echo "<input class='box' placeholder='Krankenstände' type='number' name='krankenstand'/><br /><br />";
    //Speichern Knopf
    echo "<input class='einzug' type='submit' name='speichern' value='Speichern' />";
    echo "</form>";
  }
  else{
    //Wechselt zur login.php Seite
    header('Location: login.php');
  }
?>
</body>
</html>


