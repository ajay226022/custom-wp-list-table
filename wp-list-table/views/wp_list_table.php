<?php

require_once(ABSPATH . "wp-admin/includes/class-wp-list-table.php");

/**
 * Class WLT_list_Table
 */
class ListTable extends WP_List_Table
{
    /**
     * Prepares the list of items for displaying.
     */
    public function prepare_items()
    {
        $orderBy = isset($_GET['orderby']) ? trim($_GET['orderby']) : "";
        $order = isset($_GET['order']) ? trim($_GET['order']) : "";
        $search_term = isset($_POST['s']) ? trim($_POST['s']) : "";

        $datas = $this->list_table_data($orderBy, $order, $search_term);

        // --------------------------------pagination---------------------------------------
        $per_page = 6;
        $current_page = $this->get_pagenum();
        $total_items = count($datas);
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page
        ));
        $this->items = array_slice($datas, (($current_page - 1) * $per_page), $per_page);

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
    }

    /**
     * Wp list table bulk actions 
     */
    public function get_bulk_actions()
    {
        return array(
            'delete' => 'Delete',
            'edit'   => 'Edit'
        );
    }

    /**
     * WP list table row actions
     */


    /**
     * Display columns datas
     */
    public function list_table_data($orderBy = '', $order = '', $search_term = '')
    {
        global $wpdb;
        if (!empty($search_term)) {

            $condition = array(
                'relation' => 'OR',
                array(
                    'key' => 'user_name',
                    'value' => $search_term,
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => 'user_email',
                    'value' => $search_term,
                    'compare' => 'LIKE'
                )
            );
            $args = array(
                'meta_query' => $condition
            );
            // -----------------------------------search box---------------------------------------

            $data_list = get_users($args);
            // echo "<pre>";
            // print_r($data_list);
            // echo "</pre>";
            $id = array();
            foreach ($data_list as $datas_list) {
                $id[] = $datas_list->ID;
            }

            $id = implode(',', $id);
            $all_datas = $wpdb->get_results("SELECT * FROM `wp_list_table` WHERE `user_name` LIKE '%$search_term%'");
        } else {
            if ($orderBy == 'phone' && $order == 'asc') {
                global $table_prefix;
                $table = $table_prefix . 'list_table';
                $all_datas = $wpdb->get_results("SELECT * FROM $table");
            } else {
                $all_datas = $wpdb->get_results("SELECT * FROM `wp_list_table`");
            }
        }

        $records_array = array();
        if (count($all_datas) > 0) {
            foreach ($all_datas as $index => $database) {

                $records_array[] = array(
                    "id" => $database->ID,
                    "user_name" => $database->user_name,
                    "user_email" => $database->user_email,
                    "phone" => $database->phone,
                    "image" => $database->image,
                    "date" => $database->date,
                    "status" => !empty($database->status) ? $database->status : 'approved',

                );
            }
        }
        return $records_array;
    }

    /**
     * Gets a list of all, hidden and sortable columns
     */
    public function get_hidden_columns()
    {
        return array("");
    }

    /**
     * Gets a list of sortable columns
     */
    public function get_sortable_columns()
    {
        return array(
            "date" => array("date", false)
        );
    }

    /**
     * Gets a list of columns.
     */
    public function get_columns()
    {
        $columns = array(
            "cb" => '<input type="checkbox" class=""/>',
            "id" => "ID",
            "user_name" => "Name",
            "user_email" => "Email",
            "phone" => "Phone",
            "image" => "Image",
            "date" => "Date",
            "status" => "Status"
        );
        return $columns;
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
                return $item[$column_name];
                break;
            case 'user_name':
                return $item[$column_name];
                break;
            case 'user_email':
                return $item[$column_name];
                break;
            case 'phone':
                return $item[$column_name];
                break;
            case 'image':
                return $item[$column_name];
                break;
            case 'date':
                return date("m/d/Y H:i A", strtotime($item[$column_name]));
            case 'status':
                return "<select name='status' id='status' onchange='getval(this)'>
                <option value='pending' selected>Pending</option>
                <option value='approve' data-id='" . $item['id'] . "' >Approved</option>
                <option value='rejected'>Rejected</option>
              </select>";
                break;
            case 'action':
            default:
                return "No List Found Value";
        }
    }
    // ---------------------------------------dynamic delete------------------------------------------
    public function process_bulk_action()
    {
        global $wpdb;

        $action = $this->current_action();

        switch ($action) {

            case 'delete':
                $query = $wpdb->delete('wp_list_table', array('ID' => $_GET['id']));
                break;

            case 'edit':
                break;

            default:
                // do nothing or something else
                return;
                break;
        }

        return;
    }


    // ---------------add popup click on edit btn-----------------------

    function column_user_name($item)
    {
        global $wpdb;

        $query = $wpdb->get_row("SELECT * FROM `wp_list_table` WHERE `ID`=" . $item['id'] . "");
        $name = $query->user_name;
        $email = $query->user_email;
        $phone = $query->phone;
        $image = $query->image;
        $date = $query->date;

        $actions = array(
            'edit' => '<a href="javascript:void(0)" class="thickbox" data-toggle="modal" data-target="#myModal_' . $item['id'] . '">Edit</a>
                <div class="modal mt-5" id="myModal_' . $item['id'] . '">
                        <div class="modal-dialog">
                            <div class="modal-content">

                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title">Update Detail</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body">
                                <form id="editpopup">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputName">Name</label>
                                        <input type="text" class="form-control" id="name" value="' . $name . '" placeholder="Name">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail">Email</label>
                                        <input type="email" class="form-control" id="email" value="' . $email . '" placeholder="Email">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPhone">Phone</label>
                                        <input type="number" class="form-control" id="number" value="' . $phone . '" placeholder="Phone no.">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputImage">Image</label>
                                        <input type="file" class="form-control" id="img" value="' . $image . '" placeholder="">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputDate">Date</label>
                                        <input type="Date" class="form-control" id="input_date" value="' . $date . '">
                                    </div>
                                
                                    <div class="form-group col-md-6" align="right"><br><br>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="button" id="update" data-id="' . $item['id'] . '" class="btn btn-success" >Save Change</button>
                                    </div>
                                </div>  
                            </div>
                        </div>
                    </div>
                    
                </div>',
            // -------------------------edit/delete----------------------------------
            'delete' => sprintf('<a href="?page=%s&action=%s&id=%s">Delete</a>', $_GET['page'], 'delete', $item['id']),
        );
        return sprintf('%1$s %2$s', $item['user_name'], $this->row_actions($actions));
    }

    /**
     * Rows check box
     */
    public function column_cb($items)
    {

        $top_checkbox = '<input type="checkbox" class="wlt-selected" />';
        return $top_checkbox;
    }
}

?>

<h3 class='mt-2'>Wp List Table</h3><br>
<form method='post' action='<?= $_SERVER['REQUEST_URI']; ?>' enctype='multipart/form-data'>
    <input type="file" name="import_file">
    <input type="submit" name="butimport" value="Import">
    <input type="submit" name="butexport" value="export">
</form>
<?php

function data_list_table()
{

    $table = new ListTable();
    $table->process_bulk_action();
    $table->prepare_items();
    // echo "<h4 id='statusMessage'></h4>";
    echo "<form method='POST' name='form_search' action='" . $_SERVER[' PHP_SELF'] . "?page=wp-list-table'>";
    $table->search_box("Search", "search_email");

    echo "</form>";
    $table->display();
}
data_list_table();
