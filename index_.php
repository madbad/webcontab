<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>WebContab</title>
<meta name="generator" content="Bluefish 2.0.3" >
<meta name="author" content="madbad" >
<meta name="date" content="2011-03-27T18:13:02+0200" >
<meta name="copyright" content="">
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="./favicon.ico" />

<!--ExtJs-->
<script src="./my/js/ext.js/ext-all.js" type="text/javascript"></script>
<link href="./my/js/ext.js/resources/css/ext-all.css" rel="stylesheet" type="text/css">
<script type="text/javascript">

Ext.require(['*']);


</script>

<!--myLibs-->
<script type="text/javascript">

	function oRow(){
		this.rowCount=0;
		this.add=function(){
			if (this.rowCount==0){
				this.rowCount=10;
			}else{
				this.rowCount+=40;
			}
			return this.rowCount;
		}
		this.reset=function(){
			this.rowCount=10;
			return this.rowCount;
		}
		return this;
	}
row= new oRow();
</script>
<script src="./my/js/apps.js?<?php echo rand(); ?>" type="text/javascript"></script>

<script type="text/javascript">
Ext.onReady(function(){
	Ext.FocusManager.enable({focusFrame: true});
	//alert(Ext.FocusManager.enabled);
  		//Ext.Date.defaultFormat('d/m/Y');  
	
	mainMenu();
		
	//gestioneDdt();
	/*
	windows=ddtWindow();
	windows.on('close',function(){
		this.result.get('data');
		this.result.get('numero');
	});
	*/

});



</script>
</head>
<body>
</body>
</html>