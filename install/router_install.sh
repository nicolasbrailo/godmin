#!/bin/bash

source common_functions.sh

PREREQS="sudo bind9 isc-dhcp-server apache2 php5"

if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

for pak in $PREREQS; do
	INS=$(dpkg -l "$pak" 2>&1 | grep "$pak" | egrep -v 'none|No packages found' | wc -l)
	if (( $INS==0 )); then
		echo "Package $pak missing. Can't continue."
		echo "To install it type: sudo apt-get install $PREREQS"
		exit
	fi
done


function get_user_input()
{
	msg=$1
	default=$2
	var=$3

	echo -n "$msg [$default]: "
	read val
	#val=""

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
get_user_input 'Type an IP for the WAN router (if any)' '' wan_router_ip
get_user_input 'Enter the home directory for the router' '/home/router' router_home
get_user_input 'Enter the listening port for Godmin' '8181' listen_port
get_user_input 'Enter the listening port for the captive portal' '80' captive_portal

mkdir -p $router_home

echo "Setting up IP forwards and NAT..."
source set_forwards_nat_routing.sh $wan_iface $lan_iface $lan_ip $router_home
 
echo -e "\nSetting up DNS server..."
source set_bind.sh $lan_ip $lan_tld $router_home

echo -e "\nSetting up DHCP server..."
source set_dhcp.sh $lan_ip $lan_tld $router_home $wan_router_ip 

echo -e "\nInstalling the webapp..."
source set_webapp.sh $router_home $lan_ip $listen_port $captive_portal


echo "Godmin is now ready to be used"
echo " TODO: Hookup the restart script in /home/router/net/restart.sh in /etc/rc.local, so it's run on each boot"
echo " TODO: Configure /etc/apache2/ports.conf to have apache listen to ports 80 and 8181. It's recommendend to conifgure it to listen only on your LAN IP"
echo " TODO: Connect to the admin interface and check if everything works (http://$lan_ip:$listen_port)

