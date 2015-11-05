{% extends "layouts/app.phtml" %}

{% block header_title %} Pingaroo {% endblock %}
{% block body_title %} Ping List {% endblock %}
 
{% block content %} 
<a href="{{ url('batch/new') }}"><button type="button" class="btn btn-sm btn-primary">New Batch</button></a>
<br />
<br /> 

{{ partial('common/partials/confirmModelDelete') }}

<div class="row">
    <form action="{{ url('ping') }}" method="GET">
        <div class="col-xs-3"> 
            Proxy
            <br />
            {{ select(
                    'proxyId',
                    'name': 'proxyId[]',
                    proxies, 
                    'using': ["id", "address"],
                    'class': 'form-control',
                    'multiple': 'multiple'
            ) }}
        </div>
        <div class="col-xs-3">
            URL
            <br />
            {{ select(
                    'urlId',
                    'name': 'urlId[]',
                    urls, 
                    'using': ["id", "address"],
                    'class': 'form-control',
                    'multiple': 'multiple'
            ) }}
        </div>
        <div class="col-xs-3">
            Batch
            <br />
            {{ select(
                    'batchId',
                    'name': 'batchId[]', 
                    batches, 
                    'using': ["id", "name"],
                    'class': 'form-control',
                    'multiple': 'multiple'
            ) }}
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
                    <th>ID</th>
                    <th>Proxy</th>
                    <th>Url</th>
                    <th>Actions</th>
                </thead>
                <tbody>                    
                    {% for ping in pings %}
                        <tr>
                            <td>{{ ping.id }}</td>
                            <td>{{ ping.proxyAddress }} 
                            </td>
                            <td>{{ ping.urlAddress }}
                            </td>
                            <td>
                                <a href="{{ url('ping/search/' ~ ping.id) }}">
                                    <button type="button" class="btn btn-sm btn-default">Show</button>
                                </a>
                                <a href="{{ url('batch?batchId=' ~ ping.batchId) }}">
                                    <button type="button" class="btn btn-sm btn-primary">Go to Batch</button>
                                </a>
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