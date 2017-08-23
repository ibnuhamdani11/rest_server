<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
class Restricted extends REST_Controller{
  protected $id = null;
  protected $start = 0;
  protected $length = 10;
  protected $order = "id";
  protected $desc = false;
  protected $client = "default";
  protected $sEcho = 1;
  protected $module = "default";
  protected $cmd = "default";
  protected $output = "json";
  protected $params;
  protected $condition = "and";
  protected $operator;
  protected $where = "";
  protected $haveActive = false;
  // protected $needCompany = true;
  protected $needUser = false;
  // protected $needVehicle = false;
  protected $usingCommandAlias = false;
  protected $usingSelectSimple = false;
  protected $selectAlias="";
  protected $selectCmd = "id, name";
  protected $selectSimple = "id as id";
  // protected $tableName = "m_user";
  protected $starProcess = null;
  protected $endProcess = null;
  protected $sqlLimit = "";
  protected $joinTable = true;
  // protected $keySearch = array("fullname","name","employee_code","identity_no","driver_license_no","license_plate");


  function __construct(){
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type');
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

    parent::__construct();
    $this->startProcess = microtime(true);

    if(isset($_GET['id']))
      $this->id = $_GET['id'];

    if(isset($_GET['start']))
      $this->start = $_GET['start'];

    if(isset($_GET['length']))
      $this->length = $_GET['length'];

    if(isset($_GET['iDisplayLength']))
      $this->length = $_GET['iDisplayLength'];

    if(isset($_GET['order']))
      $this->order = $_GET['order'];

    if(isset($_GET['desc']))
      $this->desc = $_GET['desc'];

    if(isset($_GET['client']))
      $this->client = $_GET['client'];

    if(isset($_GET['params']))
      $this->params = $_GET['params'];

    if(isset($_GET['sEcho']))
      $this->sEcho = $_GET['sEcho'];

    if(isset($_GET['iDisplayStart']))
        $this->start = $_GET['iDisplayStart'];
      

    if(isset($this->length) && $this->length != "-1" )
        $this->sqlLimit = " LIMIT ".$this->length;

    /*if($this->_apiuser->company_allowed=='') {
      $this->updateCompanyAccess($this->_apiuser->id);
    }*/

    /*if($this->_apiuser->vehicle_allowed=='') {
      $this->updateVehicleAccess($this->_apiuser->id);
    }*/

      $this->where = $this->_generalCondition();


  }



  function index_get($cmd="",$value=""){
    $list = null;
    $rowCount = 0;
    if($this->usingSelectSimple) {
      $list = $this->_list_by_query($this->selectSimple);
    }else {
      $list = $this->_list();
    }
    
    $rowCount = $this->_countList();
    
    if($this->client == 'datatables') {
      $this->_successResponse('datatables',$list,$rowCount);
    }else {
      $this->_successResponse('loadresult',$list,$rowCount);
    }

  }

  protected function cmd_get($val){
    if($val == "option"){
        $rowOption = $this->_rowOption();
        $this->set_response($rowOption, REST_Controller::HTTP_OK);
    }

    if($val == 'simple-select') {

        $list = $this->_list_by_query($this->selectSimple);
        $rowCount = $this->_countList();
        if($this->client == 'datatables') {
          $this->_successResponse('datatables',$list,$rowCount);
        }else {
          $this->_successResponse('loadresult',$list,$rowCount);
        }
    }
  }

  protected function id_get($val){
    if(isset($val) && $val != "" && $val != null){
      $aliasTable = "";
      $conditionalId = "id";
      if($this->joinTable) {
        $aliasTable = "bean";
        $conditionalId = $aliasTable.".".$conditionalId;
      }

        if($this->where!="")
          $this->where .= " AND ".$conditionalId."=".$val;
        else
          $this->where .= " WHERE ".$conditionalId."=".$val;
        $rowObject = $this->_list();

        if(count($rowObject) > 0) {
          $rowObject = $rowObject[0]; 
          $rowObject = $this->_addInformationObject($rowObject);
          $this->set_response($rowObject, REST_Controller::HTTP_OK);
        }else {
          $response = [
          'status' => TRUE,
          'message' => 'Your ID is not Found'
      ];
      $this->_set_custome_response($response, REST_Controller::HTTP_BAD_REQUEST);
        }

    }else {
      $response = [
          'status' => TRUE,
          'message' => 'Please defined your ID'
      ];
      $this->_set_custome_response($response, REST_Controller::HTTP_BAD_REQUEST);
    }
  }

