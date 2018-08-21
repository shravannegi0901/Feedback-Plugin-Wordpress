<?php
$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
include($path.'wp-load.php');
global $plugin_page;
$functionName = filter_input(INPUT_GET, 'functionName');
if ($functionName == "delete") {
	delete();
}if($functionName == "paginate_data"){
	paginate_data();
}
global $wpdb, $table_prefix;
function delete()
{
	global $wpdb, $table_prefix;
	if(!empty($_GET['id']))
	{
		$table_name = $wpdb->prefix . "plugins_new_feedback";
		$ids = $_GET['id'];
		$mylink = $wpdb->get_results("SELECT * FROM $table_name WHERE feedback_id = ".$ids."");
		if(!empty($mylink))
		{
			$wpdb->delete($table_name, array( 'feedback_id' => $ids), array( '%d' ) );
			?><script>
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
$pagination_count = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."plugins_link_setting");
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){
	$wp_asin_data = array(
		'facebook_link' => '',
		'pinterset_link' =>'',
		'privacy_link' =>'',
		'pagination_count' =>$_POST['pagination_count'],
	);
	$pagination_count = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."plugins_link_setting");		
	if(!empty($pagination_count)){
		$tid = array('id'=>$pagination_count[0]->id);
		$wpdb->update(''.$wpdb->prefix.'plugins_link_setting', $wp_asin_data,$tid);
	}else{
		$wpdb->insert(''.$wpdb->prefix.'plugins_link_setting', $wp_asin_data);
	}
}
$pagination_count = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."plugins_link_setting");
if(!empty($pagination_count)) {

	$pagination_count = $pagination_count[0]->pagination_count;
}else{
	$pagination_count = 10;
}
if($plugin_page == 'shortcode_list_feedback') {	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>				
	<script type="text/javascript" ></script>
	<script>
		$(document).ready(function () {
			$('.delete_data').click(function () {
				if (!confirm('Are you sure you want to delete?')) {
					return false;
				}
				var valu = $(this).attr('rel');
				var web_path = "<?php echo plugins_url(); ?>/Feedback/shortcode_list_feedback.php";
				$.ajax({
					type : "GET",
					url: web_path,
					data: {id : valu,functionName :"delete"},
					success: function(response) {											
						window.location.href='<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=shortcode_list_feedback';
					}
				}); 	
			});
			$('#load_more').click(function () {
				var valu1 = $(this).attr('rel');
				var web_path1 = "<?php echo plugins_url(); ?>/Feedback/shortcode_list_feedback.php";
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
		$(function() {
			$('form').each(function() {
				$(this).find('input').keypress(function(e) {
	// Enter pressed?
	if(e.which == 10 || e.which == 13) {
		this.form.submit();
	}
});
			});
		});
	</script>	
	<?php
	global $wpdb;
	$table_name = $wpdb->prefix . 'plugins_new_feedback';
	if((isset($_GET['response']) && !empty($_GET['response'])) && (isset($_GET['search']) && !empty($_GET['search']))){
		$response = $_GET['response'];
		$search = $_GET['search'];
		$where = "where (feedback_negative = '".$response."' AND  ".$wpdb->prefix."posts.post_title like '%".$search."%')";
	}elseif((isset($_GET['response']) &&!empty($_GET['response'])) || (isset($_GET['search']) && !empty($_GET['search']))){
		$response = $_GET['response'];
		$search = $_GET['search'];
		if(!empty($_GET['search'])){
			$where = "where (feedback_negative = '".$response."' OR  ".$wpdb->prefix."posts.post_title like '%".$search."%')";
		}else{
			$where = "where feedback_negative = '".$response."'";
		}
		
	}else{
		$where = '';
	}
	

	if(!empty($_GET['pages'])){
		$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
		$results_per_page=$pagination_count; 
		$start_from = ($_GET['pages']-1) * $results_per_page;
		$result = $wpdb->get_results("SELECT * FROM $table_name join ".$wpdb->prefix."posts ON ".$wpdb->prefix."plugins_new_feedback.post_id = ".$wpdb->prefix."posts.ID ".$where." ORDER BY feedback_id DESC LIMIT $start_from,$results_per_page");
		$result_total = $wpdb->get_results("SELECT * FROM $table_name join ".$wpdb->prefix."posts ON ".$wpdb->prefix."plugins_new_feedback.post_id = ".$wpdb->prefix."posts.ID ORDER BY feedback_id DESC");
	}else{
		$pages = '1';
		$limited = $pagination_count;
		$result = $wpdb->get_results("SELECT * FROM $table_name join ".$wpdb->prefix."posts ON ".$wpdb->prefix."plugins_new_feedback.post_id = ".$wpdb->prefix."posts.ID  ".$where." ORDER BY feedback_id DESC LIMIT ".$limited );
		$result_total = $wpdb->get_results("SELECT * FROM $table_name join ".$wpdb->prefix."posts ON ".$wpdb->prefix."plugins_new_feedback.post_id = ".$wpdb->prefix."posts.ID ORDER BY feedback_id DESC" );
	}
	$incorrect_info = $wpdb->get_results("SELECT * FROM $table_name join ".$wpdb->prefix."posts ON ".$wpdb->prefix."plugins_new_feedback.post_id = ".$wpdb->prefix."posts.ID  where feedback_negative='missing information' ORDER BY feedback_id DESC" );
	$incorrect_info_count = count((array)$incorrect_info);
	$incorrect_content = $wpdb->get_results("SELECT * FROM $table_name join ".$wpdb->prefix."posts ON ".$wpdb->prefix."plugins_new_feedback.post_id = ".$wpdb->prefix."posts.ID  where feedback_negative='missing content' ORDER BY feedback_id DESC" );
	$incorrect_content_count = count((array)$incorrect_content);
	$helpful_info = $wpdb->get_results("SELECT * FROM $table_name join ".$wpdb->prefix."posts ON ".$wpdb->prefix."plugins_new_feedback.post_id = ".$wpdb->prefix."posts.ID  where feedback_negative='helpful' ORDER BY feedback_id DESC" );
	$helpful_count = count((array)$helpful_info);
	$all_count = count((array)$result_total); ?>		
	<div class="wrap">
		<h1 class="wp-heading-inline">All Feedback List
		</h1>
		<div id="screen-options-wrap" class="hidden" tabindex="-1" aria-label="Screen Options Tab" style="display: block;">	
			<ul class="subsubsub">
				<li class="all"><a href="<?php echo site_url();?>/wp-admin/admin.php?page=shortcode_list_feedback" class="<?php if(empty($_GET['response'])){	echo "current";	} ?>" aria-current="page">All <span class="count">(<span class="all-count"><?php echo $all_count;?></span>)</span></a> |</li>
				<li class="moderated"><a class="<?php if(isset($_GET['response']) && !empty($_GET['response']) && $_GET['response'] == 'missing information'){	echo "current";	} ?>" href="<?php echo site_url();?>/wp-admin/admin.php?page=shortcode_list_feedback&response=missing+information">Incorrect Information <span class="count">(<span class="pending-count"><?php echo $incorrect_info_count;?></span>)</span></a> |</li>
				<li class="approved"><a class="<?php if(isset($_GET['response']) && !empty($_GET['response']) && $_GET['response'] == 'missing content'){	echo "current";	} ?>" href="<?php echo site_url();?>/wp-admin/admin.php?page=shortcode_list_feedback&response=missing+content">Missing Information <span class="count">(<span class="approved-count"><?php echo $incorrect_content_count;?></span>)</span></a> |</li>
				<li class="spam"><a class="<?php if(isset($_GET['response']) && !empty($_GET['response']) && $_GET['response'] == 'helpful'){	echo "current";	} ?>" href="<?php echo site_url();?>/wp-admin/admin.php?page=shortcode_list_feedback&response=helpful"">Helpful <span class="count">(<span class="spam-count"><?php echo $helpful_count;?></span>)</span></a> 
				</ul>
				<div class="tablenav top">
					<div class="alignleft actions">
						<form action="<?php echo site_url();?>/wp-admin/admin.php/" method="GET">
							<label class="screen-reader-text">Response</label>
							<input type="hidden" name="page" value="shortcode_list_feedback">
							<select name="response" id="">
								<option value="">Select Response</option>
								<option <?php if($_GET['response'] =="missing information"){ echo "selected";} ?> value="missing information"> Incorrect Information</option>
								<option <?php if($_GET['response'] =="missing content"){ echo "selected";} ?> value="missing content">Missing Information</option>
								<option <?php if($_GET['response'] =="helpful"){ echo "selected";} ?> value="helpful">Helpful</option>
							</select>
							<label class="screen-reader-text" for="post-search-input" place>Search Posts:</label>
							<input type="text" placeholder="Search by post name"  name="search" style="height: 28px; width:280px;" value="<?php if($_GET['search']){ echo $_GET['search'];} ?>">	
							<input type="submit" id="search-submit" class="button" value="Search">
						</form>
						<form action="<?php echo site_url();?>/wp-admin/admin.php?page=shortcode_list_feedback" method="POST">
							<label class="" for="post-search-input" place>Post Per Page:</label><br>
							<input type="number" placeholder=""  name="pagination_count" style="height: 28px; width:280px;" value="<?php echo $pagination_count; ?>">
							<input type="submit" id="search-submit" class="button" value="Save">
						</form><br>
					</div>
				</div><br>
				<table cellpadding="1" cellspacing="2" style="" id="" class="wp-list-table widefat fixed striped comments">
					<tr>
						<th  width="10%" class="manage-column" >
							Date
						</th>					
						<th width="15%" class="manage-column" >
							Response
						</th>
						<th width="50%"  class="manage-column">
							Feedback
						</th>
						<th width="15%"  class="manage-column">
							User Info
						</th>					
						<th class="delete_data" width="8%" class="manage-column" >
							Action
						</th>
					</tr>
					<?php 
					if($plugin_page == 'shortcode_list_feedback') {
	//$result = $wpdb->get_results("SELECT * FROM $table_name LIMIT ".$limited."");
						$i=1;
						$update_data = array('read_status'=>'yes');
						$wpdb->query('update '.$wpdb->prefix.'plugins_new_feedback set read_status = "yes"');
						if(!empty($result)){
							foreach($result as $r)
								{ ?>
									<tr>
										<td>
											<p><?php $yrdata= strtotime($r->created);
											echo date('d-M, Y', $yrdata);?></p>
										</td>
										<td>
											<?php
											if($r->feedback_negative =='helpful'){
												$response = "<p style='color:green'>Helpful</p>";
											}elseif($r->feedback_negative =='missing content'){
												$response = "<p style='color:red'>Missing Information</p>";
											}else{
												$response = "<p style='color:red'>Incorrect Information</p>";
											} ?>
											<?php echo $response;?>
										</td>
										<td>
											<p><?php echo nl2br($r->feedback);?></p>
										</td>
										<td>									
											<p><?php echo $r->name;?><br></p>
											<p><?php echo $r->email;?></p>
										</td>
										<td>
											<?php 
											if($r->type == 'post'){
												$link = get_permalink($r->post_id);
											}else{
												$link = get_page_link($r->post_id);										
											} ?>
											<p><a href="<?php echo $link;?>" target="_blank" >View Post</a></p>
											<p><a class='delete_data' href="javascript:void(0)" rel=" <?php echo $r->feedback_id;?>" >Delete</a></p>
										</td>
									</tr>
									<?php $i++;} }else{ ?>
										<tr>
											<td colspan="6" align="center">No Records Found.</td></tr>
										<?php }} ?>
									</table>
									<?php
									global $post, $wpdb;
									$table_name = $wpdb->prefix . 'plugins_new_feedback';
									$total_rows = count((array)$result_total);
									$no_of_records_per_page = $pagination_count;
									$total_pages = ceil($total_rows / $no_of_records_per_page);
	//$limit = 8;
	//$pages = ceil($rowcount / $limit);
									if (isset($_GET['pages'])) {
										$pageno = $_GET['pages'];
									} else {
										$pageno = 1;
									}
									$offset = ($pageno-1) * $no_of_records_per_page;
									if((isset($_GET['response']) &&!empty($_GET['response'])) || (isset($_GET['search']) && !empty($_GET['search']))){
										$query = "&response=".$_GET['response']."&search=".$_GET['search'];
									}else{
										$query = '';
									}
									if(!empty($total_pages)){ ?>
										Page <?php echo $pageno; ?> of <?php echo $total_pages;?> pages
										<ul class="pagination" >
											<?php if(!empty($pageno) && $pageno != 1 ){ ?>
												<li><a href="<?php echo get_site_url();?>/wp-admin/admin.php?page=shortcode_list_feedback<?php echo $query;?>&pages=1">First</a></li>
											<?php }  if($pageno > 2) { ?>
												<li class="">
													<a href="<?php  echo get_site_url().'/wp-admin/admin.php?page=shortcode_list_feedback'.$query.'&pages='.($pageno - 1);  ?>">Prev</a>
												</li>
											<?php } if($pageno > $total_pages || ($total_pages> 2 && $total_pages > $pageno)){  ?>	
												<li class="">
													<a href="<?php  echo get_site_url().'/wp-admin/admin.php?page=shortcode_list_feedback'.$query.'&pages='.($pageno + 1); ?>">Next</a>
												</li>
											<?php } if($pageno != $total_pages){ ?>
												<li><a href="<?php echo get_site_url();?>/wp-admin/admin.php?page=shortcode_list_feedback<?php echo $query;?>&pages=<?php echo $total_pages;?>">Last</a></li>
											<?php } ?>
										</ul>
									<?php }	} ?>
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
						</style>