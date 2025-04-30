document.addEventListener('DOMContentLoaded', function() {
    const teamMembers = [
        { 
            id: "member-1", 
            img: "https://randomuser.me/api/portraits/women/44.jpg", 
            name: "Samantha Nguyen", 
            title: "UX Designer",
            bio: "Specializes in creating intuitive user experiences with a focus on accessibility."
        },
        { 
            id: "member-2", 
            img: "https://randomuser.me/api/portraits/men/45.jpg", 
            name: "Ralph Edwards", 
            title: "Frontend Developer",
            bio: "Passionate about building responsive interfaces with modern JavaScript frameworks."
        },
        { 
            id: "member-3", 
            img: "https://randomuser.me/api/portraits/men/46.jpg", 
            name: "Robert Fox", 
            title: "Backend Engineer",
            bio: "Focuses on building scalable server architectures and efficient databases."
        },
        { 
            id: "member-4", 
            img: "https://randomuser.me/api/portraits/women/47.jpg", 
            name: "Alice Johnson", 
            title: "Project Manager",
            bio: "Ensures projects are delivered on time while maintaining high quality standards."
        },
        { 
            id: "member-5", 
            img: "https://randomuser.me/api/portraits/men/48.jpg", 
            name: "John Doe", 
            title: "DevOps Specialist",
            bio: "Implements CI/CD pipelines and cloud infrastructure solutions."
        },
        { 
            id: "member-6", 
            img: "https://randomuser.me/api/portraits/women/49.jpg", 
            name: "Emily Davis", 
            title: "QA Engineer",
            bio: "Dedicated to ensuring software quality through comprehensive testing strategies."
        },
        { 
            id: "member-1", 
            img: "https://randomuser.me/api/portraits/women/44.jpg", 
            name: "Samantha Nguyen", 
            title: "UX Designer",
            bio: "Specializes in creating intuitive user experiences with a focus on accessibility."
        },
        { 
            id: "member-2", 
            img: "https://randomuser.me/api/portraits/men/45.jpg", 
            name: "Ralph Edwards", 
            title: "Frontend Developer",
            bio: "Passionate about building responsive interfaces with modern JavaScript frameworks."
        },
        { 
            id: "member-3", 
            img: "https://randomuser.me/api/portraits/men/46.jpg", 
            name: "Robert Fox", 
            title: "Backend Engineer",
            bio: "Focuses on building scalable server architectures and efficient databases."
        },
        { 
            id: "member-4", 
            img: "https://randomuser.me/api/portraits/women/47.jpg", 
            name: "Alice Johnson", 
            title: "Project Manager",
            bio: "Ensures projects are delivered on time while maintaining high quality standards."
        },
        { 
            id: "member-5", 
            img: "https://randomuser.me/api/portraits/men/48.jpg", 
            name: "John Doe", 
            title: "DevOps Specialist",
            bio: "Implements CI/CD pipelines and cloud infrastructure solutions."
        },
        { 
            id: "member-6", 
            img: "https://randomuser.me/api/portraits/women/49.jpg", 
            name: "Emily Davis", 
            title: "QA Engineer",
            bio: "Dedicated to ensuring software quality through comprehensive testing strategies."
        }
    ];
    
    let currentIndex = 0;
    const membersPerSlide = 4;
    const teamRow = document.getElementById("teamRow");
    const indicatorsContainer = document.querySelector(".carousel-indicators");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
 
    function initCarousel() {
        renderCards();
        renderIndicators();
        setupEventListeners();
    }
    
    function renderCards() {
        teamRow.innerHTML = "";
        const endIndex = Math.min(currentIndex + membersPerSlide, teamMembers.length);
        
        for (let i = currentIndex; i < endIndex; i++) {
            const member = teamMembers[i];
            teamRow.innerHTML += `
                <div class="col-md-4 team-member" id="${member.id}">
                    <img src="${member.img}" alt="${member.name}" class="img-fluid">
                    <h5>${member.name}</h5>
                    <p class="position">${member.title}</p>
                    <p class="bio">${member.bio}</p>
                </div>
            `;
        }
        
    
        const emptySlots = membersPerSlide - (endIndex - currentIndex);
        for (let i = 0; i < emptySlots; i++) {
            teamRow.innerHTML += `<div class="col-md-4"></div>`;
        }
    }
    
    function renderIndicators() {
        indicatorsContainer.innerHTML = "";
        const totalSlides = Math.ceil(teamMembers.length / membersPerSlide);
        
        for (let i = 0; i < totalSlides; i++) {
            indicatorsContainer.innerHTML += `
                <div class="indicator ${i === currentIndex / membersPerSlide ? 'active' : ''}" 
                     data-index="${i * membersPerSlide}"></div>
            `;
        }
    }
    
    // Update active indicator
    function updateIndicators() {
        const indicators = document.querySelectorAll(".indicator");
        const activeIndex = Math.floor(currentIndex / membersPerSlide);
        
        indicators.forEach((indicator, index) => {
            if (index === activeIndex) {
                indicator.classList.add("active");
            } else {
                indicator.classList.remove("active");
            }
        });
    }
    
    // Navigate to specific slide
    function goToSlide(index) {
        currentIndex = index;
        renderCards();
        updateIndicators();
    }
    
    // Next slide
    function nextSlide() {
        if (currentIndex + membersPerSlide < teamMembers.length) {
            currentIndex += membersPerSlide;
            renderCards();
            updateIndicators();
        }
    }
    
    // Previous slide
    function prevSlide() {
        if (currentIndex > 0) {
            currentIndex -= membersPerSlide;
            renderCards();
            updateIndicators();
        }
    }
    
    // Set up event listeners
    function setupEventListeners() {
        prevBtn.addEventListener("click", prevSlide);
        nextBtn.addEventListener("click", nextSlide);
        
        // Indicator clicks
        document.querySelectorAll(".indicator").forEach(indicator => {
            indicator.addEventListener("click", function() {
                goToSlide(parseInt(this.dataset.index));
            });
        });
        
        // Keyboard navigation
        document.addEventListener("keydown", function(e) {
            if (e.key === "ArrowLeft") prevSlide();
            if (e.key === "ArrowRight") nextSlide();
        });
    }
    
    // Initialize the carousel
    initCarousel();
    // Auto-slide every 5 seconds
    setInterval(nextSlide, 5000);
    if(window.innerWidth < 768) {
        membersPerSlide = 3;
    }
    window.addEventListener('resize', function() {
        if(window.innerWidth < 768) {
            membersPerSlide = 3;
        } else {
            membersPerSlide = 4;
        }
        renderCards();
    });
});