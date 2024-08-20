<?php
$servername = "fdb1028.awardspace.net";
$username = "4518801_lhomme";
$password = ".0k6zrie2zgZ1;yO";
$dbname = "4518801_lhomme";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $barber_id = $_POST['barber_id'];

    $sql = "DELETE FROM blocked_slots_periodicity WHERE date='$date' AND time='$time' AND barber_id='$barber_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Periodic slot unblocked successfully";
    } else {
        echo "Error unblocking periodic slot: " . $conn->error;
    }
}
$conn->close();
?>
