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
    <meta name="keywords" content="website keywords, website keywords">
    <meta name="description" content="website description">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <!-- modernizr enables HTML5 elements and feature detects -->
    <script type="text/javascript" src="js/modernizr-1.5.min.js"></script>
    <script>
        $(function() {
            $('#datepicker').datepicker({ dateFormat: 'yy/mm/dd' }).val();
        });
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
    <?php
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
               	        <input type="submit" class="submit" name="signout" value="   SIGN OUT   ">
            	    </form>
            	</div>
    	<?php
    	    }
        ?>
        <div id="site_content">
            <a hreff="#" tooltip="On this page you can insert new parts in database, based on received invoices from vendor.">
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
                    <div class="form_settings"> 
                        <form name="add_name" id="add_name" method="POST">
                        <p><label style="padding-right:5px;">Invoice No:</label><label><input type="text" name="invoice" style="width:150px;"></label>
                        <label style="padding-right:5px; margin-left:20px;">Invoice date:</label><label><input type="text" name="invoice_date" id="datepicker" style="width:100px;"></label>
                        <label style="padding-right:5px; margin-left:20px;">Ref No:</label><label><input type="text" name="ref_no" style="width: 200px;"></label></p>
                            <table id="dynamic_field">  
                                <tr>
                                    <td style="background: #a0b9ff;">
                                        Part No. Ordered
                                    </td>
                                    <td style="background: #a0b9ff;">
                                        Part No. Received
                                    </td>
                                    <td style="background: #a0b9ff;">
                                        Part Description
                                    </td>
                                    <td style="background: #a0b9ff; cursor: pointer;" title="Zakucano na 1, dok se ne sredi bag.">
                                        Quantity
                                    </td>
                                    <td style="background: #a0b9ff;">
                                        Price
                                    </td>
                                    <td style="background: #a0b9ff;">
                                        Currency
                                    </td>
                                    <td style="background: #a0b9ff;">
                                        Note
                                    </td>
                                    <td style="background: #a0b9ff;">
                                    </td>
                                </tr>
                                <tr>  
                                    <td>
                                        <input type="text" name="part_noO[]" list="categoryname" placeholder="Enter ordered part..." style="width:150px;">
                                        <datalist id="categoryname">
                                            <?php
                                                while($row = mysqli_fetch_array($result)) {
                                            ?>
                                                <option value="<?php echo $row['part_no']; ?>"><?php echo $row2['part_no']; ?></option>
                                            <?php
                                                }
                                            ?>
                                        </datalist>
                                    </td>  
                                    <td>
                                        <input type="text" name="part_noR[]" list="categoryname"  placeholder="Enter received part..." style="width:150px;">
                                        <datalist id="categoryname">
                                            <?php
                                                while($row = mysqli_fetch_array($result)) {
                                            ?>
                                                <option value="<?php echo $row['part_no']; ?>"><?php echo $row2['part_no']; ?></option>
                                            <?php
                                                }
                                            ?>
                                        </datalist>
                                    </td>
                                    <td>
                                        <!-- Konekcija ispod je zaduzena za auto popunjavanje polja "part description" ukoliko postoji u bazi. -->
                                        <?php
                                            $connection2 = mysqli_connect("localhost","mojaprez_baza","edf951323","mojaprez_baza") or die("Error " . mysqli_error($connection2));
                                            $sql2 = "SELECT part_description FROM ServiceApp_ASUS_parts_no_lists";
                                            $result2 = mysqli_query($connection2, $sql2) or die("Error " . mysqli_error($connection2));
                                        ?>
                                        <input type="text" name="part_description[]" list="categoryname2" placeholder="Enter part description..." style="width: 400px;">
                                        <datalist id="categoryname2">
                                            <?php
                                                while($row2 = mysqli_fetch_array($result2)) {
                                            ?>
                                                <option value="<?php echo $row2['part_description']; ?>"><?php echo $row2['part_description']; ?></option>
                                            <?php
                                                }
                                            ?>
                                        </datalist>
                                    </td>
                                    <td><input type="text" name="quantity[]" value="1" style="width:40px;" readonly></td>
                                    <td><input type="text" name="price[]"  style="width:60px;"></td>
                                    <td>
                                        <select id="id" name="currency[]" style="width:60px;">
                                            <option value="EUR">EUR</option>
                                            <option value="USD">USD</option>
                                        </select>
                                    </td>
                                    <td><textarea rows="1" name="note[]"></textarea></td>
                                    <td><button type="button" name="add" id="add" class="buttonAdd">&nbsp;&nbsp;Add More fields&nbsp;&nbsp;</button></td>  
                                </tr>  
                            </table>  
                            <label style="margin-left: -180px;"><input type="button" name="submit" id="submit" class="submit" value="   Insert Invoice   "></label>
                        </form>  
                    </div> 
                </div>
            </div> 
        </div>
        <div id="scroll">
            <a title="Scroll to the top" class="top" href="#"><img src="images/top.png" alt="top"></a>
        </div>
        <footer>
      <p><img src="images/twitter.png" alt="twitter" />&nbsp;<img src="images/facebook.png" alt="facebook" />&nbsp;<img src="images/rss.png" alt="rss" /></p>
      <p><a href="index.php">Home</a> | <?php if(isset($_SESSION["$ip"])){ ?><a href="receiving_new_invoice.php">Receiving new invoice (Parts)</a> | <a href="work_order_requisition.php">Create new Requisition</a> | <a href="current_stock.php">Current Stock</a> | <a href="requisition.php">Requisition Approval</a><?php } ?></p>
      <p>Copyright &copy; Jovan Milošević | <a href="#">Master design & programming Jovan Milošević</a></p>
    </footer>
    </div>
    <script>  
        $(document).ready(function(){  
            var i=1;  
            $('#add').click(function(){  
                i++;  
                $('#dynamic_field').append('<tr id="row'+i+'"><td><input type="text" name="part_noO[]" list="categoryname" placeholder="Enter ordered part..." style="width:150px;"><datalist id="categoryname"><?php while($row = mysqli_fetch_array($result)) {?><option value="<?php echo $row['part_no']; ?>"><?php echo $row['part_no']; ?></option><?php }?></datalist></td><td><input type="text" name="part_noR[]" list="categoryname" placeholder="Enter received part..." style="width:150px;"><datalist id="categoryname"><?php while($row = mysqli_fetch_array($result)) {?><option value="<?php echo $row['part_no']; ?>"><?php echo $row['part_no']; ?></option><?php }?></datalist></td><td><input type="text" name="part_description[]" placeholder="Enter part description..." style="width: 400px;"></td><td><input type="text" name="quantity[]" value="1" style="width:40px;" readonly></td><td><input type="text" name="price[]"  style="width:60px;"></td><td><select id="id" name="currency[]" style="width:60px;"><option value="EUR">EUR</option><option value="USD">USD</option></select></td><td><textarea rows="1" name="note[]"></textarea></td><td><button type="button" name="remove" id="'+i+'" class="buttonX">&nbsp;X&nbsp;</button></td></tr>');  
            });
            $(document).on('click', '.buttonX', function(){  
                var button_id = $(this).attr("id");   
                $('#row'+button_id+'').remove();  
            });  
            $('#submit').click(function(){            
                $.ajax({  
                    url:"receiving_new_invoice_query.php",  
                    method:"POST",  
                    data:$('#add_name').serialize(),  
                    success:function(data)  
                    {  
                         alert(data);  
                         $('#add_name')[0].reset();  
                    }  
                });  
            });  
        });  
   </script>
</body>  
</html>  