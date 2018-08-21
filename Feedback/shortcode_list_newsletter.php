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
	if($plugin_page == 'shortcode_list_newsletter') {
	?>	<div class="wrap">
	<h2>Newsletter Subscriber List
	</h2>
	<h2></h2>
	<table cellpadding="1" cellspacing="2" style="" id="" class="wp-list-table widefat fixed striped comments">
	<tr>
	<th width="90" >
	<b>Sr.No</b>
	</th>
	<th width="280" >
	<b>Name</b>
	</th>
	<th width="250" >
	<b> Email-ID</b>
	</th>
	<th width="200" >
	<b> Date</b>
	</th> 
	</tr>
	<?php
	global $post, $wpdb;
	$table_name = $wpdb->prefix . 'plugins_new_feedback';
	$total_rows = $wpdb->get_var("SELECT COUNT(*) FROM $table_name where newslatter_sub = 'yes'");
	$no_of_records_per_page = 10;
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
	<?php 
	if($plugin_page == 'shortcode_list_newsletter') {
	global $wpdb;
	$table_name = 'wp_plugins_new_feedback';
	if(!empty($_GET['pages'])){
	$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
	$results_per_page=10; 
	$start_from = ($_GET['pages']-1) * $results_per_page;
	$result = $wpdb->get_results("SELECT * FROM $table_name where newslatter_sub = 'yes' ORDER BY feedback_id DESC LIMIT $start_from,$results_per_page");
	}else{
	$pages = '1';
	$limited = 10;
	$result = $wpdb->get_results("SELECT * FROM $table_name where newslatter_sub = 'yes'  ORDER BY feedback_id DESC LIMIT ".$limited."");
	}
	//$result = $wpdb->get_results("SELECT * FROM $table_name LIMIT ".$limited."");
	$i=$offset;
	if(!empty($result)){
	foreach($result as $r)
	{
	$i++; ?>
	<tr>
	<td>
	<p><?php echo $i;?></p>
	</td>
	<td>
	<p><?php echo ucfirst($r->name);?></p>
	</td>
	<td>
	<p><?php echo $r->email;?></p>
	</td>								
	<td>
	<p><?php $yrdata= strtotime($r->created);
	echo date('d-M, Y', $yrdata);?></p>
	</td>
	</tr>
	<?php } }else{ ?>
	<tr>
	<td colspan="6" align="center">No Records Found.</td></tr>
	<?php }} ?>
	</table>
	Page <?php echo $pageno; ?> of <?php echo $total_pages;?> pages
	<ul class="pagination" >
	<?php if(!empty($pageno) && $pageno != 1 ){ ?>
	<li><a href="<?php echo get_site_url();?>/wp-admin/admin.php?page=shortcode_list_newsletter&pages=1">First</a></li>
	<?php	}  if($pageno > 2) { ?>
	<li class="">
	<a href="<?php  echo get_site_url().'/wp-admin/admin.php?page=shortcode_list_newsletter&pages='.($pageno - 1);  ?>">Prev</a>
	</li>
	<?php } if($pageno > $total_pages){ ?>	
	<li class="">
	<a href="<?php  echo get_site_url().'/wp-admin/admin.php?page=shortcode_list_newsletter&pages='.($pageno + 1); ?>">Next</a>
	</li>
	<?php } if($pageno != $total_pages){ ?>
	<li><a href="<?php echo get_site_url();?>/wp-admin/admin.php?page=shortcode_list_newsletter&pages=<?php echo $total_pages;?>">Last</a></li>
	<?php } ?>
	</ul>
	<?php }} ?>
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