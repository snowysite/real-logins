<?php
// filepath: /c:/Users/dell/Documents/text/process_login.php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'login_system');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to check user credentials
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Login successful! Redirecting to dashboard...');</script>";
        // Redirect to dashboard
        header('Location: dashboard.php');
        exit();
    } else {
        echo "<script>alert('Invalid username or password. Please try again.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Simple authentication logic (replace with database queries)
    if ($username === 'admin' && $password === 'password123') {
        echo "<script>alert('Login Successful! Welcome, $username.');</script>";
        // Redirect to dashboard
        // header("Location: dashboard.php");
    } else {
        echo "<script>alert('Invalid credentials! Please try again.');</script>";
    }
}
?>
<?php
// login.php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$database = "login_system";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // Query to check if the email exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Verify the password
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Start a session and redirect to a dashboard
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('No account found with that email.');</script>";
    }
}

$conn->close();
?>
<?php
// register.php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$database = "login_system";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        // Check if email already exists
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<script>alert('Email already registered.');</script>";
        } else {
            // Hash the password and insert into database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$hashed_password')";

            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Registration successful!');</script>";
                header("Location: login.php");
                exit();
            } else {
                echo "<script>alert('Error: " . $conn->error . "');</script>";
            }
        }
    }
}

$conn->close();
?>
<?php
// config.php
$servername = "localhost";
$username = "root";
$password = "";
$database = "login_system";

// Create database connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php
require 'config.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        // Check if email already exists
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<script>alert('Email already registered.');</script>";
        } else {
            // Hash the password and insert into database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$hashed_password')";

            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Registration successful! Please login.');</script>";
                header("Location: login.php");
                exit();
            } else {
                echo "<script>alert('Error: " . $conn->error . "');</script>";
            }
        }
    }
}

$conn->close();
?>
<?php
require 'config.php'; // Include database connection
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // Query to check if the email exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('No account found with that email.');</script>";
    }
}

$conn->close();
?>
<?php
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit();
?>
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

echo "<h1>Welcome, " . htmlspecialchars($_SESSION['username']) . "!</h1>";
echo "<p><a href='logout.php'>Logout</a></p>";
?>
