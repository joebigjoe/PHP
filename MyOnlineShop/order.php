<!DOCTYPE html>
<!--include-->
<?php
    require_once('include/common.php');
    require_once 'include/dbhandler.php';
    require_once "include/functionlist.php";    
    if($_SESSION['user_id']=="guest"){
        header("Location: login.php");
        exit();
    }
?>

<!--the actual code-->
<?php
    $user_id_=null;
    $order_id=null;
    $action=null;
    $total_points=null;
    $id_decrpt=null;
    $points_decrpt=null;

    if(isset($_GET['user_id'])){
        $user_id_=$_GET['user_id'];
    }

    if(isset($_GET['order_id'])){
        $order_id=$_GET['order_id'];
        try{
            $id_decrpt = encryptDecrypt("union",  $order_id, 1);
        }catch(Exception $ex){
            $id_decrpt = "0";
        }
    }

    if(isset($_GET['action'])){
        $action=$_GET['action'];
    }

    if(isset($_GET['total_points'])){
        $total_points=$_GET['total_points'];
        try{
            $points_decrpt = encryptDecrypt("union",  $total_points, 1);
            if(strlen(trim($points_decrpt)) > 2){
                echo '<script language="javascript">';
                echo 'alert("你提交的数据不正确，开发工会积分网站的目的是为大家兑换积分方便，并未做太多的数据安全测试，如果发现有人用平时开发测试的方法篡改数据，你的积分将被清零。")';
                echo '</script>';
                DatabaseHandler::Logging($_SESSION['user_id'],"Change the post using fiddler when delete order.",$total_points);
                exit();
            }
        }catch(Exception $ex){
            exit();
        }
    }

    if($action=='delete_order'){
        $result = DatabaseHandler::DeleteAnOrder($user_id_,$id_decrpt,$points_decrpt);
        if($result>0){
            header("Location: order.php?user_id=".$_SESSION['user_id']);
            exit();
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
                <div id="order_list_contianer">
                    <div id="order_list_contianer_title">
                        <p id="order_list_contianer_title_p">用户订单列表：</p>
                    </div> 
                    <?php
                        $result_array = DatabaseHandler::GetAllTheOrderInfo($user_id_);

                        $row_count=count($result_array);

                        if($row_count==0){
                            echo '
                                <div id="no_orders"><p>当前用户没有任何订单</p></div>
                            ';
                        }else{
                             foreach($result_array as $items){
                                $campaign_status = $items["Status"];
                                 
                                echo '<div class="order_title">
                                <p>订单号：'.$items["UUID"].'</p>
                                <p>积分：'.$items["TotalPoints"].'</p>
                                </div>';

                                echo '<table class="table_order"><tr>
                                <th width="400px">商品名称：</th>
                                <th width="400px">商品描述：</th>
                                <th width="50px">数量：</th>
                                <th width="100px">单品积分：</th>
                                <th width="50px">小计：</th>
                                </tr>';

                                DatabaseHandler::GetAllTheOrderItemInfo($items['ID']);

                                echo '</table>';

                                echo '<div id="order_extra_info">附加信息：<br>'.$items["ExtraInfo"].'</div>';

                                if($campaign_status == 1){
                                    $points_encrpt = urlencode(encryptDecrypt("union",  $items["TotalPoints"], 0));
                                    $id_encrpt = urlencode(encryptDecrypt("union",  $items["ID"], 0));
                                    echo '<div id="delete_order">
                                    <a href="order.php?action=delete_order&order_id='.$id_encrpt.'&user_id='.$_SESSION['user_id'].'&total_points='.$points_encrpt.'">删除订单</a></div>';
                                }
                                echo '<div id="placeholder"></div>';
                            }
                        }
                    ?>
                </div>
            </div>
            <?php include("footer.php"); ?>
        </div>
        <div><script type="application/javascript" src="scripts/order.js"></script></div>
    </body>
</html>