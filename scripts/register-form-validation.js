function validateForm(event) {
    //variable that holds if the inputs are valid
    let isValid = true;

    //name validation to have only letters and spaces
    //it gets the input from the html with the id name. Trim is to remove any additional space
    let name = document.getElementById("name").value.trim();
    //it gets the label for the error message
    let nameError = document.getElementById("nameError");
    //it firt verify if the name is empty
    if (name === "") {
        //it sets the error message to the label error.
        nameError.textContent = "* Please enter your name.";
        //it makes the variable false because the input is not correct
        isValid = false;
    } 
    //it uses regex to validate if the name contains only letters and spaces
    else if (!/^[A-Za-z\s]+$/.test(name)) {
        //it sets the error message to the label error.
        nameError.textContent = "* Name can only contain letters and spaces.";
        //it makes the variable false because the input is not correct
        isValid = false;
    } 
    else {
        // it sets the label error to empty if there is no error
        nameError.textContent = "";
    }

    //phone validation to have only numbers between 9 to 10 digits
    //it gets the input from the html with the id phone. Trim is to remove any additional space
    let phone = document.getElementById("phone").value.trim();
    //it gets the label for the error message
    let phoneError = document.getElementById("phoneError");
    //it first verify if the phone is empty
    if (phone === "") {
         //it sets the error message to the label error.
        phoneError.textContent = "* Please enter your phone number.";
        //it makes the variable false because the input is not correct
        isValid = false;
    } 
    //it uses regex to validate if the name contains only numbers and have 9-10 digits
    else if (!/^\d{9,10}$/.test(phone)) {
        //it sets the error message to the label error.
        phoneError.textContent = "* Phone number must be 9 or 10 digits long and can only contain numbers.";
        //it makes the variable false because the input is not correct
        isValid = false;
    } 
    else {
        // it sets the label error to empty if there is no error
        phoneError.textContent = "";
    }

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

    //confirm password Validation
    //it gets the input from the html with the id confirmPassword. Trim is to remove any additional space
    let confirmPassword = document.getElementById("confirmPassword").value.trim();
    //it gets the label for the error message
    let confirmPasswordError = document.getElementById("confirmPasswordError");
    //it first verify if the confirmPassword is empty
    if (confirmPassword === "") {
        //it sets the error message to the label error.
        confirmPasswordError.textContent = "* Please enter your password confirmation.";
        //it makes the variable false because the input is not correct
        isValid = false;
    } 
    //it compares to validate if the confirm password is correct
    else if(confirmPassword != password){
        confirmPasswordError.textContent = "* The password confirmation is different from the password.";
        //it makes the variable false because the input is not correct
        isValid = false;
    }    
    else {
        // it sets the label error to empty if there is no error
        confirmPasswordError.textContent = "";
    }

    // If everything is valid, show a success message 
    if (isValid) {
        //it allows form for submission
        return true;
    }
    else{
        event.preventDefault();
        //it prevents form for submission
        return false;
    }
}