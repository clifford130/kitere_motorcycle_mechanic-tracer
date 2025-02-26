
function toggleForm() {
    var loginForm = document.getElementById('login-form');
    var registerForm = document.getElementById('register-form');
    
    loginForm.style.display = loginForm.style.display === 'none' ? 'block' : 'none';
    registerForm.style.display = registerForm.style.display === 'none' ? 'block' : 'none';

    document.getElementById('message').style.display = 'none';
}

// Show messages for login/signup success/failure
function showMessage(message) {
    var messageElement = document.getElementById('message');
    messageElement.textContent = message;
    messageElement.style.display = 'block';
}

// Login validation
function login(event) {
    event.preventDefault();
    
    var username = document.getElementById('loginUsername').value;
    var password = document.getElementById('loginPassword').value;

    if (username === 'username' && password === 'password') {
        showMessage('Login successful!');
    } else {
        showMessage('Login failed. Check your credentials.');
    }
}

// Registration validation
function register(event) {
    event.preventDefault();
    
    var username = document.getElementById('registerUsername').value;
    var password = document.getElementById('registerPassword').value;

    if (username && password) {
        showMessage('Signup successful!');
    } else {
        showMessage('Signup failed. Fill in all fields.');
    }
}

// ✅ New Form Validation (For Login & Registration Forms)
function validateForm(event, formId) {
    event.preventDefault();
    let form = document.getElementById(formId);
    let inputs = form.getElementsByTagName('input');
    
    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].hasAttribute('required') && inputs[i].value.trim() === '') {
            alert(inputs[i].placeholder + ' is required!');
            return false;
        }
    }

    // Password Validation (if present)
    let passwordInput = form.querySelector('input[name="password"]');
    if (passwordInput && passwordInput.value.length < 6) {
        alert('Password must be at least 6 characters long.');
        return false;
    }

    form.submit();
}

function redirectToSignup(selectObj) {
    var url = selectObj.value;
    if (url !== "") {
        window.location.href = url;
    }
}
