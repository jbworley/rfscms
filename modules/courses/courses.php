<?
$title="Course";
chdir("../../");
include("header.php");

function courses_action_() { eval(scg());
	sc_module_course_admin();
	sc_module_course_list();
}

function courses_action_edit_list() { eval(scg());
	if(sc_access_check("course","edit")) {	
		echo "<h2>EDIT COURSES</h2>";
		sc_button("$RFS_SITE_URL/modules/courses/courses.php?action=edit_component_types","Edit Component Types");		
		$r=sc_query("select * from courses");
		for($i=0;$i<mysql_num_rows($r);$i++){
			$course=mysql_fetch_object($r);
			echo "$course->name (id:$course->id) $course->description $course->image<br>";
		}
	}
}

function courses_action_add_component_type() { eval(scg());
	if(sc_access_check("course","edit")) {
		sc_query("insert into `course_component_type` (`name`) VALUES ('$name');");
		courses_action_edit_component_types();
	}
}

function courses_action_edit_component_types_image() { eval(scg());
	$_SESSION['select_image_path']="";
	sc_selectimage( "images","modules/courses/courses.php","edit_component_types", "course_component_type", $id, "image");
	include("footer.php");
}

function courses_action_edit_component_types() { eval(scg());
	if(sc_access_check("course","edit")) {
		$course=mfo1("select * from courses where id='$id'");	
		echo "<h2>EDIT COURSE COMPONENT TYPES</h2>";
		
		echo "<hr>";	
		// sc_ajax("Category,80","course_components","id","$course->id","category",70,				"select,table,course_component_types,name","course","edit","");
		sc_bf( sc_phpself(),
			   "action=add_component_type".$RFS_SITE_DELIMITER.	       
			   "SHOW_CLEARFOCUSTEXT_#name=name",
			   "","","","","","",50,"Add Component Type" );

		$r=sc_query("select * from course_component_type");
		for($i=0;$i<mysql_num_rows($r);$i++) {
			$cct=mysql_fetch_object($r);
			echo "<div style='float:left;' >";
			if(empty($cct->image)) $cct->image="images/icons/exclamation.png";
			echo "<a href=\"$RFS_SITE_URL/modules/courses/courses.php?action=edit_component_types_image&id=$cct->id\" ><img src=\"$RFS_SITE_URL/$cct->image\" border=\"0\" width=\"32\" title=\"Change Image\" alt=\"Change Image\" text=\"Change Image\"></a>";
			echo "</div>";
			echo "<div style='float: left;'>";
			sc_ajax("Name,80","course_component_type","id","$cct->id","name",70,"","course","edit","");			
			echo "</div>";
			echo "<hr style='clear: both;'>";
		}
	}	
}

function courses_action_edit_components() { eval(scg());
	if(sc_access_check("course","edit")) {
	$course=mfo1("select * from courses where id='$id'");
	echo "<h2>EDIT COURSE: $course->id $course->name </h2>";	
	sc_button("$RFS_SITE_URL/modules/courses/courses.php?action=edit&id=$course->id","Edit Course");
	sc_button("$RFS_SITE_URL/modules/courses/courses.php?action=edit_component_types&id=$course->id","Edit Component Types");
	echo "<hr>";	
		sc_ajax("Type,10","course_component","id","$course->id","type",420, 
					"select,table,course_component_type,name","course","edit","");
	}
	include("footer.php");
}

function courses_action_add_course() { eval(scg());
	if(sc_access_check("course","edit")) {
		echo "<h2>Add new course</h2>";
		echo $name;
		sc_query("insert into courses (`name`) VALUES ('$name') ");
		$id=mysql_insert_id();
		echo "$id";
		courses_action_edit_components();
		
	}

}


function sc_ajax_callback_courses_action_ccl() { eval(scg());
	echo sc_course_components_list($id);

}

function courses_action_edit() { eval(scg());
	if(sc_access_check("course","edit")) {
		if(empty($id)) {			
			echo "<h2>Add course</h2>";			
			sc_bf( sc_phpself(),
			   "action=add_course".$RFS_SITE_DELIMITER.	       
			   "SHOW_CLEARFOCUSTEXT_#name=name",
			   "","","","","","",50,"Add Course" );
			sc_module_course_list(1);
			exit;
		}
		
		
		
		$course=mfo1("select * from courses where id='$id'");
		echo "<h2>EDIT COURSE: $course->id $course->name </h2>";
		// if(sc_access_check("course","edit"))			sc_button("$RFS_SITE_URL/modules/courses/courses.php?action=edit_components&id=$course->id","Edit Components");
		echo "<hr>";
		
		sc_ajax("Name,80","courses","id","$course->id","name",60,"","course","edit","");	
		sc_ajax("Description,80","courses","id","$course->id","description","10,60","textarea","course","edit","");
		
		echo "<div id='coursecomponents'> </div>";
		echo '<script>
			
			function compup(x) {
				
				var url="'.$RFS_SITE_URL.'/modules/courses/courses.php";				
				var params = "action="+"sc_ajax_callback_courses_action_ccl"+
				"&id="+x;				
				http.open("POST", url, true);
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.setRequestHeader("Content-length", params.length);
				http.setRequestHeader("Connection", "close");
				http.onreadystatechange = function() {
						if(http.readyState == 4 && http.status == 200) {					
							document.getElementById("coursecomponents").innerHTML=http.responseText;
				}
			}
			http.send(params);
			compup('.$id.');
		</script> ';
		
		sc_ajax("Add component,10","course_component","parent","$course->id","type",420, 
					"select,table,course_component_type,name","course","edit","sc_ajax_callback_component_add,compup($id)");
				
		
		
		
	}
	include("footer.php");
}

function courses_action_run() { eval(scg());
	$course=mfo1("select * from courses where id='$id'");
	echo "<h2>RUN COURSE: $course->id $course->name </h2>";
	
}


?>