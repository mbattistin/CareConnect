<?php
require_once 'databaseConnection.php';

//if no user is logged in, it returns to the index page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

//gets the appointments by user role
$appointments = getAppointments($_SESSION['user_id'], $_SESSION['role']);
//get the doctors for the dropdown
$doctors = getUsersByRole('doctor');
$users = [];

//it just gets the users if the role is admin
if ($_SESSION['role'] === 'admin') {
    $users = getUsersByRole('user');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Appointments</title>
    <!-- Boostrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="scripts/appointments-form-validation.js" defer></script>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <main class="container mt-5">
        <h2 class="text-center mb-4">Appointments</h2>
        <!-- messages section -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success_message'];
                unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message'];
                unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        <!-- Insert appointment section -->
        <!-- It just allows admin and users to insert an appointment -->
        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'user'): ?>
            <form method="POST" action="databaseConnection.php?action=addAppointment" onsubmit="return validateForm(event)">
                <div class="form-row">
                    <!-- Admins can select the patient, otherwise the patient id is the user id  -->
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <div class="form-group col-md-6 col-lg-3">
                            <label for="user_id">Patient:</label>
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="">Select Patient</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <!-- doctor dropdown -->
                    <div class="form-group col-md-6 col-lg-3">
                        <label for="doctor_id">Doctor:</label>
                        <select name="doctor_id" id="doctor_id" class="form-control">
                            <option value="">Select Doctor</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= $doctor['id'] ?>"><?= htmlspecialchars($doctor['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Flatpickr component used to make it easier to select date and time -->
                    <div class="form-group col-md-6 col-lg-3">
                        <label for="appointment_date">Date:</label>
                        <input type="text" name="appointment_date" id="appointment_date" class="form-control"
                            placeholder="Select Date and Time">
                    </div>

                    <div class="form-group col-md-6 col-lg-3">
                        <label for="observation">Observation:</label>
                        <input type="text" name="observation" id="observation" class="form-control"
                            placeholder="Observation (optional)" maxlength="500">
                    </div>
                </div>

                <div class="form-group d-flex flex-wrap gap-2 justify-content-end">
                    <button type="submit" class="btn btn-dark mr-2 mb-2"><i class="fa fa-floppy-o mr-2"
                            aria-hidden="true"></i>Save</button>
                    <button type="button" class="btn btn-danger mb-2" onclick="clearFields()"><i class="fa fa-times mr-2"
                            aria-hidden="true"></i>Clean</button>
                </div>
            </form>
        <?php endif; ?>
        <!-- Appointments table section -->
        <?php if (empty($appointments)): ?>
            <div class="alert alert-info">No appointments found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Date</th>
                            <!-- no need to show the patient when the user is logged in -->
                            <?php if ($_SESSION['role'] !== 'user')
                                echo "<th>Patient</th>"; ?>
                            <!-- no need to show the doctor when the doctor is logged in -->
                            <?php if ($_SESSION['role'] !== 'doctor')
                                echo "<th>Doctor</th>"; ?>
                            <th>Observation</th>
                            <th style="width: 120px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($appt['appointment_date'])) ?></td>
                                <?php if ($_SESSION['role'] !== 'user')
                                    echo "<td>" . htmlspecialchars($appt['user_name']) . "</td>"; ?>
                                <?php if ($_SESSION['role'] !== 'doctor')
                                    echo "<td>" . htmlspecialchars($appt['doctor_name']) . "</td>"; ?>
                                <td><?= htmlspecialchars($appt['observation']) ?></td>
                                <td class="text-center">
                                    <form action="databaseConnection.php?action=removeAppointment" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                                        <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash-o mr-2"
                                                aria-hidden="true"></i>Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
    <!-- footer section -->
    <?php include 'footer.php'; ?>
    <!-- Flatpickr JS -->
    <!-- Documentation: https://flatpickr.js.org -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#appointment_date", {
            //shows time selector
            enableTime: true,
            //sets date format
            dateFormat: "d/m/Y H:i",
            //time format 24h
            time_24hr: true,
            //just allows to increment minutes by 30
            minuteIncrement: 30,
            //sets the enable date selection from tomorrow's date
            minDate: new Date().fp_incr(1),
            //just allow time bigger or equal 9
            minTime: "09:00",
            //just allow time less equal 16
            maxTime: "16:00",
            //disable user input, so they can just use the selection to avoid wrong data
            allowInput: false,
            //disable weekends selection
            disable: [
                function (date) {
                    return (date.getDay() === 0 || date.getDay() === 6);
                }
            ],
            //function that disables user to inser manually time     
            onOpen: function (selectedDates, dateStr, instance) {
                setTimeout(() => {
                    const timeInputs = instance.calendarContainer.querySelectorAll(".flatpickr-time input");
                    timeInputs.forEach(input => input.setAttribute("readonly", "readonly"));
                }, 0);
            }
        });
    </script>
</body>

</html>