<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     block_user_mnet_hosts
 * @category    blocks
 * @author      Edouard Poncelet
 * @author      Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright   2008 Valery Fremaux
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/user_mnet_hosts/locallib.php');

class block_user_mnet_hosts extends block_list {

    public function init() {
        $this->title = get_string('user_mnet_hosts', 'block_user_mnet_hosts');
    }

    public function has_config() {
        return true;
    }

    public function applicable_formats() {
        return array('all' => true, 'mod' => false, 'tag' => false, 'my' => true);
    }

    public function get_content() {
        global $THEME, $CFG, $USER, $PAGE, $OUTPUT, $DB, $SESSION, $COURSE;

        $config = get_config('block_user_mnet_hosts');

        if (empty($config->displaylimit)) {
            set_config('displaylimit', 40, 'block_user_mnet_hosts');
        }

        $PAGE->requires->js('/blocks/user_mnet_hosts/js/jump.js');

        // Only for logged in users!
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        // Impeach local administrator to roam elsewhere.
        if (($USER->username == 'admin') && ($USER->auth == 'manual') && empty($config->localadminoverride)) {
            $this->content = new StdClass();
            $this->content->footer = $OUTPUT->notification(get_string('errorlocaladminconstrainted', 'block_user_mnet_hosts'));
            return $this->content;
        }

        if (!is_enabled_auth('multimnet') && !is_enabled_auth('mnet')) {
            // No need to query anything remote related.
            $this->content = new StdClass();
            $this->content->footer = $OUTPUT->notification(get_string('errormnetauthdisabled', 'block_user_mnet_hosts'));
            return $this->content;
        }

        $systemcontext = context_system::instance();

        // Check for outgoing roaming permission first.
        if (!has_capability('moodle/site:mnetlogintoremote', $systemcontext, null, false)) {
            if (has_capability('moodle/site:config', $systemcontext)) {
                $this->content = new StdClass();
                $this->content->footer = get_string('errornocapacitytologremote', 'block_user_mnet_hosts');
            }
            return '';
        }

        if ($this->content !== null) {
            return $this->content;
        }

        $hosts = user_mnet_hosts_get_hosts();
        $mnetaccesses = user_mnet_hosts_get_access_fields();

        $this->content = new StdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if ($hosts) {
            $maxhosts = count($hosts);
            $i = 0;
            $j = 0;
            foreach ($hosts as $host) {
                $i++;
                if ($maxhosts > $config->displaylimit) {

                    $SESSION->umhfilter = optional_param('umhfilter', @$SESSION->umhfilter, PARAM_TEXT);

                    if (!empty($SESSION->umhfilter)) {
                        if (!preg_match('/'.preg_quote($SESSION->umhfilter).'/i', $host->name)) {
                            continue;
                        }
                    }
                }

                // Loop control : i all hosts / j visible only.
                $j++;
                if (($maxhosts > $config->displaylimit) && ($j >= $config->displaylimit)) {
                    if ($i < $maxhosts) {
                        $this->content->icons[] = '';
                        $this->content->items[] = get_string('usefiltertoreduce', 'block_user_mnet_hosts');
                    }
                    break;
                }

                // Implement user access filtering.
                $hostaccesskey = strtolower(user_mnet_hosts_make_accesskey($host->wwwroot, false));

                if ($host->application == 'moodle' || empty($config->maharapassthru)) {
                    if (empty($mnetaccesses[$hostaccesskey]) &&
                            !has_capability('block/user_mnet_hosts:accessall', context_system::instance())) {
                        continue;
                    }
                }

                $icon = $OUTPUT->pix_icon('/i/'.$host->application.'_host', get_string('server', 'block_mnet_hosts'));

                $this->content->icons[] = $icon;

                $cleanname = preg_replace('/^https?:\/\//', '', $host->name);
                $cleanname = str_replace('.', '', $cleanname);
                $target = '';
                if ($config->newwindow) {
                    $target = ' target="'.$cleanname.'" ';
                    $target = ' target="_blank" ';
                }

                if ($host->id == $USER->mnethostid) {
                    $this->content->items[] = '<a title="'.s($host->name).'"
                                                  href="'.$host->wwwroot.'" '.$target.'>'.s($host->name).'</a>';
                } else {
                    if (is_enabled_auth('multimnet')) {
                        $jshandler = 'javascript:multijump(\''.$CFG->wwwroot.'\','.$host->id.');';
                        $this->content->items[] = '<a title="'.s($host->name).'" href="'.$jshandler.'">'.s($host->name).'</a>';
                    } else {
                        $jshandler = 'javascript:standardjump(\''.$CFG->wwwroot.'\','.$host->id.');';
                        $this->content->items[] = '<a title="'.s($host->name).'" href="'.$jshandler.'">'.s($host->name).'</a>';
                    }
                }
            }
        } else {
            $this->content->footer = $OUTPUT->notification(get_string('nohostsforyou', 'block_user_mnet_hosts'));
        }
        if (count($hosts) > $config->displaylimit) {
            $footer = '<form name="umhfilterform" action="#">';
            $footer .= '<input type="hidden" name="id" value="'.$COURSE->id.'" />';
            $footer .= '<input class="form-minify" type="text" name="umhfilter" value="'.(@$SESSION->umhfilter).'" />';
            $filterstr = get_string('filter', 'block_user_mnet_hosts');
            $footer .= '<input class="form-minify" type="submit" name="go" value="'.$filterstr.'" />';
            $footer .= '</form>';
            $this->content->footer = $footer;
        }
        return $this->content;
    }

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
    public function remote_user_mnet_check($remoteuser, $fromwwwwroot) {
    }
}

