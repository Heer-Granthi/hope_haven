// ============================================
// Hope Haven – Main JavaScript
// ============================================

// Mobile nav toggle
function toggleNav() {
    const nav = document.getElementById('navLinks');
    nav && nav.classList.toggle('open');
}

// Auto-dismiss flash messages
document.addEventListener('DOMContentLoaded', function() {
    const flash = document.querySelector('.flash');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            flash.style.transform = 'translateX(120%)';
            flash.style.transition = 'all 0.4s ease';
        }, 3800);
    }

    // Set minimum date for appointment booking to today
    const datePicker = document.getElementById('visit_date');
    if (datePicker) {
        const today = new Date().toISOString().split('T')[0];
        datePicker.setAttribute('min', today);
    }

    // Highlight Sunday-only option in date picker
    const visitDateInput = document.getElementById('visit_date');
    if (visitDateInput) {
        visitDateInput.addEventListener('change', function() {
            const selected = new Date(this.value + 'T00:00:00');
            const day = selected.getDay(); // 0 = Sunday
            const note = document.getElementById('sunday-note');
            if (note) {
                if (day !== 0) {
                    note.textContent = '⚠️ Caring Connections visits are preferred on Sundays. You selected a ' + ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][day] + '.';
                    note.style.color = '#E65100';
                } else {
                    note.textContent = '✓ Great! Sundays are perfect for Caring Connections visits.';
                    note.style.color = '#2E7D32';
                }
            }
        });
    }

    // Confirm delete / reject actions
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm)) e.preventDefault();
        });
    });

    // Amount field toggle based on donation type
    const typeSelect = document.getElementById('donation_type');
    const amountGroup = document.getElementById('amount-group');
    if (typeSelect && amountGroup) {
        typeSelect.addEventListener('change', function() {
            amountGroup.style.display = this.value === 'money' ? 'block' : 'none';
        });
        // Initial state
        amountGroup.style.display = typeSelect.value === 'money' ? 'block' : 'none';
    }
});
