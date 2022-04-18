<?php

// CLASSES

// File or folder
class MuonFile {
	public $name;
	public $perms;
	public $size;
	public $path;
	public $browseId;
    function __construct($name,$path,$browseId=false,$getSize=true){
		$this->name=$name;
		$this->size=0;
		$permissions=fileperms($path.$name);
		$permissions=sprintf("%o",$permissions);
		$this->perms=substr($permissions,-4); 
		$this->path=$path;
		if($getSize)
			$this->size=$this->getFileSize();
		$this->browseId=$browseId;
	}
	function getFileSize($sub=""){
		$path=$this->getFullPath(false).$sub;
		if(!is_dir($path))
			return filesize($path);
		$size=0;
		foreach(scandir($path) as $item)
			if($item!="." && $item!="..")
				$size+=$this->getFileSize($sub."/".$item);
		return $size;
	}
	function sizeWithOM(){ //4096 will be 4k
		$fileSize=$this->size;
		$byteOMs=array("","k","M","G","T");
		for($i=0;$fileSize>=1024 && $i<count($byteOMs)-1;$i++)
			$fileSize/=1024;
		$fileSize=round($fileSize,2)." ".$byteOMs[$i];
		return $fileSize;
	}
	function getPath($trimDot=true){ 
		$fp=$this->path;
		if($trimDot)
			$fp=ltrim($fp,".");
		return $fp;
	}
	function getFullPath($trimDot=true){ 
		$fp=$this->path.$this->name;
		if($trimDot)
			$fp=ltrim($fp,".");
		return $fp;
	}
	function explodePath(){
		$split=trim($this->getFullPath(),"/");
		if($split)
			return explode("/",$split);
		else
			return false;
	}
}

// File you're editing
class MuonEdit extends MuonFile {
	public $content;
    function __construct($name,$path){
		parent::__construct($name,$path);
		$this->content=file_get_contents($this->getFullPath(false));
	}
	function getFolderPath(){
		return rtrim($this->getPath(),"/");
	}
    function printContent(){
		echo htmlspecialchars($this->content);
	}
}

// Folder you're browsing
class MuonBrowse extends MuonFile {
	public $folders;
	public $countFolders;
	public $files;
	public $countFiles;
    function __construct($name=".",$path="") {
		parent::__construct($name,$path,false,false);
		$fullPath=$this->getFullPath(false);
		if(!file_exists($fullPath) || !is_dir($fullPath)){
			$this->name=false;
			return 0;
		}
		$fullPath=$fullPath."/";
		$this->folders=array(); 
		$this->countFolders=0;
		$this->files=array();
		$this->countFiles=0;
		$subItems=scandir($fullPath); 
		foreach($subItems as $item)
			if($item!="." && $item!=".."){
				if(is_dir($fullPath.$item)){
					$this->folders[$this->countFolders]=new MuonFile($item,$fullPath,$this->countFolders);
					$this->size+=$this->folders[$this->countFolders]->size;
					$this->countFolders++;
				}
				else{
					$this->files[$this->countFiles]=new MuonFile($item,$fullPath,$this->countFiles);
					$this->size+=$this->files[$this->countFiles]->size;
					$this->countFiles++;
				}
			}
	}
	function getFolderPath(){
		return $this->getFullPath();
	}
}

// Folders tree
class MuonTree {
	public $name;
	public $fullPath;
	public $browsingThis;
	public $browsingInsideThis;
	public $countFolders;
	public $folders;
	public $treeId;
	function __construct($browsing=false,$name=".",$path="",$treeId=1){
		$this->name=$name;
		$this->browsingThis=false;
		$this->browsingInsideThis=false;
		if(!empty($path))
			$path.="/";
		$this->fullPath=ltrim($path.$this->name,".");
		if($browsing==$this->fullPath)
			$this->browsingThis=true;
		if(substr($browsing,0,strlen($this->fullPath))===$this->fullPath)
			$this->browsingInsideThis=true;
		$this->folders=[]; 
		$this->countFolders=0;
		$folders=scandir(".".$this->fullPath);
		foreach($folders as $item)
			if($item!="." && $item!=".." && is_dir(".".$this->fullPath."/".$item)){
				$this->folders[$this->countFolders]=new MuonTree($browsing,$item,".".$this->fullPath,$treeId);
				$treeId=$this->folders[$this->countFolders]->treeId + 1;
				$this->countFolders++;
			}
		$this->treeId=$treeId;
	}
}

?>