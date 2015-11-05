{% extends "layouts/app.phtml" %}
 
{% block header_title %}Pingaroo{% endblock %}
{% block body_title %}Create URL{% endblock %}

{% block content %} 
    <form action="{{ url('url/create') }}" method="POST">
        {{ partial( 
            'common/partials/table',
            [
                'enabled' : true,
                'model' : url,
                'columns' : 
                [
                    'address' : 'Address'
                ]
            ]
           ) 
        }}
  
        <a href="{{ url('url') }}"><button type="button" class="btn btn-sm btn-default">Back</button></a>

        <a href="javascript:void(0)"><input type="submit" class="btn btn-sm btn-primary" value="New"></input></a>
    </form>
    
{% endblock %}

{% block bottom %}
{% endblock %}