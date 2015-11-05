{% extends "layouts/app.phtml" %}

{% block header_title %} Pingaroo {% endblock %}

{% block header_js %}
<?php if(isset($batch->id)) { ?>
    {{ javascript_include('js/ajax/batch.js') }}

    <script>
        _this.id = {{ batch.id }};
    </script>
<?php } ?>
{% endblock %}


{% block body_title %} Batch List {% endblock %}

{% block content %} 
<a href="{{ url('batch/new') }}"><button type="button" class="btn btn-sm btn-primary">New Batch</button></a>

<?php if(isset($batch->id)) { ?>
<button type="button" class="btn btn-sm btn-danger"
    onclick="confirmModelDelete({{ batch.id }})">Delete</button>
<button type="button" class="btn btn-sm btn-success"
        onclick="confirmBatchReload({{ batch.id }})">Reload Batch</button>
<br />
<br /> 
<div class="col-xs-3" style="margin-left:-15px">
    <div class="input-group margin">
        <input type="text" class="form-control" id="name" value="{{ batch.name }}">
        <span class="input-group-btn">
            <button type="button" class="btn btn-info btn-flat"
                onclick="ajaxRename()">rename</button>
        </span>
    </div>
</div>

<div style="clear:both"></div>

<?php
    echo Phalcon\Tag::form(array(
            'batch/delete/' . $batch->id,
            'method' => 'post',
            'id'=>'modelForm_' . $batch->id
         ));
    echo $this->tag->endForm();
?>

<script>
    function confirmBatchReload(id) {
        if(confirm('Reload batch?')) {
            document.getElementById('modelForm_Reload_' + id).submit();
        }
    }
</script>
<?php
    echo Phalcon\Tag::form(array(
            'batch/reload/' . $batch->id,
            'method' => 'post',
        'id'=>'modelForm_Reload_' . $batch->id
     ));

    echo $this->tag->endForm();    
?>
<?php } ?>

<br />
<br />

{{ partial('common/partials/confirmModelDelete') }}

<div class="row">
    <form action="{{ url('batch') }}" method="GET">
        <div class="col-xs-3">
            Batch
            <br />
            <select id="batchId" name="batchId" class="form-control">
                <option value="-1">-- Select Batch --</option>
                <?php $batchId = isset($batch->id) ? $batch->id : -1; ?>
                {% for currentBatch in batchIds %}
                    <option value="{{ currentBatch.id }}"
                        {% if currentBatch.id == batchId %}
                            selected="selected"
                        {% endif %}>

                        {{ currentBatch.id ~ ' - ' ~ currentBatch.name }}
                    </option>
                {% endfor %}
            <select>
        </div>        
        <div class="col-xs-3">
            HTTP Code
            <br />

        {{ text_field("httpCode", 'class':'form-control') }}

        </div>
        <div class="col-xs-3">
            <br />
            <input type="submit" class="form-control" value="search">
        </div>
    </form>
        
    <div style="clear:both"></div>
    <br />
    
    <div class="col-xs-12">
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
            <table id="pingTable" class="table table-hover">
                <thead>
                    <th>Ping ID</th>
                    <th>Proxy Country</th>
                    <th>Proxy</th>
                    <th>Url</th>
                    <th>Http Code</th>
                    <th>Duration</th>
                    <th>Actions</th>
                </thead>
                <tbody>                    
                    {% for ping in pings %}
                        <tr id="pingRow_{{ ping.pingId }}">
                            <td>{{ ping.pingId }}</td>
                            <td>
                                <a class="btn btn-sm btn-default" href="{{ url('country/search/' ~ ping.countryId) }}" target="_blank">
                                    {{ ping.proxyCountryCode }}
                                </a>
                            </td>
                            <td>
								{{ ping.proxyAddress }}
                                <a class="btn btn-sm btn-default" href="{{ url('proxy/search/' ~ ping.proxyId) }}" target="_blank">view</a>
                            </td>
                            <td>{{ ping.urlAddress }}</td>
                            <td>
                                <div id="pingRowHttpCode_{{ ping.pingId }}">
                                    {{ partial('common/partials/httpCodeBox', ['httpCode' : ping.httpCode]) }}
                                </div>
                            </td>
                            <td><div id="pingRowDuration_{{ ping.pingId }}">{{ ping.duration }}</div></td>
                            <td>
                                <div id="pingRowActionsBox_{{ ping.pingId }}">
                                    <a target="_blank" href="{{ url("ping/search/" ~ ping.pingId) }}">
                                        <button type="button" class="btn btn-sm btn-default">Show</button>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="ajaxDeletePing({{ ping.pingId }})">Delete</button>
                                    <button type="button" class="btn btn-sm btn-success"
                                            onclick="ajaxReloadPing({{ ping.pingId }})">Reload</button> 
                                </div>
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#pingTable').dataTable();
});
</script>
     
{% endblock %}

{% block bottom %}
{% endblock %}