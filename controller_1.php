<?php

require_once("top.php");
require_once("validation.class.php");




$function = $_REQUEST['f'];
$adController = new AdController();

if($function !="")
	$adController->$function();


class AdController
{
    //public $journalNo,$Jdocument,$Jdisc,$Jbranch,$Jcost,$Jtotal,$Jdate;
   	public function __construct()
	{
            global $adController->MySQL;
		require_once("db.conn.php");
	}
	
	function __destruct() {
    	mysqli_close($adController->MySQL);
  	}       

	function setLang()
	{
		$_SESSION['lang'] = $_REQUEST['l'];
	}
  

         function logout()
	{
		session_destroy();
	}

	function ajaxEmailPresent()
	{
		$email = $_POST['email'];
		echo $this->checkEmailPresent($email);
	}

	function checkEmailPresent($email)
	{
		$query 	= "SELECT * FROM storeuser WHERE email = '$email'  ";
		$res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
		$num	= mysqli_num_rows($res);
		
		return $num;
	}

	function checkEmailPresentEmp()
	{
		$email			= $_REQUEST['email'];
		$nd			= $_REQUEST['nd'];
		$itemId			= $this->encrypt_decrypt(2,urldecode($nd),0);
		$itemId 		= $this->encrypt_decrypt(2,$itemId,0);
		$itemId			= $this->encrypt_decrypt(2,$itemId,0);

		if($nd == "")
			echo $this->emailPresentEmp($email,1,0);
		else
			echo $this->emailPresentEmp($email,2,$itemId);
	}

	function emailPresentEmp($email,$identifier,$id)
	{
		$storeid	= $_SESSION['storeid'];
		
		if($identifier == 1)
			$query 		= "SELECT * FROM employees WHERE email = '$email' AND storeid='$storeid' ";//
		else
			$query 		= "SELECT * FROM employees WHERE email = '$email'  AND storeid='$storeid' AND id!='$id'";

		$res		= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
		$num		= mysqli_num_rows($res);
		
		return  $num;
	}
        
        
        function userPresent()
        {
            $username = $_POST['username'];
            $sql_check = mysqli_query($adController->MySQL,"SELECT id FROM employees WHERE user='$username'") or die(mysqli_error($adController->MySQL));
            if(mysqli_num_rows($sql_check))
            {
                echo '<span style = "color:red">اسم المستخدم غير متاح </span>';
            }
            else
            {
                echo 'OK';
            }

        }
     
        
        
        function emailPresentSupp($email,$id)
	{
		$storeid	= $_SESSION['storeid'];
		

		$query 		= "SELECT * FROM suppliers WHERE email = '$email' AND email !=''  AND storeid='$storeid' AND id!='$id'";

		$res		= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
		$num		= mysqli_num_rows($res);
		
		return  $num;
	}
        function isExsist($table,$field,$value,$id ,$chick)
	{
		$storeid	= $_SESSION['storeid'];
                if ($chick==0)
                $query 		= "SELECT * FROM $table WHERE $field = '$value' AND storeid!='$storeid'";
                else
		$query 		= "SELECT * FROM $table WHERE $field = '$value' AND storeid='$storeid' AND id !='$id'";
		$res		= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
		$num		= mysqli_num_rows($res);
		return  $num;
	}
	function createnew()
	{
		$name 		= $_POST['name'];
		$email 		= $_POST['email'];
		$password 	= $_POST['password'];
		$logo 		= $_FILES['logo'];

		if($name=="")
		{
			print("<script>alert('".ERROR_NAME."');history.go(-1)</script>");
			exit();
		}

		else if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			print("<script>alert('".ERROR_EMAIL_INVALID."');history.go(-1)</script>");
			exit();
		}

		else if($password=="")
		{
			print("<script>alert('".ERROR_PASSWORD."');history.go(-1)</script>");
			exit();
		}

		else if($password < 6)
		{
			print("<script>alert('".ERROR_PASSWORD_INVALID."');history.go(-1)</script>");
			exit();
		}


		else if($password < 6)
		{
			print("<script>alert('".ERROR_PASSWORD_INVALID."');history.go(-1)</script>");
			exit();
		}

		else if($this->checkEmailPresent($email))
		{
			print("<script>alert('".ERROR_EMAIL_ALREADY_TAKEN."');history.go(-1)</script>");
			exit();
		}

		else
		{
			
			$aes 		= new AES($password, ENC_KEY, ENC_BS);
			$password 	= $aes->encrypt();
			$query 		= "INSERT INTO `storeuser` (`name`,`email`, `password`) VALUES ('$name', '$email', '$password');";
			$exe   		= mysqli_query($adController->MySQL,$query);

			if($exe)
			{
				$id	= mysqli_insert_id();
				
				$aes 		= new AES($id, ENC_KEY, ENC_BS);
				$idDir	 	= $aes->encrypt();
				$idDir		= preg_replace("/[^a-zA-Z0-9]+/", "", $idDir);;
				$mydir	= IMG_DIR.$idDir;
				if(!file_exists($mydir))
				{
					mkdir($mydir);
					chmod($mydir,0755);
				}
				$_SESSION['userlogged']="1";
				if(file_exists($logo['tmp_name']) && is_uploaded_file($logo['tmp_name'])) 
				{
					$target = $mydir."/".time()."_".$logo['name'];
					$var = move_uploaded_file($logo['tmp_name'],$target);
					if($var)
					{
						$query 		= "UPDATE`storeuser` SET logo='$target' WHERE id='$id'";
						$exe   		= mysqli_query($adController->MySQL,$query);
						if($exe)
						{
							print("<script>alert('".ACCOUNT_CREATED_SUCCESSFULLY."');location.replace('.')</script>");
							exit();
						}
					}
					else
					{
						print("<script>alert('".ACCOUNT_CREATED_WITHOUTIMAGE."');location.replace('.')</script>");
						exit();
					}
				}
			}
			else
			{
				print("<script>alert('".ERROR_IN_CREATING_ACCOUNT."');history.go(-1)</script>");
			}
		}

