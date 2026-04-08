import './bootstrap';

const csrfToken = document
	.querySelector('meta[name="csrf-token"]')
	?.getAttribute('content');

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

const lazyContainer = document.querySelector('#timeline-lazy-loader');

if (lazyContainer) {
	const loadMoreButton = lazyContainer.querySelector('[data-load-more-button]');
	const sentinel = lazyContainer.querySelector('[data-load-more-sentinel]');
	const status = lazyContainer.querySelector('[data-load-more-status]');
	const targetSelector = lazyContainer.getAttribute('data-target');
	const target = targetSelector ? document.querySelector(targetSelector) : null;
	let nextUrl = lazyContainer.getAttribute('data-next-url');
	let isLoadingMore = false;

	const updateStatus = (message) => {
		if (!status) return;
		status.classList.remove('hidden');
		status.textContent = message;
	};

	const hideLoader = () => {
		lazyContainer.classList.add('hidden');
	};

	const loadMore = async () => {
		if (!nextUrl || !target || isLoadingMore) {
			return;
		}

		isLoadingMore = true;
		updateStatus('Loading more chapters...');

		try {
			const connector = nextUrl.includes('?') ? '&' : '?';
			const response = await fetch(`${nextUrl}${connector}fragment=1`, {
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
				},
			});

			if (!response.ok) {
				throw new Error('Failed to load more chapters');
			}

			const payload = await response.json();
			target.insertAdjacentHTML('beforeend', payload.html || '');
			nextUrl = payload.nextPageUrl || '';

			if (!nextUrl) {
				hideLoader();
			} else {
				updateStatus('More chapters loaded');
			}
		} catch {
			updateStatus('Could not load more chapters. Try again.');
		} finally {
			isLoadingMore = false;
		}
	};

	loadMoreButton?.addEventListener('click', loadMore);

	if (sentinel) {
		const observer = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					loadMore();
				}
			});
		}, {
			rootMargin: '160px 0px',
		});

		observer.observe(sentinel);
	}
}

document.addEventListener('submit', async (event) => {
	const form = event.target;

	if (!(form instanceof HTMLFormElement)) {
		return;
	}

	if (form.matches('[data-optimistic-attach]')) {
		event.preventDefault();
		const select = form.querySelector('select[name="label_id"]');
		const chips = form.parentElement?.querySelector('[data-label-chips]');

		if (!(select instanceof HTMLSelectElement) || !chips) {
			form.submit();
			return;
		}

		const selectedOption = select.selectedOptions[0];
		if (!selectedOption || !selectedOption.value) {
			return;
		}

		const ghostChip = document.createElement('span');
		ghostChip.className = 'inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold text-white opacity-75';
		ghostChip.style.backgroundColor = selectedOption.dataset.labelColor || '#6b7280';
		ghostChip.textContent = `${selectedOption.textContent || 'Label'}...`;
		chips.appendChild(ghostChip);

		try {
			const body = new FormData(form);
			await fetch(form.action, {
				method: 'POST',
				body,
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
				},
			});
		} finally {
			window.location.reload();
		}
	}

	if (form.matches('[data-optimistic-detach]')) {
		event.preventDefault();
		form.classList.add('opacity-40');

		try {
			const body = new FormData(form);
			await fetch(form.action, {
				method: 'POST',
				body,
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
				},
			});
		} finally {
			window.location.reload();
		}
	}
});
