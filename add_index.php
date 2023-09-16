<?php
set_time_limit(0);
if(!file_exists("db.conn.mah.php"))
	copy("db.conn.php","db.conn.mah.php");
	
require_once("db.conn.mah.php");
echo "<pre>";

$query			= "SELECT count(id) AS cnt FROM orders_indexing";
$resOrd2		= mysqli_query($adController->MySQL,$query);
$datOrd2 		= mysqli_fetch_assoc($resOrd2);


if(intval($datOrd2['cnt']) == 0)
{
	$query 			= "SHOW INDEX FROM orders";
	$resOrd3		= mysqli_query($adController->MySQL,$query);
	$numRows		= mysqli_num_rows($resOrd3);
	
	$query 			= "SELECT COUNT(id) AS mxid FROM orders";
	$resMax			= mysqli_query($adController->MySQL,$query);
	$dataMax		= mysqli_fetch_assoc($resMax);
	$mxid			= intval($dataMax['mxid']);
	
	if($numRows < 20)
	{
		mysqli_query($adController->MySQL,"DROP TABLE orders_indexing");
		$query			= "CREATE TABLE orders_indexing LIKE orders;";
		mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));

		$query			= "SHOW COLUMNS FROM orders_indexing";
		$resOrd			= mysqli_query($adController->MySQL,$query);
		while($datOrd 	= mysqli_fetch_assoc($resOrd))
		{
			$col		= $datOrd['Field'];
			if($col 	!= "id")
			{
				$query   		= "ALTER TABLE `orders_indexing` ADD INDEX `$col` (`$col`)";
				mysqli_query($adController->MySQL,$query);
			}
		}
	
	}
	else
	{
		echo "\n\n";
		echo "<h3>SEEMS LIKE INDEXING IS DONE</h3>";
		echo "TOTAL IN orders :: ".$mxid;
		echo "\n\n";
		die;
	}
	
}


$query 			= "SELECT MAX(id) AS mxid FROM orders";
$resMax			= mysqli_query($adController->MySQL,$query);
$dataMax		= mysqli_fetch_assoc($resMax);
$mxid			= $dataMax['mxid'];

$query 			= "SELECT MAX(id) AS mxid FROM orders_indexing";
$resMax			= mysqli_query($adController->MySQL,$query);
$dataMax		= mysqli_fetch_assoc($resMax);
$mxid2			= intval($dataMax['mxid']);	


$query			= "INSERT INTO orders_indexing SELECT * FROM orders WHERE id > $mxid2 AND id <=$mxid ORDER BY id ASC ";
mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));

///////////////////////////////////////////////////

$query 			= "SELECT COUNT(id) AS mxid FROM orders_indexing";
$resMax			= mysqli_query($adController->MySQL,$query);
$dataMax		= mysqli_fetch_assoc($resMax);
$mxid2			= intval($dataMax['mxid']);

$query 			= "SELECT COUNT(id) AS mxid FROM orders";
$resMax			= mysqli_query($adController->MySQL,$query);
$dataMax		= mysqli_fetch_assoc($resMax);
$mxid			= intval($dataMax['mxid']);


echo "TOTAL IN orders :: ".$mxid;
echo "\n\n";
echo "TOTAL IN orders_indexing :: ".$mxid2;

if($mxid == $mxid2)
{
	$date			= date("d-m-Y H:i:s");
	$query			= "RENAME TABLE `orders` TO `orders_".$date."`";
	mysqli_query($adController->MySQL,$query) ;

	$query			= "RENAME TABLE `orders_indexing` TO `orders`";
	mysqli_query($adController->MySQL,$query);
}

?>