		$this->updateDataVersion();
		
	}


	function login()
	{

		$user 		= $_POST['user'];
		$password 	= $_POST['password'];
		$password 	= md5($_POST['password']);//$aes->encrypt();
		$query		= "SELECT * FROM employees WHERE user = '$user' AND password='$password'";
		$res		= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
		$num		= mysqli_num_rows($res);
                $data		= mysqli_fetch_assoc($res);			
		if($num)
		{                    
                    session_start();	
		    $language		    = $_SESSION['lang'];
		    $name		    = $data['name_'.$language];
		    $_SESSION['name']	    = $name;	
                    $_SESSION['userlogged'] = "1";		    
 	
                    $query 	= "SELECT * FROM storeuser WHERE id = '51'";  	
                    $res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                    $data	= mysqli_fetch_assoc($res);


	    

                    $_SESSION['storeid']    =$data['id'];



                    echo "1";
		}
		else
			echo USER_DOES_NOT_EXIST;

	}


	function forgot()
	{

		$email 		= $_POST['email'];

		$query 		= "SELECT * FROM storeuser WHERE email = '$email' ";
		$res		= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
		$num		= mysqli_num_rows($res);
		$data		= mysqli_fetch_assoc($res);
		if($num)
		{			
			$hashVal  	= $email."_".$data['id']."_".time();
			$aes 		= new AES($hashVal, ENC_KEY, ENC_BS);
			$hashVal	= $aes->encrypt();

			$query 		= "SELECT * FROM forgot WHERE uid = '$data[id]' ";
			$res		= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
			$num		= mysqli_num_rows($res);
			if($num)
				$query	= "UPDATE forgot SET hash='$hashVal' WHERE uid='$data[id]'";
			else
				$query	= "INSERT INTO forgot(uid,hash) VALUES('$data[id]','$hashVal')";

			$exe		= mysqli_query($adController->MySQL,$query);
			if($exe)
			{
				$mail		= new Email();
				$mail->passwordReset($hashVal);
	
				echo "1";
			}
			else
				echo ERROR_IN_RESETING_PWD;
		}
		else
				echo ERROR_IN_RESETING_PWD;
	}
        function getItemdata()
	{
            $resp                   = array();
            $lang                   = $_SESSION['lang'];
            $storeid                = $_SESSION['storeid'];
            $id                     = $_REQUEST['id'];
            
            $query                  = "SELECT * FROM items WHERE id='$id' AND storeid='$storeid' ";
            $resItem                = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            $dataItem               = mysqli_fetch_assoc($resItem);
            
            $unitquery              = "SELECT * FROM units WHERE id = '".$dataItem['unit']."'" ;
            $unitVal                = mysqli_query($adController->MySQL,$unitquery);
            $dataUnit               = mysqli_fetch_assoc($unitVal);

            $dataItem['unit_en']    = $dataUnit['name_en'];
            $dataItem['unit_ar']    = $dataUnit['name_ar'];
            
            $resp[]	= $dataItem;
            echo json_encode($resp);
	}        

        
        
      function search_items()
        {
                $array                  = array();
                $storeid		= $_SESSION['storeid'];
                $term                   = $_REQUEST['term'];
		$query			= "SELECT * FROM items WHERE ( barcode LIKE '$term%' OR name_en LIKE '%$term%' OR name_ar LIKE '%$term%') AND FIND_IN_SET('$storeid',`storeid`) ";
		$resVal			= mysqli_query($adController->MySQL,$query);
		while($data		= mysqli_fetch_assoc($resVal))
                {
			$catid  	= $this->encrypt_decrypt(1,$data['catid'],0);
                    
                        $element['id']      = $data[id];
                        $element['label']   = $data['name_en'];
                        if($data[name_ar])
                            $element['label']= $element['label']." ( ".$data[name_ar]." )";
                        if($data[barcode] != "")
                            $element['label']   = "Barcode :: ".$data[barcode]." ".$element['label'];
                        
                        $element['value']   = $element['label'];
                        $array[]            = $element;
                }

		echo  json_encode($array);
        }
        


	////// BRANCHES /////
	function addBranch()
	{
		$this->checkAuthorized();
		$name		= trim($_POST['name']);
		$street		= trim($_POST['street']);
		$country	= trim($_POST['country']);
		$state		= trim($_POST['state']);
		$city		= trim($_POST['city']);
		$phone		= trim($_POST['phone']);
		$fax		= trim($_POST['fax']);
		$crnum		= trim($_POST['crnum']);
		$crapp		= trim($_POST['crapp']);
		$crexp		= trim($_POST['crexp']);
		$munum		= trim($_POST['munum']);
		$muapp		= trim($_POST['muapp']);
		$muexp		= trim($_POST['muexp']);
		$name_ar	= trim($_POST['name_ar']);
		$street_ar	= trim($_POST['street_ar']);

		if($crapp!="")
			$crapp	= $this->dateSlashesToHiphen($crapp);
		if($crexp!="")
			$crexp	= $this->dateSlashesToHiphen($crexp);
		if($muapp!="")
			$muapp	= $this->dateSlashesToHiphen($muapp);
		if($muexp!="")
			$muexp	= $this->dateSlashesToHiphen($muexp);

		
		$error	= $this->branchValidation($name,$street,$country,$state,$city,$phone,$fax,$crnum,$crapp,$crexp,$munum,$muapp,$muexp,$name_ar,$street_ar);
		if(!$error)
		{
			$storeid	= $_SESSION['storeid'];
			$query	= "INSERT INTO  `branches` (
				`name_en` ,
				`storeid` ,
				`city` ,
				`state` ,
				`street_en` ,
				`street_ar` ,
				`country` ,
				`phone` ,
				`fax` ,
				`name_ar`,
				`crnum`,
				`crapp`,
				`crexp`,
				`munum`,
				`muapp`,
				`muexp`
				)
				VALUES ('$name',  '$storeid',  '$city',  '$state',  '$street',  '$street_ar',  '$country',  '$phone',  '$fax',  '$name_ar',  '$crnum',  '$crapp',  '$crexp','$munum', '$muapp',  '$muexp')";

				$exe = mysqli_query($adController->MySQL,$query);
				if($exe)
				{
					$this->updateAllBranches();
					echo BRANCH_ADDED_SUCCESSFULLY;
				}
				else
					echo ERROR_IN_CREATING_NEW_BRANCH;

				$this->updateDataVersion();
		}
		else
			echo $error;

	}

	function getStates()
	{
		$language 	= $_SESSION['lang'];
		$cid	  	= $_REQUEST['cid'];

		$STR 		= "<select name='state' id='state' data-rel='chosen' style='display: none;' class='chzn-done'>
					<option value=''>--".SELECT_STATE."--</option>[SELECT_DETAILS]
					</select><div id='state_chzn' class='chzn-container chzn-container-single' style='width: 220px;'><a href='javascript:void(0)' class='chzn-single'><span>--".SELECT_STATE."--</span><div><b></b></div></a><div class='chzn-drop' style='left: -9000px; width: 218px; top: 25px;'><div class='chzn-search'><input type='text' autocomplete='off' style='width: 183px;'></div><ul class='chzn-results'><li id='state_chzn_o_0' class='active-result' style=''>--".SELECT_STATE."--</li>[LI_DETAILS]</ul></div></div> &nbsp; *
				";
		echo "<option value=''>--".SELECT_STATE."--</option>";

		$i		= 1;
		$arrayOpt 	= array();
		$arrayLi	= array();
		$query 		= "SELECT * FROM us_state_master WHERE cou_id='$cid'  ORDER BY st_name_en ASC";
		$res   		= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
		while($data= mysqli_fetch_assoc($res))
		{
			$id   = $data['st_id'];
			$name = $data['st_name_'.$language];

			$arrayOpt[count($arrayOpt)] 	= "<option value='$id'>$name</option>";
			$arrayLi[count($arrayLi)] 	= "<li id='state_chzn_o_$i' class='active-result' style=''>$name</li>";
			$i++;
		}

		$arrayOpt 	= implode("",$arrayOpt);
		$arrayLi 	= implode("",$arrayLi);

		echo $arrayOpt;
		exit();
		

		$STR  = str_replace("[SELECT_DETAILS]",$arrayOpt,$STR);
		$STR  = str_replace("[LI_DETAILS]",$arrayLi,$STR);
	}

	function getCity()
	{
		$language 	= $_SESSION['lang'];
		$cid	  	= $_REQUEST['sid'];
		echo  "<option value=''>--".SELECT_CITY."</option>";;
		$query 		= "SELECT * FROM us_city_master WHERE st_id='$cid'  ORDER BY city_name_en ASC";
		$res   		= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
		while($data= mysqli_fetch_assoc($res))
		{
			$id   = $data['city_id'];
			$name = $data['city_name_'.$language];

			echo  "<option value='$id'>$name</option>";;
		}
		exit();
	}
   	function getCustomers()
	{
            $language 	= $_SESSION['lang'];
            $storeid	= $_SESSION['storeid'];
            $query      = "SELECT * FROM customers WHERE storeid='$storeid'";
            $res        = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            while($data = mysqli_fetch_assoc($res))
            {
                $id   = $data['id'];
                $phone = $data['phone'];
                $address = $data['address'];
                $name = $data['name_'.$language];
                $sel='';
                if($data['default']== '1')
                {
                     $sel = " selected='true' ";
                }
                echo "<option value='$id' "
                            . "phone = '$phone' "
                            . "address = '$address' "
                            . " $sel>$name -> $phone</option>";
            }
	}       

	function getStores()
	{
		$language 	= $_SESSION['lang'];
		$cid	  	= $_REQUEST['cid'];
                $storeid	= $_SESSION['storeid'];


		$query 		= "SELECT * FROM stores WHERE storeid='$storeid' AND branch ='$cid'  ORDER BY name_$language ASC";
		$res   		= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
		while($data= mysqli_fetch_assoc($res))
		{
			$id   = $data['id'];
			$name = $data['name_'.$language];

			echo  "<option value='$id'>$name</option>";;
		}

	}        


	function delete_data()
	{
		$this->checkAuthorized();
		$table 	 = $this->encrypt_decrypt(2,urldecode($_REQUEST['a']),0);
		$id	 = $this->encrypt_decrypt(2,urldecode($_REQUEST['b']),0);
		$p	 = $this->encrypt_decrypt(2,urldecode($_REQUEST['d']),0);

                if($table == "items")
                {
                	$query  = "INSERT INTO items_deleted SELECT o.* FROM items o WHERE o.item_thread='$id'";
                        $res    = mysqli_query($adController->MySQL,$query);
                    $query 	 = "DELETE FROM $table WHERE item_thread='$id' ";
                    $res    = mysqli_query($adController->MySQL,$query);                    
                }
                else if($table == "services")
                {
                    
                        $query  = "SELECT id,services FROM employees WHERE FIND_IN_SET($id,services)";
                        $res    = mysqli_query($adController->MySQL,$query);
                        while($data   = mysqli_fetch_assoc($res))
                        {
                             $idvalu = $data['id'];
                             $ser    = explode(",",$data['services']);
                             $bSer   = array();
                             for($ii=0;$ii < count($ser);$ii++)
                             {
                                 if($ser[$ii] != $id)
                                     $bSer[]    = $ser[$ii];
                             }
                             $ser    = implode(",",$bSer);
                             
                             $query  = "UPDATE employees SET services='$ser' WHERE id='$idvalu'";
                             mysqli_query($adController->MySQL,$query);
                             
                        }
                       
                        
                        $query  = "SELECT id,services FROM items WHERE FIND_IN_SET($id,services)";
                        $res    = mysqli_query($adController->MySQL,$query);
                        while($data   = mysqli_fetch_assoc($res))
                        {
                             $idvalu = $data['id'];
                             $ser    = explode(",",$data['services']);
                             $bSer   = array();
                             for($ii=0;$ii < count($ser);$ii++)
                             {
                                 if($ser[$ii] != $id)
                                     $bSer[]    = $ser[$ii];
                             }
                             $ser    = implode(",",$bSer);
                             
                             $query  = "UPDATE items SET services='$ser' WHERE id='$idvalu'";
                             mysqli_query($adController->MySQL,$query);
                             
                        }
                        
                	$query 	 = "DELETE FROM $table WHERE id='$id' ";             
                }
                else
                {
                    $query 	 = "DELETE FROM $table WHERE id='$id' ";
                    $this->deleteAcount ($p,$id);
                }
                
                
		echo mysqli_query($adController->MySQL,$query);


		exit();
		
	}
	function delete_invoice()
	{
		$this->checkAuthorized();
		$invoiceTable 	 = $this->encrypt_decrypt(2,urldecode($_REQUEST['vt']),0);
		$invoice  	 = $this->encrypt_decrypt(2,urldecode($_REQUEST['id']),0);
                $otherTable  	 = $this->encrypt_decrypt(2,urldecode($_REQUEST['ot']),0);
                $storeid = $_SESSION['storeid'];
                
                $query	 = "UPDATE `$invoiceTable` SET `state` ='0'
                         WHERE invoicenumber='$invoice' AND storeid='$storeid'";
                $exe = mysqli_query($adController->MySQL,$query);
                $invoiceTable= $invoiceTable."_items";
                $query	 = "UPDATE `$invoiceTable` SET`state` ='0'
                             WHERE invoice_No	='$invoice' AND storeid='$storeid'";
                $exe = mysqli_query($adController->MySQL,$query); 
                
                if ($otherTable)
                {                 
                    $query	 = "UPDATE `$otherTable` SET `state` ='0'
                             WHERE invoice_No='$invoice' AND storeid='$storeid'";
                    $exe = mysqli_query($adController->MySQL,$query);
                    $otherTable= $otherTable."_items";
                    $query	 = "UPDATE `$otherTable` SET`state` ='0'
                                 WHERE invoice_No='$invoice' AND storeid='$storeid'";
                    $exe = mysqli_query($adController->MySQL,$query); 
                    
                    $query	 = "UPDATE `journal` SET `state` ='0'
                             WHERE docNo='$invoice' ";
                    $exe = mysqli_query($adController->MySQL,$query);
                    $query	 = "UPDATE `journal_items` SET`state` ='0'
                                 WHERE reference='$invoice' ";
                    $exe = mysqli_query($adController->MySQL,$query);                     
                    
                }      
                echo '1';
		exit();
	}  
        function delete_trans()
	{
		$this->checkAuthorized();

		$invoice  	 = $this->encrypt_decrypt(2,urldecode($_REQUEST['id']),0);

                $storeid = $_SESSION['storeid'];
                
                $query	 = "UPDATE store_trans SET `state` ='0'
                         WHERE trans_number='$invoice' AND storeid='$storeid'";
                $exe = mysqli_query($adController->MySQL,$query);
                $query	 = "UPDATE store_trans_items SET`state` ='0'
                             WHERE trans_number='$invoice' AND storeid='$storeid'";
                $exe = mysqli_query($adController->MySQL,$query); 

//                    $tData     = mysqli_fetch_assoc(mysqli_query($adController->MySQL,"SELECT trans_number FROM store_trans WHERE  id='$idval' AND storeid='$storeid'"));
//                    $transNo   = $tData['trans_number'];
//                    $tData     = mysqli_fetch_assoc(mysqli_query($adController->MySQL,"SELECT id FROM income WHERE purchase_invoice= '$transNo' AND storeid='$storeid'"));
//                    $insertidIncome   = $tData['id'];
//                    $tData     = mysqli_fetch_assoc(mysqli_query($adController->MySQL,"SELECT id FROM outgo WHERE purchase_invoice= '$transNo' AND storeid='$storeid'"));
//                    $insertidOutgo   = $tData['id'];                    
                    
                    $query	 = "UPDATE income SET `state` ='0'
                             WHERE invoice_No='$invoice' AND storeid='$storeid'";
                    $exe = mysqli_query($adController->MySQL,$query);
                    $query	 = "UPDATE outgo SET `state` ='0'
                             WHERE invoice_No='$invoice' AND storeid='$storeid'";
                    $exe = mysqli_query($adController->MySQL,$query);                    
                    $query	 = "UPDATE `income_items` SET`state` ='0'
                                 WHERE invoice_No='$invoice' AND storeid='$storeid'";
                    $exe = mysqli_query($adController->MySQL,$query);                     
                     $query	 = "UPDATE `outgo_items` SET`state` ='0'
                                 WHERE invoice_No='$invoice' AND storeid='$storeid'";
                    $exe = mysqli_query($adController->MySQL,$query);          
                    
                echo '1';
		exit();
	}                
	function delete_journal()
	{
//		$this->checkAuthorized();

            $invoice = $this->encrypt_decrypt(2,urldecode($_REQUEST['no']),0);               

                
                $query	 = "UPDATE `journal` SET `state` ='0'
                         WHERE journalNo='$invoice' ";
                $exe = mysqli_query($adController->MySQL,$query);
                $query	 = "UPDATE `journal_items` SET`state` ='0'
                         WHERE journalNo='$invoice' '";
                $exe = mysqli_query($adController->MySQL,$query); 
                
   
                echo '1';
		exit();
	}  
	function deleteImages($dir,$table,$id)
	{
		
		$myDir	 = $this->getDirectoryOnlyPath($dir);
		$query 	 = "SELECT * FROM images WHERE `table`='$table' AND dir='$dir' AND foreign_id='$id'";
		$res	 = mysqli_query($adController->MySQL,$query);
		while($data = mysqli_fetch_assoc($res))
		{
			$fullPath_original 	= $myDir.$data['original'];
			$thumb_path		= $myDir.$data['thumb'];

			@unlink($fullPath_original);
			@unlink($thumb_path);
		}

		$del	 = "DELETE FROM images WHERE `table`='$table' AND dir='$dir' AND foreign_id='$id'";
		mysqli_query($adController->MySQL,$del);
		return;
	}


	function updateBranch()
	{
		$name		= trim($_POST['name']);
		$street		= trim($_POST['street']);
		$country	= trim($_POST['country']);
		$state		= trim($_POST['state']);
		$city		= trim($_POST['city']);
		$phone		= trim($_POST['phone']);
		$fax		= trim($_POST['fax']);
		$crnum		= trim($_POST['crnum']);
		$crapp		= trim($_POST['crapp']);
		$crexp		= trim($_POST['crexp']);
		$munum		= trim($_POST['munum']);
		$muapp		= trim($_POST['muapp']);
		$muexp		= trim($_POST['muexp']);
		$name_ar	= trim($_POST['name_ar']);
		$street_ar	= trim($_POST['street_ar']);
		$vat		= trim($_POST['vatnumber']);
		$branchid	= trim($_POST['nd']);

		if($crapp!="")
			$crapp	= $this->dateSlashesToHiphen($crapp);
		if($crexp!="")
			$crexp	= $this->dateSlashesToHiphen($crexp);
		if($muapp!="")
			$muapp	= $this->dateSlashesToHiphen($muapp);
		if($muexp!="")
			$muexp	= $this->dateSlashesToHiphen($muexp);


		$branchid	= $this->encrypt_decrypt(2,urldecode($branchid),0);
		$branchid 	= $this->encrypt_decrypt(2,$branchid,0);
		$branchid	= $this->encrypt_decrypt(2,$branchid,0);

		$error	= $this->branchValidation($name,$street,$country,$state,$city,$phone,$fax,$crnum,$crapp,$crexp,$munum,$muapp,$muexp,$name_ar,$street_ar);

		if(!$error)
		{
			$storeid = $_SESSION['storeid'];
			$query	 = "UPDATE `branches` SET
				`name_en` ='$name',
				`storeid` = '$storeid',
				`city` ='$city',
				`vatnumber`='$vat',
				`state`='$state' ,
				`street_en`='$street' ,
				`street_ar`='$street_ar' ,
				`country`='$country' ,
				`phone`='$phone' ,
				`fax` ='$fax',
				`name_ar`='$name_ar',
				`crnum`='$crnum',
				`crapp`='$crapp',
				`crexp`='$crexp',
				`munum`='$munum',
				`muapp`='$muapp',
				`muexp`='$muexp'
				
				 WHERE id='$branchid' AND storeid='$storeid'";

				$exe = mysqli_query($adController->MySQL,$query);
				if($exe)
				{
					$this->updateAllBranches();
					echo BRANCH_UPDATED_SUCCESSFULLY;
				}
				else
					echo ERROR_IN_UPDATING_BRANCH;

				$this->updateDataVersion();
		}
		else
			echo $error;
	}


	function branchValidation($name,$street,$country,$state,$city,$phone,$fax,$crnum,$crapp,$crexp,$munum,$muapp,$muexp,$name_ar,$street_ar)
	{
		$obj = new validation();
		if($name == "" && $name_ar =="")
			$error = NAME_REQUIRED;
		$obj->add_fields($street, 'req',STREET_REQUIRED);
		$obj->add_fields($street, 'street',STREET_INVALID);
		$obj->add_fields($country, 'req',COUNTRY_REQUIRED);
		$obj->add_fields($country, 'num',COUNTRY_IS_INVALID);
		$obj->add_fields($state, 'req',STATE_REQUIRED);
		$obj->add_fields($state, 'num',STATE_IS_INVALID);
		$obj->add_fields($city, 'req',CITY_REQUIRED);
		$obj->add_fields($city, 'num',CITY_IS_INVALID);
		$obj->add_fields($phone, 'req',PHONE_REQUIRED);
		$obj->add_fields($phone, 'num',PHONE_IS_INVALID);
		$obj->add_fields($fax, 'num',FAX_IS_INVALID);
		$obj->add_fields($crnum, 'alphanum',COMMERCIAL_REG_IS_INVALID);
		$obj->add_fields($munum, 'alphanum',MUNICIPAL_REG_IS_INVALID);
	
		$error.=$obj->validate();

		return $error;
	}

	function updateAllBranches()
	{
		$storeid 	= $_SESSION['storeid'];
		$query 		= "SELECT GROUP_CONCAT(id) AS lst FROM branches WHERE storeid='$storeid' ORDER BY name_en ASC";
		$res   		= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
		$data		= mysqli_fetch_assoc($res);
		$branch		= $data['lst'];

		$query		= "UPDATE categories SET store_branch ='$branch' WHERE allbranches='1' AND storeid='$storeid'";
		mysqli_query($adController->MySQL,$query);
		
		$query		= "UPDATE items SET store_branch ='$branch' WHERE allbranches='1' AND storeid='$storeid'";
		mysqli_query($adController->MySQL,$query);
	
	}
	////// END BRANCHES //////

	///// CATEGORIES //////

	function addCat()
	{
		$hash			= time()."_".$_SERVER['REMOTE_ADDR']."_".$_SESSION['storeid'];
		$hash			= $this->encrypt_decrypt(1, $hash,0);
		$name			= $_POST['name'];
		$name_ar		= $_POST['name_ar'];
		$branch			= $_POST['branch'];
		$show_to_cashier	= $_POST['show_to_cashier'];
		$enabled		= $_POST['enabled'];
		$storeid		= $_SESSION['storeid'];
		$allBranches		= '0';
		if(count($branch) > 0)
		{
			$allBranches		= '0';
			$query 	= "SELECT GROUP_CONCAT(id) AS lst FROM branches WHERE storeid='$storeid' AND id IN (".implode(',',$branch).") ORDER BY name_en ASC";
		}
		else 
		{
			$allBranches		= '1';
			$query 	= "SELECT GROUP_CONCAT(id) AS lst FROM branches WHERE storeid='$storeid' ORDER BY name_en ASC";
		}

		$res   	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
		$data	= mysqli_fetch_assoc($res);
		$branch	= $data['lst'];

		if($enabled == "")
			$enabled ="0";
		if($show_to_cashier == "")
			$show_to_cashier ="0";

		
		
		$obj = new validation();
		if($name == "" && $name_ar =="")
			$error = NAME_REQUIRED;

		if(!$error)
		{
			$array 	= $this->makeThumbnails(100,100,DIR_CAT_NAME);
			$query ="INSERT INTO  `categories` (
			`enabled` ,
			`name_en` ,
			`entry_person` ,
			`store_branch` ,
			`showtocashier` ,
			`name_ar` ,
			`storeid`,`hash`,`allbranches`)
			VALUES ( '$enabled',  '$name',  '$storeid','$branch','$show_to_cashier','$name_ar', '$storeid','$hash','$allBranches')";
			$exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
			$id  = mysqli_insert_id();
			if($exe)
			{
				for($i=0;$i<count($array['original']);$i++)
				{
					$original 	= $array['original'][$i];
					$thumb		= $array['thumb'][$i];
					$query = "INSERT INTO `images` (`foreign_id`, `original`, `thumb`, `table`, `dir`) VALUES ('$id', '$original', '$thumb', 'categories', '".DIR_CAT_NAME."')";
					mysqli_query($adController->MySQL,$query);
				}
				echo CATEGORY_ADDED_SUCCESSFULLY;
				$this->updateDataVersion();
			}	
			else
				echo ERROR_ADDING_CATEGORY;
			
		}
		else
 			echo $error;

	}


	function updateCat()
	{
		$name			= $_POST['name'];
		$name_ar		= $_POST['name_ar'];
		$branch			= $_POST['branch'];
		$show_to_cashier	= $_POST['show_to_cashier'];
		$enabled		= $_POST['enabled'];
		$catid			= $_POST['nd'];
		$storeid		= $_SESSION['storeid'];

		$catid			= $this->encrypt_decrypt(2,urldecode($catid),0);
		$catid 			= $this->encrypt_decrypt(2,$catid,0);
		$catid			= $this->encrypt_decrypt(2,$catid,0);

		$allBranches		= '0';
		if(count($branch) > 0)
		{
			$allBranches		= '0';
			$query 	= "SELECT GROUP_CONCAT(id) AS lst FROM branches WHERE storeid='$storeid' AND id IN (".implode(',',$branch).") ORDER BY name_en ASC";
		}
		else 
		{
			$allBranches		= '1';
			$query 	= "SELECT GROUP_CONCAT(id) AS lst FROM branches WHERE storeid='$storeid' ORDER BY name_en ASC";
		}

		$res   	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
		$data	= mysqli_fetch_assoc($res);
		$branch	= $data['lst'];

		if($enabled == "")
			$enabled ="0";
		if($show_to_cashier == "")
			$show_to_cashier ="0";

		

		$obj = new validation();
		if($name == "" && $name_ar =="")
			$error = NAME_REQUIRED;

		if(!$error)
		{
			$array 	= $this->makeThumbnails(100,100,DIR_CAT_NAME);
			$query ="UPDATE  `categories` SET
			`enabled` ='$enabled',
			`name_en` ='$name',
			`store_branch` ='$branch',
			`showtocashier` ='$show_to_cashier',
			`name_ar` ='$name_ar' ,allbranches='$allBranches' WHERE `storeid`='$storeid' AND id='$catid'";
			$exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
			if($exe)
			{
				
				if(count($array['original']) > 0)
				{
					$this->deleteImages(DIR_CAT_NAME,'categories',$catid);
					
					for($i=0;$i<count($array['original']);$i++)
					{
						$original 	= $array['original'][$i];
						$thumb		= $array['thumb'][$i];
						$query = "INSERT INTO `images` (`foreign_id`, `original`, `thumb`, `table`, `dir`) VALUES ('$catid', '$original', '$thumb', 'categories', '".DIR_CAT_NAME."')";
						mysqli_query($adController->MySQL,$query);
					}
				}
				echo CATEGORY_UPDATED_SUCCESSFULLY;
				$this->updateDataVersion();
			}	
			else
				echo ERROR_IN_UPDATING_CATEGORY;
			
		}
		else
 			echo $error;


	}

	///// END CATEGORIES ////


	/////////  ITEMS ///////////

        
        function BarcodeExist($barcode,$id)
        {
            		
            $storeid		= $_SESSION['storeid'];
            $query = "SELECT id FROM items WHERE barcode='$barcode' AND barcode!='' AND id !='$id' AND storeid ='$storeid'";
            $res	= mysqli_query($adController->MySQL,$query);
            $num	= mysqli_num_rows($res);
            if($num)
            {
                     $data	= mysqli_fetch_assoc($res);
                    echo "Barcode already exists ". $data['id'];
                    return false;
            }
            else return true;
         }


	function addMultiItem()
	{
		$name			= $_POST['name'];
		$name_ar		= $_POST['name_ar'];	
                $cat			= $_POST['cat'];
                $vat			= $_POST['vat'];
                $selt			= $_POST['selt'];
                $disc			= $_POST['disc'];  
		$barcode		= $_POST['qty-barcode'];
                $unit                   = $_POST['qty-unit'];
                $unitFill		= $_POST['qty-fill'];
		$price			= $_POST['qty-price'];
		$discount		= $_POST['qty-discountPer'];	
                $services               = $_POST['service'];
                $photo                  = $_POST['photo'];
                $enabled		= intval($_POST['enabled']);
                $taxInPrice		= intval($_POST['taxInPrice']);
                $Itype  		= intval($_POST['Itype']);
                $Qtype  		= intval($_POST['Qtype']);
                $taxInPrice		= intval($_POST['taxInPrice']);                
		$storeid		= $_SESSION['storeid'];
                $hashItem		= md5($_SERVER['REMOTE_ADDR']."_".$storeid."_".time());
		for($i=0;$i<count($barcode);$i++)
		{
			if(!$this->BarcodeExist($barcode[$i]))
                            exit();
		}

		$insertedItem = 0;
                
		for($i=0; $i<10;$i++)
		{
                    if($unit[$i] != "" )
                        {
                        
                    //allbranches,'$allBranches',
                    //`store_branch` ,'$branch', 
                        $query ="INSERT INTO `items`(
                                                    name_en ,
                                                    name_ar ,
                                                    catid ,
                                                    vat,
                                                    selt,
                                                    disc,
                                                    image,	
                                                    taxInPrice,
                                                    unit,
                                                    intimate_stock,
                                                    barcode,                                                
                                                    price ,
                                                    discount ,
                                                    storeid ,
                                                    enabled,
                                                    item_type,
                                                    measurement,
                                                    item_thread,                                                                                                
                                                    services
                                                    )
                                             VALUES (
                                                    '$name',
                                                    '$name_ar',    
                                                    '$cat',
                                                    '$vat',   
                                                    '$selt',
                                                    '$disc',  
                                                    '$photo',    
                                                    '$taxInPrice',
                                                    '$unit[$i]',
                                                    '$unitFill[$i]',
                                                    '$barcode[$i]',
                                                    '$price[$i]',  
                                                    '$discount[$i]',  
                                                    '$storeid',  
                                                    '$enabled',
                                                    '$Itype',
                                                    '$Qtype',
                                                    '$hashItem',
                                                    '$services'
                                                    )";
                                $exe = mysqli_query($adController->MySQL,$query);
                                if($exe)
                                {
                                        $insertedItem++;
                                }
                        }   
		}

		if($insertedItem > 0)
			echo $insertedItem;
		else
			echo ERROR_IN_ADDING_ITEM;
		$this->updateDataVersion();
	}
	

	function updateMultiItem()
	{
		$name			= $_POST['name'];
		$name_ar		= $_POST['name_ar'];
		$cat			= $_POST['cat'];
                $vat			= $_POST['vat'];
                $selt			= $_POST['selt'];
                $disc			= $_POST['disc'];  
                $barcode		= $_POST['qty-barcode'];
                $unit                   = $_POST['qty-unit'];
                $unitFill		= $_POST['qty-fill'];
		$price			= $_POST['qty-price'];
		$discount		= $_POST['qty-discountPer'];	
		$enabled		= intval($_POST['enabled']);
                $taxInPrice		= intval($_POST['taxInPrice']);
                $Itype  		= intval($_POST['Itype']);
                $Qtype  		= intval($_POST['Qtype']);                
		$idArray		= $_POST['itemsarray'];
		$hashofItem		= $_POST['nd'];
                $oldPhoto		= $_POST['oldPhoto'];
		$storeid		= $_SESSION['storeid'];	
		//$typeOfItem		= intval($_POST['type_of_item']);
		$services               = $_POST['service'];
		$hashofItem		= $this->encrypt_decrypt(2,urldecode($hashofItem),0);
		$hashofItem 		= $this->encrypt_decrypt(2,$hashofItem,0);
		$hashofItem		= $this->encrypt_decrypt(2,$hashofItem,0);
                $photo	        	= $_POST['oldPhoto'];
                if (isset($_POST['photo']))
                {
                    $photo                  = $_POST['photo'];
                }



		$arrayOfUpdates	= array();
		$insertedItem 	= 0;
		for($i=0; $i<count($unit);$i++)
		{
                    if($unit[$i] != "" )	
                        {
                        if($idArray[$i] != '')
				{
					//`store_branch`='$branch' ,allbranches='$allBranches',
					$idV	= $this->encrypt_decrypt(2,urldecode($idArray[$i]),0);
                                        if(!$this->BarcodeExist($barcode[$i],$idV))
                                             exit();
                                        
					$query 		="UPDATE `items` SET
                                                        name_en         ='$name' ,
                                                        name_ar         ='$name_ar' ,   
							catid           ='$cat',
                                                        vat             ='$vat',
                                                        selt            ='$selt',
                                                        disc            ='$disc', 
                                                        image           ='$photo',	
                                                        taxInPrice      ='$taxInPrice',
							unit            ='$unit[$i]',
							intimate_stock  ='$unitFill[$i]',
                                                        barcode         ='$barcode[$i]',							
    							price           ='$price[$i]',
							discount        ='$discount[$i]' ,
							storeid         ='$storeid' ,
							enabled         ='$enabled',
							item_type       ='$Itype',
                                                        measurement     ='$Qtype',   
							services        ='$services'
						 WHERE item_thread='$hashofItem' AND storeid='$storeid'  AND id='$idV'";
						$exe = mysqli_query($adController->MySQL,$query);
						if($exe)
						{
							$arrayOfUpdates[count($arrayOfUpdates)]	= $idV;
							$insertedItem++;
						}
				}
				else
				{
                                            //`store_branch` , allbranches,'$branch','$allBranches',
                        $query ="INSERT INTO `items`(
                                                    name_en ,
                                                    name_ar ,
                                                    catid ,
                                                    vat,
                                                    selt,
                                                    disc,
                                                    image,	
                                                    taxInPrice,
                                                    unit,
                                                    intimate_stock,
                                                    barcode,                                                
                                                    price ,
                                                    discount ,
                                                    storeid ,
                                                    enabled,
                                                    item_type,
                                                    measurement,
                                                    item_thread,                                                                                                
                                                    services
                                                    )
                                             VALUES (
                                                    '$name',
                                                    '$name_ar',    
                                                    '$cat',
                                                    '$vat',   
                                                    '$selt',
                                                    '$disc',  
                                                    '$photo',    
                                                    '$taxInPrice',
                                                    '$unit[$i]',
                                                    '$unitFill[$i]',
                                                    '$barcode[$i]',
                                                    '$price[$i]',  
                                                    '$discount[$i]',  
                                                    '$storeid',  
                                                    '$enabled',
                                                    '$Itype',
                                                    '$Qtype',
                                                    '$hashItem',
                                                    '$services'
                                                    )";
                                $exe = mysqli_query($adController->MySQL,$query);
							if($exe)
							{
 								$arrayOfUpdates[count($arrayOfUpdates)]	= mysqli_insert_id();
								$insertedItem++;
							}

				}
				
				
			}
		}

		if(count($arrayOfUpdates))
		{
			$arrayOfUpdates 	= implode(",",$arrayOfUpdates);
			$query			= "DELETE FROM items WHERE item_thread='$hashofItem' AND storeid='$storeid' AND id NOT IN ($arrayOfUpdates)";
			mysqli_query($adController->MySQL,$query); 
		}
		if($insertedItem > 0)
			echo $insertedItem;
		else
			echo ERROR_IN_UPDATING_ITEM."";
		

		$this->updateDataVersion();
		

	}


	function addEmp()
	{
		$name			= $_POST['name'];
		$name_ar		= $_POST['name_ar'];
		$branch			= $_POST['branch'];
		$store			= $_POST['store'];   
		$pwdVal			= $_POST['pass']; 
		$password 		= md5($pwdVal);//$aes->encrypt();                
                $user			= $_POST['user'];                 
		$email			= $_POST['email'];
		$mobile			= $_POST['mobile'];
		$type			= $_POST['type'];
                $till_control		= intval($_POST['till_control']);
                $show_report		= intval($_POST['show_report']);
                $allow_price_change	= intval($_POST['allow_price_change']);
                $allow_dis_change	= intval($_POST['allow_dis_change']);                
                $visa                   = intval($_POST['visa']);
                $mada           	= intval($_POST['mada']);                
		$passport_num		= $_POST['passport_num'];
		$passport_expiry	= $_POST['passport-expiry'];	
		$iqama_number		= $_POST['iqama_number'];
		$iqama_expiry		= $_POST['iqama-expiry'];
		$insurance_number	= $_POST['insurance_number'];
		$insurance_expiry	= $_POST['insurance-expiry'];
		$medical_number		= $_POST['medical_number'];
		$medical_expiry		= $_POST['medical-expiry'];
		$license_number		= $_POST['license_number'];
		$license_expiry		= $_POST['license-expiry'];
                $services               = implode(",",$_POST['service']);
                $services               = explode(",",$services);
                $services               = array_unique($services);
                $services               = implode(",",$services);

		$storeid		= $_SESSION['storeid'];

		$pic_path = "img/emp/";
		$pic_name = $_FILES["file"]['name'];
                //$pic_name = base64_encode($pic_name);
		$pic_tmp = $_FILES["file"]['tmp_name'];
                echo "<script> alert('$pic_name')</script>";

                $pic_dest= $pic_path.$pic_name;
                move_uploaded_file($pic_tmp,$pic_dest);
	

		
			$array 	= $this->makeThumbnails(100,100,DIR_EMP_NAME);
		
			if($passport_expiry != "")
				$passport_expiry	= $this->mysqlCompatible($passport_expiry);	
			if($iqama_expiry != "")
				$iqama_expiry		= $this->mysqlCompatible($iqama_expiry);
			if($insurance_expiry != "")
				$insurance_expiry	= $this->mysqlCompatible($insurance_expiry);
			if($medical_expiry != "")
				$medical_expiry		= $this->mysqlCompatible($medical_expiry);
			if($license_expiry != "")
				$license_expiry		= $this->mysqlCompatible($license_expiry);
                        
			$query ="INSERT INTO  `employees` (
				`name_en` ,
				`email` ,
				`password` ,
				`image` ,                                
       				`user` ,                         
				`contact` ,
				`store_branch` ,
                                `store` ,
				`type` ,
				`passport_num` ,
				`iqama_num` ,
				`insurance_num` ,
				`medical_num` ,
				`medical_expiry` ,
				`insurance_expiry` ,
				`passport_expiry` ,
				`iqama_expiry` ,
				`license_expiry` ,
				`license_num` ,
				`name_ar` ,
				`storeid`,
                                `services`,
                                `till_control`,
                                show_report,
                                allow_price_change,
                                allow_dis_change,
                                visa,
                                mada,
                                accCode
				)
				VALUES (
				'$name', "
                                . " '$email', "
                                . " '$password',"
                                . "'$pic_name','$user',  '$mobile',  '$branch','$store',  '$type',  '$passport_num',  '$iqama_number',  '$insurance_number',  '$medical_number',  '$medical_expiry',  '$insurance_expiry', 
				'$passport_expiry',  '$iqama_expiry',  '$license_expiry',  '$license_number',  '$name_ar',  '$storeid','$services','$till_control','$show_report','$allow_price_change','$allow_dis_change','$visa','$mada','$code')";
			$exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));


			if($exe)
			{
                            $id  = mysqli_insert_id();
                            $this->insertAccount($name,$name_ar,'','',28,0,$id);	
                            
//                            $this->mailFunction($email,"Account Created","Your account created with email $email and password $pwdVal");
                            for($i=0;$i<count($array['file']);$i++)
                            {
                                    $original 	= $array['file'][$i];
                                    $thumb		= $array['thumb'][$i];
                                    $query = "INSERT INTO `images` (`foreign_id`, `original`, `thumb`, `table`, `dir`) VALUES ('$id', '$original', '$thumb', 'employees', '".DIR_EMP_NAME."')";
                                    mysqli_query($adController->MySQL,$query);
                            }
                            echo EMPLOYEE_ADDED_SUCCESSFULLY;
                            $this->updateDataVersion();
			}	
			else
				echo ERROR_IN_ADDING_EMPLOYEE;
			
		
	}


	function updateEmp()
	{
            $name			= $_POST['name'];
            $name_ar		= $_POST['name_ar'];
            $branch			= $_POST['branch'];
            $store			= $_POST['store'];
            $email			= $_POST['email'];
             
            $user			= $_POST['user'];      
            
            $mobile			= $_POST['mobile'];
            $type			= $_POST['type'];
            $passport_num		= $_POST['passport_num'];
            $passport_expiry	= $_POST['passport-expiry'];	
            $iqama_number		= $_POST['iqama_number'];
            $iqama_expiry		= $_POST['iqama-expiry'];
            $insurance_number	= $_POST['insurance_number'];
            $insurance_expiry	= $_POST['insurance-expiry'];
            $medical_number		= $_POST['medical_number'];
            $medical_expiry		= $_POST['medical-expiry'];
            $license_number		= $_POST['license_number'];
            $license_expiry		= $_POST['license-expiry'];
            $itemId			= $_POST['nd'];
            $till_control		= intval($_POST['till_control']);
            $show_report		= intval($_POST['show_report']);
            $allow_price_change	= intval($_POST['allow_price_change']);
            $allow_dis_change	= intval($_POST['allow_dis_change']);                     
            $visa                   = intval($_POST['visa']);
            $mada           	= intval($_POST['mada']);                     
            $storeid		= $_SESSION['storeid'];	
            $services               = implode(",",$_POST['service']);
            $services               = explode(",",$services);
            $services               = array_unique($services);
            $services               = implode(",",$services);


            $itemId			= $this->encrypt_decrypt(2,urldecode($itemId),0);
            $itemId 		= $this->encrypt_decrypt(2,$itemId,0);
            $itemId			= $this->encrypt_decrypt(2,$itemId,0);

            if($this->emailPresentEmp($email,2,$itemId)>0)
            {
                    echo EMAIL_UNAVAILABLE;
                    exit();
            }

            $obj = new validation();
            if($name == "" && $name_ar =="")
                    $error = NAME_REQUIRED;
            if(!$error)
            {
                $array 	= $this->makeThumbnails(100,100,DIR_EMP_NAME);
                if($passport_expiry != "")
                        $passport_expiry	= $this->mysqlCompatible($passport_expiry);	
                if($iqama_expiry != "")
                        $iqama_expiry		= $this->mysqlCompatible($iqama_expiry);
                if($insurance_expiry != "")
                        $insurance_expiry	= $this->mysqlCompatible($insurance_expiry);
                if($medical_expiry != "")
                        $medical_expiry		= $this->mysqlCompatible($medical_expiry);
                if($license_expiry != "")
                        $license_expiry		= $this->mysqlCompatible($license_expiry);
                if($_POST['pass'])
                {
                                
                    $pwdVal			= $_POST['pass']; 
                    $password                   = md5($pwdVal);//$aes->encrypt();   
                    $query ="UPDATE  `employees`  SET 
                        `name_en` ='$name' ,
                        `email`  ='$email' ,
                        `user`  ='$user' ,
                        `password`='$password',
                        `contact` ='$mobile'  ,
                        `store_branch` ='$branch'  ,
                        `store` ='$store'  ,   
                        `type`  ='$type' ,
                        `passport_num` ='$passport_num'  ,
                        `iqama_num`  ='$iqama_number' ,
                        `insurance_num` ='$insurance_number'  ,
                        `medical_num`  ='$medical_number' ,
                        `medical_expiry`  ='$medical_expiry' ,
                        `insurance_expiry` ='$insurance_expiry'  ,
                        `passport_expiry` ='$passport_expiry'  ,
                        `iqama_expiry`  ='$iqama_expiry' ,
                        `license_expiry` ='$license_expiry'  ,
                        `license_num`  ='$license_number' ,
                        `services` = '$services',
                        `till_control` = '$till_control', 
                        show_report = '$show_report',
                        allow_price_change='$allow_price_change',
                        allow_dis_change='$allow_dis_change',     
                        visa = '$visa',
                        mada = '$mada',
                        `name_ar` ='$name_ar'  WHERE `storeid`='$storeid' AND id='$itemId'";
                }
                else
                {
                    $query ="UPDATE  `employees`  SET 
                        `name_en` ='$name' ,
                        `email`  ='$email' ,
                        `user`  ='$user' ,                            
                        `contact` ='$mobile'  ,
                        `store_branch` ='$branch'  ,
                        `store` ='$store'  ,   
                        `type`  ='$type' ,
                        `passport_num` ='$passport_num'  ,
                        `iqama_num`  ='$iqama_number' ,
                        `insurance_num` ='$insurance_number'  ,
                        `medical_num`  ='$medical_number' ,
                        `medical_expiry`  ='$medical_expiry' ,
                        `insurance_expiry` ='$insurance_expiry'  ,
                        `passport_expiry` ='$passport_expiry'  ,
                        `iqama_expiry`  ='$iqama_expiry' ,
                        `license_expiry` ='$license_expiry'  ,
                        `license_num`  ='$license_number' ,
                        `services` = '$services',
                        `till_control` = '$till_control', 
                        show_report = '$show_report',
                        allow_price_change='$allow_price_change',
                        allow_dis_change='$allow_dis_change',     
                        visa = '$visa',
                        mada = '$mada',
                        `name_ar` ='$name_ar'  WHERE `storeid`='$storeid' AND id='$itemId'";   
                }
                
                $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                if($exe)
                {
                    $this->updateAcount($name,$name_ar,28,$itemId);

                   // $this->mailFunction($email,"Account Updated by Admin","Your account  with email $email and password $pwdVal updated by the admin");

                    echo EMPLOYEE_UPDATED_SUCCESSFULLY;

                    $this->updateDataVersion();
                }	
                else
                        echo ERROR_IN_UPDATING_EMPLOYEE;
            }
            else
                    echo $error;

	}

	////// END EMPLOYEES ///////////////

	function dateSlashesToHiphen($dt)
	{
		$strtotime 	= strtotime($dt);
		return $date 	= date("Y-m-d H:i:s",$strtotime);
	}

	function encrypt_decrypt($action, $string,$constant) 
	{
		$converter = new Encryption();
		if($constant == 0)
		{
			if( $action == 1 ) 
				return $converter->encode($string);
			else if( $action == 2 )
				return $converter->decode($string);
		}
		else
		{
			if( $action == 1 ) 
				return $converter->encodeConstant($string);
			else if( $action == 2 )
				return $converter->decodeConstant($string);
		
		}
	}

	function checkAuthorized()
	{
		if(!isset($_SESSION['storeid']))
		{
			echo "Unauthorized Access";
			exit();
		}
	}


	function makeThumbnails($w,$h,$dirName)
	{
		$array	=array();
		$len	=$_POST['file_len'];
		$dir	=$this->getDirectoryFullPath($dirName);
		

		if(!file_exists($dir))
		{
			mkdir($dir);
			chmod($dir,0777);
		}
		for($i=0;$i<$len;$i++)
		{
			
			
			$fName=$_FILES['file_'.$i]['name'];
 			$image=$i."_".time()."_".$fName;
			$array["original"][$i] = $image;
			$img=$dir."/$image";
			$var=move_uploaded_file($_FILES['file_'.$i]['tmp_name'],$img) or die("error");
			if($var)
			{
				$resizeObj = new resize($dir."/$image");
				$resizeObj -> resizeImage($w,$h, 'crop');
				$imgVal=$i."_".time().".jpg";
				$imgwithDirVal=$dir."/".$imgVal;
				$resizeObj -> saveImage($imgwithDirVal, 100);
				if(file_exists($imgwithDirVal))
				{
					$array["thumb"][$i] = $imgVal;
				}
			}


		}

		return $array;
	}

	function getDirectoryFullPath($dirName)
	{
		$md5		=$this->encrypt_decrypt(1,$_SESSION['storeid']."dir",1);
		$dir		=IMAGE_DIRECTORY.$dirName."/".$md5;

		return $dir;
	}


	function getDirectoryOnlyPath($dirName)
	{
		$md5		=$this->encrypt_decrypt(1,$_SESSION['storeid']."dir",1);
		$dir		="../images/".$dirName."/".$md5."/";

		return $dir;
	}

	function dec()
	{
		return $this->encrypt_decrypt(2, $_REQUEST['s'],0); 
	}


	function getBranchCategories()
	{
		$language	= $_SESSION['lang'];
		$storeid	= $_SESSION['storeid'];
		$bid   		= $_REQUEST['bid'];
		if($bid != "")
		{
			$output = array();
			if($bid != "" && $bid != "null")
				$query 	= "SELECT * FROM categories WHERE storeid='$storeid' AND FIND_IN_SET('$bid',store_branch) OR store_branch IN ($bid) ORDER BY name_en ASC";
			else
				$query 	= "SELECT * FROM categories WHERE storeid='$storeid' ORDER BY name_en ASC";
			echo $query;
			$res   		= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
			while($data= mysqli_fetch_assoc($res))
			{
				$id   = $data['id'];
				$name = $data['name_'.$language];
				$output[count($output)] = "<option value='$id'>$name</option>";
	
			}
			echo implode(" ",$output);
			exit();
		}
	}


	function mysqlCompatible($dated)
	{
		$dt 	= explode('/',$dated);
		$d	= $dt[2]."-".$dt[0]."-".$dt[1]." 00:00:00";
		return $d;
	}

