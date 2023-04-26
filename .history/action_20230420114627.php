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

        $html = "<html><body>";
        $stmt = $connect->query("SELECT * FROM invoice where id=".$_POST["id"] );
        while ($row = $stmt->fetch()) {

            $html .='<table width="100%" style="font-family: sans-serif;" cellpadding="10"> 
                            <tr>
                                <td width="100%" style="text-align: center; font-size: 20px; font-weight: bold; padding: 0px;">
                                    INVOICE
                                </td>
                            </tr>
                            <tr>
                                <td height="10" style="font-size: 0px; line-height: 10px; height: 10px; padding: 0px;">&nbsp;</td>
                            </tr>
                        </table> 
                        <br> 
                        <table width="100%" style="font-family: sans-serif; font-size: 14px;" >
                        <tr>
                            <td>
                                <table width="30%" align="left" style="font-family: sans-serif; font-size: 14px;" >
                                    <tr>
                                        <td style=" padding: 0px 8px; line-height: 20px;">'.$row['client'].' </br>'.$row['client_address'].' </td> 
                                    </tr> 
                                </table>
                                <table width="30%" align="left" style="font-family: sans-serif; font-size: 14px;" >
                                    <tr>
                                        <td style="padding: 0px; line-height: 20px;">&nbsp;</td>
                                    </tr>
                                </table>
                                <table width="40%" align="right" style="font-family: sans-serif; font-size: 14px;" >
                                    <tr>
                                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"><strong>Invoice '.$row['invoice_number'].'</strong></td>
                                      </tr>
                                    <tr>
                                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"> Invoice Date: </td>
                                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">'.$row['date'].'</td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"> Payment Terms: </td>
                                        <td style="border: 1px #eee solid;  padding: 0px 8px; line-height: 20px;">'.$row['invoice_tax_total'].'</td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">Due Date: </td>
                                        <td style="border: 1px #eee solid;  padding: 0px 8px; line-height: 20px;">'.$row['due_date'].'</td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"> Amount Due: </td>
                                        <td style="border: 1px #eee solid;  padding: 0px 8px; line-height: 20px;">'.$row['invoice_total'].'</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <br>
 
        <table class="items" width="100%" style="border: 1px #eee solid; font-size: 14px; border-collapse: collapse;" cellpadding="8">
            <thead>
                <tr>
                    <td width="30%" style="border: 1px #eee solid;text-align: left;"><strong>Product</strong></td>
                    <td width="10%" style="border: 1px #eee solid;text-align: left;"><strong>Code</strong></td>
                    <td width="13%" style="border: 1px #eee solid;text-align: left;"><strong>Pack</strong></td>
                    <td width="13%" style="border: 1px #eee solid;text-align: left;"><strong>Price</strong></td>
                    <td width="13%" style="border: 1px #eee solid;text-align: left;"><strong>Qty</strong></td>
                    <td width="13%" style="border: 1px #eee solid;text-align: left;"><strong>Total</strong></td>
                    <td width="12%" style="border: 1px #eee solid;text-align: left;"><strong>Amount</strong></td> 
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px #eee solid;padding: 0px 7px; line-height: 20px;">'.$row['product'].'</td>
                    <td style="border: 1px #eee solid;padding: 0px 7px; line-height: 20px;">'.$row['product_code'].'</td>
                    <td style="border: 1px #eee solid;padding: 0px 7px; line-height: 20px;">'.$row['size'].'</td>
                    <td style="border: 1px #eee solid;padding: 0px 7px; line-height: 20px;">$'.$row['unit_price'].'</td>
                    <td style="border: 1px #eee solid;padding: 0px 7px; line-height: 20px;">'.$row['quantity'].'</td>
                    <td style="border: 1px #eee solid;padding: 0px 7px; line-height: 20px;">'.$row['total'].'</td>
                    <td style="border: 1px #eee solid;padding: 0px 7px; line-height: 20px;">$'.$row['invoice_total'].'</td>
                </tr> 
            </tbody>
        </table>
        <br>
        <table width="100%" style="font-family: sans-serif; font-size: 14px;" >
            <tr>
                <td>
                    <table width="60%" align="left" style="font-family: sans-serif; font-size: 14px;" >
                        <tr>
                            <td style="border: 1px #eee solid;padding: 0px; line-height: 20px;">Thank you for your business.
                            Payment for each invoice is due 30 days after delivery.</td>
                        </tr>
                    </table>
                    <table width="40%" align="right" style="font-family: sans-serif; font-size: 14px;" >
                        <tr>
                            <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">Subtotal</td>
                            <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">$'.$row['invoice_sub_total'].'</td>
                        </tr>
                        <tr>
                            <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">Bottle Deposit</td>
                            <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">$0.00</td>
                        </tr>
                        <tr>
                            <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">Sales Tax Total</td>
                            <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">$'.$row['invoice_tax_total'].'</td>
                        </tr>
                        <tr>
                            <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"><strong>TOTAL</strong></td>
                            <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;">$'.$row['invoice_total'].'</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <table width="100%" style="font-family: sans-serif; font-size: 14px;" >
            <br>
            <tr>
                <td>
                    <table width="25%" align="left" style="font-family: sans-serif; font-size: 14px;" >
                        <tr>
                            <td style="padding: 0px; line-height: 20px;">
                                <img src="img/protected.png" alt="protected" style="display: block; margin: auto;">
                            </td>
                        </tr>
                    </table>
                    <table width="50%" align="left" style="font-family: sans-serif; font-size: 13px; text-align: center;" >
                        <tr>
                            <td style="padding: 0px; line-height: 20px;">
                                <strong>Company Name</strong>
                                <br>
                                ABC AREA
                                <br>
                                Tel: +00 000 000 0000 | Email: info@companyname.com
                                <br>
                                Company Registered in Country Name. Company Reg. 12121212.
                                <br>
                                VAT Registration No. 021021021 | ATOL No. 1234
                            </td>
                        </tr>
                    </table>
                    <table width="25%" align="right" style="font-family: sans-serif; font-size: 14px;" >
                        <tr>
                            <td style="padding: 0px; line-height: 20px;">
                                <img src="img/abtot.png" alt="abtot" style="display: block; margin: auto;">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <br>
        </table>';

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
