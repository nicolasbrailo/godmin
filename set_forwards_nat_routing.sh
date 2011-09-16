#!/bin/bash

# Parameters from parent script (or CMD)
WAN_IFACE=$1
LAN_IFACE=$2
LAN_IP=$3
ROUTER_HOME=$4

# TODO: Check recv parameters

# Local configs
IP_FWD_CFG=/proc/sys/net/ipv4/ip_forward
IFACES_CFG=/etc/network/interfaces
NETWORKING_SCRIPT=/etc/init.d/networking
IP_FWD_CFG=net.ipv4.ip_forward
SYSCTL_CFG=/etc/sysctl.conf 
STARTUP_SCRIPT=/etc/init.d/rc.local 
SET_FWDS_SCRIPT=allow_forwardings.sh
FWDS_SCRIPT=set_forwards.sh



# Create a configuration for each interface
WAN_IFACE_CFG="auto $WAN_IFACE\n
					iface $WAN_IFACE inet dhcp"

LAN_IFACE_CFG="auto $LAN_IFACE\n
						iface $LAN_IFACE inet static\n
						address $LAN_IP\n
						netmask 255.255.255.0\n"

# If the interface is not already configured, add it to $IFACES_CFG
CFG=$(cat $IFACES_CFG | grep $WAN_IFACE | wc -l)
if (($CFG!=0)); then
	echo
	echo "WAN interface $WAN_IFACE is already configured in $IFACES_CFG";
	echo "Skipping interace, you may need to configure it by hand like this: "
	echo -e $WAN_IFACE_CFG
	echo
else
	echo -e $WAN_IFACE_CFG >> $IFACES_CFG
	echo "$WAN_IFACE configured in $IFACES_CFG"
fi

# Same for LAN iface
CFG=$(cat $IFACES_CFG | grep $LAN_IFACE | wc -l)
if ((0!=$CFG)); then
	echo
	echo "LAN interface $LAN_IFACE is already configured in $IFACES_CFG";
	echo "Skipping interace, you may need to configure it by hand like this: "
	echo -e $LAN_IFACE_CFG
	echo
else
	echo -e $LAN_IFACE_CFG >> $IFACES_CFG
	echo "$LAN_IFACE configured in $IFACES_CFG"
fi



echo "Setting up ipv4 forwarding as a permanent rule in $SYSCTL_CFG"
cat $SYSCTL_CFG | grep -v $IP_FWD_CFG > /tmp/sysctl.conf
echo "$IP_FWD_CFG = 1" >> /tmp/sysctl.conf
mv /tmp/sysctl.conf $SYSCTL_CFG
rm /tmp/sysctl.conf

echo "Restarting networking services"
/./$NETWORKING_SCRIPT restart



# Set up forwards
SET_FWDS_SCRIPT_FILE=$ROUTER_HOME"/"$SET_FWDS_SCRIPT
FWDS_SCRIPT_FILE=$ROUTER_HOME"/"$FWDS_SCRIPT

# Create the forwardings setup file
echo "#!/bin/bash" > $SET_FWDS_SCRIPT
echo "echo 1 > $IP_FWD_CFG" >> $SET_FWDS_SCRIPT
echo "iptables --table nat --append POSTROUTING " \
			"--out-interface $WAN_IFACE -j MASQUERADE" >> $SET_FWDS_SCRIPT
echo "iptables --append FORWARD --in-interface $LAN_IFACE -j ACCEPT" >> $SET_FWDS_SCRIPT
chmod +x $SET_FWDS_SCRIPT

source $SET_FWDS_SCRIPT_FILE

# We will also need this file for each individual forwarding
touch $FWDS_SCRIPT_FILE
chmod +x $FWDS_SCRIPT_FILE




# Call our startup script from within a system startup script
echo "Installing a hook to set forwardings on each startup..."

# Do black magic to escape / into \/
ESCAPED1=$(echo $SET_FWDS_SCRIPT_FILE| sed 's/\//\\\//g')
# For some reason it won't work w/o this spell
ESCAPED1=$(echo $ESCAPED1| sed 's/.sh/\\.\\sh/g')

# Do black magic to escape / into \/
ESCAPED2=$(echo $FWDS_SCRIPT_FILE| sed 's/\//\\\//g')
# For some reason it won't work w/o this spell
ESCAPED2=$(echo $ESCAPED2| sed 's/.sh/\\.\\sh/g')

cat $STARTUP_SCRIPT | \
	sed "s/do_start()/. $ESCAPED1\n. $ESCAPED2\n\ndo_start()/g" > /tmp/startupscript

mv /tmp/startupscript $STARTUP_SCRIPT
rm /tmp/startupscript
echo "Installed call to $SET_FWDS_SCRIPT_FILE in $STARTUP_SCRIPT"
echo "Installed call to $FWDS_SCRIPT_FILE in $STARTUP_SCRIPT"

