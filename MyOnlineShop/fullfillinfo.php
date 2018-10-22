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

<?php
    function is_idcard( $id )
    {
      $id = strtoupper($id);
      $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
      $arr_split = array();
      if(!preg_match($regx, $id))
      {
        return FALSE;
      }
      if(15==strlen($id)) //检查15位
      {
        $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
        @preg_match($regx, $id, $arr_split);
        //检查生日日期是否正确
        $dtm_birth = "19".$arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
        if(!strtotime($dtm_birth))
        {
          return FALSE;
        }
        else
        {
          return TRUE;
        }
      }
      else //检查18位
      {
        $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($regx, $id, $arr_split);
        $dtm_birth = $arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
        if(!strtotime($dtm_birth)) //检查生日日期是否正确
        {
          return FALSE;
        }
        else
        {
          //检验18位身份证的校验码是否正确。
          //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
          $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
          $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
          $sign = 0;
          for ( $i = 0; $i < 17; $i++ )
          {
            $b = (int) $id{$i};
            $w = $arr_int[$i];
            $sign += $b * $w;
          }
          $n = $sign % 11;
          $val_num = $arr_ch[$n];
          if ($val_num != substr($id,17, 1))
          {
            return FALSE;
          }
          else
          {
            return TRUE;
          }
        }
      }
    }

    function is_chinese_mobile_number($number){
        $pattern = '/^1([0-9]{9})/';
        if(preg_match($pattern, $number)){
            return true;
        }else{
            return false;
        }
    }
?>

<!--the actual code-->
<?php
    $english_name=null;
    $chinese_name=null;
    $identification=null;
    $passport=null;
    $passport_pinyin=null;
    $mobile_num=null;
    $email_add=null;
    $update_result=null;
    $admininfo=null;
    $admininfo_email=null;

    if(isset($_POST['submit'])){
        if(isset($_POST['name_english'])){
            if($_POST['name_english'] !=''){
                $english_name=$_POST['name_english'];
            }
        }

        if(isset($_POST['name_chinese'])){
            if($_POST['name_chinese']){
                $chinese_name=$_POST['name_chinese'];
            }
        }
        
        if(isset($_POST['id_card'])){
            if($_POST['id_card']){
                $identification=$_POST['id_card'];
            }
        }
        
        if(isset($_POST['passport'])){
            if($_POST['passport']){
                $passport=$_POST['passport'];
            }
        }
        
        if(isset($_POST['passport_pinyin'])){
            if($_POST['passport_pinyin']){
                $passport_pinyin=$_POST['passport_pinyin'];
            }
        }
        
        if(isset($_POST['mobile_phone'])){
            if($_POST['mobile_phone']){
                 $mobile_num=$_POST['mobile_phone'];
            }
        }
        
        if(isset($_POST['email'])){
            if($_POST['email']){
                $email_add=$_POST['email'];
            }
        }
        
        if(isset($_POST['admininfo'])){
            if($_POST['admininfo']){
                $admininfo=$_POST['admininfo'];
            }
        }

        if(isset($_POST['admininfo_email'])){
            if($_POST['admininfo_email']){
                $admininfo_email=$_POST['admininfo_email'];
            }
        }

        if($chinese_name && $admininfo){
            $update_result = DatabaseHandler::UpdateUserProfile($_SESSION['user_id'],$english_name,$chinese_name,$identification,$mobile_num,$email_add,$passport,$passport_pinyin,$admininfo, $admininfo_email);
        }
    }
?>

<?php
    $userinfo = DatabaseHandler::GetuserProfileById($_SESSION['user_id']);
    $userinfo_en_name= $userinfo["EnglishName"];
    if($userinfo_en_name==null){
        $userinfo_en_name="";
    }
    $userinfo_cn_name= $userinfo["ChineseName"];
    if($userinfo_cn_name==null ){
        $userinfo_cn_name="";
    }
    $userinfo_id_card= $userinfo["IDCardNo"];
    if($userinfo_id_card==null ){
        $userinfo_id_card="";
    }
    $userinfo_passport= $userinfo["Passport"];
    if($userinfo_passport==null ){
        $userinfo_passport="";
    }
    $userinfo_passport_pinyin= $userinfo["PassportPinyin"];
    if($userinfo_passport_pinyin==null ){
        $userinfo_passport_pinyin="";
    }
    $userinfo_phone_num= $userinfo["PhoneNo"];
    if($userinfo_phone_num==null ){
        $userinfo_phone_num="";
    }
    $userinfo_email_address= $userinfo["EmailAddress"];
    if($userinfo_email_address==null ){
        $userinfo_email_address="";
    }
    $userinfo_admininfo= $userinfo["AAName"];
    if($userinfo_admininfo==null ){
        $userinfo_admininfo="";
    }
    $userinfo_admininfo_email= $userinfo["AAEmail"];
    if($userinfo_admininfo_email==null ){
        $userinfo_admininfo_email="";
    }
?>

