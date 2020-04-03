<?php
//Startet Session
    session_start();
    //Mitarbeiter anlegen Knopf
    if(isset($_POST['anlegen'])){
      //Wechselt zu startseite.php
        header('Location: startseite.php');
    }
    //Mitarbeiter List Knopf
    if(isset($_POST['liste'])){
      //Wechselt zu mitarbeiter_liste.php
        header('Location: mitarbeiter_liste.php');
    }
?>
<!DOCTYPE html>
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
    /*Formatiert die ausgegebenen Daten*/
    .box{
      margin-left: 120px;
      font-size: 20px;
      margin-top: -140px;
    }
    /*Formatierung des Benutzernamen*/
    .einzug2{
      margin-left: 55px;
      margin-right: 8px;
    }
    /*Formatierung der Hyperlinks zum Kennwort ändern und Abmeldens*/
    .einzug3{
      margin-right: 5px;
    }
    /*Formatiert den Vorname und Nachnamen*/
    .einzug4{
        margin-left: 55px;
        font-size: 20px;
    }
    /*Seiten Knöpfe*/
      .bleft{
      margin-left: -140px;
      margin-top: 40px;
      font-size: 20px;
    }
  </style>
</head>
<body>
<?php
//Datenbank Ausgabe Vorname und Nachname des Mitarbeiters
    $link = new mysqli("127.0.0.1", "root", "", "zeiterfassung");
    $stmt = $link->prepare("SELECT vorname, nachname FROM mitarbeiter WHERE id = ?");
    $stmt->bind_param('i', $_SESSION['user_id_a']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_object();

    $vorname = $user->vorname;
    $nachname = $user->nachname;

    //Datenbank Ausgabe der mitarbeiterstatistik
    $stmt2 = $link->prepare("SELECT * FROM mitarbeiterstatistik WHERE mitarbeiterid = ?");
    $stmt2->bind_param('i', $_SESSION['user_id_a']);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $statistik = $result2->fetch_object();

    $id = $_SESSION['user_id_a'];

    //Datenbank Ausgabe der Einträge des Mitarbeiters
    $query = "SELECT * FROM eintrag WHERE mitarbeiterid = $id";
    $iststunden = 0;
    //Berechnung der ist Stunden
    if($result = $link->query($query)){
      
      while($row = $result->fetch_array()){
        $woche = new DateTime($row['tag']);
        $heute = new DateTime();
        if($woche->format("W") == $heute->format("W")){
          $start = new DateTime($row['start']);
          $ende = new DateTime($row['ende']);
          
          $ende->sub(new DateInterval('PT30M'));
          $seconds = abs($ende->getTimestamp()-$start->getTimestamp());
          //$ergebnis = $start->diff($ende);
          //$iststunden = $iststunden + $ergebnis->format('%h');
          $iststunden += $seconds;
        }
      }
      $stunden = gmdate("H", $iststunden);
      $minuten = gmdate("i", $iststunden);

      $iststunden = number_format($stunden+$minuten/60, 2);
    }    

    $plusminus = number_format($iststunden - $statistik->sollstunden, 2);
    
    mysqli_close($link);

    $sollst = $statistik->sollstunden;
    $urlaub = $statistik->urlaubssoll;
    $krankenstand = $statistik->krankenstand;

    $admin = $_SESSION['admin_benutzername'];
    echo "<section class='center'>";
    //Benutzername
    echo "<a class='einzug2'>".$admin." "."</a>";
    //Hyperlinks Kennwort ädern, Abmelden
    echo "<a class='einzug3' href=kennwort_aendern.php>Kennwort ändern</a>"." ";
    echo "<a class='einzug3' href=logout.php>Abmelden</a><br /><br /><br />";
    //Vorname Nachname
    echo "<a class='einzug4'>".$vorname." ".$nachname."</a>";
    echo "<form method='post'>";
    //Seiten Knöpfe
    echo "<input class='bleft' type='submit' name='anlegen' value='Mitarbeiter anlegen'><br /><br />";
    echo "<input class='bleft' type='submit' name='liste' value='Mitarbeiter Liste'>";
    echo "</form>";
    echo "<br />";
    echo "<div class='box'>";
    //Statistik Ausgabe
    echo "<a>Soll-Stunden</a> ".$sollst."<br />";
    echo "<a>Ist-Stunden</a> ".$iststunden."<br />";
    echo "<a>Plus/Minus-Stunden </a>".$plusminus."<br />";
    echo "<a>Resturlaubstage</a> ". $urlaub ."<br />";
    echo "<a>Krankenstandtage</a> ". $krankenstand ."<br />";
    echo "</div>";
    echo "</section>";
?>
</body>
</html>