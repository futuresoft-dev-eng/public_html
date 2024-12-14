<?php
session_start();
include_once('./adminsidebar.php');
include_once('db_conn2.php');

// log user activities
function logUserActivity($conn, $user_id, $activity_type, $activity_details) {
    $sql = "INSERT INTO activity_logs (user_id, activity_type, activity_details) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $user_id, $activity_type, $activity_details);
    $stmt->execute();
    $stmt->close();
}

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to perform this action.";
    exit;
}
$user_id = $_SESSION['user_id']; 


$resident_id = isset($_GET['resident_id']) ? $_GET['resident_id'] : null;
if ($resident_id) {
    $sql = "SELECT r.*, c1.category_value AS sex, c2.category_value AS civil_status, 
                        c3.category_value AS socioeconomic_category, c4.category_value AS health_status, 
                        a.category_value AS account_status
            FROM residents r
            LEFT JOIN categories c1 ON r.sex_id = c1.category_id
            LEFT JOIN categories c2 ON r.civil_status_id = c2.category_id
            LEFT JOIN categories c3 ON r.socioeconomic_category_id = c3.category_id
            LEFT JOIN categories c4 ON r.health_status_id = c4.category_id
            LEFT JOIN categories a ON r.account_status_id = a.category_id
            WHERE r.resident_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $resident_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $resident = $result->fetch_assoc();
    $stmt->close();

    $account_status = isset($resident['account_status']) ? $resident['account_status'] : 'Unknown';

    if (!$resident) {
    echo "<p>Resident not found.</p>";
    exit;
}
} else {
    echo "Resident ID not provided.";
    exit;
}
$button_label = $account_status === 'Active' ? 'DEACTIVATE' : 'REACTIVATE';
$button_action = $account_status === 'Active' ? 'deactivate' : 'reactivate';

$errors = [];

//  update resident
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_resident'])) {
    $first_name = ucfirst(strtolower($_POST['first_name']));
    $middle_name = ucfirst(strtolower($_POST['middle_name']));
    $last_name = ucfirst(strtolower($_POST['last_name']));
    $suffix = ucfirst(strtolower($_POST['suffix']));
    $sex = $_POST['sex'];
    $date_of_birth = $_POST['date_of_birth'];
    $mobile_number = $_POST['mobile_number'];
    $email_address = $_POST['email_address'];
    $civil_status = $_POST['civil_status'];
    $socioeconomic_category = $_POST['socioeconomic_category'];
    $health_status = $_POST['health_status'];
    $house_lot_number = ucfirst(strtolower($_POST['house_lot_number']));
    $street_subdivision_name = ucfirst(strtolower($_POST['street_subdivision_name']));
    $barangay = ucfirst(strtolower($_POST['barangay']));
    $municipality = ucfirst(strtolower($_POST['municipality']));
    $profile_photo_path = $resident['profile_photo_path']; 

    // Mobile number validation
    if (empty($mobile_number) || !preg_match('/^09\d{9}$/', $mobile_number)) {
        $errors[] = "Invalid mobile number. It must start with '09' and contain 11 digits.";
    }

    // Email address validation
    if (empty($email_address) || !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email_address)) {
        $errors[] = "Invalid email address. It must end with '@gmail.com'.";
    }

    // Age validation
    if (empty($date_of_birth)) {
        $errors[] = "Date of birth is required.";
    } else {
        $dob = new DateTime($date_of_birth);
        $today = new DateTime();
        $age = $today->diff($dob)->y;
        if ($age < 18) {
            $errors[] = "Invalid age. Resident must be at least 18 years old.";
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>{$error}</p>";
        }
        exit;
    }

    // photo upload
    if (!empty($_FILES['profile_photo']['name'])) {
        $photo_name = $_FILES['profile_photo']['name'];
        $target_dir = "./uploads/";
        $profile_photo_path = $target_dir . basename($photo_name);
        if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $profile_photo_path)) {
            echo "Error uploading profile photo.";
        }
    }

 
