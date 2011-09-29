#!/bin/bash

# argv
ROUTER_HOME=$1
LAN_IP=$2
LISTEN_PORT=$3
CAPTIVE_PORTAL_PORT=$4

# Webapp config
WEBROOT_SRC=../godmin_webapp
CAPTIVE_PORTAL_SRC=../captive_portal
WEBROOT=$ROUTER_HOME/godmin_webapp
CAPTIVE_PORTAL=$ROUTER_HOME/captive_portal
CONFIG_PHP=$WEBROOT/config.php
SUDO_SCRIPTS_DIR=$ROUTER_HOME/sudo_scripts
SUDO_SCRIPTS_DIR_SRC=../sudo_scripts

# System config
WEBROOT_HTACCESS=$WEBROOT/.htaccess
HTPWD_FILE=$ROUTER_HOME/htpasswd
WEBAPP_USER=www-data
SUDOERS_FILE=/etc/sudoers
GODMIN_VHOST_FILE=/etc/apache2/sites-available/godmin
CAPTIVE_PORTAL_VHOST_FILE=/etc/apache2/sites-available/captive_portal
APACHE_SITES_ENABLED=/etc/apache2/sites-enabled

# Templates
CONFIG_PHP_TMPL=default_cfg/webapp/config.php
SUDOERS_FILE_TMPL=default_cfg/webapp/sudoers
HTACCESS_TMPL=default_cfg/webapp/htaccess
HTPWD_TMPL=default_cfg/webapp/htpasswd
GODMIN_VHOST_TMPL=default_cfg/webapp/godmin.cfg
CAPTIVE_PORTAL_VHOST_TMPL=default_cfg/webapp/captive_portal.cfg
TMPL_VARS="ROUTER_HOME WEBAPP_USER LAN_IP_PREFIX LISTEN_PORT CAPTIVE_PORTAL CAPTIVE_PORTAL_PORT"

lan_ip1="$(echo $LAN_IP|awk -F'.' '{print $1}')"
lan_ip2="$(echo $LAN_IP|awk -F'.' '{print $2}')"
lan_ip3="$(echo $LAN_IP|awk -F'.' '{print $3}')"
LAN_IP_PREFIX="$lan_ip1.$lan_ip2.$lan_ip3"


# Copy the webapp
if [ -e $WEBROOT ]; then
	if [ -e $WEBROOT.bck ]; then
		warning "$WEBROOT and its backup already exist. Won't continue webapp install."
		exit
	else
		mv $WEBROOT $WEBROOT.bck
		warning "$WEBROOT already existed. Created a back up at $WEBROOT.bck"
	fi
fi

echo "Copying webapp to $WEBROOT"
cp -r $WEBROOT_SRC $WEBROOT

if [ ! -e $CAPTIVE_PORTAL ]; then
	echo "Copying the captive portal to $CAPTIVE_PORTAL"
	cp -r $CAPTIVE_PORTAL_SRC $CAPTIVE_PORTAL
else
	echo "There's a captive portal at $CAPTIVE_PORTAL. Won't create another one"
fi

# If there was an old webapp it will be saved to $WEBROOT.bck, so we can
# safely delete whatever config file is there and write our own
rm -f $CONFIG_PHP
write_cfg_from_template $CONFIG_PHP_TMPL $CONFIG_PHP "$TMPL_VARS"
echo "Wrote $CONFIG_PHP"

# Write htaccess
rm -f $WEBROOT_HTACCESS
write_cfg_from_template $HTACCESS_TMPL $WEBROOT_HTACCESS "$TMPL_VARS"
write_cfg_from_template $HTPWD_TMPL $HTPWD_FILE "$TMPL_VARS"
echo "Wrote $WEBROOT_HTACCESS and $HTPWD_FILE for access control."


# Configure apache
if [ ! -e $GODMIN_VHOST_FILE ]; then
	write_cfg_from_template $GODMIN_VHOST_TMPL $GODMIN_VHOST_FILE "$TMPL_VARS"
	echo "Configured Apache's default virtualhost for Godmin."
	echo "Godmin will attend your prayers on port $LISTEN_PORT, but you may need to configure Apache to listen on this port in /etc/apache2/ports.conf"
else
	warning "File $GODMIN_VHOST_FILE already exists, so I won't write a new one"
fi

if [ ! -e $CAPTIVE_PORTAL_VHOST_FILE ]; then
	write_cfg_from_template $CAPTIVE_PORTAL_VHOST_TMPL $CAPTIVE_PORTAL_VHOST_FILE "$TMPL_VARS"
	echo "Configured Apache's default virtualhost for the captive portal."
	echo "The captive portal will listen on port $CAPTIVE_PORTAL_PORT, but you may need to configure Apache to listen on this port in /etc/apache2/ports.conf"
else
	warning "File $CAPTIVE_PORTAL_VHOST_FILE already exists, so I won't write a new one"
fi

echo "The webapp should be pasword protected. Enter a password for username admin."
htpasswd $ROUTER_HOME/htpasswd admin


# Enable both sites, the CP and Godmin
ln -s $CAPTIVE_PORTAL_VHOST_FILE $APACHE_SITES_ENABLED/001-captive_portal
ln -s $GODMIN_VHOST_FILE $APACHE_SITES_ENABLED/000-godmin

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



