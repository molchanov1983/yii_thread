<?php


$commandScriptId = 0;
$deviceViewId = 0;
if ( isset($argv[1]) ){
    $commandScriptId = $argv[1];
}
if ( isset($argv[2]) ){
    $deviceViewId = $argv[2];
}
// change the following paths if necessary
$config=dirname(__FILE__).'/protected/config/console.php';
$yii=dirname(__FILE__).'/yii/framework/yii.php';

// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once($yii);
$consoleAll = Yii::createConsoleApplication($config);
//$consoleAll = Yii::createConsoleApplication($config)->run();

$runner = $consoleAll->getCommandRunner();
$args = array('yiic', 'ssh', 'runTasks','--csid='.$commandScriptId, '--dvid='.$deviceViewId);
//$args = array('yiic', 'ssh', 'runTasks','--params='.$commandScriptId, '--dvid='.$deviceViewId);
//$args = array('yiic', 'test', 'run', "--params=");

$runner->run($args);
