/*

This program is free software. It comes without any warranty, to
the extent permitted by applicable law. You can redistribute it
and/or modify it under the terms of the Do What The Fuck You Want
To Public License, Version 2, as published by Sam Hocevar and 
reproduced below.

DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE 
Version 2, December 2004 

Copyright (C) 2004 Sam Hocevar <sam@hocevar.net> 

	Everyone is permitted to copy and distribute verbatim or modified 
	copies of this license document, and changing it is allowed as long 
	as the name is changed. 

DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE 
TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION 

	0. You just DO WHAT THE FUCK YOU WANT TO.
*/

var ipmagnet={
	apiUrl:"index.php",
	hash:"",

	//display a short status update at the bottom of the screen
	statusDisplay:function(text){
		document.getElementById("status-text").textContent=text;	
	},

	//pad a string to a given length
	pad:function(str,padString,length){
		str=''+str;
		while(str.length<length){
			str=padString+str;
		}
		return str;
	},

	init:function(hash){
		if(!hash){
			return;
		}

		//store hash
		ipmagnet.hash=hash;

		//hook links
		var updateLink=document.getElementById("update-link");
		var clearLink=document.getElementById("clear-link");
		updateLink.href="#";
		clearLink.href="#";
		updateLink.onclick=ipmagnet.update;
		clearLink.onclick=ipmagnet.clear;

		//start interval
		setInterval(ipmagnet.update, 10000);

		ipmagnet.statusDisplay("JavaScript detected!");
	},

	update:function(){
		ipmagnet.updateTable(ipmagnet.hash, false);
	},

	clear:function(){
		ipmagnet.updateTable(ipmagnet.hash, true);
	},

	//build a single table row, given the text contents and optionally a tag name
	buildRow:function(elements, tag){
		tag=tag||"td";
		var row=document.createElement("tr");
		elements.forEach(function(e){
			var cell=document.createElement(tag);
			cell.textContent=e;
			row.appendChild(cell);
		});
		return row;
	},

	//format a javascript date object to a sensible string representation
	formatDate:function(date){
		var datePart=ipmagnet.pad(date.getDate(),'0',2)+"."+ipmagnet.pad(date.getMonth()+1,'0',2)+"."+date.getFullYear();
		var timePart=ipmagnet.pad(date.getHours(),'0',2)+":"+ipmagnet.pad(date.getMinutes(),'0',2)+":"+ipmagnet.pad(date.getSeconds(),'0',2);

		return datePart+" "+timePart;
	},

	updateTable:function(hash, clear){
		//get all hits from the database
		ajax.asyncGet(ipmagnet.apiUrl+"?ajax&hash="+hash+(clear?"&clear":""), function(req){
			//xhr completion function
			if(req.status==200){
				try{
					var response=JSON.parse(req.responseText);		
				}
				catch(e){
					ipmagnet.statusDisplay("Failed to parse response.");
					return;
				}

				if(response.code!=0){
					ipmagnet.statusDisplay(response.message);
					return;
				}
				var hitsTable=document.getElementById("conn-table");
				hitsTable.innerHTML="";
				hitsTable.appendChild(ipmagnet.buildRow(["Timestamp","IP address(es)","User Agent"],"th"));
				
				if(response.hits){
					response.hits.forEach(function(hit){
						var timestamp=new Date(parseInt(hit.timestamp)*1000);
						hitsTable.appendChild(ipmagnet.buildRow([ipmagnet.formatDate(timestamp), hit.addr, hit.agent]));
					});
				}
			}
			else{
				ipmagnet.statusDisplay("Failed to fetch data (HTTP "+req.status+")");
			}
		}, function(e){
			//error function
			ipmagnet.statusDisplay("Failed to fetch data ("+e.message+")");
		});
	}
};
