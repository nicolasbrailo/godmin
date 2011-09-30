#!/bin/bash


# TODO: The boot hooks are not working and they need to be manually installed to rc.local


# Parameters from parent script (or CMD)
WAN_IFACE=$1
LAN_IFACE=$2
LAN_IP=$3
ROUTER_HOME=$4

# System config
IFACES_CFG=/etc/network/interfaces
NETWORKING_CTL=/etc/init.d/networking
STARTUP_SCRIPT=/etc/init.d/rc.local 
SYSCTL_CFG=/etc/sysctl.conf 
PROC_IP_FWD_CFG=/proc/sys/net/ipv4/ip_forward
IP_FWD_CFG=net.ipv4.ip_forward
IF_UP_HOOK=/etc/network/if-up.d/router_restart.sh
RC_D_HOOK=/etc/init.d/godmin_router

# Script cfg
START_FWDS_SCRIPT_FILE=$ROUTER_HOME"/net/allow_forwardings.sh"
FWDS_SCRIPT_FILE=$ROUTER_HOME"/net/set_forwards.sh"
BLOCKS_SCRIPT_FILE=$ROUTER_HOME"/net/blocked_clients.sh"
RESTART_SCRIPT=$ROUTER_HOME"/net/restart.sh"
ROUTER_LOG=$ROUTER_HOME"/router.log"

# Script templates config
WAN_IFACE_TMPL=default_cfg/net/wan_iface.cfg
LAN_IFACE_TMPL=default_cfg/net/lan_iface.cfg
START_FWDS_TMPL=default_cfg/net/start_forwards.sh
RESTART_TMPL=default_cfg/net/restart.sh
IF_UP_HOOK_TMPL=default_cfg/net/ifup_router_restart.sh
RC_D_HOOK_TMPL=default_cfg/net/rc.d_restart.sh
TMPL_VARS="WAN_IFACE LAN_IFACE LAN_IP PROC_IP_FWD_CFG START_FWDS_SCRIPT_FILE FWDS_SCRIPT_FILE BLOCKS_SCRIPT_FILE RESTART_SCRIPT ROUTER_LOG"



mkdir -p "$ROUTER_HOME/net"

# Create the script for individual forwardings
if [ ! -e $FWDS_SCRIPT_FILE ]; then
	touch $FWDS_SCRIPT_FILE
fi
if [ ! -e $BLOCKS_SCRIPT_FILE ]; then
	touch $BLOCKS_SCRIPT_FILE
fi



# Write FWDS startup script
if [ ! -e $START_FWDS_SCRIPT_FILE ]; then
	write_cfg_from_template $START_FWDS_TMPL $START_FWDS_SCRIPT_FILE "$TMPL_VARS"
else
	warning "$START_FWDS_SCRIPT_FILE already exists. Won't write a new one."
fi



# Write restart script and its system hook
if [ ! -e $RESTART_SCRIPT ]; then
	write_cfg_from_template $RESTART_TMPL $RESTART_SCRIPT "$TMPL_VARS"
fi
if [ ! -e $IF_UP_HOOK ]; then
	write_cfg_from_template $IF_UP_HOOK_TMPL $IF_UP_HOOK "$TMPL_VARS"
	echo "Wrote hook in $IF_UP_HOOK to $RESTART_SCRIPT"
fi

chmod +x $FWDS_SCRIPT_FILE $BLOCKS_SCRIPT_FILE $START_FWDS_SCRIPT_FILE $RESTART_SCRIPT $IF_UP_HOOK



if [ ! -e $RC_D_HOOK ]; then
	write_cfg_from_template $RC_D_HOOK_TMPL $RC_D_HOOK "$TMPL_VARS"
	chmod +x $RC_D_HOOK 
	#update-rc.d -f godmin_router remove
	update-rc.d godmin_router defaults
	echo "Wrote hook in system restart script $RC_D_HOOK to $RESTART_SCRIPT"
fi



# If the interface is not already configured, add it to $IFACES_CFG
CFG=$(cat $IFACES_CFG | grep $WAN_IFACE | wc -l)
if ((0!=$CFG)); then
	warning "WAN interface $WAN_IFACE is already configured in $IFACES_CFG."
	warning "\tSkipping interace, check $WAN_IFACE_TMPL to see a config example."
else
	write_cfg_from_template $WAN_IFACE_TMPL $IFACES_CFG "$TMPL_VARS"
	echo "$WAN_IFACE configured in $IFACES_CFG"
fi

# Same for LAN iface
CFG=$(cat $IFACES_CFG | grep $LAN_IFACE | wc -l)
if ((0!=$CFG)); then
	warning "LAN interface $LAN_IFACE is already configured in $IFACES_CFG";
	warning "\tSkipping interace, check $LAN_IFACE_TMPL to see a config example."
else
	write_cfg_from_template $LAN_IFACE_TMPL $IFACES_CFG "$TMPL_VARS"
	echo "$LAN_IFACE configured in $IFACES_CFG"
fi


echo -e "Setting up ipv4 forwarding as a permanent rule in $SYSCTL_CFG..."
cat $SYSCTL_CFG | grep -v $IP_FWD_CFG > /tmp/sysctl.conf
echo "$IP_FWD_CFG = 1" >> /tmp/sysctl.conf
mv /tmp/sysctl.conf $SYSCTL_CFG


echo "Restarting networking services, setting up forwards"
/./$NETWORKING_CTL restart

