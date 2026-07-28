jQuery(document).ready(function($) {
    if ($('#htpopupex').length === 0) {
        return; 
    }
	var dataTime = $('#popup-timer').data('time');
	var waitTime = dataTime && !isNaN(parseInt(dataTime)) ? parseInt(dataTime) : 0;
	var closeTime = localStorage.getItem('htpopup');
	var currentTime = new Date().getTime();
	if (waitTime === 0 || !closeTime || (currentTime - closeTime) > waitTime * 60 * 60 * 1000) {
		$('#htpopupex').modal({
			fadeDuration: 250,
			fadeDelay: 0.50
		});
		$('.jquery-modal.blocker').addClass('horse-popupex');
	}
	// Delegated: jquery-modal appends .close-modal inside a setTimeout, so a
    // direct bind here attached to nothing and closing with the X never stored
    // the dismissal — the popup came back on the very next page load.
    $(document).on('click', '.close-modal, .blocker', function() {
		var closeTime = new Date().getTime();
		localStorage.setItem('htpopup', closeTime);
	});
});