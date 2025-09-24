<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'reception') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");

$appointment_id = $_GET['id'] ?? 0;

// Get invoice details
$result = $conn->query("
    SELECT a.date, p.name as patient_name, p.species, o.name as owner_name, o.contact, o.address,
           u.name as doctor_name, r.diagnosis, r.treatment, r.prescription, b.amount, b.status
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN owners o ON p.owner_id = o.id
    JOIN users u ON a.doctor_id = u.id
    LEFT JOIN records r ON a.id = r.appointment_id
    LEFT JOIN billing b ON a.id = b.appointment_id
    WHERE a.id = $appointment_id
");
$invoice = $result->fetch_assoc();

if (!$invoice) {
    die("Invoice not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo $invoice['patient_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; }
        .invoice-header { border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .invoice-details { margin-bottom: 30px; }
        .total { font-size: 1.5em; font-weight: bold; }
        @media print {
            .no-print { display: none; }
            body { font-size: 12px; }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="invoice-header text-center">
                    <h1>VetCare Pro</h1>
                    <p>Veterinary Practice Management System</p>
                    <h3>Invoice</h3>
                </div>

                <div class="row invoice-details">
                    <div class="col-6">
                        <h5>Patient Details</h5>
                        <p><strong>Patient:</strong> <?php echo $invoice['patient_name']; ?> (<?php echo $invoice['species']; ?>)</p>
                        <p><strong>Owner:</strong> <?php echo $invoice['owner_name']; ?></p>
                        <p><strong>Contact:</strong> <?php echo $invoice['contact']; ?></p>
                        <p><strong>Address:</strong> <?php echo $invoice['address']; ?></p>
                    </div>
                    <div class="col-6 text-end">
                        <h5>Appointment Details</h5>
                        <p><strong>Date:</strong> <?php echo $invoice['date']; ?></p>
                        <p><strong>Doctor:</strong> <?php echo $invoice['doctor_name']; ?></p>
                        <p><strong>Invoice #:</strong> <?php echo str_pad($appointment_id, 6, '0', STR_PAD_LEFT); ?></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h5>Medical Details</h5>
                        <p><strong>Diagnosis:</strong> <?php echo $invoice['diagnosis']; ?></p>
                        <p><strong>Treatment:</strong> <?php echo $invoice['treatment']; ?></p>
                        <p><strong>Prescription:</strong> <?php echo $invoice['prescription']; ?></p>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Consultation Fee</td>
                                    <td>$<?php echo number_format($invoice['amount'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="1" class="text-end total">Total:</td>
                                    <td class="total">$<?php echo number_format($invoice['amount'], 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <p><strong>Payment Status:</strong> <?php echo ucfirst($invoice['status']); ?></p>
                        <p>Thank you for choosing VetCare Pro!</p>
                    </div>
                </div>

                <div class="row mt-4 no-print">
                    <div class="col-12 text-center">
                        <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
                        <a href="billing.php" class="btn btn-secondary">Back to Billing</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>