$category_values = [
    'sex' => [],
    'civil_status' => [],
    'socioeconomic_category' => [],
    'health_status' => []
];

$sql = "SELECT category_id, category_value, category_type FROM categories WHERE category_type IN ('sex', 'civil_status', 'socioeconomic_category', 'health_status')";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $category_values[$row['category_type']][$row['category_id']] = $row['category_value'];
}

$changes = [];
$old_values = [
    'first_name' => $resident['first_name'],
    'middle_name' => $resident['middle_name'],
    'last_name' => $resident['last_name'],
    'suffix' => $resident['suffix'],
    'sex' => $resident['sex'],  
    'date_of_birth' => $resident['date_of_birth'],
    'mobile_number' => $resident['mobile_number'],
    'email_address' => $resident['email_address'],
    'civil_status' => $resident['civil_status'],
    'socioeconomic_category' => $resident['socioeconomic_category'],
    'health_status' => $resident['health_status'],
    'house_lot_number' => $resident['house_lot_number'],
    'street_subdivision_name' => $resident['street_subdivision_name'],
    'barangay' => $resident['barangay'],
    'municipality' => $resident['municipality']
];

foreach ($old_values as $key => $old_value) {
    $new_value = isset($_POST[$key]) ? $_POST[$key] : '';
    if (empty($new_value) && empty($old_value)) {
        continue;
    }
    if ($old_value == $new_value) {
        continue;  
    }
    if (in_array($key, ['sex', 'civil_status', 'socioeconomic_category', 'health_status'])) {
        $old_category_value = $category_values[$key][$old_value] ?? $old_value;
        $new_category_value = $category_values[$key][$new_value] ?? $new_value;

        // Only log changes 
        if ($old_category_value != $new_category_value) {
            $field_label = ucfirst(str_replace('_', ' ', $key));  
            $changes[] = "Changed {$field_label} from {$old_category_value} to {$new_category_value}";
        }
    } else {
        if ($old_value != $new_value) {
            $field_label = ucfirst(str_replace('_', ' ', $key));  
            $changes[] = "Changed {$field_label} from {$old_value} to {$new_value}";
        }
    }
}

$activity_details = "Updated resident details for Resident ID {$resident_id}. Name: {$resident['first_name']} {$resident['last_name']}. " . implode(", ", $changes);

if (!empty($changes)) {
    $sql = "UPDATE residents 
            SET first_name = ?, 
                middle_name = ?, 
                last_name = ?, 
                suffix = ?, 
                sex_id = ?, 
                date_of_birth = ?, 
                mobile_number = ?, 
                email_address = ?, 
                civil_status_id = ?, 
                socioeconomic_category_id = ?, 
                health_status_id = ?, 
                house_lot_number = ?, 
                street_subdivision_name = ?, 
                barangay = ?, 
                municipality = ?, 
                profile_photo_path = ?
            WHERE resident_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssissssssssssss", 
        $_POST['first_name'], $_POST['middle_name'], $_POST['last_name'], $_POST['suffix'], $_POST['sex'], $_POST['date_of_birth'],
        $_POST['mobile_number'], $_POST['email_address'], $_POST['civil_status'], $_POST['socioeconomic_category'], $_POST['health_status'],
        $_POST['house_lot_number'], $_POST['street_subdivision_name'], $_POST['barangay'], $_POST['municipality'],
        $_POST['profile_photo'], $resident_id 
    );

    if ($stmt->execute()) {
         // Log the update action
         $activity_type = "Updated Resident Info";
         logUserActivity($conn, $user_id, $activity_type, $activity_details);


        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showSuccessUpdateModal();
                });
              </script>";
    } else {
        echo "Error updating record: " . $stmt->error;
    }    
    $stmt->close();
}
}

$sexQuery = "SELECT category_id, category_value FROM categories WHERE category_type = 'sex'";
$sexResult = $conn->query($sexQuery);

$civilStatusQuery = "SELECT category_id, category_value FROM categories WHERE category_type = 'civil_status'";
$civilStatusResult = $conn->query($civilStatusQuery);

