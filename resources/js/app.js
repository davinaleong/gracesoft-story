import './bootstrap';

const loadingElements = document.querySelectorAll('[data-loading-text]');

loadingElements.forEach((element) => {
	element.addEventListener('click', () => {
		const loadingText = element.getAttribute('data-loading-text');

		if (!loadingText || element.dataset.loadingApplied === 'true') {
			return;
		}

		element.dataset.loadingApplied = 'true';

		if ('disabled' in element) {
			element.disabled = true;
		}

		element.classList.add('opacity-70', 'cursor-wait');

		const textNode = element.querySelector('p, span') ?? element;
		textNode.textContent = loadingText;
	});
});
