$mrs = jQuery.noConflict();
$mrs(document).ready(function() {
    var social = '<iframe src="//www.facebook.com/plugins/like.php?href=https://www.facebook.com/MyReservationSystem/&amp;send=false&amp;layout=button_count&amp;width=90&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21&amp;appId=933206733392230" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true"></iframe>';
    var links = '';
    $mrs('.mrsAccord').on('click', function() {
        $mrs(this).parent().children('.mrsAccordWrap').hide();
        $mrs(this).next('.mrsAccordWrap').show();
    });
    $mrs('.mrsTb').on('click', function() {
        var cntId = $mrs(this).parent().attr('editorId');
        var openTag = $mrs(this).attr('openTag');
        var closeTag = $mrs(this).attr('closeTag');
        var action = $mrs(this).attr('action');
        return awQuickTags(cntId, openTag, closeTag, action);
    });
    $mrs('.mrsTb-preview').on('click', function() {
        var editId = $mrs(this).attr('editorId');
    });
    $mrs('.mrsShare').on('mouseenter', function() {
        $mrs(this).find('span').remove();
        $mrs(this).prepend('<span>' + social + '</span>');
    });
    $mrs('.mrs_overlay_close').on('click', function() {
        var iframe = document.getElementById("mrs_iframe_preview");
        $mrs("#mrsIframe").attr("src", 'about:blank');
        iframe.contentWindow.document.location.reload();
        $mrs('#msr_diplay_preview').fadeOut('fast');
        return;
    });
    $mrs('div[id*="html_javascript_adder"]').find('.widget-control-actions .alignleft').append(links);
    $mrs('.color-field').wpColorPicker();
    window.addEventListener("message", function(event) {
        if (event.data.heightscript === "calendar_size") {
        	var newheight = parseInt(event.data.heightscr) + parseInt(30);
            $mrs("#msr_height_div_43556").height(newheight);
        }
    });
});

function get_mrs_pop_up(cid, view, lang, bgcolor, room, plugin_url, type_s, layout, theme) {
    var type = $mrs(type_s).val();
    var layt = $mrs(layout).val();
    var them = $mrs(theme).val();
    var calid = $mrs(cid).val();
    var langid = $mrs(lang).val();
    var bgcolors = $mrs(bgcolor).val();
    var bgcolorid = bgcolors.replace("#", "");
    var roomid = $mrs(room).val();
    var script = "";
    if(type == 1){
    script = "<div id='msr_height_script'  style='display:inline-block;width:100%'><div id='div_freebc_software' data-background='"+bgcolorid+"' data-cid='"+calid+"' data-local='"+langid+"' data-room='auto' ></div><script src='https://myreservationsystem.com/js/freebc.js' async defer ></script></div><script src='" + plugin_url + "/js/detect-element-resize.js'></script><script> var resizeElement = document.getElementById('msr_height_script');var resizeCallback = function() { var heighs = document.getElementById('msr_height_script').clientHeight; parent.postMessage( { heightscript: 'calendar_size', heightscr: heighs }, '*'  ); return;  }; addResizeListener(resizeElement, resizeCallback);</script>";
    var temp = window.btoa(encodeURI(script));
    $mrs("#mrs_iframe_preview").attr("src", plugin_url + '/preview.php?val=' + temp);
    $mrs('#msr_diplay_preview').fadeIn('fast');   
    $mrs("html, body").animate({
            scrollTop: 40
        }, "slow");
    }
    if(type == 2){
    script = "<div id='msr_height_script' style='display:inline-block;width:100%'><div id='div_mrbc_software' data-background='"+bgcolorid+"' data-cid='"+calid+"' data-local='"+langid+"' data-room='"+roomid+"' ></div><script src='https://myreservationsystem.com/js/mrbc.js' async defer ></script></div><script src='" + plugin_url + "/js/detect-element-resize.js'></script><script> var resizeElement = document.getElementById('msr_height_script');var resizeCallback = function() { var heighs = document.getElementById('msr_height_script').clientHeight; parent.postMessage( { heightscript: 'calendar_size', heightscr: heighs }, '*'  ); return;  }; addResizeListener(resizeElement, resizeCallback);</script>";
    var temp = window.btoa(encodeURI(script));
    $mrs("#mrs_iframe_preview").attr("src", plugin_url + '/preview.php?val=' + temp);
    $mrs('#msr_diplay_preview').fadeIn('fast');   
    $mrs("html, body").animate({
            scrollTop: 40
        }, "slow");
    }
    if(type == 3){
    script = "<div id='msr_height_script' style='display:inline-block;width:100%'><div id='div_tsbc_software' data-background='"+bgcolorid+"' data-cid='"+calid+"' data-local='"+langid+"' data-layout='"+layt+"' ></div><script src='https://myreservationsystem.com/js/tsbc.js' async defer ></script></div><script src='" + plugin_url + "/js/detect-element-resize.js'></script><script> var resizeElement = document.getElementById('msr_height_script');var resizeCallback = function() { var heighs = document.getElementById('msr_height_script').clientHeight; parent.postMessage( { heightscript: 'calendar_size', heightscr: heighs }, '*'  ); return;  }; addResizeListener(resizeElement, resizeCallback);</script>";
    var temp = window.btoa(encodeURI(script));
    $mrs("#mrs_iframe_preview").attr("src", plugin_url + '/preview.php?val=' + temp);
    $mrs('#msr_diplay_preview').fadeIn('fast');   
    $mrs("html, body").animate({
            scrollTop: 40
        }, "slow");
    }
    if(type == 4){
    script = "<div id='msr_height_script' style='display:inline-block;width:100%'><div id='div_mrbs_software' data-background='"+bgcolorid+"' data-cid='"+calid+"' data-local='"+langid+"' data-theme='"+them+"' ></div><script src='https://myreservationsystem.com/js/mrbs.js' async defer ></script></div><script src='" + plugin_url + "/js/detect-element-resize.js'></script><script> var resizeElement = document.getElementById('msr_height_script');var resizeCallback = function() { var heighs = document.getElementById('msr_height_script').clientHeight; parent.postMessage( { heightscript: 'calendar_size', heightscr: heighs }, '*'  ); return;  }; addResizeListener(resizeElement, resizeCallback);</script>";
    var temp = window.btoa(encodeURI(script));
    $mrs("#mrs_iframe_preview").attr("src", plugin_url + '/preview.php?val=' + temp);
    $mrs('#msr_diplay_preview').fadeIn('fast');   
    $mrs("html, body").animate({
            scrollTop: 40
        }, "slow");
    }
    return;
   
    
}