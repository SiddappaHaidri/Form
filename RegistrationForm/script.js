$(document).ready(function() {
    // Form validation rules
    const validationRules = {
        fullName: {
            validate: function(value) {
                return value.trim().length >= 3 && /^[a-zA-Z\s]+$/.test(value);
            },
            message: 'Full name must be at least 3 characters and contain only letters'
        },
        email: {
            validate: function(value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(value);
            },
            message: 'Please enter a valid email address'
        },
        phone: {
            validate: function(value) {
                return /^[0-9\-\+\(\)]{10,}$/.test(value.replace(/\s/g, ''));
            },
            message: 'Please enter a valid phone number (at least 10 digits)'
        },
        dob: {
            validate: function(value) {
                if (!value) return false;
                const date = new Date(value);
                const today = new Date();
                const age = today.getFullYear() - date.getFullYear();
                return age >= 18 && age <= 120;
            },
            message: 'You must be at least 18 years old'
        },
        gender: {
            validate: function(value) {
                return value !== '';
            },
            message: 'Please select a gender'
        },
        address: {
            validate: function(value) {
                return value.trim().length >= 5;
            },
            message: 'Address must be at least 5 characters'
        },
        city: {
            validate: function(value) {
                return value.trim().length >= 2 && /^[a-zA-Z\s]+$/.test(value);
            },
            message: 'City must be at least 2 characters and contain only letters'
        },
        country: {
            validate: function(value) {
                return value.trim().length >= 2 && /^[a-zA-Z\s]+$/.test(value);
            },
            message: 'Country must be at least 2 characters and contain only letters'
        },
        education: {
            validate: function(value) {
                return value !== '';
            },
            message: 'Please select an education level'
        },
        agree: {
            validate: function() {
                return $('#agree').is(':checked');
            },
            message: 'You must agree to the terms and conditions'
        }
    };

    // Real-time validation on blur
    $('input, select, textarea').on('blur', function() {
        const fieldName = $(this).attr('name');
        if (validationRules[fieldName]) {
            validateField(fieldName);
        }
    });

    // Real-time validation on input
    $('input[type="text"], input[type="email"], input[type="tel"], textarea').on('input', function() {
        const fieldName = $(this).attr('name');
        if (validationRules[fieldName]) {
            validateField(fieldName);
        }
    });

    // Form submission
    $('#registrationForm').on('submit', function(e) {
        e.preventDefault();

        // Clear all previous error messages
        $('.error').text('');

        // Validate all fields
        let isValid = true;
        for (let field in validationRules) {
            if (!validateField(field)) {
                isValid = false;
            }
        }

        if (isValid) {
            // Collect form data
            const formData = {
                fullName: $('#fullName').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                dob: $('#dob').val(),
                gender: $('#gender').val(),
                address: $('#address').val(),
                city: $('#city').val(),
                country: $('#country').val(),
                education: $('#education').val()
            };

            // Display results
            displayResults(formData);
        }
    });

    // Validate single field
    function validateField(fieldName) {
        const rule = validationRules[fieldName];
        const field = $('[name="' + fieldName + '"]');
        let value;

        if (fieldName === 'agree') {
            value = field.is(':checked');
        } else {
            value = field.val();
        }

        const isValid = rule.validate(value);
        const errorElement = $('#' + fieldName + 'Error');

        if (!isValid && (value !== '' || fieldName === 'agree' || $('#' + fieldName + 'Error').text() !== '')) {
            errorElement.text(rule.message);
            field.addClass('invalid');
        } else {
            errorElement.text('');
            field.removeClass('invalid');
        }

        return isValid;
    }

    // Display results
    function displayResults(data) {
        let resultHTML = '<div class="result-item">';
        
        const fields = [
            { key: 'fullName', label: 'Full Name' },
            { key: 'email', label: 'Email Address' },
            { key: 'phone', label: 'Phone Number' },
            { key: 'dob', label: 'Date of Birth' },
            { key: 'gender', label: 'Gender' },
            { key: 'address', label: 'Address' },
            { key: 'city', label: 'City' },
            { key: 'country', label: 'Country' },
            { key: 'education', label: 'Education' }
        ];

        fields.forEach(field => {
            resultHTML += `
                <div class="result-item">
                    <div class="result-label">${field.label}:</div>
                    <div class="result-value">${escapeHtml(data[field.key])}</div>
                </div>
            `;
        });

        resultHTML += '</div>';

        // Hide form and show results
        $('.form-wrapper').fadeOut(300, function() {
            $('#resultContent').html(resultHTML);
            $('#resultSection').fadeIn(300);
        });
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
});
