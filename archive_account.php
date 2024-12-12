
<?php
session_start();
include_once('db_conn2.php');
include('./adminsidebar-accountservices.php');

?>
<title>Archive Account</title>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Rounded" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<style>
    .main-content {
        margin-left: 30px; 
        padding: 20px;
    }

    .container {
        max-width: 100%;
    }

    .title h3 {
        font-size: 25px;
        font-weight: bold;
        color: #02476A;
        margin: 30px 0px 0px 170px !important;   
    }

    .table-container {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        border: 1px solid #F6F6F6;
        margin-top: 75px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 100%;
        margin-left: 156px;
    }

    #residentTable {
        width: 100%;
        margin-left: 0px;
    }

    .dataTables_filter {
        position: relative;
        display: flex;
        align-items: center;
        margin-top: -40px !important;
        position: relative;
        margin-bottom: 30px;
        top: 10px;
    }

    .dataTables_filter input {
        width: 350px;
        padding: 8px !important;
        padding-left: 25px !important;
        border-radius: 5px;
        border: 1px solid #02476A;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);  
    }

    .dataTables_filter::before {
        content: '\e8b6'; 
        font-family: 'Material Symbols Rounded';
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 18px;
        color: #02476A;
        pointer-events: none;
    }

    #statusFilter {
        margin: 40px 0px 0px 160px !important;
        padding: 5px;
        border-radius: 5px;
        border: 1px solid #02476A;
        color: #02476A;
        font-size: 13px;
    }

    .dataTables_filter label {
        margin-right: 5px;
        font-weight: bold;
        font-size: 13px;
    }
    
    .table th {
        color: #02476A;
        background-color: #E8F3F8;
        font-weight: bold;
        font-size: 13px;
        width: 680px;
    }

    .table td {
        font-size: 13px;
    }

    .table tbody td:last-child {
        text-align: center;
        display: flex;
        justify-content: center;
    }

    .view-btn {
        font-size: 13px;
        text-decoration: none;
        color: #FFFFFF;
        background-color: #4597C0;
        padding: 5px 15px;
        border-radius: 5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .view-btn .material-symbols-rounded {
        margin: 0px 5px 0px 0px !important; 
        font-size: 14px;
    }

    .export-btn {
        background-color: #0288D1;
        margin-left: 64.6%;
        margin-top: 20px;
    }

   
   
    #selectedCount {
        font-size: 14px;
        margin: 20px 0px 0px 160px;
        position: absolute;
    }

    .dataTables_paginate .paginate_button {
        font-size: 13px; 
        margin-top: 20px;
    }

    .dataTables_info {
        font-size: 13px; 
        margin-top: 20px;
    }

    .dataTables_length {
    margin-top: -30px; 
    font-size: 13px;
    }

    .dataTables_length select {
        font-size: 13px; 
    }

    .buttons-container {
        display: flex;
        margin: 20px 0;
        position: absolute;
        top: 130px;
        left: 288px;
        border-radius: none !important;
    }

    .navigation-btn {
        min-width: 406px;
        height: 50px;
        background-color: #FFFFFF;
        color: #02476A;
        border: 1px solid #ccc;
        border-radius: none !important;
        border-top-left-radius: 0px;
        border-bottom-left-radius: 0px;
        border-top-right-radius: 0px;
        border-bottom-right-radius: 0px;
        font-size: 16px;
        text-transform: uppercase;
        cursor: pointer;
        text-transform: uppercase;
        transition: background-color 0.3s ease;
        margin-left: 0px;
    }

    .navigation-btn.active {
        background-color: #4597C0 !important; 
        color: white !important;
    }

    #userAccountsBtn {
        border-top-left-radius: 10px;
        
    }

    #residentsBtn {
        background-color: #FFFFFF;
        color: #02476A;
        border: 1px solid #ccc;
    }

    #archiveBtn {
        border-top-right-radius: 10px; 
        background-color: #FFFFFF;
        color: #02476A;
        border: 1px solid #ccc;
    }

    a[id^="view-btn-"] { 
        background-color: #4597C0;
        padding: 5px 15px;
        text-transform: uppercase;
        text-decoration: none;
        color: white;
        border-radius: 5px;
        padding: 5px;
        width: 100%;
    }

    a#view-btn-<?= htmlspecialchars($row['user_id']) ?> i.fas.fa-eye { 
        font-size: 18px;  
    }

 /* Responsive */
 @media (max-width: 768px) {
            .main-content {
                margin-left: 0; 
            }
            .table-container {
                padding: 10px;
            }
            h1 {
                font-size: 1.5rem;
            }
        }
