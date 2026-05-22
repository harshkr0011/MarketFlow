# 🌌 MarketFlow

<p align="center">
  <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&w=800&q=80" width="100%" alt="MarketFlow Banner" style="border-radius: 12px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 3">
  <img src="https://img.shields.io/badge/Filament-D97706?style=for-the-badge&logo=filament&logoColor=white" alt="Filament PHP">
  <img src="https://img.shields.io/badge/Tailwind_CSS-38BDF8?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Gemini_AI-8E75C2?style=for-the-badge&logo=google-gemini&logoColor=white" alt="Gemini AI">
  <img src="https://img.shields.io/badge/SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
</p>

**MarketFlow** is an enterprise-grade, multi-tenant AI-driven CRM, marketing automation, collaboration workspace, and e-commerce portal built using **Laravel 12**, **Livewire 3**, and **Filament v3**. 

Designed for marketing agencies, brand managers, and clients, MarketFlow unifies creative asset workflows, real-time collaboration with visual design annotations, procurement RFPs, financial budget ledger approvals, and an integrated digital commerce storefront.

---

## 🚀 Key Modules & Functional Features

### 1. ✍️ AI Copywriting Playground
* **Dynamic Generation:** Integrated with Google Gemini AI (`gemini-3.1-flash-lite`) via a custom service wrapper.
* **Tone Control:** Supports professional, casual, bold, humorous, or analytical tone settings.
* **Translation Simulator:** One-click translation of generated copy into five custom profiles optimized for the Indian market:
  * English (Standard)
  * English (India - `en_in`)
  * Hinglish (Colloquial blend - `hinglish`)
  * Hindi (`hi`)
  * Spanish (`es`)
* **Fallback Mode:** Features pre-configured mock copy generators to keep your workflows responsive during offline runs or missing API keys.

### 2. 🎨 AI Visual Canvas
* **Text-to-Image Generation:** Utilizes pollinations.ai for photorealistic, abstract, and minimalist style rendering directly from simple prompts.
* **Asset Vault Integration:** Export generated images directly into the agency's **Asset Vault** with categories (Social Media, Web, Print), custom names, and billing tier allocations.

### 3. 💬 Real-Time Collaboration & Review Room
* **Visual Annotations:** Pinpoint feedback on marketing drafts using coordinates. Clicking anywhere on a shared mockup creates coordinate-mapped visual markers (`📍 [ANNOTATION] (x:X%, y:Y%): feedback`).
* **Real-time Live Chat:** Powered by **Laravel Reverb** (WebSockets) for zero-latency, multi-user conversations.
* **Campaign Lifecycle Logs:** Automated system audit logs broadcasted straight into the chat workspace upon campaign status changes (e.g. `Idea` ➡️ `Design` ➡️ `Approval` ➡️ `Live`).

### 4. 🛒 E-Commerce storefront & Billing Portal
* **Digital Products Catalog:** Offers items ranging from templates to consulting sessions.
* **Product Reviews:** Users can submit star ratings and descriptions to individual products.
* **Dual Currency Checkout:** Processes transactions in local currencies (INR) and dynamically converts billing details to USD ($) at real-time rates (1 USD = 83 INR).
* **Payment Validation:** Multi-method validation for Cards (CVC, expiry formats) and UPI IDs.
* **Automated PDF Invoices:** Built-in PDF renderer (`laravel-dompdf`) that generates downloadable, high-fidelity invoices reflecting 18% GST calculations.

### 5. 🤝 Partner Bidding Room (RFP Console)
* **Request for Proposals (RFP):** Connect campaigns to RFPs defining budget limits, scopes, and partner deadlines.
* **Proposal Management:** Partners submit bids and attach PDF proposals, allowing admins to track bids, award contracts, and decline proposals.

### 6. 📊 Budget Ledger & Financial Controls
* **Scope Tracking:** Manage agency budgets categorized by franchise, campaign, or department.
* **Drawdowns Engine:** Request cash drawdowns for specific active marketing campaigns.
* **Over-Budget Prevention:** Multi-tier validation blocks drawdown approvals if requested limits exceed remaining allocated budget caps.

### 7. ⚙️ Filament Admin Control Center
* **Complete Resource Management:** Full CRUD dashboards for Agencies, Clients, Campaigns, Workspaces, Partners, RFPs, Budgets, and Users.
* **Prompt Engineering Lab:** Configures system parameters (e.g. system prompts, AI temperatures) that calibrate Gemini AI's response patterns across all playgrounds.
* **Revenue Analytics:** Centralized financial data tracking.
* **Impersonation Mode:** Lets admin staff impersonate other users or clients to debug or audit work environments.

