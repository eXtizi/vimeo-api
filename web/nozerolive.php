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

$pageContent = '';
$pageContentx = '';
if($_POST){
	$playlist = $_POST['playlist'] ?? '';
	$match = $_POST['match'] ?? '';
	$replace = $_POST['replace'] ?? '';
	$multiline = $_POST['multiline'] ?? false;
	$dmn = $_POST['dmn'] ?? false;
	$nocase = $_POST['nocase'] ?? false;
	$random = uniqid().'-playlist.m3u8';
	if($match != ''){
		$modifiers = (($multiline == true) ? 'm':'').(($dmn == true) ? 's':'').(($nocase == true) ? 'i':'');
		$playlist = preg_replace('#'.$match.'#'.$modifiers, $replace, $playlist);
	}
	file_put_contents($random, $playlist);
	if(isset($_POST['api'])){
		echo json_encode(array('ok' => true, 'data' => "https://".$_SERVER['HTTP_HOST']."/".$random));
		exit;
	}
	$pageContent .= <<<EOL
	<a href="{$random}" target="_blank">playlist link</a>
	<br>
	<br>
<form method="post">
	<textarea placeholder="playlist" name="playlist"></textarea><br>
	<input type="text" placeholder="match [supports regex, delimiter is '#'] (optional)" value="" name="match"></input><br>
	<input type="text" placeholder="replace [supports regex, delimiter is '#'] (optional)" value="" name="replace"></input><br><br>
	<input type="checkbox" name="multiline" value="true" id="multiline" checked></input><label for="multiline">multiline</label>
	<input type="checkbox" name="dmn" value="true" id="dmn"></input><label for="dmn">dot matches newline</label>
	</input><input type="checkbox" value="true" name="nocase" id="nocase"></input><label for="nocase">case insensitive</label><br><br>
	<input type="submit" value="SAVE"></input>
</form>
EOL;
}
else{
	$pageContent .= <<<EOL
<form method="post">
	<textarea placeholder="playlist" name="playlist"></textarea><br>
	<input type="text" placeholder="match [supports regex, delimiter is '#'] (optional)" value="" name="match"></input><br>
	<input type="text" placeholder="replace [supports regex, delimiter is '#'] (optional)" value="" name="replace"></input><br><br>
	<input type="checkbox" name="multiline" value="true" id="multiline" checked></input><label for="multiline">multiline</label>
	<input type="checkbox" name="dmn" value="true" id="dmn"></input><label for="dmn">dot matches newline</label>
	</input><input type="checkbox" value="true" name="nocase" id="nocase"></input><label for="nocase">case insensitive</label><br><br>
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
