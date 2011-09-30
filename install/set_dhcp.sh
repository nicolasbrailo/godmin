#!/bin/bash

LAN_IP=$1
LAN_TLD=$2
ROUTER_HOME=$3
WAN_ROUTER_IP=$4
GROUP=www-data

# Templates used for dhcp
LAN_SUBNET_TMPL=default_cfg/isc-dhcp/lan_subnet.cfg 
WAN_SUBNET_TMPL=default_cfg/isc-dhcp/wan_subnet.cfg 
DHCP_CFG_TMPL=default_cfg/isc-dhcp/dhcpd.conf 
TMPL_VARS="LAN_TLD LAN_IP WAN_ROUTER_IP SUBNETS_CFG HOSTS_CFG WAN_NET_IP NET_IP"

# dhcp config dirs
DHCPD_CTL=/etc/init.d/isc-dhcp-server
DHCPD_CFG=/etc/dhcp/dhcpd.conf
DHCPD_DIR=$ROUTER_HOME/dhcp
SUBNETS_CFG=$ROUTER_HOME/dhcp/subnets.conf
HOSTS_CFG=$ROUTER_HOME/dhcp/static_hosts.conf
DHCPD_APPARMOR_CFG=/etc/apparmor.d/usr.sbin.dhcpd 

# Network IP
net_ip1="$(echo $LAN_IP|awk -F'.' '{print $1}')"
net_ip2="$(echo $LAN_IP|awk -F'.' '{print $2}')"
net_ip3="$(echo $LAN_IP|awk -F'.' '{print $3}')"
NET_IP="$net_ip1.$net_ip2.$net_ip3"

# WAN IP
WAN_NET_IP='a.b.c.d'
if [ "$WAN_ROUTER_IP" != "" ]; then
	wan_net_ip1="$(echo $WAN_ROUTER_IP|awk -F'.' '{print $1}')"
	wan_net_ip2="$(echo $WAN_ROUTER_IP|awk -F'.' '{print $2}')"
	wan_net_ip3="$(echo $WAN_ROUTER_IP|awk -F'.' '{print $3}')"
	WAN_NET_IP="$wan_ip1.$wan_ip2.$wan_ip3.0"

	# Used for template
	WAN_ROUTER_IP=", $WAN_ROUTER_IP"
fi


mkdir -p "$ROUTER_HOME/dhcp"
echo "Creating link to leases file from dhcp home dir"
ln -s /var/lib/dhcp $ROUTER_HOME/dhcp/leases
touch $HOSTS_CFG
touch $SUBNETS_CFG
chmod 774 $SUBNETS_CFG $HOSTS_CFG
chgrp $GROUP $SUBNETS_CFG $HOSTS_CFG

# Update security
update_apparmor $DHCPD_APPARMOR_CFG $DHCPD_DIR


if [ ! -e $DHCPD_CFG ]; then
	echo "DHCPD config file ($DHCPD_CFG) not found, can't configure DHCP"
else
	if [ -e "$DHCPD_CFG.bck" ]; then
		echo "It seems DHCPD is already configured, won't write to $DHCPD_CFG"
	else
		mv $DHCPD_CFG "$DHCPD_CFG.bck"
		write_cfg_from_template $DHCP_CFG_TMPL $DHCPD_CFG "$TMPL_VARS"
		echo "Configured DHCP to read network setup from $ROUTER_HOME/dhcp"
	fi
fi

# Write subnets config to dhcpd.conf
if [ "$WAN_ROUTER_IP" != "" ]; then
	write_cfg_from_template $WAN_SUBNET_TMPL $SUBNETS_CFG "$TMPL_VARS"
fi

write_cfg_from_template $LAN_SUBNET_TMPL $SUBNETS_CFG "$TMPL_VARS"
echo "Wrote subnets configuration to $SUBNETS_CFG"

/.$DHCPD_CTL restart

