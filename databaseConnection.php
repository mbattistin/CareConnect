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

//switch case to check with action was called
switch ($action) {
  case 'insertUserFromRegisterForm':
    insertUserFromRegisterForm($conn, $_POST);
    break;
  case 'userLogin':
    userLogin($conn, $_POST);
    break;
  case 'insertEditUser':
    insertEditUser($conn, $_POST);
    break;
  case 'userLogOut':
    
    //free all the sets variables
    session_unset();
    //removes all session data
    session_destroy();
    //goes back to the sign in page
    header("Location: signIn.php");
    exit;
    break; 
  case 'addAppointment':
    addAppointment($conn, $_POST);
    break;
  case 'removeAppointment':
    removeAppointment($conn, $_POST);
    break;
  default:
    
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
    //return the connection
    return $conn;
}

// Insert user
function insertUserFromRegisterForm($conn, $data) {
    //gets all the variables
    $name = $conn->real_escape_string($data['name']);
    $email = $conn->real_escape_string($data['email']);
    $phone = $conn->real_escape_string($data['phone']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    //builds sql command to insert
    $sql = "INSERT INTO users (full_name, email, phone_number, password)
            VALUES ('$name', '$email', '$phone', '$password')";

    //if successful goes to sig in page with a success message
    if ($conn->query($sql)) {
        header("Location: signIn.php?registration_success_message");
        exit();
    } else {
        // if an error registration happen stores a message in the session
        $_SESSION['registration_error_message'] = "Something went wrong during registration. Please try again later.";
        //return to registration page
        header("Location: register.php");
        exit();    
    }
}

//login
function userLogin($conn, $data) {
    //gets all the variables
    $email = $conn->real_escape_string($data['email']);
    $password = $data['password']; 
    
    // Check if user exists in the database
    //create select command
    $sql = "SELECT * FROM users WHERE email = ?";
    //prepares the command
    $stmt = $conn->prepare($sql);
    //binds the parameter
    $stmt->bind_param("s", $email);
    //executes the command
    $stmt->execute();
    //gets result
    $result = $stmt->get_result();
    
    // If user found, verify password
    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();
      //verifies if passaword match
      if (password_verify($password, $user['password'])) {
        //stores user information in the session variables 
        $_SESSION['username'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_id'] = $user['id'];
        //redirects to the index
        header("Location: index.php");
        exit;
      } else {
        //stores error message
        $_SESSION['login_error_message'] = "Invalid credentials. Please try again.";
        //returns to sign in page
        header("Location: signIn.php");
        exit(); 
      }
    } else {
        //stores error message
        $_SESSION['login_error_message'] = "User not found. Please register before attempting to log in.";
        //returns to sign in page
        header("Location: signIn.php");
        exit(); 
    }
}

function getAllUsers() {
    //creates database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    //checks if connection worked
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $users = [];
    //creates select command
    $stmt = $conn->prepare("SELECT id, full_name, email, phone_number, role FROM users ORDER BY full_name ASC");

    if ($stmt) {
        //executes command 
        $stmt->execute();
        //receives the result
        $result = $stmt->get_result();
        //populates the array with the result
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        //close connection
        $stmt->close();
    }
    //close connection with the database
    $conn->close();
    //return users
    return $users;
}

function getAppointments($user_id, $user_role) {
    //creates database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    //checks if connection worked
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $appointments = [];
    //creates the select command
    $sql = "SELECT a.id, a.appointment_date, a.observation,  d.full_name AS doctor_name,  u.full_name AS user_name 
            FROM appointments a JOIN users d ON a.doctor_id = d.id JOIN users u ON a.user_id = u.id";

    // Modify command based on role
    if ($user_role === 'doctor') {
        $sql .= " WHERE a.doctor_id = ?";
    } elseif ($user_role === 'user') {
        $sql .= " WHERE a.user_id = ?";
    }
    //adds the order by to the query
    $sql = $sql . " ORDER BY a.appointment_date";
    //prepares the command
    $stmt = $conn->prepare($sql);

    //if its not admin, bind the user id parameter
    if ($user_role !== 'admin') {
        $stmt->bind_param("i", $user_id);
    }
    //executes query
    $stmt->execute();
    //gets result
    $result = $stmt->get_result();
    //populates the array
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    //close connection
    $stmt->close();

    //return appointments
    return $appointments;
}   

function insertEditUser($conn, $data) {
    //gets all variables
    $id = isset($data['user_id']) ? intval($data['user_id']) : 0; 
    $name = trim($data['name']);
    $email = trim($data['email']);
    $phone = trim($data['phone']);
    $password = isset($data['password']) ? trim($data['password']) : '';
    $role = trim($data['role']);

    try {
        // update existent user
        if ($id > 0) {
            //checks if password was changed
            if (!empty($password)) {
                //encripts password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                //creates update query
                $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone_number=?, password=?, role=? WHERE id=?");
                //bind parameters
                $stmt->bind_param("sssssi", $name, $email, $phone, $hashedPassword, $role, $id);
            } else {
                //creates update query
                $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone_number=?, role=? WHERE id=?");
                //binds parameters
                $stmt->bind_param("ssssi", $name, $email, $phone, $role, $id);
            }
        } else {
            //  exception if the password is empty for new user
            if (empty($password)) {
                //stores error message
                $_SESSION['error_message'] ="Password is required to create a new user.";
            }
            //encripts password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            //creates insert connection
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?)");
            //binds parameters
            $stmt->bind_param("sssss", $name, $email, $phone, $hashedPassword, $role);
        }

        if (!$stmt->execute()) {
            // it get if the error is duplicate key
            if ($conn->errno == 1062) {
                $_SESSION['error_message'] = "A user with this email already exists.";
            } else {
                //stores error message
                $_SESSION['error_message'] ="An error happened when adding the user.";
            }  
        }
        else{
            //stores success message
            $_SESSION['success_message'] ="User successfully saved.";
        }
        //closes connection
        $stmt->close();

    } catch (Exception $e) {
        //stores error message if exception happen
        $_SESSION['error_message'] ="An error happened when adding the user.";
    }
    //redirects to user management page
    header("Location: usersManagement.php"); 
    exit();
}

