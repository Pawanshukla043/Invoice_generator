<?php
$docRoot = $_SERVER['DOCUMENT_ROOT'];
$siteRoot = "/label";
require_once($docRoot . $siteRoot . '/fpdf/fpdf.php');
require_once($docRoot . $siteRoot . '/fdpi/autoload.php');
require_once($docRoot . $siteRoot . '/vendor/autoload.php');

use \setasign\Fpdi\Fpdi;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', $docRoot . $siteRoot . '/error_log.txt');

function log_message($message)
{
    global $docRoot, $siteRoot;
    error_log(date("[Y-m-d H:i:s]") . " " . $message . "\n", 3, $docRoot . $siteRoot . "/function_flow.log");
}

function cleanAmount($val)
{
    return floatval(preg_replace('/[^\d.]/', '', $val));
}

$pdf = new Fpdi();
$pdf->AddPage();
$pdf->setSourceFile("template-final.pdf");
$tplId = $pdf->importPage(1);
$pdf->useTemplate($tplId, 0, 0);

// --- Form Inputs ---
$company_name     = $_POST['company_name']     ?? '';
$company_address  = $_POST['company_address']  ?? '';
$phone            = $_POST['phone']            ?? '';
$email            = $_POST['email']            ?? '';
$about_us         = 'All your Music 🎶🎧 services at one spot! DM us to booking your Slot! 👆';
$visit_url        = 'www.instagram.com/bluemoonproduction_';

$sender_name      = $_POST['sender_name']     ?? '';
$sender_address   = $_POST['sender_address']  ?? '';
$receiver_name    = $_POST['receiver_name']   ?? '';
$receiver_address = $_POST['receiver_address'] ?? '';

$client_name      = $_POST['client_name']      ?? '';
$client_address   = $_POST['client_address']   ?? '';

$invoice_date     = $_POST['invoice_date']     ?? '';
$terms            = $_POST['terms']            ?? '';
$due_date         = $_POST['due_date']         ?? '';

$so_no_list            = $_POST['so_no']            ?? [];
$item_description_list = $_POST['item_description'] ?? [];
$quantity_list         = $_POST['quantity']         ?? [];
$amount_list           = $_POST['amount']           ?? [];

$subtotal_val     = cleanAmount($_POST['subtotal'] ?? '0');
$total_val        = round($subtotal_val);
$advance_payment  = cleanAmount($_POST['advance_payment'] ?? '0');
$balance_due_val  = $total_val - $advance_payment;
$total_words      = $_POST['total_words'] ?? '';

$upi_name            = $_POST['upi_name']         ?? '';
$upi_id              = $_POST['upi_id']         ?? '';
$account_holder_name = $_POST['account_holder_name']     ?? '';
$account_name        = $_POST['account_name']     ?? '';
$account_number      = $_POST['account_number']   ?? '';
$account_type        = $_POST['account_type']     ?? '';
$ifsc_code           = $_POST['ifsc_code']        ?? '';
$branch              = $_POST['branch']           ?? '';
$swift_code          = $_POST['swift_code']       ?? '';

$terms_conditions = $_POST['terms_conditions'] ?? '';
$include_logo     = $_POST['include_logo'] ?? '';
$include_qr_code  = $_POST['include_qr_code'] ?? '';
$qr_with_pricing  = $_POST['qr_with_pricing'] ?? '';

$billing_items = [];
for ($i = 0; $i < count($so_no_list); $i++) {
    $item_description = $item_description_list[$i] ?? '';
    $amount = cleanAmount($amount_list[$i] ?? 0);
    $billing_items[] = "$item_description: Rs. $amount";
}
$billing_info = implode(", ", $billing_items);

