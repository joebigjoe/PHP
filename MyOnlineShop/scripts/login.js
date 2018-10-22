function loopingTheImage(){
    // set the lopping image
    var imageContainer = document.getElementById("looping_image");    
    // image array
    var imageList = ["pictures/loop/images_0.jpg", "pictures/loop/images_1.jpg", "pictures/loop/images_2.jpg","pictures/loop/images_3.jpg","pictures/loop/images_4.jpg","pictures/loop/images_5.jpg"];
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
    var form_login = document.getElementById("login_form");
    var front_end_error = document.getElementById("front_end_error");
    front_end_error.innerHTML="";
    // set the onsubmit
    form_login.onsubmit = function(){
        var userid=document.getElementById("user_id");
        var password=document.getElementById("password");
        if(userid.value == ""){
           front_end_error.innerHTML="员工号不能为空";
            return false;
        }
        if(password.value == ""){
           front_end_error.innerHTML="密码不能为空";
           return false;
        }
    }
}

window.onload = function() {
    loopingTheImage();
    verifyUserInput();
};