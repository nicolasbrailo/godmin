<?
include "config.php";

// This will be a regex for egrep
$filter_ip = ''; $filter_url = ''; $unique = false;
if (isset($_REQUEST['filter_ip'])) $filter_ip = $_REQUEST['filter_ip'];
if (isset($_REQUEST['filter_url'])) $filter_url = $_REQUEST['filter_url'];
if (isset($_REQUEST['unique'])) $unique = true;

// Setup the regexp
$filter = '';
if ($filter_ip != '') $filter = $filter_ip;

if ($filter_url != '')
{
	// In bind logs the format will be IP[.*]URL, so if it's empty
	// we just filter urls, if it's not then filter by both
	if ($filter != '') $filter .= '.*';
	$filter .= $_REQUEST['filter_url'];
}

$run_cmd = true;
if ($filter_ip == '' and $filter_url == '') $run_cmd = false;

// Keep in mind this might be a hugh file. No point in using php for this.
$cmd = "egrep \"$filter\" ".NAMED_LOG." | sort";
if ($unique === true) $cmd .= ' | uniq';

if ($filter_ip == '') $filter_ip = NETWORK_IP;


include 'layout/header.php';
?>

<h1>DNS Logs</h1>
<p>Keep in mind the DNS logs might be huge. If this script doesn't work try increasing PHP's memory limits and timeouts.</p>

<table width="100%">
<tr>
<td>
<? if ((stristr($filter_url,"porn"))||(stristr($filter_url,"sex"))){ ?>
<div id="lol">Ceiling Cat is watching you masturbate<br/>&nbsp;<br/><img src="theme/ceiling_cat.png" width="150" height="82" alt="Ceiling Cat is watching you masturbate" /></div>
<? } ?>

<form>
<p>
Show logs for IP 
	<input type="text" name="filter_ip" value="<?= $filter_ip ?>"/>
</p>

<p>
Filter URLs by
	<input type="text" name="filter_url" value="<?= $filter_url ?>"/>
</p>
<p><input type="checkbox" name="unique"
	 <?=($unique)? 'checked' : '' ?>"/>
	 Group duplicates (show unique entries)
</p>

<input type="submit" value="Search"/>
</form>
</td>
</tr></table>

<hr/>

<? if ($run_cmd) { 
	echo "Using the following command:<br/>";
	echo "<div style='border:1px coral solid;padding: 10px;'>$cmd</div>";
	echo '<pre>';
	system($cmd);
	echo '</pre>';
}else{
	echo 'Please enter a filter';
}
?>

<? include 'layout/footer.php' ?>
