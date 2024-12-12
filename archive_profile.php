
<?php
session_start();
include('auth_check.php'); 
include 'update_user.php'; 
include 'db_conn2.php';
include 'adminsidebar-accountservices.php';

// Fetch the user from the database based on some identifier (e.g., user_id)
$user_id = $_GET['user_id']; 

$sql = "SELECT * FROM archive_accounts WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if a user is found
if ($result->num_rows > 0) {
    // Fetch the user data into the $archive_accounts variable
    $archive_accounts = $result->fetch_assoc();
} else {
    // Handle the case where the user is not found
    echo "User not found!";
    exit;
}

// Don't forget to close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    
    <style>
        .container { 
            max-width: 100%; 
            height: 130vh !important;
            margin: 0 0px 0px 160px; 
            padding: 20px;
            font-family: Poppins; 
            background-color: #FFFFFF !important; 
        }
     
        .title-container h3 {
            width: 1200px;
            height: 50px;
            background-color: #4597C0;
            color: #FFFFFF;
            font-size: 20px;
            margin: 100px 0px 0px 30px;
            position: a-zA-Z0-9;
            text-transform: uppercase;
            padding: 10px;
            border-radius: 5px;

        }

        .back-button {
            background-color: #0073AC;
            color: white;
            padding: 8px 20px; 
            border-radius: 9%;
            display: flex; 
            align-items: center; 
            justify-content: center;
            cursor: pointer;
            text-decoration: none; 
            z-index: 100;
            margin: 37px 0px 0px 240px;
            position: absolute;
        }   

        .profile-container { 
            padding: 20px; 
            border-radius: 8px;
            display: flex; 
            flex-direction: row-reverse; 
            gap: 30px; 

        }
        
        .profile-info {
            width: 70%;
            margin: -20px 0px 0px -260px !important;
            position: absolute;

        }

        #createUserForm {
            margin: -35px 0px 0px 850px !important;
            position: absolute;
            width: auto;
            height: auto;
            background-color: transparent;
            font-size: 12px;
        }

        .info-group {
            margin: 0px 0px 0px 300px;
            position: absolute;
            width: 95%;
        }
        
        .info-item-id  {
            width: 130px;
            margin: 50px 0px 0px 955px;
            position: absolute;
            font-size: 14px;
        }

        .info-item-id input  {
            width: 140px;
            height: 35px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin: 80px 0px 0px 90px;
            position: absolute;
            font-size: 14px;
        }

        #userID-title {
            width: 100%;
            margin: 125px 0px 0px 140px;
            position: absolute;
            font-size: 15px;
            color: black;
        }

        #address-title {
            position: absolute;
            margin-top: -10px;
        }

        #address {
            margin: 25px 0px 0px -0px;
            width: 100%; 
            padding: 0px; 
        }

        .profile-info h3 {
            margin: 0px 0px 0px 300px;
            font-size: 20px;
            width: auto;

        }

        #position {
            margin: 40px 0px 0px -0px;
        }

        .profile-info hr {
            width: 100%;
        }

        #createUserButton {
            font-size: 18px;
            width: 17%;
            height: 9%;
            padding: 8px auto;
            border-radius: 5px;
            color: #FFFF;
            background-color: #59C447;
            text-transform: uppercase;
            margin: 420px 0px 0px 500px;
            position: absolute;
        }

        #profile_photo {
            display: none;
        } 

        #profilePhotoPreview {
            text-align: center; 
            cursor: pointer;
            width: 140px; 
            height: 140px; 
            border: 2px solid #02476A; 
            overflow: hidden; 
            display: inline-block; 
            margin: 40px 0px 0px 1085px;
            position: absolute;
        }

        .profile-info, .profile-photo { 
            flex: 1; 
            color: #02476A; 
            font-size: 17px; 
        }

        #personal-info-title {
            margin-top: -50px;
            width: 100%;
        }

        .info-group { 
            display: grid; 
            grid-template-columns: repeat(4, 1fr); 
            gap: 40px; 
        }

        .info-item label { 
            font-size: 12px; 
            font-weight: bold; 
            color: black;  
            margin-right: 50px; 
            width: 100%;
        }

        .info-item input, .info-group select {
             width: 100%; 
             padding: 8px; 
             border: 1px solid #02476A; 
             border-radius: 4px; 
             font-size: 12px; 
             position: relative;
        } 
        
        .info-item p {
            font-size: 20px;
        }

        .sex-option {
            display: flex; 
            align-items: center;
            font-size: 14px; 
            color: black; 
            margin: 0px 60px 0px 0px;
           
        }

        .sex-option input[type="radio"] {
            margin: 0px 0px 0px 0px; 
            position: absolute;
           
        }
        
        #view-activity-btn {
            font-size: 14px;
            width: 17%;
            height: 5%;
            padding: 8px auto;
            border: none;
            border-radius: 5px;
            color: #FFFF;
            background-color: #0073AC;
            text-transform: uppercase;
            margin: 40px 0px 0px 1150px;
            position: absolute;

        }

        #profilePhotoInput {
            display: none;
        }

        #job {
            margin-top: 25px;
            position: relative;
        }

        #personal {
            margin-top: -20px;
            position: relative;
        }

        #brgy {
            margin-top: 15px;
            position: relative;
        }

        .readonly-field {
            background-color: #f0f0f0;
            color: #333;
            cursor: not-allowed;
        }

        #statuslbl {
            font-size: 20px;
            color: #02476A;
            margin: 400px 0px 0px 30px;
            
        }
         
        #acc-status {
            font-size: 14px;
            width: 13.5%;
            height: 5%;
            padding: 8px 15px;
            border: 1px solid black;
            border-radius: 5px;
            color: black;
            text-transform: uppercase;
            margin: 435px 0px 0px -60px;
            position: absolute;

        }

        #resident-title {
            font-size: 20px;
            font-weight: bold;
            margin: 40px 0px 0px 320px;
            position: absolute;
            text-transform: uppercase;
        }
        
    </style>
