<?php
session_start();
error_reporting(E_ERROR);
require_once("db.conn.php");

$name 		= trim($_POST['name']);
$emailId 	= trim($_POST['email']);
$password	= trim($_POST['password']);
$city		= trim($_POST['city']);
$state		= trim($_POST['state']);
$type		= trim($_POST['type']);
$pwd		= $password;

if(count($_POST) > 0 )
{

    if($city == "")
            $city	 = '86065';
    if($state == "")
            $state	 = '1416';

    $street = "olaya";
    $st_ar  = "الشارع في العربية";


    $query  = "SELECT id FROM storeuser WHERE email='$emailId'";
    $res2   = mysqli_query($adController->MySQL,$query);
    $num    = mysqli_num_rows($res2);

    if($num)
    {
            print("<script>alert('email already present');</script>");
    }	
    else if($emailId == "")
    {
            print("<script>alert('please enter name');</script>");
    }
    else if($emailId == "")
    {
            print("<script>alert('please enter email');</script>");
    }
    else if($password == "")
    {
            print("<script>alert('please enter password');</script>");
    }
    else if($type == "")
    {
            print("<script>alert('please enter type');</script>");
    }
    else
    {	
        $password   = md5($password);
        $query      = "INSERT INTO storeuser(name,email,password) 
                      VALUES('$name','$emailId','$password')";
        
        $res        = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));	
        $insertId   = mysqli_insert_id();

        if($res)
        {
            $hash   = getRandomWord(rand(3,5))."-".$insertId."-fg";

            $query  = "INSERT INTO dataversion(storehash,storeid,type) 
                       VALUES('$hash','$insertId','$type')";
            $res    = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));


            $exploded	= explode("@",$emailId);
            $cashierEm	= trim($exploded[0])."cash@smartfgpos.com";

//-------------------------------------------------------------
            
            $query 	= "INSERT INTO branches (name_en,storeid,city,state,street_en,street_ar,country,phone)
                          VALUES ('$name','$insertId','$city','$state','$street','$st_ar','97','050000000000')";

            $res	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
            $insertId2	= mysqli_insert_id();

            $query      = "INSERT INTO employees(name_en,email,password,store_branch,type,storeid) 
                           VALUES ('Cashier','$cashierEm','e10adc3949ba59abbe56e057f20f883e','$insertId2','3','$insertId')";
            $res        = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL)); 
            
            $queryy 	=  "INSERT INTO `stores`( `name_en`, `name_ar`, `storeid`,'branch', `default`) 
                           VALUES ('Main Store','المخزن الرئيسي','$insertId','$insertId2',1') ";                                                       
            $ress	= mysqli_query($adController->MySQL,$queryy) or die(mysqli_error($adController->MySQL));	    

            $queryy 	=  "INSERT INTO `units`( `name_en`, `name_ar`, `storeid`, `default`) 
                           VALUES ('pesea','قطعة','$insertId','1') ";
            $ress	= mysqli_query($adController->MySQL,$queryy) or die(mysqli_error($adController->MySQL));	
            
 //------------------------------------------------------------- 
            
            $str = "";
            $str .=  "<table>";
                    $str .=   "<tr>";
                            $str .=   "<td colspan='2'><strong>ADMIN</strong></td>";
                    $str .=   "</tr>";
                    $str .=   "<tr>";
                            $str .=   "<td>Email</td>";
                            $str .=   "<td>$emailId</td>";
                    $str .=   "</tr>";
                    $str .=   "<tr>";
                            $str .=   "<td>Password</td>";
                            $str .=   "<td>$pwd</td>";
                    $str .=   "</tr>";

                    $str .=   "<tr>";
                            $str .=   "<td colspan='2'><strong>CASHIER</strong></td>";
                    $str .=   "</tr>";
                    $str .=   "<tr>";
                            $str .=   "<td>Email</td>";
                            $str .=   "<td>$cashierEm</td>";
                    $str .=   "</tr>";
                    $str .=   "<tr>";
                            $str .=   "<td>Password</td>";
                            $str .=   "<td>123456</td>";
                    $str .=   "</tr>";

                    $str .=   "<tr>";
                            $str .=   "<td colspan='2'><strong>STORE HASH</strong></td>";
                    $str .=   "</tr>";
                    $str .=   "<tr>";
                            $str .=   "<td>Hash</td>";
                            $str .=   "<td>$hash</td>";
                    $str .=   "</tr>";
            $str .=   "</table>";

            $_SESSION['abc'] = $str;
            print("<script>location.replace('create_new_user_.php')</script>");
        }
    }
}


function getRandomWord($len ) {
    $word = array_merge(range('a', 'z'), range('a', 'z'));
    shuffle($word);
    return substr(implode($word), 0, $len);
}

?>



<html>
<body>
	<h1>&nbsp;</h1>
	<center>
	<?php
		if($_REQUEST['a'] == "1")
			unset($_SESSION['abc']);
		
		if(!isset($_SESSION['abc']))
		{
	?>
	<form method='post' action='create_new_user_.php'>
			<table>
				<tr>
					<td><input type='text' name='name' maxlength='35' placeholder='Name...'></td>
				</tr>
				<tr>
					<td><input type='text' name='email' maxlength='35' placeholder='Email...'></td>
				</tr>
				<tr>
					<td><input type='text' name='password' maxlength='35' placeholder='Password...'></td>
				</tr>
				<tr>
					<td><select name='type'>
						<option value='1'>Restaurant</option>
						<option value='2'>Beauty Center</option>
						</select>
					</td>
				</tr>
				<tr>
					<td><input type='submit' name='submit' value='Create User'></td>
				</tr>
			</table>	
			
		
	</form>
	<?php
		}
		else
		{
			print_r($_SESSION['abc']);
			
			echo "<br><br><a href='create_new_user_.php?a=1'>Create new</a>";
		}
			
	 ?>
	</center>
</body>
<?php
mysqli_close();
?>
</html>