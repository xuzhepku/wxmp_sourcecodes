<?php
$startTime = microtime(true);
require_once dirname(__FILE__) . '/common/Common.php';

/**
 * 使用 checkSignature 函数验证请求是否合法，在不合法的情况下，记录下恶意来源 IP。
 * @return boolean
 */
function checkSignature()
{
	$signature = $_GET["signature"];
	$timestamp = $_GET["timestamp"];
	$nonce = $_GET["nonce"];

	$token = WEIXIN_TOKEN;
	$tmpArr = array($token, $timestamp, $nonce);
	sort($tmpArr);
	$tmpStr = implode( $tmpArr );
	$tmpStr = sha1( $tmpStr );

	if( $tmpStr == $signature ){
		return true;
	}else{
		return false;
	}
}

// 使用 checkSignature 函数验证请求是否合法，在不合法的情况下，记录下恶意来源 IP。
if(checkSignature()) {
	if($_GET["echostr"]) {
		echo $_GET["echostr"];
		exit(0);
	}
} else {
	//恶意请求：获取来来源ip，并写日志
	$ip = getIp();
	interface_log(ERROR, EC_OTHER, 'malicious: ' . $ip);
	exit(0);
	
}


function getWeChatObj($toUserName) {
	if($toUserName == USERNAME_FINDFACE) {
		require_once dirname(__FILE__) . '/class/WeChatCallBackFindFace.php';
		return new WeChatCallBackFindFace();
	}
	if($toUserName == USERNAME_MR) {
		require_once dirname(__FILE__) . '/class/WeChatCallBackMeiri10futu.php';
		return new WeChatCallBackMeiri10futu();
	}
	if($toUserName == USERNAME_ES) {
		require_once dirname(__FILE__) . '/class/WeChatCallBackEchoServer.php';
		return new WeChatCallBackEchoServer();
	}
	if($toUserName == USERNAME_MYZL) {
		require_once dirname(__FILE__) . '/class/WeChatCallBackMYZL.php';
		return new WeChatCallBackMYZL();
	}
	require_once dirname(__FILE__) . '/class/WeChatCallBack.php';
	return  new WeChatCallBack();
}

function exitErrorInput(){
	echo 'error input!';
	interface_log(INFO, EC_OK, "***** interface request end *****");
	interface_log(INFO, EC_OK, "*********************************");
	interface_log(INFO, EC_OK, "");
	exit ( 0 );
}

$postStr = file_get_contents ( "php://input" );

interface_log(INFO, EC_OK, "");
interface_log(INFO, EC_OK, "***********************************");
interface_log(INFO, EC_OK, "***** interface request start *****");
interface_log(INFO, EC_OK, 'request:' . $postStr);
interface_log(INFO, EC_OK, 'get:' . var_export($_GET, true));

if (empty ( $postStr )) {
	interface_log ( ERROR, EC_OK, "error input!" );
	exitErrorInput();
}
// 获取参数
// 获取 POST 数据，解析 XML 数据
$postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
if(NULL == $postObj) {
	interface_log(ERROR, 0, "can not decode xml");	
	exit(0);
}
// 根据 ToUserName 来决定是发往哪一个公众账号的消息，然后加载对应的文件，获得对应的对象（ getWeChatObj 函数）。
$toUserName = ( string ) trim ( $postObj->ToUserName );
if (! $toUserName) {
	interface_log ( ERROR, EC_OK, "error input!" );
	exitErrorInput();
} else {
	$wechatObj = getWeChatObj ( $toUserName );
}
// 调用对象的 init 函数初始化对象。 ❑ 调用对象的 process 函数处理公众账号逻辑
$ret = $wechatObj->init ( $postObj );
if (! $ret) {
	interface_log ( ERROR, EC_OK, "error input!" );
	exitErrorInput();
}
// 调用对象的 process 函数处理公众账号逻辑，得到返回消息字符串。
$retStr = $wechatObj->process ();
interface_log ( INFO, EC_OK, "response:" . $retStr );
echo $retStr;


interface_log(INFO, EC_OK, "***** interface request end *****");
interface_log(INFO, EC_OK, "*********************************");
interface_log(INFO, EC_OK, "");
$useTime = microtime(true) - $startTime;
interface_log ( INFO, EC_OK, "cost time:" . $useTime . " " . ($useTime > 4 ? "warning" : "") );
?>
