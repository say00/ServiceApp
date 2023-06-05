<?php
    session_start();
    $ip = $_SERVER["REMOTE_ADDR"];
    if(!isset($_SESSION["$ip"])) {
        echo('<script> window.location.href = "notice.php"; </script>');
    }
?>
<!DOCTYPE HTML>
<html>

<head>
  <title>Service App ASUS - Receiving</title>
  <meta name="keywords" content="website keywords, website keywords" />
  <meta name="description" content="website description" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <link rel="stylesheet" type="text/css" href="css/style.css" />
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <!-- modernizr enables HTML5 elements and feature detects -->
  <script type="text/javascript" src="js/modernizr-1.5.min.js"></script>
 <script>
  $( function() {
    $('#datepickerFrom').datepicker({ dateFormat: 'yy-mm-dd' }).val();
    $('#datepickerTo').datepicker({ dateFormat: 'yy-mm-dd' }).val();
  } );
  </script>
  <script type="text/javascript" src="js/jquery.easing-sooper.js"></script>
  <script type="text/javascript" src="js/jquery.sooperfish.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('ul.sf-menu').sooperfish();
      $('.top').click(function() {$('html, body').animate({scrollTop:0}, 'fast'); return false;});
    });
  </script>
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
        
        if(isset($_SESSION["$ip"])) {
            $user = $_SESSION["$ip"];
            $query_user = mysqli_query($dbConnection, "SELECT role FROM ServiceApp_ASUS_Users WHERE user='$user'");
            $niz = mysqli_fetch_array($query_user);
            $role = $niz[0];
            
            if($role == "W") {
                echo('<script> window.location.href = "notice2.php"; </script>');
            }
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
        <a hreff="#" tooltip="List/search all inserted invoices, and parts in that invoices.">
            <img src="images/question_mark.png" alt="question_mark" height="30" width="30" style="margin: -11px 0 0 -15px; cursor:pointer;">
        </a>
        <br>
      <div class="content">
          <div class="round">
        <form name="forma" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
          <div class="form_settings">
            <label style="padding-right: 20px;">Return No:</label><label style="padding-right: 50px;"><input type="text" name="returnS" style="width:150px;" /></label>
            <label style="padding-right: 32px;">Date from:</label><label style="padding-right: 50px;"><input type="text" name="dateFromS" id="datepickerFrom" style="width:150px;"></label>
            <label style="padding-right: 34px;">Date to:</label><label><input type="text" name="dateToS" id="datepickerTo" style="width:150px;"></label>
            <label>&nbsp;</label><input class="submit" style="margin: 0 0 0 10px;" type="submit" name="show_data" value="  Show data  " />
            <br>
            <label style="padding-right: 32px;">Part No.:</label><label style="padding-right: 50px;"><input type="text" name="partS" value="" style="width:150px;" /></label>
            <label style="padding-right: 7px;">Kayako Ticket:</label><label style="padding-right: 50px;"><input type="text" name="kayakoS" value="" style="width:150px;" /></label>
            <label style="padding-right: 9px;">Asus Ticket:</label><label style="padding-right: 50px;"><input type="text" name="asusS" value="" style="width:150px;" /></label>
            
          </div>
        </form>
        </div>
        <br>
        
        <!-- U tabeli ispod ce se prikazivati podaci koji bivaju isfiltriranisu iznad -->
        <?php
            if(isset($_GET['show_data'])){
                $returnS   = trim($_GET['returnS']);
                $dateFromS = trim($_GET['dateFromS']);
                $partS     = trim($_GET['partS']);
                $dateToS   = trim($_GET['dateToS']);
                $kayakoS   = trim($_GET['kayakoS']);
                $asusS     = trim($_GET['asusS']);
                
                $returnS  = htmlentities($returnS);
                $dateFromS = htmlentities($dateFromS);
                $partS     = htmlentities($partS);
                $dateToS   = htmlentities($dateToS);
                $kayakoS   = htmlentities($kayakoS);
                $asusS     = htmlentities($asusS);
                
                $t = date("Y-m-d");
                
                if ($dateFromS != "" and $dateToS != ""){
                    $query = "SELECT Output_date, return_no, id FROM ServiceApp_ASUS_BAD_RTV_H WHERE (Output_date BETWEEN '$dateFromS' AND '$dateToS') GROUP BY return_no ORDER BY date_long DESC";
                }
                else if ($dateFromS != "") {
                    $query = "SELECT Output_date, return_no, id FROM ServiceApp_ASUS_BAD_RTV_H WHERE (Output_date BETWEEN '$dateFromS' AND '$t') GROUP BY return_no ORDER BY date_long DESC";
                }
                else if ($returnS != "" or $partS != "") {
                    $query = "SELECT Output_date, return_no, id FROM ServiceApp_ASUS_BAD_RTV_H WHERE Returning_part LIKE '%$partS%' AND return_no LIKE '%$returnS%' GROUP BY return_no ORDER BY date_long DESC";
                }
                else if ($kayakoS != "" or $asusS != "") {
                    $query = "SELECT Output_date, return_no, id FROM ServiceApp_ASUS_BAD_RTV_H WHERE Kayako LIKE '%$kayakoS%' AND rma LIKE '%$asusS%' GROUP BY return_no ORDER BY date_long DESC";
                }
                else {
                    $query = "SELECT Output_date, return_no, id FROM ServiceApp_ASUS_BAD_RTV_H GROUP BY return_no ORDER BY date_long DESC";
                }
                $result = mysqli_query($dbConnection, $query);
                
                $developer_records = array();
            
                while($rows = mysqli_fetch_array($result)) {
                    $developer_records[] = $rows;
                }
            }
        ?>
        <div class="round2">
        <table>
          <tr>
            <td style="padding-right: 80px; background: #a0b9ff;"><b>Return No.</b></td>
            <td style="padding-right: 30px; background: #a0b9ff;"><b>Return Date</b></td>
          </tr>
          <?php
                foreach ($developer_records as $developer) {
          ?>
          <tr>
              <td><a href="returning_used_historyDetails.php?return_no=<?php echo $developer['return_no']; ?>" style="color: black;"><img src="images/arrow_right.png" alt="Arrow right" height="15" width="15" style="margin: -2px 5px -4px 0; cursor:pointer;"><?php echo $developer['return_no']; ?></a></td>
              <td><?php echo $developer['Output_date']; ?></td>
          </tr>
          <?php
                }
          ?>
        </table>
        </div>
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
</body>
</html>