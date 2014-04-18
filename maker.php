<?php
/*
 * modules auto maker tools
 * this in sample modules for quickly create new modules
 */

$dbconfig = include '../../../caches/configs/database.php';
$db = mysql_connect($dbconfig['default']['hostname'],$dbconfig['default']['root'],$dbconfig['default']['password']);
$link = mysql_select_db($dbconfig['default']['database']);
$config['module']['name']="test";
$config['module']['ename']="test";
//admin menuitems
$config = array("tablename1"=>array("showcolumn"=>array("a","b"),"op"=>array("add","edit","del","changestat")),
				"tablename2"=>array("showcolumn"=>array("a","b"),"op"=>array("add","edit","del","changestat")));







//front menuitems














//$tpl = $argv[1];
//$tables = explode(",",$argv[2]);

//tables configs
$tables = array("mg_action",
"mg_tpl");
foreach($tables as $key=>$val){
	make($val);
	//make_menu($arr);
}

mysql_close($db);
/*
 * step 1,create model
 * step 2,create action
 * step 3,create view
 */
function make($table){
	global $db;
	$mpath = "";
	$str = str_replace("tpl", "{$table}", file_get_contents("./tpl_model.class.php"));
	file_put_contents("../../model/".$table."_model.class.php", $str);
	
	$str = str_replace("tpl_list", "{$table}_list", file_get_contents("tpl_action.php"));
	$str = str_replace("tpl_add", "{$table}_add", $str);
	$str = str_replace("tpl_edit", "{$table}_edit", $str);
	$str = str_replace("tpl_init", "{$table}_init", $str);
	
	file_put_contents($table.".php", $str);
	//tpl tables column show
	//SHOW COLUMNS FROM card
	$res = mysql_query("SHOW COLUMNS FROM v9_{$table}");
	$tb=array();
	$i=0;
	while($row = mysql_fetch_array($res)){
		$tb[$i]['name']=$row['Field'];
		$tb[$i]['Type']=$row['Type'];
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
	file_put_contents("./templates/".$table."_list.tpl.php", $str);
	$str = str_replace("", "", file_get_contents("./templates/tpl_add.tpl.php"));
	file_put_contents("./templates/".$table."_add.tpl.php", $str);
	$str = str_replace("", "", file_get_contents("./templates/tpl_edit.tpl.php"));
	file_put_contents("./templates/".$table."_edit.tpl.php", $str);
	$str = str_replace("{{topstr}}", $liststr, file_get_contents("./templates/tpl_init.tpl.php"));
	file_put_contents("./templates/".$table."_init.tpl.php", $str);
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

//生成前台功能
function make_front($arr){
	global $db;
	$mpath = "";
	//替换index.php程序,生成功能
	$str = str_replace("tpl", "{$table}", file_get_contents("./index.php"));
	//生成相应模板
	file_put_contents("../../templates/".$config['module']['ename']."/".$table."_add.html", $str);
	file_put_contents("../../templates/".$config['module']['ename']."/".$table."_edit.html", $str);
	file_put_contents("../../templates/".$config['module']['ename']."/".$table."_init.html", $str);
	file_put_contents("../../templates/".$config['module']['ename']."/".$table."_list.html", $str);
	$str = str_replace("tpl_list", "{$table}_list", file_get_contents("tpl_action.php"));
	$str = str_replace("tpl_add", "{$table}_add", $str);
	$str = str_replace("tpl_edit", "{$table}_edit", $str);
	$str = str_replace("tpl_init", "{$table}_init", $str);
}