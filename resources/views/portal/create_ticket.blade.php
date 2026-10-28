<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Support Portal</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            overflow-x: hidden;
        }

        .portal-wrapper {
            width: 100%;
            max-width: 800px;
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            background: linear-gradient(to right, #fff, hsl(250, 80%, 80%));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: var(--text-muted);
            font-size: 1.05rem;
            font-weight: 300;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 3rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        /* Decorative lights */
        .card::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(120, 80, 255, 0.15) 0%, transparent 70%);
            z-index: 0;
            pointer-events: none;
        }

        .card-inner {
            position: relative;
            z-index: 1;
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
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success strong {
            color: var(--success);
            font-weight: 600;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .card {
                padding: 1.5rem;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        @media (max-width: 600px) {
            .form-group.full-width {
                grid-column: span 1;
            }
        }

        label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        input, select, textarea {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            padding: 0.9rem 1.1rem;
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
        }

        input:hover, select:hover, textarea:hover {
            border-color: rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.06);
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--input-focus);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(120, 80, 255, 0.15);
        }

        select option {
            background: hsl(220, 40%, 12%);
            color: var(--text-main);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .error-message {
            color: hsl(0, 80%, 60%);
            font-size: 0.8rem;
            margin-top: 0.25rem;
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

        .submit-btn:active {
            transform: translateY(0);
        }

        .footer-links {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--primary-hover);
        }

        /* Portal suggestions layout styling */
        .portal-grid-container {
            display: flex;
            gap: 2rem;
            align-items: start;
            max-width: 1200px;
            width: 100%;
            margin-top: 1rem;
        }

        .portal-main-col {
            flex: 1;
            min-width: 0;
        }

        .suggestions-sidebar {
            width: 360px;
            flex-shrink: 0;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: var(--shadow);
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .suggestion-item {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--card-border);
            padding: 1.2rem;
            border-radius: 14px;
            transition: all 0.25s;
        }

        .suggestion-item:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.15);
        }

        @media (max-width: 991px) {
            .portal-grid-container {
                flex-direction: column;
                align-items: stretch;
            }
            .suggestions-sidebar {
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="display: block; width: 100%; max-width: 1200px; margin: 0 auto;">

    <div class="portal-wrapper" style="max-width: 100%;">
        <div class="header">
            <h1>Customer Support Portal</h1>
            <p>Submit a help request and we will connect you to the right department.</p>
        </div>

        <div class="portal-grid-container">
            <!-- Left Side: Ticket Form -->
            <div class="portal-main-col">
                <div class="card" style="padding: 2.5rem 3rem;">
                    <div class="card-inner">
                        @if(session('success'))
                            <div class="alert-success">
                                <div>
                                    <strong>Success!</strong> {{ session('success') }}
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('tickets.store') }}" method="POST">
                            @csrf
                            
                            <div class="form-grid">
                                <!-- Customer Name -->
                                <div class="form-group">
                                    <label for="customer_name">Your Full Name</label>
                                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" placeholder="John Doe" required>
                                    @error('customer_name')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Customer Email -->
                                <div class="form-group">
                                    <label for="customer_email">Your Email Address</label>
                                    <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" placeholder="john@company.com" required>
                                    @error('customer_email')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Customer Phone -->
                                <div class="form-group">
                                    <label for="customer_phone">Phone Number (Optional)</label>
                                    <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="+1 (555) 019-2834">
                                    @error('customer_phone')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Customer Company -->
                                <div class="form-group">
                                    <label for="customer_company">Company Name (Optional)</label>
                                    <input type="text" id="customer_company" name="customer_company" value="{{ old('customer_company') }}" placeholder="Acme Corporation">
                                    @error('customer_company')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Category -->
                                <div class="form-group">
                                    <label for="category">What is the inquiry about?</label>
                                    <select id="category" name="category" required>
                                        <option value="General" {{ old('category') === 'General' ? 'selected' : '' }}>General Inquiry</option>
                                        <option value="Technical" {{ old('category') === 'Technical' ? 'selected' : '' }}>Technical Issue / Bug</option>
                                        <option value="Billing" {{ old('category') === 'Billing' ? 'selected' : '' }}>Billing & Invoicing</option>
                                        <option value="Account" {{ old('category') === 'Account' ? 'selected' : '' }}>Account & Security</option>
                                        <option value="Feedback" {{ old('category') === 'Feedback' ? 'selected' : '' }}>Product Feedback</option>
                                    </select>
                                    @error('category')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Priority -->
                                <div class="form-group">
                                    <label for="priority">Urgency Level</label>
                                    <select id="priority" name="priority" required>
                                        <option value="Low" {{ old('priority') === 'Low' ? 'selected' : '' }}>Low - Non-urgent question</option>
                                        <option value="Medium" {{ old('priority') === 'Medium' || is_null(old('priority')) ? 'selected' : '' }}>Medium - Normal inquiry</option>
                                        <option value="High" {{ old('priority') === 'High' ? 'selected' : '' }}>High - Affecting business operations</option>
                                        <option value="Urgent" {{ old('priority') === 'Urgent' ? 'selected' : '' }}>Urgent - Complete blocker / service down</option>
                                    </select>
                                    @error('priority')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Subject -->
                                <div class="form-group full-width">
                                    <label for="subject">Short Summary (Subject)</label>
                                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="e.g., Unable to login to my dashboard" required autocomplete="off">
                                    @error('subject')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="form-group full-width">
                                    <label for="description">Detailed Description</label>
                                    <textarea id="description" name="description" placeholder="Please describe your issue or question in detail so we can route it correctly..." required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="submit-btn">
                                <span>Submit Support Request</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Side: Suggestions Sidebar -->
            <div class="suggestions-sidebar" id="suggestions-sidebar">
                <h4 style="font-family: 'Outfit', sans-serif; font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: #fff;">Suggested Help Articles</h4>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">These articles might resolve your problem instantly:</p>
                <div id="suggestions-list" style="display: flex; flex-direction: column; gap: 1rem;">
                    <!-- Appended dynamically -->
                </div>
            </div>
        </div>

        <div class="footer-links" style="margin-top: 3rem; margin-bottom: 2rem;">
            <a href="{{ route('kb.index') }}" style="margin-right: 2rem;">Search Help Guides</a>
            <a href="{{ route('login') }}">← Back to Employee Login</a>
        </div>
    </div>

    <!-- Suggestion Engine Client -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subjectInput = document.getElementById('subject');
            const sidebar = document.getElementById('suggestions-sidebar');
            const listContainer = document.getElementById('suggestions-list');
            let debounceTimer;

            subjectInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = subjectInput.value.trim();

                if (query.length < 3) {
                    sidebar.style.display = 'none';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('api.kb.search') }}?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                listContainer.innerHTML = '';
                                
                                // Show top 3 suggestions
                                data.slice(0, 3).forEach(article => {
                                    const card = document.createElement('div');
                                    card.className = 'suggestion-item';
                                    const plainContent = article.content.replace(/<[^>]*>/g, '');
                                    card.innerHTML = `
                                        <h5 style="font-family: 'Outfit', sans-serif; font-size: 0.95rem; font-weight: 600; margin-bottom: 0.5rem;">
                                            <a href="{{ url('kb') }}/${article.slug}" target="_blank" style="color: hsl(250, 80%, 75%); text-decoration: none;">${article.title}</a>
                                        </h5>
                                        <p style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            ${plainContent}
                                        </p>
                                    `;
                                    listContainer.appendChild(card);
                                });

                                sidebar.style.display = 'block';
                            } else {
                                sidebar.style.display = 'none';
                            }
                        })
                        .catch(err => {
                            console.error('KB suggestion error:', err);
                        });
                }, 300);
            });
        });
    </script>

</body>
</html>
