// SLIDER

var i = 1;
var interval;

var images = [
	{image:"../img/slider/1.png"},
	{image:"../img/slider/2.png"},
	{image:"../img/slider/3.png"},
	{image:"../img/slider/4.png"},
]

function appearImg(){
	i++;

	if(i > images.length - 1){
		i = 0;
	}

	$(".slider_content").css("background-image", "url(" + images[i].image + ")");
}

interval = setInterval(appearImg, 10000);

// COPIED IP

console.log("lol");
let ip_address_popup = $('#ip_address_popup');
let header_top_right = $('#ip_address');
ip_address_popup.hide();
header_top_right.click(function () {

	let range = document.createRange();
	let selection = window.getSelection();
	range.selectNode(this);
	selection.removeAllRanges();
	selection.addRange(range);

	try {
		var result = document.execCommand('copy');
		if(result){
			ip_address_popup.show();
			setTimeout(function(){ ip_address_popup.hide() }, 400);
		}
	}catch(err) {
		alert(err);
	}

	selection = window.getSelection();
	if(typeof selection.removeRange === 'function') {
		selection.removeRange(range);
	}else if(typeof  selection.removeAllRanges === 'function') {
		selection.removeAllRanges();
	}
})

// POPUP

let popup = $(".popup");

popup.mouseover(function(event){
	$target = event.target.alt;
	if($target == null) return;
	var popup = document.getElementById("PopUp_" + $target);
	popup.classList.toggle("show");
})

popup.mouseout(function(event){
	$target = event.target.alt;
	if($target == null) return;
	var popup = document.getElementById("PopUp_" + $target);
	popup.classList.toggle("show");
})


// mon_compte

let buttons = $('.btn-menu');
let contents = $('.content-menu');

function show_menu(id) {
	Array.prototype.forEach.call(contents, function(content){
		if(content.id === id) $('#'+content.id).show();
		else $('#'+content.id).hide();
	});

	Array.prototype.forEach.call(buttons, function(button){
		let new_id = id + "_menu";
		if(button.id === new_id) $('#'+button.id)[0].classList.add('cliqued');
		else $('#'+button.id)[0].classList.remove('cliqued');
	});
}

show_menu('infos');

buttons.click(function (){
	let id = this.id.replace("_menu", "");
	show_menu(id);
});

// COLLAPSE ANIM

var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
	coll[i].addEventListener("click", function() {
		this.classList.toggle("active");
		if(this.nextElementSibling != null) {
			var content = this.nextElementSibling;
			if (content.style.maxHeight){
				content.style.maxHeight = null;
			} else {
				content.style.maxHeight = content.scrollHeight + "px";
			}
		} else {
			var contents = document.getElementsByClassName("collapsible_content");
			for (let i = 0; i < contents.length; i++) {
				let content = contents[i];
				if (content.style.maxHeight) {
					content.style.maxHeight = null;
				} else {
					content.style.maxHeight = content.scrollHeight + 1 + "px";
				}
			}
		}

	});
}

window.addEventListener("scroll", function () {

	var header = document.querySelector("header");
	var main = document.querySelector("main");

	if (window.matchMedia("(min-width: 600px)").matches && window.matchMedia("(max-height: 1024px)").matches) {

		header.classList.toggle("sticky", window.scrollY > 0);
		main.classList.toggle("sticky", window.scrollY > 0);

		/* if(window.scrollY > 0 && !header.classList.contains('sticky')) {
			header.style.transition = 'all 0.5s';
			header.style.top = '-175px';
		}
		else header.style.top = '0';

		setTimeout(function(){

			header.classList.toggle("sticky", window.scrollY > 0);
			main.classList.toggle("sticky", window.scrollY > 0);
			if(header.classList.contains('sticky')) {
				header.style.transition = 'none';
				header.style.top = '0';
			}

		}, 450); */
	}else if(header.classList.contains("sticky") || main.classList.contains("sticky")) {
		header.classList.remove("sticky");
		main.classList.remove("sticky");
	}
})

// LOGIN POPUP

let login_popup = $('.login_popup');
login_popup.hide();

function slide(element_name,current_value, max_value, type, option){
	let element = $(element_name)[0];
	element.style.marginTop = max_value + 'px';
	if(option === 'show') $(element_name).show();
	if(type === 'TopToBot') {
		if(max_value<current_value) {
			max_value+=20;
			setTimeout('slide("' + element_name + '",' + current_value + ',' + max_value + ',"TopToBot", "'+option+'")', 10);
		}

	}else if(type === 'BotToTop') {
		if(max_value>current_value) {
			max_value-=20;
			setTimeout('slide("' + element_name + '",' + current_value + ',' + max_value + ',"BotToTop", "'+option+'")', 10);
		}
	}

	if(max_value===current_value && option === 'hide') $(element_name).hide();
}

function login_form_open(value) {

	if(value) {
		$('main')[0].style.filter = 'blur(0.2rem)';
		slide('.login_popup', -15, -550, "TopToBot", 'show');
	}else {
		$('main')[0].style.filter = 'none';
		slide('.login_popup', -550, parseInt(login_popup[0].style.marginTop.replace('px', '')), "BotToTop", 'hide');
	}

}

$('#header_connexion_img').click(function () {
	if(login_popup.is(":hidden")) login_form_open(true);
	else login_form_open(false);

})

$('.login_popup_close').click(function () {
	login_form_open(false);
})

// SEARCH SECTION

$('.search_account_content').click(function () {
	window.location.href = this.children[0].value;

})

//NOTIFS SECTION
let notifs_block = $('.notifs');
notifs_block.hide();

$('.notifs_close').click(function (){
	notifs_block.hide();
	$('html')[0].style.paddingBottom = defaultStatus;
})

$('.notif_button').click(function (){
	notifs_block.show();
	$('html')[0].style.paddingBottom = $('.notifs')[0].offsetHeight + "px";
})

let notifs_read_false = $('#notifs_read_false');
let notifs_read_true = $('#notifs_read_true');
let notifs_read_false_content = $('.notifs_read_false_content')
let notifs_read_true_content = $('.notifs_read_true_content')

function show_notifs_read(value = true) {
	if(value){
		notifs_read_false_content.hide();
		notifs_read_true_content.show();
	}else {
		notifs_read_false_content.show();
		notifs_read_true_content.hide();
	}
}

show_notifs_read(false);

notifs_read_false.click(function (){
	notifs_read_true[0].classList.remove('cliqued');
	notifs_read_false[0].classList.add('cliqued');
	show_notifs_read(false);
})

notifs_read_true.click(function (){
	notifs_read_false[0].classList.remove('cliqued');
	notifs_read_true[0].classList.add('cliqued');
	show_notifs_read();
})