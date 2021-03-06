<?
include_once("include/lib.all.php");

sc_menus_register("News","$RFS_SITE_URL/modules/news/news.php");

sc_access_method_add("news", "edit");
sc_access_method_add("news", "editothers");
sc_access_method_add("news", "submit");
sc_access_method_add("news", "delete");
sc_access_method_add("news", "deleteothers");

sc_touch_dir("$RFS_SITE_PATH/images/news");

sc_database_add("news","name",		"text",	"NOT NULL");
sc_database_add("news","headline",	"text",	"NOT NULL");
sc_database_add("news","message",	"text",	"NOT NULL");
sc_database_add("news","category1","text",	"NOT NULL");
sc_database_add("news","submitter","int",		"NOT NULL DEFAULT '0'");
sc_database_add("news","time",		"timestamp","NOT NULL");
sc_database_add("news","lastupdate","timestamp","ON UPDATE CURRENT_TIMESTAMP NOT NULL");
sc_database_add("news","image_url","text",	"NOT NULL");
sc_database_add("news","image_link","text",	"NOT NULL");
sc_database_add("news","image_alt","text",	"NOT NULL");
sc_database_add("news","topstory",	"text",	"NOT NULL");
sc_database_add("news","published","text",	"NOT NULL");
sc_database_add("news","views",		"int",		"NOT NULL DEFAULT '0'");
sc_database_add("news","rating",	"text",	"NOT NULL");
sc_database_add("news","sfw",		"text",	"NOT NULL");
sc_database_add("news","page",		"int",		"NOT NULL");
sc_database_add("news","wiki",		"text",	"NOT NULL");

//////////////////////////////////////////////////////////////////////////////////
// MODULE NEWS

function news_buttons() { eval(scg());
		if(sc_access_check("news","submit")) {
				
			sc_button("$RFS_SITE_URL/modules/news/news.php?showform=yes","Submit News");
		}
	
}

