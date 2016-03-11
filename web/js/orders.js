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
    $('input[name="order_total"]').val(total);
    if ("paid" === $('input[name="mevisa_erpbundle_orders[orderPayments][0][state]"]:checked').val()) {
        if (total === parseInt($('input[name="mevisa_erpbundle_orders[orderPayments][0][amount]"]').val())) {
            $('#payment-panel').removeClass('panel-danger');
            $('#payment-panel').addClass('panel-default');
            $('#payment-panel .form-group:nth-child(3)').removeClass('has-error');
        } else {
            $('#payment-panel').removeClass('panel-default');
            $('#payment-panel').addClass('panel-danger');
            $('#payment-panel .form-group:nth-child(3)').addClass('has-error');
        }
    } else {
        $('input[name="mevisa_erpbundle_orders[orderPayments][0][amount]"]').val(total);
    }
}

function checkCompanions() {
    if ($('tbody.companions tr').length < $('input[name="mevisa_erpbundle_orders[people]"]').val()) {
        $('#companion-panel').addClass('panel-danger');
        $('#companion-panel').removeClass('panel-default');
    } else {
        $('#companion-panel').addClass('panel-default');
        $('#companion-panel').removeClass('panel-danger');
    }
}

function addProductForm() {
    addPrototypeForm('tbody.orderProducts', '<tr></tr>');
    $('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][product]"]').change(function () {
        updatePricesAndTotals();
    });
    $('input[name="mevisa_erpbundle_orders[orderProducts][' + index + '][quantity]"]').change(function () {
        updatePricesAndTotals();
    });
    $('.remove_product_link').show();
    $('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][product]"]').focus();
    $('.chosen-input').chosen({no_results_text: "Add new", allow_single_deselect: true});
}

var $productHolder;
var $addProductLink;
var $removeProductLink;
var $companionHolder;

$(document).ready(function () {


    $('input[name="mevisa_erpbundle_orders[adjustmentTotal]"]').change(function () {
        updatePricesAndTotals();
    });

    $addProductLink = $('.add_product_link');
    $removeProductLink = $('.remove_product_link');

    $productHolder = $('tbody.orderProducts');
    $productHolder.data('index', $('tbody.orderProducts tr').length);

    for (i = 0; i < $productHolder.data('index'); i++)
    {
        $('select[name="mevisa_erpbundle_orders[orderProducts][' + i + '][product]"]').change(function () {
            updatePricesAndTotals();
        });
        $('input[name="mevisa_erpbundle_orders[orderProducts][' + i + '][quantity]"]').change(function () {
            updatePricesAndTotals();
        });
    }

    $('div.orderPayments').data('index', 0);
    $('div.orderComments').data('index', $('.comments li').length);


    $addProductLink.on('click', function (e) {
        e.preventDefault();
        addProductForm();
    });

    $removeProductLink.on('click', function (e) {
        e.preventDefault();
        index = $('tbody.orderProducts').data('index');
        if (index > 1) {
            $('tbody.orderProducts tr:last').remove();
            $('tbody.orderProducts').data('index', index - 1);
        }
        updatePricesAndTotals();
    });

    if ($productHolder.data('index') <= 0) {
        $addProductLink.click();
    }

    //Order Companions
    $companionHolder = $('tbody.companions');
    $companionHolder.data('index', $('tbody.companions tr').length);

    $('.addCompanion').on('click', function (e) {
        e.preventDefault();
        addPrototypeForm('tbody.companions', '<tr></tr>');
        $('.removeCompanion').on('click', function (e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
        $('.chosen-input').chosen({no_results_text: "Add new", allow_single_deselect: true});
        checkCompanions();
    });

    $('input[name="mevisa_erpbundle_orders[people]"]').change(function () {
        var companionIndex = $companionHolder.data('index');
        $people = parseInt($('input[name="mevisa_erpbundle_orders[people]"]').val());
        $noCompanions = $people - companionIndex;
        if ($noCompanions > 0) {
            for (i = 0; i < $noCompanions; i++) {
                $('.addCompanion').click();
            }
            if ("" === $('input[name="mevisa_erpbundle_orders[orderCompanions][0][name]"]').val()) {
                $('input[name="mevisa_erpbundle_orders[orderCompanions][0][name]"]').val($('input[name="mevisa_erpbundle_orders[customer][name]"]').val());
            }
        }
        checkCompanions();
    });

    $('.removeCompanion').on('click', function (e) {
        e.preventDefault();
        $(this).parent().parent().remove();
        checkCompanions();
    });

    if ($('div.orderPayments').length) {
        addPrototypeForm('div.orderPayments', '<div></div>');
    }

    addPrototypeForm('div.orderComments', '<div></div>');

    $('#mevisa_erpbundle_orders_customer_name').focus();

    $('.align-inline .radio').addClass('radio-inline');

    //Customer Autocomplete
    $("#mevisa_erpbundle_orders_customer_name").autocomplete({
        minLength: 2,
        source: 'select_customer',
        select: function (event, ui) {
            $('#mevisa_erpbundle_orders_customer_email').val(ui.item[0].email);
            $('#mevisa_erpbundle_orders_customer_phone').val(ui.item[0].phone);
        }
    });

    $('.chosen-input').chosen({no_results_text: "Add new", allow_single_deselect: true});
});