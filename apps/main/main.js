appManager={
	name:'appManager',
	launchApp:function(appName){
		//load specific css file		
		
		//load the html file		
		
		//load the javascript file
		 var full_path='./apps/'+appName+'/'+appName+'.js';
		 var success_callback =null;
		
		jQuery.ajax({
			async: false,
			type: "GET",
			url: full_path,
			data: null,
			success: success_callback,
			dataType: 'script'
		});

		//add this app to the runningApps array
		this.runningApps.push(appName);

	},
	closeApp:function(appName){
		for (var $i=0; $i<=this.runningApps.length; $i++){
			if(this.runningApps[$i]==appName){
				//alert('removed app');
				//qua dobbiamo riimuovere lo script e l'html				
					
			}
		}
	},
	runningApps:new Array()

}

/*
$(document).ready(function() {
	appManager.launchApp('test');
	appManager.closeApp('test');
});
*/