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
* Added IS_AJAX constant for requests which came via AJAX
* Added LIVE constant to differentiate dev and live environments