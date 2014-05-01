<?PHP //$Id: block_user_mnet_hosts.php,v 1.1.1.1.2.2 2013-07-25 12:04:51 mo2dlemaster Exp $

require_once($CFG->dirroot.'/blocks/user_mnet_hosts/locallib.php');

class block_user_mnet_hosts extends block_list {
    function init() {
        $this->title = get_string('user_mnet_hosts', 'block_user_mnet_hosts') ;
    }

    function has_config() {
        return true;
    }

	function applicable_formats() {
		return array('all' => true, 'mod' => false, 'tag' => false, 'my' => true);
	}

	function get_content() {
		global $THEME, $CFG, $USER, $PAGE, $OUTPUT, $DB;
		
		$PAGE->requires->js('/blocks/user_mnet_hosts/js/jump.js');
		
		// only for logged in users!
		if (!isloggedin() || isguestuser()) {
			return false;
		}
		
		// impeach local administrator to roam elsewhere
		if (($USER->username == 'admin') && ($USER->auth == 'manual') && empty($CFG->user_mnet_hosts_admin_override)){
			$this->content = new StdClass();
			$this->content->footer = $OUTPUT->notification(get_string('errorlocaladminconstrainted', 'block_user_mnet_hosts'));
			return $this->content;
		}

		if (!is_enabled_auth('multimnet') && !is_enabled_auth('mnet')) {
			// no need to query anything remote related
			$this->content = new StdClass();
			$this->content->footer = $OUTPUT->notification(get_string('errormnetauthdisabled', 'block_user_mnet_hosts'));
			return $this->content;
		}
		
		$systemcontext = context_system::instance();
		
		// check for outgoing roaming permission first
		if (!has_capability('moodle/site:mnetlogintoremote', $systemcontext, NULL, false)) {
			if (has_capability('moodle/site:config', $systemcontext)){
				$this->content = new StdClass();
				$this->content->footer = get_string('errornocapacitytologremote', 'block_user_mnet_hosts');
			}
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
				{mnet_host} h,
				{mnet_application} a,
				{mnet_host2service} h2s_IDP,
				{mnet_service} s_IDP,
				{mnet_host2service} h2s_SP,
				{mnet_service} s_SP
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

        $hosts = $DB->get_records_sql($sql);
        
        // if mnet access profile does not exist, setup profile
        if (!$DB->get_records_select('user_info_field', " name LIKE 'access%' ")){
           // TODO : Initialize mnetaccess profile data
        }

        // get user profile fields for access to hosts
        $sql = "
            SELECT
                uif.shortname,
                data
            FROM
                {user_info_data} uid,
                {user_info_field} uif
            WHERE
                uid.userid = $USER->id AND
                uid.fieldid = uif.id AND
                uif.shortname LIKE 'access%'
        ";

        $mnet_accesses = array();

        if ($usermnetaccessfields = $DB->get_records_sql_menu($sql)){
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
	            $hostaccesskey = strtolower(user_mnet_hosts_make_accesskey($host->wwwroot, false));

				if ($host->application == 'moodle' || empty($CFG->block_u_m_h_maharapassthru)){
		            if (empty($mnet_accesses[$hostaccesskey]) && !has_capability('block/user_mnet_hosts:accessall', context_block::instance($this->instance->id))){
		                continue;
		            }
		        }

	            $icon  = '<img src="'.$OUTPUT->pix_url('/i/'.$host->application.'_host').'" class="icon" alt="'.get_string('server', 'block_mnet_hosts').'" />';

                $this->content->icons[] = $icon;

				$cleanname = preg_replace('/^https?:\/\//', '', $host->name);
				$cleanname = str_replace('.', '', $cleanname);
				$target = '';
				if (@$CFG->user_mnet_hosts_new_window){
					$target = " target=\"{$cleanname}\" "  ;
					$target = " target=\"_blank\" "  ;
				}

                if ($host->id == $USER->mnethostid) {
                    $this->content->items[]="<a title=\"" .s($host->name).
                        "\" href=\"{$host->wwwroot}\" $target >". s($host->name) ."</a>";
                } else {
                	if (is_enabled_auth('multimnet')){
	                    $this->content->items[]="<a title=\"" .s($host->name).
	                        "\" href=\"javascript:multijump('$CFG->wwwroot','$host->id')\">" . s($host->name) ."</a>";
	                } else {
	                    $this->content->items[]="<a title=\"" .s($host->name).
	                        "\" href=\"javascript:standardjump('$CFG->wwwroot','$host->id')\">" . s($host->name) ."</a>";
	                }
                }
            }
        } else {
        	$this->content->footer = $OUTPUT->notification(get_string('nohostsforyou', 'block_user_mnet_hosts'));
        }
        return $this->content;
    }

	// RPC dedicated functions
	/**
	* checks locally if an incoming user has remote provision to come in  
	* Call needs to be hooked on "login" access (and mnet landing) to
	* avoid back door effect.
	* Called by : the landing node
	* Checked in : the local node
	* @param $remoteuser : structure containing username, userremoteroot identity
	* @param $fromwwwroot : remote caller identity
	*
	* TODO : implement this security check.
	* Register it
	*/
    
	function remote_user_mnet_check($remoteuser, $fromwwwwroot){
	}
}

