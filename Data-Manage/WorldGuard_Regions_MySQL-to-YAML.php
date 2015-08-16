<?php

#   MySQL-2-Yaml
#   Minecraft WorldGuard regions converter
#   written by Amin, version 2013-08-27 0001

$db_host = 'localhost';
$db_name = 'minecraft';
$db_user = 'minecraft_db_user';
$db_pswd = 'minecraft_db_password';

$cu_world = isset($_GET['world']) ? $_GET['world'] : 'world';
$cu_world = addslashes($cu_world);

$conn = mysql_connect($db_host, $db_user, $db_pswd) or print mysql_error();
mysql_selectdb($db_name) or print mysql_error();

print "<pre>regions:\n";

$qr = mysql_query (" SELECT rc.* FROM `region_cuboid` rc
                    INNER JOIN world w ON rc.world_id = w.id
                    WHERE w.name = '$cu_world' ") or print mysql_error();
while ($rc = mysql_fetch_array($qr)) {
   $region_id = $rc['region_id'];
   $world_id = $rc['world_id'];
   print "    $region_id:
        type: cuboid".'
        min: {x: '.$rc['min_x'].', y: '.$rc['min_y'].', z: '.$rc['min_z'].'}
        max: {x: '.$rc['max_x'].', y: '.$rc['max_y'].', z: '.$rc['max_z'].'}
        priority: 0';
   $qf = mysql_query(" SELECT * FROM `region_flag` WHERE region_id = '$region_id' AND world_id = '$world_id' ") or print mysql_error();
      $flags_str = '';
      while ($rf = mysql_fetch_array($qf)) {
         $flags_str .= $rf['flag'].': '.substr($rf['value'], 0, -1).', ';
      }
      $flags_str = substr($flags_str, 0, -2);
   $qo = mysql_query("SELECT u.name FROM `region_players` rp
                      INNER JOIN `user` u ON u.id = rp.user_id
                      WHERE rp.region_id = '$region_id' AND rp.world_id = '$world_id' AND owner = '1' ") or print mysql_error();
      $owners_str = '';
      while ($ro = mysql_fetch_array($qo)) {
         $owners_str .= $ro['name'].', ';
      }
      $owners_str = substr($owners_str, 0, -2);
   $qp = mysql_query("SELECT u.name FROM `region_players` rp
                      INNER JOIN `user` u ON u.id = rp.user_id
                      WHERE rp.region_id = '$region_id' AND rp.world_id = '$world_id' AND owner = '0' ") or print mysql_error();
      $players_str = '';
      while ($ro = mysql_fetch_array($qp)) {
         $players_str .= $ro['name'].', ';
      }
      $players_str = substr($players_str, 0, -2);
   print "\n        flags: {".$flags_str."}";
   print "\n        owners:
            players: [".$owners_str."]";
   print "\n        members:";
   if ($players_str != '') { print "\n         players: [".$players_str."]"; } else { print " {}"; };
   print "\n";
}

?>
