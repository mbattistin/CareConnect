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
else {
    
}

// Function to connect and create DB/table if needed
function getDatabaseConnection() {
    // Connect without selecting DB first
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create DB if not exists
    $conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);

    // Select the database
    $conn->select_db(DB_NAME);

    // Create tables if they do not exist
    $createTableSQL = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(200) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number INT(10),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user', 'doctor') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);

    CREATE TABLE IF NOT EXISTS specialties (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        description VARCHAR(500) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS doctors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        specialty_id INT NOT NULL,
        description VARCHAR(500) NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (specialty_id) REFERENCES specialties(id)
    );

    CREATE TABLE IF NOT EXISTS appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        doctor_id INT NOT NULL,
        appointment_date TIMESTAMP NOT NULL,
        observation VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (doctor_id) REFERENCES doctors(id)
    )";
    $conn->query($createTableSQL);

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

?>