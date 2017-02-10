require(['jquery'],function($) {

    $('#box-or-lines').click(function(e){
        e.preventDefault();
        $(this).toggleClass("grid");
        $(".block_my_courses .my_courses_list .coursebox").toggleClass('grid col-lg-3');
        $(".block_my_courses .my_courses_list .coursebox").toggleClass('list col-lg-12');
    });
});