function getUsersByRole($role){
    //creates database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    //checks if connection has error
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $users = [];
    //creates select command
    $stmt = $conn->prepare("SELECT id, full_name, email, phone_number, role FROM users WHERE role = ? ORDER BY full_name ASC");
    //binds parameter
    $stmt->bind_param("s", $role); 
    //executes command 
    $stmt->execute();
    //gets resutl
    $result = $stmt->get_result();
    //stores result in array
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    //close connection
    $stmt->close();
    $conn->close(); 
    //return users
    return $users;
}

function addAppointment($conn,$data) {
    //gets variables
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : $_SESSION['user_id'];
    $doctor_id = intval($data['doctor_id']);
    $observation = $conn->real_escape_string($data['observation'] ?? '');
    $appointment_date = DateTime::createFromFormat('d/m/Y H:i', $data['appointment_date']);
    //verifies if the data format is valid
    if (!$appointment_date) {
        //stores error message
        $_SESSION['error_message'] ="Invalid date format.";
    }
    //formats date
    $formatted_date = $appointment_date->format('Y-m-d H:i:s');
    //creates insert command
    $stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_id, appointment_date, observation) VALUES (?, ?, ?, ?)");
    //binds parameters
    $stmt->bind_param("iiss", $user_id, $doctor_id, $formatted_date, $observation);
        if (!$stmt->execute()) {
            // it get if the error is duplicate key
            if ($conn->errno == 1062) {
                $_SESSION['error_message'] = "An appointment with the same time and doctor already exists.";
            }else {
                //stores insertion error
                $_SESSION['error_message'] = "An error happened when adding the appointment.";
            }
        }else{
            //stores success message
            $_SESSION['success_message'] ="Appointment successfully added.";
        }
    //closes connection
    $stmt->close();

    //redirects to appointment page
    header("Location: appointments.php"); 
    exit();
}

function removeAppointment($conn, $data) {
    //gets variable
    $appointmentId = intval($data['appointment_id']);
    //Creates delete command
    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
    //binds the parameter
    $stmt->bind_param("i", $appointmentId);
    //executes query
    if ($stmt->execute()) {
        //stores success message
        $_SESSION['success_message'] = "Appointment deleted successfully.";
    } else {
        //stores error message
        $_SESSION['error_message'] = "An error happened when deleting the appointment.";
    }
    //close connection
    $stmt->close();
    //redirects to appointment page
    header("Location: appointments.php"); 
    exit();
}
?>