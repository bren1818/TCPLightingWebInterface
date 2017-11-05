$(function(){
		
	$(document).on('change', 'input.jscolor', function() {
		
		console.log( 'Color change: ' +  Math.round(this.jscolor.rgb[0]) + ', ' +  Math.round(this.jscolor.rgb[1]) + ', ' +  Math.round(this.jscolor.rgb[2]) );
		if( $(this).parent().hasClass('light-on') ){
			$(this).parent().css('background-color', 'rgba(' + Math.round(this.jscolor.rgb[0]) + ',' + Math.round(this.jscolor.rgb[1]) + ',' + Math.round(this.jscolor.rgb[2]) + ',1)');
		}
		
		$.get("api.php?fx=color&r=" + Math.round(this.jscolor.rgb[0]) + "&g=" + Math.round(this.jscolor.rgb[1]) + "&b=" + Math.round(this.jscolor.rgb[2]) + "&type=device&bid=" + $(this).parent().attr('data-bridgeid') + "&uid=" + $(this).attr('data-device-id')  + '&val=1', function( data ) {
			console.log( data );	  
		});
		
	});	
	
	$('.room-slider').slider({
		range: "min",
		min: 0,
		max: 100,
		value: 50,
		create: function( event, ui ){
			$(this).slider("option", "value", $(this).attr('data-value') );
		},
		stop: function(event, ui) {
			var room = $(this).parent().parent(); //collection
			$.get("api.php?fx=dim&type=room&bid=" + $(this).attr('data-bridgeid') + "&uid=" + $(this).attr('data-room-id') + "&val=" + ui.value, function( data ) {
				console.log( data );
				$(room).find('.device-slider').each(function(){
					$(this).slider('value', ui.value );
				});
			});
		}
	});

	//device-color-picker
	
	$('.device-slider').slider({
		range: "min",
		min: 0,
		max: 100,
		//value: $(this).attr('data-level'),
		create: function( event, ui ){
			$(this).slider("option", "value", $(this).attr('data-value') );
			if( $(this).parent().hasClass('light-on') ){
				$(this).parent().css('background-color', 'rgba(255,255,0,' + ( $(this).attr('data-value') / 100 ) + ')');
			}
		},
		stop: function(event, ui) {
			$.get("api.php?fx=dim&type=device&bid=" + $(this).parent().attr('data-bridgeid') + "&uid=" + $(this).attr('data-device-id') + "&val=" + ui.value, function( data ) {
			  console.log( data );
			  
			  
			});
		},
		slide: function( event, ui ) {
			if( $(this).parent().hasClass('light-on') ){
				$(this).parent().css('background-color', 'rgba(255,255,0,' + ( ui.value / 100 ) + ')');
			}
		}
	});

	$('.house-slider').slider({
		range: "min",
		min: 0,
		max: 100,
		value: 50,
		stop: function(event, ui) {
			$.get("api.php?fx=dim&type=all&uid=" + $(this).attr('data-device-id') + "&val=" + ui.value, function( data ) {
			  console.log( data );
			  //set every slider to the new value
			  
			});
		}
	});

	$('button.onOffToggleButton').click(function(event){
		var roomID = $(this).attr('data-room-id');
		var bridgeID = $(this).parent().parent().attr('data-bridgeid');
		var room = $(this).parent().parent();
		var val = 0;
		if( $(this).hasClass('buttonOn') ){
			val = 1;	
		}
		
		$.get( "api.php?fx=toggle&type=room&bid=" + bridgeID + "&uid=" + roomID + "&val=" + val, function( data ) {
			  console.log( data );
			  
			  $(room).find('.room-devices .device').each(function(){
				if( val == 1){
					$(this).addClass('light-on');
					var v = $(this).find('.device-slider').slider( "value" );
					$(this).css('background-color', 'rgba(255,255,0,' + ( v / 100 ) + ')');
				}else{
					$(this).removeClass('light-on');
					$(this).css('background-color','transparent');
				}
			});
			  
		});
		
		
	});

	$('button.onOffDeviceToggleButton').click(function(event){
		var did = $(this).attr('data-device-id');
		var bid = $(this).parent().attr('data-bridgeid');
		var val = 0;
		var light = $(this);
		if( $(this).hasClass('buttonOn') ){
			val = 1;
		}
		
		$.get( "api.php?fx=toggle&type=device&bid=" + bid + "&uid=" + did + "&val=" + val, function( data ) {
			 console.log( data );
			 if( val == 1){
				$(light).parent().addClass('light-on');
				var v = $(light).parent().find('.device-slider').slider( "value" );
				$(light).parent().css('background-color', 'rgba(255,255,0,' + ( v / 100 ) + ')');
			}else{
				$(light).parent().removeClass('light-on');
				$(light).parent().css('background-color','transparent');
			}
		});
	});

	$('button.onOffHouseToggleButton').click(function(event){
		//var DID = $(this).attr('data-device-id');
		var val = 0;
		if( $(this).hasClass('buttonOn') ){
			val = 1;
		}
		
		$.get( "api.php?fx=toggle&type=all&uid=ALL&val=" + val, function( data ) {
			  console.log( data );
			$('.room-devices .device').each(function(){
				if( val == 1){
					$(this).addClass('light-on');
					var v = $(this).find('.device-slider').slider( "value" );
					$(this).css('background-color', 'rgba(255,255,0,' + ( v / 100 ) + ')');
				}else{
					$(this).removeClass('light-on');
					$(this).css('background-color','transparent');
				}
			});
			  
		});
	});
	
	$('#refresh').click(function(){
		$.get( "api.php?fx=refreshState", function( data ) {
			  console.log( data );
		});
	});


	$('#arrayDump').click(function(){
		$(this).toggleClass('toggled');
	});
});
