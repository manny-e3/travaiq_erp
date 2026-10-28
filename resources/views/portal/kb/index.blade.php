<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knowledge Base - Help Center</title>
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

        .header {
            text-align: center;
            margin-bottom: 4rem;
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

        .search-container {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .search-input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 16px;
            padding: 1.2rem 1.5rem;
            padding-right: 4.5rem;
            color: var(--text-main);
            font-family: inherit;
            font-size: 1.05rem;
            outline: none;
            transition: all 0.25s;
        }

        .search-input:focus {
            border-color: var(--input-focus);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 5px rgba(120, 80, 255, 0.15);
        }

        .search-btn {
            position: absolute;
            right: 8px;
            top: 8px;
            bottom: 8px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0 1.5rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .search-btn:hover {
            background: var(--primary-hover);
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

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .category-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 2rem;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: var(--shadow);
            transition: all 0.3s;
        }

        .category-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 15px 30px rgba(120, 80, 255, 0.1);
        }

        .category-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .category-badge {
            background: rgba(120, 80, 255, 0.15);
            color: hsl(250, 80%, 75%);
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 99px;
            font-weight: 600;
        }

        .article-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .article-item a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .article-item a:hover {
            color: #fff;
        }

        .article-views {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.3);
        }

        .no-results {
            text-align: center;
            padding: 4rem;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="nav-bar">
            <span class="nav-title">Support Desk</span>
            <div>
                <a href="{{ route('tickets.create') }}" style="margin-right: 1.5rem;">Submit a Ticket</a>
                <a href="{{ route('login') }}">Agent Login</a>
            </div>
        </div>

        <div class="header">
            <h1>How can we help you today?</h1>
            <p>Search our Knowledge Base for immediate resolution guides, how-tos, and FAQs.</p>
            
            <form action="{{ route('kb.index') }}" method="GET" class="search-container">
                <input type="text" name="q" value="{{ request('q') }}" class="search-input" placeholder="Search by keywords e.g. database, password, invoice..." required>
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        @if(request('q'))
            <h3 style="margin-bottom: 2rem; font-family: 'Outfit', sans-serif;">Search Results for "{{ request('q') }}"</h3>
        @endif

        @if($groupedArticles->isNotEmpty())
            <div class="category-grid">
                @foreach($groupedArticles as $category => $articlesList)
                    <div class="category-card">
                        <div class="category-title">
                            <span>{{ $category }}</span>
                            <span class="category-badge">{{ $articlesList->count() }} guides</span>
                        </div>
                        <ul class="article-list">
                            @foreach($articlesList as $art)
                                <li class="article-item">
                                    <a href="{{ route('kb.show', $art->slug) }}">
                                        <span>{{ $art->title }}</span>
                                        <span class="article-views">{{ $art->views }} views</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-results">
                <h3>No articles found</h3>
                <p style="margin-top: 0.5rem;">Try refining your keywords or return to the <a href="{{ route('kb.index') }}" style="color: var(--primary); text-decoration: none;">Knowledge Base Home</a>.</p>
            </div>
        @endif
    </div>

</body>
</html>
