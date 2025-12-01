# Abstract

This clinic management platform delivers a streamlined, role-driven experience for requesting, confirming, and completing medical appointments. Visitors arrive at a responsive PHP interface (`clinic/index.php`) where they can request appointments through guided forms, select preferred dates and times, and choose available doctors. Rich UI interactions powered by `clinic/js/main.js` provide modals, theme toggling, inline validations, and toast-style notifications to keep feedback clear and fast throughout the flow.

The platform supports three user personas—Assistant, Doctor, and Patient—each with a dedicated dashboard:
- Assistant oversees appointment intake and status transitions, manages doctor profiles, and reviews system metrics (`clinic/assistant_dashboard.php`).
- Doctor views upcoming and pending consultations, reviews patient history, and records prescriptions (`clinic/doctor_dashboard.php`).
- Patient tracks appointments, downloads prescriptions, and reviews medical history (`clinic/patient_dashboard.php`).

Core workflows are implemented through simple, secure PHP endpoints under `clinic/api/`:
- Appointment lifecycle: request, confirm/cancel, and complete (`appointment_request.php`, `api/update_appointment.php`).
- Doctor management: create, list, and delete profiles with optional photo upload (`api/add_doctor.php`, `api/get_doctors.php`, `api/delete_doctor.php`).
- Clinical records: add and download prescriptions, query medical history (`api/add_prescription.php`, `api/download_prescription.php`, `api/get_patient_history.php`).

Data is modeled in MariaDB with normalized tables and practical indexes for common queries. The schema includes `users` (Assistant/Doctor/Patient), `doctors`, `appointments`, `prescriptions`, `medical_history`, and `documents`, with foreign keys and cascade rules that simplify consistency (`clinic/database/clinic_system.sql`). Unique business identifiers (e.g., `APT…` for appointments, `RX…` for prescriptions) are generated server-side for traceability.

Images are handled via a Cloudinary-backed upload pipeline. When a doctor’s photo is provided, it is uploaded and stored as a secure URL, enabling robust rendering and reliable fallbacks across the UI (`clinic/config.php` → `uploadImageToCloudinary`). Environment variables are read from a local `.env` file for credentials and folder configuration, keeping sensitive values out of the code.

Authentication and session management are implemented in PHP, with role-based redirects and guarded routes to enforce access control (`clinic/config.php`). Input sanitization and prepared statements are used consistently across endpoints to reduce common web risks while maintaining performance and clarity.

Overall, the architecture favors maintainability and scalability through:
- Stateless API-style PHP scripts that encapsulate CRUD operations and status transitions.
- A clean separation between dashboards, endpoints, and utilities.
- Indexed queries aligned to frequent filters (date, doctor, patient) for efficient retrieval.
- Modular UI behaviors (modals, notifications, animations) that keep interactions smooth.

This foundation supports iterative evolution—adding new record types, extending validations, or integrating external services—without disrupting existing flows. As datasets grow, the indexed schema and role-oriented screens help staff and patients remain effective, even under increased load.

