<?php
/**
* @author coco
* @date 2016-04-15 11:11:07
* @todo 应用服务器
*/

namespace Lib;
class AppServer extends HttpServer{

	public $config;
	public static $ext = ['ico','html','jpg'];
	public function __construct($config){
		$this->config = $config;
		parent::__construct($config);
        set_error_handler(array(new Error(),'error'), E_ALL);
        set_exception_handler(array(new Error(),'exception'));
        register_shutdown_function(array(new Error(),'shutdown'));
		$this->setRootNS();
	}


	public function start(){
		ob_start();
		echo '<pre>';
		print_r($_SERVER);
		$ext = $this->getExt();
		$action = ucwords($this->getAct());
		$method = $this->getMethod();
		$act_class = '\\Act\\'.$action.'Action';
        if(!$ext){
        	$act_obj = new $act_class($this,$method);
	        print_r($act_obj);
	        $data = $act_obj->$method();
	        print_r($data);
        }
		$respData = ob_get_clean();
		$respData = $respData?$respData:$ext;
		$this->response($respData);
	}

	public function setRootNS(){
		// Loader::setRootNS('App',DOCUMENT_ROOT);
         Loader::setRootNS('Act',APP_PATH.'Act/');
        // Loader::setRootNS('Widget',WIDGET_PATH);
        // Loader::setRootNS('Task',DOCUMENT_ROOT.'/Task/');
        // Loader::setRootNS('Cron',CRON_PATH);
        // Loader::setRootNS('AppMod',APP_PATH.'Mod/');
        // Loader::setRootNS('Plugin',APP_PATH.'Plugin/');
         Loader::setRootNS('Task',FRAME_PATH.'task/');
	}
	/**
	 * [getAct 获取请求的action 默认为index]
	 * @return [type] [description]
	 */
	protected function getAct(){
		$path_info = trim(val($_SERVER,'path_info'),'/');
		if(empty($path_info)){
			return 'index';
		}else{
			$path_info = explode('/',$path_info);
			return $path_info[0];
		}
	}
	/**
	 * [getFunction 获取请求的function 默认为index]
	 * @return [type] [description]
	 */
	protected function getMethod(){

		$path_info = trim(val($_SERVER,'path_info'),'/');
		if(empty($path_info)){
			return 'index';
		}else{
			$path_info = explode('/',$path_info);
			return val($path_info,1,'index');
		}
	}


	protected function  getExt(){
		$path_info = trim(val($_SERVER,'path_info'),'/');
		$path_info = explode('.',$path_info);
		return val($path_info,1);
	}

}
