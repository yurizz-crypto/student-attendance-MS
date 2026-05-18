# Student Attendance Monitoring System (StudentAMS)

Welcome to the **Student Attendance Monitoring System (StudentAMS)**. This is a comprehensive, high-performance web application designed to track and manage student attendance seamlessly across academic institutions. 

Built with scalability in mind, StudentAMS is optimized to handle millions of records efficiently, offering powerful background processing and robust reporting features.

## 🚀 Key Features

- **Bulk Attendance Recording:** Rapidly log attendance for entire class sections with optimized database queries and upsert architecture.
- **Asynchronous Processing:** Heavy tasks like bulk operations and report generations are intelligently queued to ensure the user interface remains lightning-fast.
- **Advanced Export Engine:** Generate detailed PDF, Excel, and CSV attendance reports utilizing optimized database aggregations to prevent memory overloads.
- **Optimistic Locking:** Secure concurrent edits for User Management, preventing administrators from accidentally overwriting each other's changes.
- **Comprehensive Audit Logs:** Track every action across the system. 
- **Two-Factor Authentication (OTP):** Secure accounts with additional OTP verification layers.

## ⚙️ System Requirements

- PHP 8.3+
- Node.js & npm (for compiling frontend assets)
- PostgreSQL or MySQL (Optimized for large-scale schemas)
- Composer

## 🛠️ Quick Start Installation

1. **Clone the repository and install dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Configure Environment:**
   Copy the `.env.example` file to `.env` and fill in your database credentials.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Run Migrations & Queue Tables:**
   Set up the database structure and necessary indexes.
   ```bash
   php artisan migrate
   ```

4. **Start the System:**
   You will need three separate terminal windows to run the system properly:
   
   *Terminal 1 (PHP Server):*
   ```bash
   php artisan serve
   ```
   
   *Terminal 2 (Asset Bundler):*
   ```bash
   npm run dev
   ```
   
   *Terminal 3 (Background Queue Worker - Critical for Attendance):*
   ```bash
   php artisan queue:work
   ```

## 📊 Performance Architecture

StudentAMS is engineered for high throughput:
- **Index-Optimized Schema:** Critical tables (`attendance_records`, `attendance_sessions`) use explicit database indexes for `status`, `student_id`, and `date` resolving N+1 bottlenecks.
- **Lazy Processing:** Reports rely on Database Query Builder group aggregations (`groupBy()`, `count()`) instead of loading ORM models into memory.
- **Job Queues:** Attendance logs are instantly queued via `RecordAttendanceJob` to return immediate HTTP responses.

## 🛡️ License

StudentAMS is proprietary software. All rights reserved.
