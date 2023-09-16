<?php
require_once("db.conn.php");

$result = mysqli_query($adController->MySQL,"SHOW TABLES ");

  while ($row = mysqli_fetch_array($result))
  {
  echo $row[0].'-- ';
  $exe = mysqli_query($adController->MySQL,"ALTER TABLE `$row[0]` ENGINE = MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_bin");
  if (!$exe) 
      echo mysqli_error($adController->MySQL);
  
  echo '<br>';
  }
  echo '----------------------------';
    $currentDomain = $_SERVER['SERVER_NAME'];
    echo $currentDomain; 
    echo '<br>';
    $url= $_SERVER['HTTP_HOST'];   
        echo $url.'<br>';
    // Append the requested resource location to the URL   
    $url= $_SERVER['REQUEST_URI'];       
    echo $url.'<br>';
?>