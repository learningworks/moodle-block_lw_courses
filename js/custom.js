require(["jquery"],function($) {
    var gridsize = parseInt($(".block_lw_courses .startgrid").attr("grid-size"), 10);
    var listview = ["col-md-12", "span12", "list"];
    var gridview = ["col-md-" + gridsize, "span" + gridsize, "grid"];

    $(".lw_courses_list .coursebox").click(function(e) {
        if( $(e.target).hasClass('fa-star')){
            e.preventDefault();
            var instanceID = $(this).attr("id").replace("course-","");
            var val = 1;
            if($(this).find('.title').hasClass('star')){
                val = 0;
            }
            $(this).find('.title').toggleClass('star');
            $(this).toggleClass('isstar');
            M.util.set_user_preference('star_course_instance_' + instanceID, val);
        } else if($(e.target).hasClass('fa-eye')) {
            e.preventDefault();
            var instanceID = $(this).attr("id").replace("course-","");
            var val = 1;
            if($(this).find('.title').hasClass('hide')){
                val = 0;
            }
            $(this).find('.title').toggleClass('hide');
            $(this).toggleClass('hidden');
            M.util.set_user_preference('hide_course_instance_' + instanceID, val);
        } else {
            window.location = $(this).find("h2.title").find("a").attr("href");
        }
    });

    $('#showhidden').on('click', function(e){
        e.preventDefault();
        $(this).addClass('currentview');
        $('#showstarred').removeClass('currentview');
        $('.lw_courses_list').removeClass('showstarred');
        $('.lw_courses_list').addClass('showhidden');
    });

    $('#showstarred').on('click', function(e){
        e.preventDefault();
        $('#showhidden').removeClass('currentview');
        $(this).addClass('currentview');
        $('.lw_courses_list').removeClass("showhidden");
        $('.lw_courses_list').addClass('showstarred');
    });

    $("#box-or-lines").click(function(e) {
        e.preventDefault();
        $(this).toggleClass("grid");
        $(listview).each(function(i, v) {
            $(".lw_courses_list .box.coursebox").toggleClass(v);
        });
        $(gridview).each(function(i, v) {
            $(".lw_courses_list .box.coursebox").toggleClass(v);
        });
    });

    var divs;
    $('.lw_courses_list .box.coursebox').each(function(i){
        $(this).data('initial-index', i);
    });
    $('<a href="#" id="shows"  class="btn btn-primary">Starred Courses</a>').insertBefore('#showhidden');
    $('#showhidden').remove();
    $('#shows').on('click', function (e) {
        e.preventDefault();
        $(this).toggleClass('currentview');
        if ($(this).hasClass('currentview')) {
            divs = $('.lw_courses_list .box.coursebox').detach();
            $(divs).appendTo('.lw_courses_list').each(function () {
                var oldIndex = $(this).data('initial-index');
                if ($(this).hasClass('isstar')) {
                    oldIndex = 0;
                }
                $('.lw_courses_list .box.coursebox').eq(oldIndex).before(this);
            });
            $('.lw_courses_list .box.coursebox:not(.isstar)').fadeOut(500);
            $(this).html('All My Courses');
        } else {
            $(this).html('Starred Courses');
            if (divs) {
                $(divs).appendTo('.lw_courses_list').each(function () {
                    var oldIndex = $(this).data('initial-index');
                    $('.lw_courses_list .box.coursebox').eq(oldIndex).before(this);
                });
                $('.lw_courses_list .box.coursebox:not(.isstar)').fadeIn(500);
            }
        }
    });
});