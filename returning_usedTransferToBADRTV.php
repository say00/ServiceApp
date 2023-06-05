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
        
        $id_part = $_GET['id_part']? $_GET['id_part']: $_POST['id_part'];
        
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
    <div id="site_content">
        <?php
            $date = date("Y-m-d H:i:s", time());
            $qty  = "1";
            
            $query = mysqli_query($dbConnection,"SELECT id_part, part_noO, part_noR, part_noReturning, part_description, kayako, rma FROM ServiceApp_ASUS_BAD WHERE id_part='$id_part'");
            $niz = mysqli_fetch_array($query);
            $id_part     = $niz[0];
            $part_noO    = $niz[1];
            $part_noR    = $niz[2];
            $part_Return = $niz[3];
            $part_desc_  = $niz[4];
            $kayako      = $niz[5];
            $rma         = $niz[6];
        
            $stmt = $dbConnection->prepare("INSERT INTO ServiceApp_ASUS_BAD_RTV (id_part, Ordered_part, Received_part, Returning_part, Description_part, Kayako, rma, Quantity, Output_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('issssssis', $id_part, $part_noO, $part_noR, $part_Return, $part_desc_, $kayako, $rma, $qty, $date);
            $stmt->execute();
            
            //echo('<p style="color: blue; margin-bottom: -45px;"><br>Part successfully transferred to BAD RTV.<p>');
            $delete_from_BAD = mysqli_query($dbConnection,"DELETE FROM ServiceApp_ASUS_BAD WHERE id_part='$id_part'");
           
            echo('<script> setTimeout(function () { window.location.href = "returning_usedTest.php"; }, 0); </script>');   
        ?>
    </div>
    <div id="scroll">
      <a title="Scroll to the top" class="top" href="#"><img src="images/top.png" alt="top" /></a>
    </div>
  </div>
</body>
</html>