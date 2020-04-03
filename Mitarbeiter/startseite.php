<?php
  //Startet Session
  session_start();
  //Erstellt HTML Section welches das Programm zentriert
  echo "<section class='center'>";
  //Wenn der Knopf Ok beim Popup gedrückt wird, wird dieses hier ausgeführt
  if(isset($_POST['pause_ok'])){
    //Jetzige Zeit
    $pause = date('H:i');
    //Heutiger Tag
    $tag = date("Y/m/d");
    //Datenbank Anbindung
    $link = new mysqli("127.0.0.1", "root", "", "zeiterfassung");
    //Verändere den Eintrag Pause in der Datenbank
    $stmt = $link->prepare("UPDATE eintrag SET pause = ? WHERE mitarbeiterid = ? AND tag = ?");
    //Setzt das Fragezeichen in der Query zum Wert aus dem Benutzernamen Feld
    $stmt->bind_param('sis', $pause, $_SESSION['user_id'], $tag);
    //Führt die Query aus
    $stmt->execute();
  }
  //Wechselt die Seite wenn auf Daten auswerten gedrückt wird
  if(isset($_POST['daten'])){
    header('Location: daten_auswerten.php');
  }
  //Wenn Speichern gedrückt wird, speichert es einen Eintrag in der Datenbank
  if(isset($_SESSION['user_id']) && isset($_POST['speichern'])){
    //Abfrage ob Felder leer sind
    if(!empty($_POST['start']) && !empty($_POST['ende']) && !empty($_POST['taetigkeit']) && preg_match('/^[a-zA-Z]/', $_POST['taetigkeit'])){
      if($_POST['start'] < $_POST['ende'] && $_POST['start'] != $_POST['ende']){
        $start = new DateTime($_POST['start']);
        $ende = new DateTime($_POST['ende']);

        if($start->add(new DateInterval('PT30M')) <= $ende){
          //Heutiger Tag
          $tag = date("Y/m/d");
          //Datenbank Anbindung
          $link = new mysqli("127.0.0.1", "root", "", "zeiterfassung");
          //Hiermit kann man Umlaute in die Datenbank speichern
          mysqli_query($link, "SET NAMES utf8");
          //INSERT INTO Query
          $stmt = $link->prepare("INSERT INTO eintrag(start, ende, projektnr, taetigkeit, typ, mitarbeiterid, tag)
          VALUES (?, ? ,? ,?, ?, ?, ?)");
          //Ersetzt die Fragezeichen mit den benötigten Werten
          $stmt->bind_param('ssissis', $_POST['start'], $_POST['ende'], $_POST['projektnr'], $_POST['taetigkeit'], $_POST['typ'], $_SESSION['user_id'], $tag);
          //Führt die Query aus
          $stmt->execute();
          //Schließt die Datenbank Anbindung
          mysqli_close($link);
          //Wechselt auf die startseite.php
          header('Location: startseite.php');
        }
        //Fehlermeldung
        else{
          $fehler = "Bitte geben Sie eine Start Zeit ein, welche nicht nach dem eine halbe Stunde von der End Zeit abgezogen wurde, größer als die End Zeit ist!";
          fehlermf($fehler);
        }
      }
      //Fehlermeldung
      else{
        $fehler = "Die Start Zeit darf nicht größer als die End Zeit sein und die beiden Zahl dürfen nicht die selben Zahlen sein.";
        fehlermf($fehler);
      }
    }
    //Fehlermeldung
    else{
      $fehler = "Daten fehlen";
      fehlermf($fehler);
      
    }
  }
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
      padding-left: 150px;
      padding-top: 30px;
      padding-bottom: 30px;
      margin: auto;
      width: 400px;
      height: 400px;
    }
    /*Formatierung des Zeitfeldes Start*/
    .zeit{
      margin-left: 100px;
      font-size: 20px;
    }
    /*Formatierung des Zeitfeldes Ende*/
    .zeit2{
      margin-left: 20px;
      font-size: 20px;
    }
    /*Formatierung des Namens des Dropdown Menu*/
    .dropdown{
      font-size: 20px;
      margin-left: 100px;
    }
    /*Formatierung des eigentlichen Dropdown Menus*/
    .dropdown2{
      font-size: 20px;
      margin-left: 20px;
    }
    /*Formatierung des Tätigkeitsfeldes*/
    .taetigkeit{
      margin-left: 100px;
      width:246px;
      height: 150px;
    }
    /*Formatierung des Speichern Knopfes*/
    .einzug{
      font-size: 20px;
      margin-left: 180px;
    }
    /*Formatierung des Namens des Users*/
    .einzug2{
      margin-left: 35px;
      margin-right: 8px;
    }
    /*Formatierung der Hyperlinks zum Kennwort ändern und Abmeldens*/
    .einzug3{
      margin-right: 5px;
    }
    /*Formatiert die Seitenknöpfe*/
    .bleft{
      margin-left: -140px;
      margin-top: 40px;
      font-size: 20px;
    }
    /*Bewegt den Inhalt des Rahmens nach oben*/
    .iup{
      margin-top: -160px;
    }
    /*Formatierung des Pausen Popups*/
    .popup{
      background: #A9F5A9;
      position: absolute;
      font-weight: bold;
      border: solid;
      padding: 5px;
      width: 200px;
      margin-left: 30px;
      margin-top: -280px;
      font-size: 20px;
      visibility: hidden;
    }
    /*Knopf der Fehlermeldung*/
    .moveB{
      margin-left: 160px;
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
      margin-top: 150px;
      font-size: 20px;
      visibility: hidden;
    }
  </style>
