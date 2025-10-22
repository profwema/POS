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

    mysqli_connect("localhost","root","");
    $query      = "CREATE DATABASE IF NOT EXISTS pos_db_off";
    $exe        = mysqli_query($adController->MySQL,$query);
    mysqli_close();   

    mysqli_connect("localhost","root","");
    mysqli_select_db("pos_db_off");

    $exp    = explode(";",$str);
    for($i=0;$i<count($exp);$i++)
    {       
        echo '-----';
        mysqli_query($adController->MySQL,$exp[$i]);


    }
//        echo ' ------- DataBase Created Successfully  ------ ';    
    
    
    mysqli_close();

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