<?php
defined('R88PROJ') or die ($system_error);

if(chkPermission('root')!=true){
	session_destroy(); 
	header('Location: http://'.$_SERVER['HTTP_HOST']);
}

?>
<section id="sec-menu">
	<div class="warp">		
		<div class="onerow">
			<div class="result">
				<div class="col12">
					<p>
						<table>
							<thead>
								<tr>
									<td></td>
									<td>หมวดหมู่อาหาร </td>									
									<td>ส่วนลดสมาชิก</td>
									<td>ส่วนลดสำหรับพนักงาน</td>
									<td>แก้ไข</td>
								</tr>
							</thead>
							<tbody>
							<?php							
							$start_on = ($_GET['page']-1)*20;
							$sql = "SELECT * FROM `menu_type_cooking`";
							
							$query = mysql_query($sql);
							while($row = mysql_fetch_assoc($query)){								
							?>
								<tr>
									<td></td>
									<td><?php echo $row['type_cooking'], $future; ?></td>								
									<td><span class="icon24 statusmember status<?php echo ($row['discount_member']==0 ? ' lock' : '' ); ?>" id="member<?php echo $row['id']; ?>"></span></td>
									<td><span class="icon24 statusemployee status<?php echo ($row['discount_employee']==0 ? ' lock' : '' ); ?>" id="employee<?php echo $row['id']; ?>"></span></td>
									<td><span class="icon24 edit" id="edit<?php echo $row['id']; ?>"></span></td>
								</tr>
							<?php
							}
							?>
							</tbody>
						</table>
					</p>
				</div>
				<div id="add" class="add">เพิ่มรายการอาหาร</div>
			</div>
		</div>
	</div>
</section>