<?php
    // include decrpt
    require_once "functionlist.php";   

    // the connection constant for the DB
    define('DB_PERSISTENCY', 'true');
    define('DB_SERVER', 'DB_SERVER');
    define('DB_USERNAME', 'DB_USERNAME');
    define('DB_PASSWORD', 'DB_PASSWORD');
    define('DB_DATABASE', 'DB_DATABASE');
    define('PDO_DSN', 'mysql:host=' .DB_SERVER . ';dbname='.DB_DATABASE. ';charset=utf8');
    
    // class that provide access to the database
    class DatabaseHandler{
        
        // hold only one instance of the PDO class.
        private static $_mHandler;
        
        // private constrcutor to prevent direct creation of the object
        public function __constrcut(){
            
        }
        
        // return an initialized database handler
        private static function GetDB(){
            // create database connection only if one does not alreay exist
            if(!isset(self::$_mHandler)){
                
                try{
                    // create database connection
                    self::$_mHandler=new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => DB_PERSISTENCY));
                    // config PDO to throw exceptions
                    self::$_mHandler->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

                } catch (Exception $ex) {
                    self::Close();
                    trigger_error($ex->getMessage(), E_USER_ERROR);
                }
            }
            return self::$_mHandler;
        }
        
        // close db
        public static function Close(){
            self::$_mHandler = null;
        }
        
        // get all campain info
        public static function GetAllCampaignInfo(){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call get_all_campaigninfo");
            $dbhandler->execute();
            while ($row = $dbhandler->fetch(PDO::FETCH_ASSOC)) {
                $c_id = encryptDecrypt("union",  $row["id"], 0);
                echo '<a href="index.php?campaign_id='.$c_id.'"><li>'.$row['CampaignName'].'</li></a>';
            }
        }
        
        // get all the latest campaign id
        public static function GetLatestCampaignInfo(){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call get_latest_campaigninfo");
            $dbhandler->execute();
            $row = $dbhandler->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        
        // get all the latest campaign id
        public static function GetNeededCampaignInfo($campaignid){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call get_needed_campaigninfo(:campaign_id)");
            $dbhandler->bindValue("campaign_id",$campaignid);
            $dbhandler->execute();
            $row = $dbhandler->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        
         // get all the campaign items info
        public static function GetCampaignItemsInfo($campaignid){
            $campaign_status = self::GetCampaignStatus($campaignid);
            $show_friend = self::GetCampaignIfCanInsertMultipleItems($campaignid);
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call get_all_campaignitemsinfo(:campaign_id)");
            $dbhandler->bindValue("campaign_id",$campaignid);
            $dbhandler->execute();
            if($campaign_status == 1){
                if($show_friend == 0){
                    while ($row = $dbhandler->fetch(PDO::FETCH_ASSOC)) {
                        $item_encrpt = urlencode(encryptDecrypt("union",  $row["ID"], 0));
                        $campaign_encrpt = urlencode(encryptDecrypt("union",  $row["CampaignID"], 0));
                        $point_this = urlencode(encryptDecrypt("union",  $row["UnionPointsThis"], 0));
                        echo '
                            <div class="single_option">
                                <p>'.$row['CampaignItemName'].'</p>
                                <a href="activitydetails.php?campaign_item_id='.$item_encrpt.'" title="点击图片，查看活动详情"><img src="'.$row['PicturePath'].'" width="100" height="100"></a>
                                <p>'.$row['CampaignItemDesc'].'</p>
                                <p>所需积分：'.$row['UnionPointsThis'].'</p>
                                <a href="showfriend.php?campaign_item_id='.$item_encrpt.'"'.'>查看小伙伴</a>
                                <a href="index.php?user_id='.$_SESSION["user_id"].'&action=add_to_cart'.'&campaign_id='.$campaign_encrpt.'&campaign_item_id='.$item_encrpt.'&ci_name='.$row["CampaignItemName"].'&ci_desc='.$row["CampaignItemDesc"].'&points='.$point_this.'"'.'>加入购物车</a>
                            </div>'
                        ;
                    }
                }else{
                    while ($row = $dbhandler->fetch(PDO::FETCH_ASSOC)) {
                        $item_encrpt = urlencode(encryptDecrypt("union",  $row["ID"], 0));
                        $campaign_encrpt = urlencode(encryptDecrypt("union",  $row["CampaignID"], 0));
                        $point_this = urlencode(encryptDecrypt("union",  $row["UnionPointsThis"], 0));
                        echo '
                            <div class="single_option">
                                <p>'.$row['CampaignItemName'].'</p>
                                <img src="'.$row['PicturePath'].'" width="100" height="100"> 
                                <p>'.$row['CampaignItemDesc'].'</p>
                                <p>所需积分：'.$row['UnionPointsThis'].'</p>
                                <a href="index.php?user_id='.$_SESSION["user_id"].'&action=add_to_cart'.'&campaign_id='.$campaign_encrpt.'&campaign_item_id='.$item_encrpt.'&ci_name='.$row["CampaignItemName"].'&ci_desc='.$row["CampaignItemDesc"].'&points='.$point_this.'"'.'>加入购物车</a>
                            </div>'
                        ;
                    }
                }
            }else{
                while ($row = $dbhandler->fetch(PDO::FETCH_ASSOC)) {
                echo '
                    <div class="single_option">
                        <p>'.$row['CampaignItemName'].'</p>
                        <img src="'.$row['PicturePath'].'" width="100" height="100"> 
                        <p>'.$row['CampaignItemDesc'].'</p>
                        <p>所需积分：'.$row['UnionPointsThis'].'</p>
                    </div>'
                ;
                }
            }
            
        }
        
        // get the current compaign id from the shopping cart
        public static function GetCurrentCampaignId($user_id){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call get_currentcompaignid_from_shoppingcart(?, @result)");
            $dbhandler->bindParam(1,$user_id);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
        // get the compaign status to delete the add to cart link
        public static function GetCampaignStatus($campaignid){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call compaign_get_status(?, @result)");
            $dbhandler->bindParam(1,$campaignid);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
         // get the compaign if release all union points
        public static function GetCampaignIfReleaeAllUnionPoints($campaignid){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call compaign_get_if_releaseallunionpoints(?, @result)");
            $dbhandler->bindParam(1,$campaignid);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
        // get the compaign if need union points
        public static function GetCampaignIfNeedUnionPoints($campaignid){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call compaign_get_if_needunionpoints(?, @result)");
            $dbhandler->bindParam(1,$campaignid);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
         // get the compaign if can insert multiple items
        public static function GetCampaignIfCanInsertMultipleItems($campaignid){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call compaign_get_if_caninsertmultipleitems(?, @result)");
            $dbhandler->bindParam(1,$campaignid);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
        // get the shooping cart current item amount
        public static function GetCurrentShoppingCartAmount($user_id){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call shoppingcart_get_current_items_amount(?, @result)");
            $dbhandler->bindParam(1,$user_id);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
        // get the shooping cart current item amount
        public static function GetIfCampaignAlreadyExistInOrders($campaignid, $userid){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call orders_if_campaign_already_exist(?, ?, @result)");
            $dbhandler->bindParam(1,$campaignid);
            $dbhandler->bindParam(2,$userid);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
        // get the login result
        public static function GetLoginResult($user_name, $password_){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call get_login_result(?, ?, @result)");
            $dbhandler->bindParam(1,$user_name);
            $dbhandler->bindParam(2,$password_);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
         // add to cart
        public static function AddItemToShoppingCart($user_id, $campaign_id,$campaign_item_id,$ci_name, $ci_desc,$points, $date_now){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call add_item_to_shoppingcart(?, ?, ?, ?, ?, ?, ?, @result)");
            $dbhandler->bindParam(1,$user_id);
            $dbhandler->bindParam(2,$campaign_id);
            $dbhandler->bindParam(3,$campaign_item_id);
            $dbhandler->bindParam(4,$ci_name);
            $dbhandler->bindParam(5,$ci_desc);
            $dbhandler->bindParam(6,$points);
            $dbhandler->bindParam(7,$date_now);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
         // get all the items in the shopping cart for designated user
        public static function GetAllTheShoppingCart($userid){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call get_all_items_shoppingcart(:userid)");
            $dbhandler->bindValue("userid",$userid);
            $dbhandler->execute();
            while ($row = $dbhandler->fetch(PDO::FETCH_ASSOC)) {
                echo '
                    <tr class="shoppingcart_item">
                        <td>'.$row['CampaignItemName'].'</td>
                        <td>'.$row['CampaignItemDesc'].'</td>
                        <td><a class="minus_plus_link" href="cart.php?action=minus&id='.$row['ID'].'&qty='.$row['Quantity'].'">-</a>'.'<input type="text" class="singleamout" value='.$row['Quantity'].' disabled>'.'<a class="minus_plus_link" href="cart.php?action=plus&id='.$row['ID'].'">+</a></td>
                        <td>'.$row['Points'].'</td>
                        <td>'.$row['TotalPoints'].'</td>
                        <td><a href="cart.php?action=delete&id='.$row['ID'].'">删除</a></td>
                    </tr>
                ';
            }
        }
        
         // get all the items in the orders table for a designated order
        public static function GetAllTheOrderInfo($userid){
            $results = array();
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call orders_get_orders(:userid)");
            $dbhandler->bindValue("userid",$userid);
            $dbhandler->execute();
            while ($row = $dbhandler->fetch(PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
            return $results;
        }
        
          // get all the items in the orders item table for a designated order
        public static function GetAllTheOrderItemInfo($order_id){
            $db = self::GetDB();
              $dbhandler_ = $db->prepare("Call orders_get_order_items(:order_id)");
                $dbhandler_->bindValue("order_id",$order_id);
                $dbhandler_->execute();
                while ($row_ = $dbhandler_->fetch(PDO::FETCH_ASSOC))
                
                echo '
                    <tr>
                        <td>'.$row_['CampaignItemName'].'</td>
                        <td>'.$row_['CampaignItemDesc'].'</td>
                        <td>'.$row_['Quantity'].'</td>
                        <td>'.$row_['Points'].'</td>
                        <td>'.$row_['TotalPoints'].'</td>
                    </tr>
                ';
        }
        
         // get the totalpoints in the shopping cart for a designated user
        public static function GetTotalPointsFromCart($user_id){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call shoppingcart_calculate_totalpoints(?, @result)");
            $dbhandler->bindParam(1,$user_id);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
        // minus shopping cart for 1
        public static function ShoppingCartMinusOne($cart_id){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call shoppingcart_minus_one(?)");
            $dbhandler->bindParam(1,$cart_id);
            $dbhandler->execute();
        }
        
        // plus shopping cart for 1
        public static function ShoppingCartPlusOne($cart_id){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call shoppingcart_plus_one(?)");
            $dbhandler->bindParam(1,$cart_id);
            $dbhandler->execute();
        }
        
        // delete shopping cart for 1
        public static function ShoppingCartDeleteOne($cart_id){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call shoppingcart_delete_one(?)");
            $dbhandler->bindParam(1,$cart_id);
            $dbhandler->execute();
        }
        
        // place an order
        public static function PlaceAnOrder($user_id, $campaign_id, $uuid, $total_points, $extra_info, $date){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call orders_place_an_order_full(?,?,?,?,?,?,@result)");
            $dbhandler->bindParam(1,$user_id);
            $dbhandler->bindParam(2,$campaign_id);
            $dbhandler->bindParam(3,$uuid);
            $dbhandler->bindParam(4,$total_points);
            $dbhandler->bindParam(5,$extra_info);
            $dbhandler->bindParam(6,$date);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
        // delete an order
        public static function DeleteAnOrder($user_id,$order_id,$total_points){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call orders_delete_an_order_full(?,?,?,@result)");
            $dbhandler->bindParam(1,$user_id);
            $dbhandler->bindParam(2,$order_id);
            $dbhandler->bindParam(3,$total_points);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
         // get the unionpoints left for a designated user
        public static function GetUserUnionPoints($user_id){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call get_user_unionpoints(?, @result)");
            $dbhandler->bindParam(1,$user_id);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
         // get the employee update password
        public static function UpdateUserPasswords($password, $user_id){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call employee_update_password(?, ?, @result)");
            $dbhandler->bindParam(1,$password);
            $dbhandler->bindParam(2,$user_id);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
        // update employee profile
        public static function UpdateUserProfile($employee_id, $en_name, $cn_name, $id_card, $phonenumber, $email, $passport, $passport_pinyin, $aaname, $aaemail){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call employee_update_userprofile(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @result)");
            $dbhandler->bindParam(1,$employee_id);
            $dbhandler->bindParam(2,$en_name);
            $dbhandler->bindParam(3,$cn_name);
            $dbhandler->bindParam(4,$id_card);
            $dbhandler->bindParam(5,$phonenumber);
            $dbhandler->bindParam(6,$email);
            $dbhandler->bindParam(7,$passport);
            $dbhandler->bindParam(8,$passport_pinyin);
            $dbhandler->bindParam(9,$aaname);
            $dbhandler->bindParam(10,$aaemail);
            
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
         // get all the userprofile by id
        public static function GetuserProfileById($userid){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call employee_get_userprofile_byid(:user_id)");
            $dbhandler->bindValue("user_id",$userid);
            $dbhandler->execute();
            $row = $dbhandler->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        
         // get the current shoppingcart quantity
        public static function GetCurrentShoppingCartQuality($user_id){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call shoppingcart_get_items_quantity(?, @result)");
            $dbhandler->bindParam(1,$user_id);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
        // get the the duplicate shopping cart items
        public static function GetIfHasDuplicatedShoppingcartItems($user_id){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call shoppingcart_if_has_duplicate_campaigns(?, @result)");
            $dbhandler->bindParam(1,$user_id);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
         // get what my friends has ordered
        public static function GetAllMyFriendsOrders($campaign_item_id){
            $results = array();
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call orders_get_what_myfriend_choose(:campaign_item_id)");
            $dbhandler->bindValue("campaign_item_id",$campaign_item_id);
            $dbhandler->execute();
            while ($row = $dbhandler->fetch(PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
            return $results;
        }
        
        // get what my friends has ordered amount
        public static function GetAllMyFriendsOrdersAmount($campaign_item_id){
            $results = array();
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call orders_get_what_myfriend_choose_amount(?, @result)");
            $dbhandler->bindParam(1,$campaign_item_id);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
        
        // get pdf path from campaignitem table
        public static function GetCampaignItemPDF($campaign_item_id){
            $results = array();
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call campaignitem_get_pdf(?, @result)");
            $dbhandler->bindParam(1,$campaign_item_id);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }

        // check the shopping cart data integrity
        public static function CheckShoppingCartDataIntegrity($user_id){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call shoppingcart_check_data_integrity(?, @result)");
            $dbhandler->bindParam(1,$user_id);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }

        // logging bad bahavior
        public static function Logging($user_id, $reason,$points){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call logging_for_special_occation(?, ?, ?)");
            $dbhandler->bindParam(1,$user_id);
            $dbhandler->bindParam(2,$reason);
            $dbhandler->bindParam(3,$points);
            $dbhandler->execute();
        }

        // check the shopping cart to see if there is overdued items
        public static function CheckShoppingCartForOverdueItems($user_id){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call shoppingcart_if_items_overdue(?, @result)");
            $dbhandler->bindParam(1,$user_id);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }

        // check user exsistence
        // get the compaign if can insert multiple items
        public static function CheckUserExistence($employeeid){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call employee_check_existence(?, @result)");
            $dbhandler->bindParam(1,$employeeid);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }

        // get the compaign if can insert multiple items
        public static function GetSingleItemMoreThanOne($campaignid){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call compaign_get_if_singleitemsmorethanone(?, @result)");
            $dbhandler->bindParam(1,$campaignid);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }

        // get in campaign need extra info
        public static function GetIfCampaignNeedsExtraInfo($campaignid){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call compaign_get_if_needextrainfo(?, @result)");
            $dbhandler->bindParam(1,$campaignid);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }

        // get in campaign need extra info
        public static function GetCampaigExtraInfoMaxCount($campaignid){
            $db = self::GetDB();
            $dbhandler = $db->prepare("Call compaign_get_extrainfo_max(?, @result)");
            $dbhandler->bindParam(1,$campaignid);
            $dbhandler->execute();
            $result = $db->query("select @result")->fetch(PDO::FETCH_ASSOC);
            return $result['@result'];
        }
    }   
?>