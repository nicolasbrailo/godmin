<?
include_once 'config.php';
include_once 'parsers/Subnets_Parser.php';

if (isset($_POST["subnets_cfg"]))
{
	file_put_contents(SUBNETS_FILE, $_POST["subnets_cfg"]);
}

include 'layout/header.php';
?>

<h1>DHCP Subnets</h1>

<pre>
<?
$sp = new Subnets_Parser();
$sp->parse(file_get_contents(SUBNETS_FILE));
?>
</pre>

<br/><hr/><br>

<p class="alert">Caution: Godmin provides no syntax checking for DHCP subnets definition.
Whatever you write here will be written to the dhcp.conf file. Only change
this if you are really sure that's what you want</p>

<br/>

<form name="edit_subnets" method="post">
<textarea name="subnets_cfg" style="height: 300px; width: 700px"
	><?= file_get_contents(SUBNETS_FILE) ?></textarea><br/>
<input type="submit" value="Save changes"/>
</form>

<? include 'layout/footer.php' ?>
