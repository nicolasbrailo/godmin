#!/bin/sh -e

### BEGIN INIT INFO
# Provides:          router_services
# Required-Start:    $remote_fs
# Required-Stop:     $remote_fs
# Default-Start:     2 3 4 5
# Default-Stop:      
# Short-Description: Start router services
# Description:       Starts router services, including networking and iptables
#							forwards.
### END INIT INFO

source $RESTART_SCRIPT