function adm_action_lib_news_news_submit() { eval(scg());
    sc_gotopage("$RFS_SITE_URL/modules/news/news.php?showform=yes");
}
function adm_action_lib_news_news_edit() { eval(scg());
    sc_gotopage("$RFS_SITE_URL/modules/news/news.php?action=edityournews");
}
function sc_module_news_list($x) { eval(scg());
    sc_div("NEWS MODULE SECTION");
    echo "<h2>Last $x News Articles</h2>";
    $newslist=sc_getnewslist($newssearch);
    echo "<table border=0 cellspacing=0>";
    $ct=count($newslist); if($ct>$x) $ct=$x;
    for($cci=0;$cci<$ct;$cci++){
        echo "<tr><td class=contenttd width=2% >";
        $news=sc_getnewsdata($newslist[$cci]);
        if(!file_exists("$RFS_SITE_PATH/$news->image_url"))
            $news->image_url="$RFS_SITE_URL/images/icons/404.png";
        if(empty($news->image_url))
            $news->image_url="$RFS_SITE_URL/images/icons/news.png";
        if(!stristr($news->image_url,$RFS_SITE_URL))
            $news->image_url=$RFS_SITE_URL."/".ltrim($news->image_url,"/");

        $altern=stripslashes($news->image_alt);
        $picf="$RFS_SITE_PATH/$news->image_url";
        $picf=str_replace($RFS_SITE_URL,"",$picf);
        echo "<a href=\"$RFS_SITE_URL/modules/news/news.php?action=view&nid=$news->id\">".sc_picthumb("$picf",30,0,1	)."</a>\n";
        echo "</td><td valign=top  class=contenttd 90%>";
        echo "<a href=\"$RFS_SITE_URL/modules/news/news.php?action=view&nid=$news->id\" class=\"a_cat\">".sc_trunc("$news->headline",50)."</a>";
        $ntext=str_replace("<p>"," ",$ntext);
        $ntext=str_replace("</p>"," ",$ntext);
        $ntext=str_replace("<","&lt;",$ntext);
        echo "<font class=sc_black>$ntext</font>";
        echo "</td></tr>";
    }
    echo "</table>";
    echo "<p align=right>(<a href=$RFS_SITE_URL/modules/news/news.php class=\"a_cat\" align=right>More...</a>)</p>";
}
function sc_module_news_list_popular($x) { eval(scg());
    sc_div("NEWS MODULE SECTION");
    echo "<h2>Popular News Articles</h2>";
    //search method dictate sort order?
    $result=sc_query("select * from news where topstory!='yes' and published='yes' order by views desc limit $x");
    echo "<table border=0>";
    $ct=mysql_num_rows($result);
    for($i=0;$i<$ct;$i++)    {
        $news=mysql_fetch_object($result);
        echo "<tr><td>";
        echo "<table border=0 cellpadding=1 cellspacing=0><tr><td>";
        if(!file_exists("$RFS_SITE_PATH/$news->image_url"))
            $news->image_url="$RFS_SITE_URL/images/icons/404.png";
        if(empty($news->image_url))
            $news->image_url="$RFS_SITE_URL/images/icons/news.png";
        if(!stristr($news->image_url,$RFS_SITE_URL))
            $news->image_url=$RFS_SITE_URL."/".ltrim($news->image_url,"/");

        $altern=stripslashes($news->image_alt);
        echo "<a href=\"$RFS_SITE_URL/modules/news/news.php?action=view&nid=$news->id\"><img src=\"$news->image_url\" border=\"0\" title=\"$altern\" alt=\"$altern\" width=30 height=30></a>\n";
        echo "</td></tr></table>";
        echo "</td><td valign=top>";
        echo "<a href=\"$RFS_SITE_URL/modules/news/news.php?action=view&nid=$news->id\" class=\"a_cat\">".sc_trunc("$news->headline",50)."</a><br>";
        $ntext=str_replace("<br>"," ",stripslashes(sc_trunc("$news->message",70)));
        $ntext=str_replace("<img",$news->headline,$ntext);
        $ntext=str_replace("<iframe",$news->headline,$ntext);
        $ntext=str_replace("</body>","<nobody>",$ntext);
        $ntext=str_replace("<p>"," ",$ntext);
        $ntext=str_replace("</p>"," ",$ntext);
        echo "<font class=sc_black>$ntext</font>";
        echo "</td></tr>";
    }
    echo "</table>";
}
function sc_module_news_top_story() {
    sc_show_top_news();
}
function sc_module_news_blog_style($x) { eval(scg());
	news_buttons();
	sc_show_top_news();
	$newslist=sc_getnewslist(""); $ct=count($newslist); if($ct>$x) $ct=$x;
	echo "Older news...<br>";
	for($cci=0;$cci<$ct;$cci++) {
		$news=sc_getnewsdata($newslist[$cci]);
		sc_show_news($news->id);
    }
}

