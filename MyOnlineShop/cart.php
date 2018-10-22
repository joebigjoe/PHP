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
    // get the current campaign id
    $data_integrity = DatabaseHandler::CheckShoppingCartDataIntegrity($_SESSION['user_id']);
    $if_item_overdue = DatabaseHandler::CheckShoppingCartForOverdueItems($_SESSION['user_id']);
    $campaign_id = DatabaseHandler::GetCurrentCampaignId($_SESSION['user_id']);
    $user_unionpoints_left=DatabaseHandler::GetUserUnionPoints($_SESSION['user_id']);
    $current_shoppingcart_itemamount = DatabaseHandler::GetCurrentShoppingCartAmount($_SESSION['user_id']);
    $if_multipleinsert = DatabaseHandler::GetCampaignIfCanInsertMultipleItems($campaign_id);
    $if_needunionpoints = DatabaseHandler::GetCampaignIfNeedUnionPoints($campaign_id);
    $if_releaseallunipoints = DatabaseHandler::GetCampaignIfReleaeAllUnionPoints($campaign_id);
    $if_singleitemmorethanone = DatabaseHandler::GetSingleItemMoreThanOne($campaign_id);
    $if_orderalreadyexists = DatabaseHandler::GetIfCampaignAlreadyExistInOrders($campaign_id, $_SESSION['user_id']);      
    $current_campaign_amount_forduplication = DatabaseHandler::GetIfHasDuplicatedShoppingcartItems($_SESSION['user_id']);    
    $shoppingcart_quantity = DatabaseHandler::GetCurrentShoppingCartQuality($_SESSION['user_id']);
    $if_extrainfo = DatabaseHandler::GetIfCampaignNeedsExtraInfo($campaign_id);
    $extrainfomax = DatabaseHandler::GetCampaigExtraInfoMaxCount($campaign_id);

    $action=null;
    $cart_id=null;
    $qty=null;
    $extracontent = null;
    $totalpoints_logic=null;
    $totalpoints_decrpt = "";

    if(isset($_GET['action'])){
        $action=$_GET['action'];
    }

    if(isset($_GET['id'])){
        $cart_id=$_GET['id'];
    }

    if(isset($_GET['qty'])){
        $qty=$_GET['qty'];
    }

    if(isset($_GET['extrainfo_content_summary'])){
        $extracontent=$_GET['extrainfo_content_summary'];
    }

    if(isset($_GET['total_points'])){
        $totalpoints_logic=$_GET['total_points'];

        try{
            $totalpoints_decrpt = encryptDecrypt("union",  $totalpoints_logic, 1);
            if(strlen(trim($totalpoints_decrpt)) > 2){
                echo '<script language="javascript">';
                echo 'alert("你提交的数据不正确，开发工会积分网站的目的是为大家兑换积分方便，并未做太多的数据安全测试，如果发现有人用平时开发测试的方法篡改数据，你的积分将被清零。")';
                echo '</script>';
                DatabaseHandler::Logging($_SESSION['user_id'],"Change the post using fiddler when place order.",$totalpoints_logic);
                exit();
            }
           
        }catch(Exception $ex){
             exit();
        }    
    }
    
    switch ($action) {
        case 'minus':
            if($qty<=1){
                echo '<script language="javascript">';
                echo 'alert("购物车里的这件商品只有一件，如不再需要购买该商品，请将其从购物车内删除。")';
                echo '</script>';
            }else{
                DatabaseHandler::ShoppingCartMinusOne($cart_id);
            }
            break;
        case 'plus':
            DatabaseHandler::ShoppingCartPlusOne($cart_id);
            break;
        case 'delete':
            DatabaseHandler::ShoppingCartDeleteOne($cart_id);
            header("Location: cart.php");
            exit();
            break;
        case 'order':
            // get the smallest union points to use
            // if release all, the use all the union points left, otherwise, use 6 as the default value, when union points less than 6. use the union points left.
            
            $theunionpointstouse = 0;
            if($if_releaseallunipoints == 1){
                $theunionpointstouse = $user_unionpoints_left;
            }else{
                 if($user_unionpoints_left < 6){
                    $theunionpointstouse = $user_unionpoints_left;
                 }else{
                     $theunionpointstouse = 6;
                 }
            }

            if($_SESSION['user_id'] == "guest"){
                header('Location: login.php');
            }else{
                if((int)$if_item_overdue == 1){
                    echo '<script language="javascript">';
                    echo 'alert("您的购物车里有过期的活动选项，您需要将它删除之后，才可以重新下单。")';
                    echo '</script>'; 
                }else{
                    if((int)$data_integrity == 1){
                        echo '<script language="javascript">';
                        echo 'alert("购物车内数据不正确，开发工会积分网站的目的是为大家兑换积分方便，并未做太多的数据安全测试，如果发现有人用平时开发测试的方法篡改数据，你的积分将被清零。")';
                        echo '</script>'; 
                    }else{
                        if((int)$current_shoppingcart_itemamount==0){
                            echo '<script language="javascript">';
                            echo 'alert("购物车为空，请先选择好自己的商品再下单。")';
                            echo '</script>'; 
                        }else{
                            if((int)$current_campaign_amount_forduplication > 1){
                                echo '<script language="javascript">';
                                echo 'alert("不同活动的商品，不能出现在同一个购物车里。")';
                                echo '</script>'; 
                            }else{
                                if((int)$if_orderalreadyexists > 0){
                                    echo '<script language="javascript">';
                                    echo 'alert("本次活动您已经存在一个订单，如需修改，请删除原订单后，重新下单。")';
                                    echo '</script>'; 
                                }else{
                                    if((int)$if_multipleinsert==0){
                                        if((int)$current_shoppingcart_itemamount > 1){
                                            echo '<script language="javascript">';
                                            echo 'alert("您选择的活动只允许选择一个选项，旅行和健身卡申办，都只能选择一个旅程和健身卡提供商。")';
                                            echo '</script>';
                                        }else{
                                            if((int)$totalpoints_decrpt > (int)$theunionpointstouse){
                                                echo '<script language="javascript">';
                                                echo 'alert("积分使用规则：普通活动可以使用6个积分，年终可以使用所有剩余积分，但是您选择的积分总和有可能大于您的剩余积分。请检查您所剩的积分与活动是否冲突，并做相应商品修改。")';
                                                echo '</script>';
                                            }else{
                                                $date = (new DateTime())->format('Y-m-d H:i:s');
                                                $date = str_replace(' ', '-',$date);
                                                $date = str_replace(':', '-',$date);
                                                $uuid = $_SESSION['user_id'].'-'.$date.'-'.$totalpoints_decrpt;
                                                $result_order = DatabaseHandler::PlaceAnOrder($_SESSION['user_id'], $campaign_id, $uuid, $totalpoints_decrpt, $extracontent,$date);
                                                if($result_order > 0){
                                                    header("Location: order.php?user_id=".$_SESSION['user_id']);
                                                    exit();
                                                }
                                            }
                                        }
                                    }else{
                                        if((int)$totalpoints_decrpt > (int)$theunionpointstouse){
                                            echo '<script language="javascript">';
                                            echo 'alert("积分使用规则：普通活动可以使用6个积分，年终可以使用所有剩余积分，但是您选择的积分总和有可能大于您的剩余积分。请检查您所剩的积分与活动是否冲突，并做相应商品修改。")';
                                            echo '</script>';
                                        }else{
                                            $date = (new DateTime())->format('Y-m-d H:i:s');
                                            $date = str_replace(' ', '-',$date);
                                            $date = str_replace(':', '-',$date);
                                            $uuid = $_SESSION['user_id'].'-'.$date.'-'.$totalpoints_decrpt;
                                            $result_order = DatabaseHandler::PlaceAnOrder($_SESSION['user_id'], $campaign_id, $uuid, $totalpoints_decrpt, $extracontent, $date);
                                            if($result_order > 0){
                                                header("Location: order.php?user_id=".$_SESSION['user_id']);
                                                exit();
                                            }
                                        }
                                    }  
                                }
                            }        
                        }
                    }
                }
            }
            break;
        default:
            ;
    }  
