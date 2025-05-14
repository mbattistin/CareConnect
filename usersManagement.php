<?php
require_once 'databaseConnection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$users = getAllUsers();

$currentPage = 'User Management';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Users Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="scripts/user-management.js" defer></script>
</head>
<body>
    <!-- navbar section -->
    <?php include 'navbar.php'; ?>
    
    <h2 class="text-center">Users Management</h2>
    <main class="container mt-5">
        <?php if (!empty($message)) : ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- User form -->
        <form method="POST" class="mb-4" method="POST" action="databaseConnection.php?action=insertEditUser" onsubmit="return validateForm(event)">
            <input type="hidden" name="user_id" id="user_id">
            <div class="form-row">
                <div class="col">
                    <label for="name" class="form-label">Full name:</label>
                </div>
                <div class="col">
                    <label for="email" class="form-label">Email:</label>
                </div>
                <div class="col">
                    <label for="phone" class="form-label">Phone number:</label>
                </div>
                <div class="col">
                    <label for="password" class="form-label">Password:</label>
                </div>
                <div class="col">
                    <label for="role" class="form-label">Role:</label>
                </div>
                <div class="col">

                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="col">
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="col">
                    <input type="text" name="phone" id="phone" class="form-control" required>
                </div>
                <div class="col">
                    <input type="password" name="password" id="password" class="form-control" placeholder="optional for update">
                </div>
                <div class="col">
                    <select name="role" id="role" class="form-control" required>
                        <option value="">Select Role</option>
                        <option value="user">User</option>
                        <option value="doctor">Doctor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary" >Save</button>
                    <button type="submit" class="btn btn-danger" onclick="clearFields()">Clean </button>
                </div>
            </div>
            <div class="form-row">
                <label class="danger" hidden>* Please, fill all the fields marked in red.</label>
            </div>
        </form>

        <!-- User list -->
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">Edit</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
