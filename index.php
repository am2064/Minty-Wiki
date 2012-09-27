<?php
include_once "markdown.php";

$nav=array(
	"HOME"=>".",
	"EDIT"=>curPageURL()."&edit=true"
);

$wikiName="WIKI NAME";
$indent="|";

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

function random_color(){
    mt_srand((double)microtime()*1000000);
    $c = '';
    while(strlen($c)<6){
        $c .= sprintf("%02X", mt_rand(0, 255));
    }
    return $c;
}

function readDirectory($dir,$level=0){
	$notDirs=array();
	$bgcolor=random_color();
	#$spaces = str_repeat( "<strong class='indenter'>|</strong>", ( $level ) ); 
	$spaces = str_repeat( $indent, $level ); 
	if ($handle = opendir($dir)) {
	    while (false !== ($entry = readdir($handle))) {
		if(!preg_match('/^\..*/',$entry)){
			if (is_dir("$dir/$entry")){
				$level+=1;
				echo "<div class='directory'>\n";
				echo "<strong>$spaces Category:$entry</strong><br />\n"; 
				readDirectory("$dir/$entry",$level);
				echo "</div>\n";
			}
		}
		if (preg_match('/^.*\.(md|MD|markdown|MarkDown|text|Text|TEXT|txt|TXT)/',$entry)) {
			$entryURL='entry='.urlencode($dir).'/'.urlencode($entry);
		    array_push($notDirs, "$spaces<a href=$url?".htmlentities($entryURL).">$entry</a><br>\n");
		}
	    }
		while($entryExt=array_pop($notDirs)){
			echo $entryExt;
		}
	    closedir($handle);
	}
}

function readDirFile($dir){
	$file = "$dir"."/".$_GET['entry'];
	$mark = fopen($file,"r");
	$markContents = fread($mark,filesize("$file"));
	echo Markdown($markContents);
	fclose($mark);
}

function readDirFileRAW($dir){
	$file = "$dir"."/".$_GET['entry'];
	$mark = fopen($file,"r");
	$markContents = fread($mark,filesize("$file"));
	echo $markContents;
	fclose($mark);
}
?>
<html>
<head>
<style type="text/css">
p{font-family:"Times New Roman", Times, serif;}
body{background-color:99FFCC;}
.search{
	text-align:right;
}
.nav{
	float:left;
}
.indenter{
	font-family:monospace;
}
/*
.directory{
	border-style:solid none solid solid;
	color:#000000;
}
*/
@import wiki.css;
</style>
</head>
<body>
<div class="head">
<div class="nav">
<?php
foreach($nav as $show=>$html){
	echo "[<a href=$html>$show</a>]";
}
?>
</div>
<div class="search">
	<?php echo $wikiName; ?>
</div>
</div>
<?php 
echo "<hr>\n";
$url = curPageURL();

if (!$_GET['edit']){
	if (empty($_GET['entry'])){
		readDirectory('.');
	}
	if ( isset($_GET['entry'])){
		readDirFile('.');
	}
}
if ($_GET['edit']){
?>
	<form action="." method="post">
		<input type="hidden" name="article" value="<?php echo $_GET['entry']; ?>" >
		<textarea rows="25" cols="100"><?php readDirFileRAW('.');?></textarea>
		<br/>
		<input type="submit" value="Submit">
	</form>
<?php
}
echo "<hr>\n";
?>
<div class="nav">
<?php
foreach($nav as $show=>$html){
	echo "[<a href=$html>$show</a>]";
}
?>
</div>
</body>
</html>
