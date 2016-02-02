/*
 * TODO: Add support for tabless
 */
function addPrototypeForm(selector, wrapper, placement) {
    placement = placement || 'append';
    componentHolder = $(selector);
    if (componentHolder.length) {

//    componentHolder.data('index', componentHolder.find(':input').length);

        prototype = componentHolder.data('prototype');
        if (prototype) {
            index = componentHolder.data('index');

            newForm = prototype.replace(/__name__/g, index);
            componentHolder.data('index', index + 1);

            newWrapper = $(wrapper);
            newWrapper.data('step', index);

            newFormWrapper = newWrapper.append(newForm);

            switch (placement) {
                case 'after':
                    componentHolder.after(newFormWrapper);

                    break;
                case 'before':
                    componentHolder.before(newFormWrapper);
                    break;
                case 'append':
                default:
                    componentHolder.append(newFormWrapper);
                    break;
            }
        }
    } else {
        console.log('selector: ' + selector + ' not found');
    }
}