<?php
session_start();
include('../includes/language/'.$_SESSION['user_language'].'.php'); 
include('../includes/settings.php'); 
header("Content-type: text/html; charset=".$k['charset']);

switch($_POST['type'])
{    
    case "cpm_status":   
        $text = "<ul>";     
        // Chemin vers le fichier distant
        $remote_file = 'web/pmc/pmc_config.txt';
        $local_file = 'localfile.txt';

        // Ouverture du fichier pour �criture
        $handle = fopen($local_file, 'w');

        // Mise en place d'une connexion basique
        $conn_id = ftp_connect("www.vag-technique.fr") or die("Impossible de se connecter au serveur $ftp_server");

        // Identification avec un nom d'utilisateur et un mot de passe
        $login_result = ftp_login($conn_id, "pmc_robot", "Cm3_Pc9l");
        
        //envoyer la date et ip de connexion
        //....

        // Tente de t�l�chargement le fichier $remote_file et de le sauvegarder dans $handle
        if (ftp_fget($conn_id, $handle, $remote_file, FTP_ASCII, 0)) {
            //READ FILE
            if (file_exists($local_file)) { 
                $tableau = file($local_file);
                while(list($cle,$val) = each($tableau)) {
                    if ( substr($val,0,1) <> "#" ){
                        $tmp = explode('�',str_replace("\r\n",'<br />',$val));
                        $text .= '<li><u>'.$txt[$tmp[0]]."</u> : ".addslashes($tmp[1]).'</li>';
                        if ( $tmp[0] == "version" ) {
                            $text .= '<li><u>'.$txt['your_version']."</u> : ".$k['version'];
                            if ( floatval($k['version']) < floatval($tmp[1]) ) $text .= '&nbsp;&nbsp;<b>'.$txt['please_update'].'</b>';
                            $text .= '</li>';
                        }
                    }
                }
            }
        } else {
            echo "Il y a un probl�me lors du t�l�chargement du fichier $remote_file dans $local_file\n";
        }

        // Fermeture de la connexion et du pointeur de fichier
        ftp_close($conn_id);
        fclose($handle);

        //DELETE FILE
        unlink($local_file);
        
        echo 'document.getElementById("CPM_infos").innerHTML = "'.$text.'</ul>";';
    break;
}
?>