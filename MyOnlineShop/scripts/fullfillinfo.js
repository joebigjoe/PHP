function checkID(ID) {
    if(typeof ID !== 'string') return false;
    var city = {11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江 ",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北 ",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏 ",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外"};
    var birthday = ID.substr(6, 4) + '/' + Number(ID.substr(10, 2)) + '/' + Number(ID.substr(12, 2));
    var d = new Date(birthday);
    var newBirthday = d.getFullYear() + '/' + Number(d.getMonth() + 1) + '/' + Number(d.getDate());
    var currentTime = new Date().getTime();
    var time = d.getTime();
    var arrInt = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
    var arrCh = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
    var sum = 0, i, residue;
  
    if(!/^\d{17}(\d|x)$/i.test(ID)) return false;
    if(city[ID.substr(0,2)] === undefined) return false;
    if(time >= currentTime || birthday !== newBirthday) return false;
    for(i=0; i<17; i++) {
      sum += ID.substr(i, 1) * arrInt[i];
    }
    residue = arrCh[sum % 11];
    if (residue !== ID.substr(17, 1)) return false;
  
    return true;
}

function checkMobile(str) {
    var re = /^1\d{10}$/;
    if(re.test(str)){
        return true;
    }else{
        return false;
    }
}

function checkEmail(str){ 
    p = /^([\w\.-]+)@([a-zA-Z0-9-]+)(\.[a-zA-Z\.]+)$/; 
    if(str.search(p) == -1){ 
        return false; 
    }else{ 
        return true; 
    } 
} 

function checkPassport(number){
    var str=number;
    var Expression=/^1[45][0-9]{7}|([P|p|S|s]\d{7})|([S|s|G|g]\d{8})|([Gg|Tt|Ss|Ll|Qq|Dd|Aa|Ff]\d{8})|([H|h|M|m]\d{8,10})$/;
    var objExp=new RegExp(Expression);
    if(objExp.test(str)==true){
       return true;
    }else{
       return false;
    } 
}

function verifyUserInput(){
    // get the form
    var form_login = document.getElementById("form_fullfill_info");
    var front_end_error = document.getElementById("front_end_error");
    front_end_error.innerHTML="";
    // set the onsubmit
    form_login.onsubmit = function(){
        var aa_name=document.getElementById("admininfo");
        var cn_name=document.getElementById("name_chinese");
        var id_number=document.getElementById("id_card");
        var passport=document.getElementById("passport");
        var cell_number=document.getElementById("mobile_phone");
        var email=document.getElementById("email");
        var aa_email=document.getElementById("admininfo_email");
        
        if(cn_name.value == ""){
           front_end_error.innerHTML="中文名不能为空";
           return false;
        }

        if(aa_name.value == ""){
           front_end_error.innerHTML="AA姓名不能为空，请与您的Manager核实";
           return false;
        }

        if(id_number.value != ""){
           if(!checkID(id_number.value.trim())){
             front_end_error.innerHTML=id_number.value + "  身份证号码格式不正确";
             return false;
          }
        }
        if(cell_number.value != ""){
           if(!checkMobile(cell_number.value.trim())){
             front_end_error.innerHTML="手机号码格式不正确";
             return false;
          }
        }
        if(email.value != ""){
           if(!checkEmail(email.value.trim())){
             front_end_error.innerHTML="员工邮件格式不正确";
             return false;
          }
        }
        if(aa_email.value != ""){
           if(!checkEmail(aa_email.value.trim())){
             front_end_error.innerHTML="AA邮件格式不正确";
             return false;
          }
        }
    }
}

window.onload = function() {
    verifyUserInput();
};