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
        addPrototypeForm('tbody.companions', '<tr></tr>');
        $('.removeCompanion').on('click', function (e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
        $('.chosen-input').chosen({no_results_text: "Add new", allow_single_deselect: true});
    });

    $('.removeCompanion').on('click', function (e) {
        e.preventDefault();
        $(this).parent().parent().remove();
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