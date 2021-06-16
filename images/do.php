<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
/*
// reCAPTCHA secret key
define('SecretKey', '6LcfSVwUAAAAAMSc39ybxSyX09NsBQbq5Dq2rON7');

// allowed only POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $query_params = [
    'secret' => SecretKey,
    'response' => filter_input(INPUT_POST, 'g-recaptcha-response'),
    'remoteip' => $_SERVER['REMOTE_ADDR']
  ];
  $url = 'https://www.google.com/recaptcha/api/siteverify?'.http_build_query($query_params);
  $result = json_decode(file_get_contents($url), true);

  if ($result['success']) {
    // TODO, when reCAPTCHA verify successfully
  } else {
    // TODO, when reCAPTCHA failed
	die("reCAPTCHA failed");
  }
}else{
    // ไม่มีการส่งข้อมูลมา
	die("-No data submit.-");
}
*/
//เชื่อมต่อ Webservice
require_once("WebServiceClientLead.php");

$checkemail = false;  //เชคอีเมลล์ซ้ำใน Campaigns, true = หากซ้ำไม่ให้ลงทะเบียน , false = ซ้ำลงทะเบียนได้
$landingpage = filter_input(INPUT_POST, 'landingpage')=='false' ? true : false;// true : ใน Campaign นึงลงได้หลายครั้งแต่จะเก็บข้อมูลใว้ที่ ตาราง app_lead_registered_history
$leadstatus = 'Warm';
$leadfilter = 'Non-Qualified';

/*
*กำหนดค่าของอีเมลล์ของ Sales
*$sales_mailto['mailtoteam'] = email
*/
$mailtoteam = filter_input(INPUT_POST, 'mailtoteam');
$sales_mailto['gstar'] = array('suttipong@applicadthai.com','nathanbc46@gmail.com');
$sales_mailto['archicad'] = array('one_bc@hotmail.com');


// $sales_mailto['LDP-VRAY_8baht'] = array('wathit@applicadthai.com');
$sales_mailto['LDP-VRAY_8baht'] = array('webmaster@applicadthai.com','8baht@applicadthai.com','arada@applicadthai.com');

// $sales_mailto['LDP-SKETCHUP_8baht'] = array('wathit@applicadthai.com');
$sales_mailto['LDP-SKETCHUP_8baht'] = array('webmaster@applicadthai.com','8baht@applicadthai.com','arada@applicadthai.com');

// $sales_mailto['LDP-SKETCHUP_8baht'] = array('wathit@applicadthai.com');
$sales_mailto['LDP-ZBRUSH_8baht'] = array('webmaster@applicadthai.com','8baht@applicadthai.com','arada@applicadthai.com');

// $sales_mailto['LDP-SKETCHUP_8baht'] = array('wathit@applicadthai.com');
$sales_mailto['LDP-wox1download_8baht'] = array('aekaluk@applicadthai.com');

$sales_mailto['LDP-RENDERING_8baht'] = array('aekaluk@applicadthai.com');

$sales_mailto['LDP-ADOBE_8baht'] = array('webmaster@applicadthai.com','8baht@applicadthai.com','arada@applicadthai.com');
// $sales_mailto['LDP-ADOBE_8baht'] = array('napassorn_sr@applicadthai.com');

$sales_mailto['LDP_GstarCAD_on_8baht'] = array('webmaster@applicadthai.com','arada@applicadthai.com', 'amnaj@applicadthai.com');
$sales_mailto['dowload_gstartcad_free_trial_8b'] = array('webmaster@applicadthai.com','arada@applicadthai.com', 'amnaj@applicadthai.com');

// $sales_mailto['swid2019'] = array('wathit@applicadthai.com','wathit.pm@gmail.com');

