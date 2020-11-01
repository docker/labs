/* eslint-disable */

/**
 * To use sails.io.js in an AMD environment (e.g. with require.js),
 * replace this file with the sails.io.js file from the root of:
 * https://github.com/balderdashy/sails.io.js
 * and download a standalone copy of socket.io-client from:
 * https://github.com/socketio/socket.io-client
 * then follow the instructions at:
 * https://github.com/balderdashy/sails.io.js#requirejsamd-usage
 */

// socket.io-client version 2.0.3
// https://github.com/socketio/socket.io-client

!function(a,b){"object"==typeof exports&&"object"==typeof module?module.exports=b():"function"==typeof define&&define.amd?define([],b):"object"==typeof exports?exports.io=b():a.io=b()}(this,function(){return function(a){function b(d){if(c[d])return c[d].exports;var e=c[d]={exports:{},id:d,loaded:!1};return a[d].call(e.exports,e,e.exports,b),e.loaded=!0,e.exports}var c={};return b.m=a,b.c=c,b.p="",b(0)}([function(a,b,c){"use strict";function d(a,b){"object"===("undefined"==typeof a?"undefined":e(a))&&(b=a,a=void 0),b=b||{};var c,d=f(a),g=d.source,k=d.id,l=d.path,m=j[k]&&l in j[k].nsps,n=b.forceNew||b["force new connection"]||!1===b.multiplex||m;return n?(i("ignoring socket cache for %s",g),c=h(g,b)):(j[k]||(i("new io instance for %s",g),j[k]=h(g,b)),c=j[k]),d.query&&!b.query&&(b.query=d.query),c.socket(d.path,b)}var e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&"function"==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?"symbol":typeof a},f=c(1),g=c(7),h=c(13),i=c(3)("socket.io-client");a.exports=b=d;var j=b.managers={};b.protocol=g.protocol,b.connect=d,b.Manager=c(13),b.Socket=c(39)},function(a,b,c){(function(b){"use strict";function d(a,c){var d=a;c=c||b.location,null==a&&(a=c.protocol+"//"+c.host),"string"==typeof a&&("/"===a.charAt(0)&&(a="/"===a.charAt(1)?c.protocol+a:c.host+a),/^(https?|wss?):\/\//.test(a)||(f("protocol-less url %s",a),a="undefined"!=typeof c?c.protocol+"//"+a:"https://"+a),f("parse %s",a),d=e(a)),d.port||(/^(http|ws)$/.test(d.protocol)?d.port="80":/^(http|ws)s$/.test(d.protocol)&&(d.port="443")),d.path=d.path||"/";var g=d.host.indexOf(":")!==-1,h=g?"["+d.host+"]":d.host;return d.id=d.protocol+"://"+h+":"+d.port,d.href=d.protocol+"://"+h+(c&&c.port===d.port?"":":"+d.port),d}var e=c(2),f=c(3)("socket.io-client:url");a.exports=d}).call(b,function(){return this}())},function(a,b){var c=/^(?:(?![^:@]+:[^:@\/]*@)(http|https|ws|wss):\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?((?:[a-f0-9]{0,4}:){2,7}[a-f0-9]{0,4}|[^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/,d=["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"];a.exports=function(a){var b=a,e=a.indexOf("["),f=a.indexOf("]");e!=-1&&f!=-1&&(a=a.substring(0,e)+a.substring(e,f).replace(/:/g,";")+a.substring(f,a.length));for(var g=c.exec(a||""),h={},i=14;i--;)h[d[i]]=g[i]||"";return e!=-1&&f!=-1&&(h.source=b,h.host=h.host.substring(1,h.host.length-1).replace(/;/g,":"),h.authority=h.authority.replace("[","").replace("]","").replace(/;/g,":"),h.ipv6uri=!0),h}},function(a,b,c){(function(d){function e(){return!("undefined"==typeof window||!window.process||"renderer"!==window.process.type)||"undefined"!=typeof document&&document.documentElement&&document.documentElement.style&&document.documentElement.style.WebkitAppearance||"undefined"!=typeof window&&window.console&&(window.console.firebug||window.console.exception&&window.console.table)||"undefined"!=typeof navigator&&navigator.userAgent&&navigator.userAgent.toLowerCase().match(/firefox\/(\d+)/)&&parseInt(RegExp.$1,10)>=31||"undefined"!=typeof navigator&&navigator.userAgent&&navigator.userAgent.toLowerCase().match(/applewebkit\/(\d+)/)}function f(a){var c=this.useColors;if(a[0]=(c?"%c":"")+this.namespace+(c?" %c":" ")+a[0]+(c?"%c ":" ")+"+"+b.humanize(this.diff),c){var d="color: "+this.color;a.splice(1,0,d,"color: inherit");var e=0,f=0;a[0].replace(/%[a-zA-Z%]/g,function(a){"%%"!==a&&(e++,"%c"===a&&(f=e))}),a.splice(f,0,d)}}function g(){return"object"==typeof console&&console.log&&Function.prototype.apply.call(console.log,console,arguments)}function h(a){try{null==a?b.storage.removeItem("debug"):b.storage.debug=a}catch(c){}}function i(){var a;try{a=b.storage.debug}catch(c){}return!a&&"undefined"!=typeof d&&"env"in d&&(a=d.env.DEBUG),a}function j(){try{return window.localStorage}catch(a){}}b=a.exports=c(5),b.log=g,b.formatArgs=f,b.save=h,b.load=i,b.useColors=e,b.storage="undefined"!=typeof chrome&&"undefined"!=typeof chrome.storage?chrome.storage.local:j(),b.colors=["lightseagreen","forestgreen","goldenrod","dodgerblue","darkorchid","crimson"],b.formatters.j=function(a){try{return JSON.stringify(a)}catch(b){return"[UnexpectedJSONParseError]: "+b.message}},b.enable(i())}).call(b,c(4))},function(a,b){function c(){throw new Error("setTimeout has not been defined")}function d(){throw new Error("clearTimeout has not been defined")}function e(a){if(k===setTimeout)return setTimeout(a,0);if((k===c||!k)&&setTimeout)return k=setTimeout,setTimeout(a,0);try{return k(a,0)}catch(b){try{return k.call(null,a,0)}catch(b){return k.call(this,a,0)}}}function f(a){if(l===clearTimeout)return clearTimeout(a);if((l===d||!l)&&clearTimeout)return l=clearTimeout,clearTimeout(a);try{return l(a)}catch(b){try{return l.call(null,a)}catch(b){return l.call(this,a)}}}function g(){p&&n&&(p=!1,n.length?o=n.concat(o):q=-1,o.length&&h())}function h(){if(!p){var a=e(g);p=!0;for(var b=o.length;b;){for(n=o,o=[];++q<b;)n&&n[q].run();q=-1,b=o.length}n=null,p=!1,f(a)}}function i(a,b){this.fun=a,this.array=b}function j(){}var k,l,m=a.exports={};!function(){try{k="function"==typeof setTimeout?setTimeout:c}catch(a){k=c}try{l="function"==typeof clearTimeout?clearTimeout:d}catch(a){l=d}}();var n,o=[],p=!1,q=-1;m.nextTick=function(a){var b=new Array(arguments.length-1);if(arguments.length>1)for(var c=1;c<arguments.length;c++)b[c-1]=arguments[c];o.push(new i(a,b)),1!==o.length||p||e(h)},i.prototype.run=function(){this.fun.apply(null,this.array)},m.title="browser",m.browser=!0,m.env={},m.argv=[],m.version="",m.versions={},m.on=j,m.addListener=j,m.once=j,m.off=j,m.removeListener=j,m.removeAllListeners=j,m.emit=j,m.prependListener=j,m.prependOnceListener=j,m.listeners=function(a){return[]},m.binding=function(a){throw new Error("process.binding is not supported")},m.cwd=function(){return"/"},m.chdir=function(a){throw new Error("process.chdir is not supported")},m.umask=function(){return 0}},function(a,b,c){function d(a){var c,d=0;for(c in a)d=(d<<5)-d+a.charCodeAt(c),d|=0;return b.colors[Math.abs(d)%b.colors.length]}function e(a){function c(){if(c.enabled){var a=c,d=+new Date,e=d-(j||d);a.diff=e,a.prev=j,a.curr=d,j=d;for(var f=new Array(arguments.length),g=0;g<f.length;g++)f[g]=arguments[g];f[0]=b.coerce(f[0]),"string"!=typeof f[0]&&f.unshift("%O");var h=0;f[0]=f[0].replace(/%([a-zA-Z%])/g,function(c,d){if("%%"===c)return c;h++;var e=b.formatters[d];if("function"==typeof e){var g=f[h];c=e.call(a,g),f.splice(h,1),h--}return c}),b.formatArgs.call(a,f);var i=c.log||b.log||console.log.bind(console);i.apply(a,f)}}return c.namespace=a,c.enabled=b.enabled(a),c.useColors=b.useColors(),c.color=d(a),"function"==typeof b.init&&b.init(c),c}function f(a){b.save(a),b.names=[],b.skips=[];for(var c=("string"==typeof a?a:"").split(/[\s,]+/),d=c.length,e=0;e<d;e++)c[e]&&(a=c[e].replace(/\*/g,".*?"),"-"===a[0]?b.skips.push(new RegExp("^"+a.substr(1)+"$")):b.names.push(new RegExp("^"+a+"$")))}function g(){b.enable("")}function h(a){var c,d;for(c=0,d=b.skips.length;c<d;c++)if(b.skips[c].test(a))return!1;for(c=0,d=b.names.length;c<d;c++)if(b.names[c].test(a))return!0;return!1}function i(a){return a instanceof Error?a.stack||a.message:a}b=a.exports=e.debug=e["default"]=e,b.coerce=i,b.disable=g,b.enable=f,b.enabled=h,b.humanize=c(6),b.names=[],b.skips=[],b.formatters={};var j},function(a,b){function c(a){if(a=String(a),!(a.length>100)){var b=/^((?:\d+)?\.?\d+) *(milliseconds?|msecs?|ms|seconds?|secs?|s|minutes?|mins?|m|hours?|hrs?|h|days?|d|years?|yrs?|y)?$/i.exec(a);if(b){var c=parseFloat(b[1]),d=(b[2]||"ms").toLowerCase();switch(d){case"years":case"year":case"yrs":case"yr":case"y":return c*k;case"days":case"day":case"d":return c*j;case"hours":case"hour":case"hrs":case"hr":case"h":return c*i;case"minutes":case"minute":case"mins":case"min":case"m":return c*h;case"seconds":case"second":case"secs":case"sec":case"s":return c*g;case"milliseconds":case"millisecond":case"msecs":case"msec":case"ms":return c;default:return}}}}function d(a){return a>=j?Math.round(a/j)+"d":a>=i?Math.round(a/i)+"h":a>=h?Math.round(a/h)+"m":a>=g?Math.round(a/g)+"s":a+"ms"}function e(a){return f(a,j,"day")||f(a,i,"hour")||f(a,h,"minute")||f(a,g,"second")||a+" ms"}function f(a,b,c){if(!(a<b))return a<1.5*b?Math.floor(a/b)+" "+c:Math.ceil(a/b)+" "+c+"s"}var g=1e3,h=60*g,i=60*h,j=24*i,k=365.25*j;a.exports=function(a,b){b=b||{};var f=typeof a;if("string"===f&&a.length>0)return c(a);if("number"===f&&isNaN(a)===!1)return b["long"]?e(a):d(a);throw new Error("val is not a non-empty string or a valid number. val="+JSON.stringify(a))}},function(a,b,c){function d(){}function e(a){var c=""+a.type;return b.BINARY_EVENT!==a.type&&b.BINARY_ACK!==a.type||(c+=a.attachments+"-"),a.nsp&&"/"!==a.nsp&&(c+=a.nsp+","),null!=a.id&&(c+=a.id),null!=a.data&&(c+=JSON.stringify(a.data)),l("encoded %j as %s",a,c),c}function f(a,b){function c(a){var c=o.deconstructPacket(a),d=e(c.packet),f=c.buffers;f.unshift(d),b(f)}o.removeBlobs(a,c)}function g(){this.reconstructor=null}function h(a){var c=0,d={type:Number(a.charAt(0))};if(null==b.types[d.type])return k();if(b.BINARY_EVENT===d.type||b.BINARY_ACK===d.type){for(var e="";"-"!==a.charAt(++c)&&(e+=a.charAt(c),c!=a.length););if(e!=Number(e)||"-"!==a.charAt(c))throw new Error("Illegal attachments");d.attachments=Number(e)}if("/"===a.charAt(c+1))for(d.nsp="";++c;){var f=a.charAt(c);if(","===f)break;if(d.nsp+=f,c===a.length)break}else d.nsp="/";var g=a.charAt(c+1);if(""!==g&&Number(g)==g){for(d.id="";++c;){var f=a.charAt(c);if(null==f||Number(f)!=f){--c;break}if(d.id+=a.charAt(c),c===a.length)break}d.id=Number(d.id)}return a.charAt(++c)&&(d=i(d,a.substr(c))),l("decoded %s as %j",a,d),d}function i(a,b){try{a.data=JSON.parse(b)}catch(c){return k()}return a}function j(a){this.reconPack=a,this.buffers=[]}function k(){return{type:b.ERROR,data:"parser error"}}var l=c(3)("socket.io-parser"),m=c(8),n=c(9),o=c(11),p=c(12);b.protocol=4,b.types=["CONNECT","DISCONNECT","EVENT","ACK","ERROR","BINARY_EVENT","BINARY_ACK"],b.CONNECT=0,b.DISCONNECT=1,b.EVENT=2,b.ACK=3,b.ERROR=4,b.BINARY_EVENT=5,b.BINARY_ACK=6,b.Encoder=d,b.Decoder=g,d.prototype.encode=function(a,c){if(a.type!==b.EVENT&&a.type!==b.ACK||!n(a.data)||(a.type=a.type===b.EVENT?b.BINARY_EVENT:b.BINARY_ACK),l("encoding packet %j",a),b.BINARY_EVENT===a.type||b.BINARY_ACK===a.type)f(a,c);else{var d=e(a);c([d])}},m(g.prototype),g.prototype.add=function(a){var c;if("string"==typeof a)c=h(a),b.BINARY_EVENT===c.type||b.BINARY_ACK===c.type?(this.reconstructor=new j(c),0===this.reconstructor.reconPack.attachments&&this.emit("decoded",c)):this.emit("decoded",c);else{if(!p(a)&&!a.base64)throw new Error("Unknown type: "+a);if(!this.reconstructor)throw new Error("got binary data when not reconstructing a packet");c=this.reconstructor.takeBinaryData(a),c&&(this.reconstructor=null,this.emit("decoded",c))}},g.prototype.destroy=function(){this.reconstructor&&this.reconstructor.finishedReconstruction()},j.prototype.takeBinaryData=function(a){if(this.buffers.push(a),this.buffers.length===this.reconPack.attachments){var b=o.reconstructPacket(this.reconPack,this.buffers);return this.finishedReconstruction(),b}return null},j.prototype.finishedReconstruction=function(){this.reconPack=null,this.buffers=[]}},function(a,b,c){function d(a){if(a)return e(a)}function e(a){for(var b in d.prototype)a[b]=d.prototype[b];return a}a.exports=d,d.prototype.on=d.prototype.addEventListener=function(a,b){return this._callbacks=this._callbacks||{},(this._callbacks["$"+a]=this._callbacks["$"+a]||[]).push(b),this},d.prototype.once=function(a,b){function c(){this.off(a,c),b.apply(this,arguments)}return c.fn=b,this.on(a,c),this},d.prototype.off=d.prototype.removeListener=d.prototype.removeAllListeners=d.prototype.removeEventListener=function(a,b){if(this._callbacks=this._callbacks||{},0==arguments.length)return this._callbacks={},this;var c=this._callbacks["$"+a];if(!c)return this;if(1==arguments.length)return delete this._callbacks["$"+a],this;for(var d,e=0;e<c.length;e++)if(d=c[e],d===b||d.fn===b){c.splice(e,1);break}return this},d.prototype.emit=function(a){this._callbacks=this._callbacks||{};var b=[].slice.call(arguments,1),c=this._callbacks["$"+a];if(c){c=c.slice(0);for(var d=0,e=c.length;d<e;++d)c[d].apply(this,b)}return this},d.prototype.listeners=function(a){return this._callbacks=this._callbacks||{},this._callbacks["$"+a]||[]},d.prototype.hasListeners=function(a){return!!this.listeners(a).length}},function(a,b,c){(function(b){function d(a){if(!a||"object"!=typeof a)return!1;if(e(a)){for(var c=0,f=a.length;c<f;c++)if(d(a[c]))return!0;return!1}if("function"==typeof b.Buffer&&b.Buffer.isBuffer&&b.Buffer.isBuffer(a)||"function"==typeof b.ArrayBuffer&&a instanceof ArrayBuffer||g&&a instanceof Blob||h&&a instanceof File)return!0;if(a.toJSON&&"function"==typeof a.toJSON&&1===arguments.length)return d(a.toJSON(),!0);for(var i in a)if(Object.prototype.hasOwnProperty.call(a,i)&&d(a[i]))return!0;return!1}var e=c(10),f=Object.prototype.toString,g="function"==typeof b.Blob||"[object BlobConstructor]"===f.call(b.Blob),h="function"==typeof b.File||"[object FileConstructor]"===f.call(b.File);a.exports=d}).call(b,function(){return this}())},function(a,b){var c={}.toString;a.exports=Array.isArray||function(a){return"[object Array]"==c.call(a)}},function(a,b,c){(function(a){function d(a,b){if(!a)return a;if(g(a)){var c={_placeholder:!0,num:b.length};return b.push(a),c}if(f(a)){for(var e=new Array(a.length),h=0;h<a.length;h++)e[h]=d(a[h],b);return e}if("object"==typeof a&&!(a instanceof Date)){var e={};for(var i in a)e[i]=d(a[i],b);return e}return a}function e(a,b){if(!a)return a;if(a&&a._placeholder)return b[a.num];if(f(a))for(var c=0;c<a.length;c++)a[c]=e(a[c],b);else if("object"==typeof a)for(var d in a)a[d]=e(a[d],b);return a}var f=c(10),g=c(12),h=Object.prototype.toString,i="function"==typeof a.Blob||"[object BlobConstructor]"===h.call(a.Blob),j="function"==typeof a.File||"[object FileConstructor]"===h.call(a.File);b.deconstructPacket=function(a){var b=[],c=a.data,e=a;return e.data=d(c,b),e.attachments=b.length,{packet:e,buffers:b}},b.reconstructPacket=function(a,b){return a.data=e(a.data,b),a.attachments=void 0,a},b.removeBlobs=function(a,b){function c(a,h,k){if(!a)return a;if(i&&a instanceof Blob||j&&a instanceof File){d++;var l=new FileReader;l.onload=function(){k?k[h]=this.result:e=this.result,--d||b(e)},l.readAsArrayBuffer(a)}else if(f(a))for(var m=0;m<a.length;m++)c(a[m],m,a);else if("object"==typeof a&&!g(a))for(var n in a)c(a[n],n,a)}var d=0,e=a;c(e),d||b(e)}}).call(b,function(){return this}())},function(a,b){(function(b){function c(a){return b.Buffer&&b.Buffer.isBuffer(a)||b.ArrayBuffer&&a instanceof ArrayBuffer}a.exports=c}).call(b,function(){return this}())},function(a,b,c){"use strict";function d(a,b){if(!(this instanceof d))return new d(a,b);a&&"object"===("undefined"==typeof a?"undefined":e(a))&&(b=a,a=void 0),b=b||{},b.path=b.path||"/socket.io",this.nsps={},this.subs=[],this.opts=b,this.reconnection(b.reconnection!==!1),this.reconnectionAttempts(b.reconnectionAttempts||1/0),this.reconnectionDelay(b.reconnectionDelay||1e3),this.reconnectionDelayMax(b.reconnectionDelayMax||5e3),this.randomizationFactor(b.randomizationFactor||.5),this.backoff=new n({min:this.reconnectionDelay(),max:this.reconnectionDelayMax(),jitter:this.randomizationFactor()}),this.timeout(null==b.timeout?2e4:b.timeout),this.readyState="closed",this.uri=a,this.connecting=[],this.lastPing=null,this.encoding=!1,this.packetBuffer=[];var c=b.parser||i;this.encoder=new c.Encoder,this.decoder=new c.Decoder,this.autoConnect=b.autoConnect!==!1,this.autoConnect&&this.open()}var e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&"function"==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?"symbol":typeof a},f=c(14),g=c(39),h=c(8),i=c(7),j=c(41),k=c(42),l=c(3)("socket.io-client:manager"),m=c(37),n=c(43),o=Object.prototype.hasOwnProperty;a.exports=d,d.prototype.emitAll=function(){this.emit.apply(this,arguments);for(var a in this.nsps)o.call(this.nsps,a)&&this.nsps[a].emit.apply(this.nsps[a],arguments)},d.prototype.updateSocketIds=function(){for(var a in this.nsps)o.call(this.nsps,a)&&(this.nsps[a].id=this.generateId(a))},d.prototype.generateId=function(a){return("/"===a?"":a+"#")+this.engine.id},h(d.prototype),d.prototype.reconnection=function(a){return arguments.length?(this._reconnection=!!a,this):this._reconnection},d.prototype.reconnectionAttempts=function(a){return arguments.length?(this._reconnectionAttempts=a,this):this._reconnectionAttempts},d.prototype.reconnectionDelay=function(a){return arguments.length?(this._reconnectionDelay=a,this.backoff&&this.backoff.setMin(a),this):this._reconnectionDelay},d.prototype.randomizationFactor=function(a){return arguments.length?(this._randomizationFactor=a,this.backoff&&this.backoff.setJitter(a),this):this._randomizationFactor},d.prototype.reconnectionDelayMax=function(a){return arguments.length?(this._reconnectionDelayMax=a,this.backoff&&this.backoff.setMax(a),this):this._reconnectionDelayMax},d.prototype.timeout=function(a){return arguments.length?(this._timeout=a,this):this._timeout},d.prototype.maybeReconnectOnOpen=function(){!this.reconnecting&&this._reconnection&&0===this.backoff.attempts&&this.reconnect()},d.prototype.open=d.prototype.connect=function(a,b){if(l("readyState %s",this.readyState),~this.readyState.indexOf("open"))return this;l("opening %s",this.uri),this.engine=f(this.uri,this.opts);var c=this.engine,d=this;this.readyState="opening",this.skipReconnect=!1;var e=j(c,"open",function(){d.onopen(),a&&a()}),g=j(c,"error",function(b){if(l("connect_error"),d.cleanup(),d.readyState="closed",d.emitAll("connect_error",b),a){var c=new Error("Connection error");c.data=b,a(c)}else d.maybeReconnectOnOpen()});if(!1!==this._timeout){var h=this._timeout;l("connect attempt will timeout after %d",h);var i=setTimeout(function(){l("connect attempt timed out after %d",h),e.destroy(),c.close(),c.emit("error","timeout"),d.emitAll("connect_timeout",h)},h);this.subs.push({destroy:function(){clearTimeout(i)}})}return this.subs.push(e),this.subs.push(g),this},d.prototype.onopen=function(){l("open"),this.cleanup(),this.readyState="open",this.emit("open");var a=this.engine;this.subs.push(j(a,"data",k(this,"ondata"))),this.subs.push(j(a,"ping",k(this,"onping"))),this.subs.push(j(a,"pong",k(this,"onpong"))),this.subs.push(j(a,"error",k(this,"onerror"))),this.subs.push(j(a,"close",k(this,"onclose"))),this.subs.push(j(this.decoder,"decoded",k(this,"ondecoded")))},d.prototype.onping=function(){this.lastPing=new Date,this.emitAll("ping")},d.prototype.onpong=function(){this.emitAll("pong",new Date-this.lastPing)},d.prototype.ondata=function(a){this.decoder.add(a)},d.prototype.ondecoded=function(a){this.emit("packet",a)},d.prototype.onerror=function(a){l("error",a),this.emitAll("error",a)},d.prototype.socket=function(a,b){function c(){~m(e.connecting,d)||e.connecting.push(d)}var d=this.nsps[a];if(!d){d=new g(this,a,b),this.nsps[a]=d;var e=this;d.on("connecting",c),d.on("connect",function(){d.id=e.generateId(a)}),this.autoConnect&&c()}return d},d.prototype.destroy=function(a){var b=m(this.connecting,a);~b&&this.connecting.splice(b,1),this.connecting.length||this.close()},d.prototype.packet=function(a){l("writing packet %j",a);var b=this;a.query&&0===a.type&&(a.nsp+="?"+a.query),b.encoding?b.packetBuffer.push(a):(b.encoding=!0,this.encoder.encode(a,function(c){for(var d=0;d<c.length;d++)b.engine.write(c[d],a.options);b.encoding=!1,b.processPacketQueue()}))},d.prototype.processPacketQueue=function(){if(this.packetBuffer.length>0&&!this.encoding){var a=this.packetBuffer.shift();this.packet(a)}},d.prototype.cleanup=function(){l("cleanup");for(var a=this.subs.length,b=0;b<a;b++){var c=this.subs.shift();c.destroy()}this.packetBuffer=[],this.encoding=!1,this.lastPing=null,this.decoder.destroy()},d.prototype.close=d.prototype.disconnect=function(){l("disconnect"),this.skipReconnect=!0,this.reconnecting=!1,"opening"===this.readyState&&this.cleanup(),this.backoff.reset(),this.readyState="closed",this.engine&&this.engine.close()},d.prototype.onclose=function(a){l("onclose"),this.cleanup(),this.backoff.reset(),this.readyState="closed",this.emit("close",a),this._reconnection&&!this.skipReconnect&&this.reconnect()},d.prototype.reconnect=function(){if(this.reconnecting||this.skipReconnect)return this;var a=this;if(this.backoff.attempts>=this._reconnectionAttempts)l("reconnect failed"),this.backoff.reset(),this.emitAll("reconnect_failed"),this.reconnecting=!1;else{var b=this.backoff.duration();l("will wait %dms before reconnect attempt",b),this.reconnecting=!0;var c=setTimeout(function(){a.skipReconnect||(l("attempting reconnect"),a.emitAll("reconnect_attempt",a.backoff.attempts),a.emitAll("reconnecting",a.backoff.attempts),a.skipReconnect||a.open(function(b){b?(l("reconnect attempt error"),a.reconnecting=!1,a.reconnect(),a.emitAll("reconnect_error",b.data)):(l("reconnect success"),a.onreconnect())}))},b);this.subs.push({destroy:function(){clearTimeout(c)}})}},d.prototype.onreconnect=function(){var a=this.backoff.attempts;this.reconnecting=!1,this.backoff.reset(),this.updateSocketIds(),this.emitAll("reconnect",a)}},function(a,b,c){a.exports=c(15)},function(a,b,c){a.exports=c(16),a.exports.parser=c(23)},function(a,b,c){(function(b){function d(a,c){if(!(this instanceof d))return new d(a,c);c=c||{},a&&"object"==typeof a&&(c=a,a=null),a?(a=k(a),c.hostname=a.host,c.secure="https"===a.protocol||"wss"===a.protocol,c.port=a.port,a.query&&(c.query=a.query)):c.host&&(c.hostname=k(c.host).host),this.secure=null!=c.secure?c.secure:b.location&&"https:"===location.protocol,c.hostname&&!c.port&&(c.port=this.secure?"443":"80"),this.agent=c.agent||!1,this.hostname=c.hostname||(b.location?location.hostname:"localhost"),this.port=c.port||(b.location&&location.port?location.port:this.secure?443:80),this.query=c.query||{},"string"==typeof this.query&&(this.query=m.decode(this.query)),this.upgrade=!1!==c.upgrade,this.path=(c.path||"/engine.io").replace(/\/$/,"")+"/",this.forceJSONP=!!c.forceJSONP,this.jsonp=!1!==c.jsonp,this.forceBase64=!!c.forceBase64,this.enablesXDR=!!c.enablesXDR,this.timestampParam=c.timestampParam||"t",this.timestampRequests=c.timestampRequests,this.transports=c.transports||["polling","websocket"],this.transportOptions=c.transportOptions||{},this.readyState="",this.writeBuffer=[],this.prevBufferLen=0,this.policyPort=c.policyPort||843,this.rememberUpgrade=c.rememberUpgrade||!1,this.binaryType=null,this.onlyBinaryUpgrades=c.onlyBinaryUpgrades,this.perMessageDeflate=!1!==c.perMessageDeflate&&(c.perMessageDeflate||{}),!0===this.perMessageDeflate&&(this.perMessageDeflate={}),this.perMessageDeflate&&null==this.perMessageDeflate.threshold&&(this.perMessageDeflate.threshold=1024),this.pfx=c.pfx||null,this.key=c.key||null,this.passphrase=c.passphrase||null,this.cert=c.cert||null,this.ca=c.ca||null,this.ciphers=c.ciphers||null,this.rejectUnauthorized=void 0===c.rejectUnauthorized||c.rejectUnauthorized,this.forceNode=!!c.forceNode;var e="object"==typeof b&&b;e.global===e&&(c.extraHeaders&&Object.keys(c.extraHeaders).length>0&&(this.extraHeaders=c.extraHeaders),c.localAddress&&(this.localAddress=c.localAddress)),this.id=null,this.upgrades=null,this.pingInterval=null,this.pingTimeout=null,this.pingIntervalTimer=null,this.pingTimeoutTimer=null,this.open()}function e(a){var b={};for(var c in a)a.hasOwnProperty(c)&&(b[c]=a[c]);return b}var f=c(17),g=c(8),h=c(3)("engine.io-client:socket"),i=c(37),j=c(23),k=c(2),l=c(38),m=c(31);a.exports=d,d.priorWebsocketSuccess=!1,g(d.prototype),d.protocol=j.protocol,d.Socket=d,d.Transport=c(22),d.transports=c(17),d.parser=c(23),d.prototype.createTransport=function(a){h('creating transport "%s"',a);var b=e(this.query);b.EIO=j.protocol,b.transport=a;var c=this.transportOptions[a]||{};this.id&&(b.sid=this.id);var d=new f[a]({query:b,socket:this,agent:c.agent||this.agent,hostname:c.hostname||this.hostname,port:c.port||this.port,secure:c.secure||this.secure,path:c.path||this.path,forceJSONP:c.forceJSONP||this.forceJSONP,jsonp:c.jsonp||this.jsonp,forceBase64:c.forceBase64||this.forceBase64,enablesXDR:c.enablesXDR||this.enablesXDR,timestampRequests:c.timestampRequests||this.timestampRequests,timestampParam:c.timestampParam||this.timestampParam,policyPort:c.policyPort||this.policyPort,pfx:c.pfx||this.pfx,key:c.key||this.key,passphrase:c.passphrase||this.passphrase,cert:c.cert||this.cert,ca:c.ca||this.ca,ciphers:c.ciphers||this.ciphers,rejectUnauthorized:c.rejectUnauthorized||this.rejectUnauthorized,perMessageDeflate:c.perMessageDeflate||this.perMessageDeflate,extraHeaders:c.extraHeaders||this.extraHeaders,forceNode:c.forceNode||this.forceNode,localAddress:c.localAddress||this.localAddress,requestTimeout:c.requestTimeout||this.requestTimeout,protocols:c.protocols||void 0});return d},d.prototype.open=function(){var a;if(this.rememberUpgrade&&d.priorWebsocketSuccess&&this.transports.indexOf("websocket")!==-1)a="websocket";else{if(0===this.transports.length){var b=this;return void setTimeout(function(){b.emit("error","No transports available")},0)}a=this.transports[0]}this.readyState="opening";try{a=this.createTransport(a)}catch(c){return this.transports.shift(),void this.open()}a.open(),this.setTransport(a)},d.prototype.setTransport=function(a){h("setting transport %s",a.name);var b=this;this.transport&&(h("clearing existing transport %s",this.transport.name),this.transport.removeAllListeners()),this.transport=a,a.on("drain",function(){b.onDrain()}).on("packet",function(a){b.onPacket(a)}).on("error",function(a){b.onError(a)}).on("close",function(){b.onClose("transport close")})},d.prototype.probe=function(a){function b(){if(m.onlyBinaryUpgrades){var b=!this.supportsBinary&&m.transport.supportsBinary;l=l||b}l||(h('probe transport "%s" opened',a),k.send([{type:"ping",data:"probe"}]),k.once("packet",function(b){if(!l)if("pong"===b.type&&"probe"===b.data){if(h('probe transport "%s" pong',a),m.upgrading=!0,m.emit("upgrading",k),!k)return;d.priorWebsocketSuccess="websocket"===k.name,h('pausing current transport "%s"',m.transport.name),m.transport.pause(function(){l||"closed"!==m.readyState&&(h("changing transport and sending upgrade packet"),j(),m.setTransport(k),k.send([{type:"upgrade"}]),m.emit("upgrade",k),k=null,m.upgrading=!1,m.flush())})}else{h('probe transport "%s" failed',a);var c=new Error("probe error");c.transport=k.name,m.emit("upgradeError",c)}}))}function c(){l||(l=!0,j(),k.close(),k=null)}function e(b){var d=new Error("probe error: "+b);d.transport=k.name,c(),h('probe transport "%s" failed because of error: %s',a,b),m.emit("upgradeError",d)}function f(){e("transport closed")}function g(){e("socket closed")}function i(a){k&&a.name!==k.name&&(h('"%s" works - aborting "%s"',a.name,k.name),c())}function j(){k.removeListener("open",b),k.removeListener("error",e),k.removeListener("close",f),m.removeListener("close",g),m.removeListener("upgrading",i)}h('probing transport "%s"',a);var k=this.createTransport(a,{probe:1}),l=!1,m=this;d.priorWebsocketSuccess=!1,k.once("open",b),k.once("error",e),k.once("close",f),this.once("close",g),this.once("upgrading",i),k.open()},d.prototype.onOpen=function(){if(h("socket open"),this.readyState="open",d.priorWebsocketSuccess="websocket"===this.transport.name,this.emit("open"),this.flush(),"open"===this.readyState&&this.upgrade&&this.transport.pause){h("starting upgrade probes");for(var a=0,b=this.upgrades.length;a<b;a++)this.probe(this.upgrades[a])}},d.prototype.onPacket=function(a){if("opening"===this.readyState||"open"===this.readyState||"closing"===this.readyState)switch(h('socket receive: type "%s", data "%s"',a.type,a.data),this.emit("packet",a),this.emit("heartbeat"),a.type){case"open":this.onHandshake(l(a.data));break;case"pong":this.setPing(),this.emit("pong");break;case"error":var b=new Error("server error");b.code=a.data,this.onError(b);break;case"message":this.emit("data",a.data),this.emit("message",a.data)}else h('packet received with socket readyState "%s"',this.readyState)},d.prototype.onHandshake=function(a){this.emit("handshake",a),this.id=a.sid,this.transport.query.sid=a.sid,this.upgrades=this.filterUpgrades(a.upgrades),this.pingInterval=a.pingInterval,this.pingTimeout=a.pingTimeout,this.onOpen(),"closed"!==this.readyState&&(this.setPing(),this.removeListener("heartbeat",this.onHeartbeat),this.on("heartbeat",this.onHeartbeat))},d.prototype.onHeartbeat=function(a){clearTimeout(this.pingTimeoutTimer);var b=this;b.pingTimeoutTimer=setTimeout(function(){"closed"!==b.readyState&&b.onClose("ping timeout")},a||b.pingInterval+b.pingTimeout)},d.prototype.setPing=function(){var a=this;clearTimeout(a.pingIntervalTimer),a.pingIntervalTimer=setTimeout(function(){h("writing ping packet - expecting pong within %sms",a.pingTimeout),a.ping(),a.onHeartbeat(a.pingTimeout)},a.pingInterval)},d.prototype.ping=function(){var a=this;this.sendPacket("ping",function(){a.emit("ping")})},d.prototype.onDrain=function(){this.writeBuffer.splice(0,this.prevBufferLen),this.prevBufferLen=0,0===this.writeBuffer.length?this.emit("drain"):this.flush()},d.prototype.flush=function(){"closed"!==this.readyState&&this.transport.writable&&!this.upgrading&&this.writeBuffer.length&&(h("flushing %d packets in socket",this.writeBuffer.length),this.transport.send(this.writeBuffer),this.prevBufferLen=this.writeBuffer.length,this.emit("flush"))},d.prototype.write=d.prototype.send=function(a,b,c){return this.sendPacket("message",a,b,c),this},d.prototype.sendPacket=function(a,b,c,d){if("function"==typeof b&&(d=b,b=void 0),"function"==typeof c&&(d=c,c=null),"closing"!==this.readyState&&"closed"!==this.readyState){c=c||{},c.compress=!1!==c.compress;var e={type:a,data:b,options:c};this.emit("packetCreate",e),this.writeBuffer.push(e),d&&this.once("flush",d),this.flush()}},d.prototype.close=function(){function a(){d.onClose("forced close"),h("socket closing - telling transport to close"),d.transport.close()}function b(){d.removeListener("upgrade",b),d.removeListener("upgradeError",b),a()}function c(){d.once("upgrade",b),d.once("upgradeError",b)}if("opening"===this.readyState||"open"===this.readyState){this.readyState="closing";var d=this;this.writeBuffer.length?this.once("drain",function(){this.upgrading?c():a()}):this.upgrading?c():a()}return this},d.prototype.onError=function(a){h("socket error %j",a),d.priorWebsocketSuccess=!1,this.emit("error",a),this.onClose("transport error",a)},d.prototype.onClose=function(a,b){if("opening"===this.readyState||"open"===this.readyState||"closing"===this.readyState){h('socket close with reason: "%s"',a);var c=this;clearTimeout(this.pingIntervalTimer),clearTimeout(this.pingTimeoutTimer),this.transport.removeAllListeners("close"),this.transport.close(),this.transport.removeAllListeners(),this.readyState="closed",this.id=null,this.emit("close",a,b),c.writeBuffer=[],c.prevBufferLen=0}},d.prototype.filterUpgrades=function(a){for(var b=[],c=0,d=a.length;c<d;c++)~i(this.transports,a[c])&&b.push(a[c]);return b}}).call(b,function(){return this}())},function(a,b,c){(function(a){function d(b){var c,d=!1,h=!1,i=!1!==b.jsonp;if(a.location){var j="https:"===location.protocol,k=location.port;k||(k=j?443:80),d=b.hostname!==location.hostname||k!==b.port,h=b.secure!==j}if(b.xdomain=d,b.xscheme=h,c=new e(b),"open"in c&&!b.forceJSONP)return new f(b);if(!i)throw new Error("JSONP disabled");return new g(b)}var e=c(18),f=c(20),g=c(34),h=c(35);b.polling=d,b.websocket=h}).call(b,function(){return this}())},function(a,b,c){(function(b){var d=c(19);a.exports=function(a){var c=a.xdomain,e=a.xscheme,f=a.enablesXDR;try{if("undefined"!=typeof XMLHttpRequest&&(!c||d))return new XMLHttpRequest}catch(g){}try{if("undefined"!=typeof XDomainRequest&&!e&&f)return new XDomainRequest}catch(g){}if(!c)try{
return new(b[["Active"].concat("Object").join("X")])("Microsoft.XMLHTTP")}catch(g){}}}).call(b,function(){return this}())},function(a,b){try{a.exports="undefined"!=typeof XMLHttpRequest&&"withCredentials"in new XMLHttpRequest}catch(c){a.exports=!1}},function(a,b,c){(function(b){function d(){}function e(a){if(i.call(this,a),this.requestTimeout=a.requestTimeout,this.extraHeaders=a.extraHeaders,b.location){var c="https:"===location.protocol,d=location.port;d||(d=c?443:80),this.xd=a.hostname!==b.location.hostname||d!==a.port,this.xs=a.secure!==c}}function f(a){this.method=a.method||"GET",this.uri=a.uri,this.xd=!!a.xd,this.xs=!!a.xs,this.async=!1!==a.async,this.data=void 0!==a.data?a.data:null,this.agent=a.agent,this.isBinary=a.isBinary,this.supportsBinary=a.supportsBinary,this.enablesXDR=a.enablesXDR,this.requestTimeout=a.requestTimeout,this.pfx=a.pfx,this.key=a.key,this.passphrase=a.passphrase,this.cert=a.cert,this.ca=a.ca,this.ciphers=a.ciphers,this.rejectUnauthorized=a.rejectUnauthorized,this.extraHeaders=a.extraHeaders,this.create()}function g(){for(var a in f.requests)f.requests.hasOwnProperty(a)&&f.requests[a].abort()}var h=c(18),i=c(21),j=c(8),k=c(32),l=c(3)("engine.io-client:polling-xhr");a.exports=e,a.exports.Request=f,k(e,i),e.prototype.supportsBinary=!0,e.prototype.request=function(a){return a=a||{},a.uri=this.uri(),a.xd=this.xd,a.xs=this.xs,a.agent=this.agent||!1,a.supportsBinary=this.supportsBinary,a.enablesXDR=this.enablesXDR,a.pfx=this.pfx,a.key=this.key,a.passphrase=this.passphrase,a.cert=this.cert,a.ca=this.ca,a.ciphers=this.ciphers,a.rejectUnauthorized=this.rejectUnauthorized,a.requestTimeout=this.requestTimeout,a.extraHeaders=this.extraHeaders,new f(a)},e.prototype.doWrite=function(a,b){var c="string"!=typeof a&&void 0!==a,d=this.request({method:"POST",data:a,isBinary:c}),e=this;d.on("success",b),d.on("error",function(a){e.onError("xhr post error",a)}),this.sendXhr=d},e.prototype.doPoll=function(){l("xhr poll");var a=this.request(),b=this;a.on("data",function(a){b.onData(a)}),a.on("error",function(a){b.onError("xhr poll error",a)}),this.pollXhr=a},j(f.prototype),f.prototype.create=function(){var a={agent:this.agent,xdomain:this.xd,xscheme:this.xs,enablesXDR:this.enablesXDR};a.pfx=this.pfx,a.key=this.key,a.passphrase=this.passphrase,a.cert=this.cert,a.ca=this.ca,a.ciphers=this.ciphers,a.rejectUnauthorized=this.rejectUnauthorized;var c=this.xhr=new h(a),d=this;try{l("xhr open %s: %s",this.method,this.uri),c.open(this.method,this.uri,this.async);try{if(this.extraHeaders){c.setDisableHeaderCheck&&c.setDisableHeaderCheck(!0);for(var e in this.extraHeaders)this.extraHeaders.hasOwnProperty(e)&&c.setRequestHeader(e,this.extraHeaders[e])}}catch(g){}if("POST"===this.method)try{this.isBinary?c.setRequestHeader("Content-type","application/octet-stream"):c.setRequestHeader("Content-type","text/plain;charset=UTF-8")}catch(g){}try{c.setRequestHeader("Accept","*/*")}catch(g){}"withCredentials"in c&&(c.withCredentials=!0),this.requestTimeout&&(c.timeout=this.requestTimeout),this.hasXDR()?(c.onload=function(){d.onLoad()},c.onerror=function(){d.onError(c.responseText)}):c.onreadystatechange=function(){if(2===c.readyState){var a;try{a=c.getResponseHeader("Content-Type")}catch(b){}"application/octet-stream"===a&&(c.responseType="arraybuffer")}4===c.readyState&&(200===c.status||1223===c.status?d.onLoad():setTimeout(function(){d.onError(c.status)},0))},l("xhr data %s",this.data),c.send(this.data)}catch(g){return void setTimeout(function(){d.onError(g)},0)}b.document&&(this.index=f.requestsCount++,f.requests[this.index]=this)},f.prototype.onSuccess=function(){this.emit("success"),this.cleanup()},f.prototype.onData=function(a){this.emit("data",a),this.onSuccess()},f.prototype.onError=function(a){this.emit("error",a),this.cleanup(!0)},f.prototype.cleanup=function(a){if("undefined"!=typeof this.xhr&&null!==this.xhr){if(this.hasXDR()?this.xhr.onload=this.xhr.onerror=d:this.xhr.onreadystatechange=d,a)try{this.xhr.abort()}catch(c){}b.document&&delete f.requests[this.index],this.xhr=null}},f.prototype.onLoad=function(){var a;try{var b;try{b=this.xhr.getResponseHeader("Content-Type")}catch(c){}a="application/octet-stream"===b?this.xhr.response||this.xhr.responseText:this.xhr.responseText}catch(c){this.onError(c)}null!=a&&this.onData(a)},f.prototype.hasXDR=function(){return"undefined"!=typeof b.XDomainRequest&&!this.xs&&this.enablesXDR},f.prototype.abort=function(){this.cleanup()},f.requestsCount=0,f.requests={},b.document&&(b.attachEvent?b.attachEvent("onunload",g):b.addEventListener&&b.addEventListener("beforeunload",g,!1))}).call(b,function(){return this}())},function(a,b,c){function d(a){var b=a&&a.forceBase64;k&&!b||(this.supportsBinary=!1),e.call(this,a)}var e=c(22),f=c(31),g=c(23),h=c(32),i=c(33),j=c(3)("engine.io-client:polling");a.exports=d;var k=function(){var a=c(18),b=new a({xdomain:!1});return null!=b.responseType}();h(d,e),d.prototype.name="polling",d.prototype.doOpen=function(){this.poll()},d.prototype.pause=function(a){function b(){j("paused"),c.readyState="paused",a()}var c=this;if(this.readyState="pausing",this.polling||!this.writable){var d=0;this.polling&&(j("we are currently polling - waiting to pause"),d++,this.once("pollComplete",function(){j("pre-pause polling complete"),--d||b()})),this.writable||(j("we are currently writing - waiting to pause"),d++,this.once("drain",function(){j("pre-pause writing complete"),--d||b()}))}else b()},d.prototype.poll=function(){j("polling"),this.polling=!0,this.doPoll(),this.emit("poll")},d.prototype.onData=function(a){var b=this;j("polling got data %s",a);var c=function(a,c,d){return"opening"===b.readyState&&b.onOpen(),"close"===a.type?(b.onClose(),!1):void b.onPacket(a)};g.decodePayload(a,this.socket.binaryType,c),"closed"!==this.readyState&&(this.polling=!1,this.emit("pollComplete"),"open"===this.readyState?this.poll():j('ignoring poll - transport state "%s"',this.readyState))},d.prototype.doClose=function(){function a(){j("writing close packet"),b.write([{type:"close"}])}var b=this;"open"===this.readyState?(j("transport open - closing"),a()):(j("transport not open - deferring close"),this.once("open",a))},d.prototype.write=function(a){var b=this;this.writable=!1;var c=function(){b.writable=!0,b.emit("drain")};g.encodePayload(a,this.supportsBinary,function(a){b.doWrite(a,c)})},d.prototype.uri=function(){var a=this.query||{},b=this.secure?"https":"http",c="";!1!==this.timestampRequests&&(a[this.timestampParam]=i()),this.supportsBinary||a.sid||(a.b64=1),a=f.encode(a),this.port&&("https"===b&&443!==Number(this.port)||"http"===b&&80!==Number(this.port))&&(c=":"+this.port),a.length&&(a="?"+a);var d=this.hostname.indexOf(":")!==-1;return b+"://"+(d?"["+this.hostname+"]":this.hostname)+c+this.path+a}},function(a,b,c){function d(a){this.path=a.path,this.hostname=a.hostname,this.port=a.port,this.secure=a.secure,this.query=a.query,this.timestampParam=a.timestampParam,this.timestampRequests=a.timestampRequests,this.readyState="",this.agent=a.agent||!1,this.socket=a.socket,this.enablesXDR=a.enablesXDR,this.pfx=a.pfx,this.key=a.key,this.passphrase=a.passphrase,this.cert=a.cert,this.ca=a.ca,this.ciphers=a.ciphers,this.rejectUnauthorized=a.rejectUnauthorized,this.forceNode=a.forceNode,this.extraHeaders=a.extraHeaders,this.localAddress=a.localAddress}var e=c(23),f=c(8);a.exports=d,f(d.prototype),d.prototype.onError=function(a,b){var c=new Error(a);return c.type="TransportError",c.description=b,this.emit("error",c),this},d.prototype.open=function(){return"closed"!==this.readyState&&""!==this.readyState||(this.readyState="opening",this.doOpen()),this},d.prototype.close=function(){return"opening"!==this.readyState&&"open"!==this.readyState||(this.doClose(),this.onClose()),this},d.prototype.send=function(a){if("open"!==this.readyState)throw new Error("Transport not open");this.write(a)},d.prototype.onOpen=function(){this.readyState="open",this.writable=!0,this.emit("open")},d.prototype.onData=function(a){var b=e.decodePacket(a,this.socket.binaryType);this.onPacket(b)},d.prototype.onPacket=function(a){this.emit("packet",a)},d.prototype.onClose=function(){this.readyState="closed",this.emit("close")}},function(a,b,c){(function(a){function d(a,c){var d="b"+b.packets[a.type]+a.data.data;return c(d)}function e(a,c,d){if(!c)return b.encodeBase64Packet(a,d);var e=a.data,f=new Uint8Array(e),g=new Uint8Array(1+e.byteLength);g[0]=s[a.type];for(var h=0;h<f.length;h++)g[h+1]=f[h];return d(g.buffer)}function f(a,c,d){if(!c)return b.encodeBase64Packet(a,d);var e=new FileReader;return e.onload=function(){a.data=e.result,b.encodePacket(a,c,!0,d)},e.readAsArrayBuffer(a.data)}function g(a,c,d){if(!c)return b.encodeBase64Packet(a,d);if(r)return f(a,c,d);var e=new Uint8Array(1);e[0]=s[a.type];var g=new v([e.buffer,a.data]);return d(g)}function h(a){try{a=o.decode(a,{strict:!1})}catch(b){return!1}return a}function i(a,b,c){for(var d=new Array(a.length),e=n(a.length,c),f=function(a,c,e){b(c,function(b,c){d[a]=c,e(b,d)})},g=0;g<a.length;g++)f(g,a[g],e)}var j,k=c(24),l=c(9),m=c(25),n=c(26),o=c(27);a&&a.ArrayBuffer&&(j=c(29));var p="undefined"!=typeof navigator&&/Android/i.test(navigator.userAgent),q="undefined"!=typeof navigator&&/PhantomJS/i.test(navigator.userAgent),r=p||q;b.protocol=3;var s=b.packets={open:0,close:1,ping:2,pong:3,message:4,upgrade:5,noop:6},t=k(s),u={type:"error",data:"parser error"},v=c(30);b.encodePacket=function(b,c,f,h){"function"==typeof c&&(h=c,c=!1),"function"==typeof f&&(h=f,f=null);var i=void 0===b.data?void 0:b.data.buffer||b.data;if(a.ArrayBuffer&&i instanceof ArrayBuffer)return e(b,c,h);if(v&&i instanceof a.Blob)return g(b,c,h);if(i&&i.base64)return d(b,h);var j=s[b.type];return void 0!==b.data&&(j+=f?o.encode(String(b.data),{strict:!1}):String(b.data)),h(""+j)},b.encodeBase64Packet=function(c,d){var e="b"+b.packets[c.type];if(v&&c.data instanceof a.Blob){var f=new FileReader;return f.onload=function(){var a=f.result.split(",")[1];d(e+a)},f.readAsDataURL(c.data)}var g;try{g=String.fromCharCode.apply(null,new Uint8Array(c.data))}catch(h){for(var i=new Uint8Array(c.data),j=new Array(i.length),k=0;k<i.length;k++)j[k]=i[k];g=String.fromCharCode.apply(null,j)}return e+=a.btoa(g),d(e)},b.decodePacket=function(a,c,d){if(void 0===a)return u;if("string"==typeof a){if("b"===a.charAt(0))return b.decodeBase64Packet(a.substr(1),c);if(d&&(a=h(a),a===!1))return u;var e=a.charAt(0);return Number(e)==e&&t[e]?a.length>1?{type:t[e],data:a.substring(1)}:{type:t[e]}:u}var f=new Uint8Array(a),e=f[0],g=m(a,1);return v&&"blob"===c&&(g=new v([g])),{type:t[e],data:g}},b.decodeBase64Packet=function(a,b){var c=t[a.charAt(0)];if(!j)return{type:c,data:{base64:!0,data:a.substr(1)}};var d=j.decode(a.substr(1));return"blob"===b&&v&&(d=new v([d])),{type:c,data:d}},b.encodePayload=function(a,c,d){function e(a){return a.length+":"+a}function f(a,d){b.encodePacket(a,!!g&&c,!1,function(a){d(null,e(a))})}"function"==typeof c&&(d=c,c=null);var g=l(a);return c&&g?v&&!r?b.encodePayloadAsBlob(a,d):b.encodePayloadAsArrayBuffer(a,d):a.length?void i(a,f,function(a,b){return d(b.join(""))}):d("0:")},b.decodePayload=function(a,c,d){if("string"!=typeof a)return b.decodePayloadAsBinary(a,c,d);"function"==typeof c&&(d=c,c=null);var e;if(""===a)return d(u,0,1);for(var f,g,h="",i=0,j=a.length;i<j;i++){var k=a.charAt(i);if(":"===k){if(""===h||h!=(f=Number(h)))return d(u,0,1);if(g=a.substr(i+1,f),h!=g.length)return d(u,0,1);if(g.length){if(e=b.decodePacket(g,c,!1),u.type===e.type&&u.data===e.data)return d(u,0,1);var l=d(e,i+f,j);if(!1===l)return}i+=f,h=""}else h+=k}return""!==h?d(u,0,1):void 0},b.encodePayloadAsArrayBuffer=function(a,c){function d(a,c){b.encodePacket(a,!0,!0,function(a){return c(null,a)})}return a.length?void i(a,d,function(a,b){var d=b.reduce(function(a,b){var c;return c="string"==typeof b?b.length:b.byteLength,a+c.toString().length+c+2},0),e=new Uint8Array(d),f=0;return b.forEach(function(a){var b="string"==typeof a,c=a;if(b){for(var d=new Uint8Array(a.length),g=0;g<a.length;g++)d[g]=a.charCodeAt(g);c=d.buffer}b?e[f++]=0:e[f++]=1;for(var h=c.byteLength.toString(),g=0;g<h.length;g++)e[f++]=parseInt(h[g]);e[f++]=255;for(var d=new Uint8Array(c),g=0;g<d.length;g++)e[f++]=d[g]}),c(e.buffer)}):c(new ArrayBuffer(0))},b.encodePayloadAsBlob=function(a,c){function d(a,c){b.encodePacket(a,!0,!0,function(a){var b=new Uint8Array(1);if(b[0]=1,"string"==typeof a){for(var d=new Uint8Array(a.length),e=0;e<a.length;e++)d[e]=a.charCodeAt(e);a=d.buffer,b[0]=0}for(var f=a instanceof ArrayBuffer?a.byteLength:a.size,g=f.toString(),h=new Uint8Array(g.length+1),e=0;e<g.length;e++)h[e]=parseInt(g[e]);if(h[g.length]=255,v){var i=new v([b.buffer,h.buffer,a]);c(null,i)}})}i(a,d,function(a,b){return c(new v(b))})},b.decodePayloadAsBinary=function(a,c,d){"function"==typeof c&&(d=c,c=null);for(var e=a,f=[];e.byteLength>0;){for(var g=new Uint8Array(e),h=0===g[0],i="",j=1;255!==g[j];j++){if(i.length>310)return d(u,0,1);i+=g[j]}e=m(e,2+i.length),i=parseInt(i);var k=m(e,0,i);if(h)try{k=String.fromCharCode.apply(null,new Uint8Array(k))}catch(l){var n=new Uint8Array(k);k="";for(var j=0;j<n.length;j++)k+=String.fromCharCode(n[j])}f.push(k),e=m(e,i)}var o=f.length;f.forEach(function(a,e){d(b.decodePacket(a,c,!0),e,o)})}}).call(b,function(){return this}())},function(a,b){a.exports=Object.keys||function(a){var b=[],c=Object.prototype.hasOwnProperty;for(var d in a)c.call(a,d)&&b.push(d);return b}},function(a,b){a.exports=function(a,b,c){var d=a.byteLength;if(b=b||0,c=c||d,a.slice)return a.slice(b,c);if(b<0&&(b+=d),c<0&&(c+=d),c>d&&(c=d),b>=d||b>=c||0===d)return new ArrayBuffer(0);for(var e=new Uint8Array(a),f=new Uint8Array(c-b),g=b,h=0;g<c;g++,h++)f[h]=e[g];return f.buffer}},function(a,b){function c(a,b,c){function e(a,d){if(e.count<=0)throw new Error("after called too many times");--e.count,a?(f=!0,b(a),b=c):0!==e.count||f||b(null,d)}var f=!1;return c=c||d,e.count=a,0===a?b():e}function d(){}a.exports=c},function(a,b,c){var d;(function(a,e){!function(f){function g(a){for(var b,c,d=[],e=0,f=a.length;e<f;)b=a.charCodeAt(e++),b>=55296&&b<=56319&&e<f?(c=a.charCodeAt(e++),56320==(64512&c)?d.push(((1023&b)<<10)+(1023&c)+65536):(d.push(b),e--)):d.push(b);return d}function h(a){for(var b,c=a.length,d=-1,e="";++d<c;)b=a[d],b>65535&&(b-=65536,e+=u(b>>>10&1023|55296),b=56320|1023&b),e+=u(b);return e}function i(a,b){if(a>=55296&&a<=57343){if(b)throw Error("Lone surrogate U+"+a.toString(16).toUpperCase()+" is not a scalar value");return!1}return!0}function j(a,b){return u(a>>b&63|128)}function k(a,b){if(0==(4294967168&a))return u(a);var c="";return 0==(4294965248&a)?c=u(a>>6&31|192):0==(4294901760&a)?(i(a,b)||(a=65533),c=u(a>>12&15|224),c+=j(a,6)):0==(4292870144&a)&&(c=u(a>>18&7|240),c+=j(a,12),c+=j(a,6)),c+=u(63&a|128)}function l(a,b){b=b||{};for(var c,d=!1!==b.strict,e=g(a),f=e.length,h=-1,i="";++h<f;)c=e[h],i+=k(c,d);return i}function m(){if(t>=s)throw Error("Invalid byte index");var a=255&r[t];if(t++,128==(192&a))return 63&a;throw Error("Invalid continuation byte")}function n(a){var b,c,d,e,f;if(t>s)throw Error("Invalid byte index");if(t==s)return!1;if(b=255&r[t],t++,0==(128&b))return b;if(192==(224&b)){if(c=m(),f=(31&b)<<6|c,f>=128)return f;throw Error("Invalid continuation byte")}if(224==(240&b)){if(c=m(),d=m(),f=(15&b)<<12|c<<6|d,f>=2048)return i(f,a)?f:65533;throw Error("Invalid continuation byte")}if(240==(248&b)&&(c=m(),d=m(),e=m(),f=(7&b)<<18|c<<12|d<<6|e,f>=65536&&f<=1114111))return f;throw Error("Invalid UTF-8 detected")}function o(a,b){b=b||{};var c=!1!==b.strict;r=g(a),s=r.length,t=0;for(var d,e=[];(d=n(c))!==!1;)e.push(d);return h(e)}var p="object"==typeof b&&b,q=("object"==typeof a&&a&&a.exports==p&&a,"object"==typeof e&&e);q.global!==q&&q.window!==q||(f=q);var r,s,t,u=String.fromCharCode,v={version:"2.1.2",encode:l,decode:o};d=function(){return v}.call(b,c,b,a),!(void 0!==d&&(a.exports=d))}(this)}).call(b,c(28)(a),function(){return this}())},function(a,b){a.exports=function(a){return a.webpackPolyfill||(a.deprecate=function(){},a.paths=[],a.children=[],a.webpackPolyfill=1),a}},function(a,b){!function(){"use strict";for(var a="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",c=new Uint8Array(256),d=0;d<a.length;d++)c[a.charCodeAt(d)]=d;b.encode=function(b){var c,d=new Uint8Array(b),e=d.length,f="";for(c=0;c<e;c+=3)f+=a[d[c]>>2],f+=a[(3&d[c])<<4|d[c+1]>>4],f+=a[(15&d[c+1])<<2|d[c+2]>>6],f+=a[63&d[c+2]];return e%3===2?f=f.substring(0,f.length-1)+"=":e%3===1&&(f=f.substring(0,f.length-2)+"=="),f},b.decode=function(a){var b,d,e,f,g,h=.75*a.length,i=a.length,j=0;"="===a[a.length-1]&&(h--,"="===a[a.length-2]&&h--);var k=new ArrayBuffer(h),l=new Uint8Array(k);for(b=0;b<i;b+=4)d=c[a.charCodeAt(b)],e=c[a.charCodeAt(b+1)],f=c[a.charCodeAt(b+2)],g=c[a.charCodeAt(b+3)],l[j++]=d<<2|e>>4,l[j++]=(15&e)<<4|f>>2,l[j++]=(3&f)<<6|63&g;return k}}()},function(a,b){(function(b){function c(a){for(var b=0;b<a.length;b++){var c=a[b];if(c.buffer instanceof ArrayBuffer){var d=c.buffer;if(c.byteLength!==d.byteLength){var e=new Uint8Array(c.byteLength);e.set(new Uint8Array(d,c.byteOffset,c.byteLength)),d=e.buffer}a[b]=d}}}function d(a,b){b=b||{};var d=new f;c(a);for(var e=0;e<a.length;e++)d.append(a[e]);return b.type?d.getBlob(b.type):d.getBlob()}function e(a,b){return c(a),new Blob(a,b||{})}var f=b.BlobBuilder||b.WebKitBlobBuilder||b.MSBlobBuilder||b.MozBlobBuilder,g=function(){try{var a=new Blob(["hi"]);return 2===a.size}catch(b){return!1}}(),h=g&&function(){try{var a=new Blob([new Uint8Array([1,2])]);return 2===a.size}catch(b){return!1}}(),i=f&&f.prototype.append&&f.prototype.getBlob;a.exports=function(){return g?h?b.Blob:e:i?d:void 0}()}).call(b,function(){return this}())},function(a,b){b.encode=function(a){var b="";for(var c in a)a.hasOwnProperty(c)&&(b.length&&(b+="&"),b+=encodeURIComponent(c)+"="+encodeURIComponent(a[c]));return b},b.decode=function(a){for(var b={},c=a.split("&"),d=0,e=c.length;d<e;d++){var f=c[d].split("=");b[decodeURIComponent(f[0])]=decodeURIComponent(f[1])}return b}},function(a,b){a.exports=function(a,b){var c=function(){};c.prototype=b.prototype,a.prototype=new c,a.prototype.constructor=a}},function(a,b){"use strict";function c(a){var b="";do b=g[a%h]+b,a=Math.floor(a/h);while(a>0);return b}function d(a){var b=0;for(k=0;k<a.length;k++)b=b*h+i[a.charAt(k)];return b}function e(){var a=c(+new Date);return a!==f?(j=0,f=a):a+"."+c(j++)}for(var f,g="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_".split(""),h=64,i={},j=0,k=0;k<h;k++)i[g[k]]=k;e.encode=c,e.decode=d,a.exports=e},function(a,b,c){(function(b){function d(){}function e(a){f.call(this,a),this.query=this.query||{},h||(b.___eio||(b.___eio=[]),h=b.___eio),this.index=h.length;var c=this;h.push(function(a){c.onData(a)}),this.query.j=this.index,b.document&&b.addEventListener&&b.addEventListener("beforeunload",function(){c.script&&(c.script.onerror=d)},!1)}var f=c(21),g=c(32);a.exports=e;var h,i=/\n/g,j=/\\n/g;g(e,f),e.prototype.supportsBinary=!1,e.prototype.doClose=function(){this.script&&(this.script.parentNode.removeChild(this.script),this.script=null),this.form&&(this.form.parentNode.removeChild(this.form),this.form=null,this.iframe=null),f.prototype.doClose.call(this)},e.prototype.doPoll=function(){var a=this,b=document.createElement("script");this.script&&(this.script.parentNode.removeChild(this.script),this.script=null),b.async=!0,b.src=this.uri(),b.onerror=function(b){a.onError("jsonp poll error",b)};var c=document.getElementsByTagName("script")[0];c?c.parentNode.insertBefore(b,c):(document.head||document.body).appendChild(b),this.script=b;var d="undefined"!=typeof navigator&&/gecko/i.test(navigator.userAgent);d&&setTimeout(function(){var a=document.createElement("iframe");document.body.appendChild(a),document.body.removeChild(a)},100)},e.prototype.doWrite=function(a,b){function c(){d(),b()}function d(){if(e.iframe)try{e.form.removeChild(e.iframe)}catch(a){e.onError("jsonp polling iframe removal error",a)}try{var b='<iframe src="javascript:0" name="'+e.iframeId+'">';f=document.createElement(b)}catch(a){f=document.createElement("iframe"),f.name=e.iframeId,f.src="javascript:0"}f.id=e.iframeId,e.form.appendChild(f),e.iframe=f}var e=this;if(!this.form){var f,g=document.createElement("form"),h=document.createElement("textarea"),k=this.iframeId="eio_iframe_"+this.index;g.className="socketio",g.style.position="absolute",g.style.top="-1000px",g.style.left="-1000px",g.target=k,g.method="POST",g.setAttribute("accept-charset","utf-8"),h.name="d",g.appendChild(h),document.body.appendChild(g),this.form=g,this.area=h}this.form.action=this.uri(),d(),a=a.replace(j,"\\\n"),this.area.value=a.replace(i,"\\n");try{this.form.submit()}catch(l){}this.iframe.attachEvent?this.iframe.onreadystatechange=function(){"complete"===e.iframe.readyState&&c()}:this.iframe.onload=c}}).call(b,function(){return this}())},function(a,b,c){(function(b){function d(a){var b=a&&a.forceBase64;b&&(this.supportsBinary=!1),this.perMessageDeflate=a.perMessageDeflate,this.usingBrowserWebSocket=l&&!a.forceNode,this.protocols=a.protocols,this.usingBrowserWebSocket||(n=e),f.call(this,a)}var e,f=c(22),g=c(23),h=c(31),i=c(32),j=c(33),k=c(3)("engine.io-client:websocket"),l=b.WebSocket||b.MozWebSocket;if("undefined"==typeof window)try{e=c(36)}catch(m){}var n=l;n||"undefined"!=typeof window||(n=e),a.exports=d,i(d,f),d.prototype.name="websocket",d.prototype.supportsBinary=!0,d.prototype.doOpen=function(){if(this.check()){var a=this.uri(),b=this.protocols,c={agent:this.agent,perMessageDeflate:this.perMessageDeflate};c.pfx=this.pfx,c.key=this.key,c.passphrase=this.passphrase,c.cert=this.cert,c.ca=this.ca,c.ciphers=this.ciphers,c.rejectUnauthorized=this.rejectUnauthorized,this.extraHeaders&&(c.headers=this.extraHeaders),this.localAddress&&(c.localAddress=this.localAddress);try{this.ws=this.usingBrowserWebSocket?b?new n(a,b):new n(a):new n(a,b,c)}catch(d){return this.emit("error",d)}void 0===this.ws.binaryType&&(this.supportsBinary=!1),this.ws.supports&&this.ws.supports.binary?(this.supportsBinary=!0,this.ws.binaryType="nodebuffer"):this.ws.binaryType="arraybuffer",this.addEventListeners()}},d.prototype.addEventListeners=function(){var a=this;this.ws.onopen=function(){a.onOpen()},this.ws.onclose=function(){a.onClose()},this.ws.onmessage=function(b){a.onData(b.data)},this.ws.onerror=function(b){a.onError("websocket error",b)}},d.prototype.write=function(a){function c(){d.emit("flush"),setTimeout(function(){d.writable=!0,d.emit("drain")},0)}var d=this;this.writable=!1;for(var e=a.length,f=0,h=e;f<h;f++)!function(a){g.encodePacket(a,d.supportsBinary,function(f){if(!d.usingBrowserWebSocket){var g={};if(a.options&&(g.compress=a.options.compress),d.perMessageDeflate){var h="string"==typeof f?b.Buffer.byteLength(f):f.length;h<d.perMessageDeflate.threshold&&(g.compress=!1)}}try{d.usingBrowserWebSocket?d.ws.send(f):d.ws.send(f,g)}catch(i){k("websocket closed before onclose event")}--e||c()})}(a[f])},d.prototype.onClose=function(){f.prototype.onClose.call(this)},d.prototype.doClose=function(){"undefined"!=typeof this.ws&&this.ws.close()},d.prototype.uri=function(){var a=this.query||{},b=this.secure?"wss":"ws",c="";this.port&&("wss"===b&&443!==Number(this.port)||"ws"===b&&80!==Number(this.port))&&(c=":"+this.port),this.timestampRequests&&(a[this.timestampParam]=j()),this.supportsBinary||(a.b64=1),a=h.encode(a),a.length&&(a="?"+a);var d=this.hostname.indexOf(":")!==-1;return b+"://"+(d?"["+this.hostname+"]":this.hostname)+c+this.path+a},d.prototype.check=function(){return!(!n||"__initialize"in n&&this.name===d.prototype.name)}}).call(b,function(){return this}())},function(a,b){},function(a,b){var c=[].indexOf;a.exports=function(a,b){if(c)return a.indexOf(b);for(var d=0;d<a.length;++d)if(a[d]===b)return d;return-1}},function(a,b){(function(b){var c=/^[\],:{}\s]*$/,d=/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,e=/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,f=/(?:^|:|,)(?:\s*\[)+/g,g=/^\s+/,h=/\s+$/;a.exports=function(a){return"string"==typeof a&&a?(a=a.replace(g,"").replace(h,""),b.JSON&&JSON.parse?JSON.parse(a):c.test(a.replace(d,"@").replace(e,"]").replace(f,""))?new Function("return "+a)():void 0):null}}).call(b,function(){return this}())},function(a,b,c){"use strict";function d(a,b,c){this.io=a,this.nsp=b,this.json=this,this.ids=0,this.acks={},this.receiveBuffer=[],this.sendBuffer=[],this.connected=!1,this.disconnected=!0,c&&c.query&&(this.query=c.query),this.io.autoConnect&&this.open()}var e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&"function"==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?"symbol":typeof a},f=c(7),g=c(8),h=c(40),i=c(41),j=c(42),k=c(3)("socket.io-client:socket"),l=c(31);a.exports=b=d;var m={connect:1,connect_error:1,connect_timeout:1,connecting:1,disconnect:1,error:1,reconnect:1,reconnect_attempt:1,reconnect_failed:1,reconnect_error:1,reconnecting:1,ping:1,pong:1},n=g.prototype.emit;g(d.prototype),d.prototype.subEvents=function(){if(!this.subs){var a=this.io;this.subs=[i(a,"open",j(this,"onopen")),i(a,"packet",j(this,"onpacket")),i(a,"close",j(this,"onclose"))]}},d.prototype.open=d.prototype.connect=function(){return this.connected?this:(this.subEvents(),this.io.open(),"open"===this.io.readyState&&this.onopen(),this.emit("connecting"),this)},d.prototype.send=function(){var a=h(arguments);return a.unshift("message"),this.emit.apply(this,a),this},d.prototype.emit=function(a){if(m.hasOwnProperty(a))return n.apply(this,arguments),this;var b=h(arguments),c={type:f.EVENT,data:b};return c.options={},c.options.compress=!this.flags||!1!==this.flags.compress,"function"==typeof b[b.length-1]&&(k("emitting packet with ack id %d",this.ids),this.acks[this.ids]=b.pop(),c.id=this.ids++),this.connected?this.packet(c):this.sendBuffer.push(c),delete this.flags,this},d.prototype.packet=function(a){a.nsp=this.nsp,this.io.packet(a)},d.prototype.onopen=function(){if(k("transport is open - connecting"),"/"!==this.nsp)if(this.query){var a="object"===e(this.query)?l.encode(this.query):this.query;k("sending connect packet with query %s",a),this.packet({type:f.CONNECT,query:a})}else this.packet({type:f.CONNECT})},d.prototype.onclose=function(a){k("close (%s)",a),this.connected=!1,this.disconnected=!0,delete this.id,this.emit("disconnect",a)},d.prototype.onpacket=function(a){if(a.nsp===this.nsp)switch(a.type){case f.CONNECT:this.onconnect();break;case f.EVENT:this.onevent(a);break;case f.BINARY_EVENT:this.onevent(a);break;case f.ACK:this.onack(a);break;case f.BINARY_ACK:this.onack(a);break;case f.DISCONNECT:this.ondisconnect();break;case f.ERROR:this.emit("error",a.data)}},d.prototype.onevent=function(a){var b=a.data||[];k("emitting event %j",b),null!=a.id&&(k("attaching ack callback to event"),b.push(this.ack(a.id))),this.connected?n.apply(this,b):this.receiveBuffer.push(b)},d.prototype.ack=function(a){var b=this,c=!1;return function(){if(!c){c=!0;var d=h(arguments);k("sending ack %j",d),b.packet({type:f.ACK,id:a,data:d})}}},d.prototype.onack=function(a){var b=this.acks[a.id];"function"==typeof b?(k("calling ack %s with %j",a.id,a.data),b.apply(this,a.data),delete this.acks[a.id]):k("bad ack %s",a.id)},d.prototype.onconnect=function(){this.connected=!0,this.disconnected=!1,this.emit("connect"),this.emitBuffered()},d.prototype.emitBuffered=function(){var a;for(a=0;a<this.receiveBuffer.length;a++)n.apply(this,this.receiveBuffer[a]);for(this.receiveBuffer=[],a=0;a<this.sendBuffer.length;a++)this.packet(this.sendBuffer[a]);this.sendBuffer=[]},d.prototype.ondisconnect=function(){k("server disconnect (%s)",this.nsp),this.destroy(),this.onclose("io server disconnect")},d.prototype.destroy=function(){if(this.subs){for(var a=0;a<this.subs.length;a++)this.subs[a].destroy();this.subs=null}this.io.destroy(this)},d.prototype.close=d.prototype.disconnect=function(){return this.connected&&(k("performing disconnect (%s)",this.nsp),this.packet({type:f.DISCONNECT})),this.destroy(),this.connected&&this.onclose("io client disconnect"),this},d.prototype.compress=function(a){return this.flags=this.flags||{},this.flags.compress=a,this}},function(a,b){function c(a,b){var c=[];b=b||0;for(var d=b||0;d<a.length;d++)c[d-b]=a[d];return c}a.exports=c},function(a,b){"use strict";function c(a,b,c){return a.on(b,c),{destroy:function(){a.removeListener(b,c)}}}a.exports=c},function(a,b){var c=[].slice;a.exports=function(a,b){if("string"==typeof b&&(b=a[b]),"function"!=typeof b)throw new Error("bind() requires a function");var d=c.call(arguments,2);return function(){return b.apply(a,d.concat(c.call(arguments)))}}},function(a,b){function c(a){a=a||{},this.ms=a.min||100,this.max=a.max||1e4,this.factor=a.factor||2,this.jitter=a.jitter>0&&a.jitter<=1?a.jitter:0,this.attempts=0}a.exports=c,c.prototype.duration=function(){var a=this.ms*Math.pow(this.factor,this.attempts++);if(this.jitter){var b=Math.random(),c=Math.floor(b*this.jitter*a);a=0==(1&Math.floor(10*b))?a-c:a+c}return 0|Math.min(a,this.max)},c.prototype.reset=function(){this.attempts=0},c.prototype.setMin=function(a){this.ms=a},c.prototype.setMax=function(a){this.max=a},c.prototype.setJitter=function(a){this.jitter=a}}])});;