---

## 🛠️ Technology Stack

| Layer | Component | Description |
| :--- | :--- | :--- |
| **Framework** | [Laravel 12](https://laravel.com) | Core backend MVC architecture |
| **Frontend** | [Livewire 3](https://livewire.laravel.com) & [Volt](https://livewire.laravel.com/docs/volt) | Reactive, single-file components |
| **CSS** | [Tailwind CSS v3](https://tailwindcss.com) | Responsive, modern utility styling |
| **Admin Panel** | [Filament PHP v3](https://filamentphp.com) | Secure admin resources, pages, and forms |
| **Database** | SQLite (or MySQL) | Relational database engine |
| **WebSockets** | [Laravel Reverb](https://laravel.com/docs/reverb) | Real-time messaging and events broadcasting |
| **AI Services** | Gemini API & Pollinations.ai | Content and image generation engines |
| **PDF Generation**| [Barryvdh DomPDF](https://github.com/barryvdh/laravel-dompdf) | On-the-fly PDF invoice generation |
| **Permissions** | [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) | Role-Based Access Controls (RBAC) |
| **Billing** | [Laravel Cashier](https://laravel.com/docs/billing) | Stripe subscription and billing wrapper |

---

## 🗄️ Database Architecture & Schema

MarketFlow implements a multi-tenant layout grouped into several key models:

* **Multitenancy & Users:**
  * `User`: Application members containing roles, admin permissions, and billing identifiers.
  * `Agency`: Primary tenant entity owning distinct workspaces and portfolios.
  * `Workspace`: Virtual work divisions grouping specific campaign environments.
  * `Client`: External stakeholders bound to workspaces.
* **Campaign & Creative Asset Vault:**
  * `Campaign`: Holds status tracking (Idea, Design, In Progress, Review, Live).
  * `Asset`: Tracks generated/uploaded files, pricing tiers, and territorial boundaries.
  * `Message`: Log of all chat messages and coordinate-based visual annotations.
* **Finance & Procurement:**
  * `Budget`: Holds overall fiscal limits and scope guidelines (Franchise, Department).
  * `BudgetDrawdown`: Tracks requested funds, approvals, and campaign connections.
  * `Rfp` & `Proposal`: Bidding objects linking campaign needs to external `Partners`.
* **E-Commerce & Support:**
  * `Product`: Digital assets, templates, and consulting plans.
  * `Review`: User-submitted comments and star ratings.
  * `Ticket`: Helpdesk support cases tracked by priority.
  * `ApiUsage`: Tracks tokens used for OpenAI/Gemini operations and image credits.

---

## ⚙️ Installation & Configuration

### Prerequisites
* **PHP:** `^8.2`
* **NodeJS:** `^18.x` or higher
* **Composer**
* **Stripe CLI** *(optional, for local webhook testing)*

### 1. Step-by-Step Setup
Clone the repository, navigate into the directory, and run the pre-configured automation script:

```bash
# Automated setup (installs Composer packages, copies env, generates keys, migrates DB, and builds assets)
composer run setup
```

Alternatively, perform the commands manually:
```bash
# Install dependencies
composer install
npm install

# Copy environment variables and generate app key
copy .env.example .env
php artisan key:generate

# Run migrations and seed database
php artisan migrate --seed

# Build frontend assets
npm run build
```

### 2. Configure Gemini AI API Key
To enable live AI copywriting and smart recommendations, update your `.env` file with your Gemini credentials:
```env
GEMINI_API_KEY=your_gemini_api_key_here
```

### 3. Configure Real-Time WebSockets (Laravel Reverb)
Set up Reverb in `.env` to activate live collaboration visual comments:
```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=your_reverb_app_id
REVERB_APP_KEY=your_reverb_app_key
REVERB_APP_SECRET=your_reverb_app_secret
REVERB_HOST="localhost"
REVERB_PORT=8080
```

---

## 🏃 Running the Application

MarketFlow has a unified dev server orchestrator defined in `composer.json` that concurrently boots the HTTP server, asset compilation, Reverb websocket server, and queue worker.

```bash
# Run server, queue listeners, vite compiler, and logs concurrently
composer run dev
```

### Accessing the Panels
* **Main Application:** [http://localhost:8000](http://localhost:8000)
* **Admin Dashboard:** [http://localhost:8000/admin](http://localhost:8000/admin)

### Seeded Credentials
During database seeding, a Super Admin user is created:
* **Email:** `test@example.com`
* **Password:** `password`

---

## 🧪 Testing

The codebase includes automated PHPUnit tests covering database integrity and component responsiveness. Run the test suite using:

```bash
composer run test
```
