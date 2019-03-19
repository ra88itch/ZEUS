<?php
defined('R88PROJ') or die ($system_error);

if(chkPermission('admin')!=true){
	session_destroy(); 
	header('Location: http://'.$_SERVER['HTTP_HOST']);
}
$jquery = '';
$ajax = '';
$html = '';

$sql = "SELECT * FROM `customer_type`";
$query = mysql_query($sql);	
while($results = mysql_fetch_assoc($query)){
	$jquery .= 'var discount_percent'.$results['id'].'		= $("#discount_percent'.$results['id'].'").val();
	';
	$ajax .= ', discount_percent'.$results['id'].':discount_percent'.$results['id'].'';
	$html .= '<tr>
		<td>'.$results['customertype_name'].'</td>
		<td><input type"text" id="discount_percent'.$results['id'].'" value="'.$results['discount_percent'].'"> %</td>
	</tr>';
}
?>
<script>
$(document).ready(function(){
	$('#submit').click(function(){
		setPrice();
	});
});
function setPrice(){
	<?php echo $jquery; ?>

	ajaxCall( 'api.php', finishResponse, { mod:'mdiscount', type:'setDiscount'<?php echo $ajax; ?> });
	
}
function finishResponse(response){
	location.reload();
}
</script>
<section id="sec-stock">
	<div class="warp">
		<div class="onerow">
			<div class="result">
				<div class="col12">
					<p style="cursor:default;">
						<table>
							<thead>
								<tr>
									<td width="400px">สิทธิ์</td>
									<td width="600px">ส่วนลด</td>
								</tr>
							</thead>
							<tbody>
								<?php echo $html; ?>
								
								<tr class="submit" style="text-align:left;"><td></td><td><input type="button" id="submit" value="SAVE"></td></tr>
							</tbody>
						</table>
						
					</p>
				</div>
			</div>
		</div>
	</div>
</section>