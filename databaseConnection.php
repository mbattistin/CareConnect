<?php

session_start();

//Database parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'careconnect');

// Main router
$action = $_GET['action'] ?? '';

$conn = getDatabaseConnection();

if ($action == 'insertUserFromRegisterForm') {
    insertUserFromRegisterForm($conn, $_POST);
}
else if($action == 'userLogin'){
    userLogin($conn, $_POST);
}
else if($action == 'insertEditUser'){
    insertEditUser($conn, $_POST);
}
else if($action == 'userLogOut'){
    session_unset();
    session_destroy();
    header("Location: signIn.php");
    exit;
}
else if($action== 'addAppointment') {
    try {
        addAppointment($conn, $_POST);
        $_SESSION['success_message'] = "Appointment successfully added.";
    } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $_SESSION['error_message'] = "The selected doctor already has an appointment scheduled for this date and time.";
            } else {
                $_SESSION['error_message'] = "Error adding appointment, please try again later.";
            }
    }
    header("Location: appointments.php");
    exit();
}
else if($action == 'removeAppointment'){
    removeAppointment($conn, $_POST);
}
else {
    
}

// Function to connect and create DB/table if needed
function getDatabaseConnection() {
    // Connect to the database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($conn->connect_error) {
        die("Conection error: " . $conn->connect_error);
    }

    // Creates the database if not exists
    if (!$conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME)) {
        die("Error creating the database: " . $conn->error);
    }

    // selects the database
    $conn->select_db(DB_NAME);

    // create the users table if not exits
    $createUsersTable = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(200) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone_number INT(10),
        password VARCHAR(15) NOT NULL,
        role ENUM('admin', 'user', 'doctor') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if (!$conn->query($createUsersTable)) {
        die("Error creating the table users: " . $conn->error);
    }

    // create table appointments if not exists
    $createAppointmentsTable = "CREATE TABLE IF NOT EXISTS appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        doctor_id INT NOT NULL,
        appointment_date TIMESTAMP NOT NULL,
        observation VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_doctor_date (doctor_id, appointment_date),
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (doctor_id) REFERENCES users(id)
    )";

    if (!$conn->query($createAppointmentsTable)) {
        die("Error creating the table appointments " . $conn->error);
    }

    // verifies if an admin is already created
    $adminCheck = $conn->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");

    if ($adminCheck && $adminCheck->num_rows === 0) {
        // puts criptography to the users password
        $adminPassword = password_hash("Pass1234!", PASSWORD_DEFAULT);
        $name = "Administrator";
        $email = "admin@careconnect.com";
        $phone = 1234567890;
        $password = $adminPassword;
        $role = "admin";

        // insert the admin in the table
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?)");
        //bind the parameters to the connection
        $stmt->bind_param("ssiss", $name, $email, $phone, $password, $role);

        //executes the sql
        if (!$stmt->execute()) {
            die("Error creating the user admin " . $stmt->error);
        }
        $stmt->close();
    }

    return $conn;
}

// Insert user
function insertUserFromRegisterForm($conn, $data) {
    $name = $conn->real_escape_string($data['name']);
    $email = $conn->real_escape_string($data['email']);
    $phone = $conn->real_escape_string($data['phone']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (full_name, email, phone_number, password)
            VALUES ('$name', '$email', '$phone', '$password')";

    if ($conn->query($sql)) {
        header("Location: signIn.php?registration_success_message");
        exit();
    } else {
        
        // after error registration
        $_SESSION['registration_error_message'] = "Something went wrong during registration. Please try again later.";
        header("Location: register.php");
        exit();    
    }
}

//login
function userLogin($conn, $data) {
    $email = $conn->real_escape_string($data['email']);
    $password = $data['password']; // use raw password input
    
    // Check if user exists in the database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // If user found, verify password
    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();
    
      if (password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_id'] = $user['id'];
    
        header("Location: index.php");
        exit;
      } else {
        $_SESSION['login_error_message'] = "Invalid credentials. Please try again.";
        header("Location: signIn.php");
        exit(); 
      }
    } else {
        $_SESSION['login_error_message'] = "User not found. Please register before attempting to log in.";
        header("Location: signIn.php");
        exit(); 
    }
}

    function getAllUsers() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $users = [];
    $stmt = $conn->prepare("SELECT id, full_name, email, phone_number, role FROM users ORDER BY full_name ASC");

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        $stmt->close();
    }

    $conn->close();
    return $users;
    }

    function getAppointments($user_id, $user_role) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $appointments = [];

        $sql = "SELECT a.id, a.appointment_date, a.observation,  d.full_name AS doctor_name,  u.full_name AS user_name 
                FROM appointments a JOIN users d ON a.doctor_id = d.id JOIN users u ON a.user_id = u.id order by a.appointment_date";

        // Modify query based on role
        if ($user_role === 'doctor') {
            $sql .= " WHERE a.doctor_id = ?";
        } elseif ($user_role === 'user') {
            $sql .= " WHERE a.user_id = ?";
        }

        $stmt = $conn->prepare($sql);

        if ($user_role === 'admin') {
            $stmt->execute();
        } else {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
        }

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }

        $stmt->close();
        return $appointments;
    }   

    function insertEditUser($conn, $data) {
        $userId = $id = isset($data['user_id']) ? intval($data['user_id']) : 0; 
        $name = $conn->real_escape_string($data['name']);
        $email = $conn->real_escape_string($data['email']);
        $phone = $conn->real_escape_string($data['phone']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = $conn->real_escape_string($data['role']);

        if ($id > 0) {
            // Update existing user
            $sql = "UPDATE users SET full_name='$name', email='$email', phone_number='$phone', role='$role'";
            if (!empty($password)) {
                $sql .= ", password='$password'";
            }
            $sql .= " WHERE id=$id";
        } else {
            // Create new user
            $sql = "INSERT INTO users (full_name, email, phone_number, password, role)
                    VALUES ('$name', '$email', '$phone', '$password', '$role')";
        }

        if ($conn->query($sql)) {
            header("Location: usersManagement.php?success=1");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }

    function getUsersByRole($role){
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $users = [];

        $stmt = $conn->prepare("SELECT id, full_name, email, phone_number, role FROM users WHERE role = ? ORDER BY full_name ASC");
        $stmt->bind_param("s", $role);  // "s" indicates the role is a string
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        $stmt->close();
        $conn->close(); 

        return $users;
    }

    function addAppointment($conn,$data) {
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : $_SESSION['user_id'];
    $doctor_id = intval($data['doctor_id']);
    $observation = $conn->real_escape_string($data['observation'] ?? '');
    $appointment_date = DateTime::createFromFormat('d/m/Y H:i', $data['appointment_date']);

    if (!$appointment_date) {
        throw new Exception("Invalid date format.");
    }

    $formatted_date = $appointment_date->format('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_id, appointment_date, observation) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("iiss", $user_id, $doctor_id, $formatted_date, $observation);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();
}

function removeAppointment($conn, $data) {
    $appointmentId = intval($data['appointment_id']);

    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $appointmentId);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Appointment deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete appointment.";
    }

    $stmt->close();
    $conn->close();

    header("Location: appointments.php"); 
    exit();
}
?>