<?
/////////////////////////////////////////////////////////////////////////////////////////
// RFS CMS (c) 2012 Seth Parson http://www.sethcoder.com/
/////////////////////////////////////////////////////////////////////////////////////////

if(array_pop(explode("/",getcwd()))=="include")
	chdir("..");


include_once("include/lib.div.php");
include_once("config/config.php");
include_once("include/session.php");
sc_div(__FILE__);

if($act=="select_image_go") {
	include("lib.all.php");
    $npath=$_SESSION['select_image_path']."/".$npath;
    $npath=str_replace($RFS_SITE_PATH,"",$npath);
    echo "Image changed to $npath<BR>";
    echo "<br>$rtnpage , $rtnact, $table, $id, $image_field, $npath <br>";
    $q="update `$table` set `$image_field` = '$npath' where `id`='$id'";
    echo $q;
    sc_query($q);


    echo "<META HTTP-EQUIV=\"refresh\" content=\"0;URL=$RFS_SITE_URL/$rtnpage?action=$rtnact\">";
    exit();
}

if($act=="select_image_chdir") {
    include("lib.all.php");
    sc_selectimage($npath, $rtnpage, $rtnact, $table, $id, $image_field);
}


function sc_phpself() { eval(scg()); // /%22%3E%3Cscript%3Ealert('xss')%3C/script%3E%3Cfoo%22
	$page=htmlentities($RFS_SITE_URL.$_SERVER['PHP_SELF']);
	return $page;
}

function sc_selectimage($npath, $rtnpage, $rtnact, $table, $id, $image_field) { eval(scg());

    if(!stristr($_SESSION['select_image_path'],$RFS_SITE_PATH))
        $_SESSION['select_image_path']=$RFS_SITE_PATH.$_SESSION['select_image_path'];

    if($npath==".."){
        $dx=explode("/",$_SESSION['select_image_path']);
        $dp=array_pop($dx);
        $_SESSION['select_image_path']=join("/",$dx);
        echo $_SESSION['select_image_path']."2<BR>";
    }
    else {
        $dc=$_SESSION['select_image_path']."/".$npath;
        echo $dc."<br>";
        if( (filetype($dc)=="dir") ||
            (filetype($dc)=="link") ) {
            $_SESSION['select_image_path']=$dc;
        }
    }

    $wh=mfo1("select * from `$table` where id='$id'");
    echo "Select Image for (Table $table id[$id] ($wh->name) field[$image_field])<br>";

    $thispath=$_SESSION['select_image_path'];
    echo "$thispath<br>";


    $dir_count=0;
    $dirfiles = array();
    $handle=opendir($thispath) or die("Unable to open filepath");
    while (false!==($file = readdir($handle))) array_push($dirfiles,$file);
    closedir($handle);
    reset($dirfiles);
    asort($dirfiles);
    while(list ($key, $file) = each ($dirfiles)){
        if($file!=".") {
                $op="$thispath/$file";
                $ot=$_SESSION['select_image_path']."/$file";
                /// echo " $op $ot<br>";

                if( (@filetype("$op")=="dir") ||
                    (@filetype("$op")=="link") ) {
                   $out="act=select_image_chdir&rtnpage=$rtnpage&rtnact=$rtnact&id=$id&npath=";
                   $out.=urlencode($file);
                   $out.="&table=$table&image_field=$image_field&spath=$RFS_SITE_PATH";
                   echo "<a href='$RFS_SITE_URL/include/lib.mysql.php?$out'>
                   <img src='$RFS_SITE_URL/images/icons/Folder.png' width=32>($file)</a>";

                            }
                        }
                    }
                    echo "<hr>";
                    reset($dirfiles);
                    asort($dirfiles);
                    while(list ($key, $file) = each ($dirfiles)){
                    if($file!=".") if($file!="..") {
                            $op="$thispath/$file";
                            if(@filetype("$op")=="file") {

                                $ft = sc_getfiletype($op);
                                // echo "$ft<br>";
                if( ($ft=="jpg") || ($ft=="png") || ($ft=="gif") || ($ft=="ico") || ($ft=="bmp") ||($ft=="jpeg") ){
                    $out="act=select_image_go&rtnpage=$rtnpage&rtnact=$rtnact&id=$id&npath=";
                    $out.=urlencode($file);
                    $out.="&table=$table&image_field=$image_field&spath=$RFS_SITE_PATH";
                    echo "<a href='$RFS_SITE_URL/include/lib.mysql.php?$out'><img src='$RFS_SITE_URL/include/button.php?im=".$_SESSION['select_image_path']."/$file&t=$file&w=96&h=96&y=90&fcr=1&fcg=255&fcb=1' border=0></a>";
                }
            }
        }
    }
    for($xyz=0;$xyz<20;$xyz++) echo "<br>";
}

