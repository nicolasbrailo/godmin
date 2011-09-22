
function write_cfg_from_template()
{
	template_file=$1
	dest_file=$2
	keywords=$3

	sed_chain=""
	for var_name in $keywords; do
		# Get the value held in $$var_name
		eval "val=\$$var_name"
		# Save the escaped value (for paths only)
		escaped_val=$(echo $val | sed 's/\//\\\//g')
		# Build a SED expr to apply
		sed_expr="s/\\\$$var_name/$escaped_val/g"
		# Build the SED command
		sed_cmd="sed \"$sed_expr\""
		# Append this command to the sed chain
		sed_chain="$sed_chain | $sed_cmd"
	done

	# Execute the command. It should have this form:
	# cat TEMPLATE_FILE | \
	#					sed "s/\$PATTERN1/VALUE1/g" | \
	#					... > DEST_FILE
	eval "cat $template_file $sed_chain > $dest_file"
}


function update_apparmor()
{
	apparmor_cfg=$1
	target_dir=$2

	if [ ! -f $apparmor_cfg ]; then
		echo "No apparmor detected ($apparmor_cfg)."
		echo "	Remember to configure it, if you install it later"
		return;
	fi

	x=$(cat $apparmor_cfg | grep $target_dir | wc -l)
	if (( $x!=0 )); then
		echo "Apparmor seems already configured for $target_dir, won't alter it"
		return;
	fi

	# Find the closing brace for the apparmor cfg
	# TODO: could there be anything after this closing brace? If so, we'll lose it
	ln=$( cat $apparmor_cfg | grep -n '}' | tail -n1 | awk -F':' '{print $1}' )
	# Write everything but the closing brace
	head -n$(($ln-1)) $apparmor_cfg > /tmp/apparmor_cfg

	# Update the config
	echo -e "\t$target_dir/** rw," >> /tmp/apparmor_cfg
	echo -e "\t$target_dir/ rw," >> /tmp/apparmor_cfg
	echo "}" >> /tmp/apparmor_cfg 
	mv /tmp/apparmor_cfg $apparmor_cfg

	/./etc/init.d/apparmor restart 1>/dev/null 2>/dev/null
	echo "Apparmor configuration updated for $target_dir"
}


