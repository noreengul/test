<?php

//fetch.php

include('database_connection.php');

$column = array("id", "vendor", "invoice_number", "client","client_address");

$query = "SELECT * FROM invoice ";

if(isset($_POST["search"]["value"]))
{
    $query .= '
 WHERE vendor LIKE "%'.$_POST["search"]["value"].'%" 
 OR invoice_number LIKE "%'.$_POST["search"]["value"].'%" 
 OR client LIKE "%'.$_POST["search"]["value"].'%" 
 ';
}

if(isset($_POST["order"]))
{
    $query .= 'ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}
else
{
    $query .= 'ORDER BY id DESC ';
}
$query1 = '';

if($_POST["length"] != -1)
{
    $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $connect->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();

$data = array();

foreach($result as $row)
{
    $sub_array = array();
    $sub_array[] = $row['id'];
    $sub_array[] = $row['vendor'];
    $sub_array[] = $row['invoice_number'];
    $sub_array[] = $row['client'];
    $sub_array[] = $row['client_address'];
    $data[] = $sub_array;
}

function count_all_data($connect)
{
    $query = "SELECT * FROM invoice";
    $statement = $connect->prepare($query);
    $statement->execute();
    return $statement->rowCount();
}

$output = array(
    'draw'   => intval($_POST['draw']),
    'recordsTotal' => count_all_data($connect),
    'recordsFiltered' => $number_filter_row,
    'data'   => $data
);

echo json_encode($output);

?>
