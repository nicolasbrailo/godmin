#!/bin/bash

# argv
ROUTER_HOME=$1

# Webapp config
WEBROOT_SRC=../godmin_webapp
WEBROOT=$ROUTER_HOME/godmin_webapp/
CONFIG_PHP=$WEBROOT/config.php
SUDO_SCRIPTS_DIR=$WEBROOT/sudo_scripts

# System config
WEBAPP_USER=www-data
SUDOERS_FILE=/etc/sudoers

# Templates
CONFIG_PHP_TMPL=default_cfg/webapp/config.php
SUDOERS_TMPL=default_cfg/webapp/sudoers
TMPL_VARS="ROUTER_HOME WEBAPP_USER"



# Copy the webapp
if [ -e $WEBROOT ]; then
	if [ -e $WEBROOT.bck ]; then
		warning "Error: $WEBROOT and its backup already exist. Won't continue webapp install."
		exit
	else
		mv $WEBROOT $WEBROOT.bck
		warning "$WEBROOT already existed. Created a back up at $WEBROOT.bck"
	fi
fi

echo "Copying webapp to $WEBROOT"
cp -r $WEBROOT $WEBROOT_SRC

# If there was an old webapp it will be saved to $WEBROOT.bck, so we can
# safely delete whatever config file is there and write our own
rm -f $CONFIG_PHP
write_cfg_from_template $CONFIG_PHP $CONFIG_PHP_TMPL "$TMPL_VARS"
echo "Wrote $CONFIG_PHP"



# Copy the sudo scripts
if [ -e $SUDO_SCRIPTS_DIR ]; then
	warning "$SUDO_SCRIPTS_DIR already exists, won't copy sudo scripts."
	warning "\tThe webapp may not work without these scripts."
else
	cp -r $SUDO_SCRIPTS_DIR_SRC $SUDO_SCRIPTS_DIR
	echo "Copyied sudo scripts to $SUDO_SCRIPTS_DIR"
fi

# Write permissions for passwordless sudo for sudo_scripts
for file in $(ls $SUDO_SCRIPTS_DIR_SRC | egrep '.sh$'); do
	script="$ROUTER_HOME/sudo_scripts/$file"

	# Check if this command has already been defined in $SUDOERS_FILE
	x=$(cat $SUDOERS_FILE| grep "$script" | wc -l)
	if (($x!=0)); then
		echo "$script is already present in sudoers file, won't add it."
	else
		write_cfg_from_template $SUDOERS_FILE $SUDOERS_TMPL "$TMPL_VARS script"
		echo "$script was added to the sudoers file."
	fi
done



