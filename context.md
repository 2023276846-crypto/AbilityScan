# AbilityScan - Project Context

Based on the exploration of the codebase, here is a summary of my understanding of the **AbilityScan** project.

## Project Overview
**AbilityScan** is a specialized job portal and professional networking platform designed to bridge the gap between **Persons with Disabilities (PWD)** and inclusive **Employers**. The platform includes a unique feature where PWDs can generate a QR code for their portfolio, allowing employers to easily scan and view their profiles.

## Tech Stack
- **Backend:** Laravel 11 (PHP 8.2+) providing a RESTful API.
- **Authentication:** Laravel Sanctum for secure API token-based authentication.
- **Frontend:** Vue 3 (Single Page Application) using Vue Router, built and bundled with Vite.
- **Notable Libraries:**
  - `simplesoftwareio/simple-qrcode` (Backend): Generating QR codes.
  - `jsqr` (Frontend): Scanning and reading QR codes.
  - `chart.js` (Frontend): For rendering dashboard analytics and reports.

## Core User Roles & Features

### 1. PWD (Persons with Disabilities)
- **Profile & Portfolio:** Create and manage a professional profile.
- **QR Code Integration:** Generate a unique QR code that links to a public view of their portfolio (`/portfolio/{id}`).
- **Job Hunting:** Browse available job postings and apply for them.
- **Dashboard & Communication:** Track job application statuses and communicate via the built-in messaging system.

### 2. Employers
- **Company Profile:** Manage company details.
- **Job Management:** Post, edit, delete, and view job listings.
- **Applicant Tracking:** View candidates who applied, update application statuses, and manage the hiring pipeline.
- **Messaging:** Directly message PWD candidates.

### 3. Admin
- **User Moderation:** Approve, reject, or delete PWD and Employer accounts to maintain platform integrity.
- **Analytics & Monitoring:** View system reports, monitor activity, and access a high-level dashboard.

## Key Workflows
1. **QR Portfolio Scanning:** A PWD can share their QR code. Anyone scanning it (using the frontend `jsqr` integration) is routed to a public portfolio view.
2. **Job Application Pipeline:** Employers post jobs -> PWDs apply -> Employers review applications and update status -> Both parties can message each other.

---
*If this aligns with your vision and there are specific features, bugs, or next steps you'd like me to work on, please let me know!*
