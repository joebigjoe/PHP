function resetheight(){
    // set the lopping image
    var itemContainer = document.getElementById("ad_options_list");
    var mainContianer = document.getElementById("content");
    var items = document.getElementsByClassName("single_option");
    var itemscount = items.length;
    var itemDividedByThree = Math.round(itemscount / 3);
    // image array
    if(itemDividedByThree > 2){
       itemContainer.height = (itemDividedByThree+1)*200+".px";
    }else{
       mainContianer.height= "500px";
    }
}

window.onload = function() {
    resetheight();
};