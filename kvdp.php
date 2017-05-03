<?php
//抑制所有错误信息
@error_reporting(E_ALL &~ E_NOTICE);
@set_time_limit(0);
date_default_timezone_set('PRC');
if (!isset($_SERVER['PHP_SELF']) || empty($_SERVER['PHP_SELF'])) $_SERVER['PHP_SELF']=$_SERVER['SCRIPT_NAME'];
header('Content-type:text/html;charset=utf-8');//强制语言
define('PWD',md5('931227'));
$cookiepwd=isset($_COOKIE['sykvmanage'])?$_COOKIE['sykvmanage']:'';
$kv=new SaeKV();
$kv->init();
$a=isset($_GET['a'])?$_GET['a']:'index';
$k=isset($_REQUEST['k'])?urldecode($_REQUEST['k']):'';
$v=isset($_REQUEST['v'])?urldecode($_REQUEST['v']):'';
if($a == 'login') {
	$passwd=md5($_POST['passwd']);
	if ($passwd==PWD) { //密码正确
		setcookie('sykvmanage',$passwd,time()+3600*24*7,'/');
		echo '<script>window.location.href="?a=allkv"</script>';
		exit;
	} else { //密码错误
		echo '<script>alert("密码错误");history.go(-1);</script>';
		exit;
	}
} elseif ($a=='logout'){
	setcookie('sykvmanage','z',time()-3600);
	echo '<script>window.location.href="?a=index";</script>';
}
function check_login() {
	global $cookiepwd;
	if ($cookiepwd!=PWD) {
		echo '<script>window.location.href="?a=index"</script>';
		exit;
	}
}
?>
<!DOCTYPE html PUBLIC “-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>SY KVDB Manager</title>
<link rel="stylesheet" href="http://libs.useso.com/js/bootstrap/3.2.0/css/bootstrap.min.css" type="text/css" />
<style>
body { margin:0;font-family:"Microsoft YaHei","微软雅黑",Serif; }
h3 { font-family:"Microsoft YaHei","微软雅黑",Serif; }
.btn { margin:3px; }
.input-group .btn { margin-top:0;margin-bottom:0;margin-right:0; }
#body {
	width:900px;
	margin:10px auto;
}
#header {
	height:40px;
	margin-bottom:10px;
	line-height:30px;
	vertical-align:middle;
}
#header div {
	display:inline-block;
}
#title {
	font-size:27px;
	height:100%;
	width:100px;
}
#header .btn-group {
	width:780px;
	margin-left:10px;
	height:100%;
}
</style>
</head>
<body>
<div id="body">
	<div id="header">
		<div id="title" class="text-center">KVDB</div>
		<div class="btn-group btn-group-justified">
			<a href="?a=set" class="btn btn-default">添加/修改</a>
			<a href="?a=get" class="btn btn-default">读取</a>
			<a href="?a=del" class="btn btn-default">删除</a>
			<a href="?a=allkv" class="btn btn-default">全部</a>
			<a href="?a=logout" class="btn btn-default">退出</a>
		</div>
	</div><!-- /header -->
	<div id="main">
<?php
if ($a=='index'){
	if ($cookiepwd==PWD) exit('<script>window.location.href="?a=allkv"</script>');
	echo '<form action="?a=login" method="post"><div class="input-group"><input type="text" class="form-control" name="passwd"><span class="input-group-btn"><input class="btn btn-default" type="submit" value="确定"></span></div></form>';
} elseif ($a=='set'){
	check_login();
	if (!empty($k) && !empty($v)){ //设置KV
		$kv->set($k,$v);
		echo '<div class="alert alert-success"><h3>设置成功</h3><p>Key:<code>',htmlspecialchars($k),'</code></p><p>Val:</p><pre>',$v,'</pre></div>';
	} else { //显示设置页面
		$v = $kv->get($k)
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">添加/修改</h3>
	</div>
	<div class="panel-body text-center">
		<form action="?a=set" method="post">
			<p>Key:<input type="text" class="form-control" name="k" value="<?php echo $k; ?>" /></p>
			<p>Val:</p><textarea rows="8" name="v" class="form-control"><?php echo $v; ?></textarea>
			<p><input type="submit" value="保存" class="btn btn-default"/></p>
		</form>
	</div>
</div>
<?php
	} //显示设置页面 end
} elseif ($a=='get') { //显示已存在的值
	check_login();
	if (!empty($k)) {
		$v=$kv->get($k);
		if ($v!==FALSE) {
			$view=isset($_REQUEST['view'])?$_REQUEST['view']:'';
			if ($view=='json') $v=json_decode($v,1);
			echo '<div class="alert alert-success"><p><a href="?a=get&view=json&k=',urlencode($k),'" class="btn btn-default">Json解码</a></p><p>Key:',htmlspecialchars($k),'</p><p>Val:</p><pre>';
			var_dump($v);
			echo '</pre></div>';
		} else {
			echo '<dic class="alert alert-danger">',htmlspecialchars($k),' 不存在！</div>';
		} //显示 end
} else { //输入Key
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">查看</h3>
	</div>
	<div class="panel-body text-center">
		<form action="?a=get" method="post">
			<div class="input-group">
				<input type="text" class="form-control" name="k">
				<span class="input-group-btn">
					<input class="btn btn-default" type="submit" value="查看">
				</span>
			</div>
		</form>
	</div>
</div>
<?php
	} //查看 end
} elseif ($a=='del') {
	check_login();
	if (!empty($k)) {
		$v=$kv->delete($k);
		echo '<div class="alert alert-success">',htmlspecialchars($k),' 已删除！</div>';
	} else { //显示删除页面
?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">删除</h3>
		</div>
		<div class="panel-body text-center">
			<form action="?a=del" method="post">
				<div class="input-group">
					<input type="text" class="form-control" name="k" />
					<span class="input-group-btn"><input class="btn btn-default" type="submit" value="删除" /></span>
				</div>
			</form>
		</div>
	</div>
<?php
	} //显示删除 end
} elseif ($a=='allkv') { //全部KV
	check_login();
	$ret=$kv->pkrget('',100);
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">全部</h3>
	</div>
	<div class="panel-body text-center">
		<table class="table table-hover">
			<thead><th>Key</th><th>操作</th></thead>
			<tbody>
<?php
while (true) {
	foreach ($ret as $k=>$v) echo '<tr><td>',$k,'</td><td><a href="?a=get&k=',urlencode($k),'" class="btn btn-primary">查看</a><a href="?a=set&k=',urlencode($k),'" class="btn btn-primary">修改</a><a href="?a=del&k=',urlencode($k),'" onclick="return confirm(\'确认删除？\');" class="btn btn-danger">删除</a></p>';
	end($ret);
	$start_key=key($ret);
	$i=count($ret);
	if ($i<100) break;
	$ret=$kv->pkrget('',100,$start_key);
}
?>
			</tbody>
		</table>
	</div>
</div>
<?php
}
?>
