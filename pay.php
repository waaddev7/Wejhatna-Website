<?php
session_start();
if (!isset($_SESSION['booking_details'])) {
    header("Location: trips.php?error=no_booking_initiated");
    exit;
}
$booking_details = $_SESSION['booking_details'];
$trip_name = htmlspecialchars($booking_details['trip_name']);
$num_people = htmlspecialchars($booking_details['num_people']);
$total_price = htmlspecialchars($booking_details['total_price']);
$price_per_person = $num_people > 0 ? ($total_price / $num_people) : 0;
$price_per_person_formatted = number_format($price_per_person, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Payment</title>
    <link rel="stylesheet" href="stylepay.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="payment-summary">
        <h1>Payment</h1>
        <div class="trip-details">
            <div class="form-group">
                <label>Trip Name</label>
                <input type="text" value="<?= $trip_name; ?>" readonly>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Price / Person</label>
                    <input type="text" value="₪<?= $price_per_person_formatted; ?>" readonly>
                </div>
                <div class="form-group">
                    <label>No. of People</label>
                    <input type="text" value="<?= $num_people; ?>" readonly>
                </div>
            </div>
        </div>
        <div class="amount-details">
            <div class="detail-row">
                <span>Amount:</span>
                <span>₪<?= $total_price; ?></span>
            </div>
            <div class="detail-row total">
                <span>Amount to pay:</span>
                <span>₪<?= $total_price; ?></span>
            </div>
        </div>
        <img src="image/payy.jpg" alt="Credit Card Illustration" class="credit-card-promo-img">
    </div>

    <div class="card-details">
        <h2>Card Details</h2>
        <form action="book_trip.php" method="POST" id="payment-form">
            <div class="form-group">
                <label for="cardholder-name">Cardholder's Name</label>
                <input type="text" id="cardholder-name" name="cardholder-name" placeholder="your name" required>
            </div>
            <div class="form-group">
                <label for="card-number">Card number</label>
                <div class="input-with-icon">
                    <input type="text" id="card-number" name="card-number" placeholder="xxxx xxxx xxxx xxxx" required maxlength="19">
                </div>
            </div>

            <!-- ▼▼▼ تم تعديل هذا الجزء بالكامل ▼▼▼ -->
            <div class="form-group">
                <label for="phone-number-suffix">Phone Number</label>
                <div class="phone-input-group">
                    <span class="phone-prefix">+972</span>
                    <input type="tel" id="phone-number-suffix" name="phone-number-suffix" placeholder="599123456" required maxlength="9">
                </div>
                <!-- حقل مخفي لإرسال الرقم الكامل إلى الخادم -->
                <input type="hidden" id="phone-number" name="phone-number">
            </div>
            <!-- ▲▲▲ نهاية الجزء المعدل ▲▲▲ -->

            <div class="form-row">
                <div class="form-group">
                    <label for="expiry-month">Expiry Date</label>
                    <div class="select-group">
                        <select id="expiry-month" name="expiry-month" required></select>
                        <select id="expiry-year" name="expiry-year" required></select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="cvc">CVC/CVV</label>
                    <input type="password" id="cvc" name="cvc" placeholder="•••" required maxlength="3">
                </div>
            </div>
            <button type="submit" class="pay-button">Pay now</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- كود ملء تاريخ الانتهاء (يبقى كما هو) ---
        const monthSelect = document.getElementById('expiry-month');
        const yearSelect = document.getElementById('expiry-year');
        const currentYear = new Date().getFullYear();
        const currentMonth = new Date().getMonth() + 1;
        for (let i = 0; i <= 15; i++) {
            const year = currentYear + i;
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
        }
        function populateMonths(selectedYear) {
            monthSelect.innerHTML = '';
            const startMonth = (parseInt(selectedYear) === currentYear) ? currentMonth : 1;
            for (let i = startMonth; i <= 12; i++) {
                const option = document.createElement('option');
                const monthString = i.toString().padStart(2, '0');
                option.value = monthString;
                option.textContent = monthString;
                monthSelect.appendChild(option);
            }
        }
        yearSelect.addEventListener('change', function () { populateMonths(this.value); });
        populateMonths(currentYear);

        // --- كود التحقق من صحة الإدخال (تم تحديثه) ---
        const paymentForm = document.getElementById('payment-form');
        const cardholderNameInput = document.getElementById('cardholder-name');
        const cardNumberInput = document.getElementById('card-number');
        const phoneNumberSuffixInput = document.getElementById('phone-number-suffix');
        const fullPhoneNumberInput = document.getElementById('phone-number');
        const cvcInput = document.getElementById('cvc');

        paymentForm.addEventListener('submit', function (event) {
            // دمج رقم الهاتف قبل التحقق والإرسال
            fullPhoneNumberInput.value = '+972' + phoneNumberSuffixInput.value;

            // 1. التحقق من اسم حامل البطاقة (أحرف ومسافات فقط)
            const nameRegex = /^[a-zA-Z\u0600-\u06FF\s]+$/;
            if (!nameRegex.test(cardholderNameInput.value)) {
                alert("Cardholder's Name must contain only letters and spaces.");
                event.preventDefault(); return;
            }

            // 2. التحقق من رقم البطاقة (16 رقمًا بالضبط)
            const cardNumberValue = cardNumberInput.value.replace(/\s/g, '');
            const numbersOnlyRegex = /^[0-9]+$/;
            if (!numbersOnlyRegex.test(cardNumberValue) || cardNumberValue.length !== 16) {
                alert("Card Number must be exactly 16 digits.");
                event.preventDefault(); return;
            }

            // 3. التحقق من رقم الهاتف (9 أرقام بالضبط بعد +972)
            const phoneSuffixValue = phoneNumberSuffixInput.value;
            if (!numbersOnlyRegex.test(phoneSuffixValue) || phoneSuffixValue.length !== 9) {
                alert("Phone Number must be exactly 9 digits after +972.");
                event.preventDefault(); return;
            }

            // 4. التحقق من CVC (3 أرقام بالضبط)
            const cvcValue = cvcInput.value;
            if (!numbersOnlyRegex.test(cvcValue) || cvcValue.length !== 3) {
                alert("CVC/CVV must be exactly 3 digits.");
                event.preventDefault(); return;
            }
        });
    });
</script>

</body>
</html>