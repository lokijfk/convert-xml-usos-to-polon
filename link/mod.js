$(function() {
	//===FF===
//-------- FILTROWANIE ------
var head=$("#head");
var body = $("#body");	
var buttons = null;
var pole = null;
function _rem(){
	buttons.remove();
	buttons = null;
	pole.blur();
	pole = null;
}

head.find("input")
	.keyup(function() {
		// Zmienne indywidualne dla kazdej znalezionej komurki
        var value = $( this ).val().toUpperCase();
        var name = $(this).attr("name");        
     // --END-- Zmienne indywidualne dla kazdej znalezionej komurki
		//body.find("td[name='"+name+"']")
		$("#body td[name='"+name+"']")
		.each(function(){
			var parent = $(this).parent();
			if(parent.data("hide")===undefined){
				//obiekt do przechowywania znaczników filtracji
				var hide={
						count : function () {//funkcja liczaca metody i własciwości obiektu, zwraca minimum 1, czyli ze ona sama jest
						    var count = 0;
						    for(var prop in this) {
						        if(this.hasOwnProperty(prop))// jezeli obiekt jest właścicielem właściwości/ metody
						            count = count + 1;
						    }
						    return count;
						}
						};
				//--END-- obiekt do przechowywania znaczników filtracji
				parent.data("hide",hide);				
			}
			var tekst = $(this).text().toUpperCase();// zwraca tekst z komurki przekonwertowany na duże litery			
			if (value.substring(0,1)==="-"){//1  jeżeli tekst zaczyna się od  '-' to będzie to wylkuczenie podanej wartości
				if(value.length > 1){
				var value2= value.substring(1);
				if (tekst.indexOf(value2) === 0){//2					
					parent.hide();
					parent.data("hide")[name]=true;			
				}else{
					if(parent.data("hide")[name]){
						delete parent.data("hide")[name];
						if(parent.data("hide").count() ==1)
							parent.show();
					}
				}
				} //2
			}else{//1
			if (tekst.indexOf(value) != 0){//2  wykluczenie wartości innych niż podana				
				parent.hide();				
				parent.data("hide")[name]=true;		
			}else{
				if(parent.data("hide")[name]){
					delete parent.data("hide")[name];
					
					if(parent.data("hide").count() ==1)
					parent.show();
				}
			}
			}//1
			});// EACH
      
        
     })//KEYUP
      .keyup();
//==END==-------- FILTROWANIE ------

//----------Edycja i wysyłanie----------------
	//body.find("td")
	$("#body td")
	.each(function(){
		var td = $(this);
		var name = td.attr("name");
		var value = td.text();	
		if((name != undefined)&&(name != ""))
			//td.html("<div contenteditable='true' >"+value+"</div>");//edytowalny div :)	
			td.html("<div contenteditable='true' style='float:left;'>"+value+"</div>");//edytowalny div :)	
			div = $("<div contenteditable='true' style='float:left;display:inline;'>"+value+"</div>").focus(function(){$(this).select();});
			td.html(div);//edytowalny div :)	
			//td.attr("contenteditable","true");
			td.click(function(){
				
        		
				if(td.find("div[name='button']").length ==0){
					if(buttons !== null)_rem();
					 buttons = $("<span>");
					var button_esc = $("<div>X</div>")
									.css("float","right")
									.css("margin-right","3px")
									.mouseover(function() {
										//$(this).css("color","red");
										$(this).addClass("red");
									})
									.mouseout(function() {
										//$(this).css("color","black");
										$(this).removeClass("red");
									})
									.mousedown(function(){ 
										_rem();
										//$(this).parent().remove();
										//$(this).remove();
									});
					var button_ok = $("<div>V</div>")
									.attr("name","button")
									.css("float","right")
									.mouseover(function() {
										//$(this).css("color","blue");
										$(this).addClass("red");
									})
									.mouseout(function() {
										//$(this).css("color","black");
										$(this).removeClass("red");
									})								
									.mousedown(function(){ 
										
										var lokalizacja = window.location;
										var form = $("<form action='"+lokalizacja+"' method='POST'>");
										//form.append($("<input type='hidden' name='aktualizacja' value='0'/>"));
										podstawa = $(this).parent().parent();
										_rem(); //musi być usuwanie bo dodaje przyciski jako wartość pola
										pesel = podstawa.parent().attr("id");
										pole = podstawa.attr("name");
										wartosc = podstawa.text();
										form.html("<input type='hidden' name='aktualizacja' value='0'/>"
												+"<input type='hidden' name='pesel' value='"+pesel+"'/>"
												+"<input type='hidden' name='pole' value='"+pole+"'/>"
												+"<input type='hidden' name='wartosc' value='"+wartosc+"'/>");
										form.submit();

									});
					pole = td.find("div").focus();   //tu coś niechcacy skasowałem i nie wiem co :(, nie pamiętam co ja tu miałem :(
					buttons.append(button_ok).append(button_esc);
					td.append(buttons);		
				}//if
				else{
					
				}
			});

	
		});// EACH
	 $(document).keydown( function(event) {  // dodać jeszcze naciśnięcie przycisku enter, ale nie wiem czy to będzie bezpieczne
		 //może jak zrobię  mały panel obsługi  u góry strony to wtedy dodam tam pole ze znacznikiem czy wysłac po enterze :)
	        if ( event.which == 27 ){   //esc
	        		if(buttons !== null){
	        			_rem();
	        		}//else alert("it's ok");
	        }   
	           
	           
	  } );
/*===FF===*/ });