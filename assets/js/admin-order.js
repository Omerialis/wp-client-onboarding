'use strict';

(function() {
	let draggedRow = null;

	/**
	 * Initialize drag and drop functionality.
	 *
	 * @return {void}
	 */
	function init() {
		const tbody = document.querySelector('table.wp-list-table tbody');

		if (!tbody) {
			return;
		}

		const rows = tbody.querySelectorAll('tr[id^="post-"]');

		rows.forEach(row => {
			row.draggable = true;
			row.addEventListener('dragstart', handleDragStart);
			row.addEventListener('dragover', handleDragOver);
			row.addEventListener('drop', handleDrop);
			row.addEventListener('dragend', handleDragEnd);
		});
	}

	/**
	 * Handle drag start event.
	 *
	 * @param {DragEvent} event The drag event.
	 * @return {void}
	 */
	function handleDragStart(event) {
		draggedRow = this;
		event.dataTransfer.effectAllowed = 'move';
		this.style.opacity = '0.5';
	}

	/**
	 * Handle drag over event.
	 *
	 * @param {DragEvent} event The drag event.
	 * @return {void}
	 */
	function handleDragOver(event) {
		event.preventDefault();
		event.dataTransfer.dropEffect = 'move';

		if (this !== draggedRow) {
			this.style.borderTop = '2px solid #0073aa';
		}
	}

	/**
	 * Handle drop event.
	 *
	 * @param {DragEvent} event The drag event.
	 * @return {void}
	 */
	function handleDrop(event) {
		event.preventDefault();

		if (this === draggedRow) {
			return;
		}

		const tbody = document.querySelector('table.wp-list-table tbody');
		const rows = Array.from(tbody.querySelectorAll('tr[data-post-id]'));

		// Find indices.
		const draggedIndex = rows.indexOf(draggedRow);
		const targetIndex = rows.indexOf(this);

		// Swap rows in DOM.
		if (draggedIndex < targetIndex) {
			this.parentNode.insertBefore(draggedRow, this.nextSibling);
		} else {
			this.parentNode.insertBefore(draggedRow, this);
		}

		// Save new order via AJAX.
		saveOrder(rows);
	}

	/**
	 * Handle drag end event.
	 *
	 * @param {DragEvent} event The drag event.
	 * @return {void}
	 */
	function handleDragEnd(event) {
		this.style.opacity = '1';
		this.style.borderTop = '';

		const rows = document.querySelectorAll('table.wp-list-table tbody tr[data-post-id]');
		rows.forEach(row => {
			row.style.borderTop = '';
		});
	}

	/**
	 * Save the new order via AJAX.
	 *
	 * @param {NodeListOf<Element>} rows The table rows.
	 * @return {void}
	 */
	function saveOrder(rows) {
		const order = [];
		const tbody = document.querySelector('table.wp-list-table tbody');
		const currentRows = tbody.querySelectorAll('tr[id^="post-"]');

		currentRows.forEach(row => {
			// Extract post ID from id attribute like "post-123".
			const idMatch = row.id.match(/^post-(\d+)$/);
			if (idMatch && idMatch[1]) {
				order.push(parseInt(idMatch[1], 10));
			}
		});

		const params = new URLSearchParams();
		params.append('action', 'wcob_reorder_sections');
		params.append('nonce', wcobAdminOrder.nonce);

		// Append order items as array.
		order.forEach((postId, index) => {
			params.append('order[' + index + ']', postId);
		});

		fetch(wcobAdminOrder.ajaxUrl, {
			method: 'POST',
			body: params,
		})
			.then(response => response.json())
			.then(result => {
				if (!result.success) {
					console.error('Failed to save order:', result.data);
				}
			})
			.catch(error => {
				console.error('Error saving order:', error);
			});
	}

	// Initialize when DOM is ready.
	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
