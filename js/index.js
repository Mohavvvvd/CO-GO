window.addEventListener("DOMContentLoaded",()=>{

console.log('email :'+localStorage.getItem('email'));
console.log('password :'+localStorage.getItem('password'));
const storedEmail = localStorage.getItem('email');
const storedPassword = localStorage.getItem('password');

if (storedEmail) document.getElementById('email1').value = storedEmail;
if (storedPassword) document.getElementById('password1').value = storedPassword;

const formS = document.getElementById('signupForm');
const formL = document.getElementById('logInForm');

formS.addEventListener("submit", SignUp);
formL.addEventListener("submit", LogIn);

document.addEventListener('hidden.bs.modal', function () {
    setTimeout(() => {
      const anyModalOpen = document.querySelector('.modal.show');
      if (!anyModalOpen) {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style = ""; 
      }
    }, 300); 
  })

  


});

const SignUpCheck = () =>{
  const elements = document.querySelectorAll('#signupForm [name]');
  let isValid = true;

  elements.forEach(el => {
    const type = el.type;
    const name = el.name;
    const value = el.value.trim();

    if (type !== 'checkbox') {
      if (value === "" && value.length < 5) {
        el.classList.add('is-invalid');
        isValid = false;
      } else {
        el.classList.remove('is-invalid');
      }
    }

    if (name === 'email') {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        el.classList.add('is-invalid');
        isValid = false;
      }
    }

    if (name === 'password') {
      if (value.length < 8) {
        el.classList.add('is-invalid');
        isValid = false;
      }
    }

    if (name === 'phone') {
      const phoneRegex = /^\d{8}$/;
      if (!phoneRegex.test(value.toString())) {
        el.classList.add('is-invalid');
        isValid = false;
      }
    }
  });

  return isValid; 
}
function switchModal(currentId, targetId) {
  const currentModalEl = document.querySelector(currentId);
  const currentModal = bootstrap.Modal.getInstance(currentModalEl);
  currentModal.hide();

  currentModalEl.addEventListener('hidden.bs.modal', function onHidden() {
 
    currentModalEl.removeEventListener('hidden.bs.modal', onHidden);
    const targetModalEl = document.querySelector(targetId);
    const targetModal = new bootstrap.Modal(targetModalEl);
    targetModal.show();
  });
}

const SignUp = async (e) =>{
  e.preventDefault();  
  const formS = document.getElementById('signupForm');
  const formData = new FormData(formS);
  const appres = document.getElementById('signupmsg'); 
  appres.innerHTML = appres.innerHTML.replace(/<p[^>]*>.*?<\/p>/g, '');
  try {
      const response = await fetch('./controller/UserSignUp.php', {
          method: 'POST',
          body: formData
      });

      const data = await response.json();

      const res = document.createElement('p');
      res.textContent = data.message;
      res.style.textAlign="center";

      if (response.ok) {
          res.style.color = "green";
          appres.insertBefore(res,appres.firstChild);

          const remeber = document.getElementById('remember2');
          if(remeber && remeber.checked){
            const email = document.getElementById('email2').value;    
            const password = document.getElementById('password2').value;  
        
            localStorage.setItem('email', email);
            localStorage.setItem('password', password);
          }

          setTimeout(() => {
              window.location.href = './index.php';
          }, 2500);
      } else {
          res.style.color = "red";
          appres.insertBefore(res,appres.firstChild);
      }

  } catch (error) {
      alert("Fetch error: " + error);
  }
}

const LogIn = async (e) =>{
  e.preventDefault(); 
  const formL = document.getElementById('logInForm');
  const formData = new FormData(formL);
  const appres = document.getElementById('loginmsg'); 
  appres.innerHTML = appres.innerHTML.replace(/<p[^>]*>.*?<\/p>/g, '');
  try {
      const response = await fetch('./controller/UserLogin.php', {
          method: 'POST',
          body: formData
      });

      const data = await response.json();

      const res = document.createElement('p');
      res.textContent = data.message;
      res.style.textAlign="center";

      if (response.ok) {
          res.style.color = "green";
          appres.insertBefore(res,appres.firstChild);

          const remeber = document.getElementById('remember1');
          if(remeber && remeber.checked){
            const email = document.getElementById('email1').value;    
            const password = document.getElementById('password1').value;  

            localStorage.setItem('email', email);
            localStorage.setItem('password', password);
          }
          setTimeout(() => {
              window.location.href = './index.php';
          }, 2500);
      } else {
          res.style.color = "red";
          appres.insertBefore(res,appres.firstChild);
      }

  } catch (error) {
      alert("Fetch error: " + error);
  }
}

const LogInCheck = () => {
  const elements = document.querySelectorAll('#Logform [name]');
  let isValid = true;

  elements.forEach(el => {
    const name = el.name;
    const value = el.value.trim();

    if (value === "") {
      el.classList.add('is-invalid');
      isValid = false;
    } else {
      el.classList.remove('is-invalid');
    }

    if (name === 'email') {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        el.classList.add('is-invalid');
        isValid = false;
      }
    }

    if (name === 'password') {
      if (value.length < 8) {
        el.classList.add('is-invalid');
        isValid = false;
      }
    }
  });

  return isValid;
};
