<?php
    session_start();
    $ip = $_SERVER["REMOTE_ADDR"];
    if(!isset($_SESSION["$ip"])) {
        echo('<script> window.location.href ="notice.php"; </script>');
    }


    $connect = mysqli_connect("localhost","mojaprez_baza","edf951323","mojaprez_baza");
    $number = count($_POST["part_noO"]);  
    $date_short = date("Y-m-d");
    $date_long  = date("Y-m-d H:i:s", time());
    
    /*$date = date("d.m.Y", time());
	$date = explode(".", $date);
	$m = $date[1];
	$y = $date[2];*/
	
	$invo = $_POST["invoice"];
    $seek_invoice = mysqli_query($connect, "SELECT Invoice FROM ServiceApp_ASUS_invoices WHERE Invoice='$invo'");
    $row = mysqli_fetch_array($seek_invoice);
    $Invoice = $row[0];
    
    if($number > 0) {
        for($i=0; $i<$number; $i++) {
            if($invo == $Invoice) {
                echo "Invoice you are trying to enter, already exists in the database.";
                exit;
            }
            if(empty($_POST["invoice"])) {
                echo "The invoice field is empty. You have to fill it out.";
                exit;
            }
            if(empty($_POST["invoice_date"])) {
                echo "The invoice date field is empty. You have to fill it out.";
                exit;
            }
            if(empty($_POST["ref_no"])) {
                echo "The reference number field is empty. You have to fill it out.";
                exit;
            }
            if(empty($_POST["part_noO"][$i])) {
                echo "The ordered part field is empty. You have to fill it out.";
                exit;
            }
            if(empty($_POST["part_noR"][$i])) {
                echo "The received part field is empty. You have to fill it out.";
                exit;
            }
            if(empty($_POST["part_description"][$i])) {
                echo "The part description field is empty. You have to fill it out.";
                exit;
            }
            if(empty($_POST["quantity"][$i])) {
                echo "The quantity field is empty. You have to fill it out.";
                exit;
            }
            else {
                $part_noR = $_POST["part_noR"][$i];
    
                $stmt = $connect->prepare('INSERT INTO ServiceApp_ASUS_Blank_OK (part_noO, part_noR, part_description, quantity, date_of_receipt) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('sssis', $_POST["part_noO"][$i], $_POST["part_noR"][$i], $_POST["part_description"][$i], $_POST["quantity"][$i], $date_short);
                $stmt->execute();
                
                $req_id = mysqli_insert_id($connect); // Uzima zadnji insertovan ID iz baze
    
                $stmt2 = $connect->prepare('INSERT INTO ServiceApp_ASUS_invoices (part_noO, part_noR, part_description, quantity, Invoice, invoice_date, ref_no, price, currency, note, date_long, Date_of_receipt, part_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt2->bind_param('sssissssssssi', $_POST["part_noO"][$i], $_POST["part_noR"][$i], $_POST["part_description"][$i], $_POST["quantity"][$i], $_POST["invoice"], $_POST["invoice_date"], $_POST["ref_no"] , $_POST["price"][$i], $_POST["currency"][$i], $_POST["note"][$i], $date_long, $date_short, $req_id);
                $stmt2->execute();
                
                // Pretrazuje bazu sa svim partovima i ako ne postoji unosi ga.
                $part_no_search = mysqli_query($connect,"SELECT part_no FROM ServiceApp_ASUS_parts_no_lists WHERE part_no='$part_noR'");
                $niz = mysqli_fetch_array($part_no_search);
                $id_part_lists = $niz[0];
                
                if($id_part_lists != $part_noR){
                    $stmt_inser_new_part = $connect->prepare('INSERT INTO ServiceApp_ASUS_parts_no_lists (part_no, part_description) VALUES (?, ?)');
                    $stmt_inser_new_part->bind_param('ss', $_POST["part_noR"][$i], $_POST["part_description"][$i]);
                    $stmt_inser_new_part->execute();
                }
            }
        }
       echo "New invoice and parts are inserted.";  
    }
?>