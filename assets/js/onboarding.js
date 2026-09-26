(function () {
	"use strict";

	var btn = document.getElementById("studio-setup-import");
	var log = document.getElementById("studio-setup-import-log");
	var summary = document.getElementById("studio-setup-import-summary");
	var details = document.getElementById("studio-setup-import-details");
	var list = document.getElementById("studio-setup-import-list");
	var nav = document.querySelector(".studio-setup__nav-after-import");

	if (!btn || !window.studioOnboarding) {
		return;
	}

	function renderLog(lines, isError) {
		if (!log) {
			return;
		}

		log.hidden = false;
		log.classList.toggle("is-error", !!isError);

		if (summary) {
			summary.textContent = isError
				? studioOnboarding.i18n.error
				: studioOnboarding.i18n.successSummary || studioOnboarding.i18n.success;
		}

		if (list && details) {
			list.innerHTML = "";
			if (lines && lines.length) {
				lines.forEach(function (line) {
					var li = document.createElement("li");
					li.textContent = line;
					list.appendChild(li);
				});
				details.hidden = false;
			} else {
				details.hidden = true;
			}
		}
	}

	btn.addEventListener("click", function () {
		btn.disabled = true;
		btn.textContent = studioOnboarding.i18n.importing;
		renderLog([], false);

		if (summary) {
			summary.textContent = studioOnboarding.i18n.importing;
		}
		if (details) {
			details.hidden = true;
		}

		var body = new FormData();
		body.append("action", "atelier_noir_run_demo_import");
		body.append("nonce", studioOnboarding.nonce);

		fetch(studioOnboarding.ajaxUrl, {
			method: "POST",
			credentials: "same-origin",
			body: body,
		})
			.then(function (res) {
				return res.json();
			})
			.then(function (payload) {
				if (!payload || !payload.success) {
					throw payload;
				}
				var lines =
					(payload.data && payload.data.messages) || [];
				renderLog(lines, false);
				if (nav) {
					nav.hidden = false;
				}
				btn.textContent = studioOnboarding.i18n.success;
			})
			.catch(function (err) {
				var lines = (err && err.data && err.data.messages) || [];
				renderLog(lines, true);
				btn.disabled = false;
				btn.textContent = studioOnboarding.i18n.error;
			});
	});
})();