// MI
$sales_mailto['swid2019'] = array('tanongsak@applicadthai.com','webmaster@applicadthai.com','warattida@applicadthai.com','chanittha@applicadthai.com');
$sales_mailto['seminar_future-of-manufacturing-is-now'] = array('webmaster@applicadthai.com','warattida@applicadthai.com','chanittha@applicadthai.com','namfon@applicadthai.com','patcharee@applicadthai.com');
$sales_mailto['seminar_solidplant-2019'] = array('webmaster@applicadthai.com','warattida@applicadthai.com','donlaya@applicadthai.com','chanittha@applicadthai.com');
$sales_mailto['seminar_solution_day_chonburi'] = array('webmaster@applicadthai.com','warattida@applicadthai.com','chanittha@applicadthai.com','prapaisri@applicadthai.com');
$sales_mailto['seminar_solution_day_pathumthani'] = array('webmaster@applicadthai.com','warattida@applicadthai.com','chanittha@applicadthai.com','prapaisri@applicadthai.com');
// CI
$sales_mailto['seminar_thai-bim'] = array('webmaster@applicadthai.com','aec@applicadthai.com','tana@applicadthai.com','tanongsak@applicadthai.com','apinya@applicadthai.com','patchara@applicadthai.com','chareerat@applicadthai.com');
$sales_mailto['seminar_gstarcad'] = array('webmaster@applicadthai.com','amnaj@applicadthai.com','tanongsak@applicadthai.com','apinya@applicadthai.com','patchara@applicadthai.com','chareerat@applicadthai.com');
$sales_mailto['seminar_cadprofi'] = array('webmaster@applicadthai.com','amnaj@applicadthai.com','thanasak@applicadthai.com','tanongsak@applicadthai.com','apinya@applicadthai.com','patchara@applicadthai.com','chareerat@applicadthai.com');
/*
*กำหนดค่าของประเภทของการลงทะเบียน เพื่อกำหนดอีเมลล์ที่จะส่งหาลูกค้า
*$regis_type
*seminar -- จะส่ง barcode ในเมลล์ให้ลูกค้าด้วย
*quotation
*download
*general -- หากไม่มีการส่งค่ามา หรือส่งมาไม่ถูก จะเป็น general
*/
$regis_type = filter_input(INPUT_POST, 'regis_type') ? filter_input(INPUT_POST, 'regis_type') : 'general';

//กำหนด leadstatus,leadfilter ตาม regis_type
if($regis_type=='quotation'||$regis_type=='download'||$regis_type=='download-gstartcad'){
	$leadstatus = 'Hot';
	$leadfilter = 'Qualified';
}elseif($regis_type=='seminar'){
	$checkemail = true;//หากเป็นงาน สัมนา ให้เชคอีเมลล์ด้วย
}
//


/*เอาใว้ใส่ค่า - ให้อัตโนมัติหากไม่ได้ส่งค่ามา */
$firstname = filter_input(INPUT_POST, 'firstname') ? filter_input(INPUT_POST, 'firstname') : '-';
$lastname = filter_input(INPUT_POST, 'lastname') ? filter_input(INPUT_POST, 'lastname') : '-';
$email = filter_input(INPUT_POST, 'email') ? filter_input(INPUT_POST, 'email') : '-';
$company = filter_input(INPUT_POST, 'company') ? filter_input(INPUT_POST, 'company') : '-';
$cf_650 = filter_input(INPUT_POST, 'province') ? filter_input(INPUT_POST, 'province') : '-';

