function functional($){
	return {
    
	    /**
	     * Test:
	     * fn.format("Hello %s", "world") == "Hello world"
	     */
	    format: function(){
	        var i=1;
	        var fmtStr = arguments[0];
	        var args = arguments;
	        return fmtStr.replace(/%s/g,function(){return args[i++];})
	    },

		addStylesheet: function(url){
			/*From: http://www.hunlock.com/blogs/Howto_Dynamically_Insert_Javascript_And_CSS*/
			var headID = document.getElementsByTagName("head")[0];         
			var cssNode = document.createElement('link');
			cssNode.type = 'text/css';
			cssNode.rel = 'stylesheet';
			cssNode.href = url;
			cssNode.media = 'screen';
			headID.appendChild(cssNode);
		},

		id: function(v){
			return v;
		},
	
		map: function(fn, list){
			var out = [];
			$.each(list, function(i, v){
				out[out.length]=fn(v);
			});
			return out;
		},
	
		field: function(name){
			return function(object){
				return object[name];
			}
		},
	
		zip: function(){
			var out = [];
		
			var lengths = map(field('length'), arguments);
			var min = reduceR(function(a,b){return a<b?a:b},lengths,lengths[0]);
			for(var i=0; i<min; i++){
				out[i]=map(field(i), arguments);
			}
			return out;
		},
	
		dict: function(list){
			var out = {};
			$.each(list, function(i, v){
				out[v[0]] = v[1];
			});
			return out;
		},
	
		filter: function(pred, list){
			var out = [];
			$.each(list, function(i, v){
				if(pred(v)){
					out[out.length]=v;
				}
			});
			return out;
		},

		reduceR: function(fn, list, start){
			var acc = start;
			$.each(list, function(i, v){
				acc = fn(acc, v);
			});
			return acc;
		},
	
		contains: function(list, value){
			var ans = false;
			$.each(list, function(i, v){
				if(v==value){
					ans = true;
					return false;
				}
			});
			return ans;
		},
	
		concat: function(lista,listb){
			return lista.concat(listb);
		},
	
	
		mergeObjects: function(obj1, obj2){
			var res = {};
			for(var k in obj1){
				res[k] = obj1[k];
			}
			for(var k in obj2){
				res[k] = obj2[k];
			}
			return res;
		},
	
		parallelExecuter: function(executer, operationCount){
			var parameters = [];
			var n = 0;
			var ctr = 0;
			function makeParallel(operation){
				var id = n;
				n++;
				function cookie(){
					parameters[id] = arguments;
					ctr++;
					if(ctr == operationCount){
						executer(parameters);
					}
				}
				operation(cookie);
			}
			return makeParallel;
		},
	    /*
	     *Convert the last parameter into a list argument
	     */
	    larg: function (fn){
	    	var arity = fn.arity;
	    	var nparams = arity-1;
	        return function(){
	    		if(nparams>arguments.length){
	    			nparams = arguments.length;
	    		}

	    		var args = [];	
	    		for(var i=0;i<nparams;i++){
	    			args[i] = arguments[i];
	    		}

	    		var largs = [];
	    		alert(arguments.length-nparams);
	    		for(var i=0, n=arguments.length-nparams;i<n;i++){
	    			largs[i]=arguments[nparams+i];
	    		}
	    		args[args.length]=largs;
	    		return fn.apply(this, args);
	    	}
	    },
	
		htmlentities: function(s){
			var out = "";
			for(var i = 0; i<s.length;i++){
				out+="&#"+s.charCodeAt(i)+";"
			}
			return out;
		}
	}
}

fn = functional(jQuery);
