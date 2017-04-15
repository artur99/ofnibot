function sendNotification(text, type){
    
    new Noty({
        type: 'warning',
        layout: 'topRight',
        timeout: 1500,
        text: text,
    }).show();
}
