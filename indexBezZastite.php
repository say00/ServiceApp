<?php
    session_start();
    $ip = $_SERVER["REMOTE_ADDR"];
?>
<!DOCTYPE HTML>
<html>

<head>
  <title>Service App ASUS</title>
  <meta name="description" content="website description" />
  <meta name="keywords" content="website keywords, website keywords" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="css/style.css" />
  <!-- modernizr enables HTML5 elements and feature detects -->
  <script type="text/javascript" src="js/modernizr-1.5.min.js"></script>
</head>

<body>
    <?
        //Konekcija na bazu
        $dbConnection = mysqli_connect("localhost","mojaprez_baza","edf951323","mojaprez_baza");
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
        
        if(isset($_POST["signout"])){
            unset($_SESSION["$ip"]);
        }
    ?>
    <div id="main">
    <header>
        <div id="logo">
          <!-- <div id="logo_text"> -->
          <!-- class="logo_colour", allows you to change the colour of the text -->
          <!-- <h1><a href="index.html">CCS3<span class="logo_colour">_abstract_bw</span></a></h1> -->
          <!-- <h2>Simple. Contemporary. Website Template.</h2> -->
        <!-- </div> -->
        </div> 
        <nav>
        <div id="menu_container">
            <ul class="sf-menu" id="nav">
                <li><a href="index.php">Home</a></li>
                <li><a href="#">Settings</a>
                    <ul>
                        <li><a href="#">Administration</a>
                            <ul>
                                <li><a href="users.php">Users</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <?php
                    if(isset($_SESSION["$ip"])) {
                        $user = $_SESSION["$ip"];
                        $query_user = mysqli_query($dbConnection, "SELECT role FROM ServiceApp_ASUS_Users WHERE user='$user'");
                        $niz = mysqli_fetch_array($query_user);
                        $role = $niz[0];
                        
                        if($role == "S" or $role =="A") {
                ?>
                <li><a href="#">Service</a>
                    <ul>
                        <li><a href="receiving.php">Receiving</a>
                            <ul>
                                <li><a href="receiving_new_invoice.php">Receiving new parts</a></li>
                            </ul>
                        </li>    
                        <li><a href="work_order.php">Requisition and Work Order</a></li>
                        <li><a href="returning_used.php">BAD <-> BAD RTV -> ASUS</a></li>
                        <li><a href="recycling.php">BAD <-> Local Scrap -> Scrap</a></li>
                        <li><a href="returning_unused.php">Blank-OK <-> RTV SLOW -> ASUS</a></li>
                    </ul>
                </li>
                <?php
                        }
                ?>
                <li><a href="#">Reports</a>
                    <ul>
                        <li><a href="summary.php">Summary</a></li>
                        <li><a href="current_stock.php">Review of current stock</a></li>
                    </ul>
                </li>
                <?php
                        if($role == "W" or $role == "A") {
                ?>
                <li><a href="#">Warehouse</a>
                    <ul>
                        <li><a href="requisition.php">Requisition</a></li>
                    </ul>
                </li>
                <?php
                        }
                    }
                ?>
            </ul>
        </div>
        </nav>
    </header>
    <?php
        if(isset($_SESSION["$ip"])){
    ?>
    <div class="form_settings" style="float: right; margin: -46px 5px 0 0;">
        <form name="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
   	        <input type="submit" class="submit" name="signout" value="   SIGN OUT   " />
	    </form>
	</div>
	<?php
	    }
	?>
    <div id="site_content">
        <div class="content">
        <?php
            if(isset($_POST["login"])){
                $user = trim($_POST["user"]);
                $pass = md5($_POST["pass"]);
                
                if (empty($user) && empty($pass)) {
                    echo("<p style='color:red;'>*Both fields missing.</p>");
                }
                else if (empty($user)) {
                    echo("<p style='color:red;'>*Fill user name.</p>");
                }
                else if (empty($pass)) {
                    echo("<p style='color:red;'>*Fill password.</p>");
                }
                else{
                    //$query2 = "SELECT role FROM ServiceApp_ASUS_Users WHERE user='$user' and pass='$pass'";
                    $stmt = $dbConnection->prepare("SELECT role FROM ServiceApp_ASUS_Users WHERE user = ? and pass = ?");
                    $stmt->bind_param('ss', $user, $pass);
                    $stmt->execute();
                    $stmt->store_result();
                    
                    //$rez = mysqli_query($dbConnection,$query2);
                    //$broj_korisnika = mysqli_num_rows($rez);
                    
                    if ($stmt->num_rows == 1){
                        $_SESSION["$ip"] = $user;
                    }else{
                        /*$checkIP = mysqli_query($dbConnection, "SELECT address FROM ServiceApp_ASUS_loginAttempts WHERE address='$ip'");
              	        if(mysqli_num_rows($checkIP)>=1) {
              	            $newAtt = $attempts + "1";
              	            $date = date("Y-m-d H:i:s", time());
                            $stmt_resetAtt = $dbConnection->prepare("INSERT INTO ServiceApp_ASUS_loginAttempts (address, datetime, user, pass, attempts) VALUES (?, ?, ?, ?, ?)");
                            $stmt_resetAtt->bind_param('ssssi', $ip, $date, $user, $_POST["pass"], $attempts);
                            $stmt_resetAtt->execute();
            	   
                        }
                        else {
        	                $insertview = mysql_query("INSERT INTO mobilOprema_pageview VALUES('','yourpage','$user_ip', '$datum_unosa')");
        		            $updateview = mysql_query("UPDATE mobilOprema_totalview SET totalvisit = totalvisit+1 WHERE page='yourpage' ");
        	            }*/
                         
                        
                        $attempts = "1";
                        $date = date("Y-m-d H:i:s", time());
                        $stmt_log = $dbConnection->prepare("INSERT INTO ServiceApp_ASUS_loginAttempts (address, datetime, user, pass, attempts) VALUES (?, ?, ?, ?, ?)");
                        $stmt_log->bind_param('ssssi', $ip, $date, $user, $_POST["pass"], $attempts);
                        $stmt_log->execute();
                        echo("<p style='color:red;'>*User name or password incorrect. Try again.</p>");
                        
                        

                    }
                    //Refresuje stranicu nakon logovanja radi prikazivanja celog menija.
                    echo('<script> setTimeout(function () { window.location.href = "index.php"; }, 700); </script>');
                }
            } 
            
            if(!isset($_SESSION["$ip"])){
        ?>
            <div class="round">
                <form name="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                    <div class="form_settings">
                        <label style="padding-right: 20px;">User name:&nbsp;&nbsp;&nbsp;&nbsp;</label<labe><input type="text" name="user" style="width:150px;" /></label>
                        <label style="padding-right: 20px;"><input class="submit" style="margin: 0 0 0 -2px;" type="submit" name="login" value="    SIGN IN    " /></label>
                        <br>
                        <label style="padding-right: 7px;">Password:&nbsp;&nbsp;&nbsp;&nbsp;</label><label><input type="password" name="pass" value="" style="width:150px;" /></label>
                    </div>
                </form>
            </div>
            <!--<hr style="width:100%; height: 10px; border: 0; box-shadow: 0 10px 10px -10px #8c8b8b inset;">-->
            <br>
        <?php   
            }else{
                if(isset($_SESSION["$ip"]))
	            $user = $_SESSION["$ip"];
	            $query = "SELECT user, role, full_name FROM ServiceApp_ASUS_Users WHERE user='$user'";
	            $rez = mysqli_query($dbConnection,$query);
	            $niz = mysqli_fetch_array($rez);
	            $user = $niz[0];
	            $role = $niz[1];
	            $full_name = $niz[2];
	                    
	            echo("HI! <b><i>$full_name</i></b><br>");
        ?>
                <div class="form_settings" style="float:left;">
                    <form name="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
               	        <p style="margin-left: -180px;"><input type="submit" class="submit" name="signout" value="   SIGN OUT   " /></p>
            	    </form>
            	    <!--<hr style="width:700px;; height: 10px; border: 0; box-shadow: 0 10px 10px -10px #8c8b8b inset;">-->
                </div>
                <br><br><br><br>
        <?php   
	            }
		?>
    		<div class="round">
        		<h2>Servis WEB application for <strong>ASUS</strong> cases in warranty</h2>
                <p><u>Main menu is in the top of the page and from there you can navigate.</u></p>
                <p>On the top of every page, you can see <img src="images/question_mark.png" alt="question_mark" height="20" width="20" style="margin: 0 0 0 0; cursor:pointer;"> mark. 
                By putting mouse over, you can see basic information of what you can do on the current page that you are on.</p>
                <br>
            </div>
            <br><br>
            <div class="round">
                <!--<span class="img"><img src="images/index_img.png" alt="example" /></span>-->
            </div>
        <!--<hr style="width:auto; height: 10px; border: 0; box-shadow: 0 10px 10px -10px #8c8b8b inset;">-->
        </div>
    </div>
    <div id="scroll">
        <a title="Scroll to the top" class="top" href="#"><img src="images/top.png" alt="top" /></a>
    </div>
    <footer>
      <p><img src="images/twitter.png" alt="twitter" />&nbsp;<img src="images/facebook.png" alt="facebook" />&nbsp;<img src="images/rss.png" alt="rss" /></p>
      <p><a href="index.php">Home</a> | <?php if(isset($_SESSION["$ip"])){ ?><a href="receiving_new_invoice.php">Receiving new invoice (Parts)</a> | <a href="work_order_requisition.php">Create new Requisition</a> | <a href="current_stock.php">Current Stock</a> | <a href="requisition.php">Requisition Approval</a><?php } ?></p>
      <p>Copyright &copy; Jovan Milošević | <a href="#">Master design & programming Jovan Milošević</a></p>
    </footer>
    </div>
    <!-- javascript at the bottom for fast page loading -->
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/jquery.easing-sooper.js"></script>
    <script type="text/javascript" src="js/jquery.sooperfish.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('ul.sf-menu').sooperfish();
            $('.top').click(function() {$('html, body').animate({scrollTop:0}, 'fast'); return false;});
        });
   </script>
</body>
</html>
