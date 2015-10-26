$('.chosen-input').chosen({no_results_text: "Add new", allow_single_deselect: true});


function updatePricesAndTotals() {
    var index = $productHolder.data('index');
    var subtotal = 0;
    var total = 0;

    for (i = 0; i < index; i++) {
        $productPrice = productPrices[$('select[name="mevisa_erpbundle_orders[orderProducts][' + i + '][product]"]').val()];
        $qty = $('input[name="mevisa_erpbundle_orders[orderProducts][' + i + '][quantity]"]').val();
        $('input[name="mevisa_erpbundle_orders[orderProducts][' + i + '][unitPrice]"]').val($productPrice);
        $total = $productPrice * $qty;
        $('input[name="mevisa_erpbundle_orders[orderProducts][' + i + '][total]"]').val($total);
        subtotal += $total;
    }
    $('input[name="mevisa_erpbundle_orders[productsTotal]"]').val(subtotal);

    total = subtotal + parseInt($('input[name="mevisa_erpbundle_orders[adjustmentTotal]"]').val());
    $('input[name="mevisa_erpbundle_orders[total]"]').val(total);
}

function addProductForm($productHolder, $newProductLinkLi) {
    var prototype = $productHolder.data('prototype');
    var index = $productHolder.data('index');
    var newForm = prototype.replace(/__name__/g, index);
    $productHolder.data('index', index + 1);
    var $newFormLi = $('<li></li>').append(newForm);
    $newProductLinkLi.before($newFormLi);

    $('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][product]"]').change(function () {
        updatePricesAndTotals();
    });
    $('input[name="mevisa_erpbundle_orders[orderProducts][' + index + '][quantity]"]').change(function () {
        updatePricesAndTotals();
    });

    if (index >= 1) {
        $qty = $('input[name="mevisa_erpbundle_orders[orderProducts][0][quantity]"]').val();
        $('input[name="mevisa_erpbundle_orders[orderProducts][' + index + '][quantity]"]').val($qty);
    }

    $('input[name="mevisa_erpbundle_orders[orderProducts][0][quantity]"]').change(function () {
        var companionIndex = $companionHolder.data('index');
        if (companionIndex <= 0) {
            $noComanions = $('input[name="mevisa_erpbundle_orders[orderProducts][0][quantity]"]').val();
            for (i = 0; i < $noComanions; i++) {
                $('.addCompanion').click();
                $('input[name="mevisa_erpbundle_orders[orderCompanions][0][name]"]').val($('input[name="mevisa_erpbundle_orders[customer][name]"]').val());
            }
        }
    });

    $('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][product]"]').focus();
}


function addCompanionForm($companionHolder) {
    var prototype = $companionHolder.data('prototype');
    var index = $companionHolder.data('index');
    var newForm = prototype.replace(/__name__/g, index);
    $companionHolder.data('index', index + 1);
    var $newFormTr = $('<tr></tr>').append(newForm);
    $companionHolder.append($newFormTr);
}

var $productHolder;

var $addProductLink = $('<a href="#" class="btn btn-default btn-block add_product_link" style="margin-top:5px;"><span class="glyphicon glyphicon-plus" title="Add another product"></span></a>');
var $newProductLinkLi = $('<li></li>').append($addProductLink);

var $companionHolder;

$(document).ready(function () {
    $('input[name="mevisa_erpbundle_orders[adjustmentTotal]"]').change(function () {
        updatePricesAndTotals();
    });

    $productHolder = $('ul.orderProducts');
    $productHolder.append($newProductLinkLi);

    $productHolder.data('index', $productHolder.find(':input').length);

    $addProductLink.on('click', function (e) {
        e.preventDefault();
        addProductForm($productHolder, $newProductLinkLi);
    });

    $addProductLink.click();

    $companionHolder = $('tbody.companions');
    $companionHolder.data('index', $companionHolder.find(':input').length);
    $('.addCompanion').on('click', function (e) {
        e.preventDefault();
        addCompanionForm($companionHolder);
    });


});
