<?php
switch ($_REQUEST['func'])
{
  case "mute":
  echo mutev();
  break;
  
  case "mutescript":
  exec('scripts/mutetoggle.applescript',$retval);
  echo $retval[0];
  break;
  
  case "checkvol":
  echo chkvol();
  break;
  
  case "volup":
  echo volchange("up");
  break;
  
  case "voldown":
  echo volchange("down");
  break;
  
  case "testvol":
  testvol();
  break;
  
  case "reboot":
  reboot();
  break;
  
  case "identify":
  identify();
  break;
  
  case "appstate":
  echo isrunning($_REQUEST['app']);
  break;
  
  case "startapp":
  echo startapp($_REQUEST['app']);
  break;
  
  case "stopapp":
  echo stopapp($_REQUEST['app']);
  break;
  
  case "":
  default:
  echo "No command";
  break;
}

function isrunning($app) {
  exec("osascript -e 'tell application \"System Events\" to (displayed name of processes) contains \"$app\"'",$response);
  if ($response[0] == 'true')
    return '1';
  else
    return '0';
}

function startapp($app) {
  exec("osascript -e 'tell application \"$app\" to launch'",$response);
  if (isrunning($app) == "1")
    return "$app launched";
  else
    return "Error launching $app";
}

function stopapp($app) {
  exec("osascript -e 'tell application \"$app\" to quit'",$response);
  return "$app stopped";
}

function chkvol()
{
  return exec("osascript -e 'get (output volume of (get volume settings))'");
}

function testvol() {
  exec("osascript -e 'say \"Testing System Volume\"'");
}

function reboot() {
 exec("osascript -e 'tell application \"System Events\" to restart'");
 return "done";
}

function identify() {
  exec("osascript -e 'tell app \"Finder\" to activate' -e 'tell app \"Finder\" to display dialog \"You are connected to this mac\"'");
 return "done";
}


function mutev() {
  $data = file_get_contents("scripts/volume.txt");

  $logfile = fopen("scripts/volume.txt",'w');

  $oldvolume = chkvol();
  fwrite($logfile,$oldvolume);
  if ($oldvolume > 0) {
    exec("osascript -e 'set volume output volume 0'");
    return "Muted";
  } else {
    exec("osascript -e 'set volume output volume $data'");
    return "Volume reset: $data";
  }
  fclose($logfile);
}

function volchange($diff) {
  $logfile = fopen("scripts/volume.txt",'w');
  $oldvolume = chkvol();
  if ($diff == "up")
    $newvolume = $oldvolume + 10;
  else
    $newvolume = $oldvolume - 10;
  exec("osascript -e 'set volume output volume $newvolume'");
  fwrite($logfile,$newvolume);
  fclose($logfile);
  return "Volume: $newvolume";
}




?>