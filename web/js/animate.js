(function($){
    animate = function(contains, containsNot) {

        contains.show();
        var width = $('.champion-list .item-champion').width();
        var height = $('.champion-list .item-champion').height();
        var championListSize = $('.champion-list').width();
        var leftCoord = 0;
        var topCoord = -1;
        var coord = [];
        var paddingTop = parseInt($(".champion-list").css("padding-top"), 10);
        var paddingLeft = parseInt($(".champion-list").css("padding-left"), 10);;
        var marginTop = parseInt($(".champion-list").css("margin-top"), 10);
        var marginLeft = parseInt($(".champion-list").css("margin-left"), 10);;


        totalArticle = contains.length;

        numberArticlePerColumn = (championListSize - paddingLeft) / width;

        numberArticlePerColumn = Math.floor(numberArticlePerColumn);


        if (numberArticlePerColumn < 1)
            numberArticlePerColumn = 1;

        contains.each(function(index) {

            if(index % numberArticlePerColumn == 0) {
                leftCoord = 0;
                topCoord ++;
            }

            coord['nextLeft'] =  (width + paddingLeft + marginLeft) * leftCoord;
            coord['nextTop'] =  (height + paddingTop + marginTop) * topCoord;
            $(this).show();
            $(this).animate({ "left": coord['nextLeft'] + "px" , "top": coord['nextTop'] + "px"  }, 1000 );
            leftCoord ++;
        });

        containsNot.hide();
        containsNot.each(function() {
            $(this).animate({ "left": coord['nextLeft'] + "px" , "top": coord['nextTop'] + "px"  }, 1000 );
        });
    }
})(jQuery);