$socioeconomicCategoryQuery = "SELECT category_id, category_value FROM categories WHERE category_type = 'socioeconomic_category'";
$socioeconomicCategoryResult = $conn->query($socioeconomicCategoryQuery);

$healthStatusQuery = "SELECT category_id, category_value FROM categories WHERE category_type = 'health_status'";
$healthStatusResult = $conn->query($healthStatusQuery);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    if ($_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = './uploads/'; 
        $fileName = basename($_FILES['profile_photo']['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetPath)) {
            $query = "UPDATE residents SET profile_photo_path = ? WHERE resident_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $targetPath, $resident_id);
            $stmt->execute();
            $resident['profile_photo_path'] = $targetPath;
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_resident'])) {
    $sql = "DELETE FROM residents WHERE resident_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $resident_id);
    if ($stmt->execute()) {
         // Log the deletion action
         $activity_type = "Deleted Resident";
         $activity_details = "Deleted Resident ID {$resident_id}. Name: {$resident['first_name']} {$resident['last_name']}.";
         logUserActivity($conn, $user_id, $activity_type, $activity_details);
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showSuccessDeleteModal(); 
        });
    </script>";
    } else {
        echo "<script>alert('Error deleting record: {$stmt->error}');</script>";
    }
    $stmt->close();
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_account_status'])) {
    $action = $_POST['update_account_status'];
    $resident_id = $_POST['resident_id'];   
    $new_status_id = ($action === 'deactivate') ? 2 : 1; 
    $sql = "UPDATE residents SET account_status_id = ? WHERE resident_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $new_status_id, $resident_id);
    if ($stmt->execute()) {
          // Log the account status change
          $activity_type = $new_status_id === 1 ? "Activated Resident Account" : "Deactivated Resident Account";
          $activity_details = "Changed account status for Resident ID {$resident_id} to " . ($new_status_id === 1 ? 'Active' : 'Inactive');
          logUserActivity($conn, $user_id, $activity_type, $activity_details);
          
        echo "<script>
            alert('Account status successfully updated.');
            window.location.href = 'viewresident.php?resident_id={$resident_id}';
        </script>";
    } else {
        echo "<script>alert('Error updating account status: {$stmt->error}');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resident Details</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Symbols+Rounded">
    <style>
        .main-content {
            margin-left: 200px;
            padding: 20px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            background-color: white;
            color: white;
            padding: 10px;
            border-radius: 8px;
            gap: 15px;
        }
        .back-button {
            background-color: #0073AC;
            color: white;
            padding: 8px  20px;
            border-radius: 15%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .profile-container {
            padding: 20px;
            border-radius: 8px;
            display: flex;
            flex-direction: row-reverse; 
            gap: 30px;
        }
        .title-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #4597C0;
            padding: 10px;
            border-radius: 8px;
            color: white;
        }
        .title-container h3 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .upload-photo-button {
            background-color: #4597C0;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .profile-info, .profile-photo {
            flex: 1;
            color:#02476A;
            font-size: 17px;
        }
        .profile-photo {
            text-align: center;
            width: 250px;
            height: 200px;
            overflow: hidden;
            border: 2px solid #02476A;
            cursor: pointer;
            display: inline-block;
        }
        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .resident-id-box {
            text-align: center;
            margin-top: 10px;
        }
        .resident-id-box input {
            width: 150px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px;
            font-size: 17px;
            color: #525252;        
        }
        .resident-id-box p {
            font-size: 17px;
            color: ##000000;
            margin-top: 5px;
            font-weight: bold;
        }
        .info-group {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
        }
        .info-item label {
            font-size: 14px;
            font-weight: bold;
            color: black;
        }
        .info-item input {
            width: 100%;
            padding: 8px;
            border: 1px solid #02476A;
            border-radius: 4px;
            font-size: 14px;
        }  
        hr {
            border: 1px solid #e0e0e0;
            margin: 20px 0;
        }
        .info-group select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #fff;
            border: 1px solid #9DB6C1;
            border-radius: 4px;
            padding: 8px;
            font-size: 14px;
            color: #3C5364;
            width: 100%;
            max-width: 100%;
            cursor: pointer;
            padding-right: 24px; 
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="%239DB6C1"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 16px;
        }
.info-group select:disabled {
    color: #9DB6C1;
    background-color: #f8f9fa;
    cursor: not-allowed;
}
.info-item button {
        color: white;
        background-color: #4597C0;
        border: none; 
        cursor: pointer; 
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        color: white;
        cursor: pointer;
    }

    .info-item button:hover {
        text-decoration: none; 
    } 

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 100;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}
.modal-content {
    margin: 15% auto;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 500px;
    text-align: center;
    font-family: Arial, sans-serif;
}
.modal-content p {
    font-size: 16px;
    margin-bottom: 20px;
}
.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin: 5px;
}
.btn-yes {
    background-color: #4597C0;
    color: #fff;
}
.btn-no {
    background-color: #EA3323;
    color: #000;
}
.alert-box {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    padding: 15px 20px;
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    border-radius: 5px;
    font-size: 16px;
    text-align: center;
    width: 20%;
    z-index: 1000;
    opacity: 1;
    transition: opacity 2s ease-out;
}

