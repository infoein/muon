<?php set_time_limit(0);

/*********************************************
*  MUON FILE MANAGER                         *
*  Website: http://infoein.github.io/muon    *
*  Released under the MIT License            *
**********************************************/

/* Login */
define( "mu_username",        "admin" );//required
define( "mu_password",        "5f4dcc3b5aa765d61d8327deb882cf99" ); //required, md5-encrypted
define( "mu_cookie",          "my_site" ); //default: "cookie"

/* Visualisation */
define( "mu_title",           "Muon" ); //default: "Muon"
define( "mu_title_html",      "Muon" ); //default: "Muon"
define( "mu_browse_title",    "~ %% | ".mu_title );
define( "mu_editor_title",    "%% | ".mu_title );
define( "mu_home_link",       "https://www.example.com/" ); //default: "/"

/* Other settings */
define( "mu_guest_session",   false ); //default: false
define( "mu_guest_can_read",  false ); //default: false
define( "mu_root_dir" , "./" ); //relative path, by default "./"


/*************************************************/
/*End of Muon settings*/ define("mu_version","3.3");define("mu_website","https://infoein.github.io/muon/");if(!defined("mu_title"))define("mu_title","Muon");if(!defined("mu_title_html"))define("mu_title","Muon");if(!defined("mu_cookie"))define("mu_cookie","cookie");if(!defined("mu_guest_session"))define("mu_guest_session",false);if(!defined("mu_guest_can_read"))define("mu_guest_can_read",false);define("mu_index","./".basename($_SERVER["SCRIPT_FILENAME"]));define("mu_cookie_user","muon_".mu_cookie."_user");define("mu_cookie_pass","muon_".mu_cookie."_pass");$mu_session_status="";$mu_session_data="";$mu_session_title=mu_title;$mu_wd_script=getcwd();$mu_wd_browse=$mu_wd_script;$mu_wd_relative_prefix="";if(defined('mu_root_dir')& mu_root_dir!="" & mu_root_dir!="." & mu_root_dir!="./"){chdir(mu_root_dir);$mu_wd_browse=getcwd();chdir($mu_wd_script);if($mu_wd_script!=$mu_wd_browse)$mu_wd_relative_prefix=rtrim(mu_root_dir,"/")."/";}function validateUser($u,$p){if(!defined("mu_username")||!defined("mu_password"))return false;return $u==mu_username&&$p==mu_password;}function loggedIn(){if(!isset($_COOKIE[mu_cookie_user])||!isset($_COOKIE[mu_cookie_pass]))return false;return validateUser($_COOKIE[mu_cookie_user],$_COOKIE[mu_cookie_pass]);}function titleEditor($f){if(defined("mu_editor_title"))return str_replace("%%",$f,mu_editor_title);return mu_title;}function titleBrowse($f){if(defined("mu_browse_title")&&$f!=".")return str_replace("%%",$f,mu_browse_title);return mu_title;}function zipEnabled(){return extension_loaded("zip");}function muonInterface($optn){global $mu_session_status;return $mu_session_status==$optn;}function mainPage(){return mu_index;}function browsePage($path="/"){return mainPage()."?browse=".$path;}function editorPage($path="/"){return mainPage()."?edit=".$path;}function licensePage(){return mainPage()."?license";}function getPrevQuery($mode="_get",$pre="?",$fallback=""){if($mode=="_decode"&&isset($_GET["prev_query"]))return $pre.urldecode(str_replace("/","%",$_GET["prev_query"]));if(($mode=="_get"||$mode=="_both")&&isset($_GET["prev_query"]))return $pre."prev_query=".$_GET["prev_query"];if(($mode=="_srv"||$mode=="_both")&&!empty($_SERVER["QUERY_STRING"]))return $pre."prev_query=".str_replace("%","/",urlencode($_SERVER["QUERY_STRING"]));return $fallback;}function redirAndDie($query=""){if(!empty($query))$query="?".$query;header("location: ".mainPage().$query);die("");}function param($id){if(isset($_POST[$id])&&$_POST[$id]!="")return $_POST[$id];return false;}function respDie($resp){$feedback="plain";if(isset($_GET["feedback"]))$feedback=$_GET["feedback"];die($resp);}function getSelected($type="any",$pre=""){if($type=="any")return array_merge(getSelected("folder",$pre),getSelected("file",$pre));$arr=[];$count=0;$max=param("count_".$type."s");for($i=0;$i<$max;$i++){$q=param($type."_".$i);if($q){$arr[$count]=$pre.substr($q,1);$count++;}}return $arr;}function validFilename($string){$fname=preg_replace("/[^a-zA-Z0-9_., )(-]/","",$string);return(!empty($fname)&&$fname!="."&&$fname!=".."&&$string==$fname);}function getPasteDest($destPath,$name){$dest=$destPath.$name;if(file_exists($dest)){$explName=explode(".",$name);$ext="";if(count($explName)==1)$name=$explName[0];else{$ext=".".$explName[count($explName)-1];$explName[count($explName)-1]="";$name=implode(".",$explName);$name=substr($name,0,-1);}$i=1;while(file_exists($destPath.$name."_".$i.$ext))$i++;$dest=$destPath.$name."_".$i.$ext;}return $dest;}function deletePath($path){if(is_dir($path)){$subitems=scandir($path);foreach($subitems as $sub)if($sub!="."&&$sub!="..")deletePath($path."/".$sub);return rmdir($path);}return unlink($path);}function copyPath($source,$dest,$mustBeFile=false){if(!file_exists($source)||$mustBeFile&&is_dir($source))return false;if(is_dir($source)){$r=true;$items=scandir($source);@mkdir($dest);foreach($items as $f)if($f!="."&&$f!="..")$r=$r&&copyPath($source."/".$f,$dest."/".$f);return $r;}return copy($source,$dest);}if(isset($_POST["logout"])){setcookie(mu_cookie_user,false,time()-3600);setcookie(mu_cookie_pass,false,time()-3600);redirAndDie();}if(isset($_POST["login"])&&isset($_POST["pass"])){$u=$_POST["login"];$p=md5($_POST["pass"]);if(validateUser($u,$p)){setcookie(mu_cookie_user,$u,time()+86400*14);setcookie(mu_cookie_pass,$p,time()+86400*14);redirAndDie(getPrevQuery("_decode"));}redirAndDie("login=error".getPrevQuery("_get","&"));}if(param("req")){$request=param("req");if(!loggedIn())respDie("err_login");if(mu_guest_session)respDie("err_guest");chdir($mu_wd_browse);if($request=="rename"){$base_path=param("browsing_path");$file=getSelected();if(count($file)!=1)respDie("rename_err_count");$file=$file[0];$old_path=$base_path."/".$file;if(!file_exists($old_path))respDie("rename_err_missing");$new_name=trim(param("ren_new_name"));if(!$new_name||!validFilename($new_name))respDie("rename_err_invalid_name");$new_path=$base_path."/".$new_name;if(file_exists($new_path)&&$old_path!=$new_path)respDie("rename_err_already_existing");if(!rename($old_path,$new_path)&&$old_path!=$new_path)respDie("rename_err");respDie("rename_ok");}if($request=="unzip"){if(!zipEnabled())respDie("unzip_err_extension");$base_path=param("browsing_path");$file=getSelected();if(count($file)!=1)respDie("unzip_err_count");$file=$file[0];$full_path=$base_path."/".$file;if(!file_exists($full_path)||is_dir($full_path))respDie("unzip_err_missing");$dest_path=$base_path."/unzip_".$file;if(file_exists($dest_path)){$i=1;while(file_exists($dest_path."_".$i))$i++;$dest_path=$dest_path."_".$i;}$dest_path=$dest_path."/";$zip_file=new ZipArchive;if(!($zip_file->open($full_path)===TRUE))respDie("unzip_err_opening");if(!($zip_file->extractTo($dest_path)))respDie("unzip_err_extracting");$zip_file->close();respDie("unzip_ok");}if($request=="delete"){$files=getSelected("any",param("browsing_path")."/");$errors=false;foreach($files as $item)if(!deletePath($item))$errors=true;if($errors)respDie("delete_err");respDie("delete_ok");}if(substr($request,0,7)==="paste__"){$pasteto=substr($request,7)."/";if(!file_exists($pasteto)||!is_dir($pasteto))respDie("paste_err_destination");$path=param("browsing_path")."/";if(!file_exists($path)||!is_dir($path))respDie("paste_err_location");$cut=false;if(param("paste_meaning")=="cut")$cut=true;else if(param("paste_meaning")!="copy")respDie("paste_err_meaningless");if($path==$pasteto&&$cut)respDie("paste_ok_cut");$errors=false;$folders=getSelected("folder");for($i=0;$i<count($folders);$i++)if((substr($pasteto,0,strlen($path.$folders[$i]))===$path.$folders[$i])&&file_exists($path.$folders[$i])&&is_dir($path.$folders[$i])){if($cut)respDie("paste_err_cut_dest_inside_source");else if(!copyPath($path.$folders[$i],getPasteDest($pasteto,$folders[$i])))$errors=true;}else if(!$cut){if(!copyPath($path.$folders[$i],getPasteDest($pasteto,$folders[$i])))$errors=true;}else if(!rename($path.$folders[$i],getPasteDest($pasteto,$folders[$i])))$errors=true;$files=getSelected("file");if(!$cut){foreach($files as $file)if(!copyPath($path.$file,getPasteDest($pasteto,$file),true))$errors=true;if($errors)respDie("paste_err_copy");respDie("paste_ok_copy");}else{foreach($files as $file)if(!rename($path.$file,getPasteDest($pasteto,$file)))$errors=true;if($errors)respDie("paste_err_cut");respDie("paste_ok_cut");}respDie(null);}if($request=="perms"){$files=getSelected("any",param("browsing_path")."/");$chmod=param("perm_new_perms");if(!$chmod)respDie("perms_err_input");$chmod=octdec(intval($chmod));$errors=false;foreach($files as $item)if(!chmod($item,$chmod))$errors=true;if($errors)respDie("perms_err");respDie("perms_ok");}if($request=="zip"){if(!zipEnabled())respDie("zip_err_extension");$base_path=param("browsing_path");if(!file_exists($base_path)||!is_dir($base_path))respDie("zip_err_location");$dest_path=$base_path."/zip_".date("Ymd_Hi");if(file_exists($dest_path.".zip")){$i=1;while(file_exists($dest_path."_".$i.".zip"))$i++;$dest_path=$dest_path."_".$i;}$dest_path=$dest_path.".zip";$files=getSelected($type="file");$folders=getSelected($type="folder");$zip=new ZipArchive();if($zip->open($dest_path,$overwrite?ZIPARCHIVE::OVERWRITE:ZIPARCHIVE::CREATE)!==true)respDie("zip_err_opening");if($files)foreach($files as $file)$zip->addFile($base_path."/".$file,$file);if($folders)foreach($folders as $folder){$root_path=realpath($base_path."/".$folder);$subs=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root_path),RecursiveIteratorIterator::LEAVES_ONLY);foreach($subs as $name=>$sub)if(!$sub->isDir()){$subPath=$sub->getRealPath();$relPath=$folder."/".substr($subPath,strlen($root_path)+ 1);$zip->addFile($subPath,$relPath);}}$zip->close();if(!file_exists($dest_path))respDie("zip_err");respDie("zip_ok");}if($request=="newfolder"){$base_path=param("browsing_path");if(!file_exists($base_path)||!is_dir($base_path))respDie("newfolder_err_location");$new_name=trim(param("new_folder_name"));if(!validFilename($new_name))respDie("newfolder_err_invalid_name");$full_path=$base_path."/".$new_name;if(file_exists($full_path))respDie("newfolder_err_already_existing");if(!mkdir($full_path,0755))respDie("newfolder_err");respDie("newfolder_ok");}if($request=="newfile"){$base_path=param("browsing_path");if(!file_exists($base_path)||!is_dir($base_path))respDie("newfile_err_location");$new_name=trim(param("new_file_name"));if(!validFilename($new_name))respDie("newfile_err_invalid_name");$full_path=$base_path."/".$new_name;if(file_exists($full_path))respDie("newfile_err_already_existing");$file=fopen($full_path,"w")or respDie("newfile_err");fwrite($file,date("F j, Y, g:i a"));fclose($file);chmod($path,0755);respDie("newfile_ok");}if($request=="upload"){$path=param("upload_path")."/";if(!file_exists($path)||!is_dir($path))respDie("upload_err_location");$uploads=0;$errors=0;foreach($_FILES as $file)if($file["error"]==UPLOAD_ERR_OK and is_uploaded_file($file["tmp_name"])){$fpath=getPasteDest($path,$file["name"]);move_uploaded_file($file["tmp_name"],$fpath);chmod($fpath,0775);$uploads++;}else $errors++;if(!$uploads||$errors>1)respDie("upload_err");respDie("upload_ok");}if($request=="editsave"){$path=param("editing_path");if(file_exists($path)&&is_dir($path))die("editsave_err_location");if(!file_put_contents($path,param("editor_content"))){if(!file_exists($path))die("editsave_err_location");die("editsave_err");}die("editsave_ok");}respDie("err");}class MuonFile{public $name;public $perms;public $size;public $path;public $browseId;function __construct($name,$path,$browseId=false,$getSize=true){$this->name=$name;$this->size=0;$permissions=fileperms($path.$name);$permissions=sprintf("%o",$permissions);$this->perms=substr($permissions,-4);$this->path=$path;if($getSize)$this->size=$this->getFileSize();$this->browseId=$browseId;}function getFileSize($sub=""){$path=$this->getFullPath(false).$sub;if(!is_dir($path))return filesize($path);$size=0;foreach(scandir($path)as $item)if($item!="."&&$item!="..")$size+=$this->getFileSize($sub."/".$item);return $size;}function sizeWithOM(){$fileSize=$this->size;$byteOMs=array("","k","M","G","T");for($i=0;$fileSize>=1024&&$i<count($byteOMs)-1;$i++)$fileSize/=1024;$fileSize=round($fileSize,2)." ".$byteOMs[$i];return $fileSize;}function getPath($trimDot=true){$fp=$this->path;if($trimDot)$fp=ltrim($fp,".");return $fp;}function getFullPath($trimDot=true){$fp=$this->path.$this->name;if($trimDot)$fp=ltrim($fp,".");return $fp;}function explodePath(){$split=trim($this->getFullPath(),"/");if($split)return explode("/",$split);else return false;}}class MuonEdit extends MuonFile{public $content;function __construct($name,$path){parent::__construct($name,$path);$this->content=file_get_contents($this->getFullPath(false));}function getFolderPath(){return rtrim($this->getPath(),"/");}function printContent(){echo htmlspecialchars($this->content);}}class MuonBrowse extends MuonFile{public $folders;public $countFolders;public $files;public $countFiles;function __construct($name=".",$path=""){parent::__construct($name,$path,false,false);$fullPath=$this->getFullPath(false);if(!file_exists($fullPath)||!is_dir($fullPath)){$this->name=false;return 0;}$fullPath=$fullPath."/";$this->folders=array();$this->countFolders=0;$this->files=array();$this->countFiles=0;$subItems=scandir($fullPath);foreach($subItems as $item)if($item!="."&&$item!=".."){if(is_dir($fullPath.$item)){$this->folders[$this->countFolders]=new MuonFile($item,$fullPath,$this->countFolders);$this->size+=$this->folders[$this->countFolders]->size;$this->countFolders++;}else{$this->files[$this->countFiles]=new MuonFile($item,$fullPath,$this->countFiles);$this->size+=$this->files[$this->countFiles]->size;$this->countFiles++;}}}function getFolderPath(){return $this->getFullPath();}}class MuonTree{public $name;public $fullPath;public $browsingThis;public $browsingInsideThis;public $countFolders;public $folders;public $treeId;function __construct($browsing=false,$name=".",$path="",$treeId=1){$this->name=$name;$this->browsingThis=false;$this->browsingInsideThis=false;if(!empty($path))$path.="/";$this->fullPath=ltrim($path.$this->name,".");if($browsing==$this->fullPath)$this->browsingThis=true;if(substr($browsing,0,strlen($this->fullPath))===$this->fullPath)$this->browsingInsideThis=true;$this->folders=[];$this->countFolders=0;$folders=scandir(".".$this->fullPath);foreach($folders as $item)if($item!="."&&$item!=".."&&is_dir(".".$this->fullPath."/".$item)){$this->folders[$this->countFolders]=new MuonTree($browsing,$item,".".$this->fullPath,$treeId);$treeId=$this->folders[$this->countFolders]->treeId + 1;$this->countFolders++;}$this->treeId=$treeId;}}if(isset($_GET["license"])){$mu_session_status="license";}else if(!loggedIn()){if(!isset($_GET["login"]))redirAndDie("login".getPrevQuery("_srv","&"));$mu_session_status="login";$mu_session_data=false;if($_GET["login"]=="error")$mu_session_data=true;}else if(isset($_GET["browse"])){chdir($mu_wd_browse);$br_path=$_GET["browse"];if(substr($br_path,0,2)=="./"){redirAndDie("browse=/".substr($br_path,2));}if(substr($br_path,0,1)!="/"){redirAndDie("browse=/".$br_path);}if(strrpos($br_path,"//"))redirAndDie();$br_path="./".ltrim($br_path,"/");$br_path=rtrim($br_path,"/");$br_path=explode("/",$br_path);for($i=0;$i<count($br_path);$i++)if($br_path[$i]==".."||$i&&$br_path[$i]==".")redirAndDie();$br_name=$br_path[count($br_path)-1];$br_path[count($br_path)-1]="";$br_path=implode("/",$br_path);$mu_session_data=new MuonBrowse($br_name,$br_path);if(!$mu_session_data->name)redirAndDie();$mu_session_status="browse";$mu_session_title=titleBrowse($mu_session_data->name);chdir($mu_wd_script);}else if(isset($_GET["edit"])&&(!mu_guest_session||mu_guest_can_read)){chdir($mu_wd_browse);$editfile=".".$_GET["edit"];if(!file_exists($editfile)||is_dir($editfile)||strrpos($editfile,"/../")||strrpos($editfile,"/./")||strrpos($editfile,"//"))redirAndDie();$base_path=explode("/",$editfile);$editfile=$base_path[count($base_path)-1];$base_path[count($base_path)-1]="";$base_path=implode("/",$base_path);$mu_session_status="edit";$mu_session_data=new MuonEdit($editfile,$base_path);$mu_session_title=titleEditor($editfile);chdir($mu_wd_script);}else redirAndDie("browse=/");?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta name="robots" content="noindex" />
<title><?php echo $mu_session_title;?></title>
<link rel="apple-touch-icon" href="data:image/png;base64,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
" />
<link rel="icon" href="data:image/png;base64,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
" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0" />
<style type="text/css">body,html{height:100%;width:100%}body,*{padding:0;border:0;margin:0}*,*:before,*:after{font-family:sans-serif;font-size:1em;position:relative;color:black;-o-box-sizing:border-box;-ms-box-sizing:border-box;-moz-box-sizing:border-box;-khtml-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;line-height:normal;vertical-align:middle;text-decoration:none}.ns,.ns *{-webkit-touch-callout:none;-webkit-user-select:none;-khtml-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}body{background:#fff;font-size:15px}input{-webkit-appearance:none;border:0;background-image:none;background-color:transparent;-webkit-box-shadow:none;-moz-box-shadow:none;box-shadow:none;outline:0;margin:0;padding:0;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0}table.tab-row,table.tab-row tbody,table.tab-row tr,table.tab-row td{position:relative;z-index:2;height:100%;border-collapse:collapse;overflow:hidden}table.tab-row,table.tab-row tbody,table.tab-row tr{width:100%}.svg-ic{display:block;height:100%;width:100%;fill:#308cfc}.upload-zone{width:1em;height:1em;position:fixed;top:-2em;left:-2em;z-index:0;overflow:hidden;visibility:hidden}.layout-block{position:fixed;overflow:auto;box-shadow:0 0 1em rgba(0,0,0,0.5);background:#fff;-webkit-overflow-scrolling:touch}.browsebar{z-index:11;top:0;left:16em;right:0;height:4em}.tools{z-index:2;top:4em;left:0;width:16em;bottom:1.4em}.tree{z-index:5;top:4em;right:0;width:20em;bottom:1.4em}.main{z-index:1;top:4em;left:16em;right:20em;bottom:1.4em}.bottombar,.outputbar{z-index:10;bottom:0;left:0;width:100%;height:1.4em;overflow:hidden;padding:0 1em 0 1em}.bottombar .text,.outputbar .text{font-size:.8em;display:block;line-height:1.7em;margin:0}.resp-none .outputbar{display:none}.resp-progress .bottombar,.resp-success .bottombar,.resp-error .bottombar{display:none}.resp-success .outputbar{background:#5f5}.resp-error .outputbar{background:#f55}.tree-resetter{top:0;left:0;bottom:0;right:20em;background:transparent;display:none;position:fixed;z-index:101}.tree-copy .tree-resetter,.tree-cut .tree-resetter{display:block}.login{width:20em;height:20em;top:50%;left:50%;margin-top:-10em;margin-left:-10em;padding-top:4em}.login.license{height:40em;margin-top:-20em}.login input{padding:0 1.5em;line-height:2.6em;width:100%;background:#eee;text-align:left}input:-webkit-autofill,input:-webkit-autofill:hover,input:-webkit-autofill:focus,input:-webkit-autofill:active{-webkit-box-shadow:0 0 0 10em #eee inset!important}.login input[type=submit]{cursor:pointer;font-weight:bold;background:#308cfc;color:#fff;text-align:center}.login .header-block{height:6.7em;padding:2.5em 1.5em;padding-bottom:0}.login .header-block.err,.login .header-block.err *{color:#f55}.login .login-block{height:7.8em;margin-bottom:1.5em;padding:0 1.5em}.login .license-block{font-size:.8em;padding:2em 2em;margin-top:-5em}.login .ahr{cursor:pointer;border-bottom:1pt solid #308cfc}.login .ahr:active{color:#308cfc}.muonbar.layout-block{z-index:15;top:0;left:0;width:16em;height:4em}.muonbar.muonbar-login{width:20em;box-shadow:none;top:50%;left:50%;margin-left:-10em;margin-top:-10em}.muonbar.muonbar-license{display:none}.muonbar .logo{width:3em;height:4em;padding:1em;padding-right:0}.muonbar .logo .svg-ic{fill:#000}.muonbar .title{padding-top:.6em;vertical-align:top;font-size:1.5em;font-weight:bold;padding-left:.3em}.muonbar .title .learnmore{font-size:.4em;font-weight:normal;color:#308cfc;position:absolute;top:4em;left:1em;cursor:pointer}.muonbar .title .learnmore:active{border-bottom:1pt solid #308cfc}.muonbar .btns{padding-right:.5em}.muonbar .btn{float:right;height:4em;width:2.2em;padding:1em .1em;cursor:pointer}.muonbar .btn.gray .svg-ic{fill:#888}.muonbar .btn.tree-open-btn{top:0;right:0;margin-left:1.5em;display:none}.editor-container{margin:1.5em 0}.editor{display:block;position:relative;font-size:.8em;vertical-align:middle;background:-moz-linear-gradient(top,transparent 0,transparent 50%,#eeeeee 50%,#eeeeee 100%);background:-webkit-linear-gradient(top,transparent 0,transparent 50%,#eeeeee 50%,#eeeeee 100%);background:linear-gradient(to bottom,transparent 0,transparent 50%,#eeeeee 50%,#eeeeee 100%);background-position:top left;background-attachment:local;width:100%;outline:0;font-family:monospace;resize:none;-moz-tab-size:4;-o-tab-size:4;tab-size:4;padding-left:3.5em;padding-right:.5em}.editor-text-nowrap .editor{overflow:hidden;white-space:pre;overflow-x:scroll}.editor-lineheight{background-size:400px 40px;background-repeat:repeat;line-height:20px}.editor-numbers{display:block;position:relative;width:4em;font-size:.6em;padding-right:.5em;text-align:right;background:#fff;border-right:1pt solid #ddd;color:#888}.editor-adj{display:block;background:red}.editor-adj.hidden{display:none}.browse-container{margin:0 0 1.5em 0}.browse-item{display:block;height:auto;overflow:hidden}.browse-item label{cursor:pointer}.browse-item .tab{height:2.6em;border-bottom:1pt solid #ddd}.browse-item .browse-chk{position:relative;z-index:0;display:block;width:100%;height:20em;margin-bottom:-20em;background:transparent}.browse-item .browse-chk:after{position:absolute;z-index:1;display:block;content:" ";margin-top:.6em;margin-left:.6em;width:1.4em;height:1.4em;border:.3em solid #ddd;background:transparent;-o-border-radius:50%;-khtml-border-radius:50%;-ms-border-radius:50%;-moz-border-radius:50%;-webkit-border-radius:50%;border-radius:50%}.browse-item .browse-chk:checked{display:block;background:#deecfc}.browse-item .browse-chk:checked:after{background:#308cfc;border-color:#a1cafc}.browse-item .tab .name{line-height:1em}.browse-item .tab .name .breakwords{word-break:break-all}.browse-item .tab .name .link{font-weight:bold;cursor:pointer;border-bottom:1pt solid #308cfc}.browse-item .tab .name .link:active{color:#308cfc}.browse-item .tab .name .openlink{font-weight:normal;cursor:pointer;color:#308cfc;font-size:.7em;margin-left:1em;border-bottom:1pt solid transparent}.browse-item .tab .name .openlink.edit{margin-left:.1em;font-weight:bold}.browse-item .tab .name .openlink:active{border-color:#308cfc}.browse-item .tab .data{text-align:right}.browse-item .tab .data .data-cnt{line-height:1.2em}.browse-item .tab .data .data-cnt .size{font-size:.8em;line-height:1.2em}.browse-item .tab .data .data-cnt .size:after{content:"\a";white-space:pre}.browse-item .tab .data .data-cnt .perms{font-size:.6em;line-height:1.2em;color:#888}.browse-item .tab .sep-first{width:2.6em}.browse-item .tab .sep-mid{width:1em}.browse-item .tab .sep-last{width:.6em}.browse-item .tab .upbtn{display:block;position:relative;width:1.6em;height:1.6em;margin:0 0 .1em .5em;padding:.3em}.browse-item .tab .upbtn .svg-ic{display:block;width:100%;height:100%}.browse-item.legend{border-bottom:2pt solid #ddd}.browse-item.legend *{vertical-align:bottom;font-weight:bold;line-height:1.8em}.browse-item.legend .caption{font-size:.8em}.browsebar-data{width:100%;height:1.4em;display:block;line-height:1.2em;padding:.1em 1em .1em 1em;overflow-y:hidden;overflow-x:auto;white-space:nowrap;text-align:center}.browsebar-data .text{font-size:.8em}.browsebar-data .text,.browsebar-data .text *{line-height:1.5em}.browsebar-data .text .openlink{font-weight:normal;cursor:pointer;color:#308cfc;margin-left:.4em;border-bottom:1pt solid transparent}.browsebar-data .text .openlink:active{border-color:#308cfc}.browsebar-data .text .perms{color:#888;font-size:.9em}.browsebar-blocks{width:100%;height:2.6em;display:block;line-height:2.6em;background:#eee;overflow-y:hidden;overflow-x:auto;white-space:nowrap}.browsebar-block{display:inline-block;padding:0 .6em 0 .6em;line-height:2.6em;height:2.6em}.browsebar-block .nav{display:inline;line-height:2.6em;font-weight:bold;cursor:pointer;border-bottom:1pt solid #308cfc}.browsebar-block .nav:active{color:#308cfc}.browsebar-block.sep{padding:0}.tree-container{margin:1.6em 2em 1.6em 2em}.tree-close-btn{position:absolute;top:0;right:0;height:4em;width:4em;padding:1em;cursor:pointer;display:none;z-index:18}.tree-close-btn .svg-ic{fill:#000}.tree-copy .tree-close-btn,.tree-cut .tree-close-btn{display:block}.tree-title{font-size:1.5em;font-weight:bold;line-height:1.8em;display:none}.tree-toggle{margin:0 0 1.5em 0;font-size:.8em;line-height:1.8em}.tree-toggle .btn{cursor:pointer;color:#308cfc;font-weight:bold}.tree-ul .tree-ul{display:none}.tree-ul.tree-open{display:block}.tree-ul .tree-ul{margin-left:.5em;padding-left:1.5em;border-left:1pt solid #ddd;list-style-type:disc}.tree-opener-tablet{top:0;right:0;width:2.4em;height:4em;padding:1em .2em;z-index:14;cursor:pointer;display:none}.tree-li{display:block;list-style-type:none}.tree-name{cursor:pointer;border-bottom:1pt solid #308cfc;display:none}.tree-name:active{color:#308cfc}.tree-li.tree-browsing .tree-name{font-weight:bold}.tree-btn{width:1em;height:1.8em;display:inline-block;cursor:pointer}.tree-btn-noclick{cursor:default}.tree-btn.tree-btn-close,.tree-li.tree-open .tree-btn.tree-btn-open{display:none}.tree-btn.tree-btn-open,.tree-li.tree-open .tree-btn.tree-btn-close{display:inline-block}.tree-btn .svg-ic{width:1em;height:1em;margin:.4em 0 .4em 0}.tree-browse .tree-btn.paste{display:none}.tree-browse .tree-name.brw,.tree-copy .tree-name.cpy,.tree-cut .tree-name.cut,.tree-browse .tree-title.brw,.tree-copy .tree-title.cpy,.tree-cut .tree-title.cut{display:inline}.tools-desktop .cat{margin:1em 0 2em 0;border-bottom:1pt solid #eee}.tools-desktop .nobord{border:0}.tools-desktop .cat,.tools-desktop .row{display:block;width:100%;float:none;overflow:hidden}.tools-desktop .cat .cat-name{display:block;padding:0 1em;font-weight:bold;line-height:1.8em}.tools-desktop .row,.tools-desktop .item{height:2.3em}.tools-desktop .item{cursor:pointer;display:block;width:100%}.tools-desktop .tool-upload .close{display:none}.tools-desktop .row .item{float:left}.tools-desktop .row.row2 .item{width:50%}.tools-desktop .row.row3 .item{width:32%}.tools-desktop .row.row3 .item .btntex{margin-top:.1em;margin-bottom:-0.1em;margin-left:-0.4em}.tools .item .tab .intext input{width:100%;height:100%;background:#eee;font-size:.8em;padding:0 1em 0 1em}.tools .item .tab .submit{width:2.3em;background:#308cfc}.tools .item .tab .hidein{width:2.3em;background:#fff;display:none}.tools .item .btnic{display:block;cursor:pointer;width:2.3em;height:2.3em;padding:.4em;float:left}.tools .item .btnic .svg-ic{width:100%;height:100%}.tools .item .submit .btnic .svg-ic{fill:#fff}.tools .item .submit:active .btnic .svg-ic{fill:#deecfc}.tools .item .hidein .btnic .svg-ic{fill:#000}.tools .item .hidein:active .btnic .svg-ic{fill:#308cfc}.tools .item.close .btnic .svg-ic{fill:#000}.tools .item.close:active .btnic .svg-ic{fill:#308cfc}.tools .item .btntex{float:left;display:block;cursor:pointer;width:auto;height:2.3em;line-height:2.3em}.tools .item:active .btntex,.tools .item:active .btntex *{color:#308cfc}.tools-desktop .item .btntex.bold{font-weight:bold}.tools-mobile{display:none;white-space:nowrap;width:100%;height:4em;overflow-y:hidden;overflow-x:auto;text-align:center;padding:0 .8em;white-space:nowrap}.tools-mobile .item{height:4em;width:2.4em;display:inline-block}.tools-mobile .item.rbord{border-right:1pt solid #eee}.tools-mobile .item .btnic{display:block;cursor:pointer;width:2.3em;height:4em;padding:.4em;float:left;padding-bottom:1em}.tools-mobile .item .btntex{position:absolute;font-size:.5em;text-align:center;bottom:1em;width:100%}@media screen and (max-height:600px){.login.license{top:0;margin-top:0;height:100%;overflow-y:auto}}@media screen and (max-height:300px){.muonbar.muonbar-login,.login{top:0;margin-top:0}.login{height:100%;overflow-y:auto}}@media screen and (max-width:1024px){.browsebar{right:2.4em}.tree{z-index:19;top:0;right:-25em;width:20em;bottom:0;transition:right .5s;max-width:100%;white-space:nowrap;position:fixed}.tree-visible .tree{right:0}.main{right:0}.tree-close-btn,.tree-opener-tablet{display:block}}@media screen and (max-width:700px){.tools{top:auto;bottom:0;left:0;width:100%;height:4em}.browsebar{top:3em;left:0;right:0}.bottombar{display:none}.outputbar{bottom:auto;height:1.6em;padding-top:.2em;top:7em;z-index:7;text-align:center;vertical-align:middle}.main{top:7em;left:0;right:0;bottom:4em}.muonbar.layout-block{box-shadow:none;width:100%;height:3em}.muonbar.muonbar-login{width:20em;height:4em;padding:.5em;padding-left:0}.muonbar .logo{height:3em;padding-top:.5em;padding-bottom:.5em}.muonbar .title{padding-top:.3em}.muonbar .title .learnmore{top:3.2em}.muonbar .btns{padding-right:0}.muonbar .btn{height:3em;width:2.4em;padding:.5em .2em}.muonbar .btn.mobile-only{display:block}.muonbar .btn.tablet-only{display:none}.muonbar .btn.tree-open-btn{display:block}.tree-opener-tablet{display:none}.tools{height:auto;max-height:100%;overflow-y:auto;bottom:0;z-index:17;position:fixed}.tools-mobile{display:block}.tools-desktop .cat{padding:0;margin:0;border:0}.tools-desktop .cat .cat-name,.tools-desktop .row,.tools-desktop .item{display:none}.tools-desktop .item.tool-mobile-visible,.tools-desktop .tool-mobile-visible .item{display:block}.tools-desktop .tool-upload .item.close{position:relative;width:2.3em;top:0;left:0}.tools-desktop .tool-upload .item.submit{position:absolute;top:0;left:2.3em;display:block}.uploading-nothing .tools-desktop .tool-upload.tool-mobile-visible .item.submit{display:none},.tools-desktop .tool-upload.tool-mobile-visible .item.close{display:block}.tools .item .tab .hidein{display:table-cell}}@media screen and (max-width:300px){.muonbar .tab-row{width:300px}.muonbar{overflow-x:auto;overflow-y:hidden}.login{width:100%;left:0;margin-left:0}}.browse-select-nothing .disabled-select-nothing,.browse-select-one-folder .disabled-select-one-folder,.browse-select-one-file .disabled-select-one-file,.browse-select-big-selection .disabled-select-big-selection,.editor-changed .disabled-editor-changed,.editor-not-changed .disabled-editor-not-changed,.editor-text-wrap .disabled-editor-text-wrap,.editor-text-nowrap .disabled-editor-text-nowrap,.uploading-nothing .disabled-uploading-nothing{display:none}</style>
<script type="text/javascript">var mu_session_status="<?php echo $mu_session_status;?>";var mu_session_title="<?php echo $mu_session_title;?>";var mu_requests_to="<?php echo mainPage();?>?feedback=plain";</script>
<?php if(!muonInterface("login")&&!muonInterface("license")){?>
<script type="text/javascript">function gid(b){return document.getElementById(b)}function gval(b){return gid(b).value}function add_cl(e,f,d){if(typeof f.length!=="undefined"){for(i=0;i<f.length;i++){add_cl(e,f[i])}}else{if(f.className.indexOf(e)<0){f.className=f.className+" "+e}}}function rem_cl(e,f){if(typeof f.length!=="undefined"){for(var d=0;d<f.length;d++){rem_cl(e,f[d])}}else{f.className=f.className.replace(e,"").replace(/  /g," ")}}function arr_cl(f,g,h){add_cl(f[g],h);for(var e=0;e<g;e++){rem_cl(f[e],h)}for(var e=g+1;e<f.length;e++){rem_cl(f[e],h)}}function editor_adjust(o,q){o=o||false;q=q||false;var t=false;if(q){var t=q.which||q.keyCode;var t=q.ctrlKey?q.ctrlKey:((t===17)?true:false);if(t){t=true}else{t=false}}var u=gid("text-editor");var p=gid("text-editor-sizeadj");var q=gid("text-editor-numbers");var n=20;var l=1;var m=n;p.style.height=(u.offsetHeight-n)+"px";rem_cl("hidden",p);u.style.height=m+"px";m+=u.scrollHeight;u.style.height=m+"px";q.style.height=(m+5*n)+"px";l=m/n;var v="<br/><br/><br/><br/><br/>1";for(var s=2;s<=l;s++){v=v+"<br/>"+s}q.innerHTML=v;q.style.marginTop="-"+(m+5*n)+"px";add_cl("hidden",p);if(t){return}if(!o){document.title="* "+mu_session_title;var r=new Array("editor-changed","editor-not-changed");arr_cl(r,0,gid("layout"))}}function editor_discard(b){if(confirm("Discard changes?")){location.href=b}}function editor_text_wrap(d){var e=new Array("editor-text-nowrap","editor-text-wrap");var f=0;if(d){f=1}arr_cl(e,f,gid("layout"));editor_adjust(true)}function selected_qty(f,h){f=f||"total";h=h||false;if(f=="one"){var g=document.getElementsByClassName("browse-chk");var j=0;for(var k=0;k<g.length;k++){if(g[k].checked==true){return g[k].value}}return false}if(f=="total"){return selected_qty("folder",h)+selected_qty("file",h)}if(f=="arr"){return new Array(selected_qty("folder",h),selected_qty("file",h))}var g=document.getElementsByClassName("browse-chk-"+f);var j=0;if(h){for(var k=0;k<g.length&&j<2;k++){if(g[k].checked==true){j++}}return j}for(var k=0;k<g.length;k++){if(g[k].checked==true){j++}}return j}function select_browse_update(){var n=selected_qty("folder");var k=selected_qty("file");var o=n+k;var m=""+o+" files";var l=gid("layout");var q=new Array("browse-select-nothing","browse-select-one-folder","browse-select-one-file","browse-select-big-selection");switch(o){case 0:tree_visibility("close");arr_cl(q,0,l);break;case 1:if(n){m="folder";arr_cl(q,1,l)}else{m="file";arr_cl(q,2,l)}gid("input-rename").value=selected_qty("one").substring(1);break;default:arr_cl(q,3,l)}var j=document.getElementsByClassName("live-files-qty");for(var p=0;p<j.length;p++){j[p].innerHTML=m}}function select_browse(e){var d=document.getElementsByClassName("browse-chk");if(e=="all"){for(var f=0;f<d.length;f++){d[f].checked=true}}else{if(e=="none"){for(var f=0;f<d.length;f++){d[f].checked=false}}else{if(e=="invert"){for(var f=0;f<d.length;f++){d[f].checked=!d[f].checked}}else{gid("browse-chk-"+e).checked=true}}}select_browse_update()}function select_browse_rightclick(b){select_browse("none");if(b!=false){select_browse(b)}}function tree_meaning(e){var f=gid("layout");var d=new Array("tree-browse","tree-copy","tree-cut");if(e=="copy"){arr_cl(d,1,f)}else{if(e=="cut"){arr_cl(d,2,f)}else{arr_cl(d,0,f)}}}function tree_visibility(d){var c=gid("layout");if(d=="open"){add_cl("tree-visible",c)}else{rem_cl("tree-visible",c)}tree_meaning()}function tree_open(f,e){var d=new Array(gid("tree-li-"+f),gid("tree-ul-"+f));if(f=="all"){d=document.getElementsByClassName("tree-li-ul")}if(e=="open"){add_cl("tree-open",d)}else{rem_cl("tree-open",d)}}function tool_inpt_show(b){rem_cl("tool-mobile-visible",document.getElementsByClassName("tool-mobile-visible"));if(b!=false){add_cl("tool-mobile-visible",gid("tool-inpt-"+b))}}function tool_copy(){if(selected_qty()==0){return}tree_visibility("open");tree_meaning("copy");gid("paste-req-meaning").value="copy"}function tool_cut(){if(selected_qty()==0){return}tree_visibility("open");tree_meaning("cut");gid("paste-req-meaning").value="cut"}function paste_tool(b){request_send("paste__"+b)}function tool_delete(){if(confirm("Delete selected files?")){request_send("delete")}}function tool_perms(){if(gval("input-perms")==""){return}request_send("perms")}function tool_zip(){request_send("zip")}function tool_rename(){if(gval("input-rename")==""){return}request_send("rename")}function tool_unzip(){request_send("unzip")}function tool_newfolder(){if(gval("input-newfolder")==""){return}request_send("newfolder")}function tool_newfile(){if(gval("input-newfile")==""){return}request_send("newfile")}function tool_edit_save(){request_send("editsave")}function tool_upload(){request_send("upload","form-upload")}function upload_add(){var f=gid("upload-inputs");var h=f.getElementsByClassName("upload-file-input").length+1;var g=document.createElement("INPUT");g.setAttribute("type","file");g.setAttribute("name","upload_file_"+h);g.setAttribute("id","upload-file-"+h);g.setAttribute("class","upload-file-input");g.setAttribute("onchange","upload_change("+h+")");f.insertBefore(g,f.firstChild);f=gid("upload-labels");var j=document.createElement("div");var k='<div class="item inpt upload-label" id="upload-label-'+h+'"> 						<table class="tab tab-row"> 							<tr> 								<td class="intext"><input type="text" readonly onclick="upload_choose('+h+')" id="upload-label-name-'+h+'" placeholder="Choose file..." /></td> 								<td class="submit ns"><a title="Choose file" onclick="upload_choose('+h+')" class="btnic"><svg class="svg-ic"><use xlink:href="#ic-file" /></svg></a></td> 								<td class="submit ns"><a title="Remove" onclick="upload_remove('+h+')" class="btnic"><svg class="svg-ic"><use xlink:href="#ic-close" /></svg></a></td> 							</tr> 						</table> 					</div>';j.innerHTML=k;f.insertBefore(j.firstChild,f.lastChild)}function upload_change(g){var h=gval("upload-file-"+g);if(h==""){if(gval("upload-label-name-"+g)==""){return}gid("upload-label-name-"+g).value="";var f=document.getElementsByClassName("upload-label").length;if(f>1){gid("upload-file-"+g).disabled=true;if(gval("upload-label-name-"+g)==""){var e=gid("upload-label-"+g)}e.parentNode.removeChild(e)}if(f>2){rem_cl("uploading-nothing",gid("layout"))}else{add_cl("uploading-nothing",gid("layout"))}return}rem_cl("uploading-nothing",gid("layout"));h=h.split("\\");h=h[h.length-1].split("/");h=h[h.length-1];if(gval("upload-label-name-"+g)==""){upload_add()}gid("upload-label-name-"+g).value=h}function upload_choose(b){gid("upload-file-"+b).click()}function upload_remove(b){gid("upload-file-"+b).value="";upload_change(b)}function request_send(k,f){f=f||"form-main";var g=mu_requests_to;var j=new FormData(gid(f));var h=new XMLHttpRequest();j.append("req",k);h.onreadystatechange=function(){if(this.readyState==4&&this.status==200){request_done(this.responseText)}};if(k=="upload"){h.upload.onprogress=function(b){var a=Math.ceil((b.loaded/b.total)*100);gid("response-output").innerHTML="Upload progress: <b>"+a+"%</b>";arr_cl(new Array("resp-none","resp-error","resp-success","resp-progress"),3,gid("layout"))}}h.open("POST",g,true);h.send(j)}function request_done(p){var k="Unknown error";var s=false;var n=true;var l=false;switch(p){case"err_login":k="You're not logged in!";break;case"err_guest":k="Guest Session";s="Permission denied.";n=false;break;case"rename_err_missing":k="File not found";break;case"rename_err_invalid_name":k="Invalid name";s="File name can't contain special characters.";n=false;break;case"rename_err_already_existing":k="Already in use";s="Please, choose a different file name.";n=false;break;case"rename_err":k="Unable to rename this file";s="Check permissions.";n=false;break;case"rename_ok":l=true;k="Succesfully renamed!";break;case"unzip_err_extension":k="Missing extension";s="Enable PHP Zip extension, please.";n=false;break;case"unzip_err_missing":k="File not found";break;case"unzip_err_opening":k="Unable to open zip file";s="Is this a zip file?";n=false;break;case"unzip_err_extracting":k="Unable to unzip";s="Check permissions.";n=false;break;case"unzip_ok":l=true;k="Succesfully unzipped!";break;case"delete_err":k="The selected files can't be deleted";s="Check permissions.";n=false;break;case"delete_ok":l=true;k="Successfully deleted!";break;case"paste_err_destination":k="Destination path not found";break;case"paste_err_cut_dest_inside_source":k="Incorrect destination";s="It's a subfolder of one of the items you're moving.";n=false;break;case"paste_err_copy":k="Unable to copy one or more files";s="Check permissions.";n=false;break;case"paste_err_cut":k="Unable to move one or more files";s="Check permissions.";n=false;break;case"paste_ok_copy":l=true;k="Successfully copied!";break;case"paste_ok_cut":l=true;k="Successfully moved!";break;case"perms_err_input":k="Wrong input";s="Type a valid perms string (e.g. 0775).";n=false;break;case"perms_err":k="Unable to update permissions";n=false;break;case"perms_ok":l=true;k="Permissions succesfully changed!";break;case"zip_err_extension":k="Missing extension";s="Enable PHP Zip extension, please.";n=false;break;case"zip_err":k="Unable to create a zip archive";s="Check permissions.";n=false;break;case"zip_ok":l=true;k="Succesfully zipped!";break;case"newfolder_err_invalid_name":k="Invalid name";s="The name can't contain special characters.";n=false;break;case"newfolder_err_already_existing":k="Already in use";s="Please, choose a different folder name.";n=false;break;case"newfolder_err":k="Unable to create the new folder";s="Check permissions.";n=false;break;case"newfolder_ok":l=true;k="Folder successfully created!";break;case"newfile_err_invalid_name":k="Invalid name";s="The name can't contain special characters.";n=false;break;case"newfile_err_already_existing":k="Already in use";s="Please, choose a different file name.";n=false;break;case"newfile_err":k="Unable to create the new file";s="Check permissions.";n=false;break;case"newfile_ok":l=true;k="File successfully created!";break;case"upload_err":k="Unable to upload files";s="Maybe thery're too big.";n=false;break;case"upload_ok":l=true;k="Succesfully uploaded!";break;case"editsave_err_location":k="File not found";break;case"editsave_err":k="Unable to save changes";s="Check file's permissions.";n=false;break;case"editsave_ok":l=true;k="Succesfully saved!";n=false;document.title=mu_session_title;arr_cl(new Array("editor-changed","editor-not-changed"),1,gid("layout"));break}var q="<b> ERROR: "+k+"</b>";var o=gid("response-output");var m=new Array("resp-none","resp-error","resp-success","resp-progress");var r=1;if(l){r=2;q="<b>"+k+"</b>"}if(s){q=q+" &mdash; "+s}o.innerHTML=q;arr_cl(m,r,gid("layout"));if(n){setTimeout(function(){location.reload()},700)}else{setTimeout(function(){arr_cl(m,0,gid("layout"))},1500)}}function ctrl_on_textbox(b){if(b.target.nodeName=="INPUT"&&(b.target.type=="text"||b.target.type=="password")||b.target.nodeName=="TEXTAREA"){return true}return false}window.onload=function(){document.body.addEventListener("keydown",function(a){a=a||window.event;var c=a.which||a.keyCode;var b=a.ctrlKey?a.ctrlKey:((c===17)?true:false);if(b){switch(c){case 82:location.reload();event.preventDefault();break;case 83:if(mu_session_status=="edit"){tool_edit_save()}event.preventDefault();break;case 67:if(mu_session_status=="edit"){return}if(ctrl_on_textbox(a)){return}tool_copy();event.preventDefault();break;case 88:if(ctrl_on_textbox(a)||mu_session_status=="edit"){return}tool_cut();event.preventDefault();break;case 65:if(ctrl_on_textbox(a)||mu_session_status=="edit"){return}select_browse("all");event.preventDefault();break}}},false)};var sort_browse_elements_by__status={iter_name:0,iter_size:0,iter_perms:0,currently_by:"name"};function sort_browse_elements_by(a){var g=document.getElementById("Sortable_Browse_Elements");var c=g.getElementsByClassName("browse-item");var h=[];for(f=0;f<c.length;f++){h[f]={isdir:parseInt(c[f].getElementsByClassName("sortable_browse__isdir")[0].value),fname:c[f].getElementsByClassName("sortable_browse__name")[0].value,fsize:parseFloat(c[f].getElementsByClassName("sortable_browse__size")[0].value),perms:c[f].getElementsByClassName("sortable_browse__perms")[0].value.toString(),domobj:c[f]};h[f].dirfname=(1-h[f].isdir).toString()+""+h[f].fname}var b;if(a=="name"){if(sort_browse_elements_by__status.currently_by==a){sort_browse_elements_by__status.iter_name++}if(sort_browse_elements_by__status.iter_name==1){b=function(k,j){return k.dirfname<j.dirfname}}else{if(sort_browse_elements_by__status.iter_name==2){b=function(k,j){return k.fname>j.fname}}else{if(sort_browse_elements_by__status.iter_name==3){b=function(k,j){return k.fname<j.fname}}else{sort_browse_elements_by__status.iter_name=0;b=function(k,j){return k.dirfname>j.dirfname}}}}}else{if(a=="size"){if(sort_browse_elements_by__status.currently_by==a){sort_browse_elements_by__status.iter_size++}if(sort_browse_elements_by__status.iter_size==1){b=function(k,j){return k.fsize<j.fsize}}else{sort_browse_elements_by__status.iter_size=0;b=function(k,j){return k.fsize>j.fsize}}}else{if(a=="perms"){if(sort_browse_elements_by__status.currently_by==a){sort_browse_elements_by__status.iter_perms++}if(sort_browse_elements_by__status.iter_perms==1){b=function(k,j){return k.perms<j.perms}}else{sort_browse_elements_by__status.iter_perms=0;b=function(k,j){return k.perms>j.perms}}}}}sort_browse_elements_by__status.currently_by=a;for(var f=0;f<h.length-1;f++){for(var d=f+1;d<h.length;d++){if(b(h[f],h[d])){var e=h[f];h[f]=h[d];h[d]=e}}}for(var f=0;f<h.length;f++){g.appendChild(h[f].domobj)}}</script>
<?php }?>
</head>
<body>
<svg display="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<symbol id="ic-logo" viewBox="0 0 256 256"><path d="M 125.1 0L119.6 0.2L118.6 5.4L118.5 7.3L118.7 10.5L111.5 11.3L111 8.1L110.6 6.3L108.5 1.4L103 2.4L102.7 7.7L102.9 9.5L103.5 12.7L96.4 14.5L95.6 11.4L94.9 9.6L92.1 5L86.8 6.7L87.2 12L87.6 13.8L88.7 16.9L81.9 19.5L80.6 16.5L79.7 14.9L76.4 10.8L71.3 13.1L72.4 18.3L73 20.1L74.5 23L68.1 26.5L66.5 23.7L65.4 22.2L61.5 18.5L56.8 21.5L58.5 26.5L59.5 28.2L61.3 30.9L55.4 35.2L53.4 32.6L52.1 31.3L47.8 28.1L43.5 31.7L45.9 36.4L47 38L49.2 40.4L43.9 45.4L41.6 43.1L40.1 42L35.5 39.4L31.7 43.5L34.7 48L35.9 49.3L38.4 51.4L33.9 57.1L31.2 55.1L29.6 54.2L24.7 52.3L21.5 56.8L25 60.8L26.5 62L29.2 63.8L25.4 70L22.6 68.4L20.8 67.6L15.7 66.4L13.1 71.3L17.1 74.8L18.7 75.8L21.6 77.2L18.7 83.9L15.7 82.6L13.9 82.1L8.6 81.5L6.7 86.8L11.1 89.7L12.9 90.5L16 91.5L13.9 98.5L10.8 97.7L8.9 97.5L3.6 97.5L2.4 103L7.1 105.3L9 105.9L12.1 106.5L11 113.6L7.8 113.3L6 113.2L0.7 114L0.2 119.6L5.2 121.3L7.1 121.6L10.3 121.8L10.2 129L6.9 129L5 129.3L0 130.8L0.2 136.3L5.4 137.3L7.3 137.4L10.5 137.2L11.3 144.4L8.1 144.9L6.3 145.3L1.4 147.5L2.4 152.9L7.7 153.2L9.5 153L12.7 152.4L14.5 159.5L11.4 160.3L9.6 161L5 163.8L6.7 169.1L12 168.7L13.8 168.3L16.9 167.2L19.5 174L16.5 175.3L14.9 176.2L10.8 179.5L13.1 184.6L18.3 183.5L20.1 182.9L23 181.4L26.5 187.8L23.7 189.4L22.2 190.5L18.5 194.4L21.5 199.1L26.5 197.4L28.2 196.5L30.9 194.6L35.2 200.5L32.6 202.5L31.3 203.8L28.1 208.1L31.7 212.4L36.4 210L38 208.9L40.4 206.7L45.4 212L43.1 214.3L42 215.8L39.4 220.5L43.5 224.2L48 221.2L49.3 220L51.4 217.5L57.1 222L55.1 224.7L54.2 226.3L52.3 231.2L56.8 234.4L60.8 230.9L62 229.5L63.8 226.7L70 230.5L68.4 233.3L67.6 235.1L66.4 240.2L71.3 242.8L74.8 238.8L75.8 237.2L77.2 234.3L83.9 237.2L82.6 240.2L82.1 242L81.5 247.3L86.8 249.2L89.7 244.8L90.5 243L91.5 240L98.5 242L97.7 245.1L97.5 247L97.5 252.3L103 253.5L105.3 248.8L105.9 247L106.5 243.8L113.6 244.9L113.3 248.1L113.2 250L114 255.2L119.6 255.7L121.3 250.7L121.6 248.8L121.8 245.6L129 245.7L129 249L129.3 250.9L130.8 256L136.3 255.7L137.3 250.5L137.4 248.6L137.2 245.4L144.4 244.6L144.9 247.8L145.3 249.6L147.5 254.5L152.9 253.5L153.2 248.2L153 246.4L152.4 243.2L159.5 241.5L160.3 244.5L161 246.3L163.8 250.9L169.1 249.2L168.7 243.9L168.3 242.1L167.2 239L174 236.4L175.3 239.4L176.2 241L179.5 245.1L184.6 242.8L183.5 237.6L182.9 235.8L181.4 232.9L187.8 229.4L189.4 232.2L190.5 233.7L194.4 237.4L199.1 234.4L197.4 229.4L196.5 227.7L194.6 225L200.5 220.7L202.5 223.3L203.8 224.6L208.1 227.8L212.4 224.2L210 219.5L208.9 218L206.7 215.5L212 210.5L214.3 212.8L215.8 213.9L220.5 216.5L224.2 212.4L221.2 208L220 206.6L217.5 204.5L222 198.8L224.7 200.8L226.3 201.7L231.2 203.6L234.4 199.1L230.9 195.1L229.5 193.9L226.7 192.1L230.5 185.9L233.3 187.5L235.1 188.3L240.2 189.5L242.8 184.6L238.8 181.1L237.2 180.1L234.3 178.7L237.2 172L240.2 173.3L242 173.8L247.3 174.4L249.2 169.1L244.8 166.2L243 165.4L240 164.4L242 157.4L245.1 158.2L247 158.5L252.3 158.4L253.5 152.9L248.8 150.6L247 150L243.8 149.5L244.9 142.3L248.1 142.6L250 142.7L255.2 141.9L255.7 136.3L250.7 134.6L248.8 134.3L245.6 134.1L245.7 126.9L249 126.9L250.9 126.6L256 125.1L255.7 119.6L250.5 118.6L248.6 118.5L245.4 118.7L244.6 111.5L247.8 111L249.6 110.6L254.5 108.5L253.5 103L248.2 102.7L246.4 102.9L243.2 103.5L241.5 96.4L244.5 95.6L246.3 94.9L250.9 92.1L249.2 86.8L243.9 87.2L242.1 87.6L239 88.7L236.4 81.9L239.4 80.6L241 79.7L245.1 76.4L242.8 71.3L237.6 72.4L235.8 73L232.9 74.5L229.4 68.1L232.2 66.5L233.7 65.4L237.4 61.5L234.4 56.8L229.4 58.5L227.7 59.5L225 61.3L220.7 55.4L223.3 53.4L224.6 52.1L227.8 47.8L224.2 43.5L219.5 45.9L218 47L215.5 49.2L210.5 43.9L212.8 41.6L213.9 40.1L216.5 35.5L212.4 31.7L208 34.7L206.6 35.9L204.5 38.4L198.8 33.9L200.8 31.2L201.7 29.6L203.6 24.7L199.1 21.5L195.1 25L193.9 26.5L192.1 29.2L185.9 25.4L187.5 22.6L188.3 20.8L189.5 15.7L184.6 13.1L181.1 17.1L180.1 18.7L178.7 21.6L172 18.7L173.3 15.7L173.8 13.9L174.4 8.6L169.1 6.7L166.2 11.1L165.4 12.9L164.4 16L157.4 13.9L158.2 10.8L158.5 8.9L158.4 3.6L152.9 2.4L150.6 7.1L150 9L149.5 12.1L142.3 11L142.6 7.8L142.7 6L141.9 0.7L136.3 0.2L134.6 5.2L134.3 7.1L134.1 10.3L126.9 10.2L126.9 6.9L126.6 5L125.1 0zM 126.8 24C139 23.8 151.4 25.8 163.5 30.2C187.8 39.1 206.8 56 218.5 76.9L139.2 113.9C137.7 112.7 136 111.7 134.1 111C132.2 110.4 130.3 110 128.4 110L91.4 30.6C102.6 26.4 114.6 24.1 126.8 24zM 64.2 45.7L26.3 150C22.3 131.4 23.3 111.5 30.2 92.4C37.2 73.2 49.2 57.3 64.2 45.7zM 81 46.3L113.9 116.7C112.7 118.2 111.7 119.9 111 121.8C109.8 125.1 109.7 128.5 110.4 131.8L37.6 165.6L81 46.3zM 216.4 95.5L172.9 215L139 142.2C141.6 140.2 143.6 137.4 144.9 134.1C145.5 132.2 145.9 130.3 146 128.4L216.4 95.5zM 229.6 105.9C233.6 124.5 232.6 144.4 225.7 163.5C218.7 182.7 206.7 198.6 191.7 210.2L229.6 105.9zM 127.5 119C128.6 118.9 129.9 119.1 131 119.5C135.7 121.2 138.1 126.4 136.4 131C134.7 135.7 129.5 138.1 124.9 136.4C120.2 134.7 117.8 129.5 119.5 124.9C120.8 121.4 124 119.1 127.5 119zM 113.7 139C115.7 141.6 118.5 143.6 121.8 144.9C125.1 146.1 128.5 146.2 131.8 145.5L168.2 223.8C144.9 233.6 118 235 92.4 225.7C66.7 216.3 47 198 35.5 175.5L113.7 139z" /></symbol>
<symbol id="ic-select-all" viewBox="0 0 24 24"><path d="M3 5h2V3c-1.1 0-2 .9-2 2zm0 8h2v-2H3v2zm4 8h2v-2H7v2zM3 9h2V7H3v2zm10-6h-2v2h2V3zm6 0v2h2c0-1.1-.9-2-2-2zM5 21v-2H3c0 1.1.9 2 2 2zm-2-4h2v-2H3v2zM9 3H7v2h2V3zm2 18h2v-2h-2v2zm8-8h2v-2h-2v2zm0 8c1.1 0 2-.9 2-2h-2v2zm0-12h2V7h-2v2zm0 8h2v-2h-2v2zm-4 4h2v-2h-2v2zm0-16h2V3h-2v2zM7 17h10V7H7v10zm2-8h6v6H9V9z"/></symbol>
<symbol id="ic-unselect-all" viewBox="0 0 24 24"><path d="M5 3C3.9 3 3 3.9 3 5L5 5L5 3zM7 3L7 5L9 5L9 3L7 3zM11 3L11 5L13 5L13 3L11 3zM15 3L15 5L16.21875 5L17 4.125L17 3L15 3zM19 3L3 21L5 21L21 3L19 3zM20.875 4.28125L20.21875 5L21 5C21 4.7490625 20.960364 4.5030608 20.875 4.28125zM3 7L3 9L5 9L5 7L3 7zM7 7L7 15.375L9 13.125L9 9L12.65625 9L14.4375 7L7 7zM19 7L19 9L21 9L21 7L19 7zM17 8.625L15 10.875L15 15L11.34375 15L9.5625 17L17 17L17 8.625zM3 11L3 13L5 13L5 11L3 11zM19 11L19 13L21 13L21 11L19 11zM3 15L3 17L5 17L5 15L3 15zM19 15L19 17L21 17L21 15L19 15zM3 19C3 19.250937 3.039636 19.496939 3.125 19.71875L3.78125 19L3 19zM7.78125 19L7 19.875L7 21L9 21L9 19L7.78125 19zM11 19L11 21L13 21L13 19L11 19zM15 19L15 21L17 21L17 19L15 19zM19 19L19 21C20.1 21 21 20.1 21 19L19 19z" /></symbol>
<symbol id="ic-trash" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></symbol>
<symbol id="ic-copy" viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></symbol>
<symbol id="ic-cut" viewBox="0 0 24 24"><circle cx="6" cy="18" fill="none" r="2"/><circle cx="12" cy="12" fill="none" r=".5"/><circle cx="6" cy="6" fill="none" r="2"/><path d="M9.64 7.64c.23-.5.36-1.05.36-1.64 0-2.21-1.79-4-4-4S2 3.79 2 6s1.79 4 4 4c.59 0 1.14-.13 1.64-.36L10 12l-2.36 2.36C7.14 14.13 6.59 14 6 14c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4c0-.59-.13-1.14-.36-1.64L12 14l7 7h3v-1L9.64 7.64zM6 8c-1.1 0-2-.89-2-2s.9-2 2-2 2 .89 2 2-.9 2-2 2zm0 12c-1.1 0-2-.89-2-2s.9-2 2-2 2 .89 2 2-.9 2-2 2zm6-7.5c-.28 0-.5-.22-.5-.5s.22-.5.5-.5.5.22.5.5-.22.5-.5.5zM19 3l-6 6 2 2 7-7V3z"/></symbol>
<symbol id="ic-plus" viewBox="0 0 24 24"><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></symbol>
<symbol id="ic-upload" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></symbol>
<symbol id="ic-pencil" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></symbol>
<symbol id="ic-perms" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></symbol>
<symbol id="ic-navigate" viewBox="0 0 24 24"><path d="M12 10.9c-.61 0-1.1.49-1.1 1.1s.49 1.1 1.1 1.1c.61 0 1.1-.49 1.1-1.1s-.49-1.1-1.1-1.1zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm2.19 12.19L6 18l3.81-8.19L18 6l-3.81 8.19z"/></symbol>
<symbol id="ic-close" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></symbol>
<symbol id="ic-up-arrow" viewBox="0 0 24 24"><path d="M4 12l1.41 1.41L11 7.83V20h2V7.83l5.58 5.59L20 12l-8-8-8 8z"/></symbol>
<symbol id="ic-folder-empty" viewBox="0 0 24 24"><path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z"/></symbol>
<symbol id="ic-folder-full" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></symbol>
<symbol id="ic-scroll-top" viewBox="0 0 24 24"><path d="M8 11h3v10h2V11h3l-4-4-4 4zM4 3v2h16V3H4z"/></symbol>
<symbol id="ic-text-wrap" viewBox="0 0 24 24"><path d="M4 19h6v-2H4v2zM20 5H4v2h16V5zm-3 6H4v2h13.25c1.1 0 2 .9 2 2s-.9 2-2 2H15v-2l-3 3 3 3v-2h2c2.21 0 4-1.79 4-4s-1.79-4-4-4z"/></symbol>
<symbol id="ic-text-lines" viewBox="0 0 24 24"><path d="M2 17h2v.5H3v1h1v.5H2v1h3v-4H2v1zm1-9h1V4H2v1h1v3zm-1 3h1.8L2 13.1v.9h3v-1H3.2L5 10.9V10H2v1zm5-6v2h14V5H7zm0 14h14v-2H7v2zm0-6h14v-2H7v2z"/></symbol>
<symbol id="ic-file" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/></symbol>
<symbol id="ic-paste" viewBox="0 0 24 24"><path d="M19 3H4.99c-1.11 0-1.98.9-1.98 2L3 19c0 1.1.88 2 1.99 2H19c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 12h-4c0 1.66-1.35 3-3 3s-3-1.34-3-3H4.99V5H19v10zm-3-5h-2V7h-4v3H8l4 4 4-4z"/></symbol>
<symbol id="ic-list" viewBox="0 0 24 24"><path d="M3 5v14h17V5H3zm4 2v2H5V7h2zm-2 6v-2h2v2H5zm0 2h2v2H5v-2zm13 2H9v-2h9v2zm0-4H9v-2h9v2zm0-4H9V7h9v2z"/></symbol>
<symbol id="ic-floppy" viewBox="0 0 24 24"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></symbol>
<symbol id="ic-link" viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></symbol>
<symbol id="ic-unzip" viewBox="0 0 24 24"><path d="M20.55 5.22l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.15.55L3.46 5.22C3.17 5.57 3 6.01 3 6.5V19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.49-.17-.93-.45-1.28zM12 9.5l5.5 5.5H14v2h-4v-2H6.5L12 9.5zM5.12 5l.82-1h12l.93 1H5.12z"/></symbol>
<symbol id="ic-zip" viewBox="0 0 24 24"><path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27zM12 17.5L6.5 12H10v-2h4v2h3.5L12 17.5zM5.12 5l.81-1h12l.94 1H5.12z"/></symbol>
<symbol id="ic-new-folder" viewBox="0 0 24 24"><path d="M20 6h-8l-2-2H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-1 8h-3v3h-2v-3h-3v-2h3V9h2v3h3v2z"/></symbol>
<symbol id="ic-selection" viewBox="0 0 24 24"><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.11 0 2-.9 2-2V5c0-1.1-.89-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></symbol>
<symbol id="ic-file" viewBox="0 0 24 24"><path d="M6 2c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6H6zm7 7V3.5L18.5 9H13z"/></symbol>
<symbol id="ic-invert" viewBox="0 0 24 24"><path d="M17.66 7.93L12 2.27 6.34 7.93c-3.12 3.12-3.12 8.19 0 11.31C7.9 20.8 9.95 21.58 12 21.58c2.05 0 4.1-.78 5.66-2.34 3.12-3.12 3.12-8.19 0-11.31zM12 19.59c-1.6 0-3.11-.62-4.24-1.76C6.62 16.69 6 15.19 6 13.59s.62-3.11 1.76-4.24L12 5.1v14.49z"/></symbol>
<symbol id="ic-more" viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></symbol>
<symbol id="ic-reload" viewBox="0 0 24 24"><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></symbol>
<symbol id="ic-home" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></symbol>
<symbol id="ic-logout" viewBox="0 0 24 24"><path d="M 12 4 C 9.79 4 8 5.79 8 8 C 8 10.21 9.79 12 12 12 C 14.21 12 16 10.21 16 8 C 16 5.79 14.21 4 12 4 z M 12 5.9003906 C 13.16 5.9003906 14.099609 6.84 14.099609 8 C 14.099609 9.16 13.16 10.099609 12 10.099609 C 10.84 10.099609 9.9003906 9.16 9.9003906 8 C 9.9003906 6.84 10.84 5.9003906 12 5.9003906 z M 13.865234 12.572266 L 12.455078 13.982422 L 15.638672 17.166016 L 12.455078 20.349609 L 13.865234 21.759766 L 17.048828 18.576172 L 20.232422 21.759766 L 21.642578 20.349609 L 18.458984 17.166016 L 21.642578 13.982422 L 20.232422 12.572266 L 17.048828 15.755859 L 13.865234 12.572266 z M 12 13 C 9.33 13 4 14.34 4 17 L 4 20 L 12 20 L 12 18.099609 L 5.9003906 18.099609 L 5.9003906 17 C 5.9003906 16.36 9.03 14.900391 12 14.900391 L 12 13 z " /></symbol>
</svg>
<div id="layout" class="tree-browse browse-select-nothing uploading-nothing editor-not-changed editor-text-nowrap resp-none">
<div class="muonbar layout-block ns <?php if(muonInterface("login")){echo "muonbar-login";}else if(muonInterface("license")){echo "muonbar-license";}?>">
<form method="post" action="<?php echo mainPage();?>">
<input type="hidden" name="logout" value="logout" />
<input type="submit" style="display:none" value="Logout" id="logout-submit" />
<table class="tab-row">
<tr>
<td class="logo"><a href="<?php echo mu_website;?>" target="_blank"><svg class="svg-ic"><use xlink:href="#ic-logo" /></svg></a></td>
<td class="title"><?php echo mu_title_html;?> <a title="License" rel="license" target="_blank" href="<?php echo licensePage().getPrevQuery("_srv","&");?>" class="learnmore">Muon <?php echo mu_version;?></a></td>
<td class="sep">&nbsp;</td>
<td class="btns">
<?php if(!muonInterface("login")){?><a title="Show Tree" onclick="tree_visibility('open')" class="btn tree-open-btn"><svg class="svg-ic"><use xlink:href="#ic-list" /></svg></a><?php }?>
<a title="Refresh" onclick="location.reload()" class="btn"><svg class="svg-ic"><use xlink:href="#ic-reload" /></svg></a>
<?php if(!muonInterface("login")){?><label for="logout-submit"><a title="Logout" class="btn gray"><svg class="svg-ic"><use xlink:href="#ic-logout" /></svg></a></label><?php }?>
<?php if(defined("mu_home_link"))echo "<a title=\"Your website: ".mu_home_link."\" href=\"".mu_home_link."\" target=\"_blank\" class=\"btn gray\"><svg class=\"svg-ic\"><use xlink:href=\"#ic-link\" /></svg></a>";?>
</td>
</tr>
</table>
</form>
</div>
<?php if(muonInterface("license")){?>
<div class="layout-block login license ns">
<div class="license-block">
[ <a class="ahr" onclick="window.close()">Close</a> ] <b>Muon version <?php echo mu_version;?> &mdash;
<a class="ahr" href=" <?php echo mu_website;?>">Website</a></b>
<br/><br/>
<i>This software is released under the MIT License (open source):</i>
<br/><br/>
Copyright 2022 InfoEin
<br/><br/>
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
<br/><br/>
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
<br/><br/>
<b>Made in the <a class="ahr" href="http://europa.eu/" target="_blank">EU</a></b>
</div>
</div>
<?php die('</body></html>');}?>
<?php if(muonInterface("login")){?>
<div class="layout-block login">
<?php if($mu_session_data){?>
<div class="header-block err"><b>Wrong username/password.</b><br/>Please, try again.</div>
<?php }else{?>
<div class="header-block">
Login to manage your web space with Muon <?php echo mu_version;?> &mdash;
<a class="ahr" href="<?php echo mu_website;?>" target="_blank" title="Learn more about Muon">Mu-what?</a>
<a title="License" class="ahr" rel="license" target="_blank" href="<?php echo licensePage().getPrevQuery("_srv","&");?>" class="learnmore">License</a>
</div>
<?php }?>
<div class="login-block">
<form method="post" action="<?php echo mainPage().getPrevQuery();?>">
<input type="text" name="login" placeholder="Username" />
<input type="password" name="pass" placeholder="Password" />
<input type="submit" value="Login" />
</form>
</div>
</div>
<?php die('</body></html>');}?>
<form id="form-main">
<input type="hidden" name="<?php if(muonInterface("edit"))echo "editing_path";else echo "browsing_path";?>" value="<?php echo $mu_session_data->getFullPath(false);?>" />
<a class="tree-opener-tablet layout-block" onclick="tree_visibility('open')" title="Show Tree"><svg class="svg-ic"><use xlink:href="#ic-list" /></svg></a>
<input type="hidden" name="paste_meaning" id="paste-req-meaning" value="" />
<div class="tree ns layout-block" id="tree">
<div class="tree-close-btn" onclick="tree_visibility('close')"><svg class="svg-ic"><use xlink:href="#ic-close" /></svg></div>
<div class="tree-container">
<div class="tree-title brw">Browse:</div>
<div class="tree-title cpy">Copy <span class="live-files-qty">0</span> to:</div>
<div class="tree-title cut">Move <span class="live-files-qty">0</span> to:</div>
<div class="tree-toggle">
<span class="btn" onclick="tree_open('all','open')">Open all</span>
<span>&nbsp;&mdash;&nbsp;</span>
<span class="btn" onclick="tree_open('all','close')">Close all</span>
</div>
<div class="tree-content">
<?php function printTree($tree,$home=true){$path="/".ltrim($tree->fullPath,"/");$extra_class="";if($tree->browsingThis)$extra_class.=" tree-browsing";if($tree->browsingInsideThis)$extra_class.=" tree-open";if(!$home)$extra_class.=" tree-li-ul";echo "<li id=\"tree-li-".$tree->treeId."\" class=\"tree-li".$extra_class."\">";$name=$tree->name;if($home){$name="HOME";echo "<div class=\"tree-btn tree-btn-noclick\"><svg class=\"svg-ic\"><use xlink:href=\"#ic-home\" /></svg></div> ";}else if($tree->countFolders){?>
<a class="tree-btn tree-btn-open" onclick="tree_open('<?php echo $tree->treeId;?>','open')"><svg class="svg-ic"><use xlink:href="#ic-folder-full" /></svg></a>
<a class="tree-btn tree-btn-close" onclick="tree_open('<?php echo $tree->treeId;?>','close')"><svg class="svg-ic"><use xlink:href="#ic-folder-empty" /></svg></a>
<?php }else{echo "<span class=\"tree-btn tree-btn-noclick\"><svg class=\"svg-ic\"><use xlink:href=\"#ic-folder-empty\" /></svg></span> ";}?>
<a class="tree-name brw" title="Browse <?php echo $name;?>" href="<?php echo browsePage($path);?>"><?php echo $name;?></a>
<a class="tree-name cpy" title="Copy to <?php echo $name;?>" onclick="paste_tool('.<?php echo $tree->fullPath;?>')"><span class="tree-btn paste"><svg class="svg-ic"><use xlink:href="#ic-paste" /></svg></span> <?php echo $name;?></a>
<a class="tree-name cut" title="Move to <?php echo $name;?>" onclick="paste_tool('.<?php echo $tree->fullPath;?>')"><span class="tree-btn paste"><svg class="svg-ic"><use xlink:href="#ic-paste" /></svg></span> <?php echo $name;?></a>
<?php echo "</li>";if($tree->countFolders){echo "<ul id=\"tree-ul-".$tree->treeId."\" class=\"tree-ul".$extra_class."\">";foreach($tree->folders as $fold)printTree($fold,false);echo "</ul>";}}chdir($mu_wd_browse);printTree(new MuonTree($mu_session_data->getFolderPath()));chdir($mu_wd_script);?>
</div>
</div>
</div>
<div class="browsebar ns layout-block">
<div class="browsebar-blocks">
<span class="browsebar-block sep">&nbsp;</span>
<?php $currentPath=$mu_session_data->explodePath();if($currentPath){?>
<span class="browsebar-block folder"><a class="nav" title="Browse /" href="<?php echo browsePage();?>">HOME</a></span>
<span class="browsebar-block sep">/</span>
<?php $localPath='';for($i=0;$i<count($currentPath)-1;$i++){$localPath=$localPath.'/'.$currentPath[$i];?>
<span class="browsebar-block folder"><a class="nav" title="Browse <?php echo $currentPath[$i];?>" href="<?php echo browsePage($localPath);?>"><?php echo $currentPath[$i];?></a></span>
<span class="browsebar-block sep">/</span>
<?php }?>
<span class="browsebar-block folder"><?php echo $mu_session_data->name;?></span>
<?php }else{echo "<span class=\"browsebar-block folder\">HOME</span>";}?>
<span class="browsebar-block sep">&nbsp;</span>
</div>
<div class="browsebar-data">
<span class="text">
<?php if(muonInterface("edit"))echo "Editing: ";else echo "Browsing: ";echo "<b>";if($mu_session_data->name!=".")echo $mu_session_data->name;else echo "/";echo "</b>";?>
<a class="openlink" title="Open" href="<?php echo $mu_wd_relative_prefix;?>.<?php echo $mu_session_data->getFullPath();?>" target="_blank">[OPEN]</a>
&mdash;
<b><?php echo $mu_session_data->sizeWithOM();?>B</b> <span class="perms">P:<?php echo $mu_session_data->perms;?></span>
</span>
</div>
</div>
<div class="bottombar ns layout-block">
<span class="text">
<?php if(mu_guest_session){echo "[ <b>GUEST SESSION</b> ] ";}?>
<b>Desktop shortcuts</b> &mdash;
<?php if(muonInterface("edit"))echo "Ctrl+S: save file / Ctrl+R: reload page ";else echo "Ctrl+A: select all / Ctrl+C: copy files / Ctrl+X: move files / Ctrl+R: reload page / Right click on a file to select it only";?>
</span>
</div>
<div class="outputbar ns layout-block"><span class="text" id="response-output">&nbsp;</span></div>
<?php if(muonInterface("edit")){?>
<div id="tools ns" class="tools layout-block">
<div class="tools-desktop">
<div class="cat ns">
<span class="cat-name">Changes:</span>
<a title="Save changes" onclick="tool_edit_save()" class="item disabled-editor-not-changed">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-floppy" /></svg></span>
<span class="btntex bold">Save changes</span>
</a>
<a title="Discard changes" onclick="event.preventDefault();editor_discard('<?php echo browsePage($mu_session_data->getPath());?>')" class="item disabled-editor-not-changed">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-close" /></svg></span>
<span class="btntex">Discard changes</span>
</a>
<a title="Close editor" href="<?php echo browsePage($mu_session_data->getPath());?>" class="item disabled-editor-changed">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-close" /></svg></span>
<span class="btntex">Close editor</span>
</a>
</div>
<div class="cat ns">
<span class="cat-name">Editor:</span>
<a title="Scroll top" onclick="gid('main-editor').scrollTop=0" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-scroll-top" /></svg></span>
<span class="btntex">Scroll top</span>
</a>
<a title="Wrap text" onclick="editor_text_wrap(true)" class="item disabled-editor-text-wrap">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-text-wrap" /></svg></span>
<span class="btntex">Wrap text</span>
</a>
<a title="Don't wrap text" onclick="editor_text_wrap(false)" class="item disabled-editor-text-nowrap">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-text-lines" /></svg></span>
<span class="btntex">Don't wrap text</span>
</a>
</div>
<div class="cat ns">
<span class="cat-name">Links (new tab):</span>
<a title="Open <?php echo $mu_session_data->name;?>" href="<?php echo $mu_wd_relative_prefix;?><?php echo $mu_session_data->getFullPath(false);?>" target="_blank" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-file" /></svg></span>
<span class="btntex">Open file</span>
</a>
<a title="Open parent folder" href="<?php echo $mu_wd_relative_prefix;?><?php echo $mu_session_data->getPath(false);?>" target="_blank" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-navigate" /></svg></span>
<span class="btntex">Open parent folder</span>
</a>
</div>
</div>
<div class="tools-mobile ns">
<a title="Save changes" onclick="tool_edit_save()" class="item rbord disabled-editor-not-changed">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-floppy" /></svg></span>
<span class="btntex bold">Save</span>
</a>
<a title="Discard changes" onclick="event.preventDefault();editor_discard('<?php echo browsePage($mu_session_data->getPath());?>')" class="item rbord disabled-editor-not-changed">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-close" /></svg></span>
<span class="btntex">Discard</span>
</a>
<a title="Close editor" href="<?php echo browsePage($mu_session_data->getPath());?>" class="item rbord disabled-editor-changed">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-close" /></svg></span>
<span class="btntex">Close</span>
</a>
<a title="Scroll top" onclick="gid('main-editor').scrollTop=0" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-scroll-top" /></svg></span>
<span class="btntex">Top</span>
</a>
<a title="Wrap text" onclick="editor_text_wrap(true)" class="item rbord disabled-editor-text-wrap">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-text-wrap" /></svg></span>
<span class="btntex">Wrap</span>
</a>
<a title="Don't wrap text" onclick="editor_text_wrap(false)" class="item rbord disabled-editor-text-nowrap">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-text-lines" /></svg></span>
<span class="btntex">No wrap</span>
</a>
<a title="Open <?php echo $mu_session_data->name;?>" href="<?php echo $mu_wd_relative_prefix;?><?php echo $mu_session_data->getFullPath(false);?>" target="_blank" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-file" /></svg></span>
<span class="btntex">File</span>
</a>
<a title="Open parent folder" href="<?php echo $mu_wd_relative_prefix;?><?php echo $mu_session_data->getPath(false);?>" target="_blank" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-navigate" /></svg></span>
<span class="btntex">Folder</span>
</a>
</div>
</div>
<div class="main layout-block" id="main-editor">
<div class="editor-container">
<textarea class="editor editor-lineheight" id="text-editor" name="editor_content" spellcheck="false" onkeyup="editor_adjust(false,event)" onpaste="editor_adjust()" oncut="editor_adjust()"><?php $mu_session_data->printContent();?></textarea>
<div class="editor-numbers editor-lineheight ns" id="text-editor-numbers">&nbsp;</div>
<div class="editor-adj hidden ns" id="text-editor-sizeadj">&nbsp;</div>
</div>
</div>
<script type="text/javascript">editor_adjust(true);</script>
<?php }else{?>
<div id="tools" class="tools layout-block">
<div class="tools-desktop">
<div class="cat ns">
<span class="cat-name">Select:</span>
<div class="row row3">
<a title="Select all" onclick="select_browse('all')" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-select-all" /></svg></span>
<span class="btntex">S.All</span>
</a>
<a title="Select none" onclick="select_browse('none')" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-unselect-all" /></svg></span>
<span class="btntex">None</span>
</a>
<a title="Invert selection" onclick="select_browse('invert')" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-invert" /></svg></span>
<span class="btntex">Invert</span>
</a>
</div>
</div>
<div class="cat disabled-select-nothing">
<span class="cat-name ns">Edit:</span>
<a title="Copy file(s)" onclick="tool_copy()" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-copy" /></svg></span>
<span class="btntex">Copy <span class="live-files-qty"></span></span>
</a>
<a title="Move file(s)" onclick="tool_cut()" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-cut" /></svg></span>
<span class="btntex">Move <span class="live-files-qty"></span></span>
</a>
<a title="Delete file(s)" onclick="tool_delete()" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-trash" /></svg></span>
<span class="btntex">Delete <span class="live-files-qty"></span></span>
</a>
<div class="item inpt disabled-select-big-selection" id="tool-inpt-rename">
<table class="tab tab-row">
<tr>
<td class="hidein ns"><a class="btnic" onclick="tool_inpt_show(false)"><svg class="svg-ic"><use xlink:href="#ic-close" /></svg></a></td>
<td class="intext"><input type="text" onkeyup="if(event.keyCode===13)tool_rename()" id="input-rename" name="ren_new_name" placeholder="Rename to..." /></td>
<td class="submit ns"><a title="Rename" onclick="tool_rename()" class="btnic"><svg class="svg-ic"><use xlink:href="#ic-pencil" /></svg></a></td>
</tr>
</table>
</div>
<div class="item inpt" id="tool-inpt-perms">
<table class="tab tab-row">
<tr>
<td class="hidein ns"><a class="btnic" onclick="tool_inpt_show(false)"><svg class="svg-ic"><use xlink:href="#ic-close" /></svg></a></td>
<td class="intext"><input type="text" onkeyup="if(event.keyCode===13)tool_perms()" id="input-perms" name="perm_new_perms" placeholder="Permissions to... (e.g. 0755)" /></td>
<td class="submit ns"><a title="Change permissions" onclick="tool_perms()" class="btnic"><svg class="svg-ic"><use xlink:href="#ic-perms" /></svg></a></td>
</tr>
</table>
</div>
<?php if(zipEnabled()){?>
<a title="Zip selection" onclick="tool_zip()" class="item ns">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-zip" /></svg></span>
<span class="btntex">Zip selection</span>
</a>
<a title="Unzip file" onclick="tool_unzip()" class="item ns disabled-select-one-folder disabled-select-big-selection">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-unzip" /></svg></span>
<span class="btntex">Unzip</span>
</a>
<?php }?>
</div>
<div class="cat nobord disabled-select-one-file disabled-select-one-folder disabled-select-big-selection">
<span class="cat-name ns">Folder:</span>
<a title="Open folder in a new tab" href="<?php echo $mu_wd_relative_prefix;?><?php echo $mu_session_data->getFullPath(false);?>" target="_blank" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-navigate" /></svg></span>
<span class="btntex">Open folder (new tab)</span>
</a>
<div class="item inpt" id="tool-inpt-newfolder">
<table class="tab tab-row">
<tr>
<td class="hidein ns"><a class="btnic" onclick="tool_inpt_show(false)"><svg class="svg-ic"><use xlink:href="#ic-close" /></svg></a></td>
<td class="intext"><input type="text" onkeyup="if(event.keyCode===13)tool_newfolder()" id="input-newfolder" name="new_folder_name" placeholder="New Folder..." /></td>
<td class="submit ns"><a onclick="tool_newfolder()" title="New folder" class="btnic"><svg class="svg-ic"><use xlink:href="#ic-new-folder" /></svg></a></td>
</tr>
</table>
</div>
<div class="item inpt" id="tool-inpt-newfile">
<table class="tab tab-row">
<tr>
<td class="hidein ns"><a class="btnic" onclick="tool_inpt_show(false)"><svg class="svg-ic"><use xlink:href="#ic-close" /></svg></a></td>
<td class="intext"><input type="text" onkeyup="if(event.keyCode===13)tool_newfile()" id="input-newfile" name="new_file_name" placeholder="New File..." /></td>
<td class="submit ns"><a title="New folder" onclick="tool_newfile()" class="btnic"><svg class="svg-ic"><use xlink:href="#ic-plus" /></svg></a></td>
</tr>
</table>
</div>
</div>
<div class="cat nobord tool-upload disabled-select-one-file disabled-select-one-folder disabled-select-big-selection" id="tool-inpt-upload">
<span class="cat-name ns">Upload:</span>
<a class="item close ns" onclick="tool_inpt_show(false)"><span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-close" /></svg></span></a>
<a title="Upload" onclick="tool_upload()" class="item ns submit disabled-uploading-nothing">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-upload" /></svg></span>
<span class="btntex">Upload file(s)</span>
</a>
<div id="upload-labels"><div class="last" style="display:none">&nbsp;</div></div>
</div>
</div>
<div class="tools-mobile ns">
<a title="Select all" onclick="select_browse('all')" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-select-all" /></svg></span>
<span class="btntex">S.All</span>
</a>
<a title="Select none" onclick="select_browse('none')" class="item">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-unselect-all" /></svg></span>
<span class="btntex">None</span>
</a>
<a title="Invert selection" onclick="select_browse('invert')" class="item rbord">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-invert" /></svg></span>
<span class="btntex">Invert</span>
</a>
<a title="Copy file(s)" onclick="tool_copy()" class="item disabled-select-nothing">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-copy" /></svg></span>
<span class="btntex">Copy</span>
</a>
<a title="Move file(s)" onclick="tool_cut()" class="item disabled-select-nothing">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-cut" /></svg></span>
<span class="btntex">Move</span>
</a>
<a title="Delete file(s)" onclick="tool_delete()" class="item rbord disabled-select-nothing">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-trash" /></svg></span>
<span class="btntex">Delete</span>
</a>
<a title="Rename" class="item disabled-select-nothing disabled-select-big-selection" onclick="tool_inpt_show('rename')">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-pencil" /></svg></span>
<span class="btntex">Rename</span>
</a>
<a title="Change permissions" onclick="tool_inpt_show('perms')" class="item rbord disabled-select-nothing">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-perms" /></svg></span>
<span class="btntex">Perms</span>
</a>
<?php if(zipEnabled()){?>
<a title="Zip selection" onclick="tool_zip()" class="item disabled-select-nothing">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-zip" /></svg></span>
<span class="btntex">Zip</span>
</a>
<a title="Unzip file" onclick="tool_unzip()" class="item disabled-select-nothing disabled-select-big-selection disabled-select-one-folder">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-unzip" /></svg></span>
<span class="btntex">Unzip</span>
</a>
<?php }?>
<a title="Open folder in a new tab" href="<?php echo $mu_wd_relative_prefix;?><?php echo $mu_session_data->getFullPath(false);?>" target="_blank" class="item rbord disabled-select-big-selection disabled-select-one-folder disabled-select-one-file">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-navigate" /></svg></span>
<span class="btntex">Folder</span>
</a>
<a title="New folder" onclick="tool_inpt_show('newfolder')" class="item disabled-select-big-selection disabled-select-one-folder disabled-select-one-file">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-new-folder" /></svg></span>
<span class="btntex">+ Folder</span>
</a>
<a title="New file" onclick="tool_inpt_show('newfile')" class="item rbord disabled-select-big-selection disabled-select-one-folder disabled-select-one-file">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-plus" /></svg></span>
<span class="btntex">+ File</span>
</a>
<a title="Upload" onclick="tool_inpt_show('upload')" class="item disabled-select-big-selection disabled-select-one-folder disabled-select-one-file">
<span class="btnic"><svg class="svg-ic"><use xlink:href="#ic-upload" /></svg></span>
<span class="btntex">Upload</span>
</a>
</div>
</div>
<div class="main ns layout-block" oncontextmenu="select_browse_rightclick(false);return false">
<div class="browse-container" oncontextmenu="event.stopPropagation()">
<input type="hidden" name="count_folders" value="<?php echo $mu_session_data->countFolders+1;?>" />
<input type="hidden" name="count_files" value="<?php echo $mu_session_data->countFiles+1;?>" />
<div class="browse-item legend">
<table class="tab tab-row">
<tr>
<td class="sep-first">
<?php if(!empty($mu_session_data->getPath()))echo "<a class=\"upbtn\"  title=\"Browse upper folder\" href=\"".browsePage($mu_session_data->getPath())."\"><svg class=\"svg-ic\"><use xlink:href=\"#ic-up-arrow\" /></svg></a>";else echo "<span class=\"upbtn\"><svg class=\"svg-ic\"><use xlink:href=\"#ic-home\" /></svg></span>";?>
</td>
<td class="name"><span class="caption"><span onclick="sort_browse_elements_by('name')" style="cursor:pointer">Name</span>:</span></td>
<td class="sep-mid">&nbsp;</td>
<td class="data"><span class="caption"><span onclick="sort_browse_elements_by('size')" style="cursor:pointer">Size</span></a> / <span onclick="sort_browse_elements_by('perms')" style="cursor:pointer">Perms</span>:</span></td>
<td class="sep-last">&nbsp;</td>
</tr>
</table>
</div><div id="Sortable_Browse_Elements"><?php function printBrowsingItem($f,$type="file",$mu_session_data_path=""){global $mu_wd_relative_prefix;?>
<div class="browse-item browse-item-<?php echo $type;?>" oncontextmenu="select_browse_rightclick('<?php echo $type;?>-<?php echo $f->browseId;?>');return false">
<input class="browse-chk browse-chk-<?php echo $type;?>" onchange="select_browse_update()" type="checkbox" id="browse-chk-<?php echo $type;?>-<?php echo $f->browseId;?>" name="<?php echo $type;?>_<?php echo $f->browseId;?>" value="_<?php echo $f->name;?>" />
<input type="hidden" class="sortable_browse__isdir" value=<?php if($type=="folder")echo "1";else echo "0";?> />
<input type="hidden" class="sortable_browse__name" value="<?php echo $f->name;?>" />
<input type="hidden" class="sortable_browse__size" value=<?php echo $f->size;?> />
<input type="hidden" class="sortable_browse__perms" value="<?php echo $f->perms;?>" />
<label for="browse-chk-<?php echo $type;?>-<?php echo $f->browseId;?>">
<table class="tab tab-row" <?php if($type=="folder")echo "ondblclick=\"location.href='".browsePage($mu_session_data_path."/".$f->name)."'\"";?>>
<tr>
<td class="sep-first">&nbsp;</td>
<td class="name">
<?php if($type=="folder")echo "<a oncontextmenu=\"event.stopPropagation()\" class=\"link breakwords\" ondblclick=\"event.stopPropagation()\" title=\"Browse ".$f->name."\" href=\"".browsePage($mu_session_data_path."/".$f->name)."\">".$f->name."</a>";else echo "<span oncontextmenu=\"event.stopPropagation()\" class=\"breakwords\">".$f->name."</span>";?>
<a class="openlink" oncontextmenu="event.stopPropagation()" title="Open <?php echo $f->name;?>" href="<?php echo $mu_wd_relative_prefix;?>.<?php echo $mu_session_data_path."/".$f->name;?>" target="_blank">[OPEN]</a>
<?php if($type=="file")echo "<a oncontextmenu=\"event.stopPropagation()\" class=\"openlink edit\"  title=\"Edit ".$f->name."\" href=\"".editorPage($mu_session_data_path."/".$f->name)."\">[EDIT]</a>";?>
</td>
<td class="sep-mid">&nbsp;</td>
<td class="data">
<span class="data-cnt" oncontextmenu="event.stopPropagation()">
<span class="size"><?php echo $f->sizeWithOM();?>B</span>
<span class="perms">Perms:&nbsp;<?php echo $f->perms;?></span>
</span>
</td>
<td class="sep-last">&nbsp;</td>
</tr>
</table>
</label>
</div>
<?php }foreach($mu_session_data->folders as $f)printBrowsingItem($f,"folder",$mu_session_data->getFullPath());foreach($mu_session_data->files as $f)printBrowsingItem($f,"file",$mu_session_data->getFullPath());?></div>
</div>
</div>
<?php }?>
</form>
</div>
<div class="upload-zone">
<form id="form-upload" enctype="multipart/form-data">
<input type="hidden" name="upload_path" value="<?php echo $mu_session_data->getFullPath(false);?>" />
<div id="upload-inputs"></div>
</form>
</div>
<script type="text/javascript">upload_add();</script>
</body>
</html>
