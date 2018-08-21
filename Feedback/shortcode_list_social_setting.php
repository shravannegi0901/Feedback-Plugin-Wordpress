<?php
$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
include($path.'wp-load.php');
global $plugin_page;
global $wpdb;
if($plugin_page == 'shortcode_list_social_setting') {
	?>	
	<div class="wrap">
		<h2>Link Setting
		</h2>
		<?php
		$result= $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."plugins_link_setting");
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){
			$wp_asin_data = array(
				'facebook_link' => $_POST['facebook_link'],
				'pinterset_link' =>$_POST['pinterset_link'],
				'privacy_link' =>$_POST['privacy_link'],
				'list_key' =>$_POST['list_key'],
				'api_key' =>$_POST['api_key'],
			);
			if(!empty($result)){
				$tid = array('id'=>$result[0]->id);
				$wpdb->update(''.$wpdb->prefix.'plugins_link_setting', $wp_asin_data,$tid);
			}else{
				$wpdb->insert(''.$wpdb->prefix.'plugins_link_setting', $wp_asin_data);
			}
		}
		$result= $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."plugins_link_setting");
		if(!empty($result)){
			$fb_link = $result[0]->facebook_link;
			$p_link = $result[0]->pinterset_link;
			$priv_link = $result[0]->privacy_link;
			$list_key = $result[0]->list_key;
			$api_key = $result[0]->api_key;
		}else{
			$fb_link = '';
			$p_link = '';
			$priv_link = '';
			$list_key = '';
			$api_key = '';
		} ?>
		<div id="welcome-panel" class="welcome-panel">
			<h2></h2>
			<div id="screen-options-wrap" class="hidden" tabindex="-1" aria-label="Screen Options Tab" style="display: block;">
				<div class="row">
					<form method="POST" action="<?php echo site_url();?>/wp-admin/admin.php?page=shortcode_list_social_setting">
						<div id="titlewrap">
							<label ><strong>Facebook Link :</strong></label><br>
							<input  type="text" name="facebook_link"  id="facebook" value="<?php echo $fb_link;?>" >
							<span id="name_error"></span>
						</div><br>
						<div id="titlewrap">
							<label class="width100"><strong>Pinterset Link:</strong></label><br>
							<input type="text" name="pinterset_link"  id="pinterset" value="<?php echo $p_link;?>" >
							<span id="email_error"></span>
						</div><br>
						<div id="titlewrap">
							<label class="width100"><strong>Privacy Link:</strong></label><br>
							<input type="text" name="privacy_link"  id="privacy" value="<?php echo $priv_link;?>" >
							<span id="email_error"></span>
						</div><br>
						<div id="titlewrap">
							<label class="width100"><strong>Mailchimp key:</strong></label><br>
							<input type="text" name="api_key"  id="privacy" value="<?php echo $api_key;?>" >
							<span id="email_error"></span>
						</div><br>
						<div id="titlewrap">
							<label class="width100"><strong>Mailchimp List key:</strong></label><br>
							<input type="text" name="list_key"  id="privacy" value="<?php echo $list_key;?>" >
							<span id="email_error"></span>
						</div><br>
						<input type="Submit" value="Save">
					</form>
				</div>	
			</div>	
		</div>	
	<?php } ?>
	<style>
	input[type=text], select {
		width: 50%;
		padding: 12px 20px;
		margin: 8px 0;
		display: inline-block;
		border: 1px solid #ccc;
		border-radius: 4px;
		box-sizing: border-box;
	}
	input[type=submit] {
		width: 30%;
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
</style>