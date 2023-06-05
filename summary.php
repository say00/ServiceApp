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
  <title>Service App ASUS - Summary</title>
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
        <a tooltip="On this page you can list/search all parts that are inserted in system.">
            <img src="images/question_mark.png" alt="question_mark" height="30" width="30" style="margin: -11px 0 0 -15px; cursor:pointer;">
        </a>
        <br>
        <div class="content">
            <!-- Konekcija ispod je zaduzena za auto popunjavanje teks polja "part_no" ukoliko u bazi postoji identican part. -->
            <?php
                $connection = mysqli_connect("localhost","mojaprez_baza","edf951323","mojaprez_baza") or die("Error " . mysqli_error($connection));
                $sql = "SELECT part_no FROM ServiceApp_ASUS_parts_no_lists";
                $result = mysqli_query($connection, $sql) or die("Error " . mysqli_error($connection));
            ?>
            <div class="round">
                <form name="forma" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                    <div class="form_settings">
                        <label style="padding-right: 16px;">Part No.:</label>
                        <label style="padding-right: 50px;"><input type="text" list="categoryname" name="partS" style="width:150px;" />
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
                        <label style="padding-right: 20px;">Date from:</label><label><input type="text" name="dateFromS" id="datepickerFrom" style="width:150px;"></label>
                        <br>
                        <label style="padding-right: 24px;">Kayako:</label><label style="padding-right: 50px;"><input type="text" name="kayakoS" value="" style="width:150px;" /></label>
                        <label style="padding-right: 36px;">Date to:</label><label><input type="text" name="dateToS" id="datepickerTo" style="width:150px;"></label>
                        <label>&nbsp;</label><input class="submit" style="margin: 0 0 0 10px;" type="submit" name="show_data" value="  Show data  " />
                    </div>
                </form>
            </div>
        </div>
        <div class="content">  
        <!-- U tabeli ispod se prikazuju podaci koji bivaju isfiltriranisu iznad -->
        <?php
            if(isset($_GET['show_data'])){
                $kayakoS   = trim($_GET['kayakoS']);
                $dateFromS = trim($_GET['dateFromS']);
                $partS     = trim($_GET['partS']);
                $dateToS   = trim($_GET['dateToS']);
                
                $kayakoS   = htmlentities($kayakoS);
                $dateFromS = htmlentities($dateFromS);
                $partS     = htmlentities($partS);
                $dateToS   = htmlentities($dateToS);
                $t = date("Y-m-d");
                
                
                if ($dateFromS != "" and $dateToS != "") {
                    $search = "SELECT i.part_noO, i.part_noR, i.part_description, i.quantity, i.Invoice, i.invoice_date, i.ref_no, i.price, i.currency, i.note, i.part_id, i.ret_slow, w.document_no, w.service_provider, w.requisition_date, w.status, w.receiving_part_no, w.qty, w.kayako, w.rma, w.issuing_warehouse, w.receiving_warehouse, w.ret, w.recycle, w.doa
                                FROM ServiceApp_ASUS_invoices i
                                LEFT JOIN ServiceApp_ASUS_requisitionWO w
                                ON i.part_id = w.id_part
                                WHERE (i.Date_of_receipt BETWEEN '$dateFromS' AND '$dateToS')
                                ORDER BY i.date_long DESC";
                }
                else if ($dateFromS != "") {
                    $search = "SELECT i.part_noO, i.part_noR, i.part_description, i.quantity, i.Invoice, i.invoice_date, i.ref_no, i.price, i.currency, i.note, i.part_id, i.ret_slow, w.document_no, w.service_provider, w.requisition_date, w.status, w.receiving_part_no, w.qty, w.kayako, w.rma, w.issuing_warehouse, w.receiving_warehouse, w.ret, w.recycle, w.doa
                                FROM ServiceApp_ASUS_invoices i
                                LEFT JOIN ServiceApp_ASUS_requisitionWO w
                                ON i.part_id = w.id_part
                                WHERE (i.Date_of_receipt BETWEEN '$dateFromS' AND '$t')
                                ORDER BY i.date_long DESC";
                }
                else if ($partS != "") {
                    $search = "SELECT i.part_noO, i.part_noR, i.part_description, i.quantity, i.Invoice, i.invoice_date, i.ref_no, i.price, i.currency, i.note, i.part_id, i.ret_slow, w.document_no, w.service_provider, w.requisition_date, w.status, w.receiving_part_no, w.qty, w.kayako, w.rma, w.issuing_warehouse, w.receiving_warehouse, w.ret, w.recycle, w.doa
                                FROM ServiceApp_ASUS_invoices i
                                LEFT JOIN ServiceApp_ASUS_requisitionWO w
                                ON i.part_id = w.id_part
                                WHERE i.part_noR LIKE '%$partS%'
                                ORDER BY i.date_long DESC";
                }
                else if ($kayakoS != "") {
                    $search = "SELECT i.part_noO, i.part_noR, i.part_description, i.quantity, i.Invoice, i.invoice_date, i.ref_no, i.price, i.currency, i.note, i.part_id, i.ret_slow, w.document_no, w.service_provider, w.requisition_date, w.status, w.receiving_part_no, w.qty, w.kayako, w.rma, w.issuing_warehouse, w.receiving_warehouse, w.ret, w.recycle, w.doa
                                FROM ServiceApp_ASUS_invoices i
                                LEFT JOIN ServiceApp_ASUS_requisitionWO w
                                ON i.part_id = w.id_part
                                WHERE w.kayako LIKE '%$kayakoS%'
                                ORDER BY i.date_long DESC";
                }
                else {
                    $search = "SELECT i.part_noO, i.part_noR, i.part_description, i.quantity, i.Invoice, i.invoice_date, i.ref_no, i.price, i.currency, i.note, i.part_id, i.ret_slow, w.document_no, w.service_provider, w.requisition_date, w.status, w.receiving_part_no, w.qty, w.kayako, w.rma, w.issuing_warehouse, w.receiving_warehouse, w.ret, w.recycle, w.doa
                                FROM ServiceApp_ASUS_invoices i
                                LEFT JOIN ServiceApp_ASUS_requisitionWO w
                                ON i.part_id = w.id_part
                                ORDER BY i.date_long DESC";
                }
                $rez = mysqli_query($dbConnection, $search) or die ("Ovde je zapelo" .mysqli_error());
                
                $i = 0;
            
                while($niz = mysqli_fetch_array($rez)) {
                    $part_no[$i]       = $niz[0];
                    $iss_part_no[$i]   = $niz[1];
                    $part_desc[$i]     = $niz[2];
                    $quantity[$i]      = $niz[3];
                    $invoice[$i]       = $niz[4];
                    $invoice_date[$i]  = $niz[5];
                    $ref_no[$i]        = $niz[6];
                    $price[$i]         = $niz[7];
                    $currency[$i]      = $niz[8];
                    $note[$i]          = $niz[9];
                    $part_id[$i]       = $niz[10];
                    $ret_slow[$i]      = $niz[11];
                    $document_no[$i]   = $niz[12];
                    $srv_prov[$i]      = $niz[13];
                    $req_date[$i]      = $niz[14];
                    $status[$i]        = $niz[15];
                    $rec_part_no[$i]   = $niz[16];
                    $qty[$i]           = $niz[17];
                    $kayako[$i]        = $niz[18];
                    $rma[$i]           = $niz[19];
                    $iss_warehouse[$i] = $niz[20];
                    $rec_warehouse[$i] = $niz[21];
                    $return[$i]        = $niz[22];
                    $recycle[$i]       = $niz[23];
                    $doa[$i]           = $niz[24];
                    $id[$i]            = $niz[25];
                    $i++;
                }
            }
        ?>
        <div class="round2">
            <table>
                <tr>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>ID</b></td>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>Invoice No.</b></td>
                    <td style="padding-right: 5px; background: #a0b9ff;"><b>Invoice date</b></td>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>Reference No.</b></td>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>Req. No.</b></td>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>Req. date</b></td>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>Part Ordered</b></td>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>Part Received</b></td>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>Part Returned</b></td>
                    <td style="padding-right: 75px; background: #a0b9ff;"><b>Part Description</b></td>
                    <td style="padding-right: 15px; background: #a0b9ff;"><b>Kayako</b></td>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>RMA</b></td>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>Qty</b></td>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>Price</b></td>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>Curr.</b></td>
                    <td style="padding-right: 15px; background: #a0b9ff;"><b>Issuing warehouse</b></td>
                    <td style="padding-right: 15px; background: #a0b9ff;"><b>Receiving war.</b></td>
                    <td style="padding-right: 25px; background: #a0b9ff;"><b>Servicer</b></td>
                    <td style="padding-right: 5px; background: #a0b9ff;"><b>Status</b></td>
                    <td style="padding-right: 5px; background: #a0b9ff;"><b>Ret.</b></td>
                    <td style="padding-right: 5px; background: #a0b9ff;"><b>Ret. SLOW</b></td>
                    <td style="padding-right: 5px; background: #a0b9ff;"><b>Rec</b></td>
                    <td style="padding-right: 5px; background: #a0b9ff;"><b>DOA</b></td>
                    <td style="padding-right: 10px; background: #a0b9ff;"><b>Note</b></td>
                </tr>
                <?php
                    if($id) {
                        foreach ($part_no as $k=>$v) {
                ?>
                <tr>
                    <td style="padding-right: 10px;"><?php echo $part_id[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $invoice[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $invoice_date[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $ref_no[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $document_no[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $req_date[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $part_no[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $iss_part_no[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $rec_part_no[$k]; ?></td>
                    <td style="max-width: 150px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;"><?php echo $part_desc[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $kayako[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $rma[$k]; ?></td>
                    <td style="padding-right: 10px;">
                        <?php 
                            if ($quantity != "") {
                                echo $quantity[$k];
                            }
                            else echo $qty[$k];
                        ?>
                    </td>
                    <td style="padding-right: 10px;"><?php echo $price[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $currency[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $iss_warehouse[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $rec_warehouse[$k]; ?></td>
                    <td style='padding-right: 10px;'><?php echo $srv_prov[$k]; ?></td>
                    <td style='padding-right: 10px; color: red;'><b><?php echo $status[$k]; ?></b></td>
                    <td style="padding-right: 10px;"><?php echo $return[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $ret_slow[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $recycle[$k]; ?></td>
                    <td style="padding-right: 10px;"><?php echo $doa[$k]; ?></td>
                    <td style="max-width: 130px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;"><?php echo $note[$k]; ?></td>
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