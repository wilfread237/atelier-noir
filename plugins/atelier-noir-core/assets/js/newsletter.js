/**
 * Atelier Noir — Newsletter Popup
 */
(function () {
	'use strict';

	var DELAY         = (typeof studioNL !== 'undefined' && studioNL.delay) ? +studioNL.delay : 7000;
	var AJAXURL       = (typeof studioNL !== 'undefined' && studioNL.ajaxurl) ? studioNL.ajaxurl : '/wp-admin/admin-ajax.php';
	var COOKIE_KEY    = 'atelier_noir_nl_seen';
	var COOKIE_MINUTES = (typeof studioNL !== 'undefined' && studioNL.cookieMinutes) ? +studioNL.cookieMinutes : 30;

	function getCookie(name) {
		var match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
		return match ? decodeURIComponent(match[1]) : null;
	}

	function setCookieMinutes(name, value, minutes) {
		var maxAge = Math.max(1, minutes) * 60;
		document.cookie = name + '=' + encodeURIComponent(value) + '; path=/; max-age=' + maxAge + '; SameSite=Lax';
		try {
			localStorage.setItem(name, String(Date.now()));
		} catch (e) { /* ignore */ }
	}

	function isDismissed() {
		if (getCookie(COOKIE_KEY)) {
			return true;
		}
		try {
			var stored = parseInt(localStorage.getItem(COOKIE_KEY), 10);
			if (!isNaN(stored) && Date.now() - stored < COOKIE_MINUTES * 60 * 1000) {
				return true;
			}
		} catch (e) { /* ignore */ }
		return false;
	}

	if (isDismissed()) return;

	var popup    = document.getElementById('studio-nl-popup');
	var backdrop = document.getElementById('studio-nl-backdrop');
	var form     = document.getElementById('studio-nl-form');
	var closeBtn = document.getElementById('studio-nl-close');

	if (!popup) return;

	function open() {
		var regionOverlay = document.getElementById('studio-region-overlay');
		if (regionOverlay && !regionOverlay.classList.contains('is-hiding') && regionOverlay.style.display !== 'none') {
			setTimeout(open, 4000);
			return;
		}
		popup.removeAttribute('hidden');
		popup.classList.add('is-entering');
		popup.setAttribute('aria-hidden', 'false');
		setTimeout(function () {
			var input = popup.querySelector('.studio-nl-popup__input');
			if (input) input.focus();
		}, 300);
	}

	setTimeout(open, DELAY);

	function close(permanent) {
		popup.classList.remove('is-entering');
		popup.classList.add('is-leaving');
		if (permanent) {
			setCookieMinutes(COOKIE_KEY, '1', COOKIE_MINUTES);
		}
		setTimeout(function () {
			popup.setAttribute('hidden', '');
			popup.classList.remove('is-leaving');
			popup.setAttribute('aria-hidden', 'true');
		}, 380);
	}

	if (closeBtn) closeBtn.addEventListener('click', function () { close(true); });
	if (backdrop) backdrop.addEventListener('click', function () { close(true); });

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && !popup.hasAttribute('hidden')) close(true);
	});

	if (!form) return;

	form.addEventListener('submit', function (e) {
		e.preventDefault();

		var emailInput  = form.querySelector('.studio-nl-popup__input');
		var consentInput = form.querySelector('[name="consent"]');
		var nonceInput  = form.querySelector('[name="atelier_noir_nl_nonce"]');
		var hp          = form.querySelector('[name="atelier_noir_hp_nl"]');
		var successMsg  = form.querySelector('.studio-nl-popup__success');
		var errorMsg    = form.querySelector('.studio-nl-popup__error');
		var submitBtn   = form.querySelector('.studio-nl-popup__submit');

		if (!emailInput || !emailInput.validity.valid) {
			emailInput && emailInput.focus();
			return;
		}
		if (!consentInput || !consentInput.checked) {
			consentInput && consentInput.focus();
			return;
		}

		form.classList.add('is-loading');
		submitBtn.disabled = true;

		var body = new URLSearchParams({
			action:          'atelier_noir_newsletter_subscribe',
			email:           emailInput.value.trim(),
			consent:         '1',
			atelier_noir_nl_nonce: nonceInput ? nonceInput.value : '',
			atelier_noir_hp_nl:    hp ? hp.value : '',
		});

		fetch(AJAXURL, {
			method:  'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body:    body.toString(),
		})
		.then(function (r) { return r.json(); })
		.then(function (data) {
			form.classList.remove('is-loading');
			submitBtn.disabled = false;

			if (data.success) {
				successMsg.removeAttribute('hidden');
				errorMsg.setAttribute('hidden', '');
				form.querySelector('.studio-nl-popup__field').style.display = 'none';
				if (consentInput.closest('.studio-nl-popup__consent')) {
					consentInput.closest('.studio-nl-popup__consent').style.display = 'none';
				}
				submitBtn.style.display = 'none';
				setCookieMinutes(COOKIE_KEY, '1', COOKIE_MINUTES);
				setTimeout(function () { close(true); }, 3000);
			} else {
				errorMsg.removeAttribute('hidden');
				successMsg.setAttribute('hidden', '');
			}
		})
		.catch(function () {
			form.classList.remove('is-loading');
			submitBtn.disabled = false;
			errorMsg.removeAttribute('hidden');
			successMsg.setAttribute('hidden', '');
		});
	});

	popup.addEventListener('keydown', function (e) {
		if (e.key !== 'Tab') return;
		var focusables = Array.from(popup.querySelectorAll(
			'button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])'
		)).filter(function (el) { return el.tabIndex !== -1; });
		if (!focusables.length) return;
		var first = focusables[0];
		var last  = focusables[focusables.length - 1];
		if (e.shiftKey && document.activeElement === first) {
			e.preventDefault(); last.focus();
		} else if (!e.shiftKey && document.activeElement === last) {
			e.preventDefault(); first.focus();
		}
	});
})();
