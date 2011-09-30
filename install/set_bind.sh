#!/bin/bash

LAN_IP=$1
LAN_TLD=$2
ROUTER_HOME=$3

BIND_CTL=/etc/init.d/bind9
BIND_CFG=/etc/bind/named.conf.local
BIND_APPARMOR_CFG=/etc/apparmor.d/usr.sbin.named

# Template files
ZONE_TEMPLATE=default_cfg/bind9/zone.cfg 
LOG_TEMPLATE=default_cfg/bind9/logging.cfg 
DB_TEMPLATE=default_cfg/bind9/zone_db.cfg 
DB_REV_TEMPLATE=default_cfg/bind9/zone_rev_db.cfg 
TMPL_VARS="LAN_TLD BIND_TLD_REV_FILE BIND_TLD_REV BIND_LOGS BIND_TLD LAN_IP"

# Get the name for the rev db. No idea why it's so complicated
rev_ip1="$(echo $LAN_IP|awk -F'.' '{print $1}')"
rev_ip2="$(echo $LAN_IP|awk -F'.' '{print $2}')"
rev_ip3="$(echo $LAN_IP|awk -F'.' '{print $3}')"
rev_ip="$rev_ip3.$rev_ip2.$rev_ip1"

# Script config
BIND_DIR=$ROUTER_HOME"/bind"
BIND_LOGS=$BIND_DIR"/dns.log"
BIND_TLD=$BIND_DIR"/"$LAN_TLD".db"
BIND_TLD_REV="rev.$rev_ip.in-addr.arpa"
BIND_TLD_REV_FILE="$BIND_DIR/$BIND_TLD_REV"


if [ ! -e $BIND_CFG ]; then
	echo "Error: Bind config file ($BIND_CFG) not found. Can't continue installing bind"
	exit
fi


# Make a dir for bind config and dbs
mkdir -p $BIND_DIR
# TODO: Setup $BIND_DIR so it's writable by bind user

# Update security
update_apparmor $BIND_APPARMOR_CFG $BIND_DIR


x=$(cat $BIND_CFG | grep "zone \"$LAN_TLD\"" | wc -l)
if (($x!=0)); then
	echo "A zone for TLD $LAN_TLD has already been defined. Cowardly refusing to write another."
else
	write_cfg_from_template $ZONE_TEMPLATE $BIND_CFG "$TMPL_VARS"
	echo "Updated bind local zones file for $LAN_TLD"
fi

# TODO: This will fail if log is setup like logging\n{
x=$(cat $BIND_CFG | grep "logging.{" | wc -l)
if (($x!=0)); then
	echo "bind has already been configured for logging, won't reconfigure."
	echo -e "\tThe webapp may not have DNS logs available with this setup."
else
	write_cfg_from_template $LOG_TEMPLATE $BIND_CFG "$TMPL_VARS"
	echo "Bind will now log to $BIND_LOGS"
fi


if [ -e $BIND_TLD_REV_FILE ]; then
	echo "A rev dns zone already exists for $BIND_TLD_REV, won't create another"
else
	write_cfg_from_template $DB_REV_TEMPLATE $BIND_TLD_REV_FILE "$TMPL_VARS"
	echo "Created reverse dns zone $BIND_TLD_REV"
fi


if [ -e $BIND_TLD ]; then
	echo "A dns zone already exists for $BIND_TLD, won't create another"
else
	#TODO: Configure WAN IP?
	write_cfg_from_template $DB_TEMPLATE $BIND_TLD "$TMPL_VARS"
	echo "Created dns zone $BIND_TLD"
fi

/.$BIND_CTL restart

