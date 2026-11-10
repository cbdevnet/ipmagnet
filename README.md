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

## Setup with Docker

You can easily build your own ipmagnet docker container by following the instructions below.

1. Clone this repository, then edit the environment variables within the Dockerfile provided according to your configuration or just create a file named "Dockerfile" yourself and copy inside the content of the Dockerfile, while changing the ENV variables according to your configuration.

Variable | type | unit | example
------------- | ------------- | ------------- | -------------
URL | String | - | your-ipmagnet-url-or-IP-without-trailing-slash.com or 127.0.0.1 or 196.23.62.266
ENABLE_INTERVAL | boolean | - |  true
INTERVAL | integer | seconds | 60

2. Build the container with the `docker build -t "ipmagnet" . ` from the same folder the Dockerfile resides in.

This will create a nginx webserver and use the files from the ipmagnet repository.

3. Run the ipmagnet container by using `docker run -d --rm --name ipmagnet -p 80:80 ipmagnet` 

You can of course change the port binding aswell, e.g. `docker run -d --rm --name ipmagnet -p 443:443 ipmagnet` or `docker run -d --rm --name ipmagnet -p 8080:80 ipmagnet`

4. You can then access the webui with the defined URL you set within the "URL" environment variable of the Dockerfile and the port used. Usually it would be `127.0.0.1:80` or the public IP of the server where it is hosted.

## Setup without Docker

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

### Preventing download of the SQLite database
To protect the privacy of your users, you should configure your httpd to deny access to the database containing all
non-deleted accesses (`ipmagnet.db3` in the standard configuration) by remote users. This can either be done by having the database
file exist in a location not served by the httpd, or introducing additional configuration.

Example configuration snippet for Apache

```
<Files "ipmagnet.db3">
	Order allow,deny
	Deny from all
</Files>
```

### Basic web panel access control

Note that ipMagnet by design stores very little important data, uses randomly generated pseudonyms (hashes) for identifying it and provides
a simple deletion method for that data. Thus, attack surface and potential risk on breach is already very much minimized.

If for some reason, you want to limit access to the web panel via a password, you can either configure basic authentication in your web server,
while taking care to also embed valid credentials into the `$TRACKER` variable at line 2 or insert the following lines at/after line 60 of `index.php`:

```
if (!isset($_SERVER["PHP_AUTH_USER"]) || $_SERVER["PHP_AUTH_PW"] != "SUPER_SECRET_PASSWORD") {
    header('WWW-Authenticate: Basic realm="ipMagnet"');
    header('HTTP/1.0 401 Unauthorized');
    exit();
} 
```

Replace `SUPER_SECRET_PASSWORD` with a plaintext password of your choice. This should allow BitTorrent clients to access the tracking link without
problems while preventing access to the web panel.

### Deployment behind a reverse proxy

Deploying ipMagnet behind a reverse proxy is possible, but it is very much an advanced use-case. To do so, make sure that the front-end server
correctly sets the HTTP `X-Forwarded-For` header in the backend requests and overwrites it, if it is supplied by the client.

To access the original client data instead of the reverse proxy address, change the references to `REMOTE_ADDR` before the hit insert statements
in `index.php` with `HTTP_X_FORWARDED_FOR`.