// --- QR Code ---
if (!empty($qr_with_pricing)) {
    $qrData = "BEGIN:VCARD\nVERSION:3.0\n";
    if (!empty($subtotal_val)) $qrData .= "FN: Subtotal: Rs $subtotal_val\n";
    if (!empty($total_val)) $qrData .= "FN: Total: Rs $total_val\n";
    if (!empty($advance_payment)) $qrData .= "FN: Advance Payment: Rs $advance_payment\n";
    if (!empty($balance_due_val)) $qrData .= "FN: Balance Due: Rs $balance_due_val\n";
    if (!empty($upi_name)) {
        if (!empty($upi_name)) $qrData .= "FN: User Name: $upi_name\n";
        if (!empty($upi_id)) $qrData .= "FN: UPI ID: $upi_id\n";
    }
    if (!empty($account_holder_name)) {
        if (!empty($account_holder_name)) $qrData .= "FN: Account Holder: $account_holder_name\n";
        if (!empty($account_number)) $qrData .= "FN: Account Number: $account_number\n";
        if (!empty($ifsc_code)) $qrData .= "FN: IFSC Code: $ifsc_code\n";
    }
    $qrData .= "ITEMS:$billing_info\n"; // Add billing items to QR code
    $qrData .= "END:VCARD";
} else {
    $qrData = "BEGIN:VCARD\nVERSION:3.0\n";
    if (!empty($company_name)) $qrData .= "FN:🏢 $company_name\n"; 
    if (!empty($company_address)) $qrData .= "ADR:;;🏠 $company_address\n"; 
    if (!empty($phone)) $qrData .= "TEL:📞 $phone\n"; 
    if (!empty($email)) $qrData .= "EMAIL:✉️ $email\n"; 
    if (!empty($include_logo)) {
        if (!empty($visit_url)) $qrData .= "URL:🌐 $visit_url\n"; 
        if (!empty($about_us)) $qrData .= "NOTE:ℹ️ $about_us\n"; 
    }
    $qrData .= "ITEMS:$billing_info\n"; // Add billing items to QR code
    $qrData .= "END:VCARD";
}


$qrCode = new QrCode($qrData);
$qrCode->setSize(300);
$qrCode->setMargin(25);

$writer = new PngWriter();
$qrImage = $writer->writeString($qrCode);
$qrFilePath = $docRoot . $siteRoot . '/pdf/temp_qr.png';
file_put_contents($qrFilePath, $qrImage);

// --- Add Logo Overlay to QR ---
$logoPath = $docRoot . $siteRoot . '/new_logo.png';
if (file_exists($logoPath)) {
    $qrImageRes = imagecreatefrompng($qrFilePath);
    $qrWidth = imagesx($qrImageRes);
    $qrHeight = imagesy($qrImageRes);

    $purple = imagecolorallocate($qrImageRes, 128, 0, 128);
    $white = imagecolorallocate($qrImageRes, 255, 255, 255);

    for ($y = 0; $y < $qrHeight; $y++) {
        for ($x = 0; $x < $qrWidth; $x++) {
            $color = imagecolorat($qrImageRes, $x, $y);
            $rgb = imagecolorsforindex($qrImageRes, $color);
            $newColor = ($rgb['red'] == 0 && $rgb['green'] == 0 && $rgb['blue'] == 0) ? $purple : $white;
            imagesetpixel($qrImageRes, $x, $y, $newColor);
        }
    }

    $logoImage = imagecreatefrompng($logoPath);
    $newLogoWidth = 70;
    $newLogoHeight = 70;
    $paddedSize = 80;
    $paddedLogo = imagecreatetruecolor($paddedSize, $paddedSize);
    $white = imagecolorallocate($paddedLogo, 255, 255, 255);
    imagefill($paddedLogo, 0, 0, $white);

    imagecopyresampled($paddedLogo, $logoImage, 5, 5, 0, 0, $newLogoWidth, $newLogoHeight, imagesx($logoImage), imagesy($logoImage));

    $centerX = ($qrWidth - $paddedSize) / 2;
    $centerY = ($qrHeight - $paddedSize) / 2;
    imagecopy($qrImageRes, $paddedLogo, $centerX, $centerY, 0, 0, $paddedSize, $paddedSize);
    imagepng($qrImageRes, $qrFilePath);
    imagedestroy($qrImageRes);
    imagedestroy($logoImage);
    imagedestroy($paddedLogo);
}

// Set a new left margin
$leftMargin = 20; // Adjust this value as needed

// --- Background ---
$pdf->SetFillColor(240, 240, 240);
$pdf->Rect(5, 5, 200, 287, 'F');

$pdf->SetFont('Helvetica', 'B', 18);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(0, 10, "INVOICE", 0, 1, 'C'); // Centered header

