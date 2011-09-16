#!/bin/bash

if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

function get_user_input()
{
	msg=$1
	default=$2
	var=$3

	echo -n "$msg [$default]: "
	read val

	if [ "$val" == "" ]; then
		eval "$var=$default"
	else
		eval "$var=$val"
	fi
} 

get_user_input 'Type the name of the WAN interface' eth0 wan_iface
get_user_input 'Type the name of the LAN interface' eth1 lan_iface
get_user_input 'Type the IP of the LAN interface (GW)' '192.168.1.1' lan_ip

source set_forwards_nat_routing.sh $wan_iface $lan_iface $lan_ip "/home/nico"

