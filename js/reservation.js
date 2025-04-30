// Constants
const ROOM_PRICE_PER_DAY = 400;
const TAX_RATE = 0.13; // 13%
const EQUIPMENT_PRICES = {
    whiteboard: 100.0,
    VRH: 500.0,
    projector: 300.0,
    printer: 150.0,
    screen: 200.0,
    microphone: 50.0,
    speaker: 75.0,
    camera: 400
};

let total = 0;



document.addEventListener('DOMContentLoaded', () => {

    // Form elements
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const guestsInput = document.getElementById('guests');
    const equipmentCheckboxes = document.querySelectorAll('.equipment-list .form-check-input');
    const bookButton = document.getElementById('bookButton');

    // Summary elements
    const summaryCheckIn = document.getElementById('summaryCheckIn');
    const summaryCheckOut = document.getElementById('summaryCheckOut');
    const summaryDuration = document.getElementById('summaryDuration');
    const summaryEquipment = document.getElementById('summaryEquipment');
    const roomSummary = document.getElementById('roomSummary');
    const basePrice = document.getElementById('basePrice');
    const extrasPrice = document.getElementById('extrasPrice');
    const taxAmount = document.getElementById('taxAmount');
    const totalPrice = document.getElementById('totalPrice');
    const cardholderNameInput = document.getElementById('cardholderName');
    const cardNumberInput = document.getElementById('cardNumber');
    const expiryInput = document.getElementById('expiry');
    const cvvInput = document.getElementById('cvv');


    fetch('fetchNbEquip.php', { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            initializeEquipmentInputs(data);
        })
        .catch(error => {
            console.error('Error fetching equipment data:', error);
        });

    function initializeEquipmentInputs(data) {
        console.log("Initializing equipment inputs...");
        equipmentCheckboxes.forEach(checkbox => {
            const equipmentId = checkbox.value;
            const numberInput = document.getElementsByClassName(equipmentId)[0];
            let availableNumber = data[equipmentId] ?? 0;
            console.log(equipmentId + "+" + availableNumber)

            if (availableNumber <= 0) {
                checkbox.disabled = true;
            } else {
                if (!checkbox.checked) {
                    numberInput.disabled = true;
                }
            }
        });
    }

    // Set minimum date as today
    const today = new Date().toISOString().split('T')[0];
    startDateInput.min = today;
    endDateInput.min = today;

    // Event Listeners
    startDateInput.addEventListener('change', updateDateRange);
    endDateInput.addEventListener('change', updateDateRange);
    guestsInput.addEventListener('input', validateGuests);

    // ✅ Important: Updated checkbox event listener
    equipmentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const equipmentId = checkbox.value;
            const numberInput = document.getElementsByClassName(equipmentId)[0];

            if (checkbox.checked) {
                if (numberInput) numberInput.disabled = false;
            } else {
                if (numberInput) {
                    numberInput.disabled = true;
                    numberInput.value = '';
                }
            }

            updatePricing();
        });
    });

    

    // Form Validation
    function validateGuests() {
        const guests = parseInt(guestsInput.value) || 0;
        if (guests > 16) {
            guestsInput.value = 16;
            showAlert('Maximum 16 guests allowed');
        } else if (guests < 0) {
            guestsInput.value = 0;
        }
        updatePricing();
        updateGuestSummary();
    }

    // Update Guest Summary
    function updateGuestSummary() {
        const guests = parseInt(guestsInput.value) || 0;
        roomSummary.textContent = `Meeting Room A (${guests} Guest${guests !== 1 ? 's' : ''}):`;
        basePrice.textContent = `$${guests * ROOM_PRICE_PER_DAY}`;
    }

    // Date Range Management
    function updateDateRange() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate > endDate) {
            endDateInput.value = startDateInput.value;
        }

        endDateInput.min = startDateInput.value;

        if (startDateInput.value) {
            summaryCheckIn.textContent = formatDate(startDate);
        }
        if (endDateInput.value) {
            summaryCheckOut.textContent = formatDate(endDate);
        }

        if (startDateInput.value && endDateInput.value) {
            let days = calculateDays(startDate, endDate);
            if (days === 0) days = 1; // Minimum 1 day
            summaryDuration.textContent = `${days} Day${days > 1 ? 's' : ''}`;
        }

        updatePricing();
    }

    // Pricing Calculations
    function updatePricing() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const days = calculateDays(startDate, endDate);
    
        const base = days * ROOM_PRICE_PER_DAY;
        const newBase = base * (parseInt(guestsInput.value) || 0);
        basePrice.textContent = `$${newBase}`;
    
        // Calculate extras
        let extras = 0;
        const selectedEquipment = [];
    
        equipmentCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const equipmentId = checkbox.id;
                const numberInput = document.getElementsByClassName(equipmentId)[0];
                const equipmentQuantity = parseInt(numberInput.value) || 0;
    
                if (equipmentQuantity > 0) {
                    extras += EQUIPMENT_PRICES[equipmentId] * equipmentQuantity; // Multiply price by quantity
                    selectedEquipment.push(checkbox.nextElementSibling.textContent);
                }
            }
        });
    
        extrasPrice.textContent = `$${extras}`;
    
        updateEquipmentList(selectedEquipment);
    
        const subtotal = newBase + extras;
        const tax = subtotal * TAX_RATE;
        taxAmount.textContent = `$${tax.toFixed(2)}`;
    
        total = subtotal + tax;
        totalPrice.textContent = `$${total.toFixed(2)}`;
    
        updateGuestSummary();
    }
    

    function updateEquipmentList(selectedEquipment) {
        summaryEquipment.innerHTML = selectedEquipment.length
            ? selectedEquipment.map(item => `<li>${item}</li>`).join('')
            : '<li class="text-muted">No equipment selected</li>';
    }

    function calculateDays(start, end) {
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays || 1;
    }

    function formatDate(date) {
        return date.toLocaleDateString('en-US', {
            weekday: 'short',
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    function showAlert(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        alert.style.zIndex = '1050';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 3000);
    }

    // Booking Submission
    bookButton?.addEventListener('click', (e) => {
        e.preventDefault();

        if (!startDateInput.value || !endDateInput.value) {
            showAlert('Please select check-in and check-out dates');
            return;
        } else {
            const startDateTime = new Date(startDateInput.value);
            const startHours = startDateTime.getHours();
            if (startHours >= 23) {
                showAlert('Start time cannot be later than 11 PM.');
                startDateInput.value = '';
                return;
            }
            if (startHours < 9) {
                showAlert('Start time cannot be earlier than 9 AM.');
                startDateInput.value = '';
                return;
            }
            const endDateTime = new Date(endDateInput.value);
            const endHours = endDateTime.getHours();

            if (endHours >= 23) {
                showAlert('End time cannot be later than 11 PM.');
                endDateInput.value = '';
                return;
            }
            if (endHours < 9) {
                showAlert('End time cannot be earlier than 9 AM.');
                endDateInput.value = '';
                return;
            }
        }
        if (!guestsInput.value) {
            showAlert('Please enter number of guests');
            return;
        }
    
        // Validate payment if not cash
        
            if (!cardholderNameInput.value) {
                showAlert('Please enter cardholder name');
                return;
            }
            if (!cardNumberInput.value || cardNumberInput.value.replace(/\s/g, '').length !== 16) {
                showAlert('Please enter a valid 16-digit card number');
                return;
            }
            if (!expiryInput.value || !expiryInput.value.includes('/')) {
                showAlert('Please enter a valid expiry date (MM/YY)');
                return;
            }
            if (!cvvInput.value || cvvInput.value.length !== 3) {
                showAlert('Please enter a valid CVV');
                return;
            }

        fetch('../controller/fetchNbEquip.php', { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                if (!data) {
                    console.log('No data found');
                    return;
                }

                let isValid = true;
                equipmentCheckboxes.forEach(checkbox => {
                    let equipmentId = checkbox.value;
                    let numberInput = document.getElementsByClassName(equipmentId)[0];

                    if (checkbox.checked) {
                        numberInput.disabled = false;
                        let inputNumber = parseInt(numberInput.value) || 0;
                        let availableNumber = data[equipmentId];

                        if (inputNumber > availableNumber) {
                            showAlert(`You cannot enter more than ${availableNumber} for ${equipmentId}`);
                            numberInput.value = availableNumber;
                            isValid = false;
                        }
                    }

                });

                if (!isValid) {
                    return;
                } else {
                    document.getElementById("bookingForm").submit();
                }
            })
            .catch(error => {
                console.error('Error fetching equipment data:', error);
                showAlert('Error checking equipment availability');
            });
    });

});
