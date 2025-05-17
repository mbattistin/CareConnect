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
            <form class="mb-4" method="POST" action="databaseConnection.php?action=addAppointment"  onsubmit="return validateForm(event)">
                <div class="form-row">
                    <div class="col">
                        <label for="user_id" class="form-label">Patient:</label>
                    </div>
                    <div class="col">
                        <label for="doctor_id" class="form-label">Doctor:</label>
                    </div>
                    <div class="col">
                        <label for="phappointment_dateone" class="form-label">Date:</label>
                    </div>
                    <div class="col">
                        <label for="observation" class="form-label">Observation:</label>
                    </div>
                    <div class="col"></div>
                </div>
                <div class="form-row">
                    <?php if ($_SESSION['role'] === 'admin') : ?>
                        <div class="col">
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="">Select Patient</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="col">
                        <select name="doctor_id" id="doctor_id" class="form-control" required>
                            <option value="">Select Doctor</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= $doctor['id'] ?>"><?= htmlspecialchars($doctor['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col">
                        <input type="text" name="appointment_date" id="appointment_date" class="form-control" placeholder="Select Date and Time" required>
                    </div>

                    <div class="col">
                        <input type="text" name="observation" id="observation" class="form-control" placeholder="Observation (optional)" maxlength="500">
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="submit" class="btn btn-danger" onclick="clearFields()">Clean</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>                       
        <?php if (empty($appointments)) : ?>
            <div class="alert alert-info">No appointments found.</div>
        <?php else : ?>
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <?php if ($_SESSION['role'] !== 'user') echo "<th>Patient</th>"; ?>
                        <?php if ($_SESSION['role'] !== 'doctor') echo "<th>Doctor</th>"; ?>
                        <th>Observation</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appt): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($appt['appointment_date'])) ?></td>
                            <?php if ($_SESSION['role'] !== 'user') echo "<td>" . htmlspecialchars($appt['user_name']) . "</td>"; ?>
                            <?php if ($_SESSION['role'] !== 'doctor') echo "<td>" . htmlspecialchars($appt['doctor_name']) . "</td>"; ?>
                            <td><?= htmlspecialchars($appt['observation']) ?></td>
                            <td>
                                <form action="databaseConnection.php?action=removeAppointment" method="POST" onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                                    <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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