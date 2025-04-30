document.querySelector("form").addEventListener("submit", async function (e) {
    e.preventDefault();

    const checkbox = document.querySelector("input[type='checkbox']");
    if (!checkbox.checked) {
        alert("You must accept the terms before submitting!");
        return;
    }

    const formData = new FormData(this);

    await fetch("../controller/contactHandler.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {
            showModal(data.msg,'green');
            this.reset();
        } else {
            showModal(data.msg,'red');
        }
    })
    .catch(err => {
        alert("Something went wrong. Please try again.");
        console.error(err);
    });
});

function showModal(text,color) {
    document.getElementById("successModal").style.display = "flex";
    document.getElementById("text").textContent = text;
    document.getElementById("text").style.color = color;
}

function closeModal() {
    document.getElementById("successModal").style.display = "none";
}
