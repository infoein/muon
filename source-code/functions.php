<?php

// FUNCTIONS

// Validate user login
function validateUser($u,$p){
	if(!defined("mu_username")||!defined("mu_password"))
		return false;
	return $u==mu_username&&$p==mu_password;
}
function loggedIn(){
	if(!isset($_COOKIE[mu_cookie_user])||!isset($_COOKIE[mu_cookie_pass]))
		return false;
	return validateUser($_COOKIE[mu_cookie_user],$_COOKIE[mu_cookie_pass]);
}

// Get settings
function titleEditor($f){
	if(defined("mu_editor_title"))
		return str_replace("%%",$f,mu_editor_title);
	return mu_title;
}
function titleBrowse($f){
	if(defined("mu_browse_title")&&$f!=".")
		return str_replace("%%",$f,mu_browse_title);
	return mu_title;
}
function zipEnabled(){ //Check if php zip extension is installed
	return extension_loaded("zip");
}
function muonInterface($optn){ //Check interface status
	global $mu_session_status;
	return $mu_session_status==$optn;
}

// Links
function mainPage(){
	return mu_index;
}
function browsePage($path="/"){
	return mainPage()."?browse=".$path;
}
function editorPage($path="/"){
	return mainPage()."?edit=".$path;
}
function licensePage(){
	return mainPage()."?license";
}

// Redirect (main script)
function getPrevQuery($mode="_get",$pre="?",$fallback=""){
	if($mode=="_decode"&&isset($_GET["prev_query"]))
		return $pre.urldecode(str_replace("/","%",$_GET["prev_query"]));
	if(($mode=="_get"||$mode=="_both")&&isset($_GET["prev_query"]))
		return $pre."prev_query=".$_GET["prev_query"];
	if(($mode=="_srv"||$mode=="_both")&&!empty($_SERVER["QUERY_STRING"]))
		return $pre."prev_query=".str_replace("%","/",urlencode($_SERVER["QUERY_STRING"]));
	return $fallback;
}
function redirAndDie($query=""){
	if(!empty($query))
		$query="?".$query;
	header("location: ".mainPage().$query);
	die("");
}

// Functions for requests
function param($id){
	if(isset($_POST[$id])&&$_POST[$id]!="")
		return $_POST[$id];return false;
}
function respDie($resp){
	$feedback="plain";
	if(isset($_GET["feedback"]))
		$feedback=$_GET["feedback"];
	die($resp); //next updates may introduce redirect
}

// Functions for particular requests (copy, rename, etc)
function getSelected($type="any",$pre=""){
	if($type=="any")
		return array_merge(getSelected("folder",$pre),getSelected("file",$pre));
	$arr=[];
	$count=0;
	$max=param("count_".$type."s");
	for($i=0;$i<$max;$i++){
		$q=param($type."_".$i);
		if($q){
			$arr[$count]=$pre.substr($q,1);
			$count++;
		}
	}
	return $arr;
}
function validFilename($string){
	$fname=preg_replace("/[^a-zA-Z0-9_., )(-]/","",$string);
	return(!empty($fname)&&$fname!="."&&$fname!=".."&&$string==$fname);
}
function getPasteDest($destPath,$name){ 
	$dest=$destPath.$name;
	if(file_exists($dest)){
		$explName=explode(".",$name);
		$ext="";
		if(count($explName)==1)
			$name=$explName[0];
		else{
			$ext=".".$explName[count($explName)-1];
			$explName[count($explName)-1]="";
			$name=implode(".",$explName);
			$name=substr($name,0,-1);
		}
		$i=1;
		while(file_exists($destPath.$name."_".$i.$ext))
			$i++;
		$dest=$destPath.$name."_".$i.$ext;
	}
	return $dest;
}
function deletePath($path){ //Recursive delete
	if(is_dir($path)){
		$subitems=scandir($path);
		foreach($subitems as $sub)
			if($sub!="."&&$sub!="..")
				deletePath($path."/".$sub);
		return rmdir($path);
	}
	return unlink($path);
}
function copyPath($source,$dest,$mustBeFile=false){ //Recursive copy
	if(!file_exists($source)||$mustBeFile&&is_dir($source))
		return false;
	if(is_dir($source)){
		$r=true;
		$items=scandir($source);
		@mkdir($dest);
		foreach($items as $f)
			if($f!="."&&$f!="..")
				$r=$r&&copyPath($source."/".$f,$dest."/".$f);
		return $r;
	}
	return copy($source,$dest);
}

?>