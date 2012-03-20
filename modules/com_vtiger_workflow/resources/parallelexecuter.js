function parallelExecuter(executer, operationCount){
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
}