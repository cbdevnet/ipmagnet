<?php
	$HASH="f6108c60ba96a6e4a1bf27abf1f9ce138188e384";
	$TRACKER="http%3A%2F%2Fdev.cbcdn.com%3A80%2Fipmagnet";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>ipmagnet</title>
		<link rel="icon" href="static/favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" type="text/css" href="static/ipmagnet.css" />
		<script type="text/javascript" src="static/ajax.js"></script>
		<script type="text/javascript" src="static/ipmagnet.js"></script>
		<meta name="robots" content="noindex,nofollow" />
		<meta http-equiv="refresh" content="60; url=?hash=<?php print($HASH); ?>">
	</head>
	<body onload="ipmagnet.init();">
		<div id="title-wrap">
			<h1>ipMagnet</h1>
		</div>
		<div id="center-wrap">
			<div id="content-main">
				<div id="mission-statement">
					ipMagnet allows you to see which IP address your BitTorrent Client is handing out to it's peers and trackers!
				</div>
				Add this <a href="magnet:?xt=urn:btih:<?php print($HASH); ?>&dn=ipMagnet+Tracking+Link&tr=<?php print($TRACKER); ?>">Magnet link</a> to your downloads and watch this page.
				FYI, the address you've accessed this page with is <?php print($_SERVER["REMOTE_ADDR"]); ?>
				<div id="current-connections">
					<table id="conn-table">
						<tr>
							<th>Timestamp</th>
							<th>IP address</th>
							<th>User Agent</th>
						</tr>
					</table>
				</div>
			</div>

			<div id="footer-text">
			<span id="status-line">Status: n/a <span id="status-text"></span></span>
				<span id="meta-footer">
					<a href="https://github.com/cbdevnet/ipmagnet">[source]</a>
					<a href="http://www.kopimi.com/kopimi/"><img src="static/kopimi.png"/></a>
					<a href="http://wtfpl.net/"><img src="static/wtfpl.png"/></a>
				</span>
			</div>
		</div>
	</body>
</html>
