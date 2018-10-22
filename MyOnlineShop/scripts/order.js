function resetheight(){
    // set the lopping image
    var itemContainer = document.getElementById("order_list_contianer");
    var mainContianer = document.getElementById("content");
    var items = document.getElementsByClassName("order_title");
    var itemscount = items.length;
    // image array
    if(itemscount > 2){
       mainContianer.height = (itemscount+1)*200+".px";
    }else{
       mainContianer.height= "600px";
    }
}

window.onload = function() {
    resetheight();
};