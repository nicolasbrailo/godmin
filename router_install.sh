#!/bin/bash

PREREQS="bind9" # isc-dhcp-server"

if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

for pak in $PREREQS; do
	INS=$(dpkg -l "$pak" 2>&1 | grep "$pak" | egrep -v 'none|No packages found' | wc -l)
	if (( $INS==0 )); then
		echo "Package $pak missing. Can't continue."
		echo "To install it type: sudo apt-get install $pak"
		exit
	fi
done


function get_user_input()
{
	msg=$1
	default=$2
	var=$3

	#echo -n "$msg [$default]: "
	#read val
	val=""

	if [ "$val" == "" ]; then
		eval "$var=$default"
	else
		eval "$var=$val"
	fi
} 

get_user_input 'Type the name of the WAN interface' eth0 wan_iface
get_user_input 'Type the name of the LAN interface' eth1 lan_iface
get_user_input 'Type the IP of the LAN interface (GW)' '192.168.1.1' lan_ip
get_user_input 'Type a custom TLD for the network' 'lan' lan_tld

ROUTER_HOME="/home/nico"

# source set_forwards_nat_routing.sh $wan_iface $lan_iface $lan_ip $ROUTER_HOME
source set_bind.sh $lan_ip $lan_tld $ROUTER_HOME



