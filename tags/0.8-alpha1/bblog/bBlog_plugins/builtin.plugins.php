<?php
// this is supposed to be a GUI for plugins.
// need some help with some sort of api.
// in the mean time we'll put them in an array.
$show_plugin_menu = TRUE;
$plugin_ar  = array();

function identify_admin_plugins () {
  return array (
    'name'           =>'plugins',
    'type'           =>'builtin',
    'nicename'       =>'Plugins',
    'description'    =>'Display information about, and run plugins',
    'authors'         =>'Eaden McKee',
    'licence'         =>'GPL'
  );
}
// PLUGINS : 

function scan_for_plugins () {
    global $bBlog;
    $newplugincount = 0;
    $newpluginnames = array();
    $have_plugins = array();
    $rs = $bBlog->_adb->Execute("select * from ".T_PLUGINS);
    if($rs !== false && !$rs->EOF){
        while($plugin = $rs->FetchRow()){
            $have_plugins[$plugin['type']][$plugin['name']] = TRUE;
        }
    }
                           
            
  $plugin_files=array();
  $dir="./bBlog_plugins";
  $dh = opendir( $dir ) or die("couldn't open directory");
  while ( ! ( ( $file = readdir( $dh ) ) === false ) ) {
    if(substr($file, -3) == 'php') $plugin_files[]=$file;
  }
  closedir( $dh );
  foreach($plugin_files as $plugin_file) {
    $far = explode('.',$plugin_file);
    $type = $far[0];
    $name = $far[1];
    if($type != 'builtin') {
       include_once './bBlog_plugins/'.$plugin_file;
       $func = 'identify_'.$type.'_'.$name;
       if(function_exists($func)) {
                $newplugin = $func();
                if($have_plugins[$newplugin['type']][$newplugin['name']]!==TRUE) {
			 $q = "insert into ".T_PLUGINS." set
                         `type`='".$newplugin['type']."',
                        `name`='".$newplugin['name']."',
                         nicename='".$newplugin['nicename']."',
                         description='".addslashes($newplugin['description'])."',
			 template='".$newplugin['template']."',
                         help='".addslashes($newplugin['help'])."',
                         authors='".addslashes($newplugin['authors'])."',
                         licence='".$newplugin['licence']."'";
			 $bBlog->_adb->Execute($q);
			 $newplugincount++;
			 $newpluginnames[]=$newplugin['nicename'];

                }
       }
    }

  }
  if($newplugincount == 0) return "No new plugins found";
  else return "New plugins found : ".implode(", ",$newpluginnames);
}

if(isset($_POST['scan'])) $np = scan_for_plugins();

if(isset($_POST['scan_refresh'])) {
	$bBlog->_adb->Execute("delete  from ".T_PLUGINS);
	$np = scan_for_plugins();
	$bBlog->assign('np',"<b style='color: red;'>$np</b><br />");
}



$rs = $bBlog->_adb->Execute("select * from ".T_PLUGINS." order by type");
if($rs !== false && !$rs->EOF){
    while($plugin = $rs->FetchRow()){
        $plugins[$plugin['type']][$plugin['name']]= array( 
            "name"=>$plugin['name'],
            "id" => $plugin['id'],
            "type"=>$plugin['type'],
            "displayname"=>$plugin['nicename'],
            "description"=>$plugin['description'],
            "file"=>$plugin['type'].".".$plugin['name'].".php",
            "template"=>$plugin['template'],
            "author"=>$plugin['authors'],
            "licence"=>$plugin['licence'],
            "help"=>$plugin['help']
        );
       $plugin_ar[] = $plugins[$plugin['type']][$plugin['name']];
    }
}
// other plugin :




$p=FALSE;
if($_GET['p']) $p = $_GET['p'];
if($_POST['p']) $p = $_POST['p'];

if($p && is_array($plugins['admin'][$p])) { // successful call to plugin
	$show_plugin_menu = FALSE;
	$bBlog->assign('plugin',$plugins['admin'][$p]);
	$bBlog->assign('plugin_template','plugins/'.$plugins['admin'][$p]['template']);
	$bBlog->assign('title',$plugins['admin'][$p]['displayname']);
	include_once BBLOGROOT.'bBlog_plugins/'.$plugins['admin'][$p]['file'];
        $func = "admin_plugin_".$p."_run";
        $func($bBlog);
}

$bBlog->assign('plugin_ar',$plugin_ar);
$bBlog->assign('show_plugin_menu',$show_plugin_menu);


$bBlog->display("plugins.html");

?>