?>

<?php
    $totalpoints_ui = DatabaseHandler::GetTotalPointsFromCart($_SESSION['user_id']); 
    $points_encripted = "";
    if($totalpoints_ui == null){
        $totalpoints_ui = 0;
    }
    $points_encripted = urlencode(encryptDecrypt("union",  $totalpoints_ui, 0));
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
                <p id="cart_title">当前购物车清单：</p>
                <table id="table_cart">
                    <tr>
                        <th>商品名称：</th>
                        <th>商品描述：</th>
                        <th>数量：</th>
                        <th>单品积分：</th>
                        <th>小计：</th>
                        <th>操作：</th>
                    </tr>
                    <?php
                        DatabaseHandler::GetAllTheShoppingCart($_SESSION['user_id']);
                    ?>
                </table>
                <form method="get" action="cart.php">
                    <div>
                        <input type="hidden" name="action" value="order" />
                        <input type="hidden" name="total_points" value=<?php echo $points_encripted ?> />
                        <input type="hidden" name="user_id" value=<?php echo $_SESSION['user_id'] ?> />
                        <input type="hidden" id="if_multipleinsert_front" value=<?php echo $if_multipleinsert ?> />
                        <input type="hidden" id="if_singleitemmorethanone_front" value=<?php echo $if_singleitemmorethanone ?> />
                        <input type="hidden" id="current_campaign_amount_forduplication_front" value=<?php echo $current_campaign_amount_forduplication ?> />
                        <input type="hidden" id="if_orderalreadyexists_front" value=<?php echo $if_orderalreadyexists ?> />
                        <input type="hidden" id="if_item_overdue_front" value=<?php echo $if_item_overdue ?> />
                        <input type="hidden" id="extrainfomax_front" value=<?php echo $extrainfomax ?> />
                        <input type="hidden" id="if_extrainfo_front" value=<?php echo $if_extrainfo ?> />
                        <input type="hidden" name="extrainfo_content_summary" id="extrainfo_content_summary" value="" />
                    </div>
                    <?php
                        if($if_extrainfo == 1 &&  $shoppingcart_quantity > 1){
                            echo ' <p id="extrainfo_title">家属信息：工会购买保险使用，您自己的信息请在“完善个人信息”内填写。</p>
                            <div> 
                            <table id="extrainfo_content">
                            <tr>
                            <th>姓名</th>
                            <th>身份证号码</th> 
                            </tr>';
                              for ($i = 1; $i < $shoppingcart_quantity; $i++) {
                                    echo '<tr class="extra_info_row">';
                                    echo '<td width=30%><input size="10" type="text" class="extrainfo_name"></td>';
                                    echo '<td width=70%><input size="50" type="text" class="extrainfo_idcard"></td>';
                                    echo '</tr>';
                                }
                            echo '</table>
                            </div>';
                        }
                    ?>
                    <div id="cart_place_order" >
                        <div>
                            <p id="cart_total" >积分总计： <?php echo $totalpoints_ui; ?></p> 
                        </div>
                        <div>
                            <button id="order_button">兑换下单</button>
                        </div>  
                    </div>
                </form> 
                <div>
                    <p id="front_end_error"></p>
                </div>
            </div>
            <?php include("footer.php"); ?>
        </div>
        <div> <script src="scripts/cart20180918.js"></script></div>
    </body>
</html>