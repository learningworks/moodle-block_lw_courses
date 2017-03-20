require(['jquery'],function($) {

    boxdiv = $('#box-or-lines');
    var $listview = 'col-md-12 span12 list';
    var $currentvalue = boxdiv.attr('class');

    var i = 0;
    $('.my_courses_list').children().each(function(){
        i++;
        if (i === 1) {
            $(this).addClass('marginer');
        }
        if (i % 5 === 0) {
            $(this).addClass('marginer');
        }
    });
    boxdiv.click(function(e){
        e.preventDefault();
        $(this).toggleClass("grid");

        if ($(this).hasClass("grid")) {
            classmanipulator($listview, 'list', $currentvalue);
        } else {
            classmanipulator($currentvalue, 'grid', $listview);
        }

        equalheight('.my_courses_list .coursebox');
    });

    classmanipulator = function (r1, r2, t1)
    {
        divselect = $(".my_courses_list .coursebox");
        divselect.removeClass(r1);
        divselect.removeClass(r2);
        divselect.toggleClass(t1);
    }
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
            $($el).height('auto');
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
    };

    $(document).ready(function(){
        if ($('.startgrid').hasClass("grid")) {
            $(".my_courses_list .coursebox").addClass($currentvalue);
        }
        equalheight('.my_courses_list .coursebox .image_wrap');
        equalheight('.my_courses_list .coursebox');
    });

    $(window).resize(function(){
        equalheight('.my_courses_list .coursebox .image_wrap');
        equalheight('.my_courses_list .coursebox');

    });

    function ignoreerror()
    {
        equalheight('.my_courses_list .coursebox .image_wrap');
        equalheight('.my_courses_list .coursebox');
        return true;
    }
    window.onerror=ignoreerror();

    });