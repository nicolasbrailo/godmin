#!/bin/bash

LAN_IP=$1
LAN_TLD=$2
ROUTER_HOME=$3

BIND_CFG=/etc/bind/named.conf.local
BIND_APPARMOR_CFG=/etc/apparmor.d/usr.sbin.named

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


x=$(cat $BIND_CFG | grep "zone \"$LAN_TLD\"" | wc -l)
if (($x!=0)); then
	echo "A zone for TLD $LAN_TLD has already been defined. Cowardly refusing to write another."
else
	echo -e "zone \"$LAN_TLD\" {" >> $BIND_CFG
	echo -e "        type master;" >> $BIND_CFG
	echo -e "        file \"$BIND_TLD\";" >> $BIND_CFG
	echo -e "};" >> $BIND_CFG
	echo -e "\n" >> $BIND_CFG
	echo -e "zone \"$BIND_TLD_REV\" {" >> $BIND_CFG
	echo -e "        type master;" >> $BIND_CFG
	echo -e "        file \"$BIND_TLD_REV_FILE\";" >> $BIND_CFG
	echo -e "};" >> $BIND_CFG
	echo -e "\n" >> $BIND_CFG
	echo -e "logging {" >> $BIND_CFG
	echo -e "    channel query.log {" >> $BIND_CFG
	echo -e "        file \"$BIND_LOGS\";" >> $BIND_CFG
	echo -e "        severity debug 3;" >> $BIND_CFG
	echo -e "    };" >> $BIND_CFG
	echo -e "    category queries { query.log; };" >> $BIND_CFG
	echo -e "};" >> $BIND_CFG
	echo "Updated bind local zones file for $LAN_TLD"
fi

mkdir -p $BIND_DIR

if [ -e $BIND_TLD_REV_FILE ]; then
	echo "A rev dns zone already exists for $BIND_TLD_REV, won't create another"
else
	echo "@ IN SOA ns1.$LAN_TLD. admin.example.com. (" >> $BIND_TLD_REV_FILE
	echo "                        2006081401;" >> $BIND_TLD_REV_FILE
	echo "                        28800; " >> $BIND_TLD_REV_FILE
	echo "                        604800;" >> $BIND_TLD_REV_FILE
	echo "                        604800;" >> $BIND_TLD_REV_FILE
	echo "                        86400 " >> $BIND_TLD_REV_FILE
	echo ")" >> $BIND_TLD_REV_FILE
	echo -e "\n" >> $BIND_TLD_REV_FILE
	echo "                     IN    NS     ns1.$LAN_TLD." >> $BIND_TLD_REV_FILE
	echo "1                    IN    PTR    $LAN_TLD" >> $BIND_TLD_REV_FILE
	echo "Created reverse dns zone $BIND_TLD_REV"
fi


if [ -e $BIND_TLD ]; then
	echo "A dns zone already exists for $BIND_TLD, won't create another"
else
	echo "$LAN_TLD.      IN      SOA     ns1.$LAN_TLD. admin.$LAN_TLD. (" >> $BIND_TLD
	echo "                                                        2006081401" >> $BIND_TLD
	echo "                                                        28800" >> $BIND_TLD
	echo "                                                        3600" >> $BIND_TLD
	echo "                                                        604800" >> $BIND_TLD
	echo "                                                        38400" >> $BIND_TLD
	echo " )" >> $BIND_TLD
	echo -e "\n" >> $BIND_TLD
	echo "$LAN_TLD.      IN      NS              ns1.$LAN_TLD." >> $BIND_TLD
	echo -e "\n" >> $BIND_TLD
	echo "ns1              IN      A       192.168.0.1" >> $BIND_TLD
	echo "router           IN      A       192.168.0.1" >> $BIND_TLD
	#TODO: Configure WAN IP?

	echo "Created dns zone $BIND_TLD"
fi

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

