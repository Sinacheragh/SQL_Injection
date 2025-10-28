document.addEventListener('DOMContentLoaded', function () {
	const tabBtns = Array.from(document.querySelectorAll('.tab-btn'));
	const tabContents = Array.from(document.querySelectorAll('.tab-content'));

	function showTab(name) {
		tabContents.forEach(c => {
			if (c.getAttribute('data-content') === name) c.classList.remove('hidden');
			else c.classList.add('hidden');
		});
		tabBtns.forEach(b => {
			if (b.getAttribute('data-tab') === name) b.classList.add('ring-2','ring-green-500');
			else b.classList.remove('ring-2','ring-green-500');
		});
	}

	tabBtns.forEach(btn => {
		btn.addEventListener('click', () => showTab(btn.getAttribute('data-tab')));
	});

	showTab('sql');

	const startBtns = Array.from(document.querySelectorAll('.start-btn'));
	startBtns.forEach(b => {
		b.addEventListener('click', () => {
			const target = b.getAttribute('data-target');
			if (!target || target === '#') {
				window.open('#', '_self');
			} else {
				window.location.href = target;
			}
		});
	});

		const qrElements = Array.from(document.querySelectorAll('[id^="qr-"]'));
		qrElements.forEach(el => {
			el.addEventListener('click', () => {
				window.open('./assets/img/QR_code.svg', '_blank');
			});
		});
});
