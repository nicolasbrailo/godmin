<?
$currentpath = $_SERVER["PHP_SELF"];
$file = explode('/', $currentpath);
$currentpage = str_replace('.php', '', $file[count($file) - 1]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Godmin &#8250; <?= ucwords(str_replace('_', ' ', $currentpage)) ?></title>
<link rel="stylesheet" href="theme/style.css">
</head>

<body>
<!--[if lt IE 9]>
<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
<![endif]-->

<ul class="sandbar">
        <li id="logo"><a href="https://code.google.com/p/godmin/" target="_blank">
                <img src="theme/godmin.png" height="33" width="100" border="0" />
                </a>
        </li>

        <li<? if ($currentpage=="index"){ ?> class="active"<? } ?>>
                <a href="index.php">Home</a>
        </li>

        <li<? if ($currentpage=="leases"){ ?> class="active"<? } ?>>
                <a href="leases.php">Known DHCP Leases</a>
        </li>

        <li<? if ($currentpage=="static_hosts"){ ?> class="active"<? } ?>>
                <a href="static_hosts.php">Static Hosts</a>
        </li>

        <li<? if ($currentpage=="subnets"){ ?> class="active"<? } ?>>
                <a href="subnets.php">Configure DHCP Subnets</a>
        </li>

        <li<? if ($currentpage=="dns_logs"){ ?> class="active"<? } ?>>
                <a href="dns_logs.php">DNS Logs</a>
        </li>

        <li<? if ($currentpage=="proxy_logs"){ ?> class="active"<? } ?>>
                <a href="proxy_logs.php">Proxy Logs</a>
        </li>

        <li<? if ($currentpage=="content_filter"){ ?> class="active"<? } ?>>
                <a href="content_filter.php">Content Filter</a>
        </li>

        <li<? if ($currentpage=="system_status"){ ?> class="active"<? } ?>>
                <a href="system_status.php">System Status</a>
        </li>
</ul>

<div id="content">


