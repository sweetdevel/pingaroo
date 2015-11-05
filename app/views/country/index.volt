{% extends "layouts/app.phtml" %} 
 
{% block header_title %} Pingaroo {% endblock %}
{% block body_title %} Country List {% endblock %}

 
{% block content %} 
    <a href="<?php echo $this->url->get('country/new') ?>"><button type="button" class="btn btn-sm btn-primary">New</button></a>
    <br />
    <br /> 
    
    {{ partial( 
        'common/partials/modelListTable',
        [
            'name'   : 'country', 
            'models' : countries,
            'columns' : 
            [
                'id' : 'ID',
                'code' : 'Code',
                'name' : 'Name'
            ]
        ]
       ) 
    }} 
     
{% endblock %}

{% block bottom %}
{% endblock %}