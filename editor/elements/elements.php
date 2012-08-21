<?php



class PageLinesElement extends PageLinesSection {


	function __construct(){
		$this->id = strtolower( get_class($this) ); // lowercase class name
		$this->base_dir = PL_EDITOR . '/elements';
		$this->base_url = PL_EDITOR_URL . '/elements';
	}

	function section_persistent(){ } // Override

	function section_template() { } // Override

}

class eColumn extends PageLinesElement {

	function section_persistent(){ }

	function section_template() { 
	
		print_r($meta['content']);
		
	} 

}

