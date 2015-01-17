ipMagnet allows you to quickly check what adresses your BitTorrent client
is handing out to its Trackers. It does this by generating a magnet link,
which when being requested generates a database entry.

The source code is freely available on http://github.com/cbdevnet/ipmagnet
and is designed to be read and understood by anyone having basic knowledge
of HTML, PHP and JavaScript.

The interface can be used without having JavaScript enabled.


Requirements
	A Server accessible on the internet, providing
	- an HTTP daemon (eg. lighttpd)
	- with PHP5 (eg. php5-cgi (debian)) optionally with enabled JSON 
	  extensions for use by the AJAX interface (mostly the default by now)
	- SQLite PDO modules for PHP5 (eg. php5-sqlite (debian))
	- The user running the HTTP daemon (www-data on debian) must have
	  read/write access on the database file as well as the folder 
	  containing it
	- The short_open_tag option should be set to "off" in php.ini

Setup
	Clone the repo into a folder that is available by the httpd.
	Optionally, copy the sample config file (ipmagnet.ini.php-sample)
	to ipmagnet.ini.php and change its settings. Options include:
		Change the tracker URL to point to the public
		 location of the index.php file.
	
		Change the database path to point to the location of the
		 ipmagnet.db3 file if you want to have the database in a
		 different folder than index.php for security reasons.

	If you'd like to set a timeout after which clients should recheck their
	IP against the tracking link, set $enableInterval to true on line 4.
	WARNING: This feature may be ignored or may break some clients
	(and they'd be right) as the spec explicitly states that when a
	'failure reason' key is sent (which ipMagnet does), NO other key
	may be present. So use at your own risk.
