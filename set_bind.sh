#!/bin/bash

LAN_IP=$1
LAN_TLD=$2
ROUTER_HOME=$3

BIND_CFG=/etc/bind/named.conf.local
BIND_APPARMOR_CFG=/etc/apparmor.d/usr.sbin.named

# Script config
BIND_DIR=$ROUTER_HOME"/bind"
BIND_LOGS=$BIND_DIR"/dns.log"
BIND_TLD=$BIND_DIR"/"$LAN_TLD".db"


# Get the name for the rev db. No idea why it's so complicated
rev_ip1="$(echo $LAN_IP|awk -F'.' '{print $1}')"
rev_ip2="$(echo $LAN_IP|awk -F'.' '{print $2}')"
rev_ip3="$(echo $LAN_IP|awk -F'.' '{print $3}')"
rev_ip="$rev_ip3.$rev_ip2.$rev_ip1"
BIND_TLD_REV="rev.$rev_ip.in-addr.arpa"
BIND_TLD_REV_FILE="$BIND_DIR/$BIND_TLD_REV"


# Escape all the / from the paths
ESCAPED_BIND_TLD_REV_FILE=$(echo "$BIND_TLD_REV_FILE" | sed 's/\//\\\//g')
ESCAPED_BIND_TLD_REV_=$(echo "$BIND_TLD_REV" | sed 's/\//\\\//g')
ESCAPED_BIND_LOGS=$(echo "$BIND_LOGS" | sed 's/\//\\\//g')
ESCAPED_BIND_TLD=$(echo "$BIND_TLD" | sed 's/\//\\\//g')


function bind_write_cfg()
{
	template=$1
	dest=$2

	cat $template | \
					sed "s/\$LAN_TLD/$LAN_TLD/g" | \
					sed "s/\$BIND_TLD_REV_FILE/$ESCAPED_BIND_TLD_REV_FILE/g" | \
					sed "s/\$BIND_TLD_REV/$ESCAPED_BIND_TLD_REV_/g" | \
					sed "s/\$BIND_LOGS/$ESCAPED_BIND_LOGS/g" | \
					sed "s/\$BIND_TLD/$ESCAPED_BIND_TLD/g" >> $dest
}


x=$(cat $BIND_CFG | grep "zone \"$LAN_TLD\"" | wc -l)
if (($x!=0)); then
	echo "A zone for TLD $LAN_TLD has already been defined. Cowardly refusing to write another."
else
	bind_write_cfg default_cfg/bind9/zone.cfg $BIND_CFG
	echo "Updated bind local zones file for $LAN_TLD"
fi

mkdir -p $BIND_DIR

if [ -e $BIND_TLD_REV_FILE ]; then
	echo "A rev dns zone already exists for $BIND_TLD_REV, won't create another"
else
	bind_write_cfg default_cfg/bind9/zone_rev_db.cfg $BIND_TLD_REV_FILE
	echo "Created reverse dns zone $BIND_TLD_REV"
fi

if [ -e $BIND_TLD ]; then
	echo "A dns zone already exists for $BIND_TLD, won't create another"
else
	#TODO: Configure WAN IP?
	bind_write_cfg default_cfg/bind9/zone_db.cfg $BIND_TLD

	echo "Created dns zone $BIND_TLD"
fi

exit

if [ ! -f $BIND_APPARMOR_CFG ]; then
	echo "No apparmor detected; remember to configure it, if you install it later"
else
	x=$(cat $BIND_APPARMOR_CFG | grep $BIND_DIR | wc -l)
	if (( $x!=0 )); then
		echo "Apparmor seems already configured for $BIND_DIR, won't alter it"
	else
		# Find the closing brace for the apparmor cfg
		ln=$( cat $BIND_APPARMOR_CFG | grep -n '}' | tail -n1 | awk -F':' '{print $1}' )
		# Write everything but the closing brace
		head -n$(($ln-1)) $BIND_APPARMOR_CFG > /tmp/apparmor_cfg

		echo -e "\t$BIND_DIR/** rw," >> /tmp/apparmor_cfg
		echo -e "\t$BIND_DIR/ rw," >> /tmp/apparmor_cfg
		echo "}" >> /tmp/apparmor_cfg 
		mv /tmp/apparmor_cfg $BIND_APPARMOR_CFG
		echo "Apparmor configuration updated for $BIND_DIR"
	fi
fi

# TODO: Setup $BIND_DIR so it's writable by bind user

