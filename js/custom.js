require(['jquery'],function($) {

    $('#box-or-lines').click(function(e){
        e.preventDefault();
        $(this).toggleClass("grid");
        $(".my_courses_list .coursebox").toggleClass('col-md-12 list');
        equalheight('.my_courses_list .coursebox');
    });
    /* Thanks to CSS Tricks for pointing out this bit of jQuery
     http://css-tricks.com/equal-height-blocks-in-rows/*/

    equalheight = function(container){

        var currentTallest = 0,
            currentRowStart = 0,
            rowDivs = [],
            $el,
            topPosition = 0;
        $(container).each(function() {

            $el = $(this);
            $($el).height('auto')
            topPostion = $el.position().top;

            if (currentRowStart !== topPostion) {
                for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
                    rowDivs[currentDiv].height(currentTallest);
                }
                rowDivs.length = 0; // Empty the array.
                currentRowStart = topPostion;
                currentTallest = $el.height();
                rowDivs.push($el);
            } else {
                rowDivs.push($el);
                currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
            }
            for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
                rowDivs[currentDiv].height(currentTallest);
            }
        });
    }

    $(document).ready(function(){
        equalheight('.my_courses_list .coursebox .image_wrap');
        equalheight('.my_courses_list .coursebox');
    });

    $(window).resize(function(){
        equalheight('.my_courses_list .coursebox .image_wrap');
        equalheight('.my_courses_list .coursebox');

    });

    });