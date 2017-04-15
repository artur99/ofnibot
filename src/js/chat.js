function addMessage(is_user, message, type){
    var chat = $("#chatdata");
    var html = '', html_elems, themsg;
    message = parseForEmoji(message);
    if(is_user){
        //User message
        html += '<div class="message m2"> ' + message + ' </div>';
        html += '<div class="clear"></div>';
    }else{
        //System message
        html += '<div class="message m1"> ' + message + ' </div>';
        html += '<div class="clear"></div>';
    }

    html_elems = $(html);
    themsg = html_elems.find('.message');
    themsg.hide();
    //Append
    chat.append(html_elems);
    //Animations: scroll + show
    chat.animate({ scrollTop: chat.prop("scrollHeight")}, 500);
    setTimeout(function(){
        themsg.slideDown();
    }, 500)
}

function parseForEmoji(text){
    var rpls = [], expr;
    rpls.push([':\\)', ':)']);
    rpls.push([':P', ':P']);
    rpls.push([':D', ':D']);
    rpls.push([';\\)', ';)']);

    for(var i=0;i<rpls.length;i++){
        //   /(^|\s)(:\))($|\s)/g
        expr = new RegExp("(^|\\s)("+rpls[i][0]+")($|\\s)", "g");
        text = text.replace(expr, ' <em class="emo">'+getUnicodeChar(rpls[i][1])+'</em> ');
    }

    return text;
}
