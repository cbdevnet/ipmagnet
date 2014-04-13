ipMagnet allows you to quickly check what adresses your BitTorrent client
is handing out to its Trackers. It does this by generating a magnet link,
which when being requested generates a database entry.

The source code is freely available on http://github.com/cbdevnet/ipmagnet

Requirements
	HTTP daemon (eg. lighttpd)
	PHP5 (eg. php5-fcgi (for debian)) with enabled JSON extensions
	SQLite modules for PHP5 (eg. php5-sqlite)
	Read/write permissions for the user running the httpd
	 on the database file AND the containing folder
	The short_open_tag option should be set to "off" in php.ini	

Setup
	Clone the repo into a folder that is available by the httpd.
	Edit index.php
		Change the tracker URL (line 2) to point to the public
		 location of the index.php file.
	
		Optionally edit the database path (line 3) if you do not
		 want to have the database in the same folder for security
		 reasons.
