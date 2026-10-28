<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careers & Job Opportunities</title>
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
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 1000px;
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

        .header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 3rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            background: linear-gradient(to right, #fff, hsl(250, 80%, 80%));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .header p {
            color: var(--text-muted);
            font-size: 1.15rem;
            font-weight: 300;
            margin-bottom: 2rem;
        }

        .filters-container {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 1.5rem;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: var(--shadow);
            margin-bottom: 3rem;
        }

        .filters-form {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 1rem;
            align-items: end;
        }

        @media (max-width: 900px) {
            .filters-form {
                grid-template-columns: 1fr 1fr;
            }
            .filters-form button {
                grid-column: span 2;
            }
        }

        @media (max-width: 600px) {
            .filters-form {
                grid-template-columns: 1fr;
            }
            .filters-form button {
                grid-column: span 1;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .form-group label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-input, .form-select {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.2s;
            width: 100%;
        }

        .form-input:focus, .form-select:focus {
            border-color: var(--input-focus);
            background: rgba(255, 255, 255, 0.08);
        }

        .form-select option {
            background: hsl(220, 40%, 12%);
            color: var(--text-main);
        }

        .search-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .search-btn:hover {
            background: var(--primary-hover);
        }

        .jobs-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-bottom: 4rem;
        }

        .job-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 2rem;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: var(--shadow);
            transition: all 0.3s;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
        }

        .job-card:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 15px 30px rgba(120, 80, 255, 0.1);
        }

        @media (max-width: 768px) {
            .job-card {
                flex-direction: column;
                align-items: start;
                gap: 1.5rem;
            }
            .job-card-action {
                width: 100%;
            }
            .job-card-action a {
                width: 100%;
                text-align: center;
            }
        }

        .job-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .job-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
        }

        .job-meta-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .job-badge {
            background: rgba(120, 80, 255, 0.15);
            color: hsl(250, 80%, 75%);
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 99px;
            font-weight: 600;
        }

        .job-badge.type-badge {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            border: 1px solid var(--card-border);
        }

        .apply-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.8rem 1.8rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.25s;
            white-space: nowrap;
        }

        .apply-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(120, 80, 255, 0.3);
        }

        .no-results {
            text-align: center;
            padding: 4rem;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            color: var(--text-muted);
        }

        .no-results h3 {
            color: #fff;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="nav-bar">
            <span class="nav-title">Careers Portal</span>
            <div>
                <a href="{{ route('kb.index') }}" style="margin-right: 1.5rem;">Help Center</a>
                <a href="{{ route('login') }}">Employee Login</a>
            </div>
        </div>

        <div class="header">
            <h1>Shape the Future with Us</h1>
            <p>Explore our open positions and find a career opportunity that fits your path.</p>
        </div>

        <!-- Filters Section -->
        <div class="filters-container">
            <form action="{{ route('careers.index') }}" method="GET" class="filters-form">
                <!-- Keywords Search -->
                <div class="form-group">
                    <label for="q">Search Jobs</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-input" placeholder="Title or keyword...">
                </div>

                <!-- Department Filter -->
                <div class="form-group">
                    <label for="department_id">Department</label>
                    <select id="department_id" name="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Filter -->
                <div class="form-group">
                    <label for="location">Location</label>
                    <select id="location" name="location" class="form-select">
                        <option value="">All Locations</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Employment Type Filter -->
                <div class="form-group">
                    <label for="employment_type">Job Type</label>
                    <select id="employment_type" name="employment_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="Full-time" {{ request('employment_type') === 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="Part-time" {{ request('employment_type') === 'Part-time' ? 'selected' : '' }}>Part-time</option>
                        <option value="Contract" {{ request('employment_type') === 'Contract' ? 'selected' : '' }}>Contract</option>
                        <option value="Internship" {{ request('employment_type') === 'Internship' ? 'selected' : '' }}>Internship</option>
                    </select>
                </div>

                <button type="submit" class="search-btn">Filter</button>
            </form>
        </div>

        <!-- Jobs Listing -->
        <div class="jobs-list">
            @if($postings->isNotEmpty())
                @foreach($postings as $posting)
                    <div class="job-card">
                        <div class="job-details">
                            <span class="job-badge" style="align-self: start; margin-bottom: 0.5rem;">{{ $posting->department->name ?? 'General' }}</span>
                            <h3 class="job-title">{{ $posting->title }}</h3>
                            <div class="job-meta-row">
                                <span>📍 {{ $posting->location }}</span>
                                <span>•</span>
                                <span class="job-badge type-badge">{{ $posting->employment_type }}</span>
                                @if($posting->closing_date)
                                    <span>•</span>
                                    <span>Closes {{ $posting->closing_date->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="job-card-action">
                            <a href="{{ route('careers.show', $posting->id) }}" class="apply-btn">View & Apply</a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-results">
                    <h3>No Job Openings Found</h3>
                    <p>Try clearing your filters or search keywords to view all active listings.</p>
                    @if(request()->anyFilled(['q', 'department_id', 'location', 'employment_type']))
                        <a href="{{ route('careers.index') }}" class="apply-btn" style="margin-top: 1.5rem; text-decoration: none;">Reset Filters</a>
                    @endif
                </div>
            @endif
        </div>
    </div>

</body>
</html>
