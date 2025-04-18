<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dynamic Invoice Form</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="icon" type="image" href="bluemoon.png">

  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #f8f8f8;
      color: #333;
    }

    form {
      background-color: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      max-width: 900px;
      margin: 0 auto;
    }

    h2 {
      text-align: center;
      color: #0033cc;
    }

    .logo-wrapper {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }

    .logo-wrapper img {
      background-color: rgba(70, 68, 68, 0.815);
      padding: 10px;
      border-radius: 10px;
      width: 100px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    label i {
      margin-right: 8px;
      color: #444;
    }

    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="date"],
    textarea,
    select {
      width: 100%;
      padding: 10px;
      border: 1px solid #999;
      border-radius: 10px 0 0 0;
      box-sizing: border-box;
      font-size: 14px;
    }

    textarea {
      resize: vertical;
      min-height: 80px;
      border: 2px solid black !important;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      border: 1px solid #424242;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f0f0f0;
    }

    button,
    #result a {
      background-color: #007bff;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 3px;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      margin: 10px 5px 0 0;
    }

    button:hover {
      background-color: #0056b3;
    }

    .form-section {
      margin-bottom: 25px;
    }

    .hidden {
      display: none;
    }

    #result {
      text-align: center;
      margin-top: 20px;
    }

    /* Media Queries */
    @media (max-width: 768px) {
      form {
        width: 100%;
      }

      .logo-wrapper {
        justify-content: center;
      }

      .logo-wrapper img {
        margin: 0 auto;
      }
    }

    @media (max-width: 480px) {
      body {
        padding: 10px;
      }

      label {
        font-size: 14px;
      }

      input,
      textarea,
      select {
        font-size: 14px;
      }

      button {
        width: 100%;
        text-align: center;
      }

      th, td {
        font-size: 12px;
      }
    }
  </style>
</head>

