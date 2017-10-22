$(document).ready(function(){
    $('#divJornades').hide();
    $('#divAlarmes').hide();
    $('#amagarJornades').css('cursor','pointer');
    $('#amagarAlarmes').css('cursor','pointer');
    
    //$('#alertesTotals').
    
    
    
    
    $('#amagarJornades').click(function(){
        
        $('#divJornades').fadeToggle();
    });
    
    $('#amagarAlarmes').click(function(){
       
        $('#divAlarmes').fadeToggle();
    });
   
    
    
    
});