  protected function index_post(){
    $input_data = json_decode(trim(file_get_contents('php://input')), true);
    $input_data = (array) $input_data;


    $input_data = $this->addServerBasedData($input_data);
    if($this->db->insert($this->tableName,$input_data)) {
      $input_data['id'] = $this->db->insert_id();
      $this->addDependingData($input_data);

      $response = [
          'status' => TRUE,
          'message' => 'Data has been Saved'
      ];
      $this->_set_custome_response($response, REST_Controller::HTTP_OK);
    }else{
      $response = [
          'status' => TRUE,
          'message' => 'Failed to save Data'
      ];
      $this->_set_custome_response($response, REST_Controller::HTTP_BAD_REQUEST);
    }
    exit;
  }

  protected function id_put($val){
    if(isset($val) && $val != "" && $val != null){
      $input_data = array();
      $request_body = $this->request->body;
      
      
      if(isset($request_body[0])){

        $input_data = $request_body[0];
        
      }
      else {
        $input_data = $request_body;
      }

      // if upload in production
      if(!is_array($input_data))
        $input_data = (array)json_decode($input_data);
      else 
        $input_data = (array) $input_data;
    
      // in development
      // $input_data = (array) $input_data;
      
      $input_data = $this->addServerBasedDataPut($input_data);

      $this->db->where('id', $val);
      if($this->db->update($this->tableName, $input_data)){

        $this->addDependingDataPut($input_data);

        $response = [
            'status' => TRUE,
            'message' => 'Data has been Updated'
        ];
        $this->_set_custome_response($response, REST_Controller::HTTP_OK);

      }else{
        $response = [
            'status' => TRUE,
            'message' => 'Failed to update Data'
        ];
        $this->_set_custome_response($response, REST_Controller::HTTP_BAD_REQUEST);
      }

      exit;
    }
  }


  protected function id_delete($val){
      if(isset($val) && $val != "" && $val != null){
          if($this->db->delete($this->tableName, array('id' => $val))){
            $response = [
                'status' => TRUE,
                'message' => 'Data has been Deleted'
            ];
            $this->_set_custome_response($response, REST_Controller::HTTP_OK);
          }else{
            if($this->haveActive) {
              $data_update['active'] = false;
              $this->db->where('id',$val);
              $this->db->update($this->tableName,$data_update);
              $response = [
                  'status' => TRUE,
                  'message' => 'Data has been Deleted'
              ];
              $this->_set_custome_response($response, REST_Controller::HTTP_OK);
            }

            $response = [
                'status' => TRUE,
                'message' => 'Failed to Delete Data'
            ];
            $this->_set_custome_response($response, REST_Controller::HTTP_BAD_REQUEST);
          }
      }
    }

  protected function _addInformationObject($object){
  }
    return $object;

  protected function addDependingDataPut($input_data){

  }
  protected function addDependingData($input_data){

  }


  protected function addServerBasedData($input_data){
      $input_data['modified_date'] = date("Y-m-d H:i:s");
      $input_data['modified_by'] = $this->_apiuser->username;
      $input_data['created_by'] = $this->_apiuser->username;
      $input_data['created_date'] =date("Y-m-d H:i:s");
      return $input_data;
  }

  protected function addServerBasedDataPut($input_data){
    $input_data['modified_date'] = date("Y-m-d H:i:s");
    $input_data['modified_by'] = $this->_apiuser->username;
    return $input_data;
  }

  protected function _list_by_query($sqlQuery){
    $descExpression = "asc";
    if(isset($this->desc) && $this->desc)
      $descExpression = "desc";

    $sqlQuery .= " ".$this->where." ORDER by bean.".$this->order." ".$descExpression." ".$this->sqlLimit." OFFSET ".$this->start;

    
    $rows = $this->db->query($sqlQuery);
    return $rows->result();
  }

