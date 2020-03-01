 $(document).ready(function(){
   // Your code here
   $("a").click(function(event){
      alert("Thanks for visiting!");
      $(this).hide("slow");
      event.preventDefault();

   });
});

 $(document).ready(function() {
   $("#orderedlist li").click(function() {
     $(this).addClass("active");
   },function(){
     $(this).removeClass("active");
   });
 });

/*
var Menu={
	init:function(args){
		alert('test');
	},
	append:function(args){
		
	}	
}

var myMenu=new Menu;
myMenu.init();
*/
alert('test');