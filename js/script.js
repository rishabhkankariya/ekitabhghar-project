// Syllabus Data
const syllabusData = {
    "1 Year": {
        "1 Semester": ["Mathematics I", "Physics", "Programming Basics"],
        "2 Semester": ["Mathematics II", "Data Structures", "Digital Logic"],
    },
    "2 Year": {
        "3 Semester": ["Database Management", "Operating Systems", "Java Programming"],
        "4 Semester": ["Web Development", "Computer Networks", "Software Engineering"],
    },
    "3 Year": {
        "5 Semester": ["Machine Learning", "Cloud Computing", "AI Basics"],
        "6 Semester": ["Big Data", "Cyber Security", "Project Work"],
    },
};

// Render Syllabus Function
function renderSyllabus(data) {
    const yearsContainer = document.getElementById("yearsContainer");

    for (const year in data) {
        // Create Year Block
        const yearBlock = document.createElement("div");
        yearBlock.classList.add("year");

        // Add Year Heading
        const yearHeading = document.createElement("h2");
        yearHeading.innerText = year;
        yearBlock.appendChild(yearHeading);

        // Add Semesters
        for (const semester in data[year]) {
            const semesterBlock = document.createElement("div");
            semesterBlock.classList.add("semester");

            // Add Semester Heading
            const semesterHeading = document.createElement("h3");
            semesterHeading.innerText = semester;
            semesterBlock.appendChild(semesterHeading);

            // Add Subjects
            data[year][semester].forEach((subject) => {
                const subjectDiv = document.createElement("div");
                subjectDiv.classList.add("subject");
                subjectDiv.innerText = subject;
                semesterBlock.appendChild(subjectDiv);
            });

            yearBlock.appendChild(semesterBlock);
        }

        yearsContainer.appendChild(yearBlock);
    }
}

// Initialize the Syllabus
renderSyllabus(syllabusData); 
// Fade-in page on load
$(document).ready(function () {
    $("body").css("display", "none").fadeIn(2000);
});
