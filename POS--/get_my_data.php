<?php
set_time_limit(0);
error_reporting(E_ERROR);
session_start();
require_once("db.conn.php");

$store	= $_REQUEST['iv'];
$element= array();





$query	= "SELECT * FROM dataversion WHERE storehash='$store'";
$res	= mysqli_query($adController->MySQL,$query);
$data	= mysqli_fetch_assoc($res);
$num	= mysqli_num_rows($res);

if(!$num)
{
	$element['success'] 	= "0";
	$element['msg'] 	= "Invalid";

	echo json_encode($element);
	die;
}



$element['success'] 		= "1";
$element['dataversion']		= $data;


$storeid			= $data['storeid'];
$insertQuery			= array();
$update				= array();
$notDelids			= array();


//////////////////  SCHEMA CHANGE ////////////////////
$insertQuery[count($insertQuery)] = "CREATE TABLE IF NOT EXISTS `cash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empid` int(11) NOT NULL,
  `debit` float NOT NULL,
  `credit` float NOT NULL,
  `document` int(11) NOT NULL,
  `disc` text NOT NULL,
  `hash` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `storeid` int(11) NOT NULL,
  `branchid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `branchid` (`branchid`),
  KEY `storeid` (`storeid`),
  KEY `date` (`date`),
  KEY `document` (`document`),
  KEY `empid` (`empid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1";


$insertQuery[count($insertQuery)] = "ALTER TABLE `cash` ADD `transaction_No` INT NOT NULL DEFAULT '0' AFTER `disc`, ADD INDEX (`transaction_No`) ";
$insertQuery[count($insertQuery)] = "ALTER TABLE `cash` CHANGE `disc` `disc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";


$insertQuery[count($insertQuery)] = "CREATE TABLE IF NOT EXISTS `visa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empid` int(11) NOT NULL,
  `debit` float NOT NULL,
  `credit` float NOT NULL,
  `document` int(11) NOT NULL,
  `disc` text NOT NULL,
  `hash` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `storeid` int(11) NOT NULL,
  `branchid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `branchid` (`branchid`),
  KEY `storeid` (`storeid`),
  KEY `date` (`date`),
  KEY `document` (`document`),
  KEY `empid` (`empid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1";
$insertQuery[count($insertQuery)] = "ALTER TABLE `visa` ADD `transaction_No` INT NOT NULL DEFAULT '0' AFTER `disc`, ADD INDEX (`transaction_No`) ";
$insertQuery[count($insertQuery)] = "ALTER TABLE `visa` CHANGE `disc` `disc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";


$insertQuery[count($insertQuery)] = "CREATE TABLE IF NOT EXISTS `mada` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empid` int(11) NOT NULL,
  `debit` float NOT NULL,
  `credit` float NOT NULL,
  `document` int(11) NOT NULL,
  `disc` text NOT NULL,
  `hash` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `storeid` int(11) NOT NULL,
  `branchid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `branchid` (`branchid`),
  KEY `storeid` (`storeid`),
  KEY `date` (`date`),
  KEY `document` (`document`),
  KEY `empid` (`empid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1";
$insertQuery[count($insertQuery)] = "ALTER TABLE `mada` ADD `transaction_No` INT NOT NULL DEFAULT '0' AFTER `disc`, ADD INDEX (`transaction_No`) ";
$insertQuery[count($insertQuery)] = "ALTER TABLE `mada` ADD `transaction_No` INT NOT NULL DEFAULT '0' AFTER `disc`, ADD INDEX (`transaction_No`) ";

