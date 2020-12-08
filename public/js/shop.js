// OWNER CHOOSE SHOP

$('.purchase_form_choose').click(function () {

    let checkboxs = $(".purchase_form_choose");
    Array.prototype.forEach.call(checkboxs, function (element) {
        element.children[0].checked = false
    })

    this.children[0].checked = true;
})

// OFFER CHOOSE SHOP

function get_offer(value) {
    return {
        dons: value,
        bonus: Math.floor(value * (0.08 + ((Math.floor(value/100)/20) - (Math.floor(value/1000))/5.56))),
        price: Math.round(((value / 10) - 0.01) * 100) / 100,
    };
}

function get_offer_with_card(card) {

    let element_childrens = card.children[0].children;
    let offer_card_title = element_childrens[0];

    return get_offer(parseInt(offer_card_title.innerText.replace(' dons', '')));
}

function set_card_values(card, value = null) {

    let element_childrens = card.children[0].children;

    let offer_card_title = null;
    let offer_card_bonus = null;
    let offer_card_price = null;


    Array.prototype.forEach.call(element_childrens, function (children) {

        if(children.className === 'offer_card_title') offer_card_title = children;
        else if(children.className === 'offer_card_bonus') offer_card_bonus = children;
        else if(children.className === 'offer_card_price') offer_card_price = children;

    })

    let dons_value = parseInt(offer_card_title.innerText.replace(' dons', ''));

    if(value != null) {
        offer_card_title.innerText = value + " dons";
        dons_value = value;
    }

    let offer = get_offer(dons_value);

    offer_card_bonus.innerText = '+ ' + offer.bonus + ' en bonus';
    offer_card_price.innerText = offer.price + '€';
}

Array.prototype.forEach.call($('.offer_card'), function (element) {
    set_card_values(element);
})

// PERSONAL OFFER

$('.purchase_offer_form_value').on('input', function () {
    let value = $(this).val();

    if (this.value.length > 5) {
        this.value = this.value.slice(0,4);
    }

    if(value < 10 ) value = 10;

    let card = $('#personal_offer')[0];

    set_card_values(card, value);
    $('.purchase_form_recap')[0].children[0].textContent = 'Aucune';
    card.classList.remove("cliqued");
})

$('.offer_card').click(function () {

    let offer = get_offer_with_card(this)
    Array.prototype.forEach.call($('.offer_card'), function (element) {
        if(element.classList.contains("cliqued")) element.classList.remove("cliqued");
    })

    this.classList.add("cliqued");

    $('.purchase_form_recap')[0].children[0].textContent = offer.dons + '(+' + offer.bonus + ') Dons pour ' + offer.price + ' €';
    $('.purchase_form_recap')[0].children[1].value = offer.dons;

})
