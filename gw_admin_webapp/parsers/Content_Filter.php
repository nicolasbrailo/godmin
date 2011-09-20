<?
class Content_Filter_Parser implements Iterator
{
	function parse($conf)
	{
		$f = create_function('$s', 'return strpos($s, "url_regex")!==false;');
		$filters = array_filter(explode("\n", $conf), $f);

		foreach($filters as $filter)
		{
			$v = explode(' ', $filter);
			$name = $v[1];
			// Remove the first and last character, since they should be "
			$file = substr($v[3], 1, -1);

			array_push($this->filters, array($name, $file));
		}
	}

	function get_file_for($filter_name)
	{
		foreach($this->filters as $f)
			if ($f[0] == $filter_name) return $f[1];

		return null;
	}

	function get_conf_string_for_filter($name, $file)
	{
		$cnf  = "acl $name url_regex \"$file\"\n";
		$cnf .= "http_access deny $name\n\n";
		return $cnf;
	}

	function clear()
	{
		$this->filters = array();
	}

	private $filters = array();
	private $pos = 0;

	public function rewind() { $this->pos = 0; }
	public function current() { return $this->filters[$this->pos]; }
	public function key() { return $this->pos; }
	public function next() { ++$this->pos; }
	public function valid(){ return ($this->pos < sizeof($this->filters)); }
}
?>