//	function mailFunction($email,$subject,$msg)
//	{
//		mail($email,$subject,$msg);
//	}

	function getDateForUpdate($v)
	{
		if($v == "" || $v == "0000-00-00 00:00:00" )
			echo "";
		else
			echo date("m/d/Y",strtotime($v));
	}

	function getDateValue($v2)
	{
		$v = explode("/",$v);
		$v = $v[2]."-".$v[0]."-".$v[1];	
		return $v2;
	}

	function addExpenditure()
	{
		$desc		= $_POST['desc_en'];
		$desc_ar	= $_POST['desc_ar'];
		$cost		= $_POST['cost'];
		$branch		= $_POST['branch'];
		$dated		= $_POST['dated'];

		$storeid	= $_SESSION['storeid'];

		$obj = new validation();
		$obj->add_fields($desc, 'req',DESCRIPTION_CANNOT_BE_BLANK);
		$obj->add_fields($cost, 'req',COST_CANNOT_BE_BLANK);
		$obj->add_fields($dated, 'req',DATE_CANNOT_BE_BLANK);
		$obj->add_fields($desc_ar, 'is_arabic',ARABIC_DESC_NOT_IN_ARABIC);

		$error=$obj->validate();
		if(!$error)
		{
			$dated	= $this->mysqlCompatible($dated);
			$query="INSERT INTO `expenditure` (`store_branch`, `dated`, `desc_ar`, `cost`, `storeid`, `desc_en`) VALUES ('$branch', '$dated', '$desc_ar', '$cost', '$storeid', '$desc')";
			$exe = mysqli_query($adController->MySQL,$query);
			if($exe)
				echo COST_ADDED_SUCCESSFULLY;
			else
				echo ERROR_ADDING_COST; 


		}
		else
			echo $error;

	}

	function updateExpenditure()
	{
		$desc		= $_POST['desc_en'];
		$desc_ar	= $_POST['desc_ar'];
		$cost		= $_POST['cost'];
		$branch		= $_POST['branch'];
		$dated		= $_POST['dated'];
		$catid		= $_POST['nd'];
		$storeid	= $_SESSION['storeid'];

		$catid			= $this->encrypt_decrypt(2,urldecode($catid),0);
		$catid 			= $this->encrypt_decrypt(2,$catid,0);
		$catid			= $this->encrypt_decrypt(2,$catid,0);

		$obj = new validation();
		$obj->add_fields($desc, 'req',DESCRIPTION_CANNOT_BE_BLANK);
		$obj->add_fields($cost, 'req',COST_CANNOT_BE_BLANK);
		$obj->add_fields($dated, 'req',DATE_CANNOT_BE_BLANK);
		$obj->add_fields($desc_ar, 'is_arabic',ARABIC_DESC_NOT_IN_ARABIC);

		$error=$obj->validate();
		if(!$error)
		{
			$dated	= $this->mysqlCompatible($dated);
			$query="UPDATE `expenditure` SET `store_branch`='$branch', `dated`='$dated', `desc_ar`='$desc_ar', `cost`='$cost', `desc_en`='$desc' WHERE id='$catid' AND storeid='$storeid' ";
			$exe = mysqli_query($adController->MySQL,$query);
			if($exe)
				echo COST_UPDATED_SUCCESSFULLY;
			else
				echo ERROR_UPDATING_COST; 

		}
		else
			echo $error;

	}

	function discountAdd()
	{
		$discount	= $_POST['amount'];
		$branch		= $_POST['branch'];
		$storeid	= $_SESSION['storeid'];
		$minamount	= $_POST['minamt'];

		$obj = new validation();
		$obj->add_fields($discount, 'req',AMOUNT_CANNOT_BE_BLANK);
		$obj->add_fields($minamount, 'req',MIN_AMOUNT_CANNOT_BE_BLANK);

		if(intval($discount) > intval($minamount))
		{
			echo DISCOUNT_GR8R_THN_MIN;
			exit();
		}

		if(!$error)
		{
			$query	 	= "SELECT * FROM discount WHERE branchid='$branch' AND storeid='$storeid'";
			$res		= mysqli_query($adController->MySQL,$query);
			$dataDiscount	= mysqli_fetch_assoc($res);
			$num		= mysqli_num_rows($res);
			if(!$num)
				$query = "INSERT INTO discount(amount,branchid,storeid,minamount) VALUES('$discount','$branch','$storeid','$minamount')";
			else
				$query = "UPDATE discount SET amount='$discount',minamount='$minamount' WHERE branchid='$branch' AND storeid='$storeid'";

			$exe   = mysqli_query($adController->MySQL,$query);
			if($exe)
				echo DISCOUNT_UPDATED_SUCCESSFULLY;

			else
				echo ERROR_IN_UPDATING_DISCOUNT;
				

			$this->updateDataVersion();

		}
		else
			echo $error;

	}
        
	function updateDelivery()
	{
			$discount	= $_REQUEST['amount'];
	$branch		= $_POST['branch'];
	$storeid	= $_SESSION['storeid'];
	$minamount	= $_POST['minamt'];
			
			$query	 	= "SELECT * FROM delivery WHERE  storeid='$storeid'";
			$res		= mysqli_query($adController->MySQL,$query);
			$dataDiscount	= mysqli_fetch_assoc($res);
			$num		= mysqli_num_rows($res);
			if(!$num)
					$query = "INSERT INTO delivery(amount,branchid,storeid,minamount) VALUES('$discount','$branch','$storeid','$minamount')";
			else
					$query = "UPDATE delivery SET amount='$discount',minamount='$minamount' WHERE branchid='$branch' AND storeid='$storeid'";

			echo $exe   = mysqli_query($adController->MySQL,$query);
		   
		   
			$this->updateDataVersion();
	}

       	function updatepalDate()
	{
			$date           = $_REQUEST['date'];
                        $date        = explode("/",$date);
                        $date        = $date[2]."-".$date[1]."-".$date[0]." 00:00:00";

                        $storeid	= $_SESSION['storeid'];
			
			$query	 	= "SELECT * FROM open_palance_date WHERE  storeid='$storeid'";
			$res		= mysqli_query($adController->MySQL,$query);
			$dataDiscount	= mysqli_fetch_assoc($res);
			$num		= mysqli_num_rows($res);
			if(!$num)
					$query = "INSERT INTO open_palance_date(date,storeid) VALUES('$date','$storeid')";
			else
					$query = "UPDATE open_palance_date SET date='$date' WHERE  storeid='$storeid'";

			if ($exe   = mysqli_query($adController->MySQL,$query))
                                $state = 1;
                        $query = "UPDATE open_palance SET date='$date' WHERE  storeid='$storeid'";
                        if ($exe   = mysqli_query($adController->MySQL,$query))
                                $state = 1;
                        echo $state;		   
			$this->updateDataVersion();
	}

	function vatAdd()
	{
			$discount	= $_REQUEST['percentage'];
			$storeid	= $_SESSION['storeid'];
			
			$query	 	= "SELECT * FROM vat_default WHERE  storeid='$storeid'";
			$res		= mysqli_query($adController->MySQL,$query);
			$dataDiscount	= mysqli_fetch_assoc($res);
			$num		= mysqli_num_rows($res);
			if(!$num)
					$query = "INSERT INTO vat_default(percentage,storeid) VALUES('$discount','$storeid')";
			else
					$query = "UPDATE vat_default SET percentage='$discount' WHERE  storeid='$storeid'";
			echo $exe   = mysqli_query($adController->MySQL,$query);
		   
			$this->updateDataVersion();
	}

	function discountUpdate()
	{
		$discount	= $_POST['amount'];
		$branch		= $_POST['branch'];
		$storeid	= $_SESSION['storeid'];
		$minamount	= $_POST['minamt'];
		$idval		= $this->encrypt_decrypt(2,urldecode($_POST['vd']),0);

		$obj = new validation();
		$obj->add_fields($discount, 'req',AMOUNT_CANNOT_BE_BLANK);
		$obj->add_fields($minamount, 'req',MIN_AMOUNT_CANNOT_BE_BLANK);
	
		if(intval($discount) > intval($minamount))
		{
			echo DISCOUNT_GR8R_THN_MIN;
			exit();
		}

		if(!$error)
		{
			$query = "UPDATE discount SET amount='$discount',branchid='$branch',minamount='$minamount' WHERE id='$idval'";
			$exe   = mysqli_query($adController->MySQL,$query);
			if($exe)
				echo DISCOUNT_UPDATED_SUCCESSFULLY;

			else
				echo ERROR_IN_UPDATING_DISCOUNT;

			$this->updateDataVersion();
				

		}
		else
			echo $error;

	}


	function tableUpdate()
	{
		$table		= $_POST['table'];
		$branch		= $_POST['branch'];
		$storeid	= $_SESSION['storeid'];

		$obj = new validation();
		$obj->add_fields($table, 'req',TABLE_COUNT_CANNOT_BE_BLANK);

		$error=$obj->validate();
		if(!$error)
		{
			$query 	= "SELECT id FROM tables_count WHERE storeid='$storeid'";
			$res	= mysqli_query($adController->MySQL,$query);
			$num	= mysqli_num_rows($res);
			if($num)
				$query="UPDATE `tables_count` SET `table_c`='$table', `branch`='$branch' WHERE storeid='$storeid'";
			else
				$query="INSERT INTO `tables_count`(`table_c`, `storeid`) VALUES('$table','$storeid') ";

			$exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
			if($exe)
				echo TABLE_UPDATED_SUCCESSFULLY;
			else
				echo ERROR_UPDATING_TABLE; 

			$this->updateDataVersion();

		}
		else
			echo $error;

	}

	function getUsersCount()
	{
		$storeid= $_SESSION['storeid'];
		$query 	= "SELECT COUNT(*) AS allc FROM employees WHERE storeid='$storeid'";
		$res	= mysqli_query($adController->MySQL,$query);
		$data	= mysqli_fetch_assoc($res);
		echo 	intval($data['allc']);
		
	}

	function getSales()
	{
		$storeid= $_SESSION['storeid'];
		$query 	= "SELECT SUM(cost) AS allc FROM sales WHERE storeid='$storeid'";
		$res	= mysqli_query($adController->MySQL,$query);
		$data	= mysqli_fetch_assoc($res);
		echo 	round(floatval($data['allc']),2);
	}

	function getItemsCount()
	{
		$storeid= $_SESSION['storeid'];
		$query 	= "SELECT COUNT(*) AS allc FROM items WHERE storeid='$storeid'";
		$res	= mysqli_query($adController->MySQL,$query);
		$data	= mysqli_fetch_assoc($res);
		echo 	intval($data['allc']);
	}

	function getExpenditure()
	{
		$storeid= $_SESSION['storeid'];
		$query 	= "SELECT SUM(cost) AS allc FROM expenditure WHERE storeid='$storeid'";
		$res	= mysqli_query($adController->MySQL,$query);
		$data	= mysqli_fetch_assoc($res);
		echo 	round(floatval($data['allc']),2);
	}


	function updateDataVersion()
	{
		$storeid	= $_SESSION['storeid'];
		$query 		= "UPDATE dataversion SET dbversion=dbversion+1 WHERE storeid='$storeid'";
		$res		= mysqli_query($adController->MySQL,$query);

	}
        
        function deleteData()
        {
            $storeid	= $_SESSION['storeid'];
            $conditionSel   = base64_decode($_REQUEST['c']);
            if(intval($storeid) > 0 )
            {

                $data   = trim( $_REQUEST['d'],",");
                $data   = explode(",", $data);
                for($i=0;$i<count($data);$i++)
                {
                    $data[$i] = " o.itemid='$data[$i]' ";
                }


                $data   = implode(" OR ",$data);

                $data   = " itemid IN($_REQUEST[d]) ";
                $conditionSel = str_replace("o.","",$conditionSel);

                $query  = "INSERT INTO orders_deleted SELECT o.* FROM orders o WHERE $conditionSel AND $data";
                                    $res    = mysqli_query($adController->MySQL,$query);

                $query  = "DELETE FROM orders  WHERE $conditionSel AND $data";
                $res    = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                if($res)
                {
                        echo DATA_DELETED_SUCCESSFULLY;
                }
                else
                        echo ERROR_UPDATING_TABLE;

                exit();
            }
        }

        
            function addService()
            {
                    $name			= $_POST['name'];
                    $name_ar                    = $_POST['name_ar'];
                    $storeid                    = $_SESSION['storeid'];
                    
                    $obj = new validation();
                    if($name == "" && $name_ar =="")
                            $error = NAME_REQUIRED;

                    if(!$error)
                    {
                            $query ="INSERT INTO  `services` (
                            `name_en` ,
                            `name_ar` ,
                            `storeid`)
                            VALUES ( '$name',  '$name_ar','$storeid')";
                            $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                            $id  = mysqli_insert_id();
                            
                            if($exe)
                            {
                                    echo SERVICE_ADDED_SUCCESSFULLY;
                                    $this->updateDataVersion();
                            }	
                            else
                                    echo ERROR_ADDING_SERVICES;

                    }
                    else
                            echo $error;
            }

            function updateService()
            {
                    $name			= $_POST['name'];
                    $name_ar                    = $_POST['name_ar'];
                    $storeid                    = $_SESSION['storeid'];

                    $service			= $_POST['id'];
                    $obj = new validation();
                    if($name == "" && $name_ar =="")
                            $error = NAME_REQUIRED;

                    if(!$error)
                    {
                            
                            $query ="UPDATE  `services` SET
                            `name_en` ='$name',
                            `name_ar` ='$name_ar'  WHERE `storeid`='$storeid' AND id='$service'";
                            $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                            if($exe)
                            {
                                    echo SERVICE_UPDATED_SUCCESSFULLY;
                                    $this->updateDataVersion();
                            }	
                            else
                                    echo ERROR_IN_UPDATING_SERVICE;

                    }
                    else
                            echo $error;

            }
        
        
        
        function shiftsUpdate()
        {
            $storeid    = $_SESSION['storeid'];
            $idv        = $_POST['id'];
            $name_en    = $_POST['name_en'];
            $name_ar    = $_POST['name_ar'];
            $from       = $_POST['from_time'];
            $to         = $_POST['to_time'];
            
            
            for($i=0 ; $i<count($idv) ; $i++)
            {
                
                $nameEn     = $name_en[$i];
                $nameAr     = $name_ar[$i];
                $idValue    = $idv[$i];
                $start      = $from[$i];
                $ends       = $to[$i];                
                
                if($nameEn != '' || $nameAr !='')
                {
                    if(intval($idValue) > 0)
                        $query  = "UPDATE shifts SET starts='$start', ends='$ends',name_en='$nameEn',name_ar='$nameAr' WHERE storeid='$storeid' AND id='$idValue' ";
                    else
                        $query  = "INSERT INTO shifts(storeid,name_en,name_ar,starts,ends) VALUES('$storeid','$nameEn','$nameAr','$start','$ends')";

                    mysqli_query($adController->MySQL,$query);
                }
                
                
            }
            
           echo "1";
        }
        