//////////////////////////////////////////////////////////////////////////////////////
 //                                                                                //
 //  ███████╗ █████╗ ██╗██╗     ███████╗   ██╗ ██████╗         ██╗███████╗         //
 //  ██╔════╝██╔══██╗██║██║     ██╔════╝   ██║██╔═══██╗        ██║██╔════╝         //
 //  ███████╗███████║██║██║     ███████╗   ██║██║   ██║        ██║███████╗         //
 //  ╚════██║██╔══██║██║██║     ╚════██║   ██║██║   ██║   ██   ██║╚════██║         //
 //  ███████║██║  ██║██║███████╗███████║██╗██║╚██████╔╝██╗╚█████╔╝███████║         //
 //  ╚══════╝╚═╝  ╚═╝╚═╝╚══════╝╚══════╝╚═╝╚═╝ ╚═════╝ ╚═╝ ╚════╝ ╚══════╝         //
 //                                                                                //
 //   ╦╔═╗╦  ╦╔═╗╔═╗╔═╗╦═╗╦╔═╗╔╦╗  ╔═╗╦  ╦╔═╗╔╗╔╔╦╗  ╔═╗╔╦╗╦╔═                     //
 //   ║╠═╣╚╗╔╝╠═╣╚═╗║  ╠╦╝║╠═╝ ║   ║  ║  ║║╣ ║║║ ║   ╚═╗ ║║╠╩╗                     //
 //  ╚╝╩ ╩ ╚╝ ╩ ╩╚═╝╚═╝╩╚═╩╩   ╩   ╚═╝╩═╝╩╚═╝╝╚╝ ╩   ╚═╝═╩╝╩ ╩                     //
 //  ┌─┐┌─┐┬─┐  ┌┐┌┌─┐┌┬┐┌─┐  ┬┌─┐  ┌─┐┌┐┌┌┬┐  ┌┬┐┬ ┬┌─┐  ┌┐ ┬─┐┌─┐┬ ┬┌─┐┌─┐┬─┐    //
 //  ├┤ │ │├┬┘  ││││ │ ││├┤   │└─┐  ├─┤│││ ││   │ ├─┤├┤   ├┴┐├┬┘│ ││││└─┐├┤ ├┬┘    //
 //  └  └─┘┴└─  ┘└┘└─┘─┴┘└─┘o└┘└─┘  ┴ ┴┘└┘─┴┘   ┴ ┴ ┴└─┘  └─┘┴└─└─┘└┴┘└─┘└─┘┴└─    //
 //                                                                                //
