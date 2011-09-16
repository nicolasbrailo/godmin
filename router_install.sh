#!/bin/bash

function get_user_input()
{
	msg=$1
	default=$2

	echo -n "$msg [$default]: "
	read val

	if [ "$val" == "" ]; then
		echo $default;
	else
		echo $val;
	fi
} 

wan_iface = $(get_user_input 'Type the name of the WAN interface', 'eth0')
lan_iface = $(get_user_input 'Type the name of the LAN interface', 'eth1')
lan_ip = $(get_user_input 'Type the IP of the LAN interface', '192.168.1.1')

echo "Installing WAN $wan_iface and LAN $lan_iface ($lan_ip)"

