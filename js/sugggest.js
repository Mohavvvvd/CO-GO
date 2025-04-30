document.getElementById("reservationForm").addEventListener("submit", async (e) => {
  e.preventDefault();
  const form = e.target;
  const box = document.getElementById("result");
  const submitBtn = form.querySelector('button[type="submit"]');
  
  try {
    box.innerHTML = '<div class="alert alert-info">⏳ Checking availability...</div>';
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Checking';

    const desiredStart = document.getElementById("desiredStart").value;
    const equipmentInput = document.getElementById("requiredEquipment").value.toLowerCase();
    
    // Validate inputs
    if (!desiredStart) throw new Error("Please select a start date/time");
    if (!equipmentInput.trim()) throw new Error("Please list required equipment");

    const selectedDate = new Date(desiredStart);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
      throw new Error("The selected date/time cannot be in the past.");
    }

    const selectedHours = selectedDate.getHours();
const selectedMinutes = selectedDate.getMinutes();
const selectedTimeInMinutes = selectedHours * 60 + selectedMinutes;

const minTimeInMinutes = 9 * 60;  
const maxTimeInMinutes = 23 * 60;  

if (selectedTimeInMinutes < minTimeInMinutes || selectedTimeInMinutes > maxTimeInMinutes) {
  throw new Error("The selected time must be between 9:00 AM and 11:00 PM.");
}


    const equipmentList = equipmentInput.split(",")
      .map(s => s.trim())
      .filter(Boolean);

    // API call
    const response = await fetch("../controller/aiFetchReservation.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        desired_start: desiredStart,
        required_equipment: equipmentList,
      }),
    });

    const data = await response.json().catch(() => null);
    
    // Handle specific status codes first
    if (response.status === 404) {
      box.innerHTML = `
        <div class="alert alert-info">
          ℹ️ ${escapeHtml(data?.message || 'No availability found')}
        </div>`;
      announceResult(data?.message || 'No availability found');
      return;
    }

    if (!response.ok) {
      throw new Error(data?.message || `Server error: ${response.status}`);
    }

    console.log("API response:", data);

    // Determine response type
    let html;
    if (data.immediate_availability) {
      html = createSuccessHtml(data);
    } 
    else if (data.next_availability) {
      html = createNextAvailabilityHtml(data);
    } 
    else if (data.unavailable_equipment?.length) {
      html = createUnavailableHtml(data);
    } 
    else if (data.occupied_equipment) {
      html = createConflictHtml(data, equipmentList);
    } 
    else {
      html = '<div class="alert alert-info">ℹ️ No availability information found</div>';
    }

    box.innerHTML = html;
    announceResult(data.message || "Availability check complete");

  } catch (error) {
    console.error("Error:", error);
    const message = escapeHtml(error.message || "An unexpected error occurred");
    box.innerHTML = `
      <div class="alert alert-danger">
        ❌ ${message}
        ${error.info ? `<div class="mt-2 small">${error.info}</div>` : ''}
      </div>`;
    announceResult(`Error: ${message}`);
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = 'Check Availability';
  }
});

// Response handlers
function createNextAvailabilityHtml(data) {
  let html = `
    <div class="alert alert-warning">
      <h4 class="alert-heading">⏳ Equipment Not Immediately Available</h4>
      <p>Next available times for required equipment:</p>`;
  
  data.next_availability.forEach(item => {
    html += `
      <div class="mt-2 p-2 bg-light rounded">
        <strong>${escapeHtml(item.equipment)}</strong><br>
        <span class="text-muted small">
          ${formatDateTime(new Date(item.next_available_at))}
        </span>
      </div>`;
  });
  
  html += `
    <hr>
    <p class="mb-0">Click below to use the next available time:</p>
    <button class="btn btn-sm btn-warning mt-2" 
      onclick="document.getElementById('desiredStart').value = '${data.next_availability[0].next_available_at}'">
      Set to ${formatDateTime(new Date(data.next_availability[0].next_available_at))}
    </button>
  </div>`;
  
  return html;
}

function createUnavailableHtml(data) {
  return `
    <div class="alert alert-danger">
      <h4 class="alert-heading">❌ Equipment Unavailable</h4>
      <p>The following equipment is currently unavailable:</p>
      <ul class="mb-3">
        ${data.unavailable_equipment.map(e => `
          <li>${escapeHtml(e)}</li>
        `).join('')}
      </ul>
      ${data.message ? `<p>${escapeHtml(data.message)}</p>` : ''}
    </div>`;
}

function createConflictHtml(data, requiredEquipment) {
  const endTime = new Date(data.end);
  const occupiedSet = new Set(data.occupied_equipment.map(eq => eq.toLowerCase()));
  
  const available = requiredEquipment.filter(eq => !occupiedSet.has(eq.toLowerCase()));
  const conflicts = requiredEquipment.filter(eq => occupiedSet.has(eq.toLowerCase()));

  return `
    <div class="alert alert-warning">
      <h4 class="alert-heading">⚠️ Partial Availability</h4>
      <div class="row">
        <div class="col-md-6">
          <h5>Available Now:</h5>
          ${available.length ? `
            <ul class="list-group">
              ${available.map(eq => `
                <li class="list-group-item">
                  ✅ ${escapeHtml(eq)}
                </li>
              `).join('')}
            </ul>
          ` : '<p class="text-muted">No equipment available immediately</p>'}
        </div>
        
        <div class="col-md-6">
          <h5>Conflicts:</h5>
          ${conflicts.length ? `
            <ul class="list-group">
              ${conflicts.map(eq => `
                <li class="list-group-item">
                  ⚠️ ${escapeHtml(eq)}
                  <div class="text-muted small">
                    Available after ${formatDateTime(endTime)}
                  </div>
                </li>
              `).join('')}
            </ul>
          ` : '<p class="text-muted">No conflicts found</p>'}
        </div>
      </div>

      <hr>
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <strong>Next full availability:</strong><br>
          ${formatDateTime(endTime)}
        </div>
        <button class="btn btn-primary" 
          onclick="document.getElementById('desiredStart').value = '${endTime.toISOString()}'">
          Use This Time
        </button>
      </div>
    </div>`;
}

function createSuccessHtml(data) {
  const equipment = data.required_equipment || [];
  let equipmentItems = '';
  
  // Using for loop instead of map
  if (equipment.length > 0) {
    for (let i = 0; i < equipment.length; i++) {
      equipmentItems += `
        <li class="list-group-item bg-transparent">
          ✅ ${escapeHtml(equipment[i])}
        </li>`;
    }
  } else {
    equipmentItems = '<li class="list-group-item bg-transparent">All equipment are available</li>';
  }

  return `
    <div class="alert alert-success">
      <h4 class="alert-heading">✅ All Equipment Available!</h4>
      <p>Your requested equipment is available at the selected time:</p>
      <ul class="list-group mb-3">
        ${equipmentItems}
      </ul>
      <hr>
      <div class="d-flex justify-content-between align-items-center">
        <span>Ready to proceed with booking?</span>
        <button class="btn btn-success">Confirm Reservation</button>
      </div>
    </div>`;
}

// Utilities
function formatDateTime(date) {
  return date.toLocaleString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  });
}

function escapeHtml(unsafe) {
  return unsafe.toString()
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function announceResult(message) {
  const ariaLive = document.createElement('div');
  ariaLive.setAttribute('aria-live', 'polite');
  ariaLive.style.position = 'absolute';
  ariaLive.style.left = '-9999px';
  document.body.appendChild(ariaLive);
  ariaLive.textContent = message;
  setTimeout(() => ariaLive.remove(), 1000);
}