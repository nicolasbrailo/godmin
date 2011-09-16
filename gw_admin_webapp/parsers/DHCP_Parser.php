<?

class DHCP_Parser
{
	/**
	 * Returns a variable in a block by its token name
	 */
	function get_by_token($str, $scope_ini, $scope_end, $token, $end_tok = ';')
	{
		$tok_ini = strpos($str, $token, $scope_ini);
		if ($tok_ini === false or ($tok_ini > $scope_end)) return 'Unknown';

		$val_ini = $tok_ini + strlen($token);
		$val_end = strpos($str, $end_tok, $tok_ini);
		// If this happen's, we might have a malformed leases file
		if ($val_end === false or ($val_end > $scope_end)) return 'Unknown';

		return trim(substr($str, $val_ini, $val_end-$val_ini));
	}

	function parse($str)
	{
		$tok = $this->get_block_start_tok();
		$pos = strpos($str, $tok);
		while ($pos !== false)
		{
			$max = strpos($str, "}", $pos+1);
			$this->parse_block($str, $pos, $max);
			$pos = strpos($str, $tok, $pos+1);
		}

		return $this->get_list();
	}
}

?>
