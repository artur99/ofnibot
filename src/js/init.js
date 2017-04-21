$(document).ready(function(){
    if(weAreOn('chatApp')){
        addMessage(0, 'Hello! My name is AK9Robot. How can I help you, what would you like to identify? :)');
        registerChatHandler();
        setTimeout(function(){
            $("#chatform .chatmsginput").focus();
        }, 500);
        attachInitter();
        $(document).on('click', "#helpnsend", function(e){
            e.preventDefault();
            $("#chatform .chatmsginput").val("help");
            $("#chatform").submit();
        });
    }
})
