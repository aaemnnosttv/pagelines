<?php
/**
 * 
 *
 *  Layout Control Interface
 *
 *
 *  @package PageLines Admin
 *  @subpackage OptionsUI
 *  @since 2.0.b3
 *
 */

class PageLinesLayoutControl {


	/**
	 * Construct
	 */
	function __construct() {
		

	}

	/**
	 * 
	 *
	 *  Main Layout Drag and Drop
	 *
	 *
	 *  @package PageLines Core
	 *  @subpackage Options
	 *  @since 2.0.b3
	 *
	 */
	function draw_layout_control($optionid, $option_settings){ ?>
		<div class="layout_controls selected_template">


			<div id="layout-dimensions" class="template-edit-panel">
				<h3>Configure Layout Dimensions</h3>
				<div class="select-edit-layout">
					<div class="layout-selections layout-builder-select fix">
						<div class="layout-overview">Select Layout To Edit</div>
						<?php


						global $pagelines_layout;
						foreach(get_the_layouts() as $layout):

							$the_last_edited = (pagelines_sub_option('layout', 'last_edit')) ? pagelines_sub_option('layout', 'last_edit') : 'one-sidebar-right';

							$load_layout = ($the_last_edited == $layout) ? true : false;

						?>
						<div class="layout-select-item">
							<span class="layout-image-border <?php if($load_layout) echo 'selectedlayout';?>">
								<span class="layout-image <?php echo $layout;?>">&nbsp;</span>
							</span>
							<input type="radio" class="layoutinput" name="<?php pagelines_option_name('layout', 'last_edit'); ?>" value="<?php echo $layout;?>" <?php if($load_layout) echo 'checked';?> />
						</div>
						<?php endforeach;?>

					</div>	
				</div>
				<?php

			foreach(get_the_layouts() as $layout):

			$buildlayout = new PageLinesLayout($layout);
				?>
			<div class="layouteditor <?php echo $layout;?> <?php if($buildlayout->layout_map['last_edit'] == $layout) echo 'selectededitor';?>">
					<div class="layout-main-content" style="width:<?php echo $buildlayout->builder->bwidth;?>px">

						<div id="innerlayout" class="layout-inner-content" >
							<?php if($buildlayout->west->id != 'hidden'):?>
							<div id="<?php echo $buildlayout->west->id;?>" class="ui-layout-west innerwest loelement locontent"  style="width:<?php echo $buildlayout->west->bwidth;?>px">
								<div class="loelement-pad">
									<div class="loelement-info">
										<div class="layout_text"><?php echo $buildlayout->west->text;?></div>
										<div class="width "><span><?php echo $buildlayout->west->width;?></span>px</div>
									</div>
								</div>
							</div>
							<?php endif;?>
							<div id="<?php echo $buildlayout->center->id;?>" class="ui-layout-center loelement locontent innercenter">
								<div class="loelement-pad">
									<div class="loelement-info">
										<div class="layout_text"><?php echo $buildlayout->center->text;?></div>
										<div class="width "><span><?php echo $buildlayout->center->width;?></span>px</div>
									</div>
								</div>
							</div>
							<?php if( $buildlayout->east->id != 'hidden'):?>
							<div id="<?php echo $buildlayout->east->id;?>" class="ui-layout-east innereast loelement locontent" style="width:<?php echo $buildlayout->east->bwidth;?>px">
								<div class="loelement-pad">
									<div class="loelement-info">
										<div class="layout_text"><?php echo $buildlayout->east->text;?></div>
										<div class="width "><span><?php echo $buildlayout->east->width;?></span>px</div>
									</div>
								</div>
							</div>
							<?php endif;?>
							<div id="contentwidth" class="ui-layout-south loelement locontent" style="background: #fff;">
								<div class="loelement-pad"><div class="loelement-info"><div class="width"><span><?php echo $buildlayout->content->width;?></span>px</div></div></div>
							</div>
							<div id="top" class="ui-layout-north loelement locontent"><div class="loelement-pad"><div class="loelement-info">Content Area</div></div></div>
						</div>
						<div class="margin-west loelement"><div class="loelement-pad"><div class="loelement-info">Margin<div class="width"></div></div></div></div>
						<div class="margin-east loelement"><div class="loelement-pad"><div class="loelement-info">Margin<div class="width"></div></div></div></div>

					</div>


						<div class="layoutinputs">
							<label class="context" for="input-content-width">Global Content Width</label>
							<input type="text" name="<?php pagelines_option_name('layout', 'content_width'); ?>" id="input-content-width" value="<?php echo $buildlayout->content->width;?>" size=5 readonly/>
							<label class="context"  for="input-maincolumn-width">Main Column Width</label>
							<input type="text" name="<?php pagelines_option_name('layout', $layout, 'maincolumn_width'); ?>" id="input-maincolumn-width" value="<?php echo $buildlayout->main_content->width;?>" size=5 readonly/>

							<label class="context"  for="input-primarysidebar-width">Sidebar1 Width</label>
							<input type="text" name="<?php pagelines_option_name('layout', $layout, 'primarysidebar_width'); ?>" id="input-primarysidebar-width" value="<?php echo  $buildlayout->sidebar1->width;?>" size=5 readonly/>
						</div>
			</div>
			<?php endforeach;?>

		</div>
	</div>
	<?php }
	
	function get_layout_selector($optionid, $option_settings){ ?>
		<div id="layout_selector" class="template-edit-panel">

			<div class="layout-selections layout-select-default fix">
				<div class="layout-overview">Default Layout</div>
				<?php


				global $pagelines_layout;
				foreach(get_the_layouts() as $layout):
				?>
				<div class="layout-select-item">
					<span class="layout-image-border <?php if($pagelines_layout->layout_map['saved_layout'] == $layout) echo 'selectedlayout';?>"><span class="layout-image <?php echo $layout;?>">&nbsp;</span></span>
					<input type="radio" class="layoutinput" name="<?php pagelines_option_name('layout', 'saved_layout'); ?>" value="<?php echo $layout;?>" <?php if($pagelines_layout->layout_map['saved_layout'] == $layout) echo 'checked';?>>
				</div>
				<?php endforeach;?>

			</div>

		</div>
		<div class="clear"></div>
	<?php }

}