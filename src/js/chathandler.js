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
        init_ajax(msgtxt);
    });
}

function init_ajax(text){
    setLoading(1);
    $.ajax({
        url: "/ajax/message",
        method: "POST",
        data: {
            csrftoken: csrftoken,
            message: text
        },
        error: function(){
            ajax_error(1);
            setLoading(0);
        },
        success: function(data){
            if(typeof data == 'string'){
                try{
                    data = JSON.parse(data);
                }catch(e){
                    return ajax_error(2);
                }
            }
            if(typeof data.response != 'undefined'){
                handleResponse(data.response);
            }else{
                ajax_error(3);
            }
            setLoading(0);
        },
        timeout: 30000
    });
}

function ajax_error(type){
    if(type == 1){
        //Timeout or Server Error
        addMessage(0, "Sorry, there've been something wrong with your request. :(");
    }else if(type == 2){
        //Syntax error
        addMessage(0, "Sorry, there've been something wrong with your request. :(");
    }else{
        addMessage(0, "Uhmm... Something didn't work fine. :(");
    }
}

function setLoading(status){
    if(status){
        $(".thinking").show();
        in_process = 1;
    }else{
        $(".thinking").hide();
        in_process = 0;
    }
}

function handleResponse(response){
    console.log(response);
}
