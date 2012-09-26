<?php
namespace GearmanMysql;

/* New Model */

class Queue extends \WaxModel {
  
  public function setup() {
    $this->define("model", "CharField", array());
    $this->define("method", "CharField", array());
    $this->define("data", "TextField", array());
    $this->define("priority", "IntegerField", array());
    $this->define("run_after", "DateTimeField", array());
    $this->define("run_completed", "DateTimeField", array());
    $this->define("run_result", "TextField", array());
    $this->define("status", "IntegerField", array("default"=>0));
    $this->define("lock", "IntegerField", array("default"=>0));
  }
  
  
  public function before_insert() {
    $this->data = serialize($this->data);
  }
  
  
  public function model($model) {
    $this->model = $model;
    return $this;
  }
  
  public function method($method) {
    $this->method = $method;
    return $this;
  }
  
  public function data($data) {
    $this->data = serialize($data);
    return $this;
  }
  
  public function scope_runnable() {
    return $this->filter("status", 1,"!=")->filter("lock",0)->filter("TIMESTAMPDIFF(SECOND, `run_after`, NOW()) >= 0")->order("priority DESC, run_after DESC");
  }
  
  
  
}

