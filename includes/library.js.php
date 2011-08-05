<?php

/**
 *  Fix The WordPress Login Image URL
 */
function pl_js_ready_start(){
	return '<script type="text/javascript">/* <![CDATA[ */ jQuery(document).ready(function () {';
}

function pl_js_end(){
	return '/* ]]> */ </script>';
}
