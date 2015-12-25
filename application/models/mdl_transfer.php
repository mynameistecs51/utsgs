<?php
   class Mdl_transfer extends CI_Model
   {
      public function __construct()
      {
		parent::__construct(); 
		$now = new DateTime(null, new DateTimeZone('Asia/Bangkok')); 
		$this->datefrom = $now->format('Y-m-')."01";
		$this->dateto = $now->format('Y-m-d');
		$this->id_mbranch = $this->session->userdata("id_mbranch");
      }

	  public function addTransfer($data){
		$this->db->insert('ttransfer', $data);
	  }

	  public function updateTransfer($id,$data){
		$this->db->where('id_transfer', $id);
		$this->db->update('ttransfer', $data);
	  }

 
 public function getList($requestData){

	 	$sql_full = "
              SELECT
				a.id_mbranch,
				a.mbranch_code,
				a.mbranch_name, 
				a.status,
				a.comment
		 		FROM
				mbranch a  
	 			WHERE 1 = 1 ";
        
        $sql_search=$sql_full;
        // getting records as per search parameters
        if( !empty($requestData['columns'][0]['search']['value']) ){ //name
        $sql_search.=" AND a.mbranch_code LIKE '%".$requestData['columns'][0]['search']['value']."%' ";
        } 
        if( !empty($requestData['columns'][1]['search']['value']) ){  //salary
        $sql_search.=" AND a.mbranch_name  LIKE '%".$requestData['columns'][1]['search']['value']."%' ";
        } 
        if($requestData['columns'][3]['search']['value'] !=''){  //salary
        $sql_search.=" AND a.status= ".$requestData['columns'][3]['search']['value'];
        }
        //echo($requestData['columns'][3]['search']['value']);
        //echo($sql_search);
        $data = array(
        	'sql_full' => $sql_full,
        	'sql_search' => $sql_search 
        );
        return $data;
	  }

	public function getTransfer($id){
	  $sql = "
			SELECT 
				a.id_stock, 
				a.stock_code,  
				CONCAT(DATE_FORMAT(a.stock_date,'%d/%m/'),DATE_FORMAT(a.stock_date,'%Y')+543 ) AS stock_date, 
				b.mbranch_name,
				a.id_transfer, 
				a.chassis_number, 
				a.engine_number,
				a.id_mmodel,
				c.mmodel_name,
				a.id_mgen,
				d.gen_name,
				a.id_mcolor,
				e.color_name,
				a.chassis_number,
				a.engine_number, 
				CONCAT(DATE_FORMAT(a.recive_doc_date,'%d/%m/'),DATE_FORMAT(a.recive_doc_date,'%Y')+543 ) AS recive_doc_date,
				a.doc_reference_code,
				a.id_zone,
				f.zone_name,
				a.comment, 
				a.status,
				concat(i.firstname,' ',i.lastname) AS name_create,
				concat(i2.firstname,' ',i2.lastname) AS name_update,
				DATE_FORMAT(a.dt_create,'%d/%m/%Y %H:%i:%s') AS dt_create,
				DATE_FORMAT(a.dt_update,'%d/%m/%Y %H:%i:%s') AS dt_update
			FROM tstock a
			INNER JOIN mbranch b ON a.id_mbranch=b.id_mbranch
			INNER JOIN mmodel c ON a.id_mmodel=c.id_model
			INNER JOIN mgen d ON a.id_mgen=d.id_gen
			INNER JOIN mcolor e ON a.id_mcolor=e.id_color
			INNER JOIN mzone f ON a.id_zone=f.id_zone 
			LEFT JOIN mmember i ON a.id_create=i.id_mmember
			LEFT JOIN mmember i2 ON a.id_update=i2.id_mmember  ";
				
			if($id != ""){
				 $sql .= " WHERE a.id_stock='$id' ";
			}
 		// echo "<pre>".$sql;
			$query = $this->db->query($sql);
			return  $query->result();
 	  } 

	public function getStock($typ,$code){
	  $sql = "
			SELECT 
				a.id_stock, 
				a.stock_code,  
				CONCAT(DATE_FORMAT(a.stock_date,'%d/%m/'),DATE_FORMAT(a.stock_date,'%Y')+543 ) AS stock_date, 
				b.mbranch_name,
				a.id_transfer, 
				a.chassis_number, 
				a.engine_number,
				a.id_mmodel,
				c.mmodel_name,
				a.id_mgen,
				d.gen_name,
				a.id_mcolor,
				e.color_name,
				a.chassis_number,
				a.engine_number, 
				CONCAT(DATE_FORMAT(a.recive_doc_date,'%d/%m/'),DATE_FORMAT(a.recive_doc_date,'%Y')+543 ) AS recive_doc_date,
				a.doc_reference_code,
				a.id_zone,
				f.zone_name,
				a.comment, 
				a.status,
				concat(i.firstname,' ',i.lastname) AS name_create,
				concat(i2.firstname,' ',i2.lastname) AS name_update,
				DATE_FORMAT(a.dt_create,'%d/%m/%Y %H:%i:%s') AS dt_create,
				DATE_FORMAT(a.dt_update,'%d/%m/%Y %H:%i:%s') AS dt_update
			FROM tstock a
			INNER JOIN mbranch b ON a.id_mbranch=b.id_mbranch
			INNER JOIN mmodel c ON a.id_mmodel=c.id_model
			INNER JOIN mgen d ON a.id_mgen=d.id_gen
			INNER JOIN mcolor e ON a.id_mcolor=e.id_color
			INNER JOIN mzone f ON a.id_zone=f.id_zone 
			LEFT JOIN mmember i ON a.id_create=i.id_mmember
			LEFT JOIN mmember i2 ON a.id_update=i2.id_mmember  
			WHERE  a.status =1 
			AND a.id_mbranch= '$this->id_mbranch' "; 
			if($typ == 1){
				$sql .= " AND a.stock_code='$code' ";
			}else if($typ == 2){
				$sql .= " AND a.chassis_number='$code' ";
			}
 		// echo "<pre>".$sql;
			$query = $this->db->query($sql);
			return  $query->result();
 	  } 

 	public function getmbranch(){
	  $sql = "
			SELECT
			a.id_mbranch,a.mbranch_name
			FROM
			mbranch a
			WHERE a.status = 1 
			AND a.id_mbranch <> '$this->id_mbranch' ";
		// echo $sql;
		$query = $this->db->query($sql);
		return  $query->result();
 	}
}?>