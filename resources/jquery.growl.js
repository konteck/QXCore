/**
*	jQuery.noticeAdd() and jQuery.noticeRemove()
*	These functions create and remove growl-like notices
*
*   Copyright (c) 2009 Tim Benniks
*
*	@author 	Tim Benniks <tim@timbenniks.com>
* 	@copyright  2009 timbenniks.com
*	@version    $Id: jquery.notice.js 1 2009-01-24 12:24:18Z timbenniks $
**/
(function(jQuery)
{
	jQuery.extend({
		noticeAdd: function(options)
		{
			var defaults = {
				inEffect: 			{opacity: 'show'},	// in effect
				inEffectDuration: 	600,				// in effect duration in miliseconds
				stayTime: 			3000,				// time in miliseconds before the item has to disappear
				text: 				'',					// content of the item
				stay: 				false,				// should the notice item stay or not?
				type: 				'notice' 			// could also be error, succes
			}

			// declare varaibles
			var options, noticeWrapAll, noticeItemOuter, noticeItemInner, noticeItemClose;

			options 		= jQuery.extend({}, defaults, options);
			noticeWrapAll	= (!jQuery('.notice-wrap').length) ? jQuery('<div></div>').addClass('notice-wrap').appendTo('body') : jQuery('.notice-wrap');
			noticeItemOuter	= jQuery('<div></div>').addClass('notice-item-wrapper');
			noticeItemInner	= jQuery('<div></div>').hide().addClass('notice-item ' + options.type).appendTo(noticeWrapAll).html('<p>'+options.text+'</p>').animate(options.inEffect, options.inEffectDuration).wrap(noticeItemOuter);
			noticeItemClose	= jQuery('<div></div>').addClass('notice-item-close').prependTo(noticeItemInner).html('x').click(function() { jQuery.noticeRemove(noticeItemInner) });

			// hmmmz, zucht
			if(navigator.userAgent.match(/MSIE 6/i))
			{
		    	noticeWrapAll.css({top: document.documentElement.scrollTop});
		    }

			if(!options.stay)
			{
				setTimeout(function()
				{
					jQuery.noticeRemove(noticeItemInner);
				},
				options.stayTime);
			}
		},

		noticeRemove: function(obj)
		{
			obj.animate({opacity: '0'}, 600, function()
			{
				obj.parent().animate({height: '0px'}, 300, function()
				{
					obj.parent().remove();
				});
			});
		}
	});
})(jQuery);