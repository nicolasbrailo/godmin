<?
include_once 'config.php';
include_once 'OS.php';
include_once 'parsers/Content_Filter_Parser.php';

$cf = new Content_Filter_Parser();
$cf->parse(file_get_contents(CONTENT_FILTER_CONF));

include 'layout/header.php';
?>

<h1>Content Filter</h1>
<div id="lol">Legum servi sumus ut liberi esse possimus<br/>&nbsp;<br/><img src="theme/thought_police.png" width="150" height="186" alt="Legum servi sumus ut liberi esse possimus" /></div>

<?
if (isset($_POST["new_filter_name"]))
{
	$name = $_POST["new_filter_name"];
	$file = CONTENT_FILTER_DIR."/$name.acl";

	if (file_exists($file))
	{
		echo "Filter $name already exists, won't create a new one.";
	}else{
		$cnf = $cf->get_conf_string_for_filter($name, $file);
		file_put_contents($file, "");
		file_put_contents(CONTENT_FILTER_CONF, $cnf, FILE_APPEND | LOCK_EX);
		$cf->clear();
		$cf->parse(file_get_contents(CONTENT_FILTER_CONF));
	}

}else if (isset($_POST["delete"])) {

	$conf = '';
	foreach ($cf as $filter) {
		$name = $filter[0];
		if ($name == $_POST["filter_name"]) continue;
		$file = $cf->get_file_for($name);

		$conf .= $cf->get_conf_string_for_filter($name, $file);
	}

	if (unlink($cf->get_file_for($_POST["filter_name"]))) {
		file_put_contents(CONTENT_FILTER_CONF, $conf, LOCK_EX);
		$cf->clear();
		$cf->parse(file_get_contents(CONTENT_FILTER_CONF));
		OS::restart_content_filter();

	}else{
		echo "Couldn't delete ".$cf->get_file_for($_POST["filter_name"]);
	}

}else if (isset($_POST["filter_name"])){
	$fname = $cf->get_file_for($_POST["filter_name"]);
	file_put_contents($fname, $_POST["filter_contents"], LOCK_EX);
	OS::restart_content_filter();
}
?>


<form name="new_filter" method="post">
Available filters:
<? foreach($cf as $filter) { ?>
	<a href="content_filter.php?filter=<?= $filter[0] ?>"><?= $filter[0] ?></a> | 
<? } ?>
	<input type="submit" value="Create new filter: "/><input type="text" name="new_filter_name"/>
</form>


<? if (isset($_REQUEST["filter"]) and ($cf->get_file_for($_REQUEST["filter"]) != "")) { ?>
	<h2>Viewing filter <?= $cf->get_file_for($_REQUEST["filter"]) ?></h2>
	<form name="edit_filter" method="post">
	<textarea name="filter_contents" style="height: 500px; width: 800px"
		><?= file_get_contents($cf->get_file_for($_REQUEST["filter"])) ?></textarea><br/>
	<input type="submit" value="Save changes"/>
	<input type="submit" value="Delete filter" name="delete"/>
	<input type="hidden" name="filter_name" value="<?= $_REQUEST["filter"] ?>"/>
	</form>
<? } ?>

<? include 'layout/footer.php' ?>
