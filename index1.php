<?php
session_start(); // Start the session

// Set the session timeout limit (in seconds)
$timeout_duration = 86400; // 24 hours (86400 seconds)

// Check if the session variable 'logged_in' exists and if the session has expired
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // If not logged in, redirect to login page
    header("Location: login.html");
    exit; // Ensures no further code is executed
} 

// Check if the session has expired based on the last activity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // If session has expired, destroy the session
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header("Location: login.html"); // Redirect to login page
    exit; // Ensures no further code is executed
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="ME.png">
    <style>
        .Logo {
            height: 100px;
            width: 100px;
            border-radius: 10px;
        }

         /* Global Loader Styling */
    .global-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8); /* Dark transparent background */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999; /* Ensures it appears above all other content */
    }

    .global-loader .logo {
        width: 150px; /* Adjust size as needed */
    }
    </style>

    <!-- Global Loading Screen -->
<div id="globalLoader" class="global-loader" style="display: none;">
    <img src="ME CROP loop File.gif" alt="Loading..." class="logo">
</div>

</head>

<body class="bg-light">
    <div class="container py-4 d-flex justify-content-center">
        <div class="card w-100" style="max-width: 600px;">
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="ME.png" alt="Company Logo" class="mb-2 img-fluid Logo">
                    <h1 class="text-primary fs-4">Payroll System</h1>
                </div>

                <!-- Form connected to PHP script -->
                <form class="w-100" id="payroll-form">
                    <div class="mb-3">
                        <!-- Monthly Budget -->
                        <label for="monthly-budget" class="form-label">Monthly Budget:</label>
                        <div class="row g-2">
                            <div class="col-12 col-sm-8">
                                <input type="text" id="monthly-budget" class="form-control" disabled>
                            </div>
                            <div class="col-12 col-sm-4">
                                <button type="button" class="btn btn-warning w-100" onclick="openPayroll()">Open Pay
                                    Roll</button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <!-- Balance -->
                        <label for="balance" class="form-label">Balance:</label>
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-sm-8">
                                <input type="text" id="balance" class="form-control" disabled>
                            </div>
                            <div class="col-12 col-sm-4 text-center">
                                <p id="current-month" class="form-control-plaintext mb-0" name="Date"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Title:</label>
                        <input type="text" id="title" class="form-control" placeholder="Enter Title">
                    </div>

                    <!-- Department Dropdown -->
                    <div class="mb-3">
                        <label for="department" class="form-label">Department - Predefined:</label>
                        <select id="department" class="form-select">
                            <option value="">Select Department</option>
                            <option value="Pankaj">Pankaj</option>
                            <option value="Joel">Joel</option>
                            <option value="Alju">Alju</option>
                            <option value="Steveson">Steveson</option>
                        </select>
                    </div>

                    <!-- Charges -->
                    <div class="mb-3">
                        <label for="charges" class="form-label">Charges:</label>
                        <input type="text" id="charges" class="form-control" placeholder="Enter charges" name="Charges">
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount:</label>
                        <input type="text" id="amount" class="form-control" placeholder="Enter amount" name="Amount">
                    </div>

                    <!-- Total -->
                    <div class="mb-3">
                        <label for="total" class="form-label">Total:</label>
                        <input type="text" id="total" class="form-control" placeholder="Enter amount" name="total"
                            disabled>
                    </div>

                    <!-- Buttons -->
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" onclick="submitForm()">Add Amount</button>
                        <button type="button" class="btn btn-warning" onclick="generateNewFile()">Generate New</button>
                        <button type="button" class="btn btn-danger" id="reset" onclick="resetFields()">Reset</button>
                    </div>
                </form>
                <h5>Version 0.0.0.2</h5>
            </div>
        </div>
    </div>

    <script>
        const currentMonthElement = document.getElementById('current-month');
        const currentDate = new Date();
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        currentMonthElement.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;


        fetch('process.php', {
            method: 'POST',
            body: new FormData(document.getElementById('payroll-form'))
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                } else {
                    document.getElementById('monthly-budget').value = data.monthlyBudget;
                    document.getElementById('balance').value = data.balance;
                    document.getElementById('total').value = data.total; // Update the total value
                }
            });


        // Function to create new excel sheet
        function generateNewFile() {
            fetch('generate_new.php', {
                method: 'POST',
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('New Excel file generated successfully!');
                        // Optional: Provide download link
                        const link = document.createElement('a');
                        link.href = data.filePath; // Path to the generated file
                        link.download = 'MEPAYROLL_New.xlsx';
                        link.click();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while generating the new file.');
                });
        }



        function submitForm() {
            const form = document.getElementById('payroll-form');
            const formData = new FormData(form);

            fetch('process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        alert('Error: ' + data.error);
                    } else {
                        alert('Charges posted');
                        window.location.reload();
                    }
                });
        }

        function resetFields() {
            document.getElementById('charges').value = '';
            document.getElementById('amount').value = '';
            document.getElementById('title').value = '';
            document.getElementById('department').value = '';
            document.getElementById('total').value = '';
        }

        function openPayroll() {
            const newTab = window.open('payroll.php', '_blank');

            fetch('fetchPayrollData.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        alert('Error: ' + data.error);
                    } else {
                        newTab.onload = () => {
                            const tableBody = newTab.document.getElementById('payroll-table-body');
                            tableBody.innerHTML = '';
                            data.forEach((row, index) => {
                                const tr = newTab.document.createElement('tr');
                                tr.innerHTML = `
                                    <td>${index + 1}</td>
                                    <td>${row.charges || ''}</td>
                                    <td>${row.amount || ''}</td>
                                `;
                                tableBody.appendChild(tr);
                            });
                        };
                    }
                })
                .catch(error => console.error('Error fetching payroll data:', error));
        }
        function reloadPageIn60Seconds() {
            setTimeout(() => {
                location.reload(true); // Forces a reload from the server
            }, 60000); // 60,000 milliseconds = 60 seconds
        }

        // Call the function to start the timer
        reloadPageIn60Seconds();

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show the global loader when the page starts loading
        window.addEventListener('load', function () {
            document.getElementById('globalLoader').style.display = 'none';
        });
    
        // Show the loader when navigating away or reloading the page
        window.addEventListener('beforeunload', function () {
            document.getElementById('globalLoader').style.display = 'flex';
        });
    </script>
    
</body>

</html>