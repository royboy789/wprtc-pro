var $ = jQuery,
room = $('.localVideo').data('room'),
localSrc = '',
wprtc_comm;
maxCap = 15;
if(!!$('.localVideo').data('capacity')) { maxCap = $('.localVideo').data('capacity'); }

$(document).ready(function($){

	if( !wprtc_info.wprtc_icecomm ) {
		alert('No API Key setup, please go to your admin settings to put in the Icecomm.io API key' );
		return false;
	}
	console.log( wprtc_info.wprtc_icecomm );
	wprtc_comm = new Icecomm(wprtc_info.wprtc_icecomm, {debug: true});
	
	wprtc_comm.connect( room, {
		audio: true,
		limit: parseInt( maxCap )
	}, function(){
		console.log('connected to: ' + room);
	});
	
	wprtc_comm.on('connected', function(peer) {
		console.log('Joining Room (Remote)');
		console.log( wprtc_comm.getRoomSize(), wprtc_comm.getRemoteIDs() );
		document.getElementById('remoteVideos').appendChild(peer.getVideo());
	});
	
	wprtc_comm.on('local', function(peer) {
		console.log('Joining Room (Local)');
		console.log( wprtc_comm.getRooms(), wprtc_comm.getRoomSize(), wprtc_comm.getRemoteIDs() );
		$('.localVideo').get(0).src = peer.stream;
		$('.localVideo').attr('id', peer.id );
	});
	
	wprtc_comm.on('disconnect', function(peer) {
		console.log('Leaving Room');
		document.getElementById(peer.ID).remove();
	});
	
	
	$('#roomChange select').on('change', function(e){
		room = $(this).val();
		if( room == '-1' || room == -1 ) { return false; }
		console.log( 'changing rooms: ' + room );
		wprtc_comm.connect(room);
		if( $('.videoTitle').length ) {
			$('.videoTitle').html( room );
		}
	});
	
	
	/** REMOTE VIDEO BEHAVIOR **/
	$('body').on('click', '#remoteVideos video', function(e) {
		$('.mute_controls a').hide();
		var remote_src = $(this).attr('src'),
		remote_id = $(this).attr('id'),
		local_src = $('.localVideo').attr('src');
		
		$('.localVideo').attr('src', remote_src);
		$('.localVideo').get(0).play();
		
		$(this).attr('src', local_src);
		document.getElementById( remote_id ).play();
	});
	
	/** MUTE **/
	$('body').on('click', '.mute', function(e){
		e.preventDefault();
		
		var local = $('.localVideo').get(0);
		if( $(this).hasClass('audio') ) {
			$(this).children('span.fa').toggleClass('fa-microphone').toggleClass('fa-microphone-slash');
			console.log( wprtc_comm.getLocalID() );
			if( local.muted ) {
				local.muted = false;
			} else {
				local.muted = true;
			}
		} else {
			if( $(this).children('span.fa').hasClass('on') ) {
				wprtc_comm.close();
			} else {
				wprtc_comm.connect( room, {
					audio: local.muted,
					limit: parseInt( maxCap )
				}, function(){
					console.log('reconnected to: ' + room);
				});
			}
			
			$(this).children('span.fa').toggleClass('on').toggleClass('off');
			
			
		}
		
	})
	
	
})