<?php
/**
 * 
 * wechat basic callback
 * @author pacozhong
 *
 */

require_once dirname(__FILE__) . '/../common/Common.php';
class WeChatCallBack {
	protected $_postObject;//解析到的xml对象
	protected $_fromUserName;
	protected $_toUserName;
	protected $_createTime;
	protected $_msgType;
	protected $_msgId;
	protected $_time;
	
    public function getToUserName() {
    	return $this->_toUserName;
    }
    
//     组装提示信息，HINT_TPL在GlobalDefine.php中定义，为微信消息xml字符串格式定义
	/**
	 * public:权限是最大的，可以内部调用，实例调用等。
	 * protected: 受保护类型，用于本类和继承类调用。
	 * private: 私有类型，只有在本类中使用。
	 * @param unknown $hint
	 * @return string
	 */
    protected  function makeHint($hint) {
    	$resultStr = sprintf ( HINT_TPL, $this->_fromUserName, $this->_toUserName, $this->_time, 'text', $hint );
		return $resultStr;
    }
	
    /**
     * 也把解析到的 XML 对象作为这个类的成员变量$_ postObject 并在 init 中赋值，目的是在实现具体公众账号的业务逻辑时，具体的各类消息的特殊字段可以通过它来获取。
     * @param unknown $postObj
     * @return boolean
     */
	public function init($postObj) {
		// 获取参数
		$this->_postObject = $postObj;
		if ($this->_postObject == false) {
			return false;
		}
		$this->_fromUserName = ( string ) trim ( $this->_postObject->FromUserName );
		$this->_toUserName = ( string ) trim ( $this->_postObject->ToUserName );
		$this->_msgType = ( string ) trim ( $this->_postObject->MsgType );
		$this->_createTime = ( int ) trim ( $this->_postObject->CreateTime );
		$this->_msgId = ( int ) trim ( $this->_postObject->MsgId );
		$this->_time = time ();
		if(!($this->_fromUserName && $this->_toUserName && $this->_msgType)) {
			return false;
		}
		return true;
	}
	
	/*
	 * 实现具体公众账号的业务逻辑时，需要重载的函数
	 * 重载就是方法名相同，参数（个数或类型）不同（称之为签名不同）
	 */
	public function process() {
// 		HINT_NOT_IMPLEMEMT 在GlobalDefine.php中定义的一个提示信息
		return $this->makeHint(HINT_NOT_IMPLEMEMT);
	}
	
    
   		
}