function storesUpdate()
        {
            $storeid    = $_SESSION['storeid'];
            $idv        = $_POST['id'];
            $name_en    = $_POST['name_en'];
            $name_ar    = $_POST['name_ar'];
            $branch    = $_POST['branch'];

            
            
            for($i=0 ; $i<count($idv) ; $i++)
            {
                
                $nameEn     = $name_en[$i];
                $nameAr     = $name_ar[$i];
                $idValue    = $idv[$i];
                $brch       = $branch[$i];

                    if($nameEn != '' || $nameAr !='')
                    {
                        //echo $brch."<br>";
                        if (!$brch)
                            {
                            
                            exit("branch not null");
                            }
                        if(intval($idValue) > 0)
                        {                                                        
                            $query  = "UPDATE stores SET name_en='$nameEn',name_ar='$nameAr',branch='$brch' WHERE storeid='$storeid' AND id='$idValue' ";
                            mysqli_query($adController->MySQL,$query);
                            
                            $this->updateAcount($nameEn,$nameAr,37,$idValue);
                            $this->updateAcount($nameEn,$nameAr,40,$idValue);
                            $this->updateAcount($nameEn,$nameAr,47,$idValue);

                        }
                        else
                        {

                            $query  = "INSERT INTO stores(storeid,name_en,name_ar, branch ) VALUES('$storeid','$nameEn','$nameAr','$brch')";
                            mysqli_query($adController->MySQL,$query);
                            $id      = mysqli_insert_id();   
                            $this->insertAccount($nameEn,$nameAr,'','',37,0,$id);
                            $this->insertAccount($nameEn,$nameAr,'','',40,0,$id);
                            $this->insertAccount($nameEn,$nameAr,'','',47,0,$id);

                        }
                        mysqli_query($adController->MySQL,$query);
                    }
                    else if (intval($idValue) > 0 )
                    {
                        $query      = "SELECT * FROM stores WHERE id='$idValue' ";
                        $res        = mysqli_query($adController->MySQL,$query);
                        $dataCat    = mysqli_fetch_assoc($res);
                        if ($dataCat['default']=='0')
                        {
                            //echo "yesssss";
                            $query  = "DELETE FROM `stores` WHERE id='$idValue' ";
                            mysqli_query($adController->MySQL,$query);
                            $this->deleteAcount (37,$idValue);
                            $this->deleteAcount (40,$idValue);
                            $this->deleteAcount (47,$idValue);
                                    
                        }
                    }
            }
            
           echo "1";
        }
        function jobTitleUpdate()
        {
            $storeid    = $_SESSION['storeid'];
            $idv        = $_POST['id'];
            $name_en    = $_POST['name_en'];
            $name_ar    = $_POST['name_ar'];

            for($i=0 ; $i<count($idv) ; $i++)
            {
                
                $nameEn     = $name_en[$i];
                $nameAr     = $name_ar[$i];
                $idValue    = $idv[$i];
                
                if($nameEn != '' || $nameAr !='')
                {
                    if(intval($idValue) > 0)
                        $query  = "UPDATE jobtitle SET name_en='$nameEn',name_ar='$nameAr' WHERE storeid='$storeid' AND id='$idValue' ";
                    else
                        $query  = "INSERT INTO jobtitle(storeid,name_en,name_ar) VALUES('$storeid','$nameEn','$nameAr')";

                    mysqli_query($adController->MySQL,$query);
                }
               else if (intval($idValue) > 0 )
                {
                    $query      = "SELECT * FROM jobtitle WHERE id='$idValue' ";
                    $res        = mysqli_query($adController->MySQL,$query);
                    $dataCat    = mysqli_fetch_assoc($res);
                    if ($dataCat['default']=='0')
                    {
                        //echo "yesssss";
                        $query  = "DELETE FROM `jobtitle` WHERE id='$idValue' ";
                        mysqli_query($adController->MySQL,$query);
                    }
                }
                
            }
            
           echo "1";
            
        }

        
        function unitsUpdate()
        {
            $storeid    = $_SESSION['storeid'];
            $idv        = $_POST['id'];
            $name_en    = $_POST['name_en'];
            $name_ar    = $_POST['name_ar'];

            for($i=0 ; $i<count($idv) ; $i++)
            {
                
                $nameEn     = $name_en[$i];
                $nameAr     = $name_ar[$i];
                $idValue    = $idv[$i];
                
                if($nameEn != '' || $nameAr !='')
                {
                    if(intval($idValue) > 0)
                        $query  = "UPDATE units SET name_en='$nameEn',name_ar='$nameAr' WHERE storeid='$storeid' AND id='$idValue' ";
                    else
                        $query  = "INSERT INTO units(storeid,name_en,name_ar) VALUES('$storeid','$nameEn','$nameAr')";

                    mysqli_query($adController->MySQL,$query);
                }
               else if (intval($idValue) > 0 )
                {
                    $query      = "SELECT * FROM units WHERE id='$idValue' ";
                    $res        = mysqli_query($adController->MySQL,$query);
                    $dataCat    = mysqli_fetch_assoc($res);
                    if ($dataCat['default']=='0')
                    {
                        //echo "yesssss";
                        $query  = "DELETE FROM `units` WHERE id='$idValue' ";
                        mysqli_query($adController->MySQL,$query);
                    }
                }
                
            }
            
           echo "1";
            
        }

        function flbsUpdate()
        {
//            $storeid    = $_SESSION['storeid'];
            $idv        = $_POST['id'];
            $code        = $_POST['code'];                
            $name_en    = $_POST['name_en'];
            $name_ar    = $_POST['name_ar'];

            for($i=0 ; $i<count($idv) ; $i++)
            {
                $nameEn     = $name_en[$i];
                $nameAr     = $name_ar[$i];
                $codeI       = $code[$i];
                $idValue    = $idv[$i];
             
                if($nameEn != '' || $nameAr !='')
                {
                    if(intval($idValue) > 0)
                        $query  = "UPDATE budget_set SET name_en='$nameEn',name_ar='$nameAr',code='$codeI' WHERE id='$idValue' ";
                    else
                        $query  = "INSERT INTO budget_set (name_en,name_ar,code) VALUES('$nameEn','$nameAr','$codeI')";

                    mysqli_query($adController->MySQL,$query);
                }
               else if (intval($idValue) > 0 )
                {

                        $query  = "DELETE FROM `budget_set` WHERE id='$idValue' ";
                        mysqli_query($adController->MySQL,$query);
                }
                
            }
           echo "1";
        }        
        
        function addSupplier()
        {
            $name_en        = $_REQUEST['name'];
            $name_ar        = $_REQUEST['name_ar'];
            $com_reg        = $_REQUEST['com_reg'];
            $tax_no         = $_REQUEST['tax_no'];
            $phone          = $_REQUEST['phone'];
            $email          = $_REQUEST['email'];
            $address        = $_REQUEST['address'];

            $storeid        = $_SESSION['storeid'];
            
            $query          = "SELECT * FROM suppliers WHERE name_en = '$name_en' OR name_ar='$name_ar'";
            $res            = mysqli_query($adController->MySQL,$query);
            $num            = mysqli_num_rows($res);
            if(!$num)
            {

                
                $query      = "INSERT INTO suppliers "
                                    . "(name_en ,  name_ar ,  com_reg , tax_no  ,  phone , email  ,address, storeid,accCode) "
                            . "VALUES('$name_en','$name_ar','$com_reg','$tax_no','$phone','$email','$address','$storeid','$code')";
                $res   = mysqli_query($adController->MySQL,$query);
                $id  = mysqli_insert_id();
                $this->insertAccount($name_en,$name_ar,'','',5,0,$id);
                echo $res;
            }
            else
                echo SUPPLIER_ALREADY_EXISTS;
        }
        
        function updateSupplier()
	{
	    $suppId	    = $_POST['nd'];
            $name_en        = $_REQUEST['name'];
            $name_ar        = $_REQUEST['name_ar'];
            $com_reg        = $_REQUEST['com_reg'];
            $tax_no         = $_REQUEST['tax_no'];
            $phone          = $_REQUEST['phone'];
            $email          = $_REQUEST['email'];
            $address        = $_REQUEST['address'];

            $storeid        = $_SESSION['storeid'];

            if($this->emailPresentSupp($email,$suppId)>0)
            {
                    echo EMAIL_UNAVAILABLE;
                    exit();
            }

            
            $query ="UPDATE `suppliers`  SET 
                    `name_en` ='$name_en' ,
                    `name_ar` ='$name_ar' ,  
                    `com_reg` ='$com_reg' , 
                    `tax_no`  ='$tax_no' , 
                    `phone`   ='$phone' , 
                    `email`   ='$email' ,
                    `address` ='$address' 
                    WHERE `storeid`='$storeid' AND `id`='$suppId'";
            $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            $this->updateAcount($name_en,$name_ar,5,$suppId);

            
                        if($exe) echo '1';
                        else echo ERROR_IN_ADDING_ITEM;
        }		

        function addCustomer()
        {
            $name_en        = $_REQUEST['name'];
            $name_ar        = $_REQUEST['name_ar'];
            $tax_no         = $_REQUEST['tax_no'];
            $address        = $_REQUEST['address'];
            $phone          = $_REQUEST['phone'];
            $email          = $_REQUEST['email'];
            
            $storeid        = $_SESSION['storeid'];
            
            if($this->isExsist('customers','tax_no',$tax_no, '')>0)
            {
                    echo TAXNO_UNAVAILABLE;
                    exit();
            } 
            if($this->isExsist('customers','phone',$phone, '')>0)
            {
                    echo PHONE_UNAVAILABLE;
                    exit();
            } 
            if($this->isExsist('customers','email',$email, '')>0)
            {
                    echo EMAIL_UNAVAILABLE;
                    exit();
            }     
            

            $query      = "INSERT INTO customers"
                                    . "(name_en ,  name_ar ,  tax_no , address  ,  phone , email  , storeid) "
                            . "VALUES('$name_en','$name_ar','$tax_no','$address','$phone','$email','$storeid')";
            $res   = mysqli_query($adController->MySQL,$query)or die(mysqli_error($adController->MySQL));
            $id  = mysqli_insert_id();
            $this->insertAccount($name_en,$name_ar,'','',1,0,$id);
            echo $res;            
    }

        function updateCustomer()
	{
	    $custId	    = $_POST['nd'];
            $name_en        = $_REQUEST['name'];
            $name_ar        = $_REQUEST['name_ar'];
            $tax_no         = $_REQUEST['tax_no'];
            $address        = $_REQUEST['address'];
            $phone          = $_REQUEST['phone'];
            $email          = $_REQUEST['email'];
            $storeid        = $_SESSION['storeid'];

            if($this->isExsist('customers','tax_no',$tax_no, $custId)>0)
            {
                    echo TAXNO_UNAVAILABLE;
                    exit();
            } 
            if($this->isExsist('customers','phone',$phone, $custId)>0)
            {
                    echo PHONE_UNAVAILABLE;
                    exit();
            } 
            if($this->isExsist('customers','email',$email, $custId)>0)
            {
                    echo EMAIL_UNAVAILABLE;
                    exit();
            } 

            
            $query ="UPDATE `customers`  SET 
                    `name_en` ='$name_en' ,
                    `name_ar` ='$name_ar' ,  
                    `address` ='$address' , 
                    `tax_no`  ='$tax_no' , 
                    `phone`   ='$phone' , 
                    `email`   ='$email' 
                    WHERE `storeid`='$storeid' AND `id`='$custId'";
            $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            $this->updateAcount($name_en,$name_ar,1,$custId);
                        if($exe) echo '1';
                        else echo ERROR_IN_ADDING_ITEM;
        }		
        function updateSettings()
	{
	    $setid	    = $_POST['nd'];
            $name_en        = $_REQUEST['name'];
            $name_ar        = $_REQUEST['name_ar'];
            $tax_no         = $_REQUEST['tax_no'];
            $smoking        = $_REQUEST['smoking'];     
            $enableNegative        = $_REQUEST['enableNegative'];     
            $address        = $_REQUEST['address'];
            $phone          = $_REQUEST['phone'];
            $email          = $_REQUEST['email'];
            $activity       = $_REQUEST['activity'];
            $logo           = $_REQUEST['logo'];
            $site           = $_REQUEST['site'];
            $storeid        = $_SESSION['storeid'];

            if($this->isExsist('main_settings','tax_no',$tax_no,'',0)>0)
            {
                    echo TAXNO_UNAVAILABLE;
                    exit();
            } 
            if($this->isExsist('main_settings','phone',$phone,'',0)>0)
            {
                    echo PHONE_UNAVAILABLE;
                    exit();
            } 
            if($this->isExsist('main_settings','email',$email,'',0)>0)
            {
                    echo 'EMAIL_UNAVAILABLE';
                    exit();
            } 
            $query		= "SELECT * FROM main_settings WHERE storeid='$storeid' ";
            $res	= mysqli_query($adController->MySQL,$query);
            $num	= mysqli_num_rows($res);
            if($num)
            $query ="UPDATE `main_settings`  SET 
                    `name_en` ='$name_en' ,
                    `name_ar` ='$name_ar' ,  
                    `address` ='$address' , 
                    `tax_no`  ='$tax_no' , 
                    `phone`   ='$phone' , 
                    `email`   ='$email', 
                    `activity`='$activity',
                    `logo`    ='$logo',
                    `smoking` ='$smoking',     
                    `enableNegative` ='$enableNegative',     
                    `site`    ='$site'
                    WHERE `storeid`='$storeid'";
            else
            $query  = "INSERT INTO main_settings 
                    (  name_en ,  name_ar ,  address ,  tax_no ,  phone ,  email ,  activity ,  logo ,smoking,  site ,  storeid) 
                    VALUES
                    ('$name_en','$name_ar','$address','$tax_no','$phone','$email','$activity','$logo','$smoking','$site','$storeid')"; 
            
            $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                        if($exe) echo '1';
                        else echo ERROR_IN_ADDING_ITEM;
        }
        function updateQSettings()
	{
	    //$setid	    = $_POST['nd'];
            $barcode        = intval($_REQUEST['barcode']);
            $unit           = intval($_REQUEST['unit']);
            $disc           = intval($_REQUEST['disc']);
            $img            = intval($_REQUEST['img']);
            $vatV           = intval($_REQUEST['vatValue']);
            $vatR           = intval($_REQUEST['vatRate']);
            $storeid        = $_SESSION['storeid'];

            $query		= "SELECT * FROM quote_settings WHERE storeid='$storeid' ";
            $res	= mysqli_query($adController->MySQL,$query);
            $num	= mysqli_num_rows($res);
            if($num)
            $query ="UPDATE `quote_settings`  SET 
                    `barcode` ='$barcode' ,
                    `unit` ='$unit' ,  
                    `disc` ='$disc' , 
                    `img`  ='$img' , 
                    `vat_rate`   ='$vatR' , 
                    `vat_value`   ='$vatV'
                    WHERE `storeid`='$storeid'";
            else
            $query  = "INSERT INTO quote_settings 
                    (  barcode ,  unit ,  disc ,  img ,  vat_rate ,  vat_value ,  storeid) 
                    VALUES
                    ('$barcode','$unit','$disc','$img','$vatR','$vatV' ,'$storeid')"; 
            
            $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                        if($exe) echo '1';
                        else echo ERROR_IN_ADDING_ITEM;
        }        
        
        function getQtyOfItems()
        {
            $thread  = $_GET['it'];
            
            $str     = "<option></option>";
            $name     = "name_".$_SESSION['lang'];
            $query   = "SELECT items.unit, units.$name  FROM items, units WHERE items.item_thread='$thread' AND items.unit=units.id ";
            $res     = mysqli_query($adController->MySQL,$query);
            while($data = mysqli_fetch_assoc($res))
            {
                $str .= "<option value='$data[id]'>$data[$name]</option>";
            }
            echo $str;
        }
        
  //-----------------------------------------------------------    
       function getItemName($itemId) 
       {
           $language 	= $_SESSION['lang'];
           $queryCode  = "SELECT name_$language AS name FROM items WHERE id = '$itemId' ";
           $resCode	= mysqli_query($adController->MySQL,$queryCode);
           $dataCode   = mysqli_fetch_assoc($resCode);
           $name       =  $dataCode['name'];   
           return $name;
       }
        
       function getItemBalance ()
       {
           $item    = $_GET['item'];
           $store   = $_GET['store'];
           $old     = $_GET['old'];
           $balance = $this->getBalanceItem($item,$store,$old);
           echo $balance;
       }
       function getBalanceItem($item,$store,$old )
       {
           $storeid        = $_SESSION['storeid'];       
        
           $queryCode  = "SELECT item_thread FROM items WHERE id = '$item' ";
            $resCode	= mysqli_query($adController->MySQL,$queryCode);
            $dataCode   = mysqli_fetch_assoc($resCode);
            $thred     =  $dataCode['item_thread']; 
            
            $queryItems   = "SELECT i.id AS id, i.intimate_stock AS noOfQty "
                    . "FROM items i "
                    . "WHERE i.item_thread = '$thred' ORDER BY i.intimate_stock DESC" ;
            $resItem  =    mysqli_query($adController->MySQL,$queryItems) or die(mysqli_error($adController->MySQL));        //echo $queryAll."<br>";hhh
            while($dataItem = mysqli_fetch_assoc($resItem))
            {
                $itemsListArray[count($itemsListArray)] = $dataItem;
                $itemId = $dataItem['id'];  
                $noOfQty = $dataItem['noOfQty'];
               
                if ($item == $itemId) $itemNoOfQty = $noOfQty;
                
                $query  = "SELECT o.quantity AS qty "
                    . "FROM open_palance o "
                    . "WHERE  o.itemid = '$itemId' "
                    . "AND o.store = '$store' AND o.storeid='$storeid'";

                $res    = mysqli_query($adController->MySQL,$query);
                while($data = mysqli_fetch_assoc($res))
                {
                   $qty     = $data['qty'];
                   $total   += $qty*$noOfQty;
                }      
                
                $query = "SELECT o.quantity AS qty "
                    . "FROM income_items o "
                    . "WHERE  o.itemid = '$itemId' "
                    . "AND o.store = '$store' AND o.storeid='$storeid' AND state = '1'";
                $res = mysqli_query($adController->MySQL,$query);               
                while($data = mysqli_fetch_assoc($res))
                {
                   $qty     = $data['qty'];
                   $total   += $qty*$noOfQty;
                }  

                $query = "SELECT o.quantity AS qty "
                        . "FROM outgo_items o "
                        . "WHERE  o.itemid = '$itemId' "
                    . "AND o.store = '$store' AND o.storeid='$storeid' AND state = '1'";
                $res = mysqli_query($adController->MySQL,$query);
                while($data = mysqli_fetch_assoc($res))
                {
                   $qty     = $data['qty']*-1;
                   $total   += $qty*$noOfQty;
                }            
                
            }
            $palance = intval($total/$itemNoOfQty);
            return $palance + $old;
       }
               
        function getNewInvoiceNo($table,$field,$type,$branch)
        {
            $query  = "SELECT $field AS No FROM $table ORDER BY SUBSTRING($field, -5) DESC LIMIT 1";
            $res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            $num	= mysqli_num_rows($res);
            $data	= mysqli_fetch_assoc($res);
            if($num)
            {
                $rest = substr($data['No'], -5);  
                $trans_No = intval($rest)+1;
            }
            else 
                $trans_No = 1;
            
            $trans_No   =   $type.
                            str_pad($_SESSION['storeid'],3,"0",STR_PAD_LEFT).  // store id 
                            str_pad($branch,2,"0",STR_PAD_LEFT). // branch id 
                            '00'.  // user id 
                            str_pad($trans_No,5,"0",STR_PAD_LEFT);    
            return $trans_No;
        }             
        
        function getExistInvoiceNo($table,$fieldReq,$fieldGiven,$invoice)
        {
            $queryCode  = "SELECT $fieldReq AS no FROM $table WHERE $fieldGiven = '$invoice' ";
            $resCode	= mysqli_query($adController->MySQL,$queryCode);
            $dataCode   = mysqli_fetch_assoc($resCode);
            $code       =  $dataCode['no']; 
            return $code;
        }
        
        function getInvoiceTime($table,$invoice)
        {
            $queryCode  = "SELECT invoice_date FROM $table WHERE invoicenumber = '$invoice' ";
            $resCode	= mysqli_query($adController->MySQL,$queryCode);
            $dataCode   = mysqli_fetch_assoc($resCode);
            $time       = $dataCode['invoice_date']; 
            $time       = date("h:i:s",strtotime($time));
            return $time;
        }
        
        function resetInvoice($invoice,$invoiceTable,$otherTable)
        {
            $query	 = "DELETE FROM ".$invoiceTable." WHERE invoicenumber='$invoice' ";
            $exe = mysqli_query($adController->MySQL,$query);
            $query	 = "DELETE FROM ".$invoiceTable."_items WHERE invoice_No ='$invoice' ";
            $exe = mysqli_query($adController->MySQL,$query); 
            
            $query	 = "DELETE FROM ".$otherTable." WHERE invoice_No='$invoice' ";
            $exe = mysqli_query($adController->MySQL,$query);
            $query	 = "DELETE FROM ".$otherTable."_items WHERE invoice_No ='$invoice' ";
            $exe = mysqli_query($adController->MySQL,$query); 

            $query	 = "DELETE FROM journal WHERE docNo='$invoice' ";
            $exe = mysqli_query($adController->MySQL,$query);
            $query	 = "DELETE FROM journal_items WHERE reference='$invoice' ";
            $exe = mysqli_query($adController->MySQL,$query);        
        }

