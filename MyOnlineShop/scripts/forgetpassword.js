function loopingTheImage(){
    // set the lopping image
    var imageContainer = document.getElementById("looping_image");    
    // image array
    var imageList = ["pictures/loop/images_1.jpg", "pictures/loop/images_2.jpg","pictures/loop/images_3.jpg","pictures/loop/images_4.jpg","pictures/loop/images_5.jpg"];
    // start index
    var startIndex = 0;
    // change image
    function changeImage(){
        imageContainer.setAttribute("src",imageList[startIndex]);
        startIndex++;
        if(startIndex == imageList.length){
           startIndex = 0;
        }
    }
    // start the loop
    setInterval(changeImage, 5000);
}

function verifyUserInput(){
    // get the form
    var form_login = document.getElementById("form_reset_pass");
    var front_end_error = document.getElementById("front_end_error");
    front_end_error.innerHTML="";
    // set the onsubmit
    form_login.onsubmit = function(){
        var user_id=document.getElementById("user_id");
        var newpassword=document.getElementById("new_password");
        var newpassword_re=document.getElementById("new_re_password");
        
        if(user_id.value == ""){
           front_end_error.innerHTML="用户ID不能为空。";
           return false;
        }
        if(newpassword.value == ""){
           front_end_error.innerHTML="新密码不能为空。";
           return false;
        }
        if(newpassword_re.value == ""){
           front_end_error.innerHTML="确认新密码不能为空。";
           return false;
        }
        if(newpassword.value != "" && newpassword_re.value != "" && newpassword.value != newpassword_re.value ){
           front_end_error.innerHTML="两次输入的新密码不一致，请重新确认。";
           return false;
        }
        if(newpassword.value != "" && newpassword_re.value != "" && newpassword.value == newpassword_re.value && newpassword.value == "123456"){
           front_end_error.innerHTML="新密码不能与初始化密码相同。";
           return false;
        }
    }
}

window.onload = function() {
    loopingTheImage();
    verifyUserInput();
};