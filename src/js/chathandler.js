var in_process = 0;

function registerChatHandler(){
    $(document).on('submit', '#chatform', function(e){
        e.preventDefault();
        if(in_process){
            return;
        }
        $(this).find('.chatmsginput').focus();
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
    // $(document).on('load', '.message.is_image', refreshScroll);
}

function init_ajax(text, type){
    var start_time = new Date().getTime();
    var data = {};
    setLoading(1);
    data.csrftoken = csrftoken;
    data.message = text;
    if(typeof type != 'undefined'){
        data.type = type;
    }
    $.ajax({
        url: "/ajax/message",
        method: "POST",
        data: data,
        error: function(){
            ajax_error(1);
            setLoading(0);
        },
        success: function(data){
            var request_time, tottime = 1;
            request_time = new Date().getTime() - start_time;
            if(request_time < 1000){
                tottime = 700;
            }
            setTimeout(function(){
                if(typeof data == 'string'){
                    try{
                        data = JSON.parse(data);
                    }catch(e){
                        ajax_error(2);
                        data = undefined;
                    }
                }
                if(typeof data != 'undefined'){
                    if(typeof data.response != 'undefined'){
                        handleResponse(data.response);
                    }else{
                        ajax_error(3);
                    }
                }
                setLoading(0);
            }, tottime);
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
    addMessage(0, response, 0, 1);
}

function attachInitter(){
    $(document).on('change', '#attachinput', function(){
        imageHandler($("#attachinput")[0].files[0]);
        $("#attachinput").val('');
    });
    $(document).on('click', '#attachico', function(e){
        e.preventDefault();
        if(in_process == 0){
            $("#attachinput").click();
        }
    });
    $("html").on("dragover", function(e) {
        e.preventDefault();
        e.stopPropagation();
        if(in_process == 0){
            $(this).addClass('dragging');
        }
    });

    $("html").on("dragleave", function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragging');
    });

    $("html").on("drop", function(e) {
        e.preventDefault();
        e.stopPropagation();
        if(in_process == 0){
            imageHandler(e.originalEvent.dataTransfer.files[0]);
        }
    });

}

function imageHandler(imagedata){
    getAllAboutImage(imagedata, function(is_valid, blob_src){
        // is valid?
        if(is_valid == 0){
            addMessage(1, imagedata.name, 'file');
            setTimeout(function(){
                addMessage(0, "Sorry, I can't read the file you've sent. :( Please send a valid image.");
                setLoading(0);
            }, 1000);
        }else{
            addMessage(1, blob_src, 'image');
        }
    }, function(got_barcode, barcode, type){
        // got Barcode
        setTimeout(function(){
            setLoading(0);
            if(got_barcode){
                addMessage(0, "I found this "+type+" barcode in your image: "+barcode+". :D Please wait a bit, I'll be looking for some data...");
                init_ajax(barcode, 'barcode');
            } else {
                addMessage(0, "I'm sorry, I couldn't find any barcode in your image. :(");
            }
        }, 500);
    });
}

function getAllAboutImage(imagedata_raw, isvalid, gotbarcode, gotqr){
    var imgcontent, barcoderesult;
    var freader = new FileReader();
    var img = new Image();
    var canvas = document.createElement('canvas');

    if(
        typeof imagedata_raw == 'undefined' ||
        typeof imagedata_raw.type == 'undefined'
    ){
        return;
    }

    setLoading(1);

    if(imagedata_raw.type.indexOf('image') == -1){
        return isvalid(0);
    }

    var context = canvas.getContext('2d');
    var imagedata = freader.readAsDataURL(imagedata_raw);

    freader.onloadend = function () {
        img.src = freader.result;
        img.onload = function(){
            isvalid(1, img.src);
            canvas.width = img.width;
            canvas.height = img.height;
            context.drawImage(img, 0, 0 );
            var imgcontent = context.getImageData(0, 0, img.width, img.height);
            barcoderesult = zbarProcessImageData(imgcontent);
            if(
                barcoderesult == false ||
                typeof barcoderesult == 'undefined' ||
                typeof barcoderesult[0] == 'undefined' ||
                typeof barcoderesult[0][2] == 'undefined'
            ){
                gotbarcode(0);
            }else{
                // console.log(barcoderesult);
                gotbarcode(1, barcoderesult[0][2], barcoderesult[0][0]);
            }
        };
        img.onerror = function(){
            isvalid(0);
        }
    }
}
