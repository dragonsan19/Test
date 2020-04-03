<?php
//Startet Session
session_start();
//Wenn der Ok Knopf auf der Pause popup gedrückt wird
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
//Wenn der Zeit eintragen Knopf gedrückt wird
if(isset($_POST['zeit'])){
  //Wechselt zur startseite.php
  header('Location: startseite.php');
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
    /*Formatiert die Auswertung*/
    .box{
      margin-left: 120px;
      font-size: 20px;
      margin-top: -140px;
    }
    /*Formatiert die Postition der Namen*/
    .einzug2{
      margin-left: 35px;
      margin-right: 8px;
    }
    /*Formatiert die Postion des Kennwort ändern und Abmelden Hyperlinks*/
    .einzug3{
      margin-right: 5px;
    }
    /*Formatiert die Seiten Knöpfe*/
      .bleft{
      margin-left: -140px;
      margin-top: 40px;
      font-size: 20px;
    }
    /*Formatiert den Popup der Pause*/
    .popup{
      background: #A9F5A9;
      position: absolute;
      font-weight: bold;
      border: solid;
      padding: 5px;
      width: 200px;
      margin-left: 30px;
      margin-top: -50px;
      font-size: 20px;
      visibility: hidden;
    }
    /*Formatiert den Knopf des Popups*/
    .moveB{
      margin-left: 160px;
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
//$vorname wird zum in $_SESSION gespeicherten Vornamen
    $vorname = $_SESSION['vorname'];
    //$nachname wird zum in $_SESSION gespeicherten Nachnamen
    $nachname = $_SESSION['nachname'];
    //Datenbank Anbindung
    $link = mysqli_connect("127.0.0.1", "root", "", "zeiterfassung");
    //Query
    $stmt = $link->prepare("SELECT * FROM mitarbeiterstatistik WHERE mitarbeiterid = ?");
    //Ersetzt die Fragezeichen mit den benötigten Werten
    $stmt->bind_param('i', $_SESSION['user_id']);
    //Führt die Query aus
    $stmt->execute();
    //Das Ergebnis vom $stmt wir in $result gespeichert
    $result = $stmt->get_result();
    //In $statistik stehen die Daten auf die man zugreifen kann
    $statistik = $result->fetch_object();
    //user_id wird in $id gespeichert
    $id = $_SESSION['user_id'];

    //Query
    $query = "SELECT * FROM eintrag WHERE mitarbeiterid = $id";
    //$iststunden werden erstellt
    $iststunden = 0;
    //Abfrage ob $result eine Rückgabe hat
    if($result = $link->query($query)){
      //Berechnung der iststunden
      while($row = $result->fetch_array()){
        $woche = new DateTime($row['tag']);
        $heute = new DateTime();
        if($woche->format("W") == $heute->format("W")){
          $start = new DateTime($row['start']);
          $ende = new DateTime($row['ende']);

          //Halbe Stunde wird von der ist Stunde abgezogen
          $ende->sub(new DateInterval('PT30M'));
          //Ist Stunden werden berechnet
          $seconds = abs($ende->getTimestamp()-$start->getTimestamp());
          //$ergebnis = $ende->diff($start);
          //$iststunden = $iststunden + $ergebnis->format('%h');
          $iststunden += $seconds;
        }
      }
      $stunden = gmdate("H", $iststunden);
      $minuten = gmdate("i", $iststunden);

      $iststunden = number_format($stunden+$minuten/60, 2);

      //$iststunden = ;
    }
    
    
    //Minus Plus Stunden werden ausgerechnet
    $plusminus = number_format($iststunden - $statistik->sollstunden, 2);
    
    //Schließt Datenbank Anbindung
    mysqli_close($link);

    //Auswertung wird in Variablen gespeichert
    $sollst = $statistik->sollstunden;
    $urlaub = $statistik->urlaubssoll;
    $krankenstand = $statistik->krankenstand;

    echo "<section class='center'>";
    //Der Name wird angezeigt
    echo "<a class='einzug2'>".$vorname." ".$nachname." "."</a>";
    //Hyperlink zum Kennwort ändern
    echo "<a class='einzug3' href=kennwort_aendern.php>Kennwort ändern</a>"." ";
    //Hyperlink zum Abmelden
    echo "<a class='einzug3' href=logout.php>Abmelden</a><br /><br /><br />";
    echo "<form method='post'>";
    //Seiten Knöpfe
    echo "<input class='bleft' type='submit' name='zeit' value='Zeit eintragen'><br /><br />";
    echo "<input class='bleft' type='submit' name='daten' value='Daten auswerten'>";
    //Popup für Pause Errinnerung
    echo "<div id='popup' class='popup'>";
    echo "<a>Nach 6 Stunden muss man eine Pause machen (Arbeitnehmerschutzgesetz)</a><br />";
    echo "<input class='moveB' type='submit' name='pause_ok' value='Ok'>";
    echo "</div>";
    echo "</form>";
    //Statistik
    echo "<div class='box'>";
    echo "<a>Soll-Stunden</a> ".$sollst."<br />";
    echo "<a>Ist-Stunden</a> ".$iststunden."<br />";
    echo "<a>Plus/Minus-Stunden </a>".$plusminus."<br />";
    echo "<a>Resturlaubstage</a> ". $urlaub ."<br />";
    echo "<a>Krankenstandtage</a> ". $krankenstand ."<br />";
    echo "</div>";
    echo "</section>";
?>
</body>