//--------------------------------------------------    
        function chickSales()
        {
            $result = 1 ;
            $customer       = $_POST['customer'];          
            if($customer == "" )
            {
                $result =  SUPPLIER_COMPULSORY;
            }   
              $branch         = $_POST['branch'];
            if( $branch == "")
            {
                $result=  BRUNCH_COMPULSORY;
            }          
            
            $itemsArray     = $_POST['barcode'];
            $atleastOne     = 0;
            $state          = '';
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $qty        = $_POST['item_qty'][$i];
                if( $itemId != '' && $qty != '' )
                {
                    $atleastOne = 1;
                    
                    $store      = $_POST['store'][$i];  
                    $oldQty     = ($_POST['item_old'][$i]) ? $_POST['item_old'][$i] : 0 ;
                    $balance = $this->getBalanceItem($itemId,$store , $oldQty);  
                    //echo "<script> alert('$balance'; </script>";
                    if( $balance < $qty )
                    {
                        $itemName = $this->getItemName($itemId);
                        $state.=  $itemName.' '. HAS_BALANCE.' : '.$balance.'<br>';  
                    }   
                }
            }      
            if(!$atleastOne)
            {
                $result =  NO_ITEM_ENTERED;
            }             
            if($state)
            {
                $result =  $state;
            }  
            
  
            return $result;
        }     

        function setSales($invoice,$time,$outgo_No,$journalNo)
        {
            $storeid            =   $_SESSION['storeid'];
            $branch             =   $_POST['branch'];
            $customer           =   $_POST['customer'];
            $invoiceSup         =   $_POST['document'];
            $itemsArray         =   $_POST['barcode'];
            $final_discount     =   $_POST['final_discount'];
            $gross_total        =   $_POST['gross_total'];
            $vat_all            =   $_POST['vat_all'];
            $deliver            =   $_POST['deliver'];            
            $totalAll           =   $_POST['totalAll']; 
            $cash = 0;  $visa =0;  $mada  =0;
            $payType            = 0;   

            $cash=   $_POST['cash'];
            $visa=   $_POST['visa'];
            $mada=   $_POST['mada'];
            $left=   $_POST['left'];                
            
            if($left == 0) $payType = 1; 

            
            $pExpiry            = $_POST['purchase_expiry'];
            $pExpiry            = $pExpiry.' '.$time;
            $allDebit           =   0;  // اجمالى الجانب المدين
            $allCredit          =   0;  // اجمالى الجانب الدائن
            
             $query       = "INSERT INTO sales(invoicenumber,   branch,   customer,   document      ,invoice_date, gross_total,   vat_all,   delver,    dis_added,       all_total,  storeid, paymentType,cash,mada,visa , `left`) VALUES"
                                   . "('$invoice',     '$branch', '$customer','$invoiceSup', '$pExpiry',  '$gross_total','$vat_all','$deliver','$final_discount','$totalAll','$storeid', '$payType','$cash','$mada','$visa','$left')";
            $exe         = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));                         

            
            $queryOutgo    = "INSERT INTO outgo (outgo_No     ,branch    ,invoice_type, Beneficiary, invoice_No, date,      dis_added        , all_total ,storeid) VALUES"
                                             . "('$outgo_No','$branch' ,'SALES'  , '$customer', '$invoice', '$pExpiry','$final_discount','$totalAll','$storeid')";
            $exe            = mysqli_query($adController->MySQL,$queryOutgo);               

                        
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $store      = $_POST['store'][$i];
                $qty        = $_POST['item_qty'][$i];
                $price      = $_POST['item_price'][$i];
                $disc       = $_POST['item_disc'][$i];
                $tax        = $_POST['item_vat'][$i];   
                $itemTotal  = $_POST['item_total_after_dis'][$i];   
                
                if($store != '' && $itemId != '' && $qty != '' && $price != '')
                {
                    $query      = "INSERT INTO sales_items ( itemid  , quantity , cost   , discount , tax  , store  , invoice_No , storeid) VALUES"
                                                           . "('$itemId','$qty'    ,'$price','$disc'   ,'$tax','$store','$invoice'  ,'$storeid')";
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL)); 
                    $query      = "INSERT INTO outgo_items ( itemid   , quantity, cost   , discount, tax,    store  , outgo_No,  invoice_No   ,invoice_Type ,date , storeid) VALUES"
                                                        . "( '$itemId','$qty'   ,'$price','$disc'  , '$tax','$store','$outgo_No','$invoice','SALES','$pExpiry','$storeid')";
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));                     
                    
                    $code = $this->getAcctCode('stores',47,$store); // get account ccode of store at Revenue from sales account
                    $this->insertJurnalItem($journalNo,$branch,$code,$itemTotal,0,"قيمة بي صنف بفاتورة بيع   $invoice",$invoice,'',$pExpiry);  // اضافة قيد دتئن لحسباب المبيعات  بقيمة ثمن  كل صنف
                    $allCredit = $allCredit + $itemTotal;
                }     
           }
           $this->insertJurnalItem($journalNo,$branch,'acc029',$vat_all,0," قيمة الضريبة المستحقه على فاتورة بع $invoice",$invoice,'',$pExpiry); // اضافة قيد دائن لحساب ضريبة القيمة المضافة
           $allCredit = $allCredit + $vat_all;
           if ($deliver>0)
           {
               $this->insertJurnalItem($journalNo,$branch,'acc073',$deliver,0," قيمة خدمة التوصيل على فاتورة بع $invoice",$invoice,'',$pExpiry); // اضافة قيد دائن لحساب التوصيل
               $allCredit = $allCredit + $deliver;  
           }
           
            $discCode = $this->getAcctCode('customers',34,$customer);  //get account code for Discount allowed of customer
            $this->insertJurnalItem($journalNo,$branch,$discCode,0,$final_discount,"قيمة خصم مسموح به على فاتورة بيع $invoice",$invoice,'',$pExpiry); // اضافة قيد مدين على  حساب الخصم المسموح به  المرتبط بالعميل
            $allDebit = $allDebit + $final_discount;
            
            $custCode = $this->getAcctCode('customers',1,$customer);  // get account code for finantil acount of customer
            $this->insertJurnalItem($journalNo,$branch,$custCode,0,$totalAll," القيمة المسستحقة على العميل بفاتورة بيع$invoice ",$invoice,'',$pExpiry); //اضافة قيد مدين  على العميل  بالمبلغ المستحق على الفاتورة
            $allDebit = $allDebit + $totalAll;
            
            $totalpaid = $cash + $visa + $mada;
            if ($totalpaid > 0 )
            {
            $this->insertJurnalItem($journalNo,$branch,$custCode,$totalpaid,0,"المبلغ المسدد من العميل  مقابل فاتورة بيع $invoice",$invoice,'',$pExpiry); // اضافة قيد دائن  ل حساب العميل  بالمبل الذى سدده
            $allCredit = $allCredit + $totalpaid;
            }
            if ($cash > 0)
            {
            $this->insertJurnalItem($journalNo,$branch,'acc007',0,$cash,"المبلغ المحصل نقدا  سدادا من قيمة فاتورة بيع$invoice",$invoice,'',$pExpiry); // اضافة قيد مدين  على حساب  الصندوق لسداد ثمن الفاتورة
            $allDebit = $allDebit + $cash;
            }
            if ($visa > 0)
            {
            $this->insertJurnalItem($journalNo,$branch,'acc074',0,$visa,"المبلغ المحصل  بالفيزا  سدادا من قيمة فاتورة بيع$invoice",$invoice,'',$pExpiry); // اضافة قيد مدين  على حساب الفيزا  لسداد ثمن الفاتورة
            $allDebit = $allDebit + $visa;
            }
            if ($mada > 0)
            {
            $this->insertJurnalItem($journalNo,$branch,'acc075',0,$mada,"المبلغ المحصل  بالمدى  سدادا من قيمة فاتورة بيع$invoice",$invoice,'',$pExpiry); // اضافة قيد مدين  على حساب المدى  لسداد ثمن الفاتورة
            $allDebit = $allDebit + $mada;
            }                
            $this->insertJurnal($journalNo,$invoice,"SALES",$branch,'',$allDebit, $allCredit,$pExpiry);
            ?>


                <?php
        }        

        function addSale()
        {
            $chick = $this->chickSales();
            if ($chick ==1)
            {
                $branch      =   $_POST['branch'];
                $invoice     =   $this->getNewInvoiceNo("sales","invoicenumber",'11',$branch);     
                $outgo_No    =   $this->getNewInvoiceNo("outgo","outgo_No",'32',$branch);    
                $journalNo   =   $this->getNewInvoiceNo("journal","journalNo",'40',$branch);   
                $time        =   date("h:i:s");
               $this->setSales($invoice,$time,$outgo_No,$journalNo);
               $response['invoice']    = $invoice;
               $response['success']    = "1";
            }
            else
            {
               $response['error']    = $chick;
               $response['success']    = "0";
            }
            echo json_encode($response);

        }        
        function editSale()
        {
            $chich = $this->chickSales();
            if ($chich ==1)
            {                      
                $branch      =   $_POST['branch'];
                $invoice     =   $_POST['invoice'];
                $outgo_No   =   $this->getExistInvoiceNo('outgo','outgo_No','invoice_No',$invoice);    
                $journalNo   =   $this->getExistInvoiceNo('journal','journalNo','docNo',$invoice); 
                $time        =   $this->getInvoiceTime('sales',$invoice);
                $this->resetInvoice($invoice,'sales','outgo');
                $this->setSales($invoice,$time,$outgo_No,$journalNo);
                $response['invoice']    = $invoice;
               $response['success']    = "1";
            }
            else
            {
               $response['error']    = $chick;
               $response['success']    = "0";
            }
            echo json_encode($response);

        }   

//--------------------------------------------------     

        function chickSaleRet()
        {
            $result = 1 ;       
            $itemsArray     = $_POST['barcode'];
            $atleastOne     = 0;
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $qty        = $_POST['item_qty'][$i];
                if( $itemId != '' && $qty != '' )
                {
                    $atleastOne = 1;
                    break;
                }
            }
            if(!$atleastOne)
            {
                $result =  NO_ITEM_ENTERED;
            }   

            return $result;
        }
        
        function setPSaleRet($invoice,$time,$income_No,$journalNo)
        {
            $storeid            = $_SESSION['storeid'];
            $saleInvoice        = $_POST['saleInvoice']; 
            $branch             = $_POST['branch'];
            $customer           = $_POST['customer'];
            $document          = $_POST['document'];
            $payType            = $_POST['payType'];           
            $itemsArray         = $_POST['barcode'];
            $final_discount     =   $_POST['final_discount'];
            $gross_total        =   $_POST['gross_total'];
            $vat_all            =   $_POST['vat_all'];
            $totalAll           =   $_POST['totalAll']; 
            $pExpiry            = $_POST['purchase_expiry'];
            $pExpiry            = explode("/",$pExpiry);
            $pExpiry            = $pExpiry[2]."-".$pExpiry[1]."-".$pExpiry[0].' '.$time;
            $allDebit           =   0;  // اجمالى الجانب المدين
            $allCredit          =   0;  // اجمالى الجانب الدائن
            
            
             $query       = "INSERT INTO sales_ret(invoicenumber,salesInvoice,   branch,   customer,   document,invoice_date, paymentType,gross_total,   vat_all,   dis_added,        all_total,  storeid"
                                           . ")VALUES('$invoice',  '$saleInvoice','$branch','$customer','$document',    '$pExpiry',  '$payType', '$gross_total','$vat_all','$final_discount','$totalAll','$storeid')";
            $exe         = mysqli_query($adController->MySQL,$query);
              
            $queryOutgo = "INSERT INTO income (income_No,   branch,    invoice_type,         Beneficiary, invoice_No, date,      dis_added        , all_total ,storeid) "
                                   . "VALUES('$income_No','$branch' ,'RETURNED_SALES'  , '$customer', '$invoice', '$pExpiry','$final_discount','$totalAll','$storeid')";
            $exe         = mysqli_query($adController->MySQL,$queryOutgo);
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
               
                $store      = $_POST['store'][$i];
                $qty        = $_POST['item_qty'][$i];
                //echo "erte ".$qty;
                $price      = $_POST['item_price'][$i];
                $disc       = $_POST['item_disc'][$i];
                $tax        = $_POST['item_vat'][$i]; 
                $itemTotal  = $_POST['item_total_after_dis'][$i];   
                if($store != '' && $itemId != '' && $qty != '' && $price != '')
                {
                    
                    $query      = "INSERT INTO sales_ret_items ( itemid  , quantity , cost   , discount , tax  , store  , invoice_No , storeid) VALUES"
                                                           . "( '$itemId','$qty'    ,'$price','$disc'   ,'$tax','$store','$invoice'  ,'$storeid')";
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL)); 
                    
                    $query      = "INSERT INTO income_items ( itemid   , quantity, cost   , discount, tax,    store  , income_No,  invoice_No   ,invoice_Type ,date , storeid) VALUES"
                                                        . "( '$itemId','$qty'   ,'$price','$disc'  , '$tax','$store','$income_No','$invoice','RETURNED_SALES','$pExpiry','$storeid')";
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL)); 
                    
                    $code = $this->getAcctCode('stores',47,$store); // get account code of store
                    $this->insertJurnalItem($journalNo,$branch,$code,0,$itemTotal,"قيمة الصنف بفاتورة مرتجع بيع $invoice",$invoice,'',$pExpiry);  // اضافة قيد مدين على المبيعات بقيمة ثمن كل صنف
                    $allDebit = $allDebit + $itemTotal;
                }     
           }
           $this->insertJurnalItem($journalNo,$branch,'acc029',0,$vat_all,"الضريبة المسترجعه فاتورة مرتجع بيع $invoice",$invoice,'',$pExpiry); // اضافة قيد مدين  على  ضريبة القيمة المضافة
           $allDebit = $allDebit +  $vat_all;
            
            $discCode = $this->getAcctCode('customers',34,$customer);  //get account code for Discount allowed of customer
            $this->insertJurnalItem($journalNo,$branch,$discCode,$final_discount,0,"المسترد من الخصم المسموح به مرتجع بيع $invoice",$invoice,'',$pExpiry); // اضافة قيد دائن ل  حساب الخصم المسموح به المرتبط بالعميل
            $allCredit = $allCredit + $final_discount;
            
            $custCode = $this->getAcctCode('customers',1,$customer);  // get account code for finantil acount of customer
            $this->insertJurnalItem($journalNo,$branch,$custCode,$totalAll,0," المبلغ المطلوب سداده  للعميل  مرتجع بيع $invoice ",$invoice,'',$pExpiry); //اضافة قيد دائن لحساب العميل بالمبلغ المستحق على الفاتورة
            $allCredit = $allCredit + $totalAll;
            
            if ($payType==1)
            {
                $this->insertJurnalItem($journalNo,$branch,$custCode,0,$totalAll,"استلام القيمة المستحقة للعمل مرتجع بيع $invoice",$invoice,'',$pExpiry); // اضافة قيد مدين على حساب العميل بالمبلغ المسدد
                $allDebit = $allDebit + $totalAll;
                
                $this->insertJurnalItem($journalNo,$branch,'acc007',$totalAll,0,"سداد القيمة المستحقة للعمل مرتجع بيع $invoice",$invoice,'',$pExpiry); // اضافة قيد دائن لحساب الصندوق لسداد ثمن الفاتورة
                $allCredit = $allCredit + $totalAll;
            }  
            $this->insertJurnal($journalNo,$invoice,"RETURNED_SALES",$branch,'',$allDebit, $allCredit,$pExpiry);
        }
        
        function addSaleRet()
        {
            $chick = $this->chickSaleRet();
            if ($chick ==1)
            {
                $branch      =   $_POST['branch'];
                $invoice     =   $this->getNewInvoiceNo("sales_ret","invoicenumber",'12',$branch);     
                $income_No   =   $this->getNewInvoiceNo("income","income_No",'31',$branch);    
                $journalNo   =   $this->getNewInvoiceNo("journal","journalNo",'40',$branch);   
                $time        =   date("h:i:s");
                $this->setPSaleRet($invoice,$time,$income_No,$journalNo);
               $response['invoice']    = $invoice;
               $response['success']    = "1";
            }
            else
            {
               $response['error']    = $chick;
               $response['success']    = "0";
            }
            echo json_encode($response);
        }        

        function editSaleRet()
        {
            $chich = $this->chickSaleRet();

            if ($chich ==1)
            {       
                $branch      =   $_POST['branch'];
                $invoice     =   $_POST['invoice'];

                $income_No   =   $this->getExistInvoiceNo('income','income_No','invoice_No',$invoice);    
                $journalNo   =   $this->getExistInvoiceNo('journal','journalNo','docNo',$invoice); 
                $time        =   $this->getInvoiceTime('sales_ret',$invoice);
                
                $this->resetInvoice($invoice,'sales_ret','income');
                $this->setPSaleRet($invoice,$time,$income_No,$journalNo);
               $response['invoice']    = $invoice;
               $response['success']    = "1";
            }
            else
            {
               $response['error']    = $chick;
               $response['success']    = "0";
            }
            echo json_encode($response);
        }          
                
