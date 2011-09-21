<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Godmin &#8250; DHCP Subnets</title>
<link rel="stylesheet" href="style.css">
</head>
<?
include_once 'config.php';
include_once 'parsers/Subnets_Parser.php';

$sp = new Subnets_Parser();
?>

<body>
<? include 'menu.php' ?>
<div id="content">

<h1>DHCP Subnets</h1>

<pre>
<?
$sp->parse(file_get_contents(SUBNETS_FILE));
?>
</pre>

</div>
</body>
</html>