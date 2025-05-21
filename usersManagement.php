<?php
require_once 'databaseConnection.php';
//if the user is not admin, it is redirected to the index page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

//gets all the users
$users = getAllUsers();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Users Management</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="scripts/user-management.js" defer></script>
</head>

<body>
    <!-- navbar section -->
    <?php include 'navbar.php'; ?>

    <main class="container mt-5">
        <h2 class="text-center mb-4">Users Management</h2>
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

        <!-- User form -->
        <form method="POST" class="mb-4" action="databaseConnection.php?action=insertEditUser"
            onsubmit="return validateForm(event)">
            <input type="hidden" name="user_id" id="user_id">

            <div class="form-row">
                <div class="form-group col-md-4 col-lg-2">
                    <label for="name">Full name:</label>
                    <input type="text" name="name" id="name" class="form-control" maxlength="200">
                </div>

                <div class="form-group col-md-4 col-lg-2">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" maxlength="100">
                </div>

                <div class="form-group col-md-4 col-lg-2">
                    <label for="phone">Phone:</label>
                    <input type="text" name="phone" id="phone" class="form-control" maxlength="10">
                </div>

                <div class="form-group col-md-4 col-lg-2">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="optional for update" maxlength="15">
                </div>

                <div class="form-group col-md-4 col-lg-2">
                    <label for="role">Role:</label>
                    <select name="role" id="role" class="form-control" placeholder="Select role">
                        <option value="user">User</option>
                        <option value="doctor">Doctor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="form-group col-md-4 col-lg-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark mr-2"><i class="fa fa-floppy-o mr-2"
                            aria-hidden="true"></i>Save</button>
                    <button type="button" class="btn btn-danger" onclick="clearFields()"><i class="fa fa-times mr-2"
                            aria-hidden="true"></i>Clean</button>
                </div>
            </div>

            <div class="form-row">
                <label class="text-danger ml-3" hidden>* Please, fill all the fields marked in red.</label>
            </div>
        </form>

        <!-- User list -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th style="min-width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td class="text-center">
                                <input type="hidden" name="appointment_id" value="<?= $user['id'] ?>">
                                <button class="btn btn-sm btn-info"
                                    onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                    <i class="fa fa-pencil mr-2" aria-hidden="true"></i>Edit</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <!-- footer section -->
    <?php include 'footer.php'; ?>
</body>

</html>