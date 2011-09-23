<?
include_once 'config.php';
include_once 'parsers/Subnets_Parser.php';

$sp = new Subnets_Parser();

include 'layout/header.php';
?>

<h1>DHCP Subnets</h1>

<pre>
<?
$sp->parse(file_get_contents(SUBNETS_FILE));
?>
</pre>

<? include 'layout/footer.php' ?>
