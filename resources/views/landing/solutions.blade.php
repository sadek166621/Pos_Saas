@extends('layouts.landing')

@section('title', 'ব্যবসার সমাধান - SmartPOS SaaS')

@section('content')
<div class="py-5 bg-white border-bottom text-center">
  <div class="container py-3">
    <h1 class="fw-bold mb-2">প্রতিটি ব্যবসার জন্য বিশেষায়িত সলিউশন</h1>
    <p class="text-secondary lead mx-auto" style="max-width: 600px;">
      আপনার ব্যবসার প্রকৃতি অনুযায়ী সেরা কার্যপদ্ধতি ও ফিচার সেটআপ
    </p>
  </div>
</div>

<div class="container py-5">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="feature-card h-100">
        <div class="fs-1 mb-2">🛒</div>
        <h4 class="fw-bold mb-2">সুপার শপ ও গ্রোসারি</h4>
        <p class="text-secondary small">
          ওজন স্কেল ইন্টিগ্রেশন, ফাস্ট বারকোড স্ক্যানিং, প্যাকেটজাত আইটেমের এক্সপায়ারি ডেট ট্র্যাকিং এবং কম স্টকের দ্রুত নোটিফিকেশন।
        </p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card h-100">
        <div class="fs-1 mb-2">👔</div>
        <h4 class="fw-bold mb-2">ফ্যাশন, বুটিক ও জুতা</h4>
        <p class="text-secondary small">
          সাইজ ও কালার ভ্যারিয়েন্ট, নিজস্ব বারকোড লেবেল তৈরি ও প্রিন্ট, মৌসুমি ডিসকাউন্ট এবং ভিআইপি কাস্টমার লয়্যালটি পয়েন্ট।
        </p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card h-100">
        <div class="fs-1 mb-2">📱</div>
        <h4 class="fw-bold mb-2">ইলেকট্রনিক্স ও মোবাইল</h4>
        <p class="text-secondary small">
          IMEI ও সিরিয়াল নম্বর ট্র্যাকিং, ওয়ারেন্টি ম্যানেজমেন্ট এবং রিপেয়ার ও সার্ভিসিং চার্জের আলাদা ইনভয়েসিং।
        </p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card h-100">
        <div class="fs-1 mb-2">💊</div>
        <h4 class="fw-bold mb-2">ফার্মেসি ও ড্রাগ স্টোর</h4>
        <p class="text-secondary small">
          ঔষধের জেনেরিক নাম ও ব্র্যান্ড সার্চ, ব্যাচ ট্র্যাকিং, এক্সপায়ারি এলার্ট এবং প্রেসক্রিপশন ভিত্তিক বিক্রয় রেকর্ড।
        </p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card h-100">
        <div class="fs-1 mb-2">🏭</div>
        <h4 class="fw-bold mb-2">পাইকারি ও ডিস্ট্রিবিউশন</h4>
        <p class="text-secondary small">
          হোলসেল ও ডিলার প্রাইসিং, বড় অঙ্কের বাকির খাতা ও লেজার স্টেটমেন্ট, পারচেজ অর্ডার এবং গুদাম স্থানান্তর।
        </p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card h-100">
        <div class="fs-1 mb-2">🍽️</div>
        <h4 class="fw-bold mb-2">রেস্তোরাঁ ও ক্যাফে</h4>
        <p class="text-secondary small">
          টেবিল অর্ডার, কিচেন অর্ডার টিকেট (KOT), ভ্যাট ও সার্ভিস চার্জ ক্যালকুলেশন এবং দ্রুততম কাউন্টার চেকআউট।
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
