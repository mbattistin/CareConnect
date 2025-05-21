function editUser(user) {
    //it gets the user property and bind into the form inputs
    document.getElementById('user_id').value = user.id;
    document.getElementById('name').value = user.full_name;
    document.getElementById('email').value = user.email;
    document.getElementById('phone').value = user.phone_number;
    document.getElementById('role').value = user.role;
    document.getElementById('password').value = '';
}

function clearFields() {
    //it sets the form inputs as null
    document.getElementById('user_id').value = "";
    document.getElementById('name').value = "";
    document.getElementById('email').value = "";
    document.getElementById('phone').value = "";
    document.getElementById('role').value = "";
    document.getElementById('password').value = "";  
}

function validateForm(event) {
    let isValid = true;
    //gets the elements by the id to validate
    const name = document.getElementById("name");
    const email = document.getElementById("email");
    const phone = document.getElementById("phone");
    const password = document.getElementById("password");
    const userId = document.getElementById("user_id").value;

    //it validates if name has just letters and spaces
    if (!/^[A-Za-zÀ-ÿ\s]+$/.test(name.value.trim())) {
        //applies the invalid style in the input
        name.classList.add('is-invalid');
        isValid = false;
    } 

    //it validates the regex email
    if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email.value.trim())) {
        //applies the invalid style in the input
        email.classList.add('is-invalid');
        isValid = false;
    } 

    //it validates phone number between 9-10 digits
    if (!/^\d{9,10}$/.test(phone.value.trim())) {
        //applies the invalid style in the input
        phone.classList.add('is-invalid');
        isValid = false;
    }

    // it validates mandatory password for new users
    if (password.value.trim() !== "") {
        if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(password.value.trim())) {
            //applies the invalid style in the input
            password.classList.add('is-invalid');
            isValid = false;
        }

    } else if (userId === "") {
        //password is mandatory for new users
        //applies the invalid style in the input
        password.classList.add('is-invalid');
        isValid = false;
    } else {
        //removes the invalid style in the input
        password.classList.remove('is-invalid');
    }

    return isValid;
}