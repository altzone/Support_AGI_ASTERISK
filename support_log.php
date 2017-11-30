#!/usr/bin/php
<?php
require 'phpagi.php';
$agi = new AGI();
$db = 'base_de_données';
$dbuser = 'username';
$dbpass = 'password';
$dbhost = 'mysql_host';
mysql_connect($dbhost,$dbuser,$dbpass);
mysql_select_db("$db");

$id_log    = $agi->get_variable('IDLOG');
$logid     = $id_log['data'];
$agi->verbose("IDLOG=> $logid");

if (!empty($logid)) {
        $duration   = $agi->get_variable('CDR(billsec)');
        $billsec    = $duration['data'];
        $getrecord  = $agi->get_variable('ENREGISTREMENT');
        $record     = "$getrecord[data]";
        $getvm      = $agi->get_variable('VM_MESSAGEFILE');
        $vm         = "$getvm[data]";

        $agi->verbose("Duree du support => $billsec Sec IDLOG=$logid Record=$record VM=$vm)");
        $sql="UPDATE log set duration=\"$billsec\", record=\"$record\", message=\"$vm\" WHERE id=$logid";
        $req=mysql_query($sql);
}
