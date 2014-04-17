User Mnet Host block is an extension of Mnet_Host bloc
allowing access controlled display of MNET accesses depending on the
user's profile settings.

Access to hosts are easily manageable by access dedicated fields in user profile
so access configuration can be managed through user bulk imports.

# Version notes
#########################

2.4 (2013020801) Adds Mahara support, with a "pass all users" setting for Mahara portfolio.
Fixes a capability control for "access all sites". Now local block level capability.

Install ans setup
#################

1. Install the block as any other blocks copying the directory in /blocks dir
of your Moodle installation, then browse to notifications to register it.

2. On a configured MNET, browse to the plugin settings page and follow the link to :
"Synchronising access control fields to the network configuration".

Run the synchronisation to create expected fields. 

Conditions for a user jumping 
#############################

The user will only see nodes he is allowed to jump to depending on his profile settings.
Only SSO accessible nodes can be marked and synced.

Valuable patchs for improved experience
#######################################

User mnet control is much more consistant when unifying the profile on all MNET nodes.
This can be obtained adding a key patch that adds custom field propagation through MNET.

File patched : /auth/mnet/auth.php

Patch part 1 : Add custom profile fields to tranmitted userdata

Where : 

 in function user_authorize()
 before : 
        $userdata['myhosts'] = array();
        if ($courses = enrol_get_users_courses($user->id, false)) {
            $userdata['myhosts'][] = array('name'=> $SITE->shortname, 'url' => $CFG->wwwroot, 'count' => count($courses));
        }

Patch content :

// PATCH : Get user's custom fields and aggregate them to the user profile
$sql = "
    SELECT
        f.shortname,
        d.data
    FROM
        {user_info_field} f,
        {user_info_data} d
    WHERE
        d.userid = ? AND
        f.id = d.fieldid
";
if ($profilefields = $DB->get_records_sql_menu($sql, array($mnet_session->userid))){
    foreach($profilefields as $fieldname => $fielddata){
        $userdata["profile_field_{$fieldname}"] = $fielddata;
    }
}        
// /PATCH


Patch part 2 : Getting custom profile back and updating matching field definitions

Where : 
in function update_mnet_session()
before             
		$localuser->{$key} = $val;

in loop : 
        // update the local user record with remote user data
        foreach ((array) $remoteuser as $key => $val) {

Patch content :

// PATCH : capture profile fields, check if corresponding entry is defined and update data
if (preg_match('/^profile_field_(.*)/', $key, $matches)){
    $fieldname = $matches[1];
    if ($field = $DB->get_record('user_info_field', array('shortname' => $fieldname))){
    	$datum = new StdClass;
        $datum->fieldid = $field->id;
        $datum->userid = $localuser->id;
        $datum->data = $val;
        if ($oldrecord = $DB->get_record('user_info_data', array('fieldid' => $field->id, 'userid' => $localuser->id))){
            $datum->id = $oldrecord->id;
            $DB->update_record('user_info_data', $datum);
        } else {
            $DB->insert_record('user_info_data', $datum);
        }
    }
}

// /PATCH

Security note
#############

This block only provides significant targets to jump to to users. As users must have a global 
moodle/site:mnetlogintoremote capability, once it is allowed, a user might be able to pass through
unpublished acceses. 

A future provision for a back-check is foreseen, but still not developed.

Strict exclusion of some user should be obtained using SSO Access Control in Mnet admin, but might
not be usable for mass setup.

Local admin restrictions
########################

User Mnet Host block will not allow any local admin to roam through MNET doors. This is because
the admin account named "admin" is assigned usually to distinct physical users, and yet be named
the same.

In a Moodle network, you will surely want having a master admin that governs them all. In this case
the user_mnet_host block has a special override feature that allows the admin account to use the mnet doors
by derogation. 

you will need to add a $CFG special key to the config file, but only on the site your global admin
resides : 

if (preg_match('#http://mymainpattern#', $CFG->wwwroot)){
	$CFG->user_mnet_hosts_admin_override = 1;
}

