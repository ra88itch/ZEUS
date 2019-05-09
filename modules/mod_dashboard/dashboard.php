<?php
defined('R88PROJ') or die ($system_error);
?>
<section id="sec-dashboard">
	<div class="warp">

		<div class="onerow">
			<div class="col3">
				<a href="?mod=restaurant"><img src="images/mod_restaurant.png"></a>
			</div>
			<div class="col3">
				<a href="?mod=snooker"><img src="images/mod_snooker.png"></a>
			</div>
			<div class="col3">
				<a href="?mod=massage"><img src="images/mod_massage.png"></a>
			</div>
			<div class="col3">
				<a href="?mod=sauna"><img src="images/mod_sauna.png"></a>
			</div>
			<div class="clear"></div>
		</div>
		<div class="onerow">
			<div class="col3">
				<a href="?mod=member"><img src="images/mod_member.png"></a>
			</div>
			<div class="col3">
				<a href="?mod=cashorder"><img src="images/mod_cashorder.png"></a>
			</div>
			<div class="col3">
				<a href="?mod=customer_detail"><img src="images/mod_customer_detail.png"></a>
			</div>
			<div class="col3">
				<a href="?mod=check_bill"><img src="images/mod_check_bill.png"></a>
			</div>		
			<div class="clear"></div>
		</div>
		<div class="onerow">
			<div class="col3">
				<a href="?mod=locker"><img src="images/mod_locker.png"></a>
			</div>
			<div class="col3">
				<a href="?mod=set_card"><img src="images/mod_set_card.png"></a>
			</div>
			<div class="col3">
				<a href="?mod=set_snook"><img src="images/mod_set_snook.png"></a>
			</div>
			<div class="col3">
				<a href="?mod=c2c"><img src="images/mod_cash.png"></a>
			</div>
			<div class="clear"></div>
		</div>
		<div class="onerow">
			<div class="col3">
				<a href="?mod=ecoupon"><img src="images/mod_ecoupon.png"></a>
			</div>
			<div class="col3">
				<a href="?mod=boxing"><img src="images/mod_boxing.png"></a>
			</div>
			<div class="col3">
				<a href="?mod=boxing_ecoupon"><img src="images/mod_boxing_ecoupon.png"></a>
			</div>
			<div class="col3">
				<a href="?mod=cancel_order"><img src="images/mod_cancel_order.png"></a>
			</div>
			<div class="clear"></div>
		</div>



		<?php if(chkPermission('admin')==true){ ?>
		<div class="onerow">
			<div class="result">
				<h2>ระบบสำหรับผู้ดูแล</h2>
				<div class="col3">
					<a href="?mod=report_month"><p>ระบบสรุปรายรับ<br>ประจำเดือน</p></a>
				</div>	
				<div class="col3">
					<a href="?mod=report_cashier"><p>ระบบสรุปรายรับ<br>ประจำวัน</p></a>
				</div>
				<div class="col3">
					<a href="?mod=report_month_credit"><p>ระบบสรุปรายรับเครดิต<br>รวมแต่ละเดือน</p></a>
				</div>
				<div class="col3">
					<a href="?mod=account"><p>ระบบบริหารจัดการ<br>ผู้ใช้งาน</p></a>
				</div>
				
				<?php /*<div class="col3">
					<a href="?mod=massager_money"><p>ระบบบริหารจัดการ<br>ค่าชั่วโมงพนักงานนวด</p></a>
				</div>*/ ?>
				
				<div class="clear"></div>
				<br>
			</div>
		</div>
		<div class="onerow">
			<div class="result">
				<div class="col3">
					<a href="?mod=zone_sauna"><p>ระบบจัดการบัตร<br>ซาวน่า</p></a>
				</div>
				<div class="col3">
					<a href="?mod=zone_fitness"><p>ระบบจัดการบัตร<br>ฟิตเนส</p></a>
				</div>
				<div class="col3">
					<a href="?mod=bill_history"><p>ระบบดูรายการชำระเงิน<br>ย้อนหลัง</p></a>
				</div>
				<div class="col3">
					<a href="?mod=entertainer"><p>ระบบบริหารจัดการ<br>ผู้มีสิทธิ์เอนเตอร์เทน</p></a>
				</div>	
				<div class="clear"></div>
				<br>
			</div>
		</div>
		<div class="onerow">
			<div class="result">			
				<div class="col3">
					<a href="?mod=stock"><p>ระบบบริหารจัดการ<br>วัตถุดิบ</p></a>
				</div>	
				<div class="col3">
					<a href="?mod=store"><p>ระบบบริหารจัดการ<br>สินค้าขายหน้าร้าน</p></a>
				</div>	
				<div class="col3">
					<a href="?mod=asset"><p>ระบบบริหารจัดการ<br>อุปกรณ์ภายในร้าน</p></a>
				</div>		
				<div class="col3">
					<a href="?mod=massager"><p>ระบบบริหารจัดการ<br>พนักงานนวด</p></a>
				</div>
				<div class="clear"></div>
				<br>
			</div>
		</div>
		<div class="onerow">
			<div class="result">
				<div class="col3">
					<a href="?mod=chart_daily"><p>รายรับประจำวัน<br>แยกแผนก</p></a>
				</div>
				<div class="col3">
					<a href="?mod=mcancel_order"><p>ระบบตรวจสอบ<br>รายการอาหารที่ถูกยกเลิก</p></a>
				</div>
				<div class="col3">
					<a href="?mod=clear"><p>ระบบลบรายการ<br>ใส่ข้อมูลผิดพลาด</p></a>
				</div>
				<div class="col3">
					<a href="?mod=report_daily"><p>รายการสั่งอาหารประจำวัน<br>แยกแผนก</p></a>
				</div>
							
				<div class="clear"></div>
				<br>
			</div>
		</div>	
		<div class="onerow">
			<div class="result">
				<div class="col3">
					<a href="?mod=mecoupon"><p>ระบบบริหารจัดการ<br>eCoupon</p></a>
				</div>	
				<div class="col3">
					<a href="?mod=mboxing_ecoupon"><p>ระบบบริหารจัดการ<br>ยิมมวยคูปอง</p></a>
				</div>
							
				<div class="clear"></div>
				<br>
			</div>
		</div>						
		<?php } ?>

		<?php if(chkPermission('root')==true){ ?>
		<div class="onerow">
			<div class="result">
				<h2>ระบบสำหรับผู้บริหาร</h2>
				<div class="col3">
					<a href="?mod=price"><p>ระบบกำหนดราคา<br>ค่าบริการ</p></a>
				</div>
				<div class="col3">
					<a href="?mod=mdiscount"><p>ระบบกำหนดสิทธิ์<br>ส่วนลดค่าอาหาร</p></a>
				</div>
				<div class="col3">
					<a href="?mod=mcategory"><p>ระบบจัดการหมวดหมู่<br>เมนูอาหาร</p></a>
				</div>
				<div class="col3">
					<a href="?mod=mmenu"><p>ระบบบริหารจัดการ<br>เมนูอาหาร</p></a>
				</div>	
				<div class="clear"></div>
				<br>
			</div>
		</div>
		<div class="onerow">
			<div class="result">
				<div class="col3">
					<a href="?mod=boxing_price"><p>ระบบกำหนดราคา<br>ค่าเรียนมวย</p></a>
				</div>
				<div class="clear"></div>
				<br>
			</div>
		</div>
		<?php } ?>
	</div>
</section>