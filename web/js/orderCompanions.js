var $companionHolder;

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
});