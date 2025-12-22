function isEmail(email){
    var regex = /^([a-zA-Z0-9_.+-])+\@([a-zA-Z0-9])+\.([a-zA-Z0-9]{2,4})$/;
    return regex.test(email);
};

function isNumber(number){
    var regex = /^([0-9]{10})$/;
    return regex.test(number);
};

function isPassword(password){
    var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])([a-zA-z0-9#@_+-]{8,20})$/;
    return regex.test(password);
};

const eyeOpen = '<path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z" />';
const eyeClose = '<path d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0 0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0 0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0 0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.08L19.73,22L21,20.73L3.27,3M12,7A5,5 0 0,1 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.74,7.13 11.35,7 12,7Z" />';

$("svg").click(function(){
    if($(this).hasClass("isOpen")){
        $(this).html(eyeClose);
        $(this).removeClass("isOpen");
        $("#password").attr('type',"text");
        $("#Cpassword").attr('type',"text");
    } else{
        $(this).html(eyeOpen);
        $(this).addClass("isOpen");
        $("#password").attr('type',"password");
        $("#Cpassword").attr('type',"password");
    }
});

const Iemail = $("#email");
const Inumber = $("#number");
const Ipassword = $("#password");
const ICpassword = $("#Cpassword");
const errors = $("#errors");

$("#submit").click(function(){
    errors.html("");
    errors.css({
            'color' : "red",
            'border' : "1px solid red"
        });
    if(Iemail.val() == ""){
        errors.html("Email is required.<br/>");
    } else{
        if(isEmail(Iemail.val()) == false){
            errors.html("Enter valid email.<br/>");
        }
    }
    if(Inumber.val() == ""){
        errors.html(`${errors.html()}Phone number is required.<br/>`);
    } else{
        if(isNumber(Inumber.val()) == false){
            errors.html(`${errors.html()}Enter valid Phone number.<br/>`);
        }
    }
    if(Ipassword.val() == ""){
        errors.html(`${errors.html()}Create password *required.<br/>`);
    } else{
        if(isPassword(Ipassword.val()) == false){
            errors.html(`${errors.html()}Create a strong password.<br/>`);
        }
    }
    if(ICpassword.val() == ""){
        errors.html(`${errors.html()}Enter your confirm password.<br/>`);
    } else{
        if(Ipassword.val() != ICpassword.val()){
            errors.html(`${errors.html()}Confirm password mismatched.<br/>`)
        }
    }
    if(errors.html() == ""){
        errors.css({
            'color' : "green",
            'border' : "1px solid green"
        });
        errors.html("Account is registered successfully.");
    }
    errors.css('display', "block");
});