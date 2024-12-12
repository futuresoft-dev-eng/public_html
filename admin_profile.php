<?php
session_start();
include 'db_conn2.php';
include 'adminsidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $contact_no = $_POST['contact_no'];
    $email = $_POST['email'];
    $house_lot_number = $_POST['house_lot_number'];
    $street_subdivision_name = $_POST['street_subdivision_name'];

    $contact_no = $conn->real_escape_string($contact_no);
    $email = $conn->real_escape_string($email);
    $house_lot_number = $conn->real_escape_string($house_lot_number);
    $street_subdivision_name = $conn->real_escape_string($street_subdivision_name);

    
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE contact_no = ? AND user_id != ?");
    $stmt->bind_param("si", $contact_no, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "This contact number is already registered.";
        $stmt->close();
        exit();
    }
    $stmt->close();

    
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "This email address is already registered.";
        $stmt->close();
        exit();
    }
    $stmt->close();

    
    $stmt = $conn->prepare("
        UPDATE users 
        SET 
            contact_no = ?, 
            email = ?, 
            house_lot_number = ?, 
            street_subdivision_name = ? 
        WHERE user_id = ?
    ");
    $stmt->bind_param("ssssi", $contact_no, $email, $house_lot_number, $street_subdivision_name, $user_id);

    if ($stmt->execute()) {
        header("Location: admin_profile.php?status=success");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$stmt = $conn->prepare("
    SELECT 
        user_id, first_name, middle_name, last_name, suffix, sex, birthdate, contact_no, 
        email, house_lot_number, street_subdivision_name, barangay, city, role, position, 
        schedule, shift, profile_photo 
    FROM users 
    WHERE user_id = ?
");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "Error: User not found.";
        exit();
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
    exit();
}

// Check if the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check for success status
$success = isset($_GET['status']) && $_GET['status'] == 'success';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>

</head>
<body>
 <?php if ($success): ?>
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Your profile has been updated successfully.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</body>

<script>
    function toggleEditMode(editMode) {
        const fields = document.querySelectorAll('.editable');
        fields.forEach(field => {
            field.readOnly = !editMode;
            field.style.backgroundColor = editMode ? 'white' : 'gray';
        });

        document.getElementById('editButton').style.display = editMode ? 'none' : 'inline';
        document.getElementById('saveButton').style.display = editMode ? 'inline' : 'none';
    }

    function showModal() {
        if (validateForm()) {
            document.getElementById('confirmModal').style.display = 'block';
        }
    }

    function closeModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }

    function validateForm() {
        let isValid = true;
        const editableFields = document.querySelectorAll('.editable');

      
        document.querySelectorAll('.error-message').forEach(e => e.textContent = '');
        editableFields.forEach(field => field.style.border = '');

       
        editableFields.forEach(field => {
            if (field.value.trim() === "") {
                field.style.border = "2px solid red";
                isValid = false;
            } else {
                field.style.border = "";
            }
        });

   
        const mobileField = document.querySelector("input[name='contact_no']");
        const mobileValue = mobileField.value.trim();
        const mobileError = document.getElementById("contactError");
        const mobileRegex = /^\d{11}$/;

        if (!mobileRegex.test(mobileValue)) {
            mobileField.style.border = "2px solid red";
            mobileError.textContent = "Contact number must be 11 digits and should start with 09.";
            isValid = false;
        } else {
            mobileField.style.border = "";
            mobileError.textContent = "";
        }

  
        const emailField = document.querySelector("input[name='email']");
        const emailValue = emailField.value.trim();
        const emailError = document.getElementById("emailError");
        const emailRegex = /^[a-zA-Z0-9._%+-ñÑ]+@gmail\.com$/;

        if (!emailRegex.test(emailValue)) {
            emailField.style.border = "2px solid red";
            emailError.textContent = "Email must end with @gmail.com";
            isValid = false;
        } else {
            emailField.style.border = "";
            emailError.textContent = "";
        }

        return isValid;
    }


    function enforceNumericInput(event) {
        const keyCode = event.keyCode || event.which;
        const keyValue = String.fromCharCode(keyCode);

        if (!/^\d$/.test(keyValue) && keyCode !== 8 && keyCode !== 46) { 
            event.preventDefault();
        }
    }

  
document.addEventListener('DOMContentLoaded', () => {
    const fields = document.querySelectorAll('.editable');

    fields.forEach(field => {
        field.addEventListener('input', () => {
            field.style.border = ''; 
            const errorElement = field.nextElementSibling;
            if (errorElement && errorElement.classList.contains('error-message')) {
                errorElement.textContent = ''
            }
        });
    });
});


    function checkDuplicates(mobileValue, emailValue) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_duplicates.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                let hasDuplicate = false;

                if (response.duplicateContact) {
                    const mobileField = document.querySelector("input[name='contact_no']");
                    const mobileError = document.getElementById("contactError");
                    mobileField.style.border = "2px solid red";
                    mobileError.textContent = "This contact number is already registered.";
                    hasDuplicate = true;
                }
                if (response.duplicateEmail) {
                    const emailField = document.querySelector("input[name='email']");
                    const emailError = document.getElementById("emailError");
                    emailField.style.border = "2px solid red";
                    emailError.textContent = "This email address is already registered.";
                    hasDuplicate = true;
                }

                if (!hasDuplicate) {
                    document.getElementById('confirmModal').style.display = 'block';
                }
            }
        };
        xhr.send(`contact_no=${mobileValue}&email=${emailValue}`);
    }

    function saveChanges() {
        document.getElementById('editForm').submit();
    }
    
       // Automatically show the modal if success
        <?php if ($success): ?>
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
        <?php endif; ?>
