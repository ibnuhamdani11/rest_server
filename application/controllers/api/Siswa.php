<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . 'controllers/api/Restricted.php';
class Siswa extends Restricted {
  protected $tableName = "m_siswa";
  protected $selectCmd = "id,name";
  protected $needCompany = false;
  // penambahan
  protected $usingSelectSimple = false;
  // protected $selectSimple = "SELECT b.name as package_name, c.name as business_type_name, bean.* FROM m_company bean 
  // LEFT JOIN m_package b on (bean.package_id=b.id)
  // LEFT JOIN m_business_type c on (bean.business_type_id=c.id)";

  function __construct(){
      parent::__construct();
      /*$this->methods['user_get']['limit'] = 500; // 500 requests per hour per user/key
      $this->methods['user_post']['limit'] = 100; // 100 requests per hour per user/key
      $this->methods['user_delete']['limit'] = 50; // 50 requests per hour per user/key*/
  }

  function index_get($cmd="",$value=""){
    parent::index_get($cmd="",$value="");
  }

  function cmd_get($val){
    parent::cmd_get($val);
  }

  function id_get($val){
    parent::id_get($val);
  }

  function index_post(){
      parent::index_post();
  }

  function id_put($val){
      parent::id_put($val);
  }

  function id_delete($val){
      parent::id_delete($val);
  }
}
?>
