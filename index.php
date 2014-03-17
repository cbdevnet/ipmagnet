<?php
	$TRACKER="http%3A%2F%2Flocalhost%3A80%2Fipmagnet%2F";
	$db = new PDO("sqlite:ipmagnet.db3");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

	//todo db errhandling
	//todo ajax api

	if(isset($_GET["info_hash"])){
		$query="INSERT INTO hits (hash, timestamp, addr, agent) VALUES (:hash, :timestamp, :addr, :agent)";
		$stmt=$db->prepare($query);

		$addrs=htmlentities($_SERVER["REMOTE_ADDR"], ENT_QUOTES);
		if(isset($_GET["ipv4"])&&$_GET["ipv4"]!=$_SERVER["REMOTE_ADDR"]){
			$addrs.=", ".htmlentities($_GET["ipv4"], ENT_QUOTES);
		}
		if(isset($_GET["ipv6"])&&$_GET["ipv6"]!=$_SERVER["REMOTE_ADDR"]){
			$addrs.=", ".htmlentities($_GET["ipv6"], ENT_QUOTES);
		}

		$stmt->execute(
			array(
				":hash" => htmlentities(bin2hex($_GET["info_hash"]), ENT_QUOTES),
				":timestamp" => time(),
				":addr" => $addrs,
				":agent" => htmlentities($_SERVER["HTTP_USER_AGENT"],ENT_QUOTES)
			)
		);

		$stmt->closeCursor();
		//todo db errorhandling
		//fixme display ip here
		$resp="IP: ".$addrs;
		$resp=strlen($resp).":".$resp;
		print("d14:failure reason".$resp."e");
		die();
	}

	if(isset($_GET["hash"])){
		$HASH=htmlentities($_GET["hash"], ENT_QUOTES);
	}
	else{
		$HASH=SHA1($_SERVER["REMOTE_ADDR"]);
	}

	if(isset($_GET["clear"])){
		//todo db errhandling
		$query="DELETE FROM hits WHERE hash=:hash";
		$stmt=$db->prepare($query);
		$stmt->execute(
			array(
				":hash"=>$HASH
			)
		);
		$stmt->closeCursor();
	}

	//todo db errhandling
	$query="SELECT * FROM hits WHERE hash=:hash";
	$stmt=$db->prepare($query);
	$stmt->execute(
		array(
			":hash"=>$HASH
		)
	);

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
					ipMagnet allows you to see which IP address your BitTorrent Client is handing out to its peers and trackers!
				</div>
				Add this <a href="magnet:?xt=urn:btih:<?php print($HASH); ?>&dn=ipMagnet+Tracking+Link&tr=<?php print($TRACKER); ?>">Magnet link</a> to your downloads and watch this page.<br/>
				FYI, the address you've accessed this page with is <span id="remote-ip"><?php print($_SERVER["REMOTE_ADDR"]); ?></span>
				<div id="current-connections">
					<!-- update link --!>
					<div id="app-links">
						<a href="?hash=<?php print($HASH); ?>" class="app-link">Update</a>
						<a href="?clear&hash=<?php print($HASH); ?>" class="app-link">Clear my Data</a>
					</div>
					<table id="conn-table">
						<tr>
							<th>Timestamp</th>
							<th>IP address(es)</th>
							<th>User Agent</th>
						</tr>
						<?php
							$row=$stmt->fetch(PDO::FETCH_ASSOC);
							while($row!==FALSE){
								print("<tr>");
									print("<td>".date("d.m.Y H:i:s",$row["timestamp"])."</td>");
									print("<td>".$row["addr"]."</td>");
									print("<td>".$row["agent"]."</td>");
								print("</tr>");
								$row=$stmt->fetch(PDO::FETCH_ASSOC);
							}
							$stmt->closeCursor();
						?>
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