//------------------------------------------------        
        function chickPurchase()
        {
            $result = 1 ;
             $supplier       = $_POST['supplier'];           
            if($supplier == "" )
            {
                $result =  SUPPLIER_COMPULSORY;

            }   
              $branch         = $_POST['branch'];
            if( $branch == "")
            {
                $result=  BRUNCH_COMPULSORY;

            }          
            $itemsArray     = $_POST['barcode'];
            $atleastOne     = 0;
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $qty        = $_POST['item_qty'][$i];
                if( $itemId != '' && $qty != '' )
                {
                    $atleastOne = 1;
                    break;
                }
            }
            if(!$atleastOne)
            {
                $result =  NO_ITEM_ENTERED;

            }   

            return $result;
        }
                
        function setPurchase($invoice,$time,$income_No,$journalNo)
        {
            $storeid            = $_SESSION['storeid'];
            $branch             = $_POST['branch'];
            $supplier           = $_POST['supplier'];
            $invoiceSup         = $_POST['supplier_invoice'];
            $payType            = $_POST['payType'];           
            $itemsArray         = $_POST['barcode'];
            $final_discount     =   $_POST['final_discount'];
            $gross_total        =   $_POST['gross_total'];
            $vat_all            =   $_POST['vat_all'];
            $totalAll           =   $_POST['totalAll']; 
            $pExpiry            = $_POST['purchase_expiry'];
            $pExpiry            = explode("/",$pExpiry);
            $pExpiry            = $pExpiry[2]."-".$pExpiry[1]."-".$pExpiry[0].' '.$time;
            $allDebit           =   0;  // اجمالى الجانب المدين
            $allCredit          =   0;  // اجمالى الجانب الدائن
             $query       = "INSERT INTO purchase(invoicenumber,branch,supplier,supplier_invoice,invoice_date,paymentType,gross_total,vat_all,dis_added,all_total,storeid"
                                      . ")VALUES('$invoice','$branch','$supplier','$invoiceSup','$pExpiry','$payType','$gross_total','$vat_all','$final_discount','$totalAll','$storeid')";
            $exe         = mysqli_query($adController->MySQL,$query);
            
            $queryIncome = "INSERT INTO income (income_No     ,branch    ,invoice_type, Beneficiary, invoice_No, date,      dis_added        , all_total ,storeid) "
                                     . "VALUES('$income_No','$branch' ,'PURCHASES'  , '$supplier', '$invoice', '$pExpiry','$final_discount','$totalAll','$storeid')";
            $exe         = mysqli_query($adController->MySQL,$queryIncome);
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $store      = $_POST['store'][$i];
                $qty        = $_POST['item_qty'][$i];
                $price      = $_POST['item_price'][$i];
                $disc       = $_POST['item_disc'][$i];
                $tax        = $_POST['item_vat'][$i]; 
                $itemTotal  = $_POST['item_total_after_dis'][$i];   
                if($store != '' && $itemId != '' && $qty != '' && $price != '')
                {
                    $query      = "INSERT INTO purchase_items ( itemid  , quantity , cost   , discount , tax  , store  , invoice_No , storeid) VALUES"
                                                           . "( '$itemId','$qty'    ,'$price','$disc'   ,'$tax','$store','$invoice'  ,'$storeid')";
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL)); 
                    
                    $query      = "INSERT INTO income_items ( itemid   , quantity, cost   , discount, tax,    store  , income_No,  invoice_No   ,invoice_Type ,date , storeid) VALUES"
                                                        . "( '$itemId','$qty'   ,'$price','$disc'  , '$tax','$store','$income_No','$invoice','PURCHASES','$pExpiry','$storeid')";
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL)); 
                    
                    $code = $this->getAcctCode('stores',40,$store); // get account code of store
                    $this->insertJurnalItem($journalNo,$branch,$code,0,$itemTotal,"تكلفة  صنف بفاتورة شراء $invoice",$invoice,'',$pExpiry);  // اضافة قيد مدين على حسباب المخازن بقيمة تكلفة كل صنف
                    $allDebit = $allDebit + $itemTotal;
                }     
           }
           
           $this->insertJurnalItem($journalNo,$branch,'acc029',0,$vat_all," الضريبة المستحقة لفاتورة شراء رقم $invoice",$invoice,'',$pExpiry); // اضافة قيد مدين على حساب ضريبة القيمة المضافة
            $allDebit = $allDebit + $vat_all;

            $discCode = $this->getAcctCode('suppliers',64,$supplier);  //get account code for Acquired discount of supplier
            $this->insertJurnalItem($journalNo,$branch,$discCode,$final_discount,0," خصم مكتسب لفاتورة شراء رقم $invoice",$invoice,'',$pExpiry); // اضافة قيد دائن لحساب الخصم المكتسب المرتبط بالمورد
            $allCredit = $allCredit + $final_discount;

            $suppCode = $this->getAcctCode('suppliers',5,$supplier);  // get account code for finantil acount of supplier
            $this->insertJurnalItem($journalNo,$branch,$suppCode,$totalAll,0," المستحق للمورد بفاتورة الشراء $invoice",$invoice,'',$pExpiry); //اضافة قيد دائن لحساب المورد بالمبلغ المستحق على الفاتورة
            $allCredit = $allCredit + $totalAll;
            if ($payType==1)
            {
                $this->insertJurnalItem($journalNo,$branch,$suppCode,0,$totalAll,"استلام ثمن فاتورة الشراء $invoice",$invoice,'',$pExpiry); // اضافة قيد ممدين على حساب المورد لتحصيل قيمة الفاتورة
                $allDebit = $allDebit + $totalAll;

                $this->insertJurnalItem($journalNo,$branch,'acc007',$totalAll,0,"سداد المستحق للمورد من فاتورة الشراء ",$invoice,'',$pExpiry); // اضافة قيد دائن لحساب الصندوق لسداد ثمن الفاتورة
                $allCredit = $allCredit + $totalAll;
            }  
            $this->insertJurnal($journalNo,$invoice,"PURCHASES",$branch,'',$allDebit, $allCredit,$pExpiry);
        }
                        
        function addPurchase()
        {
            $chick = $this->chickPurchase();
            if ($chick ==1)
            {
                $branch      =   $_POST['branch'];
                $invoice     =   $this->getNewInvoiceNo("purchase","invoicenumber",'21',$branch);     
                $income_No   =   $this->getNewInvoiceNo("income","income_No",'31',$branch);    
                $journalNo   =   $this->getNewInvoiceNo("journal","journalNo",'40',$branch);   
                $time        =   date("h:i:s");
               $this->setPurchase($invoice,$time,$income_No,$journalNo);
               $response['invoice']    = $invoice;
               $response['success']    = "1";
            }
            else
            {
               $response['error']    = $chick;
               $response['success']    = "0";
            }
            echo json_encode($response);
        }
        function editPurchase()
        {
            $chich = $this->chickPurchase();
            if ($chich ==1)
            {       
                $branch      =   $_POST['branch'];
                $invoice     =   $_POST['invoice'];
                $income_No   =   $this->getExistInvoiceNo('income','income_No','invoice_No',$invoice);    
                $journalNo   =   $this->getExistInvoiceNo('journal','journalNo','docNo',$invoice); 
                $time        =   $this->getInvoiceTime('purchase',$invoice);
                
                $this->resetInvoice($invoice,'purchase','income');
                $this->setPurchase($invoice,$time,$income_No,$journalNo);
               $response['invoice']    = $invoice;
               $response['success']    = "1";
            }
            else
            {
               $response['error']    = $chick;
               $response['success']    = "0";
            }
            echo json_encode($response);
        }
 //------------------------------------------       
        function chickPurchaseRet()
        {
            $result = 1 ;       
            $itemsArray     = $_POST['barcode'];
            $atleastOne     = 0;
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $qty        = $_POST['item_qty'][$i];
                if( $itemId != '' && $qty != '' )
                {
                    $atleastOne = 1;
                    break;
                }
            }
            if(!$atleastOne)
            {
                $result =  NO_ITEM_ENTERED;
            }   

            return $result;
        }
        
        function setPurchaseRet($invoice,$time,$outgo_No,$journalNo)
        {
            $storeid            = $_SESSION['storeid'];
            $purchInvoice       = $_POST['purchInvoice']; 
            $branch             = $_POST['branch'];
            $supplier           = $_POST['supplier'];
            $invoiceSup         = $_POST['supplier_invoice'];
            $payType            = $_POST['payType'];           
            $itemsArray         = $_POST['barcode'];
            $final_discount     =   $_POST['final_discount'];
            $gross_total        =   $_POST['gross_total'];
            $vat_all            =   $_POST['vat_all'];
            $totalAll           =   $_POST['totalAll']; 
            $pExpiry            = $_POST['purchase_expiry'];
            $pExpiry            = explode("/",$pExpiry);
            $pExpiry            = $pExpiry[2]."-".$pExpiry[1]."-".$pExpiry[0].' '.$time;
            $allDebit           =   0;  // اجمالى الجانب المدين
            $allCredit          =   0;  // اجمالى الجانب الدائن
            
            
             $query       = "INSERT INTO purchase_ret(invoicenumber,purchInvoice,   branch,   supplier,   supplier_invoice,invoice_date, paymentType,gross_total,   vat_all,   dis_added,        all_total,  storeid"
                                           . ")VALUES('$invoice',  '$purchInvoice','$branch','$supplier','$invoiceSup',    '$pExpiry',  '$payType', '$gross_total','$vat_all','$final_discount','$totalAll','$storeid')";
            $exe         = mysqli_query($adController->MySQL,$query);
              
            $queryOutgo = "INSERT INTO outgo (outgo_No,   branch,    invoice_type,         Beneficiary, invoice_No, date,      dis_added        , all_total ,storeid) "
                                   . "VALUES('$outgo_No','$branch' ,'RETURNED_PURCHASES'  , '$supplier', '$invoice', '$pExpiry','$final_discount','$totalAll','$storeid')";
            $exe         = mysqli_query($adController->MySQL,$queryOutgo);
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
               
                $store      = $_POST['store'][$i];
                $qty        = $_POST['item_qty'][$i];
                //echo "erte ".$qty;
                $price      = $_POST['item_price'][$i];
                $disc       = $_POST['item_disc'][$i];
                $tax        = $_POST['item_vat'][$i]; 
                $itemTotal  = $_POST['item_total_after_dis'][$i];   
                if($store != '' && $itemId != '' && $qty != '' && $price != '')
                {
                    
                    $query      = "INSERT INTO purchase_ret_items ( itemid  , quantity , cost   , discount , tax  , store  , invoice_No , storeid) VALUES"
                                                           . "( '$itemId','$qty'    ,'$price','$disc'   ,'$tax','$store','$invoice'  ,'$storeid')";
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL)); 
                    
                    $query      = "INSERT INTO outgo_items ( itemid   , quantity, cost   , discount, tax,    store  , outgo_No,  invoice_No   ,invoice_Type ,date , storeid) VALUES"
                                                        . "( '$itemId','$qty'   ,'$price','$disc'  , '$tax','$store','$outgo_No','$invoice','RETURNED_PURCHASES','$pExpiry','$storeid')";
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL)); 
                    
                    $code = $this->getAcctCode('stores',40,$store); // get account code of store
                    $this->insertJurnalItem($journalNo,$branch,$code,$itemTotal,0,"تكلفة الصنف بفاتورة مرتجع شراء $invoice",$invoice,'',$pExpiry);  // اضافة قيد دتئن لحسباب المخازن بقيمة تكلفة كل صنف
                    $allCredit = $allCredit + $itemTotal;
                }     
           }
           $this->insertJurnalItem($journalNo,$branch,'acc029',$vat_all,0,"الضريبة المسترجعه فاتورة مرتجع شراء $invoice",$invoice,'',$pExpiry); // اضافة قيد دائن لحساب ضريبة القيمة المضافة
           $allCredit = $allCredit + $vat_all;
            
            $discCode = $this->getAcctCode('suppliers',64,$supplier);  //get account code for Acquired discount of supplier
            $this->insertJurnalItem($journalNo,$branch,$discCode,0,$final_discount,"المسترد من الخصم المكتسب مرتجع شراء $invoice",$invoice,'',$pExpiry); // اضافة قيد مدين على  حساب الخصم المكتسب المرتبط بالمورد
            $allDebit = $allDebit + $final_discount;
            
            $suppCode = $this->getAcctCode('suppliers',5,$supplier);  // get account code for finantil acount of supplier
            $this->insertJurnalItem($journalNo,$branch,$suppCode,0,$totalAll," المبلغ المطلوب استرداده من المورد مرتجع شراء $invoice ",$invoice,'',$pExpiry); //اضافة قيد دائن لحساب المورد بالمبلغ المستحق على الفاتورة
            $allDebit = $allDebit + $totalAll;
            
            if ($payType==1)
            {
                $this->insertJurnalItem($journalNo,$branch,$suppCode,$totalAll,0,"استلام القيمة المراد استردادها مرتجع شراء $invoice",$invoice,'',$pExpiry); // اضافة قيد ممدين على حساب المورد لتحصيل قيمة الفاتورة
                $allCredit = $allCredit + $totalAll;
                
                $this->insertJurnalItem($journalNo,$branch,'acc007',0,$totalAll,"سداد القيمة المطلوب استردادها من المورد مرتجع شراء $invoice",$invoice,'',$pExpiry); // اضافة قيد دائن لحساب الصندوق لسداد ثمن الفاتورة
                $allDebit = $allDebit + $totalAll;
            }  
            $this->insertJurnal($journalNo,$invoice,"RETURNED_PURCHASES",$branch,'',$allDebit, $allCredit,$pExpiry);
        }
        
        function addPurchaseRet()
        {
            $chick = $this->chickPurchaseRet();
            if ($chick ==1)
            {
                $branch      =   $_POST['branch'];
                $invoice     =   $this->getNewInvoiceNo("purchase_ret","invoicenumber",'22',$branch);     
                $outgo_No   =   $this->getNewInvoiceNo("outgo","outgo_No",'32',$branch);    
                $journalNo   =   $this->getNewInvoiceNo("journal","journalNo",'40',$branch);   
                $time        =   date("h:i:s");
                $this->setPurchaseRet($invoice,$time,$outgo_No,$journalNo);
               $response['invoice']    = $invoice;
               $response['success']    = "1";
            }
            else
            {
               $response['error']    = $chick;
               $response['success']    = "0";
            }
            echo json_encode($response);
        }        

        function editPurchaseRet()
        {
            $chich = $this->chickPurchaseRet();

            if ($chich ==1)
            {       
                $branch      =   $_POST['branch'];
                $invoice     =   $_POST['invoice'];

                $outgo_No   =   $this->getExistInvoiceNo('outgo','outgo_No','invoice_No',$invoice);    
                $journalNo   =   $this->getExistInvoiceNo('journal','journalNo','docNo',$invoice); 
                $time        =   $this->getInvoiceTime('purchase_ret',$invoice);
                
                $this->resetInvoice($invoice,'purchase_ret','outgo');
                $this->setPurchaseRet($invoice,$time,$outgo_No,$journalNo);
               $response['invoice']    = $invoice;
               $response['success']    = "1";
            }
            else
            {
               $response['error']    = $chick;
               $response['success']    = "0";
            }
            echo json_encode($response);
        }  
 
  

function addStoreTrans()
        {
            $storeid      = $_SESSION['storeid'];

            $note         = $_POST['note'];
            $from         = $_POST['from'];
            $to           = $_POST['to'];            
            
            $trans_date   = $_POST['trans_date'];
            $trans_date   = explode("/",$trans_date);
            $trans_date   = $trans_date[2]."-".$trans_date[1]."-".$trans_date[0]." 00:00:00";

            $invoice        = $_POST['invoice'];          
//            $thread         = md5($invoice);
            
            $itemsArray     = $_POST['barcode'];
            $atleastOne     = 0;
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $qty        = $_POST['item_qty'][$i];
                if( $itemId != '' && $qty != '' )
                {
                    $atleastOne = 1;
                    break;
                }
            }
            
            if(!$atleastOne)
            {
                echo NO_ITEM_ENTERED;
                return;
            }
            
            $query          = "INSERT INTO store_trans (trans_number, trans_date,    storeFrom, storeTo, note,   storeid) VALUES"
                                                    . "('$invoice',   '$trans_date', '$from',  '$to',   '$note','$storeid')";
            $exe            = mysqli_query($adController->MySQL,$query);
            $insertid       = mysqli_insert_id();  
            
            
            $query   = "SELECT branch FROM stores WHERE id ='$from'";
            $res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            $data	= mysqli_fetch_assoc($res);       
            $branchF= $data['branch'];
            
            $query   = "SELECT branch FROM stores WHERE id ='$to'";
            $res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            $data	= mysqli_fetch_assoc($res);       
            $branchT= $data['branch'];            
            
 // get new outgoNo
            $query   = "SELECT outgo_No
                    FROM outgo
                    WHERE storeid='$storeid'                                          
                    ORDER BY outgo_No DESC
                    LIMIT 1";
            $res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            $num	= mysqli_num_rows($res);
            $data	= mysqli_fetch_assoc($res);
            if($num)
            {
                $rest = substr($data['outgo_No'], -4);  
                $trans_No = intval($rest)+1;
            }
            else 
                $trans_No = 1;
            $time           = time();
            $outgoInvoice  = $storeid.'-'.$time.'-'.str_pad($trans_No,4,"0",STR_PAD_LEFT);

            $queryOutgo    = "INSERT INTO outgo (outgo_No        ,branch    ,invoice_type,     invoice_No, date, storeid) VALUES"
                                           . "('$outgoInvoice','$branchF' ,'STORE_TRANSFAIR', '$invoice', '$trans_date','$storeid')";
            $exe            = mysqli_query($adController->MySQL,$queryOutgo);            
            
           
            // get new incomeNo
            $query   = "SELECT income_No
                    FROM income
                    WHERE storeid='$storeid'                                          
                    ORDER BY income_No DESC
                    LIMIT 1";
            $res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            $num	= mysqli_num_rows($res);
            $data	= mysqli_fetch_assoc($res);
            if($num)
            {
                $rest = substr($data['income_No'], -4);  
                $trans_No = intval($rest)+1;
            }
            else 
                $trans_No = 1;
            $time           = time();
            $incomeInvoice  = $storeid.'-'.$time.'-'.str_pad($trans_No,4,"0",STR_PAD_LEFT);
//            $incomeThread   = md5($incomeInvoice);
            
            // add income invoice 

            $queryIncome    = "INSERT INTO income (income_No      ,branch    ,invoice_type,     invoice_No, date,        storeid) VALUES"
                                             . "('$incomeInvoice','$branchT' ,'STORE_TRANSFAIR', '$invoice', '$trans_date','$storeid')";
            $exe            = mysqli_query($adController->MySQL,$queryIncome);
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $qty        = $_POST['item_qty'][$i];

                
                
                if( $itemId != '' && $qty != '' )
                {
                    $query      = "INSERT INTO store_trans_items (itemid,  quantity, trans_number,     storeid) VALUES"
                                                             . "('$itemId','$qty',   '$invoice'  ,'$storeid')";
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));                     
                    
                    $query      = "INSERT INTO income_items ( itemid   , quantity, cost, discount, tax, store  , income_No,   invoice_No, invoice_Type , storeid, date) VALUES"
                                                        . "( '$itemId','$qty'   ,''   ,''       , '' ,'$to','$incomeInvoice' ,'$invoice','STORE_TRANSFAIR' ,'$storeid','$trans_date' )";
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));  
                    $query      = "INSERT INTO outgo_items ( itemid   , quantity, cost, discount, tax, store  , outgo_No,      invoice_No, invoice_Type , storeid, date) VALUES"
                                                        . "( '$itemId','$qty'   ,''   ,''       , '' ,'$from','$outgoInvoice','$invoice','STORE_TRANSFAIR' ,'$storeid','$trans_date' )";
                    
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));                     
                    
                }
                
            }
            
            echo "1";
            
        }
        
        function editStoreTrans()
        {
            $storeid      = $_SESSION['storeid'];

            $note         = $_POST['note'];
            $from         = $_POST['from'];
            $to           = $_POST['to'];            
            
            $trans_date   = $_POST['trans_date'];
            $trans_date   = explode("/",$trans_date);
            $trans_date   = $trans_date[2]."-".$trans_date[1]."-".$trans_date[0]." 00:00:00";

            $invoice        = $_POST['invoice'];          
            
            $itemsArray     = $_POST['barcode'];
            $atleastOne     = 0;
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $qty        = $_POST['item_qty'][$i];
                if( $itemId != '' && $qty != '' )
                {
                    $atleastOne = 1;
                    break;
                }
            }
            
            if(!$atleastOne)
            {
                echo NO_ITEM_ENTERED;
                return;
            }            
