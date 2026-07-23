# AbilityScan - Chapter 5: Conclusion, Limitations, and Recommendations

This document contains the drafted points for **Chapter 5** of your final project report/thesis.

---

## 5.1 Conclusion
*   **Bridge for Inclusive Employment:** *AbilityScan* successfully establishes a specialized recruitment platform that connects Persons with Disabilities (PWD) candidates with inclusive employers.
*   **Targeted 5-Layer Matching:** The implementation of the 5-Layer matching algorithm (Skills, Physical, Technology, Work Arrangement, and Sensory rules) ensures candidates are matched with jobs that accommodate their specific needs.
*   **Seamless QR Code Integration:** The QR Portfolio feature serves as a practical physical-to-digital bridge, allowing employers to instantly scan and view candidate profiles during physical job fairs or walk-in interviews.
*   **Secure Communications:** The secure in-app Mailbox allows candidates and employers to interact and schedule interviews safely without sharing personal contact information prematurely.
*   **Admin-Controlled Engine:** The dynamic algorithm weights configuration panel allows administrators to adjust matching rules in real-time to align with job market trends.

---

## 5.2 Achievements of Project Objectives
Your system successfully achieves all three objectives defined in Chapter 1:

| Objective (Chapter 1) | System Achievement (Chapter 5) | Status |
| :--- | :--- | :--- |
| **Objective 1:** To design an employment digital portfolio website for PWDs. | **Achieved.** Successfully designed a user-friendly, responsive frontend dashboard using Vue 3. Features include tailored profile forms, visual progress bars, compatibility modals, and mailbox interfaces for PWDs, Employers, and Admins. | **100% Complete** |
| **Objective 2:** To develop an employment digital portfolio website for PWDs using a rule-based matching approach. | **Achieved.** Developed a full-stack system with a Laravel 11 backend and a database schema implementing the **5-Layer Rule-Based Matching Engine** (S, P, T, W, C). The engine calculates match percentages and explains matching scores dynamically. | **100% Complete** |
| **Objective 3:** To evaluate the functionality of the proposed Web Application. | **Achieved.** Conducted full functionality testing (black-box testing) across PWD, Employer, and Admin portals. The mathematical logic of the matching engine was also verified using automated backend PHPUnit tests. | **100% Complete** |

---

## 5.3 Addressing the Problem Statements
Your system successfully solves the three problem statements described in Chapter 1:

### 1. Solving Problem 1: Critical Employment Gap (Low PWD workforce entry rate)
*   **Status:** **Solved.**
*   **Explanation:** *AbilityScan* acts as an exclusive recruitment portal dedicated to PWDs. By matching candidate profiles directly with physical, technological, and sensory job requirements, it lowers barriers to entry and increases employment matching rates. It simplifies the discovery of inclusive job vacancies, encouraging more PWD candidates to successfully enter the workforce.

### 2. Solving Problem 2: Hidden & Rigid Algorithms ("Black Boxes")
*   **Status:** **Solved.**
*   **Explanation:** 
    *   **Transparency:** The system completely removes the "black box" by offering a **Match Explanation modal** with visual progress bars. Candidates see exactly why they matched or mismatched on each rule.
    *   **Flexibility:** The algorithm is not rigid—administrators can dynamically adjust the weight percentages of the 5 layers in the Admin dashboard to align with shifting workplace requirements and policies.

### 3. Solving Problem 3: Physical-Digital Disconnect during job fairs
*   **Status:** **Solved.**
*   **Explanation:** The system generates a unique QR code portfolio for PWD candidates. Employers at physical fairs or walk-in interviews can scan the QR code using their device camera to instantly pull up the candidate's complete 5-layer digital portfolio, linking the physical interaction seamlessly with digital records.

---

## 5.4 Project Limitations
*   **Exact Rule-Based Matching:** The matching engine relies on exact tag/keyword matching rather than advanced artificial intelligence or Machine Learning (ML), which could adapt to successful hiring outcomes over time.
*   **No Native Mobile App:** The QR scanning feature is mobile-responsive on web browsers, but lacks a native mobile app wrapper, which would offer faster camera focus and a smoother user experience at physical events.
*   **Static Notifications:** The messaging and application status systems trigger updates on page reload rather than using real-time push notifications (like WebSockets).

---

## 5.5 Recommendations for Future Work
*   **AI & Machine Learning Integration:** Upgrade the matching engine to use Natural Language Processing (NLP) to match candidate experience and job descriptions contextually, instead of strict tag-matching.
*   **Native Mobile Application:** Develop native Android and iOS mobile applications to improve camera performance for scanning QR portfolios on-the-go.
*   **Real-time Push Notifications:** Implement WebSockets (such as Laravel Reverb or Pusher) to enable real-time messaging, instant match alerts, and live interview reminders.
