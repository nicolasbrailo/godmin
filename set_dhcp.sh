#!/bin/bash

LAN_IP=$1
LAN_TLD=$2
ROUTER_HOME=$3
WAN_ROUTER_IP=$4


DHCPD_CFG=/etc/dhcp/dhcpd.conf
DHCPD_DIR=$ROUTER_HOME/dhcp
SUBNETS_CFG=$ROUTER_HOME/dhcp/subnets.conf
HOSTS_CFG=$ROUTER_HOME/dhcp/static_hosts.conf
DHCPD_APPARMOR_CFG=/etc/apparmor.d/usr.sbin.dhcpd 




if [ ! -e $DHCPD_CFG ]; then
	echo "DHCPD config file ($DHCPD_CFG) not found, can't configure DHCP"
else
	if [ -e "$DHCPD_CFG.bck" ]; then
		echo "It seems DHCPD is already configured, won't write to $DHCPD_CFG"
	else
		mv $DHCPD_CFG "$DHCPD_CFG.bck"

		echo "ddns-update-style none;" > $DHCPD_CFG 
		echo "option domain-name \"$LAN_TLD\";" >> $DHCPD_CFG 

		if [ "$WAN_ROUTER_IP" != "" ]; then
			echo "option domain-name-servers $LAN_IP, $WAN_ROUTER_IP;" >> $DHCPD_CFG 
		else
			echo "option domain-name-servers $LAN_IP;" >> $DHCPD_CFG 
		fi

		echo "default-lease-time 86400;" >> $DHCPD_CFG 
		echo "max-lease-time 172800;" >> $DHCPD_CFG 
		echo "authoritative;" >> $DHCPD_CFG 
		echo "log-facility local7;" >> $DHCPD_CFG 

		echo "include \"$SUBNETS_CFG\";" >> $DHCPD_CFG 
		echo "include \"$HOSTS_CFG\";" >> $DHCPD_CFG 

		mkdir -p "$ROUTER_HOME/dhcp"
		echo "Configured DHCP to read network setup from $ROUTER_HOME/dhcp"
	fi
fi


touch $HOSTS_CFG
touch $SUBNETS_CFG


# Write subnets config to dhcpd.conf
if [ "$WAN_ROUTER_IP" != "" ]; then
	wan_ip1="$(echo $WAN_ROUTER_IP|awk -F'.' '{print $1}')"
	wan_ip2="$(echo $WAN_ROUTER_IP|awk -F'.' '{print $2}')"
	wan_ip3="$(echo $WAN_ROUTER_IP|awk -F'.' '{print $3}')"
	wan_ip="$wan_ip1.$wan_ip2.$wan_ip3"
	exit

	echo "# No service will be given on this subnet, but declaring it helps the" > $SUBNETS_CFG
	echo "# DHCP server to understand the network topology." >> $SUBNETS_CFG
	echo "Subnet $wan_ip.0 netmask 255.255.255.0 {" >> $SUBNETS_CFG
	echo "}" >> $SUBNETS_CFG
	echo -e "\n" >> $SUBNETS_CFG
fi

net_ip1="$(echo $LAN_IP|awk -F'.' '{print $1}')"
net_ip2="$(echo $LAN_IP|awk -F'.' '{print $2}')"
net_ip3="$(echo $LAN_IP|awk -F'.' '{print $3}')"
net_ip="$net_ip1.$net_ip2.$net_ip3"

echo "subnet $net_ip.0 netmask 255.255.255.0 {" >> $SUBNETS_CFG
echo "  range $net_ip.50 $net_ip.200;" >> $SUBNETS_CFG
echo "  option routers $LAN_IP;" >> $SUBNETS_CFG
echo "}" >> $SUBNETS_CFG

echo "Wrote subnets configuration to $SUBNETS_CFG"



echo "Creating link to leases file from dhcp home dir"
ln -s /var/lib/dhcp $ROUTER_HOME/dhcp/leases


# Update security
update_apparmor $DHCPD_APPARMOR_CFG $DHCPD_DIR

