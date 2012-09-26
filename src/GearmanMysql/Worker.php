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
    
  
  public function run() {
    print_r($this);
    return true;
    $jq = new Queue("runnable");
    $job_to_run = $jq->first();
    
    $job_to_run->lock = 0;
    $job->save();
      
    $commands = array($job->model, $job->method);
    $result = call_user_func_array($commands, unserialize($job->data));
    if($result !== FALSE) {
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
    return $result;
    
  }
  

  
  
}