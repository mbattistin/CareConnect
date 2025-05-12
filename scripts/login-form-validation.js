function validateForm(event) {
    //email validation 
    //it gets the input from the html with the id email. Trim is to remove any additional space
    let email = document.getElementById("email").value.trim();
    //it gets the label for the error message
    let emailError = document.getElementById("emailError");
    //it first verify if the email is empty
    if (email === "") {
        //it sets the error message to the label error.
        emailError.textContent = "* Please enter your email address.";
        //it makes the variable false because the input is not correct
        isValid = false;
    } 
    //it uses regex to validate if the email is correct
    else if(!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(email)){
        emailError.textContent = "* Invalid email address.";
        //it makes the variable false because the input is not correct
        isValid = false;
    }
    else {
        // it sets the label error to empty if there is no error
        emailError.textContent = "";
    }

    //password Validation
    //it gets the input from the html with the id password. Trim is to remove any additional space
    let password = document.getElementById("password").value.trim();
    //it gets the label for the error message
    let passwordError = document.getElementById("passwordError");
    //it first verify if the password is empty
    if (password === "") {
        //it sets the error message to the label error.
        passwordError.textContent = "* Please enter your password.";
        //it makes the variable false because the input is not correct
        isValid = false;
    } 
    //it uses regex to validate if the password is correct
    else if(!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(password)){
        passwordError.textContent = "* Password must be at least 8 characters and include uppercase, lowercase, number and special character.";
        //it makes the variable false because the input is not correct
        isValid = false;
    }    
    else {
        // it sets the label error to empty if there is no error
        passwordError.textContent = "";
    }

}