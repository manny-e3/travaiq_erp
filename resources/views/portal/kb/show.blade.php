<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} - Help Center</title>
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
            --text-main: hsl(0, 0%, 96%);
            --text-muted: hsl(220, 15%, 70%);
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
            max-width: 800px;
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

        .article-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 3rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: var(--shadow);
        }

        @media (max-width: 600px) {
            .article-card {
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

        .tags-container {
            margin-top: 3rem;
            border-top: 1px solid var(--card-border);
            padding-top: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .tag {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            font-size: 0.8rem;
            padding: 4px 10px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .tag:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .deflection-banner {
            margin-top: 3rem;
            background: rgba(120, 80, 255, 0.05);
            border: 1px dashed rgba(120, 80, 255, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
        }

        .deflection-banner p {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .deflection-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s;
        }

        .deflection-btn:hover {
            background: hsl(250, 85%, 72%);
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="nav-bar">
            <a href="{{ route('kb.index') }}">← Back to Knowledge Base</a>
            <span class="nav-title">Article Details</span>
        </div>

        <div class="article-card">
            <span class="category-badge">{{ $article->category }}</span>
            <h1>{{ $article->title }}</h1>
            
            <div class="meta">
                <span>Published {{ $article->created_at->format('M d, Y') }}</span>
                <span>{{ $article->views }} views</span>
            </div>

            <div class="content">
                {!! $article->content !!}
            </div>

            @if(!empty($article->tags) && count($article->tags) > 0)
                <div class="tags-container">
                    <span style="font-size: 0.85rem; color: var(--text-muted); margin-right: 0.5rem;">Tags:</span>
                    @foreach($article->tags as $tag)
                        <a href="{{ route('kb.index', ['tag' => $tag]) }}" class="tag">#{{ $tag }}</a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="deflection-banner">
            <p>Did this article resolve your question? If not, you can submit a support ticket to our team.</p>
            <a href="{{ route('tickets.create') }}" class="deflection-btn">Submit Support Request</a>
        </div>
    </div>

</body>
</html>
