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
    <title>Email page :- </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Custom Loader (Overlay) */
        .custom-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            /* Dark transparent background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            /* Ensures it appears above other content */
        }

        /* Logo Style */
        .custom-loader .logo {
            width: 150px;
            /* Adjust size as needed */
        }
    </style>



</head>

<body>


    <!-- Spinner (will be displayed during PDF generation) -->
    <!-- <div id="loadingSpinner" class="spinner-container" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div> -->

    <!-- custom -->
    <div id="customLoader" class="custom-loader" style="display: none;">
        <img src="ME CROP loop File.gif" alt="Loading..." class="logo">
    </div>



    <div class="container mt-4">
        <h1 class="text-center">Charges and Amount</h1>
        <form id="charges-form">
            <div class="mb-3">
                <label for="additionalInput" class="form-label">Email to Send</label>
                <input type="email" class="form-control" id="emailInput" placeholder="Enter Email"
                    name="additionalInput">
            </div>

            <div id="content" style="display: none;"></div>
            <div class="mb-3">
                <label for="total" class="form-label">Total:</label>
                <input type="text" id="total" class="form-control" name="total" disabled>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr</th>
                            <th>Charges</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="charges-table-body">
                        <!-- Data will be populated here dynamically -->
                    </tbody>
                </table>
            </div>
            <div class="text-center mt-4">
                <button type="button" class="btn btn-success me-2 pdf" onclick="savePDF()">
                    <i class="bi bi-file-earmark-pdf"></i> Save as PDF
                </button>
                <button type="button" class="btn btn-primary" onclick="sendEmail()">
                    <i class="bi bi-envelope"></i> Send Email
                </button>
            </div>
            <div class="text-center mt-4">
                <a href="index1.php"> <button type="button" class="btn btn-outline-info">
                        <i class="bi bi-house-check-fill"></i> Home
                    </button>

            </div>
        </form>
    </div>

    <script>
        // Fetch data from the server to populate table
        fetch('fetchPayrollData.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                    alert('Error: ' + data.error);
                    return;
                }

                console.log('Fetched Data:', data); // Debug the data

                const tableBody = document.getElementById('charges-table-body');
                tableBody.innerHTML = ''; // Clear any existing rows

                // Iterate over the data to create rows
                data.data.forEach((row, index) => {
                    if (!row.charges && !row.amount) return; // Skip empty rows

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                <td class="align-middle">${index + 1}</td>
                <td><input type="text" class="form-control" placeholder="Charge" value="${row.charges || ''}" name="charge${index + 1}"></td>
                <td><input type="number" class="form-control" placeholder="Amount" value="${row.amount || ''}" name="amount${index + 1}"></td>
                <td class="text-center align-middle">
                    <div class="btn-group">
                        <button type="button" class="btn btn-warning btn-sm" onclick="updateCharge(${index + 1})">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm ms-2" onclick="deleteCharge('${row.charges}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            `;
                    tableBody.appendChild(tr);
                });

                // Set the total value
                document.getElementById('total').value = data.total;

            })
            .catch(error => {
                console.error('Error fetching payroll data:', error);
                alert('Error fetching payroll data.');
            });



        // Function to delete a charge
        function deleteCharge(charge) {
            if (!charge) {
                alert("Invalid or missing charge.");
                return;
            }

            fetch('deleteCharge.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ chargesToDelete: charge }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        alert(data.message);
                        if (data.reload) {
                            location.reload(); // Reload the page after successful deletion
                        }
                    }
                })
                .catch(error => {
                    console.error('Request failed:', error);
                    alert('Error deleting charge.');

                });

        }

        function updateCharge(srNo) {
            const chargeInput = document.querySelector(`[name="charge${srNo}"]`);
            const amountInput = document.querySelector(`[name="amount${srNo}"]`);

            if (!chargeInput || !amountInput) {
                alert('Error: Unable to find inputs for the specified row.');
                return;
            }

            const charge = chargeInput.value.trim();
            const amount = amountInput.value.trim();

            if (!charge) {
                alert('Please enter a valid charge.');
                return;
            }
            if (!amount || isNaN(amount) || Number(amount) <= 0) {
                alert('Please enter a valid amount.');
                return;
            }

            const formData = new URLSearchParams();
            formData.append('srNo', srNo);  // Send Sr. No.
            formData.append('charge', charge);
            formData.append('amount', amount);

            fetch('UpdateCharges.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData.toString(),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(`Error(s): ${data.message}`);
                    }
                })
                .catch(error => {
                    console.error('Error updating charge:', error);
                    alert(`Unexpected error. Please try again.\nDetails: ${error.message}`);
                });
        }

        // function savePDF() {
        //     // Display the spinner
        //     document.getElementById('loadingSpinner').style.display = 'flex';

        //     // Call the backend to generate the PDF
        //     fetch('pdf.php', {
        //         method: 'POST', // Backend doesn't require additional data for this task
        //     })
        //         .then(response => {
        //             if (!response.ok) {
        //                 throw new Error('Network response was not ok');
        //             }
        //             return response.json();
        //         })
        //         .then(data => {
        //             if (data.status === 'success') {
        //                 alert(data.message);

        //                 // Open the PDF in a new tab
        //                 window.open(data.filePath, '_blank');
        //             } else {
        //                 alert('Error: ' + data.message);
        //             }
        //         })
        //         .catch(error => {
        //             console.error('An error occurred:', error);
        //             alert('An error occurred: ' + error);
        //         })
        //         .finally(() => {
        //             // Hide the spinner once the process is complete
        //             document.getElementById('loadingSpinner').style.display = 'none';
        //         });
        // }

        function savePDF() {
            // Display the custom loader
            document.getElementById('customLoader').style.display = 'flex';

            // Call the backend to generate the PDF
            fetch('pdf.php', {
                method: 'POST', // Backend doesn't require additional data for this task
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);

                        // Open the PDF in a new tab
                        window.open(data.filePath, '_blank');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('An error occurred:', error);
                    alert('An error occurred: ' + error);
                })
                .finally(() => {
                    // Hide the custom loader once the process is complete
                    document.getElementById('customLoader').style.display = 'none';
                });
        }

        function sendEmail() {
            // First, ensure the PDF is generated before proceeding
            savePDF();

            // Wait for 10 seconds (10000 milliseconds) before sending the email
            setTimeout(() => {
                const email = document.getElementById('emailInput').value;

                // Validate email
                if (!email) {
                    alert('Please enter a valid email address.');
                    return;
                }

                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailRegex.test(email)) {
                    alert('Please enter a valid email address.');
                    return;
                }

                // Sending the email via fetch API
                fetch('sendEmail.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ receiver_email: email }), // Send email as receiver_email
                })
                    .then(response => {
                        // Log the raw response for debugging
                        console.log('Response received:', response);

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            alert('Email sent successfully!');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error sending email:', error);
                        alert('An unexpected error occurred.');
                    });
            }, 3000); // 10 seconds delay before sending the email
        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>