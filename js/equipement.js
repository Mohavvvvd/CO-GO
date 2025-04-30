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

document.addEventListener("DOMContentLoaded", function () {
    fetch('fetchNbEquip.php')
        .then(response => response.json())  // Parse the JSON data
        .then(data => {
            if (data) {
                let isValid = true; 

                console.log(data);  

                const equipmentCheckboxes = document.querySelectorAll('.equipment-list .form-check-input');

                // Loop over each checkbox to check if it is checked
                equipmentCheckboxes.forEach(checkbox => {

                        let equipmentId = checkbox.value;
                        let numberInput = document.getElementsByClassName(equipmentId)[0];

                        if(data[equipmentId] == 0) {
                            checkbox.disabled = true;
                            numberInput.disabled = true;
                        }
                    
                });

            } else {
                console.log('No data found');
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
});


document.getElementById('bookButton').addEventListener('click', function(event) {
    event.preventDefault();

    fetch('fetchNbEquip.php')
        .then(response => response.json())  // Parse the JSON data
        .then(data => {
            if (data) {
                let isValid = true;  // Flag to track if the form is valid

                console.log(data);  // Logs the fetched data

                // Select all checkboxes and number inputs
                const equipmentCheckboxes = document.querySelectorAll('.equipment-list .form-check-input');

                // Loop over each checkbox to check if it is checked
                equipmentCheckboxes.forEach(checkbox => {

                    // If the checkbox is checked, validate the input number
                    if (checkbox.checked) {
                        let equipmentId = checkbox.value;
                        let numberInput = document.getElementsByClassName(equipmentId)[0];
                        console.log(numberInput);
                        let inputNumber = parseInt(numberInput.value) || 0;
                        let availableNumber = data[equipmentId];  // Available quantity from the database
                        console.log(inputNumber);
                        console.log(availableNumber);

                        // Compare the input number with the available quantity
                        if (inputNumber > availableNumber) {
                            showAlert(`You cannot enter more than ${availableNumber} for ${equipmentId}`);
                            numberInput.value = availableNumber;  // Optionally reset input to max available value
                            isValid = false;  // Set form as invalid
                        }
                    }
                });

                // If form is valid, you can submit it or continue with your process
                if (isValid) {
                    console.log('Form is valid, submitting...');
                    // You can add the form submission logic here
                    // For example: document.getElementById('yourForm').submit();
                    // document.getElementById('bookingForm').submit();
                    // showAlert(`Reservation is sucessfully registred !`);
                }
            } else {
                console.log('No data found');
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
});
