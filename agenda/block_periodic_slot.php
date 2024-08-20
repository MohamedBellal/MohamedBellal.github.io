<?php
$servername = "fdb1028.awardspace.net";
$username = "4518801_lhomme";
$password = ".0k6zrie2zgZ1;yO";
$dbname = "4518801_lhomme";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'], $_POST['time'], $_POST['barber_id'], $_POST['periodicity'])) {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $barber_id = intval($_POST['barber_id']);
    $periodicity = intval($_POST['periodicity']);
    $dateTime = new DateTime($date);

    $stmt = $conn->prepare("INSERT INTO blocked_slots_periodicity (date, time, barber_id, periodicity) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $date, $time, $barber_id, $periodicity);

    if ($stmt->execute()) {
        $success = true;

        // Calculate the future dates and insert them
        for ($i = 1; $i <= 9; $i++) {
            $dateTime->modify("+{$periodicity} weeks");
            $futureDate = $dateTime->format('Y-m-d');

            $stmt = $conn->prepare("INSERT INTO blocked_slots_periodicity (date, time, barber_id, periodicity) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssii", $futureDate, $time, $barber_id, $periodicity);

            if (!$stmt->execute()) {
                $success = false;
                break;
            }
        }

        if ($success) {
            echo "Periodic slots blocked successfully";
        } else {
            echo "Error blocking periodic slots: " . $stmt->error;
        }
    } else {
        echo "Error blocking initial slot: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
