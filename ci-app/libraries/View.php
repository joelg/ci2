<?php

/**
*	View Library - simplifies management and loading of views in CodeIgniter applications
*
*	@author			Ted Wood (ted[at]codeoflife.ca)
*	@last_modified	July 28th, 2007
*
*/

class View {
	var $CI;
	var $vars	= array();
	var $parts	= array();
	// configuration
	var $config	= array('parse'			=> FALSE,
						'cache_timeout'	=> 0);
	
	function View($config = array()) {
		$this->CI =& get_instance();
		if (is_array($config)) {
			$this->config = array_merge($this->config, $config);
		}
		$this->set_parse_mode($this->config['parse']);
		if ($this->config['cache_timeout'] > 0) {
			$this->cache();
		}
	}
	
	/**
	 * Toggles parsing mode on (default) or off
	 *
	 * Usage:
	 *   $this->view->set_parse_mode();
	 *
	 * @access	public
	 * @param	bool	whether to parse or not, defaults to TRUE
	 * @return	none
	 */
	function set_parse_mode($state = TRUE) {
		$this->config['parse'] = (bool) $state;
		if ($this->config['parse']) {
			$this->CI->load->library('parser');
		}
	}
	
	/**
	 * Assigns data to the view
	 *	with option to avoid overwriting existing value (useful for assigning default values)
	 *
	 * Usage:
	 *   $this->view->set($array_of_data);			// assign array of data
	 *   $this->view->set('name', 'value');			// assign atomic value
	 *   $this->view->set('name', 'value', TRUE);	// only assigns value of it doesn't exist
	 *
	 * @access	public
	 * @param	mixed	a placeholder name or array of data
	 * @param	mixed	NULL or value to assign
	 * @param	bool	controls whether existing values are replaced (default) or not
	 * @return	none
	 */
	function set($data, $value = NULL, $no_replace = FALSE) {		// no_replace: don't replace existing value
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$this->set($k, $v, $no_replace);
			}
		} elseif (!$no_replace || !isset($this->vars[$data])) {
			$this->vars[$data] = $value;
		}
	}
	
	/**
	 * Appends string values onto existing string values, if present
	 *  othewise simply assigns to view like set() function
	 *
	 * Usage:
	 *   $this->view->append($array_of_data);
	 *   $this->view->append($name, $value);
	 *
	 * @access	public
	 * @param	mixed	name of placeholder or array of data
	 * @param	mixed	NULL or value of data to assign
	 * @return	none
	 */
	function append($data, $value = NULL) {
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$this->append($k, $v);
			}
		} elseif (is_string($value)) {
			if (isset($this->vars[$data])) {
				$this->vars[$data] .= $value;
			} else {
				$this->vars[$data] = $value;
			}
		}
	}
	
	/**
	 * Retrieves value of previously-set data, or NULL if it doens't exist
	 *
	 * Usage:
	 *   $this->view->get($name_of_variable);
	 *
	 * @access	public
	 * @param	string	name of existing variable
	 * @return	mixed
	 */
	function get($name) {
		return (isset($this->vars[$name]) ? $this->vars[$name] : NULL);
	}
		
	/*** css & js ***/
	
	/**
	 * Simplifies dynamic loading of CSS files using <link> tag.
	 * Assigns rendered html into specified placeholder variable (defaults to "view_css")
	 *
	 * Usage:
	 *   $this->view->linkCSS('/css/some_styles.css');
	 *
	 * In the view:
	 *    <?php echo $view_css; ?> // must be within the <head> section
	 *
	 * @access	public
	 * @param	string	uri of CSS file
	 * @param	string	name of placeholder variable, defaults to $view_css
	 * @return	none
	 */
	function linkCSS($href, $var = 'view_css') {
		if (!isset($this->vars[$var])) {
			$this->vars[$var] = "<!-- @{$var} -->\n\t";
		}
		if (is_array($href)) {
			foreach ($href as $v) {
				$this->vars[$var] .= '<link type="text/css" rel="stylesheet" href="'.$v.'" />'."\n\t";
			}
		} else {
			$this->vars[$var] .= '<link type="text/css" rel="stylesheet" href="'.$href.'" />'."\n\t";
		}
	}
	
	/**
	 * Simplifies dynamic loading of CSS files using @import directive.
	 * Assigns rendered html into specified placeholder variable (defaults to "view_css")
	 *
	 * Usage:
	 *   $this->view->importCSS('/css/some_styles.css');
	 *
	 * In the view:
	 *    <?php echo $view_css; ?> // can be anywhere in view or a partial
	 *
	 * @access	public
	 * @param	string	uri of CSS file
	 * @param	string	name of placeholder variable, defaults to $view_css
	 * @return	none
	 */
	function importCSS($url, $var = 'view_css') {
		if (!isset($this->vars[$var])) {
			$this->vars[$var] = "<!-- @{$var} -->\n\t";
		}
		if (is_array($url)) {
			foreach ($url as $v) {
				$this->vars[$var] .= '<style type="text/css">@import url("'.$v.'");</style>'."\n\t";
			}
		} else {
			$this->vars[$var] .= '<style type="text/css">@import url("'.$url.'");</style>'."\n\t";
		}
	}
	
	/**
	 * Simplifies dynamic loading of JavaScript files.
	 * Assigns rendered html into specified placeholder variable (defaults to "view_js")
	 *
	 * Usage:
	 *   $this->view->linkJS('/js/some_functions.js');
	 *
	 * In the view:
	 *    <?php echo $view_js; ?> // will contain rendered <script> tags
	 *
	 * @access	public
	 * @param	string	uri of JavaScript file
	 * @param	string	name of placeholder variable, defaults to $view_js
	 * @return	none
	 */
	function linkJS($src, $var = 'view_js') {
		if (!isset($this->vars[$var])) {
			$this->vars[$var] = "<!-- @{$var} -->\n\t";
		}
		if (is_array($src)) {
			foreach ($src as $v) {
				$this->vars[$var] .= '<script type="text/javascript" src="'.$v.'"></script>'."\n\t";
			}
		} else {
			$this->vars[$var] .= '<script type="text/javascript" src="'.$src.'"></script>'."\n\t";
		}
	}
		
	/*** meta tags ***/
	
	/**
	 * Simplifies dynamic adding of <meta> tags
	 * Assigns rendered html into specified placeholder variable (defaults to "meta_tags")
	 *
	 * Usage:
	 *   $this->view->meta('description', "Here is a description of this page.");
	 *
	 * In the view:
	 *    <?php echo $meta_tags; ?> // must be within the <head> section
	 *
	 * @access	public
	 * @param	string	name of meta tag
	 * @param	string	value of meta tag
	 * @return	none
	 */
	function meta($name, $content) {
		if (!isset($this->vars['meta_tags'])) {
			$this->vars['meta_tags'] = "<!-- @meta_tags -->\n\t";
		}
		$this->vars['meta_tags'] .= '<meta name="'.$name.'" content="'.htmlentities($content).'" />'."\n\t";
	}
	
	/*** includes & templates ***/
	
	/**
	 * Loads a partial or prepares one for loading into the view.
	 *
	 * Usage:
	 *   $this->view->part('placeholder', 'path/to/partial', TRUE or FALSE);
	 *
	 * @access	public
	 * @param	string	name of a placeholder variable from the parent view (can be another partial)
	 * @param	string	subpath of file containing partial within /views/ folder.
	 * @param	bool	controls whether the partial is loaded immediate or when full view is loaded (default)
	 * @return	mixed
	 */
	function part($name, $view, $render_now = FALSE) {
		if ($render_now) {
			$this->vars[$name] = ($this->config['parse'] ? $this->CI->parser->parse($view, $this->vars, TRUE) : $this->CI->load->view($view, $this->vars, TRUE));
		} else {
			$this->parts[$name] = $view;
		}
	}
	
	/**
	 * Renders and displays or returns a few file. Replacement for CI's $this->load->view() method
	 *
	 * Usage:
	 *   $this->view->load('template', $data_array, TRUE or FALSE);
	 *
	 * @access	public
	 * @param	string	a name of a template file
	 * @param	string	an atomic or array of variables
	 * @param	bool	whether or not to return the view rather than display it
	 * @return	mixed
	 */
	function load($tpl, $data = array(), $return = FALSE) {
		$this->set($data);
		foreach ($this->parts as $name => $view) {
			$this->vars[$name] = ($this->config['parse'] ? $this->CI->parser->parse($view, $this->vars, TRUE) : $this->CI->load->view($view, $this->vars, TRUE));
		}
		return ($this->config['parse'] ? $this->CI->parser->parse($tpl, $this->vars, $return) : $this->CI->load->view($tpl, $this->vars, $return));
	}
	
	/*** view/part caching ***/
	
	/**
	 * Instruct CI not the cache the view on the server
	 * Send headers to instruct browser not to cache the view
	 *
	 * Usage:
	 *   $this->view->no_cache();
	 *
	 * @access	public
	 * @return	none
	 */
	function no_cache() {
		// tell CI not to cache the view
		$this->CI->output->cache(0);
		// tell the browser not to cache the view
		$this->CI->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
		$this->CI->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->CI->output->set_header('Pragma: no-cache');
		$this->CI->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	}
	
	function cache($mins = NULL) {
		if ($mins === NULL) {
			$mins = $this->config['cache_timeout'];
		}
		$this->CI->output->cache($mins);
	}

}

?>