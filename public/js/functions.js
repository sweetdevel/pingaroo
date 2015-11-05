function flashMessage(type, message) {
    var title = '';
    switch(type) {
        case 'loading':
            message = '<img src="/pingaroo/images/loading_small.gif" />';
            break;
        case 'primary':
            title = 'Message!';
            break;
        case 'success':
            title = 'Success!';
            break;
        case 'info':
            title = 'Info!';
            break;
        case 'danger':
            title = 'Error!';
            break;
        case 'warning':
            title = 'Warning!';
            break;
        default:
            title = 'Message!';
            break;
    }

    var body = '<div class="alert alert-' + type + ' alert-dismissible">';
    body += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    body += '<h4><i class="icon fa fa-ban"></i> ' + title + '</h4>';
    body += message;
    body += '</div>';

    $('#jsFlashMessage').html(body);
    $('#jsFlashMessage').css('display','block');
}

function ajaxAPI(resource, payload, callback){
    flashMessage('loading');
    
    // _this.baseUrl + '/' +
    request = resource + '/ajax/' + JSON.stringify(payload);
    $.get(request)  
    .success(function(data) {
        data = JSON.parse(data);
        try {

            flashMessage(data.success ? 'success' : 'danger', data.message);
            
            if(callback) {
                callback(data);
            }
        } catch(ex) {
            flashMessage('danger', 'Client side error: ' + ex);
            if(callback) {
                callback(data);
            }
        }
    })
    .fail(function() {
        flashMessage('danger', 'An error occured with the server response.');
        
        if(callback) {        
            var failObject = new Object();
            failObject.success = false;
            failObject.message = false;
            failObject.code = false;
            failObject.payload = false;
        
            callback(failObject);
        }
    });
}

function getSelectOption(eID, val)
{ //Loop through sequentially//
  var ele=document.getElementById(eID);
  for(var ii=0; ii<ele.length; ii++)
    if(ele.options[ii].value == val) { //Found!
      return ele.options[ii];
    }
  return false;
}

function createHttpCodeBox(httpCode) {
    var classExtension = '';
    var style = '';
    
    if(httpCode == 1) {
        classExtension = 'info';
        httpCode = 'In progess...';
    } else if(httpCode == 0) {
        classExtension = 'danger';
    } else if(httpCode == 200) {
        classExtension = 'success';
    } else if(httpCode == 404) {
        classExtension = 'default'
        style = 'background-color: black; color: white';
    } else if(httpCode == 500) {
        classExtension = 'warning';
    } else {
        classExtension = 'default';
    }

    result ='<span class="btn btn-sm btn-' + classExtension + '" style=' + style + '>';
    result += httpCode;
    result += '<span>';

    return result;
}