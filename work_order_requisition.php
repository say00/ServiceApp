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
        <a hreff="#" tooltip="On this page you can create new Requisition and send them for approval. For each part you create separate requisition.">
            <img src="images/question_mark.png" alt="question_mark" height="30" width="30" style="margin: -11px 0 0 -15px; cursor:pointer;">
        </a>
        <br>
      <div class="content">
        <div class="form_settings1">
            <form action="#" style="float:right; margin: -50px -19px 0 0;">
                Click to insert new part for returning<button class="submit" id="myBtn">&nbsp;&nbsp;Insert New Part&nbsp;&nbsp;</button>
            </form>
        </div>
        
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <?php
                    if(isset($_POST['create_new'])) {
                        $part_no   = trim($_POST['part_no']);
                        $part_desc = trim($_POST['part_desc']);
                  
                        /* Pretrazuje bazu sa svim partovima i ako ne postoji unosi ga. */
                        $part_no_search = mysqli_query($dbConnection,"SELECT part_no FROM ServiceApp_ASUS_parts_no_lists WHERE part_no='$part_no'");
                        $niz = mysqli_fetch_array($part_no_search);
                        $id_part_lists = $niz[0];
                        
                        if($id_part_lists != $part_no){
                            $stmt_inser_new_part = $dbConnection->prepare('INSERT INTO ServiceApp_ASUS_parts_no_lists (part_no, part_description) VALUES (?, ?)');
                            $stmt_inser_new_part->bind_param('ss', $part_no, $part_desc);
                            $stmt_inser_new_part->execute();
                        }
                    }
                ?>
                <span class="close">&times;</span>
                <p><h2>Create New Part No.</h2></p>
                <hr style="width:100%; height: 10px; border: 0; box-shadow: 0 10px 10px -10px #8c8b8b inset;">
                <form name="form" action="work_order_requisition.php" method="POST">
                    <div class="form_settings">
                        <label>Part No.:</label><br>
                        <label><input type="text" name="part_no" value="" style="width:150px;" /></label><br><br>
                        <label>Part description:</label><br>
                        <label><input type="text" name="part_desc" value="" /></label><br><br>
                        <label style="margin: 0 0 0 -181px;"><input class="submit" type="submit" name="create_new" value="  Create New  " /></label>
                    </div>
                    
                </form>
                <br>
                <hr style="width:100%; height: 10px; border: 0; box-shadow: 0 10px 10px -10px #8c8b8b inset;">
            </div>
        </div>
        
        <script>
            // Get the modal
            var modal = document.getElementById('myModal');
            
            // Get the button that opens the modal
            var btn = document.getElementById("myBtn");
            
            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];
            
            // When the user clicks on the button, open the modal 
            btn.onclick = function() {
                modal.style.display = "block";
            }
            
            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }
            
            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>
        
        <!-- Konekcija ispod je zaduzena za auto popunjavanje teks polja "part_no" ukoliko u bazi postoji identican part. -->
        <?php
            //$connection = mysqli_connect("localhost","mojaprez_baza","edf951323","mojaprez_baza") or die("Error " . mysqli_error($connection));
            //$sql = "SELECT part_noR, quantity FROM ServiceApp_ASUS_Blank_OK";
            //$result = mysqli_query($connection, $sql) or die("Error " . mysqli_error($connection));
        ?>
        <!--<hr style="width:100%; height: 10px; border: 0; box-shadow: 0 10px 10px -10px #8c8b8b inset;">-->
        <?php
            // Izvlaci iz base puno ime korisnika koji je ulogovan
                $user = $_SESSION["$ip"];
                $query_user = mysqli_query($dbConnection,"SELECT user, pass, role, full_name FROM ServiceApp_ASUS_Users WHERE user='$user'");
                $niz = mysqli_fetch_array($query_user);
                $user      = $niz[0];
    	        $pass      = $niz[1];
    	        $role      = $niz[2];
    	        $full_name = $niz[3];
    	    
            if(isset($_POST["save_req"])) {
                $iss_part_no = trim($_POST["iss_part_no"]);
                $ticket      = trim($_POST["ticket"]);
                $rma_ticket  = trim($_POST["rma_ticket"]);
                $iss_ware    = trim($_POST["iss_ware"]);
                $rec_part_no = trim($_POST["rec_part_no"]);
                $rec_ware    = trim($_POST["rec_ware"]);
                $date_long   = date("Y-m-d H:i:s", time());
                $req_date    = date("Y/m/d");
                $req_d       = date("y");
                $servicer    = $full_name;
                $status      = "2";
                $qty         = "1";
                
                //Na osnovu "iss_part_no" izvlacimo "part_description" iz druge baze
                $query = mysqli_query($dbConnection,"SELECT id, part_noO, part_noR, part_description FROM ServiceApp_ASUS_Blank_OK WHERE part_noR='$iss_part_no'");
                $niz = mysqli_fetch_array($query);
                $id_part    = $niz[0];
                $part_noO   = $niz[1];
                $part_noR   = $niz[2];
                $part_desc_ = $niz[3];
                
    	        if(empty($iss_part_no) && empty($ticket) && empty($rma_ticket) && empty($rec_part_no) && empty($rec_ware)) {
    	            echo('<p style="color:red;">Fill all fields.</p>');
    	        }else {
    	            //$insert_req = mysql_query('INSERT INTO ServiceApp_ASUS_requisitionWO (service_provider, requisition_date, status, id_part, issuing_part_no, receiving_part_no, part_description, qty, kayako, rma, issuing_warehouse, receiving_warehouse, date_long) 
    	                                       //VALUES ("'.$servicer.'", "'.$req_date.'", "'.$status.'", "'.$id_part.'", "'.$iss_part_no.'", "'.$rec_part_no.'", "'.$part_desc_.'", "'.$qty.'", "'.$ticket.'", "'.$rma_ticket.'", "'.$iss_ware.'", "'.$rec_ware.'", "'.$date_long.'")') or die (mysql_error());
    	            
    	            $stmt_insert_req = $dbConnection->prepare('INSERT INTO ServiceApp_ASUS_requisitionWO (service_provider, requisition_date, status, id_part, issuing_part_no, receiving_part_no, part_description, qty, kayako, rma, issuing_warehouse, receiving_warehouse, date_long) 
    	                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                    $stmt_insert_req->bind_param('ssiisssisssss', $servicer, $req_date, $status, $id_part, $iss_part_no, $rec_part_no, $part_desc_, $qty, $ticket, $rma_ticket, $iss_ware, $rec_ware, $date_long);
                    $stmt_insert_req->execute();
    	            
    	            $req_id = mysqli_insert_id($dbConnection); // Uzima zadnji insertovan ID iz baze
    	            
    	            $update_req = mysqli_query($dbConnection,"UPDATE ServiceApp_ASUS_requisitionWO SET document_no='$req_d-TRE-$req_id' WHERE id=$req_id");
    	            echo('<p style="color: blue; margin-bottom: -30px;">New requisition created.<p>');
    	            
    	            if($rec_ware == "BAD warehouse"){
    	                $transfer_part_to_BAD = mysqli_query($dbConnection,'INSERT INTO ServiceApp_ASUS_BAD (id_part, part_noO, part_noR, part_noReturning, part_description, kayako, rma, quantity, date) VALUES ("'.$id_part.'", "'.$part_noO.'", "'.$part_noR.'", "'.$rec_part_no.'", "'.$part_desc_.'", "'.$ticket.'", "'.$rma_ticket.'", "'.$qty.'", "'.$req_date.'")');
    	                
    	                //$stmt_transfer_part_to_ware_returnig = $dbConnection->prepare('INSERT INTO ServiceApp_ASUS_warehouseToVendorReturn (id_part, part_no, part_description, kayako, quantity, date) VALUES (?, ?, ?, ?, ?, ?)');
                        //$stmt_transfer_part_to_ware_returnig->bind_param('isssis', $id_part, $rec_part_no, $part_desc_, $ticket, $qty, $req_date);
                        //$stmt_transfer_part_to_ware_returnig->execute();
                        
                        $delete_from_Blank = mysqli_query($dbConnection,"DELETE FROM ServiceApp_ASUS_Blank_OK WHERE id='$id_part'");
    	            }else {
    	                $transfer_part_to_Scrap = mysqli_query($dbConnection,'INSERT INTO ServiceApp_ASUS_warehouseRecycling (id_part, Ordered_part, Received_part, Recycling_part, Description_part, Kayako, RMA, quantity, Output_date) VALUES ("'.$id_part.'", "'.$part_noO.'", "'.$part_noR.'", "'.$rec_part_no.'", "'.$part_desc_.'", "'.$ticket.'", "'.$rma_ticket.'", "'.$qty.'", "'.$req_date.'")');
    	                $update_status = mysqli_query($dbConnection, "UPDATE ServiceApp_ASUS_requisitionWO SET status='Z' WHERE id=$req_id");
    	                
    	                //$stmt_transfer_part_to_ware_recycling = $dbConnection->prepare('INSERT INTO ServiceApp_ASUS_warehouseToVendorReturn (id_part, part_no, part_description, kayako, quantity, date) VALUES (?, ?, ?, ?, ?, ?)');
                        //$stmt_transfer_part_to_ware_recycling->bind_param('isssi', $id_part, $rec_part_no, $part_desc_, $ticket, $qty, $req_date);
                        //$stmt_transfer_part_to_ware_recycling->execute();
                        
                        $delete_from_Blank = mysqli_query($dbConnection,"DELETE FROM ServiceApp_ASUS_Blank_OK WHERE id='$id_part'");
    	            }
    	            //echo('<script> setTimeout(function () { window.location.href = "work_order_requisition.php"; }, 1000); </script>');
    	        }
            }
        ?>
        
        <div class="round">
        <form name="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
            <div class="form_settings">
                <!-- Konekcija ispod je zaduzena za auto popunjavanje teks polja "part_no" ukoliko u bazi postoji identican part. -->
                <?php
                    $connection = mysqli_connect("localhost","mojaprez_baza","edf951323","mojaprez_baza") or die("Error " . mysqli_error($connection));
                    $sql = "SELECT part_noR FROM ServiceApp_ASUS_Blank_OK";
                    $result = mysqli_query($connection, $sql) or die("Error " . mysqli_error($connection));
                ?>
                <p><label style="padding-right: 105px;">Issuing part No.:</label><label><input type="text" name="iss_part_no" list="categoryname" autocomplete="off" id="part_no2" placeholder="Insert Part No..." style="width:202px;" /></label>
                <label style="padding: 0 5px 0 30px;">Kayako ticket:</label><input type="text" name="ticket" style="width:100px;"></label><label style="padding: 0 0 0 20px;">RMA ticket: </label><input type="text" name="rma_ticket" style="width:100px;"></label>
                    <datalist id="categoryname">
                        <?php
                            while($row = mysqli_fetch_array($result)) {
                        ?>
                            <option value="<?php echo $row['part_noR']; ?>"><?php echo $row['part_noR']; ?></option>
                        <?php
                            }
                        ?>
                    </datalist><br>
                </p>
                <p><label style="padding: 0 88px 0 0;">Issuing warehouse:</label><label><select id="id" name="iss_ware" style="width:215px;" >
                    <option value="Blank/OK warehouse">Blank - OK warehouse</option></select></label>
                    <!--<label style="padding: 0 42px 0 30px;">Stock:&nbsp;&nbsp;</label><label><input type="text" name="stock" value="" style="width:50px;" /></label><br>-->
                </p>
                <!-- Konekcija ispod je zaduzena za auto popunjavanje teks polja "Part No. for Returning/Recycling" ukoliko u bazi postoji identican part. -->
                <?php
                    $connection2 = mysqli_connect("localhost","mojaprez_baza","edf951323","mojaprez_baza") or die("Error " . mysqli_error($connection2));
                    $sql2 = "SELECT part_no FROM ServiceApp_ASUS_parts_no_lists";
                    $result2 = mysqli_query($connection2, $sql2) or die("Error " . mysqli_error($connection2));
                ?>
                <p><label style="padding-right: 10px;">Part No. for Returning/Recycling:</label><label><input type="text" name="rec_part_no" list="categoryname2" id="part_no2" placeholder="Insert Return Part No..." style="width:202px;" /></label>
                    <datalist id="categoryname2">
                        <?php
                            while($row2 = mysqli_fetch_array($result2)) {
                        ?>
                            <option value="<?php echo $row2['part_no']; ?>"><?php echo $row2['part_no']; ?></option>
                        <?php
                            }
                        ?>
                    </datalist><br>
                </p>
                <p><label style="padding-right: 73px;">Receiving warehouse:</label><label><select id="id" name="rec_ware" style="width:215px;" >
                    <option value="0"></option>
                    <option value="BAD warehouse">BAD warehouse</option>
                    <option value="Scrap warehouse">Scrap warehouse</option></select></label>
                </p>
                <p style="margin: 5px 0 10px -180px;"><label><input class="submit" type="submit" name="save_req" value="   Create requisition   " /></label></p>
            </div>
        </form>
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
