<?PHP //$Id: block_user_mnet_hosts.php,v 1.15 2012-02-01 09:28:36 vf Exp $

class block_user_mnet_hosts extends block_list {
    function init() {
        $this->title = get_string('user_mnet_hosts','block_user_mnet_hosts') ;
        $this->version = 2007101509;
    }

    function has_config() {
        return false;
    }

    function applicable_formats() {
        if (has_capability('moodle/site:mnetlogintoremote', get_context_instance(CONTEXT_SYSTEM), NULL, false)) {
            return array('all' => true, 'mod' => false, 'tag' => false, 'my' => true);
        } else {
            return array('site' => true, 'my' => true);
        }
    }

    function get_content() {
        global $THEME, $CFG, $USER;
        
        require_js ($CFG->wwwroot.'/blocks/user_mnet_hosts/js/jump.js');


        // only for logged in users!
        if (!isloggedin() || isguest()) {
            return false;
        }

        if (!is_enabled_auth('mnet')) {
            // no need to query anything remote related
            debugging( 'mnet authentication plugin is not enabled', DEBUG_ALL );
            return '';
        }

        // check for outgoing roaming permission first
        if (!has_capability('moodle/site:mnetlogintoremote', get_context_instance(CONTEXT_SYSTEM), NULL, false)) {
            // debugging('Missing capacity to connect');
            return '';
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        // TODO: Test this query - it's appropriate? It works?
        // get the hosts and whether we are doing SSO with them
        $sql = "
             SELECT DISTINCT 
                 h.id, 
                 h.name,
                 h.wwwroot,
                 a.name as application,
                 a.display_name
             FROM 
                 {$CFG->prefix}mnet_host h,
                 {$CFG->prefix}mnet_application a,
                 {$CFG->prefix}mnet_host2service h2s_IDP,
                 {$CFG->prefix}mnet_service s_IDP,
                 {$CFG->prefix}mnet_host2service h2s_SP,
                 {$CFG->prefix}mnet_service s_SP
             WHERE
                 h.id != '{$CFG->mnet_localhost_id}' AND
                 h.id = h2s_IDP.hostid AND
                 h.deleted = 0 AND
                 h.applicationid = a.id AND
                 h2s_IDP.serviceid = s_IDP.id AND
                 s_IDP.name = 'sso_idp' AND
                 h2s_IDP.publish = '1' AND
                 h.id = h2s_SP.hostid AND
                 h2s_SP.serviceid = s_SP.id AND
                 s_SP.name = 'sso_idp' AND
                 h2s_SP.publish = '1'
             ORDER BY
                 a.display_name,
                 h.name";

        $hosts = get_records_sql($sql);

        // if mnet access profile does not exist, setup profile
        
        if (!get_records_select('user_info_field', " name LIKE 'access%' ")){
            
           // TODO : Initialize mnetaccess profile data
        }

        // get user profile fields for access to hosts
        
        $sql = "
            SELECT
                uif.shortname,
                data
            FROM
                {$CFG->prefix}user_info_data uid,
                {$CFG->prefix}user_info_field uif
            WHERE
                uid.userid = $USER->id AND
                uid.fieldid = uif.id AND
                uif.shortname LIKE 'access%'
        ";

        $mnet_accesses = array();


        if ($usermnetaccessfields = get_records_sql_menu($sql)){        
            foreach($usermnetaccessfields as $key => $datum){
                $key = str_replace('access', '', $key);
                $mnet_accesses[str_replace('-', '', strtolower($key))] = str_replace('-', '', $datum);
            }
        }
        
        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if ($hosts) {
            foreach ($hosts as $host) {
                
	            // implemente user access filtering
	            preg_match('/https?:\/\/([^.]*)/', $host->wwwroot, $matches);
	            $hostprefix = strtolower($matches[1]);

	            if (empty($mnet_accesses[strtolower(str_replace('-', '', $hostprefix))]) && !has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM,0))){
	                continue;
	            }
                
	            $icon  = '<img src="'.$CFG->pixpath.'/i/'.$host->application.'_host.gif"'.
	                ' class="icon" alt="'.get_string('server', 'block_mnet_hosts').'" />';

                $this->content->icons[] = $icon;


                if ($host->id == $USER->mnethostid) {
                    $this->content->items[]="<a title=\"" .s($host->name).
                        "\" href=\"{$host->wwwroot}\">". s($host->name) ."</a>";
                } else {
                    $this->content->items[]="<a title=\"" .s($host->name).
                        "\" href=\"javascript:jump('$CFG->wwwroot','$host->id')\">" . s($host->name) ."</a>";
                        
                }
            }
        }
        return $this->content;
    }
}

?>