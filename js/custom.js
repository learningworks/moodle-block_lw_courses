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
        $(this).html("Hide hidden courses");
        $('.lw_courses_list').removeClass('showstarred');

        if($('.lw_courses_list').hasClass('showhidden')){
            $(this).html("Show hidden courses");
        }
        $('#showstarred').html("Show starred courses");
        $('.lw_courses_list').toggleClass('showhidden');
    });

    $('#showstarred').on('click', function(e){
        e.preventDefault();
        $(this).html("show all courses");
        if($('.lw_courses_list').hasClass('showstarred')){
            $(this).html("Show starred courses");
        }
        $('.lw_courses_list').removeClass("showhidden");
        $('#showhidden').html("Show hidden courses");
        $('.lw_courses_list').toggleClass('showstarred');
    });

    $("#box-or-lines").click(function(e) {
        e.preventDefault();
        $(this).toggleClass("grid");
        $(listview).each(function(i, v) {
            $(".lw_courses_list .coursebox").toggleClass(v);
        });
        $(gridview).each(function(i, v) {
            $(".lw_courses_list .coursebox").toggleClass(v);
        });
    });
});