//alert("Loaded test app");





content='****';
//alert(content);
		//load the javascript file
		 var full_path='./apps/test/test.html';
		 var success_callback =function(data){content=data;};
		
		jQuery.ajax({
			async: false,
			type: "GET",
			url: full_path,
			data: null,
			success: success_callback,
			dataType: 'script'
		});
		//alert(content);

/*
var content='g--------fdgdf';


prova=$.get('./apps/test/test.html', function(data) {
  //alert(data);
  alert(content)
  content=data;
//  content='prova'
    alert('1********'+content)

});
alert(content)
//alert(prova);
*/




var $opt=new Array();
$opt['name']='bfooo';
$opt['title']='Titolo fighissimo';
$opt['width']=800;
$opt['height']=800;
//$opt['top']=10;
//$opt['left']=10;
$opt['position']='bottom-left';
$opt['this']=document.body;


$opt['txt']=content;
/*
$opt['isVisible']		-> Indica se a finestra deve essere visualizzata o meno alla creazione | valore [true|false]
$opt['isTip']			-> Indica se a finestra deve avere o meno la freccetta tips | valore [true|false]
$opt['isPermanent']	-> Se impostato a true la finestra non viene chiusa ma solo nascosta | valore [true|false]
$opt['autoCloseTime']-> Chiusura automatica onmouseout | valore [millisecondi]
*/


$opt['title']='['+test+'] '+'Titolo fighissimo';
	
new MyWindow($opt);


