/**
 * Button Loading State Utility
 * Provides dynamic loading states for buttons during form submissions
 */

class ButtonLoader {
    constructor(button, loadingText = 'Processing...') {
        this.button = button;
        this.originalText = button.innerHTML;
        this.loadingText = loadingText;
        this.isLoading = false;
    }

    start() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.button.disabled = true;
        this.button.style.opacity = '0.7';
        this.button.style.cursor = 'not-allowed';
        
        // Create spinner icon
        const spinner = '<i class="fas fa-spinner fa-spin me-2"></i>';
        this.button.innerHTML = spinner + this.loadingText;
    }

    stop() {
        if (!this.isLoading) return;
        
        this.isLoading = false;
        this.button.disabled = false;
        this.button.style.opacity = '1';
        this.button.style.cursor = 'pointer';
        this.button.innerHTML = this.originalText;
    }

    reset() {
        this.stop();
    }
}

/**
 * Auto-attach loading states to forms
 * Usage: Just include this script and it will automatically handle all submit buttons
 */
document.addEventListener('DOMContentLoaded', function() {
    // Configuration for different button types
    const buttonConfig = {
        'add-property-form': { text: 'Adding Property...', icon: 'fa-plus' },
        'add-user-form': { text: 'Adding User...', icon: 'fa-plus' },
        'edit-user-form': { text: 'Updating...', icon: 'fa-save' },
        'edit-property-form': { text: 'Updating...', icon: 'fa-save' },
        'profile-form': { text: 'Saving Changes...', icon: 'fa-save' },
        'default': { text: 'Processing...', icon: 'fa-spinner' }
    };

    // Find all forms with submit buttons
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const submitButton = form.querySelector('button[type="submit"]');
        if (!submitButton) return;

        // Get configuration for this form
        const formId = form.id || 'default';
        const config = buttonConfig[formId] || buttonConfig['default'];
        
        // Create button loader instance
        const loader = new ButtonLoader(submitButton, config.text);

        // Handle form submission
        form.addEventListener('submit', function(e) {
            // Start loading state
            loader.start();

            // If form uses AJAX, don't prevent default
            // The AJAX handler should call loader.stop() when done
            
            // Store loader instance on form for external access
            form.buttonLoader = loader;
        });

        // Reset on form reset
        form.addEventListener('reset', function() {
            loader.reset();
        });
    });
});

/**
 * Manual button loader for AJAX forms
 * Usage:
 * const loader = new ButtonLoader(button, 'Saving...');
 * loader.start();
 * // ... do AJAX request
 * loader.stop();
 */
window.ButtonLoader = ButtonLoader;