</head>

<body>
    <div class="header">
        <p id="resident-title"> User's Details </p>
    <a href="archive_account.php" class="back-button">
<span class="material-symbols-rounded">arrow_back</span>
</a>    
<hr style="color: #ccc; width: 90%; position: absolute; margin: 90px 0px 0px -20px;">
    <button type="button" id="back-button" onclick="window.location.href='add_user.php';"><-</button>
    <button id="view-activity-btn">VIEW ACTIVITY LOG</button>
    </div>
 

    <div class="container">
    <div class="title-container">
        <h3>PROFILE</h3>
   <form method="POST" enctype="multipart/form-data" action="update_user.php?user_id=<?= $archive_accounts['user_id'] ?>">
    </div>

    <?php if (!empty($archive_accounts['profile_photo'])): ?>
        <img src="<?= htmlspecialchars($archive_accounts['profile_photo']) ?>" id="profilePhotoPreview" alt="Profile Photo" width="150" height="150">
    <?php else: ?>
        <p>No profile photo available.</p>
    <?php endif; ?>
<br>
<input type="file" name="profile_photo" id="profilePhotoInput" onchange="previewImage(event)"><br><br>


<div class="profile-info">
    <br>
        <div class="info-group">
        <div class="info-item-id">
    <label id="userID-title">User ID</label>
    <input type="text" value="<?= htmlspecialchars($archive_accounts['user_id']) ?>" readonly class="readonly-field"><br>
    <br>
    </div> 

    <div class="info-item" id="personal">
    <p id="personal-info-title" style="margin: -30px 0px 5px -3px;">Personal Information</p>
    <label>First Name:</label>
    <input type="text" name="first_name" oninput="capitalizeInput(event)" value="<?= htmlspecialchars($archive_accounts['first_name']) ?>" readonly class="readonly-field"><br>
    </div>

    <div class="info-item" id="personal">
    <label>Middle Name (Optional)</label>
    <input type="text" name="middle_name" oninput="capitalizeInput(event)" value="<?= htmlspecialchars($archive_accounts['middle_name']) ?>" readonly class="readonly-field"><br>
    </div>

    <div class="info-item" id="personal">
    <label>Last Name</label>
    <input type="text" name="last_name" oninput="capitalizeInput(event)" value="<?= htmlspecialchars($archive_accounts['last_name']) ?>" readonly class="readonly-field"><br>
    </div>

    <div class="info-item" id="personal">
    <label>Suffix (Optional)</label>
    <input type="text" name="suffix" oninput="capitalizeInput(event)" value="<?= htmlspecialchars($archive_accounts['suffix']) ?>" readonly class="readonly-field"><br>
    </div>

    <div class="info-item" id="personal">
    <label>Contact No</label>
    <input type="text" name="contact_no" value="<?= htmlspecialchars($archive_accounts['contact_no']) ?>" id="contactNo" maxlength="11" oninput="validateContactNumber()" readonly class="readonly-field"><br>
    </div>

    <div class="info-item" style="margin-top: -20px;" readonly class="readonly-field">
    <label>Sex</label><br>
    <div class="sex-option">
    <input type="radio" disabled name="sex" id="male-sex" value="Male" 
       style="margin: -0px 0px 0px -485px; position: absolute;" 
       <?= ($archive_accounts['sex'] === 'Male') ? 'checked' : '' ?>>
<label for="male-sex" style="margin-left: 40px; font-weight: 500; font-size: 14px;">Male</label><br>


    <input type="radio" disabled name="sex" id="female-sex" value="Female" 
       style="margin: 0px 0px 0px -402px; position: absolute;" 
       <?= ($archive_accounts['sex'] === 'Female') ? 'checked' : '' ?> readonly class="readonly-field">
