<!DOCTYPE html>
<!--start session again after header redirect-->
<?php
    require_once "include/functionlist.php";    
    session_start();
?>

<!--include needed php-->
<?php
    require_once("include/common.php");
    require_once("include/dbhandler.php");
    echo $_SESSION['user_id'];
?>

<!--This is to handle the campaign items load-->
<?php
    $campaign_id_in=-1;
    if(isset($_GET['campaign_id'])){
        $campaign_id_in=$_GET['campaign_id'];
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
            <?php include("header.php");?>
            <div id="content">
                <div class="sidebar">
                        <div id="activity_title">
                            工会活动
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
                             <p>活动规则</p>
                             <div id="ad_rule_desc">
                                 <?php
                                    $row = DatabaseHandler::GetNeededCampaignInfo($campaign_id_in);
                                    echo $row['campaigndesc'];
                                 ?>
                             </div>
                         </div>
                         <div id="ad_options">
                            活动选项
                         </div>
                         <div id="ad_options_list">
                            <?php
                                // set the needed options.
                                DatabaseHandler::GetCampaignItemsInfo($campaign_id_in);
                            ?>
                         </div>
                    </div>
            </div>
            <?php include("footer.php");?>
        </div>
    </body>
</html>