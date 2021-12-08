<?php
/* $p = <<<SSS
#EXTINF:9.920000,
nzkcvod-6-091C5A880F09U-6FB5E6087E
#EXTINF:10.000000,
nzkcvod-6-091C5A880F3389U-6FB5E6087E
#EXTINF:10.000000,
nzkcvod-6-091C5A880F3399U-6FB5E6087E
#EXTINF:10.000000,
nzkcvod-6-091C5A880F3409U-6FB5E6087E
#EXTINF:10.000000,
nzkcvod-6-091C5A880F3419U-6FB5E6087E
#EXTINF:10.000000,
nzkcvod-6-091C5A880F3429U-6FB5E6087E
#EXTINF:3.520000,
nzkcvod-6-091C5A880F3549U-6FB5E6087E
#EXT-X-ENDLIST
SSS;
preg_match_all('#^nzkcvod.+#m', $p, $m);
var_dump($m);
exit; */

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
	$content = array();
	if(preg_match('#<title>(.+)</title>#', $response, $m1)){
		$content['name'] = $m1[1];
	}
	if(preg_match('#var config = ({.*}); if \(#', $response, $m2)){
		$conf = json_decode($m2[1], true);
		foreach($conf['request']['files']['progressive'] as $progressive){
			$content[{$progressive['quality']}] = {$progressive['url']};
		}
		$content['thumb'] = $conf['video']['thumbs']['base'];
	}
	return $content;
}

$pageContent = '';
$pageContentx = '';
if($_POST){
	
	$id = $_POST['id'] ?? '';
	$ref = $_POST['ref'] ?? '';
	$qul = $_POST['qul'] ?? '';
	if($id != ''){
		$pageContent = vim($id, $ref);
		$playlist = preg_replace('#'.$match.'#'.$modifiers, $replace, $playlist);
	}
	file_put_contents($random, $playlist);
	if($id != ''){
		$pageContent = vim($id, $ref);
		echo json_encode(array('ok' => true, 'data' => $pageContent));
		exit;
	}
	$pageContent .= <<<EOL
	<a href="{$random}" target="_blank">playlist link</a>
	<br>
	<br>
<form method="post">
 	<input type="text" placeholder="Vimeo ID" value="" name="id"></input><br>
	<input type="text" placeholder="Reffer" value="" name="ref"></input><br>
	<input type="text" placeholder="Quality" value="" name="qul"></input><br><br>
	<input type="submit" value="SAVE"></input>
</form>
EOL;
}
else{
	$pageContent .= <<<EOL
<form method="post">
 	<input type="text" placeholder="Vimeo ID" value="" name="id"></input><br>
	<input type="text" placeholder="Reffer" value="" name="ref"></input><br>
	<input type="text" placeholder="Quality" value="" name="qul"></input><br><br>
	<input type="submit" value="SAVE"></input>
</form>
EOL;
}
$pageContent .= <<<STYLE
<style>
body{background-color:#000;}
textarea{background-color:#222;border:none;margin-top:15px;margin-bottom:5px;margin-left:5px;margin-right:5px;width:98%;height:200px;color:#0d0;font-family:consolas;font-size:20px;}
label{background-color:transparent;border:none;margin:5px;color:#f60;width:98%;height:25px;font-family:consolas;font-size:20px;}
input[type=text]{background-color:#222;border:none;margin:5px;color:#f60;width:98%;height:25px;font-family:consolas;font-size:20px;}
input[type=checkbox]{background-color:#222;border:none;margin:5px;}
input[type=submit]{background-color:#222;border:none;margin:5px;color:#f60;cursor:pointer;width:98%;height:25px;font-family:consolas;font-size:20px;}
a{color:#0a0;}
</style>
STYLE;
if(isset($_POST['api'])){
	echo json_encode(array('ok' => false, 'data' => ''));
	exit;
}
else{
	echo $pageContent;
}
?>
