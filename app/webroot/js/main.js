$(document).ready(function() {
	ZeroClipboard.setDefaults({
		moviePath			: '/js/vendor/ZeroClipboard/ZeroClipboard.swf',
		forceHandCursor		: true,
		trustedDomains		: location.hostname,
		allowScriptAccess	: "always"
	});

	var clip = new ZeroClipboard($(".clip-copy"));

	$(document).on("mouseup", ".js-url-field", function() {
		return $(this).select();
	});

	$(document).on("focusin", ".js-url-field", function() {
		return a = this, setTimeout(function() {
			return $(a).select();
		}, 0);
	});
});