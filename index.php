<?php
include_once "markdown.php";

$editting=false;
$backup=false;
$wikiName="WIKI NAME";
#$css="webroot/path/to/css/style.css"; //Uncomment this to use css files.
$user_nav=array(
/*
Users can place their own header entries here if they wish for them to be after HOME and EDIT
"link"=>"url"

Some manuals for PHP Markdown Extra have been included as an example
*/
"PHP Markdown Extra Cheat Sheet"=>"http://tech.thingoid.com/2006/01/markdown-cheat-sheet/index.html",
"Markdown Manual"=>"http://daringfireball.net/projects/markdown/syntax",
"PHP Markdown Extra Manual"=>"http://michelf.ca/projects/php-markdown/extra/"
);
	$nav=array(
		"HOME"=>".",
	);
if(isset($_GET['entry'])){
	if(!isset($_GET['edit'])&&$editting){
		$nav+=array("EDIT"=>curPageURL()."&edit=true");
	}
	else if(!isset($_GET['edit'])&&!$editting){
		$nav+=array("SOURCE"=>curPageURL()."&edit=true");
	}
}
$nav=array_merge($nav,$user_nav);
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

function readDirectory($dir,$level=0){
	$notDirs=array();
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
		if (preg_match('/^.*\.(md|MD|markdown|MarkDown|text|Text|TEXT|txt|TXT)$/',$entry)) {
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

	$validEntry = $_GET['entry'];
	if (strpos($validEntry, '..') !== false) {
	// entry is invalid, let's show them the main page
		readDirectory('.');
	} else {
	// entry is valid, carry on
		$file = $dir."/".$validEntry;
		$mark = fopen($file,"r");
		$markContents = fread($mark,filesize("$file"));
		echo Markdown($markContents);
		fclose($mark);
	}
}

function readDirFileRAW($dir){
	$file = "$dir"."/".$_GET['entry'];
	$mark = fopen($file,"r");
	$markContents = fread($mark,filesize("$file"));
	echo $markContents;
	fclose($mark);
}

function updateArticle($article,$update){
	if($backup){
	$file = fopen($article,"r");
	$fileContents = fread($file,filesize("$article"));
	$new_file = fopen("$article".date("dmYHi"),"w");
	if(is_writeable("$article".date("dmYHi"))){
		if(fwrite($new_file,$fileContents) === FALSE){
			echo "Could not write $article backup.<br>";
			exit;
		}
		else{echo "$article has been backed up.<br>";}
	}
	else{echo "Could not write to $article".date("dmYHi").".<br>";}
	fclose($file);
	fclose($new_file);
	}
	$file = fopen($article,"w");
	if(is_writeable("$article")){
		if(fwrite($file,$update) === FALSE){
			echo "Could not update $article.<br>";
			exit;
		}
		else{echo "$article has been updated.<br>";}
	}
	else{echo "Could not write to $article.<br>";}
	fclose($file);
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
</style>
<?php
if(isset($css)){
	print "<link rel='stylesheet' href='$css'>";
}
?>
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
	$validEntry = $_GET['entry'];
	if (strpos($validEntry, '..') !== false) {
	// entry is invalid, let's show them the main page
		readDirectory('.');
	} else {
?>
	<form action="." method="post">
		<input type="hidden" name="article" value="<?php echo $_GET['entry']; ?>" >
		<textarea name="update" rows="25" cols="100"<?php if(!$editting) echo "disabled";?>><?php readDirFileRAW('.');?></textarea>
		<br/>
		<?php if($editting){?>
		<input type="submit" value="Submit">
		<?php } ?>
	</form>
<?php
	}
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
