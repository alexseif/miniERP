function updateProductPrice(ele) {
  index = ele.attr('name').replace(/\D/g, '');
  $productId = $('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][product]"]').val();
  $productPrice = productPrices[$productId];
  $productCost = productCosts[$productId];
  $('input[name="mevisa_erpbundle_orders[orderProducts][' + index + '][unitPrice]"]').val($productPrice);
  $('input[name="mevisa_erpbundle_orders[orderProducts][' + index + '][unitCost]"]').val($productCost);
  if (10 == $productId) {
    $('input[name="mevisa_erpbundle_orders[orderProducts][' + index + '][unitPrice]"]').attr('readonly', false);
  }

}

function updatePricesAndTotals() {
  var index = $productHolder.data('index');
  var subtotal = 0;
  var total = 0;

  for (i = 0; i < index; i++) {
    $productPrice = parseInt($('input[name="mevisa_erpbundle_orders[orderProducts][' + i + '][unitPrice]"]').val());
    $qty = parseInt($('input[name="mevisa_erpbundle_orders[orderProducts][' + i + '][quantity]"]').val());
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
function agentPrices() {
  if ($agent) {
    $('#customer_agent').text('Agent');
  } else {
    $('#customer_agent').text('');
  }
  var index = $productHolder.data('index');
  for (i = 0; i < index; i++) {
    $('input[name="mevisa_erpbundle_orders[orderProducts][' + i + '][unitPrice]"]').attr('readonly', !($agent || isAccountant));
  }
}

function addProductForm() {
  addPrototypeForm('tbody.orderProducts', '<tr></tr>');
  $('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][product]"]').change(function () {
    updateProductPrice($(this));
    updateVendors($(this));
    updatePricesAndTotals();
  });
  $('input[name="mevisa_erpbundle_orders[orderProducts][' + index + '][quantity]"]').change(function () {
    updatePricesAndTotals();
  });
  $('input[name="mevisa_erpbundle_orders[orderProducts][' + index + '][unitPrice]"]').change(function () {
    updatePricesAndTotals();
  });
  $('.remove_product_link').show();
  $('input[name="mevisa_erpbundle_orders[orderProducts][' + index + '][unitPrice]"]').attr('readonly', !($agent || isAccountant));
  $('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][product]"]').focus();
  $('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][vendor]"] option:not(:first)').hide();
  $('select').chosen();
}

function addCompanions() {
  if (checkCompanions(true)) {
    addPrototypeForm('tbody.companions', '<tr></tr>');
    $('.removeCompanion').on('click', function (e) {
      e.preventDefault();
      $(this).parent().parent().remove();
      checkCompanions();
    });
    $('.chosen-input').chosen({no_results_text: "Add new", allow_single_deselect: true});
    checkCompanions();
  }
}

function validateCompanions() {
  companionsCount = $('tbody.companions tr').length;
  if (0 == companionsCount) {
    $('#companion-panel').before('<div class="alert alert-dismissible alert-error"><button type="button" class="close" data-dismiss="alert">×</button>You have to add a companion</div>');
    $('html, body').animate({
      scrollTop: $('.alert.alert-dismissible.alert-error').offset().top - 100 + 'px'
    }, 'fast');
    return false;
  }
  return true;
}

function checkCompanions(alrt) {
  companionsCount = $('tbody.companions tr').length;
  if (companionsCount < PAX) {
    $('#companion-panel').addClass('panel-danger');
    $('#companion-panel').removeClass('panel-default');
    return true;
  } else {
    if (alrt) {
      $('#companion-panel').before('<div class="alert alert-dismissible alert-error"><button type="button" class="close" data-dismiss="alert">×</button>You cannot add more companions</div>');
    }
    $('#companion-panel').addClass('panel-default');
    $('#companion-panel').removeClass('panel-danger');
    return false;
  }
}

function updateVendors(ele) {
  index = ele.attr('name').replace(/\D/g, '');
  $('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][vendor]"] option:not(:first)').hide();
  var current = vendors[$('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][product]"]').val()];
  if (current) {
    for (var y in current) {
      $('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][vendor]"] option[value=' + y + ']').show();
    }
  } else {
    $('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][vendor]"] option').show();
  }
  $('select[name="mevisa_erpbundle_orders[orderProducts][' + index + '][vendor]"]').trigger("chosen:updated");
}

var $productHolder;
var $addProductLink;
var $removeProductLink;
var $companionHolder;
var $agent = false;

$(document).ready(function () {

  $('input[name="mevisa_erpbundle_orders[adjustmentTotal]"]').change(function () {
    updatePricesAndTotals();
  });

  $addProductLink = $('.add_product_link');
  $removeProductLink = $('.remove_product_link');

  $productHolder = $('tbody.orderProducts');
  $productHolder.data('index', $('tbody.orderProducts tr').length);

  for (i = 0; i < $productHolder.data('index'); i++) {
    $productId = $('select[name="mevisa_erpbundle_orders[orderProducts][' + i + '][product]"]').val();
    if (10 == $productId) {
      $('input[name="mevisa_erpbundle_orders[orderProducts][' + i + '][unitPrice]"]').attr('readonly', false);
    }
    $('select[name="mevisa_erpbundle_orders[orderProducts][' + i + '][product]"]').change(function () {
      updateProductPrice($(this));
      updateVendors($(this));
      updatePricesAndTotals();
    });
    $('input[name="mevisa_erpbundle_orders[orderProducts][' + i + '][quantity]"]').change(function () {
      updatePricesAndTotals();
    });
    $('input[name="mevisa_erpbundle_orders[orderProducts][' + i + '][unitPrice]"]').change(function () {
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
    addCompanions();
  });
  $('.removeCompanion').on('click', function (e) {
    e.preventDefault();
    $(this).parent().parent().remove();
    checkCompanions();
  });

  $('input[name="mevisa_erpbundle_orders[people]"]').change(function () {
    PAX = $(this).val();
    companionsRem = PAX - $('tbody.companions tr').length;
    for (i = 0; i < companionsRem; i++) {
      $('.addCompanion').click();
    }
    if ("" === $('input[name="mevisa_erpbundle_orders[orderCompanions][0][name]"]').val()) {
      $('input[name="mevisa_erpbundle_orders[orderCompanions][0][name]"]').val($('input[name="mevisa_erpbundle_orders[customer][name]"]').val());
    }
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
    source: selectCustomer,
    select: function (event, ui) {
      $('#mevisa_erpbundle_orders_customer_email').val(ui.item[0].email);
      $('#mevisa_erpbundle_orders_customer_phone').val(ui.item[0].phone);
      $agent = ui.item[0].agent;
      agentPrices();
    }
  });

  $('.datepicker').datepicker({dateFormat: "dd.mm.yy"});

  $('form[name="mevisa_erpbundle_orders"]').submit(function () {
    return validateCompanions();
  });

});