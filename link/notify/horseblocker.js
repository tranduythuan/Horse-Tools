jQuery(document).ready(function($){
    if ($("#googleads").height() > 1) {
        console.log("No ad blocker detected");
        $("#ht-blocker").html("");
    } else {
        console.log("Detects ad blockers");
		$("#ht-blocker").css("display", "block");
		var htblockid = document.getElementById('ht-blockid').dataset.enabled === 'true';
		if (!htblockid) {
			function blockScrollEvent(event) {
			$(window).scrollTop(0);
			}
			$(document).on('scroll', blockScrollEvent);
			$('html, body').css('overflow', 'hidden');
		}
    }
	$( "#ht-blocker-clo" ).click(function() {
		$("#ht-blocker").css("display", "none");
	});
});