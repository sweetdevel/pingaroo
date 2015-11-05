{% extends "layouts/app.phtml" %}

{% block header_title %}Pingaroo{% endblock %}
{% block body_title %}View Ping{% endblock %}
 
{% block content %}
<div class="row">
    <div class="col-xs-4">
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <td> ID </td>
                        <td><input type="text" class="form-control"
                               name="id" id="id" value="{{ ping.id }}" 
                               disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td> Proxy </td>
                        <td><input type="text" class="form-control"
                               name="proxy" id="proxy" value="{{ ping.proxyId ~ ' - ' ~ ping.proxyAddress}}"
                               disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td> Address </td>
                        <td><input type="text" class="form-control"
                               name="address" id="address" value="{{ ping.urlId ~ ' - ' ~ ping.urlAddress}}"
                               disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td> Http Code </td>
                        <td><input type="text" class="form-control"
                               name="httpCode" id="httpCode" value="{{ ping.httpCode }}"
                               disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td> Duration </td>
                        <td><input type="text" class="form-control"
                               name="duration" id="duration" value="{{ ping.duration }}"
                               disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td> Error </td>
                        <td><textarea class="form-control"
                               name="error" id="error"
                               disabled="disabled">{{ ping.error }}</textarea>
                        </td>
                    </tr>                    
                    <tr>
                        <td> Created At </td>
                        <td><input type="text" class="form-control"
                               name="createdAt" id="createdAt" value="{{ ping.createdAt }}"
                               disabled="disabled" />
                        </td>
                    </tr>                    
                    <tr>
                        <td> Updated At </td>
                        <td><input type="text" class="form-control"
                               name="updatedAt" id="updatedAt" value="{{ ping.updatedAt }}"
                               disabled="disabled" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<a href="{{ url('ping') }}">
   <button type="button" class="btn btn-sm btn-default">Back</button>
</a>
{% endblock %}

{% block bottom %}
{% endblock %}