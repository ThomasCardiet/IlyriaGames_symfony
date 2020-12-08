var sidebarBody = document.querySelector('#hamburger-sidebar-body');
var button = document.querySelector('#hamburger-button');
var overlay = document.querySelector('#hamburger-overlay');
var activatedClass = 'hamburger-activated';
var header_bot = document.querySelector('.header_bot');

Array.prototype.forEach.call($('.header_links'), function (element) {
    sidebarBody.innerHTML += element.innerHTML;
})

button.addEventListener('click', function(e) {
    e.preventDefault();
    header_bot.classList.add(activatedClass);
    this.parentNode.classList.add(activatedClass);
});

button.addEventListener('keydown', function (e) {
    if(this.parentNode.classList.contains(activatedClass)) {
        if(e.repeat === false && e.which === 27) {
            header_bot.classList.remove(activatedClass);
            this.parentNode.classList.remove(activatedClass);
        }
    }
});

overlay.addEventListener('click', function (e) {
   e.preventDefault();
   header_bot.classList.remove(activatedClass);
   this.parentNode.classList.remove(activatedClass);
});

// RESPONSIVE

function resize() {

    // REMOVE LOGIN POPPUP MOBILE ROTATE
    var ipad_match = window.matchMedia("(max-width: 768px)").matches;
    if(ipad_match) {
        $('main')[0].style.filter = 'none';
        $('.login_popup').hide();
    }

    var mobile_match = window.matchMedia("(max-width: 414px)").matches;

    // INDEX RESPONSIVE
    var news_right = document.querySelector('.news_right');
    var news_left_title = document.querySelector('.news_left_title');
    var news_left = document.querySelector('.news_left');

    if (news_right != null && news_left_title != null)
        if(mobile_match) news_left_title.before(news_right);
        else news_left.after(news_right);

    // ACCOUNT RESPONSIVE
    var main_header_button = document.querySelector('.main_header_button');
    var main_header = document.querySelector('.main_header');

    if (main_header_button != null && main_header != null)
        if(mobile_match) main_header.after(main_header_button);
        else main_header.appendChild(main_header_button);

    var main_header_sign_up = document.querySelector('.main_header_sign_up');
    var main_header_info_text = document.querySelector('.main_header_info_text')

    if (main_header_sign_up != null && main_header != null)
        if(mobile_match) main_header.appendChild(main_header_sign_up);
        else main_header_info_text.appendChild(main_header_sign_up);

    // ACCOUNT SEARCH RESPONSIVE
    var account_search_form_btn = document.querySelector('.account_search_form_btn');
    var account_search_form_link = document.querySelector('.account_search_form_link');
    var account_search_form = document.querySelector('.account_search_form');
    var account_search_form_responsive = document.querySelector('.account_search_form_responsive');

    if (account_search_form_btn != null && account_search_form_link != null && account_search_form != null) {

        if(mobile_match) {
            account_search_form_responsive.appendChild(account_search_form_link);
            account_search_form_responsive.appendChild(account_search_form_btn);
        }else {
            account_search_form.appendChild(account_search_form_btn);
            account_search_form.appendChild(account_search_form_link);
        }

    }

    // VOTE RESPONSIVE
    var profile_header_text_account = document.querySelector('.profile_header_text_account');
    var profile_header_info_text = document.querySelector('.profile_header_info_text');
    var profile_header = document.querySelector('.profile_header');

    if (profile_header_text_account != null && profile_header_info_text != null) {

        if(mobile_match) {
            profile_header_info_text.appendChild(profile_header_text_account);
        }else {
            profile_header.appendChild(profile_header_text_account);
        }

    }

    // LOGIN RESPONSIVE
    var content_login_left = document.querySelector('.content_login_left');
    var content_login_right = document.querySelector('.content_login_right');
    var content_login = document.querySelector('.content_login');

    if (content_login_left != null && content_login_right != null && content_login != null) {

        if(mobile_match) {
            content_login.appendChild(content_login_right);
            content_login.appendChild(content_login_left);
        }else {
            content_login.appendChild(content_login_left);
            content_login.appendChild(content_login_right);
        }

    }

}
resize();
window.addEventListener('resize', resize, false);