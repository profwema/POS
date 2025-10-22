<nav class="navbar navbar-expand-lg navbar-fixed-top" role="navigation">
    <div class="navbar-inner">
	<div class="container-fluid">
	      <button  class="navbar-toggle" type="button" data-toggle="collapse" data-target="#sidebar-left" aria-controls="sidebar-left" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
  </button>
	    <a class="brand" href=".">
                <img src="img/pos1.png">WAM Tech Soft
            </a>
							
	    <!-- start: Header Menu -->
	    <div class="nav-no-collapse header-nav">
		<ul class="nav pull-right">
                    <li class = 'pos'>     
                        <a  href="pos.php" title="POS">         
                            <img src="img/cashier.jpg"> Cashier Screen
                        </a>   
                    </li>
                    <li class = 'lang'>
                        <a  href='javascript:void(0)' onclick="changeLang('ar')" class="green">Ar</a>     
                        <a  href='javascript:void(0)' onclick="changeLang('en')" class="purple">En</a>
                    </li>                                       
                    <li class="dropdown btn">
                        <a class=" dropdown-toggle" data-toggle="dropdown" href="#">
                            <img src="img/emp/user.png">
                            <span class="user-info">
                                  <small><?=WELCOME?>,</small> 
<?=$_SESSION['name']?>
                            </span>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-menu-title">
                                <span id="logout" style="cursor:pointer">
                                   
                                   <i class="fa fa-power-off"></i>
<?=LOGOUT?>
<!--                                    <i class="icon-off"></i>-->
                                </span>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
	</div>
    </div>
</nav>