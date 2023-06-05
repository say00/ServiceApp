<?php
    session_start();
    $ip = $_SERVER["REMOTE_ADDR"];
    if(!isset($_SESSION["$ip"])) {
        echo('<script> window.location.href ="notice.php"; </script>');
    }
    
    //Glavna konekcija na bazu
    $dbConnection = mysqli_connect("localhost","mojaprez_baza","edf951323","mojaprez_baza");
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    if(isset($_POST["signout"])){
        unset($_SESSION["$ip"]);
    }
    
    //Restrikcija odredjene vrste korisnika kompletnom pristupu stranici
    if(isset($_SESSION["$ip"])) {
        $user = $_SESSION["$ip"];
        $query_user = mysqli_query($dbConnection, "SELECT role FROM ServiceApp_ASUS_Users WHERE user='$user'");
        $niz = mysqli_fetch_array($query_user);
        $role = $niz[0];
        
        if($role == "W") {
            echo('<script> window.location.href = "notice2.php"; </script>');
        }
    }
    
    //Konencija sa bazom za auto popunjavanje polja prilikom unosa parta
    $connection = mysqli_connect("localhost","mojaprez_baza","edf951323","mojaprez_baza") or die("Error " . mysqli_error($connection));
    $sql = "SELECT part_noReturning, quantity FROM ServiceApp_ASUS_BAD";
    $result = mysqli_query($connection, $sql) or die("Error " . mysqli_error($connection));
    
    $sql2 = "SELECT Recycling_part, quantity FROM ServiceApp_ASUS_warehouseRecycling";
    $result2 = mysqli_query($connection, $sql2) or die("Error " . mysqli_error($connection));
    
    //Prikaz podataka iz baze Recycle
    $partS    = trim($_GET['partS']);
    $kayakoS  = trim($_GET['kayakoS']);
    $ret_date = date("Y-m-d");
      
    $partS   = htmlentities($partS);
    $kayakoS = htmlentities($kayakoS);
    
    $resultset = mysqli_query($dbConnection, ("SELECT r.id_part, r.Ordered_part, r.Received_part, r.Recycling_part, r.Description_part, r.Kayako, r.RMA, i.Invoice, i.Date_of_receipt, r.Output_date 
                                               FROM ServiceApp_ASUS_warehouseRecycling r 
                                               INNER JOIN ServiceApp_ASUS_requisitionWO w
                                               ON r.id_part = w.id_part
                                               INNER JOIN ServiceApp_ASUS_invoices i
                                               ON r.id_part = i.part_id"));
    
    $developer_records = array();
    
    while($rows = mysqli_fetch_assoc($resultset)) {
        $developer_records[] = $rows;
    }
    
    $date1 = new DateTime('2017-12-05');
    $date2 = new DateTime('2017-12-07');
    $diff  = date_diff($date1,$date2);
    
    
    if(isset($_POST["export_data"])) {
        //Povlaci sve ID-jeve iz Recycling baze i u bazi Requisition popunjava polje return.
    	$resultID = mysqli_query($dbConnection, "SELECT id_part FROM ServiceApp_ASUS_warehouseRecycling");
    	
    	$i = 0;
    	
    	while($rowsID = mysqli_fetch_array($resultID)) {
    	    $id_1[$i] = $rowsID[0];
    	    $i++;
    	}
    	
    	foreach($id_1 as $k=>$v) {
    	    $update_req = mysqli_query($dbConnection, "UPDATE ServiceApp_ASUS_requisitionWO SET recycle='YES' WHERE id_part='$id_1[$k]'");
    	}
        $delete_from_Recycle = mysqli_query($dbConnection,"DELETE FROM ServiceApp_ASUS_warehouseRecycling");
        
        
        //Forsira brauzer da umesto prikaza u prozoru downloaduje sadrzaj u excel fajlu.
    	$filename = "Returning_Data_RECYCLING_".date('Y-m-d') . ".xls";		
    	header("Content-Type: application/vnd.ms-excel");
    	header("Content-Disposition: attachment; filename=\"$filename\"");
    	$show_coloumn = false;
    	if(!empty($developer_records)) {
    	    foreach($developer_records as $record) {
    		    if(!$show_coloumn) {
    		        // display field/column names in first row
    		        echo implode("\t", array_keys($record)) . "\n";
    		        $show_coloumn = true;
    		    }
    		    echo implode("\t", array_values($record)) . "\n";
    	    }
    	}
    	exit;
    }
    
    if(isset($_post["refresh"])) {
        echo('<script> setTimeout(function () { window.location.href = "recycling.php"; }, 500); </script>');
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
            <a hreff="#" tooltip="Create recycle document. Creating this document you are removing parts from ASUS recycle warehouse.">
                <img src="images/question_mark.png" alt="question_mark" height="30" width="30" style="margin: -11px 0 0 -15px; cursor:pointer;">
            </a>
            <br>
        <?php
            if(isset($_POST["transfer_part"])) {
                $iss_part_no = trim($_POST["iss_part_no"]);
                $date        = date("Y-m-d H:i:s", time());
                $qty         = "1";
                
                //Na osnovu "iss_part_no" izvlacimo "part_description" iz druge baze
                $query = mysqli_query($dbConnection,"SELECT id_part, part_noO, part_noR, part_description, kayako, rma FROM ServiceApp_ASUS_BAD WHERE part_noReturning='$iss_part_no'");
                $niz = mysqli_fetch_array($query);
                $id_part    = $niz[0];
                $part_noO   = $niz[1];
                $part_noR   = $niz[2];
                $part_desc_ = $niz[3];
                $kayako     = $niz[4];
                $rma        = $niz[5];
                
                $sql_status = mysqli_query($dbConnection, "SELECT status FROM ServiceApp_ASUS_requisitionWO WHERE id_part='$id_part'");
                $stat = mysqli_fetch_array($sql_status);
                $status = $stat[0];
                
                if(empty($iss_part_no)) {
                    echo('<p style="color:red; font-size: 15px; margin-bottom: -25px;"><br>Field "part no" empty.</p>');
                }
                
                else if($status != "Z") {
                    echo('<p style="color:red; font-size: 15px; margin-bottom: -25px;"><br>Your requisition needs to be in status Z.</p>');
                }
                else {
                    $stmt = $dbConnection->prepare('INSERT INTO ServiceApp_ASUS_warehouseRecycling (id_part, Ordered_part, Received_part, Recycling_part, Description_part, Kayako, RMA, quantity, Output_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                    $stmt->bind_param('issssssis', $id_part, $part_noO, $part_noR, $iss_part_no, $part_desc_, $kayako, $rma, $qty, $date);
                    $stmt->execute();
                    
                    echo('<p style="color: blue; margin-bottom: -45px;"><br>Part successfully transferred to Local Scrap.<p>');
                    $delete_from_BAD = mysqli_query($dbConnection,"DELETE FROM ServiceApp_ASUS_BAD WHERE id_part='$id_part'");
                   
                    echo('<script> setTimeout(function () { window.location.href = "recycling.php"; }, 2000); </script>');
                }
            }
            
            if(isset($_POST['transfer_BAD'])) {
                $iss_part_noU = trim($_POST['iss_part_noU']);
                $qtyU = "1";
                
                $queryU = mysqli_query($dbConnection, "SELECT id_part, Ordered_part, Received_part, Description_part, Kayako, RMA FROM ServiceApp_ASUS_warehouseRecycling WHERE Recycling_part='$iss_part_noU'");
                $rows = mysqli_fetch_array($queryU);
                $id_partU   = $rows[0];
                $part_noOU  = $rows[1];
                $part_noRU  = $rows[2];
                $part_descU = $rows[3];
                $kayakoU    = $rows[4];
                $rmaU       = $rows[5];
                
                $queryI = mysqli_query($dbConnection, "SELECT requisition_date FROM ServiceApp_ASUS_requisitionWO WHERE id_part='$id_partU'");
                $rowsI = mysqli_fetch_array($queryI);
                $date_shortI = $rowsI[0];
                
                if(empty($iss_part_noU)) {
                     echo('<p style="color:red; font-size: 15px; margin-bottom: -25px;"><br>Field "part no" empty.</p>');
                }
                else {
                    $stmt = $dbConnection->prepare('INSERT INTO ServiceApp_ASUS_BAD (id_part, part_noO, part_noR, part_noReturning, part_description, kayako, rma, quantity, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                    $stmt->bind_param('issssssis', $id_partU, $part_noOU, $part_noRU, $iss_part_noU, $part_descU, $kayakoU, $rmaU, $qtyU, $date_shortI);
                    $stmt->execute();
        
                    echo('<p style="color:blue; margin-bottom: -25px;"><br>Part successfully transferred to BAD warehouse.</p>');
                    $delete_from_Local_Scrap = mysqli_query($dbConnection, "DELETE FROM ServiceApp_ASUS_warehouseRecycling WHERE id_part='$id_partU'");
                    echo('<script> setTimeout(function () { window.location.href = "recycling.php"; }, 2000); </script>');
                }
            }
        ?>
        <div class="content">
            <h2>Step 1:</h2>
            <fieldset class="field" style="background-color:#d9d9d9;">
                <legend>Transfer parts from BAD to Local Scrap</legend>
            <form name="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                <div class="form_settings">
                    <label style="padding-right: 15px;">Part No. for transferring:</label><label><input type="text" name="iss_part_no" list="categoryname" autocomplete="off" id="part_no2" placeholder="Type Part No..." style="width:202px;" /></label>
                        <datalist id="categoryname">
                        <?php
                            while($row = mysqli_fetch_array($result)) {
                        ?>
                            <option value="<?php echo $row['part_noReturning']; ?>"><?php echo $row['part_noReturning']; ?></option>
                        <?php
                            }
                        ?>
                        </datalist>
                    <input class="submit" type="submit" style="margin: 0 0 0 20px;" name="transfer_part" value="   Transfer to Local Scrap   " />
                </div>
            </form>
            </fieldset>
           
            <h2>Step 2:</h2>
            <Fieldset class="field">
                <legend>List of parts that are stored in Local Scrap</legend>
            <!--<form name="forma" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
              <div class="form_settings">
                <label style="padding-right: 10px;">Part No:</label><label style="padding-right: 50px;"><input type="text" name="partS" style="width:150px;" /></label><label style="padding-right: 10px;">Kayako ticket:</label><label style="padding-right: 20px;"><input type="text" name="kayakoS" style="width:150px;" /></label>
                <input class="submit" style="margin: 0 0 0 10px;" type="submit" name="show_data" value="  Show data  " />
              </div>-->
            </form>
            <table>
              <tr>
                <td style="padding-right: 40px; background: #a0b9ff;"><b>ID</b></td>
                <td style="padding-right: 70px; background: #a0b9ff;"><b>Part No. Ordered</b></td>
                <td style="padding-right: 70px; background: #a0b9ff;"><b>Part No. Received</b></td>
                <td style="padding-right: 70px; background: #a0b9ff;"><b>Part No. for Recycling</b></td>
                <td style="padding-right: 150px; background: #a0b9ff;"><b>Part description</b></td>
                <td style="padding-right: 50px; background: #a0b9ff;"><b>Kayako</b></td>
                <td style="padding-right: 70px; background: #a0b9ff;"><b>RMA</b></td>
                <td style="padding-right: 20px; background: #a0b9ff;"><b>Date Received</b></td>
                <td style="padding-right: 20px; background: #a0b9ff;"><b>Today's Date</b></td>
                <td style="padding-right: 5px; background: #a0b9ff;"><b>No. of days in service</b></td>
                <!--<td style="padding-right: 30px; background: #a0b9ff;"></td>-->
              </tr>
              <?php
                    foreach ($developer_records as $developer) {
              ?>
              <tr>
                <td><?php echo $developer['id_part']; ?></td>
                <td><?php echo $developer['Ordered_part']; ?></td>
                <td><?php echo $developer['Received_part']; ?></td>
                <td><?php echo $developer['Recycling_part']; ?></td>
                <td><?php echo $developer['Description_part']; ?></td>
                <td><?php echo $developer['Kayako']; ?></td>
                <td><?php echo $developer['RMA']; ?></td>
                <td><?php echo $developer['Date_of_receipt']; ?></td>
                <td><?php echo $ret_date; ?></td>
                <td><?php 
                        $then = $developer['Date_of_receipt'];
                        $then = strtotime($then);
                
                        $now = $ret_date;
                        $now = strtotime($now);
                        $difference = $now - $then;
                        $days = floor($difference / (60*60*24) );
                 
                        echo "$days days"; ?>
                </td>
                <!--<td style="padding: 3px 10px 3px 10px;"><input type="checkbox" name="part" value="part"></td>-->
              </tr>
              <?php
                    }
              ?>
            </table>
            <div class="form_settings1">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <button class="submit" id="export_data" name='export_data' value="Export to excel" >&nbsp;&nbsp;Create Return Document&nbsp;&nbsp;</button>
                </form>
                
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <button class="submit" id="refresh" name='refresh' value="Refresh table" >&nbsp;&nbsp;Refresh table&nbsp;&nbsp;</button><label style="font-size: 12px; margin-left: 10px;">*Refresh table after Creating Return Document</label>
                </form>
            </div>
            </Fieldset>
            <br>
            <hr style="height: 6px; background: url(http://ibrahimjabbari.com/english/images/hr-11.png) repeat-x 0 0; border: 0;">
            <br>
            <fieldset class="field" style="background-color:#d9d9d9;">
                <legend style="color: red;">Transfer parts back from Local Scrap to BAD</legend>
            <form name="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                <div class="form_settings">
                    <label style="padding-right: 15px;">Part No. for transferring:</label><label><input type="text" name="iss_part_noU" list="categoryname2" autocomplete="off" id="part_no2" placeholder="Type Part No..." style="width:202px;" /></label>
                        <datalist id="categoryname2">
                        <?php
                            while($row = mysqli_fetch_array($result2)) {
                        ?>
                            <option value="<?php echo $row['Recycling_part']; ?>"><?php echo $row['Recycling_part']; ?></option>
                        <?php
                            }
                        ?>
                        </datalist>
                    <input class="submit" type="submit" style="margin: 0 0 0 20px;" name="transfer_BAD" value="   Transfer back to BAD   " />
                </div>
            </form>
            </fieldset>
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