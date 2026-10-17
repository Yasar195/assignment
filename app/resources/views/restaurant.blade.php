<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sunset Grill</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fffdf7; margin: 0; color: #1f2937; }
        .container { max-width: 1100px; margin: 0 auto; padding: 32px 20px 80px; }
        nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
        .brand { font-size: 30px; font-weight: bold; color: #b45309; }
        .nav-links { display: flex; gap: 20px; }
        .nav-links a { color: #374151; text-decoration: none; }
        .hero { background: linear-gradient(135deg, #fef3c7, #ffedd5); border-radius: 18px; padding: 40px 28px; margin-bottom: 24px; }
        .hero h1 { font-size: 42px; margin: 0 0 8px; }
        .hero p { margin: 0; font-size: 18px; }
        .menu { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-top: 20px; }
        .dish { background: white; border: 1px solid #f1f5f9; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
        .dish h3 { margin-top: 0; }
        .order-btn { background: #f59e0b; color: white; border: none; padding: 12px 18px; border-radius: 10px; cursor: pointer; font-weight: bold; }
        .status { margin-top: 16px; padding: 12px 16px; background: #ecfdf5; color: #047857; border-radius: 10px; display: none; }
        .status.show { display: block; }
    </style>
</head>
<body>
    <div class="container">
        <nav>
            <div class="brand">Sunset Grill</div>
            <div class="nav-links">
                <a href="/restaurant">Home</a>
                <a href="/dashboard">Dashboard</a>
            </div>
        </nav>

        <div class="hero">
            <h1>Fresh meals, warm hospitality.</h1>
            <p>Chef-crafted plates served with a sunset view.</p>
        </div>

        <div class="menu">
            <div class="dish">
                <h3>Grilled Salmon</h3>
                <p>Herb butter, lemon rice, seasonal greens.</p>
                <button class="order-btn" data-item="Grilled Salmon">Order now</button>
            </div>
            <div class="dish">
                <h3>Chicken Bowl</h3>
                <p>Garlic quinoa, avocado, roasted vegetables.</p>
                <button class="order-btn" data-item="Chicken Bowl">Order now</button>
            </div>
            <div class="dish">
                <h3>Garden Pasta</h3>
                <p>Pesto, cherry tomatoes, parmesan.</p>
                <button class="order-btn" data-item="Garden Pasta">Order now</button>
            </div>
        </div>

        <div id="status" class="status">Order placed successfully.</div>
    </div>

    <script>
        const sessionId = 'session-' + Date.now();
        const userId = 'guest-' + Math.random().toString(36).slice(2, 10);

        function sendEvent(eventName, metadata = {}) {
            fetch('/api/events', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Session-Id': sessionId,
                    'X-User-Id': userId,
                },
                body: JSON.stringify({
                    event_name: eventName,
                    page: window.location.pathname,
                    source: 'browser',
                    session_id: sessionId,
                    user_id: userId,
                    metadata
                })
            }).catch(err => console.error('Track failed', err));
        }

        sendEvent('page_view', { title: document.title, referrer: document.referrer || 'direct' });

        document.querySelectorAll('.order-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = btn.dataset.item;
                sendEvent('order_created', { item, total: 19.99 });
                const status = document.getElementById('status');
                status.textContent = `${item} has been ordered.`;
                status.classList.add('show');
            });
        });
    </script>
</body>
</html>
