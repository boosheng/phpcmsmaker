<?php
/*
 * modules auto maker tools
 * this in sample modules for quickly create new modules
 */

$dbconfig = include '../../../caches/configs/database.php';
$db = mysql_connect($dbconfig['default']['hostname'],$dbconfig['default']['username'],$dbconfig['default']['password']);
$link = mysql_select_db($dbconfig['default']['database']);
$config['module']['name']="test";
$config['module']['ename']="test";
$config['module']['dbtag']="ats";
//admin menuitems
$configs = array("tablename1"=>array("showcolumn"=>array("a","b"),"op"=>array("add","edit","del","changestat")),
				"tablename2"=>array("showcolumn"=>array("a","b"),"op"=>array("add","edit","del","changestat")));


$fmenu = array(array('name'=>"",'action'=>"",'file'=>''));




//front menuitems

//$tpl = $argv[1];
//$tables = explode(",",$argv[2]);

//模型相关的表
$tables = array("ats_action");
$res = mysql_query("show tables like '%".$config['module']['dbtag']."%'");
while($row = mysql_fetch_array($res)){
//foreach($tables as $key=>$val){
	//后台功能
	$tablename = str_replace($dbconfig['default']['tablepre'], "", $row[0]);
	make($tablename);
	//前台功能
	make_front($tablename,$arr);
	//菜单
	//make_menu($arr);
	//模块安装文件
	make_module();
	echo "The module is created!\n";
}

mysql_close($db);
/*
 * step 1,create model
 * step 2,create action
 * step 3,create view
 */
