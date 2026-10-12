@extends('layouts.landing')

@section('title', 'যোগাযোগ ও সাপোর্ট - SmartPOS SaaS')

@section('content')
<div class="py-5 bg-white border-bottom text-center">
  <div class="container py-3">
    <h1 class="fw-bold mb-2">আমাদের সাথে যোগাযোগ করুন</h1>
    <p class="text-secondary lead mx-auto" style="max-width: 600px;">
      সফটওয়্যার ডেমো, অনবোর্ডিং সহায়তা অথবা যেকোনো প্রশ্নের জন্য আমাদের টিম প্রস্তুত
    </p>
  </div>
</div>

<div class="container py-5">
  <div class="row g-5">
    <div class="col-md-5">
      <div class="p-4 bg-white rounded-4 border shadow-sm">
        <h4 class="fw-bold mb-4">অফিসের ঠিকানা ও যোগাযোগ</h4>
        <div class="d-flex gap-3 mb-4">
          <div class="fs-4 text-primary">📍</div>
          <div>
            <h6 class="fw-bold mb-1">হেড অফিস</h6>
            <p class="text-secondary small mb-0">হাউস ৪২, রোড ১১, ধানমন্ডি, ঢাকা - ১২০৯, বাংলাদেশ</p>
          </div>
        </div>
        <div class="d-flex gap-3 mb-4">
          <div class="fs-4 text-primary">📞</div>
          <div>
            <h6 class="fw-bold mb-1">হেল্পলাইন</h6>
            <p class="text-secondary small mb-0">+৮৮০ ১৭০০-০০০০০০ (সকাল ৯টা - রাত ১০টা)</p>
          </div>
        </div>
        <div class="d-flex gap-3 mb-4">
          <div class="fs-4 text-primary">✉️</div>
          <div>
            <h6 class="fw-bold mb-1">ইমেইল</h6>
            <p class="text-secondary small mb-0">support@smartpos.com.bd</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-7">
      <div class="p-4 bg-white rounded-4 border shadow-sm">
        <h4 class="fw-bold mb-4">মেসেজ পাঠান</h4>
        <form onsubmit="event.preventDefault(); alert('ধন্যবাদ! আপনার মেসেজটি গৃহীত হয়েছে। আমাদের প্রতিনিধি দ্রুত যোগাযোগ করবেন।');">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold">আপনার নাম</label>
              <input type="text" class="form-control" required placeholder="উদাঃ মোঃ রফিকুল ইসলাম">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">মোবাইল নম্বর</label>
              <input type="text" class="form-control" required placeholder="০১৭১১XXXXXX">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold">প্রতিষ্ঠানের নাম</label>
              <input type="text" class="form-control" required placeholder="উদাঃ রহমান ফ্যাশন">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold">বার্তা / প্রশ্ন</label>
              <textarea class="form-control" rows="4" placeholder="আপনার কী ধরণের সহযোগিতা প্রয়োজন লিখুন..."></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn cta-btn-primary px-4 py-2">মেসেজ পাঠান</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