//------------------------------            

            $query   = "SELECT branch FROM stores WHERE id ='$from'";
            $res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            $data	= mysqli_fetch_assoc($res);       
            $branchF= $data['branch'];
            $query   = "SELECT branch FROM stores WHERE id ='$to'";
            $res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            $data	= mysqli_fetch_assoc($res);       
            $branchT= $data['branch'];   
            
            $query          = "UPDATE store_trans SET trans_date='$trans_date', storeFrom = '$from', storeTo = '$to', note = '$note' WHERE storeid='$storeid' AND trans_number='$invoice'";
            $exe            = mysqli_query($adController->MySQL,$query);
            
            $query          = "UPDATE income SET date='$trans_date',branch='$branchT' WHERE storeid='$storeid' AND invoice_No='$invoice'";
            $exe            = mysqli_query($adController->MySQL,$query);
            
            $incomeData     = mysqli_fetch_assoc(mysqli_query($adController->MySQL,"SELECT income_No FROM income WHERE invoice_No='$invoice' AND storeid='$storeid'"));
            $incomeNo       = $incomeData['income_No'];        
                        
            $query          = "UPDATE outgo SET date='$trans_date',branch='$branchF' WHERE storeid='$storeid' AND invoice_No='$invoice'";
            $exe            = mysqli_query($adController->MySQL,$query);
            
            $outgoData      = mysqli_fetch_assoc(mysqli_query($adController->MySQL,"SELECT outgo_No FROM outgo WHERE invoice_No='$invoice' AND storeid='$storeid'"));
            $outgoNo       = $outgoData['outgo_No'];        
                                    
            $queryArr       = array();
            $idArray        = array();
            $tArray         = array();
            $combArray      = array();
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $qty        = $_POST['item_qty'][$i];
                if( $itemId != '' && $qty != '' )            
                { 
                    $idArray[]  = $itemId; 
//                    $tArray[]   = $itemId;
//                    $combArray[]= $itemType."@@".$itemId;
                    $query      = "SELECT COUNT(id) AS cnt FROM store_trans_items WHERE storeid='$storeid' AND itemid='$itemId' AND trans_number='$invoice'";
                    $res23      = mysqli_query($adController->MySQL,$query);
                    $data23     = mysqli_fetch_assoc($res23);
                    $num23      = intval($data23['cnt']);
                    if($num23 == 0) 
                    {

                    $query        = "INSERT INTO store_trans_items (itemid,  quantity, trans_number,     storeid) VALUES"
                                                             . "('$itemId','$qty',   '$invoice'  ,'$storeid')";
                    $queryIncome  = "INSERT INTO income_items ( itemid   , quantity, cost, discount, tax, store  , income_No,   invoice_No, invoice_Type , storeid, date) VALUES"
                                                        . "( '$itemId','$qty'   ,''   ,''       , '' ,'$to','$incomeNo' ,'$invoice','STORE_TRANSFAIR' ,'$storeid','$trans_date' )";
                   $queryOutgo    = "INSERT INTO outgo_items ( itemid   , quantity, cost, discount, tax, store  , outgo_No,      invoice_No, invoice_Type , storeid, date) VALUES"
                                                        . "( '$itemId','$qty'   ,''   ,''       , '' ,'$from','$outgoNo','$invoice','STORE_TRANSFAIR' ,'$storeid','$trans_date' )";                
                     }
                    else
                    {
                        $query       = "UPDATE store_trans_items SET quantity='$qty'                WHERE storeid='$storeid' AND itemid='$itemId' AND trans_number='$invoice'";
                        $queryIncome = "UPDATE income_items      SET quantity='$qty' ,store='$to'   WHERE storeid='$storeid' AND itemid='$itemId' AND invoice_No='$invoice'";
                        $queryOutgo  = "UPDATE outgo_items       SET quantity='$qty' ,store='$from' WHERE storeid='$storeid' AND itemid='$itemId' AND invoice_No='$invoice'";
                        
                    }
                    mysqli_query($adController->MySQL,$query);
                    mysqli_query($adController->MySQL,$queryIncome);
                    mysqli_query($adController->MySQL,$queryOutgo);                    
                }
            }
            $query      = "SELECT * FROM store_trans_items WHERE trans_number='$invoice' AND storeid='$storeid'";  
            $res23      = mysqli_query($adController->MySQL,$query);
            while($data23 = mysqli_fetch_assoc($res23))
            {
                $query  = "";
                $item  = $data23['itemid'];
                if(!in_array($item, $idArray))
                {
                    $query      = "DELETE FROM store_trans_items WHERE itemid='$item' AND trans_number='$invoice' AND storeid='$storeid'";
                    file_put_contents("f.txt", $query);
                    mysqli_query($adController->MySQL,$query);
                    $query      = "DELETE FROM income_items WHERE itemid='$item' AND invoice_No='$invoice' AND storeid='$storeid'";
                    mysqli_query($adController->MySQL,$query);
                    $query      = "DELETE FROM outgo_items WHERE itemid='$item' AND invoice_No='$invoice' AND storeid='$storeid'";
                    mysqli_query($adController->MySQL,$query);
                }
            }
            echo "1";
        }        
//--------------------------------------------------    
                
        
        
function addIncoming()
        {
            $storeid        = $_SESSION['storeid'];
            $branch         = $_POST['branch'];
//            $supplier       = $_POST['supplier'];
//            $invoiceSup     = $_POST['supplier_invoice'];
            $date        = $_POST['date'];
            $date        = explode("/",$date);
            $date        = $date[2]."-".$date[1]."-".$date[0]." 00:00:00";
//            $final_discount     = $_POST['final_discount'];
//            $totalAll           = $_POST['totalAll'];
            $invoice        = $_POST['invoice'];
//            $thread         = md5($invoice);
            
            $itemsArray     = $_POST['barcode'];
            $atleastOne     = 0;
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $itemId     = explode("@@",$itemId);
                $itemId     = $itemId[1];
                $qty        = $_POST['item_qty'][$i];
                $store      = $_POST['store'][$i];
                if($store != '' && $itemId != '' && $qty != '' )
                {
                    $atleastOne = 1;
                    break;
                }
            }
            
            if(!$atleastOne)
            {
                echo NO_ITEM_ENTERED;
                return;
            }
            $query          = "INSERT INTO income (income_No, branch,   date, storeid) VALUES"
                                            . "('$invoice','$branch','$date','$storeid')";
            $exe            = mysqli_query($adController->MySQL,$query);             
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $itemId     = explode("@@",$itemId);
                $itemId     = $itemId[1];
                
                $store      = $_POST['store'][$i];
                $qty        = $_POST['item_qty'][$i];

                
                
                if($store != '' && $itemId != '' && $qty != '')
                {
                    $query      = "INSERT INTO income_items ( itemid   , quantity, cost, discount, tax, store  , income_No , storeid, date) VALUES"
                                                        . "( '$itemId','$qty'   ,''   ,''       , '' ,'$store','$invoice'  ,'$storeid','$date' )";
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));                     
           
                }
                
            }
            
            echo "1";
            
        }
        
        function editIncoming()
        {
            $idcoming       = $_POST['sd'];
            $storeid        = $_SESSION['storeid'];
            $branch         = $_POST['branch'];
//            $supplier       = $_POST['supplier'];
//            $invoiceSup     = $_POST['supplier_invoice'];
            $date        = $_POST['date'];
            $date        = explode("/",$date);
            $date        = $date[2]."-".$date[1]."-".$date[0]." 00:00:00";
            
//            $final_discount     = $_POST['final_discount'];
//            $totalAll           = $_POST['totalAll'];
            $invoice            = $_POST['invoice'];

            
            $itemsArray     = $_POST['barcode'];
            $atleastOne     = 0;
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $itemId     = explode("@@",$itemId);
                $itemId     = $itemId[1];
                $qty        = $_POST['item_qty'][$i];
                $store      = $_POST['store'][$i];
                if($store != '' && $itemId != '')
                {
                    $atleastOne = 1;
                    break;
                }
            }
            
            if(!$atleastOne)
            {
                echo NO_ITEM_ENTERED;
                return;
            }

            $query          = "UPDATE income SET branch='$branch',date='$date' WHERE storeid='$storeid' AND id='$idcoming'";

            $exe            = mysqli_query($adController->MySQL,$query);
            $insertid       = $idcoming;     
            
            
            $queryArr       = array();
            $idArray        = array();
            $tArray         = array();
            $combArray      = array();
            for($i=0; $i<count($itemsArray); $i++)
            {

                $itemId     = $itemsArray[$i];
                $itemId     = explode("@@",$itemId);
                $itemId     = $itemId[1];
                
                $store        = $_POST['store'][$i];
                $qty        = $_POST['item_qty'][$i];
                
               
                if($store != '' && $itemId != '' && $qty != '')              
                    {
                    $idArray[]  = $itemId; 
//                    $tArray[]   = $itemId;
//                    $combArray[]= $itemType."@@".$itemId;
                    $query      = "SELECT COUNT(id) AS cnt FROM income_items WHERE storeid='$storeid' AND itemid='$itemId' AND income_No='$invoice'";
                    $res23      = mysqli_query($adController->MySQL,$query);
                    $data23     = mysqli_fetch_assoc($res23);
                    $num23      = intval($data23['cnt']);
                    if($num23 == 0) 
                        $query      = "INSERT INTO income_items ( itemid  , quantity , store  , income_No , storeid, date) VALUES"
                                                               . "('$itemId','$qty' ,'$store','$invoice'  ,'$storeid', '$date')";
                    else
                        $query      = "UPDATE income_items SET quantity='$qty',store='$store' WHERE storeid='$storeid' AND itemid='$itemId' AND income_No='$invoice'";
                    mysqli_query($adController->MySQL,$query);
                }
            }
            $query      = "SELECT * FROM income_items WHERE income_No='$invoice' AND storeid='$storeid'";  
            $res23      = mysqli_query($adController->MySQL,$query);
            while($data23 = mysqli_fetch_assoc($res23))
            {
                $query  = "";
                $item  = $data23['itemid'];
                if(!in_array($item, $idArray))
                {
                    //echo $item;
                    $query      = "DELETE FROM income_items WHERE id='$data23[id]' AND storeid='$storeid'";
                    file_put_contents("f.txt", $query);
                    mysqli_query($adController->MySQL,$query);
                }
            }
            
            echo "1";
        }    

//--------------------------------------------------
function addUotgoing()
        {
            $storeid        = $_SESSION['storeid'];
            $branch         = $_POST['branch'];
//            $supplier       = $_POST['supplier'];
//            $invoiceSup     = $_POST['supplier_invoice'];
            $date        = $_POST['date'];
            $date        = explode("/",$date);
            $date        = $date[2]."-".$date[1]."-".$date[0]." 00:00:00";
            
            $invoice        = $_POST['invoice'];
//            $thread         = md5($invoice);
            $itemsArray     = $_POST['barcode'];
            $atleastOne     = 0;
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $itemId     = explode("@@",$itemId);
                $itemId     = $itemId[1];
                $qty        = $_POST['item_qty'][$i];
                $store      = $_POST['store'][$i];
                if($store != '' && $itemId != '' && $qty != '' )
                {
                    $atleastOne = 1;
                    break;
                }
            }
            
            if(!$atleastOne)
            {
                echo NO_ITEM_ENTERED;
                return;
            }
            
            
            $query          = "INSERT INTO outgo (outgo_No, branch,   date, storeid) VALUES"
                                            . "('$invoice','$branch','$date','$storeid')";
            $exe            = mysqli_query($adController->MySQL,$query);
//            $insertid       = mysqli_insert_id();      
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $itemId     = explode("@@",$itemId);
                $itemId     = $itemId[1];
                
                $store        = $_POST['store'][$i];
                $qty        = $_POST['item_qty'][$i];
                //echo "erte ".$qty;

                
                
                if($store != '' && $itemId != '' && $qty != '')
                {
                    $query      = "INSERT INTO outgo_items ( itemid   , quantity, cost, discount, tax, store  , outgo_No , storeid, date) VALUES"
                                                        . "( '$itemId','$qty'   ,''   ,''       , '' ,'$store','$invoice'  ,'$storeid','$date' )";
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));                    
                }
                
            }
            
            echo "1";
            
        }
        
        function editOutgoing()
        {
            $idcoming       = $_POST['sd'];
            $storeid        = $_SESSION['storeid'];
            $branch         = $_POST['branch'];
//            $supplier       = $_POST['supplier'];
//            $invoiceSup     = $_POST['supplier_invoice'];
            $date        = $_POST['date'];
            $date        = explode("/",$date);
            $date        = $date[2]."-".$date[1]."-".$date[0]." 00:00:00";
            
//            $final_discount     = $_POST['final_discount'];
//            $totalAll           = $_POST['totalAll'];
            $invoice            = $_POST['invoice'];
//            $thread         = md5($invoice);
            $itemsArray     = $_POST['barcode'];
            $atleastOne     = 0;
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $itemId     = explode("@@",$itemId);
                $itemId     = $itemId[1];
                $qty        = $_POST['item_qty'][$i];
                $store      = $_POST['store'][$i];
                if($store != '' && $itemId != '')
                {
                    $atleastOne = 1;
                    break;
                }
            }
            
            if(!$atleastOne)
            {
                echo NO_ITEM_ENTERED;
                return;
            }
          
            $query          = "UPDATE outgo SET branch='$branch',date='$date' WHERE storeid='$storeid' AND id='$idcoming'";

            $exe            = mysqli_query($adController->MySQL,$query);
            $insertid       = $idcoming;     
            
            
            $queryArr       = array();
            $idArray        = array();
            $tArray         = array();
            $combArray      = array();
            for($i=0; $i<count($itemsArray); $i++)
            {
//                
                
                $itemId     = $itemsArray[$i];
                $itemId     = explode("@@",$itemId);
                $itemId     = $itemId[1];
                
                $store        = $_POST['store'][$i];
                $qty        = $_POST['item_qty'][$i];

                
                if($store != '' && $itemId != '' && $qty != '')              
                    {
                    $idArray[]  = $itemId; 
//                    $tArray[]   = $itemId;
//                    $combArray[]= $itemType."@@".$itemId;
                    $query      = "SELECT COUNT(id) AS cnt FROM outgo_items WHERE storeid='$storeid' AND itemid='$itemId' AND outgo_No='$invoice'";
                    $res23      = mysqli_query($adController->MySQL,$query);
                    $data23     = mysqli_fetch_assoc($res23);
                    $num23      = intval($data23['cnt']);
                    if($num23 == 0) 
                        $query      = "INSERT INTO outgo_items ( itemid  , quantity , store  , outgo_No , storeid, date) VALUES"
                                                               . "('$itemId','$qty' ,'$store','$invoice'  ,'$storeid', '$date')";
                    else
                        $query      = "UPDATE outgo_items SET quantity='$qty',store='$store' WHERE storeid='$storeid' AND itemid='$itemId' AND outgo_No='$invoice'";
                    
                    mysqli_query($adController->MySQL,$query);
                }
                
            }
            
            $query      = "SELECT * FROM outgo_items WHERE outgo_No='$invoice' AND storeid='$storeid'";  
            $res23      = mysqli_query($adController->MySQL,$query);
            while($data23 = mysqli_fetch_assoc($res23))
            {
                $query  = "";
                $item  = $data23['itemid'];
                if(!in_array($item, $idArray))
                {
                    //echo $item;
                    $query      = "DELETE FROM outgo_items WHERE id='$data23[id]' AND storeid='$storeid'";
                    file_put_contents("f.txt", $query);
                    mysqli_query($adController->MySQL,$query);
                }
            }
            
            
            echo "1";
            
            
        }   
function addQuote()
   {
    
            $storeid        = $_SESSION['storeid'];
            $branch         = $_POST['branch'];
            $customer       = $_POST['customer'];
            $$invoiceSup     = $_POST['supplier_invoice'];
            $pExpiry        = $_POST['purchase_expiry'];
            $pExpiry        = explode("/",$pExpiry);
            $pExpiry        = $pExpiry[2]."-".$pExpiry[1]."-".$pExpiry[0]." 00:00:00";
            
            $final_discount     = $_POST['final_discount'];
            $totalAll         = $_POST['totalAll'];
            $invoice        = $_POST['invoice'];
            
            $thread         = md5($invoice);
            
            $itemsArray     = $_POST['barcode'];
            $atleastOne     = 0;
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $itemId     = explode("@@",$itemId);
                $itemId     = $itemId[1];
                $qty        = $_POST['item_qty'][$i];
                $store      = $_POST['store'][$i];
                if($store != '' && $itemId != '' && $qty != '' )
                {
                    $atleastOne = 1;
                    break;
                }
            }
            
            if(!$atleastOne)
            {
                echo NO_ITEM_ENTERED;
                return;
            }
            
            if($customer == "" || $$invoiceSup == "")
            {
                echo SUPPLIER_NAME_COMPULSORY."<br>";
                echo INVOICE_NUMBER_COMPULSORY;
                return;
            }
            
            $query          = "INSERT INTO quote (thread,invoicenumber,branch   ,supplier   ,supplier_invoice,purchase_date , dis_added       , all_total ,storeid) VALUES"
                                              . "('$thread','$invoice'  ,'$branch','$customer','$invoiceSup'   ,'$pExpiry'    ,'$final_discount','$totalAll','$storeid')";
            $exe            = mysqli_query($adController->MySQL,$query);
            $insertid       = mysqli_insert_id();      
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $itemId     = explode("@@",$itemId);
                $itemId     = $itemId[1];
                
                $store        = $_POST['store'][$i];
                $qty        = $_POST['item_qty'][$i];
                //echo "erte ".$qty;
                $price      = $_POST['item_price'][$i];
                $disc       = $_POST['item_disc'][$i];
                $tax        = $_POST['item_vat'][$i];   
                
                
                if($store != '' && $itemId != '' && $qty != '' && $price != '')
                {
                    $query      = "INSERT INTO quote_items (item_thread , itemid  , quantity , cost   , discount , tax  , store  , purchase_id , storeid) VALUES"
                                                           . "('$thread'   , '$itemId','$qty'    ,'$price','$disc'   ,'$tax','$store','$insertid'  ,'$storeid')";
                    mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));                    
                }
                
            }
            echo "1";
        }
        
     function editQuote()
        {
            $idcoming       = $_POST['sd'];
            $storeid        = $_SESSION['storeid'];
            $branch         = $_POST['branch'];
            $customer       = $_POST['customer'];
            $invoiceSup     = $_POST['supplier_invoice'];
            $pExpiry        = $_POST['purchase_expiry'];
            $pExpiry        = explode("/",$pExpiry);
            $pExpiry        = $pExpiry[2]."-".$pExpiry[1]."-".$pExpiry[0]." 00:00:00";
            
            $final_discount     = $_POST['final_discount'];
            $totalAll           = $_POST['totalAll'];
            $invoice            = $_POST['invoice'];
            
            $thread         = md5($invoice);
            
            $itemsArray     = $_POST['barcode'];
            $atleastOne     = 0;
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $itemId     = explode("@@",$itemId);
                $itemId     = $itemId[1];
                $qty        = $_POST['item_qty'][$i];
                $store      = $_POST['store'][$i];
                if($store != '' && $itemId != '' && $qty != '' )
                {
                    $atleastOne = 1;
                    break;
                }
            }
            
            if(!$atleastOne)
            {
                echo NO_ITEM_ENTERED;
                return;
            }
            
            if($customer == "" || $invoiceSup == "")
            {
                echo SUPPLIER_NAME_COMPULSORY."<br>";
                echo INVOICE_NUMBER_COMPULSORY;
                return;
            }
          
            $query          = "UPDATE quote SET branch='$branch',supplier='$customer',supplier_invoice='$invoiceSup',purchase_date='$pExpiry',dis_added='$final_discount',all_total='$totalAll' WHERE storeid='$storeid' AND id='$idcoming'";

            $exe            = mysqli_query($adController->MySQL,$query);
            $insertid       = $idcoming;     

            $queryArr       = array();
            $idArray        = array();
            $tArray         = array();
            $combArray      = array();
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                
                $itemId     = $itemsArray[$i];
                $itemId     = explode("@@",$itemId);
                $itemId     = $itemId[1];
                
                $store        = $_POST['store'][$i];
                $qty        = $_POST['item_qty'][$i];
                //echo "erte ".$qty;
                $price      = $_POST['item_price'][$i];
                $disc       = $_POST['item_disc'][$i];
                $tax        = $_POST['item_vat'][$i];   
                
                if($store != '' && $itemId != '' && $qty != '' && $price != '')              
                    {
                    $idArray[]  = $itemId; 

                    $query      = "SELECT COUNT(id) AS cnt FROM quote_items WHERE storeid='$storeid' AND itemid='$itemId' AND purchase_id='$idcoming'";
                    $res23      = mysqli_query($adController->MySQL,$query);
                    $data23     = mysqli_fetch_assoc($res23);
                    $num23      = intval($data23['cnt']);
                    if($num23 == 0) 
                    {
                        $query       = "INSERT INTO quote_items (item_thread , itemid  , quantity , cost   , discount , tax  , store  , purchase_id , storeid) VALUES"
                                                               . "('$thread'   , '$itemId','$qty'    ,'$price','$disc'   ,'$tax','$store','$insertid'  ,'$storeid')";
                    }
                    else
                    {
                        $query       = "UPDATE quote_items SET quantity='$qty' , cost='$price' ,discount='$disc' ,tax='$tax' ,store='$store' WHERE storeid='$storeid' AND itemid='$itemId' AND purchase_id='$idcoming'";
                    }
                    
                    mysqli_query($adController->MySQL,$query);
                }
            }
            
            $query      = "SELECT * FROM quote_items WHERE purchase_id='$idcoming' AND storeid='$storeid'";  
            $res23      = mysqli_query($adController->MySQL,$query);
            while($data23 = mysqli_fetch_assoc($res23))
            {
                $query  = "";
                $item  = $data23['itemid'];
                if(!in_array($item, $idArray))
                {
                    //echo $item;
                    $query      = "DELETE FROM quote_items WHERE itemid='$item' AND purchase_id='$idcoming' AND storeid='$storeid'";
                    file_put_contents("f.txt", $query);
                    mysqli_query($adController->MySQL,$query);
                }
            }
            
            echo "1";
        }        

