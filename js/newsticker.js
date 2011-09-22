$(function()
{
    $('#ticker').each(function()
    {
        var ticker = $(this);
        var fader = $('<span class="fader">&nbsp;</span>').css({display:'inline-block'});
        var links = ticker.find('ul>li');
        ticker.find('ul').replaceWith(fader);
        
        var counter = 0;
        var curLink;
        var fadeSpeed = 600;
        var showLink = function()
            {
                var newLinkIndex = (counter++) % links.length;
                var newLink = $(links[newLinkIndex]);
                var fadeInFunction = function()
                    {
                        curLink = newLink;
                        fader.append(curLink).fadeIn(fadeSpeed); 
                    };
                if (curLink)
                {
                    fader.fadeOut(fadeSpeed, function(){
                        curLink.remove();
                        setTimeout(fadeInFunction, 0);
                    });
                }
                else
                {
                    fadeInFunction();
                }
            };
        
        
        var speed = 5500;
        var autoInterval;
        
        var startTimer = function()
        {
            autoInterval = setInterval(showLink, speed);
        };
        ticker.hover(function(){
            clearInterval(autoInterval);
        }, startTimer);

        fader.fadeOut(0, function(){
            fader.text('');
            showLink();
        });
        startTimer();
        
    });
});