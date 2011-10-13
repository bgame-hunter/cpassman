<?php
/**
 * @file 		items.queries.php
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

session_start();
if (!isset($_SESSION['CPM'] ) || $_SESSION['CPM'] != 1)
	die('Hacking attempt...');


require_once('../includes/language/'.$_SESSION['user_language'].'.php');
include('../includes/settings.php');
require_once('../includes/include.php');
header("Content-type: text/html; charset=utf-8");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
include('main.functions.php');

$allowed_tags = '<b><i><sup><sub><em><strong><u><br><br /><a><strike><ul><blockquote><blockquote><img><li><h1><h2><h3><h4><h5><ol><small><font>';

//Connect to mysql server
require_once("class.database.php");
$db = new Database($server, $user, $pass, $database, $pre);
$db->connect();

//Do asked action
if ( isset($_POST['type']) ){
    switch($_POST['type'])
    {
        /*
        * CASE
        * creating a new ITEM
        */
        case "new_item":
            //decrypt and retreive data in JSON format
            require_once '../includes/libraries/crypt/aes.class.php';     // AES PHP implementation
            require_once '../includes/libraries/crypt/aesctr.class.php';  // AES Counter Mode implementation
            $data_received = json_decode((AesCtr::decrypt($_POST['data'], $_SESSION['key'], 256)), true);

            //Prepare variables
            $label = htmlspecialchars_decode($data_received['label']);
            $url = htmlspecialchars_decode($data_received['url']);
            $pw = htmlspecialchars_decode($data_received['pw']);
            $login = htmlspecialchars_decode($data_received['login']);
            $tags = htmlspecialchars_decode($data_received['tags']);

        	if (!empty($pw)) {
	            //;check if element doesn't already exist
	            $item_exists = 0;
	            $new_id = "";
	        	$data = $db->fetch_row("SELECT COUNT(*) FROM ".$pre."items WHERE label = '".addslashes($label)."' AND inactif=0");
	        	if ( $data[0] != 0 ){
	        		$item_exists = 1;
	        	}else{
	        		$item_exists = 0;
	        	}

	            if ( (isset($_SESSION['settings']['duplicate_item']) && $_SESSION['settings']['duplicate_item'] == 0 && $item_exists == 0)
	            	||
	            	(isset($_SESSION['settings']['duplicate_item']) && $_SESSION['settings']['duplicate_item'] == 1)
	            ){
	            	//set key if non personal item
	            	if($data_received['is_pf'] != 1){
	            		//generate random key
	            		$random_key = GenerateKey();
	            		$pw = $random_key.$pw;
	            	}

		            //encrypt PW
		            if ($data_received['salt_key_set']==1 && isset($data_received['salt_key_set']) && $data_received['is_pf']==1 && isset($data_received['is_pf'])){
		                $pw = encrypt($pw,mysql_real_escape_string(stripslashes($_SESSION['my_sk'])));
		                $resticted_to = $_SESSION['user_id'];
		            }else
		                $pw = encrypt($pw);

		            //ADD item
		            $new_id = $db->query_insert(
		                'items',
		                array(
		                    'label' => $label,
		                    'description' => $data_received['description'],
		                    'pw' => $pw,
		                    'url' => $url,
		                    'id_tree' => $data_received['categorie'],
		                    'login' => $login,
		                    'inactif' => '0',
							'restricted_to' => isset($data_received['restricted_to']) ? $data_received['restricted_to'] : '',
							'perso' => ( $data_received['salt_key_set']==1 && isset($data_received['salt_key_set']) && $data_received['is_pf']==1 && isset($data_received['is_pf'])) ? '1' : '0',
							'anyone_can_modify' => (isset($data_received['anyone_can_modify']) && $data_received['anyone_can_modify'] == "on") ? '1' : '0'
		                )
		            );

	            	//Store generated key
	            	if($data_received['is_pf'] != 1){
		            	$db->query_insert(
			            	'keys',
			            	array(
			            	    'table' => 'items',
			            	    'id' => $new_id,
			            	    'rand_key' => $random_key
			            	)
		            	);
	            	}

		        	//Manage retriction_to_roles
		        	if (isset($data_received['restricted_to_roles'])) {
		        		foreach (array_filter(explode(';', $data_received['restricted_to_roles'])) as $role){
		        			$db->query_insert(
			         			'restriction_to_roles',
			         			array(
			         			    'role_id' => $role,
			         			    'item_id' => $new_id
			         			)
		        			);
		        		}
		        	}

		            //log
		            $db->query_insert(
		                'log_items',
		                array(
		                    'id_item' => $new_id,
		                    'date' => mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('y')),
		                    'id_user' => $_SESSION['user_id'],
		                    'action' => 'at_creation'
		                )
		            );

		            //Add tags
		            $tags = explode(' ', $tags);
		            foreach($tags as $tag){
		                if ( !empty($tag) )
		                    $db->query_insert(
		                        'tags',
		                        array(
		                            'item_id' => $new_id,
		                            'tag' => strtolower($tag)
		                        )
		                    );
		            }

		            // Check if any files have been added
		            if ( !empty($data_received['random_id_from_files']) ){
		                $sql = "SELECT id
		                        FROM ".$pre."files
		                        WHERE id_item=".$data_received['random_id_from_files'];
		                $rows = $db->fetch_all_array($sql);
		                foreach ($rows as $reccord){
		                    //update item_id in files table
		                    $db->query_update(
		                        'files',
		                        array(
		                            'id_item' => $new_id
		                        ),
		                        "id='".$reccord['id']."'"
		                    );
		                }
		            }

		            //Update CACHE table
		            UpdateCacheTable("add_value",$new_id);

		            //Announce by email?
		            if ( $data_received['annonce'] == 1 ){
		                require_once("class.phpmailer.php");
		                //envoyer email
		                $destinataire= explode(';',$data_received['diffusion']);
		                foreach($destinataire as $mail_destinataire){
		                    //envoyer ay destinataire
		                    $mail = new PHPMailer();
		                    $mail->SetLanguage("en","../includes/libraries/phpmailer/language");
		                    $mail->IsSMTP();                                   // send via SMTP
		                    $mail->Host     = $smtp_server; // SMTP servers
		                    $mail->SMTPAuth = $smtp_auth;     // turn on SMTP authentication
		                    $mail->Username = $smtp_auth_username;  // SMTP username
		                    $mail->Password = $smtp_auth_password; // SMTP password
		                    $mail->From     = $email_from;
		                    $mail->FromName = $email_from_name;
		                    $mail->AddAddress($mail_destinataire);     //Destinataire
		                    $mail->WordWrap = 80;                              // set word wrap
		                    $mail->IsHTML(true);                               // send as HTML
		                    $mail->Subject  =  $txt['email_subject'];
		                    $mail->AltBody     =  $txt['email_altbody_1']." ".mysql_real_escape_string(stripslashes(($_POST['label'])))." ".$txt['email_altbody_2'];
		                    $corpsDeMail = $txt['email_body_1'].mysql_real_escape_string(stripslashes(($_POST['label']))).$txt['email_body_2'].
		                    $_SESSION['settings']['cpassman_url']."/index.php?page=items&group=".$_POST['categorie']."&id=".$new_id.$txt['email_body_3'];
		                    $mail->Body  =  $corpsDeMail;
		                    $mail->Send();
		                }
		            }

	                //return data
	                echo '[ { "item_exists": "'.$item_exists.'", "new_id": "'.$new_id.'", "error" : "no" } ]';
	            }

	        	else if (isset($_SESSION['settings']['duplicate_item']) && $_SESSION['settings']['duplicate_item'] == 0 && $item_exists == 1) {
	        		//return data
	        		echo '[ { "error" : "item_exists" } ]';
	        	}
        	}else{
        		echo '[ { "error" : "something_wrong" } ]';
        	}
        break;

        /*
        * CASE
        * update an ITEM
        */
        case "update_item":
            //init
            $reload_page = false;
            $return_values = array();

            //decrypt and retreive data in JSON format
            require_once '../includes/libraries/crypt/aes.class.php';     // AES PHP implementation
            require_once '../includes/libraries/crypt/aesctr.class.php';  // AES Counter Mode implementation
            $data_received = json_decode(AesCtr::decrypt($_POST['data'], $_SESSION['key'], 256), true);

            if (count($data_received) > 0) {
                //Prepare variables
                $label = htmlspecialchars_decode($data_received['label']);
                $url = htmlspecialchars_decode($data_received['url']);
                $pw = $original_pw = htmlspecialchars_decode($data_received['pw']);
                $login = htmlspecialchars_decode($data_received['login']);
                $tags = htmlspecialchars_decode($data_received['tags']);

                //Get existing values
                $data = $db->query_first("
					SELECT *
					FROM ".$pre."items
					WHERE id=".$data_received['id']
                );

            	//Manage salt key
            	if($data['perso'] != 1){
            		//Get orginal key
            		$original_key = $db->query_first("
						SELECT `rand_key`
						FROM `".$pre."keys`
						WHERE `table` LIKE 'items' AND `id`=".$data_received['id']
            		);

            		$pw = $original_key['rand_key'].$pw;
            	}


                //encrypt PW
        	    if ($data_received['salt_key_set']==1 && isset($data_received['salt_key_set']) && $data_received['is_pf']==1 && isset($data_received['is_pf'])){
        		    $pw = encrypt($pw,mysql_real_escape_string(stripslashes($_SESSION['my_sk'])));
        		    $resticted_to = $_SESSION['user_id'];
        	    }else
        		    $pw = encrypt($pw);

                //---Manage tags
                    //deleting existing tags for this item
                    $db->query("DELETE FROM ".$pre."tags WHERE item_id = '".$data_received['id']."'");

                    //Add new tags
                    $tags = explode(' ',$tags);
                    foreach($tags as $tag){
                        if ( !empty($tag) )
                            $db->query_insert(
                                'tags',
                                array(
                                    'item_id' => $data_received['id'],
                                    'tag' => strtolower($tag)
                                )
                            );
                    }

                //update item
                $db->query_update(
                    'items',
                    array(
                        'label' => $label,
                        'description' => $data_received['description'],
                        'pw' => $pw,
                        'login' => $login,
                        'url' => $url,
                        'id_tree' => $data_received['categorie'],
	                    'restricted_to' => $data_received['restricted_to'],
                        'anyone_can_modify' => (isset($data_received['anyone_can_modify']) && $data_received['anyone_can_modify'] == "on") ? '1' : '0'
                    ),
                    "id='".$data_received['id']."'"
                );


            	//Manage retriction_to_roles
            	if (isset($data_received['restricted_to_roles'])) {
            		//delete previous values
            		$db->query_delete(
	            		'restriction_to_roles',
	            		array(
		            		'item_id' => $data_received['id']
		            	)
            		);
            		//add roles for item
            		foreach (array_filter(explode(';', $data_received['restricted_to_roles'])) as $role){
            			$db->query_insert(
            			'restriction_to_roles',
            			array(
            			    'role_id' => $role,
            			    'item_id' => $data_received['id']
            			)
            			);
            		}
            	}


                //Update CACHE table
                UpdateCacheTable("update_value", $data_received['id']);

                //Log all modifications done
                    /*LABEL */
                    if ( $data['label'] != $label )
                        $db->query_insert(
                            'log_items',
                            array(
                                'id_item' => $data_received['id'],
                                'date' => mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('y')),
                                'id_user' => $_SESSION['user_id'],
                                'action' => 'at_modification',
                                'raison' => 'at_label : '.$data['label'].' => '.$label
                            )
                        );
                    /*LOGIN */
                    if ( $data['login'] != $login )
                        $db->query_insert(
                            'log_items',
                            array(
                                'id_item' => $data_received['id'],
                                'date' => mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('y')),
                                'id_user' => $_SESSION['user_id'],
                                'action' => 'at_modification',
                                'raison' => 'at_login : '.$data['login'].' => '.$login
                            )
                        );
                    /*URL */
                    if ( $data['url'] != $url && $url != "http://")
                        $db->query_insert(
                            'log_items',
                            array(
                                'id_item' => $data_received['id'],
                                'date' => mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('y')),
                                'id_user' => $_SESSION['user_id'],
                                'action' => 'at_modification',
                                'raison' => 'at_url : '.$data['url'].' => '.$url
                            )
                        );
                    /*DESCRIPTION */
                    if ( $data['description'] != $data_received['description'] )
                        $db->query_insert(
                            'log_items',
                            array(
                                'id_item' => $data_received['id'],
                                'date' => mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('y')),
                                'id_user' => $_SESSION['user_id'],
                                'action' => 'at_modification',
                                'raison' => 'at_description'
                            )
                        );
                    /*FOLDER */
                    if ( $data['id_tree'] != $data_received['categorie'] ){
                        $db->query_insert(
                            'log_items',
                            array(
                                'id_item' => $data_received['id'],
                                'date' => mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('y')),
                                'id_user' => $_SESSION['user_id'],
                                'action' => 'at_modification',
                                'raison' => 'at_category : '.$data['id_tree'].' => '.$data_received['categorie']
                            )
                        );
                        //ask for page reloading
                        $reload_page = true;
                    }
                    /*PASSWORD */
                    if ( $data['pw'] != $pw ){
                        if( isset($data_received['salt_key']) && !empty($data_received['salt_key']) ) $old_pw = decrypt($data['pw'],$data_received['salt_key']);
                        else $old_pw = decrypt($data['pw']);
                        $db->query_insert(
                            'log_items',
                            array(
                                'id_item' => $data_received['id'],
                                'date' => mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('y')),
                                'id_user' => $_SESSION['user_id'],
                                'action' => 'at_modification',
                                'raison' => 'at_pw : '.substr(addslashes($old_pw), strlen($original_key['rand_key']))
                            )
                        );
                    }

                //Reload new values
                $data_item = $db->query_first("
                    SELECT *
                    FROM ".$pre."items AS i
                    INNER JOIN ".$pre."log_items AS l ON (l.id_item = i.id)
                    WHERE i.id=".$data_received['id']."
                        AND l.action = 'at_creation'"
                );

                //Reload History
                $history = "";
                $rows = $db->fetch_all_array("
                    SELECT l.date AS date, l.action AS action, l.raison AS raison, u.login AS login
                    FROM ".$pre."log_items AS l
                    LEFT JOIN ".$pre."users AS u ON (l.id_user=u.id)
                    WHERE l.action <> 'at_shown' AND id_item=".$data_received['id']);
                foreach($rows as $reccord){
                	$reason = explode(':',$reccord['raison']);

                	if ( empty($history) ){
                		$history = date($_SESSION['settings']['date_format']." ".$_SESSION['settings']['time_format'], $reccord['date'])." - ". $reccord['login'] ." - ".$txt[$reccord['action']].
                			" - ".(!empty($reccord['raison']) ? (count($reason) > 1 ? $txt[trim($reason[0])].' : '.$reason[1] : $txt[trim($reason[0])] ):'');

                	}
                	else{
                		$history .= "<br />".date($_SESSION['settings']['date_format']." ".$_SESSION['settings']['time_format'], $reccord['date'])." - ".
                        	$reccord['login'] ." - ".$txt[$reccord['action']]." - ".
                        	(!empty($reccord['raison']) ? (count($reason) > 1 ? $txt[trim($reason[0])].' => '.$reason[1] : $txt[trim($reason[0])] ):'');
                	}
                }

                //Get list of restriction
                $liste = explode(";",$data_item['restricted_to']);
                $liste_restriction = "";
                foreach($liste as $elem){
                    if ( !empty($elem) ){
                        $data2 = $db->fetch_row("SELECT login FROM ".$pre."users WHERE id=".$elem);
                        $liste_restriction .= $data2[0].";";
                    }
                }

                //decrypt PW
                if ( empty($data_received['salt_key']) ){
                    $pw = decrypt($data_item['pw']);
                }else{
                    $pw = decrypt($data_item['pw'],mysql_real_escape_string(stripslashes($_SESSION['my_sk'])));
                }
                $pw = CleanString($pw);

                // Prepare files listing
                    $files = $files_edit = "";
                    // launch query
                    $rows = $db->fetch_all_array(
                        "SELECT *
                        FROM ".$pre."files
                        WHERE id_item=".$data_received['id']
                    );
                    foreach ($rows as $reccord){
                        // get icon image depending on file format
                        $icon_image = file_format_image($reccord['extension']);
                        // If file is an image, then prepare lightbox. If not image, then prepare donwload
                        if ( in_array($reccord['extension'],$k['image_file_ext']) )
                            $files .=   '<img src="includes/images/'.$icon_image.'" /><a class="image_dialog" href="'.$_SESSION['settings']['cpassman_url'].'/upload/'.$reccord['file'].'" title="'.$reccord['name'].'">'.$reccord['name'].'</a><br />';
                        else
                            $files .=   '<img src="includes/images/'.$icon_image.'" /><a href=\'sources/downloadFile.php?name='.urlencode($reccord['name']).'&path=../upload/'.$reccord['file'].'&size='.$reccord['size'].'&type='.urlencode($reccord['type']).'\' target=\'_blank\'>'.$reccord['name'].'</a><br />';
                        // Prepare list of files for edit dialogbox
                        $files_edit .= '<span id="span_edit_file_'.$reccord['id'].'"><img src="includes/images/'.$icon_image.'" /><img src="includes/images/document--minus.png" style="cursor:pointer;"  onclick="delete_attached_file(\"'.$reccord['id'].'\")" />&nbsp;'.$reccord['name']."</span><br />";
                    }

                //Send email
                if ( !empty($_POST['diffusion']) ){
                    require_once("class.phpmailer.php");
                    $destinataire= explode(';',$data_received['diffusion']);
                    foreach($destinataire as $mail_destinataire){
                        //envoyer ay destinataire
                        $mail = new PHPMailer();
                        $mail->SetLanguage("en","../includes/libraries/phpmailer/language");
                        $mail->IsSMTP();                                   // send via SMTP
                        $mail->Host     = $smtp_server; // SMTP servers
                        $mail->SMTPAuth = $smtp_auth;     // turn on SMTP authentication
                        $mail->Username = $smtp_auth_username;  // SMTP username
                        $mail->Password = $smtp_auth_password; // SMTP password
                        $mail->From     = $email_from;
                        $mail->FromName = $email_from_name;
                        $mail->AddAddress($mail_destinataire);     //Destinataire
                        $mail->WordWrap = 80;                              // set word wrap
                        $mail->IsHTML(true);                               // send as HTML
                        $mail->Subject  =  "Password has been updated";
                        $mail->AltBody     =  "Password for ".$label." has been updated.";
                        $corpsDeMail = "Hello,<br><br>Password for '" .$label."' has been updated.<br /><br />".
                        "You can check it <a href=\"".$_SESSION['settings']['cpassman_url']."/index.php?page=items&group=".$data_received['categorie']."&id=".$data_received['id']."\">HERE</a><br /><br />".
                        "Cheers";
                        $mail->Body  =  $corpsDeMail;
                        $mail->Send();
                    }
                }

                //Prepare some stuff to return
                $arrData = array(
            	    "files" => str_replace('"','&quot;',$files),
            	    "history" => str_replace('"','&quot;',$history),
            	    "files_edit" => str_replace('"','&quot;',$files_edit),
            	    "id_tree" => $data_item['id_tree'],
            	    "id" => $data_item['id'],
            	    "reload_page" => $reload_page,
            	    "restriction_to" => $data_received['restricted_to'].$data_received['restricted_to_roles']
                );
                //print_r($arrData);
                //Encrypt JSON data to return
                require_once '../includes/libraries/crypt/aes.class.php';     // AES PHP implementation
                require_once '../includes/libraries/crypt/aesctr.class.php';  // AES Counter Mode implementation
                $return_values = AesCtr::encrypt(json_encode($arrData,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP), $_SESSION['key'], 256);
            }else{
                //an error appears on JSON format
                $return_values = '{"error" : "format"}';
            }

            //return data
            echo $return_values;
        break;


       	/*
       	   * CASE
       	   * Copy an Item
       	*/
        case "copy_item":
        	$return_values = $pw = "";

        	if (isset($_POST['item_id']) && !empty($_POST['item_id']) && !empty($_POST['folder_id'])) {
        		// load the original record into an array
        		$original_record = $db->query_first("
					SELECT *
					FROM ".$pre."items
					WHERE id=".$_POST['item_id']
        		);

        		// insert the new record and get the new auto_increment id
        		$new_id = $db->query_insert(
        			'items',
        			array(
        				'label' => "duplicate"
        			)
        		);

        		//Check if item is PERSONAL
        		if($original_record['perso'] != 1){
        			//generate random key
        			$random_key = GenerateKey();

        			//Store generated key
        			$db->query_insert(
	        			'keys',
	        			array(
	        			    'table' => 'items',
	        			    'id' => $new_id,
	        			    'rand_key' => $random_key
	        			)
        			);

        			//get key for original pw
        			$original_key = $db->query_first('SELECT rand_key FROM `'.$pre.'keys` WHERE `table` LIKE "items" AND `id` ='.$_POST['item_id']);

        			//unsalt previous pw
        			$pw = substr(decrypt($original_record['pw']), strlen($original_key['rand_key']));
        		}


        		// generate the query to update the new record with the previous values
        		$query = "UPDATE ".$pre."items SET ";
        		foreach ($original_record as $key => $value) {
        			if($key == "id_tree"){
        				$query .= '`id_tree` = "'.$_POST['folder_id'].'", ';
        			}else if($key == "pw" && !empty($pw)){
        				$query .= '`pw` = "'.encrypt($random_key.$pw).'", ';
        			}else if ($key != "id" && $key != "key") {
        				$query .= '`'.$key.'` = "'.str_replace('"','\"',$value).'", ';
        			}
        		}
        		$query = substr($query,0,strlen($query)-2); # lop off the extra trailing comma
        		$query .= " WHERE id=".$new_id;
        		$db->query($query);


        		//Add attached itms
        		$rows = $db->fetch_all_array(
        		"SELECT *
                        FROM ".$pre."files
                        WHERE id_item=".$new_id
        		);
        		foreach ($rows as $reccord){
        			$db->query_insert(
	        			'files',
	        			array(
	        				'id_item' => $new_id,
	        				'name' => $reccord['name'],
	        				'size' => $reccord['size'],
	        				'extension' => $reccord['extension'],
	        				'type' => $reccord['type'],
	        				'file' => $reccord['file']
	        			)
        			);
        		}

        		//Add this duplicate in logs
        		$db->query_insert(
	        		'log_items',
	        		array(
	        		    'id_item' => $new_id,
	        		    'date' => mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('y')),
	        		    'id_user' => $_SESSION['user_id'],
	        		    'action' => 'at_creation'
	        		)
        		);

        		//Add the fact that item has been copied in logs
        		$db->query_insert(
	        		'log_items',
	        		array(
	        		    'id_item' => $_POST['item_id'],
	        		    'date' => mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('y')),
	        		    'id_user' => $_SESSION['user_id'],
		        		'action' => 'at_copy'
	        		)
        		);

        		//reload cache table
        		require_once("main.functions.php");
        		UpdateCacheTable("reload", "");

        		$return_values = '[{"status" : "ok"}, {"new_id" : "'.$new_id.'"}]';
        	}else{
        		//no item
        		$return_values = '[{"error" : "no_item"}, {"error_text" : "No item ID"}]';
        	}

       		//return data
       		echo $return_values;
       	break;


       	/*
       	   * CASE
       	   * Display informations of selected item
       	*/
        case "show_details_item":
            $arrData = array();

            //return ID
            $arrData['id'] = $_POST['id'];

            //Get all informations for this item
            $sql = "SELECT *
                    FROM ".$pre."items AS i
                    INNER JOIN ".$pre."log_items AS l ON (l.id_item = i.id)
                    WHERE i.id=".$_POST['id']."
                    AND l.action = 'at_creation'";
            $data_item = $db->query_first($sql);

            //Get all tags for this item
            $tags = "";
            $sql = "SELECT tag
                    FROM ".$pre."tags
                    WHERE item_id=".$_POST['id'];
            $rows = $db->fetch_all_array($sql);
            foreach ($rows as $reccord)
                $tags .= $reccord['tag']." ";

            //check that actual user can access this item
            $access = explode(';',$data_item['id_tree']);
            $restriction_active = true;
            $restricted_to = explode(';',$data_item['restricted_to']);
            if ( in_array($_SESSION['user_id'],$restricted_to) ) $restriction_active = false;
            if ( empty($data_item['restricted_to']) ) $restriction_active = false;

            //Uncrypt PW
            if ( isset($_POST['salt_key_required']) && $_POST['salt_key_required'] == 1 && isset($_POST['salt_key_set']) && $_POST['salt_key_set'] == 1){
	            $pw = decrypt($data_item['pw'],mysql_real_escape_string(stripslashes($_SESSION['my_sk'])));
                $arrData['edit_item_salt_key'] = 1;
            }else{
                $pw = decrypt($data_item['pw']);
                $arrData['edit_item_salt_key'] = 0;
            }

        	//extract real pw from salt
        	if($data_item['perso'] != 1){
        		$data_item_key = $db->query_first('SELECT rand_key FROM `'.$pre.'keys` WHERE `table`="items" AND `id`='.$_POST['id']);
        		$pw = substr($pw, strlen($data_item_key['rand_key']));
        	}

            //check if item is expired
        	if ( isset($_POST['expired_item']) && $_POST['expired_item'] == 1 ) {
        		$item_is_expired = true;
        	}else {
        		$item_is_expired = false;
        	}


            //Check if actual USER can see this ITEM
            if ((
            	( in_array($access[0],$_SESSION['groupes_visibles']) || $_SESSION['is_admin'] == 1 )
	                &&  ( $data_item['perso']==0 || ($data_item['perso']==1 && $data_item['id_user'] == $_SESSION['user_id'] ) )
	                && $restriction_active == false
            	)
            	||
            	(
            		$data_item['anyone_can_modify']==1 && ( in_array($access[0],$_SESSION['groupes_visibles']) || $_SESSION['is_admin'] == 1 )
            	)
            	||
            	(
            		@in_array($_POST['id'], $_SESSION['list_folders_limited'][$_POST['folder_id']])
            	)
            ){
            	//Allow show details
                $arrData['show_details'] = 1;

                //Display menu icon for deleting if user is allowed
                if ($data_item['id_user'] == $_SESSION['user_id'] || $_SESSION['is_admin'] == 1 || ($_SESSION['user_gestionnaire'] == 1 && $_SESSION['settings']['manager_edit'] == 1) || $data_item['anyone_can_modify']==1 || in_array($data_item['id_tree'], $_SESSION['list_folders_editable_by_role'])){
                    $arrData['user_can_modify'] = 1;
                    $user_is_allowed_to_modify = true;
                }else{
                    $arrData['user_can_modify'] = 0;
                    $user_is_allowed_to_modify = false;
                }

                //GET Audit trail
                $historique = "";
                $rows = $db->fetch_all_array("
                    SELECT l.date AS date, l.action AS action, l.raison AS raison, u.login AS login
                    FROM ".$pre."log_items AS l
                    LEFT JOIN ".$pre."users AS u ON (l.id_user=u.id)
                    WHERE id_item=".$_POST['id']."
                    AND action <> 'at_shown'
                    ORDER BY date ASC"
                );
                foreach ( $rows as $reccord ){
                	$reason = explode(':',$reccord['raison']);

                    if ( empty($historique) )
                        $historique = date($_SESSION['settings']['date_format']." ".$_SESSION['settings']['time_format'], $reccord['date'])." - ". $reccord['login'] ." - ".$txt[$reccord['action']]." - ".(!empty($reccord['raison']) ? (count($reason) > 1 ? $txt[trim($reason[0])].' : '.$reason[1] : $txt[trim($reason[0])] ):'');
                    else
                        $historique .= "<br />".date($_SESSION['settings']['date_format']." ".$_SESSION['settings']['time_format'], $reccord['date'])." - ". $reccord['login']  ." - ".$txt[$reccord['action']]." - ".(!empty($reccord['raison']) ? (count($reason) > 1 ? $txt[trim($reason[0])].' => '.$reason[1] : $txt[trim($reason[0])] ):'');
                }

                //Get restriction list for users
            	$liste = explode(";",$data_item['restricted_to']);
            	$liste_restriction = "";
            	if (count($liste) > 0) {
            		foreach($liste as $elem){
            			if ( !empty($elem) ){
            				$data2 = $db->fetch_row("SELECT login FROM ".$pre."users WHERE id=".$elem);
            				$liste_restriction .= $data2[0].";";
            			}
            		}            	}


            	//Get restriction list for roles
            	$liste_restriction_roles = array();
            	if (isset($_SESSION['settings']['restricted_to_roles']) && $_SESSION['settings']['restricted_to_roles'] == 1 && !empty($_POST['restricted_to'])) {
            		$rows = $db->fetch_all_array("
					SELECT t.title
					FROM ".$pre."roles_title AS t
					INNER JOIN ".$pre."roles_values AS v ON (t.id=v.role_id)
					WHERE v.folder_id = ".$data_item['id_tree']."
					ORDER BY t.title ASC");
            		foreach($rows as $reccord){
            			array_push($liste_restriction_roles, $reccord['title']);
            		}

            		//Add restriction if item is restricted to roles
            		$rows = $db->fetch_all_array("
					SELECT t.title
					FROM ".$pre."roles_title AS t
					INNER JOIN ".$pre."restriction_to_roles AS r ON (t.id=r.role_id)
					WHERE r.item_id = ".$data_item['id']."
					ORDER BY t.title ASC");
            		foreach($rows as $reccord){
            			if (!in_array($reccord['title'], $liste_restriction_roles)){
            				array_push($liste_restriction_roles, $reccord['title']);
            			}
            		}
            	}

				//Check if any KB is linked to this item
				if(isset($_SESSION['settings']['enable_kb']) && $_SESSION['settings']['enable_kb'] == 1){
					$tmp = "";
					$rows = $db->fetch_all_array("
							SELECT k.label, k.id
							FROM ".$pre."kb_items AS i
							INNER JOIN ".$pre."kb AS k ON (i.kb_id=k.id)
							WHERE i.item_id = ".$data_item['id']."
							ORDER BY k.label ASC");
	          		foreach($rows as $reccord){
						if(empty($tmp)){
							$tmp = "<a href='".$_SESSION['settings']['cpassman_url']."/index.php?page=kb&id=".$reccord['id']."'>".$reccord['label']."</a>";
						}else{
							$tmp .= "&nbsp;-&nbsp;<a href='".$_SESSION['settings']['cpassman_url']."/index.php?page=kb&id=".$reccord['id']."'>".$reccord['label']."</a>";
						}
	          		}
					$arrData['links_to_kbs'] = $tmp;
				}


                //Prepare DIalogBox data
                if ( $item_is_expired == false ) {
                    $arrData['show_detail_option'] = 0;
                }else if ( $user_is_allowed_to_modify == true && $item_is_expired == true ){
                    $arrData['show_detail_option'] = 1;
                }else{
                    $arrData['show_detail_option'] = 2;
                }

                $arrData['label'] = $data_item['label'];
                $arrData['pw'] = $pw;
                $arrData['url'] = $data_item['url'];
                if (!empty($data_item['url'])) {
                    $arrData['link'] = "&nbsp;<a href='". $data_item['url']."' target='_blank'><img src='includes/images/arrow_skip.png' style='border:0px;' title='Ouvrir la page'></a>";
                }

                $arrData['description'] = preg_replace('/(?<!\\r)\\n+(?!\\r)/', '',strip_tags($data_item['description'],$allowed_tags));
                $arrData['login'] = str_replace('"','&quot;',$data_item['login']);
                $arrData['historique'] = str_replace('"','&quot;',$historique);
                $arrData['id_restricted_to'] = $liste_restriction;
            	$arrData['id_restricted_to_roles'] = count($liste_restriction_roles) > 0 ? implode(";", $liste_restriction_roles).";" : "";
                $arrData['tags'] = str_replace('"','&quot;',$tags);
                $arrData['folder'] = $data_item['id_tree'];
                $arrData['anyone_can_modify'] = $data_item['anyone_can_modify'];

                //Add this item to the latests list
                if ( isset($_SESSION['latest_items']) && isset($_SESSION['settings']['max_latest_items']) && !in_array($data_item['id'],$_SESSION['latest_items']) ){
                    if ( count($_SESSION['latest_items']) >= $_SESSION['settings']['max_latest_items'] ){
                        array_pop($_SESSION['latest_items']);   //delete last items
                    }
                    array_unshift($_SESSION['latest_items'],$data_item['id']);
                    //update DB
                    $db->query_update(
                        "users",
                        array(
                            'latest_items' => implode(';',$_SESSION['latest_items'])
                        ),
                        "id=".$_SESSION['user_id']
                    );
                }

                // Prepare files listing
                    $files = $files_edit = "";
                    // launch query
                    $rows = $db->fetch_all_array(
                        "SELECT *
                        FROM ".$pre."files
                        WHERE id_item=".$_POST['id']
                    );
                    foreach ($rows as $reccord){
                        // get icon image depending on file format
                        $icon_image = file_format_image($reccord['extension']);
                        // If file is an image, then prepare lightbox. If not image, then prepare donwload
                        if ( in_array($reccord['extension'],$k['image_file_ext']) )
                            $files .=   '<img src=\'includes/images/'.$icon_image.'\' /><a class=\'image_dialog\' href=\''.$_SESSION['settings']['cpassman_url'].'/upload/'.$reccord['file'].'\' title=\''.$reccord['name'].'\'>'.$reccord['name'].'</a><br />';
                        else
                            $files .=   '<img src=\'includes/images/'.$icon_image.'\' /><a href=\'sources/downloadFile.php?name='.urlencode($reccord['name']).'&path=../upload/'.$reccord['file'].'&size='.$reccord['size'].'&type='.urlencode($reccord['type']).'\'>'.$reccord['name'].'</a><br />';
                        // Prepare list of files for edit dialogbox
                        $files_edit .= '<span id=\'span_edit_file_'.$reccord['id'].'\'><img src=\'includes/images/'.$icon_image.'\' /><img src=\'includes/images/document--minus.png\' style=\'cursor:pointer;\'  onclick=\'delete_attached_file("'.$reccord['id'].'")\' />&nbsp;'.$reccord['name']."</span><br />";
                    }
                    //display lists
                    $arrData['files_edit'] = str_replace('"','&quot;',$files_edit);
                    $arrData['files_id'] = $files;


                //Refresh last seen items
                    $text = $txt['last_items_title'].": ";
                    $_SESSION['latest_items_tab'][] = "";
                    foreach($_SESSION['latest_items'] as $item){
                        if ( !empty($item) ){
                            $data = $db->query_first("SELECT label,id_tree FROM ".$pre."items WHERE id = ".$item);
                            $_SESSION['latest_items_tab'][$item] = array(
                                'label'=>addslashes($data['label']),
                                'url'=>'index.php?page=items&group='.$data['id_tree'].'&id='.$item
                            );
                            $text .= '<span class="last_seen_item" onclick="javascript:window.location.href = \''.$_SESSION['latest_items_tab'][$item]['url'].'\'"><img src="includes/images/tag-small.png" />'.stripslashes($_SESSION['latest_items_tab'][$item]['label']).'</span>';
                        }
                    }
                    $arrData['div_last_items'] = str_replace('"','&quot;',$text);

                    //disable add bookmark if alread bookmarked
                    if ( in_array($_POST['id'],$_SESSION['favourites']) ) {
                        $arrData['favourite'] = 1;
                    }else{
                        $arrData['favourite'] = 0;
                    }

	            	//Manage user restriction
	            	if (isset($_POST['restricted'])) {
	            		$arrData['restricted'] = $_POST['restricted'];
	            	}else{
	            		$arrData['restricted'] = "";
	            	}

	            	//Add the fact that item has been copied in logs
	            	if(isset($_SESSION['settings']['log_accessed']) && $_SESSION['settings']['log_accessed'] == 1){
	            		$db->query_insert(
		            		'log_items',
		            		array(
		            		    'id_item' => $_POST['id'],
		            		    'date' => mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('y')),
		            		    'id_user' => $_SESSION['user_id'],
		            			'action' => 'at_shown'
		            		)
	            		);
	            	}
            }else{
                $arrData['show_details'] = 0;
            }
            //print_r($arrData);
            //Encrypt data to return
            require_once '../includes/libraries/crypt/aes.class.php';     // AES PHP implementation
            require_once '../includes/libraries/crypt/aesctr.class.php';  // AES Counter Mode implementation
            $return_values = AesCtr::encrypt(json_encode($arrData,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP), $_SESSION['key'], 256);

            //return data
            echo $return_values;
        break;

        /*
        * CASE
        * Generate a password
        */
        case "pw_generate":
            $key = "";
            //call class
            include('../includes/libraries/pwgen/pwgen.class.php');
            $pwgen = new PWGen();

            // Set pw size
            $pwgen->setLength($_POST['size']);
            // Include at least one number in the password
            $pwgen->setNumerals( ($_POST['num'] == "true")? true : false);
            // Include at least one capital letter in the password
            $pwgen->setCapitalize( ($_POST['maj'] == "true")? true : false);
            // Include at least one symbol in the password
            $pwgen->setSymbols( ($_POST['symb'] == "true")? true : false);
            // Complete random, hard to memorize password
            if (isset($_POST['secure']) && $_POST['secure'] == "true"){
                $pwgen->setSecure(true);
                $pwgen->setSymbols(true);
                $pwgen->setCapitalize(true);
                $pwgen->setNumerals(true);
            }else
                $pwgen->setSecure(false);

        	echo json_encode(array("key" => $pwgen->generate()),JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
        break;


       	/*
	 	* CASE
		* Delete an item
       	*/
        case "del_item":
            //delete item consists in disabling it
            $db->query_update(
                "items",
                array(
                    'inactif' => '1',
                ),
                "id = ".$_POST['id']
            );
            //log
            $db->query_insert(
                "log_items",
                array(
                    'id_item' => $_POST['id'],
                    'date' => mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('y')),
                    'id_user' => $_SESSION['user_id'],
                    'action' => 'at_delete'
                )
            );

            //Update CACHE table
            UpdateCacheTable("delete_value",$_POST['id']);
        break;


       	/*
       	* CASE
       	* Update a Group
       	*/
        case "update_rep":
        	//decrypt and retreive data in JSON format
        	require_once '../includes/libraries/crypt/aes.class.php';     // AES PHP implementation
        	require_once '../includes/libraries/crypt/aesctr.class.php';  // AES Counter Mode implementation
        	$data_received = json_decode((AesCtr::decrypt($_POST['data'], $_SESSION['key'], 256)), true);

        	//Prepare variables
        	$title = htmlspecialchars_decode($data_received['title']);

            //Check if title doesn't contains html codes
            if (preg_match_all("|<[^>]+>(.*)</[^>]+>|U", $title, $out)) {
            	//send data
            	echo '[{"error" : "'.$txt['error_html_codes'].'"}]';
            }else{

                //update Folders table
                $db->query_update(
                    "nested_tree",
                    array(
                        'title' => $title
                    ),
                    'id='.$data_received['folder']
                );

                //update complixity value
                $db->query_update(
                    "misc",
                    array(
                        'valeur' => $data_received['complexity']
                    ),
                    'intitule = "'.$data_received['folder'].'" AND type = "complex"'
                );

                //rebuild fuild tree folder
                require_once('NestedTree.class.php');
                $tree = new NestedTree($pre.'nested_tree', 'id', 'parent_id', 'title');
                $tree->rebuild();

                //send data
                echo '[{"error" : ""}]';
            }
        break;


        /*
        * CASE
        * Store hierarchic position of Group
        */
        case 'save_position':
            require_once ("NestedTree.class.php");
            $db->query_update(
                "nested_tree",
                array(
                    'parent_id' => $_POST['destination']
                ),
                'id = '.$_POST['source']
            );
            $tree = new NestedTree($pre.'nested_tree', 'id', 'parent_id', 'title');
            $tree->rebuild();
        break;

        /*
        * CASE
        * List items of a group
        */
        case 'lister_items_groupe':
            $arbo_html = $html = "";
        	$folder_is_pf = $show_error = 0;
        	$items_id_list = $rights = array();

        	//Build query limits
        	if (empty($_POST['start'])) {
        		$start = 0;
        		$html = '<ul class="liste_items">';
        	}else{
        		$start = $_POST['start'];
        		$html = '<ul class="liste_items" style="">';
        	}


            //Prepare tree
            require_once ("NestedTree.class.php");
            $tree = new NestedTree($pre.'nested_tree', 'id', 'parent_id', 'title');
            $arbo = $tree->getPath($_POST['id'], true);
            foreach($arbo as $elem){
            	if ( $elem->title == $_SESSION['user_id'] && $elem->nlevel == 1 ) {
            		$elem->title = $_SESSION['login'];
            		$folder_is_pf = 1;
            	}
            	if (empty($arbo_html)) {
            		$arbo_html = htmlspecialchars(stripslashes($elem->title), ENT_QUOTES);
            	}else{
            		$arbo_html .= " » ".htmlspecialchars(stripslashes($elem->title), ENT_QUOTES);
            	}
            }


        	//check if this folder is a PF. If yes check if saltket is set
        	if ((!isset($_SESSION['my_sk']) || empty($_SESSION['my_sk'])) && $folder_is_pf == 1) {
            	$show_error = "is_pf_but_no_saltkey";
        	}

            //check if items exist
        	if (isset($_POST['restricted']) && $_POST['restricted'] == 1) {
        		$data_count[0] = count($_SESSION['list_folders_limited'][$_POST['id']]);
        		$where_arg = " AND i.id IN (".implode(',', $_SESSION['list_folders_limited'][$_POST['id']]).")";
        	}
			//check if this folder is visible
        	else if (!in_array($_POST['id'], $_SESSION['groupes_visibles'])) {
        		require_once '../includes/libraries/crypt/aes.class.php';     // AES PHP implementation
        		require_once '../includes/libraries/crypt/aesctr.class.php';  // AES Counter Mode implementation
        		echo AesCtr::encrypt(json_encode(array("error" => "not_authorized"), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP), $_SESSION['key'], 256);
        		break;
        	}else{
        		$data_count = $db->fetch_row("SELECT COUNT(*) FROM ".$pre."items WHERE inactif = 0");
        		$where_arg = " AND i.id_tree=".$_POST['id'];
        	}

            if ($data_count[0] > 0 && empty($show_error)){
                //init variables
                $init_personal_folder = false;
                $expired_item = false;

                //List all ITEMS
            	if($folder_is_pf == 0){
            		$rows = $db->fetch_all_array("
                    SELECT DISTINCT i.id AS id, i.restricted_to AS restricted_to, i.perso AS perso, i.label AS label, i.description AS description, i.pw AS pw, i.login AS login, i.anyone_can_modify AS anyone_can_modify,
                        l.date AS date,
                        n.renewal_period AS renewal_period,
                        l.action AS log_action, l.id_user AS log_user,
                        k.rand_key AS rand_key
                    FROM ".$pre."items AS i
                    INNER JOIN ".$pre."nested_tree AS n ON (i.id_tree = n.id)
                    INNER JOIN ".$pre."log_items AS l ON (i.id = l.id_item)
					INNER JOIN ".$pre."keys AS k ON (k.id = i.id)
                    WHERE i.inactif = 0".
            		$where_arg."
                    AND l.action = 'at_creation'
                    ORDER BY i.label ASC, l.date DESC
                 	LIMIT ".$start.",".$_POST['nb_items_to_display_once']);
            	}else{
            		$rows = $db->fetch_all_array("
                    SELECT DISTINCT i.id AS id, i.restricted_to AS restricted_to, i.perso AS perso, i.label AS label, i.description AS description, i.pw AS pw, i.login AS login, i.anyone_can_modify AS anyone_can_modify,
                        l.date AS date,
                        n.renewal_period AS renewal_period,
                        l.action AS log_action, l.id_user AS log_user
                    FROM ".$pre."items AS i
                    INNER JOIN ".$pre."nested_tree AS n ON (i.id_tree = n.id)
                    INNER JOIN ".$pre."log_items AS l ON (i.id = l.id_item)
                    WHERE i.inactif = 0".
            		$where_arg."
                    AND (l.action = 'at_creation')
                    ORDER BY i.label ASC, l.date DESC
                 	LIMIT ".$start.",".$_POST['nb_items_to_display_once']);
            	}
            	// REMOVED:  OR (l.action = 'at_modification' AND l.raison LIKE 'at_pw :%')
                $id_managed = '';
                $i = 0;

                foreach( $rows as $reccord ) {
                    //exclude all results except the first one returned by query
                    if ( empty($id_managed) || $id_managed != $reccord['id'] ){

                        //Get Expiration date
                        $expiration_flag = '';
                        $expired_item = 0;
                        if ( $_SESSION['settings']['activate_expiration'] == 1 ){
                            $expiration_flag = '<img src="includes/images/flag-green.png">';
                            if ( $reccord['renewal_period']> 0 && ($reccord['date'] + ($reccord['renewal_period'] * $k['one_month_seconds'])) < time() ){
                                $expiration_flag = '<img src="includes/images/flag-red.png">';
                                $expired_item = 1;
                            }
                        }

                        //list of restricted users
                        $restricted_users_array = explode(';',$reccord['restricted_to']);
                        $item_pw = "";
                        $item_login = "";
                        $display_item = $need_sk = $can_move = $item_is_restricted_to_role = 0;

                        //TODO: Element is restricted to a group. Check if element can be seen by user
                        //=> récupérer un tableau contenant les roles associés à cet ID (a partir table restriction_to_roles)
                        $roles = $db->fetch_all_array("SELECT role_id FROM ".$pre."restriction_to_roles WHERE item_id=".$reccord['id']);
                        if (count($roles) > 0){
                        	$item_is_restricted_to_role = 1;
                        	$user_is_included_in_role = 0;
                        	foreach ($roles as $val){
                        		if (in_array($val['role_id'], $_SESSION['user_roles'])){
                        			$user_is_included_in_role = 1;
                        			break;
                        		}
                        	}
                        }


                    	//Manage the restricted_to variable
                    	if (isset($_POST['restricted'])) {
                    		$restricted_to = $_POST['restricted'];
                    	}else{
                    		$restricted_to = "";
                    	}

                    	if (isset($_SESSION['list_folders_editable_by_role']) && in_array($_POST['id'], $_SESSION['list_folders_editable_by_role'])) {
                    		if (empty($restricted_to)) {
                    			$restricted_to = $_SESSION['user_id'];
                    		}else{
                    			$restricted_to .= ','.$_SESSION['user_id'];
                    		}
                    	}

                    	//Can user modify it?
                    	if ($reccord['anyone_can_modify'] == 1 || ($_SESSION['user_id'] == $reccord['log_user']) || ($_SESSION['user_read_only'] == 1 && $folder_is_pf == 0)) {
                    		$can_move = 1;
                    	}

                        //CASE where item is restricted to a role to which the user is not associated
                        if (isset($user_is_included_in_role) && isset($item_is_restricted_to_role) && $user_is_included_in_role == 0 && $item_is_restricted_to_role == 1){
                        	$perso = '<img src="includes/images/tag-small-red.png">';
                        	$recherche_group_pf = 0;
                            $action = 'AfficherDetailsItem(\''.$reccord['id'].'\', \'0\', \''.$expired_item.'\', \''.$restricted_to.'\', \'no_display\')';
                            $display_item = $need_sk = $can_move = 0;
                        }else

                        //Case where item is in own personal folder
                        if ( in_array($_POST['id'],$_SESSION['personal_visible_groups']) && $reccord['perso'] == 1 ){
                            $perso = '<img src="includes/images/tag-small-alert.png">';
                        	$recherche_group_pf = 1;
                            $action = 'AfficherDetailsItem(\''.$reccord['id'].'\', \'1\', \''.$expired_item.'\', \''.$restricted_to.'\')';
                            $display_item = $need_sk = $can_move = 1;
                        }else
                        //CAse where item is restricted to a group of users included user
                        if ( !empty($reccord['restricted_to']) && in_array($_SESSION['user_id'],$restricted_users_array) || (isset($_SESSION['list_folders_editable_by_role']) && in_array($_POST['id'], $_SESSION['list_folders_editable_by_role']))){
                            $perso = '<img src="includes/images/tag-small-yellow.png">';
                        	$recherche_group_pf = 0;
                            $action = 'AfficherDetailsItem(\''.$reccord['id'].'\',\'0\',\''.$expired_item.'\', \''.$restricted_to.'\')';
                            $display_item = 1;
                        }else
                        //CAse where item is restricted to a group of users not including user
                        if (
                        	$reccord['perso'] == 1
                        	|| (!empty($reccord['restricted_to']) && !in_array($_SESSION['user_id'],$restricted_users_array))
                        	|| (isset($user_is_included_in_role) && isset($item_is_restricted_to_role) && $user_is_included_in_role == 0 && $item_is_restricted_to_role == 1)
                        ){
	                        if (isset($user_is_included_in_role) && isset($item_is_restricted_to_role) && $user_is_included_in_role == 0 && $item_is_restricted_to_role == 1){
	                        	$perso = '<img src="includes/images/tag-small-red.png">';
	                        	$recherche_group_pf = 0;
	                            $action = 'AfficherDetailsItem(\''.$reccord['id'].'\', \'0\', \''.$expired_item.'\', \''.$restricted_to.'\', \'no_display\')';
	                            $display_item = $need_sk = $can_move = 0;
	                        }else{
	                            $perso = '<img src="includes/images/tag-small-red.png">';
	                            $action = 'AfficherDetailsItem(\''.$reccord['id'].'\',\'0\',\''.$expired_item.'\', \''.$restricted_to.'\')';
	                            //reinit in case of not personal group
	                            if ( $init_personal_folder == false ){
	                            	$recherche_group_pf = "";
	                                $init_personal_folder = true;
	                            }
	                            //
	                            if ( !empty($reccord['restricted_to']) && in_array($_SESSION['user_id'],$restricted_users_array) )
	                            	$display_item = 1;
	                        }
	                    }
                        else{
                            $perso = '<img src="includes/images/tag-small-green.png">';
                            $action = 'AfficherDetailsItem(\''.$reccord['id'].'\',\'0\',\''.$expired_item.'\', \''.$restricted_to.'\')';
                            $display_item = 1;
                            //reinit in case of not personal group
                            if ( $init_personal_folder == false ){
                                //echo 'document.getElementById("recherche_group_pf").value = "";';
                            	$recherche_group_pf = "";
                                $init_personal_folder = true;
                            }
                        }

                        // Prepare full line
                    	$html .= '<li class="';
                        if ($can_move == 1) {
                        	$html .= 'item_draggable';
                        }else{
                        	$html .= 'item';
                        }

                    	$html .= '" id="'.$reccord['id'].'">';

                    	if ($can_move == 1) {
                    		$html .= '<img src="includes/images/grippy.png" style="margin-right:5px;cursor:hand;" alt="" class="grippy"  />';
                    	}else{
                    		$html .= '<span style="margin-left:11px;"></span>';
                    	}

						$html .= $expiration_flag.''.$perso.'&nbsp;<a id="fileclass'.$reccord['id'].'" class="file" onclick="'.$action.'">'.stripslashes($reccord['label']);
                        if (!empty($reccord['description']) && isset($_SESSION['settings']['show_description']) && $_SESSION['settings']['show_description'] == 1)
                            $html .= '&nbsp;<font size=2px>['.strip_tags(stripslashes(substr(CleanString($reccord['description']),0,30))).']</font>';
                        $html .= '</a>';

                        // display quick icon shortcuts ?
                    	if (isset($_SESSION['settings']['copy_to_clipboard_small_icons']) && $_SESSION['settings']['copy_to_clipboard_small_icons'] == 1) {
                    		$item_login = '<img src="includes/images/mini_user_disable.png" id="icon_login_'.$reccord['id'].'" />';
                    		$item_pw = '<img src="includes/images/mini_lock_disable.png" id="icon_pw_'.$reccord['id'].'" class="copy_clipboard" />';
                    		if ($display_item == true) {
                    			if (!empty($reccord['login'])) {
                    				$item_login = '<img src="includes/images/mini_user_enable.png" id="icon_login_'.$reccord['id'].'" class="copy_clipboard" title="'.$txt['item_menu_copy_login'].'" />';
                    			}
                    			if (!empty($reccord['pw'])) {
                    				$item_pw = '<img src="includes/images/mini_lock_enable.png" id="icon_pw_'.$reccord['id'].'" class="copy_clipboard" title="'.$txt['item_menu_copy_pw'].'" />';
                    			}
                    		}
                    	}


                    	//mini icon for collab
                    	if (isset($_SESSION['settings']['anyone_can_modify']) && $_SESSION['settings']['anyone_can_modify'] == 1) {
                    		if ($reccord['anyone_can_modify'] == 1) {
                    			$item_collab = '&nbsp;<img src="includes/images/mini_collab_enable.png" title="'.$txt['item_menu_collab_enable'].'" />';
                    		}else{
                    			$item_collab = '&nbsp;<img src="includes/images/mini_collab_disable.png" title="'.$txt['item_menu_collab_disable'].'" />';
                    		}
                    	}else{
                    		$item_collab = "";
                    	}


                    	//Continue line construction
                    	$html .= '<span style="float:right;margin:2px 10px 0px 0px;">'.$item_login.'&nbsp;'.$item_pw;

                    	// Prepare make Favorite small icon
                    	$html .= '&nbsp;<span id="quick_icon_fav_'.$reccord['id'].'" title="Manage Favorite" class="cursor">';
                    	if (in_array($reccord['id'], $_SESSION['favourites'])) {
                    		$html .= '<img src="includes/images/mini_star_enable.png" onclick="ActionOnQuickIcon('.$reccord['id'].',0)" />';
                    	}else {
                    		$html .= '<img src="includes/images/mini_star_disable.png"" onclick="ActionOnQuickIcon('.$reccord['id'].',1)" />';
                    	}

                        $html .= '</span>'.$item_collab.'</span></li>';

                        // increment array for icons shortcuts (don't do if option is not enabled)
                    	if (isset($_SESSION['settings']['copy_to_clipboard_small_icons']) && $_SESSION['settings']['copy_to_clipboard_small_icons'] == 1) {
	                    	if ($need_sk == true && isset($_SESSION['my_sk'])) {
	                    		$pw = decrypt($reccord['pw'],mysql_real_escape_string(stripslashes($_SESSION['my_sk'])));
	                    	}else{
	                    		$pw = substr(decrypt($reccord['pw']), strlen($reccord['rand_key']));
	                    	}
                    	}else{
                    		$pw = "";
                    	}

                    	//Build array with items
                        array_push($items_id_list,array($reccord['id'], $pw, $reccord['login'], $display_item));

                        $i ++;
                    }
                    $id_managed = $reccord['id'];

                }
                $html .= '</ul>';

                $rights = RecupDroitCreationSansComplexite($_POST['id']);
            }

            //Identify of it is a personal folder
            if (in_array($_POST['id'],$_SESSION['personal_visible_groups'])){
                $recherche_group_pf = 1;
            }else{
                $recherche_group_pf = "";
            }

        	//count
        	$count_items = $db->fetch_row("
                    SELECT COUNT(*)
                    FROM ".$pre."items AS i
                    INNER JOIN ".$pre."nested_tree AS n ON (i.id_tree = n.id)
                    INNER JOIN ".$pre."log_items AS l ON (i.id = l.id_item)
                    WHERE i.inactif = 0".
        	$where_arg."
                    AND (l.action = 'at_creation' OR (l.action = 'at_modification' AND l.raison LIKE 'at_pw :%'))
                    ORDER BY i.label ASC, l.date DESC");

        //echo $count_items[0] ."-". ($number_to_add + $start);


        	//Check list to be continued status
        	if (($_POST['nb_items_to_display_once'] + $start) < $count_items[0] ) {
        		$list_to_be_continued = "yes";
        	}
        	else {
        		$list_to_be_continued = "end";
        	}

        	//Prepare returned values
        	$return_values = array(
        		"recherche_group_pf" => $recherche_group_pf,
        		"arborescence" => "<img src='includes/images/folder-open.png' />&nbsp;".$arbo_html,
        		"array_items" => $items_id_list,
        		"items_html" => $html,
        		"error" => $show_error,
	        	"saltkey_is_required" => $folder_is_pf,
	        	"show_clipboard_small_icons" => isset($_SESSION['settings']['copy_to_clipboard_small_icons']) && $_SESSION['settings']['copy_to_clipboard_small_icons'] == 1 ? 1 : 0,
	        	"next_start" => $_POST['nb_items_to_display_once'] + $start,
	        	"list_to_be_continued" => $list_to_be_continued,
	        	"items_count" => $count_items[0]
			);


        	//Check if $rights is not null
        	if (count( $rights) > 0) {
        		$return_values = array_merge($return_values, $rights);
        	}
//print_r($return_values);

        	//Encrypt data to return
        	require_once '../includes/libraries/crypt/aes.class.php';     // AES PHP implementation
        	require_once '../includes/libraries/crypt/aesctr.class.php';  // AES Counter Mode implementation
        	$return_values = AesCtr::encrypt(json_encode($return_values,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP), $_SESSION['key'], 256);

        	//return data
        	echo $return_values;

        break;



       	/*
       	* CASE
       	* Get complexity level of a group
       	*/
        case "recup_complex":
            $data = $db->fetch_row("SELECT valeur FROM ".$pre."misc WHERE type='complex' AND intitule = '".$_POST['groupe']."'");

        	if(isset($data[0]) && (!empty($data[0]) || $data[0] == 0)){
        		$complexity = $pw_complexity[$data[0]][1];
        	}else{
        		$complexity = $txt['not_defined'];
        	}

            //afficher la visibilit?
            $visibilite = "";
            if ( !empty($data_pf[0]) ){
                $visibilite = $_SESSION['login'];
            }else{
            	$rows = $db->fetch_all_array("
								SELECT t.title
								FROM ".$pre."roles_values AS v
								INNER JOIN ".$pre."roles_title AS t ON (v.role_id = t.id)
								WHERE v.folder_id = '".$_POST['groupe']."'");
            	foreach ($rows as $reccord){
            		if ( empty($visibilite) ) $visibilite = $reccord['title'];
            		else $visibilite .= " - ".$reccord['title'];
            	}
            }

            RecupDroitCreationSansComplexite($_POST['groupe']);

        	$return_values = array(
        		"val" => $data[0],
        		"visibility" => $visibilite,
        		"complexity" => $complexity
			);

        	echo json_encode($return_values,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
        break;


       	/*
       	* CASE
       	* DELETE attached file from an item
       	*/
        case "delete_attached_file":
            //Get some info before deleting
            $data = $db->fetch_row("SELECT name,id_item,file FROM ".$pre."files WHERE id = '".$_POST['file_id']."'");
            if ( !empty($data[1]) ){

                //Delete from FILES table
                $db->query("DELETE FROM ".$pre."files WHERE id = '".$_POST['file_id']."'");

                //Update the log
                $db->query_insert(
                    'log_items',
                    array(
                        'id_item' => $data[1],
                        'date' => mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('y')),
                        'id_user' => $_SESSION['user_id'],
                        'action' => 'at_modification',
                        'raison' => 'at_del_file : '. $data[0]
                    )
                );

                //Delete file from server
                @unlink("../upload/".$data[2]);
            }
        break;


    	/*
        * CASE
        * REBUILD the description editor
       	*/
        case "rebuild_description_textarea":
            $return_values = array();
            if ( isset($_SESSION['settings']['richtext']) && $_SESSION['settings']['richtext'] == 1 ){
            	if ( $_POST['id'] == "desc" ){
            		$return_values['desc'] = '$("#desc").ckeditor({toolbar :[["Bold", "Italic", "Strike", "-", "NumberedList", "BulletedList", "-", "Link","Unlink","-","RemoveFormat"]], height: 100,language: "'. $k['langs'][$_SESSION['user_language']].'"});';
            	}else if ( $_POST['id'] == "edit_desc" ){
            		$return_values['desc'] = 'CKEDITOR.replace("edit_desc",{toolbar :[["Bold", "Italic", "Strike", "-", "NumberedList", "BulletedList", "-", "Link","Unlink","-","RemoveFormat"]], height: 100,language: "'. $k['langs'][$_SESSION['user_language']].'"});';
            	}
            }

        	//Multselect
        	$return_values['multi_select'] = '$("#edit_restricted_to_list").multiselect({selectedList: 7, minWidth: 430, height: 145, checkAllText: "'.$txt['check_all_text'].'", uncheckAllText: "'.$txt['uncheck_all_text'].'",noneSelectedText: "'.$txt['none_selected_text'].'"});';

            //Display popup
            if ( $_POST['id'] == "edit_desc" )
                $return_values['dialog'] = '$("#div_formulaire_edition_item").dialog("open");';
            else
                $return_values['dialog'] = '$("#div_formulaire_saisi").dialog("open");';

            echo $return_values;
        break;


       	/*
       	* CASE
       	* Clear HTML tags
       	*/
    	case "clear_html_tags":
    		//Get information for this item
    		$sql = "SELECT description
                    FROM ".$pre."items
                    WHERE id=".$_POST['id_item'];
    		$data_item = $db->query_first($sql);

    		//Clean up the string
    		//echo '$("#edit_desc").val("'.stripslashes(str_replace('\n','\\\n',mysql_real_escape_string(strip_tags($data_item['description'])))).'");';
			echo json_encode(array("description" => strip_tags($data_item['description'])),JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
    	break;

    	/*
    	   * FUNCTION
    	   * Launch an action when clicking on a quick icon
    	   * $action = 0 => Make not favorite
    	   * $action = 1 => Make favorite
    	*/
		case "action_on_quick_icon":
			if ($_POST['action'] == 1) {
				//Add new favourite
				array_push($_SESSION['favourites'], $_POST['id']);
				$db->query_update(
				"users",
				array(
				    'favourites' => implode(';', $_SESSION['favourites'])
				),
				'id = '.$_SESSION['user_id']
				);

				//Update SESSION with this new favourite
				$data = $db->query("SELECT label,id_tree FROM ".$pre."items WHERE id = ".$_POST['id']);
				$_SESSION['favourites_tab'][$_POST['id']] = array(
		            'label'=>$data['label'],
		            'url'=>'index.php?page=items&amp;group='.$data['id_tree'].'&amp;id='.$_POST['id']
		        );
			}else if ($_POST['action'] == 0) {
				//delete from session
				foreach ($_SESSION['favourites'] as $key => $value){
					if ($_SESSION['favourites'][$key] == $_POST['id']){
						unset($_SESSION['favourites'][$key]);
						break;
					}
				}

				//delete from DB
				$db->query("UPDATE ".$pre."users SET favourites = '".implode(';', $_SESSION['favourites'])."' WHERE id = '".$_SESSION['user_id']."'");
				//refresh session fav list
				foreach ($_SESSION['favourites_tab'] as $key => $value){
					if ($key == $_POST['id']){
						unset($_SESSION['favourites_tab'][$key]);
						break;
					}
				}
			}
		break;


		/*
		* CASE
		* Move an ITEM
		*/
    	case "move_item":
    		//get data about item
    		$data_source = $db->query_first("
					SELECT i.pw, f.personal_folder,i.id_tree, f.title
					FROM ".$pre."items AS i
					INNER JOIN ".$pre."nested_tree AS f ON (i.id_tree=f.id)
					WHERE i.id=".$_POST['item_id']
    		);

    		//get data about new folder
    		$data_destination = $db->query_first("SELECT personal_folder, title FROM ".$pre."nested_tree WHERE id = '".$_POST['folder_id']."'");

    		//update item
    		$db->query_update(
	    		'items',
	    		array(
	    		    'id_tree' => $_POST['folder_id']
	    		),
	    		"id='".$_POST['item_id']."'"
    		);

    		//previous is non personal folder and new too
    		if ($data_source['personal_folder'] == 0 && $data_destination['personal_folder'] == 0){
    			//just update is needed. Item key is the same
    		}

    		//previous is not personal folder and new is personal folder => item key exist on item => suppress it => OK !
    		else if ($data_source['personal_folder'] == 0 && $data_destination['personal_folder'] == 1){
    			//get key for original pw
    			$original_data = $db->query_first('
					SELECT k.rand_key, i.pw
					FROM `'.$pre.'keys` AS k
					INNER JOIN `'.$pre.'items` AS i ON (k.id=i.id)
					WHERE k.table LIKE "items"
					AND i.id='.$_POST['item_id']
    			);

    			//unsalt previous pw and encrupt with personal key
    			$pw = substr(decrypt($original_data['pw']), strlen($original_data['rand_key']));
    			$pw = encrypt($pw, mysql_real_escape_string(stripslashes($_SESSION['my_sk'])));

    			//update pw
    			$db->query_update(
	    			'items',
	    			array(
	    			    'pw' => $pw,
	    			    'perso' => 1
	    			),
	    			"id='".$_POST['item_id']."'"
    			);

    			//Delete key
    			$db->query_delete(
	    			'keys',
	    			array(
		    			'id' => $_POST['item_id'],
		    			'table' => 'items'
		    		)
    			);
    		}

    		//If previous is personal folder and new is personal folder too => no key exist on item
			else if ($data_source['personal_folder'] == 1 && $data_destination['personal_folder'] == 1){
				//NOTHING TO DO => just update is needed. Item key is the same
			}

    		//If previous is personal folder and new is not personal folder => no key exist on item => add new
    		else if ($data_source['personal_folder'] == 1 && $data_destination['personal_folder'] == 0){
    			//generate random key
    			$random_key = GenerateKey();

    			//store key
    			$db->query_insert(
	    			'keys',
	    			array(
	    			    'table' => 'items',
	    			    'id' => $_POST['item_id'],
	    			    'rand_key' => $random_key
	    			)
    			);

    			//update item
    			$db->query_update(
	    			'items',
	    			array(
		    			'pw' => encrypt($random_key.decrypt($data_source['pw'], mysql_real_escape_string(stripslashes($_SESSION['my_sk'])))),
		    			'perso' => 0
	    			),
	    			"id='".$_POST['item_id']."'"
    			);
    		}

				//Log item moved
				$db->query_insert(
	          'log_items',
	          array(
	              'id_item' => $_POST['item_id'],
	              'date' => mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('y')),
	              'id_user' => $_SESSION['user_id'],
								'action' => 'at_modification',
	              'raison' => 'at_moved : '.$data_source['title'].' -> '.$data_destination['title']
	          )
	      );

				echo '[{"from_folder":"'.$data_source['id_tree'].'" , "to_folder":"'.$_POST['folder_id'].'"}]';

    		break;
    }
}

if ( isset($_POST['type']) ){
    //Hide the ajax loader image
    //echo 'document.getElementById(\'div_loading\').style.display = "none";';
}


// Build the QUERY in case of GET
if ( isset($_GET['type']) ){
    switch($_GET['type'])
    {
    	/*
    	* CASE
    	* Autocomplet for TAGS
    	*/
        case "autocomplete_tags":
            //Get a list off all existing TAGS
            $rows = $db->fetch_all_array("SELECT tag FROM ".$pre."tags GROUP BY tag");
            foreach ($rows as $reccord ){
                echo $reccord['tag']."|".$reccord['tag']."\n";
            }
        break;
    }
}


/*
* FUNCTION
* Identify if this group authorize creation of item without the complexit level reached
*/
function RecupDroitCreationSansComplexite($groupe){
    global $db, $pre;
    $data = $db->fetch_row("SELECT bloquer_creation,bloquer_modification,personal_folder FROM ".$pre."nested_tree WHERE id = '".$groupe."'");

    //Check if it's in a personal folder. If yes, then force complexity overhead.
    if ( $data[2] == 1 ){
        //echo 'document.getElementById("bloquer_modification_complexite").value = "1";';
        //echo 'document.getElementById("bloquer_creation_complexite").value = "1";';
    	return array("bloquer_modification_complexite"=>1,"bloquer_creation_complexite"=>1);
    }else{
        //echo 'document.getElementById("bloquer_creation_complexite").value = "'.$data[0].'";';
    	//echo 'document.getElementById("bloquer_modification_complexite").value = "'.$data[1].'";';
    	return array("bloquer_modification_complexite"=>$data[0],"bloquer_creation_complexite"=>$data[1]);
    }

}

/*
   * FUNCTION
* permits to identify what icon to display depending on file extension
*/
function file_format_image($ext){
	global $k;
	if ( in_array($ext,$k['office_file_ext']) ) $image = "document-office.png";
	else if ( $ext == "pdf" ) $image = "document-pdf.png";
	else if ( in_array($ext,$k['image_file_ext']) ) $image = "document-image.png";
	else if ( $ext == "txt" ) $image = "document-txt.png";
	else  $image = "document.png";
	return $image;
}

/*
   * FUNCTION
   * permits to remplace some specific characters in password
*/
function password_replacement($pw){
	$pw_patterns = array('/ETCOMMERCIAL/','/SIGNEPLUS/');
	$pw_remplacements = array('&','+');
	return preg_replace($pw_patterns,$pw_remplacements,$pw);
}


?>