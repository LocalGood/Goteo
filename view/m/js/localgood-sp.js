$ = jQuery;

$(function () {
    // グロナビ, プルダウン
    $('.nav_inner ul li').hover(
        function(){
            $(this).children('ul.sub:hidden').slideToggle();
        },
        function(){
            $(this).children('ul.sub:visible').slideToggle();
        }
    );

    // smooth scroll
    $('a[href^=#]').click(function(e){
        e.preventDefault();
        var speed = 350;
        var href= $(this).attr("href");
        var target = $(href == "#" || href == "" ? 'html' : href);
        var position = target.offset().top;
        $("html, body").animate({scrollTop:position}, speed, "swing");
        return false;
    });

        if($('.flipsnap_projectnav').length > 0){
            // sp project-nav
            Flipsnap('.flipsnap_projectnav');
            var pj_flipsnap = Flipsnap('.flipsnap_projectnav', {
                distance: 82
            });
            var $next = $('.pj_next').click(function() {
                pj_flipsnap.toNext();
            });
            var $prev = $('.pj_prev').click(function() {
                pj_flipsnap.toPrev();
            });
            pj_flipsnap.element.addEventListener('fspointmove', function() {
                $next.attr('disabled', !pj_flipsnap.hasNext());
                $prev.attr('disabled', !pj_flipsnap.hasPrev());
            }, false);
        }
        if($('.flipsnap_dashboard').length > 0){
            // sp dashboard-nav
            Flipsnap('.flipsnap_dashboard');
            var db_flipsnap = Flipsnap('.flipsnap_dashboard', {
                distance: 18
            });
            var $next = $('.db_next').click(function() {
                db_flipsnap.toNext();
            });
            var $prev = $('.db_prev').click(function() {
                db_flipsnap.toPrev();
            });
            db_flipsnap.element.addEventListener('fspointmove', function() {
                $next.attr('disabled', !db_flipsnap.hasNext());
                $prev.attr('disabled', !db_flipsnap.hasPrev());
            }, false);
        }

    $('.nav_inner').meanmenu({
        meanScreenWidth: '960'
    });

    // 投稿画像etcを画面幅に収める
    var post_width = $('.post_body').width();
    if ( post_width < $('.post_body div').width() ) {
        $('.post_body div').css({
            width: post_width + 'px'
        });
    }
    if ( post_width < $('.post_body img').width() ) {
        $('.post_body img').css({
            width: post_width + 'px'
        });
    }


});

