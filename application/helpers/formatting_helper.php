<?php
function format_address($fields)
{
    if(empty($fields))
    {
        return ;
    }

    // Default format
    $default = "<strong>{% if company %} {{company}}, {% endif %}{{firstname}} {{lastname}}</strong><br><small>{{phone}} | {{email}}<br>{{address1}}<br>{% if address2 %}{{address2}}<br>{% endif %}{{city}}, {{zone}} {{zip}}<br>{{country}}</small>";

    // Fetch country record to determine which format to use
    $CI = &get_instance();
    $CI->load->model('Locations');
    $c_data = $CI->Locations->get_country($fields['country_id']);

    if(empty($c_data->address_format))
    {
        $formatted  = $default;
    } else {
        $formatted  = $c_data->address_format;
    }

    $loader = new Twig_Loader_String();
    $twig = new Twig_Environment($loader);

    $formatted = $twig->render($formatted, $fields);

    return $formatted;
}

function format_currency($value, $symbol=true)
{
    $fmt = numfmt_create( config_item('locale'), NumberFormatter::CURRENCY );
    return numfmt_format_currency($fmt, round($value,2), config_item('currency_iso'));
}
