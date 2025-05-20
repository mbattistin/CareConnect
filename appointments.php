<?php
require_once 'databaseConnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$appointments = getAppointments($_SESSION['user_id'], $_SESSION['role']);
$doctors = getUsersByRole('doctor'); 
$users = [];    

if($_SESSION['role'] === 'admin'){
   $users= getUsersByRole('user');
}

$currentPage = 'Appointments';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments</title>
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

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'user') : ?>
            <form method="POST" action="databaseConnection.php?action=addAppointment" onsubmit="return validateForm(event)">
                <div class="form-row">
                    <?php if ($_SESSION['role'] === 'admin') : ?>
                        <div class="form-group col-md-6 col-lg-3">
                            <label for="user_id">Patient:</label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="">Select Patient</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="form-group col-md-6 col-lg-3">
                        <label for="doctor_id">Doctor:</label>
                        <select name="doctor_id" id="doctor_id" class="form-control" required>
                            <option value="">Select Doctor</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= $doctor['id'] ?>"><?= htmlspecialchars($doctor['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6 col-lg-3">
                        <label for="appointment_date">Date:</label>
                        <input type="text" name="appointment_date" id="appointment_date" class="form-control" placeholder="Select Date and Time" required>
                    </div>

                    <div class="form-group col-md-6 col-lg-3">
                        <label for="observation">Observation:</label>
                        <input type="text" name="observation" id="observation" class="form-control" placeholder="Observation (optional)" maxlength="500">
                    </div>
                </div>

                <div class="form-group d-flex flex-wrap gap-2 justify-content-end">
                    <button type="submit" class="btn btn-dark mr-2 mb-2"><i class="fa fa-floppy-o mr-2" aria-hidden="true"></i>Save</button>
                    <button type="button" class="btn btn-danger mb-2" onclick="clearFields()"><i class="fa fa-times mr-2" aria-hidden="true"></i>Clean</button>
                </div>
            </form>
        <?php endif; ?>

        <?php if (empty($appointments)) : ?>
            <div class="alert alert-info">No appointments found.</div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Date</th>
                            <?php if ($_SESSION['role'] !== 'user') echo "<th>Patient</th>"; ?>
                            <?php if ($_SESSION['role'] !== 'doctor') echo "<th>Doctor</th>"; ?>
                            <th>Observation</th>
                            <th  style="width: 120px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($appt['appointment_date'])) ?></td>
                                <?php if ($_SESSION['role'] !== 'user') echo "<td>" . htmlspecialchars($appt['user_name']) . "</td>"; ?>
                                <?php if ($_SESSION['role'] !== 'doctor') echo "<td>" . htmlspecialchars($appt['doctor_name']) . "</td>"; ?>
                                <td><?= htmlspecialchars($appt['observation']) ?></td>
                                <td class="text-center">
                                    <form action="databaseConnection.php?action=removeAppointment" method="POST" onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                                        <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash-o mr-2" aria-hidden="true"></i>Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <!-- Flatpickr JS -->
     <!-- Documentation: https://flatpickr.js.org -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#appointment_date", {
            enableTime: true,
            dateFormat: "d/m/Y H:i",
            time_24hr: true,
            minuteIncrement:30,
            minDate: new Date().fp_incr(1), // tomorrow
            minTime: "09:00",
            maxTime: "16:00",
            allowInput: false,
            disable: [
                function(date) {
                    return (date.getDay() === 0 || date.getDay() === 6); // disable weekends
                }
            ],     
            onOpen: function(selectedDates, dateStr, instance) {
                setTimeout(() => {
                    const timeInputs = instance.calendarContainer.querySelectorAll(".flatpickr-time input");
                    timeInputs.forEach(input => input.setAttribute("readonly", "readonly"));
                }, 0);
                }
            });
    </script>
</body>
</html>