.alert-box.fade-out {
    opacity: 0;
}
 </style>
<script>
    function uploadPhoto() {
        const fileInput = document.getElementById('profile_photo');
        fileInput.click(); 
        fileInput.onchange = function() {
            if (fileInput.files.length > 0) {
                document.getElementById('photoUploadForm').submit(); 
            }
        };
    }
function enableEdit() {
    const accountStatus = document.getElementById('account_status').value;
    if (accountStatus === 'Active') {
        document.querySelectorAll('.info-item input, .info-item select').forEach(input => {
            if (!['barangay', 'municipality', 'account_status'].includes(input.name)) {
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
                input.style.backgroundColor = '#ffffff';
            }
        });
        document.getElementById('editButton').style.display = 'none';
        document.getElementById('updateButton').style.display = 'inline-block';
    } else {
        const alertBox = document.createElement('div');
        alertBox.textContent = 'Editing is not allowed for deactivated accounts.';
        alertBox.className = 'alert-box';
        document.body.appendChild(alertBox);

        setTimeout(() => alertBox.classList.add('fade-out'), 2000); 
        setTimeout(() => alertBox.remove(), 4000); 
    }
}


function showUpdateModal() {
    document.getElementById('confirmationModal').style.display = 'block';
}

function closeUpdateModal() {
    document.getElementById('confirmationModal').style.display = 'none';
}

function showSuccessUpdateModal() {
    const modal = document.getElementById('successUpdateModal');
    modal.style.display = 'flex';
}

function confirmUpdate(event) {
    event.preventDefault();  
    document.getElementById('confirmationModal').style.display = 'none'; 
    document.getElementById('residentUpdateForm').submit();  
}

    function showDeleteModal() {
        document.getElementById('deleteModal').style.display = 'block';
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }
    function showSuccessDeleteModal() {
        const modal = document.getElementById('successdeleteModal');
        modal.style.display = 'flex';
    }

            document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('input, select').forEach(input => {
                input.addEventListener('input', function() {
                    document.getElementById('updateButton').disabled = false; 
                    document.getElementById('updateButton').style.display = 'inline-block'; 
                });
            });
            document.getElementById('residentUpdateForm').addEventListener('submit', function(event) {
                let formChanged = false;

                document.querySelectorAll('input, select').forEach(input => {
                    if (input.value !== input.defaultValue) {
                        formChanged = true;
                    }
                });
                if (!formChanged) {
                    event.preventDefault();
                    alert("No changes were made.");
                }
            });
        });

</script>

</head>
<body>
<div class="container">
    <div class="header">
    <a href="residents_list.php" class="back-button">
    <span class="material-symbols-rounded">arrow_back</span>
