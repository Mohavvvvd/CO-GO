
  window.addEventListener("DOMContentLoaded", async () => {
    const originalData = {};
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const nomInput = document.getElementById('nom');
    const prenomInput = document.getElementById('prenom');
    const inputs = [nameInput, emailInput, phoneInput, nomInput, prenomInput];

    await fetch('../controller/getUserInfo.php')
      .then(response => response.json())
      .then(data => {
        if (data.message && typeof data.message === 'object') {
          const user = data.message;

          nameInput.value = originalData.username = user.username || '';
          emailInput.value = originalData.email = user.email || '';
          phoneInput.value = originalData.telephone = user.telephone || '';
          nomInput.value = originalData.nom = user.nom || '';
          prenomInput.value = originalData.prenom = user.prenom || '';
        } else {
          alert("Error: " + (data.message || "Failed to load user data."));
        }
      })
      .catch(error => {
        console.error('Error fetching user data:', error);
        alert("An error occurred while fetching user data.");
      });

    const editBtn = document.getElementById('editBtn');
    const saveBtn = document.getElementById('saveBtn');

    editBtn.addEventListener('click', () => {
      inputs.forEach(input => input.disabled = false);
      saveBtn.classList.remove('d-none');
      editBtn.classList.add('d-none');
    });

    document.getElementById('userForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const currentData = {
        username: nameInput.value,
        email: emailInput.value,
        telephone: phoneInput.value,
        nom: nomInput.value,
        prenom: prenomInput.value
      };

      const updatedFields = {};
      for (const key in currentData) {
        if (currentData[key] !== originalData[key]) {
          updatedFields[key] = currentData[key];
        }
      }

      if (Object.keys(updatedFields).length === 0) {
        alert('No changes detected.');
        return;
      }

      const password = prompt("Please enter your password to confirm:");

      if (!password) {
        alert("Password is required.");
        return;
      }

      updatedFields.password = password;

      try {
        const res = await fetch('../controller/update.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(updatedFields)
        });

        const result = await res.json();

        if (res.ok) {
          alert("Profile updated successfully.");
          location.reload();
        } else {
          alert("Error: " + result.message);
        }
      } catch (err) {
        console.error('Update error:', err);
        alert("An error occurred while updating.");
      }
    });
  });

