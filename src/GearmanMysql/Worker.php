<?php
namespace GearmanMysql;

/**
 * Base GearmanMysql Worker class
 * Simple responsibility, it just grabs the next available job, passing it on to the Class / Method registered
 *
 * Note returning anything other than Boolean FALSE will result in the job being completed and released
 *
 * @package default
 *
 **/
 

class Worker {
  
  public $logger = false;
    
  
  public function run() {
    echo "Attempting to run job from GearmanMysql";
    $jq = new Queue("runnable");
    $job_to_run = $jq->first();
    if(!$job_to_run) return false;
    
    $job_to_run->lock = 0;
    $job_to_run->save();
      
    $commands = array($job_to_run->model, $job_to_run->method);
    $result = call_user_func_array($commands, unserialize($job_to_run->data));
    if($result !== FALSE) {
      $job_to_run->run_completed = date('Y-m-d H:i:s');
      $job_to_run->result = $result;
      $job_to_run->status = 1;
      $job_to_run->lock = 0;
      $job_to_run->save();
    } else {
      $job_to_run->result = 0;
      $job_to_run->lock = 0;
      $job_to_run->save(); 
    }
    sleep(10);
    return $result;
    
  }
  

  
  
}