</style>
<main class="main-content">
    <div class="title">
    <h3>ARCHIVE ACCOUNT MANAGEMENT</h3>
</div>
<hr style="color: gray; width: 90%; position: absolute; margin: 30px 0px 0px 40px;">

<div class="buttons-container">
    <button class="navigation-btn" id="userAccountsBtn" onclick="activateButton('userAccountsBtn', 'add_user.php')">User Accounts</button>
    <button class="navigation-btn" id="residentsBtn" onclick="activateButton('residentsBtn', 'residents_list.php')">Resident List</button>
    <button class="navigation-btn active" id="archiveBtn" onclick="activateButton('residentsBtn', 'archive_account.php')">Archive</button>
</div>

<div class="container">
        <form id="importForm" action="import_excel.php" method="post" enctype="multipart/form-data" style="display: none;">
            <input type="file" name="file" id="fileInput" accept=".xls, .xlsx" required>
        </form>
        
       
<div class="table-container">
    <!-- Status Filter -->
    <select id="statusFilter" style="margin-bottom: 15px; padding: 5px; border-radius: 5px; border: 1px solid #02476A; color: #02476A;">
        <option value="">All</option>
        <?php
    $rolesQuery = "SELECT DISTINCT role FROM archive_accounts";
    $rolesResult = $conn->query($rolesQuery);

    if ($rolesResult->num_rows > 0) {
        while ($role = $rolesResult->fetch_assoc()) {
            echo "<option value='" . htmlspecialchars($role['role']) . "'>" . htmlspecialchars($role['role']) . "</option>";
        }
    }
    ?>
    </select>
            
            <table id="residentTable" class="table table-bordered">
            <thead>
            <tr>
                <th>User ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Suffix</th>        
                <th>Role</th>
                <th>Position</th>
                <th>Schedule</th>
                <th>Shift</th>  
                <th>Action</th>         
            </tr>
        </thead>
        <tbody>
            <?php
            // Display archived accounts
            $query = "SELECT * FROM archive_accounts ORDER BY archived_at DESC";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                // Display each archived account
                while ($row = $result->fetch_assoc()) {
                   
                    echo "<tr data-role='" . htmlspecialchars($row['role']) . "'>
                            <td>" . htmlspecialchars($row['user_id']) . "</td>
                            <td>" . htmlspecialchars($row['first_name']) . "</td>
                            <td>" . htmlspecialchars($row['middle_name']) . "</td>
                            <td>" . htmlspecialchars($row['last_name']) . "</td>
                            <td>" . htmlspecialchars($row['suffix']) . "</td>       
                            <td>" . htmlspecialchars($row['role']) . "</td>
                            <td>" . htmlspecialchars($row['position']) . "</td>
                            <td>" . htmlspecialchars($row['schedule']) . "</td>
                            <td>" . htmlspecialchars($row['shift']) . "</td>
                            <td>
                    <a href='archive_profile.php?user_id=" . htmlspecialchars($row['user_id']) . "' id='view-btn-" . htmlspecialchars($row['user_id']) . "'>
                        <i class='fas fa-eye'></i> VIEW
                    </a>
                </td>

                        </tr>";
                }
            } else {
                echo "<tr><td colspan='9' style='text-align:center;'>No archived accounts found</td></tr>";
            }
            ?>
                </tbody>
            </table>
        </div>
        
</main>