function make($table){
	global $db,$config,$dbconfig;
	$mpath = "";
	//var_dump($config);exit;
	//生成相应的模块目录
	if(!is_dir("../".$config['module']['ename'])){
		mkdir("../".$config['module']['ename']);
		mkdir("../".$config['module']['ename']."/install");
		mkdir("../".$config['module']['ename']."/templates");
		mkdir("../".$config['module']['ename']."/uninstall");
		mkdir("../../templates/default/".$config['module']['ename']);
	}
	
	$str = str_replace("tpl", "{$table}", file_get_contents("./tpl_model.class.php"));
	file_put_contents("../../model/".$table."_model.class.php", $str);
	
	$str = str_replace("tpl_list", "{$table}_list", file_get_contents("tpl_action.php"));
	$str = str_replace("tpl_add", "{$table}_add", $str);
	$str = str_replace("tpl_edit", "{$table}_edit", $str);
	$str = str_replace("tpl_init", "{$table}_init", $str);
	
	file_put_contents("../".$config['module']['ename']."/".$table.".php", $str);
	//tpl tables column show
	//SHOW COLUMNS FROM card
	$res = mysql_query("SHOW COLUMNS FROM ".$dbconfig['default']['tablepre'].$table);
	$tb=array();
	$i=0;
	while($row = mysql_fetch_array($res)){
		$tb[$i]['name']=$row['Field'];
		$tb[$i]['Type']=$row['Type'];//根据type选择需要的控件
		$liststr .= "<th align='left'>".$row['Field']."</th>";
		$liststr2 .= "<th align='left'><?php echo ".$row['Field'].";?></th>";
		$addstr .= '<tr>
			<td width="80"><?php echo L(\'username\')?></td> 
			<td><input type="text" name="info[username]"  class="input-text" id="username"></input></td>
		</tr>';
		$editor .= '<tr>
			<td width="80"><?php echo L(\'username\')?></td> 
			<td><?php echo $memberinfo[\'username\']?><?php if($memberinfo[\'islock\']) {?><img title="<?php echo L(\'lock\')?>" src="<?php echo IMG_PATH?>icon/icon_padlock.gif"><?php }?><?php if($memberinfo[\'vip\']) {?><img title="<?php echo L(\'lock\')?>" src="<?php echo IMG_PATH?>icon/vip.gif"><?php }?></td>
		</tr>';
		$i++;
	}
	//var_dump($tb);
	$strp = '<tr>
	<th  align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall(\'id[]\');"></th>
	<th align="left"></th>
	'.$liststr.'
				<th align="left"><?php echo L(\'operation\')?></th>
			</tr>';
	$str = str_replace("{{topstr}}", $liststr, file_get_contents("./templates/tpl_list.tpl.php"));
	$str = str_replace("{{liststr}}", $liststr2, $str);
	file_put_contents("../".$config['module']['ename']."/templates/".$table."_list.tpl.php", $str);
	$str = str_replace("", "", file_get_contents("./templates/tpl_add.tpl.php"));
	file_put_contents("../".$config['module']['ename']."/templates/".$table."_add.tpl.php", $str);
	$str = str_replace("", "", file_get_contents("./templates/tpl_edit.tpl.php"));
	file_put_contents("../".$config['module']['ename']."/templates/".$table."_edit.tpl.php", $str);
	$str = str_replace("{{topstr}}", $liststr, file_get_contents("./templates/tpl_init.tpl.php"));
	file_put_contents("../".$config['module']['ename']."/templates/".$table."_init.tpl.php", $str);
	echo "{$table} is over!\n";
	
}


//添加后台菜单
$arr = array();
function make_menu($arr){
	global $db;
	foreach($arr as $val){
		$sql = "";
	}
}

//生成模块安装程序
function make_module(){
	global $db,$config;
	$str = file_get_contents("./install/config.inc.php");
	$str = str_replace("{{module}}", $config['module']['ename'], $str);
	$str = str_replace("{{modulename}}", $config['module']['name'], $str);
	$str = str_replace("{{introduce}}", $config['module']['name'], $str);
	file_put_contents("../".$config['module']['ename']."/install/config.inc.php",$str);
}

//生成前台功能
function make_front($table,$arr){
	global $db,$config,$dbconfig,$fmenu;
	$mpath = "";
	$res = mysql_query("SHOW COLUMNS FROM ".$dbconfig['default']['tablepre'].$table);
	$tb=array();
	$i=0;
	while($row = mysql_fetch_array($res)){
		$tb[$i]['name']=$row['Field'];
		$tb[$i]['Type']=$row['Type'];//根据type选择需要的控件
		$liststr .= "<th align='left'>".$row['Field']."</th>";
		$liststr2 .= "<th align='left'><?php echo ".$row['Field'].";?></th>";
		if($row['Type']!="text"){
			$addstr .='<tr>
			<th>'.$row['Field'].'：</th>
			<td><input name="info['.$row['Field'].']" type="text" id="'.$row['Field'].'" size="30" value=""  class="input-text"/> </td>
			</tr>';
		}else{
			$addstr .='<tr>
			<th>'.$row['Field'].'：</th>
			<td><textarea name="info['.$row['Field'].']" type="text" id="'.$row['Field'].'"  rows="5" cols="50"></textarea> </td>
			</tr>';
		}
		
		if($row['Type']!="text"){
			$editstr .='<tr>
			<th>'.$row['Field'].'：</th>
			<td><input name="info['.$row['Field'].']" type="text" id="'.$row['Field'].'" size="30" value="<?php echo $row["'.$row['Field'].'"]?>"  class="input-text"/> </td>
			</tr>';
		}else{
			$editstr .='<tr>
			<th>'.$row['Field'].'：</th>
			<td><textarea name="info['.$row['Field'].']" type="text" id="'.$row['Field'].'"  rows="5" cols="50"><?php echo $row["'.$row['Field'].'"]?></textarea> </td>
			</tr>';
		}
	}
	
	
	//生成相应模板
	$str = file_get_contents("./left.html");
	foreach($fmenu as $val){
		$replace .= '<li {if ROUTE_A=="init"} class="on"{/if}><a href="'.$val['file'].'.php?m='.$config['module']['ename'].'&c='.$val['file'].'&a='.$val['action'].'"><img src="{IMG_PATH}icon/m_2.png" width="14" height="15" /> '.$val['name'].'</a></li>';
	}
	$str = str_replace("{{strmenu}}", $replace, $str);
	file_put_contents("../../templates/default/".$config['module']['ename']."/left.html", $str);
	$str = file_get_contents("./add.html");
	$str = str_replace("{{addstr}}", $addstr, $str);
	file_put_contents("../../templates/default/".$config['module']['ename']."/".$table."_add.html", $str);
	$str = file_get_contents("./info.html");
	$str = str_replace("{{infostr}}", $editstr, $str);
	file_put_contents("../../templates/default/".$config['module']['ename']."/".$table."_info.html", $str);
	$str = file_get_contents("./edit.html");
	$str = str_replace("{{editstr}}", $editstr, $str);
	file_put_contents("../../templates/default/".$config['module']['ename']."/".$table."_edit.html", $str);
	$str = file_get_contents("./init.html");
	//{{topliststr}} {{liststr}}
	$str = str_replace("{{topliststr}}", $liststr, $str);
	$str = str_replace("{{liststr}}", $liststr2, $str);
	file_put_contents("../../templates/default/".$config['module']['ename']."/".$table."_init.html", $str);
	$str = file_get_contents("./list.html");
	$str = str_replace("{{topliststr}}", $liststr, $str);
	$str = str_replace("{{liststr}}", $liststr2, $str);
	file_put_contents("../../templates/default/".$config['module']['ename']."/".$table."_list.html", $str);
	
	//功能函数的生成
	//替换index.php程序,生成功能
	$str = str_replace("index_db", "index_{$table}", file_get_contents("./index.php"));
	//$str = str_replace("//{{function}}", $funstr, $str);
	file_put_contents("../".$config['module']['ename']."/index_".$table.".php", $str);
}


//生成app api
function make_api(){
	
}