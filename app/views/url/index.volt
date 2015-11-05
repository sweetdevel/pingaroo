{% extends "layouts/app.phtml" %} 
 
{% block header_title %} Pingaroo {% endblock %}
{% block body_title %} URL List {% endblock %}

 
{% block content %} 
    <a href="<?php echo $this->url->get('url/new') ?>"><button type="button" class="btn btn-sm btn-primary">New</button></a>
    <br />
    <br /> 
     
    {{ partial( 
        'common/partials/modelListTable',
        [
            'name'   : 'url', 
            'models' : urls,
            'columns' : 
            [
                'id' : 'ID',
                'address' : 'Address'
            ]
        ]
       ) 
    }} 
     
{% endblock %}

{% block bottom %}
{% endblock %}