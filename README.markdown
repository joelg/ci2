Changes to CodeIgniter Release Version
======================================

core
----

* The contents of "core" have not been touched.
* index.php placed in new "html" directory.
* "core" directory renamed to "ci-core"
* "application" directory renamed to "ci-app" (I change this again for each individual app).


application/config/config.php
-----------------------------

* base_url set to http://$_SERVER['SERVER_NAME']/
* index_page set to empty string (always use mod_rewrite)
* ? added to permitted_uri_chars
* Changed uri_protocol to PATH_INFO


application/config/constants.php
--------------------------------

* Added IS_AJAX constant for requests which came via AJAX
* Added LIVE constant to differentiate dev and live environments


application/core
----------------

* Added MY_Controller.php
	* Support for "notices" and "global errors"
	* Support for $this->\_header() and $this->\_footer() functions which use views/common/header.php and views/common/footer.php
	* A _json function which outputs JSON with the correct Content-Type header.
* Added MY_Input.php
	* Allows for query strings when required (e.g. Twitter OAuth)
	
	
application/libraries
---------------------

* Added View.php library which allows multiple calls to set variables in views
	* Syntax changes from $this->load->view() to $this->view->load() and $this->view->set()