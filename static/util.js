function timeFormatter(unixTimestamp) {
	var d = new Date(unixTimestamp * 1000);
	d.setTime(d.getTime() - d.getTimezoneOffset() * 60 * 1000);
	return formatDate(d, '%Y-%M-%d %H:%m');
}

function formatDate(date, fmt) {
	function pad(value) {
		return (value.toString().length < 2) ? '0' + value : value;
	}

	return fmt.replace(/%([a-zA-Z])/g, function (_, fmtCode) {
		switch (fmtCode) {
			case 'Y':
				return date.getUTCFullYear();
			case 'M':
				return pad(date.getUTCMonth() + 1);
			case 'n':
				return date.getUTCMonth() + 1;
			case 'd':
				return pad(date.getUTCDate());
			case 'H':
				return pad(date.getUTCHours());
			case 'm':
				return pad(date.getUTCMinutes());
			case 's':
				return pad(date.getUTCSeconds());
			default:
				throw new Error('Unsupported format code: ' + fmtCode);
		}
	});
}

function long2ip(ip) {
	//  discuss at: http://locutus.io/php/long2ip/
	// original by: Waldo Malqui Silva (http://waldo.malqui.info)
	if (!isFinite(ip)) {
		return false;
	}
	return [ip >>> 24, ip >>> 16 & 0xFF, ip >>> 8 & 0xFF, ip & 0xFF].join('.')
}

//http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript
function getParameterByName(name, url) {
	if (!url) {
		url = window.location.href;
	}
	name = name.replace(/[\[\]]/g, "\\$&");
	var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
		results = regex.exec(url);
	if (!results) return null;
	if (!results[2]) return '';
	return decodeURIComponent(results[2].replace(/\+/g, " "));
}

// https://stackoverflow.com/questions/736513/how-do-i-parse-a-url-into-hostname-and-path-in-javascript
function isURL(url) {
	var parser = document.createElement('a');
	parser.href = url;

	var protocal = parser.protocol; // => "http:"
	var host = parser.host;         // => "example.com:3000"
	var hostname = parser.hostname; // => "example.com"
	var port = parser.port;         // => "3000"
	var pathname = parser.pathname; // => "/pathname/"
	var hash = parser.hash;         // => "#hash"
	var search = parser.search;     // => "?search=test"
	var origin = parser.origin;     // => "http://example.com:3000"
	if (protocal === "http:" || protocal === "https:") {
		return hostname !== '';
	}
	return false;
}

function getCookieByName(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) === ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}

function isCSRFSafeMethod(method) {
	// these HTTP methods do not require CSRF protection
	return (/^(GET|HEAD|OPTIONS|TRACE)$/.test(method));
}

$.ajaxSetup({
	beforeSend: function (xhr, settings) {
		if (!isCSRFSafeMethod(settings.type) && !this.crossDomain) {
			xhr.setRequestHeader("X-CSRF-Token", getCookieByName('csrf_token'));
		}
	}
});