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
        <div class="content">
            <!-- Konekcija ispod je zaduzena za auto popunjavanje teks polja "part_no" ukoliko u bazi postoji identican part. -->
            <?php
                $connection = mysqli_connect("localhost","mojaprez_baza","edf951323","mojaprez_baza") or die("Error " . mysqli_error($connection));
                $result = mysqli_query($connection, "SELECT part_no FROM ServiceApp_ASUS_parts_no_lists") or die ("Error " . mysqli_error($connection));
            ?>
            <div class="round">
                <form name="forma" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                    <div class="form_settings">
                        <label style="padding-right: 20px;">Part No:</label>
                        <label><input type="text" list="categoryname" name="partS" style="width:150px;" />
                            <datalist id="categoryname">
                                <?php
                                    while($row = mysqli_fetch_array($result)) {
                                ?>
                                        <option value="<?php echo $row['part_no']; ?>"><?php echo $row['part_no']; ?></option>
                                <?php
                                    }
                                ?>
                            </datalist>
                        </label>
                        <label>&nbsp;</label><input class="submit" style="margin: 0 0 0 10px;" type="submit" name="show_data" value="  Show data  " />
                    </div>
                </form>
            </div>
            <br>
        
        <!-- Magacin Blank -->
        <?php
            $partS = trim($_GET['partS']);
            $partS = htmlentities($partS);
            
            $query = "SELECT part_noO, part_noR, part_description, quantity FROM ServiceApp_ASUS_Blank_OK WHERE part_noR LIKE '%$partS%' ORDER BY part_noR";
            $result = mysqli_query($dbConnection, $query);
            
            $developer_records = array();
            
            while($rows = mysqli_fetch_array($result)) {
                $developer_records[] = $rows;
            }
        ?>
        <br>
        <hr style="height: 6px; background: url(http://ibrahimjabbari.com/english/images/hr-11.png) repeat-x 0 0; border: 0;">  
        <br>
        <div class="round2">
        <u style="color: black;">Blank - OK warehouse</u>
        <table>
            <tr>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. Ordered</b></td>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. Received</b></td>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. For Returning</b></td>
                <td style="padding-right: 300px; background: #a0b9ff; color: black;"><b>Part Description</b></td>
                <td style="padding-right: 5px; background: #a0b9ff; color: black;"><b>Quantity</b></td>
            </tr>
            <?php
                foreach ($developer_records as $developer) {
            ?>
            <tr>
                <td style="padding-right: 10px;"><?php echo $developer['part_noO']; ?></td>
                <td style="padding-right: 10px;"><?php echo $developer['part_noR']; ?></td>
                <td style="padding-right: 10px;"></td>
                <td style="padding-right: 10px;"><?php echo $developer['part_description']; ?></td>
                <td style="padding-right: 5px; text-align: right;"><?php echo $developer['quantity']; ?></td>
            </tr>
            <?php
                }
            ?>
        </table>
        </div>
        
        <!-- Magacin BAD -->
        <?php
            $partS = trim($_GET['partS']);
            $partS = htmlentities($partS);
            
            $query = "SELECT part_noO, part_noR, part_noReturning, part_description, quantity FROM ServiceApp_ASUS_BAD WHERE part_noR LIKE '%$partS%' ORDER BY part_noR";
            $result = mysqli_query($dbConnection, $query);
            
            $developer_records = array();
            
            while($rows = mysqli_fetch_array($result)) {
                $developer_records[] = $rows;
            }
        ?>
        <br>
        <hr style="height: 6px; background: url(http://ibrahimjabbari.com/english/images/hr-11.png) repeat-x 0 0; border: 0;">  
        <br>
        <div class="round2">
        <u style="color: black;">BAD warehouse</u>
        <table>
            <tr>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. Ordered</b></td>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. Received</b></td>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. For Returning</b></td>
                <td style="padding-right: 300px; background: #a0b9ff; color: black;"><b>Part Description</b></td>
                <td style="padding-right: 5px; background: #a0b9ff; color: black;"><b>Quantity</b></td>
            </tr>
            <?php
                foreach ($developer_records as $developer) {
            ?>
            <tr>
                <td style="padding-right: 10px;"><?php echo $developer['part_noO']; ?></td>
                <td style="padding-right: 10px;"><?php echo $developer['part_noR']; ?></td>
                <td style="padding-right: 10px;"><?php echo $developer['part_noReturning'] ?></td>
                <td style="padding-right: 10px;"><?php echo $developer['part_description']; ?></td>
                <td style="padding-right: 5px; text-align: right;"><?php echo $developer['quantity']; ?></td>
            </tr>
            <?php
                }
            ?>
        </table>
        </div>
        
        <!-- Magacin BAD RTV -->
        <?php
                $partS = trim($_GET['partS']);
                $partS = htmlentities($partS);
            
                $query = "SELECT Ordered_part, Received_part, Returning_part, Description_part, Quantity FROM ServiceApp_ASUS_BAD_RTV WHERE Returning_part LIKE '%$partS%' ORDER BY Received_part";
                $result = mysqli_query($dbConnection, $query);
                
                $developer_records = array();
                
                while($rows = mysqli_fetch_array($result)) {
                    $developer_records[] = $rows;
                }
        ?>
        <br>
        <hr style="height: 6px; background: url(http://ibrahimjabbari.com/english/images/hr-11.png) repeat-x 0 0; border: 0;">  
        <br>
        <div class="round2">
        <u style="color: black;">BAD RTV warehouse</u>
        <table>
            <tr>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. Ordered</b></td>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. Received</b></td>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. For Returning</b></td>
                <td style="padding-right: 300px; background: #a0b9ff; color: black;"><b>Part Description</b></td>
                <td style="padding-right: 5px; background: #a0b9ff; color: black;"><b>Quantity</b></td>
            </tr>
            <?php
                foreach ($developer_records as $developer) {
            ?>
            <tr>
                <td style="padding-right: 10px;"><?php echo $developer['Ordered_part']; ?></td>
                <td style="padding-right: 10px;"><?php echo $developer['Received_part']; ?></td>
                <td style="padding-right: 10px;"><?php echo $developer['Returning_part']; ?></td>
                <td style="padding-right: 10px;"><?php echo $developer['Description_part']; ?></td>
                <td style="padding-right: 5px; text-align: right;"><?php echo $developer['Quantity']; ?></td>
            </tr>
            <?php
                }
            ?>
        </table>
        </div>
        
        <!-- Magacin Recycle -->
        <?php
                $partS = trim($_GET['partS']);
                $partS = htmlentities($partS);
                
                $query = "SELECT Ordered_part, Received_part, Recycling_part, Description_part, quantity FROM ServiceApp_ASUS_warehouseRecycling WHERE Recycling_part LIKE '%$partS%' ORDER BY Received_part";
                $result = mysqli_query($dbConnection, $query);
                
                $developer_records = array();
                
                while($rows = mysqli_fetch_assoc($result)) {
                    $developer_records[] = $rows;
                }
        ?>
        <br>
        <hr style="height: 6px; background: url(http://ibrahimjabbari.com/english/images/hr-11.png) repeat-x 0 0; border: 0;">
        <br>
        <div class="round2">
        <u style="color: black;">Recycling warehouse</u>
        <table>
            <tr>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. Ordered</b></td>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. Received</b></td>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. For Recycling</b></td>
                <td style="padding-right: 300px; background: #a0b9ff; color: black;"><b>Part Description</b></td>
                <td style="padding-right: 5px; background: #a0b9ff; color: black;"><b>Quantity</b></td>
            </tr>
            <?php
                foreach ($developer_records as $developer) {
            ?>
            <tr>
                <td style="padding-right: 10px;"><?php echo $developer ['Ordered_part']; ?></td>
                <td style="padding-right: 10px;"><?php echo $developer ['Received_part']; ?></td>
                <td style="padding-right: 10px;"><?php echo $developer ['Recycling_part']; ?></td>
                <td style="padding-right: 10px;"><?php echo $developer ['Description_part']; ?></td>
                <td style="padding-right: 5px; text-align: right;"><?php echo $developer ['quantity']; ?></td>
            </tr>
            <?php
                }
            ?>
        </table>
        </div>
        
        <!-- Magacin RTV SLOW -->
        <?php
                $partS = trim($_GET['partS']);
                $partS = htmlentities($partS);

                $query = "SELECT Ordered_part, Received_part, Description_part, quantity FROM ServiceApp_ASUS_RTV_SLOW WHERE Received_part LIKE '%$partS%' ORDER BY Received_part";
                $result = mysqli_query($dbConnection, $query);
                
                $developer_records = array();
                
                while($rows = mysqli_fetch_array($result)) {
                    $developer_records[] = $rows;
                }
        ?>
        <br>
        <hr style="height: 6px; background: url(http://ibrahimjabbari.com/english/images/hr-11.png) repeat-x 0 0; border: 0;">
        <br>
        <div class="round2">
        <u style="color: black;">RTV SLOW warehouse</u>
        <table>
            <tr>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. Ordered</b></td>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. Received</b></td>
                <td style="padding-right: 70px; background: #a0b9ff; color: black;"><b>Part No. For Returning</b></td>
                <td style="padding-right: 300px; background: #a0b9ff; color: black;"><b>Part Description</b></td>
                <td style="padding-right: 5px; background: #a0b9ff; color: black;"><b>Quantity</b></td>
            </tr>
            <?php
                foreach ($developer_records as $developer) {
            ?>
            <tr>
                <td style="padding-right: 10px;"><?php echo $developer['Ordered_part']; ?></td>
                <td style="padding-right: 10px;"><?php echo $developer['Received_part']; ?></td>
                <td style="padding-right: 10px;"><?php echo $developer['Received_part'] ?></td>
                <td style="padding-right: 10px;"><?php echo $developer['Description_part']; ?></td>
                <td style="padding-right: 5px; text-align: right;"><?php echo $developer['quantity']; ?></td>
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
