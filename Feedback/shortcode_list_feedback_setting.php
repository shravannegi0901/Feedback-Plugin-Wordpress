<?php
	$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
	include($path.'wp-load.php');
	global $wpdb;
	global $plugin_page;
	$functionName = filter_input(INPUT_GET, 'functionName');
	if ($functionName == "delete") {
	delete();
	}if($functionName == "paginate_data"){
	paginate_data();
	}
	function delete()
	{
	if(!empty($_GET['id']))
	{
	global $wpdb, $table_prefix;
	$table_name = "".$wpdb->prefix."plugins_new_feedback_setting";
	$ids = $_GET['id'];
	$mylink = $wpdb->get_results("SELECT * FROM $table_name WHERE id = ".$ids."");
	if(!empty($mylink))
	{
	$wpdb->delete($table_name, array( 'id' => $ids), array( '%d' ) ); ?>
	<script>
	alert('Data deleted Successfully.');
	</script>
	<?php
	echo "success";exit;
	} else
	{
	echo "error";exit;
	}
	}
	}
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){
	$wp_asin_data = array(
	'post_id' => $_POST['id'],
	'type' =>'post',
	'created' => date("Y-m-d h:i:s"),
	);
	$wpdb->insert(''.$wpdb->prefix.'plugins_new_feedback_setting', $wp_asin_data);
	}
	if($plugin_page == 'shortcode_list_feedback_setting') {	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>				
	<script type="text/javascript" ></script>
	<script>
	$(document).ready(function () {
	$('.delete_data').click(function () {
	if (!confirm('Are you sure you want to delete?')) {
	return false;
	}
	var valu = $(this).attr('rel');
	var web_path = "<?php echo plugins_url(); ?>/Feedback/shortcode_list_feedback_setting.php";
	//alert(web_path);
	$.ajax({
	type : "GET",
	url: web_path,
	data: {id : valu,functionName :"delete"},										
	success: function(response) {											
	window.location.href='<?php echo bloginfo('url');?>/wp-admin/admin.php?page=shortcode_list_feedback_setting';											
	}
	}); 	
	});
	$('#load_more').click(function () {
	var valu1 = $(this).attr('rel');
	var web_path1 = "<?php echo plugins_url(); ?>/Feedback/shortcode_list_feedback_setting.php";
	$.ajax({
	type : "GET",
	url: web_path1,
	data: {pages : valu1,functionName :"paginate_data"},
	success: function(response) {alert(response);
	$('.data_mine').replaceWith(response);
	}
	}); 
	});
	});
	function copyToClipboard1(elementId) {
	var aux = document.createElement("input");
	aux.setAttribute("value", document.getElementById(elementId).innerHTML);
	document.body.appendChild(aux);
	aux.select();
	document.execCommand("copy");
	document.body.removeChild(aux);
	alert('Shortcode copy successfully');
	} 
	</script>
	<div class="wrap">
	<h2>Add Post/Page ID to Hide the Feedback Form<?php $icon = plugins_url().'/Video_Plugin/images/add-video-icon_old.jpg'; ?>
	</h2>
	<h2></h2>
	<form action="<?php echo site_url();?>/wp-admin/admin.php?page=shortcode_list_feedback_setting" method="POST">
	<label><strong>Post/Page ID:</strong></label><br>
	<input type="text" name="id"><br>
	<input type="submit" name="Add" value="Add">
	</form>
	<table cellpadding="1" cellspacing="2" style="" id="" class="wp-list-table widefat fixed striped comments">
	<tr>
	<th width="90" >
	<b>Sr.No</b>
	</th>
	<th width="280" >
	<b>Post/Page ID</b>
	</th>
	<th width="250" >
	<b> Date</b>
	</th>
	<th width="200" >
	<b> Action</b>
	</th> 
	</tr>
	<?php 
	if($plugin_page == 'shortcode_list_feedback_setting') {
	global $wpdb;
	$table_name = ''.$wpdb->prefix.'plugins_new_feedback_setting';
	if(!empty($_GET['pages'])){
	$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
	$results_per_page=8; 
	$start_from = ($_GET['pages']-1) * $results_per_page;
	$result = $wpdb->get_results("SELECT * FROM $table_name ORDER BY ID DESC LIMIT $start_from,$results_per_page");
	}else{
	$pages = '1';
	$limited = 8;
	$result = $wpdb->get_results("SELECT * FROM $table_name LIMIT ".$limited."");
	}
	//$result = $wpdb->get_results("SELECT * FROM $table_name LIMIT ".$limited."");
	$i=1;
	if(!empty($result)){
	foreach($result as $r)
	{ ?>
	<tr>
	<td>
	<p><?php echo $i;?></p>
	</td>
	<td>
	<p><?php echo $r->post_id;?></p>
	</td>			
	<td>
	<p><?php $yrdata= strtotime($r->created);
	echo date('d-M, Y', $yrdata);?></p>
	</td>
	<td>
	<?php
	if($r->type == 'post'){
	$link = get_permalink($r->post_id);
	}else{
	$link = get_page_link($r->post_id);										
	} ?>
	<p><a href="<?php echo $link;?>" target="_blank" style="color:#538ded">View Post</a></p>
	<p><a class='delete_data' href="#" style="color:#538ded" rel="<?php echo $r->id;?>">Delete</a></p>
	</td>
	</tr>
	<?php $i++;} }else{ ?>
	<tr>
	<td colspan="6" align="center">No Records Found.</td></tr>
	<?php }} ?>
	</table><?php
	global $post, $wpdb;
	$table_name = $wpdb->prefix . 'plugins_new_feedback_setting';
	$total_rows = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
	$no_of_records_per_page = 8;
	$total_pages = ceil($total_rows / $no_of_records_per_page);
	//$limit = 8;
	//$pages = ceil($rowcount / $limit);
	if (isset($_GET['pages'])) {
	$pageno = $_GET['pages'];
	} else {
	$pageno = 1;
	}
	$offset = ($pageno-1) * $no_of_records_per_page;
	if(!empty($total_pages)){ ?>
	Page <?php echo $pageno; ?> of <?php echo $total_pages;?> pages
	<ul class="pagination" >
	<?php if(!empty($pageno) && $pageno != 1 ){ ?>
	<li><a href="<?php echo get_site_url();?>/wp-admin/admin.php?page=shortcode_list_feedback_setting&pages=1">First</a></li>
	<?php }  if($pageno > 2) { ?>
	<li class="">
	<a href="<?php  echo get_site_url().'/wp-admin/admin.php?page=shortcode_list_feedback_setting&pages='.($pageno - 1);  ?>">Prev</a>
	</li>
	<?php } if($pageno > $total_pages){ ?>	
	<li class="">
	<a href="<?php  echo get_site_url().'/wp-admin/admin.php?page=shortcode_list_feedback_setting&pages='.($pageno + 1); ?>">Next</a>
	</li>
	<?php } if($pageno != $total_pages){ ?>
	<li><a href="<?php echo get_site_url();?>/wp-admin/admin.php?page=shortcode_list_feedback_setting&pages=<?php echo $total_pages;?>">Last</a></li>
	<?php } ?>
	</ul>
	<?php }} ?>
	</div>
	</div>
	<style>
	.pagination a {
	background-color: #3333;
	color: black;
	float: left;
	padding: 8px 16px;
	text-decoration: none;
	transition: background-color .3s;
	}
	.pagination a.active {
	background-color: dodgerblue;
	}
	.pagination a:hover:not(.active) {background-color: #ddd;}
	#customers a:link, a:visited {
	background-color: green;
	color: white;
	padding: 6px 8px;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	}
	input[type=submit] {
	width: 20%;
	background-color: #4CAF50;
	color: white;
	padding: 14px 20px;
	margin: 8px 0;
	border: none;
	border-radius: 4px;
	cursor: pointer;
	}
	input[type=submit]:hover {
	background-color: #45a049;
	}
	input[type=text], select {
	width: 40%;
	padding: 12px 20px;
	margin: 8px 0;
	display: inline-block;
	border: 1px solid #ccc;
	border-radius: 4px;
	box-sizing: border-box;
	}
	</style>