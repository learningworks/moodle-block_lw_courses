require(['jquery'],function($) {

    $('#box-or-lines').click(function(e){
        e.preventDefault();
        $(this).toggleClass("grid");
        $(".my_courses_list .coursebox").toggleClass('grid col-md-6');
        $(".my_courses_list .coursebox").toggleClass('list col-lg-12');
    });
});