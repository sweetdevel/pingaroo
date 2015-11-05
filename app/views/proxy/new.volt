{% extends "layouts/app.phtml" %} 

{% block header_title %}Pingaroo{% endblock %}
{% block body_title %}Create URL{% endblock %}
 
{% block content %} 
<form action="{{ url('proxy/create') }}" method="POST">
    <div class="row">
        <div class="col-xs-4">
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <td> Country </td>
                            <td>
                                <select id="countryId" name="countryId" class="form-control">
                                    <option value="-1" 
                                        {% if proxy.countryId == -1 %} selected="selected" {% endif %}>
                                        --Choose Country--
                                    </option>
                                    
                                    {% for country in countries %}
                                        <option value="{{ country.id }}"
                                            {% if country.id == proxy.countryId %} selected="selected" {% endif %}>
                                            {{ country.code }} - {{ country.name }}
                                        </option>
                                    {% endfor %}
                                </select>
                            </td>   
                        </tr>
                        <tr>
                            <td> Address </td>
                            <td><input type="text" class="form-control"
                                   name="address" id="address" value="<?php echo $proxy->address; ?>" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
 
    <a href="{{ url('proxy') }}"><button type="button" class="btn btn-sm btn-default">Back</button></a>
    <a href="javascript:void(0)"><input type="submit" class="btn btn-sm btn-primary" value="New"></input></a>
</form>
    
{% endblock %}

{% block bottom %}
{% endblock %}