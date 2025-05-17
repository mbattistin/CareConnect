function clearFields(){
    document.getElementById('doctor_id').value = "";
    document.getElementById('user_id').value = "";
    document.getElementById('appointment_date').value = null;
    document.getElementById('observation').value = null; 
}

function validateForm(event) {
    //variable that holds if the inputs are valid
    let isValid = true;

    //it gets the input from the html with the id. Trim is to remove any additional space
    let doctorId = document.getElementById("doctor_id").value.trim();

    //it validates if the id its empty
    if (doctorId === "") {
        //it sets the error style to the input.
        document.getElementById("doctor_id").classList.add('is-invalid');
        //it makes the variable false because the input is not correct
        isValid = false;
    } 
    else {
        //it removes the error style to the input.
        document.getElementById("doctor_id").classList.remove('is-invalid');
    }

    //it gets the input from the html with the id. Trim is to remove any additional space
    let userId = document.getElementById("user_id").value.trim();

    //it validates if the id its empty
    if (userId === "") {
        //it sets the error style to the input.
        document.getElementById("user_id").classList.add('is-invalid');
        //it makes the variable false because the input is not correct
        isValid = false;
    } 
    else {
        //it removes the error style to the input.
        document.getElementById("user_id").classList.remove('is-invalid');
    }

        //it gets the input from the html with the id. Trim is to remove any additional space
    let dateTime = document.getElementById("appointment_date").value.trim();

    //it validates if the id its empty
    if (dateTime === "") {
        //it sets the error style to the input.
        document.getElementById("appointment_date").classList.add('is-invalid');
        //it makes the variable false because the input is not correct
        isValid = false;
    } 
    else {
        //it removes the error style to the input.
        document.getElementById("appointment_date").classList.remove('is-invalid');
    }

    return isValid;

}