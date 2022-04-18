<?php /* Begin of Muon front-end code */ ?>
<!DOCTYPE html>
<html>
	
	<head>
		
		<meta charset="UTF-8" />
		<meta name="robots" content="noindex" />
		
		<!-- Title -->
		<title><?php echo $mu_session_title;?></title>
		
		<!-- Style -->
		<link rel="apple-touch-icon" href="<?php require "iconapple_base64"; ?>" />
		<link rel="icon" href="<?php require "iconfav_base64"; ?>" />
		<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0" />
		<style type="text/css"><?php require "style.css"; ?></style>
		
		<!-- JS local -->
		<script type="text/javascript">
			var mu_session_status="<?php echo $mu_session_status;?>";
			var mu_session_title="<?php echo $mu_session_title;?>";
			var mu_requests_to="<?php echo mainPage();?>?feedback=plain";
		</script>
		
		<!-- JS -->
		<?php 
		if(!muonInterface("login")&&!muonInterface("license")){ ?>
		<script type="text/javascript"><?php require "script.js"; ?></script>
		<?php 
		} ?> 
		
	</head>
	
	<body>
		
		<!-- Include svg icons -->
		<?php require "icons__svg"; ?>
		
		<div id="layout" class="tree-browse browse-select-nothing uploading-nothing editor-not-changed editor-text-nowrap resp-none">

			<!-- Header and logout -->
			<div class="muonbar layout-block ns <?php if(muonInterface("login")){echo "muonbar-login";}else if(muonInterface("license")){echo "muonbar-license";}?>">
				<form method="post" action="<?php echo mainPage();?>">
					<input type="hidden" name="logout" value="logout" />
					<input type="submit" style="display:none" value="Logout" id="logout-submit" />
					<table class="tab-row">
						<tr>
							<td class="logo"><a href="<?php echo mu_website;?>" target="_blank"><svg class="svg-ic"><use xlink:href="#ic-logo" /></svg></a></td>
							<td class="title"><?php echo mu_title_html; ?> <a title="License" rel="license" target="_blank" href="<?php echo licensePage().getPrevQuery("_srv","&");?>" class="learnmore">Version <?php echo mu_version; ?></a></td>
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
			
			<!-- License block (die) -->
			<?php 
			if(muonInterface("license")){ ?>
			<div class="layout-block login license ns">
				<div class="license-block">
					[ <a class="ahr" onclick="window.close()">Close</a> ]  <b>Muon version <?php echo mu_version; ?> &mdash; 
					<a class="ahr" href=" <?php echo mu_website; ?>">Website</a></b>
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
			<?php 
				die('</body></html>');
			} ?>
			
			<!-- Login block (die) -->
			<?php
			if(muonInterface("login")){ ?>
			<div class="layout-block login">
				<?php 
				if($mu_session_data){ ?>
				<div class="header-block err"><b>Wrong username/password.</b><br/>Please, try again.</div>
				<?php
				}
				else{ ?>
				<div class="header-block">
					Login to manage your web space with Muon <?php echo mu_version;?> &mdash; 
					<a class="ahr" href="<?php echo mu_website; ?>" target="_blank" title="Learn more about Muon">Mu-what?</a>
					<a title="License" class="ahr" rel="license" target="_blank" href="<?php echo licensePage().getPrevQuery("_srv","&");?>" class="learnmore">License</a>
				</div>
				<?php
				} ?>
				<div class="login-block">
					<form method="post" action="<?php echo mainPage().getPrevQuery();?>">
						<input type="text" name="login" placeholder="Username" />
						<input type="password" name="pass" placeholder="Password" />
						<input type="submit" value="Login" />
					</form>
				</div>
			</div>
			<?php 
				die('</body></html>');
			} ?>
			
			
			<!-- Blocks -->
			<form id="form-main">
				
				<!-- Current path must be passed via request -->
				<input type="hidden" name="<?php if(muonInterface("edit"))echo "editing_path";else echo "browsing_path";?>" value="<?php echo $mu_session_data->getFullPath(false);?>" />
				
				<!-- Navigation tree -->
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
							<?php 
							function printTree($tree,$home=true){
								$path="/".ltrim($tree->fullPath,"/");
								$extra_class="";
								if($tree->browsingThis)
									$extra_class.=" tree-browsing";
								if($tree->browsingInsideThis)
									$extra_class.=" tree-open";
								if(!$home)
									$extra_class.=" tree-li-ul";
								echo "<li id=\"tree-li-".$tree->treeId."\" class=\"tree-li".$extra_class."\">";
								$name=$tree->name;
								if($home){
									$name="HOME";
									echo "<div class=\"tree-btn tree-btn-noclick\"><svg class=\"svg-ic\"><use xlink:href=\"#ic-home\" /></svg></div> ";
								}
								else if($tree->countFolders){ ?>
									<a class="tree-btn tree-btn-open" onclick="tree_open('<?php echo $tree->treeId;?>','open')"><svg class="svg-ic"><use xlink:href="#ic-folder-full" /></svg></a>
									<a class="tree-btn tree-btn-close" onclick="tree_open('<?php echo $tree->treeId;?>','close')"><svg class="svg-ic"><use xlink:href="#ic-folder-empty" /></svg></a>
							<?php 
								}
								else{ 
									echo "<span class=\"tree-btn tree-btn-noclick\"><svg class=\"svg-ic\"><use xlink:href=\"#ic-folder-empty\" /></svg></span> ";
								} ?>
									<a class="tree-name brw" title="Browse <?php echo $name;?>" href="<?php echo browsePage($path);?>"><?php echo $name;?></a>
									<a class="tree-name cpy" title="Copy to <?php echo $name;?>" onclick="paste_tool('.<?php echo $tree->fullPath;?>')"><span class="tree-btn paste"><svg class="svg-ic"><use xlink:href="#ic-paste" /></svg></span> <?php echo $name;?></a>
									<a class="tree-name cut" title="Move to <?php echo $name;?>" onclick="paste_tool('.<?php echo $tree->fullPath;?>')"><span class="tree-btn paste"><svg class="svg-ic"><use xlink:href="#ic-paste" /></svg></span> <?php echo $name;?></a>
							<?php 
								echo "</li>";
								if($tree->countFolders){
									echo "<ul id=\"tree-ul-".$tree->treeId."\" class=\"tree-ul".$extra_class."\">";
									foreach($tree->folders as $fold)
										printTree($fold,false);
									echo "</ul>";
								}
							}

							chdir($mu_wd_browse);
							printTree(new MuonTree($mu_session_data->getFolderPath()));
							chdir($mu_wd_script); ?>
						</div>
					</div>
				</div>
				
				<!-- Top bar with current path -->
				<div class="browsebar ns layout-block">
					<div class="browsebar-blocks">
						<span class="browsebar-block sep">&nbsp;</span>
						<?php 
						$currentPath=$mu_session_data->explodePath();
						if($currentPath){ ?>
							<span class="browsebar-block folder"><a class="nav" title="Browse /" href="<?php echo browsePage();?>">HOME</a></span>
							<span class="browsebar-block sep">/</span>
							<?php 
							$localPath='';
							for($i=0;$i<count($currentPath)-1;$i++){
								$localPath=$localPath.'/'.$currentPath[$i];?>
								<span class="browsebar-block folder"><a class="nav" title="Browse <?php echo $currentPath[$i];?>" href="<?php echo browsePage($localPath);?>"><?php echo $currentPath[$i];?></a></span>
								<span class="browsebar-block sep">/</span>
							<?php 
							} ?>
							<span class="browsebar-block folder"><?php echo $mu_session_data->name;?></span>
						<?php 
						}
						else {
							echo "<span class=\"browsebar-block folder\">HOME</span>";
						} ?>
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
				
				<!-- Bottom bar with shortcuts (no mobile) -->
				<div class="bottombar ns layout-block">
					<span class="text">
						<?php 
						if(mu_guest_session){
							echo "[ <b>GUEST SESSION</b> ] ";
						} ?>
						<b>Desktop shortcuts</b> &mdash;
						<?php 
						if(muonInterface("edit")) 
							echo "Ctrl+S: save file / Ctrl+R: reload page ";
						else 
							echo "Ctrl+A: select all / Ctrl+C: copy files / Ctrl+X: move files / Ctrl+R: reload page / Right click on a file to select it only"; ?>
					</span>
				</div>
				
				<!-- Bar with output -->
				<div class="outputbar ns layout-block"><span class="text" id="response-output">&nbsp;</span></div>
				
				<?php 
				if(muonInterface("edit")){ ?>
				
				<!-- Editor tools -->
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

				<!-- Editor main -->
				<div class="main layout-block" id="main-editor">
					<div class="editor-container">
						<textarea class="editor editor-lineheight" id="text-editor" name="editor_content" spellcheck="false" onkeyup="editor_adjust(false,event)" onpaste="editor_adjust()" oncut="editor_adjust()"><?php $mu_session_data->printContent();?></textarea>
						<div class="editor-numbers editor-lineheight ns" id="text-editor-numbers">&nbsp;</div>
						<div class="editor-adj hidden ns" id="text-editor-sizeadj">&nbsp;</div>
					</div>
				</div>
				<script type="text/javascript">editor_adjust(true);</script>
				
				<?php 
				}
				
				else { /*browse*/ ?>
				
				<!-- Browse tools -->
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
				
				<!-- Browse main -->
				<div class="main ns layout-block" oncontextmenu="select_browse_rightclick(false);return false">
					<div class="browse-container" oncontextmenu="event.stopPropagation()">
						<input type="hidden" name="count_folders" value="<?php echo $mu_session_data->countFolders+1;?>" />
						<input type="hidden" name="count_files" value="<?php echo $mu_session_data->countFiles+1;?>" />
						<div class="browse-item legend">
							<table class="tab tab-row">
								<tr>
									<td class="sep-first">
									<?php 
									if(!empty($mu_session_data->getPath()))
										echo "<a class=\"upbtn\"  title=\"Browse upper folder\" href=\"".browsePage($mu_session_data->getPath())."\"><svg class=\"svg-ic\"><use xlink:href=\"#ic-up-arrow\" /></svg></a>";else echo "<span class=\"upbtn\"><svg class=\"svg-ic\"><use xlink:href=\"#ic-home\" /></svg></span>"; ?>
									</td>
									<td class="name"><span class="caption"><span onclick="sort_browse_elements_by('name')" style="cursor:pointer">Name</span>:</span></td>
									<td class="sep-mid">&nbsp;</td>
									<td class="data"><span class="caption"><span onclick="sort_browse_elements_by('size')" style="cursor:pointer">Size</span></a> / <span onclick="sort_browse_elements_by('perms')" style="cursor:pointer">Perms</span>:</span></td>
									<td class="sep-last">&nbsp;</td>
								</tr>
							</table>
						</div><div id="Sortable_Browse_Elements"><?php 
						function printBrowsingItem($f,$type="file",$mu_session_data_path=""){ global $mu_wd_relative_prefix; ?>
						<div class="browse-item browse-item-<?php echo $type;?>" oncontextmenu="select_browse_rightclick('<?php echo $type;?>-<?php echo $f->browseId;?>');return false">
							<input class="browse-chk browse-chk-<?php echo $type;?>" onchange="select_browse_update()" type="checkbox" id="browse-chk-<?php echo $type;?>-<?php echo $f->browseId;?>" name="<?php echo $type;?>_<?php echo $f->browseId;?>" value="_<?php echo $f->name;?>" />
							<input type="hidden" class="sortable_browse__isdir" value=<?php if($type=="folder") echo "1"; else echo "0"; ?> />
							<input type="hidden" class="sortable_browse__name" value="<?php echo $f->name; ?>" />
							<input type="hidden" class="sortable_browse__size" value=<?php echo $f->size; ?> />
							<input type="hidden" class="sortable_browse__perms" value="<?php echo $f->perms;?>" />
							<label for="browse-chk-<?php echo $type;?>-<?php echo $f->browseId;?>">
								<table class="tab tab-row" <?php if($type=="folder")echo "ondblclick=\"location.href='".browsePage($mu_session_data_path."/".$f->name)."'\"";?>>
									<tr>
										<td class="sep-first">&nbsp;</td>
										<td class="name">
											<?php
											if($type=="folder")
												echo "<a oncontextmenu=\"event.stopPropagation()\" class=\"link breakwords\" ondblclick=\"event.stopPropagation()\" title=\"Browse ".$f->name."\" href=\"".browsePage($mu_session_data_path."/".$f->name)."\">".$f->name."</a>";
											else
												echo "<span oncontextmenu=\"event.stopPropagation()\" class=\"breakwords\">".$f->name."</span>"; ?>
											<a class="openlink" oncontextmenu="event.stopPropagation()" title="Open <?php echo $f->name;?>" href="<?php echo $mu_wd_relative_prefix;?>.<?php echo $mu_session_data_path."/".$f->name;?>" target="_blank">[OPEN]</a>
											<?php
											if($type=="file")
												echo "<a oncontextmenu=\"event.stopPropagation()\" class=\"openlink edit\"  title=\"Edit ".$f->name."\" href=\"".editorPage($mu_session_data_path."/".$f->name)."\">[EDIT]</a>"; ?>
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
						<?php 
						}

						foreach($mu_session_data->folders as $f)
							printBrowsingItem($f,"folder",$mu_session_data->getFullPath());

						foreach($mu_session_data->files as $f)
							printBrowsingItem($f,"file",$mu_session_data->getFullPath()); ?></div>
					</div>
				</div>

				<?php
				} ?>
				
			</form>
			

		</div>
		
		<!-- Upload files (browse) -->
		<div class="upload-zone">
			<form id="form-upload" enctype="multipart/form-data">
				<input type="hidden" name="upload_path" value="<?php echo $mu_session_data->getFullPath(false);?>" />
				<div id="upload-inputs"></div>
			</form>
		</div>
		<script type="text/javascript">upload_add();</script>
		
	</body>
</html>
<?php /* End of Muon front-end code */ ?>