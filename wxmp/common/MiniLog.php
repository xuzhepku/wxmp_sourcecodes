<?php
// 单例类，需要获取其实例时，请求public static函数instance
class MiniLog {
	private static $_instance;//单例
	private $_path;//日志目录
	private $_pid;//进程id
	private $_handleArr;//保存不同日志界别文件fd，是一个数组，内容可能为info、debug、error等分级，结果会产生不同的文件
	
	/**
	 * 构造函数，只会被instance函数调用
	 * @param unknown $path 日志对相对应的日志目录
	 */
	function __construct($path) {
		$this->_path = $path;
		$this->_pid = getmypid();
		
	}
	
	private function __clone() {
		
	}
	
	/**
	 * 单例函数
	 */
	public static function instance($path = '/tmp/') {
		if(!(self::$_instance instanceof self)) {
			self::$_instance = new self($path);
		}
		
		return self::$_instance;
	}
	
	/**
	 * 根据文件名称获取文件fd
	 * @param unknown $fileName 文件名
	 * @return resource 文件fd
	 */
	private function getHandle($fileName) {
		if($this->_handleArr[$fileName]) {
			return $this->_handleArr[$fileName];
		}
		date_default_timezone_set('PRC');
		$nowTime = time();
		$logSuffix = date('Ymd', $nowTime);
		$handle = fopen($this->_path . '/' . $fileName . $logSuffix . ".log", 'a');
		$this->_handleArr[$fileName] = $handle;
		return $handle;
	}
	
	/**
	 * 向文件中写入日志
	 * @param unknown $fileName 文件名
	 * @param unknown $message	消息
	 * @return boolean
	 */
	public function log($fileName, $message) {
		$handle = $this->getHandle($fileName);
		$nowTime = time();
		$logPreffix = date('Y-m-d H:i:s', $nowTime);
		fwrite($handle, "[$logPreffix][$this->_pid]$message\n");
		return true;
	}
	
	/**
	 * 析构函数，关闭所有fd
	 */
	function __destruct(){
		foreach ($this->_handleArr as $key => $item) {
			if($item) {
				fclose($item);
			}
		}
	}
}

?>