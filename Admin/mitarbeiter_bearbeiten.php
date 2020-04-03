<?php
//Startet Session
  session_start();
  echo "<section class='center'>";
  //Mitarbeiter Anlegen Knopf
  if(isset($_POST['anlegen'])){
    //Wechselt zu startseite.php
    header('Location: startseite.php');
  }
  //Mitarbeiter Liste Knopf
  if(isset($_POST['liste'])){
    //Wechselt zu mitarbeiter_liste.php
    header('Location: mitarbeiter_liste.php');
  }
  //Änderungen Speichern Knopf
  if(isset($_POST['aendern'])){
    //Überprüfung des Vornamens
    if((preg_match('/[a-zA-Z]/', $_POST['vorname']) && !empty($_POST['vorname'])) || empty($_POST['vorname'])){
      //Überprüfung des Nachnamens
      if((preg_match('/[a-zA-Z]/', $_POST['nachname']) && !empty($_POST['nachname'])) || empty($_POST['nachname'])){
        //Datenbank Abfrage der Benutzernamen
        $link = new mysqli("127.0.0.1", "root", "", "zeiterfassung");
        $statement = $link->prepare("SELECT * FROM mitarbeiter WHERE id = ?");
        $statement->bind_param('s', $_SESSION['user_id']);
        $statement->execute();
        $ergebnis = $statement->get_result(); //Korrigieren
        $vorhanden = $ergebnis->fetch_object();
        $password = "";
        //Passwort Überprüfung
        if(!empty($_POST['password'])){
          $password = md5($_POST['password'].$vorhanden->benutzername);
        }
        //Abfrage ob der Benutzername bereits existiert
        if($vorhanden->benutzername != $_POST['username'] || empty($_POST['username'])){
          $stmt = $link->prepare("SELECT * FROM mitarbeiter WHERE id = ?");
          $stmt->bind_param('i', $_SESSION['user_id']);
          $stmt->execute();
          $result = $stmt->get_result(); //Korrigieren
          $user = $result->fetch_object();

          $array = array($user->vorname, $user->nachname, $user->benutzername, $user->kennwort);
          $array2 = array($_POST['vorname'], $_POST['nachname'], $_POST['username'], $password);
          
          //Array wird mit Daten gefühlt
          for($i = 0; $i<count($array); $i++){
            if($array2[$i] == ""){

            }
            else{
              $array[$i] = $array2[$i];
            }
          }
          //Bearbeitet Mitarbeiter
          $link = new mysqli("127.0.0.1", "root", "", "zeiterfassung");
          $stmt2 = $link->prepare("UPDATE mitarbeiter SET vorname = ?, nachname = ?, benutzername = ?, kennwort = ? WHERE id = ?");
          $stmt2->bind_param('ssssi', $array[0], $array[1], $array[2], $array[3], $_SESSION['user_id']);
          $stmt2->execute();
          //Wechselt zu mitarbeiter_liste.php
          header('Location: mitarbeiter_liste.php');
        }
        else{
          $fehler = "Benutzername bereits verwendet";
          fehlermf($fehler);
        }
      }
      else{
        $fehler = "Nur Buchstaben bei Nachname verwenden";
        fehlermf($fehler);
      }
    }
    else{
      $fehler = "Nur Buchstaben bei Vorname verwenden";
      fehlermf($fehler);
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
      height: 400px;
    }
    /*Formatiert den Speichern Knopf*/
	.einzug{
      font-size: 20px;
      margin-left: 15px;
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
    /*Formatiert die Eingabe Felder*/
	.box{
      font-size: 20px;
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
<?php
  $admin = $_SESSION['admin_benutzername'];
  //Anzeige vom Benutzername 
    echo "<a class='einzug2'>".$admin." "."</a>";
    //Hyperlinks Kennwort Ändern, Abmelden
    echo "<a class='einzug3' href=kennwort_aendern.php>Kennwort ändern</a>"." ";
    echo "<a class='einzug3' href=logout.php>Abmelden</a><br /><br /><br />";
  echo "<form method='post'>";
  //Seiten Knöpfe
    echo "<input class='bleft' type='submit' name='anlegen' value='Mitarbeiter anlegen'><br /><br />";
    echo "<input class='bleft' type='submit' name='liste' value='Mitarbeiter Liste'>";
    echo "</form>";
    //Eingabefelder für den Mitarbeiter
	echo "<form class='iverschieben' method='post'>";
    echo "<input class='box' placeholder='Vorname' type='text' name='vorname'>"."<br /><br />";
    echo "<input class='box' placeholder='Nachname' type='text' name='nachname'>"."<br /><br />";
    echo "<input class='box' placeholder='Benutzername' type='text' name='username'/><br /><br />";
    echo "<input class='box' placeholder='Kennwort' type='password' name='password'/><br /><br />";
    echo "<input class='einzug' name='aendern' type='submit' value='Änderungen Speichern' />";
    echo "</form>";
	echo "</section>";
?>
</body>
</html>