  protected function _list(){
    $aliasTable = "";
    if($this->joinTable) {
      $aliasTable = "bean";
    }

    $sqlQuery = "SELECT";
    if($this->usingCommandAlias) {
      $sqlQuery .= " ".$this->selectAlias;
    } else {
      $sqlQuery .=" * ";
    }
    $descExpression = "asc";
    if(isset($this->desc) && $this->desc)
     $descExpression = "desc";
   $sqlQuery .= " from ".$this->tableName." ".$aliasTable." ".$this->where." ORDER by \"".$this->order."\" ".$descExpression;

    if($this->length!='-1')
      $sqlQuery.= " ".$this->sqlLimit." OFFSET ".$this->start;


    $rows = $this->db->query($sqlQuery);
    return $rows->result();
  }

  protected function _rowOption(){
    $aliasTable = "";
    if($this->joinTable) {
      $aliasTable = "bean";
    }

    $descExpression = "asc";
    if(isset($this->desc) && $this->desc)
      $descExpression = "desc";

    $where = $this->_generalCondition();
    $sqlQuery = "SELECT ".$this->selectCmd." from ".$this->tableName." ".$aliasTable." ".$this->where." ORDER by \"".$this->order."\" ".$descExpression ;
   
    $rows = $this->db->query($sqlQuery);
    return $rows->result();
  }

  protected function _countList(){
      $aliasTable = "";
      $selectId = "id";
      if($this->joinTable) {
        $aliasTable = "bean";
        $selectId = $aliasTable.".id";
      }

      $numRow = 0;
      $sqlQuery = "Select count(".$selectId.") as count from ".$this->tableName." ".$aliasTable." " .$this->where;

      $rowCount = $this->db->query($sqlQuery);
      $resultRowCount = $rowCount->result();
      if(count($resultRowCount) > 0)
        $numRow = $resultRowCount[0]->count;
      return $numRow;
  }

  protected function _successResponse($client, $arrResponse,$rowCount){
    if($client == 'datatables') {
      $response['aaData'] = $arrResponse;
     // $response['iTotalRecords'] = $rowCount;
      // $response['iTotalDisplayRecords'] = $this->length;

      $response['iTotalRecords'] = count($arrResponse); // $this->length;
      $response['iTotalDisplayRecords'] = $rowCount;
      $response['sEcho']= $this->sEcho;
      $response['processTime']= null;
      $response['success']= true;
    }else {
      $this->endProcess = microtime(true);
      $totalProcessTime = $this->endProcess - $this->startProcess;
      $response['data'] = $arrResponse;
      $response['totalRecord'] = $rowCount;
      $response['message'] = null;
      $response['processTime']= $totalProcessTime." sec.";
      $response['success']= true;
    }
    // $this->set_response($response, REST_Controller::HTTP_OK);
    $this->_set_custome_response($response);
  }

  protected function _set_custome_response($response,$httpcode=200){
    $this->output
  ->set_status_header($httpcode)
  ->set_content_type('application/json', 'utf-8')
  ->set_output(json_encode($response, JSON_PRETTY_PRINT))
  ->_display();
  exit;
  }

