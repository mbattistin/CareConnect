function editUser(user) {
    document.getElementById('user_id').value = user.id;
    document.getElementById('name').value = user.full_name;
    document.getElementById('email').value = user.email;
    document.getElementById('phone').value = user.phone_number;
    document.getElementById('role').value = user.role;
    document.getElementById('password').value = '';
}

function clearFields(){
    document.getElementById('user_id').value = null;
    document.getElementById('name').value = null;
    document.getElementById('email').value = null;
    document.getElementById('phone').value = null;
    document.getElementById('role').value = null;
    document.getElementById('password').value = null;  
}

function validateForm(event) {
    //variable that holds if the inputs are valid
    let isValid = true;

    //name validation to have only letters and spaces
    //it gets the input from the html with the id name. Trim is to remove any additional space
    let name = document.getElementById("name").value.trim();

    //it uses regex to validate if the name contains only letters and spaces or if its empty
    if (!/^[A-Za-z\s]+$/.test(name) || name === "") {
        //it sets the error style to the input.
        document.getElementById("name").classList.add('is-invalid');
        //it makes the variable false because the input is not correct
        isValid = false;
    } 
    else {
        //it removes the error style to the input.
        document.getElementById("name").classList.remove('is-invalid');
    }

    //email validation 
    //it gets the input from the html with the id email. Trim is to remove any additional space
    let email = document.getElementById("email").value.trim();
    //it first verify if the email is empty or if its regex invalid
    if (email === "" || !/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(email)){
        document.getElementById("email").classList.add('is-invalid');
        //it makes the variable false because the input is not correct
        isValid = false;
    }
    else {
        //it removes the error style to the input.
        document.getElementById("email").classList.remove('is-invalid');
    }

    //phone validation to have only numbers between 9 to 10 digits
    //it gets the input from the html with the id phone. Trim is to remove any additional space
    let phone = document.getElementById("phone").value.trim();

    //it first verify if the phone is empty or if its regex invalid
    if (phone === "" || !/^\d{9,10}$/.test(phone)) {
        if (email === "" || !/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(email)){
            document.getElementById("phone").classList.add('is-invalid');
            //it makes the variable false because the input is not correct
            isValid = false;
        }
        else {
            //it removes the error style to the input.
            document.getElementById("phone").classList.remove('is-invalid');
        }
    }

    //password Validation
    //it gets the input from the html with the id password. Trim is to remove any additional space
    let password = document.getElementById("password").value.trim();
    let userId = document.getElementById("user_id").value.trim();
    //it first verify if the password is empty or if its regex invalid
    if ((password === "" || !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(password)) && userId != ""){
        document.getElementById("password").classList.add('is-invalid');
        //it makes the variable false because the input is not correct
        isValid = false;
    }    
    else {
        //it removes the error style to the input.
        document.getElementById("phone").classList.remove('is-invalid');
    }

    return isValid;

}