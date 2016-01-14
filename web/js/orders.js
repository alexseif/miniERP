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

function addProductForm() {
    addPrototypeForm('ul.orderProducts', '<li></li>');
//    var prototype = $productHolder.data('prototype');
//    var index = $productHolder.data('index');
//    var newForm = prototype.replace(/__name__/g, index);
//    $productHolder.data('index', index + 1);
//    var $newFormLi = $('<li></li>').append(newForm);
//    $newProductLinkLi.before($newFormLi);

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
            $noCompanions = $('input[name="mevisa_erpbundle_orders[orderProducts][0][quantity]"]').val();
            for (i = 0; i < $noCompanions; i++) {
                $('.addCompanion').click();
                $('input[name="mevisa_erpbundle_orders[orderCompanions][0][name]"]').val($('input[name="mevisa_erpbundle_orders[customer][name]"]').val());
            }
        }
    });
    $('.remove_product_link').show();

    $('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][product]"]').focus();

}

var $productHolder;

//var $addProductLink = $('<a href="#" class="btn btn-primary  add_product_link pull-left"><span class="glyphicon glyphicon-plus-sign" title="Add another product"></span></a>');
var $addProductLink = $('.add_product_link');
//var $removeProductLink = $('<a href="#" class="btn btn-danger btn-sm remove_product_link pull-right" tabindex="-100"><span class="glyphicon glyphicon glyphicon-trash" title="Remove this product"></span></a>');
var $removeProductLink = $('.remove_product_link');
//var $newProductLinkLi = $('<li></li>').append($addProductLink);

var $companionHolder;

$(document).ready(function () {
    $('input[name="mevisa_erpbundle_orders[adjustmentTotal]"]').change(function () {
        updatePricesAndTotals();
    });

    $productHolder = $('ul.orderProducts');
//    $productHolder.after($addProductLink, $removeProductLink);

    $productHolder.data('index', 0);

    $addProductLink.on('click', function (e) {
        e.preventDefault();
        addProductForm();
    });

    $removeProductLink.on('click', function (e) {
        e.preventDefault();
        index = $('ul.orderProducts').data('index');
        if (index > 1) {
            $('ul.orderProducts li:last').remove();
            $('ul.orderProducts').data('index', index - 1);
            updatePricesAndTotals();
        } else {
            $(this).hide();
        }
    });

    $addProductLink.click();

    $companionHolder = $('tbody.companions');

    $companionHolder.data('index', 0);
    $('.addCompanion').on('click', function (e) {
        e.preventDefault();
        addPrototypeForm('tbody.companions', '<tr></tr>');
        $('.removeCompanion').on('click', function (e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
    });

    $('div.orderPayments').data('index', 0);
    $('div.orderComments').data('index', 0);
//    $('div.orderDocuments').data('index', 0);
    addPrototypeForm('div.orderPayments', '<div></div>');
    addPrototypeForm('div.orderComments', '<div></div>');
//    addPrototypeForm('div.orderDocuments', '<div></div>');

    $('#mevisa_erpbundle_orders_customer_name').focus();

    $('.align-inline .radio').addClass('radio-inline');
});
