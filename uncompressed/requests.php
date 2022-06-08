<?php

// REQUESTS

$request=param("req");

// Unallowed request
if(!loggedIn())
	respDie("err_login");
if(mu_guest_session)
	respDie("err_guest");


chdir($mu_wd_browse);

if($request == "gettree"){
	/*     RENAME PROCEDURE
	 rename_err_count           
	 rename_err_missing    
	 rename_err_invalid_name    
	 rename_err_already_existing
	 rename_err                 
	 rename_ok                  */
	$tbrws = param("tbrws");
	$tname = param("tname");
	$tpath = param("tpath");
	$tid = param("tid");
	$recur = param("recur");
	chdir($mu_wd_browse);
	echo json_encode((object)["tree"=>new MuonTree($tbrws, $tname, $tpath, $tid, true),"treeId"=>$tid, "askRecur"=>$recur]);
	chdir($mu_wd_script);
	respDie("");
}


if($request == "rename"){
	/*     RENAME PROCEDURE
	 rename_err_count           
	 rename_err_missing    
	 rename_err_invalid_name    
	 rename_err_already_existing
	 rename_err                 
	 rename_ok                  */
	$base_path = param("browsing_path");
	$file = getSelected();
	if(count($file)!=1)
		respDie("rename_err_count");
	$file = $file[0];
	$old_path = $base_path."/".$file;
	if(!file_exists($old_path))
		respDie("rename_err_missing");
	$new_name = trim(param("ren_new_name"));
	if(!$new_name || !validFilename($new_name))
		respDie("rename_err_invalid_name");
	$new_path = $base_path."/".$new_name;
	if(file_exists($new_path) && $old_path!=$new_path)
		respDie("rename_err_already_existing");
	if(!rename($old_path,$new_path) && $old_path!=$new_path)
		respDie("rename_err"); 
	respDie("rename_ok");
}

if($request == "unzip"){
	/*    UNZIP PROCEDURE
	 unzip_err_extension
	 unzip_err_count           
	 unzip_err_missing   
	 unzip_err_opening
	 unzip_err_extracting                 
	 unzip_ok            */
	if(!zipEnabled())
		respDie("unzip_err_extension");
	$base_path = param("browsing_path");
	$file = getSelected();
	if(count($file)!=1)
		respDie("unzip_err_count");
	$file = $file[0];
	$full_path = $base_path."/".$file;
	if(!file_exists($full_path) || is_dir($full_path))
		respDie("unzip_err_missing");
	$dest_path=$base_path."/unzip_".$file;
	if(file_exists($dest_path)){
		$i=1;
		while(file_exists($dest_path."_".$i))
			$i++;
		$dest_path=$dest_path."_".$i;
	}
	$dest_path=$dest_path."/";
	$zip_file=new ZipArchive;
	if(!($zip_file->open($full_path)===TRUE))
		respDie("unzip_err_opening");
	if(!($zip_file->extractTo($dest_path)))
		respDie("unzip_err_extracting");
	$zip_file->close();
	respDie("unzip_ok");
}

if($request == "delete"){
	/*     DELETE PROCEDURE
	 delete_err                         
	 delete_ok             */
	$files=getSelected("any",param("browsing_path")."/");
	$errors=false;
	foreach($files as $item)
		if(!deletePath($item))
			$errors=true;
	if($errors)
		respDie("delete_err");
	respDie("delete_ok");
}

if(substr($request,0,7)==="paste__"){
	/*    COPY/CUT PROCEDURE
	 paste_err_destination
	 paste_err_location
	 paste_err_meaningless
	 paste_err_cut_dest_inside_source
	 paste_err_copy
	 paste_err_cut
	 paste_ok_copy
	 paste_ok_cut                    */
	$pasteto=substr($request,7)."/";
	if(!file_exists($pasteto) || !is_dir($pasteto))
		respDie("paste_err_destination");
	$path=param("browsing_path")."/";
	if(!file_exists($path) || !is_dir($path))
		respDie("paste_err_location");
	$cut=false;
	if(param("paste_meaning") == "cut")
		$cut=true;
	else if(param("paste_meaning") != "copy")
		respDie("paste_err_meaningless");
	if($path==$pasteto && $cut)
		respDie("paste_ok_cut"); //SUCCESS (nothing requested)
	$errors=false;
	$folders=getSelected("folder");
	for($i=0;$i<count($folders);$i++)
		if((substr($pasteto,0,strlen($path.$folders[$i]))===$path.$folders[$i]) && file_exists($path.$folders[$i]) && is_dir($path.$folders[$i])){
			if($cut)
				respDie("paste_err_cut_dest_inside_source"); //folder not empty (cut)
			else if(!copyPath($path.$folders[$i],getPasteDest($pasteto,$folders[$i])))
				$errors=true;
		}
		else if(!$cut){
			if(!copyPath($path.$folders[$i],getPasteDest($pasteto,$folders[$i])))
				$errors=true;
		}
		else if(!rename($path.$folders[$i],getPasteDest($pasteto,$folders[$i])))
			$errors=true;
	$files=getSelected("file");
	if(!$cut){
		foreach($files as $file)
			if(!copyPath($path.$file,getPasteDest($pasteto,$file),true))
				$errors=true;
		if($errors)
			respDie("paste_err_copy"); 
		respDie("paste_ok_copy");
	}
	else{
		foreach($files as $file)
			if(!rename($path.$file,getPasteDest($pasteto,$file)))
				$errors=true;
		if($errors)
			respDie("paste_err_cut"); 
		respDie("paste_ok_cut");
	}
	respDie(null);
}

