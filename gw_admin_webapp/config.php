<?

define("LEASES_FILE", "/home/router/dhcp/leases/dhcpd.leases");
define("STATIC_HOSTS_FILE", "/home/router/dhcp/static_hosts.conf");
define("SUBNETS_FILE", "/home/router/dhcp/subnets.conf");
define("FORWARDINGS_FILE", "/home/router/set_forwards.sh");
define("NAMED_FILE", "/home/router/named/boc.db");
define("NAMED_LOG", "/home/router/named/dns.log");
define("CONTENT_FILTER_CONF", "/home/router/squid/squid.acl.conf");
define("CONTENT_FILTER_DIR", "/home/router/squid/");
define("NETWORK_IP", "192.168.10");

// OS sudo commands
define("RESTART_DNS", "/home/router/sudo_scripts/restart_dns.sh");
define("RESTART_DHCP", "/home/router/sudo_scripts/restart_dhcp.sh");
define("RESTART_NAT_AND_FWDS", "/home/router/sudo_scripts/restart_nat_and_fwds.sh");
define("RESTART_CONTENT_FILTER", "/home/router/sudo_scripts/restart_squid.sh");

?>
