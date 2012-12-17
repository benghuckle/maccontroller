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
  
  case "itunes":
  $itunes = new iTunes;
  if ($_REQUEST['cmd'])
    echo $itunes->action($_REQUEST['cmd']);
  elseif ($_REQUEST['info'])
    echo $itunes->info($_REQUEST['info']);
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
  //exec("osascript -e 'tell app \"System Events\" to display dialog \"Hello World\"'");
  //exec("osascript -e 'tell app \"System Events\" to display dialog \"This mac is restarting\"'");
  //exec("osascript -e 'tell app \"Finder\" to activate' -e 'tell app \"Finder\" to display dialog \"You are connected to this mac\"'");

 //exec("osascript -e 'say \"Ben is not cool\"'");
  //exec("osascript -e 'tell application \"Finder\" to make new Finder window'");
 //exec("osascript -e 'tell application \"Byword\" to launch'");
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

/**
* iTunes controls
*/
class iTunes
{
  public function action($cmd) {
    exec("osascript -e 'tell application \"iTunes\" to $cmd'");
    switch($cmd) {
      case "playpause":
      case "stop":
      case "play":
      case "pause":
      exec("osascript -e 'tell application \"iTunes\" to return player state'",$response);
      return "iTunes is ".$response[0];
      break;
      case "next track":
      case "previous track":
      case "back track":
      return $this->info('track');
      break;
      case (preg_match("/^set the sound volume/i", $cmd)?$cmd:!$cmd):
      exec("osascript -e 'tell application \"iTunes\" to return the sound volume'",$response);
      return "Volume: $response[0]%";
      break;
      case (preg_match("/^set the current EQ/i", $cmd)?$cmd:!$cmd):
      return $this->info('eq');
      break;
      default:
      return $cmd;
      break;
    }
  }
  public function info($type) {
    switch($type) {
      case "track":
      return "Current track: ".exec("osascript -e 'tell application \"iTunes\" to return name of current track & \" by \" & artist of current track'");
      break;
      case "eq":
      return "EQ Preset: ".exec("osascript -e 'tell application \"iTunes\" to return name of current EQ preset'");
      break;
    }
  }
}

?>