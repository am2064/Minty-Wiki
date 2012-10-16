<?php
include_once "markdown.php";

$nav=array();
if(isset($_GET['entry'])){
	$nav=array(
		"HOME"=>".",
		"SOURCE"=>curPageURL()."&edit=true"
	);
}

$wikiName="WIKI NAME";

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

/*
function random_color(){
    mt_srand((double)microtime()*1000000);
    $c = '';
    while(strlen($c)<6){
        $c .= sprintf("%02X", mt_rand(0, 255));
    }
    return $c;
}
*/

function readDirectory($dir,$level=0){
	$notDirs=array();
	#$bgcolor=random_color();
	echo "<ul class='category' id='$level'>\n";
	if ($handle = opendir($dir)) {
	    while (false !== ($entry = readdir($handle))) {
		if(!preg_match('/^\..*/',$entry)){
			if (is_dir("$dir/$entry")){
				$level+=1;
				echo "<li>Category:$entry</li>\n"; 
				readDirectory("$dir/$entry",$level);
			}
		}
		if (preg_match('/^.*\.(md|MD|markdown|MarkDown|text|Text|TEXT|txt|TXT)/',$entry)) {
			$entryURL='entry='.urlencode($dir).'/'.urlencode($entry);
		    array_push($notDirs, "<li><a href=$url?".htmlentities($entryURL).">$entry</a></li>\n");
		}
	    }
		while($entryExt=array_pop($notDirs)){
			echo $entryExt;
		}
	    closedir($handle);
	}
	echo "</ul>\n";
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

function updateArticle($article,$update){
	$file = fopen($article,"r");
	$new_file = fopen("$article".date("DmYHi"),"w");
	$fileContents = fread($file,filesize("$article"));
	if(is_writeable("$article".date("DmYHi"))){
		if(fwrite($new_file,$fileContents) === FALSE){
			echo "Could not write $article backup.<br>";
			exit;
		}
		else{echo "$article has been backed up.<br>";}
	}
	else{echo "Could not write to $article".date("DmYHi").".<br>";}
	if(is_writeable("$article")){
		if(fwrite($file,$update) === FALSE){
			echo "Could not update $article update.<br>";
			exit;
		}
		else{echo "$article has been updated.<br>";}
	}
	else{echo "Could not write to $article.<br>";}
	fclose($file);
	fclose($new_file);
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
<hr>
<?php 
$url = curPageURL();

if($_POST['article']){
	if($_POST['update']){
		$article=$_POST['article'];
		$update=$_POST['update'];
		updateArticle($article,$update);
		echo "$article updated!";
	}
}

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
		<textarea name="update" rows="25" cols="100"><?php readDirFileRAW('.');?></textarea>
		<br/>
		<input type="submit" value="Submit">
	</form>
<?php
}
?>
<hr>
<div class="nav">
<?php
foreach($nav as $show=>$html){
	echo "[<a href=$html>$show</a>]";
}
?>
</div>
</body>
</html>
