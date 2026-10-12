@extends('layouts.app')

@section('title', 'ব্যবসার সেটিংস ও কনফিগারেশন - SmartPOS SaaS')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-4">
  <div>
    <h3 class="mb-1 fw-bold">সিস্টেম ও ব্যবসা সেটিংস (Settings)</h3>
    <p class="text-secondary small mb-0">ব্যবসায়িক পরিচিতি, ভ্যাট ও ট্যাক্স (BIN/TIN), এবং বাংলা এসএমএস টেমপ্লেট কনফিগারেশন</p>
  </div>
</div>

<div class="row g-4">
  <!-- Business Profile & Tax Settings -->
  <div class="col-lg-6">
    <div class="card bg-white border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-4 p-md-5">
        <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
          <i data-lucide="building" class="icon-sm text-primary"></i> ব্যবসায়িক প্রোফাইল ও ভ্যাট সেটিংস
        </h5>
        
        <form action="{{ route('settings.updateBusiness') }}" method="POST">
          @csrf
          <div class="mb-4">
            <label class="form-label fw-bold">ব্যবসার নাম (Business Name)</label>
            <input type="text" name="name" class="form-control" value="{{ $tenant->name }}" required>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-bold">মোবাইল নম্বর</label>
              <input type="text" name="phone" class="form-control" value="{{ $tenant->phone }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">ইমেইল</label>
              <input type="email" name="email" class="form-control" value="{{ $tenant->email }}">
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-bold">ঠিকানা (Address)</label>
            <input type="text" name="address" class="form-control" value="{{ $tenant->address }}" required>
          </div>

          <div class="mb-4">
            <label class="form-label fw-bold">শহর (City)</label>
            <input type="text" name="city" class="form-control" value="{{ $tenant->city }}" required>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-bold">ভ্যাট নিবন্ধন নম্বর (BIN Number)</label>
              <input type="text" name="bin_number" class="form-control" value="{{ $tenant->bin_number }}" placeholder="যেমন: 001234567-0101">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">ট্যাক্স আইডেন্টিফিকেশন (TIN Number)</label>
              <input type="text" name="tin_number" class="form-control" value="{{ $tenant->tin_number }}" placeholder="যেমন: 123456789012">
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <label class="form-label fw-bold">মুদ্রা (Currency)</label>
              <input type="text" name="currency" class="form-control" value="{{ $tenant->currency }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-bold">মুদ্রার প্রতীক (Symbol)</label>
              <input type="text" name="currency_symbol" class="form-control" value="{{ $tenant->currency_symbol }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-bold">টাইমজোন</label>
              <input type="text" name="timezone" class="form-control" value="{{ $tenant->timezone }}" required>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
            <i data-lucide="save" class="icon-sm"></i> সেটিংস সংরক্ষণ করুন
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- SMS Templates -->
  <div class="col-lg-6">
    <div class="card bg-white border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-4 p-md-5">
        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
          <i data-lucide="message-square" class="icon-sm text-info"></i> বাংলা ও ইংরেজি এসএমএস টেমপ্লেট
        </h5>
        <p class="text-secondary small mb-4">গ্রাহকদের কাছে স্বয়ংক্রিয় নোটিফিকেশন পাঠাতে টেমপ্লেটের ট্যাগসমূহ ব্যবহার করুন: <code>{business_name}</code>, <code>{invoice_number}</code>, <code>{total}</code>, <code>{due}</code></p>

        <div class="accordion" id="smsAccordion">
          @forelse($smsTemplates as $idx => $tmpl)
            <div class="accordion-item border rounded-3 mb-3">
              <h2 class="accordion-header" id="heading{{ $tmpl->id }}">
                <button class="accordion-button {{ $idx > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $tmpl->id }}">
                  <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $tmpl->type)) }}</span>
                  <span class="badge ms-2 {{ $tmpl->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $tmpl->is_active ? 'সক্রিয়' : 'বন্ধ' }}
                  </span>
                </button>
              </h2>
              <div id="collapse{{ $tmpl->id }}" class="accordion-collapse collapse {{ $idx === 0 ? 'show' : '' }}" data-bs-parent="#smsAccordion">
                <div class="accordion-body">
                  <form action="{{ route('settings.updateSmsTemplate', $tmpl->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                      <label class="form-label fw-bold">বাংলা বার্তা (Bangla SMS)</label>
                      <textarea name="template_bn" class="form-control" rows="3" required>{{ $tmpl->template_bn }}</textarea>
                    </div>

                    <div class="mb-3">
                      <label class="form-label fw-bold">English SMS</label>
                      <textarea name="template_en" class="form-control" rows="2" required>{{ $tmpl->template_en }}</textarea>
                    </div>

                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active{{ $tmpl->id }}" {{ $tmpl->is_active ? 'checked' : '' }}>
                      <label class="form-check-label" for="active{{ $tmpl->id }}">এই টেমপ্লেট সক্রিয় রাখুন</label>
                    </div>

                    <button type="submit" class="btn btn-sm btn-outline-primary fw-bold">
                      <i data-lucide="check" class="icon-xs"></i> টেমপ্লেট সেভ করুন
                    </button>
                  </form>
                </div>
              </div>
            </div>
          @empty
            <div class="alert alert-light text-center">কোনো এসএমএস টেমপ্লেট কনফিগার করা নেই।</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
