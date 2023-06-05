<?php
    session_start();
    $ip = $_SERVER["REMOTE_ADDR"];
    if(!isset($_SESSION["$ip"])) {
        echo('<script> window.location.href ="notice.php"; </script>');
    }
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
        <form name="form" action="index.php" method="POST">
   	        <input type="submit" class="submit" name="signout" value="   SIGN OUT   " />
	    </form>
	</div>
	<?php
	    }
	?>
    <div id="site_content">
        <a hreff="#" tooltip="On this page you can see your login details, change password if needed, create new user, and see list of all users.">
            <img src="images/question_mark.png" alt="question_mark" height="30" width="30" style="margin: -11px 0 0 -15px; cursor:pointer;">
        </a>
        <br>
        <div class="content" style="margin-top:10px;">
        <?php
            if(isset($_SESSION["$ip"]))
            $user = $_SESSION["$ip"];
            $query = "SELECT user, pass, role, full_name FROM ServiceApp_ASUS_Users WHERE user='$user'";
            $rez = mysqli_query($dbConnection,$query);
            $niz = mysqli_fetch_array($rez);
            $user = $niz[0];
            $pass = $niz[1];
            $role = $niz[2];
            $full_name = $niz[3];
                    
            echo("HI! <b><i>$full_name</i></b><br><br>");
            
            
             if(isset($_POST["change_pass"])) {
                $pass2 = md5($_POST["pass2"]);
            
	            if(empty($_POST["pass2"])) {
	                echo ('<p style="color:red;">*Pass field empty.</p>');
	            }
	            else {
	                $stmt_update = $dbConnection->prepare("UPDATE ServiceApp_ASUS_Users SET pass=? WHERE user=?");
	                $stmt_update->bind_param('ss', $pass2, $user);
	                $stmt_update->execute();
	                
	                echo("<p style='color:red;'>*Password changed.</p>");
	                echo('<script> setTimeout(function () { window.location.href = "users.php"; }, 1000); </script>');
	            }
            }
            ?>
            <!--<hr style="width:700px; height: 10px; border: 0; box-shadow: 0 10px 10px -10px #8c8b8b inset;">-->
            <div class="round">
	            <form name="form" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
    	            <div class="form_settings" style="font-size:14px;">
    	                <label style="text-align:right;">Username:&nbsp;&nbsp;&nbsp;</label><label><b><i><?php echo $user;?></i></b></label><br><br>
    	                <label style="text-align:right;">Password:&nbsp;&nbsp;&nbsp;&nbsp;</label><label><b><i><?php echo $pass;?></i></b></label>
    	                    <label style="text-align:right; padding-left: 50px;">New password:&nbsp;&nbsp;&nbsp;</label><label><input type="password" name="pass2" style="width:100px; height:12px;"></label>
    	                        <label style="padding-left: 20px;"><input type="submit" class="submit" name="change_pass" value="   Change pass   " style="margin-left: -10px;"></label><br><br>
    	                <label style="text-align:right; padding-right: 35px;">Role:&nbsp;&nbsp;&nbsp;</label><label><b><i><?php echo $role;?></i></b></label><br><br>
                    </div>
                </form>
            </div>
            <br><br>
            <?php
                if ($role == "A") {
            ?>
            <b>Create new user</b>
            <br><br>
            <?php
                if(isset($_POST["register"])) {
                    $username = trim($_POST['NewUser']);
                    $fullname = trim($_POST['NewName']);
                    $role     = $_POST['role'];
                    $password = md5($_POST['NewPass']);
                    $email    = trim($_POST['NewEmail']);
                    
                    if (empty($username)) {
                    echo("<p style='color:red;'>*Fill username.</p>");
                    }
                    else if (empty($fullname)) {
                        echo("<p style='color:red;'>*Fill full name.</p>");
                    }
                    else if (empty($role)) {
                        echo("<p style='color:red;'>*Fill role.</p>");
                    }
                    else if (empty($password)) {
                        echo("<p style='color:red;'>*Fill password.</p>");
                    }
                    else if (empty($email)) {
                        echo("<p style='color:red;'>*Fill Email.</p>");
                    }
                    else {
                        $stmt = $dbConnection->prepare("INSERT INTO ServiceApp_ASUS_Users (user, pass, role, full_name, email) VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param('sssss', $username, $password, $role, $fullname, $email);
                        $stmt->execute();
                        $stmt->store_result();
                        
                        echo("<p style='color: blue;'>New user registered.</p>");
                    }
                }
            ?>
            <div class="round">
                <form name="form" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
    	            <div class="form_settings" style="font-size:14px;">
    	                <label style="padding-right: 15px;">Username:</label><label style="padding-right: 30px;"><input type="text" name="NewUser" style="width:130px; height:12px; font-size:12px;"></label>
    	                <label style="padding-right: 25px;">Full Name:</label><label style="padding-right: 40px;"><input type="text" name="NewName" style="width:170px; height:12px; font-size:12px;"></label>
    	                <label style="padding-right: 20px;">Role:</label><label>
    	                    <select id="id" name="role" style="width:50px;">
                                <option value=""> - </option>
                                <option value="W">W - (Warehouse)</option>
                                <option value="A">A - (Admin)</option>
                                <option value="S">S - (Servicer)</option>
                            </select>
    	                </label>
    	                <br>
    	                <label style="padding-right: 18px;">Password:</label><label style="padding-right: 30px;"><input type="password" name="NewPass" style="width:130px; height:12px;"></label>
    	                <label style="padding-right: 19px;">E-mail:</label><label><input type="text" name="NewEmail" style="width:200px; height:12px; font-size:12px;"></label>
    	                <label style="padding-right: 14px;">&nbsp;</label><label style="padding-right: 20px;"><input class="submit" style="margin: 2px 0 0 10px;" type="submit" name="register" value="  Create new user  " /></label>
                    </div>
                </form>
            </div>
            <?php
                }
            ?>
            <br><br>
            <?php
                if ($role == "A") {
            ?>
            <b>List of users</b>
            <br><br>
            <?php
                $result = mysqli_query($dbConnection, "SELECT user, pass, role, full_name, email FROM ServiceApp_ASUS_Users");
                $developer_records = array();
                
                while ($rows = mysqli_fetch_array($result)) {
                    $developer_records[] = $rows;
                }
            ?>
            <div class="round">
                <table>
                    <tr>
                        <td style="padding-right: 50px; background: #a0b9ff;">User:</td>
                        <td style="padding-right: 80px; background: #a0b9ff;">Pass:</td>
                        <td style="padding-right: 50px; background: #a0b9ff;">Full Name:</td>
                        <td style="padding-right: 80px; background: #a0b9ff;">Email:</td>
                        <td style="padding-right: 5px; background: #a0b9ff;">Role:</td>
                    </tr>
                    <?php
                        foreach ($developer_records as $developer) {
                    ?>
                    <tr>
                        <td style="padding-right: 10px;"><?php echo $developer['user']; ?></td>
                        <td style="padding-right: 10px;"><?php echo $developer['pass']; ?></td>
                        <td style="padding-right: 10px;"><?php echo $developer['full_name']; ?></td>
                        <td style="padding-right: 10px;"><?php echo $developer['email']; ?></td>
                        <td style="text-align: center;"><?php echo $developer['role']; ?></td>
                    </tr>
                    <?php
                        }
                    ?>
                </table>
            </div>
            <?php
                }
            ?>
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