  protected function _generalCondition(){
    // $aliasTable = "";
    // $conditionalCompany = "company_id";
    // $conditionalVehicle = "vehicle_id";
    // if($this->joinTable && $this->needCompany ) {
    //   $aliasTable = "bean";
    //   $conditionalCompany = $aliasTable.".".$conditionalCompany;
    //   $conditionalVehicle = $aliasTable.".".$conditionalVehicle;
    // }

    // $strWhere = "";
    // if($this->needCompany && ($this->_apiuser->company_allowed !="" || $this->_apiuser->company_allowed != null )) {
    //   $strWhere .=" WHERE ".$conditionalCompany." in (".$this->_apiuser->company_allowed . ")";
    // }
    
    // if($this->needVehicle && ($this->_apiuser->veicle_allowed !="" || $this->_apiuser->vehicle_allowed != null )) {
    //   if($strWhere != "") {
    //     $strWhere .=" AND ";
    //   }else {
    //     $strWhere .=" WHERE ";
    //   }
    //   $strWhere .= " ".$conditionalVehicle." in (".$this->_apiuser->vehicle_allowed .")";
    // }

    // if($this->haveActive) {
    //     if($strWhere != "") {
    //         $strWhere .=" AND ";
    //      }else {
    //         $strWhere .=" WHERE ";
    //      }
    //     $strWhere .= " bean.active=true";
    // }

    // if(isset($this->params) && $this->params!="") {
    //   // echo "params : " . $this->params;
    //   $arr_params = json_decode($this->params, true);
    //   $i=0;
    //   $strWhereParams = "";
    //   foreach ($arr_params as $key => $value) {

    //     if($value != ""){
    //       if($i>0) {
    //           $strWhereParams .= " AND ";
    //       }
    //       $pos = array_search($key,$this->keySearch);
    //       if ($pos !== false) {
    //           $strWhereParams .= "LOWER(bean.".$key . ") like '%".strtolower($value)."%'";
    //        } else {
    //           $strWhereParams .= "bean.".$key . "='".$value."'";
    //       }
            
    //       $i++;
    //     }
    //   }

    //   if($strWhereParams!="") {
    //     if($strWhere != "") {
    //       $strWhere .=" AND ";
    //     }else {
    //       $strWhere .=" WHERE ";
    //     }
    //     $strWhere .= " ".$strWhereParams;
    //   }

    // }

    // return $strWhere="";
    $strWhere="";
  }

  protected function updateCompanyAccess($userId){
    $sqlQuery = "select * from r_user_company_access where user_access_id=".$userId;
    $result = $this->db->query($sqlQuery);
    $i=0;
    $strCompanyAccess = "";
    foreach ($result->result_array() as $key => $row) {
      if($i>0)
        $strCompanyAccess .=",";

      $strCompanyAccess .=$row['company_access_id'];

      $i++;
    }
    $this->_apiuser->company_allowed = $strCompanyAccess;


    // check record is exist
    $sqlQuery = "select * from m_user_access where user_id=".$userId;
    $result = $this->db->query($sqlQuery);
    if(count($result->result_array())==0) {
      $today = date("Y-m-d");
      // insert
      $sqlInsert = "INSERT into m_user_access(user_id,company_access,last_update) values (".$userId.",'".$strCompanyAccess."','".$today."')";
      $query = $this->db->query($sqlInsert);
    }else {
      // update record
      $sqlUpdateQuery = "UPDATE m_user_access set company_access='".$strCompanyAccess."' where user_id=".$userId;
      $this->db->simple_query($sqlUpdateQuery);
    }
  }

  protected function updateVehicleAccess($userId){
    $sqlQuery = "select * from r_user_vehicle_access where user_access_id=".$userId;
    $result = $this->db->query($sqlQuery);
    $i=0;
    $strVehicleAccess = "";
    foreach ($result->result_array() as $key => $row) {
      if($i>0)
        $strVehicleAccess .=",";

      $strVehicleAccess .=$row['vehicle_access_id'];
      $i++;
    }
    $this->_apiuser->vehicle_allowed = $strVehicleAccess;

    // update field company_allowed

    // check record is exist
    $sqlQuery = "select * from m_user_access where user_id=".$userId;
    $result = $this->db->query($sqlQuery);
    if(count($result->result_array())==0) {
      $today = date("Y-m-d");
      // insert
      $sqlInsert = "INSERT into m_user_access(user_id,vehicle_access,last_update) values (".$userId.",'".$strVehicleAccess."','".$today."')";
      $query = $this->db->query($sqlInsert);
    }else {
      // update record
      $sqlUpdateQuery = "UPDATE m_user_access set vehicle_access='".$strVehicleAccess."' where user_id=".$userId;
      $this->db->simple_query($sqlUpdateQuery);
    }
  }

  protected function _getObject($id=0,$table_name) {
    $result = null ;
    if($id!=0) {
      $sql_query = "SELECT * FROM ".$table_name." WHERE id=".$id;
      $result = $this->db->query($sql_query)->row();
      if(count($result) > 0) {
        return $result;
      }
    }
    return $result;
  }

  protected function index_options(){
    echo "options method";
    exit;
  }

  protected function id_options(){
    echo "options method";
    exit;
  }

  protected function cmd_options(){
    echo "options method";
    exit;
  }

}

?>
