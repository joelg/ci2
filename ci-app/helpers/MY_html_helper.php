<?php

/**
 * Show specific JavaScript files. Files should be placed in /js/specific, and an array of filenames
 * excluding file extensions should be passed to the view as the variable @js_files. Then simply call
 * this function in your header view.
 */
function include_specific_js($js_files) {
	if(!empty($js_files)){
		foreach($js_files as $js_file) {
			?><script type="text/javascript" src="/js/specific/<?=$js_file?>.js"></script><?
		}
	}
}

/**
 * Show specific JavaScript templates. Templates should be placed in /js/templates, and an array of filenames
 * excluding file extensions should be passed to the view as the variable @js_templates. Then simply call
 * this function in your header view.
 */
function include_js_templates($js_templates) {
	if(!empty($js_templates)){
		foreach($js_templates as $js_template) {
			?><script id="<?=$js_template?>" type="text/x-jquery-tmpl"><?
			echo file_get_contents(base_url().'js/templates/'.$js_template.'.tmpl');
			?></script><?
		}
	}
}


function output_title($title, $image = false)
{
	if($image && $title != "Buffer") $title = str_replace('Buffer', '<a href="/"><img src="/images/logo-new.png" alt="buffer" /></a>', $title);
	elseif($image) $title = str_replace('Buffer', '<a href="/"><img src="/images/logo-small.png" alt="buffer" /></a>', $title);
	echo $title;
}

function add_notice($message)
{
	$ci =& get_instance();
	$notices = $ci->session->flashdata('notices');
	$notices = array();
	if(!is_array($message)) $notices[] = $message;
	else $notices = $message;
	$ci->session->set_flashdata('notices', $notices);
}

function show_notices($notices) {
	if(!empty($notices)) {
		echo "<ul class='notices'>";
		foreach($notices as $notice) {
			echo "<li>$notice</li>";
		}
		echo "</ul>";
	}
}

function add_global_error($error)
{
	$ci =& get_instance();
	$global_errors = $ci->session->flashdata('global_errors');
	$global_errors = array();
	if(!is_array($error)) $global_errors[] = $error;
	else $global_errors = $error;
	$ci->session->set_flashdata('global_errors', $global_errors);
}

function show_global_errors($errors) {
	if(!empty($errors)) {
		echo "<ul class='global_errors'>";
		foreach($errors as $error) {
			echo "<li>$error</li>";
		}
		echo "</ul>";
	}
}