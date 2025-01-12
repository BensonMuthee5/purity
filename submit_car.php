<?php
// Database credentials
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "car_dealership"; 

// Create a connection to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle the file uploads
$target_dir = "uploads/"; 
$id_photo_front = $id_photo_back = ""; 

// Process the car details form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Car details
    $reg_number = $_POST['reg-number'];
    $make = $_POST['make'];
    $model = $_POST['model'];
    $colour = $_POST['colour'];
    $year = $_POST['year'];
    $mileage = $_POST['mileage'];
    $mileage_unit = $_POST['mileage-unit'];
    $accident_history = $_POST['accident-history'];
    $price = $_POST['price'];
    $location = $_POST['location'];

    // Contact details
    $first_name = $_POST['first-name'];
    $last_name = $_POST['last-name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $id_number = $_POST['id-number'];
    $contact_method = $_POST['contact-method'];
    $terms_accepted = isset($_POST['terms']) ? 1 : 0;

    // Handle ID photos (file uploads)
    if (isset($_FILES['id-photo-front']) && $_FILES['id-photo-front']['error'] == 0) {
        $id_photo_front = $target_dir . basename($_FILES['id-photo-front']['name']);
        move_uploaded_file($_FILES['id-photo-front']['tmp_name'], $id_photo_front);
    }

    if (isset($_FILES['id-photo-back']) && $_FILES['id-photo-back']['error'] == 0) {
        $id_photo_back = $target_dir . basename($_FILES['id-photo-back']['name']);
        move_uploaded_file($_FILES['id-photo-back']['tmp_name'], $id_photo_back);
    }

    // Insert the car details into the database
    $sql = "INSERT INTO car_details (reg_number, make, model, colour, year, mileage, mileage_unit, accident_history, price, location) 
            VALUES ('$reg_number', '$make', '$model', '$colour', '$year', '$mileage', '$mileage_unit', '$accident_history', '$price', '$location')";

    if ($conn->query($sql) === TRUE) {
        // Get the last inserted car details ID
        $car_id = $conn->insert_id;

        // Insert the contact details into the database
        $sql_contact = "INSERT INTO contact_details (first_name, last_name, email, phone, id_number, contact_method, id_photo_front, id_photo_back, terms_accepted) 
                        VALUES ('$first_name', '$last_name', '$email', '$phone', '$id_number', '$contact_method', '$id_photo_front', '$id_photo_back', '$terms_accepted')";

        if ($conn->query($sql_contact) === TRUE) {
            // Send a confirmation email
            $to = $email; // Send confirmation to the user
            $subject = "Car Submission Confirmation";
            $message = "Thank you for submitting your car details. We will get back to you soon.";
            $headers = "From: no-reply@company.com"; // Replace with your email
            mail($to, $subject, $message, $headers);

            echo "Car details and contact details submitted successfully!";
        } else {
            echo "Error submitting contact details: " . $conn->error;
        }
    } else {
        echo "Error submitting car details: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