$insertQuery[count($insertQuery)] = "CREATE TABLE IF NOT EXISTS `units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_en` text CHARACTER SET utf8 NOT NULL,
  `name_ar` text CHARACTER SET utf8 NOT NULL,
  `storeid` int(11) DEFAULT NULL,
  `default` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `storeid` (`storeid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1";

$insertQuery[count($insertQuery)] = "truncate units"; 
$query				  = "SELECT * FROM units WHERE storeid ='$storeid'";
$res				  = mysqli_query($adController->MySQL,$query);
$num                                = mysqli_num_rows($res);

if ($num)
{
while($data                       = mysqli_fetch_assoc($res))
{
    $insertQuery[count($insertQuery)] = "INSERT INTO units(`id`, `name_en`, `name_ar`, `storeid`, `default`) VALUES ('$data[id]','$data[name_en]','$data[name_ar]','$data[storeid]','$data[default]')";
 }
}
 else 
     {
        $insertQuery[count($insertQuery)] = "INSERT INTO units(`id`, `name_en`, `name_ar`, `storeid`, `default`) VALUES ('43','weaam]','وئام','44]','0')";

}


$insertQuery[count($insertQuery)] = "truncate discount";
$query				= "SELECT * FROM discount WHERE storeid='$storeid'";
$res				= mysqli_query($adController->MySQL,$query);
while($data                     = mysqli_fetch_assoc($res))
{
    $insertQuery[count($insertQuery)] = "INSERT INTO discount(amount,branchid,storeid,minamount) VALUES('$data[amount]','$data[branchid]','$data[storeid]','$data[minamount]')";
}


$insertQuery[count($insertQuery)] = "CREATE TABLE `vat_default` ( `id` int(11) NOT NULL AUTO_INCREMENT, `dated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `percentage` float NOT NULL, `storeid` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1";
$insertQuery[count($insertQuery)] = "ALTER TABLE branches ADD vatnumber VARCHAR(255) DEFAULT '000000' NOT NULL";
$insertQuery[count($insertQuery)] = "ALTER TABLE items ADD vat FLOAT DEFAULT 0 NOT NULL";
$insertQuery[count($insertQuery)] = "ALTER TABLE items ADD selt FLOAT DEFAULT 0 NOT NULL";
$insertQuery[count($insertQuery)] = "ALTER TABLE items ADD taxInPrice enum('0','1') default '0' not null";

$insertQuery[count($insertQuery)] = "ALTER TABLE orders ADD vat FLOAT DEFAULT 0 NOT NULL";
$insertQuery[count($insertQuery)] = "TRUNCATE  vat_default";
$query				  = "SELECT * FROM vat_default WHERE storeid='$storeid'";
$res				  = mysqli_query($adController->MySQL,$query);
while($data                       = mysqli_fetch_assoc($res))
{
    $insertQuery[count($insertQuery)] = "INSERT INTO vat_default(percentage,storeid) VALUES('$data[percentage]','$data[storeid]')";
}

$insertQuery[count($insertQuery)] = "ALTER TABLE local_devices ADD deviceid TEXT NOT NULL";
$insertQuery[count($insertQuery)] = "ALTER TABLE local_devices MODIFY uid TEXT";
$insertQuery[count($insertQuery)] = "CREATE TABLE `shifts` ( `id` int(11) NOT NULL AUTO_INCREMENT, `name_en` text NOT NULL, `name_ar` text CHARACTER SET utf8 NOT NULL, `starts` varchar(50) NOT NULL, `ends` varchar(50) NOT NULL, `storeid` int(11) NOT NULL, `branches` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1";

$insertQuery[count($insertQuery)] = "truncate shifts";
$query				  = "SELECT * FROM shifts WHERE storeid='$storeid'";
$res				  = mysqli_query($adController->MySQL,$query);
while($data                       = mysqli_fetch_assoc($res))
{
    $insertQuery[count($insertQuery)] = "INSERT INTO shifts(id,name_en,name_ar,storeid,starts,ends) VALUES('$data[id]','$data[name_en]','$data[name_ar]','$data[storeid]','$data[starts]','$data[ends]')";
}




$insertQuery[count($insertQuery)] = "alter table employees add allow_price_change enum('0','1') default '0' not null";
$insertQuery[count($insertQuery)] = "alter table employees add till_control enum('0','1') default '0' not null";
$insertQuery[count($insertQuery)] = "alter table employees add visa enum('0','1') default '1' not null";
$insertQuery[count($insertQuery)] = "alter table employees add mada enum('0','1') default '1' not null";
$insertQuery[count($insertQuery)] = "alter table employees add show_report enum('0','1') default '1' not null";
$insertQuery[count($insertQuery)] = "alter table employees add allow_dis_change enum('1','0') default '0' not null";
$insertQuery[count($insertQuery)] = "alter table employees add services text not null";
    



$insertQuery[count($insertQuery)] = "alter table orders add u_value varchar(255) not null";
$insertQuery[count($insertQuery)] = "alter table orders_deleted add u_value varchar(255) not null";
$insertQuery[count($insertQuery)] = "ALTER TABLE dataversion ADD type ENUM('1','2') DEFAULT '1' NOT NULL";
$insertQuery[count($insertQuery)] = "ALTER TABLE local_devices MODIFY uid TEXT";
$insertQuery[count($insertQuery)] = "ALTER TABLE orders_deleted MODIFY dated DATETIME";
$insertQuery[count($insertQuery)] = "ALTER TABLE orders MODIFY dated DATETIME";
$insertQuery[count($insertQuery)] = "ALTER TABLE local_devices ADD kitchenipaddress TEXT NOT NULL ";
$insertQuery[count($insertQuery)] = "alter table orders add service_by text not null";
$insertQuery[count($insertQuery)] = "alter table user_rights add type varchar(5) not null default '1,2'";
$insertQuery[count($insertQuery)] = "INSERT INTO `user_rights` (`id`, `view_items`, `add_items`, `edit_items`, `delete_items`, `view_cat`, `add_cat`, `edit_cat`, `delete_cat`, `view_emp`, `add_emp`, `edit_emp`, `delete_emp`, `designation_en`, `designation_ar`, `sales`, `reports`, `type`) VALUES (NULL, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'Service', 'الخدمات ', '1', '0', '2')";
$insertQuery[count($insertQuery)] = "update user_rights SET type='1' WHERE id IN(4,5,6)";
$insertQuery[count($insertQuery)] = "alter table items add services text not null";
$insertQuery[count($insertQuery)] = "CREATE TABLE `positionsitem` ( `id` int(11) NOT NULL AUTO_INCREMENT, `uid` int(11) NOT NULL, `catid` int(11) NOT NULL, `style` text NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
$insertQuery[count($insertQuery)] = "CREATE TABLE `positions` ( `id` int(11) NOT NULL AUTO_INCREMENT, `uid` int(11) NOT NULL, `catid` int(11) NOT NULL, `style` text NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
$insertQuery[count($insertQuery)] = "alter table positions add type int(10) not null";
$insertQuery[count($insertQuery)] = "ALTER TABLE dataversion ADD type ENUM('1','2') DEFAULT '1' NOT NULL";


$insertQuery[count($insertQuery)] = "alter table categories add position varchar(255) not null";
$insertQuery[count($insertQuery)] = "alter table orders add section varchar(255) character set utf8 collate utf8_general_ci not null";
$insertQuery[count($insertQuery)] = "CREATE TABLE `invoice_number` ( `id` int(11) NOT NULL AUTO_INCREMENT, `numberval` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
$insertQuery[count($insertQuery)] = "CREATE TABLE `enabled` ( `id` int(11) NOT NULL AUTO_INCREMENT, `enabled` enum('0','1') NOT NULL DEFAULT '1', PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
$insertQuery[count($insertQuery)] = "CREATE TABLE `sections` ( `id` int(11) NOT NULL AUTO_INCREMENT, `name` text CHARACTER SET utf8 NOT NULL, `name_en` text CHARACTER SET utf8 NOT NULL, `name_ar` text CHARACTER SET utf8 NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1";


$insertQuery[count($insertQuery)]   = "CREATE TABLE `comments` ( `id` int(11) NOT NULL AUTO_INCREMENT, `txt` text CHARACTER SET utf8 NOT NULL, `dated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1";

$insertQuery[count($insertQuery)]   = "alter table table_busy add section text character set utf8 collate utf8_general_ci not null";


$insertQuery[count($insertQuery)]   = "alter table local_devices add kitchen_printername text not null";
$insertQuery[count($insertQuery)]   = "alter table local_devices add sections text character set utf8 collate utf8_general_ci not null";

$insertQuery[count($insertQuery)]   = "alter table orders add invoice_number int(11) not null";
$insertQuery[count($insertQuery)]   = "alter table orders add invoice text not null";
$insertQuery[count($insertQuery)]   = "alter table orders add order_type int(11) not null";
$insertQuery[count($insertQuery)]   = "alter table orders add delivery_cost float not null";
$insertQuery[count($insertQuery)]   = "ALTER TABLE orders CHANGE qty qty FLOAT(11) NOT NULL";
$insertQuery[count($insertQuery)]   = "CREATE TABLE `delivery` ( `id` int(11) NOT NULL AUTO_INCREMENT, `dated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `amount` float NOT NULL, `branchid` int(11) NOT NULL, `reason` text CHARACTER SET utf8 NOT NULL, `storeid` int(11) NOT NULL, `minamount` float NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1";


// for($i=0;$i<count($insertQuery);$i++)
// {
//     mysqli_query($adController->MySQL,$insertQuery[$i]);
// }


//////////////////  END SCHEMA CHANGE ////////////////////



$query				= "SELECT * FROM categories WHERE storeid='$storeid'";
$res				= mysqli_query($adController->MySQL,$query);
while($data			= mysqli_fetch_assoc($res))
{
	$insertQuery[count($insertQuery)] = "INSERT INTO categories (id, enabled, name_en, image, entry_date, entry_person, updated_date, update_user, store_branch, showtocashier, name_ar, thumb, storeid, hash) VALUES ('$data[id]', '$data[enabled]', '$data[name_en]', '$data[image]', '$data[entry_date]', '$data[entry_person]', '$data[updated_date]', '$data[update_user]', '$data[store_branch]', '$data[showtocashier]', '$data[name_ar]', '$data[thumb]', '$data[storeid]', '$data[hash]')";

	$update[count($update)]		  = "UPDATE categories SET enabled='$data[enabled]', name_en='$data[name_en]', image='$data[image]', entry_date='$data[entry_date]', entry_person='$data[entry_person]', updated_date='$data[updated_date]', update_user='$data[update_user]', store_branch='$data[store_branch]', showtocashier='$data[showtocashier]', name_ar='$data[name_ar]', thumb='$data[thumb]', storeid='$data[storeid]', hash='$data[hash]' WHERE id='$data[id]'";

	$notDelids[] = $data[id];


}
$notDelids = implode(",",$notDelids);
$insertQuery[count($insertQuery)] = "DELETE FROM categories WHERE id NOT IN($notDelids)";


$notDelids			= array();
$query				= "SELECT * FROM items WHERE storeid='$storeid'";
$res				= mysqli_query($adController->MySQL,$query);
while($data			= mysqli_fetch_assoc($res))
{
	$insertQuery[count($insertQuery)]	=  "INSERT INTO items (id, name_en, barcode, image, location_en, unit, expiry_notification, is_service, price, discount, commission, intimate_stock, entry_date, storeid, name_ar, location_ar, expiry_date, catid, store_branch, enabled, show_to_cashier, measurement, qty_en, qty_ar, item_type, item_thread,services,vat,selt,taxInPrice) VALUES ('$data[id]', '$data[name_en]', '$data[barcode]', '$data[image]', '$data[location_en]', '$data[unit]', '$data[expiry_notification]', '$data[is_service]', '$data[price]', '$data[discount]', '$data[commission]', '$data[intimate_stock]', '$data[entry_date]', '$data[storeid]', '$data[name_ar]', '$data[location_ar]', '$data[expiry_date]', '$data[catid]', '$data[store_branch]', '$data[enabled]', '$data[show_to_cashier]', '$data[measurement]', '$data[qty_en]', '$data[qty_ar]', '$data[item_type]', '$data[item_thread]','$data[services]','$data[vat]','$data[selt]','$data[taxInPrice]')";


	$update[count($update)]			= "UPDATE items SET name_en='$data[name_en]', barcode='$data[barcode]', image='$data[image]', location_en='$data[location_en]', unit='$data[unit]', expiry_notification='$data[expiry_notification]', is_service='$data[is_service]', price='$data[price]', discount='$data[discount]', commission='$data[commission]', intimate_stock='$data[intimate_stock]', entry_date='$data[entry_date]', storeid='$data[storeid]', name_ar='$data[name_ar]', location_ar='$data[location_ar]', expiry_date='$data[expiry_date]', catid='$data[catid]', store_branch='$data[store_branch]', enabled='$data[enabled]', show_to_cashier='$data[show_to_cashier]', measurement='$data[measurement]', qty_en='$data[qty_en]',services='$data[services]', qty_ar='$data[qty_ar]', item_type='$data[item_type]', item_thread='$data[item_thread]',vat='$data[vat]',selt='$data[selt]',taxInPrice='$data[taxInPrice]' WHERE id='$data[id]'";

	$notDelids[] = $data[id];

}

$notDelids = implode(",",$notDelids);
$insertQuery[count($insertQuery)] = "DELETE FROM items WHERE id NOT IN($notDelids)";

$notDelids			= array();
$query				= "SELECT * FROM customers WHERE storeid='$storeid'";
$res				= mysqli_query($adController->MySQL,$query);
while($data			= mysqli_fetch_assoc($res))
{
	$insertQuery[count($insertQuery)]	=  "INSERT INTO customers (id, name, address, phone, dated, storeid, branchid, cardnumber) VALUES ('$data[id]', '$data[name]', '$data[address]', '$data[phone]', '$data[dated]', '$data[storeid]', '$data[branchid]', '$data[cardnumber]')";


	$update[count($update)]			= "UPDATE  customers SET  name='$data[name]' , address='$data[address]', phone='$data[phone]', dated='$data[dated]', storeid='$data[storeid]', branchid='$data[branchid]', cardnumber='$data[cardnumber]' WHERE id='$data[id]'";

	$notDelids[] = $data[id];

}

$notDelids = implode(",",$notDelids);
$insertQuery[count($insertQuery)] = "DELETE FROM customers WHERE id NOT IN($notDelids)";

$notDelids			= array();
$query				= "SELECT * FROM discount WHERE storeid='$storeid'";
$res				= mysqli_query($adController->MySQL,$query);
while($data			= mysqli_fetch_assoc($res))
{
	$insertQuery[count($insertQuery)]	=  "INSERT INTO discount (id, dated, amount, branchid, reason, storeid, minamount) VALUES ('$data[id]', '$data[id]', '$data[dated]', '$data[amount]', '$data[branchid]', '$data[reason]', '$data[storeid]',, '$data[minamount]')";


	$update[count($update)]			= "UPDATE  discount SET  dated='$data[dated]', amount='$data[amount]', branchid='$data[branchid]', reason='$data[reason], storeid='$data[storeid]', minamount='$data[minamount]' WHERE id='$data[id]'";
	$notDelids[] = $data[id];

}

$notDelids = implode(",",$notDelids);
$insertQuery[count($insertQuery)] = "DELETE FROM discount WHERE id NOT IN($notDelids)";




////////////////

$notDelids			= array();
$query				= "SELECT * FROM delivery WHERE storeid='$storeid'";
$res				= mysqli_query($adController->MySQL,$query);
while($data			= mysqli_fetch_assoc($res))
{
	$insertQuery[count($insertQuery)]	=  "INSERT INTO delivery (id, dated, amount, storeid) VALUES ('$data[id]', '$data[dated]', '$data[amount]', '$data[storeid]')";


	$update[count($update)]			= "UPDATE  delivery SET  dated='$data[dated]', amount='$data[amount]', branchid='$data[branchid]', reason='$data[reason]', storeid='$data[storeid]', minamount='$data[minamount]' WHERE id='$data[id]'";
	$notDelids[] = $data[id];

}

$notDelids = implode(",",$notDelids);
$insertQuery[count($insertQuery)] = "DELETE FROM delivery WHERE id NOT IN($notDelids)";



//////////////////
$notDelids			= array();
$query				= "SELECT * FROM employees WHERE storeid='$storeid'";
$res				= mysqli_query($adController->MySQL,$query);
while($data			= mysqli_fetch_assoc($res))
{
	$insertQuery[count($insertQuery)]	=  "INSERT INTO employees (id, name_en, image, email, password, contact, store_branch, type, gcm, passport_num, iqama_num, insurance_num, medical_num, medical_expiry, insurance_expiry, passport_expiry, iqama_expiry, license_expiry, license_num, name_ar, storeid , show_report, allow_price_change, allow_dis_change, visa, mada, till_control, services) VALUES ('$data[id]', '$data[name_en]', '$data[image]', '$data[email]', '$data[password]', '$data[contact]', '$data[store_branch]', '$data[type]', '$data[gcm]', '$data[passport_num]', '$data[iqama_num]', '$data[insurance_num]', '$data[medical_num]', '$data[medical_expiry]', '$data[insurance_expiry]', '$data[passport_expiry]', '$data[iqama_expiry]', '$data[license_expiry]', '$data[license_num]', '$data[name_ar]', '$data[storeid]','$data[show_report]','$data[allow_price_change]','$data[allow_dis_change]','$data[visa]','$data[mada]','$data[till_control]','$data[services]')";


	$update[count($update)]			=  "UPDATE  employees SET till_control='$data[till_control]',services='$data[services]',visa='$data[visa]' ,mada='$data[mada]' , show_report='$data[show_report]' ,name_en='$data[name_en]', image='$data[image]', email='$data[email]', contact='$data[contact]', store_branch='$data[store_branch]', type='$data[type]', gcm='$data[gcm]', passport_num='$data[passport_num]', iqama_num='$data[iqama_num]',allow_price_change='$data[allow_price_change]',allow_dis_change='$data[allow_dis_change]', insurance_num='$data[insurance_num]', medical_num='$data[medical_num]', medical_expiry='$data[medical_expiry]', insurance_expiry='$data[insurance_expiry]', passport_expiry='$data[passport_expiry]', iqama_expiry='$data[iqama_expiry]', license_expiry='$data[license_expiry]', license_num='$data[license_num]', name_ar='$data[name_ar]', storeid='$data[storeid]' WHERE id='$data[id]'";
	$notDelids[] = $data[id];

}

$notDelids = implode(",",$notDelids);
$insertQuery[count($insertQuery)] = "DELETE FROM employees WHERE id NOT IN($notDelids)";





$notDelids			= array();
$query				= "SELECT * FROM branches WHERE storeid='$storeid'";
$res				= mysqli_query($adController->MySQL,$query);
while($data			= mysqli_fetch_assoc($res))
{
	$insertQuery[count($insertQuery)]	=  "INSERT INTO branches (id, name_en, storeid, city, state, street_en, street_ar, country, phone, fax, name_ar, crnum, munum, crapp, crexp, muapp, muexp, dated,vatnumber) VALUES ('$data[id]', '$data[name_en]', '$data[storeid]', '$data[city]', '$data[state]', '$data[street_en]', '$data[street_ar]', '$data[country]', '$data[phone]', '$data[fax]', '$data[name_ar]', '$data[crnum]', '$data[munum]', '$data[crapp]', '$data[crexp]', '$data[muapp]', '$data[muexp]', '$data[dated]','$data[vatnumber]')";


	$update[count($update)]				=  "UPDATE branches SET name_en='$data[name_en]',vatnumber='$data[vatnumber]', storeid='$data[storeid]', city='$data[city]', state='$data[state]', street_en='$data[street_en]', street_ar='$data[street_ar]', country='$data[country]', phone='$data[phone]', fax='$data[fax]', name_ar='$data[name_ar]', crnum='$data[crnum]', munum='$data[munum]', crapp='$data[crapp]', crexp='$data[crexp]', muapp='$data[muapp]', muexp='$data[muexp]', dated='$data[dated]' WHERE id='$data[id]'";
	$notDelids[] = $data[id];

}

$notDelids = implode(",",$notDelids);
$insertQuery[count($insertQuery)] = "DELETE FROM branches WHERE id NOT IN($notDelids)";

$notDelids			= array();
$query				= "SELECT * FROM tables_count WHERE storeid='$storeid'";
$res				= mysqli_query($adController->MySQL,$query);
while($data			= mysqli_fetch_assoc($res))
{
	$insertQuery[count($insertQuery)]	=  "INSERT INTO `tables_count` (`id`, `table_c`, `storeid`, `branch`) VALUES ('$data[id]', '$data[table_c]', '$data[storeid]', '$data[branch]')";


	$update[count($update)]			=  "UPDATE `tables_count` SET `table_c`='$data[table_c]', `storeid`='$data[storeid]', `branch`='$data[branch]' WHERE id='$data[id]'";
	$notDelids[] = $data[id];

}

$notDelids = implode(",",$notDelids);
$insertQuery[count($insertQuery)] = "DELETE FROM tables_count WHERE id NOT IN($notDelids)";

$element['insert'] 	= $insertQuery;
$element['update']	= $update;


$responseValue = json_encode($element); 
if($store == "qwe-75-fg")
{
	file_put_contents("AD.txt",$responseValue);
}

echo $responseValue;

?>