$pdf->SetFont('Helvetica', '', 10);
$pdf->SetTextColor(0, 0, 0);
$lineY = 30;
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Text($leftMargin, 30, "Bill From:");
$lineY += 5;
$pdf->SetFont('Helvetica', '', 10);

if (!empty($sender_name))     $pdf->Text($leftMargin, $lineY, $sender_name);
if (!empty($sender_name))      $lineY += 5;
if (!empty($sender_address))  $pdf->Text($leftMargin, $lineY, $sender_address);
if (!empty($sender_address))   $lineY += 5;
if (!empty($company_name))    $pdf->Text($leftMargin, $lineY, $company_name);
if (!empty($company_name))     $lineY += 5;
if (!empty($company_address)) $pdf->Text($leftMargin, $lineY, $company_address);
if (!empty($company_address))  $lineY += 5;
if (!empty($phone))           $pdf->Text($leftMargin, $lineY, "Phone: $phone");
if (!empty($phone))   $lineY += 5;
if (!empty($email))           $pdf->Text($leftMargin, $lineY, "Email: $email");

// --- Bluemoon Logo ---
$logoPath = $docRoot . $siteRoot . '/bluemoon_with_black_bg.png';
if (!empty($include_logo)) {
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 150, 25, 35, 35, 'PNG');
    }
}

// --- Billing & Invoice Info ---
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Text($leftMargin, 60, "Bill To:");
$pdf->SetFont('Helvetica', '', 10);
$by = 65;
if (!empty($receiver_name))    $pdf->Text($leftMargin, $by, $receiver_name);
if (!empty($receiver_name))   $by += 5;
if (!empty($receiver_address)) $pdf->Text($leftMargin, $by, $receiver_address);
if (!empty($client_name))      $pdf->Text($leftMargin, $by, $client_name);
if (!empty($client_name))    $by += 5;
if (!empty($client_address))   $pdf->Text($leftMargin, $by, $client_address);

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Text(150, 60, "Invoice Details:");
$pdf->SetFont('Helvetica', '', 10);
$dy = 65;
if (!empty($invoice_date)) $pdf->Text(150, $dy, "Invoice Date: $invoice_date");
if (!empty($invoice_date)) $dy += 5;
if (!empty($terms))        $pdf->Text(150, $dy, "Terms: $terms");
if (!empty($terms))        $dy += 5;
if (!empty($due_date))     $pdf->Text(150, $dy, "Due Date: $due_date");

// --- Items Table ---
$pdf->SetXY($leftMargin - 5, 90);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetFillColor(0, 176, 240);

// Table Header
$pdf->Cell(20, 10, "Sl.No", 1, 0, 'C', true);
$pdf->Cell(100, 10, "Item & Description", 1, 0, 'C', true);
$pdf->Cell(20, 10, "Qty", 1, 0, 'C', true);
$pdf->Cell(40, 10, "Amount", 1, 1, 'C', true);

$pdf->SetFont('Helvetica', '', 10);

// Table Rows
for ($i = 0; $i < count($so_no_list); $i++) {
    $so    = $so_no_list[$i] ?? '';
    $desc  = $item_description_list[$i] ?? '';
    $qty   = $quantity_list[$i] ?? '';
    $amt   = cleanAmount($amount_list[$i] ?? 0);
    $amt_f = number_format($amt, 2);

    // Save the current X and Y positions
    $currentX = $pdf->GetX();
    $currentY = $pdf->GetY();

    // Serial Number
    $pdf->SetXY($leftMargin - 5, $currentY);
    $pdf->Cell(20, 10, $so, 1, 0, 'C');

    // Item Description (MultiCell)
    $pdf->SetXY($leftMargin - 5 + 20, $currentY);
    $pdf->MultiCell(100, 10, wordwrap($desc, 60, "\n", true), 1, 'L');

    // Adjust Y position for the next cells if MultiCell height exceeds 10
    $newY = $pdf->GetY();
    $rowHeight = $newY - $currentY;

    // Quantity
    $pdf->SetXY($leftMargin - 5 + 120, $currentY);
    $pdf->Cell(20, $rowHeight, $qty, 1, 0, 'C');

    // Amount
    $pdf->SetXY($leftMargin - 5 + 140, $currentY);
    $pdf->Cell(40, $rowHeight, "Rs. $amt_f", 1, 1, 'C');
}
// --- Totals ---
$pdf->SetFont('Helvetica', 'B', 10);

