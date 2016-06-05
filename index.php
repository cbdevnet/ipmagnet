<?php
	$TRACKER=urlencode("http://localhost:80/ipmagnet/"); //Remember to include the trailing slash here!
	$db = new PDO("sqlite:ipmagnet.db3");
	$enableInterval=false;
	$trackerInterval=300;
	
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

	function is_sha1($string){
		return preg_match('/^[A-Fa-f0-9]{40}$/', $string);
	}

	//BitTorrent clients will submit the info_hash parameter when requesting the magnet link
	if(isset($_GET["info_hash"])){

		//prepare the insert query
		$query="INSERT INTO hits (hash, timestamp, addr, agent) VALUES (:hash, :timestamp, :addr, :agent)";
		$stmt=$db->prepare($query);
		
		if($stmt===FALSE){
			exit("d14:failure reason16:Database failuree");
		}

		//gather all supplied ip addresses
		$addrs=htmlentities($_SERVER["REMOTE_ADDR"], ENT_QUOTES);
		if(isset($_GET["ipv4"])&&$_GET["ipv4"]!=$_SERVER["REMOTE_ADDR"]){
			$addrs.=", ".htmlentities($_GET["ipv4"], ENT_QUOTES);
		}
		if(isset($_GET["ipv6"])&&$_GET["ipv6"]!=$_SERVER["REMOTE_ADDR"]){
			$addrs.=", ".htmlentities($_GET["ipv6"], ENT_QUOTES);
		}
		if(isset($_GET["ip"])&&$_GET["ip"]!=$_SERVER["REMOTE_ADDR"]){
			$addrs.=", ".htmlentities($_GET["ip"], ENT_QUOTES);
		}

		//insert the hit into the database
		if(!($stmt->execute(
			array(
				":hash" => htmlentities(bin2hex($_GET["info_hash"]), ENT_QUOTES),
				":timestamp" => time(),
				":addr" => $addrs,
				":agent" => htmlentities($_SERVER["HTTP_USER_AGENT"],ENT_QUOTES)
			)
		))){
			//failed to insert.
		}

		$stmt->closeCursor();

		//print the ips as "failure reason" to be displayed by some clients
		$resp="IP: ".$addrs;
		$resp="14:failure reason".strlen($resp).":".$resp;

		if($enableInterval){
			$resp.="8:intervali".$trackerInterval."e";
		}

		exit("d".$resp."e");
	}

	$returnValue["message"]="ok";
	$returnValue["code"]=0;

	//if a hash was supplied and it's a valid sha1 hash, use it
	if(isset($_GET["hash"]) && is_sha1($_GET["hash"])){
		$HASH=strtolower($_GET["hash"]);
	}
	//else, generate a new one
	else{
		$HASH=SHA1(mt_rand());
		header("Location: ?hash=".$HASH);
		exit();
	}

	$returnValue["hash"]=$HASH;

	if(isset($_GET["clear"])){
		//delete all hits for a hash from the database
		$query="DELETE FROM hits WHERE hash=:hash";
		$stmt=$db->prepare($query);

		if($stmt===FALSE){
			$returnValue["message"]="Failed to prepare query.";
			$returnValue["code"]=2;
			exit(json_encode($returnValue));
		}

		$stmt->execute(
			array(
				":hash"=>$HASH
			)
		);
		
		$stmt->closeCursor();
	}

	//get all currently stored hits for a hash
	$query="SELECT * FROM hits WHERE hash=:hash";
	$stmt=$db->prepare($query);

	if($stmt===FALSE){
		$returnValue["message"]="Failed to prepare query.";
		$returnValue["code"]=3;
		exit(json_encode($returnValue));
	}

	$stmt->execute(
		array(
			":hash"=>$HASH
		)
	);

	//print the response as JSON data if called from the ajax interface
	if(isset($_GET["ajax"])){
		//fetch all hits into one array
		$returnValue["hits"]=$stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();

		//set content-type and CORS headers
		header("Content-Type: application/json");
		header("Access-Control-Allow-Origin: *");	
		exit(json_encode($returnValue));
	}

?>
<?php echo '<?xml version="1.1" encoding="UTF-8" ?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>ipMagnet</title>
		<link rel="icon" href="static/favicon.png" type="image/x-icon" />
		<link rel="stylesheet" type="text/css" href="static/ipmagnet.css" />
		<script type="text/javascript" src="static/ajax.js"></script>
		<script type="text/javascript" src="static/ipmagnet.js"></script>
		<meta name="robots" content="noindex,nofollow" />
		<noscript>
			<meta http-equiv="refresh" content="60; url=?hash=<?php print($HASH); ?>" />
		</noscript>
	</head>
	<body onload="ipmagnet.init('<?php print($HASH); ?>');">
		<div id="title-wrap">
			<h1>ipMagnet</h1>
		</div>
		<div id="center-wrap">
			<div id="content-main">
				<div id="mission-statement">
					ipMagnet allows you to see which IP address your BitTorrent Client is handing out to its peers and trackers!
				</div>
				Add this <a href="magnet:?xt=urn:btih:<?php print($HASH); ?>&amp;dn=ipMagnet+Tracking+Link&amp;tr=<?php print($TRACKER); ?>">Magnet link</a> to your 
				downloads and watch this page.<br/>
				FYI, the address you've accessed this page with is <span id="remote-ip"><?php print($_SERVER["REMOTE_ADDR"]); ?></span>
				<div id="current-connections">
					<div id="app-links">
						<a href="?hash=<?php print($HASH); ?>" class="app-link" id="update-link">Update</a>
						<a href="?clear&amp;hash=<?php print($HASH); ?>" class="app-link" id="clear-link">Clear my Data</a>
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
			<span id="status-line">Status: <span id="status-text">Using plain HTML</span></span>
				<span id="meta-footer">
					Proudly run without ads or web tracking. Set up your own with the
					<a href="https://github.com/cbdevnet/ipmagnet">[source]</a>
					<a href="http://www.kopimi.com/kopimi/"><img src="static/kopimi.png" alt="kopimi"/></a>
					<a href="http://wtfpl.net/"><img src="static/wtfpl.png" alt="wtfpl"/></a>
				</span>
			</div>
		</div>
	</body>
</html>
