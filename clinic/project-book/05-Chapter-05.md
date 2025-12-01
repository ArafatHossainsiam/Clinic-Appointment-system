# Chapter 5 — Database and Algorithm

## 5.1 Database Management System
The Clinic Management System relies on MariaDB/MySQL to manage persistent data and enforce relational constraints. PHP uses prepared statements and shared helpers in `config.php` to keep operations coherent across pages and routes. Core entities include `users` (Assistant/Doctor/Patient), `doctors`, `appointments`, `prescriptions`, `medical_history`, and `documents`, defined in `database/clinic_system.sql`. Foreign keys connect the model where it matters: appointments bind doctors and patients; prescriptions and history reference appointments and patients for traceability.

Security is layered through session management, role checks, and sanitized inputs. A typical operational flow begins in the UI, reads available doctors for a given date/time, creates or reuses a patient record by phone, inserts a `pending` appointment, and surfaces confirmation. Assistants and doctors complete the flow by updating statuses and adding prescriptions. Analytics and reporting derive counts and trends from the same schema.

## 5.2 Design and Develop Database
The relational model is implemented in MariaDB/MySQL and codified in `database/clinic_system.sql`. Data flow diagrams keep the architecture clear: inputs from Patients, Assistants, and Doctors hit PHP pages or `api/*` endpoints, persist to tables, and produce outputs such as confirmations, dashboards, and history reports. Data codification emphasizes stable identifiers, strict foreign keys, and targeted indexes—for example, appointments by doctor/date, prescriptions by patient, and users by role. Constraints maintain integrity.

Implementation proceeds through SQL imports or migrations, with verification via prepared queries in PHP. The process begins by collecting role needs and data formats for appointment intake and staff operations, then deriving the information flow—discover availability, request appointment, confirm/cancel, add prescription—into a coherent DFD. The schema distinguishes repeating data (doctors, patients) from event records (appointments, prescriptions), identifies keys and relationships, and applies normalization to remove redundancy while retaining enums for statuses and roles. An ERD reflects tables and foreign keys used by the project.

## 5.3 MVC Model
The system maps cleanly to Model–View–Controller responsibilities in PHP. The Model is the database: `users`, `doctors`, `appointments`, `prescriptions`, and `medical_history`, with foreign keys and constraints defined in `database/clinic_system.sql`. CRUD executes through prepared statements and shared helpers in `config.php`. Model rules include availability windows and daily capacity enforcement.

The View renders pages in PHP and HTML and reflects model state in real time. It presents forms and tables in `index.php`, `assistant_dashboard.php`, `doctor_dashboard.php`, and `patient_dashboard.php`, and updates UI with JavaScript notifications and modals.

The Controller consists of small API scripts under `api/` that handle user input, call model operations, and orchestrate view updates. Examples include `api/get_available_doctors.php` for availability, `api/update_appointment.php` for status transitions, `api/add_prescription.php` for clinical records, and Cloudinary image operations via helpers in `config.php`. In practice, appointment submission validates inputs, enforces time windows and capacity, and writes to `appointments` as a guided flow that surfaces confirmations and preserves auditability.

## 5.4 Algorithm
Preventing double‑booking hinges on enforcing doctor daily capacity and slot windows, with optional rejection of exact time duplicates. The system treats an appointment as valid when (a) the requested time falls within the doctor’s configured start/end window, (b) the count of active (`pending`/`confirmed`) appointments for that doctor on the requested date is below `max_slots`, and (c) no existing active appointment occupies the exact same timestamp for that doctor (if exact‑time uniqueness is required).

Operationally, checks run before insertion. The server counts active appointments for the doctor on the date and optionally queries for any appointment at the exact requested time. If capacity is reached or an exact‑time conflict exists, the request is rejected; otherwise, the appointment is inserted as `pending` and surfaced to staff for confirmation.

Example SQL checks:

```
-- capacity check
SELECT COUNT(*)
FROM appointments
WHERE doctor_id = ? AND date = ? AND status IN ('pending','confirmed');

-- exact time conflict check (optional)
SELECT 1
FROM appointments
WHERE doctor_id = ? AND date = ? AND time = ? AND status IN ('pending','confirmed')
LIMIT 1;
```

### 5.4.3 Example and Implementation
If Doctor 7 has `max_slots = 10` and 10 active appointments on 2025‑12‑20, a new request for 2025‑12‑20 at 10:30 is rejected for capacity. If capacity allows but an appointment already exists at 10:30, the request is rejected for time conflict. Requests outside the slot window—e.g., a 07:00 request when the doctor’s start time is 09:00—are rejected by the window validation.
