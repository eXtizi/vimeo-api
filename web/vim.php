<?php
$update = json_decode(file_get_contents('php://input'), true);
define('ADMIN', '960046858');
define('TOKEN', '1904043523:AAEkZMGLJEDrKbnV5Ry8nQvqylBTKHF8_w4');
define('CHAT_ID', $update["message"]["chat"]["id"]);
define('MESSAGE', $update["message"]["text"]);
define('MESSAGE_ID', $update["message"]["message_id"]);

if(MESSAGE == '/start'){
	send_message(CHAT_ID, "Welcome");
}
$conf = explode("\n", trim(MESSAGE));
if(count($conf) == 2){
	$vimeoconfig = vim($conf[0], $conf[1]);
	send_message(CHAT_ID, $vimeoconfig);
}

function vim($vim, $ref){
	$headers = array(
		'Host: player.vimeo.com',
		'User-Agent: Mozilla/5.0 (Linux; U; Android 2.2; en-us; Droid Build/FRG22D) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
		'Accept-Encoding: gzip, deflate, br',
		'Referer: '.$ref
	);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://player.vimeo.com/video/'.$vim);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	//curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8080');
	$response = curl_exec($ch);
	curl_close($ch);
	$content = '';
	if(preg_match('#<title>(.+)</title>#', $response, $m1)){
		$content .= $m1[1]."\n";
	}
	if(preg_match('#var config = ({.*}); if \(#', $response, $m2)){
		$conf = json_decode($m2[1], true);
		foreach($conf['request']['files']['progressive'] as $progressive){
			$content .= "Quality: {$progressive['quality']}\nUrl: <a href=\"{$progressive['url']}\">{$progressive['quality']}</a>";
		}
		$content .= $conf['video']['thumbs']['base'];
	}
	return $content;
}
function send_message($id, $text){
	$apiURL = 'https://api.telegram.org/bot'.TOKEN.'/sendMessage';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $apiURL);
	curl_setopt($ch, CURLOPT_HEADER,false);
	curl_setopt($ch, CURLOPT_POST, true );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt($ch, CURLOPT_POSTFIELDS, array('chat_id'=>$id, 'text'=>$text, 'parse_mode'=>'HTML'));
	$result=curl_exec($ch);
	return json_decode($result, true);
}
?>