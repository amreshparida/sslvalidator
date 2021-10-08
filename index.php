<?php date_default_timezone_set("Asia/Kolkata"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>SSl Validator</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="SSl Validator">
  <meta name="keywords" content="SSl Validator, SSL Certificate validate, ssl validate, amaresh parida, ssl expiry, ssl verify">
  <meta name="author" content="Amaresh Parida">
  <link rel="icon" href="assets/favicon.ico" sizes="16x16" type="image/png"> 
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.11.3/b-2.0.1/b-html5-2.0.1/b-print-2.0.1/datatables.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.11.3/b-2.0.1/b-html5-2.0.1/b-print-2.0.1/datatables.min.js"></script>
</head>
<body>

<div class="jumbotron text-center" style="background-color: #E9EC9B;">

<div aria-live="polite" aria-atomic="true" style="position: relative; ">
<div class="toast" style="position: absolute; top: 0; right: 0;" data-autohide="false">
    <div class="toast-header">
      <strong class="mr-auto text-primary">Download URL Excel Sheet template</strong>
    </div>
    <div class="toast-body">
    <a href="sample/urlList.xlsx" id="downloadExcel" class="btn btn-info" role="button"><i class="fa fa-cloud-download"></i> Download Template</a>
    </div>
  </div>
</div>
  <h1>SSL Certificate Validator</h1>
  <div class="row justify-content-center">
      <div class="col-sm-6">
                 <form method="post" enctype="multipart/form-data">
                        <div class="custom-file">
                            <input required name="excel" type="file" class="custom-file-input" id="customFile" accept=".xlsx, .xls">
                            <label class="custom-file-label" for="customFile">Choose URL Excel Sheet</label>
                        </div>
                        <div class="form-group mt-4">
                            <button type="submit" name="excelUpload" class="btn btn-success btn-block">Upload</button>
                        </div>
                </form>
      </div>
  </div>
</div>
  

<?php


require 'vendor/autoload.php';

if(isset($_POST['excelUpload'])){

    $url_arr = array();

    

    $name       = $_FILES['excel']['name'];  
    $uploadFilePath  = $_FILES['excel']['tmp_name'];  

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($uploadFilePath);

        $worksheet = $spreadsheet->getActiveSheet();
        $worksheetArray = $worksheet->toArray();
        for ($i=1; $i<count($worksheetArray); $i++) {

            $url_arr[] = $worksheetArray[$i][0];

        }
        


}

?>

<?php if(isset($url_arr)) { ?>

    <div class="container mb-5">

  <div class="row mb-5">
    <div class="col-sm-12 mb-5">
      <h3 class="px-1">SSL Certificate Validator Result</h3>  
      <div class="table-responsive my-4">                
      <table id="example" class="table table-bordered text-center">
    <thead>
      <tr>
        <th>URL</th>
        <th>Valid From</th>
        <th>Valid To</th>
        <th>Is Expired</th>
      </tr>
    </thead>
    <tbody>
      

<?php 

foreach($url_arr as $url)
{

    $orignal_parse = parse_url($url, PHP_URL_HOST);
    $get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
    $read = stream_socket_client("ssl://".$orignal_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
    if($read)
    {
    $cert = stream_context_get_params($read);
    $certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);

    $valid_from = date("d/m/Y",$certinfo['validFrom_time_t']);
    $valid_to = date("d/m/Y",$certinfo['validTo_time_t']);
    $today = date("d/m/Y");
    $invalid = 0;
    }
    else
    {
        $valid_from = "<span style='color:red;'>N/A</span>";
        $valid_to = "<span style='color:red;'>N/A</span>";
        $invalid = 1;
    }
?>

      <tr>
        <td><?php echo $url; ?></td>
        <td><?php echo $valid_from; ?></td>
        <td><?php echo $valid_to; ?></td>
        <td>
            <?php
            if($invalid == 0){
            $date = new DateTime(DateTime::createFromFormat('m-d-Y H:i:s', $valid_to));

            $valid_to = $date->format('Y-m-d');

                if(strtotime($today) < strtotime($valid_to))
                {
                    echo "<span style='color:green;'>Not Expired</span>";
                }
                else
                {
                    echo "<span style='color:red;'>Expired</span>";
                }
            }
            else
            {
                echo "<span style='color:red;'>N/A</span>";
            }
            ?>
        </td>
      </tr>
<?php } ?>


    </tbody>
  </table>
</div>
    </div>
  </div>
  </div>
<?php } else { ?>

    <div class="container mb-5">

  <div class="row mb-5">
    <div class="col-sm-6 mb-5">
            <img style="width:100%; height:300px;" src="assets/sslvalidator.png">
    </div>

    <div class="col-sm-6 mb-5 px-5">

        <p class="px-5">
            <h4>How it works:</h4>
        </p>

        <p class="px-5">
        <ol type="1" class="px-5" style="font-size:20px;">
                <li><a href="#downloadExcel">Download</a> the sample URL Excel Sheet </li>
                <li>Fill the downloaded Excel Sheet's first column with your URL lsits</li>
                <li>Upload the fiilled Excel Sheet to our <a href="#customFile">SSL Validator</a></li>
                <li>SSL Validator result will appear.</li>
                <li>Download the result to your computer in Excel/PDF or Print the result.</li>
        </ol> 
        </p>

    </div>
</div>
</div>


    <?php } ?>


<footer>
    <div class="row mt-4">
                <div class="col-sm-12 text-center py-4" style="background-color: #E9EC9B; position: fixed; z-index:99999; left: 0; bottom: 0;">
                        Made With <i class="fa fa-heart pulse" style="color:blue;"></i> For <a style="font-weight:bold;" target="_blank" href="https://www.linkedin.com/in/yasasvi-kondapally-bb2b02152/">Yasasvi Kondapally</a> By <a target="_blank" href="https://www.linkedin.com/in/amaresh-parida-190017159/">Amaresh Parida</a>
                </div>
    </div>
</footer>


<script>

$('.toast').toast('show');


$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});

$(document).ready(function() {
    $('#example').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'excel', 'pdf', 'print'
        ]
    } );
} );



</script>


</body>
</html>
