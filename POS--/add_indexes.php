<?php
set_time_limit(0);
require_once("db.conn.php");
$query 		= array();

$query[0]	= "ALTER TABLE orders_index ADD INDEX (itemid)";
$query[1]	= "ALTER TABLE orders_index ADD INDEX (branch)";
$query[2]	= "ALTER TABLE orders_index ADD INDEX (userid)";
$query[3]	= "ALTER TABLE orders_index ADD INDEX (storeid)";
$query[4]	= "ALTER TABLE orders_index ADD INDEX (hasvalue)";

$query[5]	= "ALTER TABLE orders_index ADD INDEX (dated)";
$query[6]	= "ALTER TABLE orders_index ADD INDEX (specific_date)";
$query[7]	= "ALTER TABLE orders_index ADD INDEX (invoice_number)";
$query[8]	= "ALTER TABLE orders_index ADD INDEX (localid)";

for($i=1;$i<count($query);$i++)
{
	mysqli_query($adController->MySQL,$query[$i]) or die(mysqli_error($adController->MySQL));
}

?>