function addOpenPalance()
        {
            $storeid        = $_SESSION['storeid'];
            $store          = $_POST['store'];
            $itemsArray     = $_POST['barcode']; 
            $date       = $_SESSION['open_palance_date'];
            $date        = explode("/",$date);
            $date        = $date[2]."-".$date[1]."-".$date[0]." 00:00:00";            
            $insertedItem=0;
            for($i=0; $i<count($itemsArray); $i++)
            {
                $itemId     = $itemsArray[$i];
                $itemId     = explode("@@",$itemId);
                $itemId     = $itemId[1];

                
                $qty        = $_POST['item_qty'][$i];
                //echo "erte ".$qty;
                $price      = $_POST['item_price'][$i];
                $disc       = $_POST['item_disc'][$i];
                $tax        = $_POST['item_vat'][$i];
                if($itemId != '' && $qty != '' )
                {
                    $query          = "SELECT itemid FROM open_palance WHERE itemid =$itemId AND storeid='$storeid'";
                    $res            = mysqli_query($adController->MySQL,$query);
                    $num            = mysqli_num_rows($res);
                    if(!$num)
                    {
                        $query      = "INSERT INTO open_palance (itemid, quantity, cost,    discount, tax, date,  store,  storeid) VALUES"
                                                           . "('$itemId','$qty',   '$price','$disc',  '$tax','$date', '$store','$storeid')";
                        $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                        if($exe)
                        {
                                $insertedItem++;
                        }
                    }
                }                       
        }
        if($insertedItem > 0)
			echo "1";
		else
			echo ERROR_IN_ADDING_ITEM;
        } 
        
	function update_openPalance()
	{
            $qty   = $_REQUEST['q'];
            $price = $_REQUEST['p'];
            $disc  = $_REQUEST['d'];
            $idVal = $_REQUEST['id'];
            $date       = $_SESSION['open_palance_date'];
            $date        = explode("/",$date);
            $date        = $date[2]."-".$date[1]."-".$date[0]." 00:00:00";  
            echo $date;

            $query  = "UPDATE open_palance SET quantity='$qty', cost='$price' ,discount='$disc',date= '$date' WHERE id='$idVal'";                                
            echo mysqli_query($adController->MySQL,$query);
	}      
        
        function fill_parent_account()
        {
            $language 	= $_SESSION['lang'];
            $output = '<option value="0">Parent Account</option>';
            $query = "SELECT * FROM  accounts WHERE hasChild ='1' ORDER BY name_$language ASC";

            $res      = mysqli_query($adController->MySQL,$query);
            while($data = mysqli_fetch_assoc($res))
            {
                $name = $data['name_'.$language];    
                $output.= "<option value='$data[id]' type='$data[type]' budget='$data[budget]'>$name</option>";
            }

            echo $output;
        }
        function fitch()
        {
            $language 	= $_SESSION['lang'];
            $pairent	= $_REQUEST['p'];
            $sql = "SELECT * FROM `accounts` WHERE  `parent` = $pairent ";
            $query = mysqli_query($adController->MySQL,$sql);
            if (mysqli_num_rows($query) > 0) 
            {
                 while ($row = mysqli_fetch_assoc($query)) 
                {
                    $id=$row['id'];
                    $p=$row['parent'];
                    $e=$row['name_en'];
                    $a=$row['name_ar'];
                    $d=$row['code'];
                    $t=$row['type'];
                    $b=$row['budget'];
                    $c=$row['hasChild'];
                    $name = $row['name_'.$language];  
                    $plus='';
                    $count=$this->getChildCount($id);
            
                    if( $count >0)
                        $plus="<i class='icon-plus-sign showChild' p='$id'></i>";
                    else
                        $plus="<i class='icon-minus'></i>";                    
                    $output.= "<div class='account'>$plus
                    <a class='acc' href='javascript:void(0)'
                            aid='$id'
                            p='$p'
                            e='$e'
                            a='$a'
                            d='$d'
                            t='$t'
                            b='$b'
                            c='$c'>"
                            .$name."</a></div>";
                    $output.= "<div id='pair-$id'class='pair'></div>";
                }
            }
            
            echo $output;
        }   
                
            function addAccount()
            {
                $pairent                    = $_POST['pairent'];
                $name_ar                    = $_POST['name_ar'];
                $name_en                    = $_POST['name_en']; 
//                    $code                       = $_POST['code'];  
                $type                       = $_POST['type'];
                $flbs                       = $_POST['flbs'];                    
                $child                      = intval($_POST['child']);

                $obj = new validation();
                if($name_en == "" && $name_ar =="")
                        $error = NAME_REQUIRED;

                if(!$error)
                {
                    $exe  = $this -> insertAccount($name_en,$name_ar,$type,$flbs,$pairent,$child,'');

                    if($exe)
                    {
                            echo ACCOUNT_ADDED_SUCCESSFULLY;
                    }	
                    else
                        echo ERROR_ADDING_ACCOUNT;
                }
                else
                    echo $error;
            }    

            function editAccount()
            {
                $pairent    = $_POST['pairent'];
                $name_ar    = $_POST['name_ar'];
                $name_en    = $_POST['name_en']; 
                //                    $code                       = $_POST['code'];  
                $type       = $_POST['type'];
                $flbs       = $_POST['flbs'];                    
                $child      = intval($_POST['child']);
                $obj        = new validation();
                if($name_en == "" && $name_ar =="")
                {
                    echo NAME_REQUIRED;
                }
                else
                {
                    $id	= $_REQUEST['aid'];
                    $sql = "SELECT type, budget FROM `accounts` WHERE `id` = $id ";
                    $query = mysqli_query($adController->MySQL,$sql);   
                    $data = mysqli_fetch_assoc($query);
                    if( $data['type']!= $type  || $data['budget']!= $flbs)
                    {
                        $this->updateChild($id,$type,$flbs)  ;  
                    }
                    $query ="UPDATE accounts SET
                            name_en='$name_en',
                            name_ar='$name_ar',
                            type='$type',
                            budget='$flbs',
                            parent='$pairent',
                            hasChild='$child'
                            WHERE id='$id'";
                    $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                    if($exe)
                    {
                        echo ACCOUNT_ADDED_SUCCESSFULLY;
                    }	
                    else
                        echo ERROR_ADDING_ACCOUNT;
                }
            }       
       
        function delAccount()       
        {
            $id         =   $_REQUEST['aid'];
            $count      =   $this->getChildCount($id);
            $query      =   "SELECT * FROM `accounts` WHERE`id`='".$id."'";
            $res	=   mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            $data	=   mysqli_fetch_assoc($res);  
            if($data['def'] =='1' || $data['refId'] != NULL || $count > 0 )
            {
                echo "Account can't be deleted" ;
            }
            else 
            { 
                $query      = "DELETE FROM `accounts` WHERE`id`='".$id."'";                                
                $res        = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                if($res)
                {
                    echo DATA_DELETED_SUCCESSFULLY;
                }
                else
                    echo ERROR_UPDATING_TABLE;
            }
        }
        function updateChild($id,$type,$flbs)
        {
            $sql = "SELECT id FROM accounts WHERE parent = '$id' ";
            $query = mysqli_query($adController->MySQL,$sql);
            if (mysqli_num_rows($query) > 0) 
            {
                while ($row = mysqli_fetch_assoc($query)) 
                {
                    $sql ="UPDATE accounts SET
                            type='$type',
                            budget='$flbs'
                            WHERE id ='$row[id]'";
                    $exe = mysqli_query($adController->MySQL,$sql) or die(mysqli_error($adController->MySQL));  
                    $this->updateChild($row['id'],$type,$flbs);                      
                }
            }           
        } 
        function getChildCount($id)
        {
                    $result=mysqli_query($adController->MySQL,"SELECT count(*) as total FROM `accounts` WHERE  `parent` = $id");
                    $data=mysqli_fetch_assoc($result);
                    return $data['total'];
        }      
        
        
        function getNewCode()
        {
            $queryCode  = "SELECT code FROM accounts ORDER BY code DESC LIMIT 1";
            $resCode	= mysqli_query($adController->MySQL,$queryCode) or die(mysqli_error($adController->MySQL));
            $numCode	= mysqli_num_rows($resCode);
            $dataCode	= mysqli_fetch_assoc($resCode);
            if($numCode)
            {
                $rest = substr($dataCode['code'], -3);  
                $code = intval($rest)+1;
            }
            else 
                $code = 1;
            $code        = 'acc'.str_pad($code,3,"0",STR_PAD_LEFT); 
            return $code;
        }

        function getAcctCode($table,$parent,$refId)
        {
            $queryCode  = "SELECT code FROM accounts WHERE parent = '$parent' AND  refId = '$refId'";
            $resCode	= mysqli_query($adController->MySQL,$queryCode);
            if (mysqli_num_rows($resCode) > 0) 
            {           
                $dataCode   = mysqli_fetch_assoc($resCode);
                $code       =  $dataCode['code']; 
            }
            else 
            {
                $queryFile  = "SELECT name_en, name_ar FROM $table WHERE id = '$refId'";
                $resFile    = mysqli_query($adController->MySQL,$queryFile) ;
                $dataFile   = mysqli_fetch_assoc($resFile);
                $name_en    =  $dataFile['name_en']; 
                $name_ar    =  $dataFile['name_ar'];   
                $code       = $this->insertAccount($name_en,$name_ar,'','',$parent,0,$refId);
            }
            return $code;
        }  
        
        function insertAccount($name_en,$name_ar,$type,$flbs,$pairent,$child,$refId)
        {
            $code = $this->getNewCode();
            if ($pairent!=0)
            {
                $queryCode  = "SELECT type,budget FROM accounts WHERE id = '$pairent'";
                $resCode	= mysqli_query($adController->MySQL,$queryCode) or die(mysqli_error($adController->MySQL));
                $dataCode	= mysqli_fetch_assoc($resCode);
                $type       =  $dataCode['type']; 
                $flbs       =  $dataCode['budget'];      
            }

            $query ="INSERT INTO accounts
                   (code,   name_en, name_ar,    type,  budget,  parent, hasChild, refId)
            VALUES ('$code', '$name_en', '$name_ar', '$type', '$flbs', '$pairent', '0', '$refId')";
            $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            return $code;
        }    
        
        
        
    	function updateAcount($name_en,$name_ar,$pairent,$fileId)
        {
            $sql = "SELECT id FROM accounts WHERE parent = '$pairent' AND refId = '$fileId' ";
            $query = mysqli_query($adController->MySQL,$sql);
            if (mysqli_num_rows($query) > 0) 
            {
                $row = mysqli_fetch_assoc($query);
                $id= $row['id'];
                mysqli_query($adController->MySQL,"UPDATE accounts SET
                name_en='$name_en',
                name_ar='$name_ar'
                WHERE id ='$id'");
            }
            else 
            {
                $this->insertAccount($name_er,$name_ar,'','',$pairent,0,$fileId);
            }
        }        
   function deleteAcount ($p,$id)
   {
       $query      = "DELETE FROM `accounts` WHERE parent= $p AND refId ='$id'";                                
       $res        = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
   }
   
   
        
   function addJournal()
   {
            $journalNo      = $_POST['journalNo'];
            $document       = $_POST['document'];           
            $branch         = $_POST['branch'];
            $date           = $_POST['date'];
            $date           = explode("/",$date);
            $date           = $date[2]."-".$date[1]."-".$date[0]." 00:00:00";
            $cost           = $_POST['cost'];
            $disc           = $_POST['disc'];
            $totalDebit     = $_POST['total-debit'];
            $totalCredit    = $_POST['total-credit'];
            $itemsArray     = $_POST['account'];
            $atleastOne     = 0;
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $code     = $itemsArray[$i];

                if($code != '' )
                {
                    $atleastOne = 1;
                    break;
                }
            }
            
            if(!$atleastOne)
            {
                echo NO_ITEM_ENTERED;
                return;
            }
            
            if($branch == "" )
            {
                echo "please choose branch";
                return;
            }
     
            $this->insertJurnal($journalNo,$document,$disc,$branch,$cost,$totalDebit, $totalCredit,$date);

            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $code       = $itemsArray[$i];
                $debit      = $_POST['debit'][$i];
                $credit     = $_POST['credit'][$i];
                //echo "erte ".$qty;
                $discItem   = $_POST['discItem'][$i];
                $ref        = $_POST['ref'][$i];
                $costItem   = $_POST['costItem'][$i];   
                
                if($code != '' )
                {
                  $this->insertJurnalItem($journalNo,$branch,$code,$credit,$debit,$discItem,$ref,$costItem,$date);
                }
                
            }
            echo "1";
            
        }

        
        
        
   
        function editJournal()
        {
            $journalNo   = $_POST['journalNo'];
            $document    = $_POST['document'];           
            $branch      = $_POST['branch'];
            $date        = $_POST['date'];
            $date        = explode("/",$date);
            $date        = $date[2]."-".$date[1]."-".$date[0]." 00:00:00";
            $cost     = $_POST['cost'];
            $disc     = $_POST['disc'];
            $totalDebit     = $_POST['total-debit'];
            $totalCredit    = $_POST['total-credit'];
            $itemsArray     = $_POST['account'];
            $atleastOne     = 0;
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                $code     = $itemsArray[$i];

                if($code != '' )
                {
                    $atleastOne = 1;
                    break;
                }
            }
            
            if(!$atleastOne)
            {
                echo NO_ITEM_ENTERED;
                return;
            }
            
            if($branch == "" )
            {
                echo "please choose branch";
                return;
            }
            $this->updateJurnal($journalNo,$document,$disc,$branch,$cost,$totalDebit, $totalCredit,$date);
            
            for($i=0; $i<count($itemsArray); $i++)
            {
                
                $code       = $itemsArray[$i];
                $debit      = $_POST['debit'][$i];
                $credit     = $_POST['credit'][$i];
                $discItem   = $_POST['discItem'][$i];
                $ref        = $_POST['ref'][$i];
                $costItem   = $_POST['costItem'][$i];   
                
                if($code != '' )
                {                

                   $codeArray[]  = $code; 
                    $query      = "SELECT id FROM journal_items WHERE accCode='$code' AND journalNo='$journalNo'";
                     $res = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                    $num	= mysqli_num_rows($res);

                    if($num > 0) 
                    {
                        $this->updateJurnalItem($journalNo,$branch,$code,$credit,$debit,$discItem,$ref,$costItem,$date);                        

                    }
                    else
                    {
                        $this->insertJurnalItem($journalNo,$branch,$code,$credit,$debit,$discItem,$ref,$costItem,$date);
                    }
                }
            }
            
            $query      = "SELECT accCode FROM journal_items WHERE journalNo='$journalNo' ";  
            $res23      = mysqli_query($adController->MySQL,$query);
            while($data23 = mysqli_fetch_assoc($res23))
            {
                $query  = "";
                $item  = $data23['accCode'];
                if(!in_array($item, $codeArray))
                {
                    echo $item.'del';
                    $query      = "DELETE FROM journal_items WHERE accCode='$item' AND journalNo='$journalNo' ";
                    $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                }
            }
            
            
            echo "1";
            
        }                
        function insertJurnal($journalNo,$document,$disc,$branch,$costCenter,$totalDebit, $totalCredit,$date)
        {
            $query          = "INSERT INTO journal 
               (`journalNo`,
                `docNo`,
                `disc`,
                `brarnch`,
                `costCenter`,
                `totalDebit`,
                `totalCredit`,
                `date`)
                VALUES
               ('$journalNo',
                '$document',
                '$disc',
                '$branch',
                '$costCenter',
                '$totalDebit',
                '$totalCredit',                    
                '$date')";
            $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            //$insertid       = mysqli_insert_id();                  
        }
       
        function updateJurnal($journalNo,$document,$disc,$branch,$costCenter,$totalDebit, $totalCredit,$date)
        {
            $query    = "UPDATE journal SET 
            `journalNo`     =   '$journalNo',
            `docNo`         =   '$document',
            `disc`          =   '$disc',
            `brarnch`        =   '$branch',
            `costCenter`    =   '$costCenter',
            `totalDebit`   =   '$totalDebit',                
            `totalCredit`   =   '$totalCredit',
             `date`           =   '$date'
             WHERE journalNo=   '$journalNo'";
            $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
        }
        function insertJurnalItem($journalNo,$branch,$code,$credit,$debit,$discItem,$ref,$costItem,$date)
        {
            
            $query = "INSERT INTO journal_items
            (
                `journalNo`,
                `brarnch`,
                `accCode`,
                `credit`,
                `debit`,
                `disc`,
                `reference`,
                `costCenter`,
                `date`
                )
                VALUES
                ('$journalNo',
                '$branch',
                '$code',
                '$credit',
                '$debit',
                '$discItem',
                '$ref',
                '$costItem',
                '$date')";
            $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            
        }  
function updateJurnalItem($journalNo,$branch,$code,$credit,$debit,$discItem,$ref,$costItem,$date)
        {
                $query    = "UPDATE journal_items SET 
                `brarnch`       ='$branch',
                `credit`        ='$credit',
                `debit`         ='$debit',
                `disc`          ='$discItem',
                `reference`     ='$ref',
                `costCenter`    ='$costItem',
                `date`          ='$date'
                WHERE `journalNo` = '$journalNo'
                AND     `accCode` = '$code'";                  
            $exe = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
        }

		
	function updateCheck()
        {
	    //$adController->MySQLi_Handle = updateConnect();
            $current = $_POST['current'];

            
            $sql_check = mysqli_query($adController->MySQL,$adController->MySQLi_Handle,"SELECT * FROM updates WHERE id > $current") or die(mysqli_error($adController->MySQL));
            $num = mysqli_num_rows($sql_check);
	    mysqli_close($adController->MySQLi_Handle);
	    if ( $num > 0) 
            {
                echo false;
            }
            else
            {
                echo true;
            }

        }	
      function dbUpdate()
        {
            $current = $_POST['current'];
            $done = 1;
                       
	    //$adController->MySQLi_Handle = updateConnect();

            $sql = mysqli_query($adController->MySQL,$adController->MySQLi_Handle,"SELECT * FROM updates WHERE id > '$current'") or 
                    die(mysqli_error($adController->MySQL));
	    $num= mysqli_num_rows($sql);

            while ($row =  mysqli_fetch_array($sql))
	    {
		$serverId = $row['id'];
		$version  = $row['version'];	
		$file = updateUrl().'sqls/'.$row['sql_file'];
		
		    //echo $file.'<br>';
                    
		    $targetFile = 'update/'.$row['sql_file'];
		    if ( copy($file, $targetFile) ) 
		    {
			$sqlFile = file_get_contents($targetFile);
		       // echo $sqlFile.'<br>';

			$pieces = explode(";", $sqlFile);
			for($i=1;$i<count($pieces);$i++)
			{
			    //echo $pieces[$i].'<br>';
			    $exe = mysqli_query($adController->MySQL,$pieces[$i]) ;
			    if (!$exe) 
			    {
				$done = 0;  
				echo mysqli_error($adController->MySQL)." ---- Error <br>";
			    }
			}
                        unlink($targetFile);
		    }
		    else 
		    { 
			echo "Copying failed."; 
		    }		    

	    }  
            mysqli_close($adController->MySQLi_Handle);
  
	    if ($done)
            {
		$query    = "UPDATE version SET "
		    . " `version`       ='$version',"
		    . " `serverId`      ='$serverId' ";    
		$exe = mysqli_query($adController->MySQL,$query); 	
                echo "1";   
            }
            else
            {
               echo("failed");
            }
	}	    
	function  filesUpdate ()
	{
                    
            $file = updateUrl().'zip/pos.zip';

            $targetFile = 'update/pos.zip';
            if ( copy($file, $targetFile) ) 
            {
		$zip = new ZipArchive;
		$res = $zip->open($targetFile);
		if ($res === TRUE)
		{
		    $zip->extractTo('/');
		    $zip->close();	
                    unlink($targetFile);
		    echo "1";    
		} 
		else 
                { 
		    echo "Extract failed."; 
		}
	    }
	    else 
            { 
		echo "Copying failed."; 
	    }	    
        }	
}

?>

