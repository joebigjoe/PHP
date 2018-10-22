<!DOCTYPE html>
<!--include needed php-->
<?php
    require_once("include/common.php");
    require_once("include/dbhandler.php");
?>

<!--the actual code-->
<?php
    $campaign_item_id = null;
    $campaign_item_id_decrpt = null;
    
    if( $_SESSION['user_id'] == "guest"){
        header("Location: login.php");
        exit();
    }

    if(isset($_GET['campaign_item_id'])){
        $campaign_item_id=$_GET['campaign_item_id'];
        try{
            $campaign_item_id_decrpt = (int)encryptDecrypt("union",  $campaign_item_id, 1);
        }catch(Exception $ex){
            exit();
        }    
    }

    if($campaign_item_id){
        $pdf=DatabaseHandler::GetCampaignItemPDF($campaign_item_id_decrpt);
    } 
?>

<html>
    <head>
        <meta charset="utf-8">
        <title>工会网店</title>
        <link rel="stylesheet" type="text/css" href="style/style.css">
    </head>
    <body>
        <div class="container">
            <?php include("header.php"); ?>
            <div id="content">
                <div class="sidebar">
                        <div id="activity_title">
                            工会活动：
                        </div>
                        <div id="activity_details">
                            <ul id="activity">
                                <?php
                                    DatabaseHandler::GetAllCampaignInfo();
                                ?>
                            </ul>
                        </div>
                    </div>
                     <div class="sidecontent">
                         <div id="activity_details">
                             <p id="activity_details_title">您所选中活动的具体内容：</p>
                             <?php
                                echo '<iframe id="iframe_pdf" src="'.$pdf.'"></iframe>';
                             ?>
                         </div>
                    </div>
            </div>
            <?php include("footer.php"); ?>
        </div>
        <div><script src="scripts/index.js"></script></div>
    </body>
</html>