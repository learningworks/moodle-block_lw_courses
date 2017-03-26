require(['jquery'],function($) {
    var gridsize = parseInt($('.block_my_courses .startgrid').attr("grid-size"));
    console.log(gridsize);
    var listview = Array('col-md-12','span12','list');
    var gridview = Array('col-md-' + gridsize, 'span' + gridsize, 'grid');

    $('#box-or-lines').click(function(e){
        e.preventDefault();
        $(this).toggleClass("grid");
        $(listview).each(function(i,v){
            $('.my_courses_list .coursebox').toggleClass(v);
        });
        $(gridview).each(function(i,v){
            $('.my_courses_list .coursebox').toggleClass(v);
        });
    });
});