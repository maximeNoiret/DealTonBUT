const passwordInput = document.getElementById('password');

passwordInput.addEventListener('input', () => {
    const val = passwordInput.value;
    checkRule('rule-length',  val.length >= 12);
    checkRule('rule-upper',   /[A-Z]/.test(val));
    checkRule('rule-number',  /[0-9]/.test(val));
    checkRule('rule-special', /[^A-Za-z0-9]/.test(val));
});

function checkRule(id, valid) {
    const el = document.getElementById(id);
    el.textContent = (valid ? '✅' : '❌') + ' ' + el.textContent.slice(2);
}

function validatePassword() {
    const val = passwordInput.value;
    if (val.length < 12 || !/[A-Z]/.test(val) || !/[0-9]/.test(val) || !/[^A-Za-z0-9]/.test(val)) {
        alert('Le mot de passe ne respecte pas les conditions requises.');
        return false;
    }
    return true;
}