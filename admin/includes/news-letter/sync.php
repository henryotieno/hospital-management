<?php
$retval = $api->lists();
?>
<script type="text/javascript">
$(document).ready(function() 
{
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#setting_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#setting_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
});
</script>
<div class="panel-body"><!--PANEL BODY DIV START-->
    <form name="template_form" action="" method="post" class="form-horizontal" id="setting_form">
	    <div class="form-group">
			<label class="col-sm-2 control-label" for="enable_quote_tab"><?php esc_html_e('Class List','hospital_mgt');?></label>
			<div class="col-sm-8">
				<div class="checkbox">
					<?php 	
						$classdata=$obj_class->get_all_classes();
						if(!empty($classdata))
						{
							foreach ($classdata as $retrieved_data)
							{
							?>
								<label>
									<input type="checkbox" name="syncmail[]"  value="<?php echo $retrieved_data->class_id?>"/><?php echo esc_html($retrieved_data->class_name);?>
								</label><br/>
							<?php 
							}
						}?>
			    </div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="list_id"><?php esc_html_e('Mailing List','hospital_mgt');?><span class="require-field">*</span></label>
			<div class="col-sm-8">
				<select name="list_id" id="list_id"  class="form-control validate[required] max_width_100">
					<option value=""><?php esc_html_e('Select list','hospital_mgt');?></option>
					<?php 
					foreach ($retval['data'] as $list)
					{
						echo '<option value="'.$list['id'].'">'.$list['name'].'</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="col-sm-offset-2 col-sm-8">        	
        	<input type="submit" value="<?php esc_html_e('Sync Mail', 'hospital_mgt' ); ?>" name="sychroniz_email" class="btn btn-success"/>
        </div>
    </form>
</div><!--PANEL BODY DIV END-->