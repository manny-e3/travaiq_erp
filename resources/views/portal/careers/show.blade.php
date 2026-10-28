<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $jobPosting->title }} - Career Opportunity</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, hsl(220, 40%, 12%), hsl(240, 30%, 6%));
            --card-bg: rgba(255, 255, 255, 0.03);
            --card-border: rgba(255, 255, 255, 0.06);
            --primary: hsl(250, 85%, 65%);
            --primary-hover: hsl(250, 85%, 72%);
            --text-main: hsl(0, 0%, 96%);
            --text-muted: hsl(220, 15%, 70%);
            --input-bg: rgba(255, 255, 255, 0.04);
            --input-border: rgba(255, 255, 255, 0.1);
            --input-focus: hsl(250, 85%, 65%);
            --success: hsl(140, 70%, 45%);
            --success-bg: rgba(40, 190, 100, 0.1);
            --success-border: rgba(40, 190, 100, 0.2);
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-gradient);
            color: var(--text-main);
            min-height: 100vh;
            padding: 3rem 1.5rem;
        }

        .container {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .nav-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2.5rem;
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 1rem;
        }

        .nav-bar a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .nav-bar a:hover {
            color: #fff;
        }

        .nav-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 2.5rem;
            align-items: start;
        }

        @media (max-width: 991px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }

        .job-detail-col {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 3rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: var(--shadow);
        }

        @media (max-width: 600px) {
            .job-detail-col {
                padding: 1.5rem;
            }
        }

        .category-badge {
            background: rgba(120, 80, 255, 0.15);
            color: hsl(250, 80%, 75%);
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 99px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 1rem;
            line-height: 1.25;
        }

        .meta {
            font-size: 0.85rem;
            color: var(--text-muted);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 1rem;
        }

        .content {
            font-size: 1.05rem;
            line-height: 1.8;
            color: hsl(0, 0%, 90%);
        }

        .content h2, .content h3 {
            font-family: 'Outfit', sans-serif;
            color: #fff;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .content p {
            margin-bottom: 1.25rem;
        }

        .content ul, .content ol {
            margin-bottom: 1.25rem;
            padding-left: 1.5rem;
        }

        .content li {
            margin-bottom: 0.5rem;
        }

        .form-col {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.5rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: var(--shadow);
            position: sticky;
            top: 2rem;
            max-height: calc(100vh - 4rem);
            overflow-y: auto;
        }

        .form-col::-webkit-scrollbar {
            width: 6px;
        }
        .form-col::-webkit-scrollbar-track {
            background: transparent;
        }
        .form-col::-webkit-scrollbar-thumb {
            background: var(--card-border);
            border-radius: 3px;
        }
        .form-col::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .form-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #fff;
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 0.75rem;
        }

        .alert-success {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: #fff;
            padding: 1.25rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .alert-success strong {
            color: var(--success);
            font-weight: 600;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        input, textarea {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            padding: 0.8rem 1rem;
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.25s;
            width: 100%;
        }

        input:focus, textarea:focus {
            border-color: var(--input-focus);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(120, 80, 255, 0.15);
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .error-message {
            color: hsl(0, 80%, 60%);
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        /* File upload wrapper */
        .file-upload-wrapper {
            position: relative;
            border: 2px dashed var(--input-border);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            background: var(--input-bg);
            cursor: pointer;
            transition: all 0.25s;
        }

        .file-upload-wrapper:hover {
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.06);
        }

        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-upload-text {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .file-upload-text strong {
            color: hsl(250, 80%, 75%);
        }

        .file-name-display {
            font-size: 0.85rem;
            color: #fff;
            margin-top: 0.5rem;
            font-weight: 500;
            display: none;
        }

        .submit-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 1rem 2rem;
            font-family: 'Outfit', sans-serif;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 14px rgba(120, 80, 255, 0.3);
            width: 100%;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(120, 80, 255, 0.45);
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="nav-bar">
            <a href="{{ route('careers.index') }}">← Back to Job Openings</a>
            <span class="nav-title">Apply Now</span>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <strong>Application Received!</strong> {{ session('success') }}
            </div>
        @endif

        <div class="main-grid">
            <!-- Left Side: Job details -->
            <div class="job-detail-col">
                <span class="category-badge">{{ $jobPosting->department->name ?? 'General' }}</span>
                <h1>{{ $jobPosting->title }}</h1>
                
                <div class="meta">
                    <span>📍 {{ $jobPosting->location }}</span>
                    <span>•</span>
                    <span>💼 {{ $jobPosting->employment_type }}</span>
                    @if($jobPosting->closing_date)
                        <span>•</span>
                        <span>Closing date: {{ $jobPosting->closing_date->format('M d, Y') }}</span>
                    @endif
                </div>

                <div class="content">
                    {!! $jobPosting->description !!}
                </div>
            </div>

            <!-- Right Side: Application Form -->
            <div class="form-col">
                <h3 class="form-title">Submit Application</h3>
                
                <form action="{{ route('careers.apply', $jobPosting->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="John" required>
                        @error('first_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Doe" required>
                        @error('last_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="johndoe@email.com" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 012-3456">
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="cover_letter">Cover Letter (Optional)</label>
                        <textarea id="cover_letter" name="cover_letter" placeholder="Why are you a good fit for this role? Describe briefly...">{{ old('cover_letter') }}</textarea>
                        @error('cover_letter')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Resume / CV (PDF, DOC, DOCX)</label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="resume_file" id="resume_file" class="file-upload-input" accept=".pdf,.doc,.docx" required>
                            <div class="file-upload-text">
                                <span style="font-size: 1.5rem; display: block; margin-bottom: 0.25rem;">📄</span>
                                Drop your CV here or <strong>browse files</strong>
                            </div>
                            <div class="file-name-display" id="file-name-display"></div>
                        </div>
                        @error('resume_file')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="submit-btn">Submit Application</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('resume_file');
            const display = document.getElementById('file-name-display');

            fileInput.addEventListener('change', function() {
                if (fileInput.files && fileInput.files.length > 0) {
                    display.innerText = "Selected file: " + fileInput.files[0].name;
                    display.style.display = "block";
                } else {
                    display.style.display = "none";
                }
            });
        });
    </script>
</body>
</html>
