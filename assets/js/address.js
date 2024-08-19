window.onload = function () {
    let currentDeleteModal;
    const form = document.getElementById('addressForm');
    const deleteLinks = document.querySelectorAll('.delete');
    const deleteModal = document.getElementById('deleteAddressModal');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    const exportAddressesXML = document.getElementById('exportAddressesXML');
    const exportAddressesJSON = document.getElementById('exportAddressesJSON');

    function validateForm() {
        const invalidFeedback = document.getElementById('invalidFeedback');
        const name = document.getElementById('name').value;
        const firstName = document.getElementById('firstName').value;
        const email = document.getElementById('email').value;
        const
            street = document.getElementById('street').value;
        const zipCode = document.getElementById('zipCode').value;
        const city = document.getElementById('city').value;

        if (name.length > 254 || firstName.length > 254 || email.length > 254 || street.length > 254 || zipCode.length > 254 || city.length > 254) {
            invalidFeedback.textContent = 'Length must be less than 254 characters.';
            return false;
        }

        if (!isValidEmail(email)) {
            invalidFeedback.textContent = 'Invalid email address';
            return false;
        }

        invalidFeedback.textContent = '';

        return true;
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }



    if(form) {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            if(!validateForm()) {
                return;
            }

            const editForm = form.dataset.edit && form.dataset.edit.length ? '/'+form.dataset.edit : '';
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            fetch('/task-address-book/index.php/address'+editForm,
                {
                    method: editForm ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new
                        Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const toast = document.getElementById('successToast');
                    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toast);
                    toastBootstrap.show();
                    if(!editForm) {
                        form.reset();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    }

    Array.from(deleteLinks).forEach(link => {
        link.addEventListener('click', function(event) {
            deleteModal.dataset.addressId = this.dataset.deleteId;
            currentDeleteModal = new bootstrap.Modal(deleteModal);
            currentDeleteModal.show();
        });
    });

    if(confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', () => {
            const addressId = deleteModal.dataset.addressId;
            const addressElement = document.querySelector(`[data-address-view="${addressId}"]`);
            fetch(`/task-address-book/index.php/address/${addressId}`, {
                method: 'DELETE'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Address deleted successfully:', data);
                    addressElement.style.display = 'none'; // Hide the element
                    currentDeleteModal.hide();
                })
                .catch(error => {
                    console.error('Error deleting address:', error);
                });
        });
    }

    if(exportAddressesXML) {
        exportAddressesXML.addEventListener('click', () => {
        fetch('/task-address-book/index.php/address?exportXML=true')
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download
                    = 'addresses.xml';
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error('Error exporting addresses:', error);
            });
        });
    }

    if(exportAddressesJSON) {
        exportAddressesJSON.addEventListener('click', () => {
            fetch('/task-address-book/index.php/address?exportJSON=true')
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download
                        = 'addresses.json';
                    a.click();
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.error('Error exporting addresses:', error);
                });
        });
    }
}