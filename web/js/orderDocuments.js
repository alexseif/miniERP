var $images = $('img.docs-link');
var $imageClose = $('<button>Close</button>');
var $imageNext = $('<button><span class="glyphicon glyphicon-arrow-right"></span></button>');
var $imagePrev = $('<button><span class="glyphicon glyphicon-arrow-left"></span></button>');
//var $imageRotate = $('<button><span class="glyphicon glyphicon-arrow-up"></span></button>');
var $imageDiv = $('<div class="moving-box"></div>');

$images.each(function (index) {
    $(this).click(function (e) {
        e.preventDefault();
        $imageDiv.remove();

        $image = $('<img src="' + $(this).data('url') + '" />');
        $imageDiv.html($imagePrev);
        $imageDiv.append($imageClose);
        $imageDiv.append($imageNext);
//        $imageDiv.append($imageRotate);
        $imageDiv.append($image);
        $(this).after($imageDiv);
        $imageDiv.resizable({maxWidth: 800, minWidth: 80, aspectRatio: true});
        $imageDiv.draggable();

        $image.css({"height": "auto", "width": "100%"});
        $imageDiv.css({"zIndex": 10, "cursor": "move", "position": "absolute"});

        $imageClose.click(function (e) {
            e.preventDefault();
            $imageDiv.remove();
        });

        $nextImage = $images[(index + 1) % $images.length];
        $prevImage = $images[(index - 1 + $images.length) % $images.length];

        $imageNext.click(function (e) {
            e.preventDefault();
            $nextImage.click();
        });
        $imagePrev.click(function (e) {
            e.preventDefault();
            $prevImage.click();
        });

    });
});