<?php
use GearmanMysql;
$queue_runner = new Worker;

$worker = new GearmanWorker();
$worker->addServer('localhost');
$worker->addFunction('reverse', array($queue_runner, "run"));
while ($worker->work());