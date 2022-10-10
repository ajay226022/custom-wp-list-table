<?php

/*
* Plugin Name: Wp_List_Table
* Description: Test.
* Author: Ajay

*/

// if(isset($_GET['page']) && $_GET['page'] == 'wp-list-table' && isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == 'delete'){
//     global $wpdb;
//     $query = $wpdb->delete('wp_list_table', array('ID' => $_GET['id']));
// }

add_action('admin_menu', 'wp_list_table_menu');

function wp_list_table_menu()

{
    add_menu_page(
        'Wp List Table', //page_title.
        'Wp List Table', //menu_title.
        'manage_options', //capability.
        'wp-list-table', //menu_slug. 
        'wp_list_table_fn', //callback function.
        'dashicons-tickets-alt', //dashicon.
        2 //position.

    );
}

// --------------------------add a page in plugin--------------------------
function wp_list_table_fn()
{

    ob_start();
    include plugin_dir_path(__FILE__) . 'views/wp_list_table.php';
    // include plugin_dir_path(__FILE__) . 'views/wp-edit-fn.php';

    $templete = ob_get_contents();
    ob_end_clean();
    echo $templete;

      
}

function wp_enqueue_admin_script()
{
    wp_enqueue_style('bootstrap-min', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.min.css', array(), '3.4.2');
    wp_enqueue_script('jquery-slim', plugin_dir_url(__FILE__) . 'js/jquery-slim.min.js', array(), '3.1.0');
    
    
    wp_enqueue_script('popper-min', plugin_dir_url(__FILE__) . 'assets/js/popper.min.js', array(), '3.2.1');
    wp_enqueue_script('bootstrap-min', plugin_dir_url(__FILE__) . 'assets/js/bootstrap.min.js', array(), '3.2.3');
    wp_enqueue_script('wlt-backend-js', plugin_dir_url(__FILE__).'assets/js/wlt_backend.js', array(),'3.2.0');
    wp_enqueue_style('wlt-backend', plugin_dir_url(__FILE__) . 'assets/css/wlt_backend.css', array(), '3.4.1');
    
}

add_action('admin_enqueue_scripts', 'wp_enqueue_admin_script');

