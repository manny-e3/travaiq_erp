# BPA Enterprise ERP & Business Process Automation

<p align="center">
  <img src="public/images/hero.png" width="800" alt="BPA Enterprise ERP Banner" style="border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.15);" />
</p>

<p align="center">
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.2+"></a>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-3.x-FB70A9?style=flat-square&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://ai.google.dev"><img src="https://img.shields.io/badge/AI-Google%20Gemini%203.8%20Flash-4285F4?style=flat-square&logo=google&logoColor=white" alt="Google Gemini"></a>
  <a href="https://spatie.be/open-source"><img src="https://img.shields.io/badge/Security-Spatie%20RBAC-0052CC?style=flat-square" alt="Spatie RBAC"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-Proprietary-gray?style=flat-square" alt="License"></a>
</p>

---

## 📌 Executive Overview

**BPA Enterprise ERP** is a modern, unified Enterprise Resource Planning and Business Process Automation platform designed for scaling organizations. It centralizes human capital operations, corporate asset lifecycle tracking, AI-powered talent acquisition, double-entry payroll ledger integration, and multi-channel customer service helpdesk workflows into a single high-performance dashboard.

---

## 🚀 Key Modules & Capabilities

### 1. 👥 Workforce & Access Control (RBAC)
- **Granular Permissions**: Role-based access control powered by Spatie Permission (`admin`, `manager`, `hr`, `user`).
- **Organization Hierarchy**: Multi-tier Department and Designation structural mapping.
- **Enterprise Security**: Multi-factor authentication (MFA/OTP verification via email), password policy enforcement, and audit logs.

### 2. 🏢 Corporate Asset Lifecycle Management
- **Hardware & Asset Registry**: Track physical assets with unique barcodes/asset tags, categories, and physical locations.
- **Depreciation Engine**: Automated straight-line and reducing-balance book value calculations.
- **Custodian Audits**: Assignment history linking equipment directly to staff profiles.
- **Document Vault**: Store purchase invoices, warranty certificates, and maintenance records.

### 3. 🎯 Talent Acquisition & AI CV Screening
- **Public Careers Portal**: Interactive careers page (`/careers`) with job postings and direct application uploads.
- **Gemini 3.8 Flash Screening Engine**: Automated resume evaluation that parses applicant PDF resumes, checks alignment against the job description, and outputs:
  - Match Score (0–100%)
  - Recommendation (`Shortlist`, `Review`, `Reject`)
  - Executive recruiter summary with key strengths and skill gaps
- **Hiring Pipeline**: Stage tracking from *New Application* to *Interview Scheduling*, *Digital Offer Letters*, and *One-Click Onboarding*.

### 4. 💼 Human Capital & Payroll Intelligence
- **Employee 360 Profiles**: Unified records covering contracts, documents, employment history, and equipment custodianship.
- **Automated Payroll**: One-click payroll processing with customizable allowances, statutory deductions, net payouts, and downloadable PDF payslips.
- **General Ledger (GL) Postings**: Automated double-entry financial ledger journal entries generated for every payroll run.
- **Leave & Attendance**: Multi-type leave balances (Annual, Sick, Casual, Maternity) with manager approval workflows.
- **Performance & OKRs**: Quarterly goal tracking (OKRs), 360-degree appraisals, peer feedback, and performance-based salary reviews.

### 5. 🎫 Service Operations & Customer Helpdesk
- **Customer Support Portal**: Self-service ticket submission interface (`/tickets/new`).
- **SLA Policy Engine**: Response and resolution target timers with priority-based escalation queues (`Urgent`, `High`, `Medium`, `Low`).
- **Knowledge Base (KB)**: Public and internal self-service documentation portal (`/kb`) with real-time AJAX search.

### 6. 📊 Executive Command Center
- Real-time KPI summary cards (Total Personnel, Asset Book Value, Candidate Pipeline, Open Tickets).
- Departmental human capital distribution progress indicators.
- Live operational queues for pending approvals, tickets, and candidate reviews.

---

## 🛠 Technology Stack

- **Backend**: PHP 8.2+, Laravel 12.x
- **Frontend / UI**: Blade Templates, DashLite Enterprise Theme, Tailwind CSS, Alpine.js, Livewire
- **Database**: MySQL 8.x
- **AI / LLM Integration**: Google Gemini 3.8 Flash via REST (`generativelanguage.googleapis.com`)
- **PDF Processing**: `smalot/pdfparser` (CV text extraction), `barryvdh/laravel-dompdf` (Payslips & documents)
- **Cache / Sessions**: Database / Redis driver support

---

## 💻 Installation & Quickstart

### Prerequisites
- PHP >= 8.2 with `curl`, `mbstring`, `pdo_mysql`, `openssl`, and `gd` extensions enabled
- Composer >= 2.x
- MySQL >= 8.0 or MariaDB (via XAMPP or standalone)
- Node.js >= 18.x & NPM

### Step-by-Step Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-org/bpa-erp.git
   cd bpa-erp
   ```

2. **Install PHP Dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure Database & Services in `.env`**:
   ```ini
   APP_NAME="BPA Enterprise ERP"
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bpa_db
   DB_USERNAME=root
   DB_PASSWORD=

   # Drivers
   SESSION_DRIVER=database
   CACHE_STORE=database
   QUEUE_CONNECTION=database

   # Google Gemini AI Integration (for CV Screening)
   GEMINI_API_KEY=your_gemini_api_key_here
   GEMINI_MODEL=gemini-3.8-flash
   ```

5. **Run Migrations & Seeders**:
   ```bash
   php artisan migrate --seed
   php artisan db:seed --class=Database\Seeders\ErpDashboardDataSeeder
   ```

6. **Install Frontend Dependencies & Build Assets**:
   ```bash
   npm install
   npm run build
   ```

7. **Start the Development Server**:
   ```bash
   php artisan serve
   ```
   Access the system at **`http://127.0.0.1:8000`** or your configured local virtual host.

---

## 🤖 AI Screening Engine Configuration

The candidate resume screening engine is powered by **Google Gemini 3.8 Flash**.

- **Implementation**: [`App\Services\AiScreeningService`](app/Services/AiScreeningService.php)
- **Configuration**: Set your API key in `.env`:
  ```ini
  GEMINI_API_KEY=AIzaSy...
  GEMINI_MODEL=gemini-3.8-flash
  ```
- **How it works**:
  1. The candidate uploads a resume via `/careers/{job}`.
  2. The service parses the PDF using `smalot/pdfparser`.
  3. The CV text and target job description are sent to Gemini 3.8 Flash with a structured JSON schema.
  4. The model returns a verified match score (0-100), automated recommendation (`Shortlist`, `Review`, `Reject`), and executive summary.

---

## 🔒 Default Role Structure

| Role | Access Level |
|---|---|
| **Super Admin / Executive** | Full platform access, global settings, financial ledger, salary reviews |
| **HR Manager** | Employee records, recruitment pipeline, leave & overtime approvals |
| **Asset Manager** | Asset catalog, maintenance lifecycle, hardware allocation |
| **Support Agent** | Helpdesk tickets, SLA management, knowledge base curation |
| **Employee / User** | Self-service portal, leave requests, appraisals, personal profile |

---

## 🛡 Security & Maintenance

- **Config & Cache Clear**:
  ```bash
  php artisan optimize:clear
  ```
- **Queue Listener** (for asynchronous tasks):
  ```bash
  php artisan queue:work --tries=3
  ```

---

## 📄 License

This software is proprietary and confidential. Unauthorized copying, distribution, or modification is strictly prohibited. &copy; 2026 BPA Enterprise ERP. All rights reserved.
