document.addEventListener('DOMContentLoaded', () => {
    // Form Validation logic
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Password verification logic
    const p1 = document.getElementById('password');
    const p2 = document.getElementById('confirm_password');
    if (p1 && p2) {
        p2.addEventListener('keyup', () => {
            if (p1.value !== p2.value) {
                p2.setCustomValidity('Passwords do not match');
            } else {
                p2.setCustomValidity('');
            }
        });
    }

    // Interactive hover effects for feature cards
    const cards = document.querySelectorAll('.feature-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.querySelector('.icon-wrapper i').classList.add('fa-beat-fade');
        });
        card.addEventListener('mouseleave', () => {
            card.querySelector('.icon-wrapper i').classList.remove('fa-beat-fade');
        });
    });
});

// For AJAX notifications
function showToast(message, type = 'success') {
    // Create toast container if not exists
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();

    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
