function addMessage(is_user, message, type, dont_escape){
    var chat = $("#chatdata");
    var html = '', html_elems, themsg, author_class, more_classes = '', escapedMessage;
    author_class = is_user ? 'm2' : 'm1';

    if(typeof type == 'undefined'){
        type = 0;
    }
    if(typeof dont_escape == 'undefined'){
        dont_escape = 0;
    }

    if(type == 'file'){
        more_classes = 'is_file';
    }else if(type == 'image'){
        more_classes = 'is_image';
    }


    message = message.toString();

    if(type == 'image'){
        html += '<img src="'+message+'" class="message ' + author_class + ' ' + more_classes + '">';
    }else{
        if(!dont_escape){
            escapedMessage = $("<div>").text(message).html();
        }else{
            escapedMessage = message;
        }
        escapedMessage = parseForEmoji(escapedMessage);
        escapedMessage = messageLastChecks(escapedMessage);

        html += '<div class="message ' + author_class + ' ' + more_classes + '"> ' + escapedMessage + '</div>';
    }
    html += '<div class="clear"></div>';

    html_elems = $(html);
    themsg = html_elems.find('.message');
    themsg.hide();
    //Append
    chat.append(html_elems);

    refreshScroll(function(time){
        if(time>500)
            time = 1000;
        else
            time = 500;

        themsg.slideDown(time);
    });
}

function messageLastChecks(message){
    message = message.replace(/\n/g, '<br>');
    return message;
}

function parseForEmoji(text){
    var rpls = [], expr;
    rpls.push([':\\)', ':)']);
    rpls.push([':P', ':P']);
    rpls.push([':D', ':D']);
    rpls.push([';\\)', ';)']);
    rpls.push([':\\(', ':(']);

    for(var i=0;i<rpls.length;i++){
        //   /(^|\s)(:\))($|\s)/g
        expr = new RegExp("(^|\\s)("+rpls[i][0]+")($|\\s)", "g");
        text = text.replace(expr, ' <em class="emo">'+getUnicodeChar(rpls[i][1])+'</em> ');
    }

    return text;
}

function getUnicodeChar(emoticon){
    var data = [];
    data[':)'] = '\u{1F60A}';
    data[':D'] = '\u{1F601}';
    data[':P'] = '\u{1F60B}';
    data[';)'] = '\u{1F609}';
    data[':('] = '\u{1F61F}';
    return typeof data[emoticon] == 'undefined' ? '' : data[emoticon];
}