<body>

  <h2>Bluemoon Production - Invoice Generator</h2>

  <div class="logo-wrapper">
    <img src="bluemoon.png" alt="Bluemoon Production Logo">
  </div>

  <form id="invoiceForm">

    <!-- Example with icons -->
    <div class="form-section">
      <label for="billingType">Billing Type: <i class="fas fa-file-invoice"></i></label>
      <select id="billingType" name="billing_type" required>
        <option value="">-- Select Billing Type --</option>
        <option value="company_to_company">Company to Company</option>
        <option value="company_to_person">Company to Person</option>
        <option value="person_to_person">Person to Person</option>
        <option value="only_person">Only Person</option>
      </select>
    </div>

    <div id="companyDetails" class="form-section hidden">
      <label><i class="fas fa-building"></i> Company Name:</label>
      <input type="text" name="company_name">

      <label><i class="fas fa-map-marker-alt"></i> Address:</label>
      <input type="text" name="company_address">

      <label><i class="fas fa-phone"></i> Phone:</label>
      <input type="text" name="phone">

      <label><i class="fas fa-envelope"></i> Email:</label>
      <input type="email" name="email">
    </div>

    <div id="personToPersonDetails" class="form-section hidden">
      <h3>From Person:</h3>
      <label><i class="fas fa-user"></i> Sender Name:</label>
      <input type="text" name="sender_name">

      <label><i class="fas fa-map-marker-alt"></i> Sender Address:</label>
      <input type="text" name="sender_address">

      <h3>To Person:</h3>
      <label><i class="fas fa-user"></i> Receiver Name:</label>
      <input type="text" name="receiver_name">

      <label><i class="fas fa-map-marker-alt"></i> Receiver Address:</label>
      <textarea name="receiver_address"></textarea>
    </div>

    <div id="clientDetails" class="form-section hidden">
      <h3>Bill To:</h3>
      <label><i class="fas fa-user-circle"></i> Client Name:</label>
      <input type="text" name="client_name">

      <label><i class="fas fa-map-marked-alt"></i> Client Address:</label>
      <input type="text" name="client_address">
    </div>

    <div class="form-section">
      <h3>Invoice Details:</h3>
      <label><i class="fas fa-calendar-alt"></i> Invoice Date:</label>
      <input type="date" name="invoice_date" required>

      <label><i class="fas fa-clipboard-list"></i> Terms:</label>
      <input type="text" name="terms">

      <label><i class="fas fa-hourglass-end"></i> Due Date:</label>
      <input type="date" name="due_date" required>
    </div>

    <div class="form-section">
      <h3>Item Details:</h3>
      <table id="itemTable">
        <thead>
          <tr>
            <th>Sl.No.</th>
            <th>Item & Description</th>
            <th>Qty</th>
            <th>Amount</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="itemBody">
          <tr>
            <td><input type="text" name="so_no[]" value="1" required></td>
            <td><textarea name="item_description[]" required></textarea></td>
            <td><input type="number" name="quantity[]" min="1" required></td>
            <td><input type="text" name="amount[]" required></td>
            <td><button type="button" style="background: #ff2e0e;" onclick="removeRow(this)">Remove</button></td>
          </tr>
        </tbody>
      </table>
      <button type="button" style="background: #1dd660;" onclick="addItemRow()">Add Item</button>
    </div>

    <div class="form-section">
      <label><i class="fas fa-calculator"></i> Sub Total:</label>
      <input type="text" name="subtotal" id="subtotal" readonly>

      <label><i class="fas fa-money-bill"></i> Total:</label>
      <input type="text" name="total" id="total" readonly>

      <label><i class="fas fa-coins"></i> Advance Payment:</label>
      <input type="text" name="advance_payment" id="advance_payment" oninput="calculateBalanceDue()">

      <label><i class="fas fa-wallet"></i> Balance Due:</label>
      <input type="text" name="balance_due" id="balance_due" readonly>

      <label><i class="fas fa-spell-check"></i> Total in Words:</label>
      <input type="text" name="total_words" id="total_words" readonly>
    </div>

    <div class="form-section">
      <h3>Payment Details:</h3>
      <label><i class="fas fa-money-check-alt"></i> Payment Method:</label>
      <select name="payment_method" id="paymentMethod" onchange="togglePaymentFields()" required>
        <option value="bank">Bank Details</option>
        <option value="upi">UPI ID</option>
      </select>

      <div id="bankFields">
        <label><i class="fas fa-user"></i> Account Holder Name:</label>
        <input type="text" name="account_holder_name">

        <label><i class="fas fa-university"></i> Bank Name:</label>
        <input type="text" name="account_name">

        <label><i class="fas fa-hashtag"></i> Account Number:</label>
        <input type="text" name="account_number">

        <label><i class="fas fa-exchange-alt"></i> Account Type:</label>
        <input type="text" name="account_type">

        <label><i class="fas fa-code"></i> IFSC Code:</label>
        <input type="text" name="ifsc_code">

        <label><i class="fas fa-map-signs"></i> Branch:</label>
        <input type="text" name="branch">

        <label><i class="fas fa-globe"></i> SWIFT Code:</label>
        <input type="text" name="swift_code">
      </div>

      <div id="upiField" class="hidden">
        <label><i class="fas fa-user"></i> UPI Account Name:</label>
        <input type="text" name="upi_name" placeholder="Enter UPI name">

        <label><i class="fas fa-id-badge"></i> UPI ID:</label>
        <input type="text" name="upi_id">
      </div>
    </div>

    <div class="form-section">
      <h3>Terms & Conditions:</h3>
      <textarea name="terms_conditions"></textarea>
    </div>

    <div class="form-section">
      <label><input type="checkbox" name="include_logo" id="include_logo"> Include Logo in PDF</label>
      <label><input type="checkbox" name="include_qr_code" id="include_qr_code"> Include QR Code in PDF</label>
      <label style="display: none;"><input type="checkbox" name="qr_with_pricing" id="qr_with_pricing"> Include Pricing in QR Code</label>
    </div>

    <button type="submit"><i class="fas fa-file-invoice"></i> Generate Invoice</button>
  </form>

  <div id="result"></div>

    <script>
        // Billing Type Toggle Logic
        document.getElementById('billingType').addEventListener('change', function() {
            const type = this.value;
            const companySection = document.getElementById('companyDetails');
            const clientSection = document.getElementById('clientDetails');
            const personToPersonSection = document.getElementById('personToPersonDetails');

            companySection.classList.add('hidden');
            clientSection.classList.add('hidden');
            personToPersonSection.classList.add('hidden');

            if (type === 'company_to_company' || type === 'company_to_person') {
                companySection.classList.remove('hidden');
                clientSection.classList.remove('hidden');
            } else if (type === 'person_to_person') {
                personToPersonSection.classList.remove('hidden');
            } else if (type === 'only_person') {
                clientSection.classList.remove('hidden');
            }
        });

        function togglePaymentFields() {
            const method = document.getElementById('paymentMethod').value;
            document.getElementById('bankFields').classList.toggle('hidden', method !== 'bank');
            document.getElementById('upiField').classList.toggle('hidden', method !== 'upi');
        }

        // Add/Remove Item Rows
        function addItemRow() {
            const tbody = document.getElementById('itemBody');
            const row = document.createElement('tr');
            row.innerHTML = `
            <td><input type="text" name="so_no[]" required></td>
            <td><textarea name="item_description[]" required></textarea></td>
            <td><input type="number" name="quantity[]" min="1" required></td>
            <td><input type="text" name="amount[]" required></td>
            <td><button type="button" style="background: #ff2e0e;" onclick="removeRow(this)">Remove</button></td>
        `;
            tbody.appendChild(row);
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
        }

        // Submit Handler
        document.getElementById('invoiceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const resultDiv = document.getElementById('result');

            resultDiv.innerHTML = '<span style="color: blue;">Processing, please wait...</span>';

            fetch('generate.php', {
                    method: 'POST',
                    body: formData
                })
                .then(async response => {
                    let contentType = response.headers.get("content-type");
                    if (!response.ok) {
                        throw new Error(`HTTP Error ${response.status}`);
                    }

                    if (contentType && contentType.includes("application/json")) {
                        return response.json();
                    } else {
                        throw new Error("Invalid server response: not JSON");
                    }
                })
                .then(data => {
                    if (data.pdf_url) {
                        resultDiv.innerHTML = `<a href="${data.pdf_url}" target="_blank">Download PDF</a>`;
                    } else if (data.error) {
                        resultDiv.innerHTML = `<span style="color:red;">Error: ${data.error}</span>`;
                    } else {
                        resultDiv.innerHTML = '<span style="color:red;">Unknown error occurred. Try again.</span>';
                    }
                })
                .catch(err => {
                    console.error('Submission Error:', err);
                    resultDiv.innerHTML = `<span style="color:red;">An error occurred during submission: ${err.message}</span>`;
                });
        });
    </script>
    <script>
        // Calculate totals when quantity or amount changes
        document.addEventListener('input', function(e) {
            if (e.target.name === 'quantity[]' || e.target.name === 'amount[]') {
                calculateTotals();
            }
        });

        // Convert numbers to words (simplified for integers)
        function numberToWords(num) {
            const a = [
                '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
                'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
                'Seventeen', 'Eighteen', 'Nineteen'
            ];
            const b = [
                '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
            ];

            if (num === 0) return 'Zero';
            if (num < 0) return 'Minus ' + numberToWords(Math.abs(num));

            let words = '';

            if (Math.floor(num / 100000) > 0) {
                words += numberToWords(Math.floor(num / 100000)) + ' Lakh ';
                num %= 100000;
            }
            if (Math.floor(num / 1000) > 0) {
                words += numberToWords(Math.floor(num / 1000)) + ' Thousand ';
                num %= 1000;
            }
            if (Math.floor(num / 100) > 0) {
                words += numberToWords(Math.floor(num / 100)) + ' Hundred ';
                num %= 100;
            }
            if (num > 0) {
                if (num < 20) {
                    words += a[num];
                } else {
                    words += b[Math.floor(num / 10)];
                    if (num % 10 > 0) {
                        words += '-' + a[num % 10];
                    }
                }
            }

            return words.trim();
        }

        function calculateTotals() {
            let subtotal = 0;
            const qtyInputs = document.querySelectorAll('input[name="quantity[]"]');
            const amtInputs = document.querySelectorAll('input[name="amount[]"]');

            for (let i = 0; i < qtyInputs.length; i++) {
                const qty = parseFloat(qtyInputs[i].value) || 0;
                const amt = parseFloat(amtInputs[i].value) || 0;
                subtotal += qty * amt;
            }

            const roundedTotal = Math.round(subtotal);

            document.getElementById('subtotal').value = subtotal.toFixed(2);
            document.getElementById('total').value = roundedTotal.toFixed(2);
            document.getElementById('total_words').value = numberToWords(roundedTotal) + ' Only';

            calculateBalanceDue(); // Also update balance
        }

        function calculateBalanceDue() {
            const total = parseFloat(document.getElementById('total').value) || 0;
            const advance = parseFloat(document.getElementById('advance_payment').value) || 0;
            const balance = total - advance;

            document.getElementById('balance_due').value = balance.toFixed(2);
        }

        document.getElementById('include_qr_code').addEventListener('change', function() {
            const qrWithPricing = document.getElementById('qr_with_pricing');
            qrWithPricing.parentElement.style.display = this.checked ? 'block' : 'none';
        });

        // Initialize visibility on page load
        document.addEventListener('DOMContentLoaded', function() {
            const includeQrCode = document.getElementById('include_qr_code');
            const qrWithPricing = document.getElementById('qr_with_pricing');
            qrWithPricing.parentElement.style.display = includeQrCode.checked ? 'block' : 'none';
        });
    </script>

</body>

</html>