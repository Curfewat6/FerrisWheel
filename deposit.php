<?php
    session_start();

    if (isset($_POST['deposit']) && isset($_SESSION['userid'])) {
        $deposit = $_POST['deposit'];
        $user_id = $_SESSION['userid'];

        // Validate deposit
        if (!is_numeric($deposit) || $deposit <= 0) {
            echo json_encode(['error' => 'Invalid deposit amount']);
            exit;
        }

        include 'db_con.php';

        $stmt = $conn->prepare("UPDATE user_table SET funds = funds + ? WHERE user_id = ?");
        if ($stmt === false) {
            echo json_encode(['error' => 'Database error: ' . $conn->error]);
            exit;
        }

        $rc = $stmt->bind_param("di", $deposit, $user_id);
        if ($rc === false) {
            echo json_encode(['error' => 'Database error: ' . $stmt->error]);
            exit;
        }

        $rc = $stmt->execute();
        if ($rc === false) {
            echo json_encode(['error' => 'Database error: ' . $stmt->error]);
            exit;
        }

        $stmt->close();
        $conn->close();

        echo json_encode(['success' => 'Deposit made successfully']);
    } else {
        echo json_encode(['error' => 'Deposit or user ID not set']);
    }
?>