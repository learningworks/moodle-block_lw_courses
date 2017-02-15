require(['jquery'],function($) {

    $('#box-or-lines').click(function(e){
        e.preventDefault();
        $(this).toggleClass("grid");
        $(".my_courses_list .coursebox").toggleClass('col-md-12');
    });
});