<?php
    if($update_result != null){
        header("Location: fullfillinfo.php?user_id=".$_SESSION['user_id']);
        exit();
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
                <p id="info_for_your_eyes_only">员工个人信息仅限个人和工会同事可见</p>
                <div id="login_right_fullfill">
                     <form id="form_fullfill_info" action="fullfillinfo.php" method="post">
                        <p id="form_fill_title">更新员工信息:</p>
                        <p class="form_fill_name">员工号: </p>
                        <p ><label id="grey_dot">*</label><input type="text" class="form_fill_input" name="employee_id" value=<?php echo $_SESSION['user_id']; ?> disabled></p>
                        <p class="form_fill_name">员工中文名: </p>
                        <p ><label class="red_dot">*</label><input type="text" class="form_fill_input" name="name_chinese" id="name_chinese" value=<?php echo "\"".$userinfo_cn_name."\""; ?> placeholder="请填写您的中文名"></p>
                        <p class="form_fill_name">AA-姓名: </p>
                        <p ><label class="red_dot">*</label><input type="text" class="form_fill_input" name="admininfo" id="admininfo" value=<?php echo "\"".$userinfo_admininfo."\""; ?> placeholder="请填写您的AA姓名"></p>
                        <p class="form_fill_name">AA-邮箱: </p>
                        <p ><label class="green_dot">*</label><input type="text" class="form_fill_input" name="admininfo_email" id="admininfo_email" value=<?php echo "\"".$userinfo_admininfo_email."\""; ?> placeholder="请填写您的AA邮箱，如果您不知道您的AA邮箱，可以选择不填"></p>
                        <p class="form_fill_name">员工英文名: </p>
                        <p ><label class="green_dot">*</label><input type="text" class="form_fill_input" name="name_english" id="name_english" value=<?php echo "\"".$userinfo_en_name."\""; ?> placeholder="请填写您的英文名，若没有英文名，请输入汉语拼音"></p>
                        <p class="form_fill_name">员工身份证号码: </p>
                        <p ><label class="green_dot">*</label><input type="text" class="form_fill_input" name="id_card" id="id_card" value=<?php echo "\"".$userinfo_id_card."\""; ?> placeholder="身份证号码用于工会旅游和健身卡申办，工会承诺不会泄露您的信息"></p>
                        <p class="form_fill_name">员工移动电话: </p>
                        <p ><label class="green_dot">*</label><input type="text" class="form_fill_input" name="mobile_phone" id="mobile_phone" value=<?php echo "\"".$userinfo_phone_num."\""; ?> placeholder="电话号码用于在发放卡券时电话联系您，工会承诺不会泄露您的信息"></p>
                        <p class="form_fill_name">员工电子邮件: </p>
                        <p ><label class="green_dot">*</label><input type="text" class="form_fill_input" name="email" id="email" value=<?php echo "\"".$userinfo_email_address."\""; ?> placeholder="电子邮件用于在发放卡券时OC联系您，工会承诺不会泄露您的信息"></p>
                        <p class="form_fill_name">员工护照号码: </p>
                        <p ><label class="green_dot">*</label><input type="text" class="form_fill_input" name="passport" id="passport" value=<?php echo "\"".$userinfo_passport."\""; ?> placeholder="护照号码用于境外工会旅游，工会承诺不会泄露您的信息"></p>
                        <p class="form_fill_name">员工护照姓名-汉语拼音: </p>
                        <p ><label class="green_dot">*</label><input type="text" class="form_fill_input" name="passport_pinyin" id="passport_pinyin" value=<?php echo "\"".$userinfo_passport_pinyin."\""; ?> placeholder="护照姓名汉语拼音用于境外工会旅游，工会承诺不会泄露您的信息"></p>
                        <p id="front_end_error"></p>
                        <p><input id="submit_button" type="submit" name="submit" value="保存信息"></p>    
                    </form>
                </div>
                <div id="login_left_fullfill">
                    <form >
                        <p id="form_fill_title">当前员工信息: </p>
                        <p class="form_fill_name">员工号: </p>
                        <p ><input type="text" class="form_fill_input" name="employee_id" value=<?php echo $_SESSION['user_id']; ?> disabled></p>
                        <p class="form_fill_name">员工中文名: </p>
                        <p ><input type="text" class="form_fill_input" name="name_chinese" value=<?php echo "\"".$userinfo_cn_name."\""; ?> disabled></p>
                        <p class="form_fill_name">AA-姓名</p>
                        <p ><input type="text" class="form_fill_input" name="admininfo" value=<?php echo "\"".$userinfo_admininfo."\""; ?> disabled></p>
                        <p class="form_fill_name">AA-邮箱</p>
                        <p ><input type="text" class="form_fill_input" name="admininfo" value=<?php echo "\"".$userinfo_admininfo_email."\""; ?> disabled></p>
                        <p class="form_fill_name">员工英文名: </p>
                        <p ><input type="text" class="form_fill_input" name="name_english" value=<?php echo "\"".$userinfo_en_name."\""; ?> disabled></p>
                        <p class="form_fill_name">员工身份证号码: </p>
                        <p ><input type="text" class="form_fill_input" name="id_card" value=<?php echo "\"".$userinfo_id_card."\""; ?> disabled></p>
                        <p class="form_fill_name">员工移动电话: </p>
                        <p ><input type="text" class="form_fill_input" name="mobile_phone" value=<?php echo "\"".$userinfo_phone_num."\""; ?> disabled></p>
                        <p class="form_fill_name">员工电子邮件: </p>
                        <p ><input type="text" class="form_fill_input" name="email" id="email" value=<?php echo "\"".$userinfo_email_address."\""; ?> disabled></p>
                        <p class="form_fill_name">员工护照号码: </p>
                        <p ><input type="text" class="form_fill_input" name="passport" value=<?php echo "\"".$userinfo_passport."\""; ?> disabled></p>
                        <p class="form_fill_name">员工护照姓名-汉语拼音: </p>
                        <p ><input type="text" class="form_fill_input" name="passport_pinyin" value=<?php echo "\"".$userinfo_passport_pinyin."\""; ?> disabled></p>
                    </form>
                </div>
            </div>
           <?php include("footer.php"); ?>
        </div>
        <div><script src="scripts/fullfillinfo.js"></script></div>
    </body>
</html>