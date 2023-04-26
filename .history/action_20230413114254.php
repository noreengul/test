<?php
ini_set('display_errors', 1);
//action.php

include('database_connection.php');

if($_POST['action'] == 'import')
{
    $rows = [];
    $fileName = $_FILES["file"]["tmp_name"];
    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($fileName, "r");
        $importCount = 0;
        while (($row = fgetcsv($file, 10000, ",")) !== FALSE) {
            $rows[] = $row;
//            fputcsv($file, $row);
//            print_r($column);die;
//            $vendor=$column[0];
//            $invoice_number=$column[1];
//            $stmt = $connect->prepare("INSERT INTO invoice (invoice_number,  $invoice_number) VALUES (?, ? )");
//            $stmt->bind_param("sss", $vendor, $invoice_number);
//
//            $vendor = "John";
//            $lastname = "Doe";
//            $stmt->execute();

        }
    }

    unset($rows[0]);

   // $statement = $connect->prepare("INSERT INTO invoice (  `vendor`, `invoice_number`, `client`, `client_address`, `date`, `due_date`, `currency`, `invoice_sub_total`, `invoice_tax_total`, `invoice_total`, `product_match`, `tipalti_approved`, `is_approved`, `product`, `product_code`, `quantity`, `size`, `unit_price`, `total`, `Observations`) VALUES (?,?)");
   // try {
        //$connect->beginTransaction();
        foreach ($rows as $row)
        {


            $row[4]=  "2023-04-27";
            $row[5]= "2023-04-27";
            $row[17]= $row[20]=='Y'?1:0;

            $statement = $connect->prepare("INSERT INTO invoice (  vendor, invoice_number,client, client_address, date, due_date, currency, invoice_sub_total, invoice_tax_total, invoice_total,  product, product_code, quantity, size, unit_price,total,product_match,tipalti_approved,is_approved, Observations) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

            $statement->execute($row);

        }
       // $connect->commit();
   // }catch (Exception $e){
      //  $connect->rollback();
       // throw $e;
    //}
    print_r($rows);



}

if($_POST['action'] == 'edit')
{
    $data = array(
        ':vendor'  => $_POST['vendor'],
        ':invoice_number'  => $_POST['invoice_number'],
        ':client'   => $_POST['client'],
        ':id'    => $_POST['id']
    );

    $query = "
 UPDATE invoice 
 SET vendor = :vendor, 
 invoice_number = :invoice_number, 
 client = :client 
 WHERE id = :id
 ";
    $statement = $connect->prepare($query);
    $statement->execute($data);
    echo json_encode($_POST);
}

if($_POST['action'] == 'delete')
{

    require "pdfcrowd.php";

    try
    {

        $html = "<html><body><h1>Invoice Details</h1>";
        $stmt = $connect->query("SELECT * FROM invoice where id=".$_POST["id"] );
        while ($row = $stmt->fetch()) {
            $html .= "<h2>Vendor: ".$row['vendor']." </h2>";
            $html .= "<h2>Invoice Number: ".$row['invoice_number']." </h2>";
            $html .= "<h2>Client: ".$row['client']." </h2>";
            $html .= "<h2>Client Address: ".$row['client_address']." </h2>";
            $html .= "<h2>Date: ".$row['date']." </h2>";
            $html .= "<h2>Due Date: ".$row['due_date']." </h2>";
            $html .= "<h2>Currency: ".$row['currency']." </h2>";
            $html .= "<h2>Invoice Sub Total: ".$row['invoice_sub_total']." </h2>";
            $html .= "<h2>Invoice Tax Total: ".$row['invoice_tax_total']." </h2>";
            $html .= "<h2>Invoice Total: ".$row['invoice_total']." </h2>";
            $html .= "<h2>Product: ".$row['product']." </h2>";
            $html .= "<h2>Product Code: ".$row['product_code']." </h2>";
            $html .= "<h2>Quantity: ".$row['quantity']." </h2>";
            $html .= "<h2>Size: ".$row['size']." </h2>";
            $html .= "<h2>Unit Price: ".$row['unit_price']." </h2>";
            $html .= "<h2>Toal: ".$row['total']." </h2>";
            $html .= "<h2>Is Approved: ".$row['is_approved']." </h2>";
        }

        $html .="</body></html>";
        // create the API client instance
        $client = new \Pdfcrowd\HtmlToPdfClient("noreengul24", "abbbc07392949ee6dbbee62423d08bcc");

        // run the conversion and write the result to a file
        $client->convertStringToFile($html, "hello.pdf");

        echo json_encode($_POST);
    }
    catch(\Pdfcrowd\Error $why)
    {
        // report the error
        error_log("Pdfcrowd Error: {$why}\n");

        // rethrow or handle the exception
        throw $why;
    }

    //$query = "
 //DELETE FROM invoice
 //WHERE id = '".$_POST["id"]."'
 //";
  //  $statement = $connect->prepare($query);
  //  $statement->execute();
   // echo json_encode($_POST);
}


?>
