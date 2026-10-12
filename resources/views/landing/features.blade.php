@extends('layouts.landing')

@section('title', 'সিস্টেম ফিচারসমূহ - SmartPOS SaaS')

@section('content')
<div class="py-5 bg-white border-bottom text-center">
  <div class="container py-3">
    <h1 class="fw-bold mb-2">SmartPOS এর অত্যাধুনিক ফিচারসমূহ</h1>
    <p class="text-secondary lead mx-auto" style="max-width: 600px;">
      একটি আধুনিক বিজনেসের জন্য প্রয়োজনীয় সকল ডিজিটাল মডিউল
    </p>
  </div>
</div>

<div class="container py-5">
  <div class="row g-5 align-items-center mb-5">
    <div class="col-md-6">
      <span class="badge bg-primary-subtle text-primary fw-bold mb-2">মডিউল ০১</span>
      <h2 class="fw-bold mb-3">গতিশীল POS ও ক্যাশ কাউন্টার</h2>
      <p class="text-secondary mb-3">
        কাস্টমারের দীর্ঘ লাইন সামলাতে প্রয়োজন বিদ্যুৎগতির বিলিং সিস্টেম। বারকোড স্ক্যানার দিয়ে স্ক্যান করার সাথে সাথেই আইটেম কার্টে যুক্ত হয়।
      </p>
      <ul class="text-secondary small ps-3">
        <li class="mb-2">হোল্ড কার্ট (Hold Cart) সুবিধা—একই সাথে একাধিক কাস্টমারের বিল প্রসেস করা যায়।</li>
        <li class="mb-2">স্প্লিট পেমেন্ট—ক্যাশ, বিকাশ, নগদ ও কার্ডে একই অর্ডারে আংশিক পেমেন্ট।</li>
        <li class="mb-2">৫৮মিমি ও ৮০মিমি থার্মাল পেপার রিসিট ও A4 ইনভয়েস প্রিন্ট।</li>
      </ul>
    </div>
    <div class="col-md-6 text-center">
      <div class="bg-light p-4 rounded-4 border shadow-sm">
        <div class="p-3 bg-white rounded-3 border text-start">
          <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
            <strong>ইনভয়েস #INV-20261012-0001</strong>
            <span class="badge bg-success">পরিশোধিত</span>
          </div>
          <div class="small text-muted mb-1">কটন পাঞ্জাবি x ১ = ৳১,৮০০</div>
          <div class="small text-muted mb-2">ফাস্ট চার্জার x ১ = ৳১,০৫০</div>
          <div class="border-top pt-2 d-flex justify-content-between fw-bold">
            <span>সর্বমোট:</span>
            <span>৳২,৮৫০</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-5 align-items-center mb-5 flex-md-row-reverse">
    <div class="col-md-6">
      <span class="badge bg-success-subtle text-success fw-bold mb-2">মডিউল ০২</span>
      <h2 class="fw-bold mb-3">মাল্টি-ব্রাঞ্চ স্টক ও ব্রাঞ্চ প্রাইস ওভাররাইড</h2>
      <p class="text-secondary mb-3">
        আপনার ব্যবসা যদি ঢাকা, চট্টগ্রাম বা সিলেটে বিস্তৃত থাকে, তবে কেন্দ্রীয় ড্যাশবোর্ড থেকেই সব আউটলেটের স্টক ও সেলস নিয়ন্ত্রণ করুন।
      </p>
      <ul class="text-secondary small ps-3">
        <li class="mb-2">লোকেশন-ভিত্তিক আলাদা বিক্রয়মূল্য নির্ধারণ করার সুযোগ।</li>
        <li class="mb-2">ব্রাঞ্চ থেকে ব্রাঞ্চে পণ্য স্থানান্তর ও রিসিভ ট্র্যাকিং।</li>
        <li class="mb-2">ব্রাঞ্চ ম্যানেজারের জন্য নিজস্ব ব্রাঞ্চের সীমায়িত অ্যাক্সেস পারমিশন।</li>
      </ul>
    </div>
    <div class="col-md-6 text-center">
      <div class="bg-light p-4 rounded-4 border shadow-sm">
        <div class="text-start">
          <div class="p-2 mb-2 bg-white rounded border d-flex justify-content-between">
            <span>ঢাকা ধানমন্ডি শাখা</span>
            <span class="fw-bold text-success">৳৮,০০,০০০ বিক্রি</span>
          </div>
          <div class="p-2 mb-2 bg-white rounded border d-flex justify-content-between">
            <span>চট্টগ্রাম জিইসি শাখা</span>
            <span class="fw-bold text-primary">৳৪,৫০,০০০ বিক্রি</span>
          </div>
          <div class="p-2 bg-white rounded border d-flex justify-content-between">
            <span>সিলেট জিন্দাবাজার শাখা</span>
            <span class="fw-bold text-info">৳৩,০০,০০০ বিক্রি</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
