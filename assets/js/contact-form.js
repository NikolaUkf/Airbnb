document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Ziskaj dáta z formulára
            const formData = new FormData(this);
            const submitBtn = document.getElementById('form-submit');
            const originalText = submitBtn.innerText;
            
            // Zákaz opätovného odoslania
            submitBtn.disabled = true;
            submitBtn.innerText = 'Odosielam...';
            
            // Odoslanie AJAX požiadavky
            fetch('process_contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Úspech
                    showAlert('success', data.message);
                    contactForm.reset();
                } else {
                    // Chyba
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                showAlert('error', 'Vyskytla sa chyba pri odosielaní.');
                console.error('Error:', error);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
            });
        });
    }
});

// Funckia na zobrazenie upozornení
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const contactForm = document.getElementById('contact-form');
    contactForm.parentElement.insertBefore(alertDiv, contactForm);
    
    // Automatické skrytie po 5 sekundách
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}