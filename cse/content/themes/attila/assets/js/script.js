jQuery(function($) {

	var body = $('body');
	var html = $('html');
	var viewport = $(window);

	/* ==========================================================================
	   Menu
	   ========================================================================== */

	function menu() {
		html.toggleClass('menu-active');
	};

	$('#menu').on({
		'click': function(event) {
			// Don't close when clicking on the search box
			if( $(event.target).parents().andSelf().is('.nav-search') ) {
				return;
			}

			menu();
		}
	});

	$('.menu-button').on({
		'click': function() {
			menu();
		}
	});

	$('.hidden-close').on({
		'click': function() {
			menu();
		}
	});

	/* ==========================================================================
	   Parallax cover
	   ========================================================================== */

	var cover = $('.cover');
	var coverPosition = 0;

	function prlx() {
		if(cover.length >= 1) {
			var windowPosition = viewport.scrollTop();
			(windowPosition > 0) ? coverPosition = Math.floor(windowPosition * 0.25) : coverPosition = 0;
			cover.css({
				'-webkit-transform' : 'translate3d(0, ' + coverPosition + 'px, 0)',
				'transform' : 'translate3d(0, ' + coverPosition + 'px, 0)'
			});
			(viewport.scrollTop() < cover.height()) ? html.addClass('cover-active') : html.removeClass('cover-active');
		}
	}
	prlx();

	viewport.on({
		'scroll': function() {
			prlx();
		},
		'resize': function() {
			prlx();
		},
		'orientationchange': function() {
			prlx();
		}
	});

	/* ==========================================================================
	   Reading Progress
	   ========================================================================== */

	function readingProgress() {
		var post = $('.post-content');

		if(post.length >= 1) {
			var postBottom = post.offset().top + post.height();
			var windowBottom = viewport.scrollTop() + viewport.height();
			var progress = 100 - (((postBottom - windowBottom) / (postBottom - viewport.height())) * 100);
			$('.progress-bar').css('width', progress + '%');
			(progress > 100) ? $('.progress-container').addClass('ready') : $('.progress-container').removeClass('ready');
		}
	}
	readingProgress();

	viewport.on({
		'scroll': function() {
			readingProgress();
		},
		'resize': function() {
			readingProgress();
		},
		'orientationchange': function() {
			readingProgress();
		}
	});

	/* ==========================================================================
	   Style code blocks with highlight and numbered lines
	   ========================================================================== */

	function codestyling() {
		$('pre code').each(function(i, e) {
			hljs.highlightBlock(e);
			var code = $(this);
			var lines = code.html().split(/\n/).length;
			var numbers = [];
			for (i = 1; i < lines; i++) {
				numbers += '<span class="line">' + i + '</span>';
			}
			code.parent().append('<div class="lines">' + numbers + '</div>');
		});
	}
	// Only apply code styling when we're not in the Postleaf editor
	if(!window.postleaf) codestyling();

	/* ==========================================================================
	   Responsive Videos with Fitvids
	   ========================================================================== */

	function video() {
		$('#wrapper').fitVids();
	}
	// Only apply Fitvids when we're not in the Postleaf editor
	if(!window.postleaf) video();

});
