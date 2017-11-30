#!/usr/bin/php
<?php
require 'phpagi.php';

function get_contrat($agi,$fichier,$porsche=0) {
        $agi->verbose("Demande numero de contrat");
        $contrat=$agi->get_data($fichier,'3000','5');
        $numcont=$contrat['result'];
        $agi->verbose("Numero de contrat : $numcont",1);
        if ($numcont != 911) {
                $sql="SELECT * from client where contrat=$numcont";
                $req=mysql_query($sql);
                $row=mysql_fetch_object($req);
                return $row->contrat;
        }
        else {
                if ($porsche != 0) return 911;
                return 0;
        }
}

function get_hno($agi,$id) {
        $now=date('G'); $day=date('N');
        $req=mysql_query("SELECT * from client where contrat=$id");
        $row=mysql_fetch_object($req);
        $agi->verbose("Il est l'heure $now et MAX=$row->h_max MIN=$row->h_min");
        if ($now < $row->h_min || $now >= $row->h_max || $day > $row->day) {
                $agi->verbose("OK Heure Ouvree");
                return 1;
        }
        else {
                $agi->verbose("NOK heure non ouvree");
                return 0;
        }
}

function check_contrat($agi,$id,$id_log,$know=0) {
        // Panne ?
        $sql="SELECT * from client where contrat=$id";
        $req=mysql_query($sql);
        $row=mysql_fetch_object($req);
        $agi->verbose("$callid => Societé Identifié : $row->soc");
        mysql_query("UPDATE log set id_cust=$row->id, num_contrat=$row->contrat WHERE id=$id_log");
        $panne=$row->panne;
        if (!$panne) {
                if (get_hno($agi,$id)) {
                        if ($know) $agi->exec('Playback non_ouvre');
                        else $agi->exec('Playback non_ouvre');
                        mysql_query("UPDATE log set hno=1 WHERE id=$id_log");
                        $porsche=get_contrat($agi,"num_contrat",1);
                        if ($porsche != 911) {
                                $agi->exec('Playback pas_compris');
                                $msg=$agi->get_data('message','1000','1');
                                if ($msg['result'] != '*') {
                                        $agi->exec('Playback merci_de_votre_appel');
                                        $agi->hangup();
                                } else {
                                        $agi->verbose("Demande messagerie");
                                        $agi->exec('goto',"message,s,1");
                                        return  1;
                                }
                        } else {
                                 mysql_query("UPDATE log set urgence=1 WHERE id=$id_log");
                        }
                }
        } else {
            //  $agi->exec('Playback incident_en_cours');
                $porsche=$agi->get_data('incident_en_cours','1000','3');
                mysql_query("UPDATE log set panne=1 WHERE id=$id_log");
                if ($porsche['result'] != 911) {
                        $agi->exec('Playback pas_compris');
                        $agi->exec('Playback merci_de_votre_appel');
                        $agi->hangup();
                } else {
                        return 911;

                }
        }
}



$agi = new AGI();
$db = 'base_de_donnée';
$dbuser = 'username';
$dbpass = 'password';
$dbhost = 'mysql_host';
mysql_connect($dbhost,$dbuser,$dbpass);
mysql_select_db("$db");


$agi->answer();
$callerid=$callid=$agi->request[agi_callerid];

$agi->verbose("New Call CallerID=> $callid");
$agi->set_variable("IDLOG","0");
$agi->verbose("Creation du log pour le CALLERID=$callid");
$sql="INSERT INTO log (clid,date) VALUES (\"$callid\",Now())";
$req=mysql_query($sql);
$id_log=mysql_insert_id();
$agi->set_variable("IDLOG","$id_log");
$agi->verbose("IDLOG=> $id_log");
$agi->verbose("$callid => Message Welcome");
$agi->exec('Playback welcome');
/*$agi->verbose("$callid => Demande enregistrement");
$chkrecord=$agi->get_data('enregistrement','3000','1');
$record=$chkrecord['result'];
if ($record == 1) {
        $agi->verbose("$callid => Refus enregistrement");
        $agi->exec('StopMixMonitor');
        $agi->set_variable("ENREGISTREMENT","REFUS");
} else {
        $agi->verbose("$callid => Enregistrement OK");
}
*/

// Verification si le calid est connu
$callerid=substr($callerid,-7);
$agi->verbose("Callerid CUT => $callerid");
$sql="SELECT * from num_tel where tel LIKE '%$callerid'";
$req=mysql_query($sql);
$row=mysql_fetch_object($req);
$id_contrat=$row->num_contrat;
if (!empty($id_contrat)) {
         $do_support=check_contrat($agi,$id_contrat,$id_log,1);
} else {
// Pas detect auto
        $id_contrat=get_contrat($agi,"start_num_contrat");
        if (empty($id_contrat)) {
                $agi->verbose("$callid => Contrat Invalide!");
                $agi->exec('Playback pas_compris');
                $id_contrat=get_contrat($agi,"num_contrat");
                if (empty($id_contrat)) {
                        $agi->verbose("$callid => Contrat Invalide! Pas de contrat ...");
                        $agi->exec('Playback pas_de_contrat');
                        $agi->exec('Playback merci_de_votre_appel');
                        $agi->hangup();
                } else {
                        $do_support=check_contrat($agi,$id_contrat,$id_log);
                }
        } else {
                $do_support=check_contrat($agi,$id_contrat,$id_log);
        }
}
        //mysql_query("UPDATE log set id_cust=$id_contrat, num_contrat=$id_contrat WHERE id=$id_log");
//$do_support=check_contrat($agi,$id_contrat,$id_log);
if ($do_support != 1) {
        $sqlcli=mysql_query("SELECT * from client where contrat=$id_contrat");
        $rowcli=mysql_fetch_object($sqlcli);
        $soc=$rowcli->soc;
        // Send 2 Prowl
        $agi->verbose("Support pour $soc");
        $event=urlencode("Appel de $soc");
        $desc=urlencode("Numéro: $callid");
        $prowl=file_get_contents("https://prowlapp.com/publicapi/add?apikey=[YOUR_API_KEY]&application=SUPPORT&event=$event&description=$desc&priority=1");
        $agi->verbose("$callid => Demande enregistrement");
        $chkrecord=$agi->get_data('demande_enregistrement','1000','1');
        $record=$chkrecord['result'];
        if ($record == 1) {
                $agi->verbose("$callid => Refus enregistrement");
                $agi->exec('StopMixMonitor');
                $agi->set_variable("ENREGISTREMENT","REFUS");
        } else {
                $agi->verbose("$callid => Enregistrement OK");
        }
}




?>
