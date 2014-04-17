<?php
/**
 * 管理员后台会员操作类
 */

defined('IN_PHPCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_app_func('util', 'content');

class mg_tpl extends admin {
	
	private $db, $verify_db;
	
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('mg_tpl_model');
		$this->_init_phpsso();
	}

	/**
	 * defalut
	 */
	function init() {
		$show_header = $show_scroll = true;
		pc_base::load_sys_class('form', '', 0);
		$this->verify_db = pc_base::load_model('member_verify_model');
		
		//搜索框
		$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
		$type = isset($_GET['type']) ? $_GET['type'] : '';
		$groupid = isset($_GET['groupid']) ? $_GET['groupid'] : '';
		$start_time = isset($_GET['start_time']) ? $_GET['start_time'] : date('Y-m-d', SYS_TIME-date('t', SYS_TIME)*86400);
		$end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);
		$grouplist = getcache('grouplist');
		foreach($grouplist as $k=>$v) {
			$grouplist[$k] = $v['name'];
		}

		$memberinfo['totalnum'] = $this->db->count();
		$memberinfo['vipnum'] = $this->db->count(array('vip'=>1));
		$memberinfo['verifynum'] = $this->verify_db->count(array('status'=>0));

		$todaytime = strtotime(date('Y-m-d', SYS_TIME));
		$memberinfo['today_member'] = $this->db->count("`regdate` > '$todaytime'");
		
		include $this->admin_tpl('mg_tpl_init');
	}
	
	function add(){
		if($_POST){
		$data = $_POST['data'];
		$result_id = $this->db->insert($data,true);
		if(!$result_id){
			showmessage(L('mass_failure'),HTTP_REFERER);
		}
		showmessage(L('operation_success'),HTTP_REFERER,'', 'add');
		}else{
			include $this->admin_tpl('mg_tpl_add');
		}
	}
	
	function edit(){
		include $this->admin_tpl('mg_tpl_edit');
	}
	
	function del(){
		
	}
	
	function mlist(){
		include $this->admin_tpl('mg_tpl_list');
	}
	
}
?>
