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

function getFamilyNumber(){
    var ids = document.getElementsByClassName("extrainfo_idcard");
    var membercount = ids.length;
    return membercount;
}

function responseToOrderClick(){
    // set the lopping image
    var orderButton = document.getElementById("order_button");
    var frontEndError = document.getElementById("front_end_error");
    var cartTotal = document.getElementsByClassName("shoppingcart_item");
    var ifcanmultipleinsert = document.getElementById("if_multipleinsert_front");
    var singleamount = document.getElementsByClassName("singleamout");
    var singlemorethanone = document.getElementById("if_singleitemmorethanone_front");
    var ifduplicatedcampaign = document.getElementById("current_campaign_amount_forduplication_front");
    var iforderalreadyexists = document.getElementById("if_orderalreadyexists_front");
    var ifitemoverdue = document.getElementById("if_item_overdue_front");
    var extrainfomax = document.getElementById("extrainfomax_front");
    var ifextrainfo = document.getElementById("if_extrainfo_front");
    
    //var extrainfo = document.getElementById("textarea_extrainfo");
    orderButton.onclick = function() {
        // this is to verify the basic info
        frontEndError.innerHTML = "";

        if(cartTotal.length == 0){
            frontEndError.innerHTML = "购买的商品为空，无法下单。";
            return false;
        }

        if(iforderalreadyexists.value >= 1){
            frontEndError.innerHTML = "此次活动您已经有一个订单，无法再次下单。 如需重新下单，请删除已存在订单后，再次下单。";
            return false;
        }

        if(ifduplicatedcampaign.value > 1){
            frontEndError.innerHTML = "您的购物车中存在两次活动的商品。 每次下单只允许存在一次活动的商品，请分开下单。";
            return false;
        }

        if(ifitemoverdue.value == 1){
            frontEndError.innerHTML = "您的购物车中存在已经过期的商品，即某次活动已经结束，但是您并未从购物车内删除该商品，请删除过期商品后再下单。";
            return false;
        }

        if(ifcanmultipleinsert.value == 0){
            if(cartTotal.length > 1){
                frontEndError.innerHTML = "此类商品只允许选择1种。 请确认您的选择种类。";
                return false;
            }else{
                //if(singleamount[0].value > 1 && extrainfo.value == ""){
                if(singlemorethanone.value == 0){
                    if(singleamount[0].value > 1){
                        frontEndError.innerHTML = "此类商品只允许选择 1 个。 请确认您的选择数量。";
                        return false;
                    }
                }  
            } 
        }else{
            if(singlemorethanone.value == 0){
                if(!checkIfAllJustOne()){
                    frontEndError.innerHTML = "此类商品只允许每类选择一种，每种选择 1 个。 请确认您的选择种类和数量。";
                    return false;
                }
            }
        }

        // this is to verify the extra info
        // max

        // only do it when needed.
        if (ifextrainfo.value == 1){

            var famnumber_db = extrainfomax.value - 1;
            if(getFamilyNumber() > famnumber_db){
                frontEndError.innerHTML = "此次活动只允许带 " + famnumber_db + " 个家属。 一个大人，两个儿童。 加上员工自己，总共 " + extrainfomax.value + " 个成员.";
                return false;
            }

            // details
            var extrainfo_sum = "";
            try{
                var names = document.getElementsByClassName("extrainfo_name");
                for (var i = 0; i < names.length; i++){
                    if(names[i].value == ""){
                        frontEndError.innerHTML = "家属姓名不能为空。";
                            return false;
                    }
                }
                var ids = document.getElementsByClassName("extrainfo_idcard");
                for (var i = 0; i < ids.length; i++){
                    if(ids[i].value == ""){
                        frontEndError.innerHTML = "身份证不能为空。";
                            return false;
                    }
                    if(!checkID(ids[i].value)){
                        frontEndError.innerHTML = "身份证格式不正确。";
                            return false;
                    }
                }

                for (var i=0; i< names.length; i++){
                    if(i != names.length-1){
                        extrainfo_sum += names[i].value + " : " + ids[i].value + "<br>";
                    }else{
                        extrainfo_sum += names[i].value + " : " + ids[i].value;
                    }
                }
                  
            }catch(err){
                frontEndError.innerHTML = err.message
                return false;
            }
            
            // set the sum value so we can pass and save to db
            var extrainfo_sum_html = document.getElementById("extrainfo_content_summary");
            extrainfo_sum_html.value = extrainfo_sum;
            }    
    };
}

function checkIfAllJustOne(){
    var singleamount = document.getElementsByClassName("singleamout");
    for (i = 0; i < singleamount.length; i++) { 
        if (singleamount[i].value > 1)
            return false;
    }
    return true;
}

window.onload = function() {
    responseToOrderClick();
};