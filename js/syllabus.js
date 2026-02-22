// Syllabus Data with PDF File Paths
const syllabusData = {
    "1 Year": {
        "1 Semester": [
            { name: "Mathematics I", pdf: "pdfs/Mathematics_I.pdf" },
            { name: "Chemistry", pdf: "pdfs/Chemistry.pdf" },
            { name: "Engineering Graphics", pdf: "pdfs/Engineering_Graphics.pdf" },
            { name: "Communication Skills & English", pdf: "pdfs/Communication_Skills.pdf" },
            { name: "Physics 1", pdf: "pdfs/Physics_1.pdf" },
            { name: "Engineering WorkShop Practice", pdf: "pdfs/Engineering_Workshop.pdf" },
            { name: "Sports & Yoga", pdf: "pdfs/Sports_Yoga.pdf" },
        ],
        "2 Semester": [
            { name: "Mathematics II", pdf: "pdfs/Mathematics_II.pdf" },
            { name: "Introduction To IT System", pdf: "pdfs/IT_System.pdf" },
            { name: "FEEE", pdf: "pdfs/FEEE.pdf" },
            { name: "Physics 2", pdf: "pdfs/Physics_2.pdf" },
            { name: "Engineering Mechanics", pdf: "pdfs/Engineering_Mechanics.pdf" },
            { name: "Environmental Science", pdf: "pdfs/Environmental_Science.pdf" },
        ],
    },
    "2 Year": {
        "3 Semester": [
            { name: "Professional Development", pdf: "pdfs/Professional_Development.pdf" },
            { name: "Summer Internship I", pdf: "pdfs/Summer_Internship_I.pdf" },
            { name: "Scripting Languages", pdf: "pdfs/Scripting_Languages.pdf" },
            { name: "Data Structures", pdf: "pdfs/Data_Structures.pdf" },
            { name: "Algorithm", pdf: "pdfs/Algorithm.pdf" },
            { name: "Computer Programming", pdf: "pdfs/Computer_Programming.pdf" },
            { name: "Computer System Organisation", pdf: "pdfs/CSO.pdf" },
        ],
        "4 Semester": [
            { name: "Computer Networks", pdf: "pdfs/Computer_Network.pdf" },
            { name: "Operating Systems", pdf: "pdfs/Operating_System.pdf" },
            { name: "Introduction to DBMS", pdf: "pdfs/DBMS.pdf" },
            { name: "Indian Knowledge", pdf: "pdfs/Indian_Knowledge.pdf" },
            { name: "Web Technologies", pdf: "pdfs/Web_Technologies.pdf" },
            { name: "Minor Project", pdf: "pdfs/Minor_Project.pdf" },
            { name: "SSAD/Software Engineering", pdf: "pdfs/Software_Engineering.pdf" },
        ],
    },
    "3 Year": {
        "5 Semester": [
            { name: "Intro. To E-governance", pdf: "pdfs/E_Governance.pdf" },
            { name: "Internet of Things", pdf: "pdfs/IoT.pdf" },
            { name: "Information Security ", pdf: "pdfs/Information_Security.pdf" },
            { name: "MultiMedia Technologies", pdf: "pdfs/Multimedia_Technologies.pdf" },
            { name: "AD.Computer Networks", pdf: "pdfs/Advance_Computer_Network.pdf" },
            { name: "Data Sciences", pdf: "pdfs/Data_Science.pdf" },
            { name: "Renewable Energy tech.", pdf: "pdfs/Renewable_Energy.pdf" },
            { name: "Operation Research", pdf: "pdfs/Operational_Research.pdf" },
            { name: "Summer Internship 2", pdf: "pdfs/Summer_Internship_2.pdf" },
            { name: "Major Project", pdf: "pdfs/Major_Project.pdf" },
        ],
        "6 Semester": [
            { name: "Entrepreneurship & Start-up", pdf: "pdfs/Entrepreneurship and Start-UPS.pdf" },
            { name: "Mobile Computing", pdf: "pdfs/Mobile Computing.pdf" },
            { name: "Network Forensics", pdf: "pdfs/Network Forensic.pdf" },
            { name: "Software Testing", pdf: "pdfs/Software Testing.pdf" },
            { name: "Free & Open Source Software", pdf: "pdfs/Free Open source Software (Foss).pdf" },
            { name: "Disaster Management", pdf: "pdfs/DISASTER MANAGEMENT.pdf" },
            { name: "Project Management", pdf: "pdfs/Project management.pdf" },
            { name: "Artifical Intelligence", pdf: "pdfs/Artificial Intelligence.pdf" },
            { name: "Engg.Eco & Accountancy", pdf: "pdfs/Engineering Economics & Accountancy.pdf" },
            { name: "Indian Constitution", pdf: "pdfs/Indian constitution.pdf" },
            { name: "Major Project", pdf: "pdfs/Major Project.pdf" },
            { name: "Seminar", pdf: "pdfs/SEMINAR.pdf" },
        ],
    },
};

// Function to Open PDF in a New Tab
function openPDF(pdfPath) {
    window.open(pdfPath, '_blank');
}

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
                subjectDiv.innerText = subject.name;

                // Make Subject Clickable to Open PDF in a New Tab
                subjectDiv.addEventListener("click", () => openPDF(subject.pdf));

                semesterBlock.appendChild(subjectDiv);
            });

            yearBlock.appendChild(semesterBlock);
        }

        yearsContainer.appendChild(yearBlock);
    }
}

// Initialize the Syllabus
renderSyllabus(syllabusData);
