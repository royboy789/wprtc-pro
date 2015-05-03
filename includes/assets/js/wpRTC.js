var $ = jQuery,
room = $('#localVideo').data('room'),
localSrc = '',
wprtc_comm;
if(!!$('#localVideo').data('capacity')) { maxCap = $('#localVideo').data('capacity'); }

$(document).ready(function($){
	
	wprtc_comm = new Icecomm('3kB4PpZaNNFN4r3xhmOVgcPn2D8rzcOTtQFh4gRwmAsaGTPwlm', {debug: true});
	
	wprtc_comm.connect( room, {
		audio: false,
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
		document.getElementById('localVideo').src = peer.stream;
	});
	
	wprtc_comm.on('disconnect', function(peer) {
		console.log('Leaving Room');
		document.getElementById(peer.ID).remove();
	});
	
	
	$('#roomChange select').on('change', function(e){
		room = $(this).val();
		if( room == '-1' || room == -1 ) { return false; }
		console.log( 'changing rooms: ' + room );
		wprtc_comm.connect(room, {audio: false});
		if( $('.videoTitle').length ) {
			$('.videoTitle').html( room );
		}
	});
	
	
})