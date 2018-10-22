<!DOCTYPE html>
<!--include needed php-->
<?php
    require_once("include/common.php");
    require_once("include/dbhandler.php");
    require_once("include/functionlist.php");   
?>

<!--This is to handle the campaign items load-->
<?php
    $row = null;
    $campaign_id_in_redirect=null;
    $campaign_id_in_redirect_decrept=null;

    if(isset($_GET['campaign_id'])){
        $campaign_id_in_redirect=$_GET['campaign_id'];
        try{
            $campaign_id_in_redirect_decrept = (int)encryptDecrypt("union",  $campaign_id_in_redirect, 1);
        }catch(Exception $ex){
            exit();
        }
        $row=DatabaseHandler::GetNeededCampaignInfo($campaign_id_in_redirect_decrept);
    } else{
        $row = DatabaseHandler::GetLatestCampaignInfo();
    }
?>

<!--Handle shooping cart-->
<?php
    $user_id = null;
    $action = null;
    $compaign_id = null;
    $compaign_id_decrpt = null;
    $compaign_item_id = null;
    $compaign_item_id_decrpt = null;
    $ci_name = null;
    $ci_desc = null;
    $points = null;
    $points_decrpt = null;

    if(isset($_GET['action'])){
        $action = $_GET['action'];
    }
    
    if ($action == 'add_to_cart') {
        if (isset($_GET['user_id']) && $_GET['user_id'] == 'guest') {
            header("Location: login.php");
            exit();
        } else {
            if (isset($_GET['user_id'])) {
                $user_id = $_GET['user_id'];
            }

            if (isset($_GET['campaign_id'])) {
                $compaign_id = $_GET['campaign_id'];
                try{
                    $compaign_id_decrpt = (int)encryptDecrypt("union",  $compaign_id, 1);
                }catch(Exception $ex){
                    $compaign_id_decrpt = "0";
                }
            }

            if (isset($_GET['campaign_item_id'])) {
                $compaign_item_id = $_GET['campaign_item_id'];
                try{
                    $compaign_item_id_decrpt = (int)encryptDecrypt("union",  $compaign_item_id, 1);
                }catch(Exception $ex){
                    $compaign_item_id_decrpt = "0";
                }
            }

            if (isset($_GET['ci_name'])) {
                $ci_name = $_GET['ci_name'];
            }

            if (isset($_GET['ci_desc'])) {
                $ci_desc = $_GET['ci_desc'];
            }

            if (isset($_GET['points'])) {
                $points = $_GET['points'];
                try{
                    $points_decrpt = (int)encryptDecrypt("union",  $points, 1);
                }catch(Exception $ex){
                    $points_decrpt = "0";
                }
            }
            $date_now = (new DateTime())->format('Y-m-d H:i:s');
            $addtocart_result = DatabaseHandler::AddItemToShoppingCart($user_id, $compaign_id_decrpt,$compaign_item_id_decrpt,$ci_name, $ci_desc,$points_decrpt, $date_now);
            if($addtocart_result > 0){
                header('Location: index.php?campaign_id='.$compaign_id);
            }
        }
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
                         <div id="ad_rule">
                             <p>活动规则：</p>
                             <div id="ad_rule_desc">
                                 <?php
                                    echo $row['campaigndesc'];
                                 ?>
                             </div>
                         </div>
                         <div id="ad_options">
                            活动选项：
                         </div>
                         <div id="ad_options_list">
                            <?php
                                // if campaign_id is not set, get the biggest one from DB.
                                DatabaseHandler::GetCampaignItemsInfo($row['id']);
                            ?>
                         </div>
                    </div>
            </div>
            <?php include("footer.php"); ?>
        </div>
        <div><script src="scripts/index.js"></script></div>
    </body>
</html>