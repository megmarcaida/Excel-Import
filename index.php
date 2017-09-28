<?php

require 'simple.xlsx.class.php';

$con = mysqli_connect('localhost','root','','fb_phone');
if (isset($_FILES['Filedata'])) {

    $file = $_FILES['Filedata']['tmp_name']; // UPLOADED EXCEL FILE

    $xlsx = new SimpleXLSX($file);

    list($cols, $rows) = $xlsx->dimension();

    if (mysqli_connect_errno())
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    foreach( $xlsx->rows() as $k => $r) { // LOOP THROUGH EXCEL WORKSHEET

        $r2 = '+' . mysqli_escape_string($con,$r[1]);
        $q = "INSERT INTO numbers(`number`,`status`) VALUE($r2,0)";

        $sql = mysqli_query($con,$q);


    } // IF ENDS HERE
    if ($sql){
        $qq = "UPDATE `numbers` SET `number`= CONCAT(' +',`number`)";
        $runSql = mysqli_query($con,$qq);
        echo '<div class="alert-success text-center success-lat">Successfully imported</div>';
    }
    else{
        echo '<script> alert("failed")</script>';
        echo $sql;
    }
} // FOR EACH LOOP

if(isset($_POST["truncate"])){

    $q = "TRUNCATE TABLE numbers";

    $sql = mysqli_query($con,$q);
}

if(isset($_POST["ExportType"]))
{
    switch ($_POST['accountStatus']){
        case '0':
        $sql = "SELECT * from numbers where status <> '0' order by id asc ";
        break;

        case 'Y':
            $sql = "SELECT * from numbers where status = 'Y' order by id asc ";
        break;
        case 'N':
            $sql = "SELECT * from numbers where status = 'N' order by id asc ";
            break;
    }
    $result = mysqli_query($con,$sql);
   $count = mysqli_num_rows($result);
    if ($count>0) {
        $rows = array();
        while ($row = mysqli_fetch_array($result)) {
            array_push($rows, $row);
        }
        $data = $rows;
        switch ($_POST["ExportType"]) {
            case "export-to-excel" :
                // Submission from
                $filename = $_POST["ExportType"] . ".xls";
                header("Content-Type: application/vnd.ms-excel");
                header("Content-Disposition: attachment; filename=\"$filename\"");
                ExportFile($data);
                //$_POST["ExportType"] = '';
                exit();
            default :
                die("Unknown action : " . $_POST["action"]);
                break;
        }
    }
    else{
        echo "<script>alert('No result found.')</script>";
    }
}
function ExportFile($records) {
    $heading = false;
    if(!empty($records))
        foreach($records as $row) {
            if(!$heading) {
                // display field/column names as a first row
                echo implode("\t", array_keys($row)) . "\n";
                $heading = true;
            }
            echo implode("\t", array_values($row)) . "\n";
        }
    exit;
}

?>


<html>
<head>
    <title>Excel Importer</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        .link {padding: 10px 15px;background: transparent;border:#bccfd8 1px solid;border-left:0px;cursor:pointer;color:#607d8b}
        .disabled {cursor:not-allowed;color: #bccfd8;}
        .current {background: #bccfd8;}
        .first{border-left:#bccfd8 1px solid;}
        .question {font-weight:bold;}
        .answer{padding-top: 10px;}
        #pagination{margin-top: 20px;padding-top: 30px;border-top: #F0F0F0 1px solid;}
        .dot {padding: 10px 15px;background: transparent;border-right: #bccfd8 1px solid;}
        #overlay {background-color: rgba(0, 0, 0, 0.6);z-index: 999;position: absolute;left: 0;top: 0;width: 100%;height: 100%;display: none;}
        #overlay div {position:absolute;left:50%;top:50%;margin-top:-32px;margin-left:-32px;}
        .page-content {padding: 20px;margin: 0 auto;}
        .pagination-setting {padding:10px; margin:5px 0px 10px;border:#bccfd8  1px solid;color:#607d8b;}
        .success-lat{padding: 10px;}
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="col-xs-12 col-md-8 col-lg-8">

        <div class="panel panel-default">
            <div class="panel-heading"><!-- Excel File Uploader -->엑셀 파일 업로더</div>
            <div class="panel-body">
                <form method="POST"  action="" enctype="multipart/form-data">

                        <div class="form-group">
                            <label for="excel"><!-- Upload file here -->파일을 여기에 업로드해:</label>
                            <input type="file" onchange="disableBtn()" required class="form-control" name="Filedata" id="excel">
                        </div>
                        <button type="submit" disabled="true" id="uploadBtn" class="btn btn-primary"><!--Submit-->제출</button>


                </form>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-4 col-lg-4">
        <div class="panel panel-default">
            <div class="panel-heading"><!-- Actions -->행동</div>
                <div class="panel-body">
                    <div class="col-xs-12 col-md-6 col-lg-6">
                        <form action="" method="post">
                            <button type="submit" disabled class="btn btn-warning" name="truncate"><!-- Truncate Table -->트렁크 테이블</button>
                        </form>
                    </div>
                    <div class="col-xs-12 col-md-6 col-lg-6">
                        <div class="btn-group pull-right">
                            <div class="form-group">
                                Export Status : <select class="form-control" name="status" id="status">
                                    <option value="0">All</option>
                                    <option value="Y">Active</option>
                                    <option value="N">Inactive</option>
                                </select>
                            </div>

                            <button type="button" class="btn btn-info"><!-- Action -->행동</button>

                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu" id="export-menu">
                                <li id="export-to-excel"><a href="#">Export to excel</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

        </div>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" id="export-form">
            <input type="hidden" value='' id='hidden-type' name='ExportType'/>
            <input type="hidden" value='' id='accountStatus' name='accountStatus'/>
        </form>
    </div>
    <div class="col-xs-12">

            <div id="pagination-result">

            </div>
    </div>

</div>
</body>
<script>
   
    function getresult(url) {
        $.ajax({
            url: url,
            type: "GET",
            /*data:  {rowcount:$("#rowcount").val(),"pagination_setting":$("#pagination-setting").val()},*/
            beforeSend: function(){$("#overlay").show();},
            success: function(data){
                $("#pagination-result").html(data);
            },
            error: function()
            {}
        });
    }
</script>
<script type="text/javascript">
    function disableBtn() {
        var uploadid = document.getElementById('uploadBtn')
        uploadid.disabled = false;

    }
</script>
<script>
    getresult("getNumbers.php");
    setInterval(function(){getresult("getNumbers.php")},5000);
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        jQuery('#export-to-excel').bind("click", function() {

            var target = $(this).attr('id');
            var status = document.getElementById('status');
            switch(target) {
                case 'export-to-excel' :
                    $('#hidden-type').val(target);
                    $('#accountStatus').val(status.value);
                    //alert($('#hidden-type').val());
                    $('#export-form').submit();
                    $('#hidden-type').val('');
                    break;
            }
        });
    });
</script>
</html>
