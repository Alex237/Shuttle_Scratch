/*!
 * Effinet Ajax Loader v1.0.0
 * @author f.morchoisne <fabien@effi-net.com>
 * Copyright 2013 Efficiency Network, Inc
 * 
 * This script should be minimized as well as exported to it's own js file
 *
 /* Ajax Loader
 ======================================================*/
jQuery(document).ready(function($) {

    var gathering = $('#gathering > div');
    init_dynamic_link();

    $('#subnavbar li a').click(function(e) {
        e.preventDefault();
        $('#subnavbar li').removeClass('active');
        $(this).parent('li').addClass('active');
        load($(this).attr('href'));
    });

    // DOM refresher
    function init_dynamic_link() {
        $('.dynamic').click(function(e) {
            e.preventDefault();
            load($(this).attr('href'));
        });
    }

    $(window).bind('popstate', function(event) {
        var state = event.originalEvent.state;
        if (state !== null) {
            load(location.href);
        }
    });

    $(document).ajaxStart(function() {
        gathering.slideDown(200);
        gathering.animate({'width': '90%'}, 3000);
    });

    $(document).ajaxStop(function() {
        gathering.finish().animate({'width': '100%'}, 200, function() {
            $(this).fadeOut(200, function() {
                $(this).width('0%');
            });
        });
    });

    function load(link) {
        $.ajax({
            url: link,
            success: function(data) {
                var content = $(data).filter('#content').html();
                var title = $(data).filter('title').text();
                $('#content').removeClass('openFade').addClass('closeFade');
                setTimeout(function() {
                    $('#content').html(content).show().removeClass('closeFade').addClass('openFade');
                    init_dynamic_link(); // Refresh DOM
                    history.pushState({id: link}, '', link); // Add actual link into the history and current browser address bar
                }, 200);
            }
        });
    }

});/**/