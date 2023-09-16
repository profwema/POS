<?php
require_once("top.php");
require_once("redirection.php");
$lang = $_SESSION['lang'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8">
	<title><?=SITE_TITLE?></title>
	<meta name="description" content="online pos">
	<meta name="author" content="futuregates.net">
	<meta name="keyword" content="online pos,pos,touchscreen pos">
	<!-- end: Meta -->
	
	<!-- start: Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- end: Mobile Specific -->
	
	<!-- start: CSS -->



	<!-- end: CSS -->
	

	<!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	  	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<link id="ie-style" href="css/ie.css" rel="stylesheet">
	<![endif]-->
	
	<!--[if IE 9]>
		<link id="ie9style" href="css/ie9.css" rel="stylesheet">
	<![endif]-->
		
	<!-- start: Favicon -->
	<link rel="shortcut icon" href="img/favicon.ico">
	<!-- end: Favicon -->
	
<style>
            
@import url("halflings.css");	
* { box-sizing: border-box; margin: 0; padding:0; }

html {
  background: #95a5a6;

  font-family: 'Helvetica Neue', Arial, Sans-Serif;
}
  
  .login-wrap {
    position: relative;
    margin: 10% auto;
    background: #ecf0f1;
    width: 350px;
    border-radius: 5px;
    box-shadow: 3px 3px 10px #333;
    padding: 15px;
  }
    
    h2 {
      text-align: center;
      font-weight: 200;
      font-size: 2em;
      margin-top: 10px;
      color: #34495e;
    }
    
    .form {
      padding-top: 20px;
    }
      
      input[type="text"],
      input[type="password"]
       {
        width: 80%;
         height: 40px;       
         font-size: 14px;
    
          margin-left: 10%;
        margin-bottom: 25px;
          <?php
          if ($lang == 'ar')
          {
          ?>
text-align: right;   
padding-right:  10px;    
        <?php
          }
          else
          {
              ?>
text-align: left;   
padding-left:  10px;   
     
        <?php
          }
              
        ?>

        outline: 0;
        -moz-outline-style: none;     
        border: 1px solid #bbb;
        border-radius: 5px;

      }
      

          .login-wrap:focus {
          border: 1px solid #3498db;
        }

      
      a {
        text-align: center;
        font-size: 10px;
        color: #3498db;
      }
        p{
          padding-bottom: 10px;
        }
        

      
      button {
	  

        margin-left: 10%;
        margin-bottom: 25px;
        height: 40px;
        border-radius: 5px;
        outline: 0;
        -moz-outline-style: none;
	  
	  float: left;
	  width: 50%;
        background: #e74c3c;
        border:none;
        color: white;
        font-size: 18px;
        font-weight: 200;
        cursor: pointer;
        transition: box-shadow .4s ease;
      }
      span
      {
	        margin-left: 5%;
		        height: 40px;
	 float: left;
	  width: 30%;
padding: 5px;
        border:none;
        color: white;
        font-size: 18px;
        font-weight: 200;
        cursor: pointer;
        transition: box-shadow .4s ease;
      }
      
      .lang-flag
      {
	  float: left;
	  border:  #27ae60 solid 1px;
	          border-radius: 50%;
		  font-size: 15px;

		  color: white;
		  padding: 5px;
		  margin-left: 5px;
                  background: #fff;
                  text-decoration: #fbfb01;
      }
.green {
    background: #37842b     !important;
}
.purple {
    background: #C13303 !important;
}    
      .lang-flag:hover
      {

    background: #999900  !important;
    color:#fbfb01;

      }

      
          .login-wrap:hover {
          box-shadow: 1px 1px 5px #555;  
        }
          
          .login-wrap:active {
            box-shadow: 1px 1px 7px #222;  
        }
        

    
      .login-wrap:after{
    content:'';
    position:absolute;
    top: 0;
    left: 0;
    right: 0;    
    background:-webkit-linear-gradient(left,               
        #27ae60 0%, #27ae60 20%, 
        #8e44ad 20%, #8e44ad 40%,
        #3498db 40%, #3498db 60%,
        #e74c3c 60%, #e74c3c 80%,
        #f1c40f 80%, #f1c40f 100%
        );
       background:-moz-linear-gradient(left,               
        #27ae60 0%, #27ae60 20%, 
        #8e44ad 20%, #8e44ad 40%,
        #3498db 40%, #3498db 60%,
        #e74c3c 60%, #e74c3c 80%,
        #f1c40f 80%, #f1c40f 100%
        );
      height: 5px;
      border-radius: 5px 5px 0 0;
  }
  
.clearfix {
  *zoom: 1;
}

.clearfix:before,
.clearfix:after {
  display: table;
  line-height: 0;
  content: "";
}

.clearfix:after {
  clear: both;
}
        </style>
		

	<?php require_once("script_php_variables.php");?>
		
		
</head>

<body>    
    <div class="login-wrap">
	<h2><?=LOGIN?></h2>
	<form id='form' class="form">
	    <input type="text" placeholder="<?=USERNAME?>" name="user" id="user" />
	    <input type="password" placeholder="<?=PASSWORD?>" name="password" id="password" />
  								

            <button type="button" id="login"><?=LOGIN?>  </button>  	
	    <span>
		<a href='javascript:void(0)' onclick="changeLang('ar')" class="lang-flag green">AR</a>
							
		<a href='javascript:void(0)' onclick="changeLang('en')" class="lang-flag purple">EN</a>
						
	    </span> 
	    <div class="clearfix"></div>
	</form>
    </div>  

	<?php require_once("script.php");?>
	
</body>
</html>
