
$.fn.ajaxForm = function (options)
{
    var that = this;
    var id = $(that).attr('id');
    var end_url = $(this).attr('action');

    options.onError = options.onError || function (){};

    var errorBlock = $('#'+id+'Error');
    if (errorBlock.length==0)
    {
        errorBlock = $('<div id="'+id+'Error"></div>').insertBefore(that);
    }

    function getHTML (errArray){
        var h = '<div class="errors">';
        $.each(errArray,function(elementId,errors){
            $.each(errors,function(errorId,errorMsg){
                h += '<p id="'+elementId+'-'+errorId+'" class="error">'+errorMsg+'</p>';
            });
        });
        h += '</div>';
        return h;
    };

    function highlightErrors (errArray){
        that.find ('INPUT,SELECT').removeClass("error");
        $.each(errArray,function(elementId,errors) {
            $('#'+elementId).addClass('error');
            $('#'+elementId+'-element').addClass('error');
        });
    };

    function doValidate (){
        var url = end_url;
        var data = $(that).serialize();
        $.post(url,data,function(response){
            if (response==true)
                options.onValid();
            else {
                errorBlock.find('.errors').remove();
                errorBlock.append(getHTML(response));
                options.onError ();

                highlightErrors(response);
            }
        },'json');
    };

    $(this).submit (function(){
        doValidate();
        return false;
    })
    return this;
}