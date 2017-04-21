function sendNotification(text, type){

    new Noty({
        type: 'warning',
        layout: 'topRight',
        timeout: 1500,
        text: text,
    }).show();
}

function refreshScroll(callback){
    //Animations: scroll + show
    var chatbox, to_scroll, time = 500;
    chatbox = $("#chatdata");
    to_scroll = chatbox.prop("scrollHeight") - (chatbox.scrollTop() + chatbox.height()) - 20;
    
    if(to_scroll > 300){
        time = 1000;
    }
    chatbox.animate(
        {scrollTop: chatbox.prop("scrollHeight")},
        time,
        function(){
            callback(time);
        }
    );
}
