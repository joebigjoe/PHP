<?php
    require_once("include/common.php");
    require_once("include/dbhandler.php");

    $user_id = $_SESSION['user_id'];
    $user_unionpoints = 0;
    $shoppingcart_quantity = 0;

    if($user_id=='guest'){
        $user_unionpoints = 0;
    }else{
         $user_unionpoints = DatabaseHandler::GetUserUnionPoints($user_id);
    }

    $shoppingcart_quantity = DatabaseHandler::GetCurrentShoppingCartQuality($user_id);
    if($user_id=='guest' || $shoppingcart_quantity==null){
        $shoppingcart_quantity = 0;
    }
?>
    
<div class="header">
    <a href="index.php" title="返航主页面"><img id="logo" src="pictures/hplogo.jpg" alt="HP Logo" /></a>
    <img id="banner" src="pictures/hp-banner.jpg" alt="HP Banner" />
</div>
<div class="menubar">
    <ul id="menu">
        <a href="index.php"><li>主页</li></a>
        <li class="menuinfo">当前用户：<?php echo $user_id ?></li>
        <li class="menuinfo">用户积分：<?php echo $user_unionpoints ?></li>
        <a href="resetpassword.php"><li>修改密码</li></a>
        <a href="fullfillinfo.php"><li>完善个人信息</li></a>
        <?php 
            echo '<a href="order.php?user_id='.$user_id.'"><li>我的订单</li></a>';
        ?>
        <a href="cart.php"><li>购物车<?php echo "[ ".$shoppingcart_quantity." ]" ?></li></a>
        <a href="logout.php"><li>退出</li></a>
    </ul>
</div>