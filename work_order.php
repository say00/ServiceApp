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
  <title>Service App ASUS - Work Order</title>
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
        <a hreff="#" tooltip="Filter created requisitions, and get more option by clicking on specific requisition.">
            <img src="images/question_mark.png" alt="question_mark" height="30" width="30" style="margin: -11px 0 0 -15px; cursor:pointer;">
        </a>
        <br>
        <div class="content">
            <!-- Konekcija ispod je zaduzena za auto popunjavanje teks polja "part_no" ukoliko u bazi postoji identican part. -->
            <?php
                $connection = mysqli_connect("localhost","mojaprez_baza","edf951323","mojaprez_baza") or die("Error " . mysqli_error($connection));
                $result = mysqli_query($connection, "SELECT part_no FROM ServiceApp_ASUS_parts_no_lists") or die("Error " . mysqli_error($connection));
            ?>
            <div class="round">
                <form name="forma" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                    <div class="form_settings">
                        <label style="padding-right: 20px;">Kayako:</label><label style="padding-right: 50px;"><input type="text" name="kayakoS" style="width:150px"; value="" /></label>
                        <label style="padding-right: 20px;">Date from:</label><label style="padding-right: 50px;"><input type="text" name="dateFromS" id="datepickerFrom" style="width:150px;"></label>
                        <label style="padding-right: 20px;">Servise provider:</label><label><select id="full_name" name="full_nameS" style="width:180px;">
                            <option value=""> - </option>
                            <?php
                               $users = mysqli_query($dbConnection,"SELECT id, full_name FROM ServiceApp_ASUS_Users");
                               while($row = mysqli_fetch_array($users)){
                                   echo "<option value='". $row['full_name'] ."'>" .$row['full_name'] ."</option>" ;
                               }
                            ?>
                            </select></label>
                            <input class="submit" style="margin-left: 10px;" type="submit" name="show_data" value="  Show data  " />
                          <br>
                        <label style="padding-right: 12px;">Part No.:</label>
                        <label style="padding-right: 50px;"><input type="text" list="categoryname" name="partS" value="" style="width:150px;" />
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
                        <label style="padding-right: 35px;">Date to:</label><label style="padding-right: 50px;"><input type="text" name="dateToS" id="datepickerTo" style="width:150px;"></label>
                        <label style="padding-right: 73px;">Status:</label><label>
                            <select id="id" name="statusS" style="width:140px;">
                                <option value=""> - </option>
                                <option value="2">2 - (Not approved)&nbsp;&nbsp;&nbsp;</option>
                                <option value="3">3 - (Approved)</option>
                                <option value="Z">Z - (WO created)</option>
                            </select></label>
                    </div>
                </form>
            </div>
            <br>
            <div class="form_settings1">
                <form action="work_order_requisition.php">
                    <button class="submit">&nbsp;&nbsp;Create new Requisition&nbsp;&nbsp;</button>
                    <a href="#" tooltip2="Open new page for creating new requisition." style="color:red; text-decoration: none;">
                        <img src="images/question_mark.png" alt="question_mark" height="25" width="25" style="margin: 0 0 -7px 3px; cursor:pointer;">
                    </a>
                </form>
                
            </div>
            <br>
            
            <!-- U tabeli ispod ce se prikazivati podaci koji bivaju isfiltriranisu iznad -->
            <?php
                if(isset($_GET['show_data'])) {
                    $dateFromS  = trim($_GET['dateFromS']);
                    $kayakoS    = trim($_GET['kayakoS']);
                    $full_nameS = trim($_GET['full_nameS']);
                    $partS      = trim($_GET['partS']);
                    $dateToS    = trim($_GET['dateToS']);
                    $statusS    = trim($_GET['statusS']);
                    
                    $dateFromS  = htmlentities($dateFromS);
                    $kayakoS    = htmlentities($kayakoS);
                    $full_nameS = htmlentities($full_nameS);
                    $partS      = htmlentities($partS);
                    $dateToS    = htmlentities($dateToS);
                    $statusS    = htmlentities($statusS);
                    $t = date("Y-m-d");
                    
                    
                    if ($dateFromS != '' and $dateToS != '') {
                        $search = mysqli_query($dbConnection,"SELECT id, document_no, service_provider, requisition_date, status, id_part, issuing_part_no, part_description, qty, kayako, rma, receiving_warehouse
                        FROM ServiceApp_ASUS_requisitionWO WHERE (requisition_date BETWEEN '$dateFromS' AND '$dateToS') ORDER BY date_long DESC") or die (mysqli_error());
                    }
                    else if ($dateFromS != '') {
                        $search = mysqli_query($dbConnection,"SELECT id, document_no, service_provider, requisition_date, status, id_part, issuing_part_no, part_description, qty, kayako, rma, receiving_warehouse
                        FROM ServiceApp_ASUS_requisitionWO WHERE (requisition_date BETWEEN '$dateFromS' AND '$t') ORDER BY date_long DESC") or die (mysqli_error());
                    }
                    else if ($kayakoS != '' or $partS != '') {
                        $search = mysqli_query($dbConnection,"SELECT id, document_no, service_provider, requisition_date, status, id_part, issuing_part_no, part_description, qty, kayako, rma, receiving_warehouse
                        FROM ServiceApp_ASUS_requisitionWO WHERE kayako LIKE '%$kayakoS%' AND issuing_part_no LIKE '%$partS%' ORDER BY date_long DESC") or die (mysqli_error());
                    }
                    else if ($statusS != '') {
                        $search = mysqli_query($dbConnection,"SELECT id, document_no, service_provider, requisition_date, status, id_part, issuing_part_no, part_description, qty, kayako, rma, receiving_warehouse
                        FROM ServiceApp_ASUS_requisitionWO WHERE status LIKE '%$statusS%' ORDER BY date_long DESC") or die (mysqli_error());
                    }
                    else if ($full_nameS != '') {
                        $search = mysqli_query($dbConnection,"SELECT id, document_no, service_provider, requisition_date, status, id_part, issuing_part_no, part_description, qty, kayako, rma, receiving_warehouse
                        FROM ServiceApp_ASUS_requisitionWO WHERE service_provider LIKE '%$full_nameS%' ORDER BY date_long DESC") or die (mysqli_error());
                    }
                    else {
                        $search = mysqli_query($dbConnection,"SELECT id, document_no, service_provider, requisition_date, status, id_part, issuing_part_no, part_description, qty, kayako, rma, receiving_warehouse
                        FROM ServiceApp_ASUS_requisitionWO ORDER BY date_long DESC") or die (mysqli_error());
                    }
                    
                    $i = 0;
                    
                    while($niz = mysqli_fetch_array($search)) {
                        $id[$i]        = $niz[0];
                        $doc_no[$i]    = $niz[1];
                        $ser_prov[$i]  = $niz[2];
                        $req_date[$i]  = $niz[3];
                        $stat[$i]      = $niz[4];
                        $id_part[$i]   = $niz[5];
                        $part_no[$i]   = $niz[6];
                        $part_desc[$i] = $niz[7];
                        $qty[$i]       = $niz[8];
                        $kayako[$i]    = $niz[9];
                        $rma[$i]       = $niz[10];
                        $rec_ware[$i]  = $niz[11];
                        $i++;
                    }
                }
            ?>
            <div class="round2">
            <table>
                <tr>
                    <td style="padding-right: 20px; background: #a0b9ff;">Document No.</td>
                    <td style="padding-right: 15px; background: #a0b9ff;">Service provider</td>
                    <td style="padding-right: 25px; background: #a0b9ff;">Req. date</td>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>Status</b></td>
                    <td style="padding-right: 25px; background: #a0b9ff;">ID</td>
                    <td style="padding-right: 90px; background: #a0b9ff;">Part No.</td>
                    <td style="padding-right: 180px; background: #a0b9ff;">Part description</td>
                    <td style="padding-right: 10px; background: #a0b9ff;">Quantity</td>
                    <td style="padding-right: 20px; background: #a0b9ff;">Kayako ticket</td>
                    <td style="padding-right: 70px; background: #a0b9ff;">RMA ticket</td>
                    <td style="padding-right: 40px; background: #a0b9ff;">Receiving warehouse</td>
                </tr>
                <?php
                    if($id) {
                        foreach($doc_no as $k=>$v) {
                ?>
                <tr>
                    <td style="padding-right:10px;"><a href="work_orderDetails.php?id=<?php echo $id[$k]; ?>" style="text-decoration: underline; color: black;"><img src="images/arrow_right.png" alt="Arrow right" height="15" width="15" style="margin: -2px 5px -4px 0; cursor:pointer;"><?php echo $doc_no[$k]; ?></a></td>
                    <td style="padding-right:10px;"><?php echo $ser_prov[$k]; ?></td>
                    <td style="padding-right:10px;"><?php echo $req_date[$k]; ?></td>
                    <td style="padding-right:10px;"><b><?php echo $stat[$k]; ?></b></td>
                    <td style="padding-right:10px;"><?php echo $id_part[$k]; ?></td>
                    <td style="padding-right:10px;"><?php echo $part_no[$k]; ?></td>
                    <td style="padding-right:10px;"><?php echo $part_desc[$k]; ?></td>
                    <td style="padding-right:10px;"><?php echo $qty[$k]; ?></td>
                    <td style="padding-right:10px;"><?php echo $kayako[$k]; ?></td>
                    <td style="padding-right:10px;"><?php echo $rma[$k]; ?></td>
                    <td style="padding-right:5px;"><?php echo $rec_ware[$k]; ?></td>
                </tr>
                <?php
                        }
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
