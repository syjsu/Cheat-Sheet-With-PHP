<?php

/**
 * 检验私密页面的密码
 *
 * 使用方法
 *
 * <?php
* 	include('password.php');
* ?>
 */
$page_pwd = md5('小小酥'); //你要设置的密码,和下面的二选一,推荐使用下面的
$page_cookname = 'my-page-password'; //你要设置的cookie名
$page_now = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
$action = @$_GET['action'];
$page_postpwd = @$_POST['page_pwd'];
$page_cookiepwd = @$_COOKIE["$page_cookname"];
$page_cookietime = time() + 60 * 60 * 24 * 7;

//输出网页的头部
$head =  '
	<head>
	<meta charset="utf-8">
	<title>product</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
	<script src="http://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<style type="text/css">body,button,input,select,textarea,h1,h2,h3,h4,h5,h6 {
		font-family: Microsoft YaHei, "宋体", Tahoma, Helvetica, Arial, "\5b8b\4f53", sans-serif;
	}
	</style>
';

//退出登录
if ($action == "logout") {
	setcookie($page_cookname, "", time() - 1);
	echo '
	<meta http-equiv="refresh" content="2";URL='.$page_now.'>

	</head>
	<body>
	<div class="container-fluid">

	<p>退出成功,2秒后自动跳转</p>
	<a role="button" class="btn btn-success" href='.$page_now.'>点此马上跳转</a>

	</div>
	</body>
	</html>
	';
	exit;
}

//有输入密码
if ($page_postpwd != "") {
	//密码错误
	if (md5("$page_postpwd") != $page_pwd) {
		echo $head;
		echo '
			<meta http-equiv="refresh" content="2";URL='.$page_now.'>

			</head>
			<body>
			<div class="container-fluid">

			<p>密码错误,2秒后自动跳转</p>
			<a role="button" class="btn btn-success" href='.$page_now.'>点此马上跳转</a>

			</div>
			</body>
			</html>
			';
		exit;
	}
	//密码正确
	else {
		//设置Cookies
		setcookie($page_cookname, md5("$page_postpwd"), $page_cookietime);
		echo $head;
		echo '
			<meta http-equiv="refresh" content="2";URL='.$page_now.'>
			</head>
			<body>
			<div class="container-fluid">

			<p>密码正确,2秒后自动跳转</p>
			<a role="button" class="btn btn-success" href='.$page_now.'>点此马上跳转</a>

			</div>
			</body>
			</html>
			';
		exit;
	}
}
//没输入密码
if ($page_cookiepwd != $page_pwd) {
	echo $head;
	echo '
		</head>
		<body>
		<div class="row text-center vertical-middle-sm">
		<div class="col-sm-12">
		<div class="container-fluid">
		<br>
		<p>这是一个私人页面,请输入您的密码</p>
		<br>
		<form method="POST">
       		<div class="form-group">
		<input type="text" class="form-control" name="page_pwd" placeholder="请输入您的密码">
		<br>
		<button type="submit" class="btn btn-default">确认</button>

		</div>
		</div>
       		</div>
		</form>
		</div>
		</body>
		</html>
		';
	exit;
}
?>
