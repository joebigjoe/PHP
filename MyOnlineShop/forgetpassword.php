<!DOCTYPE html>
<!--include-->
<?php
    require_once('include/common.php');
    require_once 'include/dbhandler.php';
?>

<!--the actual code-->
<?php
    $user_id_new=null;
    $password_new=null;
    $password_new_re=null;
    $md5ed_password_new=null;
    $md5ed_password_new_re=null;
    $updatepasswordresult=null;

    if(isset($_POST['submit'])){
        if(isset($_POST['user_id'])){
            if($_POST['user_id'] !=''){
                $user_id_new=$_POST['user_id'];
            }
        }

        if(isset($_POST['new_password'])){
            if($_POST['new_password'] !=''){
                $password_new=$_POST['new_password'];
                $md5ed_password_new=md5($password_new);
            }
        }

        if(isset($_POST['new_re_password'])){
            if($_POST['new_re_password']){
                $password_new_re=$_POST['new_re_password'];
                $md5ed_password_new_re=md5($password_new_re);
            }
        }

        $user_in_db = DatabaseHandler::CheckUserExistence($user_id_new);

        if($user_in_db > 0){
            if($md5ed_password_new && $md5ed_password_new_re){
                if($md5ed_password_new==$md5ed_password_new_re){
                    if($md5ed_password_new!='e10adc3949ba59abbe56e057f20f883e'){
                        $updatepasswordresult=DatabaseHandler::UpdateUserPasswords($md5ed_password_new,$user_id_new);
                    }else{
                        echo '<script language="javascript">';
                        echo 'alert("新密码不能为初始化密码。")';
                        echo '</script>';
                    }
                }else{
                    echo '<script language="javascript">';
                    echo 'alert("新密码不一致。")';
                    echo '</script>';
                }

            }else{
                echo '<script language="javascript">';
                echo 'alert("新密码不能为空。")';
                echo '</script>';
            }
        }else{
            echo '<script language="javascript">';
            echo 'alert("您的用户ID不在工会数据库内，请发邮件至union.shptech@hp.com咨询。")';
            echo '</script>';
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
                <div id="login_right">
                     <form id="form_reset_pass" action="forgetpassword.php" method="post">
                        <p class="form_name">用户ID: </p>
                        <p><input class="form_input" type="text" name="user_id" id="user_id"></p>
                        <p class="form_name">新密码: </p>
                        <p><input class="form_input" type="password" name="new_password" id="new_password"></p>
                        <p class="form_name">确认密码: </p>
                        <p><input class="form_input" type="password" name="new_re_password" id="new_re_password"></p>
                        <p id="front_end_error"></p>
                        <?php
                            if($updatepasswordresult!=null){
                                if($updatepasswordresult>0){
                                    session_start();
                                    session_destroy();
                                    header('Location: '.'index.php');
                                    exit;
                                }else{
                                    echo '<p class="erroroinfo">密码修改失败。您现在设置的密码应该就是您的登录密码。</p>';
                                }
                            }
                        ?>
                        <p><input id="submit_button" type="submit" name="submit" value="设置"></p>    
                    </form>
                </div>
                <div id="login_left">
                    <p id="looping_image_title">往期的精彩！</p>
                    <img id="looping_image" src="pictures/loop/images_0.jpg">
                </div>
            </div>
            <?php include("footer.php"); ?>
        </div>
        <div><script src="scripts/forgetpassword.js"></script></div>
    </body>
</html>