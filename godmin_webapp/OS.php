<?
// Facade to perform OS operations like restarting a service

class OS
{
	static function is_service_running($svc_bin_name)
	{
			  $st = exec("ps -fea|grep $svc_bin_name |grep -v grep|wc -l");
			  return $st == 1;
	}

	static function restart_all_networking()
	{
		OS::restart_nat_and_fwds();
		OS::restart_dhcp();
		OS::restart_dns();
	}

	static function restart_dns()
	{
		echo "<pre>";
		exec("sudo /bin/bash ".RESTART_DNS);
		echo "</pre>";
	}

	static function restart_dhcp()
	{
		echo "<pre>";
		exec("sudo /bin/bash ".RESTART_DHCP);
		echo "</pre>";
	}

	static function restart_nat_and_fwds()
	{
		echo "<pre>";
		exec("sudo /bin/bash ".RESTART_NAT_AND_FWDS);
		echo "</pre>";
	}

	static function restart_content_filter()
	{
		echo "<pre>";
		exec("sudo /bin/bash ".CONTENT_FILTER_RESTART);
		echo "</pre>";
	}
	
	static function generate_proxy_report()
	{
		echo "<pre>";
		exec("sudo /bin/bash ".GENERATE_PROXY_REPORT);
		echo "</pre>";
	}

}

?>
