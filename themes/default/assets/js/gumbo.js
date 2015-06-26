(function ( $ ) {
 
    $.fn.gumboTabs = function() {
        $(this).each(function(){
            $(this).children('ul').children('li').children('a').click(function(){
                $(this).parent().addClass('active').siblings('li').removeClass('active');
                $(this).closest('.tabs').children('.tab-content.active').removeClass('active');
                $($(this).attr('href')).addClass('active');
                return false; //don't change the URL
            });
        });
    };

    $.gumboTray = function(content) {
        
        $('body').append('<div class="gumbo-tray-enclosure"><div class="gumbo-tray-cover"></div><div class="gumbo-tray"><div class="gumbo-tray-content container"><div class="gumbo-tray-close"></div>'+content+'</div></div></div>');
        $('.gumbo-tray-content').css('top', $(document).scrollTop()).slideDown(300);
        $('.gumbo-tray-cover').fadeIn();
        $('.gumbo-tray-close, .gumbo-tray-cover').click(function()
        {
            $.gumboTray.close();
        });
        
    };

    $.gumboTray.close = function() {
        $('.gumbo-tray-cover').fadeOut();
        $('.gumbo-tray-content').slideUp(300,function(){
            $('.gumbo-tray-enclosure').remove();
        });
    }

    $.fn.gumboTray = function()
    {
        $.gumboTray($(this).html());
    }
    
    $.fn.gumboBanner = function(bnrIndex) {
        var timeOut = 4000;
        var fade = 500;
        if(bnrIndex == undefined)
        {
            bnrIndex = 0;
        }

        function gumboRotateBanner(elem, direction)
        {
            var cnt = $(elem).data('cnt');
            var timer = $(elem).children('.banner-timer');

            timer.width(0);
            
            if(direction == undefined)
            {
                direction = 'forward';
            }

            $(elem).children('.banner').eq(cnt).fadeOut(fade).css('position', 'absolute');
            
            if(direction == 'forward')
            {
                cnt++;
                if(cnt == $(elem).children('.banner').size())
                {
                    cnt = 0;
                }    
            }
            else
            {
                cnt -= 1;
                if(cnt < 0)
                {
                    cnt = $(elem).children('.banner').size()-1;
                }
            }
            
            $(elem).data('cnt', cnt);

            $(elem).children('.banner').eq(cnt).fadeIn(fade, function(){
                timer.animate({width: '100%'}, timeOut, function(){
                    gumboRotateBanner(elem)
                });
            }).css('position', 'static');
        }

        $(this).each(function() {
            var timer = $(this).children('.banner-timer');
            $(this).data('cnt', bnrIndex);

            $(this).children('.banner').eq(bnrIndex).css('position', 'static');
            $(this).children('.banner').not(':eq('+bnrIndex+')').hide();

            if($(this).children('.banner').size() > 1)
            {

                var elem = this;
                timer.animate({width: '100%'}, timeOut, function(){
                    gumboRotateBanner(elem)
                });

                $(this).children('.controls').click(function(){
                    timer.stop(true);
                    gumboRotateBanner(elem, $(this).attr('data-direction'));
                });

                $(this).mouseover(function(){
                    timer.stop(true);
                    $(this).children('.controls').show();
                });
            
                $(this).mouseout(function(){
                    $(this).children('.controls').hide();
                    timer.animate({width: '100%'}, timeOut, function(){
                        gumboRotateBanner(elem);
                    });
                });
            }                
        });
    }
}( jQuery ));


//Auto run this function for enabling mobile nav
$(document).ready(function(){
    
    $('.mobileNav').each(function(){
        $(this).children('li').clone().appendTo($('.navbarMobile'));
    });

    $('.navbarMobile li:has(ul)>a, .navbarMobile li:has(ul)>span').removeAttr('href').click(function(){
        $(this).parent().children('ul').slideToggle(200);
        return false;
    });


    $('.banners').gumboBanner();
    $('.tabs').gumboTabs();
    
    //close class always kills the parent
    $('body').on('click', '.close', function(){
        $(this).parent().slideUp(function(){
            $(this).remove();
        });
    });
    
    $('.table').wrap('<div class="table-respond"></div>');
});