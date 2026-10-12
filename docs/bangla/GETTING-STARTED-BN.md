# 🚀 কীভাবে শুরু করবেন (Getting Started)

## ১. লোকাল সার্ভার প্রস্তুতি
১. XAMPP বা Wamp সার্ভার চালু করুন (Apache এবং MySQL চালু থাকতে হবে)।
২. ব্রাউজারে `http://localhost/phpmyadmin` এ যান এবং নিশ্চিত হন `pos_saas` ডাটাবেস উপস্থিত আছে।
৩. প্রজেক্ট ফোল্ডারে টার্মিনাল খুলে ডিপেন্ডেন্সি এবং মাইগ্রেশন চেক করুন:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
৪. লোকাল ডেভেলপমেন্ট সার্ভার চালু করুন:
   ```bash
   php artisan serve
   ```
৫. ব্রাউজারে প্রবেশ করুন: `http://localhost:8000`

## ২. ডেমো অ্যাকাউন্ট দিয়ে সরাসরি টেস্ট করুন
- **সুপার অ্যাডমিন (Super Admin)**:
  - ইমেইল: `admin@possaas.com`
  - পাসওয়ার্ড: `password123`
  - URL: `http://localhost:8000/admin/dashboard`
- **ব্যবসার মালিক (Business Owner)**:
  - ইমেইল: `owner@rahmanfashion.com`
  - পাসওয়ার্ড: `password123`
  - URL: `http://localhost:8000/dashboard`
- **ক্যাশিয়ার (Cashier)**:
  - ইমেইল: `cashier1@rahmanfashion.com`
  - পাসওয়ার্ড: `password123`
  - URL: `http://localhost:8000/pos`
