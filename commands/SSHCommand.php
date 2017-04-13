<?php


class SSHCommand extends CConsoleCommand {
   

    /**
     * __construct function
     */
    public function __construct() {
       

    }

    /**
     * Main function 
     * @return nothing. Just die
     */
    public function actionRunTasks($csid, $dvid) { 

        $devicesListArr = ['127.0.0.1','127.0.0.2'];

        $threads = array();
        foreach ($devicesListArr as $i=>$ip) {
            $threads[$i] = new YiiThreadSSH([], array('ip'=>$ip, 'user'=>'ROOT', 'pwd'=>'123', 'commands'=>'ls,dir,...'));

        }




        // Start The Threads
        foreach ($threads as $i=>$obj) {
            $obj->start();
        }
    

        $results = array();
        foreach ($threads as $i => $obj) {
            $ip = $obj['ip'];
            $threads[$i]['ThreadObj']->join();

            $result=array(
                'starttime'=>$threads[$i]['ThreadObj']->starttime,
                'STDOUT'=>$threads[$i]['ThreadObj']->STDOUT,
                'STDERROR'=>$threads[$i]['ThreadObj']->STDERROR,
                'CODE'=>$threads[$i]['ThreadObj']->CODE,
                'endtime'=>$threads[$i]['ThreadObj']->endtime,
            );
            array_push($results, $result);    
        }

    }
   
   

}


class YiiThreadSSH extends Thread {
    
    /**
     * 
     * ..... 
     * 
     */
    
    
    
    public function __construct( $aomedata ) {
     
    }

    public function run() {
        $pathToPuttyExeFile = '.../putty.exe';
        
        $AnsiToHTMLConverter = ANSItoHTML5::getInstance();
        $Plink = new PuttyThread($AnsiToHTMLConverter, $pathToPuttyExeFile);
        
        
        //here i need to open putty using IP , USER , PASSWORD and run commands
        $responseArr = $Plink->executeCommandScripts($ip, $commandsArr);
        //return putty output 
        
        $this->output = json_encode($responseArr);
    }
    
}


class PuttyThread  {
    
    public function executeCommandScripts($configArr) {
        
        $this->_settings['ip'] = $configArr['ip'];
        $this->_settings['user'] = $configArr['user'];
        $this->_settings['pwd'] = $configArr['pwd'];
        $this->_settings['commands'] = $configArr['commands'];
        $this->_settings['tmpErrFile'] = tmpfile();
        $this->_settings['tmpOutputFile'] = tmpfile();
        
        if ( ! $this->_startRunPlink()){
            $this->_destroy();

    
            return array(
                'out' => '',
                'error' => 'error occurred while starting plink for ip='.$this->_settings['ip'],
                'code' => 1
            );
        }
      
        return $this->_executeCommands( );
    }
    
    function _executeCommands (){
        $plinkStillRunning = true;
        while($plinkStillRunning){
                   
            //here i only write commands to STDIN and read output 
            //thant all here
        }
        
        return [
            'stdout'=>'somestring',
            'stderror'=>'somestring',
        ];
    }
    
    
     private function _startRunPlink (){
        $descriptorSpec = array(
            0 => ['pipe', 'r'],
            1 => $this->_settings['tmpOutputFile'],
            2 => $this->_settings['tmpErrFile'],
        );
         

        $env = NULL;
        $options = array('bypass_shell' => false);
        $cwd = NULL;
        
        $user = $this->_settings['user'];
        $ip = $this->_settings['ip'];
        $pwd = $this->_settings['pwd'];

        $this->_process = proc_open('"'.$this->_pathToPuttyExe."\"  -auto_store_key_in_cache -ssh $user@$ip -pw $pwd", $descriptorSpec, $this->_pipes, $cwd, $env, $options);
        if ( ! is_resource( $this->_process) ) {
            return false;
        }
        
        $this->_settings['pid'] = proc_get_status($this->_process)['pid'];
      
        return true;
    }
    
}
