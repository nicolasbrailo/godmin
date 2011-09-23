<?
# *IMPORTANT NOTE*
# This file will be overwritten by the install script with the appropiate
# values for the environment, but it's still commited to the repository so you
# can checkout the project and use its folder as the webroot for development.
# 
# Keep in mind any change here must be reflected in the template config file in
# 		../install/default_cfg/webapp/config.php
# Not reflecting the changes there will break any installation of the webapp.

# DHCPd config
define("LEASES_FILE",			"/home/router/dhcp/leases/dhcpd.leases");
define("STATIC_HOSTS_FILE",	"/home/router/dhcp/static_hosts.conf");
define("SUBNETS_FILE",			"/home/router/dhcp/subnets.conf");

# Net config
define("FORWARDINGS_FILE",		"/home/router/net/set_forwards.sh");
define("NETWORK_IP",				"192.168.10");

# DNS config
define("NAMED_FILE",		"/home/router/named/boc.db");
define("NAMED_LOG",		"/home/router/named/dns.log");

# Proxy config
define("CONTENT_FILTER_CONF",		"/home/router/squid/squid.acl.conf");
define("CONTENT_FILTER_DIR",		"/home/router/squid/");
define("PROXY_REPORT_DIR",			"proxy_reports");

# OS sudo commands
define("RESTART_DNS",				"/home/router/sudo_scripts/restart_dns.sh");
define("RESTART_DHCP",				"/home/router/sudo_scripts/restart_dhcp.sh");
define("RESTART_NAT_AND_FWDS",	"/home/router/sudo_scripts/restart_nat_and_fwds.sh");
define("RESTART_CONTENT_FILTER",	"/home/router/sudo_scripts/restart_squid.sh");
define("GENERATE_PROXY_REPORT",	"/home/router/sudo_scripts/generate_proxy_report.sh");


?>