function sc_getnewstopstory(){
    $result=sc_query("select * from news where topstory='yes' and published='yes'");
    $news=mysql_fetch_object($result);
    return $news;
}
function sc_getnewsdata($news){
    $query="select * from news where id = '$news'";
    $result=sc_query($query);
    if(mysql_num_rows($result) >0 ) $news = mysql_fetch_object($result);
    return $news;
}
function sc_getnewslist($newssearch) {
    $newsbeg=$GLOBALS['top'];
    $newsend=$GLOBALS['bot'];
    $query = "select * from news where topstory!='yes' and published='yes' ";
    if(!empty($newssearch)) { $query.=" ".$newssearch; unset($newssearch); }
    if(empty($newsbeg)) $newsbeg=0; if(empty($newsend)) $newsend=10;
    $query .= " order by time desc";
    $result = sc_query($query);
    $numnews=mysql_num_rows($result);
    $i=0;
    while($i<$numnews) {
        $der = mysql_fetch_array($result);
        $newslist[$i] = $der['id'];
        $i=$i+1;
    }
    return $newslist;
}
function sc_get_news_headline($id){
    $result=sc_query("select * from news where id='$id'");
    $news=@mysql_fetch_object($result);
    return $news->headline;
}
function sc_get_top_news_id(){
    $result=sc_query("select * from news where topstory='yes' and published='yes'");
    $news=@mysql_fetch_object($result);
    return $news->id;
}
function sc_show_top_news() {
    $news=mfo1("select * from news where topstory='yes' and published='yes'");    
    sc_show_news($news->id);
}
function sc_show_news($nid) { eval(scg());
	
	$result=sc_query("select * from news where id='$nid'");
    $news=mysql_fetch_object($result);
    $userdata=mfo1("select * from users where id='$news->submitter'");
	
	echo "<div class=\"news_box\">";
		echo "<div class=\"news_headline_bar\">";
			echo "<div class=\"news_headline\">$news->headline</div>";
			echo "<div class=\"news_time\">Posted on ".sc_time($news->time)."</div>";
		echo "</div>";
		echo "<div class=\"news_article\">";
		
		echo "<div class=\"news_image\">";
	   
    $out_link=urlencode("$RFS_SITE_URL/modules/news/news.php?action=view&nid=$news->id");

    if(!empty($news->image_url)) {		
		$news->image_url=str_replace("$RFS_SITE_PATH/","",$news->image_url);
		$news->image_url=str_replace("$RFS_SITE_URL/","",$news->image_url);		
		$altern=stripslashes($news->image_alt);		
		if(empty($news->image_link))
			$news->image_link="$RFS_SITE_URL/modules/news/news.php?action=view&nid=$news->id";
		echo "<a href=\"$news->image_link\" target=\"_blank\" class=\"news_a\" >";
		if(!file_exists("$RFS_SITE_PATH/".ltrim($news->image_url,"/"))) {
			$oldimage=$news->image_url;
			$news->image_url="$RFS_SITE_URL/images/icons/404.png";
			echo "<br>($oldimage)";
		}		
		if(!stristr($news->image_url,$RFS_SITE_URL))
			$news->image_url=$RFS_SITE_URL."/".ltrim($news->image_url,"/");		
		echo "<img src=\"".sc_picthumb_raw($news->image_url,100,0,1)."\" border=\"0\" title = '$altern' alt='$altern' class='rfs_thumb'>";
	}	
	if(!empty($news->image_url)) {
		echo  "</a>";
	}
		echo "</div>";
	
		

    if(!empty($news->wiki)) {
            $wikipage=mfo1("select * from wiki where `name`='$news->wiki'");
            echo smiles(wikitext($wikipage->text));
    }	else {
        $news->message=str_replace("<a h","<a class=news_a h",$news->message);
        echo smiles(stripslashes(wikitext($news->message)));
    }
    $ourl="$RFS_SITE_URL/modules/news/news.php?action=view&nid=$nid";
	 sc_socials_content($ourl,$news->headline);		
	
	$page="$RFS_SITE_URL/modules/news/news.php?action=view&nid=$nid";	
	
	if($RFS_SITE_FACEBOOK_NEWS_COMMENTS) 
		sc_facebook_comments($page);
	
		echo "</div>";
    
		echo "<div class=\"news_edit_bar\">";
    $data=$GLOBALS['data'];
    if(($data->name==$userdata->name)||($data->access==255)) {
        
		if(!empty($news->wiki)) {
			echo "[<a href=\"$RFS_SITE_URL/modules/wiki/rfswiki.php?action=edit&name=$news->wiki\" class=news_a>edit (wiki page)</a>] \n";
			echo "[<a href=\"$RFS_SITE_URL/modules/news/news.php?action=ed&nid=$nid\" class=news_a>edit (news)</a>] \n";			
		} else {
			echo "[<a href=\"$RFS_SITE_URL/modules/news/news.php?action=ed&nid=$nid\" class=news_a>edit</a>] \n";
		}
        echo "[<a href=\"$RFS_SITE_URL/modules/news/news.php?action=de&nid=$nid\" class=news_a>remove</a>] \n";
    }   
		echo "</div>";
	echo "</div>";
}
function put_news_image($fname) { eval(scg());
    
	$file=$_FILES[$fname]['name'];
    $f_ext=sc_getfiletype($file);
    $uploadFile=$RFS_SITE_PATH."/images/news/$file";
    if(($f_ext=="gif")||($f_ext=="jpg")||($f_ext=="swf")) {
        $oldname=$file;
        if(move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)) {
            system("chmod 755 $uploadFile");
            $httppath="$RFS_SITE_PATH/images/news/".$_FILES['userfile']['name'];
            echo "<p>File stored as [<a href=\"$httppath\" target=\"_blank\">$httppath</a>]</p>\n";
        }
    }
    return $httppath;
}
function updatenews($nid){ 	eval(scg());
	$p=addslashes($GLOBALS['headline']); sc_query("UPDATE news SET headline ='$p' where id = '$nid'");


	$name=$_REQUEST['name'];
	if(stristr($name,"Choose a wiki page to use for this news article")) $name="";
	if(stristr($name,"--- NONE ---")) $name="";
	sc_query("update news set `wiki` = '$name' where `id`='$nid'");

	$p=addslashes($GLOBALS['posttext']);
	sc_query("UPDATE news SET message ='$p' where id = '$nid'");
	$p=addslashes($GLOBALS['category1']);
	sc_query("UPDATE `news` SET `category1` ='$p' where id = '$nid'");
	$p=addslashes($GLOBALS['category2']);
	if($p!="none") sc_query("UPDATE `news` SET `category2` ='$p' where id = '$nid'");
	$p=addslashes($GLOBALS['category3']);
	if($p!="none") sc_query("UPDATE `news` SET `category3` ='$p' where id = '$nid'");
	$p=addslashes($GLOBALS['category4']);
	if($p!="none") sc_query("UPDATE `news` SET `category4` ='$p' where id = '$nid'");

	$p=$GLOBALS['topstory'];
	if($p=="yes") {
		sc_query("update news set topstory='no'");
		sc_query("update news set topstory='yes' where id='$nid'");
	}

	$p=$GLOBALS['published'];
	if($p=="yes") sc_query("update news set published='yes' where id='$nid'");
	else          sc_query("update news set published='no' where id='$nid'");

	echo "<p>News article [$nid] has been updated...</p>\n";
	$loggit="*****> ".$GLOBALS['data']->name." updated news article $nid...";
}
function deletenews($nid) { eval(scg());
    echo "<table border=\"0\" align=center><tr><td class=\"sc_warning\"><center>".smiles(":X")."\n";
    echo "<br>WARNING:<br>The news article will be completely removed are you sure?</center>\n";
    echo "</td></tr></table>\n";
    echo "<table align=center><tr><td><form enctype=application/x-www-form-URLencoded action=\"$RFS_SITE_URL/modules/news/news.php\">\n";
    echo "<input type=hidden name=action value=dego><input type=hidden name=nid value=$nid>\n";
    echo "<input type=\"submit\" name=\"submit\" value=\"Yes\"></form></td>\n";
    echo "<td><form enctype=application/x-www-form-URLencoded action=\"$RFS_SITE_URL/modules/news/news.php\"><input type=\"submit\" name=\"no\" value=\"No\"></form></td></tr></table>\n";
}
function deletenewsgo($nid){ 	eval(scg());
    sc_query("DELETE FROM news where id = '$nid'");
    echo "<p>News article $nid has been deleted...</p>\n";
    $loggit="*****> ".$GLOBALS['data']->name." deleted news article $nid...";
    sc_log($loggit);

}
function editnews($nid) { eval(scg());

    $news=mysql_fetch_object(sc_query("select * from news where id='$nid'"));
    
	echo "<a href=$RFS_SITE_URL/modules/news/news.php?action=view&nid=$nid>Preview</a>";
	
	$news->image_url=str_replace("$RFS_SITE_PATH/","",$news->image_url);
	$news->image_url=str_replace("$RFS_SITE_URL/","",$news->image_url);
	
    if(!file_exists("$RFS_SITE_PATH/".ltrim($news->image_url,"/"))) {
		 $oldimage=$news->image_url;
        $news->image_url="$RFS_SITE_URL/images/icons/404.png";	
	}
    if(empty($news->image_url)) {
        $news->image_url="$RFS_SITE_URL/images/icons/news.png";		
	}
    if(!stristr($news->image_url,$RFS_SITE_URL))
        $news->image_url=$RFS_SITE_URL."/".ltrim($news->image_url,"/");
    
    echo "<table border=0 width=100%><tr><td>";
    
	//echo "<img src=\"$news->image_url\" width=100 height=100><br>";
	
	echo "<img src=\"";
	echo sc_picthumb_raw($news->image_url,100,0,1); 
	echo "\" border=\"0\" title = '$altern' alt='$altern' align=left class='rfs_thumb'>";
	
	
	if(!empty($oldimage))
		echo "($oldimage)";
		
		
    echo "</td><td>";
    echo "<table border=0><tr>";
    echo "<td align=left>";
    echo "<p>150 x 150</p>";
    echo "Enter a url";
    echo "<table border=0>\n";
    echo "<form enctype=application/x-www-form-URLencoded ";
    echo " enctype=\"multipart/form-data\" action=\"$RFS_SITE_URL/modules/news/news.php\" method=\"post\">\n";
    echo "<input type=hidden name=action value=imageurl>\n";
    echo "<input type=hidden name=nid value=$nid>";
    echo "<tr><td><input name=\"userfile\"> </td><td>";
    echo "<input type=\"submit\" name=\"submit\" value=\"URL\"></td></tr>\n";
    echo "</form>\n";
    echo "</table>\n";
    echo "Or select a file to upload";
    echo "<table border=0>\n";
    echo "<form enctype=\"multipart/form-data\" action=\"$RFS_SITE_URL/modules/news/news.php\" method=\"post\">\n";
    echo "<input type=hidden name=give_file value=news>\n";
    echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"99900000\">";
    echo "<input type=hidden name=local value=\"images/news\">";
    echo "<input type=hidden name=hidden value=yes>\n";
    echo "<input type=hidden name=nid value=$nid>";
    echo "<tr><td><input name=\"userfile\" type=\"file\"> </td><td><input type=\"submit\" name=\"submit\" value=\"Upload!\"></td></tr>\n";
    echo "</form>\n";
    echo "</table>\n";
    echo "<table border=0>\n";
    echo "<form enctype=\"multipart/form-data\" action=\"$RFS_SITE_URL/modules/news/news.php\" method=\"post\">\n";
    echo "<input type=hidden name=action value=clearnewsimage>\n";
    echo "<input type=hidden name=nid value=$nid>";
    echo "<tr><td>Or clear current image: <input type=\"submit\" name=\"submit\" value=\"Clear\"></td></tr>\n";
    echo "</form>\n";
    echo "</table>\n";
    echo "</td>";
    echo "</tr></table>";
    echo "</td>";
    echo "</tr></table>";

echo "<form enctype=application/x-www-form-URLencoded method=post action=\"$RFS_SITE_URL/modules/news/news.php\">\n";
    echo "<table border=0>";
	
	
	echo "<tr><td>";
	
    echo "Select a wiki page to use instead of text </td><td>";
    
	$wikistatus=$news->wiki;
	if(empty($wikistatus))
		$wikistatus="Choose a wiki page to use for this news article";
    
	sc_optionizer("INLINE",
					"nid=$nid",
					"wiki",
					"name",
					0,
					$wikistatus,
					1);
                    
		echo "</td></tr></table>";
					
	
    echo "<table border=0 width=100%>";
    echo "<input type=\"hidden\" name=\"action\" value=\"edgo\">\n";
    echo "<input type=\"hidden\" name=\"nid\" value=\"$nid\">\n";
	
    echo "<tr><td>Headline</td><td><input name=headline value=\"$news->headline\" size=100></td></tr>\n";
	
	
	if(empty($news->wiki))	{
		$otxt=$news->message;
		$otxt=str_replace("<","&lt;",$otxt);
		$otxt=stripslashes($otxt);
		echo "<tr><td>Message</td><td>
			<textarea 
				cols=\"70\" 
				rows=\"30\" 
				style=\"width: 80%;\"
				name=\"posttext\" >$otxt</textarea>
			</td></tr>\n";		
	}
	else echo "<tr><td>WIKI PAGE:</td><td>$news->wiki</td></tr>";
    
	echo "<tr><td>Top Story:   </td><td>
	<select name=topstory>";
	echo "<option>$news->topstory";
	echo "
	<option>no<option>yes
	</select></td></tr>\n";
	
    echo "<tr><td>Publish:   </td><td><select name=published>";
    echo "<option>$news->published";
    echo "<option>no<option>yes</select></td></tr>\n";
    echo "<tr><td>Main Category:</td><td><select name=category1>";
    if(!empty($news->category1)) echo "<option>$news->category1";
    $res=sc_query("select * from `categories` order by `name` asc");
    $ncats=mysql_num_rows($res);
    for($i=0;$i<$ncats;$i++) {
        $cat=mysql_fetch_object($res);
        echo "<option>$cat->name";
    }
    echo "</select></td></tr>\n";
    echo "<tr><td>Sub Category 1:</td><td><select name=category2>";
    if(!empty($news->category2)) echo "<option>$news->category2";
    echo "<option>none";
    $res=sc_query("select * from `categories` order by `name` asc");
    $ncats=mysql_num_rows($res);
    for($i=0;$i<$ncats;$i++) {
        $cat=mysql_fetch_object($res);
        echo "<option>$cat->name";
    }
    echo "</select></td></tr>\n";
    echo "<tr><td>Sub Category 2:</td><td><select name=category3>";
    if(!empty($news->category3)) echo "<option>$news->category3";
    echo "<option>none";
    $res=sc_query("select * from `categories` order by `name` asc");
    $ncats=mysql_num_rows($res);
    for($i=0;$i<$ncats;$i++) {
        $cat=mysql_fetch_object($res);
        echo "<option>$cat->name";
    }
    echo "</select></td></tr>\n";
    echo "<tr><td>Sub Category:</td><td><select name=category4>";
    if(!empty($news->category4)) echo "<option>$news->category4";
    echo "<option>none";
    $res=sc_query("select * from `categories` order by `name` asc");
    $ncats=mysql_num_rows($res);
    for($i=0;$i<$ncats;$i++) {
        $cat=mysql_fetch_object($res);
        echo "<option>$cat->name";
    }
    echo "</select></td></tr>\n";
    echo "<tr><td>&nbsp; </td><td><input type=\"submit\" value=\"Update News\" class=b4button></td></tr>\n";
    echo "</form></table>\n";
	
	// echo " </td></tr></table>\n";
	
	
}


