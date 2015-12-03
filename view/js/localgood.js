/**
 * Created by m454k1 on 2014/08/21.
 */
$ = jQuery;

$(function(){

    // グロナビ, プルダウン
    $('.nav_inner ul li').hover(
        function(){
            $(this).children('ul.sub:hidden').stop().show();
        },
        function(){
            $(this).children('ul.sub:visible').stop().hide();
        }
    );

    // smooth scroll
    $('div#to_page_top a[href^=#]').click(function(e){
        e.preventDefault();
        var speed = 350;
        var href= $(this).attr("href");
        var target = $(href == "#" || href == "" ? 'html' : href);
        var position = target.offset().top;
        $("html, body").animate({scrollTop:position}, speed, "swing");
        return false;
    });

});
