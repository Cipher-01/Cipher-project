document.addEventListener("DOMContentLoaded", () => {
    // 1. Sticky Header Functionality
    const header = document.querySelector("header");
    let lastScroll = 0;

    window.addEventListener("scroll", () => {
        const currentScroll = window.pageYOffset;
        if (currentScroll > 50) {
            header.classList.add("sticky");
        } else {
            header.classList.remove("sticky");
        }
        lastScroll = currentScroll;
    });

    // 2. Intersection Observer for Fade-in Animations
    const faders = document.querySelectorAll(".content-section, .welcome-section, .form-container, .table-container, .interactive-box");
    const appearOptions = {
        threshold: 0,
        rootMargin: "0px 0px -50px 0px"
    };

    const appearOnScroll = new IntersectionObserver(function (entries, observer) {
        entries.forEach(entry => {
            if (!entry.isIntersecting) {
                return;
            } else {
                entry.target.classList.add("appear");
                observer.unobserve(entry.target);
            }
        });
    }, appearOptions);

    faders.forEach(fader => {
        fader.classList.add("fade-in");
        appearOnScroll.observe(fader);
    });

    // 3. Highlight Current Day in Academics Timetable
    const tablePath = window.location.pathname;
    if (tablePath.includes('academics.html') || tablePath.includes('Academics.html')) {
        const today = new Date().getDay(); // 0 is Sunday, 1 is Monday, ... 6 is Saturday

        // Only highlight if it's a weekday (1 to 5)
        if (today >= 1 && today <= 5) {
            const tableHeaders = document.querySelectorAll("th");
            // Highlight header (index 0 is 'Time', 1 is 'Monday', so index matches `today` directly)
            if (tableHeaders[today]) {
                tableHeaders[today].style.backgroundColor = "#18bc9c";
                tableHeaders[today].style.color = "white";
                tableHeaders[today].style.border = "2px solid #18bc9c";
            }

            // Highlight column cells for that day
            const rows = document.querySelectorAll("tbody tr");
            rows.forEach(row => {
                const cells = row.querySelectorAll("td");
                if (cells[today]) {
                    cells[today].style.backgroundColor = "#e8f8f5";
                    cells[today].style.fontWeight = "bold";
                    cells[today].style.border = "2px solid #18bc9c";
                }
            });
        }
    }
});
