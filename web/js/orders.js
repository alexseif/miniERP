$('.chosen-input').chosen({no_results_text: "Add new", allow_single_deselect: true});

var $productHolder;

var $addProductLink = $('<a href="#" class="btn btn-default btn-block add_product_link" style="margin-top:5px;"><span class="glyphicon glyphicon-plus" title="Add another product"></span></a>');
var $newProductLinkLi = $('<li></li>').append($addProductLink);

var $companionHolder;

$(document).ready(function () {
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

function addProductForm($productHolder, $newProductLinkLi) {
    var prototype = $productHolder.data('prototype');
    var index = $productHolder.data('index');
    var newForm = prototype.replace(/__name__/g, index);
    $productHolder.data('index', index + 1);
    var $newFormLi = $('<li></li>').append(newForm);
    $newProductLinkLi.before($newFormLi);

    if (index >= 1) {
        $qty = $('input[name="mevisa_erpbundle_orders[orderProducts][0][quantity]"]').val();
        $('input[name="mevisa_erpbundle_orders[orderProducts][' + index + '][quantity]"]').val($qty);
    }

    $('input[name="mevisa_erpbundle_orders[orderProducts][0][quantity]"]').change(function (e) {
        var companionIndex = $companionHolder.data('index');
        if (companionIndex <= 0) {
            $noComanions = $('input[name="mevisa_erpbundle_orders[orderProducts][0][quantity]"]').val();
            for (i = 0; i < $noComanions; i++) {
                $('.addCompanion').click();
                $('input[name="mevisa_erpbundle_orders[orderCompanions][0][name]"]').val($('input[name="mevisa_erpbundle_orders[customer][name]"]').val());
            }
        }
    });
}


function addCompanionForm($companionHolder) {
    var prototype = $companionHolder.data('prototype');
    var index = $companionHolder.data('index');
    var newForm = prototype.replace(/__name__/g, index);
    $companionHolder.data('index', index + 1);
    var $newFormTr = $('<tr></tr>').append(newForm);
    $companionHolder.append($newFormTr);
}

