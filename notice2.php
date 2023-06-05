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
        include("konekcija.php");
        
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
                if(isset($_SESSION['$ip'])) {
            ?>
            <li><a href="#">Service</a>
              <ul>
                <li><a href="receiving.php">Receiving</a></li>
                <li><a href="work_order.php">Requisition and Work Order</a></li>
                <li><a href="returning_used.php">Returning used</a></li>
                <li><a href="returning_unused.php">Returning UNUSED</a></li>
                <li><a href="recycling.php">Recycling</a></li>
              </ul>
            </li>
            <li><a href="#">Reports</a>
              <ul>
                <li><a href="summary.php">Summary</a></li>
                <li><a href="current_stock.php">Review of current stock</a></li>
              </ul>
            </li>
            <li><a href="#">Warehouse</a>
              <ul>
                <li><a href="requisition.php">Requisition</a></li>
              </ul>
            </li>
            <?php
                }
            ?>
            <li><a href="contact.php">Contact Us</a></li>
          </ul>
        </div>
      </nav>
    </header>
    <?php
        if(isset($_SESSION["$ip"])){
    ?>
    <div class="form_settings" style="float: right; margin: -46px 5px 0 0;">
        <form name="form" action="index.php" method="POST">
   	        <input type="submit" class="submit" name="signout" value="   Logout   " />
	    </form>
	</div>
	<?php
	    }
	?>
    <div id="site_content">
      <div class="content" style="padding-top: 35px; font-size: 18px;">
          <p style="color:red;">You don't have permission to access this page. Please <a href="#">contact</a> administrator.</p>
      </div>
    </div>
    <div id="scroll">
      <a title="Scroll to the top" class="top" href="#"><img src="images/top.png" alt="top" /></a>
    </div>
    <footer>
      <p><img src="images/twitter.png" alt="twitter" />&nbsp;<img src="images/facebook.png" alt="facebook" />&nbsp;<img src="images/rss.png" alt="rss" /></p>
      <p><a href="index.php">Home</a> | <?php if(isset($_SESSION["$ip"])){ ?><a href="receiving_new_invoice.php">Receiving new invoice (Parts)</a> | <a href="work_order_requisition.php">Create new WO</a> | <a href="current_stock.php">Current Stock</a> |<?php } ?> <a href="contact.php">Contact Us</a></p>
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
