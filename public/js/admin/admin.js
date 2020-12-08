// burger

var burger_on = $(".fa-times");
var burger_off = $(".fa-bars");

burger_on.hide();

burger_off.click(function(){
	burger_on.show();
	burger_off.hide();
    $(".head_nav").show();
    $("main").addClass("menuOpen");
})

burger_on.click(function(){
	burger_on.hide();
	burger_off.show();
    $(".head_nav").hide();
    $("main").removeClass("menuOpen");
})

$(".head_nav").hide();