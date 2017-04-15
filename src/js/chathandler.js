var in_process = 0;

function registerChatHandler(){
    $(document).on('submit', '#chatform', function(e){
        e.preventDefault();
        if(in_process){
            return;
        }
        var msgel, msgtxt;
        msgel = $(this).find('.chatmsginput');
        msgtxt = msgel.val();
        if(msgtxt.trim().length == 0){
            return;
        }

        addMessage(1, msgtxt);
        msgel.val('');
        // init_ajax(msgtxt);
    });
}

function init_ajax(text){
    $(".thinking").show();
    in_process = 1;

}