//////////////////////////////////////////////////////////////////////////////////////

/**
 * sails.io.js
 * v1.2.1
 * ------------------------------------------------------------------------
 * JavaScript Client (SDK) for communicating with Sails.
 *
 * Note that this script is completely optional, but it is handy if you're
 * using WebSockets from the browser to talk to your Sails server.
 *
 * For tips and documentation, visit:
 * http://sailsjs.com/documentation/reference/web-sockets/socket-client
 * ------------------------------------------------------------------------
 *
 * This file allows you to send and receive socket.io messages to & from Sails
 * by simulating a REST client interface on top of socket.io. It models its API
 * after the $.ajax pattern from jQuery you might already be familiar with.
 *
 * So if you're switching from using AJAX to sockets, instead of:
 *    `$.post( url, [data], [cb] )`
 *
 * You would use:
 *    `socket.post( url, [data], [cb] )`
 */


(function() {


  //   ██████╗ ██████╗ ███╗   ██╗███████╗████████╗ █████╗ ███╗   ██╗████████╗███████╗
  //  ██╔════╝██╔═══██╗████╗  ██║██╔════╝╚══██╔══╝██╔══██╗████╗  ██║╚══██╔══╝██╔════╝
  //  ██║     ██║   ██║██╔██╗ ██║███████╗   ██║   ███████║██╔██╗ ██║   ██║   ███████╗
  //  ██║     ██║   ██║██║╚██╗██║╚════██║   ██║   ██╔══██║██║╚██╗██║   ██║   ╚════██║
  //  ╚██████╗╚██████╔╝██║ ╚████║███████║   ██║   ██║  ██║██║ ╚████║   ██║   ███████║
  //   ╚═════╝ ╚═════╝ ╚═╝  ╚═══╝╚══════╝   ╚═╝   ╚═╝  ╚═╝╚═╝  ╚═══╝   ╚═╝   ╚══════╝
  //


  /**
   * Constant containing the names of all available options
   * for individual sockets.
   *
   * @type {Array}
   */
  var SOCKET_OPTIONS = [
    'useCORSRouteToGetCookie',
    'url',
    'multiplex',
    'transports',
    'query',
    'path',
    'headers',
    'initialConnectionHeaders',
    'reconnection',
    'reconnectionAttempts',
    'reconnectionDelay',
    'reconnectionDelayMax',
    'rejectUnauthorized',
    'randomizationFactor',
    'timeout'
  ];


  /**
   * Constant containing the names of properties on `io.sails` which
   * may be configured using HTML attributes on the script tag which
   * loaded this file.
   *
   * @type {Array}
   *
   * (this is unused if loading from node.js)
   */
  var CONFIGURABLE_VIA_HTML_ATTR = [
    'autoConnect',
    'reconnection',
    'environment',
    'headers',
    'url',
    'transports',
    'path'
  ];




  /**
   * Constant containing the names of querystring
   * parameters sent when connecting any SailsSocket.
   *
   * @type {Dictionary}
   */
  var CONNECTION_METADATA_PARAMS = {
    version: '__sails_io_sdk_version',
    platform: '__sails_io_sdk_platform',
    language: '__sails_io_sdk_language'
  };


  /**
   * Constant containing metadata about the platform, language, and
   * current version of this SDK.
   *
   * @type {Dictionary}
   */
  var SDK_INFO = {
    version: '1.2.1', // <-- pulled automatically from package.json, do not change!
    language: 'javascript',
    platform: (function (){
      if (typeof module === 'object' && typeof module.exports !== 'undefined') {
        return 'node';
      }
      else {
        return 'browser';
      }
    })()
  };

  // Build `versionString` (a querystring snippet) by
  // combining SDK_INFO and CONNECTION_METADATA_PARAMS.
  SDK_INFO.versionString =
    CONNECTION_METADATA_PARAMS.version + '=' + SDK_INFO.version + '&' +
    CONNECTION_METADATA_PARAMS.platform + '=' + SDK_INFO.platform + '&' +
    CONNECTION_METADATA_PARAMS.language + '=' + SDK_INFO.language;




  //   █████╗ ██████╗ ███████╗ ██████╗ ██████╗ ██████╗     ██╗  ██╗████████╗███╗   ███╗██╗
  //  ██╔══██╗██╔══██╗██╔════╝██╔═══██╗██╔══██╗██╔══██╗    ██║  ██║╚══██╔══╝████╗ ████║██║
  //  ███████║██████╔╝███████╗██║   ██║██████╔╝██████╔╝    ███████║   ██║   ██╔████╔██║██║
  //  ██╔══██║██╔══██╗╚════██║██║   ██║██╔══██╗██╔══██╗    ██╔══██║   ██║   ██║╚██╔╝██║██║
  //  ██║  ██║██████╔╝███████║╚██████╔╝██║  ██║██████╔╝    ██║  ██║   ██║   ██║ ╚═╝ ██║███████╗
  //  ╚═╝  ╚═╝╚═════╝ ╚══════╝ ╚═════╝ ╚═╝  ╚═╝╚═════╝     ╚═╝  ╚═╝   ╚═╝   ╚═╝     ╚═╝╚══════╝
  //
  //   █████╗ ████████╗████████╗██████╗ ██╗██████╗ ██╗   ██╗████████╗███████╗███████╗
  //  ██╔══██╗╚══██╔══╝╚══██╔══╝██╔══██╗██║██╔══██╗██║   ██║╚══██╔══╝██╔════╝██╔════╝
  //  ███████║   ██║      ██║   ██████╔╝██║██████╔╝██║   ██║   ██║   █████╗  ███████╗
  //  ██╔══██║   ██║      ██║   ██╔══██╗██║██╔══██╗██║   ██║   ██║   ██╔══╝  ╚════██║
  //  ██║  ██║   ██║      ██║   ██║  ██║██║██████╔╝╚██████╔╝   ██║   ███████╗███████║
  //  ╚═╝  ╚═╝   ╚═╝      ╚═╝   ╚═╝  ╚═╝╚═╝╚═════╝  ╚═════╝    ╚═╝   ╚══════╝╚══════╝
  //
  //  ███████╗██████╗  ██████╗ ███╗   ███╗      ██╗███████╗ ██████╗██████╗ ██╗██████╗ ████████╗██╗
  //  ██╔════╝██╔══██╗██╔═══██╗████╗ ████║     ██╔╝██╔════╝██╔════╝██╔══██╗██║██╔══██╗╚══██╔══╝╚██╗
  //  █████╗  ██████╔╝██║   ██║██╔████╔██║    ██╔╝ ███████╗██║     ██████╔╝██║██████╔╝   ██║    ╚██╗
  //  ██╔══╝  ██╔══██╗██║   ██║██║╚██╔╝██║    ╚██╗ ╚════██║██║     ██╔══██╗██║██╔═══╝    ██║    ██╔╝
  //  ██║     ██║  ██║╚██████╔╝██║ ╚═╝ ██║     ╚██╗███████║╚██████╗██║  ██║██║██║        ██║   ██╔╝
  //  ╚═╝     ╚═╝  ╚═╝ ╚═════╝ ╚═╝     ╚═╝      ╚═╝╚══════╝ ╚═════╝╚═╝  ╚═╝╚═╝╚═╝        ╚═╝   ╚═╝
  //
  //
  // If available, grab the DOM element for the script tag which imported this file.
  // (skip this if this SDK is being used outside of the DOM, i.e. in a Node process)
  //
  // This is used below to parse client-side sails.io.js configuration encoded as
  // HTML attributes, as well as grabbing hold of the URL from whence the SDK was fetched.
  var thisScriptTag = (function() {
    if (
      typeof window !== 'object' ||
      typeof window.document !== 'object' ||
      typeof window.document.getElementsByTagName !== 'function'
    ) {
      return null;
    }

    // Return the URL of the last script loaded (i.e. this one)
    // (this must run before nextTick; see http://stackoverflow.com/a/2976714/486547)
    var allScriptsCurrentlyInDOM = window.document.getElementsByTagName('script');
    return allScriptsCurrentlyInDOM[allScriptsCurrentlyInDOM.length - 1];
  })();


  // Variables to contain src URL and other script tag config (for use below).
  var urlThisScriptWasFetchedFrom = '';
  var scriptTagConfig = {};


  if (thisScriptTag) {
    // Save the URL that this script was fetched from.
    urlThisScriptWasFetchedFrom = thisScriptTag.src;

    // Now parse the most common client-side configuration settings
    // from the script tag where they may be encoded as HTML attributes.
    //
    // Any configuration which may be provided as an HTML attribute may
    // also be provided prefixed with `data-`.  This is for folks who
    // need to support browsers that have issues with nonstandard
    // HTML attributes (or if the idea of using nonstandard HTML attributes
    // just creeps you out)
    //
    // If a `data-` prefixed attr is provided, it takes precedence.
    // (this is so that if you are already using one of these HTML
    //  attrs for some reason, you can keep it as-is and override
    //  it using `data-`. If you are using the `data-` prefixed version
    //  for some other purpose... well, in that case you'll just have to
    //  configure programmatically using `io.sails` instead.)
    CONFIGURABLE_VIA_HTML_ATTR.forEach(function (configKey){

      scriptTagConfig[configKey] = (function (){

        // Support 'data-' prefixed or normal attributes.
        // (prefixed versions take precedence if provided)
        var htmlAttrVal = thisScriptTag.getAttribute( 'data-'+configKey );
        if (!htmlAttrVal) {
          htmlAttrVal = thisScriptTag.getAttribute( configKey );
        }

        // The HTML attribute value should always be a string or `null`.
        // We'll try to parse it as JSON and use that, but worst case fall back
        // to the default situation of it being a string.
        if (typeof htmlAttrVal === 'string') {
          try { return JSON.parse(htmlAttrVal); } catch (e) { return htmlAttrVal; }
        }
        // If `null` was returned from getAttribute(), it means that the HTML attribute
        // was not specified, so we treat it as undefined (which will cause the property
        // to be removed below)
        else if (htmlAttrVal === null) {
          return undefined;
        }
        // Any other contingency shouldn't be possible:
        // - if no quotes are used in the HTML attribute, it still comes in as a string.
        // - if no RHS is provided for the attribute, it still comes in as "" (empty string)
        // (but we still handle this with an explicit error just in case--for debugging and support purposes)
        else throw new Error('sails.io.js :: Unexpected/invalid script tag configuration for `'+configKey+'`: `'+htmlAttrVal+'` (a `'+typeof htmlAttrVal+'`). Should be a string.');
      })();

      if (scriptTagConfig[configKey] === undefined){
        delete scriptTagConfig[configKey];
      }
    });



    // Now that they've been parsed, do an extremely lean version of
    // logical type validation/coercion of provided values.
    //////////////////////////////////////////////////////////////////

    // `autoConnect`
    if (typeof scriptTagConfig.autoConnect !== 'undefined') {
      if (scriptTagConfig.autoConnect === '') {
        // Special case for empty string.  It means `true` (see above).
        scriptTagConfig.autoConnect = true;
      }
      else if (typeof scriptTagConfig.autoConnect !== 'boolean') {
        throw new Error('sails.io.js :: Unexpected/invalid configuration for `autoConnect` provided in script tag: `'+scriptTagConfig.autoConnect+'` (a `'+typeof scriptTagConfig.autoConnect+'`). Should be a boolean.');
      }
    }


    // `environment`
    if (typeof scriptTagConfig.environment !== 'undefined') {
      if (typeof scriptTagConfig.environment !== 'string') {
        throw new Error('sails.io.js :: Unexpected/invalid configuration for `environment` provided in script tag: `'+scriptTagConfig.environment+'` (a `'+typeof scriptTagConfig.environment+'`). Should be a string.');
      }
    }


    // `headers`
    if (typeof scriptTagConfig.headers !== 'undefined') {
      if (typeof scriptTagConfig.headers !== 'object' || Array.isArray(scriptTagConfig.headers)) {
        throw new Error('sails.io.js :: Unexpected/invalid configuration for `headers` provided in script tag: `'+scriptTagConfig.headers+'` (a `'+typeof scriptTagConfig.headers+'`). Should be a JSON-compatible dictionary (i.e. `{}`).  Don\'t forget those double quotes (""), even on key names!  Use single quotes (\'\') to wrap the HTML attribute value; e.g. `headers=\'{"X-Auth": "foo"}\'`');
      }
    }


    // `url`
    if (typeof scriptTagConfig.url !== 'undefined') {
      if (typeof scriptTagConfig.url !== 'string') {
        throw new Error('sails.io.js :: Unexpected/invalid configuration for `url` provided in script tag: `'+scriptTagConfig.url+'` (a `'+typeof scriptTagConfig.url+'`). Should be a string.');
      }
    }

    // OTHER `io.sails` options are NOT CURRENTLY SUPPORTED VIA HTML ATTRIBUTES.
  }




  // Grab a reference to the global socket.io client (if one is available).
  // This is used via closure below to determine which `io` to use when the
  // socket.io client instance (`io`) is augmented to become the Sails client
  // SDK instance (still `io`).
  var _existingGlobalSocketIO = (typeof io !== 'undefined') ? io : undefined;




  //////////////////////////////////////////////////////////////
  /////
  ///// NOW FOR BUNCHES OF:
  /////  - PRIVATE FUNCTION DEFINITIONS
  /////  - CONSTRUCTORS
  /////  - AND METHODS
  /////
  //////////////////////////////////////////////////////////////
  //



  //  ███████╗ █████╗ ██╗██╗     ███████╗      ██╗ ██████╗        ██████╗██╗     ██╗███████╗███╗   ██╗████████╗
  //  ██╔════╝██╔══██╗██║██║     ██╔════╝      ██║██╔═══██╗      ██╔════╝██║     ██║██╔════╝████╗  ██║╚══██╔══╝
  //  ███████╗███████║██║██║     ███████╗█████╗██║██║   ██║█████╗██║     ██║     ██║█████╗  ██╔██╗ ██║   ██║
  //  ╚════██║██╔══██║██║██║     ╚════██║╚════╝██║██║   ██║╚════╝██║     ██║     ██║██╔══╝  ██║╚██╗██║   ██║
  //  ███████║██║  ██║██║███████╗███████║      ██║╚██████╔╝      ╚██████╗███████╗██║███████╗██║ ╚████║   ██║
  //  ╚══════╝╚═╝  ╚═╝╚═╝╚══════╝╚══════╝      ╚═╝ ╚═════╝        ╚═════╝╚══════╝╚═╝╚══════╝╚═╝  ╚═══╝   ╚═╝
  //

  /**
   * SailsIOClient()
   *
   * Augment the provided Socket.io client object (`io`) with methods for
   * talking and listening to one or more Sails backend(s).  If no `io` was
   * provided (i.e. in a browser setting), then attempt to use the global.
   *
   * This absorbs implicit `io.sails` configuration, sets a timer for
   * automatically connecting a socket (if `io.sails.autoConnect` is enabled)
   * and returns the augmented `io`.
   *
   * Note:
   * The automatically-connected socket is exposed as `io.socket`.  If this
   * socket attempts to bind event listeners or send requests before it is
   * connected, it will be queued up and replayed when the connection is
   * successfully opened.
   *
   * @param {SocketIO} io
   * @returns {SailsIOClient} [also called `io`]
   */

  function SailsIOClient(_providedSocketIO) {

    // First, determine which `io` we're augmenting.
    //
    // Prefer the passed-in `io` instance, but fall back to the
    // global one if we've got it.
    var io;
    if (_providedSocketIO) {
      io = _providedSocketIO;
    }
    else {
      io = _existingGlobalSocketIO;
    }
    // (note that for readability, we deliberately do not short circuit or use the tertiary operator above)


    // If a socket.io client (`io`) is not available, none of this will work.
    if (!io) {
      // If node:
      if (SDK_INFO.platform === 'node') {
        throw new Error('No socket.io client available.  When requiring `sails.io.js` from Node.js, a socket.io client (`io`) must be passed in; e.g.:\n```\nvar io = require(\'sails.io.js\')( require(\'socket.io-client\') )\n```\n(see https://github.com/balderdashy/sails.io.js/tree/master/test for more examples)');
      }
      // Otherwise, this is a web browser:
      else {
        throw new Error('The Sails socket SDK depends on the socket.io client, but the socket.io global (`io`) was not available when `sails.io.js` loaded.  Normally, the socket.io client code is bundled with sails.io.js, so something is a little off.  Please check to be sure this version of `sails.io.js` has the minified Socket.io client at the top of the file.');
      }
    }

    // If the chosen socket.io client (`io`) has ALREADY BEEN AUGMENTED by this SDK,
    // (i.e. if it already has a `.sails` property) then throw an error.
    if (io.sails) {
      // If node:
      if (SDK_INFO.platform === 'node') {
        throw new Error('The provided socket.io client (`io`) has already been augmented into a Sails socket SDK instance (it has `io.sails`).');
      }
      // Otherwise, this is a web browser:
      else {
        throw new Error('The socket.io client (`io`) has already been augmented into a Sails socket SDK instance.  Usually, this means you are bringing `sails.io.js` onto the page more than once.');
      }
    }


    /**
     * A little logger for this library to use internally.
     * Basically just a wrapper around `console.log` with
     * support for feature-detection.
     *
     * @api private
     * @factory
     */
    function LoggerFactory(options) {
      options = options || {
        prefix: true
      };

      // If `console.log` is not accessible, `log` is a noop.
      if (
        typeof console !== 'object' ||
        typeof console.log !== 'function' ||
        typeof console.log.bind !== 'function'
      ) {
        return function noop() {};
      }

      return function log() {
        var args = Array.prototype.slice.call(arguments);

        // All logs are disabled when `io.sails.environment = 'production'`.
        if (io.sails.environment === 'production') return;

        // Add prefix to log messages (unless disabled)
        var PREFIX = '';
        if (options.prefix) {
          args.unshift(PREFIX);
        }

        // Call wrapped logger
        console.log
          .bind(console)
          .apply(this, args);
      };
    }//</LoggerFactory>

    // Create a private logger instance
    var consolog = LoggerFactory();
    consolog.noPrefix = LoggerFactory({
      prefix: false
    });



    /**
     * What is the `requestQueue`?
     *
     * The request queue is used to simplify app-level connection logic--
     * i.e. so you don't have to wait for the socket to be connected
     * to start trying to  synchronize data.
     *
     * @api private
     * @param  {SailsSocket}  socket
     */

    function runRequestQueue (socket) {
      var queue = socket.requestQueue;

      if (!queue) return;
      for (var i in queue) {

        // Double-check that `queue[i]` will not
        // inadvertently discover extra properties attached to the Object
        // and/or Array prototype by other libraries/frameworks/tools.
        // (e.g. Ember does this. See https://github.com/balderdashy/sails.io.js/pull/5)
        var isSafeToDereference = ({}).hasOwnProperty.call(queue, i);
        if (isSafeToDereference) {
          // Get the arguments that were originally made to the "request" method
          var requestArgs = queue[i];
          // Call the request method again in the context of the socket, with the original args
          socket.request.apply(socket, requestArgs);
        }
      }

      // Now empty the queue to remove it as a source of additional complexity.
      socket.requestQueue = null;
    }



    /**
     * Send a JSONP request.
     *
     * @param  {Object}   opts [optional]
     * @param  {Function} cb
     * @return {XMLHttpRequest}
     */

    function jsonp(opts, cb) {
      opts = opts || {};

      if (typeof window === 'undefined') {
        // FUTURE: refactor node usage to live in here
        return cb();
      }

      var scriptEl = document.createElement('script');
      window._sailsIoJSConnect = function(response) {
        // In rare circumstances our script may have been vaporised.
        // Remove it, but only if it still exists
        // https://github.com/balderdashy/sails.io.js/issues/92
        if (scriptEl && scriptEl.parentNode) {
            scriptEl.parentNode.removeChild(scriptEl);
        }

        cb(response);
      };
      scriptEl.src = opts.url;
      document.getElementsByTagName('head')[0].appendChild(scriptEl);

    }




    //       ██╗███████╗ ██████╗ ███╗   ██╗      ██╗    ██╗███████╗██████╗ ███████╗ ██████╗  ██████╗██╗  ██╗███████╗████████╗
    //       ██║██╔════╝██╔═══██╗████╗  ██║      ██║    ██║██╔════╝██╔══██╗██╔════╝██╔═══██╗██╔════╝██║ ██╔╝██╔════╝╚══██╔══╝
    //       ██║███████╗██║   ██║██╔██╗ ██║█████╗██║ █╗ ██║█████╗  ██████╔╝███████╗██║   ██║██║     █████╔╝ █████╗     ██║
    //  ██   ██║╚════██║██║   ██║██║╚██╗██║╚════╝██║███╗██║██╔══╝  ██╔══██╗╚════██║██║   ██║██║     ██╔═██╗ ██╔══╝     ██║
    //  ╚█████╔╝███████║╚██████╔╝██║ ╚████║      ╚███╔███╔╝███████╗██████╔╝███████║╚██████╔╝╚██████╗██║  ██╗███████╗   ██║
    //   ╚════╝ ╚══════╝ ╚═════╝ ╚═╝  ╚═══╝       ╚══╝╚══╝ ╚══════╝╚═════╝ ╚══════╝ ╚═════╝  ╚═════╝╚═╝  ╚═╝╚══════╝   ╚═╝
    //
    //  ██████╗ ███████╗███████╗██████╗  ██████╗ ███╗   ██╗███████╗███████╗     ██╗     ██╗██╗    ██╗██████╗ ██╗
    //  ██╔══██╗██╔════╝██╔════╝██╔══██╗██╔═══██╗████╗  ██║██╔════╝██╔════╝    ██╔╝     ██║██║    ██║██╔══██╗╚██╗
    //  ██████╔╝█████╗  ███████╗██████╔╝██║   ██║██╔██╗ ██║███████╗█████╗      ██║      ██║██║ █╗ ██║██████╔╝ ██║
    //  ██╔══██╗██╔══╝  ╚════██║██╔═══╝ ██║   ██║██║╚██╗██║╚════██║██╔══╝      ██║ ██   ██║██║███╗██║██╔══██╗ ██║
    //  ██║  ██║███████╗███████║██║     ╚██████╔╝██║ ╚████║███████║███████╗    ╚██╗╚█████╔╝╚███╔███╔╝██║  ██║██╔╝
    //  ╚═╝  ╚═╝╚══════╝╚══════╝╚═╝      ╚═════╝ ╚═╝  ╚═══╝╚══════╝╚══════╝     ╚═╝ ╚════╝  ╚══╝╚══╝ ╚═╝  ╚═╝╚═╝
    //

    /**
     * The JWR (JSON WebSocket Response) received from a Sails server.
     *
     * @api public
     * @param  {Object}  responseCtx
     *         => :body
     *         => :statusCode
     *         => :headers
     *
     * @constructor
     */

    function JWR(responseCtx) {
      this.body = responseCtx.body;
      this.headers = responseCtx.headers || {};
      this.statusCode = (typeof responseCtx.statusCode === 'undefined') ? 200 : responseCtx.statusCode;
      // FUTURE: Replace this typeof short-circuit with an assertion (statusCode should always be set)

      if (this.statusCode < 200 || this.statusCode >= 400) {
        // Determine the appropriate error message.
        var msg;
        if (this.statusCode === 0) {
          msg = 'The socket request failed.';
        }
        else {
          msg = 'Server responded with a ' + this.statusCode + ' status code';
          msg += ':\n```\n' + JSON.stringify(this.body, null, 2) + '\n```';
          // (^^Note that we should always be able to rely on socket.io to give us
          // non-circular data here, so we don't have to worry about wrapping the
          // above in a try...catch)
        }

        // Now build and attach Error instance.
        this.error = new Error(msg);
      }
    }
    JWR.prototype.toString = function() {
      return '[ResponseFromSails]' + '  -- ' +
        'Status: ' + this.statusCode + '  -- ' +
        'Headers: ' + this.headers + '  -- ' +
        'Body: ' + this.body;
    };
    JWR.prototype.toPOJO = function() {
      return {
        body: this.body,
        headers: this.headers,
        statusCode: this.statusCode
      };
    };
    JWR.prototype.pipe = function() {
      // FUTURE: look at substack's stuff
      return new Error('Client-side streaming support not implemented yet.');
    };




    //          ███████╗███╗   ███╗██╗████████╗███████╗██████╗  ██████╗ ███╗   ███╗ ██╗██╗
    //          ██╔════╝████╗ ████║██║╚══██╔══╝██╔════╝██╔══██╗██╔═══██╗████╗ ████║██╔╝╚██╗
    //          █████╗  ██╔████╔██║██║   ██║   █████╗  ██████╔╝██║   ██║██╔████╔██║██║  ██║
    //          ██╔══╝  ██║╚██╔╝██║██║   ██║   ██╔══╝  ██╔══██╗██║   ██║██║╚██╔╝██║██║  ██║
    //  ███████╗███████╗██║ ╚═╝ ██║██║   ██║   ██║     ██║  ██║╚██████╔╝██║ ╚═╝ ██║╚██╗██╔╝
    //  ╚══════╝╚══════╝╚═╝     ╚═╝╚═╝   ╚═╝   ╚═╝     ╚═╝  ╚═╝ ╚═════╝ ╚═╝     ╚═╝ ╚═╝╚═╝
    //

    /**
     * @api private
     * @param  {SailsSocket} socket  [description]
     * @param  {Object} requestCtx [description]
     */

    function _emitFrom(socket, requestCtx) {

      if (!socket._raw) {
        throw new Error('Failed to emit from socket- raw SIO socket is missing.');
      }

      // Since callback is embedded in requestCtx,
      // retrieve it and delete the key before continuing.
      var cb = requestCtx.cb;
      delete requestCtx.cb;

      // Name of the appropriate socket.io listener on the server
      // ( === the request method or "verb", e.g. 'get', 'post', 'put', etc. )
      var sailsEndpoint = requestCtx.method;

      socket._raw.emit(sailsEndpoint, requestCtx, function serverResponded(responseCtx) {

        // Send back (emulatedHTTPBody, jsonWebSocketResponse)
        if (cb && !requestCtx.calledCb) {
          cb(responseCtx.body, new JWR(responseCtx));
          // Set flag indicating that callback was called, to avoid duplicate calls.
          requestCtx.calledCb = true;
          // Remove the callback from the list.
          socket._responseCbs.splice(socket._responseCbs.indexOf(cb), 1);
          // Remove the context from the list.
          socket._requestCtxs.splice(socket._requestCtxs.indexOf(requestCtx), 1);
        }
      });
    }







    //  ███████╗ █████╗ ██╗██╗     ███████╗███████╗ ██████╗  ██████╗██╗  ██╗███████╗████████╗
    //  ██╔════╝██╔══██╗██║██║     ██╔════╝██╔════╝██╔═══██╗██╔════╝██║ ██╔╝██╔════╝╚══██╔══╝
    //  ███████╗███████║██║██║     ███████╗███████╗██║   ██║██║     █████╔╝ █████╗     ██║
    //  ╚════██║██╔══██║██║██║     ╚════██║╚════██║██║   ██║██║     ██╔═██╗ ██╔══╝     ██║
    //  ███████║██║  ██║██║███████╗███████║███████║╚██████╔╝╚██████╗██║  ██╗███████╗   ██║
    //  ╚══════╝╚═╝  ╚═╝╚═╝╚══════╝╚══════╝╚══════╝ ╚═════╝  ╚═════╝╚═╝  ╚═╝╚══════╝   ╚═╝
    //

    /**
     * SailsSocket
     *
     * A wrapper for an underlying Socket instance that communicates directly
     * to the Socket.io server running inside of Sails.
     *
     * If no `socket` option is provied, SailsSocket will function as a mock. It will queue socket
     * requests and event handler bindings, replaying them when the raw underlying socket actually
     * connects. This is handy when we don't necessarily have the valid configuration to know
     * WHICH SERVER to talk to yet, etc.  It is also used by `io.socket` for your convenience.
     *
     * @constructor
     * @api private
     *
     * ----------------------------------------------------------------------
     * Note: This constructor should not be used directly. To obtain a `SailsSocket`
     * instance of your very own, run:
     * ```
     * var mySocket = io.sails.connect();
     * ```
     * ----------------------------------------------------------------------
     */
    function SailsSocket (opts){
      var self = this;
      opts = opts||{};

      // Initialize private properties
      self._isConnecting = false;
      self._mightBeAboutToAutoConnect = false;

      // Set up connection options so that they can only be changed when socket is disconnected.
      var _opts = {};
      SOCKET_OPTIONS.forEach(function(option) {
        // Okay to change global headers while socket is connected
        if (option == 'headers') {return;}
        Object.defineProperty(self, option, {
          get: function() {
            if (option == 'url') {
              return _opts[option] || (self._raw && self._raw.io && self._raw.io.uri);
            }
            return _opts[option];
          },
          set: function(value) {
            // Don't allow value to be changed while socket is connected
            if (self.isConnected() && io.sails.strict !== false && value != _opts[option]) {
              throw new Error('Cannot change value of `' + option + '` while socket is connected.');
            }
            // If socket is attempting to reconnect, stop it.
            if (self._raw && self._raw.io && self._raw.io.reconnecting && !self._raw.io.skipReconnect) {
              self._raw.io.skipReconnect = true;
              consolog('Stopping reconnect; use .reconnect() to connect socket after changing options.');
            }
            _opts[option] = value;
          }
        });
      });

      // Absorb opts into SailsSocket instance
      // See http://sailsjs.com/documentation/reference/web-sockets/socket-client/sails-socket/properties
      // for description of options
      SOCKET_OPTIONS.forEach(function(option) {
        self[option] = opts[option];
      });

      // Set up "eventQueue" to hold event handlers which have not been set on the actual raw socket yet.
      self.eventQueue = {};

      // Listen for special `parseError` event sent from sockets hook on the backend
      // if an error occurs but a valid callback was not received from the client
      // (i.e. so the server had no other way to send back the error information)
      self.on('sails:parseError', function (err){
        consolog('Sails encountered an error parsing a socket message sent from this client, and did not have access to a callback function to respond with.');
        consolog('Error details:',err);
      });

      // FUTURE:
      // Listen for a special private message on any connected that allows the server
      // to set the environment (giving us 100% certainty that we guessed right)
      // However, note that the `console.log`s called before and after connection
      // are still forced to rely on our existing heuristics (to disable, tack #production
      // onto the URL used to fetch this file.)

    }//</SailsSocket>


    /**
     * `SailsSocket.prototype._connect()`
     *
     * Begin connecting this socket to the server.
     *
     * @api private
     */
    SailsSocket.prototype._connect = function (){
      var self = this;

      self._isConnecting = true;

      // Apply `io.sails` config as defaults
      // (now that at least one tick has elapsed)
      // See http://sailsjs.com/documentation/reference/web-sockets/socket-client/sails-socket/properties
      // for description of options and default values
      SOCKET_OPTIONS.forEach(function(option) {
        if ('undefined' == typeof self[option]) {
          self[option] = io.sails[option];
        }
      });

      // Headers that will be sent with the initial request to /socket.io (Node.js only)
      self.extraHeaders = self.initialConnectionHeaders || {};

      // For browser usage (currently works with "polling" transport only)
      self.transportOptions = self.transportOptions || {};
      self.transports.forEach(function(transport) {
        self.transportOptions[transport] = self.transportOptions[transport] || {};
        self.transportOptions[transport].extraHeaders = self.initialConnectionHeaders || {};
      });

      // Log a warning if non-Node.js platform attempts to use `initialConnectionHeaders` for anything other than `polling`.
      if (self.initialConnectionHeaders && SDK_INFO.platform !== 'node' && self.transports.indexOf('polling') === -1 || self.transports.length > 1) {
        if (typeof console === 'object' && typeof console.warn === 'function') {
          console.warn('When running in browser, `initialConnectionHeaders` option is only available for the `polling` transport.');
        }
      }

      // Ensure URL has no trailing slash
      self.url = self.url ? self.url.replace(/(\/)$/, '') : undefined;

      // Mix the current SDK version into the query string in
      // the connection request to the server:
      if (typeof self.query === 'string') {
        // (If provided as a string, trim leading question mark,
        // just in case one was provided.)
        self.query = self.query.replace(/^\?/, '');
        self.query += '&' + SDK_INFO.versionString;
      }
      else if (self.query && typeof self.query === 'object') {
        throw new Error('`query` setting does not currently support configuration as a dictionary (`{}`).  Instead, it must be specified as a string like `foo=89&bar=hi`');
      }
      else if (!self.query) {
        self.query = SDK_INFO.versionString;
      }
      else {
        throw new Error('Unexpected data type provided for `query` setting: '+self.query);
      }

      // Determine whether this is a cross-origin socket by examining the
      // hostname and port on the `window.location` object.  If it's cross-origin,
      // we'll attempt to get a cookie for the domain so that a Sails session can
      // be established.
      var isXOrigin = (function (){

        // If `window` doesn't exist (i.e. being used from Node.js), then
        // we won't bother attempting to get a cookie.  If you're using sockets
        // from Node.js and find you need to share a session between multiple
        // socket connections, you'll need to make an HTTP request to the /__getcookie
        // endpoint of the Sails server (or any endpoint that returns a set-cookie header)
        // and then use the cookie value in the `initialConnectionHeaders` option to
        // io.sails.connect()
        if (typeof window === 'undefined' || typeof window.location === 'undefined') {
          return false;
        }

        // If `self.url` (aka "target") is falsy, then we don't need to worry about it.
        if (typeof self.url !== 'string') { return false; }

        // Get information about the "target" (`self.url`)
        var targetProtocol = (function (){
          try {
            targetProtocol = self.url.match(/^([a-z]+:\/\/)/i)[1].toLowerCase();
          }
          catch (e) {}
          targetProtocol = targetProtocol || 'http://';
          return targetProtocol;
        })();
        var isTargetSSL = !!self.url.match('^https');
        var targetPort = (function (){
          try {
            return self.url.match(/^[a-z]+:\/\/[^:]*:([0-9]*)/i)[1];
          }
          catch (e){}
          return isTargetSSL ? '443' : '80';
        })();
        var targetAfterProtocol = self.url.replace(/^([a-z]+:\/\/)/i, '');


        // If target protocol is different than the actual protocol,
        // then we'll consider this cross-origin.
        if (targetProtocol.replace(/[:\/]/g, '') !== window.location.protocol.replace(/[:\/]/g,'')) {
          return true;
        }


        // If target hostname is different than actual hostname, we'll consider this cross-origin.
        var hasSameHostname = targetAfterProtocol.search(window.location.hostname) === 0;
        if (!hasSameHostname) {
          return true;
        }

        // If no actual port is explicitly set on the `window.location` object,
        // we'll assume either 80 or 443.
        var isLocationSSL = window.location.protocol.match(/https/i);
        var locationPort = (window.location.port+'') || (isLocationSSL ? '443' : '80');

        // Finally, if ports don't match, we'll consider this cross-origin.
        if (targetPort !== locationPort) {
          return true;
        }

        // Otherwise, it's the same origin.
        return false;

      })();


      // Prepare to start connecting the socket
      (function selfInvoking (cb){

        // If this is an attempt at a cross-origin or cross-port
        // socket connection via a browswe, send a JSONP request
        // first to ensure that a valid cookie is available.
        // This can be disabled by setting `io.sails.useCORSRouteToGetCookie`
        // to false.
        //
        // Otherwise, skip the stuff below.
        //
        if (!(self.useCORSRouteToGetCookie && isXOrigin)) {
          return cb();
        }

        // Figure out the x-origin CORS route
        // (Sails provides a default)
        var xOriginCookieURL = self.url;
        if (typeof self.useCORSRouteToGetCookie === 'string') {
          xOriginCookieURL += self.useCORSRouteToGetCookie;
        }
        else {
          xOriginCookieURL += '/__getcookie';
        }

        // Make the AJAX request (CORS)
        jsonp({
          url: xOriginCookieURL,
          method: 'GET'
        }, cb);

      })(function goAheadAndActuallyConnect() {

        // Now that we're ready to connect, create a raw underlying Socket
        // using Socket.io and save it as `_raw` (this will start it connecting)
        self._raw = io(self.url, self);

        // If the low-level transport throws an error _while connecting_, then set the _isConnecting flag
        // to false (since we're no longer connecting with any chance of success anyway).
        // Also, in this case (and in dev mode only) log a helpful message.
        self._raw.io.engine.transport.on('error', function(err){
          if (!self._isConnecting) { return; }

          self._isConnecting = false;

          // Track this timestamp for use in reconnection messages
          // (only relevant if reconnection is enabled.)
          self.connectionErrorTimestamp = (new Date()).getTime();

          // Development-only message:
          consolog('====================================');
          consolog('The socket was unable to connect.');
          consolog('The server may be offline, or the');
          consolog('socket may have failed authorization');
          consolog('based on its origin or other factors.');
          consolog('You may want to check the values of');
          consolog('`sails.config.sockets.onlyAllowOrigins`');
          consolog('or (more rarely) `sails.config.sockets.beforeConnect`');
          consolog('in your app.');
          consolog('More info: https://sailsjs.com/config/sockets');
          consolog('For help: https://sailsjs.com/support');
          consolog('');
          consolog('Technical details:');
          consolog(err);
          consolog('====================================');
        });

        // Replay event bindings from the eager socket
        self.replay();


        /**
         * 'connect' event is triggered when the socket establishes a connection
         *  successfully.
         */
        self.on('connect', function socketConnected() {
          self._isConnecting = false;
          consolog.noPrefix(
            '\n' +
            '\n' +
            // '    |>    ' + '\n' +
            // '  \\___/  '+️
            // '\n'+
             '  |>    Now connected to '+(self.url ? self.url : 'Sails')+'.' + '\n' +
            '\\___/   For help, see: http://bit.ly/2q0QDpf' + '\n' +
             '        (using sails.io.js '+io.sails.sdk.platform+' SDK @v'+io.sails.sdk.version+')'+ '\n' +
            '         Connected at: '+(new Date())+'\n'+
            '\n'+
            '\n'+
            // '\n'+
            ''
            // ' ⚓︎ (development mode)'
            // 'e.g. to send a GET request to Sails via WebSockets, run:'+ '\n' +
            // '`io.socket.get("/foo", function serverRespondedWith (body, jwr) { console.log(body); })`'+ '\n' +
          );
        });

        self.on('disconnect', function() {

          // Get a timestamp of when the disconnect was detected.
          self.connectionLostTimestamp = (new Date()).getTime();

          // Get a shallow clone of the internal array of response callbacks, in case any of the callbacks mutate it.
          var responseCbs = [].concat(self._responseCbs || []);
          // Wipe the internal array of response callbacks before executing them, in case a callback happens to add
          // a new request to the queue.
          self._responseCbs = [];

          // Do the same for the internal request context list.
          var requestCtxs = [].concat(self._requestCtxs || []);
          self._requestCtxs = [];

          // Loop through the callbacks for all in-progress requests, and call them each with an error indicating the disconnect.
          if (responseCbs.length) {
            responseCbs.forEach(function(responseCb) {
              responseCb(new Error('The socket disconnected before the request completed.'), {
                body: null,
                statusCode: 0,
                headers: {}
              });
            });
          }

          // If there is a list of request contexts, indicate that their callbacks have been
          // called and then wipe the list.  This prevents errors in the edge case of a response
          // somehow coming back after the socket reconnects.
          if (requestCtxs.length) {
            requestCtxs.forEach(function(requestCtx) {
              requestCtx.calledCb = true;
            });
          }

          consolog('====================================');
          consolog('Socket was disconnected from Sails.');
          consolog('Usually, this is due to one of the following reasons:' + '\n' +
            ' -> the server ' + (self.url ? self.url + ' ' : '') + 'was taken down' + '\n' +
            ' -> your browser lost internet connectivity');
          consolog('====================================');
        });

        self.on('reconnecting', function(numAttempts) {
          consolog(
            '\n'+
            '        Socket is trying to reconnect to '+(self.url ? self.url : 'Sails')+'...\n'+
            '_-|>_-  (attempt #' + numAttempts + ')'+'\n'+
            '\n'
          );
        });

        self.on('reconnect', function(transport, numAttempts) {
          if (!self._isConnecting) {
            self.on('connect', runRequestQueue.bind(self, self));
          }

          var msSinceLastOffline;
          var numSecsOffline;
          if (self.connectionLostTimestamp){
            msSinceLastOffline = ((new Date()).getTime() - self.connectionLostTimestamp);
            numSecsOffline = (msSinceLastOffline / 1000);
          }
          else if (self.connectionErrorTimestamp) {
            msSinceLastOffline = ((new Date()).getTime() - self.connectionErrorTimestamp);
            numSecsOffline = (msSinceLastOffline / 1000);
          }
          else {
            msSinceLastOffline = '???';
            numSecsOffline = '???';
          }

          consolog(
            '\n'+
             '  |>    Socket reconnected successfully after'+'\n'+
            '\\___/   being offline at least ' + numSecsOffline + ' seconds.'+'\n'+
            '\n'
          );
        });

        // 'error' event is triggered if connection can not be established.
        // (usually because of a failed authorization, which is in turn
        // usually due to a missing or invalid cookie)
        self.on('error', function failedToConnect(err) {
          self._isConnecting = false;
          ////////////////////////////////////////////////////////////////////////////////////
          // Note:
          // In the future, we could provide a separate event for when a socket cannot connect
          // due to a failed `beforeConnect` (aka "authorization" if you're old school).
          // this could probably be implemented by emitting a special event from the server.
          ////////////////////////////////////////////////////////////////////////////////////

          consolog(
            'Failed to connect socket (possibly due to failed `beforeConnect` on server)',
            'Error:', err
          );
        });
      });

    };

    /**
     * Reconnect the underlying socket.
     *
     * @api public
     */
    SailsSocket.prototype.reconnect = function (){
      if (this._isConnecting) {
        throw new Error('Cannot connect- socket is already connecting');
      }
      if (this.isConnected()) {
        throw new Error('Cannot connect- socket is already connected');
      }
      return this._connect();
    };

    /**
     * Disconnect the underlying socket.
     *
     * @api public
     */
    SailsSocket.prototype.disconnect = function (){
      this._isConnecting = false;
      if (!this.isConnected()) {
        throw new Error('Cannot disconnect- socket is already disconnected');
      }
      return this._raw.disconnect();
    };



    /**
     * isConnected
     *
     * @return {Boolean} whether the socket is connected and able to
     *                   communicate w/ the server.
     */

    SailsSocket.prototype.isConnected = function () {
      if (!this._raw) {
        return false;
      }

      return !!this._raw.connected;
    };


    /**
     * isConnecting
     *
     * @return {Boolean} whether the socket is in the process of connecting
     *                   to the server.
     */

    SailsSocket.prototype.isConnecting = function () {
      return this._isConnecting;
    };

    /**
     * isConnecting
     *
     * @return {Boolean} flag that is `true` after a SailsSocket instance is
     *                   initialized but before one tick of the event loop
     *                   has passed (so that it hasn't attempted to connect
     *                   yet, if autoConnect ends up being configured `true`)
     */
    SailsSocket.prototype.mightBeAboutToAutoConnect = function() {
      return this._mightBeAboutToAutoConnect;
    };

    /**
     * [replay description]
     * @return {[type]} [description]
     */
    SailsSocket.prototype.replay = function (){
      var self = this;

      // Pass events and a reference to the request queue
      // off to the self._raw for consumption
      for (var evName in self.eventQueue) {
        for (var i in self.eventQueue[evName]) {
          self._raw.on(evName, self.eventQueue[evName][i]);
        }
      }

      // Bind a one-time function to run the request queue
      // when the self._raw connects.
      if ( !self.isConnected() ) {
        self._raw.once('connect', runRequestQueue.bind(self, self));
      }
      // Or run it immediately if self._raw is already connected
      else {
        runRequestQueue(self);
      }

      return self;
    };


    /**
     * Chainable method to bind an event to the socket.
     *
     * @param  {String}   evName [event name]
     * @param  {Function} fn     [event handler function]
     * @return {SailsSocket}
     */
    SailsSocket.prototype.on = function (evName, fn){

      // Bind the event to the raw underlying socket if possible.
      if (this._raw) {
        this._raw.on(evName, fn);
        return this;
      }

      // Otherwise queue the event binding.
      if (!this.eventQueue[evName]) {
        this.eventQueue[evName] = [fn];
      }
      else {
        this.eventQueue[evName].push(fn);
      }

      return this;
    };

    /**
     * Chainable method to unbind an event from the socket.
     *
     * @param  {String}   evName [event name]
     * @param  {Function} fn     [event handler function]
     * @return {SailsSocket}
     */
    SailsSocket.prototype.off = function (evName, fn){

      // Bind the event to the raw underlying socket if possible.
      if (this._raw) {
        this._raw.off(evName, fn);
        return this;
      }

      // Otherwise queue the event binding.
      if (this.eventQueue[evName] && this.eventQueue[evName].indexOf(fn) > -1) {
        this.eventQueue[evName].splice(this.eventQueue[evName].indexOf(fn), 1);
      }

      return this;
    };


    /**
     * Chainable method to unbind all events from the socket.
     *
     * @return {SailsSocket}
     */
    SailsSocket.prototype.removeAllListeners = function (){

      // Bind the event to the raw underlying socket if possible.
      if (this._raw) {
        this._raw.removeAllListeners();
        return this;
      }

      // Otherwise queue the event binding.
      this.eventQueue = {};

      return this;
    };

    /**
     * Simulate a GET request to sails
     * e.g.
     *    `socket.get('/user/3', Stats.populate)`
     *
     * @api public
     * @param {String} url    ::    destination URL
     * @param {Object} data   ::    parameters to send with the request [optional]
     * @param {Function} cb   ::    callback function to call when finished [optional]
     */

    SailsSocket.prototype.get = function(url, data, cb) {

      // `data` is optional
      if (typeof data === 'function') {
        cb = data;
        data = {};
      }

      return this.request({
        method: 'get',
        params: data,
        url: url
      }, cb);
    };



    /**
     * Simulate a POST request to sails
     * e.g.
     *    `socket.post('/event', newMeeting, $spinner.hide)`
     *
     * @api public
     * @param {String} url    ::    destination URL
     * @param {Object} data   ::    parameters to send with the request [optional]
     * @param {Function} cb   ::    callback function to call when finished [optional]
     */

    SailsSocket.prototype.post = function(url, data, cb) {

      // `data` is optional
      if (typeof data === 'function') {
        cb = data;
        data = {};
      }

      return this.request({
        method: 'post',
        data: data,
        url: url
      }, cb);
    };



    /**
     * Simulate a PUT request to sails
     * e.g.
     *    `socket.post('/event/3', changedFields, $spinner.hide)`
     *
     * @api public
     * @param {String} url    ::    destination URL
     * @param {Object} data   ::    parameters to send with the request [optional]
     * @param {Function} cb   ::    callback function to call when finished [optional]
     */

    SailsSocket.prototype.put = function(url, data, cb) {

      // `data` is optional
      if (typeof data === 'function') {
        cb = data;
        data = {};
      }

      return this.request({
        method: 'put',
        params: data,
        url: url
      }, cb);
    };


    /**
     * Simulate a PATCH request to sails
     * e.g.
     *    `socket.patch('/event/3', changedFields, $spinner.hide)`
     *
     * @api public
     * @param {String} url    ::    destination URL
     * @param {Object} data   ::    parameters to send with the request [optional]
     * @param {Function} cb   ::    callback function to call when finished [optional]
     */

    SailsSocket.prototype.patch = function(url, data, cb) {

      // `data` is optional
      if (typeof data === 'function') {
        cb = data;
        data = {};
      }

      return this.request({
        method: 'patch',
        params: data,
        url: url
      }, cb);
    };

    /**
     * Simulate a DELETE request to sails
     * e.g.
     *    `socket.delete('/event', $spinner.hide)`
     *
     * @api public
     * @param {String} url    ::    destination URL
     * @param {Object} data   ::    parameters to send with the request [optional]
     * @param {Function} cb   ::    callback function to call when finished [optional]
     */

    SailsSocket.prototype['delete'] = function(url, data, cb) {

      // `data` is optional
      if (typeof data === 'function') {
        cb = data;
        data = {};
      }

      return this.request({
        method: 'delete',
        params: data,
        url: url
      }, cb);
    };



    /**
     * Simulate an HTTP request to sails
     * e.g.
     * ```
     * socket.request({
     *   url:'/user',
     *   params: {},
     *   method: 'POST',
     *   headers: {}
     * }, function (responseBody, JWR) {
     *   // ...
     * });
     * ```
     *
     * @api public
     * @option {String} url    ::    destination URL
     * @option {Object} params ::    parameters to send with the request [optional]
     * @option {Object} headers::    headers to send with the request [optional]
     * @option {Function} cb   ::    callback function to call when finished [optional]
     * @option {String} method ::    HTTP request method [optional]
     */

    SailsSocket.prototype.request = function(options, cb) {

      var usage =
      'Usage:\n'+
      'socket.request( options, [fnToCallWhenComplete] )\n\n'+
      'options.url :: e.g. "/foo/bar"'+'\n'+
      'options.method :: e.g. "get", "post", "put", or "delete", etc.'+'\n'+
      'options.params :: e.g. { emailAddress: "mike@example.com" }'+'\n'+
      'options.headers :: e.g. { "x-my-custom-header": "some string" }';
      // Old usage:
      // var usage = 'Usage:\n socket.'+(options.method||'request')+'('+
      //   ' destinationURL, [dataToSend], [fnToCallWhenComplete] )';


      // Validate options and callback
      if (typeof cb !== 'undefined' && typeof cb !== 'function') {
        throw new Error('Invalid callback function!\n' + usage);
      }
      if (typeof options !== 'object' || typeof options.url !== 'string') {
        throw new Error('Invalid or missing URL!\n' + usage);
      }
      if (options.method && typeof options.method !== 'string') {
        throw new Error('Invalid `method` provided (should be a string like "post" or "put")\n' + usage);
      }
      if (options.headers && typeof options.headers !== 'object') {
        throw new Error('Invalid `headers` provided (should be a dictionary with string values)\n' + usage);
      }
      if (options.params && typeof options.params !== 'object') {
        throw new Error('Invalid `params` provided (should be a dictionary with JSON-serializable values)\n' + usage);
      }
      if (options.data && typeof options.data !== 'object') {
        throw new Error('Invalid `data` provided (should be a dictionary with JSON-serializable values)\n' + usage);
      }

      // Accept either `params` or `data` for backwards compatibility (but not both!)
      if (options.data && options.params) {
        throw new Error('Cannot specify both `params` and `data`!  They are aliases of each other.\n' + usage);
      }
      else if (options.data) {
        options.params = options.data;
        delete options.data;
      }


      // If this socket is not connected yet, queue up this request
      // instead of sending it.
      // (so it can be replayed when the socket comes online.)
      if ( ! this.isConnected() ) {

        // If no queue array exists for this socket yet, create it.
        this.requestQueue = this.requestQueue || [];
        this.requestQueue.push([options, cb]);
        return;
      }

      // Otherwise, our socket is connected, so continue prepping
      // the request.

      // Default headers to an empty object
      options.headers = options.headers || {};

      // Build a simulated request object
      // (and sanitize/marshal options along the way)
      var requestCtx = {

        method: (options.method || 'get').toLowerCase(),

        headers: options.headers,

        data: options.params || options.data || {},

        // Remove trailing slashes and spaces to make packets smaller.
        url: options.url.replace(/^(.+)\/*\s*$/, '$1'),

        cb: cb
      };

      // Get a reference to the callback list, or create a new one.
      this._responseCbs = this._responseCbs || [];

      // Get a reference to the request context list, or create a new one.
      this._requestCtxs = this._requestCtxs || [];

      // Add this callback to the list.  If the socket disconnects, we'll call
      // each cb in the list with an error and reset the list.  Otherwise the
      // cb will be removed from the list when the server responds.
      // Also add the request context to the list.  It will be removed once
      // the response comes back, or if the socket disconnects.
      if (cb) {
        this._responseCbs.push(cb);
        this._requestCtxs.push(requestCtx);
      }

      // Merge global headers in, if there are any.
      if (this.headers && 'object' === typeof this.headers) {
        for (var header in this.headers) {
          if (!options.headers.hasOwnProperty(header)) {
            options.headers[header] = this.headers[header];
          }
        }
      }

      // Send the request.
      _emitFrom(this, requestCtx);
    };



    /**
     * Socket.prototype._request
     *
     * Simulate HTTP over Socket.io.
     *
     * @api private
     * @param  {[type]}   options [description]
     * @param  {Function} cb      [description]
     */
    SailsSocket.prototype._request = function(options, cb) {
      throw new Error('`_request()` was a private API deprecated as of v0.11 of the sails.io.js client. Use `.request()` instead.');
    };







    //  ██╗ ██████╗    ███████╗ █████╗ ██╗██╗     ███████╗
    //  ██║██╔═══██╗   ██╔════╝██╔══██╗██║██║     ██╔════╝
    //  ██║██║   ██║   ███████╗███████║██║██║     ███████╗
    //  ██║██║   ██║   ╚════██║██╔══██║██║██║     ╚════██║
    //  ██║╚██████╔╝██╗███████║██║  ██║██║███████╗███████║
    //  ╚═╝ ╚═════╝ ╚═╝╚══════╝╚═╝  ╚═╝╚═╝╚══════╝╚══════╝
    //
    // Set an `io.sails` object that may be used for configuration before the
    // first socket connects (i.e. to allow auto-connect behavior to be
    // prevented by setting `io.sails.autoConnect` in an inline script
    // directly after the script tag which loaded this file).


    //  ┌─┐┌─┐┌┬┐  ┬ ┬┌─┐  ╔╦╗╔═╗╔═╗╔═╗╦ ╦╦ ╔╦╗╔═╗  ┌─┐┌─┐┬─┐  ┬┌─┐ ┌─┐┌─┐┬┬  ┌─┐
    //  └─┐├┤  │   │ │├─┘   ║║║╣ ╠╣ ╠═╣║ ║║  ║ ╚═╗  ├┤ │ │├┬┘  ││ │ └─┐├─┤││  └─┐
    //  └─┘└─┘ ┴   └─┘┴    ═╩╝╚═╝╚  ╩ ╩╚═╝╩═╝╩ ╚═╝  └  └─┘┴└─  ┴└─┘o└─┘┴ ┴┴┴─┘└─┘
    io.sails = {

      // Whether to automatically connect a socket and save it as `io.socket`.
      autoConnect: true,

      // Whether to automatically try to reconnect after connection is lost
      reconnection: false,

      // The route (path) to hit to get a x-origin (CORS) cookie
      // (or true to use the default: '/__getcookie')
      useCORSRouteToGetCookie: true,

      // The environment we're running in.
      // (logs are not displayed when this is set to 'production')
      //
      // Defaults to "development" unless this script was fetched from a URL
      // that ends in `*.min.js` or '#production', or if the conventional
      // `SAILS_LOCALS` global is set with an `_environment` of "production"
      // or "staging".  (This setting may also be manually overridden.)
      environment: (
        urlThisScriptWasFetchedFrom.match(/(\#production|\.min\.js)/g) ||
        (
          typeof window === 'object' && window &&
          typeof window.SAILS_LOCALS === 'object' && window.SAILS_LOCALS &&
          (window.SAILS_LOCALS._environment === 'staging' || window.SAILS_LOCALS._environment === 'production')
        )
      )? 'production' : 'development',

      // The version of this sails.io.js client SDK
      sdk: SDK_INFO,

      // Transports to use when communicating with the server, in the order they will be tried
      transports: ['websocket']
    };



    //  ┌─┐─┐ ┬┌┬┐┌─┐┌┐┌┌┬┐  ┬┌─┐ ┌─┐┌─┐┬┬  ┌─┐  ┌┬┐┌─┐┌─┐┌─┐┬ ┬┬ ┌┬┐┌─┐
    //  ├┤ ┌┴┬┘ │ ├┤ │││ ││  ││ │ └─┐├─┤││  └─┐   ││├┤ ├┤ ├─┤│ ││  │ └─┐
    //  └─┘┴ └─ ┴ └─┘┘└┘─┴┘  ┴└─┘o└─┘┴ ┴┴┴─┘└─┘  ─┴┘└─┘└  ┴ ┴└─┘┴─┘┴ └─┘
    //  ┬ ┬┬┌┬┐┬ ┬  ┌┬┐┬ ┬┌─┐  ╦ ╦╔╦╗╔╦╗╦    ╔═╗╔╦╗╔╦╗╦═╗╦╔╗ ╦ ╦╔╦╗╔═╗╔═╗
    //  ││││ │ ├─┤   │ ├─┤├┤   ╠═╣ ║ ║║║║    ╠═╣ ║  ║ ╠╦╝║╠╩╗║ ║ ║ ║╣ ╚═╗
    //  └┴┘┴ ┴ ┴ ┴   ┴ ┴ ┴└─┘  ╩ ╩ ╩ ╩ ╩╩═╝  ╩ ╩ ╩  ╩ ╩╚═╩╚═╝╚═╝ ╩ ╚═╝╚═╝
    //  ┌─┐┬─┐┌─┐┌┬┐  ┌┬┐┬ ┬┌─┐  ┌─┐┌─┐┬─┐┬┌─┐┌┬┐  ┌┬┐┌─┐┌─┐
    //  ├┤ ├┬┘│ ││││   │ ├─┤├┤   └─┐│  ├┬┘│├─┘ │    │ ├─┤│ ┬
    //  └  ┴└─└─┘┴ ┴   ┴ ┴ ┴└─┘  └─┘└─┘┴└─┴┴   ┴    ┴ ┴ ┴└─┘
    //
    // Now fold in config provided as HTML attributes on the script tag:
    // (note that if `io.sails.*` is changed after this script, those changes
    //  will still take precedence)
    CONFIGURABLE_VIA_HTML_ATTR.forEach(function (configKey){
      if (typeof scriptTagConfig[configKey] !== 'undefined') {
        io.sails[configKey] = scriptTagConfig[configKey];
      }
    });
    //////////////////////////////////////////////////////////////////////////////
    // Note that the new HTML attribute configuration style may eventually
    // completely replace the original approach of setting `io.sails` properties,
    // since the new strategy is easier to reason about.  Also, it would allow us
    // to remove the timeout below someday.
    //////////////////////////////////////////////////////////////////////////////




    //  ┬┌─┐ ┌─┐┌─┐┬┬  ┌─┐ ╔═╗╔═╗╔╗╔╔╗╔╔═╗╔═╗╔╦╗  /  \
    //  ││ │ └─┐├─┤││  └─┐ ║  ║ ║║║║║║║║╣ ║   ║  /   /
    //  ┴└─┘o└─┘┴ ┴┴┴─┘└─┘o╚═╝╚═╝╝╚╝╝╚╝╚═╝╚═╝ ╩  \  /

    /**
     * Add `io.sails.connect` function as a wrapper for the built-in `io()` aka `io.connect()`
     * method, returning a SailsSocket. This special function respects the configured io.sails
     * connection URL, as well as sending other identifying information (most importantly, the
     * current version of this SDK).
     *
     * @param  {String} url  [optional]
     * @param  {Object} opts [optional]
     * @return {Socket}
     */
    io.sails.connect = function(url, opts) {

      // Make URL optional
      if ('object' === typeof url) {
        opts = url;
        url = null;
      }

      // Default opts to empty object
      opts = opts || {};

      // If explicit connection url is specified, save it to options
      opts.url = url || opts.url || undefined;

      // Instantiate and return a new SailsSocket- and try to connect immediately.
      var socket = new SailsSocket(opts);
      socket._connect();
      return socket;
    };






    //  ██╗ ██████╗    ███████╗ ██████╗  ██████╗██╗  ██╗███████╗████████╗
    //  ██║██╔═══██╗   ██╔════╝██╔═══██╗██╔════╝██║ ██╔╝██╔════╝╚══██╔══╝
    //  ██║██║   ██║   ███████╗██║   ██║██║     █████╔╝ █████╗     ██║
    //  ██║██║   ██║   ╚════██║██║   ██║██║     ██╔═██╗ ██╔══╝     ██║
    //  ██║╚██████╔╝██╗███████║╚██████╔╝╚██████╗██║  ██╗███████╗   ██║
    //  ╚═╝ ╚═════╝ ╚═╝╚══════╝ ╚═════╝  ╚═════╝╚═╝  ╚═╝╚══════╝   ╚═╝
    //
    // io.socket
    //
    // The eager instance of Socket which will automatically try to connect
    // using the host that this js file was served from.
    //
    // This can be disabled or configured by setting properties on `io.sails.*` within the
    // first cycle of the event loop.
    //


    // Build `io.socket` so it exists
    // (note that this DOES NOT start the connection process)
    io.socket = new SailsSocket();
    //
    // This socket is not connected yet, and has not even _started_ connecting.
    //
    // But in the mean time, this eager socket will be queue events bound by the user
    // before the first cycle of the event loop (using `.on()`), which will later
    // be rebound on the raw underlying socket.


    //  ┌─┐┌─┐┌┬┐  ┌─┐┬ ┬┌┬┐┌─┐   ┌─┐┌─┐┌┐┌┌┐┌┌─┐┌─┐┌┬┐  ┌┬┐┬┌┬┐┌─┐┬─┐
    //  └─┐├┤  │   ├─┤│ │ │ │ │───│  │ │││││││├┤ │   │    │ ││││├┤ ├┬┘
    //  └─┘└─┘ ┴   ┴ ┴└─┘ ┴ └─┘   └─┘└─┘┘└┘┘└┘└─┘└─┘ ┴    ┴ ┴┴ ┴└─┘┴└─
    // If configured to do so, start auto-connecting after the first cycle of the event loop
    // has completed (to allow time for this behavior to be configured/disabled
    // by specifying properties on `io.sails`)

    // Indicate that the autoConnect timer has started.
    io.socket._mightBeAboutToAutoConnect = true;

    setTimeout(function() {

      // Indicate that the autoConect timer fired.
      io.socket._mightBeAboutToAutoConnect = false;

      // If autoConnect is disabled, delete the eager socket (io.socket) and bail out.
      if (io.sails.autoConnect === false || io.sails.autoconnect === false) {
        delete io.socket;
        return;
      }

      // consolog('Eagerly auto-connecting socket to Sails... (requests will be queued in the mean-time)');
      io.socket._connect();


    }, 0); // </setTimeout>


    // Return the `io` object.
    return io;
  } //</SailsIOClient>

  //
  /////////////////////////////////////////////////////////////////////////////////
  ///// </bunches of private function definitions, constructors, and methods>
  /////////////////////////////////////////////////////////////////////////////////




  //  ███████╗██╗  ██╗██████╗  ██████╗ ███████╗███████╗    ███████╗██████╗ ██╗  ██╗
  //  ██╔════╝╚██╗██╔╝██╔══██╗██╔═══██╗██╔════╝██╔════╝    ██╔════╝██╔══██╗██║ ██╔╝
  //  █████╗   ╚███╔╝ ██████╔╝██║   ██║███████╗█████╗      ███████╗██║  ██║█████╔╝
  //  ██╔══╝   ██╔██╗ ██╔═══╝ ██║   ██║╚════██║██╔══╝      ╚════██║██║  ██║██╔═██╗
  //  ███████╗██╔╝ ██╗██║     ╚██████╔╝███████║███████╗    ███████║██████╔╝██║  ██╗
  //  ╚══════╝╚═╝  ╚═╝╚═╝      ╚═════╝ ╚══════╝╚══════╝    ╚══════╝╚═════╝ ╚═╝  ╚═╝
  //


  // Add CommonJS support to allow this client SDK to be used from Node.js.
  if (SDK_INFO.platform === 'node') {
    module.exports = SailsIOClient;
  }
  // Add AMD support, registering this client SDK as an anonymous module.
  else if (typeof define === 'function' && define.amd) {
    define([], function() {
      return SailsIOClient;
    });
  }
  else {
    // Otherwise, try to instantiate the client using the global `io`:
    SailsIOClient();

    // Note:
    // If you are modifying this file manually to wrap an existing socket.io client
    // (e.g. to prevent pollution of the global namespace), you can replace the global
    // `io` with your own `io` instance above.
  }

})();
;

/* eslint-enable */
