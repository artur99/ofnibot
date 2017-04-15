$(document).ready(function(){
    if(weAreOn('chatApp')){
        addMessage(0, 'Hello! My name is AK9Robot. How can I help you? :)');
        registerChatHandler();
        setTimeout(function(){
            $("#chatform .chatmsginput").focus();
        }, 500);
    }
})
