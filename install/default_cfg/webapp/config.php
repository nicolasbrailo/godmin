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
define("LEASES_FILE",			"$ROUTER_HOME/dhcp/leases/dhcpd.leases");
define("STATIC_HOSTS_FILE",	"$ROUTER_HOME/dhcp/static_hosts.conf");
define("SUBNETS_FILE",			"$ROUTER_HOME/dhcp/subnets.conf");

# Net config
define("FORWARDINGS_FILE",		"$ROUTER_HOME/net/set_forwards.sh");
define("NETWORK_IP",				"$LAN_IP_PREFIX");

# DNS config
define("NAMED_FILE",		"$ROUTER_HOME/named/boc.db");
define("NAMED_LOG",		"$ROUTER_HOME/named/dns.log");

# Proxy config
define("CONTENT_FILTER_CONF",		"$ROUTER_HOME/proxy/squid.acl.conf");
define("CONTENT_FILTER_DIR",		"$ROUTER_HOME/proxy/");
define("PROXY_REPORT_DIR",			"proxy_logs");

# OS sudo commands
define("RESTART_DNS",				"$ROUTER_HOME/sudo_scripts/restart_dns.sh");
define("RESTART_DHCP",				"$ROUTER_HOME/sudo_scripts/restart_dhcp.sh");
define("RESTART_NAT_AND_FWDS",	"$ROUTER_HOME/sudo_scripts/restart_nat_and_fwds.sh");
define("RESTART_CONTENT_FILTER",	"$ROUTER_HOME/sudo_scripts/restart_squid.sh");
define("GENERATE_PROXY_REPORT",	"$ROUTER_HOME/sudo_scripts/generate_proxy_report.sh");

?>
