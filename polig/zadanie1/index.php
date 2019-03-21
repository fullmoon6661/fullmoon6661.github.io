<?php

  // Get data from file, THIS FILE HAVE TO BE EXIST and NOT MALFORMED
  $raw = file_get_contents("data.txt");
  $data = json_decode($raw, true);

  if ($data === null) {
    $data = array();
    $fileParser = fopen('data.txt', 'w');
    fwrite($fileParser, json_encode($data));
    fclose($fileParser);
  }

  // If user clicked submit, $_POST will not be empty
  if (isset($_POST['title']) && isset($_POST['title'])) {
    
    // Save data to the array
    $newData = array(
      'title' => $_POST['title'], 
      'body' => $_POST['body'], 
      'date' => date('Y-m-d H:i:s'), 
      'ip' => $_SERVER['REMOTE_ADDR'], 
    );

    // Push new data to data
    $data[] = $newData;

    // And save to file
    $fileParser = fopen('data.txt', 'w');
    fwrite($fileParser, json_encode($data));
    fclose($fileParser);
  }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Demo - Zadanie 1 - WWW i jzyki skryptowe</title>
        <meta charset="utf-8">
        <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <header>
            <h1>
                Demo - Zadanie1
            </h1>
            <h2>
                Prosty blog
            </h2>
        </header>
        <nav>
                <a href="../">Home</a>
                                <a href="../zadanie1">Zadanie 1</a>
                                <a href="../zadanie2">Zadanie 2</a>
                                <a href="../zadanie3">Zadanie 3</a>
                                <a href="../zadanie4">Zadanie 4</a>
                                <a href="../zadanie5">Zadanie 5</a>
                                <a href="../zadanie6">Zadanie 6</a>
                                <a href="../zadanie7">Zadanie 7</a>
                        </nav>
<section>
<!-- posts //-->
<?php 

  // Backwards loop to show from the most recent
  $i = count($data);
  while($i > 0) {

    $index = $i - 1;

    $title = $data[$index]['title'];
    $body = $data[$index]['body'];
    $date = $data[$index]['date'];
    $ip = $data[$index]['ip'];

    echo "<article nr=\"$index\" >
      <header>
        <h2>$title
      </header>
        <div>
          $body
        </div>
      <footer><p>Data: $date; IP: $ip</p></footer>
    </article>";

    $i--;
  }

?>

</section>
<section>
  <aside>
    <?php

      $time = date('Y-m-d H:i:s');
      $postCount = count($data);

      echo "<p>Ostatni wpis: $time</p>
            <p>Liczba wpisów: $postCount</p>"
    ?>
  </aside>
  <form action="index.php" method="post">
     <header><h2>Wypełnij i zapisz</h2></header>  
     <input type="text" name="title" placeholder="Tytuł wiadomosci" autofocus \><br />
     <textarea name="body" cols="80" rows="10" placeholder="Tresć wiadomosci" ></textarea><br />
     <button type="submit" >Zapisz</button>
  </form>
</section>
        <footer>
            <p style="text-align:center;">WWW i języki skryptowe - Poznań 2018</p>
        </footer>
    </body>
</html>
