var jQT = new $.jQTouch({
    icon4: 'mcicon.png',
    icon: 'mciconNR.png',
    addGlossToIcon: true,
    startupScreen: 'startup2.png',
    statusBar: 'black-translucent',

    preloadImages: [
        'themes/apple/img/ajax-loader.gif'
        ]
});
$(function(){
    $('#apps').bind('pageAnimationStart', function(e, info){ 
            $info = $(this).find('.info');
            $info.html('<p>Checking app states&hellip;</p>');
            var total = $(this).find('li').length;
            var counter = 0;
            $('span.applaunch',$(this)).each(function(){
              var $aspan = $(this);
              $.get('functions.php?func=appstate&app='+$aspan.text(),function(data){
                counter++;
                if (data == "1") {
                  $aspan.parent('li').find('input').attr('checked','checked');
                  $info.html($aspan.text()+" is running");
                } else {
                  $info.html($aspan.text()+" is not running");
                }
                if (counter == total)
                  $info.html('<p></p>');
              });
            });
            // $(this).find('.info').html('<p>Finished</p>');
        });
    $('#apps input').change(function(){
      var app = $(this).parents('li').find('span.applaunch').text();
      startLoader($('#apps'));
      
      if ($(this).is(':checked')) {
	      $.get('functions.php?func=startapp&app='+app,function(data){
          stopLoader($('#apps'),data);
        });
      } else {
        $.get('functions.php?func=stopapp&app='+app,function(data){
          stopLoader($('#apps'),data);
        });
      }
      
      /*
if ($(this).val() == 'on') {
        $.get('functions.php?func=startapp&app='+app,function(data){
          stopLoader($('#apps'),data);
        });
      } else {
        $.get('functions.php?func=stopapp&app='+app,function(data){
          stopLoader($('#apps'),data);
        });
      }
*/
    });
    $('#volume').bind('pageAnimationStart', function(e, info){ 
      startLoader($('#volume'));
      $.get('functions.php?func=checkvol',function(data){
        stopLoader($('#volume'),'Current volume: '+data);
      });
    });
    $('#volumecmd li>a').click(function(ev){
      ev.preventDefault();
      startLoader($('#volume'));
      $.get('functions.php?func='+$(this).attr('rel'),function(data) {
        stopLoader($('#volume'),data);
      });
      $(this).removeClass('active');
      return false;
    });
    $('#testvolume').click(function(ev){
      ev.preventDefault();
      var oldinfo = $('#volume .info').html();
      startLoader($('#volume'));
      $.get('functions.php?func=testvol',function(data) {
        stopLoader($('#volume'),oldinfo);
      });
      $(this).removeClass('active');
      return false;                  
    });
    /*
$('#reboot').click(function(ev){
      ev.preventDefault();
      var oldinfo = $('#volume .info').html();
      startLoader($('#volume'));
      $.get('functions.php?func=reboot',function(data) {
        stopLoader($('#volume'),oldinfo);
      });
      $(this).removeClass('active');
      return false;                  
    });
    
*/
    $('#reboot').bind('tap', function(ev){
    var confirm_result = confirm('Are you sure you want to reboot?');

    if(confirm_result != false){
          ev.preventDefault();

      $.get('functions.php?func=reboot');
    }
      
      $(this).removeClass('active');
      return false;   
    });
    
    $('#identify').bind('tap', function(ev){

      $.get('functions.php?func=identify');
      
      $(this).removeClass('active');
      return false;   
    });
    
    
	
    $('#itunes').bind('pageAnimationStart', function(e, info){
      getTrackInfo();
    });
    // $('#itunes div.toolbar a.back').click(function(){
    //   $('#itunes li>a').unbind('click');
    // });
    var itunestimeout = 0;
    $('#itunes').bind('pageAnimationEnd', function(e, info){
      $('#itunescmd li>a').click(function(ev){
        ev.preventDefault();
        startLoader($('#itunes'));
        $.get('functions.php?func=itunes&cmd='+$(this).attr('rel'),function(d) {        
          stopLoader($('#itunes'),d);
          if (!(/^Current track/).test(d)) {
            clearTimeout(itunestimeout);
            itunestimeout = setTimeout("getTrackInfo()",1400);
          }
            
        });
        $(this).removeClass('active');
        return false;
      });
      $('#itunes .info').tap(function(e){
        getTrackInfo();
      });
    });
    $('#ituneseq li>a').click(function(ev){
      ev.preventDefault();
      startLoader($('#ituneseq'));
      var eqcmd = escape('set the current EQ preset to EQ preset "'+$(this).text()+'"');
      $.get('functions.php?func=itunes&cmd='+eqcmd,function(d) {        
        stopLoader($('#ituneseq'),d);
      });
      $(this).removeClass('active');
      return false;
    });
    $('#ituneseq').bind('pageAnimationStart', function(e, info){
      startLoader($('#ituneseq'));
      $.get('functions.php?func=itunes&info=eq',function(data){
        stopLoader($('#ituneseq'),data);
      });      
    });
});
function getTrackInfo() {
  startLoader($('#itunes'));
  $.get('functions.php?func=itunes&info=track',function(data){
    stopLoader($('#itunes'),data);
  });
}
function startLoader($el) {
  $el.find('.info').html('<p style="text-align:center"><img src="themes/apple/img/ajax-loader.gif" /></p>');
}
function stopLoader($el,data) {
  $el.find('.info').html('<p style="text-align:center">'+data+'</p>');
}