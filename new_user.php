<?php

require_once("db.conn.php");




function getRandomWord($len ) 
{
    $word = array_merge(range('a', 'z'), range('a', 'z'));
    shuffle($word);
    return substr(implode($word), 0, $len);
}
function getNewCode($MySQL)
{
    $queryCode      = "SELECT code FROM accounts ORDER BY code DESC LIMIT 1";
    $resCode	= mysqli_query($MySQL,$queryCode) or die(mysqli_error($MySQL));
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

       

function insertAccount($name_en,$name_ar,$pairent,$refId,$MySQL)
{
    $code = getNewCode();
                                            
    $queryCode  = "SELECT type,budget FROM accounts WHERE id = '$pairent'";
    $resCode	= mysqli_query($MySQL,$queryCode) or die(mysqli_error($MySQL));
    $dataCode	= mysqli_fetch_assoc($resCode);
    $type       =  $dataCode['type']; 
    $flbs       =  $dataCode['budget'];            
    
    $query ="INSERT INTO accounts
           (code,   name_en, name_ar,    type,  budget,  parent, hasChild, refId)
    VALUES ('$code', '$name_en', '$name_ar', '$type', '$flbs', '$pairent', '0', '$refId')";
    $exe = mysqli_query($MySQL,$query) or die(mysqli_error($MySQL));
}  
?>


	<?php
        

        echo 'start';

            $name_en = "Main Branch";
            $name_ar = "الفرع الرئيسي";
            $query 	= "INSERT INTO `branches` (`name_en`,`name_ar`) VALUES ('$name_en','$name_ar')";
            $res	= mysqli_query($MySQL,$query) or die(mysqli_error($MySQL));
            $insertId2	= mysqli_insert_id();
            if ($res) echo 'branch added <br>';
            
             
            $name_en = "Cash cust";
            $name_ar = "عميل عام";
            $queryy 	=  "INSERT INTO `customers`( `name_en`, `name_ar`,  `default`) 
                                            VALUES ('$name_en','$name_ar','1')";

            $ress	= mysqli_query($MySQL,$queryy) or die(mysqli_error($MySQL));  
                        if ($ress) echo 'customers added <br>';
            $refId      = mysqli_insert_id();      
            insertAccount($name_en,$name_ar,1,$refId,$MySQL);
            

            
            $name_en = "admin";
            $name_ar = "مدير";
            $query      = "INSERT INTO employees(name_en,name_ar,user,password,store_branch) 
                                         VALUES ('$name_en','$name_ar','admin','".md5('123456')."','$insertId2')";
            $res        = mysqli_query($MySQL,$query) or die(mysqli_error($MySQL)); 
            if ($res) echo 'emp added <br>';
            $refId      = mysqli_insert_id();      
            insertAccount($name_en,$name_ar,28,$refId,$MySQL);
            
            
            $name_en = "Cash sup";
            $name_ar = "مورد عام";            
            $queryy 	=  "INSERT INTO `suppliers`( `name_en`, `name_ar`,  `default`) 
                                            VALUES ('$name_en','$name_ar', '1') ";
            $ress	= mysqli_query($MySQL,$queryy) or die(mysqli_error($MySQL));     
                        if ($ress) echo 'sub added <br>';
            $refId      = mysqli_insert_id();      
            insertAccount($name_en,$name_ar,5,$refId,$MySQL);
            
           
            $queryy 	=  "INSERT INTO `units`( `name_en`, `name_ar`, `default`) 
                           VALUES ('pesea','قطعة','1') ";
            $ress	= mysqli_query($MySQL,$queryy) or die(mysqli_error($MySQL));	
             if ($ress) echo 'unit added <br>';
            
            $name_en = "Main Store";
            $name_ar = "المخزن الرئيسي";                
            $queryy 	=  "INSERT INTO `stores`( `name_en`, `name_ar`, `branch`, `default`) 
                                          VALUES ('$name_en','$name_ar','$insertId2','1') ";                                                       
            $ress	= mysqli_query($MySQL,$queryy) or die(mysqli_error($MySQL));	
            if ($ress) echo 'store added <br>';
            $refId      = mysqli_insert_id();      
            insertAccount($name_en,$name_ar,37,$refId,$MySQL);
            insertAccount($name_en,$name_ar,40,$refId,$MySQL);
            insertAccount($name_en,$name_ar,47,$refId,$MySQL);            
            



mysqli_close();
?>
</html>
    
    