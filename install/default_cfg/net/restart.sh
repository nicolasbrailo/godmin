#/bin/bash

echo "Restarting router services" >> $ROUTER_LOG
date >> $ROUTER_LOG
source $START_FWDS_SCRIPT_FILE >> $ROUTER_LOG
source $FWDS_SCRIPT_FILE >> $ROUTER_LOG