</head>
<body>
<script src="jquery-3.4.1.min.js"></script>
<script type="text/javascript">
//Variable time wird zu der Loginzeit
var time = "<?php echo $_SESSION['time'] ?>";
  //Zum überprüfen ob 6 Stunden vergangen sind
  setTimeout(function pause(){
    $.ajax({
    type: "Post",
    data:{"time": time},
    url: "http://localhost/zeiterfassung/mitarbeiter/pause_check.php",
    success: function(data) {
      //Wenn 6 Stunden vergangen sind kommt ein popup
        if(data == "1"){
          document.getElementById("popup").style.visibility = "visible";
        }
        //Wenn nicht 6 Stunden vergangen sind wird es in einer Minute wiederholt
        else{
          setTimeout(pause(), 60000);
        }
    }
  });
  }, 60000);
</script>
<?php
//Abfrage ob es die user_id gibt
  if(isset($_SESSION['user_id'])){
    //$vorname wird zum in $_SESSION gespeicherten Vornamen
    $vorname = $_SESSION['vorname'];
    //$nachname wird zum in $_SESSION gespeicherten Nachnamen
    $nachname = $_SESSION['nachname'];

    //Datenbank Anbindung
    $link = mysqli_connect("127.0.0.1", "root", "", "zeiterfassung");
    //Query um die Projektnummern zu bekommen
    $query = "SELECT projektnr FROM projekte";
    //Der Name wird angezeigt
    echo "<a class='einzug2'>".$vorname." ".$nachname." "."</a>";
    //Hyperlink zum Kennwort ändern
    echo "<a class='einzug3' href=kennwort_aendern.php>Kennwort ändern</a>"." ";
    //Hyperlink zum abmelden
    echo "<a class='einzug3' href=logout.php>Abmelden</a><br /><br /><br />";
    echo "<form method='post'>";
    //Zeit eintragen Knopf, wechselt die Seite um die Zeit einzutragen
    echo "<input class='bleft' type='submit' name='zeit' value='Zeit eintragen'><br /><br />";
    //Daten auswerten Knopf, wechselt die Seite um Daten auszugeben
    echo "<input class='bleft' type='submit' name='daten' value='Daten auswerten'>";
    echo "</form>";
    echo "<form class='iup' action='startseite.php' method='post'>";
    //Startzeit und Endzeit eingabe Felder
    echo "<a class='zeit'>Start</a>"." <input type='time' name='start'>"." <a class='zeit2'>Ende</a>"." <input type='time' name='ende'>"."<br /><br />";
    //Typ Dropdown Menu
    echo "<a class='dropdown'>Typ </a>"."<select name='typ' class='dropdown2'>";
    //Typ Dropdown Optionen
    echo "<option>Tätigkeit</option>";
    echo "</select>"."<br /><br />";
    //Projektnummer Dropdown Menu
    echo "<a class='dropdown'>Projektnummer </a>"."<select name='projektnr' class='dropdown2'>";
    //Projektnummer Dropdown Optionen, bekommt man aus der Datenbank
    foreach ($link->query($query) as $row) {
        echo "<option>" . $row['projektnr'] . "</option>";
    }
    echo "</select>";
    //Tätigkeitseingabefeld
    echo "<br /><br /><textarea maxlength='255' name='taetigkeit' class='taetigkeit' placeholder='Tätigkeit'></textarea><br /><br />";
    //Speichern Knopf
    echo "<input class='einzug' type='submit' name='speichern' value='Speichern' />";
    //Pause Popup, nach 6 Stunden soll dieses Feld angezeigt werden
    echo "<div id='popup' class='popup'>";
    echo "<a>Nach 6 Stunden muss man eine Pause machen (Arbeitnehmerschutzgesetz)</a><br />";
    echo "<input class='moveB' type='submit' name='pause_ok' value='Ok'>";
    echo "</div>";
    echo "</section>";
    echo "</form>";
    //Schließt die Datenbank Anbindung
    mysqli_close($link);
  }
  else{
    //Wechselt zur login.php Seite
    header('Location: login.php');
  }
?>
</body>
</html>