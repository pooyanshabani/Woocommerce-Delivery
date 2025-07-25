<?php

add_filter('woocommerce_checkout_fields', 'delivery_datetime_checkout_field');

function delivery_datetime_checkout_field($fields) {
    $current_time = date_i18n('l j F Y');
    $current_datetime = current_datetime()->format('Y-m-d H:i:s');
    $timestamp1 = strtotime($current_datetime . ' +2 day');
    $timestamp2 = strtotime($current_datetime . ' +3 day');
    $timestamp3 = strtotime($current_datetime . ' +4 day');
    $current_time_p1 = date_i18n('l j F Y', $timestamp1);
    $current_time_p2 = date_i18n('l j F Y', $timestamp2);
    $current_time_p3 = date_i18n('l j F Y', $timestamp3);
    $fields['billing']['delivery_date_select'] = array(
        'type'      => 'select',
        'options'   => array(
            1 => $current_time_p1,
            2 => $current_time_p2,
            3 => $current_time_p3,
            4 => 'سایر',
        ),
        'label'       => ('بهترین روز تحویل را انتخاب کنید'),
        'placeholder' => ('روز مورد نظر را انتخاب کنید'),
        'required'    => true,
        'class'       => array('form-row-first'),
        'priority'    => 250, 
    );
    
       

    $fields['billing']['delivery_time_select'] = array(
        'type'      => 'select',
         'options'   => array(
            1 => 'ساعت 09:00 تا 12:00 ',
            2 => 'ساعت 17:00 تا 20:00 ',
        ),
        'label'       => ('بهترین بازه زمانی تحویل را انتخاب کنید'),
        'placeholder' => ('بازه زمانی مورد نظر را انتخاب کنید'),
        'required'    => true,
        'class'       => array('form-row-last'),
        'priority'    => 260,    
    );
    $fields['billing']['delivery_date_select_note'] = array(
        'type'       => 'text',
        'label'      => ('جزئیات تحویل را در کادر زیر وارد کنید'),
        'id'         => 'delivery_date_select_note',  
        'priority'=> 265,
    );
    
    

    return $fields ;
}

add_action('woocommerce_checkout_update_order_meta', 'save_delivery_datetime_checkout_field');

function save_delivery_datetime_checkout_field($order_id) {
    
    $current_time = date_i18n('l j F Y');
    $current_datetime = current_datetime()->format('Y-m-d H:i:s');
    $timestamp1 = strtotime($current_datetime . ' +1 day');
    $timestamp2 = strtotime($current_datetime . ' +2 day');
    $timestamp3 = strtotime($current_datetime . ' +3 day');
    $current_time_p1 = date_i18n('l j F Y', $timestamp1);
    $current_time_p2 = date_i18n('l j F Y', $timestamp2);
    $current_time_p3 = date_i18n('l j F Y', $timestamp3);
    
    $delivery_dates = array(
        1 => $current_time_p1,
        2 => $current_time_p2,
        3 =>$current_time_p3,
        4 => 'سایر',
    );

    $delivery_times = array(
        1 => 'ساعت 09:00 تا 12:00',
        2 => 'ساعت 17:00 تا 20:00',
    );

    
    if (!empty($_POST['delivery_date_select']) && isset($delivery_dates[$_POST['delivery_date_select']])) {
        update_post_meta($order_id, '_delivery_date_select', sanitize_text_field($delivery_dates[$_POST['delivery_date_select']]));
    }

    if (!empty($_POST['delivery_time_select']) && isset($delivery_times[$_POST['delivery_time_select']])) {
        update_post_meta($order_id, '_delivery_time_select', sanitize_text_field($delivery_times[$_POST['delivery_time_select']]));
    }

    if (!empty($_POST['delivery_date_select_note'])) {
        update_post_meta($order_id, 'delivery_date_select_note', sanitize_text_field($_POST['delivery_date_select_note']));
    }
}


add_action('woocommerce_admin_order_data_after_billing_address', 'display_custom_field_in_admin_order_meta', 10, 1);

function display_custom_field_in_admin_order_meta($order) {
    echo '<p><strong>' . ('روز تحویل') . ':</strong> ' . get_post_meta($order->get_id(), '_delivery_date_select', true) . '</p>';
    echo '<p><strong>' . ('زمان تحویل') . ':</strong> ' . get_post_meta($order->get_id(), '_delivery_time_select', true) . '</p>';
    echo '<p><strong>' . ('توضیحات') . ':</strong> ' . get_post_meta($order->get_id(), 'delivery_date_select_note', true) . '</p>';
    
}