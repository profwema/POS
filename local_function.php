<?php
checkDB();
$name= checkUser();
    
function checkDB()
{
    mysqli_connect("localhost","root","");
    if (mysqli_select_db("pos_db_off") == false ) 
    {

        createDB();
    }
}


function createDB()
{


    error_reporting(E_ERROR);
    require_once("translate.php");
    $str    = file_get_contents("pos_db_off.sql");

    $link = mysqli_connect("localhost","root","");
    if ($link) {
        $query = "CREATE DATABASE IF NOT EXISTS pos_db_off";
        mysqli_query($link, $query);
        mysqli_close($link);
    }

    $link2 = mysqli_connect("localhost","root","", "pos_db_off");
    if ($link2) {
        $exp = explode(";", $str ?? '');
        for($i=0; $i<count($exp); $i++) {
            if (trim($exp[$i]) !== "") {
                echo '-----';
                mysqli_query($link2, $exp[$i]);
            }
        }
        mysqli_close($link2);
    }

}
/////////////////////////////////////////////////
function checkUser()

{
    $query	= "SELECT * FROM storeuser";
    $resS	= mysqli_query($adController->MySQL,$query);
    $dataS	= mysqli_fetch_assoc($resS);
    $num	= mysqli_num_rows($resS);
    if(!$num)        print("<script>location.replace('new_user.php')</script>");
    else return $dataS['name'];
}


?>