</script>


<style>
        .container { 
            max-width: 100%; 
            height: 130vh !important;
            margin: 0 0px 0px -120px; 
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
            margin: 80px 0px 0px 270px;
            position: a-zA-Z0-9;
            text-transform: uppercase;
            padding: 10px;
            border-radius: 5px;

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
            margin: 60px 0px 0px -20px !important;
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
            display: grid; 
            grid-template-columns: repeat(4, 1fr); 
            gap: 40px;
        }

        .info-item-id  {
            width: 130px;
            margin: 50px 0px 0px 960px;
            position: absolute;
            font-size: 14px;
        }

        .info-item-id input  {
            width: 144px;
            height: 35px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin: 80px 0px 0px 90px;
            position: absolute;
            font-size: 14px;
        }

        #userID-title {
            width: 100%;
            margin: 140px 0px 0px 120px;
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

        #uploadLabel {
            font-size: 14px; 
            color: white; 
            cursor: pointer;
            font-weight: normal;
            margin: -85px 0px 0px 1030px; 
            padding: 50px;
            position: absolute;
            min-width: 1000px !important;
        }   

        #profilePhotoPreview {
            text-align: center; 
            cursor: pointer;
            width: 140px; 
            height: 140px; 
            border: 2px solid #02476A; 
            overflow: hidden; 
            display: inline-block; 
            margin: 60px 0px 0px 1330px;
            position: absolute;
        }

        #upload-icon {
            margin: -40px 0px 0px 1040px;
            position: absolute;
            color: white;
            font-size: 30px;
        }

        .profile-info, .profile-photo { 
            flex: 1; 
            color: #02476A; 
            font-size: 17px; 
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
            margin: 40px 0px 0px 1110px;
            position: absolute;
        }

        #editButton {
            font-size: 14px;
            width: 20%;
            height: 10%;
            padding: 10px auto;
            border: none;
            border-radius: 4px;
            color: #FFFF;
            background-color: #084E71;
            text-transform: uppercase;
            margin: -128px 0px 0px 980px;
            position: absolute;
        }

        #saveButton {
            font-size: 14px;
            width: 20%;
            height: 10%;
            padding: 10px auto;
            border: none;
            border-radius: 4px;
            color: #FFFF;
            background-color: #084E71;
            text-transform: uppercase;
            margin: -128px 0px 0px 980px;
            position: absolute;

        }

        #acc-title {
            font-size: 20px;
            font-weight: bold;
            margin: 40px 0px 0px -10px;
            position: absolute;
            text-transform: uppercase;
        }

        #job {
            margin-top: 20px;
            position: relative;
        }

        #personal {
            margin-top: -10px;
            position: relative;
        }

        #brgy {
            margin-top: 0px;
            position: relative;
        }

        input[readonly] {
            background-color: lightgray;
            color: #333;
            cursor: not-allowed;
        }

        .editable {
            background-color: white;
        }

        #confirmModal {
            display: none;
            position: fixed;
            width: auto;
            height: 102%;
            overflow: auto;
            justify-content: center;
            align-items: center;
            margin-top: 300px;
            margin-left: 650px;
            position: fixed;
            background: transparent;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            border-radius: 8px !important;
            padding: 20px;
            text-align: center;
            min-width: 480px !important;
            height: 190px;
            position: relative;
            font-size: 12px;
            text-align: justify;
            border: 2px solid #ccc;
        }

        .modal-content h2 {
            width: 55%;
            font-size: 15px;
            text-align: center;
            margin: 30px 0px 0px 120px;
            position: absolute;
        }

        .question-sign {
            width: 85px;
            height: auto;
            margin: 0px 0px 0px 40px !important;
            position: absolute;
        }

        button#yesbtn {
            background-color: #EA3323;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 100px 0px 0px 100px;
            width: 130px;
            font-size: 12px;
            text-transform: uppercase;
        }

        button#nobtn {
            background-color: #4597C0;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: -35px 0px 0px 250px;
            width: 130px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .error-message {
            color: red;
            font-size: 0.9em;
        }

        .success-message {
            color: green;;
            margin-top: 20px;
        }

        .error { color: red; font-size: 12px; }
        .error-border { border: 1px solid red; }
        #confirmationModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        #confirmationModal div {
            background: white;
            padding: 20px;
            border-radius: 5px;
        }
    </style>

