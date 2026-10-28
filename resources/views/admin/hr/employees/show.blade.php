@extends('layouts.admin')

@section('title', 'Employee Profile - ' . $user->name)

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                
                <!-- Head -->
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Employee Records</h3>
                            <div class="nk-block-des text-soft">
                                <p>Viewing and managing profile records for <strong>{{ $user->name }}</strong>.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.hr.employees.index') }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                                <em class="icon ni ni-arrow-left"></em><span>Back to Employees</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Main Layout -->
                <div class="nk-block">
                    <div class="row g-gs">
                        
                        <!-- Left Panel (Sidebar Card) -->
                        <div class="col-lg-4">
                            <div class="card card-bordered">
                                <div class="card-inner text-center">
                                    <div class="user-avatar lg bg-primary mb-3 style-avatar" style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; margin: 0 auto;">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h4 class="title mb-1">{{ $user->name }}</h4>
                                    <span class="sub-text mb-2">{{ $user->email }}</span>
                                    <div class="mb-3">
                                        @if($user->is_active)
                                            <span class="badge badge-dim bg-success">Active Employee</span>
                                        @else
                                            <span class="badge badge-dim bg-danger">Inactive</span>
                                        @endif
                                    </div>
                                    <hr class="preview-hr">
                                    <div class="text-start mt-3">
                                        <h6 class="overline-title text-soft">Employment Info</h6>
                                        <ul class="list-unstyled mt-2">
                                            <li class="mb-2"><strong>Employee ID:</strong> <span class="badge bg-light text-primary">{{ $user->employee_id ?? 'N/A' }}</span></li>
                                            <li class="mb-2"><strong>Designation:</strong> {{ $user->designation->name ?? $user->job_title ?? 'N/A' }}</li>
                                            <li class="mb-2"><strong>Department:</strong> {{ $user->department->name ?? 'N/A' }}</li>
                                            <li class="mb-2"><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</li>
                                            <li class="mb-2"><strong>Joined On:</strong> {{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel (Tabbed Card) -->
                        <div class="col-lg-8" x-data="{ activeTab: 'profile' }">
                            <div class="card card-bordered">
                                
                                <!-- Tab Header -->
                                <ul class="nav nav-tabs nav-tabs-card mt-0">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" :class="{ 'active': activeTab === 'profile' }" @click.prevent="activeTab = 'profile'">
                                            <em class="icon ni ni-user-circle"></em><span>Profile Details</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" :class="{ 'active': activeTab === 'contracts' }" @click.prevent="activeTab = 'contracts'">
                                            <em class="icon ni ni-file-text"></em><span>Contracts ({{ $user->contracts->count() }})</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" :class="{ 'active': activeTab === 'vault' }" @click.prevent="activeTab = 'vault'">
                                            <em class="icon ni ni-folder-open"></em><span>Document Vault ({{ $user->documents->count() }})</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" :class="{ 'active': activeTab === 'history' }" @click.prevent="activeTab = 'history'">
                                            <em class="icon ni ni-activity"></em><span>Employment History</span>
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-0">
                                    
                                    <!-- TAB 1: Profile Details -->
                                    <div class="tab-pane" :class="{ 'active': activeTab === 'profile' }">
                                        <div class="card-inner">
                                            <h5 class="title mb-4">Edit Profile & Qualifications</h5>
                                            <form action="{{ route('admin.hr.employees.updateProfile', $user->id) }}" method="POST"
                                                  x-data="{
                                                      education: {{ json_encode($user->profile->education ?? []) }},
                                                      certifications: {{ json_encode($user->profile->certifications ?? []) }},
                                                      skills: {{ json_encode($user->profile->skills ?? []) }},
                                                      languages: {{ json_encode($user->profile->languages ?? []) }},
                                                      newSkill: '',
                                                      newLanguage: '',
                                                      addEducation() {
                                                          this.education.push({ school: '', degree: '', year: '' });
                                                      },
                                                      removeEducation(index) {
                                                          this.education.splice(index, 1);
                                                      },
                                                      addCertification() {
                                                          this.certifications.push({ name: '', issuer: '', year: '' });
                                                      },
                                                      removeCertification(index) {
                                                          this.certifications.splice(index, 1);
                                                      },
                                                      addSkill() {
                                                          if (this.newSkill.trim()) {
                                                              this.skills.push(this.newSkill.trim());
                                                              this.newSkill = '';
                                                          }
                                                      },
                                                      removeSkill(index) {
                                                          this.skills.splice(index, 1);
                                                      },
                                                      addLanguage() {
                                                          if (this.newLanguage.trim()) {
                                                              this.languages.push(this.newLanguage.trim());
                                                              this.newLanguage = '';
                                                          }
                                                      },
                                                      removeLanguage(index) {
                                                          this.languages.splice(index, 1);
                                                      }
                                                  }">
                                                @csrf
                                                
                                                <!-- Row 1: Basic Info -->
                                                <div class="row g-4 mb-4">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Full Name</label>
                                                            <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Phone Number</label>
                                                            <input type="text" class="form-control" name="phone" value="{{ old('phone', $user->phone) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Designation</label>
                                                            <select class="form-select form-control" name="designation_id">
                                                                <option value="">No Designation</option>
                                                                @foreach($designations as $desig)
                                                                    <option value="{{ $desig->id }}" {{ old('designation_id', $user->designation_id) == $desig->id ? 'selected' : '' }}>{{ $desig->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Department</label>
                                                            <select class="form-select form-control" name="department_id">
                                                                <option value="">Unassigned</option>
                                                                @foreach($departments as $dept)
                                                                    <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Row 2: Personal Details -->
                                                <h6 class="overline-title text-soft mb-3">Personal Data</h6>
                                                <div class="row g-4 mb-4">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Gender</label>
                                                            <select class="form-select form-control" name="gender">
                                                                <option value="">Select Gender</option>
                                                                <option value="Male" {{ old('gender', $user->profile->gender ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                                                                <option value="Female" {{ old('gender', $user->profile->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                                                                <option value="Other" {{ old('gender', $user->profile->gender ?? '') === 'Other' ? 'selected' : '' }}>Other</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Date of Birth</label>
                                                            <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', isset($user->profile->date_of_birth) ? $user->profile->date_of_birth->format('Y-m-d') : '') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Marital Status</label>
                                                            <select class="form-select form-control" name="marital_status">
                                                                <option value="">Select Marital Status</option>
                                                                <option value="Single" {{ old('marital_status', $user->profile->marital_status ?? '') === 'Single' ? 'selected' : '' }}>Single</option>
                                                                <option value="Married" {{ old('marital_status', $user->profile->marital_status ?? '') === 'Married' ? 'selected' : '' }}>Married</option>
                                                                <option value="Divorced" {{ old('marital_status', $user->profile->marital_status ?? '') === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                                                <option value="Widowed" {{ old('marital_status', $user->profile->marital_status ?? '') === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Emergency Contact Name</label>
                                                            <input type="text" class="form-control" name="emergency_contact_name" value="{{ old('emergency_contact_name', $user->profile->emergency_contact_name ?? '') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Emergency Contact Phone</label>
                                                            <input type="text" class="form-control" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $user->profile->emergency_contact_phone ?? '') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="form-label">Residential Address</label>
                                                            <textarea class="form-control" name="address" rows="2">{{ old('address', $user->profile->address ?? '') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Row 3: Qualifications (Alpine.js) -->
                                                <h6 class="overline-title text-soft mb-3">Qualifications & Skills</h6>
                                                
                                                <!-- Education -->
                                                <div class="mb-4 bg-light p-3 rounded">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="fw-bold">Education History</span>
                                                        <button type="button" class="btn btn-xs btn-outline-primary" @click="addEducation"><em class="icon ni ni-plus"></em> Add Education</button>
                                                    </div>
                                                    <template x-for="(edu, index) in education" :key="index">
                                                        <div class="row g-2 mb-2 align-items-end">
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control form-control-sm" :name="'education['+index+'][school]'" x-model="edu.school" placeholder="School/University" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control form-control-sm" :name="'education['+index+'][degree]'" x-model="edu.degree" placeholder="Degree/Course" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="number" class="form-control form-control-sm" :name="'education['+index+'][year]'" x-model="edu.year" placeholder="Graduation Year" required>
                                                            </div>
                                                            <div class="col-md-1 text-end">
                                                                <button type="button" class="btn btn-sm btn-icon btn-trigger text-danger" @click="removeEducation(index)"><em class="icon ni ni-trash"></em></button>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <div x-show="education.length === 0" class="text-muted text-center py-2 fs-12px">No education records added yet.</div>
                                                </div>

                                                <!-- Certifications -->
                                                <div class="mb-4 bg-light p-3 rounded">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="fw-bold">Certifications & Licenses</span>
                                                        <button type="button" class="btn btn-xs btn-outline-primary" @click="addCertification"><em class="icon ni ni-plus"></em> Add Certification</button>
                                                    </div>
                                                    <template x-for="(cert, index) in certifications" :key="index">
                                                        <div class="row g-2 mb-2 align-items-end">
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control form-control-sm" :name="'certifications['+index+'][name]'" x-model="cert.name" placeholder="Certificate Name" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control form-control-sm" :name="'certifications['+index+'][issuer]'" x-model="cert.issuer" placeholder="Issuing Body" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="number" class="form-control form-control-sm" :name="'certifications['+index+'][year]'" x-model="cert.year" placeholder="Year" required>
                                                            </div>
                                                            <div class="col-md-1 text-end">
                                                                <button type="button" class="btn btn-sm btn-icon btn-trigger text-danger" @click="removeCertification(index)"><em class="icon ni ni-trash"></em></button>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <div x-show="certifications.length === 0" class="text-muted text-center py-2 fs-12px">No certifications added yet.</div>
                                                </div>

                                                <!-- Skills Tags -->
                                                <div class="row mb-4">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Professional Skills</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control form-control-sm" x-model="newSkill" placeholder="Enter a skill..." @keydown.enter.prevent="addSkill">
                                                                <button class="btn btn-sm btn-primary" type="button" @click="addSkill">Add</button>
                                                            </div>
                                                            <div class="mt-2 d-flex flex-wrap gap-1">
                                                                <template x-for="(skill, index) in skills" :key="index">
                                                                    <span class="badge bg-outline-info text-info d-flex align-items-center">
                                                                        <span x-text="skill"></span>
                                                                        <input type="hidden" :name="'skills['+index+']'" :value="skill">
                                                                        <a href="#" class="ms-1 text-info" @click.prevent="removeSkill(index)"><em class="icon ni ni-cross"></em></a>
                                                                    </span>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Languages -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Languages Spoken</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control form-control-sm" x-model="newLanguage" placeholder="Enter a language..." @keydown.enter.prevent="addLanguage">
                                                                <button class="btn btn-sm btn-primary" type="button" @click="addLanguage">Add</button>
                                                            </div>
                                                            <div class="mt-2 d-flex flex-wrap gap-1">
                                                                <template x-for="(lang, index) in languages" :key="index">
                                                                    <span class="badge bg-outline-success text-success d-flex align-items-center">
                                                                        <span x-text="lang"></span>
                                                                        <input type="hidden" :name="'languages['+index+']'" :value="lang">
                                                                        <a href="#" class="ms-1 text-success" @click.prevent="removeLanguage(index)"><em class="icon ni ni-cross"></em></a>
                                                                    </span>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Action -->
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- TAB 2: Contracts -->
                                    <div class="tab-pane" :class="{ 'active': activeTab === 'contracts' }">
                                        <div class="card-inner">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h5 class="title">Contracts & Agreements</h5>
                                                <button data-bs-toggle="modal" data-bs-target="#addContractModal" class="btn btn-sm btn-primary"><em class="icon ni ni-plus"></em> Add Contract</button>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Contract Type</th>
                                                            <th>Period</th>
                                                            <th>Salary</th>
                                                            <th>Status</th>
                                                            <th>Attachment</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($user->contracts as $contract)
                                                        <tr>
                                                            <td><strong>{{ $contract->contract_type }}</strong></td>
                                                            <td>
                                                                {{ $contract->start_date->format('M d, Y') }} - 
                                                                {{ $contract->end_date ? $contract->end_date->format('M d, Y') : 'Indefinite' }}
                                                            </td>
                                                            <td>@currency($contract->basic_salary)</td>
                                                            <td>
                                                                <span class="badge {{ $contract->status === 'Active' ? 'bg-success' : ($contract->status === 'Pending' ? 'bg-warning' : 'bg-danger') }}">
                                                                    {{ $contract->status }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($contract->document_path)
                                                                    <a href="{{ asset('storage/' . $contract->document_path) }}" target="_blank" class="btn btn-xs btn-outline-primary"><em class="icon ni ni-download"></em> Download</a>
                                                                @else
                                                                    <span class="text-muted fs-12px">No File</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center p-3 text-muted">No contracts generated yet.</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TAB 3: Document Vault -->
                                    <div class="tab-pane" :class="{ 'active': activeTab === 'vault' }">
                                        <div class="card-inner">
                                            <div class="row g-gs">
                                                
                                                <!-- Upload Document Form -->
                                                <div class="col-md-5 border-end">
                                                    <h5 class="title mb-3">Upload Vault Document</h5>
                                                    <form action="{{ route('admin.hr.employees.addDocument', $user->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="form-group mb-3">
                                                            <label class="form-label">Document Name</label>
                                                            <input type="text" class="form-control" name="document_name" placeholder="e.g. CV, Driving License" required>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label class="form-label">Document Type</label>
                                                            <select class="form-select form-control" name="document_type" required>
                                                                <option value="CV">CV / Resume</option>
                                                                <option value="ID Card">ID / Passport</option>
                                                                <option value="Certificate">Qualification Certificate</option>
                                                                <option value="Contract">Signed Contract</option>
                                                                <option value="Other">Other Document</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label class="form-label">File File</label>
                                                            <input type="file" class="form-control" name="file" required>
                                                            <span class="text-soft fs-11px">PDF, DOC, DOCX, JPG, PNG, ZIP. Max 5MB.</span>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label class="form-label">Notes (optional)</label>
                                                            <textarea class="form-control" name="notes" rows="2"></textarea>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary w-100">Upload to Vault</button>
                                                    </form>
                                                </div>

                                                <!-- Document List -->
                                                <div class="col-md-7">
                                                    <h5 class="title mb-3">Vault Contents</h5>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Type</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($user->documents as $doc)
                                                            <tr>
                                                                <td>
                                                                    <strong>{{ $doc->document_name }}</strong>
                                                                    <span class="sub-text text-muted fs-11px">{{ $doc->created_at->format('d M Y, h:i A') }}</span>
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-dim bg-info">{{ $doc->document_type }}</span>
                                                                </td>
                                                                <td>
                                                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-xs btn-icon btn-dim btn-primary"><em class="icon ni ni-download"></em></a>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center p-3 text-muted">Document Vault is empty.</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- TAB 4: Employment History -->
                                    <div class="tab-pane" :class="{ 'active': activeTab === 'history' }">
                                        <div class="card-inner">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h5 class="title">Career Trajectory Timeline</h5>
                                                <button data-bs-toggle="modal" data-bs-target="#addHistoryModal" class="btn btn-sm btn-primary"><em class="icon ni ni-plus"></em> Log Change</button>
                                            </div>

                                            <!-- Career Timeline View -->
                                            <div class="nk-timeline">
                                                <ul class="nk-timeline-list">
                                                    @forelse($user->employmentHistories as $history)
                                                    <li class="nk-timeline-item">
                                                        <div class="nk-timeline-item-inner">
                                                            <div class="nk-timeline-symbol bg-primary"><em class="icon ni ni-activity-round-fill"></em></div>
                                                            <div class="nk-timeline-content">
                                                                <div class="nk-timeline-title-group">
                                                                    <h5 class="nk-timeline-title">{{ $history->designation->name ?? $history->job_title }}</h5>
                                                                    <span class="badge bg-light text-primary">{{ $history->change_reason }}</span>
                                                                </div>
                                                                <div class="nk-timeline-date">
                                                                    {{ $history->start_date->format('M d, Y') }} - 
                                                                    {{ $history->end_date ? $history->end_date->format('M d, Y') : 'Present' }}
                                                                </div>
                                                                <p class="text-soft">
                                                                    <strong>Department:</strong> {{ $history->department->name ?? 'Unassigned' }}<br>
                                                                    @if($history->basic_salary)
                                                                        <strong>Salary:</strong> @currency($history->basic_salary)<br>
                                                                    @endif
                                                                    @if($history->notes)
                                                                        <strong>Notes:</strong> {{ $history->notes }}
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @empty
                                                    <li class="nk-timeline-item">
                                                        <div class="nk-timeline-item-inner">
                                                            <div class="nk-timeline-symbol bg-secondary"><em class="icon ni ni-arrow-right"></em></div>
                                                            <div class="nk-timeline-content">
                                                                <h5 class="nk-timeline-title">Initial Onboarding</h5>
                                                                <div class="nk-timeline-date">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</div>
                                                                <p class="text-soft">Employee joined the enterprise.</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @endforelse
                                                </ul>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Contract -->
<div class="modal fade" tabindex="-1" id="addContractModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate New Contract</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.hr.employees.addContract', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label">Contract Type</label>
                        <select class="form-select form-control" name="contract_type" required>
                            <option value="Full-time">Full-time Contract</option>
                            <option value="Part-time">Part-time Contract</option>
                            <option value="Contract">Fixed-term Contract</option>
                            <option value="Internship">Internship Agreement</option>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">End Date (optional)</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Basic Salary</label>
                        <div class="form-control-wrap">
                            <div class="form-icon form-icon-left" style="font-style: normal; font-weight: bold;">@currencySymbol</div>
                            <input type="number" step="0.01" class="form-control" name="basic_salary" placeholder="e.g. 5500.00" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Initial Status</label>
                        <select class="form-select form-control" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Pending">Pending Signature</option>
                            <option value="Expired">Expired</option>
                            <option value="Terminated">Terminated</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Upload Signed Document (optional)</label>
                        <input type="file" class="form-control" name="document">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Notes (optional)</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                    <div class="form-group text-end mt-4">
                        <button type="submit" class="btn btn-primary">Save Contract</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Log History Milestone -->
<div class="modal fade" tabindex="-1" id="addHistoryModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Career/Role Change</h5>
                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.hr.employees.addHistory', $user->id) }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label">New Designation</label>
                        <select class="form-select form-control" name="designation_id">
                            <option value="">No Designation</option>
                            @foreach($designations as $desig)
                                <option value="{{ $desig->id }}" {{ $user->designation_id == $desig->id ? 'selected' : '' }}>{{ $desig->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">New Department</label>
                        <select class="form-select form-control" name="department_id">
                            <option value="">Unassigned</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Effective Date</label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">End Date (optional)</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Basic Salary (optional)</label>
                        <div class="form-control-wrap">
                            <div class="form-icon form-icon-left" style="font-style: normal; font-weight: bold;">@currencySymbol</div>
                            <input type="number" step="0.01" class="form-control" name="basic_salary" placeholder="Leave blank if unchanged">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Change Reason</label>
                        <select class="form-select form-control" name="change_reason" required>
                            <option value="Promotion">Promotion</option>
                            <option value="Transfer">Departmental Transfer</option>
                            <option value="Initial Hiring">Initial Hiring</option>
                            <option value="Salary Adjustment">Salary Adjustment</option>
                            <option value="Role Reclassification">Role Reclassification</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Notes (optional)</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                    <div class="form-group text-end mt-4">
                        <button type="submit" class="btn btn-primary">Save Milestone</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
