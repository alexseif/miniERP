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

var $companionHolder;
var $image = $('<img />');
var $imageClose = $('<button>Close</button>');
var $imageDiv = $('<div style="display:inline-block"></div>');

$(document).ready(function () {
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

    $('.align-inline .radio').addClass('radio-inline');
    $('.chosen-input').chosen({no_results_text: "Add new", allow_single_deselect: true});

    $('.docs-link').click(function () {
        $image.remove();
        $imageClose.remove();
        $imageDiv.remove();
        $image = $('<img src="' + $(this).data('link') + '" />');
        $imageDiv.append($imageClose);
        $imageDiv.append($image);
        $(this).after($imageDiv);
        $image.resizable();
        $imageDiv.draggable({cursor: "move", zIndex: 10});
        $imageDiv.css("zIndex", 1);
        $imageDiv.css("cursor", "move");
        $imageDiv.css('position', 'absolute');
        $('#companion-panel').css("zIndex", ($imageDiv.zIndex() + 1));
        $imageClose.click(function () {
            $image.remove();
            $imageDiv.remove();
        });
    });
});