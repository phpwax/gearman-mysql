<?php
namespace GearmanMysql;

/* New Model */

class Queue extends WaxModel {
  
  public function setup() {
    $this->define("model", "CharField", array());
    $this->define("method", "CharField", array());
    $this->define("data", "TextField", array());
    $this->define("run_after", "DateTimeField", array());
    $this->define("run_completed", "DateTimeField", array());
    $this->define("priority", "IntegerField", array());
    $this->define("run_result", "TextField", array());
    $this->define("status", "IntegerField", array("default"=>0));
    $this->define("lock", "IntegerField", array("default"=>0));
  }
  
  
  public function before_insert() {
    $this->data = serialize($this->data);
  }
  
  
  public function run() {
    $jq = new Queue("runnable");
    $jobs_to_run = $jq->all();
    foreach($jobs_to_run as $job) {
      $job->lock = 0;
      $job->save();
      $commands = array($job->model, $job->method);
      $result = call_user_func_array($commands, unserialize($job->data));
      if($result) {
        $job->run_completed = date('Y-m-d H:i:s');
        $job->result = $result;
        $job->status = 1;
        $job->lock = 0;
        $job->save();
      } else {
       $job->result = 0;
       $job->lock = 0;
       $job->save(); 
      }
    }
    
  }
  
  public function scope_runnable() {
    return $this->filter("status", 1,"!=")->filter("lock",0)->filter("TIMESTAMPDIFF(SECOND, `run_after`, NOW()) >= 0")->order("run_after DESC");
  }
  
  
  
}

