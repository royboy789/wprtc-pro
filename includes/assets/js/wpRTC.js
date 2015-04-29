var $ = jQuery,
room = $('#localVideo').data('room'),
localSrc = '';
if(!!$('#localVideo').data('capacity')) { maxCap = $('#localVideo').data('capacity'); }

var wprtc_comm = new Icecomm('3kB4PpZaNNFN4r3xhmOVgcPn2D8rzcOTtQFh4gRwmAsaGTPwlm');

wprtc_comm.connect('custom room', {audio: false});

wprtc_comm.on('connected', function(peer) {
	console.log('Joining Room (Remote): ' + room );
	document.body.appendChild(peer.getVideo());
});

wprtc_comm.on('local', function(peer) {
	console.log('Joining Room (Local): ' + room );
	localVideo.src = peer.stream;
});

wprtc_comm.on('disconnect', function(peer) {
	console.log('Leaving Room: ' + room );
	document.getElementById(peer.ID).remove();
});