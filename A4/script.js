const verror = $('.error');
const vEmail = document.getElementById('email');
const vNumber = document.getElementById('number');
const vPassword = document.getElementById('password');
const vCpassword = document.getElementById('cpassword');
const vSubmit = document.getElementById('submit');

const eyeOpen = `
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
<path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
<path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
</svg>`;
const eyeClose = `
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
<path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z"/>
<path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>
</svg>`;
let view = eyeOpen;

const eye = document.querySelector('.eye');
eye.addEventListener('click',()=>{
    if(view === eyeOpen){
        view = eyeClose;
        eye.innerHTML = view;
        vPassword.type='password';
        vCpassword.type='password';
    } else{
        view = eyeOpen;
        eye.innerHTML = view;
        vPassword.type='text';
        vCpassword.type='text';
    }
});

function emailCheck(emailText){
    let check = /^[a-zA-Z0-9_+.-]+\@[a-zA-Z0-9]+\.[a-zA-Z]{2,4}$/;
    return check.test(emailText);    
};

function numberCheck(numberInt){
    let check = /^[0-9]{10}$/;
    return check.test(numberInt);
};

function passwordCheck(passwordText){
    let check = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])+[a-zA-Z0-9!@#$%^&*()_+=`~-]{8,20}$/;
    return check.test(passwordText);
};

function cpasswordCheck(cpasswordText){
    return vPassword.value ==vCpassword.value ? true : false;
};

let err = '';

vSubmit.addEventListener('click',()=>{
    verror.css({
        'display':'block',
        'border':'1px solid darkred',
        'color':'darkred'
    });
    err = '';
    if(vEmail.value === ''){
        err += 'Email address is required.<br>';
    } else{
        let check = emailCheck(vEmail.value);
        if(check===false){
            err += 'Input valid email address.<br>';
        }
    }
    if(vNumber.value === ''){
        err += 'Phone number is required.<br>';
    } else{
        let check = numberCheck(vNumber.value);
        if(check===false){
            err += 'Input valid phone number.<br>';
        }
    }
    if(vPassword.value === ''){
        err += 'Create your strong password.<br>';
    } else{
        let check = passwordCheck(vPassword.value);
        if(check===false){
            err += 'Password min 8 chars: Upper, Lower & Number<br>';
        }
    }
    if(vCpassword.value === ''){
        err += 'Confirm your password.<br>';
    } else{
        let check = cpasswordCheck(vCpassword.value);
        if(check===false){
            err += 'Confirm password is mismatched.<br>';
        }
    }
    verror.html(`${err}`);
    if(verror.html() === ''){
        alert('Successfully account registered!');
        verror.html('Successfully account registered!');
        verror.css({
            'display':'block',
            'border':'1px solid darkgreen',
            'color':'darkgreen'
        });
    } else{
        verror.css({
            'display':'block',
        });
    }
});