$checkbox_01 = filter_input(INPUT_POST, 'checkbox-01') ? filter_input(INPUT_POST, 'checkbox-01') : '-';
$checkbox_02 = filter_input(INPUT_POST, 'checkbox-02') ? filter_input(INPUT_POST, 'checkbox-02') : '-';
$checkbox_03 = filter_input(INPUT_POST, 'checkbox-03') ? filter_input(INPUT_POST, 'checkbox-03') : '-';
$checkbox_04 = filter_input(INPUT_POST, 'checkbox-04') ? filter_input(INPUT_POST, 'checkbox-04') : '-';
$checkbox_05 = filter_input(INPUT_POST, 'checkbox-05') ? filter_input(INPUT_POST, 'checkbox-05') : '-';
$checkbox_06 = filter_input(INPUT_POST, 'checkbox-06') ? filter_input(INPUT_POST, 'checkbox-06') : '-';
$checkbox_07 = filter_input(INPUT_POST, 'checkbox-07') ? filter_input(INPUT_POST, 'checkbox-07') : '-';
$checkbox_08 = filter_input(INPUT_POST, 'checkbox-08') ? filter_input(INPUT_POST, 'checkbox-08') : '-';
$checkbox_09 = filter_input(INPUT_POST, 'checkbox-09') ? filter_input(INPUT_POST, 'checkbox-09') : '-';
$checkbox_10 = filter_input(INPUT_POST, 'checkbox-10') ? filter_input(INPUT_POST, 'checkbox-10') : '-';

$check_list_1 = $checkbox_01.', '.$checkbox_02.', '.$checkbox_03.', '.$checkbox_04.', '.$checkbox_05.', '.$checkbox_06.', '.$checkbox_07.', '.$checkbox_08.', '.$checkbox_09.', '.$checkbox_10;

// $firstname = filter_input(INPUT_POST, 'firstname');
// $lastname = filter_input(INPUT_POST, 'lastname');
// $email = filter_input(INPUT_POST, 'email');
// $company = filter_input(INPUT_POST, 'company');
// $cf_650 = filter_input(INPUT_POST, 'province');

//สำหรับ redirect
$redirect = filter_input(INPUT_POST, 'redirect');

//description
$description = '';
if(isset($_POST['description'])){
	foreach($_POST['description'] as $index=>$value){
		$description .=
$index.' : '.$value.'
';
	}
}
//
//campaignid
$campaignid = filter_input(INPUT_POST, 'campaignid');
//

//กำหนดหากเป็น Industry เกี่ยวกับการศึกษาให้ส่งให้ทีม EDU
$assigned='';
if(filter_input(INPUT_POST, 'industry')=='Education'||filter_input(INPUT_POST, 'industry')=='Government and Military'){
	$assigned = 311; //(EDU) EDU Solution
}

$urlreference = filter_input(INPUT_POST, 'urlreference') ? filter_input(INPUT_POST, 'urlreference') : filter_input(INPUT_POST, 'urlreferent');
//กำหนดค่า
$params = array(
	   'campaignid' => $campaignid, //ID ของ Campaign
	   'firstname' => $firstname,
	   'lastname' => $lastname,
		'designation' => filter_input(INPUT_POST, 'designation'), //ตำแหน่ง
		'cf_805' => filter_input(INPUT_POST, 'department'), //แผนก
		'email' => $email,
		'company' => $company,
		'website' => filter_input(INPUT_POST, 'website'),
		'industry' => filter_input(INPUT_POST, 'industry'),
		'leadstatus' => $leadstatus, //leadstatus
		'leadsource' => 'Marketing Campaign', //leadsource
		'phone' => filter_input(INPUT_POST, 'phone'),
		'mobile' => filter_input(INPUT_POST, 'mobile'),
		'fax' => filter_input(INPUT_POST, 'fax'),
		'lane' => filter_input(INPUT_POST, 'lane'),
		'city' => filter_input(INPUT_POST, 'city'),
		'cf_650' => $cf_650, //จังหวัด
		'code' => filter_input(INPUT_POST, 'code'),
		'country' => filter_input(INPUT_POST, 'country'),
		'description' => $description.','.$check_list_1 ,
		'cf_842' => $leadfilter , //leads filter
		'cf_659' => $urlreference, //Url reference
		'assigned' => $assigned, // กำหนด $assigned มาโดยตรงโดยไม่อิง Campaign
		'landingpage' => $landingpage, //ใน Campaign นึงลงได้หลายครั้งแต่จะเก็บข้อมูลใว้ที่ ตาราง app_lead_registered_history
		'checkemail' => $checkemail, //เชคอีเมลล์ซ้ำใน Campaigns, true = หากซ้ำไม่ให้ลงทะเบียน , false = ซ้ำลงทะเบียนได้
);

