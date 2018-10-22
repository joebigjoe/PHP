<!DOCTYPE html>
<!--include-->
<?php
    require_once('include/common.php');
    require_once 'include/dbhandler.php';
?>

<!--the actual code-->
<?php
    $user_name_in=null;
    $password_in=null;
    $loginresult=null;

    if(isset($_POST['user_id'])){
        $user_name_in=$_POST['user_id'];
    }
    
    if(isset($_POST['password'])){
        $password_in=$_POST['password'];
        $md5ed_password = md5($password_in);
    }

    if($user_name_in && $md5ed_password){
        $loginresult=DatabaseHandler::GetLoginResult($user_name_in,$md5ed_password);
    } 
?>

<html>
    <head>
        <meta charset="utf-8">
        <title>用户登录-工会网店</title>
        <link rel="stylesheet" type="text/css" href="style/style.css">
        <script src="scripts/login.js"></script>
    </head>
    <body>
        <div class="container">
            <?php include("header.php"); ?>
            <div id="content">
                <div id="login_right">
                     <form id="login_form" action="login.php" method="post">
                        <p id="login_title">欢迎登录HP工会积分兑换网站</p>
                        <p class="form_name">用户ID:</p>
                        <p><input class="form_input" type="text" name="user_id" id="user_id" placeholder="用户工号"></p>
                        <p class="form_name">密 码: </p>
                        <p><input class="form_input" type="password" name="password" id="password" placeholder="用户密码"></p>
                        <p id="front_end_error"></p>
                        <?php
                            if($loginresult==1){
                                $_SESSION['user_id']=$user_name_in;
                                $_SESSION['password']=$md5ed_password;
                                header('Location: '.'index.php');
                                exit();
                            }else if($loginresult==2){
                                echo '<p class="erroroinfo">用户名和密码错误。</p>';
                            }else if ($loginresult==3){
                                echo '<p class="erroroinfo">密码错误。</p>';
                            }
                        ?>
                        <p>
                            <input id="submit_button" type="submit" name="submit" value="登录">
                            <input class="forget_password_button" type="button" name="forget_password" value="忘记密码" onclick="javascript:window.location.href='forgetpassword.php'">
                        </p>    
                    </form>
                </div>
                <div id="login_left">
                    <p id="looping_image_title">往期的精彩！</p>
                    <img id="looping_image" src="pictures/loop/images_0.jpg">
                </div>
            </div>
            <?php include("footer.php"); ?>
        </div>
    </body>
</html>