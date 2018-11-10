<?php
class MWDViewThemes_mwd {
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
		$rows_data = $this->model->get_rows_data();
		$page_nav = $this->model->page_nav();
		$search_value = ((isset($_POST['search_value'])) ? esc_html($_POST['search_value']) : '');
		$asc_or_desc = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
		$order_by_array = array('id', 'title', 'default');
		$order_by = isset($_POST['order_by']) && in_array(esc_html(stripslashes($_POST['order_by'])), $order_by_array) ? esc_html(stripslashes($_POST['order_by'])) :  'id';
		$order_class = 'manage-column column-title sorted ' . $asc_or_desc;
		$ids_string = '';
		$form_types = array('0' => 'Select Type', 'embedded' => 'embedded', 'popover' => 'popover', 'topbar' => 'topbar', 'scrollbox' => 'scrollbox');
		MWD_Library::mwd_upgrade_pro();
		?>
		
		<form class="wrap" id="themes_form" method="post" action="admin.php?page=themes_mwd">
			<?php wp_nonce_field('nonce_mwd', 'nonce_mwd'); ?>
			<div class="mwd-page-banner">
				<div class="mwd-logo">
				</div>
				<div class="mwd-logo-title">Themes</div>
				<button class="mwd-button add-button medium" onclick="mwd_set_input_value('task', 'add'); mwd_form_submit(event, 'themes_form');">
					<span></span>
					Add New
				</button>
				<div class="mwd-page-actions">
					<button class="mwd-button  save-as-copy-button small" onclick="mwd_set_input_value('task', 'copy_themes'); mwd_form_submit(event, 'themes_form');">
						<span></span>
						Copy
					</button>
					<button class="mwd-button delete-button small" onclick="if (confirm('Do you want to delete selected item(s)?')) { mwd_set_input_value('task', 'delete_all'); } else { return false; }">
						<span></span>
						Delete
					</button>
				</div>
			</div>
			<div class="mwd-clear"></div>
			<div class="tablenav top">
				<?php
					MWD_Library::search('Title', $search_value, 'themes_form');
					MWD_Library::html_page_nav($page_nav['total'], $page_nav['limit'], 'themes_form');
				?>
			</div>
			<table class="wp-list-table widefat fixed pages">
				<thead>
					<th class="manage-column column-cb check-column table_small_col"><input id="check_all" type="checkbox" style="margin:0;"/></th>
					<th class="table_small_col <?php if ($order_by == 'id') { echo $order_class; } ?>">
						<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'id'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'id' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'themes_form')" href="">
						<span>ID</span><span class="sorting-indicator"></span></a>
					</th>
					<th class="<?php if ($order_by == 'title') { echo $order_class; } ?>">
						<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'title'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'title' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'themes_form')" href="">
						<span>Title</span><span class="sorting-indicator"></span></a>
					</th>
					<th class="table_big_col <?php if ($order_by == 'default') { echo $order_class; } ?>">
						<a onclick="mwd_set_input_value('task', ''); mwd_set_input_value('order_by', 'default'); mwd_set_input_value('asc_or_desc', '<?php echo (($order_by == 'default' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); mwd_form_submit(event, 'themes_form')" href="">
						<span>Default</span><span class="sorting-indicator"></span></a>
					</th>
					<th class="table_small_col">Edit</th>
					<th class="table_small_col">Delete</th>
				</thead>
				<tbody id="tbody_arr">
				<?php
					if ($rows_data) {
						foreach ($rows_data as $row_data) {
							$alternate = (!isset($alternate) || $alternate == 'class="alternate"') ? '' : 'class="alternate"';
							$default_image = (($row_data->default) ? 'default' : 'notdefault');
							$default = (($row_data->default) ? '' : 'setdefault');
							?>
							<tr id="tr_<?php echo $row_data->id; ?>" <?php echo $alternate; ?>>
								<td class="table_small_col check-column">
									<input id="check_<?php echo $row_data->id; ?>" name="check_<?php echo $row_data->id; ?>" type="checkbox"/>
								</td>
								<td class="table_small_col"><?php echo $row_data->id; ?></td>
								<td>
									<a onclick="mwd_set_input_value('task', 'edit'); mwd_set_input_value('current_id', '<?php echo $row_data->id; ?>'); mwd_form_submit(event, 'themes_form')" href="" title="Edit"><?php echo $row_data->title; ?></a>
								</td>
								<td class="table_big_col">
									<?php if ($default != '') { ?>
										<a onclick="mwd_set_input_value('task', '<?php echo $default; ?>'); mwd_set_input_value('current_id', '<?php echo $row_data->id; ?>'); mwd_form_submit(event, 'themes_form')" href="">
									<?php } ?>
										<img src="<?php echo MWD_URL . '/images/' . $default_image . '.png?ver='. get_option("mwd_version").''; ?>" />
									<?php if ($default != '') { ?>
										</a>
									<?php } ?>
								</td>
								<td class="table_small_col">
									<button class="mwd-icon edit-icon" onclick="mwd_set_input_value('task', 'edit'); mwd_set_input_value('current_id', '<?php echo $row_data->id; ?>'); mwd_form_submit(event, 'themes_form');">
										<span></span>
									</button>
								</td>
								<td class="table_small_col">
									<button class="mwd-icon delete-icon" onclick="if (confirm('Do you want to delete selected item(s)?')) { mwd_set_input_value('task', 'delete'); mwd_set_input_value('current_id', '<?php echo $row_data->id; ?>'); mwd_form_submit(event, 'themes_form'); } else {return false;}">
										<span></span>
									</button>
								</td>
							</tr>
							<?php
							$ids_string .= $row_data->id . ',';
						}
					}
					?>
				</tbody>
			</table>
			<input id="task" name="task" type="hidden" value=""/>
			<input id="current_id" name="current_id" type="hidden" value=""/>
			<input id="ids_string" name="ids_string" type="hidden" value="<?php echo $ids_string; ?>"/>
			<input id="asc_or_desc" name="asc_or_desc" type="hidden" value="asc"/>
			<input id="order_by" name="order_by" type="hidden" value="<?php echo $order_by; ?>"/>
		</form>
		<?php
	}

	public function edit($id, $reset) {
		$row = $this->model->get_row_data($id, $reset);
		$page_title = 'Theme: ' . $row->title;
		$param_values = json_decode(html_entity_decode($row->params), true);
		$border_types = array( 'solid' => 'Solid', 'dotted' => 'Dotted', 'dashed' => 'Dashed', 'double' => 'Double', 'groove' => 'Groove', 'ridge' => 'Ridge', 'inset' => 'Inset', 'outset' => 'Outset', 'initial' => 'Initial', 'inherit' => 'Inherit', 'hidden' => 'Hidden', 'none' => 'None' );
		$borders = array('top' => 'Top', 'right' => 'Right', 'bottom' => 'Bottom', 'left' => 'Left' );
		$border_values = array('top' => 'BorderTop', 'right' => 'BorderRight', 'bottom' => 'BorderBottom', 'left' => 'BorderLeft' );
		$position_types = array('static' => 'Static', 'relative' => 'Relative', 'fixed' => 'Fixed', 'absolute' => 'Absolute' );

		$font_weights = array( 'normal' => 'Normal', 'bold' => 'Bold', 'bolder' => 'Bolder', 'lighter' => 'Lighter', 'initial' => 'Initial' );
		$aligns = array( 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' );
		$aligns_no_center = array( 'left' => 'Left', 'right' => 'Right' );

		$basic_fonts = array(  'arial' => 'Arial', 'lucida grande' => 'Lucida grande', 'segoe ui' => 'Segoe ui', 'tahoma' => 'Tahoma', 'trebuchet ms' => 'Trebuchet ms', 'verdana' => 'Verdana', 'cursive' =>'Cursive', 'fantasy' => 'Fantasy','monospace' => 'Monospace', 'serif' => 'Serif' );

		$bg_repeats = array(  'repeat' => 'repeat', 'repeat-x' => 'repeat-x', 'repeat-y' => 'repeat-y', 'no-repeat' => 'no-repeat', 'initial' => 'initial', 'inherit' => 'inherit');

		$google_fonts = MWD_Library::mwd_get_google_fonts();
		$font_families = $basic_fonts + $google_fonts;
		$fonts = implode("|", str_replace(' ', '+', $google_fonts));
		wp_enqueue_style('mwd_googlefonts', 'https://fonts.googleapis.com/css?family=' . $fonts . '&subset=greek,latin,greek-ext,vietnamese,cyrillic-ext,latin-ext,cyrillic', null, null);

		$tabs = array(
			'global' => 'Global Parameters',
			'header' => 'Header',
			'content' => 'Content',
			'input_select' => 'Inputbox',
			'choices' => 'Choices',
			'subscribe' => 'General Buttons',
			'paigination' => 'Pagination',
			'buttons' => 'Buttons',
			'close_button' => 'Close(Minimize) Button',
			'minimize' => 'Minimize Text',
			'other' => 'Other',
			'custom_css' => 'Custom CSS'
		);

		$all_params = array(
			'global' => array(
				array (
					'label' => '',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => '',
					'after' => ''
				),
				array (
					'label' => 'Font Family',
					'name' => 'GPFontFamily',
					'type' => 'select',
					'options' => $font_families,
					'class' => '',
					'value' => $param_values['GPFontFamily'],
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'AGPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['AGPWidth'],
					'after' => '%'
				),
				array (
					'label' => 'Width (for scrollbox, popup form types)',
					'name' => 'AGPSPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['AGPSPWidth'],
					'after' => '%'
				),
				array (
					'label' => 'Padding',
					'name' => 'AGPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['AGPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'AGPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['AGPMargin'],
					'placeholder' => 'e.g. 5px 10px or 5% 10%',
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'AGPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'AGPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['AGPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'AGPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['AGPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'AGPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['AGPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'AGPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['AGPBorderRadius'],
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'AGPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['AGPBoxShadow'],
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => '</div>'
				)
			),
			'header' => array(
				array (
					'label' => 'General Parameters',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Alignment',
					'name' => 'HPAlign',
					'type' => 'select',
					'options' => $borders,
					'class' => '',
					'value' => $param_values['HPAlign'],
					'after' => ''
				),
				array (
					'label' => 'Background Color',
					'name' => 'HPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['HPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'HPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['HPWidth'],
					'after' => '%'
				),
				array (
					'label' => 'Width (for topbar form type)',
					'name' => 'HTPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['HTPWidth'],
					'after' => '%'
				),
				array (
					'label' => 'Padding',
					'name' => 'HPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['HPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'HPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['HPMargin'],
					'placeholder' => 'e.g. 5px 10px or 5% 10%',
					'after' => 'px/%'
				),
				array (
					'label' => 'Text Align',
					'name' => 'HPTextAlign',
					'type' => 'select',
					'options' => $aligns,
					'class' => '',
					'value' => $param_values['HPTextAlign'],
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'HPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'HPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['HPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'HPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['HPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'HPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['HPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'HPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['HPBorderRadius'],
					'after' => 'px</div>'
				),
				array (
					'label' => 'Title Parameters',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Font Size',
					'name' => 'HTPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['HTPFontSize'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'HTPWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => $param_values['HTPWeight'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'HTPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['HTPColor'],
					'after' => ''
				),
				array (
					'label' => 'Description Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Font Size',
					'name' => 'HDPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['HDPFontSize'],
					'after' => 'px'
				),
				array (
					'label' => 'Color',
					'name' => 'HDPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['HDPColor'],
					'after' => ''
				),
				array (
					'label' => 'Image Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Alignment',
					'name' => 'HIPAlign',
					'type' => 'select',
					'options' => $borders,
					'class' => '',
					'value' => $param_values['HIPAlign'],
					'after' => 'px'
				),
				array (
					'label' => 'Width',
					'name' => 'HIPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['HIPWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'HIPHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['HIPHeight'],
					'after' => 'px</div>'
				)
			),
			'content' => array(
				array (
					'label' => 'General Parameters',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'GPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['GPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Font Size',
					'name' => 'GPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['GPFontSize'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'GPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => $param_values['GPFontWeight'],
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'GPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['GPWidth'],
					'after' => '%'
				),
				array (
					'label' => 'Width (for topbar form type)',
					'name' => 'GTPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['GTPWidth'],
					'after' => '%'
				),
				array (
					'label' => 'Alignment',
					'name' => 'GPAlign',
					'type' => 'select',
					'options' => $aligns,
					'class' => '',
					'value' => $param_values['GPAlign'],
					'after' => ''
				),
				array (
					'label' => 'Background URL',
					'name' => 'GPBackground',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['GPBackground'],
					'after' => ''
				),
				array (
					'label' => 'Background Repeat',
					'name' => 'GPBackgroundRepeat',
					'type' => 'select',
					'options' => $bg_repeats,
					'class' => '',
					'value' => $param_values['GPBackgroundRepeat'],
					'after' => ''
				),
				array (
					'label' => 'Background Position',
					'name1' => 'GPBGPosition1',
					'name2' => 'GPBGPosition2',
					'type' => '2text',
					'class' => 'mwd-2text',
					'value1' => $param_values['GPBGPosition1'],
					'value2' => $param_values['GPBGPosition2'],
					'before1' => '',
					'before2' => '',
					'after' => '%/left..'
				),
				array (
					'label' => 'Background Size',
					'name1' => 'GPBGSize1',
					'name2' => 'GPBGSize2',
					'type' => '2text',
					'class' => 'mwd-2text',
					'value1' => $param_values['GPBGSize1'],
					'value2' => $param_values['GPBGSize2'],
					'before1' => '',
					'before2' => '',
					'after' => '%/px'
				),
				array (
					'label' => 'Color',
					'name' => 'GPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['GPColor'],
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'GPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['GPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'GPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['GPMargin'],
					'placeholder' => 'e.g. 5px 10px or 5% 10%',
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'GPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'GPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['GPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'GPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['GPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'GPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['GPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'GPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['GPBorderRadius'],
					'after' => 'px</div>'
				),
				array (
					'label' => 'Mini labels (name, phone, address, checkbox, radio) Parameters',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Font Size',
					'name' => 'GPMLFontSize',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['GPMLFontSize'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'GPMLFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => $param_values['GPMLFontWeight'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'GPMLColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['GPMLColor'],
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'GPMLPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['GPMLPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'GPMLMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['GPMLMargin'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Section Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'SEPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['SEPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'SEPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SEPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'SEPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SEPMargin'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Section Column Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Padding',
					'name' => 'COPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['COPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'COPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['COPMargin'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Footer Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Width',
					'name' => 'FPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['FPWidth'],
					'after' => '%'
				),
				array (
					'label' => 'Padding',
					'name' => 'FPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['FPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'FPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['FPMargin'],
					'after' => 'px/%</div>'
				)
			),
			'input_select' => array(
				array (
					'label' => '',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => '',
					'after' => ''
				),
				array (
					'label' => 'Height',
					'name' => 'IPHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['IPHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Size',
					'name' => 'IPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['IPFontSize'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'IPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => $param_values['IPFontWeight'],
					'after' => ''
				),
				array (
					'label' => 'Background Color',
					'name' => 'IPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['IPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'IPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['IPColor'],
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'IPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['IPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'IPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['IPMargin'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'IPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'IPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['IPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'IPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['IPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'IPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['IPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'IPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['IPBorderRadius'],
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'IPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['IPBoxShadow'],
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => '</div>'
				),
				array (
					'label' => 'Dropdown additional',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Appearance',
					'name' => 'SBPAppearance',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SBPAppearance'],
					'after' => ''
				),
				array (
					'label' => 'Background URL',
					'name' => 'SBPBackground',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SBPBackground'],
					'after' => ''
				),
				array (
					'label' => 'Background Repeat',
					'name' => 'SBPBGRepeat',
					'type' => 'select',
					'options' => $bg_repeats,
					'class' => '',
					'value' => $param_values['SBPBGRepeat'],
					'after' => ''
				),
				array (
					'label' => 'Background Position',
					'name1' => 'SBPBGPos1',
					'name2' => 'SBPBGPos2',
					'type' => '2text',
					'class' => 'mwd-2text',
					'value1' => $param_values['SBPBGPos1'],
					'value2' => $param_values['SBPBGPos2'],
					'before1' => '',
					'before2' => '',
					'after' => '%/left..'
				),
				array (
					'label' => 'Background Size',
					'name1' => 'SBPBGSize1',
					'name2' => 'SBPBGSize2',
					'type' => '2text',
					'class' => 'mwd-2text',
					'value1' => $param_values['SBPBGSize1'],
					'value2' => $param_values['SBPBGSize2'],
					'before1' => '',
					'before2' => '',
					'after' => '%/px'
				),
				array (
					'label' => '',
					'type' => 'label',
					'class' => '',
					'after' => '</div>'
				)
			),
			'choices' => array(
				array (
					'label' => 'Single Choice',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Input Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'SCPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['SCPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'SCPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SCPWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'SCPHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SCPHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Border',
					'name' => 'SCPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'SCPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['SCPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'SCPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['SCPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'SCPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SCPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Margin',
					'name' => 'SCPMargin',
					'type' => 'text',
					'class' => '5px',
					'value' => $param_values['SCPMargin'],
					'after' => ''
				),
				array (
					'label' => 'Border Radius',
					'name' => 'SCPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SCPBorderRadius'],
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'SCPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SCPBoxShadow'],
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => ''
				),
				array (
					'label' => 'Checked Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'SCCPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['SCCPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'SCCPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SCCPWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'SCCPHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SCCPHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Margin',
					'name' => 'SCCPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SCCPMargin'],
					'after' => ''
				),
				array (
					'label' => 'Border Radius',
					'name' => 'SCCPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SCCPBorderRadius'],
					'after' => 'px</div>'
				),
				array (
					'label' => 'Multiple Choice',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Input Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'MCPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['MCPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'MCPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MCPWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'MCPHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MCPHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Border',
					'name' => 'MCPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'MCPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['MCPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'MCPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['MCPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'MCPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MCPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Margin',
					'name' => 'MCPMargin',
					'type' => 'text',
					'class' => '5px',
					'value' => $param_values['MCPMargin'],
					'after' => ''
				),
				array (
					'label' => 'Border Radius',
					'name' => 'MCPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MCPBorderRadius'],
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'MCPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MCPBoxShadow'],
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => ''
				),
				array (
					'label' => 'Checked Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'MCCPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['MCCPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Background URL',
					'name' => 'MCCPBackground',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MCCPBackground'],
					'after' => ''
				),
				array (
					'label' => 'Background Repeat',
					'name' => 'MCCPBGRepeat',
					'type' => 'select',
					'options' => $bg_repeats,
					'class' => '',
					'value' => $param_values['MCCPBGRepeat'],
					'after' => ''
				),
				array (
					'label' => 'Background Position',
					'name1' => 'MCCPBGPos1',
					'name2' => 'MCCPBGPos2',
					'type' => '2text',
					'class' => 'mwd-2text',
					'value1' => $param_values['MCCPBGPos1'],
					'value2' => $param_values['MCCPBGPos2'],
					'before1' => '',
					'before2' => '',
					'after' => '%/left..'
				),
				array (
					'label' => 'Width',
					'name' => 'MCCPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MCCPWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'MCCPHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MCCPHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Margin',
					'name' => 'MCCPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MCCPMargin'],
					'after' => ''
				),
				array (
					'label' => 'Border Radius',
					'name' => 'MCCPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MCCPBorderRadius'],
					'after' => 'px</div>'
				)
			),
			'subscribe' => array(
				array (
					'label' => 'Global Parameters',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Alignment',
					'name' => 'SPAlign',
					'type' => 'select',
					'options' => $aligns_no_center,
					'class' => '',
					'value' => $param_values['SPAlign'],
					'after' => '</div>'
				),
				array (
					'label' => 'Subscribe',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'SPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['SPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'SPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SPWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'SPHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SPHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Size',
					'name' => 'SPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SPFontSize'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'SPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => $param_values['SPFontWeight'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'SPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['SPColor'],
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'SPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'SPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SPMargin'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'SPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'SPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['SPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'SPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['SPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'SPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'SPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SPBorderRadius'],
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'SPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SPBoxShadow'],
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => ''
				),
				array (
					'label' => 'Hover Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'SHPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['SHPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'SHPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['SHPColor'],
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'SHPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'SHPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['SHPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'SHPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['SHPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'SHPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['SHPBorderWidth'],
					'after' => 'px</div>'
				),
				array (
					'label' => 'Reset',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'BPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['BPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'BPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['BPWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'BPHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['BPHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Size',
					'name' => 'BPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['BPFontSize'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'BPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => $param_values['BPFontWeight'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'BPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['BPColor'],
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'BPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['BPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'BPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['BPMargin'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'BPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'BPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['BPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'BPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['BPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'BPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['BPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'BPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['BPBorderRadius'],
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'BPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['BPBoxShadow'],
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => ''
				),
				array (
					'label' => 'Hover Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'BHPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['BHPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'BHPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['BHPColor'],
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'BHPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'BHPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['BHPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'BHPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['BHPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'BHPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['BHPBorderWidth'],
					'after' => 'px</div>'
				)
			),
			'paigination' => array(
				array (
					'label' => 'Active',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'mwd-mini-title',
					'after' => ''
				),
				array (
					'label' => 'Background Color',
					'name' => 'PSAPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['PSAPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Font Size',
					'name' => 'PSAPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSAPFontSize'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'PSAPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => $param_values['PSAPFontWeight'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'PSAPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['PSAPColor'],
					'after' => ''
				),
				array (
					'label' => 'Height',
					'name' => 'PSAPHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSAPHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Line Height',
					'name' => 'PSAPLineHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSAPLineHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Padding',
					'name' => 'PSAPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSAPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'PSAPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSAPMargin'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'PSAPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'PSAPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['PSAPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'PSAPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['PSAPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'PSAPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSAPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'PSAPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSAPBorderRadius'],
					'after' => 'px</div>'
				),
				array (
					'label' => 'Deactive',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'mwd-mini-title',
					'after' => ''
				),
				array (
					'label' => 'Background Color',
					'name' => 'PSDPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['PSDPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Font Size',
					'name' => 'PSDPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSDPFontSize'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'PSDPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => $param_values['PSDPFontWeight'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'PSDPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['PSDPColor'],
					'after' => ''
				),
				array (
					'label' => 'Height',
					'name' => 'PSDPHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSDPHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Line Height',
					'name' => 'PSDPLineHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSDPLineHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Padding',
					'name' => 'PSDPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSDPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'PSDPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSDPMargin'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'PSDPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'PSDPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['PSDPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'PSDPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['PSDPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'PSDPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSDPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'PSDPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSDPBorderRadius'],
					'after' => 'px</div>'
				),
				array (
					'label' => 'Steps',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => 'mwd-mini-title',
					'after' => ''
				),
				array (
					'label' => 'Alignment',
					'name' => 'PSAPAlign',
					'type' => 'select',
					'options' => $aligns ,
					'class' => '',
					'value' => $param_values['PSAPAlign'],
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'PSAPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PSAPWidth'],
					'after' => 'px</div>'
				),
				array (
					'label' => 'Percentage',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => 'mwd-mini-title',
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'PPAPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PPAPWidth'],
					'placeholder' => 'e.g. 100% or 500px',
					'after' => 'px/%</div>'
				)
			),
			'buttons' => array(
				array (
					'label' => 'Global Parameters',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Font Size',
					'name' => 'BPFontSize',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['BPFontSize'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'BPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => $param_values['BPFontWeight'],
					'after' => '</div>'
				),
				array (
					'label' => 'Next Button Parameters',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'NBPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['NBPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'NBPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['NBPWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'NBPHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['NBPHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Line Height',
					'name' => 'NBPLineHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['NBPLineHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Color',
					'name' => 'NBPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['NBPColor'],
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'NBPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['NBPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'NBPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['NBPMargin'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'NBPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'NBPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['NBPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'NBPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['NBPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'NBPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['NBPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'NBPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['NBPBorderRadius'],
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'NBPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['NBPBoxShadow'],
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => ''
				),
				array (
					'label' => 'Hover Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'NBHPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['NBHPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'NBHPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['NBHPColor'],
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'NBHPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'NBHPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['NBHPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'NBHPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['NBHPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'NBHPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['NBHPBorderWidth'],
					'after' => 'px</div>'
				),
				array (
					'label' => 'Previous Button Parameters',
					'type' => 'panel',
					'class' => 'col-md-6',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'PBPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['PBPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Width',
					'name' => 'PBPWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PBPWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Height',
					'name' => 'PBPHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PBPHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Line Height',
					'name' => 'PBPLineHeight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PBPLineHeight'],
					'after' => 'px'
				),
				array (
					'label' => 'Color',
					'name' => 'PBPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['PBPColor'],
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'PBPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PBPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'PBPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PBPMargin'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'PBPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'PBPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['PBPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'PBPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['PBPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'PBPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PBPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'PBPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PBPBorderRadius'],
					'after' => 'px'
				),
				array (
					'label' => 'Box Shadow',
					'name' => 'PBPBoxShadow',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PBPBoxShadow'],
					'placeholder' => 'e.g. 5px 5px 2px #888888',
					'after' => ''
				),
				array (
					'label' => 'Hover Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'PBHPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['PBHPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'PBHPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['PBHPColor'],
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'PBHPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'PBHPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['PBHPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'PBHPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['PBHPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'PBHPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['PBHPBorderWidth'],
					'after' => 'px</div>'
				)
			),
			'close_button' => array(
				array (
					'label' => '',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => '',
					'after' => ''
				),
				array (
					'label' => 'Position',
					'name' => 'CBPPosition',
					'type' => 'select',
					'options' => $position_types,
					'class' => '',
					'value' => $param_values['CBPPosition'],
					'after' => ''
				),
				array (
					'label' => 'Top',
					'name' => 'CBPTop',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['CBPTop'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Right',
					'name' => 'CBPRight',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['CBPRight'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Bottom',
					'name' => 'CBPBottom',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['CBPBottom'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Left',
					'name' => 'CBPLeft',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['CBPLeft'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Background Color',
					'name' => 'CBPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['CBPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Font Size',
					'name' => 'CBPFontSize',
					'type' => 'text',
					'class' => '13',
					'value' => $param_values['CBPFontSize'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'CBPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => $param_values['CBPFontWeight'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'CBPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['CBPColor'],
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'CBPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['CBPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'CBPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['CBPMargin'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'CBPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'CBPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['CBPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'CBPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['CBPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'CBPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['CBPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'CBPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['CBPBorderRadius'],
					'after' => 'px'
				),
				array (
					'label' => 'Hover Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'CBHPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['CBHPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'CBHPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['CBHPColor'],
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'CBHPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'CBHPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['CBHPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'CBHPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['CBHPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'CBHPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['CBHPBorderWidth'],
					'after' => 'px</div>'
				)
			),
			'minimize' => array(
				array (
					'label' => '',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => '',
					'after' => ''
				),
				array (
					'label' => 'Background Color',
					'name' => 'MBPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['MBPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Font Size',
					'name' => 'MBPFontSize',
					'type' => 'text',
					'class' => '13',
					'value' => $param_values['MBPFontSize'],
					'after' => 'px'
				),
				array (
					'label' => 'Font Weight',
					'name' => 'MBPFontWeight',
					'type' => 'select',
					'options' => $font_weights,
					'class' => '',
					'value' => $param_values['MBPFontWeight'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'MBPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['MBPColor'],
					'after' => ''
				),
				array (
					'label' => 'Text Align',
					'name' => 'MBPTextAlign',
					'type' => 'select',
					'options' => $aligns,
					'class' => '',
					'value' => $param_values['MBPTextAlign'],
					'after' => ''
				),
				array (
					'label' => 'Padding',
					'name' => 'MBPPadding',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MBPPadding'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Margin',
					'name' => 'MBPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MBPMargin'],
					'after' => 'px/%'
				),
				array (
					'label' => 'Border',
					'name' => 'MBPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'MBPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['MBPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'MBPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['MBPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'MBPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MBPBorderWidth'],
					'after' => 'px'
				),
				array (
					'label' => 'Border Radius',
					'name' => 'MBPBorderRadius',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MBPBorderRadius'],
					'after' => 'px'
				),
				array (
					'label' => 'Hover Parameters',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background Color',
					'name' => 'MBHPBGColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['MBHPBGColor'],
					'after' => ''
				),
				array (
					'label' => 'Color',
					'name' => 'MBHPColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['MBHPColor'],
					'after' => ''
				),
				array (
					'label' => 'Border',
					'name' => 'MBHPBorder',
					'type' => 'checkbox',
					'options' => $borders,
					'class' => '',
					'after' => ''
				),
				array (
					'label' => 'Border Color',
					'name' => 'MBHPBorderColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['MBHPBorderColor'],
					'after' => ''
				),
				array (
					'label' => 'Border Type',
					'name' => 'MBHPBorderType',
					'type' => 'select',
					'options' => $border_types,
					'class' => '',
					'value' => $param_values['MBHPBorderType'],
					'after' => ''
				),
				array (
					'label' => 'Border Width',
					'name' => 'MBHPBorderWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['MBHPBorderWidth'],
					'after' => 'px</div>'
				)
			),

			'other' => array(
				array (
					'label' => 'Deactive Text',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Color',
					'name' => 'OPDeInputColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['OPDeInputColor'],
					'after' => ''
				),
				array (
					'label' => 'Font Style',
					'name' => 'OPFontStyle',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['OPFontStyle'],
					'after' => ''
				),
				array (
					'label' => 'Required',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Color',
					'name' => 'OPRColor',
					'type' => 'text',
					'class' => 'color',
					'value' => $param_values['OPRColor'],
					'after' => ''
				),
				array (
					'label' => 'Date Picker',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background URL',
					'name' => 'OPDPIcon',
					'type' => 'text',
					'class' => '',
					'placeholder' => '',
					'value' => $param_values['OPDPIcon'],
					'after' => ''
				),
				array (
					'label' => 'Background Repeat',
					'name' => 'OPDPRepeat',
					'type' => 'select',
					'options' => $bg_repeats,
					'class' => '',
					'value' => $param_values['OPDPRepeat'],
					'after' => ''
				),
				array (
					'label' => 'Background Position',
					'name1' => 'OPDPPos1',
					'name2' => 'OPDPPos2',
					'type' => '2text',
					'class' => 'mwd-2text',
					'value1' => $param_values['OPDPPos1'],
					'value2' => $param_values['OPDPPos2'],
					'before1' => '',
					'before2' => '',
					'after' => '%/left..'
				),
				array (
					'label' => 'Margin',
					'name' => 'OPDPMargin',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['OPDPMargin'],
					'after' => 'px/%'
				),
				array (
					'label' => 'File Upload',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Background URL',
					'name' => 'OPFBgUrl',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['OPFBgUrl'],
					'after' => ''
				),
				array (
					'label' => 'Background Repeat',
					'name' => 'OPFBGRepeat',
					'type' => 'select',
					'options' => $bg_repeats,
					'class' => '',
					'value' => $param_values['OPFBGRepeat'],
					'after' => ''
				),
				array (
					'label' => 'Background Position',
					'name1' => 'OPFPos1',
					'name2' => 'OPFPos2',
					'type' => '2text',
					'class' => 'mwd-2text',
					'value1' => $param_values['OPFPos1'],
					'value2' => $param_values['OPFPos2'],
					'before1' => '',
					'before2' => '',
					'after' => '%/left..'
				),
				array (
					'label' => 'Grading',
					'type' => 'label',
					'class' => 'mwd-mini-title',
					'after' => '<br/>'
				),
				array (
					'label' => 'Text Width',
					'name' => 'OPGWidth',
					'type' => 'text',
					'class' => '',
					'value' => $param_values['OPGWidth'],
					'after' => 'px</div>'
				)
			),
			'custom_css' => array(
				array (
					'label' => '',
					'type' => 'panel',
					'class' => 'col-md-12',
					'label_class' => '',
					'after' => ''
				),
				array (
					'label' => 'Custom CSS',
					'name' => 'CUPCSS',
					'type' => 'textarea',
					'class' => '',
					'value' => $param_values['CUPCSS'],
					'after' => '</div>'
				),
			)
		);
		$active_tab = isset($_REQUEST["active_tab"]) && $_REQUEST["active_tab"] ? $_REQUEST["active_tab"] : 'global';
		$pagination = isset($_REQUEST["pagination"]) ? $_REQUEST["pagination"] : 'none';
		MWD_Library::mwd_upgrade_pro();
		?>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.min.js"></script>

		
		<div ng-app="ThemeParams">
			<div ng-controller="MWDTheme">
				<form id="mwd-themes-form" method="post" action="admin.php?page=themes_mwd" style="width:99%;" >
					<?php wp_nonce_field('nonce_mwd', 'nonce_mwd'); ?>
					<div class="mwd-page-header">
						<div class="mwd-logo">
						</div>
						<div class="mwd-page-title">
							<?php echo $page_title; ?>
						</div>
						<div class="mwd-page-actions">
							<?php if ($id) { ?>
								<button class="mwd-button save-as-copy-button medium" onclick="if (mwd_check_required('title', 'Title') || !submitbutton()) {return false;}; mwd_set_input_value('task', 'save_as_copy');">
									<span></span>
									Save as Copy
								</button>
							<?php } ?>
							<button class="mwd-button save-button small" onclick="if (mwd_check_required('title', 'Title') || !submitbutton()) {return false;}; mwd_set_input_value('task', 'save');">
								<span></span>
								Save
							</button>
							<button class="mwd-button apply-button small" onclick="if (mwd_check_required('title', 'Title') || !submitbutton()) {return false;}; mwd_set_input_value('task', 'apply');">
								<span></span>
								Apply
							</button>
							<button class="mwd-button cancel-button small" onclick="mwd_set_input_value('task', 'cancel');">
								<span></span>
								Cancel
							</button>
						</div>
						<div class="mwd-clear"></div>
					</div>

					<input type="hidden" id="task" name="task" value=""/>
					<input type="hidden" id="params" name="params" value=""/>
					<input type="hidden" id="current_id" name="current_id" value="<?php echo $row->id; ?>"/>
					<input type="hidden" id="default" name="default" value="<?php echo $row->default; ?>"/>
					<input type="hidden" name="active_tab" id="active_tab" value="<?php echo $active_tab; ?>" />

					<style>
					.mwd-form{
						background-color:{{GPBGColor}};
						font-family:{{GPFontFamily}};
						width:{{AGPWidth}}%;
						padding:{{AGPPadding}} !important;
						margin:{{AGPMargin}};
						border-radius:{{AGPBorderRadius}}px;
						box-shadow:{{AGPBoxShadow}};
						position: relative;
					}

					.mwd-form-header.alignLeft,
					.mwd-form-content.alignLeft{
						border-radius:{{AGPBorderRadius}}px;
					}

					.mwd-form.borderRight{
						border-right:{{AGPBorderWidth}}px {{AGPBorderType}} {{AGPBorderColor}};
					}

					.mwd-form.borderLeft{
						border-left:{{AGPBorderWidth}}px {{AGPBorderType}} {{AGPBorderColor}};
					}

					.mwd-form.borderTop{
						border-top:{{AGPBorderWidth}}px {{AGPBorderType}} {{AGPBorderColor}};
					}

					.mwd-form.borderBottom{
						border-bottom:{{AGPBorderWidth}}px {{AGPBorderType}} {{AGPBorderColor}};
					}

					.mwd-form-content{
						font-size:{{GPFontSize}}px;
						font-weight:{{GPFontWeight}};
						width:{{GPWidth}}%;
						color:{{GPColor}};
						padding:{{GPPadding}} !important;
						margin:{{GPMargin}};
						border-radius:{{GPBorderRadius}}px;
					}

					.mwd-form-content.isBG{
						background:url(<?php echo MWD_URL; ?>/{{GPBackground}}) {{GPBackgroundRepeat}} {{GPBGPosition1}} {{GPBGPosition2}};
						background-size: {{GPBGSize1}} {{GPPBGSize2}};
					}

					.mwd-form-content.borderRight{
						border-right:{{GPBorderWidth}}px {{GPBorderType}} {{GPBorderColor}};
					}

					.mwd-form-content.borderLeft{
						border-left:{{GPBorderWidth}}px {{GPBorderType}} {{GPBorderColor}};
					}

					.mwd-form-content.borderTop{
						border-top:{{GPBorderWidth}}px {{GPBorderType}} {{GPBorderColor}};
					}

					.mwd-form-content.borderBottom{
						border-bottom:{{GPBorderWidth}}px {{GPBorderType}} {{GPBorderColor}};
					}

					.mwd-form-content label{
						font-size:{{GPFontSize}}px !important;
					}

					.mwd-form-content .mwd-section{
						background-color:{{SEPBGColor}};
						padding:{{SEPPadding}};
						margin:{{SEPMargin}};
					}


					.mwd-form-content .mwd-column{
						padding:{{COPPadding}};
						margin:{{COPMargin}};
					}

					.mwd-form-content input[type="text"],
					.mwd-form-content select{
						font-size:{{IPFontSize}}px;
						font-weight:{{IPFontWeight}};
						height:{{IPHeight}}px;
						line-height:{{IPHeight}}px;
						background-color:{{IPBGColor}};
						color:{{IPColor}};
						padding:{{IPPadding}};
						margin:{{IPMargin}};
						border-radius:{{IPBorderRadius}}px;
						box-shadow:{{IPBoxShadow}};
						border:none;
					}

					.mwd-form-content input[type="text"].borderRight,
					.mwd-form-content select.borderRight{
						border-right:{{IPBorderWidth}}px {{IPBorderType}} {{IPBorderColor}} !important;
					}

					.mwd-form-content input[type="text"].borderLeft,
					.mwd-form-content select.borderLeft{
						border-left:{{IPBorderWidth}}px {{IPBorderType}} {{IPBorderColor}} !important;
					}

					.mwd-form-content input[type="text"].borderTop,
					.mwd-form-content select.borderTop{
						border-top:{{IPBorderWidth}}px {{IPBorderType}} {{IPBorderColor}} !important;
					}

					.mwd-form-content input[type="text"].borderBottom,
					.mwd-form-content select.borderBottom{
						border-bottom:{{IPBorderWidth}}px {{IPBorderType}} {{IPBorderColor}} !important;
					}

					.mwd-form-content select{
						appearance: {{SBPAppearance}};
						-moz-appearance: {{SBPAppearance}};
						-webkit-appearance: {{SBPAppearance}};
						background:{{IPBGColor}};
					}

					.mwd-form-content select.isBG{
						background:{{IPBGColor}} url(<?php echo MWD_URL; ?>/{{SBPBackground}}) {{SBPBGRepeat}} {{SBPBGPos1}} {{SBPBGPos2}};
						background-size: {{SBPBGSize1}} {{SBPBGSize2}};
					}

					.mwd-form-example label.mini_label{
						font-size:{{GPMLFontSize}}px !important;
						font-weight:{{GPMLFontWeight}};
						color:{{GPMLColor}};
						padding:{{GPMLPadding}};
						margin:{{GPMLMargin}};
						width: initial;
					}

					.mwd-button-reset {
						background-color:{{BPBGColor}};
						color:{{BPColor}};
						height:{{BPHeight}}px;
						width:{{BPWidth}}px;
						margin:{{BPMargin}};
						padding:{{BPPadding}};
						box-shadow:{{BPBoxShadow}};
						border-radius:{{BPBorderRadius}}px;
						outline: none;
						border: none !important;
					}

					.mwd-button-reset.borderRight {
						border-right:{{BPBorderWidth}}px {{BPBorderType}} {{BPBorderColor}} !important;
					}

					.mwd-button-reset.borderLeft {
						border-left:{{BPBorderWidth}}px {{BPBorderType}} {{BPBorderColor}} !important;
					}

					.mwd-button-reset.borderTop {
						border-top:{{BPBorderWidth}}px {{BPBorderType}} {{BPBorderColor}} !important;
					}

					.mwd-button-reset.borderBottom {
						border-bottom:{{BPBorderWidth}}px {{BPBorderType}} {{BPBorderColor}} !important;
					}

					.mwd-button-reset:hover {
						background-color:{{BHPBGColor}};
						color:{{BHPColor}};
						outline: none;
						border: none !important;
					}

					.mwd-button-reset.borderHoverRight:hover {
						border-right:{{BHPBorderWidth}}px {{BHPBorderType}} {{BHPBorderColor}} !important;
					}

					.mwd-button-reset.borderHoverLeft:hover {
						border-left:{{BHPBorderWidth}}px {{BHPBorderType}} {{BHPBorderColor}} !important;
					}

					.mwd-button-reset.borderHoverTop:hover {
						border-top:{{BHPBorderWidth}}px {{BHPBorderType}} {{BHPBorderColor}} !important;
					}

					.mwd-button-reset.borderHoverBottom:hover {
						border-bottom:{{BHPBorderWidth}}px {{BHPBorderType}} {{BHPBorderColor}} !important;
					}

					.mwd-form-content button,
					.mwd-wdform-page-button{
						font-size: {{BPFontSize}}px;
						font-weight: {{BPFontWeight}};
					}

					.mwd-previous-page .mwd-wdform-page-button{
						background-color:{{PBPBGColor}};
						color:{{PBPColor}};
						height:{{PBPHeight}}px;
						line-height:{{PBPLineHeight}}px;
						width:{{PBPWidth}}px;
						margin:{{PBPMargin}};
						padding:{{PBPPadding}};
						border-radius:{{PBPBorderRadius}}px;
						box-shadow:{{PBPBoxShadow}};
						outline: none;
					}

					.mwd-previous-page .mwd-wdform-page-button.borderRight {
						border-right:{{PBPBorderWidth}}px {{PBPBorderType}} {{PBPBorderColor}} !important;
					}

					.mwd-previous-page .mwd-wdform-page-button.borderLeft {
						border-left:{{PBPBorderWidth}}px {{PBPBorderType}} {{PBPBorderColor}} !important;
					}

					.mwd-previous-page .mwd-wdform-page-button.borderTop {
						border-top:{{PBPBorderWidth}}px {{PBPBorderType}} {{PBPBorderColor}} !important;
					}

					.mwd-previous-page .mwd-wdform-page-button.borderBottom {
						border-bottom:{{PBPBorderWidth}}px {{PBPBorderType}} {{PBPBorderColor}} !important;
					}

					.mwd-previous-page .mwd-wdform-page-button:hover {
						background-color:{{PBHPBGColor}};
						color:{{PBHPColor}};
					}

					.mwd-previous-page .mwd-wdform-page-button.borderHoverRight:hover {
						border-right:{{PBHPBorderWidth}}px {{PBHPBorderType}} {{PBHPBorderColor}} !important;
					}

					.mwd-previous-page .mwd-wdform-page-button.borderHoverLeft:hover {
						border-left:{{PBHPBorderWidth}}px {{PBHPBorderType}} {{PBHPBorderColor}} !important;
					}

					.mwd-previous-page .mwd-wdform-page-button.borderHoverTop:hover {
						border-top:{{PBHPBorderWidth}}px {{PBHPBorderType}} {{PBHPBorderColor}} !important;
					}

					.mwd-previous-page .mwd-wdform-page-button.borderHoverBottom:hover {
						border-bottom:{{PBHPBorderWidth}}px {{PBHPBorderType}} {{PBHPBorderColor}} !important;
					}


					.mwd-next-page .mwd-wdform-page-button{
						background-color:{{NBPBGColor}} !important;
						color:{{NBPColor}} !important;
						height:{{NBPHeight}}px !important;
						line-height:{{NBPLineHeight}}px !important;
						width:{{NBPWidth}}px !important;
						margin:{{NBPMargin}} !important;
						padding:{{NBPPadding}} !important;
						border-radius:{{NBPBorderRadius}}px;
						box-shadow:{{NBPBoxShadow}} !important;
					}

					.mwd-next-page .mwd-wdform-page-button.borderRight {
						border-right:{{NBPBorderWidth}}px {{NBPBorderType}} {{NBPBorderColor}} !important;
					}

					.mwd-next-page .mwd-wdform-page-button.borderLeft {
						border-left:{{NBPBorderWidth}}px {{NBPBorderType}} {{NBPBorderColor}} !important;
					}

					.mwd-next-page .mwd-wdform-page-button.borderTop {
						border-top:{{NBPBorderWidth}}px {{NBPBorderType}} {{NBPBorderColor}} !important;
					}

					.mwd-next-page .mwd-wdform-page-button.borderBottom {
						border-bottom:{{NBPBorderWidth}}px {{NBPBorderType}} {{NBPBorderColor}} !important;
					}

					.mwd-next-page .mwd-wdform-page-button:hover {
						background-color:{{NBHPBGColor}} !important;
						color:{{NBHPColor}} !important;
						outline: none;
					}

					.mwd-next-page .mwd-wdform-page-button.borderHoverRight:hover {
						border-right:{{NBHPBorderWidth}}px {{NBHPBorderType}} {{NBHPBorderColor}} !important;
					}

					.mwd-next-page .mwd-wdform-page-button.borderHoverLeft:hover {
						border-left:{{NBHPBorderWidth}}px {{NBHPBorderType}} {{NBHPBorderColor}} !important;
					}

					.mwd-next-page .mwd-wdform-page-button.borderHoverTop:hover {
						border-top:{{NBHPBorderWidth}}px {{NBHPBorderType}} {{NBHPBorderColor}} !important;
					}

					.mwd-next-page .mwd-wdform-page-button.borderHoverBottom:hover {
						border-bottom:{{NBHPBorderWidth}}px {{NBHPBorderType}} {{NBHPBorderColor}} !important;
					}

					.mwd-button-subscribe {
						background-color:{{SPBGColor}} !important;
						font-size:{{SPFontSize}}px !important;
						font-weight:{{SPFontWeight}} !important;
						color:{{SPColor}} !important;
						height:{{SPHeight}}px !important;
						width:{{SPWidth}}px !important;
						margin:{{SPMargin}} !important;
						padding:{{SPPadding}} !important;
						box-shadow:{{SPBoxShadow}} !important;
						border-radius: {{SPBorderRadius}}px;
						outline: none;
						border: none !important;
					}

					.mwd-button-subscribe.borderRight {
						border-right:{{SPBorderWidth}}px {{SPBorderType}} {{SPBorderColor}} !important;
					}

					.mwd-button-subscribe.borderLeft {
						border-left:{{SPBorderWidth}}px {{SPBorderType}} {{SPBorderColor}} !important;
					}

					.mwd-button-subscribe.borderTop {
						border-top:{{SPBorderWidth}}px {{SPBorderType}} {{SPBorderColor}} !important;
					}

					.mwd-button-subscribe.borderBottom {
						border-bottom:{{SPBorderWidth}}px {{SPBorderType}} {{SPBorderColor}} !important;
					}

					.mwd-button-subscribe:hover {
						background-color:{{SHPBGColor}} !important;
						color:{{SHPColor}} !important;
						outline: none;
						border: none !important;
					}

					.mwd-button-subscribe.borderHoverRight:hover {
						border-right:{{SHPBorderWidth}}px {{SHPBorderType}} {{SHPBorderColor}} !important;
					}

					.mwd-button-subscribe.borderHoverLeft:hover {
						border-left:{{SHPBorderWidth}}px {{SHPBorderType}} {{SHPBorderColor}} !important;
					}

					.mwd-button-subscribe.borderHoverTop:hover {
						border-top:{{SHPBorderWidth}}px {{SHPBorderType}} {{SHPBorderColor}} !important;
					}

					.mwd-button-subscribe.borderHoverBottom:hover {
						border-bottom:{{SHPBorderWidth}}px {{SHPBorderType}} {{SHPBorderColor}} !important;
					}

					.radio-div label span {
						height:{{SCPHeight}}px;
						width:{{SCPWidth}}px;
						background-color:{{SCPBGColor}};
						margin:{{SCPMargin}};
						box-shadow:{{SCPBoxShadow}};
						border-radius: {{SCPBorderRadius}}px;
						border: none;
						display: inline-block;
						vertical-align: middle;
						box-sizing: content-box !important;
					}

					.radio-div input[type='radio']:checked + label span:after {
						content: '';
						width:{{SCCPWidth}}px;
						height:{{SCCPHeight}}px;
						background:{{SCCPBGColor}};
						border-radius:{{SCCPBorderRadius}}px;
						margin:{{SCCPMargin}}px;
						display: block;
					}

					.radio-div label span.borderRight {
						border-right:{{SCPBorderWidth}}px {{SCPBorderType}} {{SCPBorderColor}} !important;
					}

					.radio-div label span.borderLeft {
						border-left:{{SCPBorderWidth}}px {{SCPBorderType}} {{SCPBorderColor}} !important;
					}

					.radio-div label span.borderTop {
						border-top:{{SCPBorderWidth}}px {{SCPBorderType}} {{SCPBorderColor}} !important;
					}

					.radio-div label span.borderBottom {
						border-bottom:{{SCPBorderWidth}}px {{SCPBorderType}} {{SCPBorderColor}} !important;
					}

					.checkbox-div label span {
						height:{{MCPHeight}}px;
						width:{{MCPWidth}}px;
						background-color:{{MCPBGColor}};
						margin:{{MCPMargin}};
						box-shadow:{{MCPBoxShadow}};
						border-radius: {{MCPBorderRadius}}px;
						border: none;
						display: inline-block;
						vertical-align: middle;
						box-sizing: content-box !important;
					}

					.checkbox-div input[type='checkbox']:checked + label span:after {
						content: '';
						width:{{MCCPWidth}}px;
						height:{{MCCPHeight}}px;
						border-radius:{{MCCPBorderRadius}}px;
						margin:{{MCCPMargin}}px;
						display: block;
						background:{{MCCPBGColor}} ;
					}

					.checkbox-div.isBG input[type='checkbox']:checked + label span:after{
						background:{{MCCPBGColor}} url(<?php echo MWD_URL; ?>/{{MCCPBackground}}) {{MCCPBGRepeat}} {{MCCPBGPos1}} {{MCCPBGPos2}};
					}

					.checkbox-div label span.borderRight {
						border-right:{{MCPBorderWidth}}px {{MCPBorderType}} {{MCPBorderColor}} !important;
					}

					.checkbox-div label span.borderLeft {
						border-left:{{MCPBorderWidth}}px {{MCPBorderType}} {{MCPBorderColor}} !important;
					}

					.checkbox-div label span.borderTop {
						border-top:{{MCPBorderWidth}}px {{MCPBorderType}} {{MCPBorderColor}} !important;
					}

					.checkbox-div label span.borderBottom {
						border-bottom:{{MCPBorderWidth}}px {{MCPBorderType}} {{MCPBorderColor}} !important;
					}

					.mwd-form-pagination {
						width:{{AGPWidth}}%;
						margin:{{AGPMargin}};
					}

					.mwd-footer{
						font-size:{{GPFontSize}}px;
						font-weight:{{GPFontWeight}};
						width:{{FPWidth}}%;
						padding:{{FPPadding}};
						margin:{{FPMargin}};
						color:{{GPColor}};
						clear: both;
					}

					.mwd-pages-steps{
						text-align: {{PSAPAlign}};
					}

					.active-step{
						background-color: {{PSAPBGColor}};
						font-size: {{PSAPFontSize}}px;
						font-weight: {{PSAPFontWeight}};
						color: {{PSAPColor}};
						width: {{PSAPWidth}}px;
						height: {{PSAPHeight}}px;
						line-height: {{PSAPLineHeight}}px;
						margin: {{PSAPMargin}};
						padding: {{PSAPPadding}};
						border-radius: {{PSAPBorderRadius}}px;

						text-align: center;
						display: inline-block;
						cursor: pointer;
					}

					.active-step.borderRight {
						border-right:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.active-step.borderLeft {
						border-left:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.active-step.borderTop {
						border-top:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.active-step.borderBottom {
						border-bottom:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.deactive-step{
						background-color: {{PSDPBGColor}};
						font-size: {{PSDPFontSize}}px;
						font-weight: {{PSDPFontWeight}};
						color: {{PSDPColor}};
						width: {{PSAPWidth}}px;
						height: {{PSDPHeight}}px;
						line-height: {{PSDPLineHeight}}px;
						margin: {{PSDPMargin}};
						padding: {{PSDPPadding}};
						border-radius: {{PSDPBorderRadius}}px;

						text-align: center;
						display: inline-block;
						cursor: pointer;
					}

					.deactive-step.borderRight {
						border-right:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.deactive-step.borderLeft {
						border-left:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.deactive-step.borderTop {
						border-top:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.deactive-step.borderBottom {
						border-bottom:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.active-percentage {
						background-color: {{PSAPBGColor}};
						font-size: {{PSAPFontSize}}px;
						font-weight: {{PSAPFontWeight}};
						color: {{PSAPColor}};
						width: {{PSAPWidth}}px;
						height: {{PSAPHeight}}px;
						line-height: {{PSAPLineHeight}}px;
						margin: {{PSAPMargin}};
						padding: {{PSAPPadding}};
						border-radius: {{PSAPBorderRadius}}px;

						display: inline-block;
					}

					.active-percentage.borderRight {
						border-right:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.active-percentage.borderLeft {
						border-left:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.active-percentage.borderTop {
						border-top:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.active-percentage.borderBottom {
						border-bottom:{{PSAPBorderWidth}}px {{PSAPBorderType}} {{PSAPBorderColor}} !important;
					}

					.deactive-percentage {
						background-color: {{PSDPBGColor}};
						font-size: {{PSDPFontSize}}px;
						font-weight: {{PSDPFontWeight}};
						color: {{PSDPColor}};
						width: {{PPAPWidth}};
						height: {{PSDPHeight}}px;
						line-height: {{PSDPLineHeight}}px;
						margin: {{PSDPMargin}};
						padding: {{PSDPPadding}};
						border-radius: {{PSDPBorderRadius}}px;

						display: inline-block;
					}

					.deactive-percentage.borderRight {
						border-right:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.deactive-percentage.borderLeft {
						border-left:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.deactive-percentage.borderTop {
						border-top:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.deactive-percentage.borderBottom {
						border-bottom:{{PSDPBorderWidth}}px {{PSDPBorderType}} {{PSDPBorderColor}} !important;
					}

					.mwd-close-icon {
						color: {{CBPColor}};
						font-size: {{CBPFontSize}}px;
						font-weight: {{CBPFontWeight}};
						text-align: center;
					}

					.mwd-close {
						position: {{CBPPosition}};
						top: {{CBPTop}};
						right: {{CBPRight}};
						bottom: {{CBPBottom}};
						left: {{CBPLeft}};
						background-color: {{CBPBGColor}};
						padding: {{CBPPadding}};
						margin: {{CBPMargin}};
						border-radius: {{CBPBorderRadius}}px;
						border: none;
						cursor: pointer;
					}

					.mwd-close.borderRight{
						border-right:{{CBPBorderWidth}}px {{CBPBorderType}} {{CBPBorderColor}} !important;
					}

					.mwd-close.borderLeft{
						border-left:{{CBPBorderWidth}}px {{CBPBorderType}} {{CBPBorderColor}} !important;
					}

					.mwd-close.borderTop{
						border-top:{{CBPBorderWidth}}px {{CBPBorderType}} {{CBPBorderColor}} !important;
					}

					.mwd-close.borderBottom {
						border-bottom:{{CBPBorderWidth}}px {{CBPBorderType}} {{CBPBorderColor}} !important;
					}

					.mwd-close:hover{
						background-color:{{CBHPBGColor}};
						color:{{CBHPColor}};
						outline: none;
						border: none !important;
					}

					.mwd-close.borderHoverRight:hover {
						border-right:{{CBHPBorderWidth}}px {{CBHPBorderType}} {{CBHPBorderColor}} !important;
					}

					.mwd-close.borderHoverLeft:hover {
						border-left:{{CBHPBorderWidth}}px {{CBHPBorderType}} {{CBHPBorderColor}} !important;
					}

					.mwd-close.borderHoverTop:hover{
						border-top:{{CBHPBorderWidth}}px {{CBHPBorderType}} {{CBHPBorderColor}} !important;
					}

					.mwd-close.borderHoverBottom:hover {
						border-bottom:{{CBHPBorderWidth}}px {{CBHPBorderType}} {{CBHPBorderColor}} !important;
					}

					.mwd-form-header {
						background-color:{{HPBGColor}};
						width:{{HPWidth}}%;
						padding:{{HPPadding}} !important;
						margin:{{HPMargin}};
						border-radius:{{HPBorderRadius}}px;
					}

					.mwd-form-header .htitle {
						font-size:{{HTPFontSize}}px;
						color:{{HTPColor}};
						text-align:{{HPTextAlign}};
						padding: 10px 0;
						line-height:{{HTPFontSize}}px;
						font-weight:{{HTPWeight}};
						
					}

					.mwd-form-header .himage img {
						width:{{HIPWidth}}px;
						height:{{HIPHeight}}px;
					}

					.mwd-form-header .himage.imageTop,
					.mwd-form-header .himage.imageBottom{
						text-align:{{HPTextAlign}};
					}


					.mwd-form-header .hdescription {
						font-size:{{HDPFontSize}}px;
						color:{{HDPColor}};
						text-align:{{HPTextAlign}};
						padding: 5px 0;
					}

					.mwd-form-header.borderRight{
						border-right:{{HPBorderWidth}}px {{HPBorderType}} {{HPBorderColor}};
					}

					.mwd-form-header.borderLeft{
						border-left:{{HPBorderWidth}}px {{HPBorderType}} {{HPBorderColor}};
					}

					.mwd-form-header.borderTop{
						border-top:{{HPBorderWidth}}px {{HPBorderType}} {{HPBorderColor}};
					}

					.mwd-form-header.borderBottom{
						border-bottom:{{HPBorderWidth}}px {{HPBorderType}} {{HPBorderColor}};
					}

					.mwd-form-header.alignLeft,
					.mwd-form-content.alignLeft {
						display: table-cell;
						vertical-align:middle;
					}

					.wdform-required {
						color: {{OPRColor}};
					}
					
					.mwd-calendar-button {
						position: relative; 
					}

					.mwd-calendar-button span{
						position: absolute;
						padding: 10px;
						pointer-events: none;
						right: 3px;
						top: 0px;
						
						width: 20px;
						height: 20px;
						margin: {{OPDPMargin}};
						background: url(<?php echo MWD_URL; ?>/{{OPDPIcon}}) {{OPDPRepeat}} {{OPDPPos1}} {{OPDPPos2}};
					}

					.subscribe-reset {
						float: {{SPAlign}};
						margin-right:-15px;
					}
					</style>

					<div class="mwd-themes mwd-mailchimp container-fluid">
						<div class="row">
							<div class="col-md-6 col-sm-5">
								<div class="mwd-sidebar">
									<div class="mwd-row">
										<label>Theme title: </label>
										<input type="text" id="title" name="title" value="<?php echo $row->title; ?>"/>
									</div>
									<br />
								</div>
								<br />
								<div class="mwd-themes-tabs col-md-12">
									<ul>
										<?php
										foreach($tabs as $tkey => $tab) {
											$active_class = $active_tab == $tkey ? "mwd-theme-active-tab" : "";
											echo '<li><a id="'.$tkey.'" href="#" class="'.$active_class.'" >'.$tab.'</a></li>';
										}
										?>
									</ul>
									<div class="mwd-clear"></div>
									<div class="mwd-themes-tabs-container">
										<?php
										$k = 0;
										foreach($all_params as $pkey => $params) {
											$show_hide_class = $active_tab == $pkey ? '' : 'mwd-hide';
											echo '<div id="'.$pkey.'-content" class="mwd-themes-container '.$show_hide_class.'">';
												foreach($params as $param){
													if($param["type"] == 'panel') {
														echo '<div class="'.$param["class"].'">';
													}
													if($param["type"] != 'panel' || ($param["type"] == 'panel' && $param["label"]) )
														echo '<div class="mwd-row">';
													if($param["type"] == 'panel' && $param["label"]) {
														echo '<label class="'.$param["label_class"].'" >'.$param["label"].'</label>'.$param["after"];
													} else {
														if($param["type"] == 'text') {
															echo '<label>'.$param["label"].'</label>
																<input type="'.$param["type"].'" name="'.$param["name"].'" class="'.$param["class"].'" ng-model="'.$param["name"].'" ng-init="'.$param["name"].'=\''.$param["value"].'\'" value="'.$param["value"].'" placeholder="'.(isset($param["placeholder"]) ? $param["placeholder"] : "").'" />'.$param["after"];
														}
														else {
															if($param["type"] == '2text') {
																echo '<label>'.$param["label"].'</label>
																<div class="'.$param["class"].'" style="display:inline-block; vertical-align: middle;">
																	<div style="display:table-row;">
																		<span style="display:table-cell;">'.$param["before1"].'</span><input type="text" name="'.$param["name1"].'" ng-model="'.$param["name1"].'" ng-init="'.$param["name1"].'=\''.$param["value1"].'\'" value="'.$param["value1"].'" placeholder="'.(isset($param["placeholder"]) ? $param["placeholder"] : "").'" style="display:table-cell; "/>'.$param["after"].'
																	</div>
																	<div style="display:table-row;">
																		<span style="display:table-cell;">'.$param["before2"].'</span><input type="text" name="'.$param["name2"].'" class="'.$param["class"].'" ng-model="'.$param["name2"].'" ng-init="'.$param["name2"].'=\''.$param["value2"].'\'" value="'.$param["value2"].'" placeholder="'.(isset($param["placeholder"]) ? $param["placeholder"] : "").'" style="display:table-cell; "/>'.$param["after"].'
																	</div>
																</div>
																';

															}
															else {
																if($param["type"] == 'select') {
																	echo '<label>'.$param["label"].'</label>
																		<select name="'.$param["name"].'" ng-model="'.$param["name"].'" ng-init="'.$param["name"].'=\''.$param["value"].'\'">';
																		foreach($param["options"] as $option_key => $option) {
																			echo '<option value="'.$option_key.'">'.$option.'</option>';
																	}
																	echo '</select>'.$param["after"];
																} else {
																	if($param["type"] == 'label') {
																		echo '<label class="'.$param["class"].'" >'.$param["label"].'</label>'.$param["after"];
																	} else {
																		if($param["type"] == 'checkbox') {
																			echo '<label>'.$param["label"].'</label>
																				<div class="mwd-btn-group">';
																			foreach($param["options"] as $op_key => $option){
																				$init = isset($param_values[$param["name"].ucfirst($op_key)]) ? 'true' : 'false';
																				echo '<div class="mwd-ch-button">
																						<input type="checkbox" id="'.$param["name"].ucfirst($op_key).'" name="'.$param["name"].ucfirst($op_key).'" value="'.$op_key.'" ng-model="'.$param["name"].ucfirst($op_key).'" ng-checked="'.$param["name"].ucfirst($op_key).'" ng-init="'.$param["name"].ucfirst($op_key).'='.$init.'"><label for="'.$param["name"].ucfirst($op_key).'">'.$option.'</label>
																					</div>';
																			}
																			echo '</div>';

																		} else{
																			if($param["type"] == 'hidden') {
																				echo '<input type="'.$param["type"].'" />'.$param["after"];
																			} else {
																				if($param["type"] == 'textarea') {
																					echo '<label>'.$param["label"].'</label>
																						<textarea name="'.$param["name"].'" rows="5"  columns="10" style="vertical-align:middle;">'.$param["value"].'</textarea>';
																				}
																			}

																		}
																	}
																}
															}
														}
													}
													if($param["type"] != 'panel' || ($param["type"] == 'panel' && $param["label"]) )
														echo '</div>';
												}
											echo '</div>';
										} ?>
									</div>
									</div>
								</div>
							</div>
							<div class="mwd-preview-form col-md-6 col-sm-7" style="display:none;">
								<div class="form-example-preview mwd-sidebar col-md-12">
									<p>Preview</p>
									<div class="mwd-row">
										<label>Pagination Type: </label>
										<div class="pagination-type" ng-init="pagination='<?php echo $pagination; ?>'">
											<input type="radio" id="step" name="pagination-type" value="step" ng-model="pagination"/>
											<label for="step">Step</label>
											<input type="radio" id="percentage" name="pagination-type" value="percentage" ng-model="pagination" />
											<label for="percentage">Percentage</label>
											<input type="radio" id="none" name="pagination-type" value="none" ng-model="pagination" />
											<label for="none">None</label>
										</div>
									</div>
									</div>
								<div class="mwd-clear"></div>
								<br />
								<div class="mwd-content">
									<div class="mwd-form-example form-embedded">
										<div class="mwd-form-pagination">
											<div class="mwd-pages-steps" ng-show="pagination == 'step'">
												<span class="active-step" ng-class="{borderRight : PSAPBorderRight, borderLeft : PSAPBorderLeft, borderBottom : PSAPBorderBottom, borderTop : PSAPBorderTop}">1(active)</span>
												<span class="deactive-step" ng-class="{borderRight : PSDPBorderRight, borderLeft : PSDPBorderLeft, borderBottom : PSDPBorderBottom, borderTop : PSDPBorderTop}">2</span>
											</div>
											<div class="mwd-pages-percentage" ng-show="pagination == 'percentage'">
												<div class="deactive-percentage" ng-class="{borderRight : PSDPBorderRight, borderLeft : PSDPBorderLeft, borderBottom : PSDPBorderBottom, borderTop : PSDPBorderTop}">
													<div class="active-percentage" ng-class="{borderRight : PSAPBorderRight, borderLeft : PSAPBorderLeft, borderBottom : PSAPBorderBottom, borderTop : PSAPBorderTop}" style="width: 50%;">
														<b class="wdform_percentage_text">50%</b>
													</div>
													<div class="wdform_percentage_arrow">
													</div>
												</div>
											</div>
											<div>
											</div>
										</div>

                    <div class="mwd-form" ng-class="{borderRight : AGPBorderRight, borderLeft : AGPBorderLeft, borderBottom : AGPBorderBottom, borderTop : AGPBorderTop}">
											<div ng-show="HPAlign != 'bottom' && HPAlign != 'right'" ng-class="{borderRight : HPBorderRight, borderLeft : HPBorderLeft, borderBottom : HPBorderBottom, borderTop : HPBorderTop, alignLeft : HPAlign == 'left'}" class="mwd-form-header">
												<div ng-show="HIPAlign != 'bottom' && HIPAlign != 'right'" ng-class="{imageRight : HIPAlign == 'right', imageLeft :  HIPAlign == 'left', imageBottom : HIPAlign == 'bottom', imageTop :  HIPAlign == 'top'}" class="himage">
													<img src="<?php echo MWD_URL; ?>/images/preview_header.png" />
												</div>
												<div ng-class="{imageRight : HIPAlign == 'right', imageLeft :  HIPAlign == 'left', imageBottom : HIPAlign == 'bottom', imageTop :  HIPAlign == 'top'}" class="htext">
													<div class="htitle">Subscribe Our Newsletter </div>
													<div class="hdescription">Join our mailing list to receive the latest news from our team.</div>
												</div>
												<div ng-show="HIPAlign == 'bottom' || HIPAlign == 'right'" ng-class="{imageRight : HIPAlign == 'right', imageLeft :  HIPAlign == 'left', imageBottom : HIPAlign == 'bottom', imageTop :  HIPAlign == 'top'}" class="himage">
													<img src="<?php echo MWD_URL; ?>/images/preview_header.png" />
												</div>
											</div>

											<div class="mwd-form-content" ng-class="{isBG : GPBackground != '', borderRight : GPBorderRight, borderLeft : GPBorderLeft, borderBottom : GPBorderBottom, borderTop : GPBorderTop, alignLeft : HPAlign == 'left' || HPAlign == 'right'}">
												<div class="container-fluid">
													<div class="embedded-form">
														<div class="mwd-section mwd-{{GPAlign}}">
															<div class="mwd-column">
																<div class="mwd-row">
																	<div type="type_submitter_mail" class="wdform-field">
																		<div class="wdform-label-section" style="float: left; width: 90px;"><span class="wdform-label">E-mail:</span><span class="wdform-required">*</span>
																		</div>
																		<div class="wdform-element-section" style="width: 150px;">
																			<input type="text" value="example@example.com" style="width: 100%;" ng-class="{borderRight : IPBorderRight, borderLeft : IPBorderLeft, borderBottom : IPBorderBottom, borderTop : IPBorderTop}" />
																		</div>
																	</div>
																</div>
																<div class="mwd-row">
																	<div type="type_country" class="wdform-field">
																		<div class="wdform-label-section" style="float: left; width: 90px;">
																			<span class="wdform-label">Country:</span>
																		</div>
																		<div class="wdform-element-section wdform_select" style=" width: 150px;">
																			<select style="width: 100%;" ng-class="{isBG : SBPBackground != '', borderRight : IPBorderRight, borderLeft : IPBorderLeft, borderBottom : IPBorderBottom, borderTop : IPBorderTop}">
																				<option value="Armenia">Armenia</option>
																			</select>
																		</div>
																	</div>
																</div>
																
																<div class="mwd-row">
																	<div type="type_radio" class="wdform-field">
																		<div class="wdform-label-section" style="float: left; width: 90px;">
																			<span class="wdform-label">Radio:</span>
																		</div>
																		<div class="wdform-element-section " style="display:table;">
																			<div style="display: table-row; vertical-align:top">
																				<div style="display: table-cell;">
																					<div class="radio-div check-rad">
																						<input type="radio" id="em-rad-op-1" value="option 1">
																						<label for="em-rad-op-1" class="mini_label">
																							<span ng-class="{borderRight : SCPBorderRight, borderLeft : SCPBorderLeft, borderBottom : SCPBorderBottom, borderTop : SCPBorderTop}"></span>option 1
																						</label>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="mwd-row">
																	<div type="type_checkbox" class="wdform-field">
																		<div class="wdform-label-section" style="float: left; width: 90px;">
																			<span class="wdform-label">Checkbox:</span>
																		</div>
																		<div class="wdform-element-section" style="display: table;">
																			<div style="display: table-row; vertical-align:top">
																				<div style="display: table-cell;">
																					<div class="checkbox-div forlabs" ng-class="{isBG : MCCPBackground != ''}">
																						<input type="checkbox" id="em-ch-op-1" value="option 1">
																						<label for="em-ch-op-1" class="mini_label"><span ng-class="{borderRight : MCPBorderRight, borderLeft : MCPBorderLeft, borderBottom : MCPBorderBottom, borderTop : MCPBorderTop}"></span>option 1</label>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="mwd-row">
																	<div type="type_date" class="wdform-field">
																		<div class="wdform-label-section" style="float: left; width: 90px;">
																			<span class="wdform-label">Date:</span>
																		</div>
																		<div class="wdform-element-section mwd-calendar-button" style="width: 150px;">
																			<input type="text" value="" style="width: 100%;" ng-class="{borderRight : IPBorderRight, borderLeft : IPBorderLeft, borderBottom : IPBorderBottom, borderTop : IPBorderTop}" />
																			<span></span>
																		</div>
																	</div>
																</div>
																<div class="mwd-row">
																	<div type="type_submit_reset" class="wdform-field subscribe-reset">
																		<div class="wdform-label-section" style="display: table-cell;"></div>
																		<div class="wdform-element-section" style="display: table-cell;">
																			<button type="button" class="mwd-button-subscribe" ng-class="{borderRight : SPBorderRight, borderLeft : SPBorderLeft, borderBottom : SPBorderBottom, borderTop : SPBorderTop, borderHoverRight : SHPBorderRight, borderHoverLeft : SHPBorderLeft, borderHoverBottom : SHPBorderBottom, borderHoverTop : SHPBorderTop}" >Subscribe</button>
																			<button type="button" class="mwd-button-reset" ng-class="{borderRight : BPBorderRight, borderLeft : BPBorderLeft, borderBottom : BPBorderBottom, borderTop : BPBorderTop, borderHoverRight : BHPBorderRight, borderHoverLeft : BHPBorderLeft, borderHoverBottom : BHPBorderBottom, borderHoverTop : BHPBorderTop}">Reset</button>
																		</div>
																	</div>
																</div>
																<div class="mwd-clear"></div>
															</div>
															
														</div>
														<div class="mwd-close-icon" ng-class="{borderRight : CBPBorderRight, borderLeft : CBPBorderLeft, borderBottom : CBPBorderBottom, borderTop : CBPBorderTop, borderHoverRight : CBHPBorderRight, borderHoverLeft : CBHPBorderLeft, borderHoverBottom : CBHPBorderBottom, borderHoverTop : CBHPBorderTop}">
															<span class="mwd-close fa fa-close" ng-class="{borderRight : CBPBorderRight, borderLeft : CBPBorderLeft, borderBottom : CBPBorderBottom, borderTop : CBPBorderTop, borderHoverRight : CBHPBorderRight, borderHoverLeft : CBHPBorderLeft, borderHoverBottom : CBHPBorderBottom, borderHoverTop : CBHPBorderTop}"></span>
														</div>
														<div class="mwd-footer" ng-show="pagination != 'none'">
															<div style="width: 100%;">
																<div style="width: 100%; display: table;">
																	<div style="display: table-row-group;">
																		<div  style="display: table-row;">
																			<div  class="mwd-previous-page" style="display: table-cell; width: 45%;">
																				<div class="mwd-wdform-page-button" ng-class="{borderRight : PBPBorderRight, borderLeft : PBPBorderLeft, borderBottom : PBPBorderBottom, borderTop : PBPBorderTop,  borderHoverRight : PBHPBorderRight, borderHoverLeft : PBHPBorderLeft, borderHoverBottom : PBHPBorderBottom, borderHoverTop : PBHPBorderTop}"><span class="fa fa-angle-double-left"></span> Previous</div>
																			</div>
																			<div class="page-numbers text-center" style="display: table-cell;">
																				<span>2/3</span>
																			</div>
																			<div class="mwd-next-page" style="display: table-cell; width: 45%; text-align: right;">
																				<div class="mwd-wdform-page-button" ng-class="{borderRight : NBPBorderRight, borderLeft : NBPBorderLeft, borderBottom : NBPBorderBottom, borderTop : NBPBorderTop, borderHoverRight : NBHPBorderRight, borderHoverLeft : NBHPBorderLeft, borderHoverBottom : NBHPBorderBottom, borderHoverTop : NBHPBorderTop}">Next <span class="fa fa-angle-double-right"></span></div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div ng-show="HPAlign == 'bottom' || HPAlign == 'right'" ng-class="{borderRight : HPBorderRight, borderLeft : HPBorderLeft, borderBottom : HPBorderBottom, borderTop : HPBorderTop, alignLeft : HPAlign == 'right'}" class="mwd-form-header">
												<div ng-show="HIPAlign != 'bottom' && HIPAlign != 'right'" ng-class="{imageRight : HIPAlign == 'right', imageLeft :  HIPAlign == 'left', imageBottom : HIPAlign == 'bottom', imageTop :  HIPAlign == 'top'}" class="himage">
													<img src="<?php echo MWD_URL; ?>/images/preview_header.png" />
												</div>
												<div ng-class="{imageRight : HIPAlign == 'right', imageLeft :  HIPAlign == 'left', imageBottom : HIPAlign == 'bottom', imageTop :  HIPAlign == 'top'}" class="htext">
													<div class="htitle">Subscribe Our Newsletter </div>
													<div class="hdescription">Join our mailing list to receive the latest news from our team.</div>
												</div>
												<div ng-show="HIPAlign == 'bottom' || HIPAlign == 'right'" ng-class="{imageRight : HIPAlign == 'right', imageLeft :  HIPAlign == 'left', imageBottom : HIPAlign == 'bottom', imageTop :  HIPAlign == 'top'}" class="himage">
													<img src="<?php echo MWD_URL; ?>/images/preview_header.png" />
												</div>
											</div>
										</div>
									</div>
									<div class="mwd-clear"></div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<script>
			angular.module('ThemeParams', []).controller('MWDTheme', function($scope) {
			});

			(function(jQuery){
				jQuery.fn.serializeObject = function(){

					var self = this,
						json = {},
						push_counters = {},
						patterns = {
							"validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
							"key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
							"push":     /^$/,
							"fixed":    /^\d+$/,
							"named":    /^[a-zA-Z0-9_]+$/
						};

					this.build = function(base, key, value){
						base[key] = value;
						return base;
					};

					this.push_counter = function(key){
						if(push_counters[key] === undefined){
							push_counters[key] = 0;
						}
						return push_counters[key]++;
					};

					jQuery.each(jQuery(this).serializeArray(), function(){

						// skip invalid keys
						if(!patterns.validate.test(this.name)){
							return;
						}

						var k,
							keys = this.name.match(patterns.key),
							merge = this.value,
							reverse_key = this.name;

						while((k = keys.pop()) !== undefined){

							// adjust reverse_key
							reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

							// push
							if(k.match(patterns.push)){
								merge = self.build([], self.push_counter(reverse_key), merge);
							}

							// fixed
							else if(k.match(patterns.fixed)){
								merge = self.build([], k, merge);
							}

							// named
							else if(k.match(patterns.named)){
								merge = self.build({}, k, merge);
							}
						}

						json = jQuery.extend(true, json, merge);
					});

					return json;
				};
			})(jQuery);

			jQuery(".mwd-themes-tabs li a").click(function(){
				jQuery(".mwd-themes-tabs-container .mwd-themes-container").hide();
				jQuery(".mwd-themes-tabs li a").removeClass("mwd-theme-active-tab");
				jQuery("#"+jQuery(this).attr("id")+'-content').show();
				jQuery(this).addClass("mwd-theme-active-tab");
				jQuery("#active_tab").val(jQuery(this).attr("id"));
				return false;
			});


			function submitbutton() {
				var all_params = jQuery('#mwd-themes-form').serializeObject();
				jQuery('#params').val(JSON.stringify(all_params).replace('<?php echo MWD_URL ?>', 'MWD_URL'));
				return true;
			}

			jQuery('.color').spectrum({
				showAlpha: true,
				showInput: true,
				showSelectionPalette: true,
				preferredFormat: "hex",
				allowEmpty: true,
				move: function(color){
					jQuery(this).val(color.toHexString());
					jQuery(this).trigger("change");
				}
			});

			setTimeout(function(){
				jQuery('.mwd-preview-form').show();
			}, 1500);
			
			setTimeout(function(){
				var mwd_form_example_pos = jQuery('.mwd-content').offset().top;
				jQuery(window).scroll(function() {
					if(jQuery(this).scrollTop() > mwd_form_example_pos) {
						jQuery('.mwd-content').css({'position' : 'fixed', 'top': '32px', 'z-index' : '10000', 'width' : jQuery(".form-example-preview").outerWidth()+'px'});

					} else{
						jQuery('.mwd-content').css({'position' : 'relative', 'top' : '32px', 'z-index' : '', 'width' : ''});
					}
				});

			}, 2500);

		</script>
		<?php
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
