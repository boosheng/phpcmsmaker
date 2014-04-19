<?php
/**
 * 会员前台管理中心、账号管理、收藏操作类
 */

defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('foreground');
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);

class index_db extends foreground {

	private $times_db;
	
	function __construct() {
		parent::__construct();
		$this->_username = param::get_cookie('_username');
		$this->_userid = param::get_cookie('_userid');
		$this->_groupid = get_memberinfo($this->_userid,'groupid');
		$this->http_user_agent = $_SERVER['HTTP_USER_AGENT'];
		$this->index_db = pc_base::load_model('index_db_model');
	}

	public function init() {
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$where = array('id'=>$this->_username,'replyid'=>'0');
 		$infos = $this->index_db->listinfo($where,$order = 'id DESC',$page, 10);
 		$infos = new_html_special_chars($infos);
 		$pages = $this->index_db->pages;
		include template('{{module}}', 'index_db_init');
	}
	
//自动生成函数，crud  table_index.php	
//{{function}}
	public function add(){
		if($_POST){
			$data = $_POST;
			if($this->index_db->insert($data)){
				showmessage(L('operation_success'),HTTP_REFERER);
			}else{
				showmessage("添加失败，请重试！",HTTP_REFERER);
			}
		}else{
			include template('{{module}}', 'index_db_add');
		}
	}
	
	
	public function edit(){
		if($_POST){
			$where = "id=".$_POST['id'];
			unset($_POST['id']);
			$data=$_POST;
			$this->index_db->update($data,$where);
		}else{
			include template('{{module}}', 'index_db_edit');
		}
	}
	
	//is ajax
	public function del(){
		if($_REQUEST['ajax']){
			if($this->index_db->delete(array('id'=>intval($_REQUEST['id'])))){
				echo json_encode("ok");
			}
		}else{
			if($this->index_db->delete(array('id'=>intval($_REQUEST['id'])))){
				showmessage(L('operation_success'),HTTP_REFERER);
			}
		}
	}
	
	public function lists(){
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$where = array('uid'=>$this->_userid,'stat'=>'1');
 		$infos = $this->index_db->listinfo($where,$order = 'id DESC',$page, 10);
 		$infos = new_html_special_chars($infos);
 		$pages = $this->index_db->pages;
		include template('{{module}}', 'index_db_list');
	}
	
	public function info(){
		$infos = $this->index_db->get_one(array('id'=>intval($_REQUEST['id'])));
		include template('{{module}}', 'index_db_info');
	}
	
}
?>