/**
	The awesome	
	CodeBlue pseudo-cross-browser XHR/AJAX Code Library
	
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

var ajax={
	/**
		Create a XHR Object.
		IE Compat code removed.
	*/
	ajaxRequest:function(){
		if (window.XMLHttpRequest){//every sane browser, ever.
			return new XMLHttpRequest();
		}
		else{
			return false;
		}
	},
	
	/**
		Make an asynchronous GET request
		Calls /readyfunc/ with the request as parameter upon completion (readyState == 4)
	*/
	asyncGet:function(url,readyfunc,errfunc,user,pass){
		var request=new this.ajaxRequest();
		request.onreadystatechange=
			function(){
				if (request.readyState==4){
					readyfunc(request);
				}
			};
			
		request.open("GET",url,true,user,pass);
		try{
			request.send(null);
		}
		catch(e){
			errfunc(e);
		}
		return request;
	},
	
	/**
		Make an asynchronous POST request
		Calls /readyfunc/ with the request as parameter upon completion (readyState == 4)
		
		/payload/ should contain the data to be POSTed in the format specified by contentType,
		by defualt form-urlencoded
		
		
	*/
	asyncPost:function(url,payload,readyfunc,errfunc,contentType,user,pass){
		contentType=contentType||"application/x-www-form-urlencoded";
		
		var request=new this.ajaxRequest();
		request.onreadystatechange=
			function(){
				if (request.readyState==4){
					readyfunc(request);
				}
			};
			
		request.open("POST", url, true, user, pass);
		request.setRequestHeader("Content-type", contentType);
		try{
			request.send(payload);
		}
		catch(e){
			errfunc(e);
		}
		return request;
	},
	
	/**
		Perform a synchronous GET request
		This function does not do any error checking, so exceptions might
		be thrown.
	*/
	syncGet:function(url,user,pass){
		var request=new this.ajaxRequest();
		request.open("GET", url, false, user, pass);
		request.send(null);
		return request;
	},
	
	/**
		Perform a synchronous POST request, with /payload/
		being the data to POST in the specified format (default: form-urlencoded)
	*/
	syncPost:function(url, payload, contentType, user, pass){
		contentType=contentType||"application/x-www-form-urlencoded";
	
		var request=new this.ajaxRequest();
		request.open("POST", url, false, user, pass);
		request.setRequestHeader("Content-type", contentType);
		request.send(payload);
		return request;
	}
};