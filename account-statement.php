<?php
    error_reporting(E_ALL);
    require_once("top.php");
    require_once("redirection.php");
    require_once("controller.php");

    $storeid	= $_SESSION['storeid'];
    $language	= $_SESSION['lang'];
    $acc = array();
    
    function getAccounts($account)
    {
        global $acc;
        $query		= "SELECT id, hasChild FROM accounts  WHERE code ='$account'  ";
        $result         = mysqli_query($adController->MySQL,$query);
        $fetchRes 	= mysqli_fetch_assoc($result);
        if ($fetchRes['hasChild']==0)
        {
            $acc[]=$account;
        }
        else 
        {
            $sql = "SELECT code FROM accounts WHERE parent = '$fetchRes[id]' ";
            $query = mysqli_query($adController->MySQL,$sql);
            if (mysqli_num_rows($query) > 0) 
            {
                while ($row = mysqli_fetch_assoc($query)) 
                {
                    getAccounts($row['code']);                      
                }
            }           
        }
    }  

   ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
              <title>WAM Tech Soft</title>
      <?php require_once("header.php");?>	
        <style>
          .datetime
              {
               width: 75% ;
               border:  green solid 1px !important;
          }
          .credit
          {
             color: green;
          }
          .debit
          {
              color: red;
          }
          .movement
          {
              background-color:  #ffffbb !important;
          }
          .balance 
          {
              background-color:  #ddddff !important;
          }          
          
         @media (max-width: 700px) {
         #mobile 
         {
         display:block;
         }
         #mobile .row-fluid
         {
         display:none;
         }
         #desktop
         {
         display:none;
         }
         }
         @media (min-width: 701px)
         {
         #mobile 
         {
         display:none;
         }
         #desktop
         {
         display:block;
         }
         }

        </style>
        <link href="css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen">
    </head>
    <body>
      <?php require_once("header_top.php");?>
        <div class="container-fluid-full">
            <div class="row-fluid">
            <?php require_once("left_menu.php");?>
                <div id="content" class="span10">
                    <div>
                        <div class="box span12">
                            <div class="box-header" data-original-title>
                                <h2><i class="halflings-icon edit"></i><span class="break"></span><?=ITEMS_REPORT?></h2>
                                <div class="box-icon">
                                    <a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-content">
                                <form class="form-horizontal" autocomplete="off" action="<?=$pgName?>" id='form' method="POST">
                                    <fieldset>
                                        <div class="control-group">
                                            <div class="salesOptions">
                                                <label class="control-label" for="typeahead"><?=ACCOUNT_NAME?></label>
                                                <div class="controls">
                                                    <select   class='span3' name="account" id='account' style="width:90%" >
                                                        <option value="0">&nbsp;</option>
                                                    <?php
                                                        $language 	= $_SESSION['lang'];
                                                        $query = "SELECT * FROM  accounts ORDER BY parent ASC";

                                                        $res      = mysqli_query($adController->MySQL,$query);
                                                        while($data = mysqli_fetch_assoc($res))
                                                        {
                                                            $name = $data['name_'.$language];  
                                                            $code = $data['code'];  
                                                            $resP  = mysqli_query($adController->MySQL,"SELECT name_$language AS parent FROM  accounts WHERE id ='$data[parent]'");
                                                            $dataP = mysqli_fetch_assoc($resP);
                                                            $parent= $dataP['parent'];
                                                            $sel	= "";                                                                
                                                            
                                                            if(isset($_REQUEST["account"]) && $_REQUEST["account"] == $data['code'])
                                                            {
                                                                $sel = " selected='true' ";
                                                            }
                                                            echo "<option value='$code' $sel>$parent -> $name  </option>";
                                                        }

                                                    ?>
                                                    </select> 
                                                </div>
                                            </div>
                                        </div>       
                                        <div class="clearfix"></div>                             
                                        <div class="control-group">
                                            <div class="salesOptions">
                                                <label class="control-label"><?=BRANCH?> :</label>
                                                <div class="controls">
                                                    <select name="branch"  class="span3">
                                                        <option value="">&nbsp;</option>
                                                        <?php
                                                                $query = "SELECT * FROM branches  ORDER BY name_en ASC";
                                                                $res   = mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                                                                while($data= mysqli_fetch_assoc($res))
                                                                {
                                                                    $id   = $data['id'];
                                                                    $name = $data['name_'.$language];                                                                    
                                                                    $brn	= "";
                                                                       if($_REQUEST['branch'] == $id)
                                                                       {                                                                            
                                                                           $brn = " selected='true' ";
                                                                       }
                                                                       echo "<option value='$id' $brn>$name</option>";

                                                                }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="salesOptions" >
                                                <label class="control-label"><?=COST_CENTER?></label>
                                                <div class="controls">
                                                    <select name="cost" data-rel="chosen">
                                                        <option value=''> </option>  
                                                    </select>
                                                </div>
                                            </div>  
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="control-group">
                                            <div class="salesOptions">                               
                                                <label class="control-label" for="date01"><?=FROM_DATE?></label>
                                                <div class="controls">
                                                    <input type="text" class="datetime datetimepicker" id="fr_date" name="fr_date" value="<?=$_POST['fr_date']?>">              
                                                </div>
                                            </div>
                                            <div class="salesOptions">                                                                 
                                                <label class="control-label" for="date02"><?=TO_DATE?></label>
                                                <div class="controls">
                                                    <input type="text" class="datetime datetimepicker" id="to_date" name="to_date" value="<?=$_POST['to_date']?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <button type="submit" class="btn btn-primary btn-lg" name="Submit" onclick="loadReport()"><?=LOAD_FORM?></button>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>

                      
                        <?php
                        if(isset($_POST['Submit']))
                        {
                             
 
                            $conditionRep='';
                            $account                 = $_POST['account'];  
                            if ($account) 
                            {
                                getAccounts($account);      



                                
                                $conditionRep .= "AND accCode IN ('" . implode("','", $acc) . "')" ;

                            }
                            $branch		= $_POST['branch'];  
                            if ($branch) $conditionRep .=" AND brarnch = '$branch'";
                            $fdate		        = $_POST['fr_date'];  
                            if ($fdate) $conditionRep .=" AND date >= '$fdate'";                 
                            $tdate		        = $_POST['to_date'];  
                            if ($tdate) $conditionRep .=" AND date <= '$tdate'";                     
                        ?>
                    <div>
                        <div class="box span12">   
                            <div class="clearfix"></div>
                            <div class="titles"><?=TOTALS?>  </div>
                            <div class="totalAll">
                                <table class="table table-bordered bootstrap-datatable " style="width:50%;" align="center">
                                    <thead>
                                        <tr>      
                                             <th class="credit "><?=TOTAL?> <?=CREDIT?></th>      
                                            <th class='debit '><?=TOTAL?> <?=DEBIT?></th>
                                            <th class="credit "><?=BALANCE?> <?=CREDIT?> </th>
                                            <th class='debit '><?=BALANCE?> <?=DEBIT?> </th>
                                        </tr>
                                    </thead>   
                                    <tbody>
                                        <tr>
                                            <td class='credit movement'id="credit"></td> 
                                            <td class='debit movement' id="debit"></td>
                                            <td class='credit balance' id="balanceCredit"></td>                             
                                            <td class='debit balance' id="balanceDebit"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="clearfix"></div>
                            <hr class="br">                            
                            <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                <thead>
                                    <tr>
                                        <th><?=DATE?></th>    
                                        <th class="credit "><?=BALANCE?> <?=CREDIT?> </th>
                                         <th class='debit '><?=BALANCE?> <?=DEBIT?> </th>        
                                         <th class="credit "><?=CREDIT?></th>                                        
                                        <th class='debit '><?=DEBIT?></th>
                                        <th><?=COST_CENTER?></th>
<!--                                        <TH><?=REFERENE?></TH>-->
                                        <th><?=DISCRIPTION?></th>                                        
                                        <th><?=ACCOUNT_NAME?></th>    
                                         <th><?=BRANCH?></th>                                             
                                        <th><?=JOURNAL_NUMBER?></th>   


                                        <th> </th>         
                                    </tr>
                                </thead>   
                                <tbody>
                            <?php                                    
                            $language 	= LANG;
                            $storeid	= $_SESSION['storeid'];
                            $oldBalanceDebit = 0 ;
                            $oldBalanceCredit = 0 ; 
                            $totalDebit=0;
                            $totalCredit=0;
                            $i=0;
                            $query	 	= "SELECT * FROM journal_items WHERE state = '1' $conditionRep ORDER BY date ";

                            $res 	 	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
                            while($data 	= mysqli_fetch_assoc($res))
                            {
                                $code           = $data['accCode'];                            
                                $query		= "SELECT * FROM accounts  WHERE code ='$code'  ";
                                $resCode	= mysqli_query($adController->MySQL,$query);
                                $dataCode 	= mysqli_fetch_assoc($resCode);
                                $accName	= $dataCode['name_'.$language];                                
                            
                                $pranch         = $data['brarnch'];                            
                                $query		= "SELECT * FROM branches  WHERE id ='$pranch' ";
                                $resBra		= mysqli_query($adController->MySQL,$query);
                                $dataBra 	= mysqli_fetch_assoc($resBra);
                                $branch	        = $dataBra['name_'.$language];
                                
                                $journalNo      = $data["journalNo"];										
                                $disc           = $data["disc"];
                                $ref            = $data["reference"];                                
                                $credit         = $data["credit"];
                                $debit          = $data["debit"];                                                                                
                                $date           = $data['date'];
                                
                                $newBalanceDebit  = $oldBalanceDebit - $oldBalanceCredit + $debit - $credit;
                                $newBalanceCredit = $oldBalanceCredit - $oldBalanceDebit + $credit - $debit ; 
                                if ($newBalanceDebit <= 0)  $newBalanceDebit = 0 ;
                                if ($newBalanceCredit <= 0)  $newBalanceCredit = 0 ;

                                
                                echo "<tr>";
  
                                echo "<td>$date</td>";        
                                echo "<td class='credit balance'>".flout_format($newBalanceCredit)."</td>";                                
                                echo "<td class='debit balance'>".flout_format($newBalanceDebit)."</td>";    
                                echo "<td class='credit movement'>".flout_format($credit)."</td>";  
                                echo "<td class='debit movement'>".flout_format($debit)."</td>";
                                echo "<td></td>";                                
//                                echo "<td>$ref</td>";                                 
                                echo "<td>$disc</td>";                                 
                                echo "<td>$accName</td>";       
                                 echo "<td>$branch</td>";                               
                                echo "<td>$journalNo</td>";             

                            
                                echo "<td>".++$i."</td>";    
                                echo "</tr>";
                                $oldBalanceDebit = $newBalanceDebit ; 
                                $oldBalanceCredit = $newBalanceCredit ;   
                                $totalDebit  += $debit;
                                $totalCredit += $credit;
                                
                            }
                            $BalanceDebit  = $totalDebit - $totalCredit ;
                            $BalanceCredit = $totalCredit - $totalDebit ; 
                            if ($BalanceDebit <= 0)  $BalanceDebit = 0 ;
                            if ($BalanceCredit <= 0)  $BalanceCredit = 0 ;
                        }
                            ?>
                                    
                                </tbody>
                            </table>                                

                            
                            
                        </div>
                    </div>
         <?php require_once("footer.php");?>
                    <script type="text/javascript" src="js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
                    <script type="text/javascript">  
                        $('.datetimepicker').datetimepicker({
                            format:'yyyy-mm-dd hh:00:00',
                            minView:1,        
                            showMeridian: true,
                            todayBtn: true,
                            autoclose: true
                            //         ampm: true // FOR AM/PM FORMAT
                        });    
                                                  
                    </script>
                    <script type="text/javascript">     
                        $(document).ready(function(){            
                            var config = {'.span3':{ width: '90%' } };
                            for (var selector in config) 
                            {
                                $(selector).chosen(config[selector]);
                            }            
                            $("#credit").html("<?=flout_format($totalCredit)?>");            
                            $("#debit").html("<?=flout_format($totalDebit)?>");     

                            $("#balanceCredit").html("<?=flout_format($BalanceCredit)?>");                
                            $("#balanceDebit").html("<?=flout_format($BalanceDebit)?>");            

                        })                              
                    </script>
                </div>
            </div>
        </div>      
    </body>
</html>