{# IN PROGRESS #}
{% if httpCode == 1 %}
    <span class="btn btn-sm btn-info">In progess...
{% elseif httpCode == 0 %}
    <span class="btn btn-sm btn-danger">
{% elseif httpCode == 200 %}
    <span class="btn btn-sm btn-success">
{% elseif httpCode == 404 %} 
    <span class="btn btn-sm btn-default" style="background-color: black; color: white">
{% elseif httpCode == 500 %}
    <span class="btn btn-sm btn-warning">
{% else %}
    <span class="btn btn-sm btn-default">
{% endif %}
    {{ httpCode }}
</span>