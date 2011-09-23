#!/bin/bash

# Cleanup
iptables --table nat --flush
iptables --flush

echo 1 > $PROC_IP_FWD_CFG
iptables --table nat --append POSTROUTING --out-interface $WAN_IFACE -j MASQUERADE
iptables --append FORWARD --in-interface $LAN_IFACE -j ACCEPT