//ทำการสร้าง Leads
$result = createlead($params);
if($result){
	if($result[0] == true){ //หากสร้างสำเร็จ
		//print_r($result);
		/*ส่งเมลล์หาลูกค้า*/
		$name = $firstname.' '.$lastname;
		$to[] = $email;

		switch ($regis_type) {
			case "seminar_swid2019":
				//สร้าง Barcode
				// include composer autoload
					require_once 'vendor/autoload.php';
					$generator = new \Picqer\Barcode\BarcodeGeneratorJPG();
					$imagecode = 'data:image/jpeg;base64,' . base64_encode($generator->getBarcode($result[20], $generator::TYPE_CODE_128,2,40));

					$description = ''; //ใส่รายละเอียดเพิ่มเติมได้ $description = '<p>.....</p>';
					// ฟังค์ชั่น ส่งเมลล์ ลูกค้า
					// mailtocustomer_seminar_swid2019($to,$name,$result,$imagecode,$description);
				//
				break;
			case "seminar":
				//สร้าง Barcode
				// include composer autoload
					require_once 'vendor/autoload.php';
					$generator = new \Picqer\Barcode\BarcodeGeneratorJPG();
					$imagecode = 'data:image/jpeg;base64,' . base64_encode($generator->getBarcode($result[20], $generator::TYPE_CODE_128,2,40));

					$description = ''; //ใส่รายละเอียดเพิ่มเติมได้ $description = '<p>.....</p>';
					mailtocustomer_seminar($to,$name,$result,$imagecode,$description);
				//
				break;
			case "quotation":
					$description = ''; //ใส่รายละเอียดเพิ่มเติมได้ $description = '<p>.....</p>';
					mailtocustomer_quotation($to,$name,$result,$description);
				break;
			case "download":
					$downloadlink = '<strong> - Brochure SINDOH 3DWOX 1 </strong> <a href="https://8baht.com/wp-content/uploads/2019/08/WOX-THAI.pdf">Click</a>';
					$description = ''; //ใส่รายละเอียดเพิ่มเติมได้ $description = '<p>.....</p>';
					mailtocustomer_download($to,$name,$result,$downloadlink,$description);
				break;
			case "download-gstartcad":
					$downloadlink = '<strong> - GstarCAD2021 SP0- 64Bit </strong> <a href="https://upload.applicadgroup.com/index.php/s/GaQZ3VQUwLPaQhp">Click</a>';
					$description = ''; //ใส่รายละเอียดเพิ่มเติมได้ $description = '<p>.....</p>';
					mailtocustomer_downloadgstartcad($to,$name,$result,$downloadlink,$description);
				break;
			case "contact":
					$description = ''; //ใส่รายละเอียดเพิ่มเติมได้ $description = '<p>.....</p>';
					mailtocustomer_general($to,$name,$result,$description);
				break;
			default:
					$description = ''; //ใส่รายละเอียดเพิ่มเติมได้ $description = '<p>.....</p>';
					mailtocustomer_general($to,$name,$result,$description);
		}


		/*ส่งเมลล์หา Sales*/
		if(isset($sales_mailto[$mailtoteam])){
			$description = ''; //ใส่รายละเอียดเพิ่มเติมได้ $description = '<p>.....</p>';
			if($redirect){
				$description = '<p>ลิ้งค์ที่เกี่ยวข้อง : '.$redirect.'</p>';
			}
			//หากลงทะเบียนซ้ำในหน้า Landingpage เดียวกัน
			if(!isset($result[19])&&$landingpage==true){
				$result[19] = $result[0];
				$result[20] = '-';
				$result[21] = $result[1];
				$description .= '<p style="color:red">*** ลูกค้าท่านนี้ลงทะเบียนซ้ำในหน้า Landingpage เดียวกัน เช่น ขอใบเสนอราคา แล้วมาดาวน์โหลดโปรแกรมต่อ เป็นต้น ให้คลิกดูรายละเอียดการลงทะเบียนในแท็บ Register History On Same Campaign ในหน้า Leads Detail ***</p>';
			}
					// ฟังค์ชั่น ส่งเมลล์ เซลล์
			mailtosales($sales_mailto[$mailtoteam],$params,$result[19],$result[20],$result[21],$description);
		}
		//
		//แสดงผลการลงทะเบียนในบราวเซอร์

		/*
		if($regis_type=='seminar'){

			$msg .= ' Register Code : '.$result[20].'<br />
			<img src="'.$imagecode.'"><br />
			ใช้บาร์โค๊ดนี้สำหรับลงทะเบียนเข้างาน <ถูกส่งเข้าเมลล์เรียบร้อยแล้ว> <br />';
		}
		*/
		if($regis_type=='download'){

			$msg .= '
			<p>ช้อมูลถูกจัดส่งเรียบร้อยแล้ว โปรดตรวจสอบที่อีเมล์ของท่าน<br>
			ขอขอบพระคุณที่สนใจสินค้าของเราค่ะ
			</p>
			';
		}
		if($regis_type=='download-gstartcad'){

			$msg .= '
			<p>ช้อมูลถูกจัดส่งเรียบร้อยแล้ว โปรดตรวจสอบที่อีเมล์ของท่าน<br>
			ขอขอบพระคุณที่สนใจสินค้าของเราค่ะ
			</p>
			';
		}
		if($regis_type=='seminar_swid2019'){

			$msg .= '
			<p>ข้อมูลการลงทะเบียนของท่านได้ถูกส่งถึงเจ้าหน้าที่เรียบร้อยแล้ว ซึ่งทางเจ้าหน้าที่จะทำการยืนยันกับท่านอีกครั้งหนึ่ง</p>
				<br>
			<p>	* สำหรับผู้ที่ได้เข้าร่วมสัมมนาจะได้รับการยืนยันทางโทรศัพท์หรืออีเมล์จากเจ้าหน้าที่<br>
					* สงวนสิทธิ์สำหรับผู้ได้รับการยืนยันจากการลงทะเบียนล่วงหน้าเท่านั้น<br>
					* ไม่รับลงทะเบียนหน้างานนะคะ</p>
			';
		}
		if($regis_type=='seminar'){

			$msg .= '
			<p>ข้อมูลการลงทะเบียนของท่านได้ถูกส่งถึงเจ้าหน้าที่เรียบร้อยแล้ว ซึ่งทางเจ้าหน้าที่จะทำการยืนยันกับท่านอีกครั้งหนึ่ง</p>
				<br>
			<p>	* สำหรับผู้ที่ได้เข้าร่วมสัมมนาจะได้รับการยืนยันทางโทรศัพท์หรืออีเมล์จากเจ้าหน้าที่<br>
					* สงวนสิทธิ์สำหรับผู้ได้รับการยืนยันจากการลงทะเบียนล่วงหน้าเท่านั้น<br>
					* ไม่รับลงทะเบียนหน้างานนะคะ</p>
			';
		}
		if($regis_type=='contact'){

			$msg .= '
			<p>ข้อมูลถูกส่งไปยังเจ้าหน้าที่แล้วค่ะ</p>
			';
		}

//		$msg .= $name.'<br />
//		'.$company.'<br />
//		</p>';
		$msg .= '</p>';

		if($redirect){
			$msg .= '<a href="'.$redirect.'" class="btn btn-primary">Finish</a>';
		}
		echo message($msg);
		exit();
	}else{ //หากมี Error
		$msg = '';
		if($redirect){
			$msg .= '<br /><a href="'.$redirect.'" class="btn btn-primary mt-3">Back to register</a>';
		}
		echo message_error($result[1].$msg);
		//echo $result[2]; //หาก error sql และต้องการดู error
		exit();
	}
}

?>
