{% extends "layouts/app.phtml" %}

{% block header_title %}Pingaroo{% endblock %}
{% block body_title %}Create Ping Batch{% endblock %}

{% block content %}
<form action="{{ url('batch/create') }}" method="POST">
    <div class="row">
        <div class="col-xs-4"> 
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <td> Name </td>
                            <td><input type="text" class="form-control"
                                   name="name" id="name" value="{{ name }}" />
                            </td>
                        </tr>
                        <tr>
                            <td> Proxy </td>
                            <td>
                                <select id="proxyId" name="proxyId[]" class="form-control" multiple="multiple">                                    
                                    {% for proxy in proxies %}
                                        <option value="{{ proxy.id }}"<?php
                                            if(in_array($proxy->id, $reflected_proxyId)) {
                                                echo 'selected="selected"';
                                            }
                                        ?>>
                                            ({{ proxy.id }}) {{ proxy.getCountry().code }} -  {{ proxy.address }}
                                        </option>
                                    {% endfor %}
                                </select>
                            </td>   
                        </tr>
                        <tr>
                            <td> Target URL </td>
                            <td>
                                <select id="address" name="address" class="form-control">
                                    <option value="-1">-- Select Address --</option>
                                    {% for urlAddress in urls %}
                                        <option value="{{ urlAddress.id }}"<?php
                                            if(in_array($urlAddress->id, $reflected_address)) {
                                                echo 'selected="selected"';
                                            }
                                        ?>>
                                            {{ urlAddress.address }}
                                        </option>
                                    {% endfor %}
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
 
    <a href="{{ url('batch') }}"><button type="button" class="btn btn-sm btn-default">Back</button></a>
    <a href="javascript:void(0)"><input type="submit" class="btn btn-sm btn-primary" value="New"></input></a>
</form>
    
{% endblock %}

{% block bottom %}
{% endblock %}