</style>

</head>
<body>

<div class="header">
        <p id="acc-title"> My Account </p>
<hr style="color: #ccc; width: 90%; position: absolute; margin: 90px 0px 0px -20px;">
    <button type="button" id="back-button" onclick="window.location.href='add_user.php';"></button>
    <button id="view-activity-btn">VIEW ACTIVITY LOG</button>
    </div>

    <div class="container">
    <div class="title-container">
        <h3>PROFILE</h3>
    </div>

<?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <p id="successMessage" class="success-message" style="transition: opacity 0.5s;">
        Profile information has been updated successfully.
    </p>
    <script>
        setTimeout(function () {
            const successMessage = document.getElementById('successMessage');
            successMessage.style.opacity = '0'; 
            setTimeout(function () {
                successMessage.style.display = 'none'; 
            }, 500); 
        }, 2000); 
    </script>
<?php endif; ?>

    
    
   <form id="editForm" method="POST">
    <?php if (!empty($user['profile_photo'])): ?>
        <img src="<?= htmlspecialchars($user['profile_photo']) ?>" id="profilePhotoPreview" alt="Profile Photo" width="150" height="150">
    <?php else: ?>
        <p>No profile photo available.</p>
    <?php endif; ?>
    
    <div class="profile-info">
    <br>
        <div class="info-group">
        <div class="info-item-id">
        <label id="userID-title">User ID</label>
    <label>
        <input type="text" value="<?= htmlspecialchars($user['user_id']) ?>" readonly>
    </label>
    <br>
    </div>

    <div class="info-item" id="personal">
    <p id="personal-info-title" style="margin: -30px 0px 5px -3px;">Personal Information</p>
    <label>First Name
        <input type="text" value="<?= htmlspecialchars($user['first_name']) ?>" readonly>
    </label>
    </div>

    <div class="info-item" id="personal">
    <label>Middle Name 
        <input type="text" value="<?= htmlspecialchars($user['middle_name']) ?>" readonly>
    </label>
    </div>

    <div class="info-item" id="personal">
    <label>Last Name 
        <input type="text" value="<?= htmlspecialchars($user['last_name']) ?>" readonly>
    </label>
    </div>

    <div class="info-item" id="personal">
    <label>Suffix
        <input type="text" value="<?= htmlspecialchars($user['suffix']) ?>" readonly>
    </label>
    </div>
    
    <div class="info-item" style="margin-top: -15px;">
    <label>Sex</label>
    <label>
    <input type="radio" name="sex" value="Male" <?= $user['sex'] === 'Male' ? 'checked' : '' ?> disabled 
    style="position: absolute; margin: 5px 0px 0px -500px;"> 
    <span style="margin-left: 30px;">Male</span>
    </label>

    <label>
    <input type="radio" name="sex" value="Female" <?= $user['sex'] === 'Female' ? 'checked' : '' ?> disabled 
    style="position: absolute; margin: -35px 0px 0px -400px;"> 
    <span style="margin: -37px 0px 0px 120px; position: absolute;">Female</span>
