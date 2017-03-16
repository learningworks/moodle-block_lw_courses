require(['jquery'],function($) {

    console.log($('#box-or-lines').attr('class'));
    var $listview = 'col-md-12 span12 list';
    var $currentvalue = $('#box-or-lines').attr('class');

    var i = 0;
    $('.my_courses_list').children().each(function(){
        console.log($(this));
        if (!$(this).hasClass('category')) {
            i++;
        }
        if (i === 1) {
            $(this).addClass('marginer');
        }
        if (i%5 === 0) {
            $(this).addClass('marginer');

        }
    });
    $('#box-or-lines').click(function(e){
        e.preventDefault();
        $(this).toggleClass("grid");

        if ($(this).hasClass("grid")) {
            console.log('grid');
            $(".my_courses_list .coursebox").removeClass($listview);
            $(".my_courses_list .coursebox").removeClass('list');
            $(".my_courses_list .coursebox").toggleClass($currentvalue);
        } else {
            console.log('list');
            $(".my_courses_list .coursebox").removeClass($currentvalue);
            $(".my_courses_list .coursebox").removeClass('grid');
            $(".my_courses_list .coursebox").toggleClass($listview);
        }

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
        if ($('#box-or-lines').hasClass("grid")) {
            $('.my_courses_list').children().each(function(){
                $(this).addClass($currentvalue);
            });
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