<?php
/**
 * @file 		folders.queries.php
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
if ($_SESSION['CPM'] != 1)
	die('Hacking attempt...');

include('../includes/language/'.$_SESSION['user_language'].'.php');
include('../includes/settings.php');
header("Content-type: text/html; charset==utf-8");
include('main.functions.php');
require_once ("NestedTree.class.php");

//Connect to mysql server
require_once("class.database.php");
$db = new Database($server, $user, $pass, $database, $pre);
$db->connect();

// CASE where title is changed
if ( isset($_POST['newtitle']) ){
    $id = explode('_',$_POST['id']);
    //update DB
    $db->query_update(
        'nested_tree',
        array(
            'title' => mysql_real_escape_string(stripslashes(($_POST['newtitle'])))
        ),
        "id=".$id[1]
    );
    //Show value
    echo ($_POST['newtitle']);
}

// CASE where RENEWAL PERIOD is changed
else if ( isset($_POST['renewal_period']) && !isset($_POST['type']) ){
    //Check if renewal period is an integer
    if ( is_int(intval($_POST['renewal_period'])) ){
        $id = explode('_',$_POST['id']);
        //update DB
        $db->query_update(
            'nested_tree',
            array(
                'renewal_period' => mysql_real_escape_string(stripslashes(($_POST['renewal_period'])))
            ),
            "id=".$id[1]
        );
        //Show value
        echo ($_POST['renewal_period']);
    }else{
        //Show ERROR
        echo ($txt['error_renawal_period_not_integer']);
    }
}

// CASE where the parent is changed
else if ( isset($_POST['newparent_id']) ){
    $id = explode('_',$_POST['id']);
    //Store in DB
    $db->query_update(
        'nested_tree',
        array(
            'parent_id' => $_POST['newparent_id']
        ),
        "id=".$id[1]
    );
    //Get the title to display it
    $data = $db->fetch_row("SELECT title FROM ".$pre."nested_tree WHERE id = ".$_POST['newparent_id']);
    //show value
    echo ($data[0]);
    //rebuild the tree grid
    $tree = new NestedTree($pre.'nested_tree', 'id', 'parent_id', 'title');
    $tree->rebuild();
}

// CASE where complexity is changed
else if ( isset($_POST['changer_complexite']) ){
    $id = explode('_',$_POST['id']);

    //Check if group exists
    $tmp = $db->fetch_row("SELECT COUNT(*) FROM ".$pre."misc WHERE type = 'complex' AND intitule = '".$id[1]."'");
    if ( $tmp[0] == 0 ){
        //Insert into DB
        $db->query_insert(
            'misc',
            array(
                'type' => 'complex',
                'intitule' => $id[1],
                'valeur' => $_POST['changer_complexite']
            )
        );
    }else{
        //update DB
        $db->query_update(
            'misc',
            array(
                'valeur' => $_POST['changer_complexite']
            ),
            "type='complex' AND  intitule = ".$id[1]
        );
    }

    //Get title to display it
    echo $mdp_complexite[$_POST['changer_complexite']][1];

    //rebuild the tree grid
    $tree = new NestedTree($pre.'nested_tree', 'id', 'parent_id', 'title');
    $tree->rebuild();
}

// Several other cases
else if ( isset($_POST['type']) ){
    switch($_POST['type'])
    {
        // CASE where DELETING a group
        case "supprimer_groupe":
            // this will delete all sub folders and items associated
            $tree = new NestedTree($pre.'nested_tree', 'id', 'parent_id', 'title');

            // Get through each subfolder
            $folders = $tree->getDescendants($_POST['id'],true);
            foreach($folders as $folder){
                //delete folder
            	$db->query("DELETE FROM ".$pre."nested_tree WHERE id = ".$folder->id);

            	//delete row
            	//echo '$("#row_'.$folder->id.'").remove();';

                //delete items & logs
                $items = $db->fetch_all_array("SELECT id FROM ".$pre."items WHERE id_tree='".$folder->id."'");
                foreach( $items as $item ) {
                    //Delete item
                    $db->query("DELETE FROM ".$pre."items WHERE id = ".$item['id']);
                    //log
                    $db->query("DELETE FROM ".$pre."log_items WHERE id_item = ".$item['id']);
                }
            }

            //rebuild tree
            $tree = new NestedTree($pre.'nested_tree', 'id', 'parent_id', 'title');
            $tree->rebuild();

            //Actualize the variable
            $_SESSION['nb_folders'] --;

            //Refresh the page
            echo 'RefreshPage("form_groupes");';
        break;


        //CASE where ADDING a new group
        case "add_folder":
        	$error = "";

        	//decrypt and retreive data in JSON format
        	require_once '../includes/libraries/crypt/aes.class.php';     // AES PHP implementation
        	require_once '../includes/libraries/crypt/aesctr.class.php';  // AES Counter Mode implementation
        	$data_received = json_decode((AesCtr::decrypt($_POST['data'], $_SESSION['key'], 256)), true);

        	//Prepare variables
        	$title = htmlspecialchars_decode($data_received['title']);
        	$complexity = htmlspecialchars_decode($data_received['complexity']);
        	$parent_id = htmlspecialchars_decode($data_received['parent_id']);
        	$renewal_period = htmlspecialchars_decode($data_received['renewal_period']);


            //Check if title doesn't contains html codes
            if (preg_match_all("|<[^>]+>(.*)</[^>]+>|U", $title, $out)) {
            	$error = 'error_html_codes';
            }

            //Check if duplicate folders name are allowed
            $create_new_folder = true;
            if ( isset($_SESSION['settings']['duplicate_folder']) && $_SESSION['settings']['duplicate_folder'] == 0 ){
                $data = $db->fetch_row("SELECT COUNT(*) FROM ".$pre."nested_tree WHERE title = '".addslashes($title)."'");
                if ( $data[0] != 0 ){
                	$error = 'error_group_exist';
                    $create_new_folder = false;
                }
            }

            if ( $create_new_folder == true ){
            	//check if parent folder is personal
            	$data = $db->fetch_row("SELECT personal_folder FROM ".$pre."nested_tree WHERE id = '".$parent_id."'");
            	if ( $data[0] == 1 ){
            		$is_personal = 1;
            	}else{
            		$is_personal = 0;
            	}

            	//create folder
                $new_id=$db->query_insert(
                    "nested_tree",
                    array(
                        'parent_id' => $parent_id,
                        'title' => $title,
                        'personal_folder' => $is_personal,
                        'renewal_period' => $renewal_period,
                        'bloquer_creation' => '0',
                        'bloquer_modification' => '0'
                    )
                );

                //Add complexity
                $db->query_insert(
                    "misc",
                    array(
                        'type' => 'complex',
                        'intitule' => $new_id,
                        'valeur' => $complexity
                    )
                );

                //Add this folder to the role the creator has
                foreach(array_filter(explode(';', $_SESSION['fonction_id'])) as $role_id) {
                    $db->query_insert(
                        "roles_values",
                        array(
                            'folder_id' => $new_id,
                            'role_id' =>  $role_id
                        )
                    );
                }

                require_once('NestedTree.class.php');
                $tree = new NestedTree($pre.'nested_tree', 'id', 'parent_id', 'title');
                $tree->rebuild();

                //Get user's rights
                IdentifyUserRights($_SESSION['groupes_visibles'].';'.$new_id,$_SESSION['groupes_interdits'],$_SESSION['is_admin'],$_SESSION['fonction_id'],true);

            	echo '[ { "error" : "'.$error.'" } ]';

            }
        break;

        //CASE where UPDATING a new group
        case "update_folder":
        	$error = "";

        	//decrypt and retreive data in JSON format
        	require_once '../includes/libraries/crypt/aes.class.php';     // AES PHP implementation
        	require_once '../includes/libraries/crypt/aesctr.class.php';  // AES Counter Mode implementation
        	$data_received = json_decode((AesCtr::decrypt($_POST['data'], $_SESSION['key'], 256)), true);

        	//Prepare variables
        	$title = htmlspecialchars_decode($data_received['title']);
        	$complexity = htmlspecialchars_decode($data_received['complexity']);
        	$parent_id = htmlspecialchars_decode($data_received['parent_id']);
        	$renewal_period = htmlspecialchars_decode($data_received['renewal_period']);


        	//Check if title doesn't contains html codes
        	if (preg_match_all("|<[^>]+>(.*)</[^>]+>|U", $title, $out)) {
        		$error = 'error_html_codes';
        	}

       		$db->query_update(
        		"nested_tree",
        		array(
        		    'parent_id' => $parent_id,
        		    'title' => $title,
        		    'personal_folder' => 0,
        		    'renewal_period' => $renewal_period,
        		    'bloquer_creation' => '0',
        		    'bloquer_modification' => '0'
	        	),
        		"id='".$data_received['id']."'"
       		);

       		//Add complexity
       		$db->query_update(
        		"misc",
        		array(
        		    'valeur' => $complexity
	        	),
       			array(
       				'intitule' => $data_received['id'],
       				'type' => 'complex'
				)
       		);


       		require_once('NestedTree.class.php');
       		$tree = new NestedTree($pre.'nested_tree', 'id', 'parent_id', 'title');
       		$tree->rebuild();

       		//Get user's rights
       		IdentifyUserRights($_SESSION['groupes_visibles'].';'.$data_received['id'],$_SESSION['groupes_interdits'],$_SESSION['is_admin'],$_SESSION['fonction_id'],true);



        	echo '[ { "error" : "'.$error.'" } ]';
        	break;


        //CASE where to update the associated Function
        case "fonction":
            $val = explode(';',$_POST['valeur']);
            $valeur = $_POST['valeur'];
            //Check if ID already exists
            $data = $db->fetch_row("SELECT authorized FROM ".$pre."rights WHERE tree_id = '".$val[0]."' AND fonction_id= '".$val[1]."'");
            if ( empty($data[0]) ){
                //Insert into DB
                $db->query_insert(
                    'rights',
                    array(
                        'tree_id' => $val[0],
                        'fonction_id' => $val[1],
                        'authorized' => 1
                    )
                );
            }else{
                //Update DB
                if ($data[0]==1)
                    $db->query_update(
                        'rights',
                        array(
                            'authorized' => 0
                        ),
                        "id = '".$val[0]."' AND fonction_id= '".$val[1]."'"
                    );
                else
                    $db->query_update(
                        'rights',
                        array(
                            'authorized' => 1
                        ),
                        "id = '".$val[0]."' AND fonction_id= '".$val[1]."'"
                    );
            }
        break;

        // CASE where to authorize an ITEM creation without respecting the complexity
        case "modif_droit_autorisation_sans_complexite":
            $db->query_update(
                'nested_tree',
                array(
                    'bloquer_creation' => $_POST['droit']
                ),
                "id = '".$_POST['id']."'"
            );
        break;

        // CASE where to authorize an ITEM modification without respecting the complexity
        case "modif_droit_modification_sans_complexite":
            $db->query_update(
                'nested_tree',
                array(
                    'bloquer_modification' => $_POST['droit']
                ),
                "id = '".$_POST['id']."'"
            );
        break;
    }
}
?>