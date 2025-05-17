window.onload = function () {
    generateCaptcha();
};
    
    function generateCaptcha() {
        var captcha = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        for (var i = 0; i < 6; i++) {
            captcha += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        document.getElementById('captchaText').textContent = captcha;
    }
