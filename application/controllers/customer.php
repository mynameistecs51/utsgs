<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Customer extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->ctl="customer";
		$this->load->model('mdl_customer');
		$this->load->model('mdl_getProvince');
		$this->load->model('mdl_stock');
		$now = new DateTime(null, new DateTimeZone('Asia/Bangkok'));
		$this->dt_now = $now->format('Y-m-d H:i:s');
		$this->datenow =$now->format('d/m/').($now->format('Y')+543);
		$this->datefrom = "01/".$now->format('m/Y');
		$this->dateto = $now->format('d/m/Y');
		$this->id_mmember = $this->session->userdata('id_mmember');
		$this->mmember_code = $this->session->userdata('mmember_code');
		$this->id_mposition=$this->session->userdata("id_mposition");
		$this->id_mbranch=$this->session->userdata("id_mbranch");
		$this->SCREENNAME=$this->template->getScreenName($this->ctl);
		if($this->session->userdata("id_mmember")==""){
			redirect('authen/');
		}else if($this->template->CheckAuthen($this->id_mposition,$this->ctl)=="0"){
			redirect('authen/');
		}
	}

	public function index()
	{
		$SCREENID="L001";
		$this->mainpage($SCREENID);
		$this->load->view('customer/'.$SCREENID,$this->data);
	}

	public function getProvince() //แสดงรายการ รหัสไปรษณีย์ จังหวัด อำเภอ ตำบล
	{
		$zipcode =  $_POST['zipcode'];
		$showdata = $this->mdl_getProvince->getProvince($zipcode);

		$province = array('province_id'=>$showdata[0]['PROVINCE_ID'],'province_name' => $showdata[0]['PROVINCE_NAME'],'amphur_id'=>$showdata[0]['AMPHUR_ID'],'amphur_name' => $showdata[0]['AMPHUR_NAME'],'zipcode ' => $showdata[0]['ZIPCODE']);
		foreach ($showdata as $rowProvince) {
			$dataProvince = array(
				'district_id' => $rowProvince['DISTRICT_ID'],
				'district_name' => $rowProvince['DISTRICT_NAME'],
				);
			array_push($province,array('district_name'=>$dataProvince['district_name'],'district_id'=>$dataProvince['district_id']));
		}
		echo json_encode($showdata);

	}

	public function getList()
	{
		$requestData= $_REQUEST;
		$sqlQuery= $this->mdl_customer->getList($requestData);
		$this->datatables->getDatatables($requestData,$sqlQuery);
	}

	public function getMmodel()
	{
		if($_POST['id_mmodel']){
			$rs = $this->mdl_stock->getmmodel($_POST['id_mmodel']);
			echo json_encode($rs);
		}
	}
	public function getMgen()		//get model Car
	{
		if ($_POST['id_mmodel'])
		{
			$rs=$this->mdl_stock->getmgen($_POST['id_mmodel']);
			echo json_encode($rs);
		}
	}

	public function getMcolor()		//get color car
	{
		if ($_POST['id_mgen'])
		{
			$rs=$this->mdl_stock->getMcolor($_POST['id_mgen']);
			echo json_encode($rs);
		}
	}


	public function checkUser()
	{
		if ($_POST['user'])
		{
			echo $this->mdl_customer->getUser($_POST['user']);
		}
	}

	public function alert($massage)
	{
		echo "<meta charset='UTF-8'>
		<SCRIPT LANGUAGE='JavaScript'>
			window.alert('$massage')';
		</SCRIPT>";
	}

	public function convert_date($val_date)
	{
		$date = str_replace('/', '-',$val_date);
		$d=$date[0].$date[1];
		$m=$date[3].$date[4];
		$y=$date[6].$date[7].$date[8].$date[9];
		$y=intval($y)-543;
		$date = $y."-".$m."-".$d;
			//$date = date("Y-m-d", strtotime($date));
		return $date;
	}

	public function mainpage($SCREENID)
	{
		$SCREENNAME="MEMBER";
		$this->data['controller'] = $this->ctl;
		$this->data['base_url'] = base_url();
		$this->data['pagename']=$this->template->getPageName($this->ctl);
		$this->data['mmember_name'] = $this->session->userdata("mmember_name");
		$this->data['mbranch_name'] = $this->session->userdata("mbranch_name");
		$this->data["lastLogin"] = $this->session->userdata('lastLogin');
		$this->data["id_mmember"] =$this->session->userdata("id_mmember");
		$this->data["id_mposition"] =$this->session->userdata("id_mposition");
		$this->data["datefrom"] =$this->datefrom;
		$this->data["dateto"] =$this->dateto;
		$this->data["datenow"] =$this->datenow;
		$this->data["listBranch"] = $this->mdl_customer->getMbranch();
		$this->data['listMmodel']= $this->mdl_stock->getmmodel();
		$this->data['listSale']= $this->mdl_customer->getTypeSale();
		$this->data["header"]=$this->template->getHeader(base_url(),$SCREENNAME,$this->data['mmember_name'],$this->data["lastLogin"],$this->data["id_mposition"],$this->data['mbranch_name']);
		$this->data["btn"] =$this->template->checkBtnAuthen($this->data["id_mposition"],$this->ctl);
		$this->data['url_add']= base_url().$this->ctl."/add/";
		$this->data['url_edit']= base_url().$this->ctl."/edit/";
		$this->data['url_detail']=base_url().$this->ctl."/detail/";
		$this->data['url_print']=base_url().$this->ctl."/printpdf/";
		$this->data['dtnow'] = $this->template->dtnow();
		$this->data["footer"] = $this->template->getFooter();
		$this->data['NAV'] =$this->SCREENNAME;
	}

	public function ADD()
	{
		$SCREENID="A001";
		$this->mainpage($SCREENID);
		$this->data['mmember_code']=$this->mmember_code;
		$this->data["id_mbranch"] = $this->id_mposition;
		$this->load->view('customer/'.$SCREENID,$this->data);
	}
	public function DETAIL($id)
	{
		$data_array = array();
		foreach($this->mdl_customer->getCustomer($id) as $key=>$row_customer){

			if(isset($data_array[$row_customer->id_customer])){
				array_push($data_array[$row_customer->id_customer]['cars'], array(
					'model_name' =>$row_customer->mmodel_name,
					'gen_name'=>$row_customer->gen_name,
					'color'=>$row_customer->color_name));
				continue;
			}
			if( !isset($data_array[$row_customer->id_customer])){
				$data_array[$row_customer->id_customer] = array('id_customer_att' =>$row_customer->att_id_customer,
					'id_customer'=>$row_customer->id_customer,
					'customer_code' =>$row_customer->customer_code,
					'customer_date' => $row_customer->customer_date,
					'accounts_receivable' => $row_customer->accounts_receivable,
					'is_cus_new' => $row_customer->is_cus_new,
					'is_type' => $row_customer->is_type,
					'is_company' =>$row_customer->is_company,
					'is_tit' =>$row_customer->id_tit,
					'is_car_type' => $row_customer->is_car_type,
					'firstname' => $row_customer->firstname,
					'lastname' => $row_customer->lastname,
					'birth_date' => $row_customer->birth_date,
					'adr_line' =>$row_customer->adr_line,
					'post_code' =>$row_customer->post_code,
					'id_mdistric' => $row_customer->id_mdistric,
					'idcard_number' => $row_customer->idcard_number,
					'driver_card_number' =>$row_customer->driver_card_number,
					'email' =>$row_customer->email,
					'telephone' =>$row_customer->telephone,
					'mobile' =>$row_customer->mobile,
					// 'sales_consultants' => $row_customer->sales_consultants,
					'member_name' => $row_customer->member_name,
					'id_mbranch' =>$row_customer->id_mbranch,
					'customer_source' => $row_customer->customer_source,
					'reason' => $row_customer->reason,
					'comment' =>$row_customer->comment,
					'status' =>$row_customer->status,
					'mbranch_name'=>$row_customer->mbranch_name,
					'cars' => array(
						$key => array(
							'model_name' =>$row_customer->mmodel_name,
							'gen_name'=>$row_customer->gen_name,
							'color'=>$row_customer->color_name
							)
						)
					);
				}
				}
				$SCREENID="D001";
				$this->mainpage($SCREENID);
				$this->data['listcustomer']= $data_array;
				// $this->data['listcustomer']= $this->mdl_customer->getCustomer($id);
				$this->load->view('customer/'.$SCREENID,$this->data);
		}
		
		public function EDIT($id,$idx)
		{
			$data_array = array();
			foreach($this->mdl_customer->getCustomer($id) as $key=>$row_customer){

				if(isset($data_array[$row_customer->id_customer])){
					array_push($data_array[$row_customer->id_customer]['cars'], 
						array(
						'id_model' => $row_customer->id_model,
						'id_gen' => $row_customer->id_gen,
						'id_color' => $row_customer->id_color,
						'model_name' =>$row_customer->mmodel_name,
						'gen_name'=>$row_customer->gen_name,
						'color'=>$row_customer->color_name
						)
					);
					continue;
				}
				if( !isset($data_array[$row_customer->id_customer])){
					$data_array[$row_customer->id_customer] = array('id_customer_att' =>$row_customer->att_id_customer,
						'id_customer'=>$row_customer->id_customer,
						'customer_code' =>$row_customer->customer_code,
						'customer_date' => $row_customer->customer_date,
						'accounts_receivable' => $row_customer->accounts_receivable,
						'is_cus_new' => $row_customer->is_cus_new,
						'is_type' => $row_customer->is_type,
						'is_company' =>$row_customer->is_company,
						'is_tit' =>$row_customer->id_tit,
						'is_car_type' => $row_customer->is_car_type,
						'firstname' => $row_customer->firstname,
						'lastname' => $row_customer->lastname,
						'birth_date' => $row_customer->birth_date,
						'adr_line' =>$row_customer->adr_line,
						'post_code' =>$row_customer->post_code,
						'id_mdistric' => $row_customer->id_mdistric,
						'idcard_number' => $row_customer->idcard_number,
						'driver_card_number' =>$row_customer->driver_card_number,
						'email' =>$row_customer->email,
						'telephone' =>$row_customer->telephone,
						'mobile' =>$row_customer->mobile,
						'member_name' => $row_customer->member_name,
						'sales_consultants' => $row_customer->sales_consultants,
						'id_mbranch' =>$row_customer->id_mbranch,
						'customer_source' => $row_customer->customer_source,
						'reason' => $row_customer->reason,
						'comment' =>$row_customer->comment,
						'status' =>$row_customer->status,
						'mbranch_name'=>$row_customer->mbranch_name,
						'cars' => array(
							$key => array(
								'id_model' => $row_customer->id_model,
								'id_gen' => $row_customer->id_gen,
								'id_color' => $row_customer->id_color,
								'model_name' =>$row_customer->mmodel_name,
								'gen_name'=>$row_customer->gen_name,
								'color'=>$row_customer->color_name
								)
							)
						);
					}
					}
			$SCREENID="E001";
			$this->mainpage($SCREENID);
			$this->data['idx']=$idx;
			$this->data['listcustomer']= $data_array;
			$this->load->view('customer/'.$SCREENID,$this->data);
		}

		public function saveadd()
		{
			$customerCode = $this->mdl_customer->getCodeCustomer();
			if($_POST):
				parse_str($_POST['form'], $post);
					//$code= $this->getCode();
			$objective = "";
			$ob = count($post['objective']);
			for ($i=0; $i < $ob; $i++) {
				$objective .=$post['objective'][$i].',';
			}

			$origin = "";
			$orig = count($post['origin']);
			for ($j=0; $j < $orig; $j++) {
				$origin .=$post['origin'][$j].',';
			}
			$data = array(
				 "id_customer" => '',
				"customer_code"  =>	$customerCode,
				"customer_date" =>	$this->convert_date($post['customer_date']),
				"bye_date" 	=>	'0000-00-00 ',
				"accounts_receivable"	 =>	$post['accounts_receivable'],
				"is_cus_new" 	=> $post['customer'],
				"is_type"		=>	$post['is_type'],
				"is_company" 	=>	$post['is_company'],
				"id_tit"		=> 	$post['is_tit'],
				"firstname" 	=>	$post['firstname_th'],
				"lastname" 	=>	$post['lastname_th'],
				"birth_date"	=>	$this->convert_date($post['birthdate']),
				"adr_line" 	=>	$post['address'],
				"post_code" 	=>	$post['zipcode'],
				"id_mprovince" 	=>	$post['province'],
				"id_mamphur"	=>	$post['amphur'],
				"id_mdistric"	=>	$post['district'],
				"idcard_number"	=>	$post['idcard_number'],
				"driver_card_number"	=>	$post['drv_card_num'],
				"email"		=>	$post['email'],
				"telephone"	=> 	$post['telephone'],
				"mobile"		=>	$post['mobile'],
				"sales_consultants"	=>	$post['adviser'],
				"is_car_type" 	=> $post['typeCar'],
				"customer_source"		=>	substr($origin,0,-1),
				"reason"	=>	substr($objective,0,-1),
				"id_mbranch"	=>	$post['branch'],
				"comment"	=> 	str_replace("\n", "<br>\n",$post['comment']),
				"status"		=>	 1,
				"id_create"	=>	 $this->id_mmember,
				"dt_create"	=> 	$this->dt_now,
				"id_update"	=> 	$this->id_mmember,
				"dt_update"	=> 	$this->dt_now
				);
						// echo "<pre>";
						// print_r($data);exit;
		$insert = $this->mdl_customer->addcustomer($data);
		$massage = "บันทึกข้อมูล เรียบร้อย !";
		$this->alert($massage);
						// echo json_encode($data);

		$countModel =  count($post['id_mmodel']);
		for ($k=0; $k < $countModel ; $k++) {
			$modelAtt[$k] = array(
				'id_customer_car_att' =>	'',
				'id_customer' 	=> 	$insert,
				'id_model' 		=>	$post['id_mmodel'][$k],
				'id_gen'			=>	$post['id_mgen'][$k],
				'id_color'		=>	$post['id_mcolor'][$k],
				"comment"	=> 	str_replace("\n", "<br>\n",$post['comment']),
				"status"		=>	 1,
				"id_create"	=>	 $this->id_mmember,
				"dt_create"	=> 	$this->dt_now,
				"id_update"	=> 	$this->id_mmember,
				"dt_update"	=> 	$this->dt_now,
				);
			$this->mdl_customer->insert_customerAtt($modelAtt[$k]);
		}

		endif;

		}

	public function saveUpdate()
	{
		if($_POST):
			parse_str($_POST['form'], $post);
				$id = $post['id_customer'];

			$objective = "";
			$ob = count($post['objective']);
			for ($i=0; $i < $ob; $i++) {
				$objective .=$post['objective'][$i].',';
			}

			$origin = "";
			$orig = count($post['origin']);
			for ($j=0; $j < $orig; $j++) {
				$origin .=$post['origin'][$j].',';
			}
			$data = array(
				 // "id_customer" => '',
				"customer_date" =>	$this->convert_date($post['customer_date']),
				"bye_date" 	=>	'0000-00-00 ',
				"accounts_receivable"	 =>	$post['accounts_receivable'],
				"is_cus_new" 	=> $post['customer'],
				"is_type"		=>	$post['is_type'],
				"is_company" 	=>	$post['is_company'],
				"id_tit"		=> 	$post['is_tit'],
				"firstname" 	=>	$post['firstname_th'],
				"lastname" 	=>	$post['lastname_th'],
				"birth_date"	=>	$this->convert_date($post['birthdate']),
				"adr_line" 	=>	$post['address'],
				"post_code" 	=>	$post['zipcode'],
				"id_mprovince" 	=>	$post['province'],
				"id_mamphur"	=>	$post['amphur'],
				"id_mdistric"	=>	$post['district'],
				"idcard_number"	=>	$post['idcard_number'],
				"driver_card_number"	=>	$post['drv_card_num'],
				"email"		=>	$post['email'],
				"telephone"	=> 	$post['telephone'],
				"mobile"		=>	$post['mobile'],
				"sales_consultants"	=>	$post['adviser'],
				"is_car_type" 	=> $post['typeCar'],
				"customer_source"		=>	substr($origin,0,-1),
				"reason"	=>	substr($objective,0,-1),
				"id_mbranch"	=>	$post['branch'],
				"comment"	=> 	str_replace("\n", "<br>\n",$post['comment']),
				"status"		=>	 1,
				"id_create"	=>	 $this->id_mmember,
				"dt_create"	=> 	$this->dt_now,
				"id_update"	=> 	$this->id_mmember,
				"dt_update"	=> 	$this->dt_now
				);
						// echo "<pre>";
						// print_r($data);exit;
		$insert = $this->mdl_customer->update_customer($id,$data);
		$massage = "บันทึกข้อมูล เรียบร้อย !";
		$this->alert($massage);
						// echo json_encode($data);

		$countModel =  count($post['id_mmodel']);
		for ($k=0; $k < $countModel ; $k++) {
			$modelAtt[$k] = array(
				// 'id_customer_car_att' =>	'',
				'id_customer' 	=> 	$id,
				'id_model' 		=>	$post['id_mmodel'][$k],
				'id_gen'			=>	$post['id_mgen'][$k],
				'id_color'		=>	$post['id_mcolor'][$k],
				"comment"	=> 	str_replace("\n", "<br>\n",$post['comment']),
				"status"		=>	 1,
				"id_create"	=>	 $this->id_mmember,
				"dt_create"	=> 	$this->dt_now,
				"id_update"	=> 	$this->id_mmember,
				"dt_update"	=> 	$this->dt_now,
				);
			$this->mdl_customer->update_customerAtt($id,$modelAtt[$k]);
		}

		endif;
	}
}?>
