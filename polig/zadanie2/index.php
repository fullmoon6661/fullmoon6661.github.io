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

  $defaultPostID = '';
  $defaultComment = '';
  $defaultCommentAuthor = '';

  $isInTopic = isset($_GET["topic"]);
  if ($isInTopic) {

    // Handle form post
    if (isset($_POST['post']) && isset($_POST['username'])) {

      $newComment = array(
        'comment' => $_POST['post'],
        'author' => $_POST['username'],
        'createdAt' => date('Y-m-d H:i:s'),
      );

      // If postid is not empty, means its editing a comment
      if ($_POST['postid'] !== '') {

        $data[$_GET["topic"]]['comments'][$_POST['postid']] = $newComment;

      } else {

        $data[$_GET["topic"]]['comments'][] = $newComment;
      }      

      $fileParser = fopen('data.txt', 'w');
      fwrite($fileParser, json_encode($data));
      fclose($fileParser);
    }

    // Handle comments edit/delete command by checking the existence of "id" field
    if (isset($_GET['id'])) {
      
      if ($_GET['cmd'] == 'edit') {

        $defaultPostID = $_GET['id'];
        $defaultComment = $data[$_GET["topic"]]["comments"][$_GET['id']]["comment"];
        $defaultCommentAuthor = $data[$_GET["topic"]]["comments"][$_GET['id']]["author"];

      } elseif ($_GET['cmd'] == 'delete') {
        
        // Remove specific item from comment inside topic
        unset($data[$_GET["topic"]]["comments"][$_GET['id']]);

      } else {
        # Pass, no valid "cmd"
      }

      $fileParser = fopen('data.txt', 'w');
      fwrite($fileParser, json_encode($data));
      fclose($fileParser);
    }

  } else {

    if (isset($_POST['topic']) && isset($_POST['topic_body']) && isset($_POST['username'])) {

      $newTopic = array(
        'title' => $_POST['topic'],
        'body' => $_POST['topic_body'],
        'author' => $_POST['username'],
        'createdAt' => date('Y-m-d H:i:s'),
        'comments' => array(),
      );

      $data[] = $newTopic;

      $fileParser = fopen('data.txt', 'w');
      fwrite($fileParser, json_encode($data));
      fclose($fileParser);
    }
  }

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Demo - Zadanie 1 - WWW i jzyki skryptowe</title>
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate"/>
    <meta http-equiv="Pragma" content="no-cache"/>
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
  <body>
    <header>
        <h1>Demo - Zadanie 2</h1>
        <h2>Proste forum</h2>
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
      <?php
        if($isInTopic) {
          
          $topicID = $_GET["topic"];
          $topic = $data[$topicID];

          echo "
            <nav>
              <table>
                <tr>";

          $prevTopic = $topicID - 1;
          if ($prevTopic >= 0) {
            echo "
              <td>
                <a style=\"float:left;\" href=\"index.php?topic=$prevTopic\"><-- Poprzedni temat</a>
              </td>
            ";
          } else {
            echo "<td></td>";
          }

          echo "
            <td  style=\"width: 33%;\">
              <a href=\"index.php\">Lista tematów</a>
            </td>
          ";

          $nextTopic = $topicID + 1;
          if ($nextTopic <= (count($data) - 1)) {
            echo "
              <td  style=\"width: 33%;\">  
                <a  style=\"float:right;\" href=\"index.php?topic=$nextTopic\">Następny temat --></a>
              </td>
            ";
          } else {
            echo "<td></td>";
          }

          echo "</tr>
              </table>
            </nav>
          ";

          echo "
            <article class=\"topic\">
              <header>
                Temat dyskusji: <b>{$topic['title']}</b>
              </header>
              <div>{$topic['body']}</div>
              <footer>ID: $topicID, Autor: {$topic['author']}, Data: {$topic['createdAt']}    </footer>
            </article>";

          echo "
            <p>Możesz dodać nową wypowiedź za pomocą <a href=\"#post_form\">formularza</a></p>
          ";

          if (count($data[$topicID]["comments"]) > 0) {
            foreach ($data[$topicID]["comments"] as $key => $value) {
              echo "
                <article>
                  <div>{$value['comment']}</div>
                  <footer>
                    <nav>
                      <a href=\"?topic=$topicID&id=$key&cmd=edit\">EDYTUJ</a>  
                      <a class=\"danger\" href=\"?topic=$topicID&id=$key&cmd=delete\">KASUJ</a>
                    </nav> 
                    ID: $topicID, Autor: {$value['author']}, Utworzono dnia: {$value['createdAt']}
                  </footer>
                </article>
              ";
            }            
          } else {
            echo "<p>To forum nie zawiera jeszcze żadnych głosów w dyskusji!</p>";
          }

          echo "
            <form action=\"index.php?topic=$topicID\" method=\"post\">
                <a name=\"post_form\"></a>
                <header>
                    <h2>Dodaj nowa wypowiedź do dyskusji</h2>
                </header>
                <textarea name=\"post\" autofocus cols=\"80\" rows=\"10\" placeholder=\"Wpisz tu swoją wypowiedź.\">$defaultComment</textarea>
                <br/>
                <input type=\"text\" name=\"username\" placeholder=\"Imię autora\" value=\"$defaultCommentAuthor\" \>
                <br/>
                <input type=\"hidden\" name=\"postid\" value=\"$defaultPostID\"/>
                <button type=\"submit\">Zapisz</button>
            </form>
          ";

        } else {
          
          echo ("
              <p>
                Możesz dodac nowy temart za pomocą <a href='#topic_form'>formularza</a>
              </p>"
            );

          // Topic list
          foreach ($data as $key => $value) {
            
            echo ("
              <article class=\"topic\">
                <header></header>
                <div>
                    <a href=\"?topic=$key\">{$value["title"]}</a>
                </div>
                <footer>ID: $key, Autor: {$value["author"]}, Utworzono: {$value["createdAt"]}, Liczba wpisów: ".count($value["comments"])."</footer>
              </article>
            ");
          }

          // Topic form
          echo ("
            <form action=\"index.php\" method=\"post\">
              <a name=\"topic_form\"></a>
              <header>
                  <h2>Dodaj nowy temat do dyskusji</h2>
              </header>
              <input type=\"text\" name=\"topic\" placeholder=\"Nowy temat\" autofocus \>
              <br/>
              <textarea name=\"topic_body\" cols=\"80\" rows=\"10\" placeholder=\"Opis nowego tematu\"></textarea>
              <br/>
              <input type=\"text\" name=\"username\" placeholder=\"Imię autora\" \>
              <br/>
              <button type=\"submit\">Zapisz</button>
            </form>
          ");
        }
      ?>      
    </section>
    <footer>Ostatni wpis na formu powstał dnia: - brak postów -</footer>
  </body>
</html>
