<?php
$date = $_GET['date'];

require_once("setPDF.php");

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false); 
$pdf->SetTitle('DAILY REPORT of '.$date);
$pdf->SetSubject('DAILY REPORT of '.$date);
$pdf->SetHeaderData('img-logo.png', '80', '', '');
$pdf->SetHeaderMargin('5');
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, '30', PDF_MARGIN_RIGHT);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
$pdf->setLanguageArray($l); 
$pdf->SetFont('freeserif', '', 10);

$pdf->AddPage();
$array = '';
$credit = 0;
$i = 1;
$html = '<h1  style="font-size:60px">สรุปรายรับเครดิตของ '.$date.' </h1>';
$html .= '<table><tr><td width="20%">No.</td><td width="30%">Date</td><td width="50%" align="right">THB</td></tr>';

$sql = "SELECT `grand_total`, `payment`, `realtimes`, `checkout` FROM `invoice_bill` WHERE DATE_FORMAT(`checkout`, '%Y-%m')='$date' AND `payment`='2' ORDER BY `checkout`";
$query = mysql_query($sql);
while($result = mysql_fetch_assoc($query)){
	if($result['realtimes'] != '0000-00-00 00:00:00'){
		$times = $result['realtimes'];
	}else{
		$times = $result['checkout'];
	}
	$html .= '<tr><td width="20%">'.$i.'</td><td width="30%">'.$times.'</td><td width="50%" align="right">'.$result['grand_total'].'</td></tr>';
	$credit = $credit+$result['grand_total'];
	$i++;
}
$html .= '<tr><td width="20%"></td><td width="30%" align="right"><b>TOTAL</b></td><td width="50%" align="right"><b>'.$credit.'</b></td></tr>';
$html .= '</table>';

$pdf->writeHTML($html, true, 0, true, 0);
$pdf->Output('creditreport-'.$date.'.pdf', 'I');
?>