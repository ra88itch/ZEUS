<?php
function chkMassage($massage_type){
	switch($massage_type){
		case 3:
			return 'นวดสปา/น้ำมัน';
			break;
		default:
			return 'นวดแผนโบราณ';
			break;
	}
}



$date = $_GET['date'];
require_once("setPDF.php");

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false); 

$pdf->SetTitle('DAILY REPORT of DATE');
$pdf->SetSubject('DAILY REPORT of DATE');



$pdf->SetHeaderData('img-logo.png', '80', '', '');
$pdf->SetHeaderMargin('5');
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->SetMargins(PDF_MARGIN_LEFT, '30', PDF_MARGIN_RIGHT);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
$pdf->setLanguageArray($l); 
$pdf->SetFont('freeserif', '', 10);

$sql = "SELECT * FROM `zone_category` WHERE `id` BETWEEN 16 AND 18";
$query = mysql_query($sql);
while($result = mysql_fetch_assoc($query)){
	if($result['id']=='16'){
		$warranty = $result['charge'];
	}else if($result['id']=='17'){
		$msg = 	$result['charge'];
	}else if($result['id']=='18'){
		$spa = 	$result['charge'];
	}
}


$html = '<table><tr><td width="15%">หมายเลข</td><td width="35%">ประเภทบริการ</td><td width="35%">เวลา</td><td width="15%">เบิกจ่ายพนักงาน</td></tr>';
$employee_arr = array();
$sql = "SELECT (`times_in_min`/60) AS `hours`, `massage_type`, `employee_id`, `start` FROM `order_massage` WHERE DATE(`start`)='$date' AND `times_in_min`!='0' AND `massage_type`<'4' ORDER BY `employee_id`";
$query = mysql_query($sql);
while($result = mysql_fetch_assoc($query)){
	$chkMassage = chkMassage($result['massage_type']);
	if($result['massage_type']==3){
		$charge_per_hour = $spa;
	}else{
		$charge_per_hour = $msg;
	}
	$html .= '<tr><td width="15%">'.$result['employee_id'].'</td><td width="35%">'.$chkMassage.'</td><td width="35%">'.$result['start'].'</td><td width="15%">'.($result['hours']*$charge_per_hour).'</td></tr>';
	if(!in_array($result['employee_id'], $employee_arr)){
		array_push($employee_arr, $result['employee_id']);
	}
}

/*$sql = "SELECT `massager_no` FROM `massage_money` WHERE `date`='$date'";
$query = mysql_query($sql);
$result = mysql_fetch_assoc($query);
$massager_no = explode(',', trim($result['massager_no']));
$count = count($massager_no);
for($i=0; $i < $count; $i++){
	if(!in_array($massager_no[$i], $employee_arr)){
		$html .= '<tr><td width="15%">'.$massager_no[$i].'</td><td width="35%">เงินประกันรายได้</td><td width="35%"></td><td width="15%">'.$warranty.'</td></tr>';
	}
}*/
$html .= '</table>';


$pdf->AddPage();

$htmlcontent=stripslashes($html);
$htmlcontent=AdjustHTML($htmlcontent);

$pdf->writeHTML($htmlcontent, true, 0, true, 0);

$pdf->Output('massager-'.$date.'.pdf', 'I');
?>