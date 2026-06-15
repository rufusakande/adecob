// Form validation for multi-step forms
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (!form) return;

    // Validation functions
    function validateStep(stepNumber) {
        const step = document.getElementById(`step-${stepNumber}`);
        if (!step) return true;

        const requiredFields = step.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    function validateAllSteps() {
        let allValid = true;
        
        for (let i = 1; i <= 3; i++) {
            if (!validateStep(i)) {
                allValid = false;
                // Show the first invalid step
                showStep(i);
                break;
            }
        }
        
        return allValid;
    }

    function showStep(stepNumber) {
        // Hide all steps
        document.querySelectorAll('.step').forEach(step => {
            step.style.display = 'none';
        });
        
        // Show the target step
        const targetStep = document.getElementById(`step-${stepNumber}`);
        if (targetStep) {
            targetStep.style.display = 'block';
        }
    }

    // Enhanced nextStep function with validation
    window.nextStep = function(step) {
        if (validateStep(step)) {
            const currentStep = document.getElementById(`step-${step}`);
            const nextStep = document.getElementById(`step-${step + 1}`);
            if (currentStep && nextStep) {
                currentStep.style.display = 'none';
                nextStep.style.display = 'block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        } else {
            alert('Veuillez remplir tous les champs obligatoires de cette étape.');
        }
    };

    // Enhanced prevStep function
    window.prevStep = function(step) {
        const currentStep = document.getElementById(`step-${step}`);
        const prevStep = document.getElementById(`step-${step - 1}`);
        if (currentStep && prevStep) {
            currentStep.style.display = 'none';
            prevStep.style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    // Form submission handler
    form.addEventListener('submit', function(e) {
        if (!validateAllSteps()) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires avant de soumettre le formulaire.');
            return false;
        }
    });

    // Real-time validation
    document.addEventListener('input', function(e) {
        if (e.target.hasAttribute('required')) {
            if (e.target.value.trim()) {
                e.target.classList.remove('is-invalid');
            }
        }
    });
});
