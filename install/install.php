<?
@session_name(str_replace(" ","_","R_F_S_INSTALLER"));
@session_cache_expire(99999);
@session_start();

function odb(){
	$mysql=@mysql_connect(	$GLOBALS['authdbaddress'], $GLOBALS['authdbuser'], $GLOBALS['authdbpass']);
	if(empty($mysql))	{
		return false;
	}
	mysql_select_db( $GLOBALS['authdbname'], $mysql);
	return $mysql;
}
function sc_query($query) {
	if(stristr($query,"`users`")) { $x=sc_query_user_db($query); return $x; }
	$mysql=odb(); if($mysql==false) return false;
	$result=mysql_query($query,$mysql);
	if(empty($result)) return false;
	return $result;
}
function sc_query_user_db($q){
    $r=sc_query_other_db($GLOBALS['userdbname'], $GLOBALS['userdbaddress'], $GLOBALS['userdbuser'],$GLOBALS['userdbpass'],$q);
    return$r;
}
function sc_query_other_db($db,$host,$user,$pass,$query){
	$mysql=mysql_connect($host,$user,$pass);
	mysql_select_db($db, $mysql);
	$result=mysql_query($query,$mysql);
	return $result;
}

$RFS_SITE_PATH = getcwd();
$cwdx=explode("/",$RFS_SITE_PATH);
$installd=array_pop($cwdx);
$RFS_SITE_PATH=join("/",$cwdx);
if($installd!="install") {
	$RFS_SITE_PATH.="/".$installd;
	chdir("install");
}

$RFS_SITE_URL  = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
$hostx=explode("/",$RFS_SITE_URL);
$page=array_pop($hostx);
$folder=array_pop($hostx);
$RFS_SITE_URL=join("/",$hostx);
if($folder!="install") $RFS_SITE_URL.="/".$folder;

$table_width="85%";

include("../include/version.php");
$build=file_get_contents("../build.dat");
$RFS_VERSION.=" BUILD $build";

echo "<html><head><title>RFS CMS $RFS_VERSION Installer</title>";
echo "<link rel=\"stylesheet\" href=\"$RFS_SITE_URL/install/install.css\" type=\"text/css\">\n";
echo "</head><body style='
background-image: url(\"$RFS_SITE_URL/install/install.bkg.jpg\");
background-width: 100%;
background-height: 100%;
background-repeat: no-repeat;
background-attachment: fixed;
'>";

echo "<div width=100% style='background-color: blue; color:white;'>
RFS CMS $RFS_VERSION Installation.
For full docs and support goto
<a href=\"http://www.sethcoder.com/modules/wiki/rfswiki.php?name=RFS+Content+Management+System\" 
target=_blank>SethCoder.com</a> (Home of RFS CMS)
</div>
";

foreach( $_REQUEST as $k => $v ) { if(stristr($k,"rfs_")) { $GLOBALS["$k"]=$v; } }

$action=$_REQUEST['action'];

if(($action=="go_install") || (empty($action)) ) {

	echo "<center> <p></p><p></p>";
    echo "<table border=0 width=$table_width><tr><td class=formboxd>";
	echo "<center><h1> RFS CMS $RFS_VERSION </h1></center>";
    echo "</td></tr></table>";
    echo "
        <table border=0 width=$table_width><tr><td class=formboxd>
		<br>";
		echo "
        <p align=center>Really Frickin Simple Content Management System (RFSCMS) $RFS_VERSION Installation.</p>";
		echo "
<p align = center>
Thank you for deciding to try out RFSCMS.<br>
I have put a lot of hard work into making this over the years.<br>
My goal was to make the best possible CMS, while at the same time making it easy to use.<br>

If you have any questions or comments, or would like to make suggestions for further releases of RFS, please contact me.<br>
<br>
<a href='mailto:defectiveseth@gmail.com'>Email me!</a></p>";

  echo "<p align=center>
  Follow me on Twitter!<br>
  <br>
<a href=\"https://twitter.com/sethcoder\"
class=\"twitter-follow-button\"
data-show-count=\"true\"
data-show-screen-name=\"false\"> </a> <script>!function(d,s,id)
{var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id))
	{js=d.createElement(s);js.id=id;
	js.src=\"//platform.twitter.com/widgets.js\";
	fjs.parentNode.insertBefore(js,fjs);}}(document,\"script\",\"twitter-wjs\");
	</script> </p>";

echo '
<p align=center>
<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WP3BJCCH5P8N2">Please Read! A personal appeal by the founder of RFS CMS...<br>
Like my site? Then please donate so I can buy nom noms...</a>
</p>';
        echo "<br></td></tr></table>
        <table width=$table_width><tr><td class=formboxd><center>
        <br>
       <p align=center>I'm going to ask you some questions, and I want to have them answered immediately.</p>
        <p align=center>If you don't know what to enter, you should contact your system administrator.
		Press the button to continue...</p>
        <a href=$RFS_SITE_URL/install/install.php?action=step_a&submit=Begin>BEGIN!</a>
        <br>&nbsp;<br>
        </td></tr></table>
        </center>
        ";
}


