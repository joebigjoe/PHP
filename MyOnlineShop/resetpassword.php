<!DOCTYPE html>
<!--include-->
<?php
    require_once('include/common.php');
    require_once 'include/dbhandler.php';
    if($_SESSION['user_id']=="guest"){
        header("Location: login.php");
        exit();
    }
?>

<!--the actual code-->
<?php
    $password_new=null;
    $password_new_re=null;
    $md5ed_password_new=null;
    $md5ed_password_new_re=null;
    $updatepasswordresult=null;

    if(isset($_POST['submit'])){
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

        if($md5ed_password_new && $md5ed_password_new_re){
            if($md5ed_password_new==$md5ed_password_new_re){
                if($md5ed_password_new!='e10adc3949ba59abbe56e057f20f883e'){
                    if($md5ed_password_new!=$_SESSION['password']){
                         $updatepasswordresult=DatabaseHandler::UpdateUserPasswords($md5ed_password_new,$_SESSION['user_id']);
                    }else{
                        echo '<script language="javascript">';
                        echo 'alert("新密码不能为当前密码。")';
                        echo '</script>';
                    }
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
                     <form id="form_reset_pass" action="resetpassword.php" method="post">
                        <p class="form_name">旧密码: </p>
                        <p><input class="form_input" type="password" name="old_password" value=<?php echo $_SESSION['password']; ?> disabled></p>
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
                                    echo '<p class="erroroinfo">密码修改错误。</p>';
                                }
                            }
                        ?>
                        <p><input id="submit_button" type="submit" name="submit" value="修改"></p>    
                    </form>
                </div>
                <div id="login_left">
                    <p id="looping_image_title">往期的精彩！</p>
                    <img id="looping_image" src="pictures/loop/images_0.jpg">
                </div>
            </div>
            <?php include("footer.php"); ?>
        </div>
        <div><script src="scripts/resetpassword.js"></script></div>
    </body>
</html>