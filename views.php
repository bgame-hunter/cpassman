<?php
/**
 * @file 		views.php
 * @author		Nils Laumaillé
 * @version 	2.0
 * @copyright 	(c) 2009-2011 Nils Laumaillé
 * @licensing 	CC BY-ND (http://creativecommons.org/licenses/by-nd/3.0/legalcode)
 * @link		http://cpassman.org
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if (!isset($_SESSION['CPM'] ) || $_SESSION['CPM'] != 1)
	die('Hacking attempt...');
//Load file
require_once ("views.load.php");

// show TABS permitting to select specific actions
echo '
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">'.$txt['logs_passwords'].'</a></li>
        <li><a href="#tabs-2">'.$txt['deletion'].'</a></li>
        <li><a href="#tabs-3">'.$txt['logs'].'</a></li>
        <li><a href="#tabs-4">'.$txt['renewal_menu'].'</a></li>
    </ul>';

    //TAB 1 - Log password
    echo '
    <div id="tabs-1">
        <p>
        '.$txt['logs_1'].' : <input type="text" id="log_jours" /> <img src="includes/images/asterisk.png" onClick="GenererLog()" style="cursor:pointer;" />
        </p>
        <div id="lien_pdf" style="text-align:center; width:100%; margin-top:15px;"></div>
    </div>';

    //TAB 2 - DELETION
    echo '
    <div id="tabs-2">
        <h3>'.$txt['deletion_title'].'</h3>
        <div id="liste_elems_del" style="margin-left:30px;margin-top:10px;"></div>
    </div>';

    //TAB 3 - LOGS
    echo '
    <div id="tabs-3">
 		<div id="radio_logs">
			<input type="radio" id="radio1" name="radio" onclick="displayLogs(\'connections_logs\',1)" /><label for="radio1">'.$txt['connections'].'</label>
			<input type="radio" id="radio2" name="radio" onclick="displayLogs(\'errors_logs\',1)" /><label for="radio2">'.$txt['errors'].'</label>
			<input type="radio" id="radio3" name="radio" onclick="displayLogs(\'access_logs\',1)" /><label for="radio3">'.$txt['at_shown'].'</label>
		</div>
        <div id="div_show_system_logs" style="margin-left:30px;margin-top:10px;display:none;">
        	<div id="filter_access_logs_div" style="display:none;margin-bottom:10px;">
        		<label for="filter_access_logs" style="font-weight:bold;">'.$txt['find'].':</label>&nbsp;<input type="text" id="filter_access_logs" />
				&nbsp;<img src="includes/images/arrow_refresh.png" onclick="displayLogs(\'access_logs\',1)" />
			</div>
	        <table>
	            <thead>
	                <tr>
	                    <th>'.$txt['date'].'</th>
	                    <th id="th_url">'.$txt['url'].'</th>
	                    <th>'.$txt['label'].'</th>
	                    <th>'.$txt['user'].'</th>
	                </tr>
	            </thead>
	            <tbody id="tbody_logs">
	            </tbody>
	        </table>
	        <div id="log_pages" style="margin-top:10px;"></div>
        </div>
    </div>';

    //TAB 4 - RENEWAL
    echo '
    <div id="tabs-4">
        '.$txt['renewal_selection_text'].'
        <select id="expiration_period">
            <option value="0">'.$txt['expir_today'].'</option>
            <option value="1month">'.$txt['expir_one_month'].'</option>
            <option value="6months">'.$txt['expir_six_months'].'</option>
            <option value="1year">'.$txt['expir_one_year'].'</option>
        </select>
        <img src="includes/images/asterisk.png" style="cursor:pointer;" alt="" onclick="generate_renewal_listing()" />
        <span id="renewal_icon_pdf" style="margin-left:15px;display:none;cursor:pointer;"><img src="includes/images/document-pdf-text.png" alt="" title="'.$txt['generate_pdf'].'" onclick="generate_renewal_pdf()" /></span>
        <div id="list_renewal_items" style="width:700px;margin:10px auto 0 auto;"></div>
        <input type="hidden" id="list_renewal_items_pdf" />
    </div>';

    echo '
</div>
';


?>