<script>
$(document).ready(function () {
    const table = $('#residentTable').DataTable({
        language: {
            search: "",
            searchPlaceholder: "     Search...",
        },
        stateSave: true
    });

    $('#deactivateSelectedBtn, #reactivateSelectedBtn, #deleteSelectedBtn, .export-btn').prop('disabled', true);
    $('.export-btn').css('background-color', '#C5C5C5');

    if (!$('.import-btn').length) {
        $("div.dataTables_filter").prepend(`
        <!-- Create New Resident -->
            <a href="create_user.php" class="create-btn" style="display:none">
                <span class="material-symbols-rounded" style="display:none">add</span> CREATE NEW
            </a>
            <button type="button" class="import-btn" style="display: none;">
                <span class="material-symbols-rounded">upload</span> IMPORT DATA
            </button>
        `);
    }
    $('.import-btn').off('click').on('click', function () {
        $('#fileInput').click();
    });

    $('#fileInput').off('change').on('change', function () {
        if (this.files.length > 0) {
            $('#importForm').submit(); 
        }
    });
    $('#deleteSelectedBtn').on('click', function () {
        const selectedResidents = [];
        $('.rowCheckbox:checked').each(function () {
            selectedResidents.push($(this).val());
        });
        if (selectedResidents.length > 0) {
            if (confirm('Are you sure you want to delete the selected residents?')) {
                $('#selectedResidentsInput').val(JSON.stringify(selectedResidents));
                $('#deleteForm').submit();
            }
        } else {
            alert('No residents selected for deletion.');
        }
    });
    $('#statusFilter').on('change', function () {
        const selectedRole = $(this).val();
        table.column(5).search(selectedRole).draw();
    });
    $('#selectAll').on('click', function () {
        $('.rowCheckbox').prop('checked', this.checked);
        toggleButtons();  
    });
    $('.rowCheckbox').on('click', function () {
        $('#selectAll').prop('checked', $('.rowCheckbox:checked').length === $('.rowCheckbox').length);
        toggleButtons();  
    });
    function toggleButtons() {
        const selectedResidents = $('.rowCheckbox:checked').length;  
        $('#deactivateSelectedBtn, #reactivateSelectedBtn, #deleteSelectedBtn').prop('disabled', selectedResidents === 0);
        
        if (selectedResidents > 0) {
            $('.export-btn').css('pointer-events', 'auto'); 
            $('.export-btn').css('background-color', ''); 
        } else {
            $('.export-btn').css('pointer-events', 'none'); 
            $('.export-btn').css('background-color', '#C5C5C5'); 
        }

        $('#selectedCount').text(selectedResidents + ' Selected');
    }
    $('#deactivateSelectedBtn').on('click', function () {
        updateStatus('deactivate');
    });
    $('#reactivateSelectedBtn').on('click', function () {
        updateStatus('reactivate');
    });
    function updateStatus(action) {
        const selectedResidents = [];
        $('.rowCheckbox:checked').each(function () {
            selectedResidents.push($(this).val());
        });
        if (selectedResidents.length > 0) {
            const confirmMessage = action === 'deactivate' ? 
                'Are you sure you want to deactivate the selected residents?' : 
                'Are you sure you want to reactivate the selected residents?';
            if (confirm(confirmMessage)) {
                $('#statusResidentsInput').val(JSON.stringify(selectedResidents));
                $('#statusActionInput').val(action);
                $('#statusUpdateForm').submit();
            }
        } else {
            alert('No residents selected for status update.');
        }
    }
    table.on('draw', function () {
        toggleButtons();  
    });
});

function activateButton(buttonId, redirectUrl) {
        // Remove the active class from all buttons
        const buttons = document.querySelectorAll('.navigation-btn');
        buttons.forEach((btn) => {
            btn.classList.remove('active');
        });

        // Add the active class to the clicked button
        const activeButton = document.getElementById(buttonId);
        activeButton.classList.add('active');

        // Redirect to the provided URL
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    }
</script>