if($request == "perms"){
	/*    PERMS PROCEDURE
	 perms_err_input
	 perms_err                         
	 perms_ok            */
	$files=getSelected("any",param("browsing_path")."/");
	$chmod=param("perm_new_perms");
	if(!$chmod)
		respDie("perms_err_input");
	$chmod=octdec(intval($chmod));
	$errors=false;
	foreach($files as $item)
		if(!chmod($item,$chmod))
			$errors=true;
	if($errors)
		respDie("perms_err"); 
	respDie("perms_ok"); 
}

if($request == "zip"){
	/*  ZIP PROCEDURE 
	 zip_err_extension
	 zip_err_location
	 zip_err_opening
	 zip_err
	 zip_ok           */
	if(!zipEnabled())
		respDie("zip_err_extension");
	$base_path=param("browsing_path");
	if(!file_exists($base_path) || !is_dir($base_path))
		respDie("zip_err_location"); //path not found
	$dest_path=$base_path."/zip_".date("Ymd_Hi");
	if(file_exists($dest_path.".zip")){
		$i=1;
		while(file_exists($dest_path."_".$i.".zip"))
			$i++;
		$dest_path=$dest_path."_".$i;
	}
	$dest_path=$dest_path.".zip";
	$files=getSelected($type="file");
	$folders=getSelected($type="folder");
	$zip=new ZipArchive();
	if($zip->open($dest_path,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true)
		respDie("zip_err_opening");
	if($files)
		foreach($files as $file)
			$zip->addFile($base_path."/".$file,$file);
	if($folders)
		foreach($folders as $folder){
			$root_path = realpath($base_path."/".$folder);
			$subs=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root_path),RecursiveIteratorIterator::LEAVES_ONLY);
			foreach ($subs as $name => $sub)
				if(!$sub->isDir()){
					$subPath=$sub->getRealPath();
					$relPath=$folder."/".substr($subPath, strlen($root_path) + 1);
					$zip->addFile($subPath,$relPath);
				}
		}
	$zip->close();
	if(!file_exists($dest_path))
		respDie("zip_err");
	respDie("zip_ok");
}

if($request == "newfolder"){
	/*        NEW FOLDER PROCEDURE
	 newfolder_err_location
	 newfolder_err_invalid_name
	 newfolder_err_already_existing
	 newfolder_err
	 newfolder_ok                  */
	$base_path=param("browsing_path");
	if(!file_exists($base_path) || !is_dir($base_path))
		respDie("newfolder_err_location"); 
	$new_name=trim(param("new_folder_name"));
	if(!validFilename($new_name))
		respDie("newfolder_err_invalid_name"); 
	$full_path=$base_path."/".$new_name;
	if(file_exists($full_path))
		respDie("newfolder_err_already_existing"); 
	if(!mkdir($full_path,0755))
		respDie("newfolder_err"); 
	respDie("newfolder_ok"); 
}

if($request == "newfile"){
	/*      NEW FILE PROCEDURE
	 newfile_err_location
	 newfile_err_invalid_name
	 newfile_err_already_existing
	 newfile_err
	 newfile_ok                  */
	$base_path=param("browsing_path");
	if(!file_exists($base_path) || !is_dir($base_path))
		respDie("newfile_err_location"); 
	$new_name=trim(param("new_file_name"));
	if(!validFilename($new_name))
		respDie("newfile_err_invalid_name"); 
	$full_path=$base_path."/".$new_name;
	if(file_exists($full_path))
		respDie("newfile_err_already_existing");
	$file=fopen($full_path,"w") or respDie("newfile_err");
	fwrite($file,date("F j, Y, g:i a"));
	fclose($file);
	chmod($path,0755);
	respDie("newfile_ok"); 
}

if($request == "upload"){
	/*     UPLOAD PROCEDURE
	 upload_err_location
	 upload_err
	 upload_ok                  */
	$path=param("upload_path")."/";
	if(!file_exists($path) || !is_dir($path))
		respDie("upload_err_location");
	$uploads=0;
	$errors=0;
	foreach($_FILES as $file)
		if($file["error"]==UPLOAD_ERR_OK and is_uploaded_file($file["tmp_name"])){
			$fpath=getPasteDest($path,$file["name"]);
			move_uploaded_file($file["tmp_name"],$fpath);
			chmod($fpath,0775);
			$uploads++;
		}
		else
			$errors++;
	if(!$uploads || $errors>1)
		respDie("upload_err");
	respDie("upload_ok"); 
}

if($request == "editsave"){
	/*       SAVE FILE PROCEDURE
	 editsave_err_location
	 editsave_err
	 editsave_ok                */
	$path=param("editing_path");
	if(file_exists($path) && is_dir($path))
		die("editsave_err_location"); 
	if(!file_put_contents($path,param("editor_content"))){
		if(!file_exists($path))
			die("editsave_err_location"); 
		die("editsave_err"); 
	}
	die("editsave_ok"); 
}

respDie("err"); //Unknown error fallback

?>