/////////////////////////////////////////////////////////////////////////////////////////
function sc_is_csv_data($table,$where,$field,$var) {
    $r=sc_query("select * from $table where $where");
    $row=mysql_fetch_array($r);
    $fx=explode(",",$row["$field"]);
    for($i=0;$i<count($fx);$i++){
        if($fx[$i]==$var) return true;
    }
    return false;
}
/////////////////////////////////////////////////////////////////////////////////////////
function sc_set_csv_data($table,$field,$var) {

}
/////////////////////////////////////////////////////////////////////////////////////////
function sc_clear_csv_data($table,$field,$var) {

}
/////////////////////////////////////////////////////////////////////////////////////////
function sc_access_check($func_page,$act){

	$d=$GLOBALS['data']; $ax=$d->access;

	if(empty($func_page)) $func_page=sc_phpself();

	$q="select * from `access` where `access`='$ax' and `page`='$func_page' and action='$act'";
	$r=sc_query($q);
	$ret=false;
	if(mysql_num_rows($r)) {
		$ret=true;
	}
	d_echo( "sc_admin_check(): $ret" );
	d_echo( $func_page );
	d_echo( $ax );
	d_echo( $act );
	d_echo( "[$q]");

	// if(!$ret) $ret="false"; 
	// sc_info("ACCESS CHECK: ($ax) $func_page $act : ($ret)","WHITE","RED");

	return $ret;
}
/////////////////////////////////////////////////////////////////////////////////////////
function usersonline($name) {
	if(empty($name)) $name="(Guest)";
	$li="0";
	$data=$GLOBALS['data'];
	if($data->name==$name) $li="1";

	$REMOTE_ADDR=getenv("REMOTE_ADDR");

	// $PHP_SELF=$_SERVER['PHP_SELF'];
	$PHP_SELF=sc_phpself();

	$refer=getenv("HTTP_REFERER");
	$timeoutseconds = 300;
	$timestamp = time();
	$timeout = $timestamp-$timeoutseconds;

	$res=sc_query("select * from useronline where `ip`='$REMOTE_ADDR'");
	if(mysql_num_rows($res)) {
		$usro=mysql_fetch_object($res);
		$insert = sc_query("update useronline set `name`='$name' where `ip`='$REMOTE_ADDR'");
		$insert = sc_query("update useronline set `timestamp`='$timestamp' where `ip`='$REMOTE_ADDR'");
		$insert = sc_query("update useronline set `loggedin`='$li' where `ip`='$REMOTE_ADDR'");

	} else {
		$res=sc_query("select * from useronline where name='$name'");
		if(mysql_num_rows($res)) {
			$insert = sc_query("update useronline set timestamp='$timestamp' where name='$name'");
			$insert = sc_query("update useronline set page='$PHP_SELF' where name='$name'");
			$insert = sc_query("update useronline set `loggedin`='$li' where `ip`='$REMOTE_ADDR'");
		} else {
			$insert = sc_query("INSERT INTO useronline
			                   (`timestamp`, `ip`,           `name`,   `loggedin`, `page`)
			                   VALUES ('$timestamp','$REMOTE_ADDR', '$name',  '$li', '$PHP_SELF')"); // '$refer',
		}

		// if(!($insert)) { print "Useronline Insert Failed > "; }
	}

	$delete = sc_query("DELETE FROM useronline WHERE timestamp<$timeout");

	$result = sc_query("SELECT DISTINCT ip FROM useronline");
	$user = mysql_num_rows($result);
	return $user;
}
/////////////////////////////////////////////////////////////////////////////////////////
function usersloggedin() {
	$result = sc_query("SELECT DISTINCT ip FROM useronline WHERE loggedin='1'");
	$user = mysql_num_rows($result);
	return $user;
}
/////////////////////////////////////////////////////////////////////////////////////////
function users_logged_details() {
	$result = sc_query("SELECT DISTINCT ip,page,name FROM useronline");
	$nusers = mysql_num_rows($result);
	$user="";
	for($i=0; $i<$nusers; $i++) {
		$usrdata=mysql_fetch_object($result);
		// $usrdata->page=str_replace("/","",$usrdata->page);
		$pg=explode("/",$usrdata->page);
		$upg=$pg[count($pg)-1];
		$user.="$usrdata->name ($upg)";
		if(($nusers>1) && ($i<( $nusers-1)))
			$user.="<br>";
	}
	return $user;
}
/////////////////////////////////////////////////////////////////////////////////////////
function sc_useravatar($user) { echo sc_getuseravatar($user); }
function sc_getuseravatar($user) { eval(scg()); $userdata=sc_getuserdata($user);
	$ret = "<a href=\"$RFS_SITE_URL/showprofile.php?user=$userdata->name\">\n";
	if(empty($userdata->avatar)) $userdata->avatar="$RFS_SITE_URL/images/no_avatar.gif";
    $g=sc_getfiletype($userdata->avatar);
    if(($g=="png")||($g=="bmp") ||
		($g=="gif")||($g=="jpg") )  {
        $ret.= "<img src=\"$userdata->avatar\" ";
        $ret.= "title=\"$userdata->sentence\" ";
        $ret.= "alt=\"$userdata->sentence\" width=100 border=0>";
    }
    if($g=="swf") $ret.= sc_getflashcode($userdata->avatar,100,100);
    $ret.= "</a>\n";
    return($ret);
}


/////////////////////////////////////////////////////////////////////////////////////////
function sc_setuservar($name,$var,$set) {
	sc_query_user_db("UPDATE users SET `$var`='$set' where name = '$name'");
}
function sc_setvar($table,$var,$set,$name,$sname) {
	sc_query("UPDATE `$table` SET `$var`='$set' where `$name` = '$sname'");
}
/////////////////////////////////////////////////////////////////////////////////////////
function sc_getusername($x){
    $o=$x;
    if(is_numeric($x)) {
        $ur=sc_query_user_db("select * from users where id='$x'");
        $u=mysql_fetch_object($ur);
        $o=$u->first_name;
    }
    return $o;
}
/////////////////////////////////////////////////////////////////////////////////////////
function sc_newuser($name,$pass,$e){
    $time1=date("Y-m-d H:i:s");
    $result=sc_query_user_db("INSERT INTO `users` (`name`, `pass`, `email`, `first_login`)
                                           VALUES ('$name', '$pass', '$e', '$time1');");
}
/////////////////////////////////////////////////////////////////////////////////////////
function sc_query_user_db($q){
//echo "sc_query_user_db();		";
    $r=sc_query_other_db($GLOBALS['userdbname'], $GLOBALS['userdbaddress'], $GLOBALS['userdbuser'],$GLOBALS['userdbpass'],$q);
    return$r;
}
/////////////////////////////////////////////////////////////////////////////////////////
function sc_is_alias($x) {
    $data=sc_getuserdata($_SESSION['valid_user']);
    $ax=explode(",",$data->alias);
    for($j=0;$j<count($ax);$j++) {
        if($ax[$j]==$x) return TRUE;
    }
    return FALSE;
}
/////////////////////////////////////////////////////////////////////////////////////////
function sc_getuserdata($name){
    if(is_numeric($name)){
		$result = sc_query_user_db("select * from `users` where `id` = '$name'");
		$d=mysql_fetch_object($result); 
		return ($d);
	}	
    else {
        $r=sc_query_user_db("select * from users");
        $n=mysql_num_rows($r);
        for($i=0;$i<$n;$i++) {
            $d=mysql_fetch_object($r);
            if($d->name==$name) return $d;
            $ax=explode(",",$d->alias);
            for($j=0;$j<count($ax);$j++) {
                if($ax[$j]==$name) return $ax[$j];
            }
        }
    }
    return 0;
}
/////////////////////////////////////////////////////////////////////////////////////////
function sc_query_other_db($db,$host,$user,$pass,$query){
$mysql=mysql_connect($host,$user,$pass);
mysql_select_db($db, $mysql);
$result=mysql_query($query,$mysql);
return $result;
}
/////////////////////////////////////////////////////////////////////////////////////////
function sc_getuserdatabyfield($field,$data){
    $result=
    sc_query_user_db("select * from users where `$field` = '$data'");
    return mysql_fetch_object($result);
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_adddownloads($user,$points){
  $result = sc_query("select * from users where name = '$user'");
  if(mysql_num_rows($result) >0 ){
    $ud=mysql_fetch_object($result);
    $downloads=intval($ud->downloads)+intval($points);
    sc_query("UPDATE users SET files_downloaded=$downloads where name = '$user'");
  }
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_delimiter($t){
	$d="\n";
	$sd=$GLOBALS['RFS_SITE_DELIMITER'];
	if(stristr($t,$sd)) 		$d=$sd;
	//if(empty($d)) if(stristr($t,",")) $d=",";
	return $d;
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_mcount($user) {
		sc_div( __FUNCTION__ . "($user) -> " . __FILE__);

	$p=$_SERVER['PHP_SELF'];
	$ip=getenv("REMOTE_ADDR");

	$r=mfo1("select * from counters where page = '$p'");

	if(!empty($r->page)){
		$r->hits_raw+=1;
		if($r->last_ip!=$ip){
			$r->last_ip=$ip;
			$r->hits_unique+=1;
		}

		sc_query("update counters set user='$user' where page='$r->page'");
		sc_query("update counters set hits_raw='$r->hits_raw' where page='$r->page'");
		sc_query("update counters set hits_unique='$r->hits_unique' where page='$r->page'");
		sc_query("update counters set last_ip='$r->last_ip' where page='$r->page'");
	}
	else{
		$ct=1;
		$q="INSERT INTO `counters` (`id`, `page`, `user`, `last_ip`, `hits_raw`, `hits_unique`)
							 VALUES (NULL, '$p', '$user', '$ip', '$ct', '$ct');";
		sc_query($q);
	}
	return $r->hits_unique;
}
///////////////////////////////////////////////////////////////////////////////////////////////
function odb(){
	$mysql=@mysql_connect(	$GLOBALS['authdbaddress'],
				$GLOBALS['authdbuser'],
                  		$GLOBALS['authdbpass']);
	if(empty($mysql))
{

 return false;
}
	mysql_select_db( $GLOBALS['authdbname'], $mysql);
	return $mysql;
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_query($query) {

// echo $query."<br>hi<br>";

	if(stristr($query,"`users`")) { $x=sc_query_user_db($query); return $x; }

// echo "sc_query(); ";

	$mysql=odb(); if($mysql==false) return false;
	$result=mysql_query($query,$mysql);
	if(empty($result)) return false;
	return $result;
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_dtv($table){
    $query ="DESCRIBE $table";
    $result = mysql_query($query);
    while($i = mysql_fetch_assoc($result)){
         echo $i['Field'];
         echo "<br>";
    }
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_tableexists($table) {
    $r=sc_query("SELECT '$table '
        FROM information_schema.tables
        WHERE table_schema = '".$GLOBALS['authdbname']."'
        AND table_name = '$table';");
    return(mnr($r));
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_newtable($table){
    if(!sc_tableexists($table)){
        $dbn=$GLOBALS['authdbname'];
        $q="CREATE TABLE  `$dbn`.`$table` (`name` TEXT NOT NULL) ENGINE = MYISAM ;";
        echo "CREATING TABLE [$table]<br>";
        $r=sc_query($q);
    }
    else{
        echo "TABLE [$table] already exists!<br>";
    }
}
///////////////////////////////////////////////////////////////////////////////////////////////
function mfo1($query){
    $res=sc_query($query);
	if($res)
		return mysql_fetch_object($res);
	else return $res;
}
///////////////////////////////////////////////////////////////////////////////////////////////
function mfo($res){
	return mysql_fetch_object($res);
}
///////////////////////////////////////////////////////////////////////////////////////////////
function mnr($res){
    if(!$res)  return 0;
	return mysql_num_rows($res);
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_db_get($table,$key,$kv,$field){
    $q="select $field from $table where $key = \"$kv\"";
    //echo $q;
    $res=sc_query($q);
    $i=mysql_fetch_assoc($res);
    reset($i);
    $j=current($i);
    return $j;
}
///////////////////////////////////////////////////////////////////////////////////////////////
// update database
function sc_updb($table,$key_field,$key_value){
    $res=sc_query("select * from `$table` where `$key_field`='$key_value' limit 1");
    if(mysql_num_rows($res)==0) sc_query("insert into `$table` (`$key_field`) values ('$key_value');");
    $res=sc_query("DESCRIBE $table");
    while($i = mysql_fetch_assoc($res)){
        //echo $i['Field']."::".$_REQUEST[$i['Field']]."<br>";
        //if($_REQUEST[$i['Field']]!=''){
            $q ="update $table set `";
            $q.=$i['Field'];
            $q.="`='".
			addslashes(
			$_REQUEST["{$i['Field']}"]
			)
			."' ";
				//$vv=
				$vv=addslashes($key_value);

				$q.="where `$key_field`='$vv'";

				//echo $_REQUEST["{$i['Field']}"];

            d_echo("[$q]");
            sc_query($q);
        //}
    }
}
///////////////////////////////////////////////////////////////////////////////////////////////
// update database (only show results dont updat database)
function sc_updb_2($table,$key_field,$key_value){
    $res=sc_query("select * from `$table` where `$key_field`='$key_value' limit 1");
    if(mysql_num_rows($res)==0)
    sc_query("insert into `$table` (`$key_field`) values ('$key_value');");
    $res=sc_query("DESCRIBE $table");
    while($i = mysql_fetch_assoc($res)){
        //echo $i['Field']."::".$_REQUEST[$i['Field']]."<br>";
        if($_REQUEST[$i['Field']]!=''){
            $q ="update $table set `";
            $q.=$i['Field'];
            $q.="`='".addslashes($_REQUEST["{$i['Field']}"])."' ";
            $q.="where `$key_field`='".addslashes($key_value)."'";
            echo "$q<br>";
            //sc_query($q);
        }
    }
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_confirmform($message,$page,$hiddenvars){
	echo "\n<sc_confirmform [START]================================================ />\n";



    echo "<table border=0 width=400>\n";
    echo "<tr><td align=center>\n";
    echo "<br>\n";
    echo "<form action=\"$page\" method=\"POST\" enctype=\"application/x-www-form-URLencoded\">\n";

    echo "<div class=confirmform>";
    echo "$message";



    $hidvar_a=explode(sc_delimiter($hiddenvars),$hiddenvars);
    for($i=0;$i<count($hidvar_a);$i++){
        $hidvar_b=explode("=",$hidvar_a[$i]);
        echo "<input type=hidden name=\"".$hidvar_b[0]."\" value=\"".$hidvar_b[1]."\">\n";
    }
    sc_makebuttonstart();
    echo "<input style='font-size:x-small' type=submit name=yes value=Yes>\n";
    echo "<input style='font-size:x-small' type=submit name=no value=No>\n";
    sc_makebuttonend();


    echo "</div>";

    echo "<br><br>\n";
    echo "</td></tr>\n";
    echo "</table>\n";
    echo "</form>\n";
	echo "<sc_confirmform [END]================================================ />\n";
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_db_query($query,$becho){
    
    $gt=0;
    
	if(stristr($query,"users")) 	 $res=sc_query_user_db($query);
    else                             $res=sc_query($query);
        
    if($res)
    if($becho){
        
        $num=@mysql_num_rows($res);
        
		//echo "<br>query returned $num results<hr>";
        echo "<table border=0 cellpadding=5>";
        $hdr=0;
        while($row=@mysql_fetch_assoc($res)){
            $gt++; if($gt>2) $gt=1;
            if($hdr==0){
                echo "<tr>";
                reset($row);
                while(key($row)!==NULL){
                    echo "<th>";
                    echo key($row);
                    echo "</th>";
                    next($row);
                }
                echo "</tr>";
                $hdr=1;
            }
            //$ra=array();
            //var_dump(array_keys($row));
            reset($row);
            //echo $row[key($row)]." ";
            echo "<tr>";
            while(key($row)!==NULL){
                echo "<td class=sc_project_table_$gt>";
                echo current($row);
                echo "</td>";
                next($row);
            }
            echo "</tr>";
            /*
            echo $row[0];
            $rnum=count($row);
            for($k=0;$k<$rnum;$k++){
                echo $row[$k]." ";
            }
            echo "<br>";
            */
        }
        echo "</table>";
        /*
        $t=array();
        for($i=0;$i<$num;$i++){
            array_push($t, mysql_result($res,$i));
            $tnum=count($t);
            for($k=0;$k<$tnum;$k++)
            echo $t[$k]." ";
            echo "<br>";
        }
        */
    }
    return $res;
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_db_query_form($page,$action,$query){
    echo "<form action=\"$page\" method=\"POST\" enctype=\"application/x-www-form-URLencoded\">";
    echo "<input type=\"hidden\" name=\"action\" value=\"$action\">";
    echo "<textarea rows=5 cols=100 name=\"query\">";
    $query=str_replace("</textarea>","&lt;/textarea>",$query);
    echo stripslashes($query);
    echo "</textarea><br>";
    echo "<input type=\"submit\" name=\"submit\" value=\"submit query\">";
    echo "</form>";
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_db_dumptable($table,$showform,$key,$search){ eval(scg());

    if(stristr($showform, $RFS_SITE_DELIMITER)) {
            $gx=explode($RFS_SITE_DELIMITER,$showform);
            $showform=$gx[0];
    }

    $page=sc_phpself();
    $gt=0;
    $res=sc_query("select * from `$table` $search");
    $num=mysql_num_rows($res);
    echo "<table border=0 cellpadding=5>";
    $hdr=0;
    while($row=mysql_fetch_assoc($res)){
        $gt++; if($gt>2) $gt=1;
        if($hdr==0){
            echo "<tr>";
            if($showform=="showform"){
            echo "<th></th>";
            }
            reset($row);
            while(key($row)!==NULL){
                echo "<th>";
                echo key($row);
                echo "</th>";
                next($row);
            }
            echo "</tr>";
            $hdr=1;
        }
        reset($row);

        echo "<tr>";

        

        $showform_action="sc_";

        if(count($gx)) {
            $showform_action=$gx[1];
        }

        if($showform=="showform"){
            echo "<td>";
            while(key($row)!=NULL) {
                if(key($row)==$key){
                    $key_val=current($row);
                }
                next($row);
            }

            sc_makebuttonstart();
            sc_makebutton("$page?action=".$showform_action."edit_$table&$key=$key_val","Edit");
            sc_makebutton("$page?action=".$showform_action."del_$table&$key=$key_val","Delete");
            sc_makebuttonend();

            echo"</td>";
        }
        reset($row);
        while(key($row)!==NULL){
            echo "<td class=sc_project_table_$gt>";
            echo nl2br(current($row));
            echo "</td>";
            next($row);
        }
        echo "</tr>";
    }
    echo "</table>";

    return $res;

}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_db_dumptables(){
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_vars_join($x){
    if(!is_array($x)) return;
     foreach ($x as $y => $z) {
        if(is_string($k) || (is_int($k) && $k < 0)){
            echo "$y -> $z";
        }
    }
}
///////////////////////////////////////////////////////////////////////////////////////////////
// Optionizer
function sc_optionizer(	$page,
							$hiddenvars,
							$table,
							$key,
							$use_id_method,
							$default,
							$on_change_method
							){



	$folder_mode=0;
	if( ($use_id_method==3) || 
		($key=="FOLDERMODE")) {
		$folder_mode=1;
		$use_id_method=0;
	}
	//echo "use_id_method[$use_id_method]<br>";
	//echo "folder_mode[$folder_mode]<br>";
	
	if($page!="INLINE")
		echo "<form action=\"$page\" method=\"POST\" enctype=\"application/x-www-form-URLencoded\">";
	$omit='';

	$hv=explode(sc_delimiter($hiddenvars),$hiddenvars);
   
    $where='';
	$distinct='';

    for($hz=0;$hz<count($hv);$hz++){
        $he=explode("=",$hv[$hz]);
        for($hy=0;$hy<count($he);$hy+=2){
			
			$dontshowhv=false;
			
			if($he[$hy]=="SELECTNAME") {
				$selname=$he[$hy+1];
				$dontshowhv=true;
				
			}
			
			if($he[$hy]=="DISTINCT") {
				$dontshowhv=true;
				if($he[$hy+1]=="TRUE"){
					$distinct=" DISTINCT ";			
				}
				
			}
			
			if($he[$hy]=="omit") {
				$dontshowhv=true;
				if($omit=='')  $omit=$he[$hy+1];
				else           $omit=$omit.", ".$he[$hy+1];
			}
			if($he[$hy]=="include") {
				$dontshowhv=true;
				if($incl=='')  $incl=$he[$hy+1];
				else			 $incl=$incl.", ".$he[$hy+1];
			}
			if($dontshowhv==true) {
				
			}
			else {
				echo "<input type=\"hidden\" name=\"".$he[$hy]."\" value=\"".$he[$hy+1]."\">";				
			}
        }
    }
	if(!empty($omit)) {
	$exomit=explode(",",$omit);
	if(count($exomit)) {
		for($omi=0;$omi<count($exomit);$omi++){
				$exwhat=explode(":",$exomit[$omi]);
				if($where==''){
					if(!empty($exwhat[0]))
						$where.=" where $exwhat[0] != '$exwhat[1]'";
				}
				else{
					if(!empty($exwhat[0]))
						$where.=" and $exwhat[0] != '$exwhat[1]'";
				}
			}
		}
	}

	if(!empty($incl)) {
		$exincl=explode(",",$incl);
		if(count($exincl)) {
			for($ini=0;$ini<count($exincl);$ini++){
					$exwhat=explode(":",$exincl[$ini]);
					if($where==''){
						if(!empty($exwhat[0]))
							$where.=" where $exwhat[0] = '$exwhat[1]'";
					}
					else{
						if(!empty($exwhat[0]))
							$where.=" and $exwhat[0] = '$exwhat[1]'";
					}
				}
			}
		}


	if($folder_mode==0) {
		 //echo "WHERE: $where";
		 if(stristr($key,sc_delimiter($key))) {
				$xkey=explode(sc_delimiter($key),$key);
				$key=$xkey[0];
				$key2=$xkey[1];				
		 } else $key2='';
		 
		 if(empty($selname)) $selname=$key;

		echo "<select id=\"optionizer_$selname\" name=\"$selname\" width=20   ";
		if($on_change_method)
			echo "onchange=\"this.form.submit()\" ";
		echo ">";
		echo "<option >$default";
		echo "<option >--- None ---";

		$scoq="select $distinct $key";
		if(!empty($key2))
			$scoq.=",$key2";
		$scoq.=",id from $table $where order by $key asc";

		$r=sc_query($scoq);
		if($r) {
			for($i=0;$i<mysql_num_rows($r);$i++){
				$d=mysql_fetch_object($r);
				echo "<option ";
				if($use_id_method){
					echo "value=\"$d->id\" ";
				}
				echo ">".$d->$key;

				if(!empty($d->$key2)) {
					echo "(".$d->$key2.")";
				}
			}
		}
		echo "</select>";
	}

	else {
		if(empty($selname)) $selname=$key;
		echo "<select id=\"optionizer_$selname\" name=\"$selname\" width=20   ";
		if($on_change_method)
			echo "onchange=\"this.form.submit()\" ";
		echo ">";
		echo "<option >$default";
		echo "<option >--- None ---";


					$dir_count=0;
					$dirfiles = array();
					$handle=opendir($table) or die("Unable to open filepath");
					while (false!==($file = readdir($handle))) array_push($dirfiles,$file);
					closedir($handle);
					reset($dirfiles);
					asort($dirfiles);

					while(list ($key, $file) = each ($dirfiles)){
					if($file!=".")
							if($file!="..")
								if(!is_dir($dir."/".$file))
									echo "<option>$file";
					}

		echo "</select>";
	}

	if($page!="INLINE") {
		if(!$on_change_method)
			echo "<input type=\"submit\" name=\"submit\" value=\"Change\">";
		echo "</form>";
	}
}
///////////////////////////////////////////////////////////////////////////////////////////////
// simple add form based on table
// (short command for build form)
function sc_bfa($table){ eval(scg());
    
    sc_bf(sc_phpself(),"action=add",$table,"","","name","include","",60,"add");
}
///////////////////////////////////////////////////////////////////////////////////////////////
// sc_bqf (build quick form)
// takes 2 vars and will build a form using sc_bf
function sc_bqf($hiddenvars,$submit){ eval(scg());	
    sc_bf(sc_phpself(),$hiddenvars, "", "", "", "", "", "", 20, $submit);
}
///////////////////////////////////////////////////////////////////////////////////////////////
// sc_bf (build form)
// $page, $hiddenvars, $table, $query, $hidevars, $specifiedvars, $svarf , $tabrefvars, $width, $submit
//
// $page        = page that the form will action
// $hiddenvars  = list of hiddenvars seperated by comma, or SHOW_XXX_#ROWS#COLS#<name>=<defaultvault> (seperated by comma)
// $table		  = which table to use
// $query       = query of fields to include in the form, if empty will use all fields
// $hidevars    = list of vars to hide, seperated by comma
// $specifiedvars = specify a var
// $svarf       = include or omit (will either include only $specifiedvars, or will omit only $specifiedvars)
// $tabrefvars  =
// $width       = default width of the form
// $submit      = the submit button text
function sc_bf($page, $hiddenvars, $table, $query, $hidevars, $specifiedvars, $svarf , $tabrefvars, $width, $submit){ eval(scg());
	$delimiter=$RFS_SITE_DELIMITER;
	$gt=1;
    if(!stristr($page,$RFS_SITE_URL)) $page="$RFS_SITE_URL/$page";

	// d_echo("HIDDEN VARS: $hiddenvars");

    if(empty($svarf)) $svarf="omit";
    echo "<table cellspacing=0 cellpadding=0>";
    echo "<tr><td>";

    echo "<form action=\"$page\" method=\"POST\" enctype=\"multipart/form-data\">";


	d_echo($hiddenvars);

	$hidvar_a=explode(sc_delimiter($hiddenvars),$hiddenvars);

    for($i=0;$i<count($hidvar_a);$i++){
        $hidvar_b=explode("=",$hidvar_a[$i]);
         d_echo("$hidvar_b[0] $hidvar_b[1]");

        if( (!stristr($hidvar_b[0],"TT_")) &&
            (!stristr($hidvar_b[0],"LABEL_")) &&
            (!stristr($hidvar_b[0],"SHOW_")) ){

				d_echo("[".$hidvar_b[0]." = ".$hidvar_b[1]."]");
				echo "<input type=hidden name=\"".$hidvar_b[0]."\" value=\"".$hidvar_b[1]."\">\n";

        }
    }

    echo "</td>";
    echo "<td></td></tr>";
    $gt++; if($gt>2) $gt=1;
	
	$hvars=explode(sc_delimiter($hidevars),$hidevars);
	$svars=explode(sc_delimiter($specifiedvars),$specifiedvars);
	$tvars=explode(sc_delimiter($tabrefvars),$tabrefvars);

    if(!empty($query)) {
        $res=sc_query($query);
		if($res){
        $dat=mysql_fetch_object($res);
        for($i=0;$i<count($hidvar_a);$i++){
            $hidvar_b=explode("=",$hidvar_a[$i]);
            if(empty($dat->{$hidvar_b[0]}))
            @eval("\$dat->".$hidvar_b[0]."=\"".$hidvar_b[1]."\";");
            //$dat[$hidvar_b[0]]=
            //d_echo("[".$hidvar_b[0]."] [".$hidvar_b[1]."]<br>");
            //d_echo({$dat['value']});
            //d_echo($dat->value);
            //if($hidvar_b[0]==))
            //{
                //echo "[".$hidvar_b[0]."=".$hidvar_b[1]."]";
                //echo "<input type=hidden name=\"".$hidvar_b[0]."\" value=\"".$hidvar_b[1]."\">\n";
            //}
        }
 //echo "[$query][$res][$dat->name][$dat->id]<br>";
		}

    }
    if(!empty($table)){
        $result = sc_query("SHOW FULL COLUMNS FROM $table");
        //echo "[$result]<br>";
        while($i = mysql_fetch_assoc($result)){


            $this_codearea=false;
            $name=ucwords(str_replace("_"," ",$i['Field']));
            //echo "[$name]<br>";
            $tref=0;
            for($k=0;$k<count($tvars);$k++){
                $tparts=explode("=",$tvars[$k]);
                if($tparts[0]==$i['Field']){
                    $tref=1;
                    $tref_table=$tparts[1];
                }
            }
            if($tref){
                echo "<tr><td class=sc_project_table_$gt align=right>\n";
                echo $name;
                echo "</td><td class=sc_project_table_$gt>";
                //echo "<br>{$i['Comment']}<br>";
                echo "<select name=\"".$i['Field']."\">";
                if(!empty($dat->{$i['Field']})){
                   $q="select * from `$tref_table` where `id`='";
                   $q.=$dat->{$i['Field']};
                   $q.="'";
                   $tres=sc_query($q);
                   $obj=mysql_fetch_object($tres);
                    echo "<option value=$obj->id>$obj->name";// echo "<option>".$dat->$i['Field'];
               }

                $tres=sc_query("select * from `$tref_table` order by `name`");
                for($k=0;$k<mysql_num_rows($tres);$k++){
                    $obj=mysql_fetch_object($tres);
                    echo "<option value=$obj->id>$obj->name";
                }
                echo "</select>";
                echo "</td></tr>";
                $gt++; if($gt>2) $gt=1;
            }
            else{
                //echo "[$svarf]<br>";
                if($svarf=="include") $omit=0;
                if($svarf=="omit") $omit=1;

                for($k=0;$k<count($svars);$k++){
                    if($svarf=="include"){
                        if($svars[$k]==$i['Field']){
                            //echo "[omit=1]<br>";
                            $omit=1;
                        }
                    }
                    if($svarf=="omit"){
                        if($svars[$k]==$i['Field']){
                            $omit=0;
                        }
                    }
                }

                if($omit==1){
                    $hidden=0;
                    $relabel=false;
                    $type="text";
                    $TT=0;
                    $rows=6;
                    $cols=$width;

                    for($k=0;$k<count($hvars);$k++){
                        if($hvars[$k]==$i['Field']){
                            $hidden=1;
                            $type="hidden";
                        }
                    }

                    //if($hidden==0)
                    //{
                        $hidvar_a=explode(sc_delimiter($hiddenvars),$hiddenvars);
                        for($j=0;$j<count($hidvar_a);$j++){
                            $hidvar_b=explode("=",$hidvar_a[$j]);
                            //echo "[$hidvar_b[0]] [$hidvar_b[1]]<br>";
                            if(stristr($hidvar_b[0],"TT_")){
                                $field=explode("TT_",$hidvar_b[0]);
                                if($field[1]==$i['Field']){
                                    $TT=1;
                                    $type=$hidvar_b[1];
                                    break;
                                    //echo "--- $field[1] $hidvar_b[1]<br>";
                                }
                                else{
                                    //echo $field[1];
                                    $rw=explode("#",$field[1]);
                                    if(count($rw)==3){
                                        $rows=$rw[0];
                                        $cols=$rw[1];
                                        $taname=$rw[2];
                                        d_echo("[3 TT_ Count]");
                                    }
                                    else if(count($rw)==2){
                                        $rows=$rw[0];
                                        $taname=$rw[1];
                                        d_echo("[2 TT_ Count]");
                                    }

                                    if($taname==$i['Field']){
                                        $TT=1;
                                        $type=$hidvar_b[1];

                                        //d_echo( "[".$rw[0]."]");
                                        d_echo( "[".$rw[1]."]");
                                        d_echo( "[".$rw[2]."]");
                                        d_echo( "[".count($rw)."]");
                                        // echo  "[".$i['Field']."]<br>" ;
                                        // echo $dat->{$i['Field']};
                                    }
                                }
                                //echo "<input name=\"userfile\" type=\"file\">";
                            }

                            if(stristr($hidvar_b[0],"LABEL_")){
                                $field=explode("LABEL_",$hidvar_b[0]);
                                if($i['Field']==$field[1])
                                    $relabel=true;
                                $label=$hidvar_b[1];
                                //$type="label";
                                //echo "FOUND LABEL:[".$field[1]."][$label][$type]<br>";
                            }
                             //   echo "<input type=hidden name=\"".$hidvar_b[0]."\" value=\"".$hidvar_b[1]."\">\n";
                        }

                        d_echo( "[$type][$rows][$cols]");

                        if($hidden==0){
                            echo "<tr><td class=sc_project_table_$gt align=right>\n";
                            if($relabel==true)  echo $label;
                            else                echo $name;
                            echo "</td><td class=sc_project_table_$gt>";
                        }
                        if($i['Field']=="password") $type="password";
                        if($i['Field']=="pass") $type="password";

                        switch($type){
                         /* button
                            checkbox
                            image
                            radio
                            reset   */
                            case "textarea":

                                echo " <textarea rows=$rows cols=$cols name=\"";
                                echo $i['Field'];
                                echo "\">";

                                $code=str_replace("</textarea>","&lt;/textarea>",$dat->{$i['Field']});
                                echo stripslashes($code);

                                echo "</textarea>\n";

                                break;

                            case "codearea":

                                $this_codearea=true;
                                $godat=$dat->{$i['Field']};
                                show_codearea("sc_bf_codearea", $rows,$cols,$i['Field'],$godat);

                                break;


                            case "colorpicker":
                                $cp=$i['Field'];

                                echo "<!-- flooble.com Color Picker start -->";
                                include($GLOBALS['site_path']."/js/flooble_color_picker.js");
                                //echo "<script language=\"Javascript\" type=\"text/javascript\" src=\"".$GLOBALS['$RFS_SITE_URL']."/rfs/js/flooble_color_picker.js\"></script>";
                                echo " &nbsp;&nbsp;<a href=\"javascript:pickColor('pick$cp');\" id=\"pick$cp\"
                                style=\"border: 1px solid #000000; font-family:Verdana; font-size:14px;
                                text-decoration: none;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                                <input id=\"pick$cp"."field\" size=\"7\"
                                onChange=\"relateColor('pick$cp', this.value);\" title=\"color\" name=\"";
                                echo $cp;
                                echo "\" value=\"".$dat->{$i['Field']}."\">
                                <script language=\"javascript\">relateColor('pick$cp', getObj('pick$cp"."field').value);</script>
                                <noscript></noscript>
                                <!-- flooble Color Picker end -->
                                ";

                                break;

                            case "hidden":
                                echo "<tr><td height=0px; width=0px;>";
                            case "file":
                            case "text":
                            case "submit":
                            case "password":

                                if($type=="file"){
                                    $fn=$dat->{$i['Field']};
                                    if(!empty($fn)){
                                        $ft=sc_getfiletype($fn);
                                        if( ($ft=="gif") ||
                                            ($ft=="png") ||
                                            ($ft=="jpg") ||
                                            ($ft=="jpeg") ||
                                            ($ft=="bmp") ){
                                            echo "<img width=32 height=32 src=\"$$RFS_SITE_URL/images/$fn\">";
                                        }
                                        echo "currently [$fn]";
                                        echo "<br>";
                                    }
                                }

                                echo " <input type=\"$type\" ";
                                echo "size=$width ";
                                echo "name=\"".$i['Field']."\" ";
									$outvar=$dat->{$i['Field']};
									$outvar=str_replace("\"","&quot;",$outvar);
                                echo "value=\"$outvar\">\n";

                                if($hidden==1)
                                    echo "</td><td>";

                            default:

                                break;
                        }

                        if($hidden==0){
                            echo"</td></tr>\n";
                            $gt++; if($gt>2) $gt=1;
                        }
                    //}
                }
            }
        }
    }

    $hidvar_a=explode(sc_delimiter($hiddenvars),$hiddenvars);

    for($j=0;$j<count($hidvar_a);$j++) {

        $hidvar_b=explode("=",$hidvar_a[$j]);
        d_echo("[$hidvar_b[0]] [$hidvar_b[1]]");
		
		if(stristr($hidvar_b[0],"SHOW_SELECTOR_")) {
			
			// examples:
			// SHOW_SELECTOR_colors#name#text_color#$ocolor
			// SHOW_SELECTOR_exam_question_types#type#type#$qt->type
			
            if($this_codearea==false){
            $field=explode("#",str_replace("SHOW_SELECTOR_","",$hidvar_b[0]));
			  $default=$field[3];
            $name=$field[2];
            $key=$field[1];
            $table=$field[0];
			
				$keys=explode("&",$key);
				if(count($keys)>1) {
					$key=join($RFS_SITE_DELIMITER,$keys);
				}
            
            echo "<tr><td class=sc_project_table_$gt align=right>";
			
				echo ucwords($name);
			
            echo "</td><td class=sc_project_table_$gt>";
	
			
				sc_optionizer("INLINE","SELECTNAME=$name".$RFS_SITE_DELIMITER,
								$table,
								$key,
								0,
								$default,
								0
								);

            echo "</td></tr>";
            $gt++; if($gt>2) $gt=1;
            }
        }
		
		
        if(stristr($hidvar_b[0],"SHOW_CODEAREA_")) {
            if($this_codearea==false){
            $field=explode("#",str_replace("SHOW_CODEAREA_","",$hidvar_b[0]));
            $name=$field[2];
            $cols=$field[1];
            $rows=$field[0];
            //echo "[".$hidvar_b[0]."][$rows][$cols]";
            echo "<tr><td class=sc_project_table_$gt align=right>";
            echo "</td><td class=sc_project_table_$gt>";
            show_codearea( "sc_bf_codearea",$rows,$cols,$name,$hidvar_b[1]);
            echo "</td></tr>";
            $gt++; if($gt>2) $gt=1;
            }
        }

        if(stristr($hidvar_b[0],"SHOW_TEXT2_")){
            $field=explode("SHOW_TEXT_",$hidvar_b[0]);
            $hidvar_b[0]=str_replace("SHOW_TEXT_","",$hidvar_b[0]);
            // echo "--- $field[1] $hidvar_b[1]<br>";
            echo "<tr><td class=sc_project_table_$gt align=right>";
            echo ucwords(str_replace("_"," ",$hidvar_b[0]));
            echo "</td><td class=sc_project_table_$gt>";
            echo " <input ";
            echo "size=$width ";
            echo "name=\"".$field[1]."\" ";
            echo "value=\"".$hidvar_b[1]."\"";
            echo ">\n";
            echo "</td></tr>";
            $gt++; if($gt>2) $gt=1;
        }


		if(stristr($hidvar_b[0],"SHOW_TEXT_")){
				d_echo("SHOW_TEXT_ found... ".$hidvar_b[0]);
            $field=explode("#",$hidvar_b[0]);
            $hidvar_b[0]=str_replace("SHOW_TEXT_","",$hidvar_b[0]);
            $cols=$width;
            $rows=6;
            $taname=$hidvar_b[0];
            $rw=explode("#",$hidvar_b[0]);

            if(count($rw)==3){
                $rows=$rw[0];
                $cols=$rw[1];
                $taname=$rw[2];
            }
            else if(count($rw)==2){
                $rows=$rw[0];
                $taname=$rw[1];
            }
            // echo "--- $field[1] $hidvar_b[1]<br>";
            echo "<tr><td class=sc_project_table_$gt align=right>";
            echo ucwords(str_replace("_"," ",$taname));
            echo "</td><td class=sc_project_table_$gt>";

				echo " <input ";
				echo "size=$cols ";
				echo "name=\"".$taname."\" ";
				echo "value=\"".$hidvar_b[1]."\"";
				echo ">\n";

            //echo "<textarea rows=$rows cols=$cols name=\"$taname\">";
            //$code=str_replace("</textarea>","&lt;/textarea>",$hidvar_b[1]);
            //echo stripslashes($code);
            //echo "</textarea>";
            /*
            echo " <input ";
            echo "size=$width ";
            echo "name=\"".$field[1]."\" ";
            echo "value=\"\"";
            echo ">\n";
            */
            echo "</td></tr>";
            $gt++; if($gt>2) $gt=1;
        }

        if(stristr($hidvar_b[0],"SHOW_TEXTAREA_")){
            $field=explode("#",$hidvar_b[0]);
            $hidvar_b[0]=str_replace("SHOW_TEXTAREA_","",$hidvar_b[0]);
            $cols=$width;
            $rows=6;
            $taname=$hidvar_b[0];
            $rw=explode("#",$hidvar_b[0]);
            //echo "[".$rw[0]."]<br>";
            //echo "[".$rw[1]."]<br>";
            //echo "[".$rw[2]."]<br>";
            //echo "[".count($rw)."]<bn>";
            if(count($rw)==3){
                $rows=$rw[0];
                $cols=$rw[1];
                $taname=$rw[2];
            }
            else if(count($rw)==2){
                $rows=$rw[0];
                $taname=$rw[1];
            }
            // echo "--- $field[1] $hidvar_b[1]<br>";
            echo "<tr><td class=sc_project_table_$gt align=right>";
            echo ucwords(str_replace("_"," ",$taname));
            echo "</td><td class=sc_project_table_$gt>";
            echo "<textarea rows=$rows cols=$cols name=\"$taname\">";
            $code=str_replace("</textarea>","&lt;/textarea>",$hidvar_b[1]);
            echo stripslashes($code);
            echo "</textarea>";
            /*
            echo " <input ";
            echo "size=$width ";
            echo "name=\"".$field[1]."\" ";
            echo "value=\"\"";
            echo ">\n";
            */
            echo "</td></tr>";
            $gt++; if($gt>2) $gt=1;
        }

		if(stristr($hidvar_b[0],"SHOW_PASSWORD_")){
            $field=explode("#",$hidvar_b[0]);
            $hidvar_b[0]=str_replace("SHOW_PASSWORD_","",$hidvar_b[0]);
            $cols=$width;
            $rows=6;
            $taname=$hidvar_b[0];
            $rw=explode("#",$hidvar_b[0]);
            //echo "[".$rw[0]."]<br>";
            //echo "[".$rw[1]."]<br>";
            //echo "[".$rw[2]."]<br>";
            //echo "[".count($rw)."]<bn>";
            if(count($rw)==3){
                $rows=$rw[0];
                $cols=$rw[1];
                $taname=$rw[2];
            }
            else if(count($rw)==2){
                $rows=$rw[0];
                $taname=$rw[1];
            }
            // echo "--- $field[1] $hidvar_b[1]<br>";
            echo "<tr><td class=sc_project_table_$gt align=right>";
            echo ucwords(str_replace("_"," ",$taname));
            echo "</td><td class=sc_project_table_$gt>";

				echo " <input type=password ";
				echo "size=$cols ";
				echo "name=\"".$taname."\" ";
				echo "value=\"".$hidvar_b[1]."\"";
				echo ">\n";

            //echo "<textarea rows=$rows cols=$cols name=\"$taname\">";
            //$code=str_replace("</textarea>","&lt;/textarea>",$hidvar_b[1]);
            //echo stripslashes($code);
            //echo "</textarea>";
            /*
            echo " <input ";
            echo "size=$width ";
            echo "name=\"".$field[1]."\" ";
            echo "value=\"\"";
            echo ">\n";
            */
            echo "</td></tr>";
            $gt++; if($gt>2) $gt=1;
        }

    }

    if(!empty($submit)){
	    echo "<tr><td></td><td>";
        sc_makebuttonstart();
	    echo "<input style='font-size:x-small; min-width:100px;' type=submit name=submit value=\"$submit\">";
        sc_makebuttonend();
	    echo "</td></tr>";
    }
    echo "</form>";
    echo "</table>";
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_form_end($submit){
    echo "<input type=submit name=submit value=\"$submit\">";
    echo "</form>";
}
///////////////////////////////////////////////////////////////////////////////////////////////
function sc_form_start($page,$action){
    echo "<form action=\"$page\" method=\"POST\" enctype=\"application/x-www-form-URLencoded\">";
    echo "<input type=\"hidden\" name=\"action\" value=\"$action\">";
}
///////////////////////////////////////////////////////////////////////////////////////////////
function show_codearea($id,$rows,$cols,$name,$indata){
// echo $GLOBALS['$RFS_SITE_URL'];
 ?>
    <script language="Javascript" type="text/javascript" src="3rdparty/editarea/edit_area/edit_area_full.js"></script>
	<script language="Javascript" type="text/javascript">
		// initialisation

		editAreaLoader.init({
			id: "<? echo $id; ?>"	// id of the textarea to transform
			,start_highlight: true
			,font_size: "8"
			,font_family: "verdana, monospace"
			,allow_resize: "y"
			,allow_toggle: false
			,language: "en"
			,syntax: "php"
			,toolbar: " charmap, |, search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help"
			//new_document, save, load, |,
			,load_callback: "my_load"
			,save_callback: "my_save"
			,plugins: "charmap"
			,charmap_default: "arrows" });

		// callback functions
		function my_save(id, content){
		    id.form.submit();
			//alert("Here is the content of the EditArea '"+ id +"' as received by the save callback function:\n"+content);
		}
		function my_load(id){
			editAreaLoader.setValue(id, "The content is loaded from the load_callback function into EditArea");
		}
		function test_setSelectionRange(id){
			editAreaLoader.setSelectionRange(id, 100, 150);
		}
		function test_getSelectionRange(id){
			var sel =editAreaLoader.getSelectionRange(id);
			alert("start: "+sel["start"]+"\nend: "+sel["end"]);
		}
		function test_setSelectedText(id){
			text= "[REPLACED SELECTION]";
			editAreaLoader.setSelectedText(id, text);
		}
		function test_getSelectedText(id){
			alert(editAreaLoader.getSelectedText(id));
		}
		function editAreaLoaded(id){
			if(id=="example_2"){
				open_file1();
				open_file2();
			}
		}
		function open_file1(){
			var new_file= {id: "to\\ Ã© # â¬ to", text: "$authors= array();\n$news= array();", syntax: 'php', title: 'beautiful title'};
			editAreaLoader.openFile('example_2', new_file);
		}
		function open_file2(){
			var new_file= {id: "Filename", text: "<a href=\"toto\">\n\tbouh\n</a>\n<!-- it's a comment -->", syntax: 'html'};
			editAreaLoader.openFile('example_2', new_file);
		}
		function close_file1(){
			editAreaLoader.closeFile('example_2', "to\\ Ã© # â¬ to");
		}
		function toogle_editable(id){
            editAreaLoader.execCommand(id, 'set_editable', !editAreaLoader.execCommand(id, 'is_editable'));
		}
	</script>
 <?
//    $ca_rows=$rows*16; $ca_cols=$cols*7.20;
    echo "<textarea id=\"$id\" style=\"height: $rows"."px; width: $cols"."px;\" name=\"$name\">";
    if(stristr($indata,"FILE_LOAD_")){
        $file=$GLOBALS['site_path'].str_replace("FILE_LOAD_","",$indata);
        $fp=fopen($file,"r");
        if($fp){
            $indata = fread($fp, filesize($file));
            fclose($fp);
        }
    }
    $code=str_replace("</textarea>","&lt;/textarea>",$indata);
    echo stripslashes($code);
    echo "</textarea>";
    //echo "<BR><a href=\"http://sourceforge.net/projects/editarea/\" target=_blank>EditArea</a> JavaScript Browser Editor";
}

/////////////////////////////////////////////////////////////////////////
function sc_option_countries() {
	echo "
	<option>United States
	<option>Afghanistan
	<option>Albania
	<option>Algeria
	<option>Andorra
	<option>Angola
	<option>Antigua and Barbuda
	<option>Argentina
	<option>Armenia
	<option>Australia
	<option>Austria
	<option>Azerbaijan
	<option>Bahamas
	<option>Bahrain
	<option>Bangladesh
	<option>Barbados
	<option>Belarus
	<option>Belgium
	<option>Belize
	<option>Benin
	<option>Bhutan
	<option>Bolivia
	<option>Bosnia and Herzgovina
	<option>Botswana
	<option>Brazil
	<option>Brunei
	<option>Bulgaria
	<option>Burkina Faso
	<option>Burundi
	<option>Cambodia
	<option>Cameroon
	<option>Canada
	<option>Cape Verde
	<option>Central African Republic
	<option>Chad
	<option>Chile
	<option>China
	<option>Columbia
	<option>Comoros
	<option>Congo (Brazzaville)
	<option>Congo, Democratic Republic
	<option>Costa Rica
	<option>Croatia
	<option>Cuba
	<option>Cyprus
	<option>Czech Republic
	<option>Cote d'lvoire
	<option>Denmark
	<option>Djibouti
	<option>Dominica
	<option>Dominican Republic
	<option>East Timor (Timor Timur)
	<option>Ecuador
	<option>Egypt
	<option>El Salvador
	<option>Equatorial Guinea
	<option>Eritrea
	<option>Ethiopia
	<option>Fiji
	<option>Finland
	<option>France
	<option>Gabon
	<option>Gambia
	<option>Georgia
	<option>Germany
	<option>Ghana
	<option>Greece
	<option>Grenada
	<option>Guatemala
	<option>Guinea
	<option>Guinea-Bissau
	<option>Guyana
	<option>Haiti
	<option>Honduras
	<option>Hungary
	<option>Iceland
	<option>India
	<option>Indonesia
	<option>Iran
	<option>Iraq
	<option>Ireland
	<option>Israel
	<option>Italy
	<option>Jamaica
	<option>Japan
	<option>Jordan
	<option>Kazakhstan
	<option>Kenya
	<option>Kiribati
	<option>Korea, Best
	<option>Korea, South
	<option>Kuwait
	<option>Kyrgyzstan
	<option>Laos
	<option>Latvia
	<option>Lebanon
	<option>Lesotho
	<option>Liberia
	<option>Libya
	<option>Liechtenstein
	<option>Lithuania
	<option>Luxembourg
	<option>Macedonia, Former Yugoslav Republic
	<option>Madagasgar
	<option>Malawi
	<option>Malaysia
	<option>Maldives
	<option>Mali
	<option>Malta
	<option>Marshall Islands
	<option>Mauritania
	<option>Mauritius
	<option>Mexico
	<option>Micronesia, Federated States of
	<option>Moldova
	<option>Monaco
	<option>Mongolia
	<option>Morocco
	<option>Mozambique
	<option>Myanmar
	<option>Nambia
	<option>Nauru
	<option>Nepal
	<option>Netherlands
	<option>New Zealand
	<option>Nicaragua
	<option>Niger
	<option>Nigeria
	<option>Norway
	<option>Oman
	<option>Pakistan
	<option>Palau
	<option>Panama
	<option>Papua New Guinea
	<option>Paraguay
	<option>Peru
	<option>Phillipines
	<option>Poland
	<option>Portugal
	<option>Qatar
	<option>Romania
	<option>Russia
	<option>Rwanda
	<option>Saint Kitts and Nevis
	<option>Saint Lucia
	<option>Saint Vincent and The Grenadines
	<option>Samoa
	<option>San Marino
	<option>Sao Tome and Principe
	<option>Saudia Arabia
	<option>Senegal
	<option>Serbia and Montenegro
	<option>Seychelles
	<option>Sierra Leone
	<option>Singapore
	<option>Slovakia
	<option>Slovenia
	<option>Solomon Islands
	<option>Somalia
	<option>South Africa
	<option>South Sudan
	<option>Spain
	<option>Sri Lanka
	<option>Sudan	
	<option>Suriname
	<option>Swaziland
	<option>Sweden
	<option>Switzerland
	<option>Syria
	<option>Taiwan
	<option>Tajikistan
	<option>Tanzania
	<option>Thailand
	<option>Togo
	<option>Tonga
	<option>Trinidad and Tobago
	<option>Tunisia
	<option>Turkey
	<option>Turkmenistan
	<option>Tuvalu
	<option>Uganda
	<option>Ukraine
	<option>United Arab Emirates
	<option>United Kingdom
	<option>United States
	<option>Uruguay
	<option>Uzbekistan
	<option>Vanuatu
	<option>Vatican City
	<option>Venezuela
	<option>Vietnam
	<option>Western Sahara
	<option>Yemen
	<option>Zambia
	<option>Zimbabwe
	";
}

/////////////////////////////////////////////////////////////////////////////////////////
// This file can not have any trailing spaces
?>