// Remove extra space between the items table and totals
$pdf->SetXY($leftMargin - 5, $pdf->GetY() + 2); // Adjust the Y position slightly

// Subtotal
$pdf->Cell(140, 10, "Subtotal", 1, 0, 'R');
$pdf->Cell(40, 10, "Rs. " . number_format($subtotal_val, 2), 1, 1, 'C');
$pdf->SetXY($leftMargin - 5, $pdf->GetY() + 2); // Adjust the Y position slightly

// Advance Payment
$pdf->Cell(140, 10, "Advance Payment", 1, 0, 'R');
$pdf->Cell(40, 10, "Rs. " . number_format($advance_payment, 2), 1, 1, 'C');
$pdf->SetXY($leftMargin - 5, $pdf->GetY() + 2); // Adjust the Y position slightly

// Balance Due
$pdf->Cell(140, 10, "Balance Due", 1, 0, 'R');
$pdf->Cell(40, 10, "Rs. " . number_format($balance_due_val, 2), 1, 1, 'C');
$pdf->SetXY($leftMargin - 5, $pdf->GetY() + 2); // Adjust the Y position slightly

// Total
$pdf->Cell(140, 10, "Total", 1, 0, 'R');
$pdf->Cell(40, 10, "Rs. " . number_format($total_val, 2), 1, 1, 'C');
$pdf->SetXY($leftMargin - 5, $pdf->GetY() + 2); // Adjust the Y position slightly

// Total in Words
$pdf->SetFont('Helvetica', 'B', 10);
if (!empty($total_words)) {
    $pdf->SetXY($leftMargin, $pdf->GetY() + 5); // Add a small gap before the total in words
    $pdf->MultiCell(190 - $leftMargin, 5, "Total in Words: Rupees $total_words", 0, 'L');
}
// --- Bank / UPI Details ---
$bankY = $pdf->GetY() + 20;
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Text($leftMargin, $bankY, "Account Details:");
$pdf->SetFont('Helvetica', '', 10);
$bankY += 5;
if (!empty($upi_name))       $pdf->Text($leftMargin, $bankY, "UPI Account Name: $upi_name");
$bankY += 5;
if (!empty($upi_id))         $pdf->Text($leftMargin, $bankY, "UPI ID: " . $upi_id);
$bankY += 5;
if (!empty($account_name))   $pdf->Text($leftMargin, $bankY, "Account Name: $account_name");
$bankY += 5;
if (!empty($account_number)) $pdf->Text($leftMargin, $bankY, "Account Number: $account_number");
$bankY += 5;
if (!empty($account_type))   $pdf->Text($leftMargin, $bankY, "Account Type: $account_type");
$bankY += 5;
if (!empty($ifsc_code))      $pdf->Text($leftMargin, $bankY, "IFSC Code: $ifsc_code");
$bankY += 5;
if (!empty($branch))         $pdf->Text($leftMargin, $bankY, "Branch: $branch");
$bankY += 5;
if (!empty($swift_code))     $pdf->Text($leftMargin, $bankY, "SWIFT Code: $swift_code");

// --- Terms & Conditions ---
if (!empty($terms_conditions)) {
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->Text($leftMargin, $bankY + 10, "Terms & Conditions:");
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetXY($leftMargin, $bankY + 15);
    $pdf->MultiCell(190 - $leftMargin, 5, wordwrap($terms_conditions, 100, "\n", true));
}

// --- QR Code ---
if (!empty($include_qr_code)) {
    $pdf->Image($qrFilePath, 150, $pdf->GetY() - 50, 35, 35, 'PNG');
}

// --- Output ---
$t = time();
$file = date("dmY-is", $t) . ".pdf";
$file_url = $siteRoot . '/pdf/' . $file;
$filename = $docRoot . $siteRoot . '/pdf/' . $file;

$pdf->Output($filename, 'F');
header('Content-Type: application/json');
echo json_encode(["pdf_url" => $file_url]);
log_message('PDF created successfully');
exit;
