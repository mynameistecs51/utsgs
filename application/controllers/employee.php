<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class employee extends CI_Controller 
{ 
	public function __construct()
	{
		parent::__construct();
		$this->ctl="employee";
		$this->load->model('mdl_employee'); 
		date_default_timezone_set('Asia/Bangkok');
		$now = new DateTime(null, new DateTimeZone('Asia/Bangkok')); 
		$this->dt_now = $now->format('Y-m-d H:i:s');
		$this->datefrom = "01/".$now->format('m/Y');
		$this->dateto = $now->format('d/m/Y');
		$this->id_mmember = $this->session->userdata('id_mmember');
		$this->id_mposition=$this->session->userdata("id_mposition");
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
	$this->load->view('employee/'.$SCREENID,$this->data);
	
}
public function getList()
{
    $requestData= $_REQUEST;  
    $sqlQuery= $this->mdl_employee->getList($requestData);  
    $this->datatables->getDatatables($requestData,$sqlQuery);
}

public function getCode()
{
	$mcmp_code='M';
	$lastCode=$this->mdl_employee->getCodeLast($mcmp_code);
	return $lastCode;
}

public function checkUser()
{  
	if ($_POST['user'])
	{ 
		echo $this->mdl_employee->getUser($_POST['user']);
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
		$this->data['pagename']=$this->template->getPageName($this->ctl); 
		$this->data['mmember_name'] = $this->session->userdata("mmember_name");
		$this->data['mbranch_name'] = $this->session->userdata("mbranch_name");
		$this->data["lastLogin"] = $this->session->userdata('lastLogin');
		$this->data["id_mmember"] =$this->session->userdata("id_mmember");
		$this->data["id_mposition"] =$this->session->userdata("id_mposition"); 
		$this->data["datefrom"] =$this->datefrom;
		$this->data["dateto"] =$this->dateto;
		$this->data['listMposition']= $this->mdl_employee->getmposition();
		$this->data['listMbranch']= $this->mdl_employee->getmbranch();
		$this->data["header"]=$this->template->getHeader(base_url(),$SCREENNAME,$this->data['mmember_name'],$this->data["lastLogin"],$this->data["id_mposition"],$this->data['mbranch_name']);
		$this->data["btn"] =$this->template->checkBtnAuthen($this->data["id_mposition"],$this->ctl);
		$this->data['url_add']=base_url().$this->ctl."/add/";
		$this->data['url_edit']=base_url().$this->ctl."/edit/";
		$this->data['url_detail']=base_url().$this->ctl."/detail/";
		$this->data['url_print']=base_url().$this->ctl."/printpdf/";
		$this->data["footer"] = $this->template->getFooter(); 
		$this->data['NAV'] =$this->SCREENNAME; 		
}

public function ADD()
	{
			$SCREENID="A001";
			$this->mainpage($SCREENID); 
			$this->load->view('employee/'.$SCREENID,$this->data); 
	}
public function DETAIL($id)
	{
			$SCREENID="D001";
			$this->mainpage($SCREENID); 
			$this->data['listemployee']= $this->mdl_employee->getemployee($id);
			$this->load->view('employee/'.$SCREENID,$this->data);
	}
public function EDIT($id,$idx)
	{
			$SCREENID="E001"; 
			$this->mainpage($SCREENID); 
			$this->data['idx']=$idx;
			$this->data['listemployee']= $this->mdl_employee->getemployee($id);
			$this->load->view('employee/'.$SCREENID,$this->data);
	}

public function saveadd()
{
	if($_POST):
     parse_str($_POST['form'], $post); 
		$data = array(
			"id_mmember"		=> '', 
			"id_mposition"		=> $post['id_mposition'], 
			"id_mbranch"		=> $post['id_mbranch'],
			"mmember_code"		=> $post['mmember_code'],  
			"id_mmember_tit"	=> $post['id_mmember_tit'], 
			"firstname"			=> $post['firstname'], 
			"lastname"			=> $post['lastname'], 
			"birthdate"			=> $this->convert_date($post['birthdate']),
			"startdate"			=> '0000-00-00',
			"resigndate"		=> '0000-00-00', 
			"adr_line1"			=> str_replace("\n", "<br>\n",$post['adr_line1']),
			"adr_line2"			=> str_replace("\n", "<br>\n",$post['adr_line2']), 
			"idcard_num"		=> $post['idcard_num'], 
			"drv_lcn_num"		=> $post['drv_lcn_num'], 
			"email"				=> $post['email'], 
			"telephone"			=> $post['telephone'], 
			"mobile"			=> $post['mobile'], 
			"fax"				=> $post['fax'], 
			"username"			=> $post['username'], 
			"password"			=> MD5($post['password']),
			"comment"			=> str_replace("\n", "<br>\n",$post['comment']),
			"status"			=> 1,
			"id_create"			=> $this->id_mmember,
			"dt_create"			=> $this->dt_now,
			"id_update"			=> $this->id_mmember,	
			"dt_update"			=> $this->dt_now
		);
		$this->mdl_employee->addmmember($data); 
    endif;
}

public function saveUpdate()
{
	if($_POST):
    	parse_str($_POST['form'], $post);   
		$status = $post['status'] == 1 ? 1:0 ;
		$resigndate = $status == 0 ?  $this->dt_now : '0000-00-00';
		$data = array(
			"mmember_code"		=> $post['mmember_code'],
			"id_mposition"		=> $post['id_mposition'], 
			"id_mbranch"		=> $post['id_mbranch'], 
			"id_mmember_tit"	=> $post['id_mmember_tit'], 
			"firstname"			=> $post['firstname'], 
			"lastname"			=> $post['lastname'], 
			"birthdate"			=> $this->convert_date($post['birthdate']),
			"startdate"			=> '0000-00-00',
			"resigndate"		=> $resigndate, 
			"adr_line1"			=> str_replace("\n", "<br>\n",$post['adr_line1']),
			"adr_line2"			=> str_replace("\n", "<br>\n",$post['adr_line2']), 
			"idcard_num"		=> $post['idcard_num'], 
			"drv_lcn_num"		=> $post['drv_lcn_num'], 
			"email"				=> $post['email'], 
			"telephone"			=> $post['telephone'], 
			"mobile"			=> $post['mobile'], 
			"fax"				=> $post['fax'],
			"comment"			=> str_replace("\n", "<br>\n",$post['comment']),
			"status"			=> $status,
			"id_update"			=> $this->id_mmember,	
			"dt_update"			=> $this->dt_now
		); 
		$this->mdl_employee->updateMmember($post['id_mmember'],$data);
 
	endif;
}

public function saveChangePassword()
{
	if($_POST):
    	parse_str($_POST['form'], $post);  
		if($post['old_pass']!=''){
 			$old_pass=$this->mdl_employee->checkOldPass($id,MD5($post['old_pass']));
		}else{
			$old_pass=1;
		}  
		if($old_pass=='1'){ 
			if($post['pass']!=''){
				$data = array( 
					"password"			=> MD5($post['password']),  
					"id_update"			=> $this->id_mmember,	
					"dt_update"			=> $this->dt_now
					);
			} 
			$this->mdl_employee->updateMmember($post['id_mmember'],$data);
		}
	endif; 
}

}?>

