<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Report_stock extends CI_Controller 
{ 
	public function __construct()
	{
		parent::__construct();
		$this->ctl="report_stock";
		$this->load->model('mdl_report_stock'); 
		$now = new DateTime(null, new DateTimeZone('Asia/Bangkok')); 
		$this->dt_now = $now->format('Y-m-d H:i:s');
		$Y=$now->format('Y')+543;
		$this->datefrom = "01/".$now->format('m/').$Y;
		$this->dateto = $now->format('d/m/').$Y;
		$this->datenow = $now->format('d/m/').$Y;
		$this->id_mmember = $this->session->userdata('id_mmember');
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
	$this->load->view('report_stock/'.$SCREENID,$this->data);
	
}
public function getList()
{
    $requestData= $_REQUEST; 
    $sqlQuery= $this->mdl_report_stock->getList($requestData);  
    $this->datatables->getDatatables($requestData,$sqlQuery);
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
			$date = date("Y-m-d", strtotime($date));
			return $date;
}

public function mainpage($SCREENID)
{ 
		$SCREENNAME="รายงาน สต๊อกรถยนต์";
		$this->data["namepage"] ='รายงาน สต๊อกรถยนต์';
		$this->data['controller'] = $this->ctl;
		$this->data['pagename']=$this->template->getPageName($this->ctl);
		$this->data['base_url'] = base_url();
		$this->data['mmember_name'] = $this->session->userdata("mmember_name");
		$this->data['mbranch_name'] = $this->session->userdata("mbranch_name");
		$this->data["lastLogin"] = $this->session->userdata('lastLogin');
		$this->data["id_mmember"] =$this->session->userdata("id_mmember");
		$this->data["id_mposition"] =$this->session->userdata("id_mposition");  
		$this->data['listMbranch']= $this->mdl_report_stock->getmbranch();
		$this->data['listMzone']= $this->mdl_report_stock->getmZone($this->session->userdata("id_mbranch"));
		$this->data["datenow"] = $this->datenow;
		$this->data["datefrom"] =$this->datefrom;
		$this->data["dateto"] =$this->dateto;
		$this->data["header"]=$this->template->getHeader(base_url(),$SCREENNAME,$this->data['mmember_name'],$this->data["lastLogin"],$this->data["id_mposition"],$this->data['mbranch_name']);
		$this->data["btn"] =$this->template->checkBtnAuthen($this->data["id_mposition"],$this->ctl);
		$this->data['url_add']=$this->data['base_url'].$this->ctl."/add/";
		$this->data['url_edit']=$this->data['base_url'].$this->ctl."/edit/";
		$this->data['url_detail']=$this->data['base_url'].$this->ctl."/detail/";
		$this->data["footer"] = $this->template->getFooter(); 
		$this->data['NAV'] =$this->SCREENNAME; 		
}

public function ADD()
	{
			$SCREENID="A001";
			$this->data['pagename']=$this->SCREENNAME;
			$this->data["datenow"] =$this->datenow;
			$this->mainpage($SCREENID); 
			$this->load->view('report_stock/'.$SCREENID,$this->data); 
	}
public function DETAIL($id)
	{
			$SCREENID="D001";
			$this->data['pagename']=$this->SCREENNAME;
			$this->data["datenow"] =$this->datenow;
			$this->mainpage($SCREENID); 
			$this->data['listreport_stock']= $this->mdl_report_stock->getreport_stock($id);
			$this->load->view('report_stock/'.$SCREENID,$this->data);
	}
public function EDIT($id,$idx)
	{
			$SCREENID="E001"; 
			$this->data['pagename']=$this->SCREENNAME;
			$this->data["datenow"] =$this->datenow;
			$this->mainpage($SCREENID); 
			$this->data['idx']=$idx;
			$this->data['listreport_stock']= $this->mdl_report_stock->getreport_stock($id);
			$this->load->view('report_stock/'.$SCREENID,$this->data);
	}

public function saveadd()
{
	if($_POST):
     parse_str($_POST['form'], $post);
		//$code= $this->getCode();  
		$data = array(
			"mreport_stock_code"			=> $post['mreport_stock_code'],
			"name_en"			=> $post['name_en'],
			"name_th"			=> $post['name_th'],
			"comment"			=> str_replace("\n", "<br>\n",$post['comment']),
			"status"			=> 1,
			"id_create"			=> $this->id_mmember,
			"dt_create"			=> $this->dt_now,
			"id_update"			=> $this->id_mmember,
			"dt_update"			=> $this->dt_now
		);
		//print_r($data);exit();
			$this->mdl_report_stock->addmreport_stock($data);
			$massage = "บันทึกข้อมูล เรียบร้อย !";
			$this->alert($massage);
    endif;
}

public function saveUpdate()
{
	if($_POST):
    parse_str($_POST['form'], $post);
	$id=$post['id_mreport_stock'];
					$data = array(
						"name_en"			=> $post['name_en'], 
						"name_th"			=> $post['name_th'],
						"comment"			=> str_replace("\n", "<br>\n",$post['comment']),
						"status"			=> $post['status'],
						"id_update"			=> $this->id_mmember,
						"dt_update"			=> $this->dt_now
					);
			$this->mdl_report_stock->updatemreport_stock($id,$data);
			$massage = "แก้ไขข้อมูล เรียบร้อย !";
			$this->alert($massage);
	endif;
}
} ?>