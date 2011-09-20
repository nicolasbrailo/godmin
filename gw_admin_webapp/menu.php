<?php
$currentpath = $_SERVER["PHP_SELF"];
$file = explode('/', $currentpath);
$currentpage = $file[count($file) - 1];
?>

<ul class="sandbar">
        <li id="logo"><a href="https://code.google.com/p/godmin/">
                <img src="img/godmin.png" height="33" width="100" border="0" />
                </a>
        </li>

        <li <? if ($currentpage=="leases.php"){ ?> class="active"<? } ?>>
                <a href="leases.php">Known DHCP Leases</a>
        </li>

        <li <? if ($currentpage=="subnets.php"){ ?> class="active"<? } ?>>
                <a href="subnets.php">Configure DHCP subnets</a>
        </li>

        <li <? if ($currentpage=="static_hosts.php"){ ?> class="active"<? } ?>>
                <a href="static_hosts.php">Static hosts</a>
        </li>

        <li <? if ($currentpage=="dns_logs.php"){ ?> class="active"<? } ?>>
                <a href="dns_logs.php">DNS Logs</a>
        </li>

        <li <? if ($currentpage=="content_filter.php"){ ?> class="active"<? } ?>>
                <a href="content_filter.php">Content Filter</a>
        </li>
</ul>
