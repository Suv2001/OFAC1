<!-- <?php
include("../templates/session_management.php");
include("../templates/db_connection.php");

// if (!isset($_SESSION['eid'])) {
//     header('location: ../index.php');
//     exit();
// }

include("../templates/user_auth.php");


$employee_id = $_SESSION['eid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['csvFile'])) {
        $filepath = $_FILES['csvFile']['tmp_name']; 
        if (($handle = fopen($filepath, mode: 'r')) !== FALSE) {
            fgetcsv($handle);

            echo '<div class="container my-5">';
            echo '<h2 class="text-center mb-4">Businesses Status Report</h2>';
            echo '<div class="table-responsive">';

            echo '<table class="table table-hover table-striped table-bordered">';
            echo '<thead class="thead-dark">';
            echo '<tr>';
            echo '<th>Business Name</th>';
            echo '<th>Owner</th>';
            echo '<th>Address</th>';
            echo '<th>Registration Number</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $business_name = mysqli_real_escape_string($conn, htmlspecialchars($data[0]));
                $owner = mysqli_real_escape_string($conn, htmlspecialchars($data[1]));
                $address = mysqli_real_escape_string($conn, htmlspecialchars($data[2]));
                $reg_no = mysqli_real_escape_string($conn, htmlspecialchars($data[3]));

                $query = "SELECT * FROM ofac_master WHERE reg_no LIKE '$reg_no'";
                $result = mysqli_query($conn, $query);
                $b_data = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $status = $b_data ? $b_data['status'] : 'Not Found';

                $history_query = "INSERT INTO upload_history (business_name, owner, address, reg_no, uploaded_by, status)
                                  VALUES ('$business_name', '$owner', '$address', '$reg_no', '$employee_id', '$status')";
                mysqli_query($conn, $history_query);

                echo '<tr>';
                echo "<td>$business_name</td>";
                echo "<td>$owner</td>";
                echo "<td>$address</td>";
                echo "<td>$reg_no</td>";
                echo "<td>$status</td>";
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            echo '</div>';

            fclose($handle);
        }
    }
}
?>