function ajaxRename(data){
    // The callback
    if(data) {
        if(data.success) {
            oldOption = getSelectOption('batchId', _this.id);
            oldOption.text = _this.id + ' - ' + $('#name').val();
        } else {
            // alert('fail');                
        }

    // The API request
    } else {
        var request = new Object();
        request.action = 'rename';
        request.args = new Object();
        request.args.id = _this.id;
        request.args.name = $('#name').val();

        ajaxAPI('batch', request, ajaxRename);
    }
}

function ajaxDeletePing(data){
    // The callback
    if(typeof(data) != 'number') {
        if(data.success) {
            $('#pingRow_' + data.payload.id).animate({
                opacity: 0.5
            }, 'fast', function() {
                var $pingRow = $('#pingRow_' + data.payload.id);
              
                var colspan = $pingRow.find("tr:first td").length;

                var message = '<td colspan="' + colspan + '">';
                message += 'Ping <b>' + data.payload.id + '</b> deleted</td>';
              
                $pingRow.html(message);
            });     
                   
        } else {
            // alert('fail');                
        }

    // The API request
    } else {
        var pingId = data;
        
        var request = new Object();
        request.action = 'delete';
        request.args = new Object();
        request.args.id = pingId;

        ajaxAPI('ping', request, ajaxDeletePing);
    }
}

function ajaxReloadPing(data){
    // The callback
    if(typeof(data) != 'number') {
        $rowHttpCode = $('#pingRowHttpCode_' + data.payload.id);
        $rowDuration = $('#pingRowDuration_' + data.payload.id);
                
        if(data.success) {
            $rowHttpCode.html(createHttpCodeBox(data.payload.httpCode));
            $rowDuration.html(data.payload.duration);
            
            if(data.payload.httpCode != 200) {
                debugMessage = 'Reload completed.';
                debugMessage += '<br />Extra info: ' + data.payload.error;
                flashMessage('info', debugMessage);                
            }
        } else {
            $rowHttpCode.html('No code returned.');
            $rowDuration.html('No duration returned.');
        }

        $('#pingRowActionsBox_' + data.payload.id).show('slow');

    // The API request
    } else {        
        var pingId = data;
        
        var request = new Object();
        request.action = 'reload';
        request.args = new Object();
        request.args.id = pingId;
        
        $('#pingRowActionsBox_' + pingId).hide('slow');
        $('#pingRowDuration_' + pingId).html('calculating...');
        $('#pingRowHttpCode_' + pingId).html('waiting...');
        
        ajaxAPI('ping', request, ajaxReloadPing);
    }
}