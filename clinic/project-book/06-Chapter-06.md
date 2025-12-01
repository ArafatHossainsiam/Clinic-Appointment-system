# Chapter 6 — Tools and Technology

The Clinic Management System uses a practical toolchain that fits PHP development. Documents are authored with Microsoft Office, and presentations use Microsoft PowerPoint. Coding is done in Visual Studio Code. Architecture diagrams—DFDs, context diagrams, and ERDs—are produced with Mermaid in Markdown or Draw.io. Flowcharts leverage Mermaid so specifications remain close to implementation.

## 6.1 Technology
The UI and page orchestration run on PHP with HTML, CSS, and JavaScript. MariaDB/MySQL provides durable storage, and Cloudinary handles image uploads and delivery. Sessions secure role‑based dashboards for assistants, doctors, and patients.

## 6.2 HTML
Semantic HTML underpins the structure and accessibility of the interface. The application works across modern browsers, integrates cleanly with CSS, and exposes meaningful roles and labels so assistive technologies interpret the UI correctly.

## 6.3 CSS
Styling uses lightweight, maintainable CSS classes and component‑level patterns. Responsive layouts and theming are handled declaratively, keeping design decisions close to the interface.

## 6.4 JavaScript
JavaScript (`js/main.js`) powers dynamic UI interactions, validations, notifications, and small animations that improve clarity without blocking rendering. It coordinates with PHP endpoints to fetch availability and submit status changes.

## 6.5 Database (MariaDB/MySQL)
MariaDB/MySQL stores users, doctors, appointments, prescriptions, and medical history with foreign keys and indexes. Prepared statements and sanitization enforce safe CRUD operations and preserve integrity.

## 6.6 Cloudinary (Images)
Cloudinary provides managed image storage and a global CDN for optimized delivery. Uploads flow through helpers in `config.php`, storing secure URLs for downstream rendering and administration. Deletions are handled via the same utility, keeping the image lifecycle tidy and predictable.
