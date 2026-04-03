(function() {
    const inputs = document.querySelectorAll('.otp-input');
    const otpValue = document.getElementById('otpValue');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const timerElement = document.getElementById('timer');
    const timerValue = document.getElementById('timerValue');
    
    // Get time remaining from data attribute or script tag
    const scriptTag = document.querySelector('script[data-time-remaining]');
    let timeRemaining = scriptTag ? parseInt(scriptTag.getAttribute('data-time-remaining')) : 0;
    const hasError = scriptTag ? scriptTag.getAttribute('data-has-error') === 'true' : false;

    // Auto-focus first input
    if (inputs.length > 0) {
        inputs[0].focus();
    }

    // Handle input
    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            const value = e.target.value;
            
            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }

            // Move to next input
            if (value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            // Update hidden input
            updateOTPValue();
            
            // Remove error class
            input.classList.remove('error');
        });

        // Handle backspace
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Handle paste
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').slice(0, 6);
            
            if (!/^\d+$/.test(pastedData)) return;
            
            [...pastedData].forEach((char, i) => {
                if (inputs[i]) {
                    inputs[i].value = char;
                }
            });
            
            inputs[Math.min(pastedData.length, inputs.length - 1)].focus();
            updateOTPValue();
        });
    });

    function updateOTPValue() {
        const otp = Array.from(inputs).map(input => input.value).join('');
        if (otpValue) {
            otpValue.value = otp;
        }
        if (verifyBtn) {
            verifyBtn.disabled = otp.length !== 6;
        }
    }

    // Timer countdown
    if (timerValue && timeRemaining > 0) {
        const countdown = setInterval(() => {
            if (timeRemaining > 0) {
                timeRemaining--;
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = timeRemaining % 60;
                timerValue.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                
                if (timeRemaining <= 30) {
                    if (timerElement) timerElement.classList.add('expired');
                    timerValue.classList.add('expired');
                }
            } else {
                clearInterval(countdown);
                timerValue.textContent = 'EXPIRED';
                if (timerElement) timerElement.classList.add('expired');
                timerValue.classList.add('expired');
                if (verifyBtn) verifyBtn.disabled = true;
                inputs.forEach(input => {
                    input.disabled = true;
                    input.classList.add('error');
                });
            }
        }, 1000);
    }

    // Add error shake on wrong OTP
    if (hasError) {
        inputs.forEach(input => input.classList.add('error'));
        setTimeout(() => {
            inputs.forEach(input => {
                input.value = '';
                input.classList.remove('error');
            });
            if (inputs[0]) inputs[0].focus();
        }, 600);
    }
})();

