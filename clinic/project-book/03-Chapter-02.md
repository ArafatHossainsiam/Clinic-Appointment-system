# Chapter 2 — Literature Review

## 2.1 What Is Online Appointment Booking
Online appointment booking allows patients to reserve consultations over the internet without visiting a clinic in person. In healthcare contexts, patients expect to discover available services and specialists, filter by specialty and time, and complete requests through guided forms with validation and immediate feedback. In this project, the experience is delivered through `index.php` and role‑based dashboards, with availability checks against configured doctor slot windows and daily capacity.

## 2.2 Models of Appointment Booking
Healthcare appointment platforms commonly serve a clinic‑to‑patient model where staff publish schedules and patients book directly. Multi‑clinic networks extend this to multiple locations and specialties, requiring consolidated views, filtering, and access control. Staff‑assisted scenarios let assistants confirm or cancel requests and maintain doctor profiles, which aligns with `assistant_dashboard.php`. Telemedicine adds virtual visits to the mix, but the same booking primitives—date, time, doctor, and notes—apply.

## 2.3 How Online Booking Conducts
A typical session starts when a patient opens the home page and requests an appointment. The form collects name or phone (used as patient ID), password for account reuse or creation, date, time, doctor, and problem description. JavaScript in `js/main.js` validates required fields, sets minimum dates, and shows notifications. Availability is checked via `api/get_available_doctors.php`, which enforces the doctor’s slot window and remaining daily capacity.

On submission, `appointment_request.php` validates inputs, creates or reuses a patient record, confirms the doctor exists, enforces time windows, and caps bookings by `max_slots`. The request is stored as `pending`. Assistants review all appointments in their dashboard and can confirm or cancel via `api/update_appointment.php`. Doctors see confirmed and pending appointments in `doctor_dashboard.php`, access patient history via `api/get_patient_history.php`, and add prescriptions through `api/add_prescription.php`.

## 2.4 Market Research (Online Healthcare Booking)
Healthcare literature emphasizes accessibility, trust, and reliability. Patient demographics—age, mobility, device usage, and accessibility needs—influence preferred booking times and the importance of clear instructions and reminders. Demand varies by season and specialty; operational efficiency benefits from accurate availability, prompt confirmations, and consistent records. Competitive analyses compare wait times, specialist coverage, cancellation policies, and communication quality.

Technology trends include secure portals, privacy‑preserving data handling, and robust media where applicable (e.g., doctor photos). Regulations such as local health privacy laws and general data protection guidance apply; consent, secure session handling, and transparent policies are essential. Satisfaction and reviews hinge on appointment clarity, follow‑up reliability, and symptom documentation. Operational metrics include daily capacity usage, confirmation rates, cancellation rates, and repeat visits.

## 2.5 Summary
Online appointment systems consolidate discovery and reservation into an accessible, always‑available experience. Reliability stems from validated forms, secure sessions, and indexed queries aligned to real clinic workflows. By combining role‑based dashboards with clear availability policies and resilient image handling, clinics can scale patient access while preserving data integrity and staff efficiency.
