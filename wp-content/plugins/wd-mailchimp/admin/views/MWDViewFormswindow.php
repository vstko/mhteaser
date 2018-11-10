<?php

class MWDViewFormswindow {
	////////////////////////////////////////////////////////////////////////////////////////
	// Events                                                                             //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Constants                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Variables                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	private $model;


	////////////////////////////////////////////////////////////////////////////////////////
	// Constructor & Destructor                                                           //
	////////////////////////////////////////////////////////////////////////////////////////
	public function __construct($model) {
		$this->model = $model;
	}
	////////////////////////////////////////////////////////////////////////////////////////
	// Public Methods                                                                     //
	////////////////////////////////////////////////////////////////////////////////////////
	public function display() {
		$rows = $this->model->get_form_data();
		?>
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<title>MailChimp WD</title>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <?php
      wp_print_styles('forms');
      wp_print_styles('list-tables');
      wp_print_scripts('jquery');
      ?>
      <style>
        #WD-MailChimpForm .panel_wrapper {
          min-height: 110px;
        }
        #WD-MailChimpForm .mceActionPanel #insert {
          float: right;
          outline:none;
          display: inline-block;
          text-decoration: none;
          font-size: 13px;
          line-height: 26px;
          height: 28px;
          margin: 0;
          padding: 0 10px 1px;
          cursor: pointer;
          border-width: 1px;
          border-style: solid;
          -webkit-appearance: none;
          -webkit-border-radius: 3px;
          -webkit-box-sizing: border-box;
          -webkit-box-shadow: 0 1px 0 #ccc;
          box-shadow: 0 1px 0 #ccc;
          border-radius: 3px;
          white-space: nowrap;
          -moz-box-sizing: border-box;
          box-sizing: border-box;
          background: #0085ba;
          border-color: #0073aa #006799 #006799;
          -webkit-box-shadow: 0 1px 0 #006799;
          box-shadow: 0 1px 0 #006799;
          color: #fff;
          text-shadow: 0 -1px 1px #006799, 1px 0 1px #006799, 0 1px 1px #006799, -1px 0 1px #006799;
        }
        #WD-MailChimpForm .no-form{
          padding: 10px;
          font-size: 15px;
          color: red;
        }
      </style>
			<base target="_self">
		</head>
		<body id="WD-MailChimpForm" class="wp-core-ui">
			<div class="panel_wrapper">
				<div id="display_panel" class="panel current">
					<?php if($rows){ ?>
					<table>
						<tr>
							<td style="vertical-align: middle; text-align: left;">
								<select name="mailchimp_form_id" id="mailchimp_form_id" style="width: 275px; text-align: left;">
									<option value="0" selected="selected">- Select a Form -</option>
									<?php foreach ($rows as $row) { ?>
										<option value="<?php echo $row->id; ?>"><?php echo $row->title.' (ID '.$row->id.')'; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
					</table>
					<?php } else { ?>
						<div class="no-form">You have not yet created 'embedded' type form.</div>
					<?php } ?>
				</div>
			</div>
			<?php if($rows){ ?>
			<div class="mceActionPanel">
        <div class="mceActionPanel">
          <input type="submit" id="insert" name="insert" value="<?php echo __('Insert', 'contact_form_maker'); ?>" onClick="mwd_insert_shortcode();"/>
        </div>
			</div>
			<?php } ?>
			<script type="text/javascript">
			var short_code = get_params("mwd-mailchimp");
			if (short_code) {
				document.getElementById("mailchimp_form_id").value = short_code['id'];
			}
        
			function get_params(module_name) {
				var selected_text = top.tinyMCE.activeEditor.selection.getContent();
				var module_start_index = selected_text.indexOf("[" + module_name);
				var module_end_index = selected_text.indexOf("]", module_start_index);
				var module_str = "";
				if ((module_start_index == 0) && (module_end_index > 0)) {
					module_str = selected_text.substring(module_start_index + 1, module_end_index);
				}
				else {
					return false;
				}
				var params_str = module_str.substring(module_str.indexOf(" ") + 1);
				var key_values = params_str.split(" ");
				var short_code_attr = new Array();
				for (var key in key_values) {
					var short_code_index = key_values[key].split('=')[0];
					var short_code_value = key_values[key].split('=')[1];
					short_code_value = short_code_value.substring(1, short_code_value.length - 1);
					short_code_attr[short_code_index] = short_code_value;
				}
				return short_code_attr;
			}
			function mwd_insert_shortcode() {
				if (document.getElementById('mailchimp_form_id').value == '- Select Form -') {
          top.tinyMCE.activeEditor.windowManager.close(window);
				}
				else {
					var tagtext;
					tagtext = '[mwd-mailchimp id="' + document.getElementById('mailchimp_form_id').value + '"]';
					top.tinyMCE.execCommand('mceInsertContent', false, tagtext);
          top.tinyMCE.activeEditor.windowManager.close(window);
				}
			}
			</script>
		</body>
		</html>
		<?php
		die();
	}

	////////////////////////////////////////////////////////////////////////////////////////
	// Getters & Setters                                                                  //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Private Methods                                                                    //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Listeners                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
}