<?php
error_reporting(E_ALL);
require_once("top.php");
require_once("redirection.php");
require_once("controller.php");
$language 	= LANG;
$storeid	= $_SESSION['storeid'];

if(isset($_REQUEST["catid"]))
 $_SESSION['catItems']= $_REQUEST["catid"]


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WAM Tech Soft</title>
	<?php require_once("header.php");?>	
</head>

<body>
		<?php require_once("header_top.php");?>
	
    <div class="container-fluid-full">
        <div class="row-fluid">
			<?php require_once("left_menu.php");?>
			<div id="content" class="span10">
			<div>  <!--class="row-fluid sortable"-->
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon edit"></i><span class="break"></span><?=ITEMS?></h2>
					</div>
					<div class="box-content">
						<p align="right">
							<a class="btn btn-success" href="add_item.php">
								<i class="icon-plus"></i>
							</a>
						</p>                        
                        <div class="box-content">
                        <form class="form-horizontal"  action="<?=$pgName?>" id='form' method="POST">
                           <fieldset>
                        <table width="100%" border="0">
  <tr>
    <td width="30%"><div class="control-group">
            <label class="control-label" for="typeahead3" style="width:30px">
                                          
                                <?=CATEGORY?>
            </label>
                                          <div class="controls" style="margin-left:60px">
                                             <select onchange="this.form.submit()" class='span3' name="catid">
                                                <option value="">&nbsp;</option>
                                                <?php
                                                   $query= "SELECT * FROM categories WHERE storeid='$storeid'";// id IN ( SELECT catid FROM items WHERE id IN (SELECT DISTINCT itemid FROM orders  $condition))";
                                                   $res		= mysqli_query($adController->MySQL,$query);
                                                   while($data 	= mysqli_fetch_assoc($res))
                                                   {
                                                       $name	= $data["name_".$language];
                                                   
                                                       $sel	= "";                                                                
                                                   
                                                       if(isset( $_SESSION['catItems']) && $_SESSION['catItems'] == $data['id'])
                                                               {
                                                                       $sel = " selected='true' ";
                                                               }
                                                   
                                                               echo "<option value='$data[id]' $sel>$name</option>";
                                                               }
                                                   ?>											
                                             </select>
                                          </div>
                                       </div></td>
    <td width="30%">&nbsp;</td>
    <td width="30%">&nbsp;</td>
  </tr>
</table>

                                       
                          </fieldset>
                                       </form>
                                       </div>
						<table class="table table-striped table-bordered bootstrap-datatable datatable">
						  	<thead>
							  <tr>
                                                              <th></th>
								  <th><?=IMAGE?></th>
								  <th><?=ITEM_NAME?></th>
								  <th><?=CATEGORY?></th>
								  <th><?=ENABLED?></th>
                                                                  <th><?=BARCODE?></th>
								  <th><?=EDITING?></th>
							  </tr>
						  	</thead>   
						  	<tbody>
                            <?php
                            $catSearch='';
                            if(isset( $_SESSION['catItems']))
                            {
                                 if ( $_SESSION['catItems']!='')
                                 {
                                       $catSearch=" AND catid = ". $_SESSION['catItems']." ";
                                 }
                         }
                                          ?>
                            
								<?php
                                                                        $i=0;
									$query	 	= "SELECT * FROM items  WHERE storeid='$storeid' $catSearch GROUP BY item_thread";
									$res 	 	= mysqli_query($adController->MySQL,$query) or die(mysqli_error($adController->MySQL));
									while($data 	= mysqli_fetch_assoc($res))
									{
                                                                            $i++;
										

										$query		  = "SELECT * FROM categories WHERE id='$data[catid]' AND storeid='$storeid'";
										$resCat		  = mysqli_query($adController->MySQL,$query);
										$dataCat 	  = mysqli_fetch_assoc($resCat);

										$name	 	= $data["name_$language"];
										if($name == "")
										{
											if($language == "ar")
												$name	= $data['name_en'];
											else
												$name	= $data['name_ar'];
										}
											
										$category	= $dataCat['name_'.$language];
										
										$enabled	= $data["enabled"];
										
										$is_service	= $data["is_service"];
										if($enabled=="1")
											$enabled=YES;
										else
											$enabled=NO;
									

										if($is_service=="1")
											$is_service=YES;
										else
											$is_service=NO;

										$dirVal		= urlencode($adController->encrypt_decrypt(1,DIR_ITEM_NAME,0));
										$idval		= urlencode($adController->encrypt_decrypt(1,$data['item_thread'],0));
										$tableName	= urlencode($adController->encrypt_decrypt(1,'items',0));
										$secondIdval	= urlencode($adController->encrypt_decrypt(1,$idval,0));

                                                                                
                                                                                $thumb	= $data['image'];
//										$query 		= "SELECT * FROM images WHERE foreign_id='$data[id]' AND `table`='items'";
//										$resImage	= mysqli_query($adController->MySQL,$query);
//										$dataImage	= mysqli_fetch_assoc($resImage);
//										$img		= $adController->getDirectoryOnlyPath(DIR_ITEM_NAME);
//										$thumb		= $dataImage['thumb'];
									

										echo "<tr>";
                                                                                echo "<td> $i </td>";
                                                                                        echo "<td>";
                                                                                	if($thumb != "")
                                                                                            echo "<img src='$thumb' class='thumb'>";
											//$thumb 	= NO_IMAGE;
											echo "</td>";
											echo "<td>$name</td>";
											echo "<td>$category</td>";
											echo "<td>$enabled</td>";                                                                                                                    
                                                                                        echo "<td class='center'>
													<a class='btn btn-dark' href='barcode_item.php?sd=$secondIdval'>
														<i class='halflings-icon white barcode'></i>  
													</a>  </td>";                                                                                      
											echo "<td class='center'>
													<a class='btn btn-info' href='edit_item.php?sd=$secondIdval'>
														<i class='halflings-icon white edit'></i>  
													</a>
													<a class='btn btn-danger' href='javascript:void(0)' onclick='javascript:deleteData(\"$tableName\",\"$idval\",\"$dirVal\");'>
														<i class='halflings-icon white trash'></i> 
													</a>
												</td>";
                                                                                        echo "</tr>";
									}
								?>
							</tbody>
					  </table>            
					</div>
				</div>

			</div>

			

		</div>
		</div>
		</div>
		
	
	<div class="clearfix"></div>
	
	<?php require_once("footer.php");?>
      <script type="text/javascript">
                $(document).ready(function(){
            
               var config = {
              '.span3'     : { width: '95%' }
            };
            for (var selector in config) {
              $(selector).chosen(config[selector]);
            }
				});
       </script>
            
</body>
</html>
