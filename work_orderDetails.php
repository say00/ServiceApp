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
        
        $id_wo = $_GET['id']? $_GET['id']: $_POST['id'];
        
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
        <a hreff="#" tooltip="Details for selected Requisition.">
            <img src="images/question_mark.png" alt="question_mark" height="30" width="30" style="margin: -11px 0 0 -15px; cursor:pointer;">
        </a>
        <br>
        <div class="content">
            <?php
                $invoice_data = mysqli_query($dbConnection,'SELECT document_no, service_provider, requisition_date, status, id_part, issuing_part_no, receiving_part_no, part_description, qty, kayako, rma, issuing_warehouse, receiving_warehouse, ret, recycle, doa 
                                                            FROM ServiceApp_ASUS_requisitionWO WHERE id='.$id_wo);
                $niz = mysqli_fetch_array($invoice_data);
                $document_no       = $niz[0];
                $service_provider  = $niz[1];
                $requisition_date  = $niz[2];
                $status            = $niz[3];
                $id_part           = $niz[4];
                $issuing_part_no   = $niz[5];
                $receiving_part_no = $niz[6];
                $part_description  = $niz[7];
                $qty               = $niz[8];
                $kayako            = $niz[9];
                $rma               = $niz[10];
                $issuing_w         = $niz[11];
                $receiving_w       = $niz[12];
                $returning         = $niz[13];
                $recycle           = $niz[14];
                $doa               = $niz[15];

                if(isset($_POST["create_wo"])) {
                    $update_wo = "UPDATE ServiceApp_ASUS_requisitionWO SET status='Z' WHERE id='$id_wo'";
                    mysqli_query($dbConnection,$update_wo) or die("Greska: " . mysqli_error($update_wo));
                    
                    echo("<p style='color: blue;'>Work Order created.</p>");
                    echo("<script> setTimeout(function () { window.location.href = 'work_orderDetails.php?id=$id_wo'; }, 2000); </script>");
                }
                
                if(isset($_POST["bad"])) {
                    echo("<p style='color: red;'>&nbsp;Work order can be created only when Requisition is being approved or is in status 3.</p>");
                }
                
                if(isset($_POST["done"])) {
                    echo("<p>Your requisition is in status Z, and Work order has already been created.</p>");
                }
            ?>
            <div class="round">
            <table>
                <tr>
                    <td style="padding-right: 20px; background: #c7c6c6;"><b>ID</b></td>
                    <td style="padding-right: 20px; background: #c7c6c6;"><b>Doc No.</b></td>
                    <td style="padding-right: 20px; background: #c7c6c6;"><b>Servicer</b></td>
                    <td style="padding-right: 20px; background: #c7c6c6;"><b>Req Date</b></td>
                    <td style="padding-right: 10px; background: #c7c6c6;"><b>Status</b></td>
                    <td style="padding-right: 20px; background: #c7c6c6;"><b>Part No.</b></td>
                    <td style="padding-right: 20px; background: #c7c6c6;"><b>Returning Part No.</b></td>
                    <td style="padding-right: 20px; background: #c7c6c6;"><b>Part Description</b></td>
                    <td style="padding-right: 10px; background: #c7c6c6;"><b>Quantity</b></td>
                    <td style="padding-right: 10px; background: #c7c6c6;"><b>Kayako</b></td>
                    <td style="padding-right: 20px; background: #c7c6c6;"><b>RMA</b></td>
                    <td style="padding-right: 20px; background: #c7c6c6;"><b>Issuing warehouse</b></td>
                    <td style="padding-right: 20px; background: #c7c6c6;"><b>Receiving warehouse</b></td>
                    <td style="padding-right: 20px; background: #c7c6c6;"><b>Return</b></td>
                    <td style="padding-right: 20px; background: #c7c6c6;"><b>Recycle</b></td>
                    <td style="padding-right: 20px; background: #c7c6c6;"><b>DOA</b></td>
                    <td style="padding-right: 10px; background: #c7c6c6;"><b>Create WO</b></td>
                </tr>
                <tr>
                    <td style="padding-right:20px;"><?=$id_part;?></td>
                    <td style="padding-right:20px;"><?php echo $document_no; ?></td>
                    <td style="padding-right:20px;"><?php echo $service_provider; ?></td>
                    <td style="padding-right:20px;"><?php echo $requisition_date; ?></td>
                    <td style="padding-right:20px;"><b style="color: red;"><?php echo $status; ?></b></td>
                    <td style="padding-right:20px;"><?php echo $issuing_part_no; ?></td>
                    <td style="padding-right:20px;"><?php echo $receiving_part_no; ?></td>
                    <td style="padding-right:20px;"><?php echo $part_description; ?></td>
                    <td style="padding-right:20px;"><?php echo $qty; ?></td>
                    <td style="padding-right:20px;"><?php echo $kayako; ?></td>
                    <td style="padding-right:20px;"><?php echo $rma; ?></td>
                    <td style="padding-right:20px;"><?php echo $issuing_w; ?></td>
                    <td style="padding-right:20px;"><?php echo $receiving_w; ?></td>
                    <td style="padding-right:20px;"><?php echo $returning; ?></td>
                    <td style="padding-right:20px;"><?php echo $recycle; ?></td>
                    <td style="padding-right:20px;"><?php echo $doa; ?></td>
                    <?php
                        if($status == "3") {
                    ?>
                            <form name="form" action="<?php $_SERVER['SELF_PHP'] ?>" method="POST">
                                <td style="padding-right:5px;">
                                    <input class="submit" type="submit" name="create_wo" value="   Create WO   " style="margin: 0 0 -1px 1px; padding-bottom: 2px; height: 22px; cursor: pointer; border: 0; background: #222; color: #FFF;
                                    border-radius: 7px 7px 7px 7px; -moz-border-radius: 7px 7px 7px 7px; -webkit-border: 7px 7px 7px 7px;  ">
                                </td>
                            </form>
                    <?php
                        }if($status == "Z") {
                    ?>
                            <form name="form" action="<?php $_SERVER['SELF_PHP'] ?>" method="POST">
                                <td style="padding-right:5px;">
                                    <input class="submit" type="submit" name="done" value="   Create WO   " style="margin: 0 0 -1px 1px; padding-bottom: 2px; height: 22px; cursor: pointer; border: 0; background: #222; color: #FFF;
                                    border-radius: 7px 7px 7px 7px; -moz-border-radius: 7px 7px 7px 7px; -webkit-border: 7px 7px 7px 7px;  ">
                                </td>
                            </form>
                    <?php
                        }if($status == "2") {
                    ?>
                            <form name="form" action="<?php $_SERVER['SELF_PHP'] ?>" method="POST">
                                <td style="padding-right:5px;">
                                    <input class="submit" type="submit" name="bad" value="   Create WO   " style="margin: 0 0 -1px 1px; padding-bottom: 2px; height: 22px; cursor: pointer; border: 0; background: #222; color: #f00;
                                    border-radius: 7px 7px 7px 7px; -moz-border-radius: 7px 7px 7px 7px; -webkit-border: 7px 7px 7px 7px;  ">
                                </td>
                            </form>
                    <?php
                        }
                    ?>
                </tr>
            </table>
            
            <?php
                if ($role == "A") {
                    if(isset($_POST['DeleteReq'])) {
                        $select_pO = mysqli_query($dbConnection, "SELECT part_noO, Date_of_receipt FROM ServiceApp_ASUS_invoices WHERE part_id='$id_part'");
                        $result = mysqli_fetch_array($select_pO);
                        $part_noO   = $result[0];
                        $date_short = $result[1];
                        
                        //Checks if the part is returned or recycled
                        $checks = mysqli_query($dbConnection, "SELECT ret, recycle FROM ServiceApp_ASUS_requisitionWO WHERE id_part='$id_part'");
                        $checkRes = mysqli_fetch_array($checks);
                        $checkRet     = $checkRes[0];
                        $checkRecycle = $checkRes[1];
                        
                        if ($checkRet != "") {
                            echo ("<p style='color: red;'>Requisition cannot be deleted, because part is returned to vendor!<br></p>");
                        }
                        
                        else if ($checkRecycle != "") {
                            echo ("<p style='color: red;'>Requisition cannot be deleted, because part is recycled!<br></p>");
                        }
                        
                        else if ($status == "3" or $status == "2") {
                            $stmtUpdateC = $dbConnection->prepare('INSERT INTO ServiceApp_ASUS_Blank_OK (id, part_noO, part_noR, part_description, quantity, date_of_receipt) VALUES (?, ?, ?, ?, ?, ?)');
                            $stmtUpdateC->bind_param('isssis', $id_part, $part_noO, $issuing_part_no, $part_description, $qty, $date_short);
                            $stmtUpdateC->execute();
                            
                            $delete_Req = mysqli_query($dbConnection, 'DELETE FROM ServiceApp_ASUS_requisitionWO WHERE id_part='.$id_part);
                            
                            if($receiving_w == "BAD warehouse"){
                                $delete_from_BAD = mysqli_query($dbConnection,"DELETE FROM ServiceApp_ASUS_BAD WHERE id_part='$id_part'");
                                echo ("<p style='color: red;'>Requisition deleted.</p>");
                                echo('<script> setTimeout(function () { window.location.href = "work_order.php"; }, 3000); </script>');
                            }
                            else {
                                $delete_from_Recycle_W = mysqli_query($dbConnection,"DELETE FROM ServiceApp_ASUS_warehouseRecycling WHERE id_part='$id_part'");
                                echo ("<br>Requisition deleted.<br><br>");
                                echo('<script> setTimeout(function () { window.location.href = "work_order.php"; }, 3000); </script>');
            	            }
                        }
                        else if ($status == "Z") {
                            echo ('<script language="javascript"> alert("Work Order in status Z deleted.") </script>');
                            $stmtUpdateC = $dbConnection->prepare('INSERT INTO ServiceApp_ASUS_Blank_OK (id, part_noO, part_noR, part_description, quantity, date_of_receipt) VALUES (?, ?, ?, ?, ?, ?)');
                            $stmtUpdateC->bind_param('isssis', $id_part, $part_noO, $issuing_part_no, $part_description, $qty, $date_short);
                            $stmtUpdateC->execute();
                            
                            $delete_Req = mysqli_query($dbConnection, 'DELETE FROM ServiceApp_ASUS_requisitionWO WHERE id_part='.$id_part);
                            
                            if($receiving_w == "BAD warehouse"){
                                $delete_from_BAD = mysqli_query($dbConnection,"DELETE FROM ServiceApp_ASUS_BAD WHERE id_part='$id_part'");
                                echo ("<p style='color: red;'>Requisition deleted.</p>");
                                echo('<script> setTimeout(function () { window.location.href = "work_order.php"; }, 3000); </script>');
                            }
                            else {
                                $delete_from_Recycle_W = mysqli_query($dbConnection,"DELETE FROM ServiceApp_ASUS_warehouseRecycling WHERE id_part='$id_part'");
                                echo ("<br>Requisition deleted.<br><br>");
                                echo('<script> setTimeout(function () { window.location.href = "work_order.php"; }, 3000); </script>');
            	            }
                        }
                    }
                    
                    if(isset($_POST['CreateDOA'])) {
                        //Checks if the part is returned or recycled
                        $checks = mysqli_query($dbConnection, "SELECT ret, recycle FROM ServiceApp_ASUS_requisitionWO WHERE id_part='$id_part'");
                        $checkRes = mysqli_fetch_array($checks);
                        $checkRet     = $checkRes[0];
                        $checkRecycle = $checkRes[1];
                        
                        if ($checkRet != "") {
                            echo ("<p style='color: red;'>Requisition cannot be marked as DOA, because part is returned to vendor!<br></p>");
                        }
                        
                        else if ($checkRecycle != "") {
                            echo ("<p style='color: red;'>Requisition cannot be marked as DOA, because part is recycled!<br></p>");
                        }
                        else {
                            $update_req = "UPDATE ServiceApp_ASUS_requisitionWO SET doa='YES', status='Z' WHERE id='$id_wo'";
                            mysqli_query($dbConnection,$update_req) or die("Greska: " . mysqli_error($update_req));
                        
                            echo('<script> setTimeout(function () { window.location.href = "work_orderDetails.php?id='.$id_wo.'"; }, 500); </script>'); 
                        }
                    }
            ?>
                    
                    <form name="form" action="<?php $_SERVER['SELF_PHP'] ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete requisition? This action is IRREVERSIBLE!');">
                        <input class="submit" type="submit" name="DeleteReq" value="   Delete Requisition   " style="margin: 0 0 15px 1px; padding-bottom: 1px; height: 25px; cursor: pointer; border: 0; background: #222; color: #fff;
                        border-radius: 7px 7px 7px 7px; -moz-border-radius: 7px 7px 7px 7px; -webkit-border: 7px 7px 7px 7px;  ">
                        <a href="#" tooltip2="This action will delete requisition and return part to Blank/OK warehouse.">
                            <img src="images/question_mark.png" alt="question_mark" height="25" width="25" style="margin: 0 0 -7px 1px; cursor:pointer;">
                        </a>
                    </form>
                    
                    <form name="form" action="<?php $_SERVER['SELF_PHP'] ?>" method="POST" onsubmit="return confirm('Create part fail (DOA)?');">    
                        <input class="submit" type="submit" name="CreateDOA" value="   Create DOA   " style="margin: 0 0 -1px 0; padding-bottom: 1px; height: 25px; cursor: pointer; border: 0; background: #222; color: #fff;
                        border-radius: 7px 7px 7px 7px; -moz-border-radius: 7px 7px 7px 7px; -webkit-border: 7px 7px 7px 7px;  ">
                        <a href="#" tooltip2="This action will mark the part as DOA.">
                            <img src="images/question_mark.png" alt="question_mark" height="25" width="25" style="margin: 0 0 -7px 1px; cursor:pointer;">
                        </a>
                    </form>
                    
            <?php
                }
                
                if ($role == "S") {
                    if(isset($_POST['DeleteReq'])) {
                        $select_pO = mysqli_query($dbConnection, "SELECT part_noO, Date_of_receipt FROM ServiceApp_ASUS_invoices WHERE part_id='$id_part'");
                        $result = mysqli_fetch_array($select_pO);
                        $part_noO   = $result[0];
                        $date_short = $result[1];
                        
                        //Checks if the part is returned or recycled
                        $checks = mysqli_query($dbConnection, "SELECT ret, recycle FROM ServiceApp_ASUS_requisitionWO WHERE id_part='$id_part'");
                        $checkRes = mysqli_fetch_array($checks);
                        $checkRet     = $checkRes[0];
                        $checkRecycle = $checkRes[1];
                        
                        if ($checkRet != "") {
                            echo ("<p style='color: red;'>Requisition cannot be deleted, because part is returned to vendor!<br></p>");
                        }
                        
                        else if ($checkRecycle != "") {
                            echo ("<p style='color: red;'>Requisition cannot be deleted, because part is recycled!<br></p>");
                        }
                        
                        else if ($status == "3" or $status == "2") {
                            $stmtUpdateC = $dbConnection->prepare('INSERT INTO ServiceApp_ASUS_Blank_OK (id, part_noO, part_noR, part_description, quantity, date_of_receipt) VALUES (?, ?, ?, ?, ?, ?)');
                            $stmtUpdateC->bind_param('isssis', $id_part, $part_noO, $issuing_part_no, $part_description, $qty, $date_short);
                            $stmtUpdateC->execute();
                            
                            $delete_Req = mysqli_query($dbConnection, 'DELETE FROM ServiceApp_ASUS_requisitionWO WHERE id_part='.$id_part);
                            
                            if($receiving_w == "BAD warehouse"){
                                $delete_from_BAD = mysqli_query($dbConnection,"DELETE FROM ServiceApp_ASUS_BAD WHERE id_part='$id_part'");
                                echo ("<p style='color: red;'>Requisition deleted.</p>");
                                echo('<script> setTimeout(function () { window.location.href = "work_order.php"; }, 3000); </script>');
                            }
                            else {
                                $delete_from_Recycle_W = mysqli_query($dbConnection,"DELETE FROM ServiceApp_ASUS_warehouseRecycling WHERE id_part='$id_part'");
                                echo ("<br>Requisition deleted.<br><br>");
                                echo('<script> setTimeout(function () { window.location.href = "work_order.php"; }, 3000); </script>');
            	            }
                        }
                        else if ($status == "Z") {
                            echo ('<script language="javascript"> alert("Work Order is in status Z, you cannot delete it. Contact administrator.") </script>');
                        }
                        
                    }
                    
                    if(isset($_POST['CreateDOA'])) {
                        //Checks if the part is returned or recycled
                        $checks = mysqli_query($dbConnection, "SELECT ret, recycle FROM ServiceApp_ASUS_requisitionWO WHERE id_part='$id_part'");
                        $checkRes = mysqli_fetch_array($checks);
                        $checkRet     = $checkRes[0];
                        $checkRecycle = $checkRes[1];
                        
                        if ($checkRet != "") {
                            echo ("<p style='color: red;'>Requisition cannot be marked as DOA, because part is returned to vendor!<br></p>");
                        }
                        
                        else if ($checkRecycle != "") {
                            echo ("<p style='color: red;'>Requisition cannot be marked as DOA, because part is recycled!<br></p>");
                        }
                        else {
                            $update_req = "UPDATE ServiceApp_ASUS_requisitionWO SET doa='YES', status='Z' WHERE id='$id_wo'";
                            mysqli_query($dbConnection,$update_req) or die("Greska: " . mysqli_error($update_req));
                        
                            echo('<script> setTimeout(function () { window.location.href = "work_orderDetails.php?id='.$id_wo.'"; }, 500); </script>'); 
                        }
                    }
            ?>
                    
                    <form name="form" action="<?php $_SERVER['SELF_PHP'] ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete requisition? This action is IRREVERSIBLE!');">
                        <input class="submit" type="submit" name="DeleteReq" value="   Delete Requisition   " style="margin: 0 0 -1px 1px; padding-bottom: 1px; height: 25px; cursor: pointer; border: 0; background: #222; color: #fff;
                        border-radius: 7px 7px 7px 7px; -moz-border-radius: 7px 7px 7px 7px; -webkit-border: 7px 7px 7px 7px;  ">
                        
                        <input class="submit" type="submit" name="CreateDOA" value="   Create DOA   " style="margin: 0 0 -1px 40px; padding-bottom: 1px; height: 25px; cursor: pointer; border: 0; background: #222; color: #fff;
                        border-radius: 7px 7px 7px 7px; -moz-border-radius: 7px 7px 7px 7px; -webkit-border: 7px 7px 7px 7px;  ">
                    </form>
                    
            <?php
                }
            ?>
            </div>
            <br>
            <?php
                if($status == "2") {
                    echo ("<p style='color: red;'>&nbsp;Requisition is NOT approved.</p>");
                }
                if($status == "3") {
                    
                }
                if($status == "Z") {
                    echo ("<p style='color: blue;'>&nbsp;Work Order is CREATED.</p>");
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
</body>
</html>