<?php 

// BACK - END

// Default settings
define( "mu_version", "3.2" );
define( "mu_website", "http://infoein.altervista.org/muon" );
if(!defined("mu_title"))
    define( "mu_title", "Muon" );
if(!defined("mu_title_html"))
    define( "mu_title", "Muon" );
if(!defined("mu_cookie"))
    define( "mu_cookie", "cookie" );
if(!defined("mu_guest_session"))
    define( "mu_guest_session", false );
if(!defined("mu_guest_can_read"))
    define( "mu_guest_can_read", false );
define( "mu_index", "./".basename($_SERVER["SCRIPT_FILENAME"]));
define( "mu_cookie_user", "muon_".mu_cookie."_user" );
define( "mu_cookie_pass", "muon_".mu_cookie."_pass" );
$mu_session_status="";
$mu_session_data="";
$mu_session_title=mu_title;

// Browsing folder
$mu_wd_script = getcwd();
$mu_wd_browse = $mu_wd_script;
$mu_wd_relative_prefix = "";
if (defined('mu_root_dir') & mu_root_dir!="" & mu_root_dir!="." & mu_root_dir!="./"){
	chdir(mu_root_dir);
	$mu_wd_browse = getcwd();
	chdir($mu_wd_script);
	if ($mu_wd_script!=$mu_wd_browse)
		$mu_wd_relative_prefix = rtrim(mu_root_dir,"/")."/";
}

// Include functions
require "functions.php";

// Logout/Login (cookies)
if(isset($_POST["logout"])){
	setcookie(mu_cookie_user,false,time()-3600);
	setcookie(mu_cookie_pass,false,time()-3600);
	redirAndDie();
}
if(isset($_POST["login"])&&isset($_POST["pass"])){
	$u=$_POST["login"];
	$p=md5($_POST["pass"]);
	if(validateUser($u,$p)){
		setcookie(mu_cookie_user,$u,time()+86400*14);
		setcookie(mu_cookie_pass,$p,time()+86400*14);
		redirAndDie(getPrevQuery("_decode"));
	}
	redirAndDie("login=error".getPrevQuery("_get","&"));
}

// Handle requests
if(param("req")){
	require "requests.php";
}

// Include classes
require "classes.php";

// Identify status
if(isset($_GET["license"])){ //Display license
	$mu_session_status="license";
}
else if(!loggedIn()){ //Login screen
	if(!isset($_GET["login"]))
		redirAndDie("login".getPrevQuery("_srv","&"));
	$mu_session_status="login";
	$mu_session_data=false;
	if($_GET["login"]=="error")
		$mu_session_data=true;
}
else if(isset($_GET["browse"])){ //Browsing folder
	chdir($mu_wd_browse);
	$br_path=$_GET["browse"];
	if(substr($br_path,0,2)=="./"){
		redirAndDie("browse=/".substr($br_path,2));
	}
	if(substr($br_path,0,1)!="/"){
		redirAndDie("browse=/".$br_path);
	}
	if(strrpos($br_path,"//"))
		redirAndDie();
	$br_path="./".ltrim($br_path,"/");
	$br_path=rtrim($br_path,"/");
	$br_path=explode("/",$br_path);
	for($i=0;$i<count($br_path);$i++)
		if($br_path[$i]==".."||$i&&$br_path[$i]==".")
			redirAndDie();
	$br_name=$br_path[count($br_path)-1];
	$br_path[count($br_path)-1]="";
	$br_path=implode("/",$br_path);
	$mu_session_data=new MuonBrowse($br_name,$br_path);
	if(!$mu_session_data->name)redirAndDie();
	$mu_session_status="browse";
	$mu_session_title=titleBrowse($mu_session_data->name);
	chdir($mu_wd_script);
}
else if(isset($_GET["edit"])&&(!mu_guest_session||mu_guest_can_read)){ //File editor
	chdir($mu_wd_browse);
	$editfile=".".$_GET["edit"];
	if(!file_exists($editfile)||is_dir($editfile)||strrpos($editfile,"/../")||strrpos($editfile,"/./")||strrpos($editfile,"//"))
		redirAndDie();
	$base_path=explode("/",$editfile);
	$editfile=$base_path[count($base_path)-1];
	$base_path[count($base_path)-1]="";
	$base_path=implode("/",$base_path);
	$mu_session_status="edit";
	$mu_session_data=new MuonEdit($editfile,$base_path);
	$mu_session_title=titleEditor($editfile);
	chdir($mu_wd_script);
}
else //Fallback
	redirAndDie("browse=/");

?>