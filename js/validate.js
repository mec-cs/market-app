$(document).ready(function () {
$('#profileForm').validate({
    rules: {
    name: {
        required: true
    },
    email: {
        required: true,
        email: true
    },
    password: {
        required: true,
        minlength: 8
    },
    city: {
        required: true
    },
    district: {
        required: true
    },
    address: {
        required: true
    }
    },
    messages: {
    name: 'Please enter name.',
    email: {
        required: 'Please enter an email address.',
        email: 'Please enter a valid email address.',
    },
    password: {
        required: 'Please enter a password.',
        minlength: 'Password must be at least 8 characters long.',
    },
    city: 'Please enter a city.',
    district: 'Please enter a district.',
    address: "Please enter an address."
    },
    submitHandler: function (form) {
        form.submit();
    }
});
});