</label>
</div>

    
    <div class="info-item" id="personal">
    <label>Birthday 
        <input type="text" value="<?= htmlspecialchars($user['birthdate']) ?>" readonly>
    </label>
    </div>

    <div class="info-item" id="personal">  
    <label>Mobile Number 
        <input type="text" class="editable" name="contact_no" onkeypress="enforceNumericInput(event)" pattern="\d{11}" title="11-digit number starting with 09" value="<?= htmlspecialchars($user['contact_no']) ?>" readonly>
        <div id="contactError" class="error-message"></div>
    </label>
    </div>
    
    <div class="info-item" id="personal">
    <label>Email Address
        <input type="email" class="editable" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
        <div id="emailError" class="error-message"></div>
    </label>
    </div>

    <div class="info-item" id="brgy" style="margin-top: -30px;">
    <p id="address-title">Address</p>
    <br>
    <label>House/Lot Number
        <input type="text" class="editable" name="house_lot_number" value="<?= htmlspecialchars($user['house_lot_number']) ?>" readonly>
    </label><br>
    </div>

    <div class="info-item" id="brgy">
    <label>Street/Subdivision 
        <input type="text" class="editable" name="street_subdivision_name" value="<?= htmlspecialchars($user['street_subdivision_name']) ?>" readonly>
    </label><br>
    </div>

    <div class="info-item" id="brgy">
    <label>Barangay <input type="text" value="<?= htmlspecialchars($user['barangay']) ?>" readonly>
    </label><br>
    </div>

    <div class="info-item" id="brgy">
    <label>City
        <input type="text" value="<?= htmlspecialchars($user['city']) ?>" readonly>
    </label><br>
    </div>

    <div class="info-item" id="job" style=" margin-top: -13px;">
    <p style="margin: 5px 0px 0px -3px;">Job Description</p>
    <label>Role
        <input type="text" value="<?= htmlspecialchars($user['role']) ?>" readonly>
    </label><br>
    </div>

    <div class="info-item" id="job">
    <label>Position 
        <input type="text" value="<?= htmlspecialchars($user['position']) ?>" readonly>
    </label><br>
    </div>

    <div class="info-item" id="job">
    <label>Schedule
        <input type="text" value="<?= htmlspecialchars($user['schedule'] ?? 'Unassigned') ?>" readonly>
    </label><br>
    </div>

    <div class="info-item" id="job">
    <label>Shift
        <input type="text" value="<?= htmlspecialchars($user['shift'] ?? 'Unassigned') ?>" readonly>
    </label><br>
    </div>

    <button type="button" id="editButton" onclick="toggleEditMode(true)">Edit Information</button>
    <button type="button" id="saveButton" style="display:none;" onclick="showModal()">Save Changes</button>

<div id="confirmModal" class="modal">
    <div class="modal-content">
    <img class="question-sign" src="images/question.png">
        <h2>Are you sure you want to save changes?</h2>
        <button id="yesbtn" type="button" onclick="saveChanges()">Yes</button>
        <button id="nobtn" type="button" onclick="closeModal()">No</button>
    </div>
</div>

</body>
</html>