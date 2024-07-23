<?php
require_once HMS_PLUGIN_DIR. '/lib/vendor/autoload.php';
class MJ_hmgt_virtual_appointment
{
	// GET ALL MEETING DATA IN ZOOM
	public function MJ_hmgt_get_all_meeting_data_in_zoom()
	{
		global $wpdb;
		$table_zoom_meeting= $wpdb->prefix. 'hmgt_zoom_meeting';
		$result = $wpdb->get_results("SELECT * FROM $table_zoom_meeting");
		return $result;
	}
	// GET SINGAL MEETING DATA IN ZOOM
	public function MJ_hmgt_get_singal_meeting_data_in_zoom($meeting_id)
	{
		global $wpdb;
		$table_zoom_meeting= $wpdb->prefix. 'hmgt_zoom_meeting';
		$result = $wpdb->get_row("SELECT * FROM $table_zoom_meeting WHERE meeting_id=$meeting_id");
		return $result;
	}
	public function MJ_hmgt_get_singal_meeting_data_in_zoom_with_appointment_id($appointment_id)
	{
		global $wpdb;
		$table_zoom_meeting= $wpdb->prefix. 'hmgt_zoom_meeting';
		$result = $wpdb->get_row("SELECT * FROM $table_zoom_meeting WHERE appointment_id=$appointment_id");
		return $result;
	}	
	// DELETE MEETING
	public function MJ_hmgt_delete_meeting_in_zoom($meeting_id)
	{
		global $wpdb;
		// generate_access_token();
		$meeting_data = $this->MJ_hmgt_get_singal_meeting_data_in_zoom($meeting_id);
		try
		{
			if(!empty($meeting_data))
			{
				$client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
				$arr_token = get_option('hmgt_virtual_appointment_access_token');
		    	$token_decode = json_decode($arr_token);
			    $accessToken = $token_decode->access_token;
			    $zoom_meeting_id = $meeting_data->zoom_meeting_id;
			    $response = $client->request('DELETE', "/v2/meetings/{$zoom_meeting_id}", [
			    "headers" => [
			        "Authorization" => "Bearer $accessToken"
			    ]
			    ]);
			}
			$table_zoom_meeting= $wpdb->prefix. 'hmgt_zoom_meeting';
			$result = $wpdb->query("DELETE FROM $table_zoom_meeting WHERE meeting_id=$meeting_id");
		}catch(Exception $e){
	    	if(401 == $e->getCode())
			{
				generate_access_token();
			}
			else
			{
				wp_redirect ( admin_url().'admin.php?page=hmgt_virtual_appointment&tab=meeting_list&message=5');
			}
	    }
		return $result;
	}
	// PAST PARTICIPEL LIST
	public function MJ_hmgt_view_past_participle_list_in_zoom($meeting_uuid)
	{
		// generate_access_token();
		$client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
		$arr_token = get_option('hmgt_virtual_appointment_access_token');
    	$token_decode = json_decode($arr_token);
	    $accessToken = $token_decode->access_token;
	    try
	    {
			$response = $client->request('GET', "v2/past_meetings/{$meeting_uuid}/participants", [
	            "headers" => [
	                "Authorization" => "Bearer $accessToken"
	            ],
	            "Query" =>[
	                "type" => 'past',
	                "page_size" => 30,
	                "include_fields" => 'device',
	            ]
	        ]);
	        $result = json_decode($response->getBody());
	    }catch(Exception $e)
	    {
	    	if(401 == $e->getCode())
			{
				generate_access_token();
			}
			// elseif (404== $e->getCode()) 
			// {
			// 	wp_redirect ( admin_url().'admin.php?page=smgt_virtual_classroom&tab=meeting_list&message=6');
			// }
			// else
			// {
			// 	wp_redirect ( admin_url().'admin.php?page=smgt_virtual_classroom&tab=meeting_list&message=5');
			// }
	    }
	    return $result;
	}
	public function MJ_hmgt_get_all_meeting_created_by($created_by)
	{
		global $wpdb;
		$table_zoom_meeting= $wpdb->prefix. 'hmgt_zoom_meeting';
		$result = $wpdb->get_results("SELECT * FROM $table_zoom_meeting WHERE doctor_id=$created_by OR created_by=$created_by");
		return $result;
	}
	public function MJ_hmgt_get_all_patient_meeting_data_in_zoom($created_by)
	{
		global $wpdb;
		$table_zoom_meeting= $wpdb->prefix. 'hmgt_zoom_meeting';
		$result = $wpdb->get_results("SELECT * FROM $table_zoom_meeting WHERE patient_id=$created_by OR created_by=$created_by");
		return $result;
	}
}
?>