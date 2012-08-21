<?php
/**
 * 
 *
 *  PageLines Front End Template Class
 *
 *
 *  @package PageLines Framework
 *  @subpackage Sections
 *  @since 3.0.0
 *  
 *
 */
class EditorInterface {


	function __construct( ) {
		
	}
	
	function area_start($a){
		
		printf( '<div class="pl-area">%s<div class="pl-content"><div class="pl-inner">', $this->area_controls($a)); 
		
		
	}
	
	function area_end(){
		echo '</div></div></div>';
	}
	
	function area_controls($a){
		
		ob_start();
		?>

		<div class="pl-area-controls">
			<div class="controls-toggle-btn btn btn-inverse btn-mini"><?php echo $a['name'];?> <b class="caret"></b></div>
			<div class="controls-buttons btn-toolbar">
				<div class="btn-group">
					<button class="btn btn-mini btn-inverse" href="#editModal" onClick="drawModal(\'Page Builder\');">Add New Area</button>
				</div><div class="btn-group">
					<button class="btn btn-mini btn-inverse dropdown-toggle" data-toggle="dropdown" >Add Section <b class="caret"></b></button> 
					<ul id="add_section" class="dropdown-menu">
						<li><a href="#">Drop</a></li>
					</ul>
				</div><div class="btn-group">
					<button class="btn btn-mini btn-inverse dropdown-toggle" data-toggle="dropdown">Add Element <b class="caret"></b></button>
					<ul id="add_element" class="dropdown-menu">
						<li><a href="#">Drop</a></li>
						<li><a href="#">Drop II</a></li>
					</ul>
				</div>
			</div>
		</div>
		<?php
		
		return ob_get_clean();
	}
	
	function section_controls($sid, $s){
		
		?>
		
		<div id="<?php echo $sid;?>_control" class="pl-section-controls">
			<div class="controls-left">
				<a title="Section Decrease Width" href="#" class="pl-control pl-control-icon section-decrease">L</a>
				<span title="Width" class="pl-control section-size">12/12</span>
				<a title="Section Increase Width" href="#" class="pl-control pl-control-icon section-increase">R</a>
				<a title="Increase Offset" href="#" class="pl-control pl-control-icon section-offset-increase">OL</a>
				<span title="Offset Size" class="pl-control offset-size"></span>
				<a title="Reduce Offset" href="#" class="pl-control pl-control-icon section-offset-reduce">OR</a>
				<a title="Force New Row" href="#" class="pl-control pl-control-icon section-start-row">S</a>
			</div>
			<span class="controls-title"><?php echo $s->name;?></span>
			<div class="controls-right">
				<a title="Edit Section" href="#" class="pl-control pl-control-icon section-edit">E</a>
				<a title="Clone Section" href="#" class="pl-control pl-control-icon section-clone">C</a>
				<a title="Delete Section" href="#" class="pl-control pl-control-icon section-delete">X</a>
			</div>
		</div>
		<?php
		
	}

}


