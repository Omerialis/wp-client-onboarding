'use strict';

/**
 * Client-side validation and UX enhancements for JSON import form.
 */
document.addEventListener('DOMContentLoaded', function () {
	const fileInput = document.getElementById('wcob_import_file');
	const form = document.getElementById('wcob-import-form');
	const fileInfo = document.getElementById('wcob-file-info');
	const fileName = document.getElementById('wcob-file-name');
	const submitButton = form.querySelector('button[type="submit"]');

	if (!fileInput) {
		return;
	}

	// Update file name display when file is selected.
	fileInput.addEventListener('change', function () {
		if (this.files && this.files[0]) {
			const file = this.files[0];

			// Validate file extension.
			if (!file.name.toLowerCase().endsWith('.json')) {
				this.value = '';
				fileInfo.style.display = 'none';
				alert(wcobImport.invalidFileMsg);
				return;
			}

			// Display selected file name.
			fileName.textContent = file.name;
			fileInfo.style.display = 'block';
		} else {
			fileInfo.style.display = 'none';
		}
	});

	// Disable submit button during upload.
	form.addEventListener('submit', function (e) {
		if (!fileInput.files || !fileInput.files[0]) {
			e.preventDefault();
			alert(wcobImport.noFileMsg);
			return;
		}

		// Disable submit button to prevent double submission.
		submitButton.disabled = true;
		submitButton.textContent = wcobImport.importingMsg;
	});
});
