$('.chosen-input').chosen({no_results_text: "Add new", allow_single_deselect: true});
$(document).ready(function () {
    $('.product_price_id option[value!=""]').hide();
    $('.product_id').on('change', function (e) {
        productChanged(e.target.value);
    });
    $companionHolder = $('tbody.companions');
    $companionHolder.data('index', $companionHolder.find(':input').lenght);
    $('.addCompanion').on('click', function (e) {
        e.preventDefault();
        addCompanionForm($companionHolder);
    });
});


function addCompanionForm($companionHolder) {
    var prototype = $companionHolder.data('prototype');
    var index = $companionHolder.data('index');
    var newForm = prototype.replace(/__name__/g, index);
    $companionHolder.data('index', index + 1);
    var $newFormTr = $('<tr></tr>').append(newForm);
    $companionHolder.append($newFormTr);
}


function productChanged(productId) {

    // deselect state
    $('.product_price_id').val('');
    // hide all prices
    $('.product_price_id option[value!=""]').hide();
    showPrices(products[productId]);
    return;
}

function showPrices(prices) {
    for (var i = 0; i < prices.length; i++) {
        $('.product_price_id option[value="' + prices[i].id + '"]').show();
    }
}