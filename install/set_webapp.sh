#!/bin/bash

# argv
ROUTER_HOME=$1
LAN_IP=$2
LISTEN_PORT=$3

# Webapp config
WEBROOT_SRC=../godmin_webapp
WEBROOT=$ROUTER_HOME/godmin_webapp
CONFIG_PHP=$WEBROOT/config.php
SUDO_SCRIPTS_DIR=$ROUTER_HOME/sudo_scripts
SUDO_SCRIPTS_DIR_SRC=../sudo_scripts

# System config
WEBROOT_HTACCESS=$WEBROOT/.htaccess
HTPWD_FILE=$ROUTER_HOME/htpasswd
WEBAPP_USER=www-data
SUDOERS_FILE=/etc/sudoers
VHOST_FILE=/etc/apache2/sites-available/default

# Templates
CONFIG_PHP_TMPL=default_cfg/webapp/config.php
SUDOERS_FILE_TMPL=default_cfg/webapp/sudoers
HTACCESS_TMPL=default_cfg/webapp/htaccess
HTPWD_TMPL=default_cfg/webapp/htpasswd
VHOST_TMPL=default_cfg/webapp/apache.cfg
TMPL_VARS="ROUTER_HOME WEBAPP_USER LAN_IP_PREFIX LISTEN_PORT"

lan_ip1="$(echo $LAN_IP|awk -F'.' '{print $1}')"
lan_ip2="$(echo $LAN_IP|awk -F'.' '{print $2}')"
lan_ip3="$(echo $LAN_IP|awk -F'.' '{print $3}')"
LAN_IP_PREFIX="$lan_ip1.$lan_ip2.$lan_ip3"


# -- # Copy the webapp
# -- if [ -e $WEBROOT ]; then
# -- 	if [ -e $WEBROOT.bck ]; then
# -- 		warning "$WEBROOT and its backup already exist. Won't continue webapp install."
# -- 		exit
# -- 	else
# -- 		mv $WEBROOT $WEBROOT.bck
# -- 		warning "$WEBROOT already existed. Created a back up at $WEBROOT.bck"
# -- 	fi
# -- fi
# -- 
# -- echo "Copying webapp to $WEBROOT"
# -- cp -r $WEBROOT_SRC $WEBROOT
# -- 
# -- # If there was an old webapp it will be saved to $WEBROOT.bck, so we can
# -- # safely delete whatever config file is there and write our own
# -- rm -f $CONFIG_PHP
# -- write_cfg_from_template $CONFIG_PHP_TMPL $CONFIG_PHP "$TMPL_VARS"
# -- echo "Wrote $CONFIG_PHP"
# -- 
# -- # Write htaccess
# -- rm -f $WEBROOT_HTACCESS
# -- write_cfg_from_template $HTACCESS_TMPL $WEBROOT_HTACCESS "$TMPL_VARS"
# -- write_cfg_from_template $HTPWD_TMPL $HTPWD_FILE "$TMPL_VARS"
# -- echo "Wrote $WEBROOT_HTACCESS and $HTPWD_FILE for access control."


# Configure apache
ok=1
if [ -e $VHOST_FILE ]; then
	if [ -e "$VHOST_FILE.bck" ]; then
		warning "File $VHOST_FILE.bck already exists, so I can't backup $VHOST_FILE"
		warning "	Cowardly refusing to configure Apache, you'll have to do it by hand"
		ok=0
	else
		mv $VHOST_FILE "$VHOST_FILE.bck"
	fi
fi

if ((1==$ok)); then
	write_cfg_from_template $VHOST_TMPL $VHOST_FILE "$TMPL_VARS"
	echo "Configured Apache's default virtualhost for Godmin."
	echo "Godmin will attend your prayers on port $LISTEN_PORT, but you may need to configure Apache to listen on this port in /etc/apache2/ports.conf"
	echo "The webapp should be pasword protected. Enter a password for username admin."
	htpasswd $ROUTER_HOME/htpasswd admin
fi



# Copy the sudo scripts
if [ -e $SUDO_SCRIPTS_DIR ]; then
	warning "$SUDO_SCRIPTS_DIR already exists, won't copy sudo scripts."
	warning "\tThe webapp may not work without these scripts."
else
	cp -r $SUDO_SCRIPTS_DIR_SRC $SUDO_SCRIPTS_DIR
	echo "Copied sudo scripts to $SUDO_SCRIPTS_DIR"
fi

# Write permissions for passwordless sudo for sudo_scripts
for file in $(ls $SUDO_SCRIPTS_DIR_SRC | egrep '.sh$'); do
	script="$ROUTER_HOME/sudo_scripts/$file"

	# Check if this command has already been defined in $SUDOERS_FILE
	x=$(cat $SUDOERS_FILE| grep "$script" | wc -l)
	if (($x!=0)); then
		echo "$script is already present in sudoers file, won't add it."
	else
		# We turn the var name to uppercase because that's how it is in the template
		SCRIPT=$script
		write_cfg_from_template $SUDOERS_FILE_TMPL $SUDOERS_FILE "$TMPL_VARS SCRIPT"
		echo "$script was added to the sudoers file."
	fi
done



