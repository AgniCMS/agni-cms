/**
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */


function ajax_admin_fpw(thisobj) {
	var serialize_val = thisobj.serialize();
	
	// disable submit button
	$('.fpw-button').attr('disabled', 'disabled');
	
	// set loading status
	$('.ajax_fpw_status').html('<img src="'+base_url+'public/themes/system/site-admin/images/loading.gif" alt="" />');
	
	$.ajax({
		url: site_url+'site-admin/login/resetpw',
		type: 'POST',
		data: serialize_val,
		dataType: 'json',
		success: function(data) {
			$('.fpw-button').removeAttr('disabled');
			$('.ajax_fpw_status').html('');
			$('.form-status-fpw').html('<div class="alert alert-'+data.form_status+'">'+data.form_status_message+'</div>');
			if (data.result == true) {
				$('.form-fpw').hide('fade', {}, 'fast');
			} else {
				$('.captcha').attr('src', base_url+'public/images/securimage_show.php?' + Math.random());
			}
		},
		error: function(data, status, e) {
			alert('Request reset password error '+e);
			$('.fpw-button').removeAttr('disabled');
		}
	});
	return false;
}// ajax_admin_fpw


function ajax_admin_login(thisobj) {
	var serialize_val = thisobj.serialize();
	
	// disable submit button.
	$('.login-button').attr('disabled', 'disabled');
	
	// set loading status
	$('.ajax_status').html('<img src="'+base_url+'public/themes/system/site-admin/images/loading.gif" alt="" />');
	
	$.ajax({
		url: thisobj.attr('action'),
		type: 'POST',
		data: serialize_val,
		dataType: 'json',
		success: function(data) {
			if (data.form_status === true) {
				window.location = data.go_to;
			} else {
				$('.login-button').removeAttr('disabled');
				$('.ajax_status').html('');
				$('.form-status').html('<div class="alert alert-'+data.form_status+'">'+data.form_status_message+'</div>');
				
				$('.captcha').attr('src', base_url+'public/images/securimage_show.php?' + Math.random());
				$('.login-username').focus();
				if (data.show_captcha == true) {
					$('.captcha-field').show('fade', {}, 'fast');
				} else {
					$('.captcha-field').hide('fade', {}, 'fast');
				}
			}
		},
		error: function(data, status, e) {
			alert('Login error '+e);
			$('.login-button').removeAttr('disabled');
		}
	});
	return false;
}// ajax_admin_login


function change_redirect(obj) {
	window.location = $(obj).val();
}// change_redirect


function checkAll(pForm, boxName, parent) {
	for (i = 0; i < pForm.elements.length; i++)
		if (pForm.elements[i].name == boxName)
			pForm.elements[i].checked = parent;
}// checkAll


function htmlspecialchars_decode (string, quote_style) {
	// http://kevin.vanzonneveld.net
	// +   original by: Mirek Slugen
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   bugfixed by: Mateusz "loonquawl" Zalega
	// +      input by: ReverseSyntax
	// +      input by: Slawomir Kaniecki
	// +      input by: Scott Cariss
	// +      input by: Francois
	// +   bugfixed by: Onno Marsman
	// +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	// +      input by: Ratheous
	// +      input by: Mailfaker (http://www.weedem.fr/)
	// +      reimplemented by: Brett Zamir (http://brett-zamir.me)
	// +    bugfixed by: Brett Zamir (http://brett-zamir.me)
	// *     example 1: htmlspecialchars_decode("<p>this -&gt; &quot;</p>", 'ENT_NOQUOTES');
	// *     returns 1: '<p>this -> &quot;</p>'
	// *     example 2: htmlspecialchars_decode("&amp;quot;");
	// *     returns 2: '&quot;'
	var optTemp = 0,
	i = 0,
	noquotes = false;
	if (typeof quote_style === 'undefined') {
		quote_style = 2;
	}
	string = string.toString().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
	var OPTS = {
		'ENT_NOQUOTES': 0,
		'ENT_HTML_QUOTE_SINGLE': 1,
		'ENT_HTML_QUOTE_DOUBLE': 2,
		'ENT_COMPAT': 2,
		'ENT_QUOTES': 3,
		'ENT_IGNORE': 4
	};
	if (quote_style === 0) {
		noquotes = true;
	}
	if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
		quote_style = [].concat(quote_style);
		for (i = 0; i < quote_style.length; i++) {
			// Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
			if (OPTS[quote_style[i]] === 0) {
				noquotes = true;
			} else if (OPTS[quote_style[i]]) {
				optTemp = optTemp | OPTS[quote_style[i]];
			}
		}
		quote_style = optTemp;
	}
	if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
		string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
		// string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
	}
	if (!noquotes) {
		string = string.replace(/&quot;/g, '"');
	}
	// Put this in last place to avoid escape being double-decoded
	string = string.replace(/&amp;/g, '&');

	return string;
}// htmlspecialchars_decode


function insert_media(element) {
	element = htmlspecialchars_decode(element, 'ENT_QUOTES');
	tinyMCE.activeEditor.execCommand('mceInsertContent', false, element);
	window.parent.close_dialog();
	return false;
}// insert_media


function make_tabs() {
	$("#tabs").tabs({cookie: {expires: 30}});
}// make_tabs


function noenter(e) {
	var key;     
	if(window.event)
		key = window.event.keyCode; //IE
	else
		key = e.which; //firefox and others

	return (key != 13);
}// noenter


function remove_feature_image() {
	$('#input-feature-image').val('');
	$('.feature-image-img').html('');
	return false;
}// remove_feature_image


function set_feature_image(num) {
	var parents = $(parent.document.body);
	$(parents).find('#input-feature-image').val(num);
	window.parent.update_feature_image(num);
	window.parent.close_dialog();
}


function UpdateTableHeaders() {
	$("div.divTableWithFloatingHeader").each(function() {
		var originalHeaderRow = $(".tableFloatingHeaderOriginal", this);
		var floatingHeaderRow = $(".tableFloatingHeader", this);
		var offset = $(this).offset();
		var scrollTop = $(window).scrollTop();
		if ((scrollTop > offset.top) && (scrollTop < offset.top + $(this).height())) {
			floatingHeaderRow.css("visibility", "visible");
			floatingHeaderRow.css("top", Math.min(scrollTop - offset.top, $(this).height() - floatingHeaderRow.height()) + "px");

			// Copy cell widths from original header
			$("th", floatingHeaderRow).each(function(index) {
				var cellWidth = $("th", originalHeaderRow).eq(index).css('width');
				$(this).css('width', cellWidth);
			});

			// Copy row width from whole table
			floatingHeaderRow.css("width", $(this).css("width"));
		}
		else {
			floatingHeaderRow.css("visibility", "hidden");
			floatingHeaderRow.css("top", "0px");
		}
	});
}// UpdateTableHeaders


var delay = (function(){
	var timer = 0;
	return function(callback, ms){
		clearTimeout (timer);
		timer = setTimeout(callback, ms);
	};
})();


$(document).ready(function() {
	$('.login-username').focus();// auto focus at input username [login page]
	
	$('#js-check').removeClass('icon-remove').addClass('icon-ok');// jquery checked javascript requirement at login page
	
	$('.forget-toggle').click(function() {
		$('.forget-form').toggle('fade');
	});// toggle forgot user,pass
	
	$("ul.sf-menu").supersubs({
		minWidth:    12,   // minimum width of sub-menus in em units
		maxWidth:    27,   // maximum width of sub-menus in em units
		extraWidth:  1     // extra width can ensure lines don't sometimes turn over
	}).superfish({
		delay:         300,
		speed: 0
	});
});