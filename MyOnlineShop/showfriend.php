<!DOCTYPE html>
<!--include-->
<?php
    require_once('include/common.php');
    require_once 'include/dbhandler.php';
    require_once "include/functionlist.php";    
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
        $results=DatabaseHandler::GetAllMyFriendsOrders($campaign_item_id_decrpt);
        $amount=DatabaseHandler::GetAllMyFriendsOrdersAmount($campaign_item_id_decrpt);
    } 
?>

<html>
    <head>
        <meta charset="utf-8">
        <title>工会网店</title>
        <link rel="stylesheet" type="text/css" href="style/style.css">
        <script src="scripts/login.js"></script>
    </head>
    <body>
        <div class="container">
            <?php include("header.php"); ?>
            <div id="content">
                <div id="friendorder_title">
                    <p id="friendorder_title_p">其他小伙伴也参加了此次活动</p>
                </div>
                <div id="friendorder_content">
                    <table id="friendorder_content_table">
                        <tr>
                            <th>员工号：</th>
                            <th>员工姓名：</th>
                            <th>活动名称：</th>
                            <th>活动描述：</th>
                            <th>数量：</th>
                        </tr>
                        <?php
                            foreach($results as $item){
                                echo '
                                    <tr>
                                        <td>'.$item["EmployeeID"].'</td>
                                        <td>'.$item["ChineseName"].'</td>
                                        <td>'.$item["CampaignItemName"].'</td>
                                        <td>'.$item["CampaignItemDesc"].'</td>
                                        <td>'.$item["Quantity"].'</td>
                                    </tr>
                                ';
                            }
                        ?>
                    </table>
                    <div id="total">
                        <p id="friendorder_total_p">总计：<?php echo $amount ?> </p>
                    </div>
                </div>
            </div>
            <?php include("footer.php"); ?>
        </div>
    </body>
</html>