<?php

//die("Forbidden");//Uncomment if you don't need to use Muon ASSEMBLER!



if(!file_exists("interface.php")||!file_exists("core.php")||!file_exists("header.php"))
    die("Missing files");
function array_repl($file,$arr,$from,$to){
	$str = file_get_contents($file);
	foreach($arr as $a){
		$x=str_replace("%%",$a,$from);
		$y=str_replace("%%",file_get_contents($a),$to);
		$str=str_replace($x,$y,$str);
	}
	return $str;
};

?>
<title>Muon ASSEMBLER</title>
<h1>Muon assembler</h1>

<?php
if(isset($_POST["pntToFile"]) && $_POST["pntToFile"]=="yes"){
$postlink="<br/><a href=\"".basename($_SERVER['PHP_SELF'])."\">Reload page</a>";
// Code
$code="";
if (!isset($_POST["txACode"]) || !isset($_POST["txASett"]))
  die("ERROR: Missing code<br/>".$postlink);
$code = str_replace(" ?"."><"."?php "," ",$_POST["txASett"].$_POST["txACode"]);
// File name
$fname = [];
if (isset($_POST["fileExt"]) && $_POST["fileExt"]=="txt")
  $fname = ["muon.txt"];
else if (isset($_POST["fileExt"]) && $_POST["fileExt"]=="php")
  $fname = ["muon.php"];
else if (isset($_POST["fileExt"]) && $_POST["fileExt"]=="both")
  $fname = ["muon.php","muon.txt"];
else
  die("ERROR: Missing or wrong extension<br/>".$postlink);
foreach ($fname as $fnm){ 
  $fp = fopen($fnm, 'w');
  fwrite($fp, $code);
  fclose($fp);
  echo "Assembled to ".$fnm."!<br/>";
}
die($postlink);
}
?>

<form method="post" action="<?php echo basename($_SERVER['PHP_SELF']); ?>">

<h2>Part 1 - Settings</h2>
<p>You ought not to change this code</p>
<textarea id="txASett" name="txASett" style="width:600px;height:400px"><?php
$cont = array_repl("header.php",[ ],"","");
echo htmlspecialchars(str_replace("?><?php","",$cont));
?></textarea><br/> 
<br/> 

<h2>Part 2 - Code</h2>
<p>If you want to compress code (optional), copy this part to 
<a href="https://htmlcompressor.com/compressor/" target="_blank">https://htmlcompressor.com/compressor/</a>, 
compress it as <b>xhtml+php</b> with <b>Minimize PHP code</b> and <b>Never strip quotes</b> 
options, then copy it again here.<p>
<textarea id="txACode" name="txACode" style="width:600px;height:400px"><?php
$cont = array_repl("core.php",[ "functions.php", "classes.php", "requests.php" ],"require \"%%\";","?>%%<?php");
echo htmlspecialchars(str_replace("?><?php","",$cont));
$cont = array_repl("interface.php",[ "iconfav_base64", "iconapple_base64", "icons__svg", "script.js", "style.css" ],"<?php require \"%%\"; ?>","%%");
echo htmlspecialchars($cont);
?></textarea><br/>
<br/> 

<h2>Print to file</h2>

<p>Press the following button to build "muon.php" or "muon.txt" (then you'll have to manually change the extension) file.</p>
<p><b>Extension:</b> <input type="radio" name="fileExt" id="extPHP" value="php" /> 
<label for="extPHP">PHP</label> &nbsp; | &nbsp;
<input type="radio" name="fileExt" id="extTXT" value="txt" checked /> 
<label for="extTXT">TXT</label> &nbsp; | &nbsp;
<input type="radio" name="fileExt" id="extBOTH" value="both" /> 
<label for="extBOTH">BOTH</label></p>
<p><input type="submit" value="Compile to file" /></p>
<input type="hidden" value="yes" name="pntToFile" />
</form>

<h2>or print to textarea (via JS)</h2>
<script>
function finalComp(){
var t = document.getElementById("txASett").value + document.getElementById("txACode").value;
document.getElementById("txAOUT").value = t.replace(" ?"+"><"+"?php "," ");
}
</script>
<p>Press the following button and copy the code to a php file. That's going to be Muon.</p>
<p><button onclick="finalComp()">Print code</button></p>
<textarea id="txAOUT" style="width:600px;height:400px"></textarea><br/>