if($action=="step_b") {

if(     ($rfs_db_password   !=  $rfs_db_password_confirm) ||
        ($rfs_udb_password  !=  $rfs_udb_password_confirm) ||
        ($rfs_password      !=  $rfs_password_c)                        ) {

        echo "<div width=100% style='background-color: red; color:white;'>CAN NOT PROCEED: THE PASSWORDS DID NOT MATCH!</div>";
        $action="step_a";
	}
	else {
        if(empty($rfs_udb_name))     $rfs_udb_name=$rfs_db_name;
        if(empty($rfs_udb_address))  $rfs_udb_address=$rfs_db_address;
        if(empty($rfs_udb_user))     $rfs_udb_user=$rfs_db_user;
        if(empty($rfs_udb_password)) $rfs_udb_password=$rfs_db_password;

        echo "<div width=100% style='background-color: cyan; color: black;'>Attempting to create $RFS_SITE_PATH/config/config.php</div>";
        $fp=fopen("$RFS_SITE_PATH/config/config.php","wt");

        fwrite($fp, "<?\n// RFSCMS $RFS_VERSION http://www.sethcoder.com/\n");
        fwrite($fp, "\$GLOBALS['authdbname']    = \"$rfs_db_name\"; \n");
        fwrite($fp, "\$GLOBALS['authdbaddress'] = \"$rfs_db_address\"; \n");
        fwrite($fp, "\$GLOBALS['authdbuser']    = \"$rfs_db_user\"; \n");
        fwrite($fp, "\$GLOBALS['authdbpass']    = \"$rfs_db_password\"; \n");
        fwrite($fp, "\$GLOBALS['userdbname']    = \"$rfs_udb_name\"; \n");
        fwrite($fp, "\$GLOBALS['userdbaddress'] = \"$rfs_udb_address\"; \n");
        fwrite($fp, "\$GLOBALS['userdbuser']    = \"$rfs_udb_user\"; \n");
        fwrite($fp, "\$GLOBALS['userdbpass']    = \"$rfs_udb_password\"; \n ?>");

        fclose($fp);

        if(file_exists("$RFS_SITE_PATH/config/config.php")) {

            echo "<div width=100% style='background-color: green; color:white;'>$RFS_SITE_PATH/config/config.php created</div>";

            $GLOBALS["authdbname"]    = $rfs_db_name;
            $GLOBALS["authdbaddress"] = $rfs_db_address;
            $GLOBALS["authdbuser"]    = $rfs_db_user;
            $GLOBALS["authdbpass"]    = $rfs_db_password;
            $GLOBALS["userdbname"]    = $rfs_udb_name;
            $GLOBALS["userdbaddress"] = $rfs_udb_address;
            $GLOBALS["userdbuser"]    = $rfs_udb_user;
            $GLOBALS["userdbpass"]    = $rfs_udb_password;

			echo $GLOBALS["authdbname"]." DB IN USE <BR>";
			
///////////////////////////////////////////////////////////////////////////////
// UPDATE DATABASE install.sql
$qx=explode(";",file_get_contents("$RFS_SITE_PATH/install/install.sql"));
for($i=0;$i<count($qx);$i++) {
	$q=$qx[$i];
	sc_query("$q;");
}
$rfs_password=md5($rfs_password);
sc_query("
INSERT INTO `users` (`name`, `pass`, `real_name`, `email`, `access`, `access_groups`, `theme`) VALUES
('$rfs_admin', '$rfs_password', '$rfs_admin_name', '$rfs_admin_email',  '255',  'Administrator', 'default'); ");
					
///////////////////////////////////////////////////////////////////////////////
// CHECK DATABASE				
$r=sc_query("select * from users");
$n=0;
if($r) $n=mysql_num_rows($r);
if(!$n) {
	echo "<div width=100% style='background-color: red; color:white;'>Database error! database: $rfs_udb_name, $rfs_udb_address, $rfs_udb_user, $rfs_udb_password </div>";
	$action="step_a";
} else {

echo "<div width=100% style='background-color: green; color:white;'>Database activated... Adding RFSCMS junk so stuff works correctly.</div>";
///////////////////////////////////////////////////////////////////////////////
// UPDATE DATABASE				
sc_query("
INSERT INTO `site_vars` (`name`, `value`) VALUES
('path', '$RFS_SITE_PATH'),
('url',  '$RFS_SITE_URL'),
('name', '$rfs_site_name'); ");
///////////////////////////////////////////////////////////////////////////////
// UPDATE DATABASE install.data.sql
$f="$RFS_SITE_PATH/install/install.data.sql";
$qx=explode("-;-",file_get_contents($f));
for($i=0;$i<count($qx);$i++) {
	$q=$qx[$i];
	echo "<hr>";
	echo nl2br("$q;");
	sc_query("$q;");
}
///////////////////////////////////////////////////////////////////////////////
		
///////////////////////////////////////////////////////////////////////////////
// Make system folders
system("mkdir $RFS_SITE_PATH/log");
system("mkdir $RFS_SITE_PATH/files");
system("mkdir $RFS_SITE_PATH/files/pictures");
system("mkdir $RFS_SITE_PATH/images");
system("mkdir $RFS_SITE_PATH/images/avatars");


           echo "  <center> <p>&nbsp;</p><p>&nbsp;</p>
					<table border=0 width=$table_width>
					<tr><td class=formboxd><center>
					<p>Congratulations, Your site is configured!</p>
					<p>You should now delete the install folder, 
					unless you want malicious stuff to happen.</p>	
					<p>The page should redirect in 5 seconds, if it
					doesn't click here <a href=$RFS_SITE_URL>$RFS_SITE_URL</a>
					to begin customizing</p>
					<p> &nbsp; </p>
					</center>
				   </td></tr></table>";
				   
				echo "<META HTTP-EQUIV=\"refresh\" content=\"5;URL=$RFS_SITE_URL\">";
            }
        }
        else {
            echo "<div width=100% style='background-color: red; color:white;'>Can not create $RFS_SITE_PATH/config/config.php make sure $RFS_SITE_PATH/config folder permission is set to 777</div>";
            $action="step_a";
        }
	}
}


if($action=="step_a") {

    echo "<div width=100% style='background-color: green; color:white;'>Detected file path: $RFS_SITE_PATH</div><div width=100% style='background-color: green; color:white;'>Detected domain name: $RFS_SITE_URL</div>";
	if(empty($rfs_site_name))   $rfs_site_name="My Cool RFS Site!";
	if(empty($rfs_site_path))   $rfs_site_path=$RFS_SITE_PATH;
	if(empty($rfs_db_name))     $rfs_db_name="rfs_cms";
	if(empty($rfs_db_address))  $rfs_db_address="localhost";
	if(empty($rfs_db_user))     $rfs_db_user="rfs_cms_user";
	if(empty($rfs_admin))       $rfs_admin="Your_User_Name";
	if(empty($rfs_admin_name))  $rfs_admin_name="Your Real Name";
	if(empty($rfs_country))     $rfs_country="Country of your home";
	if(empty($rfs_admin_email)) $rfs_admin_email="youremail@youremaildomain.what";

    echo "  <center> <p></p><p></p><table border=0 width=$table_width><tr><td class=formboxd>
            <center><h1>Enter your information</h1></center>
            </td></tr></table>
            <table width=$table_width><tr><td class=formboxd><center>
            <table width=100%><tr><td><p></p></td></tr></table>
            <table width=100%>
            <tr><td>
<form action=\"install.php\" method=\"POST\" enctype=\"application/x-www-form-URLencoded\"></td>                <td>                    <input type=hidden name=action value=step_b>
</td></tr><tr>

<td>Name of your site (not domain name)</td>
<td><input size=100 type=\"text\" name=\"rfs_site_name\" value=\"$rfs_site_name\"></td>
</tr>

<tr>
<td>Your Username for the site</td>
<td><input size=100 type=\"text\" name=\"rfs_admin\" value=\"$rfs_admin\"></td>
</tr>

<tr>
<td>Your password for the site</td>
<td><input size=100 type=\"password\" name=\"rfs_password\" value=\"$rfs_password\"></td>
</tr>

<tr>
<td>Your password for the site (confirm)</td>
<td><input size=100 type=\"password\" name=\"rfs_password_c\" value=\"$rfs_password_c\"></td>
</tr>

<tr>
<td>Your Real Name</td>
<td><input size=100 type=\"text\" name=\"rfs_admin_name\" value=\"$rfs_admin_name\"></td>
</tr>

<tr>
<td>Your email address</td>
<td><input size=100 type=\"text\" name=\"rfs_admin_email\" value=\"$rfs_admin_email\"></td>
</tr>

<tr>
<td>Working directory</td>
<td><input size=100 type=\"text\" name=\"rfs_site_path\" value=\"$rfs_site_path\"></td>
</tr>
<tr>
<td>Database Name

<a href=\"$rfs_site_url/3rdparty/phpmyadmin/\" target=_blank>Set up Database</a>
</td>
<td><input size=100 type=\"text\" name=\"rfs_db_name\" value=\"$rfs_db_name\"></td>
</tr>
<tr>
<td>Database Address</td>
<td><input  size=100 type=\"text\" name=\"rfs_db_address\" value=\"$rfs_db_address\"></td>
</tr>
<tr>
<td>Database User</td>
<td><input  size=100 type=\"text\" name=\"rfs_db_user\" value=\"$rfs_db_user\"></td>
</tr>
<tr>
<td>Database Password</td>
<td><input size=100 type=\"password\" name=\"rfs_db_password\" value=\"\">                    </td>
</tr>
<tr>
<td>(Confirm Password)</td>
<td><input size=100 type=\"password\" name=\"rfs_db_password_confirm\" value=\"\"></td>
</tr>
<tr>

<tr>
<td>User Database Name (leave blank if it is the same database as above)</td>
<td><input size=100 type=\"text\" name=\"rfs_udb_name\" value=\"$rfs_udb_name\"></td>
</tr>
<tr>
<td>User Database Address</td>
<td><input  size=100 type=\"text\" name=\"rfs_udb_address\" value=\"$rfs_udb_address\"></td>
</tr>
<tr>
<td>User Database User</td>
<td><input  size=100 type=\"text\" name=\"rfs_udb_user\" value=\"$rfs_udb_user\"></td>
</tr>
<tr>
<td>User Database Password</td>
<td><input size=100 type=\"password\" name=\"rfs_udb_password\" value=\"\">                    </td>
</tr>
<tr>
<td>(Confirm Password)</td>
<td><input size=100 type=\"password\" name=\"rfs_udb_password_confirm\" value=\"\"></td>
</tr>
<tr>
<td></td>
<td><input type=submit name=submit value=\"Proceed\">

</form>

</td>
</tr>

</table>
</td></tr></table>
</center> ";
}