</a>
        <h2>RESIDENT DETAILS</h2>
    </div>
    <hr>

    <div class="title-container">
        <h3>PROFILE</h3>
        <button type="button" class="upload-photo-button"  onclick="uploadPhoto()">
            <span class="material-symbols-rounded">file_upload</span> UPLOAD A PHOTO
        </button>
    </div>

    <div class="profile-container">
    <form id="photoUploadForm" method="post" enctype="multipart/form-data" style="display: inline;">
            <input type="file" name="profile_photo" id="profile_photo" accept="image/*" style="display: none;">
           
            <div class="profile-photo">
                <img src="<?php echo htmlspecialchars($resident['profile_photo_path']); ?>" alt="Resident Photo">
            </div>
           
            <div class="resident-id-box">
            <input type="text" value="<?php echo htmlspecialchars($resident['resident_id']); ?>" readonly>
            <p>Resident ID</p>
        </div>
</form>
<div class="profile-info">
    <h3>Personal Information</h3>

    <?php if (!empty($errorMessage)): ?>
            <div style="color: red; font-weight: bold; margin-bottom: 20px;">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?> 
    <form method="POST" id="residentUpdateForm">
    <div class="info-group">
            <!-- First row -->
            <div class="info-item">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($resident['first_name']); ?>" readonly style="background-color: #F5F5F5;">
            </div>
            <div class="info-item">
                <label>Middle Name (Optional)</label>
                <input type="text" name="middle_name" value="<?php echo htmlspecialchars($resident['middle_name']); ?>" readonly style="background-color: #F5F5F5;">
            </div>
            <div class="info-item">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($resident['last_name']); ?>" readonly style="background-color: #F5F5F5;">
            </div>
            <div class="info-item">
                <label>Suffix (Optional)</label>
                <input type="text" name="suffix" value="<?php echo htmlspecialchars($resident['suffix']); ?>" readonly style="background-color: #F5F5F5;">
            </div>
            <!-- Second row -->
            <div class="info-item">
                <label for="sex">Sex:</label>
                <div class="radio-group">
                    <?php while ($row = mysqli_fetch_assoc($sexResult)) : ?>
                        <label class="radio-option">
                            <input type="radio" name="sex" value="<?php echo htmlspecialchars($row['category_id']); ?>"
                                <?php echo $resident['sex_id'] == $row['category_id'] ? 'checked' : ''; ?> disabled>
                            <?php echo htmlspecialchars($row['category_value']); ?>
                        </label>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="info-item">
                <label>Birthday</label>
                <input type="date" name="date_of_birth" value="<?php echo htmlspecialchars($resident['date_of_birth']); ?>" readonly style="background-color: #F5F5F5;">
            </div>
            <div class="info-item">
                <label>Mobile Number</label>
                <input type="text" name="mobile_number" value="<?php echo htmlspecialchars($resident['mobile_number']); ?>" readonly style="background-color: #F5F5F5;">
            </div>
            <div class="info-item">
                <label>Email Address</label>
                <input type="text" name="email_address" value="<?php echo htmlspecialchars($resident['email_address']); ?>" readonly style="background-color: #F5F5F5;">
            </div>
            <!-- Third row -->
            <div class="info-item">
                <label for="civil_status">Civil Status:</label>
                <select name="civil_status" disabled>
                    <?php while ($row = mysqli_fetch_assoc($civilStatusResult)) : ?>
                        <option value="<?php echo htmlspecialchars($row['category_id']); ?>"
                            <?php echo $resident['civil_status_id'] == $row['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['category_value']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="info-item">
                <label for="socioeconomic_category">Socioeconomic Category</label>
                <select name="socioeconomic_category" disabled>
                    <?php while ($row = mysqli_fetch_assoc($socioeconomicCategoryResult)) : ?>
                        <option value="<?php echo htmlspecialchars($row['category_id']); ?>"
                            <?php echo $resident['socioeconomic_category_id'] == $row['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['category_value']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="info-item">
                <label for="health_status">Health Status</label>
                <select name="health_status" disabled style="background-color: #F5F5F5;">
                    <?php while ($row = mysqli_fetch_assoc($healthStatusResult)) : ?>
                        <option value="<?php echo htmlspecialchars($row['category_id']); ?>"
                            <?php echo $resident['health_status_id'] == $row['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['category_value']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <!-- Fourth row -->
            <div class="info-item">
                <label>House/Lot Number</label>
                <input type="text" name="house_lot_number" value="<?php echo htmlspecialchars($resident['house_lot_number']); ?>" readonly style="background-color: #F5F5F5;">
            </div>
            <div class="info-item">
                <label>Street/Subdivision Name</label>
                <input type="text" name="street_subdivision_name" value="<?php echo htmlspecialchars($resident['street_subdivision_name']); ?>" readonly style="background-color: #F5F5F5;">
            </div>
            <div class="info-item">
                <label>Barangay</label>
                <input type="text" name="barangay" value="<?php echo htmlspecialchars($resident['barangay']); ?>" readonly style="background-color: #C5C5C5;">
            </div>
            <div class="info-item">
                <label>Municipality</label>
                <input type="text" name="municipality" value="<?php echo htmlspecialchars($resident['municipality']); ?>" readonly style="background-color: #C5C5C5;">
            </div>
              
<hr>

</div>
                <button type="button" id="editButton" class="btn" style="background-color: #4597C0; color: white;" onclick="enableEdit()">EDIT</button>
                <button type="submit" id="updateButton" name="update_resident" class="btn" style="display:none; background-color: #4597C0; color: white;" disabled>UPDATE</button>
            </form>


<!-- Account Status -->
<div class="info-item">
    <label for="account_status">Account Status: </label> <br>
    <input type="text" id="account_status" name="account_status" value="<?php echo htmlspecialchars($account_status); ?>" readonly style="background-color: #C5C5C5; border: none; margin-bottom: 10px; width: 20%; " >
    <form method="POST" style="display:inline;">
        <input type="hidden" name="resident_id" value="<?php echo htmlspecialchars($resident_id); ?>">
        <button type="submit" name="update_account_status" value="<?php echo $button_action; ?>">
            <?php echo $button_label; ?>
        </button>
    </form>
</div>
<br>

<div class="info-item">
<button type="button" id="deleteButton" onclick="showDeleteModal()" style="background-color: #EA3323; color: white;">DELETE</button>
</div>

<!-- Confirmation Modal (Update) -->
<div id="confirmationModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h2>Confirm Update</h2>
            <p>Are you sure you want to update this resident's details?</p>
            <button class="btn btn-yes" onclick="confirmUpdate(event)">Yes</button>
            <button class="btn btn-no" onclick="closeUpdateModal()">No</button>
        </div>
    </div>


<!-- Success Modal (Update) -->
<div id="successUpdateModal" class="modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <span class="material-symbols-rounded" style="color:green; ">check_circle</span>Resident Updated Successfully
            </div>
            <p>Resident details updated successfully!</p>
            <div class="modal-buttons">
                <a href='viewresident.php?resident_id=<?php echo htmlspecialchars($resident_id); ?>' class="btn btn-yes">OK</a>
            </div>
        </div>
    </div>





<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <p>Are you sure you want to delete this resident?</p>
        <form method="POST">
            <button type="button" onclick="closeDeleteModal()" class="btn btn-no">No</button>
            <button type="submit" name="delete_resident" class="btn btn-yes">Yes</button>
        </form>
    </div>
</div>
<!-- Success Modal (Delete) -->
<div id="successdeleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <span class="material-symbols-rounded" style="color: red;">check_circle</span>Resident Deleted Successfully
        </div>
        <p>The resident profile has been deleted.</p>
        <div class="modal-buttons">
        <a href="residents_list.php" class="btn btn-yes">OK</a>
        </div>
    </div>
</div>
</form>
        </div>
    </div>
</div>
</body>
</html>
