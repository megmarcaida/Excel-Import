<?php
require_once("dbcontroller.php");
$db_handle = new DBController();


$sql = "SELECT * from numbers order by id asc ";
$activecountSql = "SELECT * from numbers where status = 'Y'";
$inactivecountSql = "SELECT * from numbers where status = 'N'";


$faq = $db_handle->runQuery($sql);
$active = $db_handle->numRows($activecountSql);
$inactive = $db_handle->numRows($inactivecountSql);
$accountschecked = $active + $inactive;
$allcount = $db_handle->numRows($sql);

$output = '<div class="col-xs-12 col-md-12 col-lg-12"><b class="text-primary">Active Accounts :</b> <span class="badge badge-primary">'.$active.'</span>&nbsp; &nbsp;&nbsp;';
$output .= '<b class="text-danger">Inactive Accounts :</b> <span class="badge badge-secondary">'.$inactive.'</span>&nbsp; &nbsp;&nbsp;';
$output .= '<b class="text-info">Accounts Checked :</b> <span class="badge badge-secondary">'.$accountschecked.'</span>&nbsp; &nbsp;&nbsp;';
$output .= '<b class="text-warning">Remaining Accounts :</b> <span class="badge badge-secondary">'.($allcount - $accountschecked).'</span>&nbsp; &nbsp;&nbsp;';
$output .= '<b class="text-success">Total Accounts :</b> <span class="badge badge-secondary">'.$allcount.'</span></div><br><br>';
$output .= '<table class="table table-responsive table-condensed"><tr><th>#</th><th>번호</th> <th>스타투스</th> <th>프로필 링크</th></tr>';
$i=1;
if ($faq) {
    foreach ($faq as $k => $v) {
        switch ($faq[$k]["status"]){
            case "Y":
                $output .= '<tr class="bg-success">';
            break;
            case "N":
                $output .= '<tr class="bg-danger">';
                break;
            case "0":
                $output .='<tr class="bg-default">';
                break;
        }
        $output .= '<td>' . $i++ . '</td>';
        $output .= '<td>' . $faq[$k]["number"] . '</td>';
        $output .= '<td>' . $faq[$k]["status"] . '</td>';
        $output .= '<td>' . $faq[$k]["url"] . '</td>';
        $output .= '</tr>';
    }
    $output .= "</table>";
}
print $output;

?>