function shownews() { eval(scg());
	$month_name=$GLOBALS['month_name'];
	$day_name=$GLOBALS['day_name'];
	$data=$GLOBALS['data'];
	if($GLOBALS['action']=="catsrch") {
        $derr=$cat_desc[$GLOBALS['crit']];
        echo "<p>Category [$derr] search...</p>";
        $t=$GLOBALS['crit']; $kt=sprintf("%03d|",$t);
        $newssearch="and categories like '%$kt%'";
    }
    if($GLOBALS['action']=="search") {
        $t=$GLOBALS['crit'];
        $newssearch="and message like '%$t%' or headline like '%$t%'";
    }
    if(empty($GLOBALS['top'])) $GLOBALS['top']=0;
    if(empty($GLOBALS['bot'])) $GLOBALS['bot']=1500;
    $newslist=sc_getnewslist($newssearch);
	
    // search method dictate sort order?
	
	echo "<table border=0 cellspacing=0 cellpadding=4 width=100%>";
	
	if($data->access==255) {
		echo "<tr>";
		echo "<td class=contenttd width=2%>Views</td>";
		echo "<td class=contenttd width=2%> &nbsp;  </td>";
		echo "<td class=contenttd> &nbsp; </td>";

		
		echo "</tr>";
	}
    
	for($i=0;$i<count($newslist);$i++) {
        $news=sc_getnewsdata($newslist[$i]);
		echo "<tr>";
		
	
		//////////////////
		echo "<td class=contenttd>";		
			if($data->access==255) echo "$news->views";
		echo "</td>";
		//////////////////

		$altern=stripslashes($news->image_alt);		
		if(empty($news->image_url))
			$news->image_url="images/icons/news.png";			
		if(!file_exists("$RFS_SITE_PATH/".ltrim($news->image_url,"/"))) {
			$oldimage=$news->image_url;
			$news->image_url="$RFS_SITE_URL/images/icons/404.png";
		}
		if(!stristr($news->image_url,$RFS_SITE_URL))
			$news->image_url=$RFS_SITE_URL."/".ltrim($news->image_url,"/");
		
		/////////////////
		echo "<td class=contenttd>";
		echo "<a href=\"$RFS_SITE_URL/modules/news/news.php?action=view&nid=$news->id\">";
		echo "<img src=\"$news->image_url\" border=\"0\" title=\"$altern\" alt=\"$altern\" width=30 height=30>";
		echo "</a>\n";
		echo "</td>";
		/////////////////

		/////////////////
		echo "<td class=contenttd valign=top>";		
		echo "<a href=\"$RFS_SITE_URL/modules/news/news.php?action=view&nid=$news->id\" class=\"a_cat\">$news->headline</a><br>";
       $ntext=str_replace("<br>"," ",stripslashes(sc_trunc("$news->message",80)));
       $ntext=str_replace("<p>"," ",$ntext);
       $ntext=str_replace("</p>"," ",$ntext);
		$ntext=str_replace("<","&lt;",$ntext);
       echo $ntext;
       echo "</td>";
	   
	   
	   /////////////////
	   echo "</tr>";
    }
    
    echo "</table>";
}


?>
