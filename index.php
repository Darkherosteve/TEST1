<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minions Entertainment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>

    <h2 class="text-center">AQUA TRANS</h2>

    <div class="container mt-5">
      <form action="process.php" method="POST">
        <div class="mb-3">
          <label for="vehicle_no" class="form-label">Vehicle No:</label>
          <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" required>
        </div>
        <div class="mb-3">
          <label for="date" class="form-label">Date</label>
          <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="mb-3">
          <label for="consignment_no" class="form-label">Consignment No:</label>
          <input type="text" class="form-control" id="consignment_no" name="consignment_no" required>
        </div>
        <div class="mb-3">
          <label for="from_location" class="form-label">From Location:</label>
          <input type="text" class="form-control" id="from_location" name="from_location" required>
        </div>
        <div class="mb-3">
          <label for="to_location" class="form-label">To Location:</label>
          <input type="text" class="form-control" id="to_location" name="to_location" required>
        </div>
        <div class="mb-3">
          <label for="consignor" class="form-label">Consignor:</label>
          <input type="text" class="form-control" id="consignor" name="consignor" required>
        </div>
        <div class="mb-3">
          <label for="consignee" class="form-label">Consignee:</label>
          <input type="text" class="form-control" id="consignee" name="consignee" required>
        </div>
        <div class="mb-3">
          <label for="cargo_description" class="form-label">Cargo Description:</label>
          <input type="text" class="form-control" id="cargo_description" name="cargo_description" required>
        </div>
        <div class="mb-3">
          <label for="quantity" class="form-label">Quantity:</label>
          <input type="text" class="form-control" id="quantity" name="quantity" required>
        </div>
        <div class="mb-3">
          <label for="gross_weight" class="form-label">Gross Weight:</label>
          <input type="text" class="form-control" id="gross_weight" name="gross_weight">
        </div>
        <div class="mb-3">
          <label for="tare_weight" class="form-label">Tare Weight:</label>
          <input type="text" class="form-control" id="tare_weight" name="tare_weight">
        </div>
        <div class="mb-3">
          <label for="net_weight" class="form-label">Net Weight:</label>
          <input type="text" class="form-control" id="net_weight" name="net_weight">
        </div>
        <div class="mb-3">
          <label for="booking_no" class="form-label">Booking No:</label>
          <input type="text" class="form-control" id="booking_no" name="booking_no">
        </div>
        <div class="mb-3">
          <label for="invoice_no" class="form-label">Invoice No:</label>
          <input type="text" class="form-control" id="invoice_no" name="invoice_no">
        </div>
        <div class="mb-3">
          <label for="eway_bill_no" class="form-label">Eway Bill No:</label>
          <input type="text" class="form-control" id="eway_bill_no" name="eway_bill_no">
        </div>
        <div class="mb-3">
          <label for="container_no" class="form-label">Container No:</label>
          <input type="text" class="form-control" id="container_no" name="container_no">
        </div>
        <div class="mb-3">
          <label for="seal_no" class="form-label">Seal No:</label>
          <input type="text" class="form-control" id="seal_no" name="seal_no">
        </div>
        <div class="mb-3">
          <label for="remark" class="form-label">Remarks:</label>
          <input type="text" class="form-control" id="remark" name="remark">
        </div>
        <div class="mb-3">
          <label for="arrival_time" class="form-label">Arrival Date:</label>
          <input type="date" class="form-control" id="arrival_time" name="arrival_time">
        </div>
        <div class="mb-3">
          <label for="departure_time" class="form-label">Departure Date:</label>
          <input type="date" class="form-control" id="departure_time" name="departure_time">
        </div>
        <div class="d-grid gap-2 col-6 mx-auto">
          <button class="btn btn-primary" type="submit">Submit</button>
          <button class="btn btn-danger" type="reset">Reset</button>
        </div>
      </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
