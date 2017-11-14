ipMagnet
========

ipMagnet allows you to quickly check what adresses your BitTorrent client is handing out to its Trackers.
It does this by generating a magnet link, which when being requested by BitTorrent clients generates a database entry.

The source code is freely available on http://github.com/cbdevnet/ipmagnet and is designed to be read and understood by
anyone having basic knowledge of HTML, PHP and JavaScript.

The interface can be used without having JavaScript enabled and without allowing access to external resources which would
facilitate further tracking.

## Requirements

* A web server accessible on the internet
	* providing an HTTP daemon (eg. lighttpd) with
	* a working PHP installation (eg. php7.0-cgi for Debian)
	* optionally with enabled JSON extensions for use by the AJAX interface (mostly the default by now)
* SQLite PDO modules for the PHP installation (eg. php7.0-sqlite3 on Debian)
* The user running the HTTP daemon (www-data on debian) must have read/write access on the database file as well as the  folder containing it

## Setup

1. Clone the repo into a folder that is available by the http daemon.
2. Edit index.php
	* Change the tracker URL (line 2) to point to the public location of the index.php file.
	* Optionally edit the database path (line 3) if you do not want to have the database in the same folder for security reasons (or set up your webserver so it denies access to the database file).

### Setting a timeout value

If you'd like to set a timeout after which clients should recheck their IP against the tracking link, set
`$enableInterval` to true on line 4.

**WARNING:** This feature may be ignored or may break some clients (and they'd be right). The BitTorrent specification
explicitly states that when a 'failure reason' key is sent (which ipMagnet does), NO other key may be present.
Use this feature at your own risk.

### High traffic hosts

Large or high-volume installations, much as I would encourage everyone to host their own instances instead, should
probably use some advanced safeguards. This includes using an RDBMS more suited for high workloads (such as PostgreSQL
or MariaDB) as the data backend. Switching the backing data store can be done by changing the DSN (Data Source Name).
Please refer to the PHP PDO manual for information on how to do that.

The database needs to contain a table named 'hits' with the columns
```sql
'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE
'hash' TEXT NOT NULL or VARCHAR(40) NOT NULL /* (depending on your RDBMS) */
'timestamp' INTEGER NOT NULL
'addr' TEXT NOT NULL or VARCHAR(255) NOT NULL
'agent' TEXT NOT NULL or VARCHAR(255) NOT NULL
```

Another good idea would be to use a cronjob to regularly wipe the database to ensure better privacy on behalf of the users.

### Disallowing download of the SQLite database
To protect the privacy of your users, you should configure your httpd to disallow access to the database containing all
non-deleted accesses (`ipmagnet.db3` in the standard configuration). This can either be done by having the database
file exist in a location not served by the httpd, or introducing additional configuration.

Example configuration snippet for Apache

```
<Files "ipmagnet.db3">
	Order allow,deny
	Deny from all
</Files>
```
