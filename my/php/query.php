<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>WebContab Calcolo costi</title>
		<meta charset="utf-8">

		<!--jQuery-->
		<script type="text/javascript" src="./jquery/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="./jquery/jquery-ui-1.8.1.custom.min.js"></script>
		<!--
		<link type="text/css" rel="stylesheet" href="./jquery/css/ui-darkness/jquery-ui-1.8.1.custom.css">
		-->
		<style type="text/css" media="print" />		
			 form{
				display:none;
			 }
		</style>
		<style type="text/css">
			@PAGE landscape {size: landscape;}
			TABLE {PAGE: landscape;} 
			@page rotated { size : landscape }
			table, tr, td , th{
				font-size:x-small;
				padding:0px;
				margin:0;
				text-align:right;
				border:1px solid #000000;
				    border-collapse: collapse;
				margin-left:0.5em;
			}
			td, th{
				padding-left:4px;
				padding-right:4px;
			}
			th{
				font-weight:bold;
				text-align:left;
			}
			hr{
				margin-top:150px;
			}
			#rimanenze td{
				height:2em;
				width:9em;
				text-align:left;
			}
			div {
				float:left;
			}
			form label{
				display:block;
				font-weight:bold;
				width:15 em;
			}
			.totali{
			 	 font-size:1.5em;
			}
		</style>
	</head>
	<body>
		<form name="input" action="./query.php" method="get">
		Cliente input or list
		Articolo input or list
		Dal
		al
			<button type="submit">Cerca</button>
		</form> 
</body>