<label for="female-sex" style="margin: -0px 0px 0px 120px; font-weight: 500; font-size: 14px; position: absolute;">Female</label><br><br>
</div>
    </div>

    <div class="info-item" id="personal">
    <label>Birthdate</label>
    <input type="date" name="birthdate" value="<?= htmlspecialchars($archive_accounts['birthdate']) ?>" readonly class="readonly-field"><br>
    </div>

    <div class="info-item" id="personal">
    <label>Email</label>
    <input type="text" name="email" value="<?= htmlspecialchars($archive_accounts['email']) ?>" readonly class="readonly-field"><br>
    </div>

    <div class="info-item" id="brgy" style="margin-top: -10px;">
    <p id="address-title">Address</p>
    <br>
    <label>City</label>
    <input type="text" name="city" value="<?= htmlspecialchars($archive_accounts['city']) ?>" readonly class="readonly-field"><br>
    </div>

    <div class="info-item" id="brgy">
    <label>Barangay</label>
    <input type="text" name="barangay" value="<?= htmlspecialchars($archive_accounts['barangay']) ?>" readonly class="readonly-field"><br>
    </div>

    <div class="info-item" id="brgy">
    <label>House/Lot Number</label>
    <input type="text" name="house_lot_number" oninput="capitalizeInput(event)" value="<?= htmlspecialchars($archive_accounts['house_lot_number']) ?>" readonly class="readonly-field"><br>
    </div>

    <div class="info-item" id="brgy">
    <label>Street/Subdivision Name</label>
    <input type="text" name="street_subdivision_name" oninput="capitalizeInput(event)" value="<?= htmlspecialchars($archive_accounts['street_subdivision_name']) ?>" readonly class="readonly-field"><br>
    </div>

    <div class="info-item">
    <p style="margin: -5px 0px 0px -3px;">Job Description</p>
    <label>Role</label>
    <select name="role" disabled style="background-color: #f0f0f0;">
        <option value="Admin" <?= ($archive_accounts['role'] === 'Admin') ? 'selected' : '' ?>>Admin</option>
        <option value="Local Authority" <?= ($archive_accounts['role'] === 'Local Authority') ? 'selected' : '' ?>>Local Authority</option>
    </select><br>
    </div>

    <div class="info-item" id="job">
    <label>Position</label>
    <select name="position" disabled style="background-color: #f0f0f0;">
        <option value="Executive Officer" <?= ($archive_accounts['position'] === 'Executive Officer') ? 'selected' : '' ?>>Executive Officer</option>
    </select><br>
    </div>

    <div class="info-item" id="job">
    <label>Work Day Schedule</label>
    <input type="text" name="schedule" value="<?= htmlspecialchars($archive_accounts['schedule']) ?>" readonly class="readonly-field"><br>
    </div>

    <div class="info-item" id="job">
    <label>Work Time Schedule</label>
    <input type="text" name="shift" value="<?= htmlspecialchars($archive_accounts['shift']) ?>" readonly class="readonly-field"><br>
    </div>
</form>
 </div>
 </div>



<script> // This script is for deactivating adn reactivating the account
document.getElementById('deactivateButton')?.addEventListener('click', function () {
    showModal('deactivateModal');
});

document.getElementById('reactivateButton')?.addEventListener('click', function () {
    showModal('reactivateModal');
});

document.getElementById('unlockButton')?.addEventListener('click', function () {
    showModal('unlockModal');
});

function showModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function confirmStatusChange(action) {
    var form = document.getElementById('statusForm');
    var actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = action;
    form.appendChild(actionInput);
    form.submit();
}

</script>



<script>//This script is for the archive_account.php
    const archiveButton = document.getElementById('archiveButton');
    archiveButton.addEventListener('click', function () {
        document.getElementById('archiveModal').style.display = 'flex';
    });

    function confirmArchive() {
        const userId = archiveButton.value;
        let form = document.createElement("form");
        form.method = "POST";
        form.action = "archive_account.php";

        let input = document.createElement("input");
        input.type = "hidden";
        input.name = "user_id";
        input.value = userId;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
        closeModal('archiveModal');
    }
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
</script>

<script>// This script is for the capitalization of the inputs
        function capitalizeInput(event) {
            let input = event.target;
            let value = input.value.toLowerCase().replace(/\b\w/g, (char) => char.toUpperCase());
            input.value = value;
        }
        function capitalizePlaceholder() {
            let inputs = document.querySelectorAll('input[placeholder]');
            inputs.forEach(input => {
                let placeholder = input.getAttribute('placeholder');
                input.setAttribute('placeholder', placeholder.charAt(0).toUpperCase() + placeholder.slice(1));
            });
        }
        window.onload = capitalizePlaceholder;
    </script>

    <script>
    // Preview image before uploading
    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('profilePhotoPreview');

        // Check if a file is selected
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result; 
            };
            reader.readAsDataURL(file); 
        } else {
            preview.src = "default_image.jpg";  
        }
    }
</script>

</body>
</html>
