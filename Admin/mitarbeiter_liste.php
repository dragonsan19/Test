<?php
  //Startet Session
    session_start();
    echo "<section class='center'>";
    //Mitarbeiter anlegen Knopf
    if(isset($_POST['anlegen'])){
      //Wechselt zu startseite.php
        header('Location: startseite.php');
    }
    //Mitarbeiter bearbeiten Knopf
    if(isset($_POST['aendern'])){
      if(isset($_POST['Id'])){
        $_SESSION['user_id'] = $_POST['Id'];
        //Wechselt zu mitarbeiter_bearbeiten.php
        header('Location: mitarbeiter_bearbeiten.php');
      }
      else{
        $fehler = "Bitte wählen sie einen Mitarbeiter aus";
        fehlermf($fehler);
      }
    }
    //Mitarbeiter löschen Knopf
    if(isset($_POST['loeschen'])){
      if(isset($_POST['Id'])){
        $link = new mysqli("127.0.0.1", "root", "", "zeiterfassung");
        $stmt = $link->prepare("DELETE FROM mitarbeiter WHERE id = ?");
        $stmt->bind_param('i', $_POST['Id']);
        $stmt->execute();
      }
      else{
        $fehler = "Bitte wählen sie einen Mitarbeiter aus";
        fehlermf($fehler);
      }
    }
    //Mitarbeiter Statistik Knopf
    if(isset($_POST['statistik'])){
      if(isset($_POST['Id'])){
        $_SESSION['user_id_a'] = $_POST['Id'];
        //Wechselt zu mitarbeiter_statistik.php
        header('Location: mitarbeiter_statistik.php');
      }
      //Fehlermeldung
      else{
        $fehler = "Bitte wählen sie einen Mitarbeiter aus";
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
    /*Bearbeiten & Löschen Knopf*/
    .bleft2{
      margin-left: 20px;
    }
    /*Mitarbeiter Statistik Knopf*/
    .bleft3{
      margin-left: 40px;
    }
    /*Mitarbeiter Liste Position*/
    .iverschieben{
      margin-top: -140px;
      margin-left: 100px;
    }
    /*Mitarbeiter Liste*/
    .text{
     border: solid;
     margin: auto;
     width: 260px;
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

    //Datenbank Abfrage
    $link = mysqli_connect("127.0.0.1", "root", "", "zeiterfassung");
    $query = "SELECT * FROM mitarbeiter";
    
    //Anzeige Benutzername
    echo "<a class='einzug2'>".$admin." "."</a>";
    //Hyperlinks Kennwort ändern & Abmelden
    echo "<a class='einzug3' href=kennwort_aendern.php>Kennwort ändern</a>"." ";
    echo "<a class='einzug3' href=logout.php>Abmelden</a><br /><br /><br />";
    echo "<form method='post'>";
    //Seiten Knöpfe
    echo "<input class='bleft' type='submit' name='anlegen' value='Mitarbeiter anlegen'><br /><br />";
    echo "<input class='bleft' type='submit' name='liste' value='Mitarbeiter Liste'>";
    echo "</form>";
    echo "<form class='iverschieben' method='post'>";
    echo "<div class='text'>";
    echo "<form method='post'>";
    foreach ($link->query($query) as $row) {
        ?>
        <!-- Erstelle Mitarbeiter Liste-->
        <input type ='radio' name ='Id' value='<?php echo $row['id']; ?>'>
        <?php echo $row['vorname']." ".$row['nachname']." ".$row['benutzername']; ?> 
        <br />
        <?php
    }
    echo "<br />";
    //Knöpfe
    echo "<input class='bleft2' type='submit' value='Bearbeiten' name='aendern'/>";
    echo "<input class='bleft2' type='submit' value='Löschen' name='loeschen'/> <br>";
    echo "<input class='bleft3' type='submit' value='Mitarbeiter Statistik' name='statistik'/> <br>";
    echo "</form>";

    echo "</div>";
    echo "</form>";
    echo "</section>";
  ?>
</body>
  </html>
